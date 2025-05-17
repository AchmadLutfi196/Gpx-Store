<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Promo Codes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Promo Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                            
                        Forms\Components\TextInput::make('promotion_title')
                            ->label('Judul Promo')
                            ->maxLength(255)
                            ->helperText('Judul yang akan ditampilkan untuk promo ini'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(255)
                            ->helperText('Penjelasan detail tentang promo ini'),
                            
                        Forms\Components\Select::make('discount_type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->default('percentage'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('discount_value')
                                    ->label('Discount Value')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix(function($get) {
                                        return $get('discount_type') === 'percentage' ? '%' : 'Rp';
                                    }),
                                    
                                Forms\Components\TextInput::make('minimum_order')
                                    ->label('Minimum Order')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->default(0),
                            ]),
                            
                        Forms\Components\TextInput::make('maximum_discount')
                            ->label('Maximum Discount (for percentage)')
                            ->helperText('Maximum discount amount for percentage discount. Leave 0 for no limit.')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->label('Start Date')
                                    ->default(now()),
                                    
                                Forms\Components\DateTimePicker::make('end_date')
                                    ->label('End Date')
                                    ->minDate(function($get) {
                                        $startDate = $get('start_date');
                                        return $startDate;
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger'),
                                    
                                Forms\Components\TextInput::make('usage_limit')
                                    ->label('Usage Limit')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Number of times this code can be used. 0 means unlimited.')
                                    ->default(0),
                            ]),
                        Forms\Components\Toggle::make('show_on_homepage')
                        ->label('Show on Homepage')
                        ->helperText('Display this promo in the homepage banner section')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('danger'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Promo Code')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('promotion_title')
                    ->label('Judul Promo')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Value')
                    ->formatStateUsing(function ($record) {
                        return $record->discount_type === 'percentage'
                            ? "{$record->discount_value}%"
                            : "Rp " . number_format($record->discount_value, 0, ',', '.');
                    }),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('No End Date'),
                    
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '∞')
                    ->sortable(),
                Tables\Columns\IconColumn::make('show_on_homepage')
                    ->label('On Homepage')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                    
                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn ($query) => $query->where('end_date', '<', now())),
                    
                Tables\Filters\Filter::make('active_date')
                    ->label('Currently Active')
                    ->query(function ($query) {
                        return $query->where('is_active', true)
                            ->where(function ($query) {
                                $query->whereNull('start_date')
                                    ->orWhere('start_date', '<=', now());
                            })
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>=', now());
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}