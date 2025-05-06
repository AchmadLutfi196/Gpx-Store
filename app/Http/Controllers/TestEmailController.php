<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    public function sendTestEmail()
    {
        try {
            Mail::raw('This is a test email from GPX Store', function($message) {
                $message->to('your-test-email@example.com')
                        ->subject('GPX Store Test Email');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Test email has been sent. Please check your inbox/spam folder.'
            ]);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }
}
