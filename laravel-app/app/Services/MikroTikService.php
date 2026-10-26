<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MikroTikService
{
    private bool $mockMode;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private int $timeout;
    
    public function __construct()
    {
        $this->host = config('mikrotik.host');
        $this->port = config('mikrotik.port');
        $this->username = config('mikrotik.username');
        $this->password = config('mikrotik.password');
        $this->timeout = config('mikrotik.timeout');
        $this->mockMode = config('mikrotik.mock');
    }
    
    /**
     * اختبار الاتصال
     */
    public function testConnection(): array
    {
        if ($this->mockMode) {
            return [
                'success' => true,
                'mode' => 'mock',
                'message' => '🟡 وضع المحاكاة (Mock Mode)',
            ];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            $resource = $client->query('/system/resource/print')->read();
            $client->disconnect();
            
            return [
                'success' => true,
                'mode' => 'real',
                'message' => '✅ متصل بنجاح',
                'data' => $resource,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'mode' => 'real',
                'message' => '❌ ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * إضافة مستخدم hotspot
     */
    public function addUser(string $cardNumber, string $speed, string $duration = '1d'): array
    {
        // السرعة بصيغة MikroTik: rx/tx
        $rateLimit = $speed;
        
        if ($this->mockMode) {
            Log::info('MikroTik MOCK: Add user', [
                'name' => $cardNumber,
                'password' => $cardNumber,
                'rate_limit' => $rateLimit,
            ]);
            
            return [
                'success' => true,
                'mode' => 'mock',
                'message' => "تم إضافة {$cardNumber} (محاكاة)",
            ];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            $client->query('/ip/hotspot/user/add', [
                'name' => $cardNumber,
                'password' => $cardNumber,
                'profile' => config('mikrotik.hotspot.profile', 'default'),
                'limit-uptime' => $duration,
                'rate-limit' => $rateLimit,
                'comment' => 'YHM:' . now()->format('Y-m-d'),
                'server' => config('mikrotik.hotspot.server', 'all'),
            ])->read();
            
            $client->disconnect();
            
            Log::info('MikroTik: User added', ['card' => $cardNumber]);
            
            return [
                'success' => true,
                'mode' => 'real',
                'message' => "تم إضافة {$cardNumber}",
            ];
        } catch (\Throwable $e) {
            Log::error('MikroTik Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'mode' => 'real',
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * حذف مستخدم
     */
    public function removeUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            return ['success' => true, 'message' => "حذف {$cardNumber} (محاكاة)"];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            // ابحث عن المستخدم
            $users = $client->query('/ip/hotspot/user/print', [
                '?name' => $cardNumber,
            ])->read();
            
            if (empty($users)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'المستخدم غير موجود'];
            }
            
            // احذفه
            $client->query('/ip/hotspot/user/remove', [
                '.id' => $users[0]['.id'],
            ])->read();
            
            $client->disconnect();
            
            return ['success' => true, 'message' => "حذف {$cardNumber}"];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * فصل مستخدم نشط
     */
    public function disconnectUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            return ['success' => true, 'message' => "فصل {$cardNumber} (محاكاة)"];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            // ابحث عن الجلسة النشطة
            $sessions = $client->query('/ip/hotspot/active/print', [
                '?user' => $cardNumber,
            ])->read();
            
            if (empty($sessions)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'المستخدم غير متصل'];
            }
            
            // افصله
            $client->query('/ip/hotspot/active/remove', [
                '.id' => $sessions[0]['.id'],
            ])->read();
            
            $client->disconnect();
            
            return ['success' => true, 'message' => "تم فصل {$cardNumber}"];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * جلب المتصلين النشطين
     */
    public function getActiveSessions(): array
    {
        if ($this->mockMode) {
            // بيانات محاكاة
            return [
                [
                    'user' => '1234567890',
                    'address' => '192.168.88.100',
                    'mac-address' => 'AA:BB:CC:DD:EE:01',
                    'uptime' => '1h30m',
                    'bytes-in' => 10485760,
                    'bytes-out' => 2097152,
                ],
                [
                    'user' => '0987654321',
                    'address' => '192.168.88.101',
                    'mac-address' => 'AA:BB:CC:DD:EE:02',
                    'uptime' => '45m',
                    'bytes-in' => 5242880,
                    'bytes-out' => 1048576,
                ],
            ];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            $sessions = $client->query('/ip/hotspot/active/print')->read();
            
            $client->disconnect();
            
            return $sessions;
        } catch (\Throwable $e) {
            Log::error('MikroTik: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * تغيير سرعة مستخدم
     */
    public function changeSpeed(string $cardNumber, string $newSpeed): array
    {
        if ($this->mockMode) {
            return ['success' => true, 'message' => "تغيير السرعة إلى $newSpeed (محاكاة)"];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            $users = $client->query('/ip/hotspot/user/print', [
                '?name' => $cardNumber,
            ])->read();
            
            if (empty($users)) {
                $client->disconnect();
                return ['success' => false, 'message' => 'المستخدم غير موجود'];
            }
            
            $client->query('/ip/hotspot/user/set', [
                '.id' => $users[0]['.id'],
                'rate-limit' => $newSpeed,
            ])->read();
            
            $client->disconnect();
            
            return ['success' => true, 'message' => "تم تغيير السرعة"];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * جلب إحصائيات النظام
     */
    public function getSystemInfo(): array
    {
        if ($this->mockMode) {
            return [
                'uptime' => '5d 12h',
                'cpu-load' => '15',
                'free-memory' => '250000000',
                'total-memory' => '536870912',
                'version' => '7.12 (mock)',
            ];
        }
        
        try {
            $client = $this->getClient();
            $client->connect();
            
            $resource = $client->query('/system/resource/print')->read();
            $identity = $client->query('/system/identity/print')->read();
            
            $client->disconnect();
            
            return array_merge(
                $resource[0] ?? [],
                ['identity' => $identity[0]['name'] ?? 'MikroTik']
            );
        } catch (\Throwable $e) {
            return [];
        }
    }
    
    /**
     * إنشاء Client
     */
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
}
