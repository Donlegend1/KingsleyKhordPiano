@extends('layouts.admin')

@section('content')

<main class="flex-1 p-6 bg-gray-50 min-h-screen" x-data="{ tab: 'video' }">

    {{-- Top bar: back link + category switcher --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 max-w-5xl">
        <a href="{{ route('admin.audio-quiz') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-gray-400 hover:text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Audio Quiz
        </a>

        <select onchange="if (this.value) window.location = this.value"
            class="bg-white border border-gray-200 rounded-full pl-4 pr-9 py-2 text-[12px] font-semibold text-gray-700 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
            @foreach ($allCategories as $cat)
                <option value="{{ route('admin.audio-quiz', ['category' => $cat['db_category']]) }}" {{ $cat['db_category'] === $category ? 'selected' : '' }}>
                    {{ $cat['label'] }}
                </option>
            @endforeach
        </select>
    </div>

    @if (session('success'))
        <div class="max-w-5xl mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-[13px] font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-5xl mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-[13px] font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-5 max-w-5xl">

        {{-- Left sidebar --}}
        <div>
            {{-- Category name (editable) --}}
            <div x-data="{ editing: false }" class="mb-4">
                <div x-show="!editing" class="flex items-center gap-2">
                    <p class="text-lg font-bold text-gray-900">{{ $categoryLabel }}</p>
                    <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-pen text-[11px]"></i>
                    </button>
                </div>

                <form x-show="editing" x-cloak action="{{ route('admin.audio-quiz.category.rename', ['category' => $category]) }}" method="POST" class="space-y-2">
                    @csrf
                    <div class="flex items-center gap-2">
                        <input type="text" name="name" value="{{ $categoryLabel }}" required
                            class="flex-1 min-w-0 bg-white border border-gray-900 rounded-lg px-3 py-1.5 text-[14px] font-bold text-gray-900 outline-none">
                        <button type="submit" class="text-green-600 hover:text-green-700 flex-shrink-0" title="Save">
                            <i class="fa-solid fa-check text-[13px]"></i>
                        </button>
                        <button type="button" @click="editing = false" class="text-gray-300 hover:text-gray-600 flex-shrink-0" title="Cancel">
                            <i class="fa-solid fa-xmark text-[13px]"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400">Renaming will require re-linking fixed answer options for this category's lessons.</p>
                </form>
            </div>

            <div class="space-y-1.5 max-h-[560px] overflow-y-auto pr-1">
                @foreach ($lessons as $lesson)
                    @php $isActive = $activeLesson && $lesson->id === $activeLesson->id; @endphp
                    <div x-data="{ editing: false }" class="relative">
                        <a href="{{ route('admin.audio-quiz', ['category' => $category, 'quiz' => $lesson->id]) }}"
                            x-show="!editing"
                            class="flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border bg-white transition-colors
                            {{ $isActive ? 'border-gray-900' : 'border-gray-200 hover:border-gray-300' }}">
                            <span class="text-[12.5px] truncate {{ $isActive ? 'font-bold text-gray-900' : 'text-gray-600' }}">
                                {{ $lesson->title }}
                            </span>
                            <span class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[10px] font-bold text-gray-400">{{ $lesson->questions->count() }}</span>
                                <button type="button" @click.stop.prevent="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                </button>
                                <span class="w-3.5 h-3.5 rounded-full border
                                    {{ $isActive ? 'border-gray-900 bg-gray-900' : 'border-gray-300' }}"></span>
                            </span>
                        </a>

                        <form x-show="editing" x-cloak action="{{ route('admin.audio-quiz.lesson.rename', ['quiz' => $lesson->id]) }}" method="POST"
                            class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-gray-900 bg-white">
                            @csrf
                            <input type="text" name="title" value="{{ $lesson->title }}" required
                                class="flex-1 min-w-0 text-[12.5px] border-0 focus:ring-0 p-0 outline-none">
                            <button type="submit" class="text-green-600 hover:text-green-700 flex-shrink-0" title="Save">
                                <i class="fa-solid fa-check text-[12px]"></i>
                            </button>
                            <button type="button" @click="editing = false" class="text-gray-300 hover:text-gray-600 flex-shrink-0" title="Cancel">
                                <i class="fa-solid fa-xmark text-[12px]"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main panel --}}
        <div>
            @if ($activeLesson)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                    <h2 class="text-lg font-bold text-gray-900 mb-1">{{ $activeLesson->title }}</h2>
                    <p class="text-[12px] text-gray-400">{{ $activeLesson->questions->count() }} question(s) configured</p>
                </div>

                {{-- Tabs: Video / Difficulty / Reference Audio / Guide Text --}}
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden mb-5">
                    <div class="flex items-center gap-1 p-1.5 border-b border-gray-100 bg-gray-50">
                        <button type="button" @click="tab = 'video'"
                            :class="tab === 'video' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-bold transition-all">
                            <i class="fa-solid fa-video text-[11px]"></i> Video
                        </button>
                        <button type="button" @click="tab = 'difficulty'"
                            :class="tab === 'difficulty' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-bold transition-all">
                            <i class="fa-solid fa-gauge text-[11px]"></i> Difficulty
                        </button>
                        <button type="button" @click="tab = 'audio'"
                            :class="tab === 'audio' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-bold transition-all">
                            <i class="fa-solid fa-headphones text-[11px]"></i> Reference Audio
                        </button>
                        <button type="button" @click="tab = 'prompt'"
                            :class="tab === 'prompt' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-bold transition-all">
                            <i class="fa-solid fa-comment-dots text-[11px]"></i> Guide Text
                        </button>
                    </div>

                    <div class="p-6">
                        {{-- Video tab --}}
                        <div x-show="tab === 'video'" x-cloak>
                            <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="space-y-3">
                                @csrf
                                <label class="block text-[12px] font-semibold text-gray-600">Embed video</label>
                                <p class="text-[11px] text-gray-400 -mt-1">Paste a VdoCipher, Wistia, or other iframe/script embed code — same as the roadmap videos.</p>
                                <textarea name="video_url" rows="4" placeholder="Paste a VdoCipher iframe embed, or any other embed URL / iframe / script code..."
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[13px] text-gray-800 font-mono placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">{{ $activeLesson->video_url }}</textarea>
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-2.5 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-check text-[11px]"></i> Save Video
                                </button>
                            </form>
                        </div>

                        {{-- Difficulty tab --}}
                        <div x-show="tab === 'difficulty'" x-cloak>
                            <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="space-y-3">
                                @csrf
                                <label class="block text-[12px] font-semibold text-gray-600">Difficulty level</label>
                                <select name="difficulty" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                    @foreach (['Beginner', 'Intermediate', 'Advanced'] as $level)
                                        <option value="{{ $level }}" {{ $activeLesson->difficulty === $level ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-2.5 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-check text-[11px]"></i> Save Difficulty
                                </button>
                            </form>
                        </div>

                        {{-- Reference audio tab --}}
                        <div x-show="tab === 'audio'" x-cloak class="space-y-5">
                            <p class="text-[11px] text-gray-400">Optional. Add one or more named reference clips (e.g. "C Major Scale") for students to listen to before taking the quiz.</p>

                            @if ($activeLesson->referenceAudios->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach ($activeLesson->referenceAudios as $refAudio)
                                        <div class="flex items-center gap-3 border border-gray-100 rounded-xl px-3.5 py-2.5">
                                            <span class="text-[12.5px] font-semibold text-gray-700 flex-shrink-0 w-32 truncate">
                                                {{ $refAudio->name ?: 'Untitled' }}
                                            </span>
                                            <audio src="{{ asset($refAudio->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>
                                            <form action="{{ route('admin.audio-quiz.reference-audio.destroy', ['referenceAudio' => $refAudio->id]) }}"
                                                method="POST" onsubmit="return confirm('Delete this reference audio?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                                    <i class="fa-solid fa-trash text-[13px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form action="{{ route('admin.audio-quiz.reference-audio.store', ['quiz' => $activeLesson->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-3 border-t border-gray-100 pt-4">
                                @csrf
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Add reference audio</label>
                                <input type="text" name="name" placeholder="Name (e.g. C Major Scale)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-2.5 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-plus text-[11px]"></i> Add Reference Audio
                                </button>
                            </form>
                        </div>

                        {{-- Guide text tab --}}
                        <div x-show="tab === 'prompt'" x-cloak>
                            <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="space-y-3">
                                @csrf
                                <label class="block text-[12px] font-semibold text-gray-600">Question guide text</label>
                                <p class="text-[11px] text-gray-400 -mt-1">Shown above the audio player while a student answers each question, e.g. "Identify the interval you hear." Leave blank to use the default wording for this quiz type.</p>
                                <input type="text" name="question_prompt" value="{{ $activeLesson->question_prompt }}" maxlength="255"
                                    placeholder="e.g. Identify the interval you hear."
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-2.5 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-check text-[11px]"></i> Save Guide Text
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Fixed options / melody sequence + questions --}}
                @if ($answerType === 'sequence')
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Correct melody</label>
                                @include('admin.audio-quiz.partials.note-sequence-picker', ['name' => 'correct_option', 'initialSequence' => [], 'maxNotes' => $sequenceNoteCount, 'allowBlackKeys' => $allowBlackKeys])
                            </div>

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @elseif ($answerType === 'chord-sequence')
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Correct progression</label>
                                @include('admin.audio-quiz.partials.option-sequence-picker', ['name' => 'correct_option', 'initialSequence' => [], 'maxNotes' => $chordSequenceLength, 'options' => $chordSequenceOptions])
                            </div>

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @elseif ($answerType === 'chord-naming')
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            @include('admin.audio-quiz.partials.chord-naming-picker', ['options' => $chordNamingOptions])

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @elseif ($answerType === 'progression-recognition')
                    @if (!$activeLesson->progression_numbers)
                        <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                            <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-2">Set Progression Pattern</p>
                            <p class="text-[12px] text-gray-400 mb-3">The scale-degree numbers for this progression (e.g. "2,5,1"). Set this once — every question will then show one chord-quality dropdown per position, ready to fill in.</p>
                            <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <input type="text" name="progression_numbers" placeholder="e.g. 2,5,1" required
                                    class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors flex-shrink-0">
                                    <i class="fa-solid fa-check text-[11px]"></i> Save
                                </button>
                            </form>
                        </div>
                    @else
                        @php $prPositions = explode(',', $activeLesson->progression_numbers); @endphp
                        <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">Add Question</p>
                                <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    <span class="text-[11px] text-gray-400">Pattern:</span>
                                    <input type="text" name="progression_numbers" value="{{ $activeLesson->progression_numbers }}"
                                        class="w-20 bg-gray-50 border border-gray-200 rounded-full px-2.5 py-1 text-[11px] font-semibold text-gray-700 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                    <button type="submit" class="text-[11px] font-semibold text-gray-400 hover:text-gray-800 transition-colors">Update</button>
                                </form>
                            </div>

                            <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                                method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                    <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                        class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                                </div>

                                <div>
                                    <p class="text-[12px] font-semibold text-gray-600 mb-2">Select the chord quality for each chord</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @foreach ($prPositions as $idx => $num)
                                            <div class="border border-gray-100 rounded-xl p-3">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-900 text-white text-[10px] font-bold flex-shrink-0">{{ $num }}</span>
                                                    <span class="text-[12px] font-semibold text-gray-700">Chord {{ $idx + 1 }}</span>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mb-1">Chord quality</p>
                                                <select name="qualities[]" required
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[12.5px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                                    <option value="" disabled selected>Select</option>
                                                    @foreach ($progressionQualityOptions as $qIdx => $qLabel)
                                                        <option value="{{ $qIdx }}">{{ $qLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-plus text-[11px]"></i>
                                    Add Question
                                </button>
                            </form>
                        </div>
                    @endif
                @elseif ($answerType === 'progression-degree')
                    @if (!$activeLesson->progression_numbers)
                        <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                            <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-2">Set Progression Pattern</p>
                            <p class="text-[12px] text-gray-400 mb-3">The scale-degree numbers for this progression (e.g. "2,5,1"). Set this once — every question will then show one chord-quality dropdown plus extension pills per position, ready to fill in.</p>
                            <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <input type="text" name="progression_numbers" placeholder="e.g. 2,5,1" required
                                    class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors flex-shrink-0">
                                    <i class="fa-solid fa-check text-[11px]"></i> Save
                                </button>
                            </form>
                        </div>
                    @else
                        @php $pdPositions = explode(',', $activeLesson->progression_numbers); @endphp
                        <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">Add Question</p>
                                <form action="{{ route('admin.audio-quiz.lesson.update', ['quiz' => $activeLesson->id]) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    <span class="text-[11px] text-gray-400">Pattern:</span>
                                    <input type="text" name="progression_numbers" value="{{ $activeLesson->progression_numbers }}"
                                        class="w-20 bg-gray-50 border border-gray-200 rounded-full px-2.5 py-1 text-[11px] font-semibold text-gray-700 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                    <button type="submit" class="text-[11px] font-semibold text-gray-400 hover:text-gray-800 transition-colors">Update</button>
                                </form>
                            </div>

                            <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                                method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                    <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                        class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                                </div>

                                <div class="space-y-3">
                                    <p class="text-[12px] font-semibold text-gray-600">Select the chord quality (and extensions) for each chord</p>
                                    @foreach ($pdPositions as $idx => $num)
                                        <div class="border border-gray-100 rounded-xl p-3 space-y-2.5">
                                            <div class="flex items-center gap-2">
                                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-900 text-white text-[10px] font-bold flex-shrink-0">{{ $num }}</span>
                                                <span class="text-[12px] font-semibold text-gray-700">Chord {{ $idx + 1 }}</span>
                                            </div>

                                            <div>
                                                <p class="text-[11px] text-gray-400 mb-1">Chord quality</p>
                                                <select name="qualities[]" required
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[12.5px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                                    <option value="" disabled selected>Select quality</option>
                                                    @foreach ($progressionQualityOptions as $qIdx => $qLabel)
                                                        <option value="{{ $qIdx }}">{{ $qLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <p class="text-[11px] text-gray-400 mb-1">Extensions (optional, select all that apply)</p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($progressionDegreeOptions as $dIdx => $dLabel)
                                                        <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-gray-200 text-[11px] font-semibold text-gray-600 cursor-pointer has-[:checked]:bg-gray-900 has-[:checked]:text-white has-[:checked]:border-gray-900 transition-colors">
                                                            <input type="checkbox" name="degrees[{{ $idx }}][]" value="{{ $dIdx }}" class="hidden">
                                                            {{ $dLabel }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                    <i class="fa-solid fa-plus text-[11px]"></i>
                                    Add Question
                                </button>
                            </form>
                        </div>
                    @endif
                @elseif ($answerType === 'none')
                    <div class="bg-white border border-dashed border-gray-200 rounded-2xl px-6 py-10 text-center mb-5">
                        <p class="text-[13px] font-medium text-gray-500">Fixed answer options haven't been set up for this lesson yet.</p>
                        <p class="text-[12px] text-gray-400 mt-1">We'll wire this category up in a future step.</p>
                    </div>
                @elseif ($answerType === 'compound')
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-3">Fixed Answer Options</p>
                        <p class="text-[11px] text-gray-400 mb-3">This lesson needs both a chord quality and an inversion.</p>
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach ($compoundOptions['quality'] as $option)
                                <span class="px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-[12px] font-semibold text-gray-700">{{ $option }}</span>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($compoundOptions['inversion'] as $option)
                                <span class="px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-[12px] font-semibold text-indigo-700">{{ $option }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Chord quality</label>
                                    <select name="quality" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                        <option value="" disabled selected>Select quality</option>
                                        @foreach ($compoundOptions['quality'] as $index => $option)
                                            <option value="{{ $index }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Inversion</label>
                                    <select name="inversion" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                        <option value="" disabled selected>Select inversion</option>
                                        @foreach ($compoundOptions['inversion'] as $index => $option)
                                            <option value="{{ $index }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @elseif ($answerType === 'multiselect')
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-3">Fixed Answer Options</p>
                        <p class="text-[11px] text-gray-400 mb-3">This lesson can have more than one correct answer per question.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($fixedOptions as $option)
                                <span class="px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-[12px] font-semibold text-gray-700">{{ $option }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Correct answers (select all that apply)</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($fixedOptions as $index => $option)
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 text-[12px] font-semibold text-gray-700 cursor-pointer has-[:checked]:bg-gray-900 has-[:checked]:text-white has-[:checked]:border-gray-900 transition-colors">
                                            <input type="checkbox" name="correct_option[]" value="{{ $index }}" class="hidden">
                                            {{ $option }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-3">Fixed Answer Options</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($fixedOptions as $option)
                                <span class="px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-[12px] font-semibold text-gray-700">
                                    {{ $option }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-5">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-4">Add Question</p>

                        <form action="{{ route('admin.audio-quiz.questions.store', ['quiz' => $activeLesson->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Audio file</label>
                                <input type="file" name="audio" accept=".mp3,.wav,.ogg" required
                                    class="block w-full text-[13px] text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-black border border-gray-200 rounded-xl px-1 py-1">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Correct answer</label>
                                <select name="correct_option" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-[14px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none">
                                    <option value="" disabled selected>Select the correct option</option>
                                    @foreach ($fixedOptions as $index => $option)
                                        <option value="{{ $index }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="flex items-center gap-2 bg-gray-900 text-white text-[13px] font-semibold px-5 py-3 rounded-full hover:bg-black transition-colors">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                Add Question
                            </button>
                        </form>
                    </div>
                @endif

                <div class="bg-white border border-gray-100 rounded-2xl p-6">
                    <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-3">Questions</p>
                    @forelse ($activeLesson->questions as $question)
                        @if ($answerType === 'sequence')
                            @php
                                $seqIndices = array_map('intval', explode(',', $question->correct_option));
                                $seqLabels = array_map(fn ($i) => $sequenceLabels[$i] ?? $i, $seqIndices);
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                                            {{ implode(' → ', $seqLabels) }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change melody">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @include('admin.audio-quiz.partials.note-sequence-picker', ['name' => 'correct_option', 'initialSequence' => $seqIndices, 'maxNotes' => $sequenceNoteCount, 'allowBlackKeys' => $allowBlackKeys])
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'chord-sequence')
                            @php
                                $chordSeqIndices = array_map('intval', explode(',', $question->correct_option));
                                $chordSeqLabels = array_map(fn ($i) => $chordSequenceOptions[$i] ?? $i, $chordSeqIndices);
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                                            {{ implode(' → ', $chordSeqLabels) }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change progression">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @include('admin.audio-quiz.partials.option-sequence-picker', ['name' => 'correct_option', 'initialSequence' => $chordSeqIndices, 'maxNotes' => $chordSequenceLength, 'options' => $chordSequenceOptions])
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'progression-degree')
                            @php
                                $pdParts = explode(';', $question->correct_option);
                                $pdNumbers = $pdParts[0] ?? '';
                                $pdGroups = array_slice($pdParts, 1);
                                $pdQualities = [];
                                $pdDegreesPerPosition = [];
                                $pdSummaryParts = [];
                                foreach (array_values(explode(',', $pdNumbers)) as $posIdx => $num) {
                                    $group = $pdGroups[$posIdx] ?? '';
                                    [$qToken, $dToken] = array_pad(explode(',', $group, 2), 2, '');
                                    $qIdx = $qToken !== '' ? (int) $qToken : null;
                                    $degIndices = $dToken !== '' ? array_map('intval', explode('-', $dToken)) : [];
                                    $pdQualities[] = $qIdx;
                                    $pdDegreesPerPosition[] = $degIndices;

                                    $qLabel = $qIdx !== null ? ($progressionQualityOptions[$qIdx] ?? $qIdx) : '?';
                                    $degLabels = array_map(fn ($i) => $progressionDegreeOptions[$i] ?? $i, $degIndices);
                                    $pdSummaryParts[] = $num . ':' . $qLabel . (count($degLabels) ? ' (' . implode(',', $degLabels) . ')' : '');
                                }
                                $pdSummary = implode(' · ', $pdSummaryParts);
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap max-w-[260px] truncate" title="{{ $pdSummary }}">
                                            {{ $pdSummary }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answer">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @foreach (explode(',', $pdNumbers) as $idx => $num)
                                            <div class="border border-gray-100 rounded-xl p-3 space-y-2.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-900 text-white text-[10px] font-bold flex-shrink-0">{{ $num }}</span>
                                                    <span class="text-[12px] font-semibold text-gray-700">Chord {{ $idx + 1 }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] text-gray-400 mb-1">Chord quality</p>
                                                    <select name="qualities[]" required
                                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[12.5px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                                        <option value="" disabled>Select quality</option>
                                                        @foreach ($progressionQualityOptions as $qIdx => $qLabel)
                                                            <option value="{{ $qIdx }}" {{ ($pdQualities[$idx] ?? null) === $qIdx ? 'selected' : '' }}>{{ $qLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] text-gray-400 mb-1">Extensions (optional)</p>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach ($progressionDegreeOptions as $dIdx => $dLabel)
                                                            <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-gray-200 text-[11px] font-semibold text-gray-600 cursor-pointer has-[:checked]:bg-gray-900 has-[:checked]:text-white has-[:checked]:border-gray-900 transition-colors">
                                                                <input type="checkbox" name="degrees[{{ $idx }}][]" value="{{ $dIdx }}" class="hidden" {{ in_array($dIdx, $pdDegreesPerPosition[$idx] ?? []) ? 'checked' : '' }}>
                                                                {{ $dLabel }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'progression-recognition')
                            @php
                                $prParts = explode(';', $question->correct_option);
                                $prNumbers = $prParts[0] ?? '';
                                $prQualityTokens = ($prParts[1] ?? '') !== '' ? explode('|', $prParts[1]) : [];
                                $prQualityIndices = array_map(fn ($t) => $t === '' ? null : (int) $t, $prQualityTokens);
                                $prQualityLabels = array_map(
                                    fn ($i) => $i === null ? '?' : ($progressionQualityOptions[$i] ?? $i),
                                    $prQualityIndices
                                );
                                $prSummary = collect(explode(',', $prNumbers))
                                    ->map(fn ($num, $idx) => $num . ':' . ($prQualityLabels[$idx] ?? '?'))
                                    ->implode(' · ');
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap max-w-[240px] truncate" title="{{ $prSummary }}">
                                            {{ $prSummary }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answer">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            @foreach (explode(',', $prNumbers) as $idx => $num)
                                                <div class="border border-gray-100 rounded-xl p-3">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-900 text-white text-[10px] font-bold flex-shrink-0">{{ $num }}</span>
                                                        <span class="text-[12px] font-semibold text-gray-700">Chord {{ $idx + 1 }}</span>
                                                    </div>
                                                    <p class="text-[11px] text-gray-400 mb-1">Chord quality</p>
                                                    <select name="qualities[]" required
                                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[12.5px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                                        <option value="" disabled>Select</option>
                                                        @foreach ($progressionQualityOptions as $qIdx => $qLabel)
                                                            <option value="{{ $qIdx }}" {{ ($prQualityIndices[$idx] ?? null) === $qIdx ? 'selected' : '' }}>{{ $qLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'chord-naming')
                            @php
                                $cnParts = explode(';', $question->correct_option);
                                $cnQuality = isset($cnParts[0]) && $cnParts[0] !== '' ? (int) $cnParts[0] : null;
                                $cnDegreeIndices = [];
                                $cnDegreeAccidentals = [];
                                foreach (($cnParts[1] ?? '') !== '' ? explode(',', $cnParts[1]) : [] as $token) {
                                    if (preg_match('/^(\d+)([#b])?$/', trim($token), $m)) {
                                        $idx = (int) $m[1];
                                        $cnDegreeIndices[] = $idx;
                                        if (!empty($m[2])) {
                                            $cnDegreeAccidentals[$idx] = $m[2];
                                        }
                                    }
                                }
                                $cnExcludeIndices = ($cnParts[2] ?? '') !== '' ? array_map('intval', explode(',', $cnParts[2])) : [];

                                $cnQualityLabel = $chordNamingOptions['quality'][$cnQuality] ?? '?';
                                $cnDegreeLabels = array_map(
                                    fn ($i) => ($chordNamingOptions['degree'][$i] ?? $i) . ($cnDegreeAccidentals[$i] ?? ''),
                                    $cnDegreeIndices
                                );
                                $cnExcludeLabels = array_map(fn ($i) => $chordNamingOptions['exclude'][$i] ?? $i, $cnExcludeIndices);
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap max-w-[220px] truncate" title="{{ $cnQualityLabel }} · {{ implode(', ', $cnDegreeLabels) }} · {{ implode(', ', $cnExcludeLabels) }}">
                                            {{ $cnQualityLabel }} · {{ implode(', ', $cnDegreeLabels) }} · {{ implode(', ', $cnExcludeLabels) }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answer">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-4">
                                        @csrf
                                        @include('admin.audio-quiz.partials.chord-naming-picker', [
                                            'options' => $chordNamingOptions,
                                            'initialQuality' => $cnQuality,
                                            'initialDegrees' => $cnDegreeIndices,
                                            'initialAccidentals' => $cnDegreeAccidentals,
                                            'initialExclude' => $cnExcludeIndices,
                                        ])
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'compound')
                            @php
                                [$qIdx, $invIdx] = array_pad(array_map('intval', explode(',', $question->correct_option)), 2, 0);
                                $qLabel = $compoundOptions['quality'][$qIdx] ?? $qIdx;
                                $invLabel = $compoundOptions['inversion'][$invIdx] ?? $invIdx;
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                                            {{ $qLabel }} / {{ $invLabel }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answer">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-3">
                                            <select name="quality" required
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-[13px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                                @foreach ($compoundOptions['quality'] as $index => $option)
                                                    <option value="{{ $index }}" {{ $index === $qIdx ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                            <select name="inversion" required
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-[13px] text-gray-800 focus:bg-white focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                                @foreach ($compoundOptions['inversion'] as $index => $option)
                                                    <option value="{{ $index }}" {{ $index === $invIdx ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($answerType === 'multiselect')
                            @php
                                $selectedIndices = array_map('intval', explode(',', $question->correct_option));
                                $selectedLabels = array_map(fn ($i) => $fixedOptions[$i] ?? $i, $selectedIndices);
                            @endphp
                            <div x-data="{ editing: false }" class="py-3 border-b border-gray-50 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                    <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                                            {{ implode(', ', $selectedLabels) }}
                                        </span>
                                        <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answers">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                        method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                            <i class="fa-solid fa-trash text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" x-cloak class="mt-3">
                                    <form action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($fixedOptions as $index => $option)
                                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 text-[12px] font-semibold text-gray-700 cursor-pointer has-[:checked]:bg-gray-900 has-[:checked]:text-white has-[:checked]:border-gray-900 transition-colors">
                                                    <input type="checkbox" name="correct_option[]" value="{{ $index }}" class="hidden" {{ in_array($index, $selectedIndices) ? 'checked' : '' }}>
                                                    {{ $option }}
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                class="flex items-center gap-1.5 bg-gray-900 text-white text-[12px] font-semibold px-4 py-2 rounded-full hover:bg-black transition-colors">
                                                <i class="fa-solid fa-check text-[10px]"></i> Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                class="text-[12px] font-semibold text-gray-500 hover:text-gray-800 px-4 py-2 rounded-full border border-gray-200 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div x-data="{ editing: false }" class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-b-0">
                                <audio src="{{ asset($question->audio_path) }}" controls class="h-9 flex-1 min-w-0"></audio>

                                <div x-show="!editing" class="flex items-center gap-2 flex-shrink-0">
                                    <span class="px-2.5 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                                        {{ $fixedOptions[$question->correct_option] ?? $question->correct_option }}
                                    </span>
                                    <button type="button" @click="editing = true" class="text-gray-300 hover:text-gray-600 transition-colors" title="Change correct answer">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </button>
                                </div>

                                <form x-show="editing" x-cloak action="{{ route('admin.audio-quiz.questions.update', ['question' => $question->id]) }}" method="POST"
                                    class="flex items-center gap-2 flex-shrink-0">
                                    @csrf
                                    <select name="correct_option" onchange="this.form.submit()"
                                        class="bg-gray-50 border border-gray-200 rounded-full pl-3 pr-7 py-1.5 text-[11px] font-semibold text-gray-700 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none">
                                        @foreach ($fixedOptions as $index => $option)
                                            <option value="{{ $index }}" {{ $index === (int) $question->correct_option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="editing = false" class="text-gray-300 hover:text-gray-600 transition-colors" title="Cancel">
                                        <i class="fa-solid fa-xmark text-[12px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.audio-quiz.questions.destroy', ['question' => $question->id]) }}"
                                    method="POST" onsubmit="return confirm('Delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors flex-shrink-0" title="Delete">
                                        <i class="fa-solid fa-trash text-[13px]"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @empty
                        <div class="border border-dashed border-gray-200 rounded-xl px-6 py-10 text-center">
                            <p class="text-[13px] font-medium text-gray-500">No questions yet for this lesson.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="bg-white border border-dashed border-gray-200 rounded-2xl px-6 py-16 text-center">
                    <p class="text-[13px] font-medium text-gray-500">No lessons found for {{ $category }}.</p>
                </div>
            @endif
        </div>
    </div>
</main>

@endsection
