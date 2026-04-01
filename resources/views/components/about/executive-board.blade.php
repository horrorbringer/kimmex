@props(['executives'])

<div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-16">
        <span class="text-accent-orange font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">{{ __('THE LEADERSHIP') }}</span>
        <h2 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase tracking-tight">
            {{ __('Executive Board') }}
        </h2>
    </div>

    <!-- Tiered Grid -->
    <div class="space-y-12">
        <!-- CEO -->
        @if($ceo = $executives->where('type', 'executive')->firstWhere('name', 'TOUCH KIM'))
            <div class="flex justify-center">
                <div class="w-full max-w-sm">
                    <x-about.team-member-card :member="$ceo" :isCEO="true" />
                </div>
            </div>
        @endif

        <!-- DCEO & DGM -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($executives->where('type', '!=', 'ceo')->reject(fn($e) => $e['name'] === 'TOUCH KIM') as $exec)
                <x-about.team-member-card :member="$exec" />
            @endforeach
        </div>
    </div>
</div>
