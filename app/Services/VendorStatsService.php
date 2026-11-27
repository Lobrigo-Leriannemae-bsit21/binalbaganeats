<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard numbers for ONE vendor. Every query is limited to that vendor's own orders.
 * "Sales" means the food subtotal of Delivered orders (the delivery fee is not the vendor's).
 */
class VendorStatsService
{
    /** @return array<string, int> */
    public function summary(User $vendor): array
    {
        $orders = fn () => Order::query()->where('vendor_id', $vendor->id);

        $salesSum = $orders()->where('order_status', OrderStatus::Delivered->value)->sum('subtotal');

        return [
            'total_orders' => $orders()->count(),
            'pending_orders' => $orders()->where('order_status', OrderStatus::Pending->value)->count(),
            'delivered_orders' => $orders()->where('order_status', OrderStatus::Delivered->value)->count(),
            'today_orders' => $orders()->whereDate('created_at', today())->count(),
            'sales_cents' => (int) round(((float) $salesSum) * 100),
            'menu_total' => $vendor->menuItems()->count(),
            'menu_available' => $vendor->menuItems()->where('is_available', true)->count(),
        ];
    }

    /**
     * Food sales per day for the last $days days (oldest first), by the day each order was delivered.
     *
     * @return array<int, array{label: string, full: string, cents: int, orders: int, percent: int}>
     */
    public function dailySales(User $vendor, int $days = 7): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = DB::table('orders')
            ->join('status_histories', 'status_histories.order_id', '=', 'orders.id')
            ->where('orders.vendor_id', $vendor->id)
            ->where('status_histories.status', OrderStatus::Delivered->value)
            ->where('status_histories.created_at', '>=', $start)
            ->selectRaw('DATE(status_histories.created_at) as day, SUM(orders.subtotal) as total, COUNT(*) as orders_count')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $row = $rows->get($date->toDateString());

            $series[] = [
                'label' => $date->format('D'),
                'full' => $date->format('M j'),
                'cents' => $row ? (int) round(((float) $row->total) * 100) : 0,
                'orders' => $row ? (int) $row->orders_count : 0,
                'percent' => 0,
            ];
        }

        $max = max(array_column($series, 'cents'));

        foreach ($series as &$day) {
            $day['percent'] = ($max > 0 && $day['cents'] > 0)
                ? max(4, (int) round($day['cents'] / $max * 100))
                : 0;
        }
        unset($day);

        return $series;
    }

    /** @return Collection<int, Order> */
    public function recentOrders(User $vendor, int $limit = 5): Collection
    {
        return Order::query()
            ->where('vendor_id', $vendor->id)
            ->with('customer:id,name')
            ->withSum('items', 'quantity')
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}