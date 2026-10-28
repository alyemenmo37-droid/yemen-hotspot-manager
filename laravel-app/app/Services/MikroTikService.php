<?php

namespace App\Services;

use App\Models\MikrotikLog;
use App\Services\Mikrotik\MockManager;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;

class MikroTikService
{
    private bool $mockMode;

    private string $host;

    private int $port;

    private string $username;

    private string $password;

    private int $timeout;

    private MockManager $mock;

    public function __construct()
    {
        $this->host = config('mikrotik.host');
        $this->port = config('mikrotik.port');
        $this->username = config('mikrotik.username');
        $this->password = config('mikrotik.password');
        $this->timeout = config('mikrotik.timeout');
        $this->mockMode = config('mikrotik.mock');
        $this->mock = new MockManager;
    }

    public function isMock(): bool
    {
        return $this->mockMode;
    }

    // ═══════════════════════════════════════
    // TEST
    // ═══════════════════════════════════════
    public function testConnection(): array
    {
        if ($this->mockMode) {
            return [
                'success' => true,
                'mode' => 'mock',
                'message' => '🟡 وضع المحاكاة',
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
                'message' => '✅ متصل',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'mode' => 'real',
                'message' => '❌ '.$e->getMessage(),
            ];
        }
    }

    // ═══════════════════════════════════════
    // ADD USER
    // ═══════════════════════════════════════
    public function addUser(string $cardNumber, string $speed, string $duration = '1d'): array
    {
        if ($this->mockMode) {
            $result = $this->mock->addUser($cardNumber, $cardNumber, [
                'rate-limit' => $speed,
                'limit-uptime' => $duration,
            ]);

            $this->log('add_user', $cardNumber, true, 'محاكاة');

            return [
                'success' => true,
                'mode' => 'mock',
                'message' => "✅ {$cardNumber} (محاكاة)",
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
                'rate-limit' => $speed,
                'comment' => 'YHM',
            ])->read();

            $client->disconnect();

            return ['success' => true, 'mode' => 'real', 'message' => "✅ {$cardNumber}"];
        } catch (\Throwable $e) {
            Log::error('MikroTik addUser', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // REMOVE
    // ═══════════════════════════════════════
    public function removeUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            return $this->mock->removeUser($cardNumber);
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

            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // DISCONNECT ACTIVE
    // ═══════════════════════════════════════

    // ═══════════════════════════════════════
    // ACTIVE SESSIONS
    // ═══════════════════════════════════════
    public function getActiveSessions(): array
    {
        if ($this->mockMode) {
            // محاكاة نشاط عشوائي
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

    /**
     * إنشاء جلسة (تسجيل دخول)
     */
    public function connectUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            $result = $this->mock->createSession($cardNumber);
            $this->log('connect', $cardNumber, $result['success'], $result['message'] ?? 'نجح');

            return $result;
        }

        // Real: MikroTik ينشئ الجلسات تلقائياً عند تسجيل الدخول
        return ['success' => false, 'message' => 'غير مدعوم في Real Mode (يتم تلقائياً)'];
    }

    /**
     * فصل جلسة
     */
    public function disconnectUser(string $cardNumber): array
    {
        if ($this->mockMode) {
            $result = $this->mock->disconnectSession($cardNumber);
            $this->log('disconnect', $cardNumber, $result['success'], $result['message'] ?? '');

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

            $client->query('/ip/hotspot/active/remove', ['.id' => $sessions[0]['.id']])->read();
            $client->disconnect();

            return ['success' => true, 'message' => "تم فصل {$cardNumber}"];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // CHANGE SPEED
    // ═══════════════════════════════════════
    public function changeSpeed(string $cardNumber, string $newSpeed): array
    {
        if ($this->mockMode) {
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

            return ['success' => true, 'message' => "السرعة: $newSpeed"];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ═══════════════════════════════════════
    // SYSTEM INFO
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
    // CLIENT
    // ═══════════════════════════════════════
    private function getClient()
    {
        return new Client([
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->username,
            'pass' => $this->password,
            'timeout' => $this->timeout,
        ]);
    }

    private function log(string $action, ?string $card, bool $success, string $message): void
    {
        MikrotikLog::create([
            'action' => $action,
            'card_number' => $card,
            'status' => $success ? 'success' : 'failed',
            'message' => $message,
            'mode' => $this->mockMode ? 'mock' : 'real',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
