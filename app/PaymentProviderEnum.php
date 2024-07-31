<?php

namespace App;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;

enum PaymentProviderEnum: string implements HasLabel, HasIcon, HasColor
{
    case EFECTIVO       = 'efectivo';
    case TRANSFERENCIA  = 'transferencia';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EFECTIVO => __('messages.payment.efectivo'),
            self::TRANSFERENCIA => __('messages.payment.transferencia'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EFECTIVO => 'success',
            self::TRANSFERENCIA => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EFECTIVO => 'forkawesome-money',
            self::TRANSFERENCIA => 'forkawesome-cc-visa',
        };
    }
}
