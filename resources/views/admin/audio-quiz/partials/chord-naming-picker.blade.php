@php
    $qualityOptions = $options['quality'];
    $degreeOptions = $options['degree'];
    $accidentalIndices = $options['accidentalIndices'];
    $excludeOptions = $options['exclude'];

    $initialQuality = $initialQuality ?? null;
    $initialDegrees = $initialDegrees ?? [];
    $initialAccidentals = $initialAccidentals ?? [];
    $initialExclude = $initialExclude ?? [];
@endphp

<div x-data="{
        quality: {{ $initialQuality !== null ? (int) $initialQuality : 'null' }},
        degrees: {{ json_encode(array_values($initialDegrees)) }},
        accidentals: {{ json_encode((object) $initialAccidentals) }},
        exclude: {{ json_encode(array_values($initialExclude)) }},
        toggleDegree(i) {
            if (this.degrees.includes(i)) {
                this.degrees = this.degrees.filter(d => d !== i);
                delete this.accidentals[i];
            } else {
                this.degrees.push(i);
            }
        },
        setAccidental(i, sym) {
            this.accidentals[i] = this.accidentals[i] === sym ? undefined : sym;
            if (!this.accidentals[i]) delete this.accidentals[i];
        },
        selectExclude(i) {
            this.exclude = [i];
        },
        get degreesEncoded() { return this.degrees.map(i => '' + i + (this.accidentals[i] || '')).join(','); },
        get excludeEncoded() { return this.exclude.join(','); },
    }"
    class="space-y-5">

    <input type="hidden" name="quality" :value="quality">
    <input type="hidden" name="degrees_encoded" :value="degreesEncoded">
    <input type="hidden" name="exclude_encoded" :value="excludeEncoded">

    <div>
        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-2">Chord Quality (pick one)</p>
        <div class="flex flex-wrap gap-2">
            @foreach ($qualityOptions as $index => $label)
                <button type="button" @click="quality = {{ $index }}"
                    class="px-3 py-1.5 rounded-full border text-[12px] font-semibold transition-colors"
                    :class="quality === {{ $index }} ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 text-gray-700 hover:border-gray-300'">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div>
        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-2">Chord Degree (select all that apply)</p>
        <div class="flex flex-wrap gap-2">
            @foreach ($degreeOptions as $index => $label)
                <div class="inline-flex items-stretch rounded-full border overflow-hidden transition-colors"
                    :class="degrees.includes({{ $index }}) ? 'border-gray-900' : 'border-gray-200'">
                    <button type="button" @click="toggleDegree({{ $index }})"
                        class="flex items-center px-3 py-1.5 text-[12px] font-semibold transition-colors"
                        :class="degrees.includes({{ $index }}) ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'">
                        {{ $label }}
                    </button>
                    @if (in_array($index, $accidentalIndices))
                        <div x-show="degrees.includes({{ $index }})" x-cloak
                            class="flex items-stretch border-l"
                            :class="degrees.includes({{ $index }}) ? 'border-gray-900' : 'border-gray-200'">
                            <button type="button" @click="setAccidental({{ $index }}, '#')"
                                class="w-6 flex items-center justify-center text-[11px] font-bold border-r border-gray-200 transition-colors"
                                :class="accidentals[{{ $index }}] === '#' ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-50'">
                                #
                            </button>
                            <button type="button" @click="setAccidental({{ $index }}, 'b')"
                                class="w-6 flex items-center justify-center text-[11px] font-bold transition-colors"
                                :class="accidentals[{{ $index }}] === 'b' ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-50'">
                                b
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase mb-2">Exclude (pick one)</p>
        <div class="flex flex-wrap gap-2">
            @foreach ($excludeOptions as $index => $label)
                <button type="button" @click="selectExclude({{ $index }})"
                    class="px-3 py-1.5 rounded-full border text-[12px] font-semibold transition-colors"
                    :class="exclude.includes({{ $index }}) ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 text-gray-700 hover:border-gray-300'">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</div>
