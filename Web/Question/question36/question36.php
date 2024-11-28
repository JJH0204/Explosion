<?php
header('Content-Type: application/json');

// 정답 설정
$answers = [
    "question1" => ["Dual Stack", "듀얼스택", "DualStack", "듀얼 스택"], // 여러 표현 추가
    "question2" => "NAT",
    "question3" => "RIP"
];

// 클라이언트에서 전송된 데이터 받기
$data = json_decode(file_get_contents('php://input'), true);

// 대소문자와 공백을 무시하며 비교
$isCorrect = true;
foreach ($answers as $key => $correctAnswer) {
    if (is_array($correctAnswer)) {
        // 배열인 경우, 입력값이 배열 안의 어느 값과도 일치하는지 확인
        $isValid = false;
        foreach ($correctAnswer as $validAnswer) {
            if (strcasecmp(preg_replace('/\s+/', '', trim($data[$key])), preg_replace('/\s+/', '', $validAnswer)) === 0) {
                $isValid = true;
                break;
            }
        }
        if (!$isValid) {
            $isCorrect = false;
            break;
        }
    } else {
        // 단일 값인 경우 공백 제거 후 비교
        if (!isset($data[$key]) || strcasecmp(preg_replace('/\s+/', '', trim($data[$key])), preg_replace('/\s+/', '', $correctAnswer)) !== 0) {
            $isCorrect = false;
            break;
        }
    }
}

// 결과 반환
if ($isCorrect) {
    echo json_encode([
        "status" => "success",
        "flag" => "FLAG{network_admin_pro}"
    ]);
} else {
    echo json_encode([
        "status" => "error"
    ]);
}
?>
