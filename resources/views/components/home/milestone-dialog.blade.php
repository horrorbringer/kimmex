<div x-cloak x-show="detailOpen" @keydown.escape.window="closeDetail()" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="milestone-detail-{{ $index }}">
    <div x-show="detailOpen" x-transition.opacity @click="closeDetail()" class="absolute inset-0 bg-titan-navy/80 backdrop-blur-sm"></div>
    <div x-show="detailOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-5 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-5 scale-95" @click.stop class="relative z-10 max-h-[88vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
        <button x-ref="detailClose" type="button" @click="closeDetail()" class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-titan-navy/5 text-titan-navy transition-colors hover:bg-titan-red hover:text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-titan-red/30" aria-label="{{ __('Close') }}"><x-lucide-x class="h-5 w-5" /></button>
        <p class="pr-12 font-heading text-xl font-black text-titan-red">{{ $milestone['year'] }}</p>
        <h3 id="milestone-detail-{{ $index }}" class="mt-1 pr-10 font-heading text-md font-black tracking-tight text-titan-navy sm:text-xl {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">{{ $milestone['title'] }}</h3>
        <div class="prose prose-sm mt-6 max-w-none leading-relaxed text-titan-navy/70 [&_a]:font-semibold [&_a]:text-titan-red [&_img]:rounded-xl [&_img]:shadow-sm">{!! $detail !!}</div>
    </div>
</div>
