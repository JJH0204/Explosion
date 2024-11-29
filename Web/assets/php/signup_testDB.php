<?php
/*
    signup_testDB.php
    - 회원가입 용도
    - 회원가입 시 flameDB 조작 용도
*/
class SignupTestDB {
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli('db', 'root', 'rootpassword', 'flameDB');
        if ($this->conn->connect_error) {
            throw new Exception('flameDB connection failed: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }

    private function checkDuplicateID($ID) {
        $stmt = $this->conn->prepare("SELECT ID FROM ID_info WHERE ID = ?");
        $stmt->bind_param("s", $ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    private function checkDuplicateNickname($Nickname) {
        $stmt = $this->conn->prepare("SELECT NICKNAME FROM USER_info WHERE NICKNAME = ?");
        $stmt->bind_param("s", $Nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    private function insertIDInfo($ID, $PW, $Nickname) {
        $hashedPW = password_hash($PW, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO ID_info (ID, PW, NICKNAME) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $ID, $hashedPW, $Nickname);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert into ID_info: ' . $stmt->error);
        }
    }

    private function insertUserInfo($ID, $Nickname) {
        $stmt = $this->conn->prepare("INSERT INTO USER_info (ID, NICKNAME) VALUES (?, ?)");
        $stmt->bind_param("ss", $ID, $Nickname);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert into USER_info: ' . $stmt->error);
        }
    }

    private function isRestrictedNickname($Nickname) {
        $restrictedNames = ['admin', 'flame', 'root'];
        return in_array(strtolower($Nickname), $restrictedNames);
    }

    public function process($ID, $PW, $Nickname) {
        $this->conn->begin_transaction();

        try {
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
            $this->insertUserInfo($ID, $Nickname);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        } finally {
            $this->conn->close();
        }
    }
}