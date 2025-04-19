<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Menampilkan halaman kontak
     */
    public function index()
    {
        return view('contact');
    }
    
    /**
     * Menyimpan pesan kontak dari form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek pesan wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
            'message.min' => 'Isi pesan minimal 10 karakter.',
        ]);
        
        // Set status belum dibaca
        $validated['is_read'] = false;
        
        // Simpan pesan ke database
        $contactMessage = ContactMessage::create($validated);
        
        // Kirim email ke admin
        try {
            // Gunakan email admin dari konfigurasi atau default
            $adminEmail = config('mail.admin_address', 'admin@gpx-store.com');
            Mail::to($adminEmail)->send(new ContactFormSubmission($contactMessage));
            
            // Kirim juga ke alamat email tetap sebagai backup (opsional)
            // Mail::to('cs@gpx-store.com')->send(new ContactFormSubmission($contactMessage));
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan proses
            Log::error('Gagal mengirim email notifikasi: ' . $e->getMessage());
        }
        
        // Kirim notifikasi ke admin panel Filament (jika digunakan)
        try {
            if (class_exists(\Filament\Notifications\Notification::class)) {
                $this->sendFilamentNotification($contactMessage);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi Filament: ' . $e->getMessage());
        }
        
        // Redirect ke halaman kontak dengan pesan sukses
        return redirect()->route('contact.index')
            ->with('success', 'Pesan Anda telah berhasil dikirim! Tim customer service kami akan menghubungi Anda segera.');
    }
    
    /**
     * Kirim notifikasi ke Filament admin panel
     * Metode ini diisolasi untuk mencegah error jika Filament tidak digunakan
     */
    protected function sendFilamentNotification($contactMessage)
    {
        // Hanya panggil jika class Notification tersedia
        if (method_exists(\Filament\Notifications\Notification::class, 'make')) {
            $notification = \Filament\Notifications\Notification::make()
                ->title('Pesan Kontak Baru')
                ->icon('heroicon-o-envelope')
                ->iconColor('danger')
                ->body('Dari: ' . $contactMessage->name . ' - ' . $contactMessage->subject);
            
            // Tambahkan action jika method tersedia
            if (method_exists($notification, 'actions')) {
                $notification->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('Lihat')
                        ->url(route('filament.admin.resources.contact-messages.view', $contactMessage->id)),
                ]);
            }
            
            // Kirim notifikasi
            // Get admin users to send the notification to
            $users = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            // Send notification to all admin users
            foreach ($users as $user) {
                $notification->sendToDatabase($user);
            }
        }
    }
}