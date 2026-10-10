<?php
// لعبة تصنيف العمر
$ages = [5, 15, 25, 40, 70];

foreach ($ages as $age) {
    echo "العمر $age: ";
    
    if ($age < 13) {
        echo "طفل\n";
    } elseif ($age < 20) {
        echo "مراهق\n";
    } elseif ($age < 60) {
        echo "بالغ\n";
    } else {
        echo "مسن\n";
    }
}
?>
