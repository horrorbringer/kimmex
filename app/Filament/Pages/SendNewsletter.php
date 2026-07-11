<?php

namespace App\Filament\Pages;

use App\Mail\NewsAnnouncementMail;
use App\Models\NewsArticle;
use App\Models\Subscriber;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

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
                ->helperText(__('Choose which article to send to all active subscribers.')),

            TextInput::make('customIntro')
                ->label(__('Custom Intro (optional)'))
                ->placeholder(__('e.g. Check out our latest project update!'))
                ->helperText(__('This appears above the article in the email. Leave blank for default.')),
        ];
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

        $count = 0;
        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new NewsAnnouncementMail($article, $subscriber, $this->customIntro));
                $count++;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Newsletter send failed', [
                    'subscriber' => $subscriber->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Notification::make()
            ->success()
            ->title(__('Newsletter sent!'))
            ->body(__(':count emails sent successfully.', ['count' => $count]))
            ->send();

        $this->articleId = null;
        $this->customIntro = '';
    }
}
