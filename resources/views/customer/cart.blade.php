@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Shopping Cart')
@section('page-title', 'Shopping Cart')
@section('page-subtitle', 'Review your selected food items before proceeding to checkout')

@section('content')
@if (empty($cart))
    <div class="be-card p-5 text-center">
        <i class="bi bi-cart-x fs-1 text-be-muted"></i>
        <h5 class="fw-bold text-be-primary mt-3">Your shopping cart is empty</h5>
        <p class="text-be-muted">Browse participating vendors in Barangay San Pedro to add delicious local meals.</p>
        <a href="{{ route('customer.vendors') }}" class="btn btn-be-accent mt-2">Browse Vendors</a>
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="be-card p-4">
                <div class="table-responsive">
                    <table class="table be-table mb-0">
                        <thead>
                            <tr>
                                <th>Food Item</th>
                                <th>Vendor</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $item)
                                <tr>
                                    <td class="fw-bold text-be-primary">{{ $item['name'] }}</td>
                                    <td class="small text-be-muted">{{ $item['vendor_name'] }}</td>
                                    <td>₱{{ number_format($item['price'], 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('customer.cart.update') }}" class="d-flex gap-1">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item['item_id'] }}">
                                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" max="20" class="form-control form-control-sm" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-be-primary">Update</button>
                                        </form>
                                    </td>
                                    <td class="fw-bold">₱{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('customer.cart.remove') }}">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item['item_id'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="be-card p-4">
                <h5 class="fw-bold text-be-primary mb-3">Cart Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-be-muted">Food Subtotal</span>
                    <span class="fw-bold">₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 small text-be-muted">
                    <span>Delivery Fee</span>
                    <span>Calculated by Zone at Checkout</span>
                </div>
                <hr>
                <a href="{{ route('customer.checkout') }}" class="btn btn-be-accent w-100 py-2">
                    Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <a href="{{ route('customer.vendors') }}" class="btn btn-outline-be-primary w-100 mt-2">
                    Add More Items
                </a>
            </div>
        </div>
    </div>
@endif
@endsection