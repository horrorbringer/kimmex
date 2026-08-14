<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\OptimizedFileUpload;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('DocumentFormTabs')
                    ->tabs([
                        // ─── TAB 1: CONTENT & IDENTITY ───
                        Tab::make('content_identity')
                            ->label(__('Content & Identity'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Tabs::make('LanguageTabs')
                                    ->tabs([
                                        // 🇬🇧 ENGLISH TAB
                                        Tab::make('en')
                                            ->label('🇬🇧 '.__('English (English)'))
                                            ->schema([
                                                Section::make(__('Document Identity (English)'))
                                                    ->components([
                                                        TextInput::make('title_en')
                                                            ->label(__('Document Title (English)'))
                                                            ->placeholder('e.g. KIM MEX Corporate Profile & Safety Guidelines 2026')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->suffixAction(AIHelper::getTranslateAction('title_en', 'title_km', 'Khmer', 'km', 'en'))
                                                            ->hintAction(AIHelper::getImproveAction('title_en', 'Improve this document title to be clear, professional, and descriptive.'))
                                                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                                                if (! $get('_slug_manual') && filled($state)) {
                                                                    $set('slug', Str::slug($state));
                                                                }
                                                            }),

                                                        Grid::make(2)->schema([
                                                            TextInput::make('slug')
                                                                ->label(__('Slug / URL Key'))
                                                                ->helperText(__('Auto-generated. Click ✏️ to edit manually.'))
                                                                ->unique(ignoreRecord: true)
                                                                ->required()
                                                                ->disabled(fn (Get $get) => ! $get('_slug_manual'))
                                                                ->dehydrated()
                                                                ->suffixAction(
                                                                    Action::make('toggleSlugManual')
                                                                        ->icon(fn (Get $get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                                                        ->tooltip(fn (Get $get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                                                        ->action(function (Set $set, Get $get) {
                                                                            $set('_slug_manual', ! $get('_slug_manual'));
                                                                        })
                                                                ),
                                                            Hidden::make('_slug_manual')->default(false)->dehydrated(false),

                                                            Select::make('document_category_id')
                                                                ->label(__('Document Category'))
                                                                ->relationship('documentCategory', 'name', fn ($query) => $query->where('isActive', true)->orderBy('name->en'))
                                                                ->searchable()
                                                                ->preload()
                                                                ->createOptionForm([
                                                                    TextInput::make('name')->label(__('Name'))->required(),
                                                                    TextInput::make('slug')->label(__('Slug'))->required(),
                                                                ])
                                                                ->required(),
                                                        ]),
                                                    ]),

                                                Section::make(__('Document Content (English)'))
                                                    ->components([
                                                        RichEditor::make('description_en')
                                                            ->label(__('Description & Overview (English)'))
                                                            ->placeholder(__('Write a comprehensive description or summary of this document...'))
                                                            ->hintActions([
                                                                AIHelper::getGenerateAction('description_en', 'Document Description'),
                                                                AIHelper::getImproveAction('description_en', 'Make this document overview clear, structured, and informative.'),
                                                                AIHelper::getTranslateAction('description_en', 'description_km', 'Khmer', 'km', 'en'),
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->columnSpanFull(),
                                                    ]),
                                            ]),

                                        // 🇰🇭 KHMER TAB
                                        Tab::make('km')
                                            ->label('🇰🇭 '.__('Khmer (ភាសាខ្មែរ)'))
                                            ->schema([
                                                Section::make(__('Document Identity (Khmer / ភាសាខ្មែរ)'))
                                                    ->components([
                                                        TextInput::make('title_km')
                                                            ->label(__('ចំណងជើងឯកសារ (Khmer Title)'))
                                                            ->placeholder('បញ្ចូលចំណងជើងឯកសារជាភាសាខ្មែរ...')
                                                            ->live(onBlur: true)
                                                            ->suffixAction(AIHelper::getTranslateAction('title_km', 'title_en', 'English', 'en', 'km'))
                                                            ->hintAction(AIHelper::getImproveAction('title_km', 'កែលម្អចំណងជើងឯកសារនេះឱ្យកាន់តែច្បាស់លាស់ និងមានលក្ខណៈវិជ្ជាជីវៈ')),
                                                    ]),

                                                Section::make(__('Document Content (Khmer / ភាសាខ្មែរ)'))
                                                    ->components([
                                                        RichEditor::make('description_km')
                                                            ->label(__('សេចក្តីពិពណ៌នាឯកសារ (Khmer Description)'))
                                                            ->placeholder('បញ្ចូលព័ត៌មានលម្អិត ឬសេចក្តីសង្ខេបនៃឯកសារជាភាសាខ្មែរ...')
                                                            ->hintActions([
                                                                AIHelper::getTranslateAction('description_km', 'description_en', 'English', 'en', 'km'),
                                                                AIHelper::getImproveAction('description_km', 'កែលម្អការពិពណ៌នានេះឱ្យកាន់តែត្រឹមត្រូវតាមវេយ្យាករណ៍ និងវិជ្ជាជីវៈ'),
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->columnSpanFull(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        // ─── TAB 2: FILES & MEDIA ───
                        Tab::make('files_media')
                            ->label(__('Files & Media'))
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        // 1. DOCUMENT FILE
                                        Section::make(__('Primary Document File'))
                                            ->description(__('Upload a file or provide a direct cloud download link.'))
                                            ->headerActions([
                                                Action::make('switchFileSource')
                                                    ->label(fn (Get $get) => ($get('fileUrl_source') ?? 'upload') === 'url' ? __('Switch to Upload') : __('Switch to External Link'))
                                                    ->icon(fn (Get $get) => ($get('fileUrl_source') ?? 'upload') === 'url' ? 'heroicon-m-arrow-up-tray' : 'heroicon-m-link')
                                                    ->color(fn (Get $get) => ($get('fileUrl_source') ?? 'upload') === 'url' ? 'primary' : 'gray')
                                                    ->tooltip(fn (Get $get) => ($get('fileUrl_source') ?? 'upload') === 'url' ? __('Switch to local file upload') : __('Switch to external URL (Google Drive, Dropbox, etc.)'))
                                                    ->action(function (Set $set, Get $get) {
                                                        $current = $get('fileUrl_source') ?? 'upload';
                                                        $set('fileUrl_source', $current === 'url' ? 'upload' : 'url');
                                                    }),
                                            ])
                                            ->components([
                                                Hidden::make('fileUrl_source')->default('upload'),

                                                FileUpload::make('fileUrl')
                                                    ->label(__('Upload File (PDF, DOCX, XLSX, ZIP, etc.)'))
                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                    ->visibility('public')
                                                    ->directory('documents/files')
                                                    ->acceptedFileTypes([
                                                        'application/pdf',
                                                        'application/msword',
                                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                        'application/vnd.ms-excel',
                                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                        'application/vnd.ms-powerpoint',
                                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                                        'application/zip',
                                                        'application/x-rar-compressed',
                                                        'text/plain',
                                                    ])
                                                    ->preserveFilenames()
                                                    ->maxSize(51200)
                                                    ->helperText(__('PDF, Word, Excel, PowerPoint, ZIP, TXT up to 50MB.'))
                                                    ->visible(fn (Get $get) => ($get('fileUrl_source') ?? 'upload') === 'upload'),

                                                TextInput::make('fileUrl_external')
                                                    ->label(__('External Document Link / Cloud URL'))
                                                    ->placeholder('https://drive.google.com/... or https://example.com/file.pdf')
                                                    ->prefixIcon('heroicon-o-link')
                                                    ->helperText(__('Supports Google Drive, OneDrive, Dropbox, AWS S3, or direct URL.'))
                                                    ->url()
                                                    ->live(onBlur: true)
                                                    ->suffixActions([
                                                        Action::make('openFileLink')
                                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                                            ->tooltip(__('Test link in new tab'))
                                                            ->url(fn (Get $get) => $get('fileUrl_external'), shouldOpenInNewTab: true)
                                                            ->visible(fn (Get $get) => filled($get('fileUrl_external'))),
                                                        Action::make('clearFileUrl')
                                                            ->icon('heroicon-o-x-mark')
                                                            ->tooltip(__('Clear URL'))
                                                            ->action(fn (Set $set) => $set('fileUrl_external', ''))
                                                            ->visible(fn (Get $get) => filled($get('fileUrl_external'))),
                                                    ])
                                                    ->visible(fn (Get $get) => $get('fileUrl_source') === 'url'),

                                                Placeholder::make('fileUrl_preview')
                                                    ->hiddenLabel()
                                                    ->content(function (Get $get) {
                                                        $url = trim((string) $get('fileUrl_external'));
                                                        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                                                            return new HtmlString('
                                                                <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 text-gray-400 text-center">
                                                                    <svg class="w-6 h-6 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                    <span class="text-xs font-medium">'.e(__('Enter an external document or cloud URL above.')).'</span>
                                                                </div>
                                                            ');
                                                        }

                                                        $host = parse_url($url, PHP_URL_HOST) ?? '';
                                                        $provider = 'Direct File Link';
                                                        $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700';

                                                        if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
                                                            $provider = 'Google Drive / Docs';
                                                            $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700';
                                                        } elseif (str_contains($host, 'onedrive') || str_contains($host, 'sharepoint')) {
                                                            $provider = 'Microsoft OneDrive';
                                                            $badgeColor = 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/30 dark:text-sky-300 dark:border-sky-700';
                                                        } elseif (str_contains($host, 'dropbox.com')) {
                                                            $provider = 'Dropbox';
                                                            $badgeColor = 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700';
                                                        }

                                                        $ext = strtoupper(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                                                        if (empty($ext) || strlen($ext) > 5) {
                                                            $ext = 'CLOUD-DOC';
                                                        }

                                                        return new HtmlString('
                                                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3.5 shadow-2xs space-y-2">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-mono font-bold text-[11px] text-titan-navy dark:text-gray-200 border border-gray-200 dark:border-gray-600 shrink-0">
                                                                            '.e($ext).'
                                                                        </div>
                                                                        <div class="min-w-0">
                                                                            <div class="font-semibold text-xs text-gray-900 dark:text-gray-100 flex items-center gap-1.5 truncate">
                                                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border '.e($badgeColor).'">'.e($provider).'</span>
                                                                            </div>
                                                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 font-mono">'.e($host).'</p>
                                                                        </div>
                                                                    </div>
                                                                    <a href="'.e($url).'" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-xs font-semibold inline-flex items-center gap-1 transition-all shrink-0">
                                                                        '.__('Test').' ↗
                                                                    </a>
                                                                </div>
                                                                <div class="flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium pt-1.5 border-t border-gray-100 dark:border-gray-700/60">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                    <span>'.__('External Document Connected').'</span>
                                                                </div>
                                                            </div>
                                                        ');
                                                    })
                                                    ->visible(fn (Get $get) => $get('fileUrl_source') === 'url'),
                                            ]),

                                        // 2. THUMBNAIL COVER
                                        Section::make(__('Document Thumbnail'))
                                            ->description(__('Optional preview image for card display.'))
                                            ->headerActions([
                                                Action::make('switchThumbSource')
                                                    ->label(fn (Get $get) => ($get('thumbnailUrl_source') ?? 'upload') === 'url' ? __('🖼️ Switch to Upload') : __('🔗 Switch to External URL'))
                                                    ->icon(fn (Get $get) => ($get('thumbnailUrl_source') ?? 'upload') === 'url' ? 'heroicon-m-arrow-up-tray' : 'heroicon-m-link')
                                                    ->color(fn (Get $get) => ($get('thumbnailUrl_source') ?? 'upload') === 'url' ? 'primary' : 'gray')
                                                    ->tooltip(fn (Get $get) => ($get('thumbnailUrl_source') ?? 'upload') === 'url' ? __('Switch to local image upload') : __('Switch to external image URL'))
                                                    ->action(function (Set $set, Get $get) {
                                                        $current = $get('thumbnailUrl_source') ?? 'upload';
                                                        $set('thumbnailUrl_source', $current === 'url' ? 'upload' : 'url');
                                                    }),
                                            ])
                                            ->components([
                                                Hidden::make('thumbnailUrl_source')->default('upload'),

                                                OptimizedFileUpload::image('thumbnailUrl')
                                                    ->label(__('Thumbnail File'))
                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                    ->visibility('public')
                                                    ->directory('documents/thumbnails')
                                                    ->visible(fn (Get $get) => ($get('thumbnailUrl_source') ?? 'upload') === 'upload'),

                                                TextInput::make('thumbnailUrl_external')
                                                    ->label(__('External Thumbnail URL'))
                                                    ->placeholder('https://images.unsplash.com/... or https://example.com/cover.jpg')
                                                    ->prefixIcon('heroicon-o-link')
                                                    ->helperText(__('Direct image link (JPG, PNG, WEBP, AVIF).'))
                                                    ->url()
                                                    ->live(onBlur: true)
                                                    ->suffixActions([
                                                        Action::make('openThumbLink')
                                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                                            ->tooltip(__('Open in new tab'))
                                                            ->url(fn (Get $get) => $get('thumbnailUrl_external'), shouldOpenInNewTab: true)
                                                            ->visible(fn (Get $get) => filled($get('thumbnailUrl_external'))),
                                                        Action::make('clearThumbUrl')
                                                            ->icon('heroicon-o-x-mark')
                                                            ->tooltip(__('Clear URL'))
                                                            ->action(fn (Set $set) => $set('thumbnailUrl_external', ''))
                                                            ->visible(fn (Get $get) => filled($get('thumbnailUrl_external'))),
                                                    ])
                                                    ->visible(fn (Get $get) => $get('thumbnailUrl_source') === 'url'),

                                                Placeholder::make('thumbnailUrl_preview')
                                                    ->hiddenLabel()
                                                    ->content(function (Get $get) {
                                                        $url = trim((string) $get('thumbnailUrl_external'));
                                                        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                                                            return new HtmlString('
                                                                <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 text-gray-400 text-center">
                                                                    <svg class="w-6 h-6 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                    <span class="text-xs font-medium">'.e(__('Enter an external image URL to see live preview.')).'</span>
                                                                </div>
                                                            ');
                                                        }

                                                        $ext = strtoupper(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                                                        if (empty($ext) || strlen($ext) > 5) {
                                                            $ext = 'IMG';
                                                        }

                                                        return new HtmlString('
                                                            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900 shadow-sm">
                                                                <div class="aspect-[16/9] w-full overflow-hidden bg-gray-950 flex items-center justify-center relative">
                                                                    <img src="'.e($url).'" 
                                                                         alt="Thumbnail Preview" 
                                                                         class="w-full h-full object-cover" 
                                                                         onload="
                                                                            const w = this.naturalWidth;
                                                                            const h = this.naturalHeight;
                                                                            const r = (w / h).toFixed(2);
                                                                            let ratioName = \'Landscape\';
                                                                            if (Math.abs(r - 1.78) < 0.1) ratioName = \'16:9\';
                                                                            else if (Math.abs(r - 1.33) < 0.1) ratioName = \'4:3\';
                                                                            else if (Math.abs(r - 1.0) < 0.1) ratioName = \'1:1\';
                                                                            else if (r < 0.9) ratioName = \'Portrait\';

                                                                            let quality = \'Standard\';
                                                                            let qColor = \'bg-blue-500/20 text-blue-400 border-blue-500/30\';
                                                                            if (w >= 1920) { quality = \'Full HD\'; qColor = \'bg-emerald-500/20 text-emerald-300 border-emerald-500/30\'; }
                                                                            else if (w >= 1280) { quality = \'HD\'; qColor = \'bg-cyan-500/20 text-cyan-300 border-cyan-500/30\'; }

                                                                            const metaBox = document.getElementById(\'doc-thumb-meta\');
                                                                            if (metaBox) {
                                                                                metaBox.innerHTML = `
                                                                                    <div class=\'flex items-center gap-1.5 flex-wrap text-[11px]\'>
                                                                                        <span class=\'font-mono font-bold text-gray-800 dark:text-gray-100\'>${w}&times;${h}px</span>
                                                                                        <span class=\'text-gray-300 dark:text-gray-600\'>&bull;</span>
                                                                                        <span class=\'text-gray-500 dark:text-gray-400\'>${ratioName}</span>
                                                                                        <span class=\'px-1 py-0.2 rounded text-[9px] font-bold border ${qColor}\'>${quality}</span>
                                                                                    </div>
                                                                                `;
                                                                            }
                                                                         "
                                                                         onerror="this.parentElement.innerHTML=\'<div class=\\\'p-3 text-xs text-rose-500 font-medium text-center\\\'>⚠️ Invalid image link.</div>\'" />
                                                                    <div class="absolute top-1.5 right-1.5 bg-black/75 backdrop-blur-md text-white text-[9px] font-mono font-bold px-1.5 py-0.5 rounded border border-white/10">
                                                                        '.e($ext).'
                                                                    </div>
                                                                </div>
                                                                <div class="p-2.5 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                                                                    <div id="doc-thumb-meta" class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                                        <span class="animate-pulse">⏳ '.__('Detecting...').'</span>
                                                                    </div>
                                                                    <a href="'.e($url).'" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold text-[11px] inline-flex items-center gap-0.5 shrink-0">
                                                                        '.__('View Full').' ↗
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        ');
                                                    })
                                                    ->visible(fn (Get $get) => $get('thumbnailUrl_source') === 'url'),
                                            ]),
                                    ]),
                            ]),

                        // ─── TAB 3: SETTINGS & VISIBILITY ───
                        Tab::make('settings')
                            ->label(__('Settings & Visibility'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make(__('Publishing & Access'))
                                    ->components([
                                        Grid::make(3)->schema([
                                            Toggle::make('isPublic')
                                                ->label(__('Publicly Accessible'))
                                                ->helperText(__('Visible in public document library'))
                                                ->default(true),
                                            Toggle::make('is_featured')
                                                ->label(__('Featured Document'))
                                                ->helperText(__('Pinned at the top of document library'))
                                                ->default(false),
                                            Toggle::make('isActive')
                                                ->label(__('Active Status'))
                                                ->helperText(__('Enable or disable document'))
                                                ->default(true),
                                        ]),
                                    ]),

                                Section::make(__('Document Statistics'))
                                    ->collapsed()
                                    ->hiddenOn('create')
                                    ->columns(3)
                                    ->components([
                                        TextInput::make('fileSize')
                                            ->label(__('File Size'))
                                            ->disabled(),
                                        TextInput::make('fileType')
                                            ->label(__('File Type'))
                                            ->disabled(),
                                        TextInput::make('downloadCount')
                                            ->label(__('Downloads'))
                                            ->numeric()
                                            ->disabled()
                                            ->default(0),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
