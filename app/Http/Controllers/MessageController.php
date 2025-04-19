<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

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
     * Proses cek status pesan dan tampilkan hasilnya
     */
    public function viewStatus(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message_id' => 'required|numeric',
        ]);
        
        $message = ContactMessage::where('id', $validated['message_id'])
                   ->where('email', $validated['email'])
                   ->first();
        
        if (!$message) {
            return redirect()->route('message.check-status')
                ->withInput()
                ->withErrors(['error' => 'Pesan tidak ditemukan. Pastikan email dan ID pesan yang Anda masukkan benar.']);
        }
        
        return view('message-status', compact('message'));
    }
}