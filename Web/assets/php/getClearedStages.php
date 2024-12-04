<?php
/*
    getClearedStages.php
    - 클리어한 스테이지 조회
    - 클리어한 스테이지 몬스터 카드 출력 용도
*/
// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 0);

// JSON 헤더 설정
header('Content-Type: application/json');

class ClearedStagesDB {
    private $conn;
    
    public function __construct() {
        // config.php 파일로 DB 연결 시도
        $this->conn = include 'config.php';

        // config.php 정상 실행 여부 확인
        if (!$this->conn instanceof mysqli) {
            throw new Exception("Database connection was not established correctly.");
        }
    }

    public function getClearedStages($id) {
        // CLEARED_STAGE 테이블에서 challenge_id 조회
        $stmt = $this->conn->prepare("SELECT challenge_id FROM CLEARED_STAGE WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->conn->error);
        }

        $stmt->bind_param("s", $id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute query: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $clearedStages = [];
        
        while ($row = $result->fetch_assoc()) {
            $clearedStages[] = (int)$row['challenge_id'];
        }

        return $clearedStages;
    }

    public function close() {
        $this->conn->close();
    }
}

try {
    session_start();
    if (!isset($_SESSION['id'])) {
        throw new Exception('User not logged in');
    }

    $id = $_SESSION['id'];
    
    $db = new ClearedStagesDB();
    $clearedStages = $db->getClearedStages($id);
    $db->close();

    echo json_encode([
        'success' => true,
        'data' => $clearedStages
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 