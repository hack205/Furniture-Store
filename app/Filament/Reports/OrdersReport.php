<?php

namespace App\Filament\Reports;

use App\Helpers\Dates;
use App\Models\Order;
use Filament\Forms\Form;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\VerticalSpace;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class OrdersReport extends Report
{
    public ?string $heading = "Reporte";

    protected static ?string $model = Order::class;


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
                        Text::make(__('messages.reports.orders_report'))
                            ->fontXl()
                            ->fontBold()
                            ->primary(),
                        Body\Table::make()
                            ->columns([
                                Body\TextColumn::make("number")
                                    ->label(__('messages.order.number')),
                                Body\TextColumn::make('customer')
                                    ->label(__('messages.customer.name')),
                                Body\TextColumn::make("product")
                                    ->label(__('messages.order.product')),
                                Body\TextColumn::make("created_at")
                                    ->label(__('messages.order.created_at'))
                                    ->date(),
                                Body\TextColumn::make("total")
                                    ->label(__('messages.order.total'))
                                    ->money('MXN'),

                            ])
                            ->data(
                                function (?array $filters) {

                                    $search = $filters['search'] ?? null;
                                    [$from, $to] = Dates::getCarbonInstancesFromDateString($filters['created_at'] ?? null);

                                    $query = Order::with('customer')
                                        ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from))
                                        ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to))
                                        ->when($search, fn($query) => $query->where('product', 'like', "%{$search}%"))
                                        ->take(100)
                                        ->get()
                                        ->map(function ($row){
                                            return [
                                                'created_at' => $row->created_at,
                                                'total' => $row->total,
                                                'number' => $row->number,
                                                'product'=> $row->product,
                                                'customer' => $row->customer->name
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
                    ->placeholder(__('messages.order.product')),

                DateRangePicker::make("created_at")
                    ->label(__('messages.order.created_at'))
                    ->placeholder(__('messages.reports.select_date_range')),
            ]);
    }
}
