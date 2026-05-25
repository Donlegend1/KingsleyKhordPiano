import React, { useState, useEffect, useCallback, useRef } from 'react';
import ReactDOM from 'react-dom/client';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    useCarousel,
} from '@/components/ui/carousel';

const reels = [
    { id: 'DWlNC7ujJhw', url: 'https://www.instagram.com/reel/DWlNC7ujJhw', name: 'Kingsley Khord', title: 'Passing Chord to the 4' },
    { id: 'DYsKXPIswOa', url: 'https://www.instagram.com/reel/DYsKXPIswOa', name: 'Kingsley Khord', title: 'Full Breakdown on YouTube' },
    { id: 'DWqchJpDPFc', url: 'https://www.instagram.com/reel/DWqchJpDPFc', name: 'Kingsley Khord', title: "I'm Still Here — Dorinda Clark Cole" },
    { id: 'DL2a4D7iJar', url: 'https://www.instagram.com/reel/DL2a4D7iJar', name: 'Kingsley Khord', title: 'Piano Lesson — Full Breakdown' },
    { id: 'DLR5wW4MNKs', url: 'https://www.instagram.com/reel/DLR5wW4MNKs', name: 'Kingsley Khord', title: 'Passing Chord to the 2' },
    { id: 'DLUeSuDMqDg', url: 'https://www.instagram.com/reel/DLUeSuDMqDg', name: 'Kingsley Khord', title: 'Piano Movement for Your 5 Chord' },
    { id: 'DK9BocFsTP1', url: 'https://www.instagram.com/reel/DK9BocFsTP1', name: 'Kingsley Khord', title: 'Scale Over a Dominant 7th Chord' },
    { id: 'DGeHT8AsOIu', url: 'https://www.instagram.com/reel/DGeHT8AsOIu', name: 'Kingsley Khord', title: 'Incredible Piano Movement on the 3' },
    { id: 'DAJ2r8EI4Sq', url: 'https://www.instagram.com/reel/DAJ2r8EI4Sq', name: 'Kingsley Khord', title: 'Full Tutorial on YouTube' },
];

const MIDDLE_INDEX = Math.floor(reels.length / 2);

const ReelSlide = ({ reel, isActive, onClick }) => {
    const embedUrl = `${reel.url}/embed`;

    return (
        <div
            className={`transition-all duration-500 cursor-pointer ${
                isActive ? 'scale-100 opacity-100 z-10' : 'scale-[0.88] opacity-50 hover:opacity-70'
            }`}
            onClick={onClick}
        >
            <div className="relative aspect-[9/16] rounded-2xl overflow-hidden shadow-2xl bg-black mx-auto">
                <iframe
                    src={embedUrl}
                    className="w-full h-full border-0"
                    allowFullScreen
                    scrolling="no"
                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                    title={reel.name}
                    style={{ pointerEvents: isActive ? 'auto' : 'none' }}
                />
                {!isActive && (
                    <div className="absolute inset-0 bg-black/10" />
                )}
            </div>
            {isActive && (
                <div className="text-center mt-4 text-white">
                    <h3 className="text-lg font-bold">{reel.name}</h3>
                    <p className="text-sm opacity-80">{reel.title} &gt;</p>
                </div>
            )}
        </div>
    );
};

const CarouselNavButton = ({ direction, onClick }) => (
    <button
        onClick={onClick}
        className={`absolute top-1/2 -translate-y-1/2 z-20 w-10 h-10 md:w-12 md:h-12 rounded-full 
            bg-white/15 hover:bg-white/25 backdrop-blur-sm border border-white/25
            flex items-center justify-center text-white transition-all duration-200
            ${direction === 'prev' ? 'left-2 md:left-6' : 'right-2 md:right-6'}`}
        aria-label={direction === 'prev' ? 'Previous slide' : 'Next slide'}
    >
        {direction === 'prev'
            ? <ChevronLeft className="w-5 h-5 md:w-6 md:h-6" />
            : <ChevronRight className="w-5 h-5 md:w-6 md:h-6" />
        }
    </button>
);

const CarouselDots = ({ total, current, onDotClick }) => (
    <div className="flex justify-center gap-2 mt-8">
        {Array.from({ length: total }).map((_, index) => (
            <button
                key={index}
                onClick={() => onDotClick(index)}
                className={`rounded-full transition-all duration-300 ${
                    index === current
                        ? 'bg-[#FFD736] w-6 h-2.5'
                        : 'bg-white/25 w-2.5 h-2.5 hover:bg-white/40'
                }`}
                aria-label={`Go to slide ${index + 1}`}
            />
        ))}
    </div>
);

const CarouselInner = () => {
    const { api } = useCarousel();
    const [current, setCurrent] = useState(MIDDLE_INDEX);
    const [count, setCount] = useState(0);
    const initialized = useRef(false);

    useEffect(() => {
        if (!api) return;

        if (!initialized.current) {
            api.scrollTo(MIDDLE_INDEX, true);
            initialized.current = true;
        }

        setCount(api.scrollSnapList().length);
        setCurrent(api.selectedScrollSnap());

        const onSelect = () => setCurrent(api.selectedScrollSnap());
        api.on('select', onSelect);
        return () => api.off('select', onSelect);
    }, [api]);

    const scrollPrev = useCallback(() => api?.scrollPrev(), [api]);
    const scrollNext = useCallback(() => api?.scrollNext(), [api]);
    const scrollTo = useCallback((index) => api?.scrollTo(index), [api]);

    return (
        <>
            <CarouselContent className="-ml-2 md:-ml-3 py-2">
                {reels.map((reel, index) => (
                    <CarouselItem
                        key={reel.id + '-' + index}
                        className="pl-2 md:pl-3 basis-[45%] sm:basis-[30%] md:basis-[22%] lg:basis-[18%]"
                    >
                        <ReelSlide
                            reel={reel}
                            isActive={index === current}
                            onClick={() => scrollTo(index)}
                        />
                    </CarouselItem>
                ))}
            </CarouselContent>

            <CarouselNavButton direction="prev" onClick={scrollPrev} />
            <CarouselNavButton direction="next" onClick={scrollNext} />
            <CarouselDots total={count} current={current} onDotClick={scrollTo} />
        </>
    );
};

const CoachReels = () => {
    return (
        <section className="bg-[#172554] py-16 overflow-hidden">
            <div className="text-center mb-10 px-4">
                <h2 class="text-4xl md:text-3xl  font-extrabold text-[#FFD736] tracking-tight">Listen, Learn and Apply</h2>
            </div>

            <div className="relative py-4">
                <Carousel
                    opts={{
                        align: 'center',
                        loop: true,
                        slidesToScroll: 1,
                        containScroll: false,
                    }}
                >
                    <CarouselInner />
                </Carousel>
            </div>
        </section>
    );
};

export default CoachReels;

if (document.getElementById('coach-reels')) {
    const root = ReactDOM.createRoot(document.getElementById('coach-reels'));
    root.render(<CoachReels />);
}
