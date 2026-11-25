<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles\UserRoles;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\LessonCompletion;
use App\Models\LessonView;
use App\Models\User;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageUserController extends Controller
{
    public function show(Request $request, User $user)
    {
        $this->authorizeAdmin($request);
        $tabs = ['profile' => 'Profile & Activity', 'assessment' => 'Assessment', 'subscription' => 'Subscription'];
        $activeTab = $request->string('tab')->toString();
        $activeTab = isset($tabs[$activeTab]) ? $activeTab : 'profile';
        $assessment = UserAssessment::where('user_id', $user->id)->latest()->orderByDesc('id')->first();
        $country = trim((string) (data_get($user->metadata, 'signup_country') ?? $user->country));
        $country = $country === '' ? null : (DB::table('countries')->where('country_code', strtoupper($country))->value('country_name') ?? $country);
        $data = compact('user', 'tabs', 'activeTab', 'assessment', 'country');

        if ($activeTab === 'profile') {
            $lastSignIn = DB::table('member_sign_in_days')->where('user_id', $user->id)->max('date');
            $roadmap = CourseProgress::where('user_id', $user->id);
            $completed = LessonCompletion::where('user_id', $user->id)->whereNot(function ($query) {
                $query->where('completable_type', Course::class)->whereExists(function ($progress) {
                    $progress->selectRaw('1')->from('course_progress')
                        ->whereColumn('course_progress.user_id', 'lesson_completions.user_id')
                        ->whereColumn('course_progress.course_id', 'lesson_completions.completable_id');
                });
            });
            $completedCount = (clone $roadmap)->count() + (clone $completed)->count();
            $recentCompletions = $roadmap->with('course')->latest()->limit(10)->get()
                ->map(fn ($row) => ['title' => $row->course?->title, 'date' => $row->created_at])
                ->concat($completed->with('completable')->latest()->limit(10)->get()
                    ->map(fn ($row) => ['title' => $row->completable?->title, 'date' => $row->created_at]))
                ->sortByDesc('date')->take(10);
            $recentViews = LessonView::where('user_id', $user->id)->with('viewable')
                ->orderByDesc('updated_at')->limit(10)->get();
            $loginDays = DB::table('member_sign_in_days')->where('user_id', $user->id)->pluck('date')
                ->concat(DB::table('user_daily_logins')->where('user_id', $user->id)->pluck('date'))
                ->map(fn ($date) => substr((string) $date, 0, 10))
                ->unique()->sortDesc()->values();
            $loginDayCount = $loginDays->count();
            $loginHistory = $loginDays->take(30);
            $lastSignIn = $lastSignIn ?: $loginDays->first();
            $data += compact('lastSignIn', 'completedCount', 'recentCompletions', 'recentViews', 'loginHistory', 'loginDayCount');
        }

        if ($activeTab === 'subscription') {
            $subscription = $user->latestLocalSubscription();
            $startsAt = $user->subscription_started_at ?? $subscription?->created_at;
            $endsAt = $user->subscription_expires_at ?? $subscription?->ends_at;
            $status = strtolower((string) ($user->subscription_status ?? $subscription?->stripe_status));
            $accessStatus = match (true) {
                $status === 'paused' => 'Paused',
                in_array($status, ['canceled', 'cancelled'], true) => 'Canceled',
                $user->hasActiveSubscription() => 'Active',
                default => 'Inactive',
            };
            $data += compact('startsAt', 'endsAt', 'accessStatus');
        }

        return view('admin.manage-user', $data);
    }

    public function update(Request $request, User $user, AdminController $admin)
    {
        $this->authorizeAdmin($request);
        $request->request->remove('payment_status');
        $request->query->remove('payment_status');
        $admin->editUser($request, $user);

        $tab = $request->input('section') === 'subscription' ? 'subscription' : 'profile';

        return $this->redirectTo($user, $tab)->with('success', 'User details saved.');
    }

    public function updateAccess(Request $request, User $user, AdminController $admin)
    {
        $this->authorizeAdmin($request);
        $response = $admin->updateSubscriptionAccess($request, $user);

        if (!$response->isSuccessful()) {
            return back()->withInput()->withErrors(['ends_at' => $response->getData(true)['message']]);
        }

        return $this->redirectTo($user, 'subscription')->with('success', $response->getData(true)['message']);
    }

    public function destroy(Request $request, User $user, AdminController $admin)
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 403);
        $admin->destroy($request, $user);

        return redirect('/admin/users')->with('success', 'User deleted.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array($request->user()->role, [UserRoles::ADMIN->value, UserRoles::SUPERADMIN->value], true), 403);
    }

    private function redirectTo(User $user, string $tab)
    {
        return redirect()->route('admin.users.manage', ['user' => $user, 'tab' => $tab]);
    }
}
