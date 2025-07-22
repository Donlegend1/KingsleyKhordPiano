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
              Practice with Purpose and Precision 
            </h1>
            <p class="text-base font-playfair text-gray-700 leading-relaxed">
              Follow a clear, step-by-step structure that tells you exactly what to practice, when to focus on it, and how to do it effectively. Our guided approach removes guesswork, helping you maximize progress and make each practice session
               more productive. By staying on track, you'll save time while achieving better results. </p>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="min-w-full h-full flex flex-col justify-center items-center text-center px-4">
          <div class="w-full max-w-2xl">
            <h1 class="text-xl md:text-4xl font-bold leading-tight mb-4">
              Play Gospel Music like a Pro
            </h1>
            <p class="text-base font-playfair text-gray-700 leading-relaxed">
              Learn to play all your favorite gospel songs effortlessly by mastering the correct structure and chord patterns. With our approach, you’ll be able to quickly understand and apply the essential musical elements, allowing you to play with confidence and accuracy. Whether you’re a beginner or 
              looking to refine your skills, this method will help you learn quickly
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
