<?php

namespace App\Services;

use App\Models\MikrotikLog;
use App\Services\Mikrotik\MockManager;
use App\Services\Radius\RadiusService;
use Illuminate\Support\Facades\Log;

class MikroTikService
{
    private bool $mockMode;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private int $timeout;
    private MockManager $mock;
    private RadiusService $radius;

    public function __construct()
    {
        // تحميل الإعدادات
        $this->host = config('mikrotik.host');
        $this->port = config('mikrotik.port');
        $this->username = config('mikrotik.username');
        $this->password = config('mikrotik.password');
        $this->timeout = config('mikrotik.timeout');
        $this->mockMode = config('mikrotik.mock');

        // تهيئة الخدمات
        $this->mock = new MockManager();
        $this->radius = new RadiusService();
    }

    /**
     * هل النظام في وضع المحاكاة؟
     */
    public function isMock(): bool
    {
        return $this->mockMode;
    }

    // ═══════════════════════════════════════
    // 1. اختبار الاتصال
    // ═══════════════════════════════════════
    public function testConnection(): array
    {
        if ($this->mockMode) {
            return [
                'success' => true,
                'mode' => 'mock',
                'message' => '🟡 وضع المحاكاة (Mock Mode)',
                'users_count' => $this->mock->getUsersCount(),
            ];
        }

        try {
            $client = $this->getClient();
            $client->connect();
            $client->query('/system/identity/print')->read();
            $client->disconnect();

            return [
                'success' => true,
                'mode' => 'real',
                'message' => '✅ متصل بـ MikroTik',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'mode' => 'real',
                'message' => '❌ ' . $e->getMessage(),
            ];
        }
    }

