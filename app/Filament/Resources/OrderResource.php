<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Models\CustomerStatusEnum;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
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
                                    ->label(__('messages.order.number'))
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label(__('messages.customer.created_at'))
                                    ->default(now())
                                    ->required()
                                    ->afterStateUpdated(function (?Order $order, $state) {
                                        if($order){
                                            $order->created_at = $state;
                                            $order->save();
                                        }
                                    }),
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->label(__('messages.order.customer'))
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('messages.customer.name'))
                                    ->required()
                                    ->maxLength(255),
                                    Forms\Components\TextInput::make('phone')
                                    ->label(__('messages.customer.phone'))
                                    ->maxLength(10),
                                Forms\Components\TextInput::make('city')
                                    ->label(__('messages.customer.city'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('colony')
                                    ->label(__('messages.customer.colony'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->label(__('messages.customer.address'))
                                    ->columnSpan('full')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('street_between_1')
                                    ->label(__('messages.customer.street_1'))
                                    ->nullable()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('street_between_2')
                                    ->label(__('messages.customer.street_2'))
                                    ->nullable()
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->label(__('messages.customer.status_customer'))
                                    ->options(CustomerStatusEnum::asSelectArray())
                                    ->default(CustomerStatusEnum::UNKNOWN->value)
                                    ->required(),
                                    ])
                                    ->createOptionAction(fn (Action $action) => $action
                                    ->modalHeading(__('messages.customer.create_customer'))
                                    ->modalSubmitActionLabel(__('messages.customer.create_customer'))
                                    ->modalWidth('lg')
                                    ),
                                Forms\Components\TextInput::make('agent')
                                    ->label(__('messages.order.agent')),
                                Forms\Components\TextInput::make('route')
                                    ->required()
                                    ->label(__('messages.order.route')),
                                Forms\Components\TextInput::make('payment_conditions')
                                    ->label(__('messages.order.payment_conditions'))
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('advance')
                                    ->label(__('messages.order.advance'))
                                    ->content(fn(?Order $record): string => number_format(
                                        $record ? $record->payments()->first()?->amount ?? 0 : 0,
                                        2
                                    )),
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
                                Forms\Components\DateTimePicker::make('archived_at')
                                    ->label(__('messages.order.archived_at')),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('id')
                                        ->icon('heroicon-m-clipboard')
                                        ->label(__('messages.order.print'))
                                        ->url(fn(?Order $record) => route('print.form', $record->id))
                                        ->openUrlInNewTab()
                                    ]),
                            ])
                            ->columnSpan(['lg' => 3])
                            ->hidden(fn(?Order $record) => $record === null),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),
                Forms\Components\Section::make(__('messages.order.product'))
                    ->schema([
                        Forms\Components\TextInput::make('product')
                            ->label(__('messages.order.product'))
                            ->required(),
                        Forms\Components\TextInput::make('total')
                            ->label(__('messages.order.total'))
                            ->live()
                            ->columnStart('sm')
                            ->required()
                            ->prefix('$')
                    ]),
                Forms\Components\Section::make(__('messages.order.payment_summary'))
                    ->schema([
                        Forms\Components\Placeholder::make('total_paid')
                            ->label(__('messages.order.total_paid'))
                            ->content(fn(?Order $record): string => number_format(
                                $record ? $record->payments()->sum('amount') : 0, 2
                            )),
                        Forms\Components\Placeholder::make('total_due')
                            ->label(__('messages.order.total_due'))
                            ->content(fn(?Order $record): string => number_format(
                                $record ? $record->total - $record->payments()->sum('amount') : 0, 2
                            )),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
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
                    ->label(__('messages.order.created_at'))
                    ->sortable()
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('settled_status')
                    ->label('Order Status')
                    ->options([
                        'all' => __('messages.order.all'),
                        'archived' => __('messages.order.archived_at'),
                        'notarchived' => __('messages.order.not_archived'),
                        'settled' => __('messages.order.paid_orders'),
                    ])
                    ->default('notarchived')
                    ->query(function ($query, $state) {
                        $status = $state['value'];
                        if ($status === 'settled') {
                            return $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id) >= orders.total');
                            } elseif ($status === 'unsettled') {
                            return $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.order_id = orders.id) < orders.total');
                            }
                        else if($status === 'archived'){
                            $query->whereNotNull('archived_at');
                        }else if($status === 'notarchived'){
                            $query->whereNull('archived_at');
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
                Tables\Actions\Action::make('file')
                    ->label(fn(Order $record) => $record->archived_at ? __('messages.order.unarchive') : __('messages.order.file_qualify'))
                    ->form([
                        Forms\Components\Select::make('customer_status')
                            ->label(__('messages.customer.rating_customer'))
                            ->options(CustomerStatusEnum::asSelectArray())
                            ->required()
                            ->hidden(fn(Order $record) => $record->archived_at),
                    ])
                    ->action(function (Order $record, array $data) {
                        if ($record->archived_at) {
                            $record->archived_at = null;
                        } else {
                            $record->archived_at = Carbon::now();
                            $record->customer->update([
                                'status' => $data['customer_status'],
                            ]);
                        }
                        $record->save();
                        Notification::make()
                            ->title($record->archived_at ? __('messages.order.filed_successfully') : __('messages.order.successfully_unarchived'))
                            ->success()
                            ->send();
                    })
                    ->modalHeading(fn(Order $record) => $record->archived_at ? __('messages.order.unarchive') : __('messages.order.file_qualify'))
                    ->modalSubmitActionLabel(fn(Order $record) => $record->archived_at ? __('messages.order.unarchive') : __('messages.button.save'))
                    ->modalWidth('lg')
                    ->color(fn(Order $record) => $record->archived_at ? 'danger' : 'primary'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->groups([
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

        $set('total', $total_price);
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
