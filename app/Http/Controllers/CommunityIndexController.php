<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\MidiFile;
use App\Models\PDFDownload;
use App\Models\AudioDownload;
use App\Models\UserAssessment;
use App\Models\LearnSong;
use App\Models\ExtraCourse;
use App\Models\Course;
use App\Models\Feedback;
use App\Models\PostComment;
use App\Models\PostReply;
use App\Models\Liveshow;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CommunityIndexController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Check assessment
        $assessment = UserAssessment::where('user_id', $userId)->latest()->first();

        // Calculate progress statistics
        $levels = ['Beginner', 'Intermediate', 'Advanced'];
        $progress = collect($levels)->mapWithKeys(function ($level) use ($userId) {
            $total = DB::table('courses')
                ->where('level', $level)
                ->count();

            $completed = DB::table('course_progress')
                ->join('courses', 'course_progress.course_id', '=', 'courses.id')
                ->where('course_progress.user_id', $userId)
                ->where('courses.level', $level)
                ->distinct('course_progress.course_id')
                ->count('course_progress.course_id');

            return [$level => [
                'total' => $total,
                'completed' => $completed,
            ]];
        })->toArray();

        $totalCompleted = DB::table('course_progress')
            ->where('user_id', $userId)
            ->distinct('course_id')
            ->count('course_id');

        // Milestones
        $milestonesList = [
            ['name' => 'Starter', 'lessons' => 1],
            ['name' => 'Player', 'lessons' => 4],
            ['name' => 'Performer', 'lessons' => 9],
            ['name' => 'Artist', 'lessons' => 14],
            ['name' => 'Maestro', 'lessons' => 21],
            ['name' => 'Master', 'lessons' => 31],
            ['name' => 'Grand Master', 'lessons' => 43],
            ['name' => 'Composer', 'lessons' => 58],
            ['name' => 'Conductor', 'lessons' => 76],
            ['name' => 'Virtuoso', 'lessons' => 96],
            ['name' => 'Prodigy', 'lessons' => 121],
            ['name' => 'Piano Legend', 'lessons' => 151],
        ];

        $achievedCount = 0;
        foreach ($milestonesList as $ms) {
            if ($totalCompleted >= $ms['lessons']) {
                $achievedCount++;
            }
        }

        // Resume Last Lesson
        $resumeLesson = null;
        $resumeUrl = '#';
        $lastBookmark = $user->bookmarks()
            ->whereIn('bookmarkable_type', ['App\Models\Upload', 'App\Models\LearnSong', 'App\Models\ExtraCourse', 'App\Models\Course'])
            ->latest()
            ->first();

        if ($lastBookmark && $lastBookmark->bookmarkable) {
            $resumeLesson = $lastBookmark->bookmarkable;
            $resumeType = $lastBookmark->bookmarkable_type;
            $resumeUrl = ($resumeType === 'App\Models\Course') 
                ? '/member/course/' . $resumeLesson->level . '?selected_course=' . $resumeLesson->id 
                : '/member/lesson/' . $resumeLesson->id;
        } else {
            $resumeLesson = LearnSong::where('status', 'active')->first() 
                ?? ExtraCourse::where('status', 'active')->first() 
                ?? Course::where('status', 'active')->first();
            if ($resumeLesson) {
                $resumeType = get_class($resumeLesson);
                $resumeUrl = ($resumeType === 'App\Models\Course') 
                    ? '/member/course/' . $resumeLesson->level . '?selected_course=' . $resumeLesson->id 
                    : '/member/lesson/' . $resumeLesson->id;
            }
        }

        $feedbackList = Feedback::with('user')->latest()->take(20)->get();

        return view('community.index', compact(
            'user',
            'assessment',
            'progress',
            'totalCompleted',
            'achievedCount',
            'resumeLesson',
            'resumeUrl',
            'feedbackList'
        ));
    }

    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        if (empty($validated['rating']) && empty($validated['comment'])) {
            return response()->json(['message' => 'Please add a rating or comment.'], 422);
        }

        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'] ?? null,
            'comment' => $validated['comment'] ?? null,
        ]);

        $feedback->load('user');

        return response()->json([
            'message' => 'Feedback received',
            'feedback' => [
                'id' => $feedback->id,
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
                'created_at' => $feedback->created_at->diffForHumans(),
                'user' => [
                    'name' => trim($feedback->user->first_name . ' ' . $feedback->user->last_name),
                    'passport' => $feedback->user->passport,
                ],
            ],
        ], 201);
    }

   public function space()
   {
      return view('community.space');
   }

   public function members()
   {
       return view('community.members');
   }

   public function activityFeed()
   {
      return view('community.activity-feed', $this->communitySidebarData());
   }

   public function announcement()
   {
      return view('community.announcement');
   }

   public function sayHello()
   {
      return view('community.say-hello');
   }

   protected function forumCategoryDefinitions(): array
   {
      return [
         'beginner_guided_practice' => [
            'title' => 'Beginner Group',
            'description' => 'Connect with other Beginner Users, Ask Questions, and Submit Videos Here.',
         ],
         'intermediate_guided_practice' => [
            'title' => 'Intermediate Group',
            'description' => 'Connect with other Intermediate Users, Ask Questions, and Submit Videos Here.',
         ],
         'advanced_guided_practice' => [
            'title' => 'Advanced Group',
            'description' => 'Connect with other Advanced Users, Ask Questions, and Submit Videos Here.',
         ],
      ];
   }

   protected function communityCategoryDefinitions(): array
   {
      return [
         'student_challenges' => [
            'title' => 'Student Challenges',
            'description' => 'React and post your submissions to the monthly piano challenge Kingsley gives.',
         ],
         'progress_report' => [
            'title' => 'Progress Report',
            'description' => 'Share updates on how your practice is going.',
         ],
         'public_pledges' => [
            'title' => 'Public Pledges',
            'description' => 'Commit to a goal publicly and let the community help keep you accountable.',
         ],
         'workspace_showcase' => [
            'title' => 'Workspace Showcase',
            'description' => 'Show off your piano setup and practice space.',
         ],
      ];
   }

   public function forum()
   {
      $forumCategories = collect($this->forumCategoryDefinitions())
         ->map(function ($category, $subcategory) {
            return array_merge($category, [
               'subcategory' => $subcategory,
               'count' => Post::where('subcategory', $subcategory)->count(),
            ]);
         })
         ->values();

      $communityCategories = collect($this->communityCategoryDefinitions())
         ->map(function ($category, $subcategory) {
            return array_merge($category, [
               'subcategory' => $subcategory,
               'count' => Post::where('subcategory', $subcategory)->count(),
            ]);
         })
         ->values();

      return view('community.forum', array_merge(
         $this->communitySidebarData(),
         compact('forumCategories', 'communityCategories')
      ));
   }

   public function forumCategory(Request $request, string $subcategory)
   {
      $definitions = array_merge(
         $this->forumCategoryDefinitions(),
         $this->communityCategoryDefinitions()
      );

      abort_unless(isset($definitions[$subcategory]), 404);

      $category = $definitions[$subcategory];
      $category['subcategory'] = $subcategory;
      $category['count'] = Post::where('subcategory', $subcategory)->whereNull('parent_post_id')->count();
      $category['followers_count'] = \App\Models\TopicFollow::where('subcategory', $subcategory)->count();
      $category['is_following'] = \App\Models\TopicFollow::where('subcategory', $subcategory)
         ->where('user_id', auth()->id())
         ->exists();

      $categoryStats = $this->forumCategoryStats($subcategory);

      if ($subcategory === 'student_challenges') {
         $topics = $this->forumCategoryTopics($subcategory, $request->get('sort', 'latest'));

         return response()
            ->view('community.forum-category', array_merge(
               compact('category', 'categoryStats', 'topics'),
               $this->communitySidebarData()
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
      }

      return view('community.forum-category', compact('category', 'categoryStats'));
   }

   public function markAllTopicsRead(string $subcategory)
   {
      $postIds = Post::where('subcategory', $subcategory)->pluck('id');

      $alreadyRead = \App\Models\PostRead::where('user_id', auth()->id())
         ->whereIn('post_id', $postIds)
         ->pluck('post_id');

      $rows = $postIds->diff($alreadyRead)->map(fn ($postId) => [
         'user_id' => auth()->id(),
         'post_id' => $postId,
         'created_at' => now(),
         'updated_at' => now(),
      ]);

      if ($rows->isNotEmpty()) {
         \App\Models\PostRead::insert($rows->all());
      }

      return back();
   }

   protected function forumCategoryTopics(string $subcategory, string $sort = 'latest')
   {
      $query = Post::where('subcategory', $subcategory)
         ->whereNull('parent_post_id')
         ->with('user')
         ->withCount('comments')
         ->orderByDesc('is_pinned');

      match ($sort) {
         'oldest' => $query->oldest(),
         'popular' => $query->orderByDesc('views'),
         default => $query->orderByDesc('created_at'),
      };

      $paginator = $query->paginate(10, ['*'], 'topics_page')->withQueryString();

      $posts = $paginator->getCollection()->filter(fn ($post) => $post->user);

      $readPostIds = auth()->id()
         ? \App\Models\PostRead::where('user_id', auth()->id())
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('post_id')
            ->all()
         : [];

      $paginator->setCollection(
         $posts
            ->map(fn ($post) => [
               'id' => $post->id,
               'title' => $post->title,
               'author' => $post->user->display_name ?: trim($post->user->first_name . ' ' . $post->user->last_name),
               'avatar' => $post->user->passport,
               'created_at' => $post->created_at,
               'views' => $post->views,
               'replies' => $post->comments_count,
               'is_pinned' => $post->is_pinned,
               'is_read' => in_array($post->id, $readPostIds),
               'url' => route('singlePost', $post->id),
            ])
            ->values()
      );

      return $paginator;
   }

   protected function forumCategoryStats(string $subcategory, ?int $parentPostId = null): array
   {
      $scope = function ($query) use ($subcategory, $parentPostId) {
         return $parentPostId
            ? $query->where('parent_post_id', $parentPostId)
            : $query->where('subcategory', $subcategory)->whereNull('parent_post_id');
      };

      $posts = $scope(Post::query())->get(['id', 'user_id', 'title', 'created_at']);
      $postIds = $posts->pluck('id');
      $commentIds = PostComment::whereIn('post_id', $postIds)->pluck('id');

      $lastCommentAt = PostComment::whereIn('post_id', $postIds)->max('created_at');
      $lastReplyAt = PostReply::whereIn('comment_id', $commentIds)->max('created_at');
      $lastActivityAt = collect([$posts->max('created_at'), $lastCommentAt, $lastReplyAt])
         ->filter()
         ->max();

      $topPosters = $scope(Post::query())
         ->selectRaw('user_id, count(*) as posts_count')
         ->groupBy('user_id')
         ->orderByDesc('posts_count')
         ->limit(4)
         ->with('user')
         ->get()
         ->filter(fn ($row) => $row->user)
         ->map(fn ($row) => [
            'name' => $row->user->display_name ?: trim($row->user->first_name . ' ' . $row->user->last_name),
            'avatar' => $row->user->passport,
            'posts_count' => $row->posts_count,
         ])
         ->values();

      $popularPost = $scope(Post::query())
         ->withCount('comments', 'likes')
         ->orderByDesc('comments_count')
         ->orderByDesc('likes_count')
         ->orderByDesc('created_at')
         ->with('user')
         ->first();

      return [
         'last_reply_label' => $this->shortDuration($lastActivityAt),
         'top_posters' => $topPosters,
         'popular_post' => $popularPost && $popularPost->user ? [
            'author' => $popularPost->user->display_name ?: trim($popularPost->user->first_name . ' ' . $popularPost->user->last_name),
            'avatar' => $popularPost->user->passport,
            'date' => $popularPost->created_at->format('F j, Y'),
            'title' => $popularPost->title,
            'url' => route('singlePost', $popularPost->id),
         ] : null,
      ];
   }

   protected function shortDuration($date): string
   {
      if (!$date) {
         return '—';
      }

      $date = \Carbon\Carbon::parse($date);
      $now = now();

      $years = $date->diffInYears($now);
      if ($years >= 1) {
         return $years . ' yr' . ($years > 1 ? 's' : '');
      }

      $months = $date->diffInMonths($now);
      if ($months >= 1) {
         return $months . ' mo';
      }

      $days = $date->diffInDays($now);
      if ($days >= 1) {
         return $days . ' d';
      }

      $hours = $date->diffInHours($now);
      if ($hours >= 1) {
         return $hours . ' hr';
      }

      return 'Just now';
   }

   public function toggleFollowTopic(Request $request, string $subcategory)
   {
      $definitions = array_merge(
         $this->forumCategoryDefinitions(),
         $this->communityCategoryDefinitions()
      );

      abort_unless(isset($definitions[$subcategory]), 404);

      $existing = \App\Models\TopicFollow::where('subcategory', $subcategory)
         ->where('user_id', auth()->id())
         ->first();

      if ($existing) {
         $existing->delete();
      } else {
         \App\Models\TopicFollow::create([
            'user_id' => auth()->id(),
            'subcategory' => $subcategory,
         ]);
      }

      return response()->json([
         'following' => ! $existing,
         'followers_count' => \App\Models\TopicFollow::where('subcategory', $subcategory)->count(),
      ]);
   }

   /**
    * Shared sidebar data (stats, contributors, upcoming events, latest
    * activity, recent members) used by both the Activity Feed and Forum
    * pages, which share the same right-hand sidebar layout.
    */
   protected function communitySidebarData(): array
   {
      $memberCount = User::where('role', 'member')->count();
      $onlineCount = User::where('role', 'member')
         ->where('last_login_at', '>=', now()->subMinutes(15))
         ->count();
      $postCount = Post::where('subcategory', '!=', 'exclusive_feed')->count();

      $recentMembers = User::where('role', 'member')
         ->orderByDesc('created_at')
         ->orderByDesc('id')
         ->take(5)
         ->get(['id', 'first_name', 'last_name', 'display_name', 'passport', 'created_at']);

      $contributors = [
         'month' => $this->leaderboardQuery(now()->subMonth())->take(3)->get(),
         'all' => $this->leaderboardQuery()->take(3)->get(),
      ];

      $latestPosts = Post::with(['user', 'blocks'])
         ->latest()
         ->take(5)
         ->get()
         ->map(function ($post) {
            $textBlock = $post->blocks->firstWhere('type', 'text');

            return [
               'type' => 'Post',
               'excerpt' => $post->title ?: ($textBlock ? Str::limit(strip_tags($textBlock->content), 60) : 'View post'),
               'user' => $post->user,
               'created_at' => $post->created_at,
               'url' => route('singlePost', $post),
            ];
         });

      $latestComments = PostComment::with(['user', 'post'])
         ->latest()
         ->take(5)
         ->get()
         ->map(function ($comment) {
            return [
               'type' => 'Comment',
               'excerpt' => Str::limit(strip_tags($comment->body), 60),
               'user' => $comment->user,
               'created_at' => $comment->created_at,
               'url' => $comment->post ? route('singlePost', $comment->post) : null,
            ];
         });

      $latestReplies = PostReply::with('user')
         ->latest()
         ->take(5)
         ->get()
         ->map(function ($reply) {
            $comment = PostComment::find($reply->comment_id);

            return [
               'type' => 'Reply',
               'excerpt' => Str::limit(strip_tags($reply->body), 60),
               'user' => $reply->user,
               'created_at' => $reply->created_at,
               'url' => $comment?->post_id ? route('singlePost', $comment->post_id) : null,
            ];
         });

      $latestActivity = $latestPosts
         ->concat($latestComments)
         ->concat($latestReplies)
         ->filter(fn ($item) => $item['url'] !== null)
         ->sortByDesc('created_at')
         ->take(5)
         ->values();

      $upcomingEvents = Liveshow::where('start_time', '>=', now())
         ->where(function ($query) {
            $query->where('access_type', 'all');

            if (auth()->user()->premium) {
               $query->orWhere('access_type', 'premium');
            }
         })
         ->withCount('bookedUsers')
         ->orderBy('start_time')
         ->take(5)
         ->get();

      return compact(
         'memberCount',
         'onlineCount',
         'postCount',
         'recentMembers',
         'contributors',
         'latestActivity',
         'upcomingEvents'
      );
   }

   public function accountSettings()
   {
      $user = auth()->user();

      $profileFields = [
         'passport' => 'Profile Photo',
         'biography' => 'Bio',
         'phone_number' => 'Phone Number',
         'country' => 'Country',
         'skill_level' => 'Skill Level',
      ];
      $profileNextStep = null;
      $profileFilledCount = 0;
      foreach ($profileFields as $field => $label) {
         if (!empty($user->$field)) {
            $profileFilledCount++;
         } elseif (!$profileNextStep) {
            $profileNextStep = $label;
         }
      }
      $profilePercent = (int) round(($profileFilledCount / count($profileFields)) * 100);

      return view('community.account-settings', compact('user', 'profileNextStep', 'profilePercent'));
   }

   const DISPLAY_NAME_CHANGE_LIMIT = 3;
   const DISPLAY_NAME_CHANGE_WINDOW_DAYS = 30;

   public function displayNameForm()
   {
      $user = auth()->user();
      [$changeCount, $windowStart] = $this->displayNameChangeState($user->id);

      return view('community.display-name', compact('user', 'changeCount', 'windowStart'));
   }

   public function updateDisplayName(Request $request)
   {
      $user = auth()->user();
      [$changeCount, ] = $this->displayNameChangeState($user->id);

      if ($changeCount >= self::DISPLAY_NAME_CHANGE_LIMIT) {
         return back()->withErrors([
            'display_name' => 'You have reached the maximum number of display name changes allowed in this period.',
         ]);
      }

      $validated = $request->validate([
         'display_name' => ['required', 'string', 'min:2', 'max:50'],
      ]);

      \App\Models\DisplayNameChange::create([
         'user_id' => $user->id,
         'old_name' => $user->display_name,
         'new_name' => $validated['display_name'],
      ]);

      $user->update(['display_name' => $validated['display_name']]);

      return redirect()->route('community.account-settings')->with('success', 'Your display name has been updated.');
   }

   /**
    * [changesUsedInWindow, windowStartDate] for the rolling N-day limit.
    */
   protected function displayNameChangeState(int $userId): array
   {
      $windowFloor = now()->subDays(self::DISPLAY_NAME_CHANGE_WINDOW_DAYS);

      $changes = \App\Models\DisplayNameChange::where('user_id', $userId)
         ->where('created_at', '>=', $windowFloor)
         ->orderBy('created_at')
         ->get();

      $windowStart = $changes->first()?->created_at ?? now();

      return [$changes->count(), $windowStart];
   }

   public function notifications()
   {
      $communitySection = \App\Enums\Notification\NotificationSectionEnum::COMMUNITY->value;

      $notifications = auth()->user()->notifications()
         ->where('data->data->section', $communitySection)
         ->latest()
         ->get();

      $page = request()->get('page', 1);
      $perPage = 20;
      $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
         $notifications->forPage($page, $perPage),
         $notifications->count(),
         $perPage,
         $page,
         ['path' => request()->url(), 'query' => request()->query()]
      );

      return view('community.notifications', ['notifications' => $paginated]);
   }

   public function profile()
   {
      $user = auth()->user();
      $postsCount = \App\Models\Post::where('user_id', $user->id)->count();
      $assessment = \App\Models\UserAssessment::where('user_id', $user->id)->latest()->first();
      $skillLevel = $assessment->skill_level ?? null;

      // Same "Academy Stats" figures used on the community dashboard, for consistency.
      $totalCompleted = \DB::table('course_progress')
         ->where('user_id', $user->id)
         ->distinct('course_id')
         ->count('course_id');

      $milestonesList = [1, 4, 9, 14, 21, 31, 43, 58, 76, 96, 121, 151];
      $achievedCount = collect($milestonesList)->filter(fn ($lessons) => $totalCompleted >= $lessons)->count();

      return view('community.profile', compact(
         'user', 'postsCount', 'skillLevel', 'assessment', 'totalCompleted', 'achievedCount'
      ));
   }

   public function leaderboard()
   {
      // The champion/standout summary card always reflects the true #1,
      // independent of which page of the (now paginated) list is showing.
      $monthlyChampion = $this->leaderboardQuery(now()->startOfMonth())->first();
      $allTimeChampion = $this->leaderboardQuery()->first();

      $monthlyLeaders = $this->leaderboardQuery(now()->startOfMonth())
         ->paginate(10, ['*'], 'leaderboard_page')
         ->withQueryString();

      $topMembers = $this->leaderboardQuery()
         ->paginate(10, ['*'], 'members_page')
         ->withQueryString();

      return view('community.leaderboard', compact('monthlyLeaders', 'topMembers', 'monthlyChampion', 'allTimeChampion'));
   }

    public function single()
    {
       return view('community.single');
    }

   public function subcategory($subcategory)
   {
      $user = auth()->user();

      // Get active subscription (Stripe or manual)
      $activeSubscription = $user->hasActiveSubscription();

      if ($subcategory === 'lessons') {
         if (!$user || !$activeSubscription) {
            return redirect('/member/plan');
         }
         $tutorials = \App\Models\Tutorial::where('status', 'active')->latest()->get();
         return view('community.tutorials', compact('tutorials', 'subcategory'));
      }

      return view('community.subcategory', compact(
         'subcategory',
         'activeSubscription'
      ));
   }

   public function tutorialShow(\App\Models\Tutorial $tutorial)
   {
      $user = auth()->user();

      if (!$user || !$user->hasActiveSubscription()) {
         return redirect('/member/plan');
      }

      $comments = \App\Models\TutorialComment::where('tutorial_id', $tutorial->id)
         ->with('user')
         ->latest()
         ->get();

      return view('community.tutorial-show', compact('tutorial', 'comments'));
   }

   public function storeTutorialComment(\Illuminate\Http\Request $request)
   {
      $request->validate([
         'tutorial_id' => 'required|exists:tutorials,id',
         'comment' => 'required|string|max:2000',
      ]);

      \App\Models\TutorialComment::create([
         'tutorial_id' => $request->tutorial_id,
         'user_id' => auth()->id(),
         'comment' => $request->comment,
      ]);

      return back()->with('success', 'Comment added.');
   }

   public function updateTutorialComment(\Illuminate\Http\Request $request, \App\Models\TutorialComment $comment)
   {
      abort_if($comment->user_id !== auth()->id(), 403);

      $request->validate([
         'comment' => 'required|string|max:2000',
      ]);

      $comment->update(['comment' => $request->comment]);

      return back()->with('success', 'Comment updated.');
   }

   public function destroyTutorialComment(\App\Models\TutorialComment $comment)
   {
      abort_if($comment->user_id !== auth()->id(), 403);

      $comment->delete();

      return back()->with('success', 'Comment deleted.');
   }

   public function singlePost(Post $post)
   {
      // Raw query builder increment (bypassing Eloquent, which auto-touches
      // `updated_at` even via Builder::increment()) so viewing a post doesn't
      // bump its timestamp and falsely make it look "latest".
      \Illuminate\Support\Facades\DB::table('posts')->where('id', $post->id)->increment('views');
      $post->views++;

      if (auth()->id()) {
         \App\Models\PostRead::firstOrCreate([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
         ]);
      }

      $definitions = array_merge(
         $this->forumCategoryDefinitions(),
         $this->communityCategoryDefinitions()
      );

      if ($post->subcategory === 'student_challenges') {
         $category = [
            'title' => $post->title,
            'description' => $post->blocks()->where('type', 'text')->value('content') ?? '',
            'subcategory' => $post->subcategory,
            // Scoped to this specific challenge's own submissions, not the
            // whole Student Challenges category.
            'count' => Post::where('parent_post_id', $post->id)->count(),
            'parent_post_id' => $post->id,
            'video_url' => $post->video_url,
         ];

         $categoryStats = $this->forumCategoryStats($post->subcategory, $post->id);
         $parentCategoryLabel = $definitions[$post->subcategory]['title'] ?? 'Forums';
         $forceActiveNavKey = 'forum';

         return view('community.topic-page', compact('category', 'categoryStats', 'parentCategoryLabel', 'forceActiveNavKey'));
      }

      $breadcrumbs = [];
      $forceActiveNavKey = null;
      if (isset($definitions[$post->subcategory])) {
         $breadcrumbs[] = ['label' => 'Forums', 'url' => route('community.forum')];
         $breadcrumbs[] = ['label' => $definitions[$post->subcategory]['title'], 'url' => route('community.forum.category', $post->subcategory)];
         $forceActiveNavKey = 'forum';
      }
      $breadcrumbs[] = ['label' => $post->title];

      return view('community.single-post', compact('post', 'breadcrumbs', 'forceActiveNavKey'));
   }

   public function pdfDownloads()
   {
      $user = auth()->user();

      if (!$user || !$user->hasActiveSubscription()) {
         return redirect('/member/plan');
      }

      $pdfList = PDFDownload::latest()->get();


      return view('community.pdf-downloads', compact('pdfList'));
   }

   public function audioDownloads()
   {
      $user = auth()->user();

      if (!$user || !$user->hasActiveSubscription()) {
         return redirect('/member/plan');
      }

      $tracksAndLoops = AudioDownload::where('category', 'tracks_loops')->get();
      $pianoPlays = AudioDownload::where('category', 'piano_plays')->get();

      return view('community.audio-downloads', compact('tracksAndLoops', 'pianoPlays'));
   }

   public function midiDownloads()
   {
      $user = auth()->user();

      if (!$user || !$user->hasActiveSubscription()) {
         return redirect('/member/plan');
      }

      $midiFiles = MidiFile::all();

      return view('community.midi-files.midi-downloads', compact('midiFiles'));
   }

   private function leaderboardQuery($from = null): Builder
   {
      $postsSubquery = DB::table('posts')
         ->selectRaw('user_id, COUNT(*) as posts_count')
         ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
         ->groupBy('user_id');

      $commentsSubquery = DB::table('post_comments')
         ->selectRaw('user_id, COUNT(*) as comments_count')
         ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
         ->groupBy('user_id');

      $repliesSubquery = DB::table('post_replies')
         ->selectRaw('user_id, COUNT(*) as replies_count')
         ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
         ->groupBy('user_id');

      $likesSubquery = DB::table('post_likes')
         ->selectRaw('user_id, COUNT(*) as likes_count')
         ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
         ->groupBy('user_id');

      $progressSubquery = DB::table('course_progress')
         ->selectRaw('user_id, COUNT(DISTINCT course_id) as completed_count')
         ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
         ->groupBy('user_id');

      return User::query()
         ->select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.display_name',
            'users.passport',
            'communities.id as community_id',
            'communities.user_name',
            'communities.verified_status',
         ])
         ->join('communities', 'communities.user_id', '=', 'users.id')
         ->leftJoinSub($postsSubquery, 'post_totals', fn ($join) => $join->on('post_totals.user_id', '=', 'users.id'))
         ->leftJoinSub($commentsSubquery, 'comment_totals', fn ($join) => $join->on('comment_totals.user_id', '=', 'users.id'))
         ->leftJoinSub($repliesSubquery, 'reply_totals', fn ($join) => $join->on('reply_totals.user_id', '=', 'users.id'))
         ->leftJoinSub($likesSubquery, 'like_totals', fn ($join) => $join->on('like_totals.user_id', '=', 'users.id'))
         ->leftJoinSub($progressSubquery, 'progress_totals', fn ($join) => $join->on('progress_totals.user_id', '=', 'users.id'))
         ->where(function ($query) {
            $query->whereNull('communities.status')
               ->orWhere('communities.status', '!=', 'blocked');
         })
         ->selectRaw('COALESCE(post_totals.posts_count, 0) as posts_count')
         ->selectRaw('COALESCE(comment_totals.comments_count, 0) as comments_count')
         ->selectRaw('COALESCE(reply_totals.replies_count, 0) as replies_count')
         ->selectRaw('COALESCE(like_totals.likes_count, 0) as likes_count')
         ->selectRaw('COALESCE(progress_totals.completed_count, 0) as completed_count')
         ->selectRaw('
            (COALESCE(post_totals.posts_count, 0) * 5) +
            (COALESCE(comment_totals.comments_count, 0) * 3) +
            (COALESCE(reply_totals.replies_count, 0) * 2) +
            (COALESCE(like_totals.likes_count, 0) * 1) +
            (COALESCE(progress_totals.completed_count, 0) * 10) +
            ((CASE 
               WHEN COALESCE(progress_totals.completed_count, 0) >= 151 THEN 12
               WHEN COALESCE(progress_totals.completed_count, 0) >= 121 THEN 11
               WHEN COALESCE(progress_totals.completed_count, 0) >= 96 THEN 10
               WHEN COALESCE(progress_totals.completed_count, 0) >= 76 THEN 9
               WHEN COALESCE(progress_totals.completed_count, 0) >= 58 THEN 8
               WHEN COALESCE(progress_totals.completed_count, 0) >= 43 THEN 7
               WHEN COALESCE(progress_totals.completed_count, 0) >= 31 THEN 6
               WHEN COALESCE(progress_totals.completed_count, 0) >= 21 THEN 5
               WHEN COALESCE(progress_totals.completed_count, 0) >= 14 THEN 4
               WHEN COALESCE(progress_totals.completed_count, 0) >= 9 THEN 3
               WHEN COALESCE(progress_totals.completed_count, 0) >= 4 THEN 2
               WHEN COALESCE(progress_totals.completed_count, 0) >= 1 THEN 1
               ELSE 0
            END) * 20) as total_points
         ')
         ->orderByDesc('total_points')
         ->orderByDesc('posts_count')
         ->orderByDesc('comments_count')
         ->orderBy('users.first_name');
   }
}
