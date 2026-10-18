<?php
/**
 * YHM - InputValidator
 */

declare(strict_types=1);

class InputValidator
{
    public static function cardNumber(string $input): string
    {
        $cleaned = trim($input);
        if (!preg_match('/^\d{10}$/', $cleaned)) {
            throw new InvalidArgumentException('رقم البطاقة: 10 أرقام بالضبط');
        }
        return $cleaned;
    }
    
    public static function amount(mixed $input): int
    {
        $value = filter_var($input, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 1000000],
        ]);
        if ($value === false) {
            throw new InvalidArgumentException('السعر: رقم صحيح موجب');
        }
        return $value;
    }
    
    public static function speed(string $input): string
    {
        $allowed = ['512k/512k', '256k/1M', '512k/2M', '2M/15M'];
        if (!in_array($input, $allowed, true)) {
            throw new InvalidArgumentException('السرعة غير مسموحة');
        }
        return $input;
    }
    
    public static function status(string $input): string
    {
        $allowed = ['unused', 'active', 'expired', 'stopped'];
        if (!in_array($input, $allowed, true)) {
            throw new InvalidArgumentException('الحالة غير صالحة');
        }
        return $input;
    }
    
    public static function integer(mixed $input, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int
    {
        $value = filter_var($input, FILTER_VALIDATE_INT);
        if ($value === false || $value < $min || $value > $max) {
            throw new InvalidArgumentException("رقم صحيح بين $min و $max");
        }
        return $value;
    }
}
