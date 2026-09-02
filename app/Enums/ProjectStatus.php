<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasColor, HasLabel
{
    case ONGOING = 'ONGOING';
    case COMPLETED = 'COMPLETED';
    case PLANNED = 'PLANNED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ONGOING => __('Ongoing'),
            self::COMPLETED => __('Completed'),
            self::PLANNED => __('Planned'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ONGOING => 'warning',
            self::COMPLETED => 'success',
            self::PLANNED => 'info',
        };
    }
}
