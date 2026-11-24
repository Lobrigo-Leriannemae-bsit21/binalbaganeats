<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    private function initializeRiderSession(): void
    {
        if (!session()->has('rider_jobs')) {
            session([
                'rider_jobs' => [
                    [
                        'order_number' => 'BE-2026-1048',
                        'vendor' => 'San Pedro Batchoy & Grill',
                        'customer' => 'Maria Santos',
                        'zone' => Auth::user()->preferred_delivery_area ?? 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'address' => 'Zone 1, Poblacion, Binalbagan',
                        'items' => '2x Super Special Batchoy',
                        'delivery_fee' => 25.00,
                        'status' => 'Assigned',
                        'rider' => Auth::user()->name,
                        'created_at' => '10 mins ago',
                    ],
                    [
                        'order_number' => 'BE-2026-1052',
                        'vendor' => 'Nanay Rosa’s Lutong Bahay',
                        'customer' => 'Juan Dela Cruz',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'address' => 'Market Side, Brgy. San Pedro',
                        'items' => '1x Kadyos, Baboy, Langka (KBL)',
                        'delivery_fee' => 25.00,
                        'status' => 'Ready for Pickup',
                        'rider' => null,
                        'created_at' => '5 mins ago',
                    ],
                ],
                'rider_buy_for_me' => [
                    [
                        'request_id' => 'BFM-2026-088',
                        'customer' => 'Lerianne Mae Lobrigo',
                        'establishment' => 'Binalbagan Public Market Fruit Stand #4',
                        'items' => '2 kg Lacatan Bananas, 1 kg Sweet Mangoes',
                        'budget' => 300.00,
                        'actual_expense' => 265.00,
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'delivery_address' => 'Zone 2, Brgy. San Pedro, Binalbagan',
                        'rider' => Auth::user()->name,
                        'status' => 'Picked Up',
                        'settlement_status' => 'Pending Reimbursement upon Delivery',
                        'created_at' => '12 mins ago',
                    ],
                    [
                        'request_id' => 'BFM-2026-091',
                        'customer' => 'Carlo Mendoza',
                        'establishment' => 'Poblacion Hardware & Supplies',
                        'items' => '1x PVC Pipe 1/2 inch, 2x PVC Elbow',
                        'budget' => 180.00,
                        'actual_expense' => null,
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'delivery_address' => 'Enclaro Road, Binalbagan',
                        'rider' => null,
                        'status' => 'Open Request',
                        'settlement_status' => 'Unassigned',
                        'created_at' => '25 mins ago',
                    ],
                ],
            ]);
        }
    }

    public function dashboard()
    {
        $this->initializeRiderSession();
        $jobs = session('rider_jobs', []);
        $errands = session('rider_buy_for_me', []);

        $assignedJobs = array_filter($jobs, fn($j) => $j['rider'] === Auth::user()->name && !in_array($j['status'], ['Delivered', 'Cancelled']));
        $openJobs = array_filter($jobs, fn($j) => empty($j['rider']) && $j['status'] === 'Ready for Pickup');
        $assignedErrands = array_filter($errands, fn($e) => $e['rider'] === Auth::user()->name && $e['status'] !== 'Delivered');

        return view('rider.dashboard', [
            'assignedCount' => count($assignedJobs),
            'availableCount' => count($openJobs),
            'errandCount' => count($assignedErrands),
            'approvedArea' => Auth::user()->preferred_delivery_area ?? 'Zone 1 - Brgy. San Pedro',
            'assignedJobs' => $assignedJobs,
        ]);
    }

    public function availableJobs()
    {
        $this->initializeRiderSession();
        $jobs = array_filter(session('rider_jobs', []), fn($j) => empty($j['rider']) && $j['status'] === 'Ready for Pickup');

        return view('rider.available-jobs', compact('jobs'));
    }

    public function acceptJob($orderNumber)
    {
        $this->initializeRiderSession();
        $jobs = session('rider_jobs', []);

        foreach ($jobs as &$job) {
            if ($job['order_number'] === $orderNumber && empty($job['rider'])) {
                $job['rider'] = Auth::user()->name;
                $job['status'] = 'Assigned';
                break;
            }
        }
        unset($job);

        session(['rider_jobs' => $jobs]);
        return back()->with('success', "Delivery request #{$orderNumber} successfully accepted and assigned to you!");
    }

    public function assignedDeliveries()
    {
        $this->initializeRiderSession();
        $jobs = array_filter(session('rider_jobs', []), fn($j) => $j['rider'] === Auth::user()->name);

        return view('rider.assigned', compact('jobs'));
    }

    public function deliveryDetails($orderNumber = null)
    {
        $this->initializeRiderSession();
        $jobs = session('rider_jobs', []);
        $selected = $orderNumber
            ? collect($jobs)->firstWhere('order_number', $orderNumber)
            : collect($jobs)->firstWhere('rider', Auth::user()->name);

        return view('rider.details', compact('jobs', 'selected'));
    }

    public function updateDeliveryStatus(Request $request, $orderNumber)
    {
        $this->initializeRiderSession();
        $request->validate([
            'status' => 'required|in:Assigned,Picked Up,Delivered',
        ]);

        $jobs = session('rider_jobs', []);
        foreach ($jobs as &$job) {
            if ($job['order_number'] === $orderNumber) {
                $job['status'] = $request->status;
                break;
            }
        }
        unset($job);

        session(['rider_jobs' => $jobs]);
        return back()->with('success', "Delivery #{$orderNumber} status updated to {$request->status}.");
    }

    public function buyForMeRequests()
    {
        $this->initializeRiderSession();
        $errands = session('rider_buy_for_me', []);

        return view('rider.buy-for-me', compact('errands'));
    }

    public function acceptBuyForMe($requestId)
    {
        $this->initializeRiderSession();
        $errands = session('rider_buy_for_me', []);

        foreach ($errands as &$errand) {
            if ($errand['request_id'] === $requestId && empty($errand['rider'])) {
                $errand['rider'] = Auth::user()->name;
                $errand['status'] = 'Accepted';
                $errand['settlement_status'] = 'Pending Out-of-Pocket Purchase';
                break;
            }
        }
        unset($errand);

        session(['rider_buy_for_me' => $errands]);
        return back()->with('success', "Buy-for-Me request #{$requestId} accepted!");
    }

    public function recordExpense(Request $request, $requestId)
    {
        $this->initializeRiderSession();
        $request->validate([
            'actual_expense' => 'required|numeric|min:1',
        ]);

        $errands = session('rider_buy_for_me', []);
        foreach ($errands as &$errand) {
            if ($errand['request_id'] === $requestId) {
                if ($request->actual_expense > $errand['budget']) {
                    return back()->with('error', 'Expense cannot exceed customer maximum budget (' . $errand['budget'] . ').');
                }
                $errand['actual_expense'] = (float) $request->actual_expense;
                $errand['status'] = 'Purchasing / Picked Up';
                $errand['settlement_status'] = 'Pending Customer Reimbursement';
                break;
            }
        }
        unset($errand);

        session(['rider_buy_for_me' => $errands]);
        return back()->with('success', "Actual expense ₱" . number_format($request->actual_expense, 2) . " recorded for #{$requestId}.");
    }

    public function updateSettlement(Request $request, $requestId)
    {
        $this->initializeRiderSession();
        $errands = session('rider_buy_for_me', []);

        foreach ($errands as &$errand) {
            if ($errand['request_id'] === $requestId) {
                $errand['settlement_status'] = 'Settled & Reimbursed';
                $errand['status'] = 'Delivered';
                break;
            }
        }
        unset($errand);

        session(['rider_buy_for_me' => $errands]);
        return back()->with('success', "Settlement for #{$requestId} marked as Settled & Reimbursed.");
    }

    public function expenses()
    {
        $this->initializeRiderSession();
        $errands = array_filter(session('rider_buy_for_me', []), fn($e) => $e['rider'] === Auth::user()->name && !is_null($e['actual_expense']));
        $totalOut = array_sum(array_column($errands, 'actual_expense'));

        return view('rider.expenses', compact('errands', 'totalOut'));
    }

   public function history()
    {
        $this->initializeRiderSession();
        $jobs = array_filter(session('rider_jobs', []), fn($j) => $j['rider'] === Auth::user()->name && $j['status'] === 'Delivered');

        return view('rider.history', compact('jobs'));
    }

    public function notifications()
    {
        $notifications = [
            ['title' => 'New Delivery Job Available', 'message' => 'San Pedro Batchoy & Grill order #BE-2026-1052 is ready for pickup in your approved zone.', 'time' => '5 mins ago', 'unread' => true],
            ['title' => 'New Buy-for-Me Errand Request', 'message' => 'Carlo Mendoza submitted an errand request at Poblacion Hardware & Supplies (Budget: ₱180.00).', 'time' => '25 mins ago', 'unread' => true],
        ];

        return view('rider.notifications', compact('notifications'));
    }

    public function profile()
    {
        return view('rider.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'preferred_delivery_area' => 'required|string|max:255',
        ]);

        $user->update($validated);
        return back()->with('success', 'Rider profile updated.');
    }
}