<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SubmitPaymentRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    /** Submit (or re-submit after a rejection) the GCash reference and screenshot. */
    public function store(SubmitPaymentRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orders->submitGcashPayment(
                $order,
                $request->validated('transaction_reference'),
                $request->file('payment_proof'),
            );
        } catch (OrderException $e) {
            return redirect()->route('orders.show', $order)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment details submitted. The vendor will verify your payment.');
    }

    /** Streams the payment screenshot from private storage, only to the order's owner. */
    public function proof(Order $order): StreamedResponse
    {
        Gate::authorize('view', $order);

        $path = $order->payment?->proof_of_payment;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}