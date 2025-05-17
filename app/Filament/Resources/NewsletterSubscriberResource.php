<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Marketing';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\Toggle::make('confirmed')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'unsubscribed' => 'Unsubscribed',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('confirmed_at')
                    ->withoutSeconds(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('confirmed')
                    ->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'unsubscribed',
                    ]),
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'unsubscribed' => 'Unsubscribed',
                    ]),
                Tables\Filters\Filter::make('confirmed')
                    ->query(fn ($query) => $query->where('confirmed', true))
                    ->label('Confirmed Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (NewsletterSubscriber $record) => $record->update(['confirmed' => true, 'status' => 'active', 'confirmed_at' => now()]))
                    ->visible(fn (NewsletterSubscriber $record) => !$record->confirmed),
                Tables\Actions\Action::make('unsubscribe')
                    ->label('Unsubscribe')
                    ->icon('heroicon-o-x-mark') // Updated from 'heroicon-o-x' to 'heroicon-o-x-mark'
                    ->color('danger')
                    ->action(fn (NewsletterSubscriber $record) => $record->update(['status' => 'unsubscribed']))
                    ->visible(fn (NewsletterSubscriber $record) => $record->status !== 'unsubscribed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('confirm_selected')
                    ->label('Confirm Selected')
                    ->icon('heroicon-o-check')
                    ->action(fn (Collection $records) => $records->each->update(['confirmed' => true, 'status' => 'active', 'confirmed_at' => now()])),
                Tables\Actions\BulkAction::make('unsubscribe_selected')
                    ->label('Unsubscribe Selected')
                    ->icon('heroicon-o-x-mark') // Updated from 'heroicon-o-x' to 'heroicon-o-x-mark'
                    ->color('danger')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'unsubscribed'])),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }    
}
