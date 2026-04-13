<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\MidiFile;
use App\Models\PDFDownload;
use App\Models\AudioDownload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CommunityIndexController extends Controller
{
    public function index()
    {
        return view('community.index');
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

      $beginners = PDFDownload::where('category', 'beginner')->get();
      $intermediate = PDFDownload::where('category', 'intermediate')->get();
      $advanced = PDFDownload::where('category', 'advanced')->get();

      return view('community.pdf-downloads', compact('beginners', 'intermediate', 'advanced'));
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
         ->where(function ($query) {
            $query->whereNull('communities.status')
               ->orWhere('communities.status', '!=', 'blocked');
         })
         ->selectRaw('COALESCE(post_totals.posts_count, 0) as posts_count')
         ->selectRaw('COALESCE(comment_totals.comments_count, 0) as comments_count')
         ->selectRaw('COALESCE(reply_totals.replies_count, 0) as replies_count')
         ->selectRaw('COALESCE(like_totals.likes_count, 0) as likes_count')
         ->selectRaw('
            (COALESCE(post_totals.posts_count, 0) * 10) +
            (COALESCE(comment_totals.comments_count, 0) * 5) +
            (COALESCE(reply_totals.replies_count, 0) * 3) +
            (COALESCE(like_totals.likes_count, 0) * 1) as total_points
         ')
         ->orderByDesc('total_points')
         ->orderByDesc('posts_count')
         ->orderByDesc('comments_count')
         ->orderBy('users.first_name');
   }
}
