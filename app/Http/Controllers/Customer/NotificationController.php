<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        return view('customer.notifications.index', [
            'notifications' => $query->paginate(10)->withQueryString(),
            'filter' => $filter,
            'unreadCount' => $user->notifications()->whereNull('read_at')->count(),
        ]);
    }

    /** Marks one notification read and opens what it is about. Only the owner's notifications are reachable. */
    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();

        $item->markAsRead();

        $url = $item->data['url'] ?? null;

        // Only ever redirect to a path on this site (blocks "//evil.example" and full addresses).
        $safe = is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//');

        return redirect()->to($safe ? $url : route('notifications.index'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $user = $request->user();

        DatabaseNotification::query()
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')->with('success', 'All notifications were marked as read.');
    }
}