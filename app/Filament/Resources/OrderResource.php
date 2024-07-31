<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\OrderStatusEnum;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\PaymentResource\RelationManagers;

class OrderResource extends Resource
    {
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('messages.orders');
    }

    public static function getLabel(): string
    {
        return __('messages.order.order');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.orders');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('messages.order.order_details'))
                            ->label(__('messages.order.order_details'))
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->default('OR-' . random_int(1000, 9999))
                                    ->label(__('messages.order.number'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->label(__('messages.order.customer'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('agent_id')
                                    ->relationship('agent', 'name')
                                    ->label(__('messages.order.agent'))
                                    ->searchable(),
                                Forms\Components\DateTimePicker::make('archived_at')
                                    ->label(__('messages.order.archived_at')),
                                Forms\Components\ToggleButtons::make('status')
                                    ->label(__('messages.order.status'))
                                    ->required()
                                    ->inline()
                                    ->options(OrderStatusEnum::class),
                                Forms\Components\MarkdownEditor::make('notes')
                                    ->label(__('messages.order.notes'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 12 : 9]),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(__('messages.order.created_at'))
                                    ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(__('messages.order.update_at'))
                                    ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->columnSpan(['lg' => 3])
                            ->hidden(fn(?Order $record) => $record === null),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),

                Forms\Components\Section::make(__('messages.order.order_items'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label(__('messages.order.order_items'))
                            ->required()
                            ->reorderableWithButtons()
                            ->relationship('items')
                            ->reorderable(true)
                            ->live()
                            ->afterStateUpdated(fn($get, $set, $state) => self::updateTotals($get, $set))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('messages.order.product'))
                                    ->live()
                                    ->afterStateUpdated(fn($get, $set, ?string $state) => self::updateItem($get, $set))
                                    ->required(),
                                Forms\Components\TextInput::make('qty')
                                    ->label(__('messages.order.qty'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn($get, $set, ?string $state) => self::updateItem($get, $set)),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('messages.order.unit_price'))
                                    ->default(0)
                                    ->numeric()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn($get, $set, ?string $state) => self::updateItem($get, $set)),
                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->disabled()
                                    ->live()
                            ])->columns(4)
                    ]),

                Forms\Components\Section::make('Total')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->live()
                            ->columnStart('sm')
                            ->disabled()
                            ->prefix('$')
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('messages.order.number'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('messages.order.customer'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.order.status'))
                    ->sortable()
                    ->searchable()
                    ->badge(),
                Tables\Columns\IconColumn::make('is_settled')
                    ->label(__('messages.order.is_settled'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(function (Order $record) {
                        return $record->isSettled();
                        }),
                Tables\Columns\TextColumn::make('amount_due')
                    ->label(__('messages.order.amount_due'))
                    ->money('MXN')
                    ->color('danger')
                    ->getStateUsing(function (Order $record) {
                        return $record->amountDue();
                        }),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('messages.order.total'))
                    ->sortable()
                    ->searchable()
                    ->money('MXN')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->money()
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatusEnum::class),
                Tables\Filters\SelectFilter::make('settled_status')
                    ->label('Order Status')
                    ->options([
                        'all' => 'All Orders',
                        'settled' => 'Paid Orders',
                        'unsettled' => 'Unsettled Orders',
                    ])
                    ->query(function ($query, $state) {
                        $status = $state['value'];
                        if ($status === 'settled') {
                            return $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id) >= orders.total');
                            } elseif ($status === 'unsettled') {
                            return $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id) < orders.total');
                            }
                        return $query;
                        }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                        })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->groups([
                'status',
                Tables\Grouping\Group::make('customer.name')
                    ->label('Author name')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function updateItem(Get $get, Set $set): void
    {
        $set('total', $get('qty') * $get('unit_price'));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('items');

        if (!$items)
            $items = [];

        $total_price = 0;

        foreach ($items as $item)
            $total_price += $item['unit_price'] * $item['qty'];

        $set('total_price', $total_price);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
