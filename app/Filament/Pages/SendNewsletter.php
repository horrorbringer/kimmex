<?php

namespace App\Filament\Pages;

use App\Jobs\SendNewsletterJob;
use App\Models\NewsArticle;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SendNewsletter extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.send-newsletter';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-paper-airplane';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Communications');
    }

    public static function getNavigationLabel(): string
    {
        return __('Send Newsletter');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public ?string $articleId = null;
    public string $customIntro = '';

    // Preview state
    public ?array $previewData = null;
    public bool $showPreview = false;
    public bool $alreadySent = false;
    public ?string $lastSentInfo = null;

    public function getFormSchema(): array
    {
        return [
            Select::make('articleId')
                ->label(__('Select Article'))
                ->options(
                    NewsArticle::query()
                        ->whereNotNull('publishedAt')
                        ->orderByDesc('publishedAt')
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($a) => [$a->id => $a->getTranslation('title', 'en') . ' (' . ($a->publishedAt?->format('M d') ?? '') . ')'])
                )
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadPreview())
                ->helperText(__('Choose which article to send to all active subscribers.')),

            TextInput::make('customIntro')
                ->label(__('Custom Intro (optional)'))
                ->placeholder(__('e.g. Check out our latest project update!'))
                ->helperText(__('This appears above the article in the email. Leave blank for default.')),
        ];
    }

    public function loadPreview(): void
    {
        $this->showPreview = false;
        $this->previewData = null;
        $this->alreadySent = false;
        $this->lastSentInfo = null;

        if (!$this->articleId) {
            return;
        }

        $article = NewsArticle::find($this->articleId);
        if (!$article) {
            return;
        }

        // Check if already sent
        $previousSend = NewsletterSend::where('article_id', $this->articleId)
            ->whereIn('status', ['completed', 'sending'])
            ->latest('sent_at')
            ->first();

        if ($previousSend) {
            $this->alreadySent = true;
            $this->lastSentInfo = __('This article was already sent on :date to :count subscribers.', [
                'date' => $previousSend->sent_at?->format('M d, Y H:i') ?? $previousSend->created_at->format('M d, Y H:i'),
                'count' => $previousSend->subscriber_count,
            ]);
        }

        $this->previewData = [
            'title' => $article->getTranslation('title', 'en'),
            'excerpt' => $article->getTranslation('excerpt', 'en'),
            'coverImage' => $article->coverImage,
            'publishedAt' => $article->publishedAt?->format('M d, Y'),
            'category' => $article->getTranslation('category', 'en'),
        ];

        $this->showPreview = true;
    }

    public function send(): void
    {
        if (!$this->articleId) {
            Notification::make()->danger()->title(__('Please select an article.'))->send();
            return;
        }

        $article = NewsArticle::find($this->articleId);
        if (!$article) {
            Notification::make()->danger()->title(__('Article not found.'))->send();
            return;
        }

        $subscribers = Subscriber::active()->get();

        if ($subscribers->isEmpty()) {
            Notification::make()->warning()->title(__('No active subscribers found.'))->send();
            return;
        }

        // Create the newsletter send record
        $newsletterSend = NewsletterSend::create([
            'article_id' => $article->id,
            'sent_by' => auth()->id(),
            'custom_intro' => $this->customIntro ?: null,
            'subscriber_count' => $subscribers->count(),
            'status' => 'pending',
        ]);

        // Dispatch individual jobs per subscriber (queued)
        $newsletterSend->markSending();

        foreach ($subscribers as $subscriber) {
            SendNewsletterJob::dispatch($newsletterSend, $subscriber, $article, $this->customIntro);
        }

        Notification::make()
            ->success()
            ->title(__('Newsletter queued!'))
            ->body(__(':count emails queued for delivery.', ['count' => $subscribers->count()]))
            ->send();

        // Reset form
        $this->articleId = null;
        $this->customIntro = '';
        $this->showPreview = false;
        $this->previewData = null;
        $this->alreadySent = false;
        $this->lastSentInfo = null;
    }

    public function forceSend(): void
    {
        $this->send();
    }

    public function getRecentSendsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return NewsletterSend::with('article', 'sender')
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    public function getActiveSubscriberCountProperty(): int
    {
        return Subscriber::active()->count();
    }
}
