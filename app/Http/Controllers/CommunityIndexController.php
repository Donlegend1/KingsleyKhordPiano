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

        return view('community.index', compact(
            'user', 
            'assessment', 
            'progress', 
            'totalCompleted', 
            'achievedCount', 
            'resumeLesson', 
            'resumeUrl'
        ));
    }

   public function space()
   {
      return view('community.space');
   }

   public function members()
   {
       return view('community.members');
   }

   public function leaderboard()
   {
      $monthlyLeaders = $this->leaderboardQuery(now()->startOfMonth())->take(10)->get();
      $topMembers = $this->leaderboardQuery()->take(10)->get();

      return view('community.leaderboard', compact('monthlyLeaders', 'topMembers'));
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

   public function singlePost(Post $post)
   {
      return view('community.single-post', compact('post'));
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
