<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $communitySection = \App\Enums\Notification\NotificationSectionEnum::COMMUNITY->value;

        $notifications = auth()->user()->notifications()
            ->latest()
            ->get()
            ->filter(function ($n) use ($communitySection) {
                $section = $n->data['data']['section'] ?? null;
                return $section !== $communitySection;
            })
            ->values();

        // Mark all filtered (non-community) notifications as read
        $unreadIds = $notifications->whereNull('read_at')->pluck('id');
        auth()->user()->notifications()->whereIn('id', $unreadIds)->update(['read_at' => now()]);

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedNotifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $notifications->forPage($page, $perPage),
            $notifications->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $notifications = $paginatedNotifications;

        return view('memberpages.notifications', compact('notifications'));
    }

    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
