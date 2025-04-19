<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminResponseMail;
use Illuminate\Support\Facades\Log;

class ContactResponseController extends Controller
{
    /**
     * Mengirim balasan ke pengirim pesan
     */
    public function sendResponse(Request $request, $id)
    {
        // Validasi request
        $validated = $request->validate([
            'admin_response' => 'required|string|min:10',
        ]);
        
        // Ambil pesan
        $message = ContactMessage::findOrFail($id);
        
        // Update pesan dengan balasan admin
        $message->admin_response = $validated['admin_response'];
        $message->response_sent = true;
        $message->save();
        
        // Kirim email balasan
        try {
            Mail::to($message->email)->send(new AdminResponseMail($message));
            return redirect()->back()->with('success', 'Balasan berhasil dikirim ke pengirim.');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email balasan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}