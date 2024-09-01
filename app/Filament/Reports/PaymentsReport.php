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
                                Body\TextColumn::make("customer_name")
                                ->label(__('messages.customer.customer')),
                                Body\TextColumn::make("order_id")
                                ->label(__('messages.order.order')),
                                Body\TextColumn::make("amount")
                                    ->label(__('messages.payment.amount'))
                                    ->money('MXN'),
                                Body\TextColumn::make("method")
                                    ->label(__('messages.payment.method')),
                            ])
                            ->data(
                                function (?array $filters) {
                                    $search = $filters['search'] ?? null;
                                    [$from, $to] = Dates::getCarbonInstancesFromDateString($filters['created_at'] ?? null);
                                
                                    $query = Payment::with('order.customer')
                                        ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from))
                                        ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to))
                                        ->when($search, function ($query) use ($search) {
                                            $query->whereHas('order.customer', function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%");
                                            })
                                            ->orWhere('method', 'like', "%{$search}%");
                                        })
                                        ->take(100)
                                        ->get()
                                        ->map(function ($row){
                                            return [
                                                'created_at' => $row->created_at,
                                                'amount' => $row->amount,
                                                'method' => $row->method,
                                                'reference' => $row->reference,
                                                'order_number' => $row->order->number,
                                                'order_id' => $row->order->id,
                                                'customer_name' => $row->order->customer->name,
                                            ];
                                        });
                                
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
                // ...
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('search')
                    ->placeholder(__('Cliente')),

                DateRangePicker::make("created_at")
                    ->label(__('messages.payment.created_at'))
                    ->placeholder(__('messages.reports.select_date_range')),
            ]);
    }
}

