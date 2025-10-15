import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'; // <-- Добавлено

Alpine.plugin(collapse); // <-- Добавлено


window.Alpine = Alpine;

Alpine.start();

// Modal functions (CSP-safe)
window.openModal = function(name) {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
};

window.closeModal = function(name) {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));
};

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

    // Create track button handler
    const createTrackBtn = document.getElementById('create-track-btn');
    if (createTrackBtn) {
        createTrackBtn.addEventListener('click', () => {
            openModal('create-track-modal');
        });
    }

    // Cancel button inside create track modal
    const createTrackCancelBtn = document.getElementById('create-track-cancel');
    if (createTrackCancelBtn) {
        createTrackCancelBtn.addEventListener('click', () => {
            closeModal('create-track-modal');
        });
    }
});
