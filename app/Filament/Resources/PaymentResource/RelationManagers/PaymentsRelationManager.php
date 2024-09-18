<?php

namespace App\Filament\Resources\PaymentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static ?string $title = null;

    public function __construct() {
        self::$title = 'Pagos';
    }

    public static function getModelLabel(): string
    {
        return __('messages.payment.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.payments');
    }

    public static function getRecordLabel(): string
    {
        return __('messages.payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.payments');
    }

    public static function getLabel(): string
    {
        return __('messages.payment.payment');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.payments');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(__('messages.payment.amount'))
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->debounce(500)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $amount = (float) $get('amount');
                        $set('remaining_balance', $this->calculateRemainingBalance($amount));
                    }),

                Forms\Components\TextInput::make('remaining_balance')
                    ->label(__('messages.payment.remaining_balance'))
                    ->disabled()
                    ->reactive(),

                Forms\Components\DatePicker::make('created_at')
                    ->label(__('messages.payment.created_at'))
                    ->default(now())
                    ->required(),
            ]);
    }

    private function calculateRemainingBalance(float $amount): float
    {
        $order = $this->getOrder();
        $total = $order->total;
        $paid = $order->payments()->sum('amount');

        return max($total - $paid - $amount, 0);
    }

    private function getOrder()
    {
        return $this->ownerRecord;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('messages.payment.amount'))
                    ->sortable()
                    ->searchable()
                    ->money('MXN')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('MXN')
                            ->label(__('messages.payment.amount'))
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('messages.payment.created_at'))
                    ->sortable()
                    ->date(),
            ])
            ->defaultSort('created_at')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
