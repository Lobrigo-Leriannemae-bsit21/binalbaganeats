<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BuyForMeStatus;
use App\Exceptions\BuyForMeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\BuyForMeAdvanceRequest;
use App\Http\Requests\Customer\BuyForMeStoreRequest;
use App\Models\BuyForMeRequest;
use App\Models\DeliveryZone;
use App\Services\BuyForMeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuyForMeController extends Controller
{
    public function __construct(private readonly BuyForMeService $service)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $limit = (int) config('buyforme.max_open_requests', 3);

        $openCount = $user->buyForMeRequests()
            ->whereIn('request_status', BuyForMeStatus::activeValues())
            ->count();

        return view('customer.buyforme.index', [
            'requests' => $user->buyForMeRequests()->with(['rider', 'deliveryZone'])->latest('id')->paginate(8),
            'atLimit' => $openCount >= $limit,
            'limit' => $limit,
            'advancePercent' => (int) config('buyforme.advance_percent', 50),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $limit = (int) config('buyforme.max_open_requests', 3);

        $openCount = $user->buyForMeRequests()
            ->whereIn('request_status', BuyForMeStatus::activeValues())
            ->count();

        if ($openCount >= $limit) {
            return redirect()->route('buyforme.index')
                ->with('error', "You can have at most {$limit} open requests at a time. Please wait for one to finish.");
        }

        // One-time token: a second submission of the same page is rejected.
        $token = (string) Str::uuid();
        $request->session()->put('buyforme_token', $token);

        return view('customer.buyforme.create', [
            'zones' => DeliveryZone::query()->active()->orderBy('zone_name')->get(),
            'user' => $user,
            'token' => $token,
            'advancePercent' => (int) config('buyforme.advance_percent', 50),
        ]);
    }

    public function store(BuyForMeStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        $expected = $request->session()->get('buyforme_token');

        if (! is_string($expected) || ! hash_equals($expected, (string) $request->input('form_token'))) {
            return redirect()->route('buyforme.index')
                ->with('status', 'This request was already submitted or has expired. Please check your requests.');
        }

        // Only one submission per customer at a time (stops rapid double-clicks).
        $lock = Cache::lock('buyforme-user-'.$user->id, 15);

        if (! $lock->get()) {
            return back()->withInput()->with('error', 'Your request is already being processed. Please wait a moment.');
        }

        try {
            $buyForMe = $this->service->create($user, $request->validated());
            $request->session()->forget('buyforme_token');
        } catch (BuyForMeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } finally {
            $lock->release();
        }

        return redirect()->route('buyforme.show', $buyForMe)
            ->with('success', 'Your request was submitted. A rider in your barangay will pick it up soon.');
    }

    public function show(BuyForMeRequest $buyForMe): View
    {
        Gate::authorize('view', $buyForMe);

        $buyForMe->load([
            'rider',
            'deliveryZone',
            'statusHistories' => fn ($query) => $query->oldest('id'),
        ]);

        return view('customer.buyforme.show', [
            'buyForMe' => $buyForMe,
            'advancePercent' => (int) config('buyforme.advance_percent', 50),
            'overrunPercent' => (int) config('buyforme.overrun_percent', 10),
        ]);
    }

    public function cancel(Request $request, BuyForMeRequest $buyForMe): RedirectResponse
    {
        Gate::authorize('view', $buyForMe);

        try {
            $this->service->cancelByCustomer($buyForMe, $request->user());
        } catch (BuyForMeException $e) {
            return redirect()->route('buyforme.show', $buyForMe)->with('error', $e->getMessage());
        }

        return redirect()->route('buyforme.show', $buyForMe)->with('success', 'Your request has been cancelled.');
    }

    public function advance(BuyForMeAdvanceRequest $request, BuyForMeRequest $buyForMe): RedirectResponse
    {
        try {
            $this->service->submitAdvance(
                $buyForMe,
                $request->user(),
                $request->validated('gcash_reference_number'),
                $request->file('advance_proof'),
            );
        } catch (BuyForMeException $e) {
            return redirect()->route('buyforme.show', $buyForMe)->with('error', $e->getMessage());
        }

        return redirect()->route('buyforme.show', $buyForMe)
            ->with('success', 'Advance payment details submitted. Your rider will confirm it shortly.');
    }

    /** The customer's GCash screenshot, from private storage, owner only. */
    public function advanceProof(BuyForMeRequest $buyForMe): StreamedResponse
    {
        Gate::authorize('view', $buyForMe);

        return $this->privateFile($buyForMe->advance_proof);
    }

    /** The rider's purchase receipt, from private storage, owner only. */
    public function receipt(BuyForMeRequest $buyForMe): StreamedResponse
    {
        Gate::authorize('view', $buyForMe);

        return $this->privateFile($buyForMe->receipt_image);
    }

    private function privateFile(?string $path): StreamedResponse
    {
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}