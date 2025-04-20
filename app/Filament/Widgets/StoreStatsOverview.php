<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StoreStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Fungsi helper untuk format angka
        $formatCurrency = function ($amount) {
            if ($amount >= 1000000) {
                return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . ' Jt';
            } elseif ($amount >= 1000) {
                return 'Rp ' . number_format($amount / 1000, 0, ',', '.') . ' Rb';
            } else {
                return 'Rp ' . number_format($amount, 0, ',', '.');
            }
        };

        // Ambil data penjualan dan statistik
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $totalOrders = Order::count();
        $totalUsers = User::count();

        // Data untuk month
        $monthStart = date('Y-m-01');
        $ordersThisMonth = Order::whereDate('created_at', '>=', $monthStart)->count();
        $newUsersThisMonth = User::whereDate('created_at', '>=', $monthStart)->count();

        // Ambil data untuk chart (7 hari terakhir)
        $lastWeekSales = $this->getLastWeekData();

        return [
            Stat::make('Total Penjualan', $formatCurrency($totalSales))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->chart($lastWeekSales['salesData'])
                ->color('success'),

            Stat::make('Total Orders', $totalOrders)
                ->description($ordersThisMonth . ' bulan ini')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->chart($lastWeekSales['ordersData'])
                ->color('primary'),

            Stat::make('Total Users', $totalUsers)
                ->description($newUsersThisMonth . ' registrasi baru')
                ->descriptionIcon('heroicon-o-users')
                ->chart($lastWeekSales['usersData'])
                ->color('info'),
        ];
    }

    private function getLastWeekData(): array
    {
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        
        // Query untuk penjualan harian
        $salesData = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('SUM(total_amount) as daily_total'),
                DB::raw('COUNT(*) as daily_count')
            )
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');
            
        // Query untuk user baru
        $usersData = User::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select(
                DB::raw('DATE(created_at) as register_date'),
                DB::raw('COUNT(*) as daily_count')
            )
            ->groupBy('register_date')
            ->orderBy('register_date')
            ->get()
            ->keyBy('register_date');
        
        // Generate data arrays
        $salesChartData = [];
        $ordersChartData = [];
        $usersChartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // Sales data
            $dailySales = $salesData[$date]['daily_total'] ?? 0;
            $salesChartData[] = (float)$dailySales;
            
            // Orders data
            $dailyOrders = $salesData[$date]['daily_count'] ?? 0;
            $ordersChartData[] = (int)$dailyOrders;
            
            // Users data
            $dailyUsers = $usersData[$date]['daily_count'] ?? 0;
            $usersChartData[] = (int)$dailyUsers;
        }
        
        return [
            'salesData' => $salesChartData,
            'ordersData' => $ordersChartData,
            'usersData' => $usersChartData,
        ];
    }
}