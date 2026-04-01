@props(['departments'])

<div class="max-w-[1700px] mx-auto px-6">
    <!-- Department Tabs -->
    <div class="flex flex-wrap justify-center gap-2 mb-16 pb-4 overflow-x-auto scrollbar-hide">
        @foreach($departments as $dept)
            <button 
                @click="activeDept = '{{ $dept['slug'] }}'"
                :class="activeDept === '{{ $dept['slug'] }}' ? 'bg-titan-navy text-white shadow-xl scale-105' : 'bg-gray-100 text-titan-navy hover:bg-gray-200'"
                class="px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all duration-300 border border-transparent"
            >
                {{ $dept['name'] }}
            </button>
        @endforeach
    </div>

    <!-- Active Department View -->
    @foreach($departments as $dept)
        <template x-if="activeDept === '{{ $dept['slug'] }}'">
            <div 
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-y-16 gap-x-8"
            >
                @foreach($dept['staff'] as $member)
                    <x-about.team-member-card :member="$member" />
                @endforeach
            </div>
        </template>
    @endforeach

    @if($departments->isEmpty())
        <div class="text-center py-20 text-titan-navy/30 italic">
            {{ __('No departmental staff configured yet.') }}
        </div>
    @endif
</div>
