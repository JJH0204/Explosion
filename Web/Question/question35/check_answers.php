<?php
header('Content-Type: application/json');

// 정답 설정
$answers = [
    "question1" => "/etc/resolv.conf",
    "question2" => "/etc/services",
    "question3" => "ARP"
];

// 클라이언트에서 전송된 데이터 받기
$data = json_decode(file_get_contents('php://input'), true);

// 결과 검증
$isCorrect = true;
foreach ($answers as $key => $correctAnswer) {
    if (!isset($data[$key]) || strcasecmp(trim($data[$key]), $correctAnswer) !== 0) {
        $isCorrect = false;
        break;
    }
}

if ($isCorrect) {
    echo json_encode([
        "status" => "success",
        "flag" => "FLAG{linux_master}"
    ]);
} else {
    echo json_encode([
        "status" => "error"
    ]);
}
?>
