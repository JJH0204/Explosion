<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";

try {
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $Nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;

    if ($ID && $PW && $Nickname) {
        // 트랜잭션 시작
        $conn->begin_transaction();

        try {
            // ID 중복 확인
            $checkSql = "SELECT * FROM ID_info WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            if (!$checkStmt) {
                throw new Exception('Statement preparation failed: ' . $conn->error);
            }

            $checkStmt->bind_param("s", $ID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // 중복된 ID가 있는 경우
                echo json_encode(['success' => false, 'error' => 'ID already exists']);
                $conn->rollback(); // 트랜잭션 롤백
                exit;
            }

            // 비밀번호 해싱
            $hashedPW = password_hash($PW, PASSWORD_DEFAULT);

            // ID_info 테이블에 사용자 정보 삽입
            $sql1 = "INSERT INTO ID_info (ID, PW, Nickname) VALUES (?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            if (!$stmt1) {
                throw new Exception('Failed to prepare statement for ID_info: ' . $conn->error);
            }

            $stmt1->bind_param("sss", $ID, $hashedPW, $Nickname);
            if (!$stmt1->execute()) {
                throw new Exception('Failed to execute statement for ID_info: ' . $stmt1->error);
            }

            // Score 테이블에 사용자 정보 삽입
            $sql2 = "INSERT INTO Score (ID, Nickname) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            if (!$stmt2) {
                throw new Exception('Failed to prepare statement for Score: ' . $conn->error);
            }

            $stmt2->bind_param("ss", $ID, $Nickname);
            if (!$stmt2->execute()) {
                throw new Exception('Failed to execute statement for Score: ' . $stmt2->error);
            }

            // 모든 작업이 성공하면 커밋
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'User registered successfully']);
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } finally {
            // 리소스 정리
            if (isset($checkStmt)) $checkStmt->close();
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            $conn->close();
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID, PW, or Nickname not set']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;