import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then((reg) => {
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                if (confirm('New content available. Reload to update?')) {
                                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            })
            .catch(() => {
                // SW unavailable — site works without PWA
            });
    });
}
