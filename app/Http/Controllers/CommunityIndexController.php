<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\MidiFile;
use App\Models\PDFDownload;
use App\Models\AudioDownload;

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
      $beginners = PDFDownload::where('category', 'beginners')->get();
      $intermediate = PDFDownload::where('category', 'intermediate')->get();
      $advanced = PDFDownload::where('category', 'advanced')->get();

      return view('community.pdf-downloads', compact('beginners', 'intermediate', 'advanced'));
   }

   public function audioDownloads()
   {
      $tracksAndLoops = AudioDownload::where('category', 'tracks_loops')->get();
      $pianoPlays = AudioDownload::where('category', 'piano_plays')->get();

      return view('community.audio-downloads', compact('tracksAndLoops', 'pianoPlays'));
   }

   public function midiDownloads()
   {
      $midiFiles = MidiFile::all();
      // dd($midiFiles);
      return view('community.midi-files.midi-downloads', compact('midiFiles'));
   }
}
