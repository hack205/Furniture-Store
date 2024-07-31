<?php

namespace App;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;

enum OrderStatusEnum: string implements HasLabel, HasIcon, HasColor
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED  = 'completed';
    case DECLINED   = 'declined';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING       => __('messages.order.pending'),
            self::PROCESSING    => __('messages.order.processing'),
            self::COMPLETED     => __('messages.order.completed'),
            self::DECLINED      => __('messages.order.declined'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING     => 'info',
            self::PROCESSING  => 'warning',
            self::COMPLETED   => 'success',
            self::DECLINED    => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING     => 'heroicon-o-pencil',
            self::PROCESSING  => 'forkawesome-refresh',
            self::COMPLETED   => 'forkawesome-truck',
            self::DECLINED    => 'heroicon-o-no-symbol',
        };
    }
}
