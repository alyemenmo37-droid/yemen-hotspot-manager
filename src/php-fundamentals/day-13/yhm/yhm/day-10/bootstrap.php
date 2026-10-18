<?php
/**
 * YHM - Bootstrap
 * تحميل الإعدادات و إنشاء PDO
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getPDO(): PDO
{
    static $pdo = null;
    
    if ($pdo === null) {
        $config = require __DIR__ . '/config.php';
        
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['database'],
            $config['charset']
        );
        
        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("فشل الاتصال: " . $e->getMessage());
        }
    }
    
    return $pdo;
}
