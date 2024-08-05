<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Tables;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static ?string $title = null;

    public function __construct() {
        self::$title = 'Órdenes';
        $title = __('messages.orders');
        //self::$title = $title;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('messages.order.number')),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('messages.order.total'))
                    ->sortable()
                    ->searchable()
                    ->money()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->money()
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('messages.order.created_at'))
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make(__('messages.order.open_order'))
                ->tooltip(__('messages.order.open_order'))
                ->icon('heroicon-m-arrow-top-right-on-square')

                ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', ['record' => $record->id]))
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
