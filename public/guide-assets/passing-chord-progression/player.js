const progression = document.querySelector('[data-progression]');
const play = progression.querySelector('[data-play]');
const reset = progression.querySelector('[data-reset]');
const status = progression.querySelector('[data-audio-status]');
const cards = [...progression.querySelectorAll('[data-chord-card]')];
const playingClasses = progression.dataset.playingClasses.split(' ');
const selectors = cards.map(card => card.querySelector('select'));
const AudioContextClass = window.AudioContext || window.webkitAudioContext;
const durations = [0.85, 0.85, 1.5];
const buffers = new Map();
let context;
let playback;

function updateNotes() {
    cards.forEach((card, index) => {
        card.querySelector('[data-chord-notes]').textContent = selectors[index].selectedOptions[0].dataset.notes;
    });
}

function loadSample(note) {
    // The guide piano library samples nearby pitches for these chromatic notes.
    const sample = {51: 50, 56: 55, 58: 57, 61: 60}[note] ?? note;
    if (!buffers.has(sample)) {
        const url = new URL(`../chord-extensions/piano/${sample}.mp3`, import.meta.url);
        buffers.set(sample, fetch(url).then(response => {
            if (!response.ok) throw new Error('Piano sample unavailable');
            return response.arrayBuffer();
        }).then(data => context.decodeAudioData(data)).catch(error => {
            buffers.delete(sample);
            throw error;
        }));
    }
    return buffers.get(sample).then(buffer => ({buffer, rate: 2 ** ((note - sample) / 12)}));
}

function finish(message) {
    if (playback) {
        cancelAnimationFrame(playback.frame);
        playback.output.disconnect();
        playback.sources.forEach(source => source.stop());
        playback = undefined;
    }
    cards.forEach(card => card.classList.remove(...playingClasses));
    selectors.forEach(selector => { selector.disabled = false; });
    reset.disabled = false;
    play.disabled = false;
    play.textContent = 'Play Progression';
    status.textContent = message;
}

async function playProgression() {
    if (playback) {
        finish('Playback stopped.');
        return;
    }

    play.disabled = true;
    reset.disabled = true;
    selectors.forEach(selector => { selector.disabled = true; });
    status.textContent = 'Loading piano…';

    try {
        context ??= new AudioContextClass();
        await context.resume();
        const chords = await Promise.all(cards.map((card, index) => {
            const upper = selectors[index].selectedOptions[0].dataset.midi.split(',').map(Number);
            return Promise.all([Number(card.dataset.bass), ...upper].map(loadSample));
        }));

        const output = context.createGain();
        output.gain.value = 0.55;
        output.connect(context.destination);
        playback = {output, sources: []};
        let time = context.currentTime + 0.06;
        const startTimes = chords.map((chord, index) => {
            const start = time;
            time += durations[index];
            chord.forEach(({buffer, rate}) => {
                const source = context.createBufferSource();
                const envelope = context.createGain();
                source.buffer = buffer;
                source.playbackRate.value = rate;
                envelope.gain.setValueAtTime(0.2, start);
                envelope.gain.setValueAtTime(0.2, time - 0.12);
                envelope.gain.linearRampToValueAtTime(0, time);
                source.connect(envelope).connect(output);
                source.start(start);
                source.stop(time);
                playback.sources.push(source);
            });
            return start;
        });

        function showActiveChord() {
            if (context.currentTime >= time) {
                finish('Progression complete.');
                return;
            }
            cards.forEach((card, index) => {
                const active = context.currentTime >= startTimes[index] && context.currentTime < (startTimes[index + 1] ?? time);
                playingClasses.forEach(className => card.classList.toggle(className, active));
            });
            playback.frame = requestAnimationFrame(showActiveChord);
        }

        play.disabled = false;
        play.textContent = 'Stop';
        status.textContent = 'Playing progression…';
        showActiveChord();
    } catch {
        finish('Piano audio could not play. Please try again.');
    }
}

selectors.forEach(selector => selector.addEventListener('change', updateNotes));
reset.hidden = false;
reset.addEventListener('click', () => {
    selectors.forEach(selector => { selector.selectedIndex = 0; });
    updateNotes();
    status.textContent = 'Standard progression restored.';
});
if (AudioContextClass) {
    play.hidden = false;
    play.addEventListener('click', playProgression);
} else {
    status.textContent = 'Piano playback is unavailable in this browser.';
}
