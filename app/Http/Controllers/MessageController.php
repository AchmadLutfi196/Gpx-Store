<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Tampilkan form untuk mengecek status pesan
     */
    public function checkStatus()
    {
        return view('message-check');
    }
    
    /**
     * Proses cek status pesan dan tampilkan semua pesan dari email tersebut
     */
    public function viewStatus(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);
        
        // Ambil semua pesan untuk email ini, urutkan berdasarkan terbaru
        $messages = ContactMessage::where('email', $validated['email'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        if ($messages->isEmpty()) {
            return redirect()->route('message.check-status')
                ->withInput()
                ->withErrors(['error' => 'Tidak ada pesan yang ditemukan untuk email ini. Pastikan email yang Anda masukkan benar.']);
        }
        
        return view('message-list', compact('messages'));
    }
    
    /**
     * Lihat detail satu pesan
     */
    public function viewMessage($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Optional: Tandai pesan sebagai dibaca jika belum
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        
        return view('message-status', compact('message'));
    }
}