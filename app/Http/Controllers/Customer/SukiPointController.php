<?php

namespace App\Http\Controllers\Customer;

use App\Enums\SukiPointStatus;
use App\Enums\SukiPointType;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\SukiPointService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SukiPointController extends Controller
{
    public function __construct(private readonly SukiPointService $suki)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $rules = $this->suki->rules();

        return view('customer.suki.index', [
            'balance' => $this->suki->availableBalance($user),
            'pending' => $this->suki->pendingEarned($user),
            'held' => $this->suki->heldPoints($user),
            'lifetime' => $this->suki->lifetimeEarned($user),
            'used' => $this->suki->usedPoints($user),
            'rules' => $rules,
            'recent' => $user->sukiPointTransactions()->with('order')->latest('id')->limit(5)->get(),
        ]);
    }

    public function history(Request $request): View
    {
        $user = $request->user();

        $type = in_array($request->query('type'), ['earned', 'redeemed'], true) ? $request->query('type') : null;
        $status = in_array($request->query('status'), ['pending', 'approved', 'applied'], true) ? $request->query('status') : null;

        $transactions = $user->sukiPointTransactions()
            ->with('order')
            ->when($type, fn ($query, string $value) => $query->where('type', $value))
            ->when($status, fn ($query, string $value) => $query->where('status', $value))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('customer.suki.history', [
            'transactions' => $transactions,
            'type' => $type,
            'status' => $status,
            'balance' => $this->suki->availableBalance($user),
        ]);
    }

    /** Shows how many points the customer can use on the CURRENT cart. */
    public function redeem(Request $request, CartService $cart): View
    {
        $user = $request->user();
        $rules = $this->suki->rules();

        $lines = $cart->lines();
        $subtotal = $cart->subtotal($lines);
        $subtotalCents = (int) round($subtotal * 100);

        $balance = $this->suki->availableBalance($user);
        $max = $this->suki->maxRedeemable($balance, $subtotalCents);

        return view('customer.suki.redeem', [
            'rules' => $rules,
            'balance' => $balance,
            'cartEmpty' => $lines->isEmpty(),
            'subtotal' => $subtotal,
            'max' => $max,
            'canRedeem' => $max >= $rules['min_redeem'],
        ]);
    }
}