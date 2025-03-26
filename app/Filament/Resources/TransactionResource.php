<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $pluralLabel = 'Transaksi';
    protected static ?string $slug = 'transaksi';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')->disabled(),
                Forms\Components\TextInput::make('user_id')->label('User ID')->disabled(),
                Forms\Components\TextInput::make('total_amount')->label('Total')->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'settlement' => 'Berhasil',
                        'expire' => 'Kadaluarsa',
                        'cancel' => 'Dibatalkan',
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user_id')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'pending' => 'warning',
                        'settlement' => 'success',
                        'expire' => 'danger',
                        'cancel' => 'danger',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'settlement' => 'Berhasil',
                        'expire' => 'Kadaluarsa',
                        'cancel' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
