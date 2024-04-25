import { defineConfig } from 'vite';
import vue from "@vitejs/plugin-vue2";
import laravel from 'laravel-vite-plugin';
import commonjs from 'vite-plugin-commonjs';

export default defineConfig({
    define:{
        'process.env': process.env
    },
    plugins: [
        commonjs({
            filter(id) {
                if (id.includes('node_modules/redux-storage/build-es')) {
                    return true;
                }
            },
        }),
        vue(),

        laravel({
            input: [
                'resources/js/app.js',
            ],
            refresh: true
        }),
    ],
});
