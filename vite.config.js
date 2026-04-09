import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import process from 'node:process';

import { cloudflare } from "@cloudflare/vite-plugin";

const useCloudflare = process.env.USE_CLOUDFLARE === 'true';

export default defineConfig({
    build: {
        outDir: 'dist',
    },
    plugins: [laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
    }), ...(useCloudflare ? [cloudflare()] : [])],
});