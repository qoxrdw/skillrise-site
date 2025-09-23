import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/notes-create.js',
                'resources/js/notes-edit.js',
                'resources/js/delete-confirm.js',
                'resources/js/add-question.js',
            ],
            refresh: true,
        }),
    ],
});
