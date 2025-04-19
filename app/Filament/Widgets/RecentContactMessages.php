<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentContactMessages extends BaseWidget
{
    protected static ?string $heading = 'Pesan Kontak Terbaru';
    
    protected static ?int $sort = 3;
    
    protected function getTableQuery(): Builder
    {
        return ContactMessage::query()
            ->latest()
            ->limit(5);
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('subject')
                ->label('Subjek')
                ->limit(30),
                
            Tables\Columns\TextColumn::make('created_at')
                ->label('Dikirim Pada')
                ->dateTime('d M Y H:i'),
                
            Tables\Columns\BadgeColumn::make('is_read')
                ->label('Status')
                ->enum([
                    false => 'Belum Dibaca',
                    true => 'Sudah Dibaca',
                ])
                ->colors([
                    'danger' => false,
                    'success' => true,
                ]),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Lihat')
                ->icon('heroicon-o-eye')
                ->url(fn (ContactMessage $record): string => route('filament.resources.contact-messages.view', $record)),
        ];
    }
}