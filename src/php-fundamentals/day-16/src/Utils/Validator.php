<?php
declare(strict_types=1);

namespace Yhm\Utils;

class Validator
{
    public static function cardNumber(string $input): string
    {
        $cleaned = trim($input);
        if (!preg_match('/^\d{10}$/', $cleaned)) {
            throw new \InvalidArgumentException('رقم البطاقة: 10 أرقام');
        }
        return $cleaned;
    }
    
    public static function amount(mixed $input): int
    {
        $value = filter_var($input, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 1000000],
        ]);
        if ($value === false) {
            throw new \InvalidArgumentException('السعر: رقم صحيح موجب');
        }
        return $value;
    }
    
    public static function speed(string $input): string
    {
        $allowed = ['512k/512k', '256k/1M', '512k/2M', '2M/15M'];
        if (!in_array($input, $allowed, true)) {
            throw new \InvalidArgumentException('السرعة غير صالحة');
        }
        return $input;
    }
}
