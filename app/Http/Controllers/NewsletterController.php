<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
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
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak valid.'
            ], 422);
        }

        $email = $request->email;

        // Check if email already exists
        $existingSubscriber = NewsletterSubscriber::where('email', $email)->first();
        if ($existingSubscriber) {
            if ($existingSubscriber->confirmed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ini sudah terdaftar dalam newsletter kami.'
                ], 422);
            } else {
                // Email exists but not confirmed
                return response()->json([
                    'success' => true,
                    'needConfirmation' => true,
                    'message' => 'Apakah Anda yakin ingin berlangganan newsletter kami?'
                ]);
            }
        }

        try {
            // Create a new pending subscription
            NewsletterSubscriber::create([
                'email' => $email,
                'confirmed' => false,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'needConfirmation' => true,
                'message' => 'Apakah Anda yakin ingin berlangganan newsletter kami?'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Confirm newsletter subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'confirm' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak valid.'
            ], 422);
        }

        $email = $request->email;
        $confirm = $request->confirm;

        if (!$confirm) {
            // User declined subscription
            NewsletterSubscriber::where('email', $email)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas pertimbangan Anda.'
            ]);
        }

        try {
            // Find or create subscriber and confirm
            $subscriber = NewsletterSubscriber::updateOrCreate(
                ['email' => $email],
                [
                    'confirmed' => true, 
                    'status' => 'active',
                    'confirmed_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Email Anda telah berhasil terdaftar untuk newsletter kami.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }
}
