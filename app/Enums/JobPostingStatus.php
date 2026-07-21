<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JobPostingStatus: string implements HasColor, HasLabel
{
    case DRAFT = 'DRAFT';
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
    case FILLED = 'FILLED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::OPEN => __('Open'),
            self::CLOSED => __('Closed'),
            self::FILLED => __('Filled'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::OPEN => 'success',
            self::CLOSED => 'warning',
            self::FILLED => 'info',
        };
    }
}
