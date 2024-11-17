<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";
$user_dbname = "user_DB";

try {
    // testDB 선택 및 연결
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        throw new Exception('testDB connection failed: ' . $conn->connect_error);
    }

    // USER_db 데이터베이스 생성 및 연결
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `$user_dbname`";
    if (!$conn->query($createDbSql)) {
        throw new Exception('Failed to create USER_db database');
    }

    $user_conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $user_dbname);
    if ($user_conn->connect_error) {
        throw new Exception('USER_db connection failed: ' . $user_conn->connect_error);
    }

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $Nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;

    // POST 데이터 디버깅
    error_log("Received ID: " . print_r($ID, true));
    error_log("Received Nickname: " . print_r($Nickname, true));

    if ($ID && $PW && $Nickname) {
        $conn->begin_transaction();
        $user_conn->begin_transaction();

        try {
            // ID 중복 확인
            $checkIdSql = "SELECT * FROM ID_info WHERE ID = ?";
            $checkIdStmt = $conn->prepare($checkIdSql);
            $checkIdStmt->bind_param("s", $ID);
            $checkIdStmt->execute();
            
            if ($checkIdStmt->get_result()->num_rows > 0) {
                throw new Exception('이미 존재하는 ID입니다.');
            }

            // 닉네임 중복 확인
            $checkNickSql = "SELECT * FROM USER_info WHERE NICKNAME = ?";
            $checkNickStmt = $conn->prepare($checkNickSql);
            $checkNickStmt->bind_param("s", $Nickname);
            $checkNickStmt->execute();
            
            if ($checkNickStmt->get_result()->num_rows > 0) {
                throw new Exception('이미 존재하는 닉네임입니다.');
            }

            // USER_db에서도 닉네임 테이블 존재 여부 확인
            $tableName = mysqli_real_escape_string($user_conn, $Nickname . "_table");
            $checkTableSql = "SHOW TABLES LIKE '$tableName'";
            $tableExists = $user_conn->query($checkTableSql)->num_rows > 0;
            
            if ($tableExists) {
                throw new Exception('이미 존재하는 닉네임입니다.');
            }

            $hashedPW = password_hash($PW, PASSWORD_DEFAULT);

            if ($checkResult->num_rows > 0) {
                error_log("Duplicate ID found: " . $ID);
                echo json_encode(['success' => false, 'error' => 'ID already exists']);
                $conn->rollback();
                exit;
            }

            // ID_info 테이블 구조 확인
            $tableCheckResult = $conn->query("DESCRIBE ID_info");
            error_log("ID_info table structure: " . print_r($tableCheckResult->fetch_all(), true));

            // testDB의 ID_info 테이블에 삽입
            $hashedPW = password_hash($PW, PASSWORD_DEFAULT);
            $sql1 = "INSERT INTO ID_info (ID, PW, Nickname) VALUES (?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("sss", $ID, $hashedPW, $Nickname);
            if (!$stmt1->execute()) {
                throw new Exception('Failed to insert into ID_info: ' . $stmt1->error);
            }

            // testDB의 USER_info 테이블에 삽입
            $sql2 = "INSERT INTO USER_info (ID, Nickname) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ss", $ID, $Nickname);
            if (!$stmt2->execute()) {
                throw new Exception('Failed to insert into USER_info: ' . $stmt2->error);
            }

            // USER_db에 사용자 테이블 생성
            $tableName = mysqli_real_escape_string($user_conn, $Nickname . "_table");
            $createTableSql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                nickname VARCHAR(50) PRIMARY KEY,
                adminflag VARCHAR(100) DEFAULT NULL
            )";
            
            if (!$user_conn->query($createTableSql)) {
                throw new Exception('Failed to create user table');
            }

            // 생성된 테이블에 초기 데이터 삽입
            $insertDataSql = "INSERT INTO `$user_dbname`.`$tableName` (nickname, adminflag) VALUES (?, NULL)";
            $stmt3 = $user_conn->prepare($insertDataSql);
            if (!$stmt3) {
                throw new Exception('Failed to prepare insert statement: ' . $user_conn->error);
            }
            $stmt3->bind_param("s", $Nickname);
            if (!$stmt3->execute()) {
                throw new Exception('Failed to insert initial data: ' . $stmt3->error);
            }

            // MySQL 사용자 생성 및 권한 부여
            $createUserSql = "CREATE USER IF NOT EXISTS '$ID'@'localhost' IDENTIFIED BY '$PW'";
            if (!$user_conn->query($createUserSql)) {
                throw new Exception('Failed to create MySQL user: ' . $user_conn->error);
            }

            // UPDATE 권한만 부여
            $grantSql = "GRANT UPDATE ON `$user_dbname`.`$tableName` TO '$ID'@'localhost'";
            if (!$user_conn->query($grantSql)) {
                throw new Exception('Failed to grant permissions: ' . $user_conn->error);
            }

            // 권한 즉시 적용
            $user_conn->query("FLUSH PRIVILEGES");

            $user_conn->commit();
            $conn->commit();
            error_log("Transaction committed successfully");
            echo json_encode(['success' => true, 'message' => 'User registered successfully']);

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Transaction failed: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        error_log("Missing required fields");
        echo json_encode(['success' => false, 'error' => 'ID, PW, or Nickname not set']);
    }
} catch (Exception $e) {
    error_log("Signup process failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
error_log("Signup process completed");
