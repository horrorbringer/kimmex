<?php

namespace App\Jobs;

use App\Mail\NewsAnnouncementMail;
use App\Models\NewsArticle;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly NewsletterSend $newsletterSend,
        public readonly Subscriber $subscriber,
        public readonly NewsArticle $article,
        public readonly string $customIntro = '',
        public readonly ?string $subjectOverride = null,
        public readonly array $segments = [],
    ) {}

    public function handle(): void
    {
        // If segments were specified, verify subscriber still matches before sending
        if (!empty($this->segments)) {
            $subscriberTags = $this->subscriber->tags ?? [];
            $hasMatchingTag = !empty(array_intersect($this->segments, $subscriberTags));
            if (!$hasMatchingTag) {
                // Subscriber no longer matches the target segments, skip sending but count as sent
                $this->newsletterSend->incrementSent();
                $this->checkCompletion();
                return;
            }
        }

        try {
            Mail::to($this->subscriber->email)
                ->send(new NewsAnnouncementMail(
                    $this->article,
                    $this->subscriber,
                    $this->customIntro,
                    $this->subjectOverride,
                ));

            $this->newsletterSend->incrementSent();
        } catch (\Throwable $e) {
            Log::error('Newsletter send failed', [
                'subscriber' => $this->subscriber->email,
                'article_id' => $this->article->id,
                'newsletter_send_id' => $this->newsletterSend->id,
                'error' => $e->getMessage(),
            ]);

            $this->newsletterSend->incrementFailed();

            // Let the queue retry if attempts remain
            if ($this->attempts() >= $this->tries) {
                $this->checkCompletion();
            }

            throw $e;
        }

        $this->checkCompletion();
    }

    protected function checkCompletion(): void
    {
        $send = $this->newsletterSend->fresh();

        if (($send->sent_count + $send->failed_count) >= $send->subscriber_count) {
            if ($send->failed_count > 0 && $send->sent_count === 0) {
                $send->markFailed();
            } else {
                $send->markCompleted();
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Newsletter job permanently failed', [
            'subscriber' => $this->subscriber->email,
            'article_id' => $this->article->id,
            'error' => $exception->getMessage(),
        ]);

        $this->newsletterSend->incrementFailed();
        $this->checkCompletion();
    }
}
