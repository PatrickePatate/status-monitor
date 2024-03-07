import { defineConfig } from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/scss/page.scss', 'resources/js/page.js', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                ],
        }),
    ],
});
