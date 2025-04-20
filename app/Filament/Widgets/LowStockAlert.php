<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 10)
                    ->where('stock', '>', 0)
                    ->orderBy('stock')
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
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('update')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ])
            ->heading('Peringatan Stok Rendah')
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('Lihat Semua')
                    ->url(route('filament.admin.resources.products.index', [
                        'tableFilters[stock][min]' => 0,
                        'tableFilters[stock][max]' => 10,
                    ]))
                    ->icon('heroicon-o-arrow-right'),
            ])
            ->emptyStateHeading('Tidak ada produk dengan stok rendah')
            ->emptyStateDescription('Semua produk memiliki stok yang cukup')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}