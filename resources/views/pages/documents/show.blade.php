@php
    use App\Models\Document;
    use Illuminate\Support\Facades\Storage;

    /** @var string $slug */
    $doc = Document::with('documentCategory')->where('slug', $slug)->where('isPublic', true)->firstOrFail();

    // For a related docs feel, just pull latest 4 from same category
    $relatedDocs = Document::with('documentCategory')->where('document_category_id', $doc->document_category_id)
        ->where('id', '!=', $doc->id)
        ->where('isPublic', true)
        ->latest()
        ->take(4)
        ->get();

    $categoryName = $doc->documentCategory ? $doc->documentCategory->getTranslation('name', app()->getLocale()) : $doc->category;
@endphp

<x-layouts.app :title="$doc->title" :description="$doc->description ?? ''">

    <div class="min-h-screen bg-white text-titan-navy">

        <!-- DARK HERO -->
        <section class="relative bg-titan-navy pt-[140px] pb-24 px-6 overflow-hidden">
            <!-- Cinematic Background -->
            <div class="absolute inset-0">
                @if($doc->thumbnailUrl)
                    <img src="{{ Storage::url($doc->thumbnailUrl) }}" class="w-full h-full object-cover opacity-60" alt="" />
                @else
                    <img src="{{ asset('images/heroes/documents-bg.png') }}" class="w-full h-full object-cover opacity-40" alt="" />
                @endif
                <!-- Brightened Overlays -->
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/40 via-transparent to-transparent"></div>
            </div>

            <!-- Intense Glow -->
            <div class="absolute top-0 right-0 -mr-32 -mt-32 w-[600px] h-[600px] bg-accent-orange/15 blur-[150px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 max-w-[1200px] mx-auto">
                <!-- Breadcrumb -->
                <nav
                    class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-white/30 mb-10">
                    <a href="/documents" class="hover:text-accent-orange transition-colors">{{ __('Documents') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-white/50">{{ $categoryName }}</span>
                </nav>

                <div class="flex flex-col lg:flex-row gap-16 items-start">
                    <!-- Left -->
                    <div class="flex-1">
                        @if($doc->is_featured)
                            <div
                                class="inline-flex items-center gap-2 bg-accent-orange text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg mb-6">
                                <x-lucide-star class="w-3 h-3" />
                                {{ __('FEATURED') }}
                            </div>
                        @endif
                        <div
                            class="inline-flex items-center gap-2 bg-white/5 border border-white/10 text-white/50 text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-lg mb-6 {{ $doc->is_featured ? 'ml-3' : '' }}">
                            {{ $categoryName }}
                        </div>

                        <h1
                            class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tighter mb-6 max-w-2xl">
                            {{ $doc->title }}
                        </h1>
                        <p class="text-white/60 text-lg leading-relaxed max-w-xl mb-10 font-medium">
                            {{ $doc->description }}</p>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-4">
                            @if($doc->fileUrl)
                                <a href="{{ Storage::url($doc->fileUrl) }}" download target="_blank"
                                    class="inline-flex items-center gap-3 bg-accent-orange hover:bg-white hover:text-titan-navy text-white px-8 py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-300 shadow-lg group">
                                    <x-lucide-download class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform" />
                                    {{ __('Download Document') }}
                                </a>
                            @endif
                            <a href="/documents"
                                class="inline-flex items-center gap-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white px-6 py-4 rounded-xl font-bold text-sm uppercase tracking-widest transition-all duration-300">
                                <x-lucide-arrow-left class="w-4 h-4" />
                                {{ __('Back to Library') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right: Meta card -->
                    <div class="lg:w-72 shrink-0 bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-sm">
                        <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-6">
                            {{ __('Document Info') }}</h3>
                        <div class="space-y-5">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                    <x-lucide-file-text class="w-3.5 h-3.5 text-accent-orange" />
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-white/25 uppercase tracking-wider">
                                        {{ __('Type') }}</div>
                                    <div class="text-sm font-bold text-white">{{ $doc->fileType ?? 'PDF' }}</div>
                                </div>
                            </div>
                            @if($doc->fileSize)
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                        <x-lucide-hard-drive class="w-3.5 h-3.5 text-accent-orange" />
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-white/25 uppercase tracking-wider">
                                            {{ __('File Size') }}</div>
                                        <div class="text-sm font-bold text-white">{{ $doc->fileSize }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                    <x-lucide-calendar class="w-3.5 h-3.5 text-accent-orange" />
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-white/25 uppercase tracking-wider">
                                        {{ __('Published') }}</div>
                                    <div class="text-sm font-bold text-white">{{ $doc->created_at->format('M Y') }}
                                    </div>
                                </div>
                            </div>
                            @if($doc->downloadCount > 0)
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                        <x-lucide-download class="w-3.5 h-3.5 text-accent-orange" />
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-white/25 uppercase tracking-wider">
                                            {{ __('Downloads') }}</div>
                                        <div class="text-sm font-bold text-white">{{ number_format($doc->downloadCount) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENT PREVIEW -->
        <section class="py-20 px-6 max-w-[1200px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Details -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="w-6 h-[2px] bg-accent-orange"></span>
                        <span
                            class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Resource Output') }}</span>
                    </div>
                    <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tight mb-8">{{ __('Overview') }}
                    </h2>

                    <div class="prose prose-lg text-titan-navy/80">
                        <p class="mb-4">{{ $doc->description }}</p>
                        <p class="text-sm text-titan-navy/50">
                            {{ __('This resource has been securely stored and verified by Kimmex Construction & Investment Co., Ltd. For any inquiries regarding its data, please contact the engineering department.') }}
                        </p>
                    </div>

                    @if($doc->fileUrl)
                        <!-- Download CTA box -->
                        <div
                            class="mt-10 p-8 rounded-2xl bg-titan-navy flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-40 h-40 bg-accent-orange/10 rounded-full blur-[60px] pointer-events-none">
                            </div>
                            <div class="relative z-10">
                                <div class="text-[10px] font-black text-accent-orange uppercase tracking-[0.4em] mb-2">
                                    {{ __('Secure Resource') }}</div>
                                <p class="text-white font-bold text-sm max-w-xs">
                                    {{ __('Get immediate access to this document in its original format.') }}</p>
                            </div>
                            <a href="{{ Storage::url($doc->fileUrl) }}" download target="_blank"
                                class="relative z-10 shrink-0 inline-flex items-center gap-3 bg-accent-orange hover:bg-white hover:text-titan-navy text-white px-7 py-3.5 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-300">
                                <x-lucide-download class="w-4 h-4" />
                                {{ __('Download Now') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Preview image -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="flex items-center gap-3 mb-8">
                            <span class="w-6 h-[2px] bg-accent-orange"></span>
                            <span
                                class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Preview') }}</span>
                        </div>
                        <div
                            class="rounded-2xl overflow-hidden border border-gray-100 shadow-lg aspect-[3/4] bg-gray-50 relative group flex items-center justify-center">
                            @if($doc->thumbnailUrl)
                                <img src="{{ Storage::url($doc->thumbnailUrl) }}" alt="{{ $doc->title }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[10s]" />
                            @else
                                <div
                                    class="absolute inset-0 bg-[radial-gradient(#00000010_1px,transparent_1px)] [background-size:10px_10px]">
                                </div>
                                <x-lucide-file-text
                                    class="w-24 h-24 text-titan-navy/10 relative z-10 group-hover:scale-110 group-hover:text-accent-orange transition-all duration-700" />
                            @endif
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 to-transparent flex items-end p-6 pointer-events-none">
                                <div class="text-white text-xs font-bold uppercase tracking-widest opacity-80">
                                    {{ $doc->fileType ?? 'DOCUMENT' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- RELATED DOCUMENTS -->
        @if(count($relatedDocs) > 0)
            <section class="bg-gray-50 py-16 px-6">
                <div class="max-w-[1200px] mx-auto">
                    <div class="flex items-center gap-3 mb-10">
                        <span class="w-6 h-[2px] bg-accent-orange"></span>
                        <span
                            class="text-[11px] font-black uppercase tracking-[0.35em] text-titan-navy/40">{{ __('Also Relevant') }}</span>
                    </div>
                    <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight mb-8">
                        {{ __('Related Documents') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($relatedDocs as $rel)
                            @php $relCatName = $rel->documentCategory ? $rel->documentCategory->getTranslation('name', app()->getLocale()) : $rel->category; @endphp
                            <a href="/documents/{{ $rel->slug }}"
                                class="group flex items-start gap-5 p-6 bg-white rounded-2xl border border-gray-100 hover:border-accent-orange/20 hover:shadow-md transition-all duration-300">
                                <div
                                    class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-gray-50 flex items-center justify-center relative">
                                    @if($rel->thumbnailUrl)
                                        <img src="{{ Storage::url($rel->thumbnailUrl) }}"
                                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                    @else
                                        <x-lucide-file-text
                                            class="w-6 h-6 text-titan-navy/20 relative z-10 group-hover:text-accent-orange transition-colors" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-black text-accent-orange uppercase tracking-widest mb-1">
                                        {{ $relCatName }}</div>
                                    <h4
                                        class="font-bold text-sm text-titan-navy group-hover:text-accent-orange transition-colors leading-snug">
                                        {{ $rel->title }}</h4>
                                    <div class="text-[11px] text-titan-navy/30 mt-1">{{ $rel->fileType ?? 'PDF' }}
                                        {{ $rel->fileSize ? '· ' . $rel->fileSize : '' }}</div>
                                </div>
                                <x-lucide-arrow-right
                                    class="w-4 h-4 text-titan-navy/20 group-hover:text-accent-orange group-hover:translate-x-1 transition-all duration-300 shrink-0 mt-1" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

    </div>

</x-layouts.app>