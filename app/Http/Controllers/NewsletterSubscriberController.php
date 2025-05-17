<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterWelcome;
use Illuminate\Support\Facades\Log;

class NewsletterSubscriberController extends Controller
{
    /**
     * Subscribe a new email to the newsletter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar atau tidak valid.'
            ], 422);
        }

        try {
            // Create a new newsletter subscription
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'status' => 'pending',
                'confirmed' => false
            ]);

            // Send welcome email
            try {
                Mail::to($request->email)->send(new NewsletterWelcome($request->email));
                Log::info('Newsletter welcome email sent to: ' . $request->email);
            } catch (\Exception $e) {
                Log::error('Failed to send newsletter welcome email: ' . $e->getMessage(), ['email' => $request->email]);
                // Continue execution even if email fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Email Anda berhasil didaftarkan untuk newsletter kami. Silakan periksa kotak masuk Anda untuk konfirmasi.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to subscribe to newsletter: ' . $e->getMessage(), ['email' => $request->email]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }
}
