<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Models\Order;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make(__('pagination.all'))->badge($this->getModel()::count())];

        $tiers = Order::orderBy('status', 'asc')->get('status');

        foreach ($tiers as $tier) {
            $name = $tier->status->getLabel();
            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make($name)
                ->badge($tier->customers_count)
                ->modifyQueryUsing(function ($query) use ($tier) {
                    return $query->where('status', $tier->status->value);
                });
        }

        return $tabs;
    }
}

