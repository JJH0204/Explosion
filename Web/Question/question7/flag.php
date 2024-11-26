<?php
header('Content-Type: application/json');

// 플래그를 서버에서 처리
$flag = "FLAG{find_the_O_success}";
echo json_encode(["flag" => $flag]);
?>
