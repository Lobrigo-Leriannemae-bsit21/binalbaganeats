<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    private const RELATIONS = [
        'items.menuItem',
        'vendorProfile',
        'deliveryZone',
        'payment',
        'delivery.rider',
    ];

    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $requested = $request->query('status');
        $filter = in_array($requested, ['active', 'delivered', 'cancelled'], true) ? $requested : 'all';

        $query = $user->customerOrders()
            ->with(['vendorProfile', 'payment'])
            ->withSum('items', 'quantity')
            ->latest('id');

        match ($filter) {
            'active' => $query->whereIn('order_status', OrderStatus::activeValues()),
            'delivered' => $query->where('order_status', OrderStatus::Delivered->value),
            'cancelled' => $query->whereIn('order_status', [OrderStatus::Declined->value, OrderStatus::Cancelled->value]),
            default => null,
        };

        // Plain query builder (no model casts) so the status text can be used as an array key.
        $rows = DB::table('orders')
            ->where('customer_id', $user->id)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $counts = [
            'all' => (int) $rows->sum(),
            'active' => (int) collect(OrderStatus::activeValues())->sum(fn (string $status) => $rows[$status] ?? 0),
            'delivered' => (int) ($rows[OrderStatus::Delivered->value] ?? 0),
            'cancelled' => (int) (($rows[OrderStatus::Declined->value] ?? 0) + ($rows[OrderStatus::Cancelled->value] ?? 0)),
        ];

        return view('customer.orders.index', [
            'orders' => $query->paginate(8)->withQueryString(),
            'filter' => $filter,
            'counts' => $counts,
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load([...self::RELATIONS, 'statusHistories' => fn ($q) => $q->oldest('id')]);

        return view('customer.orders.show', ['order' => $order]);
    }

    public function confirmation(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(self::RELATIONS);

        return view('customer.orders.confirmation', ['order' => $order]);
    }

    public function track(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load([...self::RELATIONS, 'statusHistories' => fn ($q) => $q->oldest('id')]);

        return view('customer.orders.track', ['order' => $order]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        try {
            $this->orders->cancelByCustomer($order, $request->user());
        } catch (OrderException $e) {
            return redirect()->route('orders.show', $order)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Your order has been cancelled.');
    }
}