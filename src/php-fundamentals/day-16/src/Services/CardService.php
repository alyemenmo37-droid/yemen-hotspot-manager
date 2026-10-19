<?php
declare(strict_types=1);

namespace Yhm\Services;

use Yhm\Models\Card;
use Yhm\Utils\Str;
use Yhm\Utils\Validator;

class CardService
{
    private array $cards = [];
    
    public function add(string $number, int $amount, string $speed): Card
    {
        // التحقق
        Validator::cardNumber($number);
        Validator::amount($amount);
        Validator::speed($speed);
        
        // تحقق من التكرار
        foreach ($this->cards as $c) {
            if ($c->number === $number) {
                throw new \RuntimeException("البطاقة {$number} موجودة");
            }
        }
        
        $card = new Card(
            id: count($this->cards) + 1,
            number: $number,
            amount: $amount,
            speed: $speed,
        );
        
        $this->cards[] = $card;
        return $card;
    }
    
    public function generate(int $count, int $amount, string $speed): array
    {
        $cards = [];
        for ($i = 0; $i < $count; $i++) {
            do {
                $number = Str::random(10);
            } while ($this->find($number) !== null);
            
            $cards[] = $this->add($number, $amount, $speed);
        }
        return $cards;
    }
    
    public function find(string $number): ?Card
    {
        foreach ($this->cards as $c) {
            if ($c->number === $number) return $c;
        }
        return null;
    }
    
    public function all(): array
    {
        return $this->cards;
    }
}
