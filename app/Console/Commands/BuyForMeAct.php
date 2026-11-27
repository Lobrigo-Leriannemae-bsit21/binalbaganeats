<?php

namespace App\Console\Commands;

use App\Exceptions\BuyForMeException;
use App\Models\BuyForMeRequest;
use App\Models\User;
use App\Services\BuyForMeService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BuyForMeAct extends Command
{
    protected $signature = 'buyforme:act
        {code : Request code such as BFM-000001, or the numeric id}
        {action : accept, estimate, confirm-advance, reject-advance, purchase or deliver}
        {--rider= : Rider email (accept only; defaults to the sample rider)}
        {--amount= : Pesos (estimate and purchase)}';

    protected $description = 'DEVELOPMENT ONLY: acts as the rider on a Buy-for-Me request, using the same rules the rider app will use';

    public function handle(BuyForMeService $service): int
    {
        if (app()->isProduction()) {
            $this->error('This development command is disabled in production.');

            return self::FAILURE;
        }

        $key = trim((string) $this->argument('code'));

        $request = BuyForMeRequest::query()
            ->where('request_code', Str::upper($key))
            ->when(ctype_digit($key), fn ($query) => $query->orWhere('id', (int) $key))
            ->first();

        if (! $request) {
            $this->error("Request {$key} was not found.");

            return self::FAILURE;
        }

        $action = Str::lower((string) $this->argument('action'));

        try {
            if ($action === 'accept') {
                $email = $this->option('rider') ?: 'rider@sample.binalbaganeats.test';
                $rider = User::query()->where('email', $email)->first();

                if (! $rider) {
                    $this->error("Rider {$email} not found. Run: php artisan db:seed --class=SampleRiderSeeder");

                    return self::FAILURE;
                }

                $service->acceptByRider($request, $rider);
            } else {
                $rider = $request->rider;

                if (! $rider) {
                    $this->error('No rider has accepted this request yet. Use the "accept" action first.');

                    return self::FAILURE;
                }

                match ($action) {
                    'estimate' => $service->confirmEstimate($request, $rider, $this->amount()),
                    'confirm-advance' => $service->confirmAdvance($request, $rider),
                    'reject-advance' => $service->rejectAdvance($request, $rider),
                    // The rider app will require a receipt photo. The command records none.
                    'purchase' => $service->recordPurchase($request, $rider, $this->amount(), null),
                    'deliver' => $service->completeDelivery($request, $rider),
                    default => throw new BuyForMeException('Unknown action. Use: accept, estimate, confirm-advance, reject-advance, purchase, deliver.'),
                };
            }
        } catch (BuyForMeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $request->refresh();

        $this->info("{$request->request_code}: {$request->request_status->label()} (payment: {$request->settlement_status->label()})");

        if ($request->advance_amount !== null) {
            $this->line('Estimate ₱'.number_format((float) $request->estimated_expense, 2)
                .' | Delivery fee ₱'.number_format((float) $request->delivery_fee, 2)
                .' | Advance ₱'.number_format((float) $request->advance_amount, 2));
        }

        return self::SUCCESS;
    }

    private function amount(): string
    {
        $amount = $this->option('amount');

        if (! is_numeric($amount)) {
            throw new BuyForMeException('Add an amount, for example: --amount=300');
        }

        return (string) $amount;
    }
}