@props(['member', 'isCEO' => false])

@php
    $type = $member['type'] ?? 'staff';
    $styles = match($type) {
        'director' => ['bg' => 'bg-indigo-600', 'border' => 'border-indigo-600', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50 border-indigo-200'],
        'manager' => ['bg' => 'bg-amber-500', 'border' => 'border-amber-500', 'text' => 'text-amber-500', 'light' => 'bg-amber-50 border-amber-200'],
        'staff' => ['bg' => 'bg-rose-500', 'border' => 'border-rose-500', 'text' => 'text-rose-500', 'light' => 'bg-rose-50 border-rose-200'],
        'department' => ['bg' => 'bg-emerald-500', 'border' => 'border-emerald-500', 'text' => 'text-emerald-500', 'light' => 'bg-emerald-50 border-emerald-200'],
        default => ['bg' => $isCEO ? 'bg-titan-red' : 'bg-titan-navy', 'border' => $isCEO ? 'border-titan-red' : 'border-titan-navy', 'text' => $isCEO ? 'text-titan-red' : 'text-titan-navy', 'light' => 'bg-gray-50 border-gray-200']
    };

    $roleColor = $isCEO ? 'bg-titan-red text-white' : $styles['bg'] . ' text-white';
    $isDepartment = $type === 'department';
@endphp

@if($isDepartment)
    <div class="flex flex-col items-center group relative z-10 w-full">
        <div class="{{ $styles['light'] }} border px-6 py-3 rounded-xl backdrop-blur-sm shadow-sm transition-all duration-500 group-hover:shadow-md group-hover:scale-105">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $styles['text'] }} opacity-50 mb-1 block text-center italic">{{ __('DEPARTMENT') }}</span>
            <h3 class="text-sm font-black {{ $styles['text'] }} uppercase tracking-tight text-center whitespace-nowrap">
                {{ $member['name'] }}
            </h3>
        </div>
    </div>
@else
    <div class="flex flex-col items-center group relative z-10 w-full cursor-pointer" 
         @click="selectedMember = {{ Js::from($member) }}">
        
        <div class="relative rounded-full overflow-hidden border-[4px] border-white shadow-xl transition-all duration-700 group-hover:scale-110 group-hover:shadow-lg {{ $isCEO ? 'w-32 h-32 mb-4 group-hover:shadow-titan-red/20' : 'w-24 h-24 mb-4 group-hover:shadow-current' }}">
            @if(isset($member['image']) && $member['image'])
                <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="object-cover object-top w-full h-full" />
            @else
                <div class="absolute inset-0 bg-gray-50 flex items-center justify-center text-gray-300">
                    <x-lucide-users class="{{ $isCEO ? 'w-12 h-12' : 'w-8 h-8' }}" />
                </div>
            @endif
            <div class="absolute inset-0 bg-current opacity-0 group-hover:opacity-10 transition-opacity duration-500 {{ $styles['text'] }}"></div>
        </div>
        
        <div class="flex flex-col items-center text-center px-4">
            <div class="{{ $roleColor }} px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest mb-2 shadow-lg scale-90 group-hover:scale-100 transition-transform duration-500 whitespace-nowrap">
                {{ Str::limit($member['role'] ?? '', 25) }}
            </div>
            <h3 class="text-xs md:text-sm font-black text-titan-navy uppercase tracking-tight leading-tight group-hover:{{ $styles['text'] }} transition-colors duration-500 max-w-[160px]">
                {{ $member['name'] }}
            </h3>
        </div>
    </div>
@endif
