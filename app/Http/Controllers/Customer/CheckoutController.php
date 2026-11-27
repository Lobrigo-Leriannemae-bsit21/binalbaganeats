<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PlaceOrderRequest;
use App\Models\DeliveryZone;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\SukiPointService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
        private readonly SukiPointService $suki,
    ) {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($lines->contains(fn (array $line) => ! $line['orderable'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart can’t be ordered right now. Please remove them to continue.');
        }

        $vendor = $this->cart->vendorProfile();
        $user = $request->user();
        $subtotal = $this->cart->subtotal($lines);
        $subtotalCents = (int) round($subtotal * 100);

        // One-time token: a second submission of the same page is rejected.
        $token = (string) Str::uuid();
        $request->session()->put('checkout_token', $token);

        $balance = $this->suki->availableBalance($user);
        $rules = $this->suki->rules();

        return view('customer.checkout.index', [
            'lines' => $lines,
            'vendor' => $vendor,
            'subtotal' => $subtotal,
            'zones' => DeliveryZone::query()->active()->orderBy('zone_name')->get(),
            'user' => $user,
            'token' => $token,
            'gcashAvailable' => filled($vendor?->gcash_number),
            'suki' => [
                'balance' => $balance,
                'min' => $rules['min_redeem'],
                'max' => $this->suki->maxRedeemable($balance, $subtotalCents),
                'valuePesos' => $rules['value_pesos'],
                'percent' => $rules['max_discount_percent'],
                'earnable' => $this->suki->pointsForSubtotalCents($subtotalCents),
            ],
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $user = $request->user();

        $expected = $request->session()->get('checkout_token');

        if (! is_string($expected) || ! hash_equals($expected, (string) $request->input('checkout_token'))) {
            return redirect()->route('orders.index')
                ->with('status', 'This checkout was already submitted or has expired. Please check your orders.');
        }

        // Only one checkout per customer at a time (stops rapid double-clicks).
        $lock = Cache::lock('checkout-user-'.$user->id, 15);

        if (! $lock->get()) {
            return back()->withInput()
                ->with('error', 'Your order is already being processed. Please wait a moment.');
        }

        try {
            $order = $this->checkout->placeOrder(
                $user,
                array_merge($request->validated(), ['save_address' => $request->boolean('save_address')]),
                $request->file('payment_proof'),
            );

            $request->session()->forget('checkout_token');
        } catch (OrderException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } finally {
            $lock->release();
        }

        return redirect()->route('orders.confirmation', $order)
            ->with('success', 'Your order has been placed!');
    }
}