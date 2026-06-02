<section class="coaches-section py-14" style="background-color: #0d1b3e;">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <h2 class="text-center text-white font-bold mb-10"
        style="font-size: clamp(1.4rem, 4vw, 2.2rem); letter-spacing: 0.04em;">
        Learn To Apply
    </h2>

    <div class="swiper coaches-swiper">
        <div class="swiper-wrapper">

            @php
                $base = 'https://www.youtube.com/embed/';
                $params = '?rel=0&modestbranding=1&playsinline=1&controls=1';
                $coaches = [
                    ['src' => $base.'JqeVxcsKu4A'.$params, 'name' => 'Coach 1'],
                    ['src' => $base.'ckNI-O_TuRc'.$params,  'name' => 'Coach 2'],
                    ['src' => $base.'gVtATcXUaM0'.$params,  'name' => 'Coach 3'],
                    ['src' => $base.'F_TPjWB3dYg'.$params,  'name' => 'Coach 4'],
                    ['src' => $base.'_kEHErnLoTk'.$params,  'name' => 'Coach 5'],
                    ['src' => $base.'qcfC5zn1hzs'.$params,  'name' => 'Coach 6'],
                    ['src' => $base.'HR0AU0Xd1RE'.$params,  'name' => 'Coach 7'],
                    ['src' => $base.'ghC7W6keZ-Y'.$params,  'name' => 'Coach 8'],
                    ['src' => $base.'yHsgcyA4tZ0'.$params,  'name' => 'Coach 9'],
                ];
            @endphp

            @foreach ($coaches as $coach)
            <div class="swiper-slide">
                <div style="aspect-ratio:9/16; background:#000; border-radius:1.5rem;
                            overflow:hidden; border:3px solid rgba(255,255,255,0.9);
                            box-shadow:0 12px 40px rgba(0,0,0,0.4);">
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
            </div>
            @endforeach

        </div>

        <div class="swiper-button-prev coaches-prev"></div>
        <div class="swiper-button-next coaches-next"></div>
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
    function updateAutoplay(swiper) {
        swiper.slides.forEach(function (slide) {
            var iframe = slide.querySelector('iframe[data-src]');
            if (!iframe) return;
            var base = iframe.getAttribute('data-src');
            if (slide.classList.contains('swiper-slide-active')) {
                iframe.src = base + '&autoplay=1&mute=1';
            } else {
                // Reset src to stop playback on non-active slides
                iframe.src = base;
            }
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
                init:                    function (s) { updateAutoplay(s); },
                slideChangeTransitionEnd: function (s) { updateAutoplay(s); },
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
