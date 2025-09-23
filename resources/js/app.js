import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Rename track toggle (CSP-safe)
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('rename-toggle');
    const form = document.getElementById('rename-form');
    const cancelButton = document.getElementById('rename-cancel');

    if (toggleButton && form && cancelButton) {
        toggleButton.addEventListener('click', () => {
            form.classList.remove('hidden');
            toggleButton.classList.add('hidden');
            cancelButton.classList.remove('hidden');
            const input = form.querySelector('input[name="name"]');
            if (input) {
                input.focus();
                input.select();
            }
        });

        cancelButton.addEventListener('click', () => {
            form.classList.add('hidden');
            cancelButton.classList.add('hidden');
            toggleButton.classList.remove('hidden');
        });
    }
});