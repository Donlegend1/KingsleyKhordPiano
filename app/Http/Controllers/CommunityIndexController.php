<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Str;
use App\Models\Community;

class CommunityIndexController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
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

    public function single()
    {
       return view('community.single');
    }

    public function subcategory($subcategory)
    {
       return view('community.subcategory', compact('subcategory'));
    }

   public function singlePost(Post $post)
   {
      return view('community.single-post', compact('post'));
   }
}
