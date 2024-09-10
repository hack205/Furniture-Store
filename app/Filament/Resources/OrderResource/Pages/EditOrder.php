<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_payments')
                ->label(__('messages.order.print_payments'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-printer')
                ->url(fn($record) => route('print.orderpayments', $record->id))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
