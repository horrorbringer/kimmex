<div class="flex items-center gap-2 mr-2">
    <button wire:click="switchProvider('{{ $provider === 'gemini' ? 'ollama' : 'gemini' }}')" 
        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        title="{{ $provider === 'gemini' ? 'Switch to Ollama' : 'Switch to Google Gemini' }}">
        @if($provider === 'gemini')
            <x-heroicon-m-sparkles class="text-blue-500" style="width: 18px; height: 18px;" />
        @else
            <x-heroicon-m-cpu-chip class="text-orange-500" style="width: 18px; height: 18px;" />
        @endif
    </button>

    <select wire:model.live="model" 
        class="block w-40 rounded-lg border-gray-300 bg-white py-1 px-3 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
        style="height: 36px;">
        @forelse($availableModels as $key => $name)
            <option value="{{ $key }}">{{ \Illuminate\Support\Str::limit($name, 22) }}</option>
        @empty
            <option value="">No models</option>
        @endforelse
    </select>
</div>
