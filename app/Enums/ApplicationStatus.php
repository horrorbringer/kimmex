<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ApplicationStatus: string implements HasColor, HasLabel
{
    case PENDING = 'PENDING';
    case REVIEWING = 'REVIEWING';
    case SHORTLISTED = 'SHORTLISTED';
    case INTERVIEW = 'INTERVIEW';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::REVIEWING => __('Reviewing'),
            self::SHORTLISTED => __('Shortlisted'),
            self::INTERVIEW => __('Interview'),
            self::ACCEPTED => __('Accepted'),
            self::REJECTED => __('Rejected'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::REVIEWING => 'info',
            self::SHORTLISTED => 'primary',
            self::INTERVIEW => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function emailSubject(): string
    {
        return match ($this) {
            self::PENDING => __('Application Received'),
            self::REVIEWING => __('Application Under Review'),
            self::SHORTLISTED => __('You\'ve Been Shortlisted!'),
            self::INTERVIEW => __('Interview Invitation'),
            self::ACCEPTED => __('Congratulations! Application Accepted'),
            self::REJECTED => __('Application Update'),
        };
    }
}
