import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite';
import browserSync from 'vite-plugin-browser-sync';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true, // optional, but helpful in Docker/WSL
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/**',
                'routes/**',
                'resources/views/**',
            ],
        }),
        tailwindcss(),
        browserSync({
            proxy: 'http://localhost',
            files: [
                'app/**/*.php',
                'routes/**/*.php',
                'resources/views/**/*.blade.php',
            ],
            notify: false,
            open: false,
        }),
    ],
});
