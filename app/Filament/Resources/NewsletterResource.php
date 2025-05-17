<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'Marketing';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sent' => 'Sent',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Schedule Send Date')
                    ->withoutSeconds()
                    ->visible(fn ($get) => $get('status') === 'scheduled'),
                Forms\Components\DateTimePicker::make('sent_at')
                    ->label('Sent Date')
                    ->withoutSeconds()
                    ->disabled()
                    ->visible(fn ($get) => $get('status') === 'sent'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'sent',
                    ]),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
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
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sent' => 'Sent',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send')
                    ->label('Send Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(function (Newsletter $record) {
                        // Mark as sent
                        $record->update(['status' => 'sent', 'sent_at' => now()]);
                        
                        // Send the newsletter to all active subscribers
                        $subscribers = NewsletterSubscriber::where('status', 'active')
                            ->where('confirmed', true)
                            ->get();
                        
                        $sentCount = 0;
                        $errorCount = 0;
                        
                        foreach ($subscribers as $subscriber) {
                            try {
                                Mail::to($subscriber->email)->send(new NewsletterCampaign($record));
                                $sentCount++;
                            } catch (\Exception $e) {
                                Log::error('Failed to send newsletter to ' . $subscriber->email, [
                                    'error' => $e->getMessage(),
                                    'newsletter_id' => $record->id
                                ]);
                                $errorCount++;
                            }
                        }
                        
                        if ($sentCount > 0) {
                            Notification::make()
                                ->title('Newsletter Sent')
                                ->body("Successfully sent to {$sentCount} subscribers" . ($errorCount > 0 ? " ({$errorCount} failed)" : ""))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Newsletter Not Sent')
                                ->body('No active subscribers found or all sending attempts failed.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->visible(fn (Newsletter $record) => $record->status !== 'sent'),
            ])
            ->bulkActions([
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
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }    
}
