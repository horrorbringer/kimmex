import './bootstrap';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

const setPageLoading = (isLoading) => {
    document.documentElement.dataset.pageLoading = isLoading ? 'true' : 'false';
};

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');

    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const href = link.getAttribute('href');
    const destination = new URL(link.href, window.location.href);

    if (!href || href.startsWith('#') || link.target || link.hasAttribute('download') || destination.origin !== window.location.origin) {
        return;
    }

    setPageLoading(true);
}, { capture: true });

window.addEventListener('pageshow', () => setPageLoading(false));

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
