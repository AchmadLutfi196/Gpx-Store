<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OutOfStockProducts extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '=', 0)
                    ->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Gambar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->date('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('update')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ])
            ->heading('Produk Habis Stok')
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('Lihat Semua')
                    ->url(route('filament.admin.resources.products.index', [
                        'tableFilters[stock][value]' => '0',
                    ]))
                    ->icon('heroicon-o-arrow-right'),
            ])
            ->emptyStateHeading('Tidak ada produk habis stok')
            ->emptyStateDescription('Semua produk memiliki stok')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}