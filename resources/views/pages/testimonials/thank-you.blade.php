<x-layouts.app title="Thank You" description="Your testimonial has been submitted.">
    <div class="min-h-[60vh] flex items-center justify-center bg-white px-6 py-20">
        <div class="text-center max-w-md">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-6">
                <x-lucide-check-circle class="w-7 h-7 text-green-500" />
            </div>
            <h1 class="text-2xl font-black text-titan-navy uppercase tracking-tight mb-3">Thank You!</h1>
            <p class="text-sm text-titan-navy/50 leading-relaxed mb-6">
                Your testimonial for the
                <span class="font-semibold text-titan-navy/70">{{ $project->getTranslation('title', 'en') }}</span>
                project has been submitted successfully.
            </p>
            <p class="text-xs text-titan-navy/30 mb-8">Our team will review your feedback and may feature it on our website.</p>
            <a href="/" class="inline-flex items-center gap-2 h-10 px-6 rounded bg-titan-navy text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-titan-red transition-colors">
                <x-lucide-arrow-left class="w-3.5 h-3.5" />Return Home
            </a>
        </div>
    </div>
</x-layouts.app>
