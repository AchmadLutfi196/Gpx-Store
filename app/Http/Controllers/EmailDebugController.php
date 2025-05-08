<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailDebugController extends Controller
{
    public function debug()
    {
        $config = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => '********', // Hidden for security
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
            'QUEUE_CONNECTION' => config('queue.default'),
        ];
        
        return response()->json([
            'email_config' => $config,
            'php_extensions' => [
                'openssl' => extension_loaded('openssl'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'curl' => extension_loaded('curl'),
            ]
        ]);
    }
}
