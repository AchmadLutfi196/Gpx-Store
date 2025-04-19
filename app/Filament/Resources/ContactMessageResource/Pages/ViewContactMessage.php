<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminResponseMail;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        $recordName = $this->record->name ?? 'Pengirim';
        
        return [
            Actions\Action::make('send_response')
                ->label('Kirim Balasan')
                ->form([
                    Textarea::make('admin_response')
                        ->label('Balasan')
                        ->required()
                        ->rows(5)
                        ->placeholder('Ketik balasan Anda untuk pengirim...')
                        ->default($this->record->admin_response),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'admin_response' => $data['admin_response'],
                        'response_sent' => true,
                        'is_read' => true,
                    ]);
                    
                    try {
                        Mail::to($this->record->email)
                            ->send(new AdminResponseMail($this->record));
                            
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
                // FIXED: Use string directly instead of closure
                ->modalHeading('Kirim Balasan ke ' . $recordName)
                ->modalSubmitActionLabel('Kirim Balasan'),
                
            Actions\EditAction::make(),
        ];
    }
}