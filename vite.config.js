import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import basicSsl from '@vitejs/plugin-basic-ssl';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/index.tsx'],
            refresh: true,
        }),
        react(),
        basicSsl(),
    ],
    define: {
        // "process.env.SHOPIFY_API_KEY": JSON.stringify(import.meta.env?.VITE_SHOPIFY_API_KEY),
    },
    server: {
        https: true,
    },
});
