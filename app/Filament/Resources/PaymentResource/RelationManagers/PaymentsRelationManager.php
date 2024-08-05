<?php

namespace App\Filament\Resources\PaymentResource\RelationManagers;

use App\PaymentProviderEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static ?string $title = null;

    public function __construct() {
        self::$title = 'Pagos';
        //$title = __('messages.payments');
        //self::$title = $title;
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
                Forms\Components\TextInput::make('reference')
                    ->label(__('messages.payment.reference'))
                    ->required()
                    ->maxLength(255),
            Forms\Components\TextInput::make('amount')
                ->label(__('messages.payment.amount'))
                ->required()
                ->numeric(),
            Forms\Components\ToggleButtons::make('method')
                    ->label(__('messages.payment.method'))
                    ->required()
                    ->inline()
                    ->options(PaymentProviderEnum::class)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('messages.payment.reference')),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('messages.payment.method'))
                    ->sortable()
                    ->searchable()
                    ->badge(),
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
