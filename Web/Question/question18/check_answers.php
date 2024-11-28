<?php
header("Content-Type: application/json");

// 정답과 플래그
$correctAnswers = [
    "subnet1" => "192.168.1.0-192.168.1.63",
    "subnet2" => "192.168.1.64-192.168.1.127",
    "subnet3" => "192.168.1.128-192.168.1.191",
    "subnet4" => "192.168.1.192-192.168.1.255",
    "host1" => "192.168.1.1-192.168.1.62",
    "host2" => "192.168.1.65-192.168.1.126",
    "host3" => "192.168.1.129-192.168.1.190",
    "host4" => "192.168.1.193-192.168.1.254"
];

$flag = "FLAG{SUBNETTING_MASTER}";

// 클라이언트 데이터 가져오기
$data = json_decode(file_get_contents("php://input"), true);

// 정답 검증
foreach ($correctAnswers as $key => $value) {
    if (!isset($data[$key]) || $data[$key] !== $value) {
        echo json_encode(["success" => false]);
        exit;
    }
}

// 모든 정답이 맞으면 플래그 반환
echo json_encode(["success" => true, "flag" => $flag]);
