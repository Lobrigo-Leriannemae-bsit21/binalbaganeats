<?php

namespace App\Services;

use App\Enums\BuyForMeSettlement;
use App\Enums\BuyForMeStatus;
use App\Exceptions\BuyForMeException;
use App\Models\BuyForMeRequest;
use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Every Buy-for-Me action goes through here, so the customer pages, the development
 * command and (later) the rider and admin apps all follow the same rules.
 *
 * Flow:
 *  Open Request -> Accepted -> Awaiting Advance Payment -> Purchasing -> On the Way -> Delivered
 *  (Cancelled is possible until the customer submits the advance.)
 *
 * Settlement: Unassigned -> Awaiting Advance -> Advance Submitted -> Partially Paid -> Fully Paid
 */
class BuyForMeService
{
    public function __construct(private readonly CustomerNotifier $notifier)
    {
    }

    // ---------------------------------------------------------------- customer

    /** @param array<string, mixed> $data Validated form data. */
    public function create(User $customer, array $data): BuyForMeRequest
    {
        return DB::transaction(function () use ($customer, $data) {
            // Lock the customer row so two simultaneous submissions cannot exceed the limit.
            User::query()->whereKey($customer->id)->lockForUpdate()->first();

            $limit = (int) config('buyforme.max_open_requests', 3);

            $open = BuyForMeRequest::query()
                ->where('customer_id', $customer->id)
                ->whereIn('request_status', BuyForMeStatus::activeValues())
                ->count();

            if ($open >= $limit) {
                throw new BuyForMeException("You can have at most {$limit} open requests at a time. Please wait for one to finish.");
            }

            $zone = DeliveryZone::query()->active()->find((int) $data['delivery_zone_id']);

            if (! $zone) {
                throw new BuyForMeException('The selected delivery zone is no longer available. Please choose another.');
            }

            $request = BuyForMeRequest::create([
                'request_code' => 'TMP-'.Str::random(20), // replaced below once the id is known
                'customer_id' => $customer->id,
                'delivery_zone_id' => $zone->id,
                'establishment_name' => $data['establishment_name'],
                'items_summary' => $data['items_summary'],
                'delivery_address' => $data['delivery_address'],
                'delivery_fee' => $zone->delivery_fee, // fee captured now, like an order
                'request_status' => BuyForMeStatus::Open,
                'settlement_status' => BuyForMeSettlement::Unassigned,
            ]);

            $request->update(['request_code' => sprintf('BFM-%06d', $request->id)]);

            $request->statusHistories()->create([
                'status' => BuyForMeStatus::Open->value,
                'updated_by' => $customer->id,
            ]);

            return $request;
        });
    }

    /**
     * Free until the advance is submitted. After that the customer must contact support
     * (refunds are handled outside the app).
     *
     * @throws BuyForMeException
     */
    public function cancelByCustomer(BuyForMeRequest $request, User $customer): void
    {
        DB::transaction(function () use ($request, $customer) {
            $locked = $this->lock($request);

            if ((int) $locked->customer_id !== (int) $customer->id) {
                throw new BuyForMeException('You cannot change this request.');
            }

            $advanceIn = in_array($locked->settlement_status, [
                BuyForMeSettlement::AdvanceSubmitted,
                BuyForMeSettlement::PartiallyPaid,
                BuyForMeSettlement::FullyPaid,
            ], true);

            if ($advanceIn || ! $locked->request_status->customerCancellable()) {
                throw new BuyForMeException(
                    'This request can no longer be cancelled in the app. If you need help, please contact the BinalbaganEats team.'
                );
            }

            $this->moveTo($locked, BuyForMeStatus::Cancelled, $customer);
        });
    }

