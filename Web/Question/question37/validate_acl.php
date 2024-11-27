<?php
header('Content-Type: application/json');

// ACL 정책: permit이 deny보다 먼저 나와야 하며 마지막 줄은 permit ip any any여야 함
$requiredLastLine = "permit ip any any";
$permitRules = [
    "permit icmp host 192.168.30.120 host 192.168.10.110 echo",
    "permit icmp host 192.168.30.120 host 192.168.10.110 echo-reply",
    "permit tcp host 192.168.30.120 host 192.168.10.110 eq www",
    "permit tcp host 192.168.30.110 host 192.168.10.110 eq ftp"
];
$denyRules = [
    "deny icmp host 192.168.30.100 host 192.168.10.100 echo",
    "deny icmp host 192.168.30.100 host 192.168.10.100 echo-reply",
    "deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo",
    "deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo-reply"
];

// POST 데이터 가져오기
$data = json_decode(file_get_contents('php://input'), true);
$userACL = trim($data['acl'] ?? '');

// ACL 규칙을 줄 단위로 분리
$userLines = explode("\n", $userACL);
$userLines = array_map('trim', $userLines);

// 입력값 검증
if (empty($userLines) || count($userLines) < count($permitRules) + count($denyRules) + 1) {
    echo json_encode(["status" => "error", "message" => "ACL 규칙이 부족합니다."]);
    exit;
}

// 마지막 줄 검증
if ($userLines[count($userLines) - 1] !== $requiredLastLine) {
    echo json_encode(["status" => "error", "message" => "마지막 줄이 올바르지 않습니다."]);
    exit;
}

// 허용 규칙 검증
$permitFound = [];
$denyFound = [];
$remainingPermitRules = $permitRules;
$remainingDenyRules = $denyRules;
$denyEncountered = false;

foreach ($userLines as $index => $line) {
    if ($line === $requiredLastLine) {
        continue; // 마지막 줄은 이미 검증됨
    }

    if (strpos($line, "permit") === 0) {
        if ($denyEncountered) {
            echo json_encode(["status" => "error", "message" => "permit 규칙이 deny 규칙 이후에 위치할 수 없습니다."]);
            exit;
        }

        if (($key = array_search($line, $remainingPermitRules)) !== false) {
            $permitFound[] = $line;
            unset($remainingPermitRules[$key]);
        } else {
            echo json_encode(["status" => "error", "message" => "허용 규칙이 올바르지 않습니다."]);
            exit;
        }
    } elseif (strpos($line, "deny") === 0) {
        $denyEncountered = true;

        if (($key = array_search($line, $remainingDenyRules)) !== false) {
            $denyFound[] = $line;
            unset($remainingDenyRules[$key]);
        } else {
            echo json_encode(["status" => "error", "message" => "차단 규칙이 올바르지 않습니다."]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "잘못된 ACL 명령어가 포함되어 있습니다."]);
        exit;
    }
}

// 모든 허용 및 차단 규칙이 포함되었는지 확인
if (!empty($remainingPermitRules) || !empty($remainingDenyRules)) {
    echo json_encode(["status" => "error", "message" => "모든 규칙이 올바르게 포함되지 않았습니다."]);
    exit;
}

// 모든 검증이 통과된 경우 플래그 반환
echo json_encode(["status" => "success", "flag" => "FLAG{ACL_CONFIGURATION_CORRECT}"]);
?>
