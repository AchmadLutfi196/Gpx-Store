<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'salesChart';
    protected static ?string $heading = 'Grafik Penjualan';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 2;

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Select::make('period')
                ->label('Periode')
                ->options([
                    'daily' => 'Harian',
                    'weekly' => 'Mingguan',
                    'monthly' => 'Bulanan',
                ])
                ->default('daily')
                ->live()
        ];
    }

    protected function getOptions(): array
    {
        $period = $this->filterFormData['period'] ?? 'daily';

        $data = match ($period) {
            'weekly' => $this->getWeeklySalesData(),
            'monthly' => $this->getMonthlySalesData(),
            default => $this->getDailySalesData(),
        };

        // Praformat data langsung di PHP daripada menggunakan formatter JS
        if (isset($data['series'][0]['data'])) {
            foreach ($data['series'][0]['data'] as $key => &$value) {
                // Konversi ke Jt atau Rb dalam data
                if ($value >= 1000000) {
                    // Format ke X.X Jt di PHP
                    $formattedValue = round($value / 1000000, 1);
                    $data['series'][0]['data'][$key] = $formattedValue;
                    
                    // Update label kategori untuk menambahkan unit
                    if (isset($data['xaxis']['categories'][$key])) {
                        $data['xaxis']['categories'][$key] = $data['xaxis']['categories'][$key] . ' (' . $formattedValue . ' Jt)';
                    }
                } elseif ($value >= 1000) {
                    // Format ke X Rb di PHP
                    $formattedValue = round($value / 1000);
                    $data['series'][0]['data'][$key] = $formattedValue;
                    
                    // Update label kategori untuk menambahkan unit
                    if (isset($data['xaxis']['categories'][$key])) {
                        $data['xaxis']['categories'][$key] = $data['xaxis']['categories'][$key] . ' (' . $formattedValue . ' Rb)';
                    }
                }
                
                // Pastikan semua nilai adalah integer
                $data['series'][0]['data'][$key] = (int)$data['series'][0]['data'][$key];
            }
        }

        return array_merge([
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => [
                    'show' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            // Hindari penggunaan formatter kustom
            'yaxis' => [
                'decimalsInFloat' => 0,
                'title' => [
                    'text' => $period === 'weekly' || $period === 'monthly' ? 
                        'Dalam Jutaan/Ribuan' : 'Dalam Ribuan/Jutaan'
                ],
            ],
            'tooltip' => [
                'enabled' => true,
                'shared' => true,
            ],
        ], $data);
    }

    private function getDailySalesData(): array
    {
        // Implementasi seperti sebelumnya dengan konversi ke integer
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-13 days'));

        $salesData = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('CAST(SUM(total_amount) AS INTEGER) as daily_total') 
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('order_date')
            ->get();

        $days = [];
        $values = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $days[] = date('d M', strtotime($date));
            
            $dayData = $salesData->firstWhere('order_date', $date);
            $values[] = $dayData ? (int)$dayData->daily_total : 0;
        }

        return [
            'series' => [
                [
                    'name' => 'Penjualan',
                    'data' => $values,
                ],
            ],
            'xaxis' => [
                'categories' => $days,
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'colors' => ['#4ade80'],
        ];
    }

    // Metode lainnya seperti sebelumnya
    
    
        private function getWeeklySalesData(): array
        {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = date('Y-m-d');
    
            $salesData = Order::where('status', 'completed')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->select(
                    DB::raw('DATE(created_at) as order_date'),
                    DB::raw('FLOOR(SUM(total_amount)) as daily_total') 
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('order_date')
                ->get();
    
            $days = [];
            $values = [];
    
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $days[] = date('d M', strtotime($date));
                
                $dayData = $salesData->firstWhere('order_date', $date);
                $values[] = $dayData ? (int)$dayData->daily_total : 0;
            }
    
            return [
                'series' => [
                    [
                        'name' => 'Penjualan',
                        'data' => $values,
                    ],
                ],
                'xaxis' => [
                    'categories' => $days,
                    'labels' => [
                        'style' => [
                            'fontSize' => '12px',
                        ],
                    ],
                ],
                'colors' => ['#4ade80'],
            ];
        }

    private function getMonthlySalesData(): array
    {
        $startDate = date('Y-m-d', strtotime('-11 months'));
        $endDate = date('Y-m-d');

        $salesData = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as order_date'),
                DB::raw('CAST(SUM(total_amount) AS INTEGER) as monthly_total') 
            )
            ->groupBy(DB::raw('strftime("%Y-%m", created_at)'))
            ->orderBy('order_date')
            ->get();

        $months = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($date));
            
            $monthData = $salesData->firstWhere('order_date', $date);
            $values[] = $monthData ? (int)$monthData->monthly_total : 0;
        }

        return [
            'series' => [
                [
                    'name' => 'Penjualan',
                    'data' => $values,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'colors' => ['#4ade80'],
        ];
    }

}