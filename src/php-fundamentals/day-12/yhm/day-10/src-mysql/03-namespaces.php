<?php
/**
 * Day 6 - Namespaces
 */

echo "╔" . str_repeat("═", 55) . "╗\n";
echo "║  📦 Namespaces في PHP                            ║\n";
echo "╚" . str_repeat("═", 55) . "╝\n\n";

// ═══════════════════════════════════════
// محاكاة Namespaces
// ═══════════════════════════════════════

// namespace App\Models;
class User
{
    public function __construct(public string $name) {}
    
    public function info(): string
    {
        return "👤 المستخدم: {$this->name}";
    }
}

// namespace App\Services;
class UserService
{
    public function create(string $name): User
    {
        return new User($name);
    }
    
    public function greet(User $user): string
    {
        return "مرحباً، " . $user->info();
    }
}


// ═══════════════════════════════════════
// الاستخدام
// ═══════════════════════════════════════

$service = new UserService();

$user1 = $service->create('محمد');
$user2 = $service->create('أحمد');
$user3 = $service->create('علي');

echo $service->greet($user1) . "\n";
echo $service->greet($user2) . "\n";
echo $service->greet($user3) . "\n";


// ═══════════════════════════════════════
// مثال واقعي: استخدام Namespaces
// ═══════════════════════════════════════
echo "\n📌 Namespace حقيقي (مثال):\n";
echo "  الكود التالي سيُستخدم في Laravel:\n\n";

echo <<<'CODE'
namespace App\Models;

class Card extends Model
{
    protected $fillable = ['number', 'amount', 'status'];
}

// في Controller:
use App\Models\Card;

$card = Card::find(1);
CODE;

echo "\n✅ اكتمل التمرين!\n";
