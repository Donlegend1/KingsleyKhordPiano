<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveCoachingBooking;
use App\Models\PersonalizedGuidanceRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonalizedGuidanceController extends Controller
{
    public function index()
    {
        $requests = PersonalizedGuidanceRequest::with('user')->get()->keyBy('user_id');
        $bookings = LiveCoachingBooking::with('user')->orderByDesc('date')->orderByDesc('time')->get()->groupBy('user_id');

        $userIds = $requests->keys()->merge($bookings->keys())->unique();

        $entries = $userIds->map(function ($userId) use ($requests, $bookings) {
            $request = $requests->get($userId);
            $userBookings = $bookings->get($userId, collect());

            return (object) [
                'user' => optional($request)->user ?? optional($userBookings->first())->user,
                'request' => $request,
                'bookings' => $userBookings,
                'latestAt' => collect([
                    optional($request)->created_at,
                    optional($userBookings->first())->created_at,
                ])->filter()->max(),
            ];
        })->filter(fn ($entry) => $entry->user)->sortByDesc('latestAt')->values();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $entries->forPage($page, $perPage)->values(),
            $entries->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.personalized_guidance.index', ['entries' => $paginated]);
    }

    public function show(User $user)
    {
        $guidanceRequest = PersonalizedGuidanceRequest::where('user_id', $user->id)->latest()->first();
        $bookings = LiveCoachingBooking::where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('admin.personalized_guidance.show', [
            'user' => $user,
            'request' => $guidanceRequest,
            'bookings' => $bookings,
        ]);
    }

    public function markReviewed(PersonalizedGuidanceRequest $guidanceRequest)
    {
        $guidanceRequest->update(['status' => 'reviewed']);
        return back()->with('success', 'Marked as reviewed.');
    }
}
