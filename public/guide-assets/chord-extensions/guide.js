function bassShapes({ root, third, seventh }) {
    return {
        root: [root],
        fifth: [root - 12, root - 5],
        octave: [root - 12, root],
        octaveFifth: [root - 12, root - 5, root],
        tenth: [root - 12, root - 5, root + third],
        seventh: [root, root + seventh],
        triad: [root, root + third, root + 7],
        seventhShell: [root, root + third, root + seventh],
    };
}

const chords = {
    seventh: {
        name: "Cmaj7",
        shapes: [{
            name: "E minor",
            notes: [64, 67, 71],
            explanation: "C under E minor gives you C–E–G–B. B is the major 7th. Try an inversion to change the top note.",
        }],
    },
    ninth: {
        name: "Cmaj9",
        shapes: [
            {
                name: "E minor 7",
                notes: [64, 67, 71, 74],
                explanation: "Keep C in the bass and use E minor 7 in your right hand. The D adds the 9th.",
            },
            {
                name: "G major",
                notes: [67, 71, 74],
                explanation: "G major gives you G–B–D in the right hand. Include E in the left hand for the major 3rd.",
            },
        ],
    },
    eleventh: {
        name: "Cmaj9♯11",
        shapes: [
            {
                name: "G major 7",
                notes: [55, 59, 62, 66],
                explanation: "Play G major 7 over a C bass shape that includes E. F♯ is the raised 11th.",
            },
            {
                name: "B minor",
                notes: [59, 62, 66],
                explanation: "B minor gives you B–D–F♯: the major 7th, 9th, and raised 11th. Play it over a C bass shape that includes E.",
            },
        ],
    },
    thirteenth: {
        name: "Cmaj13♯11",
        shapes: [
            {
                name: "B minor 7",
                notes: [59, 62, 66, 69],
                explanation: "Play B minor 7 over a C bass shape that includes E. A is the 13th, and F♯ keeps the raised 11th.",
            },
            {
                name: "D major",
                notes: [62, 66, 69],
                explanation: "D major supplies the 9th, raised 11th, and 13th. In the left hand, C–E–B supplies the root, major 3rd, and major 7th.",
            },
        ],
    },
};

const shape = (name, notes, explanation) => ({ name, notes, explanation });

const qualities = {
    major: { root: 48, third: 4, seventh: 11, chords },
    minor: {
        root: 50, third: 3, seventh: 10,
        chords: {
            seventh: { name: "Dm7", shapes: [
                shape("F major", [53, 57, 60], "F major over D gives D–F–A–C. F is the minor 3rd and C is the flat 7th."),
            ] },
            ninth: { name: "Dm9", shapes: [
                shape("F major 7", [53, 57, 60, 64], "F major 7 over D adds E, the 9th."),
                shape("A minor", [57, 60, 64], "A minor supplies A–C–E. Add F in the left hand for the minor 3rd."),
            ] },
            eleventh: { name: "Dm11", shapes: [
                shape("F sus2", [53, 55, 60], "F sus2 over D gives F–G–C. G is the natural 11th."),
                shape("C major", [60, 64, 67], "C major supplies C–E–G. Add F in the left hand for the minor 3rd."),
                shape("A minor 7", [57, 60, 64, 67], "A minor 7 supplies A–C–E–G. Add F in the left hand for the minor 3rd."),
            ] },
            thirteenth: { name: "Dm13", shapes: [
                shape("C major 7", [60, 64, 67, 71], "C major 7 over D gives C–E–G–B. B is the 13th. Add F in the left hand to establish the minor quality."),
                shape("E minor", [52, 55, 59], "E minor supplies E–G–B. Use D–F–C in the left hand to include the minor 3rd and flat 7th."),
            ] },
        },
    },
    dominant: {
        root: 55, third: 4, seventh: 10,
        chords: {
            seventh: { name: "G7", shapes: [
                shape("B diminished", [59, 62, 65], "B diminished over G gives G–B–D–F. B is the major 3rd and F is the flat 7th."),
            ] },
            ninth: { name: "G9", shapes: [
                shape("B half-diminished 7", [59, 62, 65, 69], "B–D–F–A over G adds A, the 9th."),
                shape("D minor", [62, 65, 69], "D minor supplies D–F–A. Add B in the left hand for the major 3rd."),
            ] },
            eleventh: { name: "G11", shapes: [
                shape("F major", [53, 57, 60], "F major supplies F–A–C over G. Leaving B out lets C, the natural 11th, take the place of the 3rd."),
                shape("D minor 7", [62, 65, 69, 72], "D minor 7 supplies D–F–A–C over G. Try it without B to hear the suspended sound."),
            ] },
            thirteenth: { name: "G13", shapes: [
                shape("F major 7", [53, 57, 60, 64], "F major 7 supplies F–A–C–E over G. E is the 13th and C is the natural 11th."),
                shape("A minor", [57, 60, 64], "A minor supplies A–C–E. Add F in the left hand for the flat 7th."),
            ] },
        },
    },
};

