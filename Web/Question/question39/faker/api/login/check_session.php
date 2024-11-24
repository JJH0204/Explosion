<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../db_connect.php');

function isValidAdminSession() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_logged_in'])) {
        return false;
    }

    try {
        global $conn;
        
        if (!$conn) {
            error_log("Database connection failed in isValidAdminSession");
            return false;
        }

        $query = "SELECT ACCESS FROM USER_info WHERE ID = ? AND ACCESS = 1 LIMIT 1";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }

        $stmt->bind_param("s", $_SESSION['user_id']);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }

        $result = $stmt->get_result();
        if (!$result) {
            error_log("Result failed: " . $stmt->error);
            return false;
        }

        $user = $result->fetch_assoc();
        
        $stmt->close();
        $conn->close();
        
        return $user !== null;
    } catch (Exception $e) {
        error_log("Session check error: " . $e->getMessage());
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
        return false;
    }
}

// API 엔드포인트로 직접 호출된 경우
if (basename($_SERVER['PHP_SELF']) == 'check_session.php') {
    try {
        $isValid = isValidAdminSession();
        
        if ($isValid) {
            echo json_encode([
                'success' => true,
                'isAdmin' => true,
                'message' => 'Valid session'
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'isAdmin' => false,
                'message' => 'Invalid session'
            ]);
            
            // 잘못된 세션 제거
            session_destroy();
        }
    } catch (Exception $e) {
        error_log("Session check endpoint error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'isAdmin' => false,
            'message' => 'Server error during session check'
        ]);
    }
}
?>
