<?php

namespace App\Filament\Resources\NewsArticles\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\OptimizedFileUpload;
use App\Filament\Support\TranslationHelper;
use App\Models\Employee;
use App\Models\NewsCategory;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
                                                    ->columns(2)
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
                                                            ->helperText(__('Auto-generated from title. Click ✏️ to edit manually.'))
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
                                                    ->components([
                                                        Textarea::make('excerpt_en')
                                                            ->label(__('Excerpt / Summary (English)'))
                                                            ->placeholder('Brief overview of this news article for cards and previews...')
                                                            ->hintActions([
                                                                AIHelper::getImproveAction('excerpt_en', 'Make this news summary more engaging and impactful.'),
                                                                AIHelper::getTranslateAction('excerpt_en', 'excerpt_km', 'Khmer', 'km', 'en'),
                                                            ])
                                                            ->rows(2)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('metaDescription_en')) {
                                                                    $set('metaDescription_en', $state);
                                                                }
                                                            }),

                                                        RichEditor::make('content_en')->resizableImages()
                                                            ->label(__('Content (English)'))
                                                            ->required()
                                                            ->toolbarButtons([
                                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                                [ToolbarButtonGroup::make('Heading', ['h2', 'h3', 'h4'])->textualButtons()],
                                                                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                                                ['blockquote', 'bulletList', 'orderedList', 'table'],
                                                                ['attachFiles', 'horizontalRule'],
                                                                ['undo', 'redo'],
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->fileAttachmentsDirectory('news/content')
                                                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                                            ->hintActions([
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
                                                    ->components([
                                                        Textarea::make('excerpt_km')
                                                            ->label(__('សេចក្តីសង្ខេប (Khmer Excerpt)'))
                                                            ->placeholder('បញ្ចូលសេចក្តីសង្ខេបព័ត៌មានជាភាសាខ្មែរ...')
                                                            ->hintActions([
                                                                AIHelper::getTranslateAction('excerpt_km', 'excerpt_en', 'English', 'en', 'km'),
                                                                AIHelper::getImproveAction('excerpt_km', 'កែលម្អសេចក្តីសង្ខេបនេះឱ្យកាន់តែច្បាស់លាស់'),
                                                            ])
                                                            ->rows(2)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                                                if (! $get('metaDescription_km')) {
                                                                    $set('metaDescription_km', $state);
                                                                }
                                                            }),

                                                        RichEditor::make('content_km')->resizableImages()
                                                            ->label(__('ខ្លឹមសារព័ត៌មាន (Khmer Content)'))
                                                            ->toolbarButtons([
                                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                                [ToolbarButtonGroup::make('Heading', ['h2', 'h3', 'h4'])->textualButtons()],
                                                                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                                                ['blockquote', 'bulletList', 'orderedList', 'table'],
                                                                ['attachFiles', 'horizontalRule'],
                                                                ['undo', 'redo'],
                                                            ])
                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                            ->fileAttachmentsVisibility('public')
                                                            ->fileAttachmentsDirectory('news/content')
                                                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                                            ->hintActions([
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
                                    ->components([
                                        Radio::make('coverImage_source')
                                            ->label(__('Cover Image Source'))
                                            ->options([
                                                'upload' => __('📁 Upload Image File'),
                                                'url' => __('🔗 External Image URL'),
                                            ])
                                            ->default('upload')
                                            ->inline()
                                            ->live(),

                                        OptimizedFileUpload::hero('coverImage')
                                            ->directory('news/covers')
                                            ->label(__('Cover Image File'))
                                            ->visible(fn (Get $get) => ($get('coverImage_source') ?? 'upload') === 'upload'),

                                        TextInput::make('coverImageUrl')
                                            ->label(__('External Cover Image URL'))
                                            ->placeholder('https://images.unsplash.com/... or https://example.com/image.jpg')
                                            ->helperText(__('Enter direct image link (e.g., https://... or http://...)'))
                                            ->url()
                                            ->live(onBlur: true)
                                            ->visible(fn (Get $get) => $get('coverImage_source') === 'url'),
                                    ]),

                                Section::make(__('Gallery'))
                                    ->description(__('Upload files or add external image links (max 12)'))
                                    ->collapsible()
                                    ->components([
                                        Radio::make('gallery_source')
                                            ->label(__('Gallery Source'))
                                            ->options([
                                                'upload' => __('📁 Upload Files'),
                                                'urls' => __('🔗 External Image URLs'),
                                                'both' => __('⚡ Both (Uploads + URLs)'),
                                            ])
                                            ->default('upload')
                                            ->inline()
                                            ->live(),

                                        FileUpload::make('gallery')
                                            ->label(__('Gallery Images (Upload)'))
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
                                            ->placeholder(__('Paste image URL and press Enter...'))
                                            ->helperText(__('Add direct image links (https://...)'))
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
                                            ->helperText(__('Paste a YouTube or Vimeo link.')),
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

                                Section::make(__('Author'))
                                    ->columns(3)
                                    ->components([
                                        Select::make('authorId')
                                            ->label(__('Author (Employee)'))
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
                                            ->suffixAction(AIHelper::getTranslateAction('authorName_en', 'authorName_km', 'Khmer', 'km', 'en'))
                                            ->default(auth()->user()?->name),
                                        TextInput::make('authorName_km')
                                            ->label(__('Author Name (Khmer)'))
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
}
