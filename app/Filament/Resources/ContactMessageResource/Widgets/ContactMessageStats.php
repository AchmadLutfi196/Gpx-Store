<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactMessagesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pesan', ContactMessage::count())
                ->description('Total pesan kontak')
                ->descriptionIcon('heroicon-o-document')
                ->color('primary'),
                
            Stat::make('Belum Dibaca', ContactMessage::where('is_read', false)->count())
                ->description('Membutuhkan perhatian')
                ->descriptionIcon('heroicon-o-exclamation')
                ->color('danger'),
                
            Stat::make('Pesan Hari Ini', ContactMessage::whereDate('created_at', now())->count())
                ->description('Diterima dalam 24 jam terakhir')
                ->descriptionIcon('heroicon-o-clock')
                ->color('success'),
        ];
    }
}