<div class="container mx-auto px-4 py-10">
  <!-- Header Text -->
  <div class="text-center mb-10">
    <p class="text-black font-bold text-2xl sm:text-3xl lg:text-4xl">
      The Pros Don’t Just Play
    </p>
    <p class="mt-4 text-base sm:text-lg mx-auto max-w-xl">
      They understand the fundamentals of music well enough to teach others to follow in their footsteps.​
    </p>
  </div>

  <!-- Feature Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  @foreach (['musictheory', 'roadmap', 'eartraining', 'pianoexercise'] as $image)
  <div class="h-80 sm:h-96 bg-cover bg-center rounded-lg relative overflow-hidden"
       style="background-image: url('/images/{{ $image }}.png')">

    <!-- Full overlay covering the card -->
    <div class="absolute inset-0 bg-black bg-opacity-60 flex items-end p-4 text-white">
      <p class="text-sm sm:text-base leading-snug">
        @switch($image)
          @case('musictheory')
            Replace your nervousness with steel-like confidence. A solid understanding of the fundamentals of music theory will expose you to the structures and systems of music. Also, you'll learn to communicate your musical ideas eloquently.
            @break
          @case('roadmap')
            Gain a clear, structured plan of the curriculum, designed to guide you through every skill level—beginner, intermediate, and advanced. Get a hands-on understanding of the essential steps needed to progress with confidence.
            @break
          @case('eartraining')
            Learn to expand your musical vocabulary and repertoire. You'll soon be able to recognise and identify chord progressions just by listening to their melodies. This will, in turn, help you develop a flawless and incredible playing style.
            @break
          @case('pianoexercise')
            Hands-on daily piano exercises to strengthen your fingers. What's learning to play the piano without consistent, hands-on practice? Your fingers need a lot of practical training and drilling to produce flawless rhythms.
            @break
        @endswitch
      </p>
    </div>
  </div>
  @endforeach
</div>


  <!-- Academy Welcome Section -->
  <div class="flex flex-col lg:flex-row justify-center items-center gap-8 mt-16 px-4">
    <!-- Logo -->
    <div class="w-full lg:w-5/12 text-center lg:text-left">
      <img src="/logo/logoblack.png" alt="logo" class="w-[80%] mx-auto lg:mx-0 h-auto">
    </div>

    <!-- Text Content -->
    <div class="w-full lg:w-7/12">
      <p class="font-bold text-2xl sm:text-3xl lg:text-[38px] leading-snug">
        Welcome to KingsleyKhord Music Academy
      </p>
      <p class="font-medium mt-4 mb-6 text-base sm:text-lg">
        Here, the fundamental concepts of the piano are broken down into digestible, easy-to-follow lessons.
      </p>
      <ul class="list-disc pl-5 space-y-2 text-base">
        <li>Roadmap for all skill levels​</li>
        <li>Premium midi files​</li>
        <li>Ear Training Quiz​</li>
        <li>Zoom Live Lessons​</li>
        <li>Supportive Community​</li>
      </ul>
    </div>
  </div>

  <!-- CTA Button -->
  <div class="flex justify-center mt-12">
    <a href="/plans" class="flex items-center bg-[#FFD736] uppercase px-6 py-3 hover:bg-[#c2ab39] text-black rounded-md font-semibold text-base sm:text-lg transition">
      All Membership Features
      <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
  </div>
</div>
