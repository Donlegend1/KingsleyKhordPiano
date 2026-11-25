const lesson = document.querySelector("[data-accompaniment-lesson]");
const panels = [...lesson.querySelectorAll("[data-performance-panel]")];
const players = panels.map((panel) => panel.querySelector("audio"));
let notes = {};

function formatTime(seconds) {
    const time = Math.floor(seconds);
    return Math.floor(time / 60) + ":" + String(time % 60).padStart(2, "0");
}

fetch(lesson.dataset.noteSource)
    .then((response) => response.ok ? response.json() : {})
    .then((data) => { notes = data; })
    .catch(() => {});

for (const panel of panels) {
    const audio = panel.querySelector("audio");
    const recording = panel.dataset.performancePanel;
    const keys = [...panel.querySelectorAll("[data-performance-note]")];
    const controls = panel.querySelector("[data-recording-controls]");
    const button = controls.querySelector("[data-recording-toggle]");
    const icon = controls.querySelector("[data-recording-icon]");
    const label = controls.querySelector("[data-recording-label]");
    const seek = controls.querySelector("[data-recording-seek]");
    const elapsed = controls.querySelector("[data-recording-elapsed]");
    const duration = controls.querySelector("[data-recording-duration]");
    const status = controls.querySelector("[data-recording-status]");
    let frame;

    function updateProgress() {
        if (!Number.isFinite(audio.duration) || audio.duration <= 0) return;
        seek.disabled = false;
        seek.max = audio.duration;
        seek.value = audio.currentTime;
        seek.style.setProperty("--played", `${audio.currentTime / audio.duration * 100}%`);
        elapsed.textContent = formatTime(audio.currentTime);
        duration.textContent = formatTime(audio.duration);
    }

    function updateButton() {
        const playing = !audio.paused && !audio.ended;
        icon.textContent = playing ? "Ⅱ" : "▶";
        label.textContent = playing ? "Pause recording" : "Play recording";
    }

    button.addEventListener("click", async () => {
        if (!audio.paused) {
            audio.pause();
            return;
        }

        status.hidden = true;
        button.disabled = true;
        icon.textContent = "…";
        label.textContent = "Loading recording…";
        try {
            if (audio.error) audio.load();
            await audio.play();
        } catch {
            status.textContent = "The recording couldn’t play. Try again.";
            status.hidden = false;
        } finally {
            button.disabled = false;
            updateButton();
        }
    });

    seek.addEventListener("input", () => {
        audio.currentTime = Number(seek.value);
        updateProgress();
        highlight();
    });
    audio.addEventListener("loadedmetadata", updateProgress);
    audio.addEventListener("durationchange", updateProgress);
    audio.addEventListener("timeupdate", updateProgress);
    audio.addEventListener("playing", updateButton);

    function highlight() {
        const time = audio.currentTime * 1000;
        const activeNotes = new Set(
            (notes[recording] || []).filter(([, start, end]) => start <= time && end > time).map(([pitch]) => pitch)
        );
        for (const key of keys) {
            if (activeNotes.has(Number(key.dataset.performanceNote))) key.dataset.hand = "right";
            else delete key.dataset.hand;
        }
    }

    audio.addEventListener("play", () => {
        for (const player of players) {
            if (player !== audio && !player.paused) player.pause();
        }
        cancelAnimationFrame(frame);
        const update = () => {
            highlight();
            if (!audio.paused && !audio.ended) frame = requestAnimationFrame(update);
        };
        update();
    });
    audio.addEventListener("seeked", highlight);
    audio.addEventListener("pause", () => {
        cancelAnimationFrame(frame);
        updateButton();
    });
    audio.addEventListener("ended", () => {
        cancelAnimationFrame(frame);
        for (const key of keys) delete key.dataset.hand;
        updateButton();
    });

    audio.classList.remove("block");
    audio.hidden = true;
    controls.hidden = false;
}
