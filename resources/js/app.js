import './bootstrap';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

// Register Alpine plugins via Livewire's Alpine hook.
// Livewire 3+ bundles Alpine — do NOT import/start it manually.
document.addEventListener('livewire:init', () => {
    window.Alpine.plugin(intersect);
    window.Alpine.plugin(collapse);
});

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
