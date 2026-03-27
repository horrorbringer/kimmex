<div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-1 mr-4 shadow-inner"
    style="height: 32px; align-self: center;">
    <a href="{{ route('lang.switch', 'en') }}"
        class="flex items-center justify-center px-4 rounded-md text-[10px] font-black uppercase tracking-tighter transition-all duration-300 no-underline"
        style="
            height: 24px; 
            min-width: 40px;
            {{ app()->getLocale() === 'en' ? 'background-color: #ff6b00; color: white; box-shadow: 0 4px 6px -1px rgba(255,107,0,0.3);' : 'color: #9ca3af;' }}
        ">
        EN
    </a>
    <a href="{{ route('lang.switch', 'km') }}"
        class="flex items-center justify-center px-4 rounded-md text-[10px] font-black uppercase tracking-tighter transition-all duration-300 no-underline"
        style="
            height: 24px; 
            min-width: 40px;
            {{ app()->getLocale() === 'km' ? 'background-color: #ff6b00; color: white; box-shadow: 0 4px 6px -1px rgba(255,107,0,0.3);' : 'color: #9ca3af;' }}
        ">
        KH
    </a>
</div>