<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $recordTitleAttribute = 'address_line';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('address_line')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('province')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('postal_code')
                    ->required()
                    ->maxLength(10),
                Forms\Components\Toggle::make('is_default')
                    ->label('Default Address')
                    ->default(false),
                Forms\Components\Select::make('type')
                    ->options([
                        'home' => 'Home',
                        'office' => 'Office',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->default('home'),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(15),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(500),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('address_line'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('province'),
                Tables\Columns\TextColumn::make('postal_code'),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('phone_number'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }    
}
