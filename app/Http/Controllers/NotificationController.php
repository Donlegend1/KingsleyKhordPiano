<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $query = auth()->user()->notificationsForArea(false);
        (clone $query)->whereNull('read_at')->update(['read_at' => now()]);
        $notifications = $query->latest()->paginate(20)->withQueryString();

        return view('memberpages.notifications', compact('notifications'));
    }

    public function markAllAsRead(Request $request)
    {
        $request->validate(['area' => 'sometimes|in:community,member']);

        $request->user()->notificationsForArea($request->input('area') === 'community')
            ->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }

    public function openCommunityNotification(Request $request, string $id)
    {
        $notification = $request->user()->notificationsForArea(true)->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data['data'] ?? [];
        $url = parse_url($data['url'] ?? '');
        $path = $url['path'] ?? '';
        // Older notifications stored absolute URLs, which can outlive a domain change.
        preg_match('~^/(?:member/)?post/([1-9][0-9]*)/?$~D', $path, $legacyPost);
        $postId = $data['post_id'] ?? ($legacyPost[1] ?? null);
        if (is_scalar($postId) && preg_match('/^[1-9][0-9]*$/D', (string) $postId)) {
            $commentId = $data['comment_id'] ?? null;
            $fragment = $commentId ? 'comment-' . $commentId : ($url['fragment'] ?? '');
            $destination = route('singlePost', ['post' => $postId], false);
            if (preg_match('/^comment-[1-9][0-9]*$/D', $fragment)) {
                $destination .= '#' . $fragment;
            }

            return redirect()->to($destination, 303);
        }

        if (!str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, chr(92))
            || (isset($url['host']) && $url['host'] !== $request->getHost())) {
            return redirect()->route('community.notifications', [], 303);
        }

        $destination = $path
            . (isset($url['query']) ? '?' . $url['query'] : '')
            . (isset($url['fragment']) ? '#' . $url['fragment'] : '');

        return redirect()->to($destination, 303);
    }

    public function updatePreference(Request $request)
    {
        $request->validate([
            'notification_preference' => ['required', 'in:push,email,disabled'],
        ]);

        if ($request->notification_preference === 'push' && !$request->user()->pushSubscriptions()->exists()) {
            return response()->json(['message' => 'Register a browser for push notifications first.'], 422);
        }

        auth()->user()->update([
            'notification_preference' => $request->notification_preference,
        ]);

        return response()->json(['success' => true]);
    }
}
