<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeBannerResource\Pages;
use App\Models\HomeBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HomeBannerResource extends Resource
{
    protected static ?string $model = HomeBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Home Banner';
    
    protected static ?string $navigationGroup = 'Website Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('banner_image')
                    ->label('Banner Image')
                    ->image()
                    ->imageEditor()
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeMode('cover')
                    ->directory('banners')
                    ->required(),
                    
                Forms\Components\TextInput::make('banner_title')
                    ->label('Banner Title (Optional)')
                    ->placeholder('Koleksi Terbaru 2025')
                    ->maxLength(100),
                    
                Forms\Components\TextInput::make('banner_subtitle')
                    ->label('Banner Subtitle (Optional)')
                    ->placeholder('Eksklusif GPX')
                    ->maxLength(200),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only one banner can be active at a time'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_image')
                    ->label('Banner')
                    ->width(200)
                    ->height(100),
                    
                Tables\Columns\TextColumn::make('banner_title')
                    ->label('Title')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHomeBanners::route('/'),
            'create' => Pages\CreateHomeBanner::route('/create'),
            'edit' => Pages\EditHomeBanner::route('/{record}/edit'),
        ];
    }
}
