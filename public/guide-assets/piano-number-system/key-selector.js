const demo = document.querySelector('[data-number-system]');
const picker = demo.querySelector('[data-key-picker]');
const key = picker.querySelector('select');
const panels = demo.querySelectorAll('[data-key-panel]');

function showKey() {
    panels.forEach(panel => {
        panel.hidden = panel.dataset.keyPanel !== key.value;
    });
}

key.addEventListener('change', showKey);
showKey();
picker.hidden = false;
