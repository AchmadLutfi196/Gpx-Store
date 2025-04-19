<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class ContactFormSubmission extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ContactMessage
     */
    public $contactMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Pesan Baru dari Form Kontak - ' . $this->contactMessage->subject)
                   ->markdown('emails.contact-form-submission');
    }
}