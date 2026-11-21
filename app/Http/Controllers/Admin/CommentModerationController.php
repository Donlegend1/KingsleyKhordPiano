<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExtraCourse;
use App\Models\LearnSong;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One place to see and delete every comment on the site. Comments live in six
 * tables (lessons, lesson replies, forum, forum replies, tutorials, guides),
 * so this merges them into a single newest-first feed.
 */
class CommentModerationController extends Controller
{
    /** key => [label, badge colour classes] */
    public const SOURCES = [
        'lesson' => ['Lesson comment', 'bg-blue-50 text-blue-700'],
        'lesson_reply' => ['Lesson reply', 'bg-sky-50 text-sky-700'],
        'forum' => ['Community post comment', 'bg-violet-50 text-violet-700'],
        'forum_reply' => ['Community post reply', 'bg-fuchsia-50 text-fuchsia-700'],
        'tutorial' => ['Tutorial comment', 'bg-amber-50 text-amber-700'],
        'guide' => ['Guide discussion', 'bg-emerald-50 text-emerald-700'],
    ];

    private const PER_PAGE = 15;

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $source = (string) $request->query('source', 'all');
        $search = trim((string) $request->query('q'));

        $feed = DB::query()->fromSub($this->feed(), 'c')
            ->leftJoin('users', 'users.id', '=', 'c.user_id')
            ->select('c.*', 'users.first_name', 'users.last_name', 'users.email');

        $counts = DB::query()->fromSub($this->feed(), 'c')
            ->selectRaw('source, count(*) as total')->groupBy('source')->pluck('total', 'source');

        if (isset(self::SOURCES[$source])) {
            $feed->where('c.source', $source);
        } else {
            $source = 'all';
        }

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $feed->where(function ($q) use ($like) {
                $q->where('c.body', 'like', $like)
                    ->orWhere('users.first_name', 'like', $like)
                    ->orWhere('users.last_name', 'like', $like)
                    ->orWhere('users.email', 'like', $like);
            });
        }

        $total = (clone $feed)->count();
        $page = LengthAwarePaginator::resolveCurrentPage();
        $rows = $feed->orderByDesc('c.created_at')->orderByDesc('c.id')
            ->forPage($page, self::PER_PAGE)->get();

        $comments = new LengthAwarePaginator(
            $this->withContext($rows), $total, self::PER_PAGE, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.comments.index', [
            'comments' => $comments,
            'sources' => self::SOURCES,
            'source' => $source,
            'search' => $search,
            'counts' => $counts,
            'grandTotal' => $counts->sum(),
        ]);
    }

    public function destroy(Request $request, string $source, int $id)
    {
        $this->authorizeAdmin($request);
        abort_unless(isset(self::SOURCES[$source]), 404);

        $deleted = DB::transaction(function () use ($source, $id) {
            switch ($source) {
                case 'lesson':
                    // No foreign key on replies, so remove them explicitly.
                    DB::table('course_video_comment_replies')->where('course_video_comment_id', $id)->delete();
                    return DB::table('course_video_comments')->where('id', $id)->delete();
                case 'lesson_reply':
                    return DB::table('course_video_comment_replies')->where('id', $id)->delete();
                case 'forum':
                    // Replies and likes are removed by the database cascade.
                    return DB::table('post_comments')->where('id', $id)->delete();
                case 'forum_reply':
                    return DB::table('post_replies')->where('id', $id)->delete();
                case 'tutorial':
                    return DB::table('tutorial_comments')->where('id', $id)->delete();
                default: // guide: child replies cascade
                    return DB::table('piano_guide_comments')->where('id', $id)->delete();
            }
        });

        abort_if($deleted === 0, 404);

        return back()->with('success', 'Comment deleted.');
    }

    private function feed()
    {
        $cols = fn (string $table, string $source, string $body, string $extra) => DB::table($table)->selectRaw(
            "? as source, id, user_id, {$body} as body, created_at, {$extra}", [$source]
        );

        return $cols('course_video_comments', 'lesson', 'comment', "category as ctx_a, cast(course_id as char) as ctx_b, url as ctx_c")
            ->unionAll($cols('course_video_comment_replies', 'lesson_reply', 'reply', "cast(course_video_comment_id as char) as ctx_a, null as ctx_b, null as ctx_c"))
            ->unionAll($cols('post_comments', 'forum', 'body', "cast(post_id as char) as ctx_a, null as ctx_b, null as ctx_c"))
            ->unionAll($cols('post_replies', 'forum_reply', 'body', "cast(comment_id as char) as ctx_a, null as ctx_b, null as ctx_c"))
            ->unionAll($cols('tutorial_comments', 'tutorial', 'comment', "cast(tutorial_id as char) as ctx_a, null as ctx_b, null as ctx_c"))
            ->unionAll($cols('piano_guide_comments', 'guide', 'body', "guide_slug as ctx_a, null as ctx_b, null as ctx_c"));
    }

    /** Adds a human "where" label and link to each row on the current page. */
    private function withContext(Collection $rows): Collection
    {
        return $rows->map(function ($row) {
            [$where, $link] = $this->locate($row);
            $row->where = $where;
            $row->link = $link;
            $row->member = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: 'Deleted user';
            $row->created = \Illuminate\Support\Carbon::parse($row->created_at);

            return $row;
        });
    }

    private function locate(object $row): array
    {
        switch ($row->source) {
            case 'lesson':
                $type = null;
                if ($row->ctx_c && preg_match('/[?&]type=([a-z_]+)/', $row->ctx_c, $m)) {
                    $type = $m[1];
                }
                $title = match (true) {
                    $row->ctx_a === 'course' => Course::find($row->ctx_b)?->title,
                    $type === 'learn_song' => LearnSong::find($row->ctx_b)?->title,
                    $type === 'extra_course' => ExtraCourse::find($row->ctx_b)?->title,
                    default => Upload::find($row->ctx_b)?->title,
                };

                return [$title ?: 'Lesson no longer available', $row->ctx_c ?: null];
            case 'lesson_reply':
                $parent = DB::table('course_video_comments')->where('id', $row->ctx_a)->value('comment');

                return [$parent ? 'Reply to: "' . Str::limit($parent, 50) . '"' : 'Reply to a deleted comment', null];
            case 'forum':
            case 'forum_reply':
                $postId = $row->source === 'forum'
                    ? $row->ctx_a
                    : DB::table('post_comments')->where('id', $row->ctx_a)->value('post_id');
                $post = DB::table('posts')->where('id', $postId)->first(['id', 'title', 'body', 'category']);
                if (! $post) {
                    return ['Post no longer available', null];
                }

                return [$post->title ?: Str::limit((string) $post->body, 60), route('singlePost', $post->id)];
            case 'tutorial':
                return [DB::table('tutorials')->where('id', $row->ctx_a)->value('title') ?: 'Tutorial no longer available', null];
            default:
                $title = config("piano-guides.{$row->ctx_a}.title");

                return [$title ?: Str::headline((string) $row->ctx_a), route('guides.show', $row->ctx_a)];
        }
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array($request->user()->role, [UserRoles::ADMIN->value, UserRoles::SUPERADMIN->value], true), 403);
    }
}
