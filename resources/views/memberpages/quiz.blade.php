@extends('layouts.member')

@php
  $noHeader = true;
  $noFooter = true;
@endphp

@section('content')
<script>
  window.quizQuestions = @json($questions);
</script>
<div
  x-data="{
    currentQuestionIndex: 0,
    questions: window.quizQuestions,
    answers: {},
    init() {
      // Initialize empty arrays for multi-select questions
      this.questions.forEach(q => {
        if (q.type === 'multi') {
          this.answers[q.id] = [];
        } else {
          this.answers[q.id] = '';
        }
      });
    },
    get currentQuestion() {
      return this.questions[this.currentQuestionIndex];
    },
    get answeredCount() {
      return this.questions.filter(q => {
        if (q.type === 'multi') {
          return this.answers[q.id] && this.answers[q.id].length > 0;
        }
        return this.answers[q.id] !== '';
      }).length;
    },
    get progressPercent() {
      return Math.round((this.answeredCount / this.questions.length) * 100);
    },
    toggleMultiSelect(questionId, optionId) {
      if (!this.answers[questionId]) {
        this.answers[questionId] = [];
      }
      const idx = this.answers[questionId].indexOf(optionId);
      if (idx > -1) {
        this.answers[questionId].splice(idx, 1);
      } else {
        this.answers[questionId].push(optionId);
      }
    },
    isOptionSelected(questionId, optionValue, isMulti = false) {
      if (isMulti) {
        return this.answers[questionId] && this.answers[questionId].includes(optionValue);
      }
      return this.answers[questionId] === optionValue;
    },
    selectSingle(questionId, optionValue) {
      this.answers[questionId] = optionValue;
    },
    canGoNext() {
      const q = this.currentQuestion;
      if (!q) return false;
      if (q.type === 'multi') {
        // Multi-select questions are optional or require at least one select. Let's allow empty selections or enforce.
        // For Q15 (informational) or Q8, we can let them click Next.
        return true;
      }
      return this.answers[q.id] !== '';
    },
    nextQuestion() {
      if (this.currentQuestionIndex < this.questions.length - 1) {
        this.currentQuestionIndex++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        this.submitQuiz();
      }
    },
    prevQuestion() {
      if (this.currentQuestionIndex > 0) {
        this.currentQuestionIndex--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    submitQuiz() {
      this.$refs.quizForm.submit();
    }
  }"
  class="min-h-screen bg-gray-50 flex flex-col font-sans"
>

  {{-- ── Custom Quiz Header ── --}}
  <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-3">
      <a href="{{ route('home') }}" class="flex items-center">
        <img src="/logo/logoblack.png" alt="Kingsley Khord" class="h-9 w-auto">
      </a>
    </div>

    <div class="flex items-center gap-4">
      <a href="/member/getstarted?step=2" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-xs font-bold transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Exit Quiz
      </a>
      @if(Auth::user()->passport)
        <img src="{{ Auth::user()->passport }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100">
      @else
        <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
          {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
      @endif
    </div>
  </header>

  {{-- ── Main Container ── --}}
  <div class="flex-grow flex flex-col items-center justify-center p-4 sm:p-8">
    <div class="max-w-3xl w-full flex flex-col gap-6">

      {{-- Progress Bar Card --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-3">
          <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Overall Progress</span>
            <div class="text-xl font-extrabold text-indigo-600 mt-0.5" x-text="progressPercent + '% Complete'">0% Complete</div>
          </div>
          <div class="text-right">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Questions</span>
            <div class="text-sm font-bold text-gray-700 mt-0.5" x-text="(currentQuestionIndex + 1) + ' / ' + questions.length">1 / 15</div>
          </div>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5">
          <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + progressPercent + '%'"></div>
        </div>
      </div>

      {{-- Question Card --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col gap-6 min-h-[480px] justify-between relative overflow-hidden">
        
        <div>
          {{-- Question Header Badge --}}
          <div class="flex items-center justify-between mb-6">
            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-extrabold rounded-full" x-text="'Question ' + (currentQuestionIndex + 1) + ' of ' + questions.length">
              Question 1 of 15
            </span>
            <span class="text-xs font-bold text-indigo-500" x-text="currentQuestion.category">
              Piano Fundamentals
            </span>
          </div>

          {{-- Question Text --}}
          <h2 class="text-2xl font-extrabold text-gray-900 leading-tight mb-2" x-text="currentQuestion.question">
            Can you play major scales?
          </h2>
          <p class="text-sm text-gray-400 mb-6" x-text="currentQuestion.type === 'multi' ? 'Select all options that apply.' : 'Select the option that best describes your current ability.'">
            Select the option that best describes your current ability.
          </p>

          {{-- Options Stack --}}
          <div class="grid grid-cols-1 gap-3">
            
            {{-- Single-Select Options --}}
            <template x-if="currentQuestion.type === 'single'">
              <template x-for="opt in currentQuestion.options" :key="opt.text">
                <button
                  @click="selectSingle(currentQuestion.id, opt.text)"
                  class="w-full text-left p-4 rounded-xl border-2 transition-all flex items-start gap-4 hover:border-indigo-300 hover:bg-indigo-50/10 group"
                  :class="isOptionSelected(currentQuestion.id, opt.text) ? 'border-indigo-600 bg-indigo-50/20' : 'border-gray-100 bg-white'"
                >
                  <div class="mt-0.5 w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                       :class="isOptionSelected(currentQuestion.id, opt.text) ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 bg-white'">
                    <div class="w-2 h-2 rounded-full bg-white" x-show="isOptionSelected(currentQuestion.id, opt.text)"></div>
                  </div>
                  <div class="flex-grow min-w-0">
                    <p class="font-bold text-sm text-gray-900" x-text="opt.text"></p>
                    <p class="text-xs text-gray-400 mt-1 leading-relaxed" x-text="opt.subtext"></p>
                  </div>
                </button>
              </template>
            </template>

            {{-- Multi-Select Options --}}
            <template x-if="currentQuestion.type === 'multi'">
              <template x-for="opt in currentQuestion.options" :key="opt.id || opt.text">
                <button
                  @click="toggleMultiSelect(currentQuestion.id, opt.id || opt.text)"
                  class="w-full text-left p-4 rounded-xl border-2 transition-all flex items-start gap-4 hover:border-indigo-300 hover:bg-indigo-50/10 group"
                  :class="isOptionSelected(currentQuestion.id, opt.id || opt.text, true) ? 'border-indigo-600 bg-indigo-50/20' : 'border-gray-100 bg-white'"
                >
                  <div class="mt-0.5 w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                       :class="isOptionSelected(currentQuestion.id, opt.id || opt.text, true) ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 bg-white'">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" x-show="isOptionSelected(currentQuestion.id, opt.id || opt.text, true)">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <div class="flex-grow min-w-0">
                    <p class="font-bold text-sm text-gray-900" x-text="opt.text"></p>
                    <p class="text-xs text-gray-400 mt-1 leading-relaxed" x-text="opt.subtext"></p>
                  </div>
                </button>
              </template>
            </template>

          </div>
        </div>

        {{-- Tip Box --}}
        <div class="mt-6 p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl flex gap-3 items-start" x-show="currentQuestion.tip">
          <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
          </div>
          <div>
            <p class="text-xs font-bold text-indigo-900">Pro Tip</p>
            <p class="text-xs text-indigo-700/80 mt-1 leading-relaxed" x-text="currentQuestion.tip"></p>
          </div>
        </div>

      </div>

      {{-- Navigation Buttons --}}
      <div class="flex items-center justify-between">
        <button
          @click="prevQuestion()"
          :disabled="currentQuestionIndex === 0"
          class="flex items-center gap-1.5 px-6 py-3 border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl text-xs font-bold transition disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
          Previous
        </button>

        <button
          @click="nextQuestion()"
          :disabled="!canGoNext()"
          class="flex items-center gap-1.5 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <span x-text="currentQuestionIndex === questions.length - 1 ? 'Submit Assessment' : 'Next Question'">Next Question</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" x-show="currentQuestionIndex < questions.length - 1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  {{-- Hidden form for Laravel submission --}}
  <form id="quiz-form" x-ref="quizForm" action="{{ route('member.quiz.submit') }}" method="POST" class="hidden">
    @csrf
    <template x-for="(val, qId) in answers">
      <div>
        <template x-if="Array.isArray(val)">
          <div>
            <template x-for="item in val">
              <input type="hidden" :name="'answers[' + qId + '][]'" :value="item">
            </template>
          </div>
        </template>
        <template x-if="!Array.isArray(val)">
          <input type="hidden" :name="'answers[' + qId + ']'" :value="val">
        </template>
      </div>
    </template>
  </form>

</div>
@endsection
