<?php

namespace App\Filament\Resources\NewsArticles\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\OptimizedFileUpload;
use App\Filament\Support\TranslationHelper;
use App\Models\Employee;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Support\PublicStorage;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class NewsArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('NewsEditor')
                    ->tabs([
                        // ─── TAB 1: DUAL LANGUAGE CONTENT (EN & KH) ───
                        Tab::make(__('Content'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Tabs::make('LanguageTabs')
                                    ->tabs([
                                        // ─── 🇬🇧 ENGLISH TAB ───
                                        Tab::make('🇬🇧 '.__('English (Original)'))
                                            ->schema([
                                                Section::make(__('Article Identity (English)'))
                                                    ->components([
                                                        TextInput::make('title_en')
                                                            ->label(__('Title (English)'))
                                                            ->placeholder('e.g., Kimmex Celebrates New Milestone in Sustainable Construction')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->suffixAction(AIHelper::getTranslateAction('title_en', 'title_km', 'Khmer', 'km', 'en'))
                                                            ->hintAction(AIHelper::getImproveAction('title_en', 'Improve this news title to be more catchy, concise, and professional.'))
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('_slug_manual')) {
                                                                    $set('slug', Str::slug($state));
                                                                }
                                                                if (! $get('metaTitle_en')) {
                                                                    $set('metaTitle_en', $state);
                                                                }
                                                            }),
                                                        TextInput::make('slug')
                                                            ->label(__('Slug (URL Path)'))
                                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Auto-generated from title. Click ✏️ to edit manually.'))
                                                            ->unique(ignoreRecord: true)
                                                            ->required()
                                                            ->disabled(fn ($get) => ! $get('_slug_manual'))
                                                            ->dehydrated()
                                                            ->suffixAction(
                                                                Action::make('toggleSlugManual')
                                                                    ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                                                    ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                                                    ->action(function (Set $set, $get) {
                                                                        $set('_slug_manual', ! $get('_slug_manual'));
                                                                    })
                                                            ),
                                                        Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                                                    ]),

                                                Section::make(__('Article Body (English)'))
                                                    ->headerActions([
                                                        Action::make('toggleExcerptEn')
                                                            ->label(fn ($get, $record) => ($get('_show_excerpt_en') ?? (filled($get('excerpt_en')) || filled($record?->getTranslation('excerpt', 'en', false)))) ? __('Hide Summary') : __('Add Summary / Excerpt'))
                                                            ->tooltip(__('Optional 1-2 sentence overview used for SEO search snippets, email newsletters, and social media share previews (Facebook, Telegram, LinkedIn).'))
                                                            ->icon(fn ($get, $record) => ($get('_show_excerpt_en') ?? (filled($get('excerpt_en')) || filled($record?->getTranslation('excerpt', 'en', false)))) ? 'heroicon-m-minus' : 'heroicon-m-plus')
                                                            ->size('xs')
                                                            ->outlined()
                                                            ->color(fn ($get, $record) => ($get('_show_excerpt_en') ?? (filled($get('excerpt_en')) || filled($record?->getTranslation('excerpt', 'en', false)))) ? 'gray' : 'primary')
                                                            ->action(function (Set $set, $get, $record) {
                                                                $isOpen = $get('_show_excerpt_en') ?? (filled($get('excerpt_en')) || filled($record?->getTranslation('excerpt', 'en', false)));
                                                                $set('_show_excerpt_en', ! $isOpen);
                                                            }),
                                                    ])
                                                    ->components([
                                                        Hidden::make('_show_excerpt_en')->default(false)->dehydrated(false),

                                                        Textarea::make('excerpt_en')
                                                            ->label(__('Excerpt / Summary (English)'))
                                                            ->placeholder('Brief overview of this news article for cards and previews...')
                                                            ->helperText(__('Short summary displayed in SEO search results, newsletter emails, and social media cards.'))
                                                            ->hintActions([
                                                                AIHelper::getImproveAction('excerpt_en', 'Make this news summary more engaging and impactful.'),
                                                                AIHelper::getTranslateAction('excerpt_en', 'excerpt_km', 'Khmer', 'km', 'en'),
                                                            ])
                                                            ->rows(2)
                                                            ->visible(fn ($get, $record) => $get('_show_excerpt_en') ?? (filled($get('excerpt_en')) || filled($record?->getTranslation('excerpt', 'en', false))))
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('metaDescription_en')) {
                                                                    $set('metaDescription_en', $state);
                                                                }
                                                            }),

                                                        RichEditor::make('content_en')->resizableImages()
                                                            ->label(__('Content (English)'))
                                                            ->extraInputAttributes(['style' => 'min-height: 5rem;'])
                                                            ->required()
                                                            ->toolbarButtons([
                                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                                [ToolbarButtonGroup::make('Heading', ['h2', 'h3', 'h4'])->textualButtons()],
                                                                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                                                ['blockquote', 'bulletList', 'orderedList', 'table'],
                                                                ['attachFiles', 'horizontalRule', 'codeBlock'],
                                                                ['undo', 'redo'],
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->fileAttachmentsDirectory('news/content')
                                                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'])
                                                            ->hintActions([
                                                                self::getInsertExternalMediaAction('content_en'),
                                                                AIHelper::getGenerateAction('content_en', 'News Article'),
                                                                AIHelper::getImproveAction('content_en', 'Rewrite this news article to be more professional and articulate.'),
                                                                AIHelper::getTranslateAction('content_en', 'content_km', 'Khmer', 'km', 'en'),
                                                            ])
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, $state, $get) {
                                                                if (! $get('excerpt_en')) {
                                                                    $excerpt = Str::limit(strip_tags($state), 160);
                                                                    $set('excerpt_en', $excerpt);
                                                                    if (! $get('metaDescription_en')) {
                                                                        $set('metaDescription_en', $excerpt);
                                                                    }
                                                                }
                                                                $wordCount = str_word_count(strip_tags($state));
                                                                $set('readTime', (int) ceil($wordCount / 200));
                                                            }),
                                                    ]),

                                                Section::make(__('SEO Meta Tags (English)'))
                                                    ->collapsed()
                                                    ->description(__('Custom SEO title and description for search engines'))
                                                    ->components([
                                                        TextInput::make('metaTitle_en')
                                                            ->label(__('Meta Title (English)')),
                                                        Textarea::make('metaDescription_en')
                                                            ->label(__('Meta Description (English)'))
                                                            ->rows(2),
                                                    ]),
                                            ]),

                                        // ─── 🇰🇭 KHMER TAB ───
                                        Tab::make('🇰🇭 '.__('Khmer (ភាសាខ្មែរ)'))
                                            ->schema([
                                                Section::make(__('Article Identity (Khmer / ភាសាខ្មែរ)'))
                                                    ->components([
                                                        TextInput::make('title_km')
                                                            ->label(__('ចំណងជើងព័ត៌មាន (Khmer Title)'))
                                                            ->placeholder('បញ្ចូលចំណងជើងព័ត៌មានជាភាសាខ្មែរ...')
                                                            ->live(onBlur: true)
                                                            ->suffixAction(AIHelper::getTranslateAction('title_km', 'title_en', 'English', 'en', 'km'))
                                                            ->hintAction(AIHelper::getImproveAction('title_km', 'កែលម្អចំណងជើងព័ត៌មាននេះឱ្យកាន់តែទាក់ទាញ និងច្បាស់លាស់'))
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('metaTitle_km')) {
                                                                    $set('metaTitle_km', $state);
                                                                }
                                                            }),
                                                    ]),

                                                Section::make(__('Article Body (Khmer / ភាសាខ្មែរ)'))
                                                    ->headerActions([
                                                        Action::make('toggleExcerptKm')
                                                            ->label(fn ($get, $record) => ($get('_show_excerpt_km') ?? (filled($get('excerpt_km')) || filled($record?->getTranslation('excerpt', 'km', false)))) ? __('លាក់សេចក្តីសង្ខេប (Hide)') : __('+ បន្ថែមសេចក្តីសង្ខេប (+ Add Summary)'))
                                                            ->tooltip(__('សេចក្តីសង្ខេបខ្លី ១-២ ប្រយោគសម្រាប់បង្ហាញលើការចែករំលែកបណ្តាញសង្គម (Facebook/Telegram), លទ្ធផលស្វែងរក SEO Google និងអ៊ីមែលព័ត៌មាន (Optional summary for social sharing, SEO snippets, and newsletters).'))
                                                            ->icon(fn ($get, $record) => ($get('_show_excerpt_km') ?? (filled($get('excerpt_km')) || filled($record?->getTranslation('excerpt', 'km', false)))) ? 'heroicon-m-minus' : 'heroicon-m-plus')
                                                            ->size('xs')
                                                            ->outlined()
                                                            ->color(fn ($get, $record) => ($get('_show_excerpt_km') ?? (filled($get('excerpt_km')) || filled($record?->getTranslation('excerpt', 'km', false)))) ? 'gray' : 'primary')
                                                            ->action(function (Set $set, $get, $record) {
                                                                $isOpen = $get('_show_excerpt_km') ?? (filled($get('excerpt_km')) || filled($record?->getTranslation('excerpt', 'km', false)));
                                                                $set('_show_excerpt_km', ! $isOpen);
                                                            }),
                                                    ])
                                                    ->components([
                                                        Hidden::make('_show_excerpt_km')->default(false)->dehydrated(false),

                                                        Textarea::make('excerpt_km')
                                                            ->label(__('សេចក្តីសង្ខេប (Khmer Excerpt)'))
                                                            ->placeholder('បញ្ចូលសេចក្តីសង្ខេបព័ត៌មានជាភាសាខ្មែរ...')
                                                            ->helperText(__('សេចក្តីសង្ខេបខ្លីសម្រាប់បង្ហាញលើការចែករំលែកបណ្តាញសង្គម (Facebook/Telegram), SEO និងអ៊ីមែលព័ត៌មាន។'))
                                                            ->hintActions([
                                                                AIHelper::getTranslateAction('excerpt_km', 'excerpt_en', 'English', 'en', 'km'),
                                                                AIHelper::getImproveAction('excerpt_km', 'កែលម្អសេចក្តីសង្ខេបនេះឱ្យកាន់តែច្បាស់លាស់'),
                                                            ])
                                                            ->rows(2)
                                                            ->visible(fn ($get, $record) => $get('_show_excerpt_km') ?? (filled($get('excerpt_km')) || filled($record?->getTranslation('excerpt', 'km', false))))
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('metaDescription_km')) {
                                                                    $set('metaDescription_km', $state);
                                                                }
                                                            }),

                                                        RichEditor::make('content_km')->resizableImages()
                                                            ->label(__('ខ្លឹមសារព័ត៌មាន (Khmer Content)'))
                                                            ->extraInputAttributes(['style' => 'min-height: 5rem;'])
                                                            ->toolbarButtons([
                                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                                [ToolbarButtonGroup::make('Heading', ['h2', 'h3', 'h4'])->textualButtons()],
                                                                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                                                ['blockquote', 'bulletList', 'orderedList', 'table'],
                                                                ['attachFiles', 'horizontalRule', 'codeBlock'],
                                                                ['undo', 'redo'],
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->fileAttachmentsDirectory('news/content')
                                                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'])
                                                            ->hintActions([
                                                                self::getCopyFromEnglishAction('content_km'),
                                                                self::getSyncMediaFromEnglishAction('content_km'),
                                                                self::getInsertExternalMediaAction('content_km'),
                                                                AIHelper::getTranslateAction('content_km', 'content_en', 'English', 'en', 'km'),
                                                                AIHelper::getImproveAction('content_km', 'កែលម្អអត្ថបទនេះឱ្យកាន់តែមានលក្ខណៈវិជ្ជាជីវៈ និងត្រឹមត្រូវតាមវេយ្យាករណ៍'),
                                                            ])
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, $state, $get) {
                                                                if (! $get('excerpt_km') && $state) {
                                                                    $excerpt = Str::limit(strip_tags($state), 160);
                                                                    $set('excerpt_km', $excerpt);
                                                                    if (! $get('metaDescription_km')) {
                                                                        $set('metaDescription_km', $excerpt);
                                                                    }
                                                                }
                                                            }),
                                                    ]),

                                                Section::make(__('SEO Meta Tags (Khmer / ភាសាខ្មែរ)'))
                                                    ->collapsed()
                                                    ->description(__('Custom SEO title and description in Khmer'))
                                                    ->components([
                                                        TextInput::make('metaTitle_km')
                                                            ->label(__('Meta Title (Khmer)')),
                                                        Textarea::make('metaDescription_km')
                                                            ->label(__('Meta Description (Khmer)'))
                                                            ->rows(2),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        // ─── TAB 2: MEDIA ───
                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('Cover Image'))
                                    ->description(__('Recommended: 1200 × 675 px (16:9 ratio). Ideal for news cards, social media sharing (OG), and hero banners.'))
                                    ->components([
                                        ToggleButtons::make('coverImage_source')
                                            ->label(__('Cover Image Source'))
                                            ->options([
                                                'upload' => __('Upload File'),
                                                'url' => __('External Image URL'),
                                            ])
                                            ->icons([
                                                'upload' => 'heroicon-m-arrow-up-tray',
                                                'url' => 'heroicon-m-link',
                                            ])
                                            ->colors([
                                                'upload' => 'primary',
                                                'url' => 'info',
                                            ])
                                            ->default('upload')
                                            ->inline()
                                            ->live(),

                                        OptimizedFileUpload::hero('coverImage')
                                            ->directory('news/covers')
                                            ->label(__('Cover Image File'))
                                            ->helperText(__('Recommended: 1200 × 675 px (16:9) or 1600 × 900 px. Formats: WebP, JPG, PNG (Max 5MB).'))
                                            ->hintAction(static::getPreviewSocialShareAction())
                                            ->live()
                                            ->visible(fn (Get $get) => ($get('coverImage_source') ?? 'upload') === 'upload'),

                                        TextInput::make('coverImageUrl')
                                            ->label(__('External Cover Image URL'))
                                            ->helperText(__('Direct URL to high-resolution landscape image (16:9 recommended, e.g. 1200 × 675 px).'))
                                            ->placeholder('https://images.unsplash.com/... or https://example.com/image.jpg')
                                            ->prefixIcon('heroicon-o-link')
                                            ->hintAction(static::getPreviewSocialShareAction())
                                            ->url()
                                            ->live(onBlur: true)
                                            ->suffixActions([
                                                Action::make('openLink')
                                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                                    ->tooltip(__('Open image in new tab'))
                                                    ->url(fn (Get $get) => $get('coverImageUrl'), shouldOpenInNewTab: true)
                                                    ->visible(fn (Get $get) => filled($get('coverImageUrl'))),
                                                Action::make('clearUrl')
                                                    ->icon('heroicon-o-x-mark')
                                                    ->tooltip(__('Clear URL'))
                                                    ->action(fn (Set $set) => $set('coverImageUrl', ''))
                                                    ->visible(fn (Get $get) => filled($get('coverImageUrl'))),
                                            ])
                                            ->visible(fn (Get $get) => $get('coverImage_source') === 'url'),

                                        Placeholder::make('coverImage_preview')
                                            ->hiddenLabel()
                                            ->content(function (Get $get) {
                                                $url = trim((string) $get('coverImageUrl'));
                                                if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                                                    return new HtmlString('
                                                        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 text-gray-400 text-center">
                                                            <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            <span class="text-xs font-medium">'.e(__('Enter a valid image URL above to see live preview and metadata.')).'</span>
                                                        </div>
                                                    ');
                                                }

                                                $ext = strtoupper(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                                                if (empty($ext) || strlen($ext) > 5) {
                                                    $ext = 'WEB-IMG';
                                                }

                                                return new HtmlString('
                                                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900 shadow-md max-w-xl">
                                                        <div class="aspect-[16/9] w-full overflow-hidden bg-gray-950 flex items-center justify-center relative group">
                                                            <img src="'.e($url).'" 
                                                                 alt="Cover Preview" 
                                                                 class="w-full h-full object-cover" 
                                                                 onload="
                                                                    const w = this.naturalWidth;
                                                                    const h = this.naturalHeight;
                                                                    const r = (w / h).toFixed(2);
                                                                    let ratioName = \'Landscape\';
                                                                    if (Math.abs(r - 1.78) < 0.1) ratioName = \'16:9 (Hero Standard)\';
                                                                    else if (Math.abs(r - 1.33) < 0.1) ratioName = \'4:3\';
                                                                    else if (Math.abs(r - 1.0) < 0.1) ratioName = \'1:1 (Square)\';
                                                                    else if (r < 0.9) ratioName = \'Portrait\';

                                                                    let quality = \'Standard\';
                                                                    let qColor = \'bg-blue-500/20 text-blue-400 border-blue-500/30\';
                                                                    if (w >= 3840) { quality = \'4K Ultra HD\'; qColor = \'bg-amber-500/20 text-amber-300 border-amber-500/30\'; }
                                                                    else if (w >= 2560) { quality = \'2K QHD\'; qColor = \'bg-purple-500/20 text-purple-300 border-purple-500/30\'; }
                                                                    else if (w >= 1920) { quality = \'Full HD 1080p\'; qColor = \'bg-emerald-500/20 text-emerald-300 border-emerald-500/30\'; }
                                                                    else if (w >= 1280) { quality = \'HD 720p\'; qColor = \'bg-cyan-500/20 text-cyan-300 border-cyan-500/30\'; }

                                                                    const metaBox = document.getElementById(\'cover-img-meta\');
                                                                    if (metaBox) {
                                                                        metaBox.innerHTML = `
                                                                            <div class=\'flex items-center gap-2 flex-wrap\'>
                                                                                <span class=\'font-mono font-bold text-gray-800 dark:text-gray-100\'>${w} &times; ${h} px</span>
                                                                                <span class=\'text-gray-300 dark:text-gray-600\'>&bull;</span>
                                                                                <span class=\'text-gray-500 dark:text-gray-400\'>${ratioName}</span>
                                                                                <span class=\'px-1.5 py-0.5 rounded text-[10px] font-bold border ${qColor}\'>${quality}</span>
                                                                            </div>
                                                                        `;
                                                                    }
                                                                 "
                                                                 onerror="this.parentElement.innerHTML=\'<div class=\\\'p-6 text-xs text-rose-500 font-medium text-center\\\'>⚠️ Unable to load image from this URL. Please verify the link.</div>\'" />
                                                            <div class="absolute top-2 right-2 bg-black/75 backdrop-blur-md text-white text-[10px] font-mono font-bold px-2 py-1 rounded-md border border-white/10 shadow-sm">
                                                                '.e($ext).'
                                                            </div>
                                                        </div>
                                                        <div class="p-3.5 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 space-y-2">
                                                            <div class="flex items-center justify-between text-xs">
                                                                <div id="cover-img-meta" class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                                                    <span class="animate-pulse">⏳ '.__('Detecting dimensions...').'</span>
                                                                </div>
                                                                <a href="'.e($url).'" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold text-xs inline-flex items-center gap-1 shrink-0">
                                                                    '.__('View Full Size').' ↗
                                                                </a>
                                                            </div>
                                                            <div class="flex items-center gap-2 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium pt-1 border-t border-gray-100 dark:border-gray-700/60">
                                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                <span>'.__('Live External Image Connected').'</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->visible(fn (Get $get) => $get('coverImage_source') === 'url'),
                                    ]),

                                Section::make(__('Gallery'))
                                    ->description(__('Upload files or add external image links (max 12). Recommended: 1200 × 800 px (3:2) or 1200 × 900 px (4:3).'))
                                    ->collapsible()
                                    ->components([
                                        ToggleButtons::make('gallery_source')
                                            ->label(__('Gallery Source'))
                                            ->options([
                                                'upload' => __('Upload Files'),
                                                'urls' => __('External Image URLs'),
                                                'both' => __('Both (Uploads + URLs)'),
                                            ])
                                            ->icons([
                                                'upload' => 'heroicon-m-arrow-up-tray',
                                                'urls' => 'heroicon-m-link',
                                                'both' => 'heroicon-m-sparkles',
                                            ])
                                            ->colors([
                                                'upload' => 'primary',
                                                'urls' => 'info',
                                                'both' => 'warning',
                                            ])
                                            ->default('upload')
                                            ->inline()
                                            ->live(),

                                        FileUpload::make('gallery')
                                            ->label(__('Gallery Images (Upload)'))
                                            ->helperText(__('Recommended standard: 1200 × 800 px (3:2) or 1200 × 900 px (4:3). Formats: WebP, JPG, PNG.'))
                                            ->image()
                                            ->multiple()
                                            ->maxFiles(12)
                                            ->reorderable()
                                            ->disk(config('filesystems.public_uploads_disk'))
                                            ->visibility('public')
                                            ->directory('news/gallery')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('1080')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                                            ->mimeTypeMap(['webp' => 'image/webp', 'avif' => 'image/avif'])
                                            ->panelLayout('grid')
                                            ->visible(fn (Get $get) => in_array($get('gallery_source') ?? 'upload', ['upload', 'both'], true)),

                                        TagsInput::make('galleryUrls')
                                            ->label(__('External Gallery URLs'))
                                            ->helperText(__('Add direct URLs to photos (1200 × 800 px or 1200 × 900 px recommended).'))
                                            ->placeholder(__('Paste image URL and press Enter...'))
                                            ->prefixIcon('heroicon-o-link')
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Paste direct image links (https://...) and hit Enter to add multiple.'))
                                            ->live(onBlur: true)
                                            ->visible(fn (Get $get) => in_array($get('gallery_source'), ['urls', 'both'], true)),

                                        Placeholder::make('galleryUrls_preview')
                                            ->hiddenLabel()
                                            ->content(function (Get $get) {
                                                $urls = (array) ($get('galleryUrls') ?? []);
                                                $validUrls = array_filter($urls, fn ($u) => is_string($u) && filter_var(trim($u), FILTER_VALIDATE_URL));

                                                if (empty($validUrls)) {
                                                    return new HtmlString('
                                                        <div class="p-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-400 text-center">
                                                            '.__('No external gallery URLs added yet. Paste links above to see preview grid with dimensions.').'
                                                        </div>
                                                    ');
                                                }

                                                $itemsHtml = '';
                                                foreach ($validUrls as $idx => $url) {
                                                    $ext = strtoupper(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)) ?: 'IMG';
                                                    $itemsHtml .= '
                                                        <div class="relative group rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900 aspect-[16/10] shadow-sm">
                                                            <img src="'.e($url).'" 
                                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                                                                 onload="
                                                                    const el = document.getElementById(\'g-meta-'.($idx + 1).'\');
                                                                    if (el) el.innerText = `${this.naturalWidth}&times;${this.naturalHeight}`;
                                                                 "
                                                                 onerror="this.parentElement.classList.add(\'opacity-50\')" />
                                                            <div class="absolute top-1 left-1 bg-black/75 text-white text-[9px] font-bold px-1.5 py-0.5 rounded backdrop-blur-xs">#'.($idx + 1).'</div>
                                                            <div id="g-meta-'.($idx + 1).'" class="absolute bottom-1 left-1 bg-black/75 text-white text-[9px] font-mono px-1.5 py-0.5 rounded backdrop-blur-xs">...</div>
                                                            <a href="'.e($url).'" target="_blank" class="absolute bottom-1 right-1 bg-black/75 hover:bg-black text-white text-[9px] font-medium px-1.5 py-0.5 rounded backdrop-blur-xs">↗</a>
                                                        </div>
                                                    ';
                                                }

                                                return new HtmlString('
                                                    <div class="pt-2">
                                                        <div class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-1.5">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                            '.count($validUrls).' '.__('External Images Active (with live dimensions):').'
                                                        </div>
                                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                                            '.$itemsHtml.'
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->visible(fn (Get $get) => in_array($get('gallery_source'), ['urls', 'both'], true)),
                                    ]),

                                Section::make(__('Video'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->components([
                                        TextInput::make('videoUrl')
                                            ->label(__('Video URL'))
                                            ->url()
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Paste a YouTube or Vimeo link.')),
                                    ]),
                            ]),

                        // ─── TAB 3: PUBLISHING ───
                        Tab::make(__('Publishing'))
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make(__('Schedule & Category'))
                                    ->columns(3)
                                    ->components([
                                        DateTimePicker::make('publishedAt')
                                            ->label(__('Published At'))
                                            ->required()
                                            ->default(now())
                                            ->native(false),
                                        Select::make('news_category_id')
                                            ->label(__('Category'))
                                            ->relationship('newsCategory', 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()) ?: $record->getTranslation('name', 'en'))
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                Grid::make(2)->components([
                                                    TextInput::make('name')
                                                        ->label(__('Category Name'))
                                                        ->placeholder('e.g., Corporate News')
                                                        ->required()
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                                        ->suffixAction(TranslationHelper::getAutoTranslateAction('name')),
                                                    TextInput::make('slug')
                                                        ->label(__('Slug'))
                                                        ->placeholder('e.g., corporate-news')
                                                        ->required(),
                                                ]),
                                                Textarea::make('description')
                                                    ->label(__('Description'))
                                                    ->rows(2)
                                                    ->hintAction(TranslationHelper::getAutoTranslateAction('description')),
                                                Grid::make(2)->components([
                                                    TextInput::make('order_index')
                                                        ->label(__('Display Order'))
                                                        ->numeric()
                                                        ->default(0),
                                                    Toggle::make('is_active')
                                                        ->label(__('Active Status'))
                                                        ->default(true),
                                                ]),
                                            ])
                                            ->createOptionAction(fn (Action $action) => $action
                                                ->modalHeading(__('Create News Category'))
                                                ->modalWidth('lg')
                                            )
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state) {
                                                    $cat = NewsCategory::find($state);
                                                    if ($cat) {
                                                        $set('category', $cat->getTranslation('name', 'en'));
                                                    }
                                                }
                                            }),
                                        Hidden::make('category'),
                                        TextInput::make('readTime')
                                            ->label(__('Read Time'))
                                            ->suffix(__('mins'))
                                            ->numeric(),
                                    ]),

                                Section::make(__('Author Details'))
                                    ->description(__('Author profile and avatar will be featured at the bottom of the article and on news preview cards.'))
                                    ->columns(3)
                                    ->components([
                                        Select::make('authorId')
                                            ->label(__('Author (Employee)'))
                                            ->helperText(__('Select linked employee profile'))
                                            ->relationship('author', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state) {
                                                    $employee = Employee::find($state);
                                                    if ($employee) {
                                                        $set('authorName_en', $employee->name);
                                                        $set('authorName_km', $employee->name);
                                                    }
                                                }
                                            })
                                            ->default(auth()->user()?->employee?->id),
                                        TextInput::make('authorName_en')
                                            ->label(__('Author Name (English)'))
                                            ->helperText(__('Custom display name (EN)'))
                                            ->suffixAction(AIHelper::getTranslateAction('authorName_en', 'authorName_km', 'Khmer', 'km', 'en'))
                                            ->default(auth()->user()?->name),
                                        TextInput::make('authorName_km')
                                            ->label(__('Author Name (Khmer)'))
                                            ->helperText(__('Custom display name (KM)'))
                                            ->suffixAction(AIHelper::getTranslateAction('authorName_km', 'authorName_en', 'English', 'en', 'km'))
                                            ->default(auth()->user()?->name),
                                    ]),

                                Section::make(__('Tags & Visibility'))
                                    ->columns(2)
                                    ->components([
                                        TagsInput::make('tags')
                                            ->label(__('Tags'))
                                            ->placeholder('news, update, announcement')
                                            ->columnSpanFull(),
                                        TextInput::make('year')
                                            ->label(__('Year'))
                                            ->numeric()
                                            ->default(date('Y')),
                                        Grid::make(3)->components([
                                            Toggle::make('isFeatured')
                                                ->label(__('Featured'))
                                                ->inline(false),
                                            Toggle::make('isTrending')
                                                ->label(__('Trending'))
                                                ->inline(false),
                                            Toggle::make('isActive')
                                                ->label(__('Active'))
                                                ->default(true)
                                                ->inline(false),
                                        ]),
                                    ]),

                                Section::make(__('Related Projects'))
                                    ->collapsed()
                                    ->description(__('Link this article to related projects'))
                                    ->components([
                                        Select::make('projects')
                                            ->label(__('Projects'))
                                            ->relationship('projects', 'title')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function getInsertExternalMediaAction(string $targetField): Action
    {
        return Action::make('insertExternalMedia_'.$targetField)
            ->label(__('Insert External Link / Image'))
            ->icon('heroicon-m-link')
            ->color('info')
            ->modalHeading(__('Insert External Media or Link'))
            ->modalDescription(__('Embed an external image or create a file download link from any URL.'))
            ->modalSubmitActionLabel(__('Insert into Article'))
            ->form([
                ToggleButtons::make('media_type')
                    ->label(__('Media Type'))
                    ->options([
                        'image' => __('External Image (Embed <img>)'),
                        'link' => __('File / Web Link (Download <a> link)'),
                    ])
                    ->icons([
                        'image' => 'heroicon-m-photo',
                        'link' => 'heroicon-m-arrow-down-tray',
                    ])
                    ->colors([
                        'image' => 'primary',
                        'link' => 'success',
                    ])
                    ->default('image')
                    ->inline()
                    ->live(),
                TextInput::make('url')
                    ->label(__('External URL'))
                    ->placeholder('https://example.com/image.webp or https://.../document.pdf')
                    ->url()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->label(fn (Get $get) => $get('media_type') === 'image' ? __('Image Caption / Alt Text') : __('Link / Button Text'))
                    ->placeholder(fn (Get $get) => $get('media_type') === 'image' ? 'e.g. Groundbreaking Ceremony' : 'e.g. Download Press Release (PDF)')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, Set $set, Get $get) use ($targetField) {
                $currentHtml = (string) ($get($targetField) ?? '');
                $url = htmlspecialchars($data['url'], ENT_QUOTES, 'UTF-8');
                $caption = htmlspecialchars($data['caption'], ENT_QUOTES, 'UTF-8');

                if ($data['media_type'] === 'image') {
                    $snippet = "<p><img src=\"{$url}\" alt=\"{$caption}\" class=\"rounded-xl shadow-md my-4 max-w-full h-auto\" /></p>";
                } else {
                    $snippet = "<p><a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"text-titan-red underline font-bold inline-flex items-center gap-1\">{$caption} &rarr;</a></p>";
                }

                $set($targetField, $currentHtml.$snippet);
            });
    }

    protected static function getCopyFromEnglishAction(string $targetField = 'content_km'): Action
    {
        return Action::make('copyFromEnglish_'.$targetField)
            ->label(__('Copy from EN'))
            ->icon('heroicon-m-document-duplicate')
            ->tooltip(__('Copy content & embedded images from English to Khmer'))
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('Copy content & images from English?'))
            ->modalDescription(__('This will copy the full article and all embedded images from the English tab into Khmer, so you can edit the Khmer text around the images without re-uploading.'))
            ->modalSubmitActionLabel(__('Copy into Khmer Editor'))
            ->action(function (Get $get, Set $set) use ($targetField) {
                $enContent = (string) ($get('content_en') ?? '');

                if (empty(trim(strip_tags($enContent)))) {
                    Notification::make()
                        ->warning()
                        ->title(__('English content is empty'))
                        ->body(__('Please write content or upload images in the English tab first.'))
                        ->send();

                    return;
                }

                $set($targetField, $enContent);

                Notification::make()
                    ->success()
                    ->title(__('Copied from English'))
                    ->body(__('Content and embedded images have been copied into the Khmer editor. You can now edit the Khmer text around the images.'))
                    ->send();
            });
    }

    protected static function getSyncMediaFromEnglishAction(string $targetField = 'content_km'): Action
    {
        return Action::make('syncMediaFromEnglish_'.$targetField)
            ->label(__('Sync Images from EN'))
            ->icon('heroicon-m-photo')
            ->tooltip(__('Sync all images uploaded in English into this Khmer editor'))
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('Sync images from English?'))
            ->modalDescription(__('This extracts all images and figures from English that are missing in Khmer and inserts them.'))
            ->modalSubmitActionLabel(__('Sync Images'))
            ->action(function (Get $get, Set $set) use ($targetField) {
                $enContent = (string) ($get('content_en') ?? '');
                $kmContent = (string) ($get($targetField) ?? '');

                preg_match_all('/<figure\b[^>]*>[\s\S]*?<\/figure>|<p\b[^>]*>(?:\s*<img\b[^>]*\/?>\s*)+<\/p>|<img\b[^>]*\/?>/i', $enContent, $matches);
                $images = $matches[0] ?? [];

                if (empty($images)) {
                    Notification::make()
                        ->warning()
                        ->title(__('No images found in English'))
                        ->body(__('The English article does not have any embedded images to sync.'))
                        ->send();

                    return;
                }

                $changed = false;

                // 1. Clean and replace any temporary Livewire preview images in Khmer with the clean English image tags
                if (str_contains($kmContent, 'preview-file') || str_contains($kmContent, 'livewire-')) {
                    $imgIndex = 0;
                    $kmContent = preg_replace_callback(
                        '/<p>\s*<img[^>]*(?:preview-file|livewire-)[^>]*\/?>\s*<\/p>|<img[^>]*(?:preview-file|livewire-)[^>]*\/?>/i',
                        function () use ($images, &$imgIndex, &$changed) {
                            $replacement = $images[$imgIndex] ?? null;
                            if ($replacement) {
                                $imgIndex++;
                                $changed = true;

                                return str_starts_with($replacement, '<p>') ? $replacement : "<p>{$replacement}</p>";
                            }

                            return '';
                        },
                        $kmContent
                    ) ?? $kmContent;
                }

                // 2. Insert any remaining English images that are not yet present in Khmer
                $appended = 0;
                foreach ($images as $imgTag) {
                    $identifier = null;
                    if (preg_match('/src=([\'"])(.*?)\1/i', $imgTag, $srcMatch)) {
                        $identifier = $srcMatch[2];
                    } elseif (preg_match('/data-id=([\'"])(.*?)\1/i', $imgTag, $idMatch)) {
                        $identifier = $idMatch[2];
                    }

                    if ($identifier && ! str_contains($kmContent, $identifier)) {
                        $kmContent .= "\n".(str_starts_with($imgTag, '<p>') ? $imgTag : "<p>{$imgTag}</p>");
                        $appended++;
                        $changed = true;
                    }
                }

                if ($changed) {
                    $set($targetField, $kmContent);
                    Notification::make()
                        ->success()
                        ->title(__('Images Synced'))
                        ->body(__('English images have been synced and temporary files updated in the Khmer editor.'))
                        ->send();
                } else {
                    Notification::make()
                        ->info()
                        ->title(__('Already up to date'))
                        ->body(__('All images from English are already present in the Khmer editor.'))
                        ->send();
                }
            });
    }

    public static function getPreviewSocialShareAction(): Action
    {
        return Action::make('previewSocialShare')
            ->label(__('Preview Social Share'))
            ->icon('heroicon-m-share')
            ->color('primary')
            ->visible(function (Get $get, ?NewsArticle $record): bool {
                $coverSource = $get('coverImage_source') ?? 'upload';
                if ($coverSource === 'url') {
                    return filled($get('coverImageUrl'));
                }

                $cover = $get('coverImage');
                if (! empty($cover)) {
                    return true;
                }

                return $record && filled($record->coverImage);
            })
            ->modalHeading(__('Social Media Share Preview'))
            ->modalDescription(__('Real-time simulation of how this article will appear across major social platforms.'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'))
            ->modalWidth('4xl')
            ->modalContent(function (Get $get, ?NewsArticle $record) {
                // 1. Resolve Cover Image URL (robust against Livewire uploads, stored paths, and external links)
                $coverSource = $get('coverImage_source') ?? 'upload';
                $coverUrl = '';

                if ($coverSource === 'url') {
                    $rawUrl = trim((string) $get('coverImageUrl'));
                    if (! empty($rawUrl) && filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                        $coverUrl = $rawUrl;
                    }
                } else {
                    $coverVal = $get('coverImage');

                    if (empty($coverVal) && $record) {
                        $coverVal = $record->coverImage;
                    }

                    if (is_array($coverVal)) {
                        $coverVal = reset($coverVal);
                    }

                    if (! empty($coverVal)) {
                        if (is_object($coverVal) && method_exists($coverVal, 'temporaryUrl')) {
                            try {
                                $coverUrl = $coverVal->temporaryUrl();
                            } catch (\Throwable $e) {
                                $coverUrl = '';
                            }
                        } elseif (is_string($coverVal)) {
                            if (filter_var($coverVal, FILTER_VALIDATE_URL)) {
                                $coverUrl = $coverVal;
                            } else {
                                $coverUrl = PublicStorage::urlIfExists($coverVal, '')
                                    ?: asset('storage/'.ltrim($coverVal, '/'));
                            }
                        }
                    }
                }

                // 2. Resolve Text Meta
                $title = trim((string) ($get('metaTitle_en') ?: $get('title_en') ?: $get('title_km') ?: __('Kimmex Announces New Milestone in Construction Excellence')));
                $excerpt = trim((string) ($get('metaDescription_en') ?: $get('excerpt_en') ?: $get('excerpt_km') ?: __('Discover how Kim Mex Construction & Investment continues to lead Cambodia infrastructure developments with quality engineering and modern technology.')));
                $slug = trim((string) ($get('slug') ?: 'kimmex-news-article'));
                $appUrl = rtrim(config('app.url', 'https://kimmex.com.kh'), '/');
                $articleUrl = $appUrl.'/news/'.$slug;

                $imageHtml = ! empty($coverUrl)
                    ? '<img src="'.e($coverUrl).'" alt="Social Cover" style="width: 100%; height: 100%; object-fit: cover; display: block;" />'
                    : '<div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #111827; color: #9ca3af; padding: 2rem; text-align: center;"><svg style="width: 2.5rem; height: 2.5rem; margin-bottom: 0.5rem; color: #6b7280; opacity: 0.6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span style="font-size: 0.75rem; font-weight: 500; color: #d1d5db;">'.e(__('No image uploaded yet. Upload a cover photo to preview.')).'</span></div>';

                return new HtmlString('
                    <div x-data="{ platform: \'facebook\', viewMode: \'desktop\' }" class="social-modal-root" style="display: flex; flex-direction: column; gap: 1rem; padding: 0.25rem 0;">
                        <style>
                            .social-modal-root {
                                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                                color: #1f2937;
                            }
                            .dark .social-modal-root { color: #f3f4f6; }
                            .social-controls-bar {
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                justify-content: space-between;
                                gap: 0.625rem;
                                padding: 0.375rem;
                                background-color: #f3f4f6;
                                border-radius: 0.75rem;
                                border: 1px solid #e5e7eb;
                                flex-wrap: wrap;
                            }
                            .dark .social-controls-bar {
                                background-color: #1f2937;
                                border-color: #374151;
                            }
                            .social-tab-btn {
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                padding: 6px 12px;
                                border-radius: 8px;
                                font-size: 12px;
                                font-weight: 600;
                                cursor: pointer;
                                border: 1px solid transparent;
                                transition: all 0.15s ease;
                                white-space: nowrap;
                                background: transparent;
                                color: #4b5563;
                            }
                            .dark .social-tab-btn { color: #9ca3af; }
                            .social-tab-btn.is-active {
                                background: #ffffff;
                                color: #2563eb;
                                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                border-color: #e5e7eb;
                                font-weight: 700;
                            }
                            .dark .social-tab-btn.is-active {
                                background: #111827;
                                color: #60a5fa;
                                border-color: #374151;
                            }
                            .social-view-btn {
                                display: inline-flex;
                                align-items: center;
                                gap: 4px;
                                padding: 4px 10px;
                                border-radius: 6px;
                                font-size: 11px;
                                font-weight: 600;
                                cursor: pointer;
                                border: none;
                                background: transparent;
                                color: #6b7280;
                                transition: all 0.15s ease;
                            }
                            .dark .social-view-btn { color: #9ca3af; }
                            .social-view-btn.is-active {
                                background: #ffffff;
                                color: #111827;
                                font-weight: 700;
                                box-shadow: 0 1px 2px rgba(0,0,0,0.08);
                            }
                            .dark .social-view-btn.is-active {
                                background: #374151;
                                color: #ffffff;
                            }
                            .social-card-img-box {
                                width: 100%;
                                aspect-ratio: 16 / 9;
                                background-color: #000000;
                                position: relative;
                                overflow: hidden;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }
                            .social-card-img-box img {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                display: block;
                            }
                            .social-card-fb {
                                background: #ffffff;
                                border: 1px solid #e5e7eb;
                                border-radius: 16px;
                                overflow: hidden;
                                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
                                transition: all 0.2s ease;
                                margin: 0 auto;
                            }
                            .dark .social-card-fb {
                                background: #242526;
                                border-color: #3e4042;
                                color: #e4e6eb;
                            }
                            .social-card-li {
                                background: #ffffff;
                                border: 1px solid #e5e7eb;
                                border-radius: 16px;
                                overflow: hidden;
                                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
                                transition: all 0.2s ease;
                                margin: 0 auto;
                            }
                            .dark .social-card-li {
                                background: #1b1f23;
                                border-color: #38434f;
                                color: #f3f2ef;
                            }
                            .social-card-tg {
                                background: #eef2f5;
                                border: 1px solid #e5e7eb;
                                border-radius: 16px;
                                padding: 16px;
                                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
                                transition: all 0.2s ease;
                                margin: 0 auto;
                            }
                            .dark .social-card-tg {
                                background: #0e1621;
                                border-color: #242f3d;
                            }
                            .social-card-x {
                                background: #ffffff;
                                border: 1px solid #e5e7eb;
                                border-radius: 16px;
                                padding: 16px;
                                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
                                transition: all 0.2s ease;
                                margin: 0 auto;
                            }
                            .dark .social-card-x {
                                background: #000000;
                                border-color: #2f3336;
                                color: #e7e9ea;
                            }
                            .social-card-google {
                                background: #ffffff;
                                border: 1px solid #e5e7eb;
                                border-radius: 16px;
                                padding: 20px;
                                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
                                transition: all 0.2s ease;
                                margin: 0 auto;
                            }
                            .dark .social-card-google {
                                background: #202124;
                                border-color: #3c4043;
                                color: #bdc1c6;
                            }
                            .social-clamp-2 {
                                display: -webkit-box;
                                -webkit-line-clamp: 2;
                                -webkit-box-orient: vertical;
                                overflow: hidden;
                            }
                            .social-clamp-3 {
                                display: -webkit-box;
                                -webkit-line-clamp: 3;
                                -webkit-box-orient: vertical;
                                overflow: hidden;
                            }
                        </style>

                        <!-- TOP CONTROLS: PLATFORMS + VIEWPORT SWITCHER -->
                        <div class="social-controls-bar">
                            <!-- Platform Tabs -->
                            <div style="display: flex; align-items: center; gap: 4px; overflow-x: auto;">
                                <button type="button" 
                                        @click="platform = \'facebook\'"
                                        :class="platform === \'facebook\' ? \'is-active\' : \'\'"
                                        class="social-tab-btn">
                                    <span style="width: 10px; height: 10px; border-radius: 9999px; background-color: #2563eb; display: inline-block;"></span>
                                    Facebook
                                </button>
                                <button type="button" 
                                        @click="platform = \'linkedin\'"
                                        :class="platform === \'linkedin\' ? \'is-active\' : \'\'"
                                        class="social-tab-btn">
                                    <span style="width: 10px; height: 10px; border-radius: 9999px; background-color: #0a66c2; display: inline-block;"></span>
                                    LinkedIn
                                </button>
                                <button type="button" 
                                        @click="platform = \'telegram\'"
                                        :class="platform === \'telegram\' ? \'is-active\' : \'\'"
                                        class="social-tab-btn">
                                    <span style="width: 10px; height: 10px; border-radius: 9999px; background-color: #229ed9; display: inline-block;"></span>
                                    Telegram
                                </button>
                                <button type="button" 
                                        @click="platform = \'twitter\'"
                                        :class="platform === \'twitter\' ? \'is-active\' : \'\'"
                                        class="social-tab-btn">
                                    <span style="width: 10px; height: 10px; border-radius: 9999px; background-color: #000000; display: inline-block;"></span>
                                    X (Twitter)
                                </button>
                                <button type="button" 
                                        @click="platform = \'google\'"
                                        :class="platform === \'google\' ? \'is-active\' : \'\'"
                                        class="social-tab-btn">
                                    <span style="width: 10px; height: 10px; border-radius: 9999px; background-color: #10b981; display: inline-block;"></span>
                                    Google Search
                                </button>
                            </div>

                            <!-- Actual Size Switcher (Desktop 550px vs Mobile 375px) -->
                            <div style="display: flex; align-items: center; gap: 4px; padding: 2px; background-color: rgba(229, 231, 235, 0.8); border-radius: 8px;">
                                <button type="button"
                                        @click="viewMode = \'desktop\'"
                                        :class="viewMode === \'desktop\' ? \'is-active\' : \'\'"
                                        class="social-view-btn">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span>Desktop (550px)</span>
                                </button>
                                <button type="button"
                                        @click="viewMode = \'mobile\'"
                                        :class="viewMode === \'mobile\' ? \'is-active\' : \'\'"
                                        class="social-view-btn">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span>Mobile (375px)</span>
                                </button>
                            </div>
                        </div>

                        <!-- CANVAS AREA (Actual 1:1 Size Preview Container) -->
                        <div style="padding: 1.25rem 0.75rem; background-color: rgba(249, 250, 251, 0.8); border-radius: 1rem; border: 1px dashed #d1d5db; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                            
                            <!-- 1. FACEBOOK POST PREVIEW -->
                            <div x-show="platform === \'facebook\'" x-cloak 
                                 :style="viewMode === \'desktop\' ? \'max-width: 550px; width: 100%;\' : \'max-width: 375px; width: 100%; box-shadow: 0 0 0 8px rgba(0,0,0,0.06); border-radius: 24px;\'"
                                 class="social-card-fb">
                                <!-- Actual Dimension Header -->
                                <div style="padding: 6px 14px; background-color: #eff6ff; border-bottom: 1px solid #dbeafe; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #1d4ed8; font-family: monospace;">
                                    <span style="font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                        <span style="width: 8px; height: 8px; border-radius: 9999px; background-color: #2563eb; display: inline-block;"></span>
                                        Facebook Feed Card
                                    </span>
                                    <span x-text="viewMode === \'desktop\' ? \'Width: 550px (Desktop Scale)\' : \'Width: 375px (Mobile Scale)\'"></span>
                                </div>
                                <!-- FB Header -->
                                <div style="padding: 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f3f4f6;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 40px; height: 40px; border-radius: 9999px; background-color: #dc2626; color: white; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                            KM
                                        </div>
                                        <div style="line-height: 1.25;">
                                            <div style="display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 700;">
                                                <span>Kim Mex Construction & Investment Co., Ltd.</span>
                                                <svg style="width: 14px; height: 14px; color: #3b82f6; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            </div>
                                            <div style="font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                                <span>Just now</span> &bull; <span title="Public">🌐</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="color: #9ca3af; font-weight: 700; font-size: 18px; line-height: 1; padding: 0 4px;">&bull;&bull;&bull;</div>
                                </div>
                                <!-- FB Caption -->
                                <div style="padding: 14px; font-size: 14px; line-height: 1.5;">
                                    '.e($excerpt).'
                                </div>
                                <!-- FB Link Card -->
                                <div style="border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">
                                    <div class="social-card-img-box">
                                        '.$imageHtml.'
                                        <div style="position: absolute; bottom: 8px; right: 8px; padding: 2px 6px; border-radius: 4px; background-color: rgba(0,0,0,0.75); font-size: 10px; font-family: monospace; color: #ffffff; font-weight: 700;">
                                            16:9 (1200 &times; 675)
                                        </div>
                                    </div>
                                    <div style="padding: 14px; display: flex; flex-direction: column; gap: 4px;">
                                        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">
                                            KIMMEX.COM.KH
                                        </div>
                                        <div style="font-size: 15px; font-weight: 700; line-height: 1.35;">
                                            '.e($title).'
                                        </div>
                                        <div class="social-clamp-2" style="font-size: 12px; color: #6b7280; line-height: 1.5;">
                                            '.e($excerpt).'
                                        </div>
                                    </div>
                                </div>
                                <!-- FB Actions Footer -->
                                <div style="padding: 8px 16px; border-top: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #6b7280;">
                                    <div>👍 ❤️ 84</div>
                                    <div>12 comments &bull; 6 shares</div>
                                </div>
                                <div style="padding: 4px 8px; border-top: 1px solid #f3f4f6; display: grid; grid-template-columns: repeat(3, 1fr); text-align: center; font-size: 12px; font-weight: 600; color: #4b5563;">
                                    <div style="padding: 8px; border-radius: 8px;">👍 Like</div>
                                    <div style="padding: 8px; border-radius: 8px;">💬 Comment</div>
                                    <div style="padding: 8px; border-radius: 8px;">↗ Share</div>
                                </div>
                            </div>

                            <!-- 2. LINKEDIN POST PREVIEW -->
                            <div x-show="platform === \'linkedin\'" x-cloak 
                                 :style="viewMode === \'desktop\' ? \'max-width: 552px; width: 100%;\' : \'max-width: 375px; width: 100%; box-shadow: 0 0 0 8px rgba(0,0,0,0.06); border-radius: 24px;\'"
                                 class="social-card-li">
                                <!-- Actual Dimension Header -->
                                <div style="padding: 6px 14px; background-color: #f0f9ff; border-bottom: 1px solid #e0f2fe; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #0369a1; font-family: monospace;">
                                    <span style="font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                        <span style="width: 8px; height: 8px; border-radius: 9999px; background-color: #0a66c2; display: inline-block;"></span>
                                        LinkedIn Feed Post
                                    </span>
                                    <span x-text="viewMode === \'desktop\' ? \'Width: 552px (Desktop Scale)\' : \'Width: 375px (Mobile Scale)\'"></span>
                                </div>
                                <!-- LI Header -->
                                <div style="padding: 14px; display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 44px; height: 44px; border-radius: 8px; background-color: #0b1f3a; color: white; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                        KM
                                    </div>
                                    <div style="line-height: 1.25;">
                                        <div style="font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                            <span>Kim Mex Construction & Investment</span>
                                        </div>
                                        <div style="font-size: 11px; color: #6b7280;">
                                            Civil Engineering & Building Construction &bull; 1,480 followers
                                        </div>
                                        <div style="font-size: 10px; color: #9ca3af; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                            <span>1h</span> &bull; <span>🌐</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- LI Caption -->
                                <div style="padding: 0 14px 12px 14px; font-size: 14px; line-height: 1.5;">
                                    '.e($excerpt).'
                                </div>
                                <!-- LI Card -->
                                <div style="border: 1px solid #e5e7eb; border-radius: 12px; margin: 0 14px 14px 14px; overflow: hidden; background-color: #f9fafb;">
                                    <div class="social-card-img-box">
                                        '.$imageHtml.'
                                        <div style="position: absolute; bottom: 8px; right: 8px; padding: 2px 6px; border-radius: 4px; background-color: rgba(0,0,0,0.75); font-size: 10px; font-family: monospace; color: #ffffff; font-weight: 700;">
                                            16:9 Standard
                                        </div>
                                    </div>
                                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 2px;">
                                        <div style="font-size: 14px; font-weight: 700; line-height: 1.35;">
                                            '.e($title).'
                                        </div>
                                        <div style="font-size: 12px; color: #6b7280;">
                                            kimmex.com.kh &bull; 3 min read
                                        </div>
                                    </div>
                                </div>
                                <!-- LI Actions -->
                                <div style="padding: 6px 12px; border-top: 1px solid #f3f4f6; display: grid; grid-template-columns: repeat(4, 1fr); text-align: center; font-size: 12px; font-weight: 600; color: #4b5563;">
                                    <div style="padding: 8px; border-radius: 8px;">👍 Like</div>
                                    <div style="padding: 8px; border-radius: 8px;">💬 Comment</div>
                                    <div style="padding: 8px; border-radius: 8px;">🔁 Repost</div>
                                    <div style="padding: 8px; border-radius: 8px;">✈️ Send</div>
                                </div>
                            </div>

                            <!-- 3. TELEGRAM CHAT PREVIEW -->
                            <div x-show="platform === \'telegram\'" x-cloak 
                                 :style="viewMode === \'desktop\' ? \'max-width: 420px; width: 100%;\' : \'max-width: 340px; width: 100%;\'"
                                 class="social-card-tg">
                                <div style="font-size: 11px; font-family: monospace; color: #6b7280; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-weight: 700; color: #229ed9;">Telegram Chat Bubble</span>
                                    <span x-text="viewMode === \'desktop\' ? \'Bubble: 420px\' : \'Bubble: 340px\'"></span>
                                </div>
                                <div style="background-color: #ffffff; border-radius: 16px; padding: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 8px;">
                                    <div style="border-left: 3px solid #229ed9; padding-left: 10px; display: flex; flex-direction: column; gap: 4px;">
                                        <div style="font-size: 12px; font-weight: 700; color: #229ed9;">
                                            Kim Mex Construction
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; line-height: 1.25;">
                                            '.e($title).'
                                        </div>
                                        <div class="social-clamp-3" style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                                            '.e($excerpt).'
                                        </div>
                                    </div>
                                    <div class="social-card-img-box" style="border-radius: 12px;">
                                        '.$imageHtml.'
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; font-size: 10px; color: #9ca3af; font-family: monospace; padding-top: 4px;">
                                        <span>'.date('H:i').'</span>
                                        <span style="color: #229ed9; font-weight: 700;">✓✓</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. X (TWITTER) POST PREVIEW -->
                            <div x-show="platform === \'twitter\'" x-cloak 
                                 :style="viewMode === \'desktop\' ? \'max-width: 504px; width: 100%;\' : \'max-width: 375px; width: 100%; box-shadow: 0 0 0 8px rgba(0,0,0,0.06); border-radius: 24px;\'"
                                 class="social-card-x">
                                <div style="font-size: 11px; font-family: monospace; color: #6b7280; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f3f4f6; padding-bottom: 6px;">
                                    <span style="font-weight: 700; color: #1f2937;">X (Twitter) Large Summary Card</span>
                                    <span x-text="viewMode === \'desktop\' ? \'Width: 504px\' : \'Width: 375px\'"></span>
                                </div>
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 9999px; background-color: #dc2626; color: white; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                        KM
                                    </div>
                                    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 8px;">
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280;">
                                            <span style="font-weight: 700; font-size: 14px; color: #111827;">Kim Mex Construction</span>
                                            <span>@kimmex_kh</span>
                                            <span>&bull;</span>
                                            <span>Just now</span>
                                        </div>
                                        <div style="font-size: 14px; line-height: 1.5;">
                                            '.e($title).'
                                        </div>
                                        <div style="border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb; background-color: #f9fafb;">
                                            <div class="social-card-img-box">
                                                '.$imageHtml.'
                                            </div>
                                            <div style="padding: 12px;">
                                                <div style="font-size: 11px; color: #6b7280;">kimmex.com.kh</div>
                                                <div style="font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.e($title).'</div>
                                                <div style="font-size: 12px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.e($excerpt).'</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. GOOGLE SEARCH RESULT PREVIEW -->
                            <div x-show="platform === \'google\'" x-cloak 
                                 :style="viewMode === \'desktop\' ? \'max-width: 600px; width: 100%;\' : \'max-width: 375px; width: 100%; box-shadow: 0 0 0 8px rgba(0,0,0,0.06); border-radius: 24px;\'"
                                 class="social-card-google">
                                <div style="font-size: 11px; font-family: monospace; color: #9ca3af; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f3f4f6; padding-bottom: 6px;">
                                    <span style="font-weight: 700; color: #059669;">Google SERP Snippet</span>
                                    <span x-text="viewMode === \'desktop\' ? \'Width: 600px (Desktop Scale)\' : \'Width: 375px (Mobile Scale)\'"></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #374151; margin-bottom: 6px;">
                                    <div style="width: 24px; height: 24px; border-radius: 9999px; background-color: #dc2626; color: white; font-size: 10px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        KM
                                    </div>
                                    <div style="line-height: 1.25; min-width: 0;">
                                        <div style="font-weight: 600; color: #111827;">Kim Mex Construction</div>
                                        <div style="font-size: 11px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.e($articleUrl).'</div>
                                    </div>
                                </div>
                                <div style="color: #1a0dab; font-size: 18px; font-weight: 500; line-height: 1.35; cursor: pointer; margin-bottom: 6px;">
                                    '.e($title).' - KIMMEX
                                </div>
                                <div style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                                    <span style="color: #9ca3af; font-weight: 500;">'.date('M d, Y').' &mdash; </span>
                                    '.e($excerpt).'
                                </div>
                            </div>
                        </div>

                        <!-- HELPER FOOTER WITH EXACT SPECS -->
                        <div style="padding: 14px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; font-size: 12px; color: #78350f; display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; flex-shrink: 0; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>'.e(__('Scale: Displays standard 1:1 pixel width. Edit SEO Meta in Content tab for custom text.')).'</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; font-family: monospace; font-size: 11px; color: #b45309; flex-shrink: 0; font-weight: 700;">
                                <span>Image: 1200 &times; 675 px (16:9)</span>
                            </div>
                        </div>
                    </div>
                ');
            });
    }
}
