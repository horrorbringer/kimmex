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
                // Check for updates every 60 minutes
                setInterval(() => reg.update(), 60 * 60 * 1000);

                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'activated' && navigator.serviceWorker.controller) {
                                // New SW activated — reload to get fresh assets
                                window.location.reload();
                            }
                        });
                    }
                });
            })
            .catch(() => {
                // SW unavailable — site works without PWA
            });

        // Handle controller change (when skipWaiting activates new SW)
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            window.location.reload();
        });
    });
}
