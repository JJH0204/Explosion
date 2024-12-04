<?php
/*
    signup_userDB.php
    - 회원가입 용도
    - 회원가입 시 userDB 조작 용도
*/
class SignupSqlDB {
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli('localhost', 'db_admin', 'flamerootpassword', 'DB_sql');
        if ($this->conn->connect_error) {
            throw new Exception('userDB connection failed: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }

    private function createNicknameTable($Nickname) {
        $tableName = $this->conn->real_escape_string($Nickname);
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
            nickname VARCHAR(50) PRIMARY KEY,
            flag VARCHAR(100) DEFAULT NULL
        )";
        
        if (!$this->conn->query($sql)) {
            throw new Exception('Failed to create nickname table: ' . $this->conn->error);
        }
    }

    private function insertInitialData($Nickname) {
        $tableName = $this->conn->real_escape_string($Nickname);
        $stmt = $this->conn->prepare("INSERT INTO `$tableName` (nickname) VALUES (?)");
        $stmt->bind_param("s", $Nickname);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert initial data: ' . $stmt->error);
        }
    }

    private function createDBUser($ID, $PW, $Nickname) {
        $escapedID = $this->conn->real_escape_string($ID);
        $escapedPW = $this->conn->real_escape_string($PW);  // 원래 비밀번호 사용
        $escapedNickname = $this->conn->real_escape_string($Nickname);

        // 원래 비밀번호로 MySQL 사용자 생성
        $createUserSql = "CREATE USER IF NOT EXISTS '$escapedID'@'localhost' IDENTIFIED BY 'firewalld'";
        if (!$this->conn->query($createUserSql)) {
            throw new Exception('Failed to create MySQL user: ' . $this->conn->error);
        }

        // 권한 부여
        $grantSql = "GRANT UPDATE, SELECT ON DB_sql.`$escapedNickname` TO '$escapedID'@'localhost'";
        if (!$this->conn->query($grantSql)) {
            throw new Exception('Failed to grant permissions: ' . $this->conn->error);
        }

        // 권한 적용
        $this->conn->query("FLUSH PRIVILEGES");
    }

    private function cleanup($ID, $Nickname) {
        // 오류 발생 시 정리 작업
        $tableName = $this->conn->real_escape_string($Nickname);
        $escapedID = $this->conn->real_escape_string($ID);
        
        $this->conn->query("DROP TABLE IF EXISTS `$tableName`");
        $this->conn->query("DROP USER IF EXISTS '$escapedID'@'localhost'");
        $this->conn->query("FLUSH PRIVILEGES");
    }

    public function process($ID, $PW, $Nickname) {
        $this->conn->begin_transaction();

        try {
            $this->createNicknameTable($Nickname);
            $this->insertInitialData($Nickname);
            $this->createDBUser($ID, $PW, $Nickname);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->cleanup($ID, $Nickname);
            throw $e;
        } finally {
            $this->conn->close();
        }
    }
} 