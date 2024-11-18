<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // 응답을 JSON 형식으로 설정

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";
$charset = 'utf8mb4';

try {
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;

    if ($ID && $PW) {
        $sql = "SELECT * FROM ID_info WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("s", $ID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($PW, $user['PW'])) {
                session_start();
                session_regenerate_id(true); // 세션 ID 재생성
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['nickname'] = $user['NICKNAME'];

                echo json_encode(['success' => true, 'ID' => $user['ID'], 'NICKNAME' => $user['NICKNAME']]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'ID or PW not set']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
} finally {
    if ($conn) {
        $conn->close();
    }
}
exit;
