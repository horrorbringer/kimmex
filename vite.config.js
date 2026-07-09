import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-enhancements.js'],
                refresh: true,
            }),
            tailwindcss(),
            VitePWA({
                injectRegister: null,
                registerType: 'autoUpdate',
                includeAssets: ['favicon.ico', 'logo.png'],
                manifest: {
                    name: 'KIMMEX',
                    short_name: 'KIMMEX',
                    description: 'Kimmex is a leading construction and engineering company delivering high-quality building and management solutions.',
                    theme_color: '#0B2B5C',
                    background_color: '#FFFFFF',
                    display: 'standalone',
                    orientation: 'portrait-primary',
                    start_url: '/',
                    scope: '/',
                    categories: ['construction', 'engineering', 'business'],
                    icons: [
                        {
                            src: 'pwa-icons/icon-192.png',
                            sizes: '192x192',
                            type: 'image/png',
                        },
                        {
                            src: 'pwa-icons/icon-512.png',
                            sizes: '512x512',
                            type: 'image/png',
                        },
                        {
                            src: 'pwa-icons/icon-512.png',
                            sizes: '512x512',
                            type: 'image/png',
                            purpose: 'any maskable',
                        },
                    ],
                },
                workbox: {
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,jpeg,webp,woff2}'],
                    navigateFallback: '/',
                    runtimeCaching: [
                        {
                            urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                            handler: 'CacheFirst',
                            options: {
                                cacheName: 'google-fonts-cache',
                                expiration: {
                                    maxEntries: 20,
                                    maxAgeSeconds: 60 * 60 * 24 * 365,
                                },
                            },
                        },
                    ],
                },
            }),
        ],
        server: {
            host: env.VITE_DEV_SERVER_HOST || '127.0.0.1',
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