    // ═══════════════════════════════════════
    // 2. إضافة مستخدم (بطاقة)
    // ═══════════════════════════════════════
    public function addUser(string $cardNumber, string $speed, string $duration = '1d'): array
    {
        // 1. إضافة إلى RADIUS أولاً (المصادقة المركزية)
        $radiusResult = $this->radius->addUser($cardNumber, $speed, 1440);
        
        if (!$radiusResult['success']) {
            $this->log('add_user', $cardNumber, false, 'RADIUS: ' . $radiusResult['message']);
            return [
                'success' => false,
                'message' => 'فشل RADIUS: ' . $radiusResult['message'],
            ];
        }

        // 2. في وضع المحاكاة
        if ($this->mockMode) {
            $result = $this->mock->addUser($cardNumber, $cardNumber, [
                'rate-limit' => $speed,
                'limit-uptime' => $duration,
            ]);

            $this->log('add_user', $cardNumber, true, 'Mock + RADIUS');

            return [
                'success' => true,
                'mode' => 'mock',
                'message' => "✅ {$cardNumber} (محاكاة + RADIUS)",
            ];
        }

        // 3. في الوضع الحقيقي
        try {
            $client = $this->getClient();
            $client->connect();

            $client->query('/ip/hotspot/user/add', [
                'name' => $cardNumber,
                'password' => $cardNumber,
                'profile' => config('mikrotik.hotspot.profile', 'default'),
                'limit-uptime' => $duration,
                'rate-limit' => $speed,
                'comment' => 'YHM',
            ])->read();

            $client->disconnect();

            $this->log('add_user', $cardNumber, true, 'Real + RADIUS');

            return [
                'success' => true,
                'mode' => 'real',
                'message' => "✅ {$cardNumber}",
            ];
        } catch (\Throwable $e) {
            $this->log('add_user', $cardNumber, false, $e->getMessage());
            Log::error('MikroTik addUser', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // ═══════════════════════════════════════
    // 3. حذف مستخدم
    // ═══════════════════════════════════════
    public function removeUser(string $cardNumber): array
    {
        // 1. حذف من RADIUS
        $this->radius->removeUser($cardNumber);

        // 2. حذف من MikroTik (Mock أو Real)
        if ($this->mockMode) {
            $result = $this->mock->removeUser($cardNumber);
            $this->log('remove_user', $cardNumber, true, 'Mock + RADIUS');
            return $result;
        }

        try {
            $client = $this->getClient();
            $client->connect();

            $users = $client->query('/ip/hotspot/user/print', ['?name' => $cardNumber])->read();

            if (empty($users)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'غير موجود'];
            }

            $client->query('/ip/hotspot/user/remove', ['.id' => $users[0]['.id']])->read();
            $client->disconnect();

            $this->log('remove_user', $cardNumber, true, 'Real + RADIUS');
            return ['success' => true];

        } catch (\Throwable $e) {
            $this->log('remove_user', $cardNumber, false, $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // 4. إنشاء جلسة (تسجيل دخول)
    // ═══════════════════════════════════════
    public function connectUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            $result = $this->mock->createSession($cardNumber);
            $this->log(
                'connect',
                $cardNumber,
                $result['success'],
                $result['message'] ?? 'تم الاتصال'
            );
            return $result;
        }

        // في الوضع الحقيقي، MikroTik ينشئ الجلسة تلقائياً
        return ['success' => false, 'message' => 'Real Mode: الجلسة تُنشأ تلقائياً'];
    }

    // ═══════════════════════════════════════
    // 5. فصل جلسة
    // ═══════════════════════════════════════
    public function disconnectUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            $result = $this->mock->disconnectSession($cardNumber);
            $this->log(
                'disconnect',
                $cardNumber,
                $result['success'],
                $result['message'] ?? 'تم الفصل'
            );
            return $result;
        }

        try {
            $client = $this->getClient();
            $client->connect();

            $sessions = $client->query('/ip/hotspot/active/print', ['?user' => $cardNumber])->read();

            if (empty($sessions)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'غير متصل'];
            }

            // إزالة الجلسة (فصل)
            $client->query('/ip/hotspot/active/remove', ['.id' => $sessions[0]['.id']])->read();
            $client->disconnect();

            $this->log('disconnect', $cardNumber, true, 'Real');
            return ['success' => true, 'message' => "تم فصل {$cardNumber}"];

        } catch (\Throwable $e) {
            $this->log('disconnect', $cardNumber, false, $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // 6. جلب الجلسات النشطة
    // ═══════════════════════════════════════
    public function getActiveSessions(): array
    {
        if ($this->mockMode) {
            $this->mock->simulateTraffic();
            return array_values($this->mock->getSessions());
        }

        try {
            $client = $this->getClient();
            $client->connect();
            $sessions = $client->query('/ip/hotspot/active/print')->read();
            $client->disconnect();
            return $sessions;
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ═══════════════════════════════════════
    // 7. تغيير السرعة
    // ═══════════════════════════════════════
    public function changeSpeed(string $cardNumber, string $newSpeed): array
    {
        // تحديث في RADIUS
        \DB::table('radreply')
            ->where('username', $cardNumber)
            ->where('attribute', 'Mikrotik-Rate-Limit')
            ->update(['value' => $newSpeed]);

        if ($this->mockMode) {
            $this->log('change_speed', $cardNumber, true, "Mock: $newSpeed");
            return ['success' => true, 'message' => "⚡ $newSpeed (محاكاة)"];
        }

        try {
            $client = $this->getClient();
            $client->connect();

            $users = $client->query('/ip/hotspot/user/print', ['?name' => $cardNumber])->read();

            if (empty($users)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'غير موجود'];
            }

            $client->query('/ip/hotspot/user/set', [
                '.id' => $users[0]['.id'],
                'rate-limit' => $newSpeed,
            ])->read();

            $client->disconnect();

            $this->log('change_speed', $cardNumber, true, $newSpeed);
            return ['success' => true, 'message' => "السرعة: $newSpeed"];

        } catch (\Throwable $e) {
            $this->log('change_speed', $cardNumber, false, $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // 8. معلومات النظام
    // ═══════════════════════════════════════
    public function getSystemInfo(): array
    {
        if ($this->mockMode) {
            return [
                'identity' => 'YHM-Mock-Router',
                'version' => '7.12 (mock)',
                'uptime' => '5d 12h',
                'cpu-load' => '15',
                'free-memory' => '250 MB',
                'total-memory' => '512 MB',
                'users_count' => $this->mock->getUsersCount(),
            ];
        }

        try {
            $client = $this->getClient();
            $client->connect();

            $resource = $client->query('/system/resource/print')->read();
            $identity = $client->query('/system/identity/print')->read();
            $users = $client->query('/ip/hotspot/user/print')->read();

            $client->disconnect();

            return array_merge(
                $resource[0] ?? [],
                [
                    'identity' => $identity[0]['name'] ?? 'MikroTik',
                    'users_count' => count($users),
                ]
            );
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ═══════════════════════════════════════
    // 9. إنشاء اتصال (Real Mode)
    // ═══════════════════════════════════════
    private function getClient()
    {
        return new \RouterOS\Client([
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->username,
            'pass' => $this->password,
            'timeout' => $this->timeout,
        ]);
    }

    // ═══════════════════════════════════════
    // 10. تسجيل العمليات في قاعدة البيانات
    // ═══════════════════════════════════════
    private function log(string $action, ?string $card, bool $success, string $message): void
    {
        try {
            MikrotikLog::create([
                'action' => $action,
                'card_number' => $card,
                'status' => $success ? 'success' : 'failed',
                'message' => $message,
                'mode' => $this->mockMode ? 'mock' : 'real',
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log MikroTik action: ' . $e->getMessage());
        }
    }
}
