<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function initializeAdminSession(): void
    {
        if (!session()->has('admin_zones')) {
            session([
                'admin_zones' => [
                    ['id' => 'Z1', 'name' => 'Zone 1 - Brgy. San Pedro & Poblacion Core', 'distance' => '0 – 2.0 km', 'fee' => 25.00, 'travel_mins' => 10, 'status' => 'Active'],
                    ['id' => 'Z2', 'name' => 'Zone 2 - Brgy. Progreso & Marina', 'distance' => '2.1 – 4.0 km', 'fee' => 35.00, 'travel_mins' => 15, 'status' => 'Active'],
                    ['id' => 'Z3', 'name' => 'Zone 3 - Brgy. Canmoros & Enclaro', 'distance' => '4.1 – 6.5 km', 'fee' => 45.00, 'travel_mins' => 20, 'status' => 'Active'],
                    ['id' => 'Z4', 'name' => 'Zone 4 - Brgy. Paglaum & Santol', 'distance' => '6.6 – 9.0 km', 'fee' => 55.00, 'travel_mins' => 25, 'status' => 'Active'],
                    ['id' => 'Z5', 'name' => 'Zone 5 - Outer Barangays (Payao / Bi-ao)', 'distance' => '9.1 – 14.0 km', 'fee' => 70.00, 'travel_mins' => 35, 'status' => 'Active'],
                ],
                'admin_logs' => [
                    ['timestamp' => 'Nov 19, 2026 05:45 PM', 'actor' => 'Local Food Vendor (Nanay Rosa)', 'action' => 'Verified GCash reference GC-908471992 for Order #BE-2026-1055', 'module' => 'Process 5.0'],
                    ['timestamp' => 'Nov 19, 2026 05:30 PM', 'actor' => 'Delivery Rider (Cyrus Ace Taborite)', 'action' => 'Accepted Delivery Request #BE-2026-1048 in Zone 1', 'module' => 'Process 4.0'],
                    ['timestamp' => 'Nov 19, 2026 05:12 PM', 'actor' => 'Customer (Lerianne Mae Lobrigo)', 'action' => 'Submitted Buy-for-Me Request #BFM-2026-088 (Budget: ₱300.00)', 'module' => 'Process 4.0'],
                    ['timestamp' => 'Nov 19, 2026 04:50 PM', 'actor' => 'System Administrator', 'action' => 'Updated Zone 2 travel-time estimate to 15 mins', 'module' => 'Process 6.0'],
                ],
                'admin_transactions' => [
                    ['order_number' => 'BE-2026-1055', 'type' => 'Food Order', 'customer' => 'Lerianne Mae Lobrigo', 'vendor_or_store' => 'Nanay Rosa’s Lutong Bahay', 'amount' => 270.00, 'method' => 'GCash', 'status' => 'Verified', 'flagged' => false],
                    ['order_number' => 'BE-2026-1051', 'type' => 'Food Order', 'customer' => 'Mark Anthony Reyes', 'vendor_or_store' => 'San Pedro Batchoy & Grill', 'amount' => 225.00, 'method' => 'Cash on Delivery', 'status' => 'Pending', 'flagged' => false],
                    ['order_number' => 'BFM-2026-088', 'type' => 'Buy-for-Me', 'customer' => 'Lerianne Mae Lobrigo', 'vendor_or_store' => 'Binalbagan Public Market Fruit Stand #4', 'amount' => 265.00, 'method' => 'External Reimbursement', 'status' => 'Picked Up', 'flagged' => false],
                    ['order_number' => 'BE-2026-1039', 'type' => 'Food Order', 'customer' => 'Paolo Garcia', 'vendor_or_store' => 'San Pedro Batchoy & Grill', 'amount' => 185.00, 'method' => 'GCash', 'status' => 'Flagged', 'flagged' => true],
                ],
            ]);
        }
    }

    public function dashboard()
    {
        $this->initializeAdminSession();

        $totalUsers = User::count();
        $pendingCount = User::whereIn('role', ['vendor', 'rider'])->where('status', 'pending')->count();
        $approvedVendors = User::where('role', 'vendor')->where('status', 'approved')->count();
        $approvedRiders = User::where('role', 'rider')->where('status', 'approved')->count();
        $pendingUsers = User::whereIn('role', ['vendor', 'rider'])->where('status', 'pending')->latest()->take(5)->get();
        $logs = array_slice(session('admin_logs', []), 0, 4);

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingCount',
            'approvedVendors',
            'approvedRiders',
            'pendingUsers',
            'logs'
        ));
    }

    public function accounts(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->get();
        return view('admin.accounts', compact('users'));
    }

    public function pendingApprovals()
    {
        $pendingUsers = User::whereIn('role', ['vendor', 'rider'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.approvals', compact('pendingUsers'));
    }

    public function approveAccount(Request $request, User $user)
    {
        $this->initializeAdminSession();

        if ($user->role === 'rider' && $request->filled('approved_area')) {
            $user->preferred_delivery_area = $request->approved_area;
        }

        $user->status = 'approved';
        $user->save();

        $logs = session('admin_logs', []);
        array_unshift($logs, [
            'timestamp' => 'Just now',
            'actor' => 'System Administrator',
            'action' => "Approved {$user->role} registration for {$user->name}" . ($user->role === 'rider' ? " (Zone: {$user->preferred_delivery_area})" : ''),
            'module' => 'Process 1.3',
        ]);
        session(['admin_logs' => $logs]);

        return back()->with('success', "{$user->name} ({$user->role}) has been approved and can now log in.");
    }

    public function rejectAccount(User $user)
    {
        $this->initializeAdminSession();
        $user->status = 'rejected';
        $user->save();

        $logs = session('admin_logs', []);
        array_unshift($logs, [
            'timestamp' => 'Just now',
            'actor' => 'System Administrator',
            'action' => "Rejected {$user->role} registration for {$user->name}",
            'module' => 'Process 1.3',
        ]);
        session(['admin_logs' => $logs]);

        return back()->with('success', "{$user->name}'s registration has been marked as rejected.");
    }

    public function vendors()
    {
        $vendors = User::where('role', 'vendor')->latest()->get();
        return view('admin.vendors', compact('vendors'));
    }

    public function riders()
    {
        $this->initializeAdminSession();
        $riders = User::where('role', 'rider')->latest()->get();
        $zones = session('admin_zones', []);

        return view('admin.riders', compact('riders', 'zones'));
    }

    public function zones()
    {
        $this->initializeAdminSession();
        return view('admin.zones', ['zones' => session('admin_zones', [])]);
    }

    public function saveZone(Request $request)
    {
        $this->initializeAdminSession();
        $validated = $request->validate([
            'id' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'distance' => 'required|string|max:50',
            'fee' => 'required|numeric|min:10|max:500',
            'travel_mins' => 'required|integer|min:5|max:120',
        ]);

        $zones = session('admin_zones', []);
        $updated = false;

        foreach ($zones as &$z) {
            if (!empty($validated['id']) && $z['id'] === $validated['id']) {
                $z['name'] = $validated['name'];
                $z['distance'] = $validated['distance'];
                $z['fee'] = (float) $validated['fee'];
                $z['travel_mins'] = (int) $validated['travel_mins'];
                $updated = true;
                break;
            }
        }
        unset($z);

        if (!$updated) {
            $zones[] = [
                'id' => 'Z' . (count($zones) + 1),
                'name' => $validated['name'],
                'distance' => $validated['distance'],
                'fee' => (float) $validated['fee'],
                'travel_mins' => (int) $validated['travel_mins'],
                'status' => 'Active',
            ];
        }

        session(['admin_zones' => $zones]);
        return back()->with('success', 'Delivery zone configuration saved and updated for ETA & fee calculations.');
    }

    public function fees()
    {
        $this->initializeAdminSession();
        return view('admin.fees', ['zones' => session('admin_zones', [])]);
    }

    public function updateFee(Request $request)
    {
        $this->initializeAdminSession();
        $validated = $request->validate([
            'zone_id' => 'required|string',
            'fee' => 'required|numeric|min:10|max:500',
            'travel_mins' => 'required|integer|min:5|max:120',
        ]);

        $zones = session('admin_zones', []);
        foreach ($zones as &$z) {
            if ($z['id'] === $validated['zone_id']) {
                $z['fee'] = (float) $validated['fee'];
                $z['travel_mins'] = (int) $validated['travel_mins'];
                break;
            }
        }
        unset($z);

        session(['admin_zones' => $zones]);
        return back()->with('success', "Updated delivery fee and travel time for {$validated['zone_id']}.");
    }

    public function monitoring(Request $request)
    {
        $this->initializeAdminSession();
        $tab = $request->query('view', 'orders');
        $transactions = session('admin_transactions', []);
        $flagged = array_filter($transactions, fn($t) => $t['flagged']);

        return view('admin.monitoring', compact('tab', 'transactions', 'flagged'));
    }

    public function logs()
    {
        $this->initializeAdminSession();
        return view('admin.logs', ['logs' => session('admin_logs', [])]);
    }

    public function transactions()
    {
        $this->initializeAdminSession();
        return view('admin.transactions', ['transactions' => session('admin_transactions', [])]);
    }

    public function reports()
    {
        $this->initializeAdminSession();
        $transactions = session('admin_transactions', []);
        $totalVolume = array_sum(array_column($transactions, 'amount'));

        return view('admin.reports', [
            'totalVolume' => $totalVolume,
            'orderCount' => count($transactions),
            'customerCount' => User::where('role', 'customer')->count(),
            'vendorCount' => User::where('role', 'vendor')->where('status', 'approved')->count(),
            'riderCount' => User::where('role', 'rider')->where('status', 'approved')->count(),
        ]);
    }

    public function profile()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($validated);
        return back()->with('success', 'Administrator profile updated.');
    }
}