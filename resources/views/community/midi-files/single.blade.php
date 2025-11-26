@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="w-full flex justify-center py-10">

    <div class="w-full max-w-4xl">

    {{-- ===========================
        VIDEO SECTION
    ============================ --}}
    <div class="w-full rounded-md overflow-hidden shadow-lg">

        @if ($MidiFile->video_type === 'youtube')
            {{-- YOUTUBE --}}
            <iframe 
                width="100%"
                height="400"
                src="https://www.youtube.com/embed/{{ $MidiFile->video_path }}"
                title="YouTube video"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>

        @elseif ($MidiFile->video_type === 'google')
            {{-- GOOGLE DRIVE VIDEO --}}
            <iframe 
                src="https://drive.google.com/file/d/{{ $MidiFile->video_path }}/preview"
                width="100%" 
                height="400" 
                allow="autoplay"
            ></iframe>

        @elseif ($MidiFile->video_type === 'local')
            {{-- LOCAL MP4 VIDEO --}}
            <video width="100%" height="400" controls class="rounded">
                <source src="{{ asset('midi-files/videos/' . $MidiFile->video_path) }}" type="video/mp4">
                Your browser does not support HTML5 video.
            </video>

        @elseif ($MidiFile->video_type === 'iframe')
            {{-- RAW IFRAME FROM DB --}}
            {!! $MidiFile->video_path !!}
        @else
            {{-- FALLBACK PLACEHOLDER --}}
            <div class="w-full h-[400px] bg-gray-200 flex items-center justify-center text-gray-500">
                No video available
            </div>
        @endif

    </div>

    {{-- ===========================
        DOWNLOAD BUTTONS
    ============================ --}}
    <div class="flex items-center justify-center gap-6 mt-8">
        
        <a href="{{ route('midi.download.midi', $MidiFile->id) }}"
            class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-900">
            Download Midi File
        </a>

        <a href="{{ route('midi.download.lmv', $MidiFile->id) }}"
            class="bg-red-600 text-white px-6 py-2 rounded shadow hover:bg-red-700">
            Download LMV File
        </a>

    </div>

    {{-- ===========================
        FOOTER SMALL LINK
    ============================ --}}
    <div class="text-center mt-5">
        <a href="#" 
            id="openLearnModal"
            class="text-gray-600 underline text-sm hover:text-gray-800">
             How to learn MIDI FILES with Midiculous Free Player
         </a>
    </div>
<!-- =======================
     LEARN VIDEO MODAL
=========================== -->
<div id="learnModal" 
    class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">

    <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-3xl relative">

        <!-- Close Button -->
        <button id="closeLearnModal"
            class="absolute top-3 right-3 text-white bg-red-600 rounded-full px-3 py-1">
            ✕
        </button>

       <iframe 
          id="learnVideo"
          src="https://drive.google.com/file/d/19kLzKfc3HB0v2dYJHcUzBrnFqOE4YPm4/preview"
          width="100%" 
          height="450"
          allow="autoplay"
          allowfullscreen
      ></iframe>

    </div>
</div>

</div>


</div>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const openBtn = document.getElementById("openLearnModal");
    const modal = document.getElementById("learnModal");
    const closeBtn = document.getElementById("closeLearnModal");
    const videoFrame = document.getElementById("learnVideo");

    const videoURL = "https://drive.google.com/file/d/19kLzKfc3HB0v2dYJHcUzBrnFqOE4YPm4/preview";

    // Open modal
    openBtn.addEventListener("click", (e) => {
        e.preventDefault();
        modal.classList.remove("hidden");
        videoFrame.src = videoURL; // Load video
    });

    // Close modal
    function closeModal() {
        modal.classList.add("hidden");
        videoFrame.src = ""; // Stop video
    }

    closeBtn.addEventListener("click", closeModal);

    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
});
</script>


@endsection
