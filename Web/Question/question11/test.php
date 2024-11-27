<?php
// predict.php 파일을 만들어서 실행
$timeSlot = 9626222;
mt_srand($timeSlot);

$numbers = [];
while (count($numbers) < 6) {
    $num = mt_rand(1, 45);
    if (!in_array($num, $numbers)) {
        $numbers[] = $num;
    }
}
sort($numbers);
print_r($numbers);
?>