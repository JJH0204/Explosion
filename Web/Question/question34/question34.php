<?php
header("Content-Type: application/json");

// POST 요청이 아닌 경우 접근 차단
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Forbidden"]);
    exit;
}

// Content-Type이 application/json이 아닌 경우 차단
if ($_SERVER["CONTENT_TYPE"] !== "application/json") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid content type"]);
    exit;
}

// 정답과 플래그
$correctAnswers = [
    "dllMainAddr" => "0x1000D02E",
    "dnsRequest" => "pics.practicalmalwareanalysis.com",
    "hostIndicator" => ["vmx32to64.exe", "SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", "vmx32to64.exe, SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run"],
    "networkIndicator" => "www.practicalmalwareanalysis.com"
];

$flag = "FLAG{M4lw4r3_4n4lys1s_Pr0}";

// 클라이언트로부터 입력 받기
$data = json_decode(file_get_contents("php://input"), true);

// 답안 검증
foreach ($data as $key => $value) {
    if ($key === "hostIndicator") {
        // hostIndicator는 세 가지 답 중 하나만 맞아도 정답
        // 1. vmx32to64.exe
        // 2. SOFTWARE\Microsoft\Windows\CurrentVersion\Run
        // 3. 둘 다 쓴 경우 (vmx32to64.exe, SOFTWARE\Microsoft\Windows\CurrentVersion\Run)
        if (!in_array($value, $correctAnswers[$key])) {
            echo json_encode(["status" => "error"]);
            exit;
        }
    } else {
        if ($value !== $correctAnswers[$key]) {
            echo json_encode(["status" => "error"]);
            exit;
        }
    }
}

// 모든 문제 정답
echo json_encode(["status" => "success", "flag" => $flag]);