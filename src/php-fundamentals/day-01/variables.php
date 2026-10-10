<?php
// المتغيرات في PHP
$name = "محمد";
$age = 25;
$city = "صنعاء";
$isStudent = true;

echo "الاسم: $name\n";
echo "العمر: $age سنة\n";
echo "المدينة: $city\n";
echo "طالب: " . ($isStudent ? "نعم" : "لا") . "\n";

// العمليات الحسابية
$price = 250;
$quantity = 3;
$total = $price * $quantity;

echo "\nالمجموع: $total ريال\n";
?>
