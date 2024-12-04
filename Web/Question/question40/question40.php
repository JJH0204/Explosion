<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 기준일: 2024년 11월 26일, 문제 번호 968
$baseDate = new DateTime('2024-11-26 00:00:00', new DateTimeZone('Asia/Seoul'));
$baseNumber = 968;

// 현재 한국 시간
$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));

// 기준일로부터 지난 일수 계산
$diff = $now->diff($baseDate);
$daysPassed = $diff->invert ? $diff->days : -$diff->days;

// 현재 문제 번호 계산
$currentNumber = $baseNumber + $daysPassed;

$FLAG = "FLAG{W0rd_V3ct0r_S3m4nt1c_M4st3r}";

$url = 'https://semantle-ko.newsjel.ly/top_scores/' . $currentNumber;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
if(curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    $data = json_decode($response, true);
    if ($data) {
        if (isset($_GET['word']) && $_GET['word'] === $data['key']) {
            $data['flag'] = $FLAG;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Invalid response']);
    }
}

curl_close($ch);
?>