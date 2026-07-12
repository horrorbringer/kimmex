<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterSend extends Model
{
    use HasUuids;

    protected $fillable = [
        'article_id',
        'sent_by',
        'custom_intro',
        'subject_a',
        'subject_b',
        'is_ab_test',
        'ab_test_percentage',
        'winning_subject',
        'ab_completed_at',
        'subscriber_count',
        'sent_count',
        'failed_count',
        'status',
        'sent_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'completed_at' => 'datetime',
            'ab_completed_at' => 'datetime',
            'subscriber_count' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer',
            'sent_by' => 'integer',
            'is_ab_test' => 'boolean',
            'ab_test_percentage' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(NewsArticle::class, 'article_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function markSending(): void
    {
        $this->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function incrementSent(): void
    {
        $this->increment('sent_count');
    }

    public function incrementFailed(): void
    {
        $this->increment('failed_count');
    }

    /**
     * Check if this A/B test is awaiting a winner selection.
     */
    public function isAwaitingWinner(): bool
    {
        return $this->is_ab_test
            && $this->status === 'completed'
            && is_null($this->winning_subject)
            && is_null($this->ab_completed_at);
    }

    /**
     * Mark the A/B test as completed with a winning subject.
     */
    public function markAbCompleted(string $winner): void
    {
        $this->update([
            'winning_subject' => $winner,
            'ab_completed_at' => now(),
        ]);
    }
}
