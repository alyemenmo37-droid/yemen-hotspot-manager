<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $to, string $message): bool
    {
        // محاكاة إرسال رسالة واتساب
        Log::channel('single')->info("📱 WhatsApp to {$to}: {$message}");

        // في الإنتاج، استخدم UltraMsg أو Twilio هنا
        return true;
    }
}
