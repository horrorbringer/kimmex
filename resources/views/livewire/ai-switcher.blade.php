<div
    class="flex items-center px-3 py-1 ml-4 rounded-full shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center space-x-3">
        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">AI:</span>

        <div class="flex bg-gray-200 dark:bg-gray-700 p-0.5 rounded-lg">
            <button wire:click="switchProvider('gemini')" @class([
                'flex items-center px-2 py-1 text-xs font-medium rounded-md transition-all',
                'bg-white dark:bg-gray-600 shadow-sm text-primary-600 dark:text-primary-400' =>
                    $provider === 'gemini',
                'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' =>
                    $provider !== 'gemini',
            ]) title="Use Google Gemini">
                <x-heroicon-m-sparkles @class(['w-3.5 h-3.5', 'text-blue-500' => $provider === 'gemini']) />
            </button>

            <button wire:click="switchProvider('ollama')" @class([
                'flex items-center px-2 py-1 text-xs font-medium rounded-md transition-all',
                'bg-white dark:bg-gray-600 shadow-sm text-primary-600 dark:text-primary-400' =>
                    $provider === 'ollama',
                'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' =>
                    $provider !== 'ollama',
            ]) title="Use Ollama">
                <x-heroicon-m-cpu-chip @class(['w-3.5 h-3.5', 'text-orange-500' => $provider === 'ollama']) />
            </button>
        </div>

        <select wire:model.live="model"
            class="text-[10px] py-0.5 pl-2 pr-6 border-none bg-transparent font-medium text-gray-700 dark:text-gray-300 focus:ring-0 cursor-pointer max-w-[120px]">
            @forelse($availableModels as $key => $name)
                <option value="{{ $key }}">{{ \Illuminate\Support\Str::limit($name, 20) }}</option>
            @empty
                <option value="">No models</option>
            @endforelse
        </select>
    </div>
</div>
