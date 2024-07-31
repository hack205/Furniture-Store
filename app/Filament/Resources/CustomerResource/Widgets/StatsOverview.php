<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        // Calcular estadísticas actuales
        $totalCustomers = Customer::count();
        $newCustomersLastMonth = Customer::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $totalRevenue = Order::sum('total');
        $averageRevenuePerCustomer = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;
        $totalOrders = Order::count();
        $averageOrdersPerCustomer = $totalCustomers > 0 ? $totalOrders / $totalCustomers : 0;

        // Calcular estadísticas anteriores para comparar
        $totalCustomersLastMonth = Customer::where('created_at', '<', Carbon::now()->subMonth())->count();
        $newCustomersLastTwoMonths = Customer::where('created_at', '>=', Carbon::now()->subMonths(2))
                                             ->where('created_at', '<', Carbon::now()->subMonth())->count();
        $totalRevenueLastMonth = Order::where('created_at', '<', Carbon::now()->subMonth())->sum('total');
        $totalOrdersLastMonth = Order::where('created_at', '<', Carbon::now()->subMonth())->count();

        // Calcular diferencias
        $customersIncrease = $totalCustomers - $totalCustomersLastMonth;
        $newCustomersIncrease = $newCustomersLastMonth - $newCustomersLastTwoMonths;
        $revenueIncrease = $totalRevenue - $totalRevenueLastMonth;
        $ordersIncrease = $totalOrders - $totalOrdersLastMonth;
        $averageRevenuePerCustomerIncrease = $averageRevenuePerCustomer - ($totalRevenueLastMonth / max($totalCustomersLastMonth, 1));
        $averageOrdersPerCustomerIncrease = $averageOrdersPerCustomer - ($totalOrdersLastMonth / max($totalCustomersLastMonth, 1));

        return [
            Stat::make(__('messages.dashboard.total_customers'), number_format($totalCustomers))
                ->description(number_format($customersIncrease) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($customersIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customersIncrease >= 0 ? 'success' : 'danger'),

            Stat::make(__('messages.dashboard.new_customers_last_mount'), number_format($newCustomersLastMonth))
                ->description(number_format($newCustomersIncrease) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($newCustomersIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($newCustomersIncrease >= 0 ? 'success' : 'danger'),

            Stat::make(__('messages.dashboard.total_revenue'), '$' . number_format($totalRevenue, 2))
                ->description('$' . number_format($revenueIncrease, 2) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($revenueIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueIncrease >= 0 ? 'success' : 'danger'),

            Stat::make(__('messages.dashboard.avg_renevue_per_customer'), '$' . number_format($averageRevenuePerCustomer, 2))
                ->description('$' . number_format($averageRevenuePerCustomerIncrease, 2) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($averageRevenuePerCustomerIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($averageRevenuePerCustomerIncrease >= 0 ? 'success' : 'danger'),

            Stat::make(__('messages.dashboard.total_orders'), number_format($totalOrders))
                ->description(number_format($ordersIncrease) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($ordersIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersIncrease >= 0 ? 'success' : 'danger'),

            Stat::make(__('messages.dashboard.avg_orders_per_customer'), number_format($averageOrdersPerCustomer, 2))
                ->description(number_format($averageOrdersPerCustomerIncrease, 2) . ' '.__('messages.dashboard.increase'))
                ->descriptionIcon($averageOrdersPerCustomerIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($averageOrdersPerCustomerIncrease >= 0 ? 'success' : 'danger'),
        ];
    }
}
