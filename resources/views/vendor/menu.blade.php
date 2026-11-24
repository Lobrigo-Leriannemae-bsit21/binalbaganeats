@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Menu Management')
@section('page-title', 'Menu Items & Pricing')
@section('page-subtitle', 'Add, edit, toggle availability, or remove menu items')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold text-be-primary mb-0">Menu Catalog ({{ count($menu) }} items)</h6>
    <button class="btn btn-be-accent" data-bs-toggle="modal" data-bs-target="#addMenuModal">
        <i class="bi bi-plus-lg me-1"></i> Add Menu Item
    </button>
</div>

<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Dish Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Prep Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($menu as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-be-primary">{{ $item['name'] }}</div>
                            <small class="text-be-muted">{{ $item['desc'] }}</small>
                        </td>
                        <td><span class="badge bg-be-sage text-be-primary">{{ $item['category'] }}</span></td>
                        <td class="fw-bold">₱{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['prep'] }} mins</td>
                        <td><x-badge :status="$item['available'] ? 'Available' : 'Unavailable'" /></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-be-primary" data-bs-toggle="modal" data-bs-target="#editMenuModal{{ $item['id'] }}">Edit</button>
                                <form method="POST" action="{{ route('vendor.menu.delete', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>

                            <x-modal id="editMenuModal{{ $item['id'] }}" title="Edit {{ $item['name'] }}">
                                <form method="POST" action="{{ route('vendor.menu.update', $item['id']) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Item Name</label>
                                        <input type="text" name="name" value="{{ $item['name'] }}" class="form-control" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">Price (₱)</label>
                                            <input type="number" step="0.01" name="price" value="{{ $item['price'] }}" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-semibold">Prep Time (mins)</label>
                                            <input type="number" name="prep" value="{{ $item['prep'] }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="available" value="1" id="avail{{ $item['id'] }}" {{ $item['available'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="avail{{ $item['id'] }}">Item Available for Ordering</label>
                                    </div>
                                    <button type="submit" class="btn btn-be-primary w-100">Update Item</button>
                                </form>
                            </x-modal>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<x-modal id="addMenuModal" title="Add New Menu Item">
    <form method="POST" action="{{ route('vendor.menu.add') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Food Item Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Category</label>
                <input type="text" name="category" value="Main Viand" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Price (₱)</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Prep Time (mins)</label>
                <input type="number" name="prep" value="15" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Short Description</label>
            <textarea name="desc" rows="2" class="form-control" required></textarea>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="available" value="1" id="newItemAvail" checked>
            <label class="form-check-label" for="newItemAvail">Available Immediately</label>
        </div>
        <button type="submit" class="btn btn-be-accent w-100">Save to Menu</button>
    </form>
</x-modal>
@endsection