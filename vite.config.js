import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/css/custom_login.css','resources/css/custom_body.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
