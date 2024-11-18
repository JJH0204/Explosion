<?php
header('Content-Type: application/json');

$correctAnswers = [
    'task1Command1' => 'router ospf 1',
    'task1Command2' => 'network 192.168.1.0 0.0.0.255 area 0',
    'task2Command1' => 'redistribute ospf 1 metric 5',
    'task2Command2' => 'redistribute rip',
    'task3Command1' => 'router rip',
    'task3Command2' => 'network 10.0.0.0',
    'task4Command' => 'ip route 10.0.0.0 255.0.0.0 192.168.1.2'
];

$inputs = json_decode(file_get_contents('php://input'), true);

foreach ($correctAnswers as $key => $correctAnswer) {
    if (!isset($inputs[$key]) || $inputs[$key] !== $correctAnswer) {
        echo json_encode(['status' => 'error', 'message' => '정답이 틀렸습니다.']);
        exit;
    }
}

echo json_encode(['status' => 'success', 'flag' => 'FLAG{routing_master}']);
?>
