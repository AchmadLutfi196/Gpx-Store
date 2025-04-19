<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ContactMessage $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesan Kontak Baru dari ' . $this->contactMessage->name)
            ->greeting('Halo Admin!')
            ->line('Anda telah menerima pesan kontak baru dari ' . $this->contactMessage->name . '.')
            ->line('Subjek: ' . $this->contactMessage->subject)
            ->action('Lihat Pesan', route('filament.resources.contact-messages.view', $this->contactMessage->id))
            ->line('Terima kasih telah menggunakan aplikasi Gpx-Store.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Pesan Kontak Baru',
            'message' => 'Dari: ' . $this->contactMessage->name . ' - ' . $this->contactMessage->subject,
            'contact_message_id' => $this->contactMessage->id,
        ];
    }
    
    public static function sendToFilament(ContactMessage $contactMessage)
    {
        FilamentNotification::make()
            ->title('Pesan Kontak Baru')
            ->icon('heroicon-o-mail')
            ->iconColor('danger')
            ->body('Dari: ' . $contactMessage->name . ' - ' . $contactMessage->subject)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->button()
                    ->label('Lihat')
                    ->url(route('filament.resources.contact-messages.view', $contactMessage->id)),
            ])
            ->send();
    }
}