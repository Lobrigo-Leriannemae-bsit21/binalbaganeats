<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorStatsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, VendorStatsService $stats): View
    {
        $vendor = $request->user();

        return view('vendor.dashboard', [
            'store' => $vendor->vendorProfile,
            'summary' => $stats->summary($vendor),
            'chart' => $stats->dailySales($vendor, 7),
            'recentOrders' => $stats->recentOrders($vendor, 5),
        ]);
    }
}