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
    protected int|string|array $columnSpan = 'full';

    
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
                
            Tables\Columns\TextColumn::make('is_read')
                ->label('Status')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Sudah Dibaca' : 'Belum Dibaca')
                ->badge()
                ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Lihat')
                ->icon('heroicon-o-eye')
                ->url(fn (ContactMessage $record): string => route('filament.admin.resources.contact-messages.edit', $record)),
        ];
    }
}