import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend.css',
                'resources/css/admin.css',
                'resources/js/frontend/index.js',
                'resources/js/admin/index.js',
            ],
            refresh: true,
        }),
    ],
});
