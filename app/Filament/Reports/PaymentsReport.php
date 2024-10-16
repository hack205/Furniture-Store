<?php

namespace App\Filament\Reports;
use App\Models\Payment;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Text;
use App\Helpers\Dates;
use EightyNine\Reports\Components\Header;
use Filament\Forms\Form;
use EightyNine\Reports\Components\VerticalSpace;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class PaymentsReport extends Report
{
    public ?string $heading = "Reporte";
    protected static ?string $model = Payment::class;
    protected static ?string $navigationLabel = 'Reporte de Pagos';

    public ?float $totalPayments = 0;

    public function header(Header $header): Header
    {
        return $header
            ->schema([
                Header\Layout\HeaderRow::make()
                    ->schema([
                        Header\Layout\HeaderColumn::make()
                            ->schema([
                                Text::make(__('messages.shop'))
                                    ->title()
                                    ->primary(),
                                Text::make(__('messages.reports.report'))
                                    ->subtitle(),
                            ])->alignRight()
                    ]),
            ]);
    }


    public function body(Body $body): Body
    {
        return $body
            ->schema([
                Body\Layout\BodyColumn::make()
                    ->schema([
                        Text::make(__('messages.reports.payments_report'))
                            ->fontXl()
                            ->fontBold()
                            ->primary(),
                        Body\Table::make()
                            ->columns([
                                Body\TextColumn::make("created_at")
                                    ->label(__('messages.order.created_at'))
                                    ->date(),
                                Body\TextColumn::make("amount")
                                    ->label(__('messages.payment.amount'))
                                    ->money('MXN'),
                            ])
                            ->data(
                                function (?array $filters) {
                                    $search = $filters['search'] ?? null;
                                    $order = $filters['order'] ?? null;
                                    [$from, $to] = Dates::getCarbonInstancesFromDateString($filters['created_at'] ?? null);

                                    $query = Payment::with('order')
                                        ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from))
                                        ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to))
                                        ->when($search, function ($query) use ($search) {
                                            $query->whereHas('order.customer', function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%");
                                            });
                                        })
                                        ->when($order, function ($query) use ($order) {
                                            $query->whereHas('order', function ($query) use ($order) {
                                                $query->where('number', 'like', "%{$order}%");
                                            });
                                        })
                                        ->orderBy('created_at', 'asc')
                                        ->when(!$from && !$to, function ($query) {
                                            return $query->take(100);
                                        })
                                        ->get()
                                        ->map(function ($row){
                                            return [
                                                'created_at' => $row->created_at,
                                                'amount' => $row->amount,
                                                'reference' => $row->reference,
                                                'order_number' => $row->order->number,
                                                'order_id' => $row->order->id,
                                                'number' => $row->order->number,
                                                'customer_name' => $row->order->customer->name,
                                            ];
                                        });
                                    $this->totalPayments = $query->sum('amount');

                                    return $query;
                                }
                            ),
                        VerticalSpace::make(),

                    ]),
            ]);
    }

    public function footer(Footer $footer): Footer
    {
        return $footer
            ->schema([
                Text::make('Total de Pagos: ' . number_format($this->totalPayments, 2) . ' MXN')
                    ->fontBold()
                    ->primary(),
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('search')
                    ->label(__('messages.customers'))
                    ->placeholder(__('messages.customers')),

                \Filament\Forms\Components\TextInput::make('order')
                    ->label(__('messages.orders'))
                    ->placeholder(__('messages.orders')),

                DateRangePicker::make("created_at")
                    ->label(__('messages.payment.created_at'))
                    ->placeholder(__('messages.reports.select_date_range')),
            ]);
    }
}
