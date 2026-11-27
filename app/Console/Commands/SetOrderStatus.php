<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetOrderStatus extends Command
{
    protected $signature = 'orders:set-status
        {order : Order number such as BE-000001, or the numeric id}
        {status : Next status such as accepted, preparing, "ready for pickup", "on the way", delivered, declined or cancelled}';

    protected $description = 'DEVELOPMENT ONLY: move an order to its next status using the same rules and Suki Points steps the apps will use';

    public function handle(OrderStatusService $statuses): int
    {
        if (app()->isProduction()) {
            $this->error('This development command is disabled in production.');

            return self::FAILURE;
        }

        $key = trim((string) $this->argument('order'));

        $order = Order::query()
            ->where('order_number', Str::upper($key))
            ->when(ctype_digit($key), fn ($query) => $query->orWhere('id', (int) $key))
            ->first();

        if (! $order) {
            $this->error("Order {$key} was not found.");

            return self::FAILURE;
        }

        $wanted = $this->normalize((string) $this->argument('status'));
        $next = collect(OrderStatus::cases())
            ->first(fn (OrderStatus $status) => $this->normalize($status->value) === $wanted);

        if (! $next) {
            $this->error('Unknown status. Use one of: '.collect(OrderStatus::cases())->map->value->implode(', '));

            return self::FAILURE;
        }

        $from = $order->order_status->label();

        try {
            $statuses->transition($order, $next);
        } catch (OrderException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$order->order_number}: {$from} -> {$next->label()}");

        return self::SUCCESS;
    }

    private function normalize(string $value): string
    {
        return (string) preg_replace('/[^a-z]/', '', Str::lower($value));
    }
}