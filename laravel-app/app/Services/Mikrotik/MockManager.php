<?php

namespace App\Services\Mikrotik;

use App\Models\MikrotikLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MockManager
{
    private const USERS_KEY = 'mikrotik_mock_users';

    private const SESSIONS_KEY = 'mikrotik_mock_sessions';

    // ═══════════════════════════════════════
    // USERS (البطاقات في الراوتر)
    // ═══════════════════════════════════════

    public function addUser(string $name, string $password, array $options = []): array
    {
        $users = $this->getUsers();

        $users[$name] = [
            'name' => $name,
            'password' => $password,
            'rate-limit' => $options['rate-limit'] ?? '256k/1M',
            'profile' => $options['profile'] ?? 'default',
            'created-at' => now()->toDateTimeString(),
            'uptime-limit' => $options['limit-uptime'] ?? '1d',
        ];

        $this->saveUsers($users);

        return ['success' => true, 'user' => $users[$name]];
    }

    public function removeUser(string $name): array
    {
        // احذف المستخدم
        $users = $this->getUsers();
        if (! isset($users[$name])) {
            return ['success' => false, 'message' => 'المستخدم غير موجود'];
        }
        unset($users[$name]);
        $this->saveUsers($users);

        // احذف جلساته
        $this->disconnectSession($name);

        return ['success' => true];
    }

    public function getUser(string $name): ?array
    {
        return $this->getUsers()[$name] ?? null;
    }

    public function getUsers(): array
    {
        return Cache::get(self::USERS_KEY, []);
    }

    public function getUsersCount(): int
    {
        return count($this->getUsers());
    }

    public function clearAll(): int
    {
        $count = count($this->getUsers());
        Cache::forget(self::USERS_KEY);
        Cache::forget(self::SESSIONS_KEY);

        return $count;
    }

    // ═══════════════════════════════════════
    // SESSIONS (المتصلون الآن)
    // ═══════════════════════════════════════

    /**
     * إنشاء جلسة (تسجيل دخول)
     */
    public function createSession(string $name): array
    {
        $users = $this->getUsers();

        if (! isset($users[$name])) {
            return ['success' => false, 'message' => 'المستخدم غير موجود'];
        }

        $sessions = $this->getSessions();

        // إذا كان مسجل مسبقاً
        if (isset($sessions[$name])) {
            return ['success' => false, 'message' => 'مسجل دخول مسبقاً'];
        }

        $ip = '192.168.88.'.(100 + count($sessions));

        $sessions[$name] = [
            'user' => $name,
            'address' => $ip,
            'mac-address' => sprintf('AA:BB:CC:DD:EE:%02X', count($sessions) + 1),
            'uptime' => '0m',
            'bytes-in' => 0,
            'bytes-out' => 0,
            'connected-at' => now()->toDateTimeString(),
            'last-activity' => now()->toDateTimeString(),
        ];

        Cache::put(self::SESSIONS_KEY, $sessions, now()->addDays(7));

        return ['success' => true, 'session' => $sessions[$name]];
    }

    /**
     * إنهاء جلسة (تسجيل خروج)
     */
    public function disconnectSession(string $name): array
    {
        $sessions = $this->getSessions();

        if (! isset($sessions[$name])) {
            return ['success' => false, 'message' => 'غير متصل'];
        }

        $session = $sessions[$name];
        unset($sessions[$name]);
        Cache::put(self::SESSIONS_KEY, $sessions, now()->addDays(7));

        // سجل في audit log
        MikrotikLog::create([
            'action' => 'session_end',
            'card_number' => $name,
            'status' => 'success',
            'message' => 'استهلك: '.round(($session['bytes-in'] + $session['bytes-out']) / 1024 / 1024, 2).' MB',
            'mode' => 'mock',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return ['success' => true, 'session' => $session];
    }

    public function getSessions(): array
    {
        return Cache::get(self::SESSIONS_KEY, []);
    }

    public function getSessionsCount(): int
    {
        return count($this->getSessions());
    }

    /**
     * محاكاة استهلاك البيانات (تحديث عشوائي)
     */
    public function simulateTraffic(): void
    {
        $sessions = $this->getSessions();

        foreach ($sessions as &$s) {
            // زيادة عشوائية بين 0.1 و 2 MB
            $delta = rand(100000, 2000000);
            $s['bytes-in'] += $delta;
            $s['bytes-out'] += (int) ($delta / 10);
            $s['uptime'] = now()->diffInMinutes(Carbon::parse($s['connected-at'])).'m';
            $s['last-activity'] = now()->toDateTimeString();
        }
        unset($s);

        Cache::put(self::SESSIONS_KEY, $sessions, now()->addDays(7));
    }

    // ═══════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════

    private function saveUsers(array $users): void
    {
        Cache::put(self::USERS_KEY, $users, now()->addDays(30));
    }
}
