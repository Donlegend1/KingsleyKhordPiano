<section class="coaches-section py-14" style="
    background-color: #0a0a0f;
    background-image: linear-gradient(180deg, #0a0a0f 0%, #11111a 100%);
    position: relative;
    overflow: hidden;
">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <div class="text-center mb-10 px-4" style="position: relative; z-index: 1;">
        <span class="inline-block text-[#FFD736] text-xs font-bold uppercase tracking-widest mb-3">
            Real Lessons, Real Skills
        </span>
        <h2 class="text-white font-black" style="font-size: clamp(2rem, 5vw, 3.2rem); letter-spacing: -0.03em;">
            Learn To Apply
        </h2>
    </div>

    <div class="swiper coaches-swiper">
        <div class="swiper-wrapper">

            @php
                $base = 'https://www.youtube.com/embed/';
                $params = '?rel=0&modestbranding=1&playsinline=1&controls=1&enablejsapi=1';
                // TODO: replace these placeholder topic labels with the real
                // lesson topic demonstrated in each video.
                $coaches = [
                    ['src' => $base.'XNypgaUtlRY'.$params, 'name' => 'Chord Voicings'],
                    ['src' => $base.'UOBAL7mmkHY'.$params, 'name' => 'Gospel Runs & Fills'],
                    ['src' => $base.'hwvpOGtSk6A'.$params, 'name' => 'Left Hand Grooves'],
                    ['src' => $base.'gVtATcXUaM0'.$params, 'name' => 'Praise Break Layering'],
                    ['src' => $base.'JqeVxcsKu4A'.$params, 'name' => 'Chord Substitutions'],
                    ['src' => $base.'XonpAmgCHtY'.$params, 'name' => 'Worship Intros'],
                    ['src' => $base.'ckNI-O_TuRc'.$params, 'name' => 'Modulation Techniques'],
                    ['src' => $base.'_kEHErnLoTk'.$params, 'name' => 'Reharmonization'],
                    ['src' => $base.'yPCoUFZ6csY'.$params, 'name' => 'Choir Backup Progressions'],
                ];
            @endphp

            @foreach ($coaches as $coach)
            <div class="swiper-slide">
                <div style="aspect-ratio:9/16; background:#000; border-radius:1.25rem;
                            overflow:hidden; box-shadow:0 12px 40px rgba(0,0,0,0.4);">
                    <iframe
                        src="{{ $coach['src'] }}"
                        data-src="{{ $coach['src'] }}"
                        title="{{ $coach['name'] }}"
                        frameborder="0"
                        style="width:100%;height:100%;display:block;"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
                <p style="color:rgba(255,255,255,0.8); font-size:13px; font-weight:600; text-align:center; margin-top:12px;">
                    {{ $coach['name'] }}
                </p>
            </div>
            @endforeach

        </div>

        <div class="swiper-button-prev coaches-prev"></div>
        <div class="swiper-button-next coaches-next"></div>
    </div>

    <div class="text-center mt-4" style="position: relative; z-index: 1;">
        <a href="/plans#pricing"
           class="inline-flex items-center gap-2 bg-[#FFD736] hover:bg-[#FFC700] text-black font-bold text-sm sm:text-base px-7 py-3 rounded-full shadow-lg shadow-[#FFD736]/25 transition-all duration-300 hover:-translate-y-0.5">
            Learn These Skills Yourself
            <i class="fa fa-angle-right"></i>
        </a>
    </div>

</section>

<style>
    .coaches-section { overflow-x: hidden; }

    .coaches-swiper {
        width: 100%;
        padding: 30px 0 50px !important;
    }

    /* Non-active slides shrink and fade */
    .coaches-swiper .swiper-slide {
        transition: transform 0.3s ease, opacity 0.3s ease;
        transform: scale(0.78);
        opacity: 0.6;
    }
    .coaches-swiper .swiper-slide-active {
        transform: scale(1);
        opacity: 1;
    }
    .coaches-swiper .swiper-slide-prev,
    .coaches-swiper .swiper-slide-next {
        transform: scale(0.88);
        opacity: 0.85;
    }

    /* Circular arrow buttons */
    .coaches-prev,
    .coaches-next {
        --swiper-navigation-color: #fff;
        --swiper-navigation-size: 20px;
        width: 44px !important;
        height: 44px !important;
        border-radius: 50%;
        background: rgba(0,0,0,0.4);
        top: 50%;
        transform: translateY(-50%);
        margin-top: 0;
    }
    .coaches-prev  { left: 14px;  }
    .coaches-next  { right: 14px; }
    .coaches-prev::after,
    .coaches-next::after { font-size: 14px !important; font-weight: 800; }
</style>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
(function () {
    function extractId(src) {
        var match = src.match(/embed\/([^?]+)/);
        return match ? match[1] : '';
    }

    // No autoplay — user must press play. `loop=1&playlist=<id>` makes a
    // single YouTube embed replay itself automatically once it finishes.
    function buildSrc(base) {
        return base + '&loop=1&playlist=' + extractId(base);
    }

    function applyLoop(swiper) {
        swiper.slides.forEach(function (slide) {
            var iframe = slide.querySelector('iframe[data-src]');
            if (!iframe) return;
            var base = iframe.getAttribute('data-src');
            iframe.src = buildSrc(base);
        });
    }

    function initCoaches() {
        var swiper = new Swiper('.coaches-swiper', {
            loop: true,
            centeredSlides: true,
            grabCursor: true,
            spaceBetween: 16,
            slidesPerView: 2,
            navigation: {
                nextEl: '.coaches-next',
                prevEl: '.coaches-prev',
            },
            breakpoints: {
                640:  { slidesPerView: 2,   spaceBetween: 16 },
                768:  { slidesPerView: 3,   spaceBetween: 18 },
                1024: { slidesPerView: 4,   spaceBetween: 22 },
            },
            on: {
                init: function (s) { applyLoop(s); },
            },
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoaches);
    } else {
        initCoaches();
    }
})();
</script>
