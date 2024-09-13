<?php

namespace App\Models;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;

enum CustomerStatusEnum: string implements HasLabel, HasIcon, HasColor
{
    case UNKNOWN  = 'unknown';
    case BAD      = 'bad';
    case GOOD     = 'good';
    case EXCELLENT = 'excellent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNKNOWN => __('messages.customer.status_unknown'),
            self::BAD => __('messages.customer.status_bad'),
            self::GOOD => __('messages.customer.status_good'),
            self::EXCELLENT => __('messages.customer.status_excellent'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::UNKNOWN => 'gray',
            self::BAD => 'danger',
            self::GOOD => 'warning',
            self::EXCELLENT => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UNKNOWN => 'heroicon-o-question-mark-circle',
            self::BAD => 'heroicon-o-x-circle',
            self::GOOD => 'heroicon-o-check-circle',
            self::EXCELLENT => 'heroicon-o-star',
        };
    }

    public static function asSelectArray(): array
    {
        return [
            self::UNKNOWN->value => __('messages.customer.status_unknown'),
            self::BAD->value => __('messages.customer.status_bad'),
            self::GOOD->value => __('messages.customer.status_good'),
            self::EXCELLENT->value => __('messages.customer.status_excellent'),
        ];
    }
}
