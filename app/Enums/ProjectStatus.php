<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel, HasColor
{
    case ONGOING = 'ONGOING';
    case COMPLETED = 'COMPLETED';
    case PLANNED = 'PLANNED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ONGOING => 'Ongoing',
            self::COMPLETED => 'Completed',
            self::PLANNED => 'Planned',
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
