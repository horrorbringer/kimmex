<?php

namespace Tests\Feature;

use App\Mail\NewsAnnouncementMail;
use App\Mail\WeeklyDigestMail;
use App\Mail\WelcomeSubscriberMail;
use App\Models\NewsArticle;
use App\Models\Subscriber;
use Illuminate\Support\Collection;
use Tests\TestCase;

class NewsletterMailSubjectTest extends TestCase
{
    public function test_newsletter_and_subscriber_mail_subjects_use_plain_separators(): void
    {
        $article = new NewsArticle;
        $article->setTranslation('title', 'en', 'Kimmex Marks a New Milestone');
        $subscriber = new Subscriber([
            'name' => 'Sok Chan',
            'email' => 'sok.chan@example.com',
            'unsubscribe_token' => 'unsubscribe-token',
        ]);

        $newsMail = new NewsAnnouncementMail($article, $subscriber);
        $digestMail = new WeeklyDigestMail(new Collection, new Collection, $subscriber);
        $welcomeMail = new WelcomeSubscriberMail($subscriber);

        $this->assertSame('Kimmex News: Kimmex Marks a New Milestone', $newsMail->envelope()->subject);
        $this->assertStringStartsWith('Kimmex Weekly Digest: ', $digestMail->envelope()->subject);
        $this->assertSame('Welcome to Kimmex Newsletter', $welcomeMail->envelope()->subject);
    }
}
