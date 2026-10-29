<?php

namespace App\Services\Radius;

use App\Models\Card;
use Illuminate\Support\Facades\DB;

class RadiusService
{
    /**
     * إضافة بطاقة إلى RADIUS
     */
    public function addUser(string $cardNumber, string $speed, int $durationMinutes = 1440): array
    {
        try {
            DB::beginTransaction();
            
            // 1. بيانات المصادقة (username + password)
            DB::table('radcheck')->insert([
                'username' => $cardNumber,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $cardNumber,
            ]);
            
            // 2. سرعة (Rate-Limit)
            DB::table('radreply')->insert([
                'username' => $cardNumber,
                'attribute' => 'Mikrotik-Rate-Limit',
                'op' => ':=',
                'value' => $speed,
            ]);
            
            // 3. مدة (Session-Timeout)
            $seconds = $durationMinutes * 60;
            DB::table('radreply')->insert([
                'username' => $cardNumber,
                'attribute' => 'Session-Timeout',
                'op' => ':=',
                'value' => (string) $seconds,
            ]);
            
            // 4. حد البيانات (إذا وُجد)
            $card = Card::where('number', $cardNumber)->first();
            if ($card && $card->data_limit_mb) {
                $bytes = $card->data_limit_mb * 1024 * 1024;
                DB::table('radreply')->insert([
                    'username' => $cardNumber,
                    'attribute' => 'Mikrotik-Total-Limit',
                    'op' => ':=',
                    'value' => (string) $bytes,
                ]);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "RADIUS: {$cardNumber}",
            ];
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * حذف من RADIUS
     */
    public function removeUser(string $cardNumber): array
    {
        try {
            DB::beginTransaction();
            
            DB::table('radcheck')->where('username', $cardNumber)->delete();
            DB::table('radreply')->where('username', $cardNumber)->delete();
            DB::table('radusergroup')->where('username', $cardNumber)->delete();
            
            DB::commit();
            
            return ['success' => true];
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * جلب بيانات المستخدم
     */
    public function getUser(string $cardNumber): ?array
    {
        $check = DB::table('radcheck')
            ->where('username', $cardNumber)
            ->first();
        
        if (!$check) {
            return null;
        }
        
        $replies = DB::table('radreply')
            ->where('username', $cardNumber)
            ->get()
            ->pluck('value', 'attribute')
            ->toArray();
        
        return [
            'username' => $check->username,
            'password' => $check->value,
            'replies' => $replies,
        ];
    }
    
    /**
     * جلب استهلاك المستخدم
     */
    public function getUsage(string $cardNumber): array
    {
        $rows = DB::table('radacct')
            ->where('username', $cardNumber)
            ->get();
        
        $totalIn = $rows->sum('acctinputoctets');
        $totalOut = $rows->sum('acctoutputoctets');
        
        return [
            'sessions' => $rows->count(),
            'bytes_in' => $totalIn,
            'bytes_out' => $totalOut,
            'total_mb' => round(($totalIn + $totalOut) / 1024 / 1024, 2),
        ];
    }
/**
 * جلب المستخدمين المنتهين (المفروض فصلهم)
 */
public function getExpiredSessions(): array
{
    return DB::table('radacct')
        ->whereNull('acctstoptime')
        ->where('acctstarttime', '<', now()->subHours(24))
        ->get()
        ->toArray();
}

/**
 * جلب المستخدمين الذين تجاوزوا حد البيانات
 */
public function getOverQuotaUsers(): array
{
    return DB::table('radacct')
        ->whereNull('acctstoptime')
        ->select('username')
        ->selectRaw('SUM(acctinputoctets + acctoutputoctets) AS total_usage')
        ->groupBy('username')
        ->havingRaw('SUM(acctinputoctets + acctoutputoctets) > (
            SELECT CAST(value AS UNSIGNED) 
            FROM radreply 
            WHERE radreply.username = radacct.username 
            AND radreply.attribute = "Mikrotik-Total-Limit"
        )')
        ->get()
        ->toArray();
}

/**
 * إنهاء جلسة (من RADIUS side)
 */
public function closeSession(string $acctSessionId): void
{
    DB::table('radacct')
        ->where('acctsessionid', $acctSessionId)
        ->whereNull('acctstoptime')
        ->update([
            'acctstoptime' => now(),
            'acctterminatecause' => 'Admin-Reset',
        ]);
}

/**
 * مزامنة استهلاك البيانات من radacct → cards
 * 
 * @return array إحصائيات
 */
public function syncDataUsage(): array
{
    $stats = [
        'updated' => 0,
        'exceeded' => 0,
        'errors' => 0,
    ];
    
    // ═══════════════════════════════════════
    // 1. جلب مجموع الاستهلاك لكل مستخدم
    // ═══════════════════════════════════════
    $usage = DB::table('radacct')
        ->select('username')
        ->selectRaw('
            COALESCE(SUM(acctinputoctets), 0) AS total_in,
            COALESCE(SUM(acctoutputoctets), 0) AS total_out,
            COALESCE(SUM(acctinputoctets + acctoutputoctets), 0) AS total_bytes
        ')
        ->whereNotNull('username')
        ->where('username', '!=', '')
        ->groupBy('username')
        ->get();
    
    // ═══════════════════════════════════════
    // 2. تحديث كل بطاقة
    // ═══════════════════════════════════════
    foreach ($usage as $row) {
        try {
            $usedMb = (int) round($row->total_bytes / 1024 / 1024);
            
            $card = \App\Models\Card::where('number', $row->username)->first();
            
            if (!$card) {
                continue;
            }
            
            // تحديث الاستهلاك
            $oldUsed = $card->data_used_mb;
            $card->update(['data_used_mb' => $usedMb]);
            $stats['updated']++;
            
            // ═══════════════════════════════════════
            // 3. فحص التجاوز
            // ═══════════════════════════════════════
            if ($card->data_limit_mb > 0 && $usedMb >= $card->data_limit_mb) {
                $stats['exceeded']++;
                
                // سجل التجاوز
                \Log::warning('Data limit exceeded', [
                    'card' => $card->number,
                    'limit_mb' => $card->data_limit_mb,
                    'used_mb' => $usedMb,
                ]);
                
                // إرسال CoA Disconnect
                $coa = new CoaClient();
                $coa->disconnect($card->number);
                
                // تغيير الحالة
                $card->update(['status' => \App\Models\Card::STATUS_EXPIRED]);
            }
            
        } catch (\Throwable $e) {
            $stats['errors']++;
            \Log::error('Sync usage error', [
                'username' => $row->username,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    return $stats;
}

/**
 * جلب أعلى المستهلكين
 */
public function getTopConsumers(int $limit = 10): array
{
    return DB::table('radacct')
        ->select('username')
        ->selectRaw('
            SUM(acctinputoctets + acctoutputoctets) AS total_bytes,
            COUNT(*) AS sessions_count
        ')
        ->whereNotNull('username')
        ->where('username', '!=', '')
        ->groupBy('username')
        ->orderByDesc('total_bytes')
        ->limit($limit)
        ->get()
        ->toArray();
}

/**
 * جلب إحصائيات عامة
 */
public function getOverallStats(): array
{
    $stats = DB::table('radacct')
        ->selectRaw('
            COUNT(*) AS total_sessions,
            COUNT(DISTINCT username) AS unique_users,
            COALESCE(SUM(acctinputoctets), 0) AS total_in,
            COALESCE(SUM(acctoutputoctets), 0) AS total_out,
            COALESCE(SUM(acctinputoctets + acctoutputoctets), 0) AS total_bytes
        ')
        ->first();
    
    return [
        'total_sessions' => (int) $stats->total_sessions,
        'unique_users' => (int) $stats->unique_users,
        'total_gb' => round($stats->total_bytes / 1024 / 1024 / 1024, 2),
        'download_gb' => round($stats->total_in / 1024 / 1024 / 1024, 2),
        'upload_gb' => round($stats->total_out / 1024 / 1024 / 1024, 2),
    ];
}
}
