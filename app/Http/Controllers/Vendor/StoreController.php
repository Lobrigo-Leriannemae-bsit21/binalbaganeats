<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdatePaymentRequest;
use App\Http\Requests\Vendor\UpdateStoreImageRequest;
use App\Http\Requests\Vendor\UpdateStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Every action works on the SIGNED-IN vendor's own store. No store id is ever taken from the browser. */
class StoreController extends Controller
{
    private const IMAGE_FOLDER = 'vendor-images';

    public function edit(Request $request): View
    {
        return view('vendor.store.edit', ['store' => $request->user()->vendorProfile]);
    }

    public function update(UpdateStoreRequest $request): RedirectResponse
    {
        $vendor = $request->user();
        $data = $request->validated();

        DB::transaction(function () use ($vendor, $data) {
            $vendor->vendorProfile->update($data);

            // Keep the duplicate name column on users in step (vendor_profiles is the source of truth).
            $vendor->forceFill(['storefront_name' => $data['business_name']])->save();
        });

        return redirect()->route('vendor.store.edit')->with('success', 'Your store information was updated.');
    }

    public function updateImage(UpdateStoreImageRequest $request): RedirectResponse
    {
        $store = $request->user()->vendorProfile;
        $old = $store->vendor_image;

        // Stored on the public disk with a random, server-chosen name.
        $path = $request->file('store_image')->store(self::IMAGE_FOLDER, 'public');

        $store->update(['vendor_image' => $path]);

        $this->deleteImage($old);

        return redirect()->route('vendor.store.edit')->with('success', 'Your store image was updated.');
    }

    public function destroyImage(Request $request): RedirectResponse
    {
        $store = $request->user()->vendorProfile;

        if ($store->vendor_image === null) {
            return redirect()->route('vendor.store.edit')->with('status', 'There is no store image to remove.');
        }

        $old = $store->vendor_image;
        $store->update(['vendor_image' => null]);

        $this->deleteImage($old);

        return redirect()->route('vendor.store.edit')->with('success', 'Your store image was removed.');
    }

    public function updatePayment(UpdatePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->vendorProfile->update([
            'gcash_number' => $data['gcash_number'] ?? null,
            'gcash_name' => $data['gcash_name'] ?? null,
        ]);

        return redirect()->route('vendor.store.edit')->with(
            'success',
            filled($data['gcash_number'] ?? null)
                ? 'Your GCash details were saved. Customers can now pay you by GCash.'
                : 'GCash was turned off. Customers can only pay you by Cash on Delivery.'
        );
    }

    public function updateAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate(['is_available' => ['required', 'boolean']]);

        $open = (bool) $validated['is_available'];

        $request->user()->vendorProfile->update(['is_available' => $open]);

        return back()->with(
            'success',
            $open ? 'Your store is now open for orders.' : 'Your store is now closed. Customers can browse but not order.'
        );
    }

    /** Deletes a file only if it lives in our own image folder. */
    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, self::IMAGE_FOLDER.'/')) {
            Storage::disk('public')->delete($path);
        }
    }
}