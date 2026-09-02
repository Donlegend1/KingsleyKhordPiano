@php
    $initialSequence = $initialSequence ?? [];
    $fieldName = $name ?? 'correct_option';
    $maxNotesValue = $maxNotes ?? null;
    $allowBlackKeys = $allowBlackKeys ?? false;
    $inertTitle = "Sharps/flats aren't used in this lesson";
    $whiteLabels = ['Doh', 'Re', 'Mi', 'Fa', 'Sol', 'La', 'Ti', 'Doh'];
    $blackKeys = [
        ['label' => 'Di', 'after' => 0, 'index' => 8],
        ['label' => 'Ri', 'after' => 1, 'index' => 9],
        ['label' => 'Fi', 'after' => 3, 'index' => 10],
        ['label' => 'Si', 'after' => 4, 'index' => 11],
        ['label' => 'Toh', 'after' => 5, 'index' => 12],
    ];
@endphp

<div x-data="{
        sequence: {{ json_encode(array_values($initialSequence)) }},
        maxNotes: {{ $maxNotesValue !== null ? (int) $maxNotesValue : 'null' }},
        labels: ['Doh', 'Re', 'Mi', 'Fa', 'Sol', 'La', 'Ti', 'Doh', 'Di', 'Ri', 'Fi', 'Si', 'Toh'],
        get isFull() { return this.maxNotes !== null && this.sequence.length >= this.maxNotes; },
        add(i) { if (this.isFull) return; this.sequence.push(i); },
        removeLast() { this.sequence.pop(); },
        clear() { this.sequence = []; },
    }"
    class="space-y-3">

    <input type="hidden" name="{{ $fieldName }}" :value="sequence.join(',')">

    @if ($maxNotesValue !== null)
        <p class="text-[11px] text-gray-400">This lesson requires exactly {{ $maxNotesValue }} notes.</p>
    @endif

    <div class="flex flex-wrap items-center gap-1.5 min-h-[30px] px-3 py-2 rounded-xl bg-gray-50 border border-gray-200">
        <template x-if="sequence.length === 0">
            <span class="text-[12px] text-gray-400">Click notes below to build the melody, in order...</span>
        </template>
        <template x-for="(note, idx) in sequence" :key="idx">
            <span class="px-2 py-1 rounded-full bg-gray-900 text-white text-[11px] font-semibold whitespace-nowrap">
                <span x-text="idx + 1"></span>. <span x-text="labels[note]"></span>
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

    <div class="relative flex w-full max-w-md h-24 select-none" :class="isFull ? 'opacity-40 pointer-events-none' : ''">
        @foreach ($whiteLabels as $i => $label)
            <button type="button" @click="add({{ $i }})" :disabled="isFull"
                class="relative flex-1 flex items-end justify-center pb-2 border border-gray-200 first:rounded-l-lg last:rounded-r-lg border-l-0 first:border-l bg-white hover:bg-gray-50 transition-colors disabled:cursor-not-allowed">
                <span class="text-[10px] font-semibold text-gray-400">{{ $label }}</span>
            </button>
        @endforeach
        @foreach ($blackKeys as $key)
            @if ($allowBlackKeys)
                <button type="button" @click="add({{ $key['index'] }})" :disabled="isFull"
                    class="absolute top-0 h-[62%] w-[9%] flex items-end justify-center pb-1.5 rounded-b-md shadow-md z-10 bg-gray-900 hover:bg-gray-700 transition-colors disabled:cursor-not-allowed"
                    style="left: {{ (($key['after'] + 1) / count($whiteLabels)) * 100 }}%; transform: translateX(-50%);">
                    <span class="text-[9px] font-semibold text-gray-300">{{ $key['label'] }}</span>
                </button>
            @else
                <button type="button" title="{{ $inertTitle }}"
                    class="absolute top-0 h-[62%] w-[9%] flex items-end justify-center pb-1.5 rounded-b-md shadow-md z-10 bg-gray-900"
                    style="left: {{ (($key['after'] + 1) / count($whiteLabels)) * 100 }}%; transform: translateX(-50%);">
                    <span class="text-[9px] font-semibold text-gray-300">{{ $key['label'] }}</span>
                </button>
            @endif
        @endforeach
    </div>
</div>
