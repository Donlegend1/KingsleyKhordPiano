<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use App\Helpers\VideoHelper;

class AdminTutorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tutorials = Tutorial::latest()->get();
        return view('admin.tutorials.index', compact('tutorials'));
    }

    public function create()
    {
        return view('admin.tutorials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'video_type' => 'required|string|in:vimeo,youtube,google,local,iframe',
            'duration' => 'nullable|string|max:50',
            'level' => 'nullable|string|max:100',
            'author_name' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'author_avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title', 'description', 'video_url', 'video_type', 'duration', 'level'
        ]);

        if ($request->filled('author_name')) {
            $data['author_name'] = $request->input('author_name');
        }

        // Extract ID for specific video types if needed
        $videoType = $request->input('video_type');
        $videoPath = $request->input('video_url');
        if ($videoType === 'youtube') {
            $data['video_url'] = $this->extractYoutubeId($videoPath);
        } elseif ($videoType === 'google') {
            $data['video_url'] = $this->extractGoogleDriveId($videoPath);
        } elseif ($videoType === 'vimeo') {
            $data['video_url'] = $this->extractVimeoId($videoPath);
        } else {
            $data['video_url'] = $videoPath;
        }

        // Move thumbnail file
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();

            if (file_exists(base_path('../public_html'))) {
                $destination = base_path('../public_html/uploads/thumbnails');
            } else {
                $destination = public_path('uploads/thumbnails');
            }

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $thumbnail->move($destination, $filename);
            $data['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        // Move author avatar file
        if ($request->hasFile('author_avatar')) {
            $avatar = $request->file('author_avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();

            if (file_exists(base_path('../public_html'))) {
                $destination = base_path('../public_html/uploads/avatars');
            } else {
                $destination = public_path('uploads/avatars');
            }

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $avatar->move($destination, $filename);
            $data['author_avatar'] = 'uploads/avatars/' . $filename;
        }

        Tutorial::create($data);

        return redirect()->route('admin.tutorials.index')->with('success', 'Tutorial uploaded successfully!');
    }

    public function edit(Tutorial $tutorial)
    {
        return view('admin.tutorials.edit', compact('tutorial'));
    }

    public function update(Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'video_type' => 'required|string|in:vimeo,youtube,google,local,iframe',
            'duration' => 'nullable|string|max:50',
            'level' => 'nullable|string|max:100',
            'author_name' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'author_avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title', 'description', 'video_url', 'video_type', 'duration', 'level'
        ]);

        if ($request->filled('author_name')) {
            $data['author_name'] = $request->input('author_name');
        }

        // Extract ID for specific video types
        $videoType = $request->input('video_type');
        $videoPath = $request->input('video_url');
        if ($videoType === 'youtube') {
            $data['video_url'] = $this->extractYoutubeId($videoPath);
        } elseif ($videoType === 'google') {
            $data['video_url'] = $this->extractGoogleDriveId($videoPath);
        } elseif ($videoType === 'vimeo') {
            $data['video_url'] = $this->extractVimeoId($videoPath);
        } else {
            $data['video_url'] = $videoPath;
        }

        // Move thumbnail file if present
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();

            if (file_exists(base_path('../public_html'))) {
                $destination = base_path('../public_html/uploads/thumbnails');
            } else {
                $destination = public_path('uploads/thumbnails');
            }

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $thumbnail->move($destination, $filename);
            $data['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        // Move author avatar file if present
        if ($request->hasFile('author_avatar')) {
            $avatar = $request->file('author_avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();

            if (file_exists(base_path('../public_html'))) {
                $destination = base_path('../public_html/uploads/avatars');
            } else {
                $destination = public_path('uploads/avatars');
            }

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $avatar->move($destination, $filename);
            $data['author_avatar'] = 'uploads/avatars/' . $filename;
        }

        $tutorial->update($data);

        return redirect()->route('admin.tutorials.index')->with('success', 'Tutorial updated successfully!');
    }

    public function destroy(Tutorial $tutorial)
    {
        $tutorial->delete();
        return redirect()->route('admin.tutorials.index')->with('success', 'Tutorial deleted successfully!');
    }

    private function extractYoutubeId($url)
    {
        if (preg_match('/youtu\.be\/([^\?]+)|youtube\.com\/watch\?v=([^\&]+)|youtube\.com\/embed\/([^\?&]+)|youtube\.com\/shorts\/([^\?&]+)/', $url, $matches)) {
            return !empty($matches[1]) ? $matches[1] : (!empty($matches[2]) ? $matches[2] : (!empty($matches[3]) ? $matches[3] : ($matches[4] ?? $url)));
        }
        return $url;
    }

    private function extractGoogleDriveId($url)
    {
        if (preg_match('/drive\.google\.com\/(?:u\/\d+\/)?file\/d\/([^\/\?\&]+)/', $url, $matches)) {
            return $matches[1];
        }
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $params);
            if (isset($params['id'])) {
                return $params['id'];
            }
        }
        return $url;
    }

    private function extractVimeoId($url)
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        return $url;
    }
}
