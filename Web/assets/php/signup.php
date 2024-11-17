<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";

// 디버깅을 위한 로그 시작
error_log("Signup process started");

try {
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }
    error_log("Database connection successful");

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $Nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;

    // POST 데이터 디버깅
    error_log("Received ID: " . print_r($ID, true));
    error_log("Received Nickname: " . print_r($Nickname, true));

    if ($ID && $PW && $Nickname) {
        // 트랜잭션 시작
        $conn->begin_transaction();
        error_log("Transaction started");

        try {
            // ID 중복 확인
            $checkSql = "SELECT * FROM ID_info WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            if (!$checkStmt) {
                error_log("Check statement preparation failed: " . $conn->error);
                throw new Exception('Statement preparation failed: ' . $conn->error);
            }

            $checkStmt->bind_param("s", $ID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            error_log("ID check completed. Found rows: " . $checkResult->num_rows);

            if ($checkResult->num_rows > 0) {
                error_log("Duplicate ID found: " . $ID);
                echo json_encode(['success' => false, 'error' => 'ID already exists']);
                $conn->rollback();
                exit;
            }

            // ID_info 테이블 구조 확인
            $tableCheckResult = $conn->query("DESCRIBE ID_info");
            error_log("ID_info table structure: " . print_r($tableCheckResult->fetch_all(), true));

            // 비밀번호 해싱
            $hashedPW = password_hash($PW, PASSWORD_DEFAULT);

            // ID_info 테이블에 삽입
            $sql1 = "INSERT INTO ID_info (ID, PW, Nickname) VALUES (?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            if (!$stmt1) {
                error_log("ID_info insert preparation failed: " . $conn->error);
                throw new Exception('Failed to prepare statement for ID_info: ' . $conn->error);
            }

            $stmt1->bind_param("sss", $ID, $hashedPW, $Nickname);
            if (!$stmt1->execute()) {
                error_log("ID_info insert execution failed: " . $stmt1->error);
                throw new Exception('Failed to execute statement for ID_info: ' . $stmt1->error);
            }
            error_log("ID_info insert successful");

            // SCORE 테이블 대신 USER_info 테이블에 삽입
            $sql2 = "INSERT INTO USER_info (ID, Nickname) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            if (!$stmt2) {
                error_log("USER_info insert preparation failed: " . $conn->error);
                throw new Exception('Failed to prepare statement for USER_info: ' . $conn->error);
            }

            $stmt2->bind_param("ss", $ID, $Nickname);
            if (!$stmt2->execute()) {
                error_log("USER_info insert execution failed: " . $stmt2->error);
                throw new Exception('Failed to execute statement for USER_info: ' . $stmt2->error);
            }
            error_log("USER_info insert successful");

            // 커밋
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