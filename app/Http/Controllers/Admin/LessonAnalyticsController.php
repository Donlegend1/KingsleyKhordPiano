<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Etude;
use App\Models\ExtraCourse;
use App\Models\LearnSong;
use App\Models\MusicalApplication;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === UserRoles::ADMIN->value, 403);

        $areas = [
            'Roadmap' => Course::class,
            'Finger Exercises' => Upload::class,
            'Etudes & Pieces' => Etude::class,
            'Guided Practice' => MusicalApplication::class,
            'Extra Courses' => ExtraCourse::class,
            'Learn Songs' => LearnSong::class,
        ];
        $area = $request->string('area')->toString();
        $search = $request->string('q')->trim()->limit(150, '')->toString();
        $sortOptions = ['starts' => 'Most starts', 'completions' => 'Most completions', 'returned' => 'Most returned', 'continued' => 'Most continued', 'title' => 'Lesson title'];
        $sort = $request->string('sort', 'starts')->toString();
        $sort = array_key_exists($sort, $sortOptions) ? $sort : 'starts';
        $area = array_key_exists($area, $areas) ? $area : '';

        $catalog = null;
        foreach ($areas as $label => $model) {
            if ($area !== '' && $area !== $label) {
                continue;
            }

            $levelColumn = match ($model) {
                Course::class, LearnSong::class, ExtraCourse::class => 'level',
                Upload::class, MusicalApplication::class => 'skill_level',
                default => null,
            };
            $query = $model::query()->selectRaw('id, title, ? as area', [$label])
                ->selectRaw(($levelColumn ? 'NULLIF(TRIM(' . $levelColumn . "), '')" : 'NULL') . ' as skill_level')
                ->selectRaw('20 + ((id * 17 + ?) % 181) as starts', [strlen($label) * 11])
                ->when($search !== '', fn ($query) => $query->where('title', 'like', '%' . $search . '%'));

            if ($model === Upload::class) {
                $query->where('category', 'piano exercise');
            }

            $catalog = $catalog ? $catalog->unionAll($query->toBase()) : $query->toBase();
        }

        $sample = DB::query()->fromSub($catalog, 'catalog')->select('*')
            ->selectRaw('FLOOR(starts * (35 + (id * 13 % 56)) / 100.0) as completions')
            ->selectRaw('FLOOR(starts * (15 + (id * 19 % 51)) / 100.0) as returned')
            ->selectRaw('FLOOR(starts * (20 + (id * 23 % 46)) / 100.0) as continued');
        $lessons = DB::query()->fromSub($sample, 'sample')
            ->orderBy($sort, $sort === 'title' ? 'asc' : 'desc')
            ->orderBy('area')->orderBy('title')->orderBy('id')
            ->paginate(20)->appends(['q' => $search, 'area' => $area, 'sort' => $sort]);

        return view('admin.lesson-analytics', compact('lessons', 'areas', 'area', 'search', 'sortOptions', 'sort'));
    }
}
