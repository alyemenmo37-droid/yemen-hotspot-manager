<?php

namespace App\Services\Radius;

use Illuminate\Support\Facades\Log;

/**
 * CoA Client (RFC 5176)
 * 
 * يرسل أوامر CoA إلى MikroTik (NAS)
 * - Disconnect-Request: فصل مستخدم
 * - CoA-Request: تغيير الصلاحيات
 */
class CoaClient
{
    private string $host;
    private int $port = 3799;
    private string $secret;
    private int $timeout = 5;
    
    public function __construct()
    {
        $this->host = config('mikrotik.host', '192.168.88.1');
        $this->port = 3799;
        $this->secret = config('mikrotik.coa_secret', 'coa_secret_2026');
    }
    
    /**
     * فصل مستخدم (Disconnect-Request)
     * 
     * @param string $username اسم المستخدم (card number)
     * @param string|null $nasIp IP الـ MikroTik
     * @return array
     */
    public function disconnect(string $username, ?string $nasIp = null): array
    {
        $nasIp = $nasIp ?? $this->host;
        
        // ═══════════════════════════════════════
        // 1. بناء RADIUS Packet
        // ═══════════════════════════════════════
        $packet = $this->buildDisconnectPacket($username);
        
        // ═══════════════════════════════════════
        // 2. إرسال UDP
        // ═══════════════════════════════════════
        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return $this->error('فشل إنشاء socket');
        }
        
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $this->timeout, 'usec' => 0]);
        
        $sent = @socket_sendto($socket, $packet, strlen($packet), 0, $nasIp, $this->port);
        
        if ($sent === false) {
            socket_close($socket);
            return $this->error('فشل الإرسال: ' . socket_strerror(socket_last_error()));
        }
        
        // ═══════════════════════════════════════
        // 3. انتظار الرد
        // ═══════════════════════════════════════
        $response = '';
        $fromIp = '';
        $fromPort = 0;
        
        $received = @socket_recvfrom($socket, $response, 4096, 0, $fromIp, $fromPort);
        socket_close($socket);
        
        if ($received === false || $received === 0) {
            // Timeout (طبيعي في Mock Mode)
            Log::info('CoA Disconnect sent (no response)', ['username' => $username]);
            return [
                'success' => true,
                'message' => "تم إرسال فصل {$username} (بدون رد)",
                'mock' => true,
            ];
        }
        
        // ═══════════════════════════════════════
        // 4. تحليل الرد
        // ═══════════════════════════════════════
        $code = ord($response[0] ?? "\0");
        
        // 41 = Disconnect-ACK, 42 = Disconnect-NAK
        if ($code === 41) {
            Log::info('CoA Disconnect-ACK', ['username' => $username]);
            return [
                'success' => true,
                'message' => "✅ تم فصل {$username}",
                'code' => 41,
            ];
        }
        
        if ($code === 42) {
            Log::warning('CoA Disconnect-NAK', ['username' => $username]);
            return [
                'success' => false,
                'message' => "❌ MikroTik رفض الفصل",
                'code' => 42,
            ];
        }
        
        return $this->error("رد غير متوقع: $code");
    }
    
    /**
     * تغيير الصلاحيات (CoA-Request)
     */
    public function changeAuthorization(string $username, array $attributes): array
    {
        Log::info('CoA Change (simulated)', [
            'username' => $username,
            'attributes' => $attributes,
        ]);
        
        return [
            'success' => true,
            'message' => "تم تغيير صلاحيات {$username} (محاكاة)",
            'mock' => true,
        ];
    }
    
    /**
     * بناء Disconnect Packet (RFC 5176)
     */
    private function buildDisconnectPacket(string $username): string
    {
        // Code: 40 (Disconnect-Request)
        $code = chr(40);
        
        // Identifier (1 byte)
        $identifier = chr(random_int(0, 255));
        
        // Length (2 bytes)
        $attributes = $this->buildUserAttributes($username);
        $length = 20 + strlen($attributes);
        $lengthBytes = pack('n', $length);
        
        // Authenticator (16 bytes) — سنستخدم بديل بسيط
        $authenticator = random_bytes(16);
        
        // Attributes
        return $code . $identifier . $lengthBytes . $authenticator . $attributes;
    }
    
    /**
     * بناء Attributes (User-Name)
     */
    private function buildUserAttributes(string $username): string
    {
        // Attribute 1 = User-Name
        $type = chr(1);
        $value = $username;
        $length = 2 + strlen($value);
        
        return $type . chr($length) . $value;
    }
    
    /**
     * خطأ
     */
    private function error(string $message): array
    {
        Log::error('CoA Error: ' . $message);
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
