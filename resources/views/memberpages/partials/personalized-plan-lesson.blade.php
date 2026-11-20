@php
  $lessonName = ($lesson['name'] ?? null) ?: 'Untitled lesson';
@endphp

<div class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-lg border px-3 py-3 transition-colors sm:flex-nowrap sm:px-4"
     :class="isCompleted({{ \Illuminate\Support\Js::from($lessonKey) }}) ? 'border-green-300 bg-green-50' : 'border-transparent bg-gray-50'">
  <div class="w-full min-w-0 flex-1 basis-full sm:basis-auto">
    <p class="break-words text-sm font-medium text-gray-800"
       :class="{ 'line-through text-gray-500': isCompleted({{ \Illuminate\Support\Js::from($lessonKey) }}) }">{{ $lessonName }}</p>
    @if(!empty($lessonContext) || !empty($lesson['duration']))
      <p class="mt-0.5 text-xs text-gray-600">
        {{ $lessonContext }}
        @if(!empty($lessonContext) && !empty($lesson['duration'])) &middot; @endif
        @if(!empty($lesson['duration'])) {{ $lesson['duration'] }} min @endif
      </p>
    @endif
  </div>
  @if(!empty($lesson['url']))
    <a href="{{ $lesson['url'] }}" target="_blank" rel="noopener noreferrer"
       aria-label="Open lesson: {{ $lessonName }}"
       class="whitespace-nowrap text-xs font-semibold text-[#1447A6] hover:underline">
      Open Lesson
    </a>
  @endif
  <a href="{{ $progressForumUrl }}" x-show="isCompleted({{ \Illuminate\Support\Js::from($lessonKey) }})" x-cloak
     class="whitespace-nowrap text-xs font-semibold text-[#1447A6] hover:underline">Share progress</a>
  <label class="inline-flex items-center gap-2 whitespace-nowrap text-xs font-medium text-gray-700">
    <input type="checkbox"
           class="h-5 w-5 shrink-0 rounded border-2 border-gray-400 bg-white text-green-600 focus:ring-green-500 disabled:opacity-60"
           aria-label="Mark complete: {{ $lessonName }}"
           :checked="isCompleted({{ \Illuminate\Support\Js::from($lessonKey) }})"
           :disabled="saving"
           @change="toggleLesson({{ \Illuminate\Support\Js::from($lessonKey) }}, $event.target, {{ \Illuminate\Support\Js::from($lessonName) }})">
    Mark complete
  </label>
</div>
