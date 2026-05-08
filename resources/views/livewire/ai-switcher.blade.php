<div class="flex items-center ml-4" style="margin-left: 1rem;">
    <div style="display: flex; align-items: center; gap: 6px; padding: 6px; background-color: rgba(243, 244, 246, 0.8); backdrop-filter: blur(12px); border-radius: 12px; border: 1px solid rgba(229, 231, 235, 0.6); box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.3s ease;">
        
        <!-- Glowing Provider Toggle -->
        <div style="display: flex; align-items: center; background-color: white; border-radius: 8px; padding: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #f3f4f6;">
            <!-- Gemini Button -->
            <button wire:click="switchProvider('gemini')" 
                title="Switch to Gemini"
                style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; transition: all 0.3s; cursor: pointer; border: none; {{ $provider === 'gemini' ? 'background: linear-gradient(to top right, #6366f1, #a855f7, #ec4899); box-shadow: 0 2px 4px rgba(0,0,0,0.1);' : 'background: transparent;' }}">
                <x-heroicon-m-sparkles style="width: 16px; height: 16px; {{ $provider === 'gemini' ? 'color: white; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;' : 'color: #9ca3af;' }}" />
            </button>
            
            <!-- Ollama Button -->
            <button wire:click="switchProvider('ollama')" 
                title="Switch to Ollama"
                style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; transition: all 0.3s; cursor: pointer; border: none; {{ $provider === 'ollama' ? 'background: linear-gradient(to top right, #f97316, #f59e0b); box-shadow: 0 2px 4px rgba(0,0,0,0.1);' : 'background: transparent;' }}">
                <x-heroicon-m-cpu-chip style="width: 16px; height: 16px; {{ $provider === 'ollama' ? 'color: white;' : 'color: #9ca3af;' }}" />
            </button>
        </div>

        <!-- Model Selector -->
        <div style="position: relative; display: flex; align-items: center; background-color: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #f3f4f6;">
            <select wire:model.live="model"
                style="appearance: none; -webkit-appearance: none; -moz-appearance: none; width: 100%; padding: 6px 32px 6px 12px; font-size: 11px; font-weight: 700; color: #374151; background: transparent; border: none; outline: none; cursor: pointer; min-width: 140px; max-w-[180px];">
                @forelse($availableModels as $key => $name)
                    <option value="{{ $key }}" style="color: #111827;">{{ \Illuminate\Support\Str::limit($name, 25) }}</option>
                @empty
                    <option value="">{{ __('No models') }}</option>
                @endforelse
            </select>
            <!-- Custom Caret -->
            <div style="position: absolute; right: 10px; pointer-events: none; color: #9ca3af; display: flex; align-items: center;">
                <x-heroicon-m-chevron-up-down style="width: 16px; height: 16px;" />
            </div>
        </div>
        
    </div>
</div>
