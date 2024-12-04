<?php
/*
    signup_testDB.php
    - 회원가입 용도
    - 회원가입 시 flameDB 조작 용도
*/
class signupFlameDB {
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli('localhost', 'db_admin', 'flamerootpassword', 'DB_flame');
        if ($this->conn->connect_error) {
            throw new Exception('flameDB connection failed: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }

    private function checkDuplicateID($ID) {
        $stmt = $this->conn->prepare("SELECT id FROM ID_INFO WHERE id = ?");
        $stmt->bind_param("s", $ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    private function checkDuplicateNickname($Nickname) {
        $stmt = $this->conn->prepare("SELECT nickname FROM ID_INFO WHERE nickname = ?");
        $stmt->bind_param("s", $Nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    private function insertIDInfo($ID, $PW, $Nickname) {
        $hashedPW = password_hash($PW, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO ID_INFO (id, pw, nickname) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $ID, $hashedPW, $Nickname);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert into ID_INFO: ' . $stmt->error);
        }
    }

    private function insertUserInfo($ID) {
        $stmt = $this->conn->prepare("INSERT INTO USER_INFO (id) VALUES (?)");
        // $stmt->bind_param("ss", $ID);
        $stmt->bind_param("s", $ID);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert into USER_INFO: ' . $stmt->error);
        }
    }

    private function isRestrictedNickname($Nickname) {
        $restrictedNames = ['admin', 'flame', 'root'];
        return in_array(strtolower($Nickname), $restrictedNames);
    }

    public function process($ID, $PW, $Nickname) {
        
        try {
            // 트랜잭션 시작
            error_log("트랜잭션이 시작되었습니다.");
            $this->conn->begin_transaction();

            if ($this->isRestrictedNickname($Nickname)) {
                throw new Exception('사용할 수 없는 닉네임입니다.');
            }

            if ($this->checkDuplicateID($ID)) {
                throw new Exception('이미 존재하는 ID입니다.');
            }

            if ($this->checkDuplicateNickname($Nickname)) {
                throw new Exception('이미 존재하는 닉네임입니다.');
            }

            $this->insertIDInfo($ID, $PW, $Nickname);
            // $this->insertUserInfo($ID, $Nickname);
            $this->insertUserInfo($ID);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        } finally {
            $this->conn->close();
        }
    }
}