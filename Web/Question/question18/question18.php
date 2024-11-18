<?php
header("Content-Type: application/json");

// 정답과 플래그
$correctAnswers = [
    "subnetAStart" => "192.168.0.0",
    "subnetAEnd" => "192.168.0.63",
    "subnetBStart" => "192.168.0.64",
    "subnetBEnd" => "192.168.0.95",
    "subnetCStart" => "192.168.0.96",
    "subnetCEnd" => "192.168.0.111",
    "subnetDStart" => "192.168.0.112",
    "subnetDEnd" => "192.168.0.127",
    "subnet3" => "/26, /27, /28, /28"
];

$flag = "FLAG{SUBNETTING_SUCCESS}";

// IP 범위 검증 함수
function isIpInRange($ip, $startIp, $endIp) {
    $ipToNum = function($ip) {
        return array_reduce(explode('.', $ip), function($acc, $octet) {
            return ($acc << 8) + (int)$octet;
        }, 0);
    };

    $ipNum = $ipToNum($ip);
    $startNum = $ipToNum($startIp);
    $endNum = $ipToNum($endIp);

    return $ipNum >= $startNum && $ipNum <= $endNum;
}

// 클라이언트로부터 입력 받기
$data = json_decode(file_get_contents("php://input"), true);

// 문제 2 범위 검증
if (isset($data["subnet2"])) {
    $subnet2 = explode(",", $data["subnet2"]);
    if (count($subnet2) !== 2) {
        echo json_encode(["status" => "error"]);
        exit;
    }

    $ipA = trim($subnet2[0]);
    $ipB = trim($subnet2[1]);

    // Subnet A 유효 IP 범위: 192.168.0.1 ~ 192.168.0.62
    $subnetAValid = isIpInRange($ipA, "192.168.0.1", "192.168.0.62");

    // Subnet B 유효 IP 범위: 192.168.0.65 ~ 192.168.0.94
    $subnetBValid = isIpInRange($ipB, "192.168.0.65", "192.168.0.94");

    if (!$subnetAValid || !$subnetBValid) {
        echo json_encode(["status" => "error"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error"]);
    exit;
}

// 다른 문제 정답 검증
foreach ($correctAnswers as $key => $value) {
    if ($key !== "subnet2" && $data[$key] !== $value) {
        echo json_encode(["status" => "error"]);
        exit;
    }
}

// 모든 문제 정답
echo json_encode(["status" => "success", "flag" => $flag]);
