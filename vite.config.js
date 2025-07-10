// vite.config.js

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'; // <--- Add this line

export default defineConfig({
    plugins: [
        laravel({
            // Update the input path to your new .scss file
            input: ['resources/css/app.scss', 'resources/sass/app.scss', 'resources/js/app.js'], 
            refresh: true,
        }),
    ],
    // Add the resolve object with an alias for bootstrap
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
});
