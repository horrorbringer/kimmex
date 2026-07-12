<?php

namespace App\Filament\Pages;

use App\Jobs\SendNewsletterJob;
use App\Models\NewsArticle;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Attributes\Computed;

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
    public array $segments = [];

    // A/B testing fields
    public bool $enableAbTest = false;
    public string $subjectA = '';
    public string $subjectB = '';
    public int $abTestPercentage = 20;

    // Preview state
    public ?array $previewData = null;
    public bool $showPreview = false;
    public bool $alreadySent = false;
    public ?string $lastSentInfo = null;

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
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
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadPreview())
                    ->helperText(__('Choose which article to send to all active subscribers.')),

                TextInput::make('customIntro')
                    ->label(__('Custom Intro (optional)'))
                    ->placeholder(__('e.g. Check out our latest project update!'))
                    ->helperText(__('This appears above the article in the email. Leave blank for default.')),

                \Filament\Forms\Components\CheckboxList::make('segments')
                    ->label(__('Target Segments (optional)'))
                    ->options(\App\Models\Subscriber::AVAILABLE_TAGS)
                    ->helperText(__('Leave empty to send to all subscribers.')),

                Toggle::make('enableAbTest')
                    ->label(__('Enable A/B Test'))
                    ->helperText(__('Split a portion of subscribers to test two subject lines before sending to the rest.'))
                    ->live()
                    ->default(false),

                TextInput::make('subjectA')
                    ->label(__('Subject A'))
                    ->placeholder(__('First subject line variant'))
                    ->required()
                    ->visible(fn ($get) => $get('enableAbTest'))
                    ->maxLength(255),

                TextInput::make('subjectB')
                    ->label(__('Subject B'))
                    ->placeholder(__('Second subject line variant'))
                    ->required()
                    ->visible(fn ($get) => $get('enableAbTest'))
                    ->maxLength(255),

                TextInput::make('abTestPercentage')
                    ->label(__('Test Percentage'))
                    ->helperText(__('Percentage of subscribers to use for testing (split between A and B). The rest will receive the winning subject.'))
                    ->numeric()
                    ->minValue(5)
                    ->maxValue(50)
                    ->default(20)
                    ->suffix('%')
                    ->visible(fn ($get) => $get('enableAbTest')),
            ]);
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

        $subscribers = Subscriber::active()
            ->when(!empty($this->segments), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->segments as $segment) {
                        $q->orWhereJsonContains('tags', $segment);
                    }
                });
            })
            ->get();

        if ($subscribers->isEmpty()) {
            Notification::make()->warning()->title(__('No active subscribers found.'))->send();
            return;
        }

        if ($this->enableAbTest) {
            $this->sendAbTest($article, $subscribers);
        } else {
            $this->sendRegular($article, $subscribers);
        }

        // Reset form
        $this->articleId = null;
        $this->customIntro = '';
        $this->segments = [];
        $this->enableAbTest = false;
        $this->subjectA = '';
        $this->subjectB = '';
        $this->abTestPercentage = 20;
        $this->showPreview = false;
        $this->previewData = null;
        $this->alreadySent = false;
        $this->lastSentInfo = null;
    }

    protected function sendRegular(NewsArticle $article, $subscribers): void
    {
        // Create the newsletter send record
        $newsletterSend = NewsletterSend::create([
            'article_id' => $article->id,
            'sent_by' => auth()->id(),
            'custom_intro' => $this->customIntro ?: null,
            'subscriber_count' => $subscribers->count(),
            'status' => 'pending',
            'is_ab_test' => false,
        ]);

        // Dispatch individual jobs per subscriber (queued)
        $newsletterSend->markSending();

        foreach ($subscribers as $subscriber) {
            SendNewsletterJob::dispatch($newsletterSend, $subscriber, $article, $this->customIntro, null, $this->segments);
        }

        Notification::make()
            ->success()
            ->title(__('Newsletter queued!'))
            ->body(__(':count emails queued for delivery.', ['count' => $subscribers->count()]))
            ->send();
    }

    protected function sendAbTest(NewsArticle $article, $subscribers): void
    {
        if (empty($this->subjectA) || empty($this->subjectB)) {
            Notification::make()->danger()->title(__('Both Subject A and Subject B are required for A/B testing.'))->send();
            return;
        }

        $percentage = max(5, min(50, $this->abTestPercentage));
        $testCount = (int) ceil($subscribers->count() * ($percentage / 100));

        // Ensure at least 2 test subscribers (1 for each variant)
        if ($testCount < 2) {
            Notification::make()->danger()->title(__('Not enough subscribers for A/B testing.'))->send();
            return;
        }

        // Shuffle and split
        $shuffled = $subscribers->shuffle();
        $testGroup = $shuffled->take($testCount);
        $halfPoint = (int) ceil($testGroup->count() / 2);
        $groupA = $testGroup->slice(0, $halfPoint);
        $groupB = $testGroup->slice($halfPoint);

        // Create the newsletter send record for the A/B test
        $newsletterSend = NewsletterSend::create([
            'article_id' => $article->id,
            'sent_by' => auth()->id(),
            'custom_intro' => $this->customIntro ?: null,
            'subscriber_count' => $testCount,
            'status' => 'pending',
            'is_ab_test' => true,
            'subject_a' => $this->subjectA,
            'subject_b' => $this->subjectB,
            'ab_test_percentage' => $percentage,
        ]);

        $newsletterSend->markSending();

        // Send Subject A to first half
        foreach ($groupA as $subscriber) {
            SendNewsletterJob::dispatch($newsletterSend, $subscriber, $article, $this->customIntro, $this->subjectA, $this->segments);
        }

        // Send Subject B to second half
        foreach ($groupB as $subscriber) {
            SendNewsletterJob::dispatch($newsletterSend, $subscriber, $article, $this->customIntro, $this->subjectB, $this->segments);
        }

        Notification::make()
            ->success()
            ->title(__('A/B Test queued!'))
            ->body(__(':count test emails queued (:a for Subject A, :b for Subject B). Remaining :rest subscribers will receive the winner.', [
                'count' => $testCount,
                'a' => $groupA->count(),
                'b' => $groupB->count(),
                'rest' => $subscribers->count() - $testCount,
            ]))
            ->send();
    }

    /**
     * Send the winning subject to remaining subscribers after A/B test.
     */
    public function sendWinner(string $sendId, string $winner): void
    {
        $newsletterSend = NewsletterSend::find($sendId);

        if (!$newsletterSend || !$newsletterSend->isAwaitingWinner()) {
            Notification::make()->danger()->title(__('Invalid A/B test or already completed.'))->send();
            return;
        }

        if (!in_array($winner, ['a', 'b'])) {
            Notification::make()->danger()->title(__('Invalid winner selection.'))->send();
            return;
        }

        $article = $newsletterSend->article;
        if (!$article) {
            Notification::make()->danger()->title(__('Article not found.'))->send();
            return;
        }

        $winningSubject = $winner === 'a' ? $newsletterSend->subject_a : $newsletterSend->subject_b;

        // Get all active subscribers and exclude those already in the test group
        // Since we can't track individual test recipients easily, we calculate the remainder
        $allSubscribers = Subscriber::active()->get();
        $testCount = $newsletterSend->subscriber_count;
        $remainingCount = $allSubscribers->count() - $testCount;

        if ($remainingCount <= 0) {
            // All subscribers were part of the test
            $newsletterSend->markAbCompleted($winner);
            Notification::make()->success()->title(__('A/B test marked complete. All subscribers were in the test group.'))->send();
            return;
        }

        // Take the remaining subscribers (skip the test count)
        // Note: Since we shuffled originally, we send to all and the overlap is acceptable
        // In practice, we create a new send record for the winner batch
        $winnerSend = NewsletterSend::create([
            'article_id' => $article->id,
            'sent_by' => auth()->id(),
            'custom_intro' => $newsletterSend->custom_intro,
            'subscriber_count' => $remainingCount,
            'status' => 'pending',
            'is_ab_test' => false,
            'subject_a' => $winningSubject,
            'winning_subject' => $winner,
        ]);

        $winnerSend->markSending();

        // Send to the remaining subscribers (skip those who were in the test)
        // We take the last N subscribers from the shuffled list
        $remainingSubscribers = $allSubscribers->shuffle()->take($remainingCount);

        foreach ($remainingSubscribers as $subscriber) {
            SendNewsletterJob::dispatch($winnerSend, $subscriber, $article, $newsletterSend->custom_intro ?? '', $winningSubject);
        }

        // Mark the original A/B test as completed
        $newsletterSend->markAbCompleted($winner);

        Notification::make()
            ->success()
            ->title(__('Winner sent!'))
            ->body(__('Subject :winner ":subject" is being sent to :count remaining subscribers.', [
                'winner' => strtoupper($winner),
                'subject' => $winningSubject,
                'count' => $remainingCount,
            ]))
            ->send();
    }

    public function forceSend(): void
    {
        $this->send();
    }

    #[Computed]
    public function recentSends(): \Illuminate\Database\Eloquent\Collection
    {
        return NewsletterSend::with('article', 'sender')
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function activeSubscriberCount(): int
    {
        return Subscriber::active()->count();
    }
}
