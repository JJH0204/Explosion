<?php
header('Content-Type: application/json');

// 정답 ACL (순서를 강제하는 첫 번째와 마지막 줄)
$requiredFirstLine = "permit tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www";
$requiredLastLine = "permit ip any any";

// 나머지 정답 ACL (순서는 상관없음)
$correctRules = [
    "deny tcp 150.150.96.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny tcp 150.150.160.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo-reply",
    "deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo-reply",
    "deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo-reply"
];

// POST 데이터 가져오기
$data = json_decode(file_get_contents('php://input'), true);
$userACL = trim($data['acl'] ?? '');

// 입력값을 줄 단위로 분리
$userLines = explode("\n", $userACL);
$userLines = array_map('trim', $userLines); // 공백 제거

// 입력값 검증
if (empty($userLines) || count($userLines) < 2) {
    echo json_encode(["status" => "error", "message" => "ACL 규칙을 입력하세요."]);
    exit;
}

// 첫 번째 줄 검증
if ($userLines[0] !== $requiredFirstLine) {
    echo json_encode(["status" => "error", "message" => "첫 번째 줄이 올바르지 않습니다."]);
    exit;
}

// 마지막 줄 검증
if ($userLines[count($userLines) - 1] !== $requiredLastLine) {
    echo json_encode(["status" => "error", "message" => "마지막 줄이 올바르지 않습니다."]);
    exit;
}

// 중간 줄 검증 (순서 상관없음)
$middleLines = array_slice($userLines, 1, -1); // 첫 번째와 마지막 줄 제거
$remainingRules = $correctRules;

foreach ($middleLines as $line) {
    if (($key = array_search($line, $remainingRules)) !== false) {
        unset($remainingRules[$key]); // 매칭된 규칙 제거
    } else {
        echo json_encode(["status" => "error", "message" => "중간 줄에 올바르지 않은 규칙이 포함되어 있습니다."]);
        exit;
    }
}

// 모든 검증이 통과된 경우 플래그 반환
if (empty($remainingRules)) {
    echo json_encode(["status" => "success", "flag" => "FLAG{ACL_RULES_CORRECT}"]);
} else {
    echo json_encode(["status" => "error", "message" => "중간 줄 규칙이 부족합니다."]);
}
?>
