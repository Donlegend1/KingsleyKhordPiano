@php
    $initialSequence = $initialSequence ?? [];
    $fieldName = $name ?? 'correct_option';
    $maxNotesValue = $maxNotes ?? null;
    $optionLabels = $options ?? [];
@endphp

<div x-data="{
        sequence: {{ json_encode(array_values($initialSequence)) }},
        maxNotes: {{ $maxNotesValue !== null ? (int) $maxNotesValue : 'null' }},
        labels: {{ json_encode(array_values($optionLabels)) }},
        get isFull() { return this.maxNotes !== null && this.sequence.length >= this.maxNotes; },
        add(i) { if (this.isFull) return; this.sequence.push(i); },
        removeLast() { this.sequence.pop(); },
        clear() { this.sequence = []; },
    }"
    class="space-y-3">

    <input type="hidden" name="{{ $fieldName }}" :value="sequence.join(',')">

    @if ($maxNotesValue !== null)
        <p class="text-[11px] text-gray-400">This lesson requires exactly {{ $maxNotesValue }} chords.</p>
    @endif

    <div class="flex flex-wrap items-center gap-1.5 min-h-[30px] px-3 py-2 rounded-xl bg-gray-50 border border-gray-200">
        <template x-if="sequence.length === 0">
            <span class="text-[12px] text-gray-400">Click chords below to build the progression, in order...</span>
        </template>
        <template x-for="(opt, idx) in sequence" :key="idx">
            <span class="px-2 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                <span x-text="idx + 1"></span>. <span x-text="labels[opt]"></span>
            </span>
        </template>
        <span x-show="maxNotes !== null" class="ml-auto text-[11px] font-semibold text-gray-400 flex-shrink-0">
            <span x-text="sequence.length"></span>/<span x-text="maxNotes"></span>
        </span>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" @click="removeLast()"
            class="text-[11px] font-semibold text-gray-500 hover:text-gray-800 px-3 py-1.5 rounded-full border border-gray-200 transition-colors">
            Remove last
        </button>
        <button type="button" @click="clear()"
            class="text-[11px] font-semibold text-gray-500 hover:text-red-600 px-3 py-1.5 rounded-full border border-gray-200 transition-colors">
            Clear
        </button>
    </div>

    <div class="flex flex-wrap gap-2" :class="isFull ? 'opacity-40 pointer-events-none' : ''">
        @foreach ($optionLabels as $index => $label)
            <button type="button" @click="add({{ $index }})" :disabled="isFull"
                class="px-3 py-1.5 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-[12px] font-semibold text-gray-700 transition-colors disabled:cursor-not-allowed">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
