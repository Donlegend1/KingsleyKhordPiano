<section class="relative bg-cover bg-center h-[60vh] overflow-hidden" style="background-image: url('/images/Background.jpg')">
  <div class="relative z-10 h-full flex items-center justify-center px-4">

    <!-- Previous Button -->
    <button id="prevBtn" class="absolute left-4 top-1/2 transform -translate-y-1/2 px-3 py-2 z-20">
      <img src="/icons/previous.png" alt="previous" class="w-6 h-6">
    </button>

    <!-- Slider Content Wrapper -->
    <div class="w-full max-w-4xl h-full overflow-hidden">
      <div id="slider" class="flex transition-transform duration-700 ease-in-out h-full" style="transform: translateX(0%)">

        <!-- Slide 1 -->
        <div class="min-w-full h-full flex flex-col justify-center items-center text-center px-4">
          <div class="w-full max-w-2xl">
            <h1 class="text-xl md:text-4xl font-bold leading-tight mb-4">
              Practice with Confidence and Mastery
            </h1>
            <p class="text-base font-playfair text-gray-700 leading-relaxed">
              Learn to understand the theory behind every chord, lick, and run so you’re not just playing notes but truly grasping how they connect. 
              Dive into the structure and relationships within music, and gain the ability to interpret, adapt, and even create your own unique sound with confidence.
            </p>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="min-w-full h-full flex flex-col justify-center items-center text-center px-4">
          <div class="w-full max-w-2xl">
            <h1 class="text-xl md:text-4xl font-bold leading-tight mb-4">
              Unlock Your Creativity
            </h1>
            <p class="text-base font-playfair text-gray-700 leading-relaxed">
              With our guided approach, you'll explore improvisation techniques and develop your own musical ideas.
            </p>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="min-w-full h-full flex flex-col justify-center items-center text-center px-4">
          <div class="w-full max-w-2xl">
            <h1 class="text-xl md:text-4xl font-bold leading-tight mb-4">
              Master Theory Effortlessly
            </h1>
            <p class="text-base font-playfair text-gray-700 leading-relaxed">
              From basic scales to advanced harmonies, gain the tools to decode music theory like a pro.
            </p>
          </div>
        </div>

      </div>
    </div>
 
    <!-- Next Button -->
    <button id="nextBtn" class="absolute right-4 top-1/2 transform -translate-y-1/2 px-3 py-2 z-20">
      <img src="/icons/next.png" alt="next" class="w-6 h-6">
    </button>
    
  </div>
 
</section>
<div id="pricing" class="mt-10"></div>

<script>
  const slider = document.getElementById("slider");
  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");

  let currentIndex = 0;
  const totalSlides = slider.children.length;

  function showSlide(index) {
    slider.style.transform = `translateX(-${index * 100}%)`;
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    showSlide(currentIndex);
  }

  nextBtn.addEventListener("click", nextSlide);
  prevBtn.addEventListener("click", prevSlide);

  // Auto Slide Every 5 Seconds
  setInterval(nextSlide, 5000);
</script>
