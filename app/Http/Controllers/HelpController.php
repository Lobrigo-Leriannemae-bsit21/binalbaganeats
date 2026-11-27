<?php

namespace App\Http\Controllers;

use App\Services\SukiPointService;
use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function __invoke(SukiPointService $suki): View
    {
        return view('help', [
            'faqs' => $this->faqs($suki),
            'icons' => [
                'Ordering' => 'bi-bag',
                'Payments' => 'bi-wallet2',
                'Delivery' => 'bi-truck',
                'Suki Points' => 'bi-gift',
                'Buy-for-Me' => 'bi-bag-heart',
                'Account' => 'bi-person',
            ],
            'contact' => $this->contact(),
        ]);
    }

    /** Contact details come from .env. Invalid or empty values are dropped. */
    private function contact(): array
    {
        $email = config('support.email');
        $phone = config('support.phone');
        $facebook = config('support.facebook');
        $hours = config('support.hours');

        return [
            'email' => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null,
            'phone' => is_string($phone) && trim($phone) !== '' ? trim($phone) : null,
            'phone_link' => is_string($phone) ? preg_replace('/[^0-9+]/', '', $phone) : null,
            'facebook' => is_string($facebook) && preg_match('#^https?://#i', $facebook) && filter_var($facebook, FILTER_VALIDATE_URL) ? $facebook : null,
            'hours' => is_string($hours) && trim($hours) !== '' ? trim($hours) : null,
        ];
    }

    /** Answers use the real values from config, so they never go out of date. */
    private function faqs(SukiPointService $suki): array
    {
        $rules = $suki->rules();
        $fmt = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');

        $earn = number_format($rules['earn_peso_per_point']);
        $value = $fmt($rules['value_pesos']);
        $advance = (int) config('buyforme.advance_percent', 50);
        $overrun = (int) config('buyforme.overrun_percent', 10);
        $openLimit = (int) config('buyforme.max_open_requests', 3);

        return [
            'Ordering' => [
                ['q' => 'How do I place an order?', 'a' => 'Open Vendors, choose a store, add dishes to your cart and check out. At checkout you enter your delivery address, choose your barangay and pick a payment method.'],
                ['q' => 'Can I order from more than one vendor at once?', 'a' => 'Not in a single order. Your cart holds one vendor’s food at a time. If you add food from another vendor, you will be asked whether to start a new cart.'],
                ['q' => 'Why can’t I order from some stores?', 'a' => 'A store may be closed or a dish may be unavailable. You can still browse the menu, but ordering is paused until the store reopens.'],
                ['q' => 'Can I cancel my order?', 'a' => 'Yes, until the vendor accepts it. Open the order from My Orders and choose Cancel order. After that, it can no longer be cancelled in the app.'],
            ],
            'Payments' => [
                ['q' => 'Which payment methods can I use?', 'a' => 'Cash on Delivery (you pay the rider in cash) and GCash. GCash is available only for vendors who have set up a GCash account.'],
                ['q' => 'How does GCash payment work?', 'a' => 'Send the total to the vendor’s GCash number shown at checkout, then enter the reference number from your receipt and upload a screenshot. The vendor checks it manually, because payments are not processed automatically.'],
                ['q' => 'My GCash payment was rejected. What now?', 'a' => 'Open the order from My Orders, check your reference number and screenshot, and submit them again.'],
                ['q' => 'Can I reuse a GCash reference number?', 'a' => 'No. Each reference number can only be used once.'],
                ['q' => 'How do refunds work?', 'a' => 'If you cancelled something you already paid for by GCash, or a Buy-for-Me request after sending the advance, please contact support. Refunds are handled outside the app.'],
            ],
            'Delivery' => [
                ['q' => 'How is the delivery fee calculated?', 'a' => 'By barangay. Choose your barangay at checkout and the fee is shown before you place your order.'],
                ['q' => 'How long will my order take?', 'a' => 'Your order confirmation shows an estimate: the vendor’s preparation time plus the travel time to your barangay.'],
                ['q' => 'How can I contact my rider?', 'a' => 'Once a rider is assigned, their name and phone number appear on your order’s tracking page.'],
            ],
            'Suki Points' => [
                ['q' => 'How do I earn Suki Points?', 'a' => "You earn 1 point for every ₱{$earn} of food in your order (the delivery fee does not count), on both Cash on Delivery and GCash orders. Points are whole numbers, and cancelled orders earn nothing."],
                ['q' => 'How do I use my points?', 'a' => "Each point is worth ₱{$value} off. At checkout, tick Use my Suki Points. You must use at least {$rules['min_redeem']} points, covering up to {$rules['max_discount_percent']}% of the food subtotal. Delivery fees can’t be discounted."],
                ['q' => 'Why are some of my points pending?', 'a' => 'Points from an order stay pending until it is delivered. Points you use are held until delivery and are returned if the order is cancelled or declined.'],
                ['q' => 'Do Suki Points expire?', 'a' => 'No. Your points never expire.'],
            ],
            'Buy-for-Me' => [
                ['q' => 'What is Buy-for-Me?', 'a' => 'You name a shop and the items you want, and a rider in your barangay buys them and delivers them to you.'],
                ['q' => 'How does payment work?', 'a' => "After a rider accepts, they confirm the estimated cost. You then send {$advance}% of the estimated total (items plus delivery fee) in advance by GCash. The rider buys your items, and you pay the remaining balance when they arrive. If the items cost less than your advance, the rider returns the change."],
                ['q' => 'What if the items cost more than the estimate?', 'a' => "The rider can record at most {$overrun}% above the estimate. If it costs more, the rider will contact you first, and an admin adjusts the amount."],
                ['q' => 'Can I cancel a request?', 'a' => "Yes, until you send the advance payment. After that, please contact support. You can have up to {$openLimit} open requests at a time."],
            ],
            'Account' => [
                ['q' => 'How do I change my address or phone number?', 'a' => 'Open your name in the top menu, then Profile (name and phone) or Delivery address (address and preferred barangay).'],
                ['q' => 'Can I change my email address?', 'a' => 'Not in the app yet. Please contact support.'],
                ['q' => 'How do I change my password?', 'a' => 'Go to your account, then Change password. You need your current password, and your other devices will be signed out.'],
            ],
        ];
    }
}