const noteNames = ["C", "C♯", "D", "E♭", "E", "F", "F♯", "G", "A♭", "A", "B♭", "B"];
const describeNotes = (notes) => notes
    .map((midi) => noteNames[midi % 12] + (Math.floor(midi / 12) - 1))
    .join(" · ");

function rightHandVoicing(notes, bassNotes, inversion) {
    const floor = Math.max(48, Math.max(...bassNotes) + 1);
    const inverted = [...notes.slice(inversion), ...notes.slice(0, inversion).map((note) => note + 12)];
    const octaveShift = Math.ceil((floor - inverted[0]) / 12) * 12;
    return inverted.map((note) => note + octaveShift);
}

for (const lab of document.querySelectorAll("[data-voicing-lab]")) {
    const options = lab.querySelector("[data-chord-options]");
    const qualityPicker = lab.querySelector("[data-chord-quality]");
    const eleven = options.querySelector('[data-voicing="eleventh"]');
    const seventhLabel = (setup) => setup.seventh === 11 ? "major seventh" : "flat seventh";
    const bass = lab.querySelector("[data-bass]");
    const shapeOptions = lab.querySelector("[data-shape-options]");
    const inversion = lab.querySelector("[data-inversion]");
    const playButton = lab.querySelector("[data-play-chord]");
    const playLabel = playButton.querySelector("[data-play-label]");
    const keys = [...lab.querySelectorAll("[data-midi]")];
    const chordName = lab.querySelector("[data-chord-name]");
    const leftNotes = lab.querySelector("[data-left-notes]");
    const rightNotes = lab.querySelector("[data-right-notes]");
    const rightShape = lab.querySelector("[data-right-shape]");
    const voicingNote = lab.querySelector("[data-voicing-note]");
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;

    let selected = "seventh";
    const quality = () => qualities[qualityPicker.value];
    const activeChord = () => quality().chords[selected];
    let audioContext;
    let playedNotes = [];
    const pianoBuffers = new Map();
    const soundingNotes = new Set();

    function loadPianoNote(note) {
        if (!pianoBuffers.has(note)) {
            const sample = new URL(`./piano/${note}.mp3`, import.meta.url);
            const buffer = fetch(sample).then(async (response) => {
                if (!response.ok) throw new Error("Piano sample unavailable");
                return audioContext.decodeAudioData(await response.arrayBuffer());
            }).catch((error) => {
                pianoBuffers.delete(note);
                throw error;
            });
            pianoBuffers.set(note, buffer);
        }
        return pianoBuffers.get(note);
    }

    function updateKeyboard() {
        const chord = activeChord();
        const shape = chord.shapes[Number(shapeOptions.value)];
        const position = Number(inversion.value);
        const left = bassShapes(quality())[bass.value];
        const right = rightHandVoicing(shape.notes, left, position);
        playedNotes = [...left, ...right];

        const pitchClasses = playedNotes.map((note) => note % 12);
        const { root, third, seventh } = quality();
        const hasThird = pitchClasses.includes((root + third) % 12);
        const hasSeventh = pitchClasses.includes((root + seventh) % 12);
        const isMajor = qualityPicker.value === "major";
        const name = isMajor && !hasSeventh ? "C6/9♯11" : chord.name;
        const omissions = [];
        if (!hasThird) omissions.push("3rd");
        if (!hasSeventh && !isMajor) omissions.push("7th");
        chordName.textContent = omissions.length ? name + " (no " + omissions.join(" or ") + ")" : name;
        leftNotes.textContent = describeNotes(left);
        rightNotes.textContent = describeNotes(right);
        rightShape.textContent = shape.name;

        const missing = [];
        if (!hasThird) missing.push(noteNames[(root + third) % 12] + " (the " + (third === 3 ? "minor" : "major") + " 3rd)");
        if (!hasSeventh) missing.push(noteNames[(root + seventh) % 12] + " (the " + (seventh === 11 ? "major" : "flat") + " 7th)");
        voicingNote.textContent = missing.length
            ? shape.explanation + " This bass shape leaves out " + missing.join(" and ") + "."
            : shape.explanation;

        for (const key of keys) {
            const pitch = Number(key.dataset.midi);
            if (left.includes(pitch)) key.dataset.hand = "left";
            else if (right.includes(pitch)) key.dataset.hand = "right";
            else delete key.dataset.hand;
        }
    }

    function selectShape() {
        const shape = activeChord().shapes[Number(shapeOptions.value)];
        inversion.value = "0";
        const thirdInversion = inversion.options[3];
        thirdInversion.hidden = thirdInversion.disabled = shape.notes.length < 4;
        updateKeyboard();
    }

    function loadShapes() {
        const shapes = activeChord().shapes;
        shapeOptions.replaceChildren(...shapes.map((shape, index) => new Option(shape.name, index)));
        shapeOptions.hidden = shapes.length < 2;
        rightShape.hidden = shapes.length > 1;
        selectShape();
    }

    options.addEventListener("click", (event) => {
        const button = event.target.closest("[data-voicing]");
        if (!button || button.dataset.voicing === selected) return;

        selected = button.dataset.voicing;
        for (const option of options.querySelectorAll("[data-voicing]")) {
            option.setAttribute("aria-pressed", String(option === button));
        }

        loadShapes();
    });

    qualityPicker.addEventListener("change", () => {
        const setup = quality();
        const seventh = seventhLabel(setup);
        bass.options.namedItem("guide-bass-seventh").textContent = "Root + " + seventh;
        bass.options.namedItem("guide-bass-shell").textContent = "Root + third + " + seventh;
        bass.options.namedItem("guide-bass-triad").textContent = setup.third === 3 ? "Minor triad" : "Major triad";
        eleven.textContent = qualityPicker.value === "major" ? "♯11th" : "11th";
        loadShapes();
    });

    shapeOptions.addEventListener("change", selectShape);
    bass.addEventListener("change", updateKeyboard);
    inversion.addEventListener("change", updateKeyboard);

    playButton.addEventListener("click", async () => {
        playButton.disabled = true;
        playLabel.textContent = "Loading piano…";
        const notes = [...playedNotes];

        try {
            audioContext ??= new AudioContextClass();
            await audioContext.resume();
            const buffers = await Promise.all(notes.map(loadPianoNote));
            const start = audioContext.currentTime + 0.025;
            for (const note of soundingNotes) note.stop(start);

            const volume = audioContext.createGain();
            volume.gain.value = 0.85 / Math.sqrt(notes.length);
            volume.connect(audioContext.destination);
            let remaining = buffers.length;

            for (const buffer of buffers) {
                const note = audioContext.createBufferSource();
                note.buffer = buffer;
                note.connect(volume);
                soundingNotes.add(note);
                note.onended = () => {
                    soundingNotes.delete(note);
                    note.disconnect();
                    if (--remaining === 0) volume.disconnect();
                };
                note.start(start);
            }
            playLabel.textContent = "Hear chord";
        } catch {
            playLabel.textContent = "Retry piano audio";
        } finally {
            playButton.disabled = false;
        }
    });

    bass.value = "root";
    qualityPicker.value = "major";
    loadShapes();
    qualityPicker.closest("[data-quality-field]").hidden = false;
    options.hidden = false;
    bass.hidden = false;
    inversion.hidden = false;
    playButton.hidden = !AudioContextClass;
}
