<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminResponseMail;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Pesan Kontak';
    
    protected static ?string $navigationGroup = 'Customer Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengirim')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->disabled()
                            ->required(),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled()
                            ->required(),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->disabled(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Detail Pesan')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled()
                            ->required(),
                        
                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->disabled()
                            ->required()
                            ->rows(6),
                        
                        Forms\Components\Toggle::make('is_read')
                            ->label('Sudah Dibaca')
                            ->required(),
                        
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Internal')
                            ->helperText('Catatan ini hanya untuk admin dan tidak dikirim ke pengirim')
                            ->placeholder('Tambahkan catatan internal untuk pesan ini...')
                            ->rows(3),
                    ]),
                
                Forms\Components\Section::make('Balasan')
                    ->schema([
                        Forms\Components\Textarea::make('admin_response')
                            ->label('Balasan ke Pengirim')
                            ->rows(6),
                            
                        Forms\Components\Toggle::make('response_sent')
                            ->label('Balasan Terkirim')
                            ->disabled(),
                    ])
                    ->visible(fn (): bool => request()->route()->hasParameter('record')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                    
                // Status pesan
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Dibaca')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                // Status balasan
                Tables\Columns\IconColumn::make('response_sent')
                    ->label('Dibalas')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                // Add user relationship column
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_read')
                    ->label('Status Dibaca')
                    ->options([
                        '0' => 'Belum Dibaca',
                        '1' => 'Sudah Dibaca',
                    ]),
                
                Tables\Filters\SelectFilter::make('response_sent')
                    ->label('Status Balasan')
                    ->options([
                        '0' => 'Belum Dibalas',
                        '1' => 'Sudah Dibalas',
                    ]),
                
                // Add filter by user
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name'),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                // Gunakan Modal Action untuk respons
                Tables\Actions\Action::make('sendResponse')
                    ->label('Balas')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->modalHeading(fn (ContactMessage $record) => 'Balas ke: ' . $record->name)
                    ->modalDescription(fn (ContactMessage $record) => 'Subjek: ' . $record->subject)
                    ->form([
                        Forms\Components\Section::make('Pesan Asli')
                            ->schema([
                                Forms\Components\Textarea::make('original_message')
                                    ->label('Pesan dari Pengirim')
                                    ->default(fn (ContactMessage $record) => $record->message)
                                    ->disabled()
                                    ->rows(3)
                                    ->extraAttributes(['class' => 'bg-gray-50']),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                            
                        Forms\Components\Textarea::make('admin_response')
                            ->label('Balasan')
                            ->required()
                            ->rows(5)
                            ->placeholder('Ketik balasan Anda...'),
                    ])
                    ->action(function (ContactMessage $record, array $data): void {
                        // Update record
                        $record->update([
                            'admin_response' => $data['admin_response'],
                            'response_sent' => true,
                            'is_read' => true,
                        ]);
                        
                        // Kirim email
                        try {
                            Mail::to($record->email)
                                ->send(new AdminResponseMail($record));
                            
                            Notification::make()
                                ->title('Balasan berhasil dikirim')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal mengirim email')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (ContactMessage $record): bool => !$record->response_sent),
                
                Tables\Actions\Action::make('mark_as_read')
                    ->label('Tandai Dibaca')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => true]))
                    ->visible(fn (ContactMessage $record): bool => !$record->is_read),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsRead')
                        ->label('Tandai Dibaca')
                        ->icon('heroicon-o-check')
                        ->action(fn (Builder $query) => $query->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListContactMessages::route('/'),
            'create' => Pages\CreateContactMessage::route('/create'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}