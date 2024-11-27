<?php
/*
    get_cleared_stages.php
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
        $this->conn = new mysqli('localhost', 'admin', 'flamerootpassword', 'flameDB');
        if ($this->conn->connect_error) {
            throw new Exception('flameDB connection failed: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8');
    }

    public function getClearedStages($nickname) {
        $stmt = $this->conn->prepare("SELECT ANSWER FROM CLEARED_STAGE WHERE NICKNAME = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->conn->error);
        }

        $stmt->bind_param("s", $nickname);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute query: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $clearedStages = [];
        
        while ($row = $result->fetch_assoc()) {
            $clearedStages[] = (int)$row['ANSWER'];
        }

        return $clearedStages;
    }

    public function close() {
        $this->conn->close();
    }
}

try {
    session_start();
    if (!isset($_SESSION['nickname'])) {
        throw new Exception('User not logged in');
    }

    $nickname = $_SESSION['nickname'];
    
    $db = new ClearedStagesDB();
    $clearedStages = $db->getClearedStages($nickname);
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