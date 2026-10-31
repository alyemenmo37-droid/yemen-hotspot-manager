<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $apiUrl;
    private bool $enabled;
    
    public function __construct()
    {
        $this->token = config('notifications.telegram.token') ?? '';
        $this->chatId = config('notifications.telegram.chat_id') ?? '';
        $this->apiUrl = config('notifications.telegram.api_url') . $this->token;
        $this->enabled = config('notifications.telegram.enabled') && !empty($this->token) && !empty($this->chatId);
    }
    
    /**
     * إرسال رسالة نصية
     */
    public function send(string $message, string $parseMode = 'HTML'): bool
    {
        if (!$this->enabled) {
            Log::info('Telegram disabled or not configured');
            return false;
        }
        
        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);
            
            if ($response->successful()) {
                Log::info('Telegram sent', ['message' => substr($message, 0, 50)]);
                return true;
            }
            
            Log::error('Telegram failed', ['response' => $response->body()]);
            return false;
            
        } catch (\Throwable $e) {
            Log::error('Telegram error', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * إشعار: بطاقة جديدة
     */
    public function notifyNewCard(string $number, int $amount, string $speed): bool
    {
        $message = "🎫 <b>بطاقة جديدة</b>\n\n"
            . "📇 الرقم: <code>{$number}</code>\n"
            . "💰 السعر: {$amount} ريال\n"
            . "⚡ السرعة: {$speed}\n"
            . "🕐 " . now()->format('Y-m-d H:i');
        
        return $this->send($message);
    }
    
    /**
     * إشعار: تجاوز حد البيانات
     */
    public function notifyLimitExceeded(string $number, int $limitMb, int $usedMb): bool
    {
        $percentage = $limitMb > 0 ? round($usedMb / $limitMb * 100, 1) : 0;
        
        $message = "⚠️ <b>تجاوز حد البيانات</b>\n\n"
            . "📇 البطاقة: <code>{$number}</code>\n"
            . "📊 الحد: {$limitMb} MB\n"
            . "📈 المستخدم: {$usedMb} MB ({$percentage}%)\n"
            . "🔌 تم الفصل تلقائياً\n"
            . "🕐 " . now()->format('Y-m-d H:i');
        
        return $this->send($message);
    }
    
    /**
     * إشعار: مخزون منخفض
     */
    public function notifyLowStock(int $count, int $threshold): bool
    {
        $message = "📦 <b>تنبيه المخزون</b>\n\n"
            . "📊 البطاقات المتاحة: <b>{$count}</b>\n"
            . "⚠️ أقل من الحد: {$threshold}\n\n"
            . "💡 يُنصح بإضافة بطاقات جديدة.";
        
        return $this->send($message);
    }
    
    /**
     * إشعار: مستخدم جديد
     */
    public function notifyNewUser(string $name, string $email, string $role): bool
    {
        $message = "👤 <b>مستخدم جديد</b>\n\n"
            . "📝 الاسم: {$name}\n"
            . "📧 البريد: {$email}\n"
            . "🎭 الدور: {$role}\n"
            . "🕐 " . now()->format('Y-m-d H:i');
        
        return $this->send($message);
    }
    
    /**
     * إشعار: فشل CoA
     */
    public function notifyCoaFailure(string $number, string $error): bool
    {
        $message = "🚨 <b>فشل CoA</b>\n\n"
            . "📇 البطاقة: <code>{$number}</code>\n"
            . "❌ الخطأ: {$error}\n"
            . "🕐 " . now()->format('Y-m-d H:i');
        
        return $this->send($message);
    }
    
    /**
     * اختبار الاتصال
     */
    public function test(): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'غير مفعل أو غير مضبوط'];
        }
        
        $success = $this->send(
            "🧪 <b>اختبار Telegram</b>\n\n"
            . "✅ YHM يعمل!\n"
            . "🕐 " . now()->format('Y-m-d H:i:s')
        );
        
        return [
            'success' => $success,
            'message' => $success ? '✅ تم الإرسال' : '❌ فشل',
        ];
    }
}
