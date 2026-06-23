import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/css/style2.css',
                'resources/css/style_noti.css',
                'resources/css/style_profile.css',
                'resources/css/style_home.css',
                'resources/js/app.js',
                'resources/js/script.js',
            ],
            refresh: true,
        }),
    ],
});
