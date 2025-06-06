<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Pesanan';
    
    protected static ?string $navigationGroup = 'E-Commerce';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Nomor Pesanan')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->label('Pelanggan')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'failed' => 'Failed',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, ?Order $record) {
                                // Log untuk debugging
                                Log::info('Status pesanan diubah di Filament', [
                                    'order_id' => $record ? $record->id : null,
                                    'old_status' => $record ? $record->status : null,
                                    'new_status' => $state
                                ]);
                                
                                // Jika status diubah menjadi 'cancelled', set cancelled_at
                                if ($state === 'cancelled' && $record && $record->status !== 'cancelled') {
                                    $set('cancelled_at', now());
                                }
                                    }),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, ?Order $record) {
                                // Log untuk debugging
                                Log::info('Status pembayaran diubah di Filament', [
                                    'order_id' => $record ? $record->id : null,
                                    'old_payment_status' => $record ? $record->payment_status : null,
                                    'new_payment_status' => $state
                                ]);
                                
                                // Jika payment_status diubah menjadi 'cancelled', set cancelled_at
                                if ($state === 'cancelled' && $record && $record->payment_status !== 'cancelled') {
                                    $set('cancelled_at', now());
                                }
                            }),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->numeric()
                            ->label('Biaya Pengiriman'),
                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->label('Pajak'),
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->label('Diskon'),
                        Forms\Components\Select::make('shipping_method')
                            ->options([
                                'regular' => 'Regular',
                                'express' => 'Express',
                                'same_day' => 'Same Day',
                            ])
                            ->label('Metode Pengiriman'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pesanan')
                            ->nullable(),
                    ])->columnSpan(8),
                    
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Tanggal Pemesanan')
                            ->content(fn (Order $record): string => $record->created_at->format('d F Y, H:i')),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->content(fn (Order $record): string => $record->updated_at->format('d F Y, H:i')),
                    ])->columnSpan(4),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'failed']),
                    ]),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => fn ($state) => in_array($state, ['failed', 'cancelled']),
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn (string $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn($query) => $query->whereDate('created_at', '>=', $data['dari_tanggal']),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn($query) => $query->whereDate('created_at', '<=', $data['sampai_tanggal']),
                            );
                    }),
            ])
            ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('cancel')
                ->label('Batalkan')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => 
                    !in_array($record->status, ['cancelled', 'completed']))
                ->action(function (Order $record): void {
                    // Set status ke cancelled
                    $record->status = 'cancelled';
                    
                    // Cek status pembayaran saat ini
                    $wasPaid = $record->payment_status === 'completed';
                    
                    // Set payment status sesuai kondisi
                    if ($record->payment_status !== 'completed') {
                        $record->payment_status = 'cancelled';
                    } else {
                        $record->payment_status = 'refunded'; // Jika sudah dibayar, tandai sebagai refunded
                    }
                    
                    $record->cancelled_at = now();
                    $record->save();
                    
                    // Restore promo usage_limit jika promo dipakai
                    if ($record->promo_code_id) {
                        $promo = PromoCode::find($record->promo_code_id);
                        if ($promo && $promo->used_count > 0) {
                            // Kembalikan penggunaan promo
                            $promo->used_count--;
                            $promo->save();
                            
                            // Log untuk debugging
                            Log::info('Penggunaan promo dikembalikan', [
                                'order_id' => $record->id,
                                'promo_id' => $promo->id,
                                'promo_code' => $promo->code,
                                'used_count_new' => $promo->used_count
                            ]);
                            
                            // Kembalikan penggunaan promo
                            $promo->restoreUsage();
                        }
                        // if ($promo) {
                        //     $promo->restoreUsage();
                            
                        //     Log::info('Penggunaan promo dikembalikan', [
                        //         'order_id' => $record->id,
                        //         'promo_id' => $promo->id,
                        //         'promo_code' => $promo->code,
                        //         'used_count_new' => $promo->used_count
                        //     ]);
                        // }
                    }
                    
                    // Kembalikan stok HANYA jika pembayaran sebelumnya completed
                    if ($wasPaid) {
                        // Kembalikan stok untuk setiap item pesanan
                        foreach ($record->items as $item) {
                            $product = $item->product;
                            if ($product) {
                                $product->stock += $item->quantity;
                                $product->save();
                                
                                Log::info('Stok dikembalikan via action Filament', [
                                    'order_id' => $record->id,
                                    'product_id' => $product->id,
                                    'quantity_returned' => $item->quantity,
                                    'new_stock' => $product->stock
                                ]);
                            }
                        }
                        
                        Notification::make()
                            ->title('Pesanan dibatalkan dan stok dikembalikan')
                            ->success()
                            ->send();
                    } else {
                        Log::info('Pesanan dibatalkan tanpa mengubah stok (belum dibayar)', [
                            'order_id' => $record->id
                        ]);
                        
                        Notification::make()
                            ->title('Pesanan dibatalkan')
                            ->success()
                            ->send();
                    }
                }),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
    
    public static function getRelations(): array
    {
        return [
            OrderResource\RelationManagers\ItemsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }    
}