    /**
     * Stores the customer's advance GCash reference and screenshot (private disk).
     * Allowed only while the rider is waiting for the advance.
     *
     * @throws BuyForMeException
     */
    public function submitAdvance(BuyForMeRequest $request, User $customer, string $reference, UploadedFile $proof): void
    {
        $newPath = $proof->store('buyforme-proofs', 'local');
        $oldPath = null;

        try {
            DB::transaction(function () use ($request, $customer, $reference, $newPath, &$oldPath) {
                $locked = $this->lock($request);

                if ((int) $locked->customer_id !== (int) $customer->id) {
                    throw new BuyForMeException('You cannot change this request.');
                }

                if ($locked->request_status !== BuyForMeStatus::AwaitingAdvance
                    || $locked->settlement_status !== BuyForMeSettlement::AwaitingAdvance) {
                    throw new BuyForMeException('This request is not waiting for an advance payment.');
                }

                $oldPath = $locked->advance_proof;

                $locked->update([
                    'gcash_reference_number' => $reference,
                    'advance_proof' => $newPath,
                    'settlement_status' => BuyForMeSettlement::AdvanceSubmitted,
                ]);
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($newPath);

            throw (BuyForMeException::fromDuplicateReference($e) ?? $e);
        }

        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }
    }

    // ------------------------------------------- rider actions (Phase 3 UI; dev command for now)

    /** First rider to accept gets it. The rider must serve the request's barangay. */
    public function acceptByRider(BuyForMeRequest $request, User $rider): BuyForMeRequest
    {
        return DB::transaction(function () use ($request, $rider) {
            $locked = $this->lock($request);

            if (! $rider->isRider() || ! $rider->isApproved()) {
                throw new BuyForMeException('Only approved riders can accept requests.');
            }

            if ($locked->request_status !== BuyForMeStatus::Open || $locked->rider_id !== null) {
                throw new BuyForMeException('This request was already taken or is no longer open.');
            }

            $serves = $rider->serviceZones()->whereKey($locked->delivery_zone_id)->exists();

            if (! $serves) {
                throw new BuyForMeException('This request is outside the barangays you serve.');
            }

            $this->moveTo($locked, BuyForMeStatus::Accepted, $rider, ['rider_id' => $rider->id]);

            return $locked;
        });
    }

    /** Rider confirms the estimated food cost. The advance is calculated here. */
    public function confirmEstimate(BuyForMeRequest $request, User $rider, string|float|int $estimatedPesos): BuyForMeRequest
    {
        $estimateCents = (int) round(((float) $estimatedPesos) * 100);
        $maxPesos = (int) config('buyforme.max_estimate', 100000);

        if ($estimateCents <= 0 || $estimateCents > $maxPesos * 100) {
            throw new BuyForMeException('Enter an estimate between ₱1 and ₱'.number_format($maxPesos).'.');
        }

        return DB::transaction(function () use ($request, $rider, $estimateCents) {
            $locked = $this->lock($request);
            $this->assertRider($locked, $rider);

            if ($locked->request_status !== BuyForMeStatus::Accepted) {
                throw new BuyForMeException('The estimate can only be confirmed right after accepting the request.');
            }

            if (blank($rider->gcash_number)) {
                throw new BuyForMeException('Add your GCash number first, so the customer knows where to send the advance.');
            }

            $totalCents = $estimateCents + $locked->feeCents();

            // Advance = percent of the estimated total, rounded UP to a whole peso.
            $percent = max(1, min(100, (int) config('buyforme.advance_percent', 50)));
            $advancePesos = intdiv($totalCents * $percent + 9999, 10000);

            $this->moveTo($locked, BuyForMeStatus::AwaitingAdvance, $rider, [
                'estimated_expense' => $this->money($estimateCents),
                'advance_amount' => $this->money($advancePesos * 100),
                'settlement_status' => BuyForMeSettlement::AwaitingAdvance,
            ]);

            return $locked;
        });
    }

    /** Rider checked their own GCash account and received the advance. */
    public function confirmAdvance(BuyForMeRequest $request, User $rider): BuyForMeRequest
    {
        return DB::transaction(function () use ($request, $rider) {
            $locked = $this->lock($request);
            $this->assertRider($locked, $rider);

            if ($locked->request_status !== BuyForMeStatus::AwaitingAdvance
                || $locked->settlement_status !== BuyForMeSettlement::AdvanceSubmitted) {
                throw new BuyForMeException('There is no submitted advance payment to confirm.');
            }

            $this->moveTo($locked, BuyForMeStatus::Purchasing, $rider, [
                'settlement_status' => BuyForMeSettlement::PartiallyPaid,
                'advance_confirmed_at' => now(),
            ]);

            return $locked;
        });
    }

    /** Rider did not receive the money. The customer can submit their details again. */
    public function rejectAdvance(BuyForMeRequest $request, User $rider): BuyForMeRequest
    {
        $oldProof = null;

        $locked = DB::transaction(function () use ($request, $rider, &$oldProof) {
            $locked = $this->lock($request);
            $this->assertRider($locked, $rider);

            if ($locked->request_status !== BuyForMeStatus::AwaitingAdvance
                || $locked->settlement_status !== BuyForMeSettlement::AdvanceSubmitted) {
                throw new BuyForMeException('There is no submitted advance payment to reject.');
            }

            $oldProof = $locked->advance_proof;

            $locked->update([
                'gcash_reference_number' => null,
                'advance_proof' => null,
                'settlement_status' => BuyForMeSettlement::AwaitingAdvance,
            ]);

            DB::afterCommit(fn () => $this->notifier->buyForMeAdvanceRejected($locked));

            return $locked;
        });

        if ($oldProof) {
            Storage::disk('local')->delete($oldProof);
        }

        return $locked;
    }

    /**
     * Rider records what they actually spent. It may be at most "overrun_percent" above the
     * estimate. $receiptPath is the stored receipt photo (the rider app will require it).
     */
    public function recordPurchase(BuyForMeRequest $request, User $rider, string|float|int $actualPesos, ?string $receiptPath): BuyForMeRequest
    {
        $actualCents = (int) round(((float) $actualPesos) * 100);

        if ($actualCents <= 0) {
            throw new BuyForMeException('Enter the actual amount spent.');
        }

        return DB::transaction(function () use ($request, $rider, $actualCents, $receiptPath) {
            $locked = $this->lock($request);
            $this->assertRider($locked, $rider);

            if ($locked->request_status !== BuyForMeStatus::Purchasing) {
                throw new BuyForMeException('Purchases can only be recorded after the advance is confirmed.');
            }

            $estimateCents = (int) round(((float) $locked->estimated_expense) * 100);
            $percent = max(0, (int) config('buyforme.overrun_percent', 10));
            $capCents = intdiv($estimateCents * (100 + $percent), 100);

            if ($actualCents > $capCents) {
                throw new BuyForMeException(
                    'The actual expense can be at most ₱'.number_format($capCents / 100, 2)
                    ." ({$percent}% above the ₱".number_format($estimateCents / 100, 2).' estimate). '
                    .'Contact the customer, then ask an admin to adjust the amount.'
                );
            }

            $this->moveTo($locked, BuyForMeStatus::OnTheWay, $rider, [
                'actual_expense' => $this->money($actualCents),
                'receipt_image' => $receiptPath,
            ]);

            return $locked;
        });
    }

    /** Rider delivered the items and settled the balance (or returned the change). */
    public function completeDelivery(BuyForMeRequest $request, User $rider): BuyForMeRequest
    {
        return DB::transaction(function () use ($request, $rider) {
            $locked = $this->lock($request);
            $this->assertRider($locked, $rider);

            $this->moveTo($locked, BuyForMeStatus::Delivered, $rider, [
                'settlement_status' => BuyForMeSettlement::FullyPaid,
            ]);

            return $locked;
        });
    }

    // ----------------------------------------------------------------- helpers

    private function lock(BuyForMeRequest $request): BuyForMeRequest
    {
        return BuyForMeRequest::query()->whereKey($request->id)->lockForUpdate()->firstOrFail();
    }

    private function assertRider(BuyForMeRequest $locked, User $rider): void
    {
        if (! $rider->isRider() || (int) $locked->rider_id !== (int) $rider->id) {
            throw new BuyForMeException('This request is not assigned to you.');
        }
    }

    /** Validates the status order, saves the change, writes the history and notifies the customer. */
    private function moveTo(BuyForMeRequest $locked, BuyForMeStatus $next, ?User $actor, array $extra = []): void
    {
        if (! $locked->request_status->canTransitionTo($next)) {
            throw new BuyForMeException(
                "A request that is {$locked->request_status->label()} cannot change to {$next->label()}."
            );
        }

        $locked->update(array_merge($extra, ['request_status' => $next]));

        $locked->statusHistories()->create([
            'status' => $next->value,
            'updated_by' => $actor?->id,
        ]);

        // Notify only after the change is safely saved.
        DB::afterCommit(fn () => $this->notifier->buyForMeStatusChanged($locked, $next, $actor));
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}