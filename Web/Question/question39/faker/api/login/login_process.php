<?php
header('Content-Type: application/json');

// 에러 로깅 활성화
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once '../db_connect.php';

try {
    // POST 데이터 받기
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    // POST 데이터 디버깅
    error_log("Received username: " . $username);
    
    // 사용자 검증
    $stmt = $conn->prepare("SELECT * FROM USER_info WHERE ID = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $password === $user['PASSWORD'] && $user['ACCESS']) {
        // 로그인 성공
        session_start();
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['ID'];
        $_SESSION['admin_logged_in'] = true;
        
        // 활동 로그 기록
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare(
            "INSERT INTO activity_logs (activity_type, ip_address, status, user_id, details) 
             VALUES (?, ?, ?, ?, ?)"
        );
        if (!$log_stmt) {
            error_log("Log prepare failed: " . $conn->error);
        } else {
            $activity_type = 'LOGIN_ATTEMPT';
            $status = 'SUCCESS';
            $details = '관리자 로그인 성공';
            $log_stmt->bind_param("sssss", $activity_type, $ip, $status, $user['ID'], $details);
            if (!$log_stmt->execute()) {
                error_log("Log execute failed: " . $log_stmt->error);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => '로그인 성공',
            'isAdmin' => true
        ]);
    } else {
        // 로그인 실패 이유 확인
        $failureReason = '';
        if (!$user) {
            $failureReason = '존재하지 않는 사용자';
        } elseif ($password !== $user['PASSWORD']) {
            $failureReason = '잘못된 비밀번호';
        } elseif (!$user['ACCESS']) {
            $failureReason = '접근 권한 없음';
        }
        
        // 실패 로그 기록
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare(
            "INSERT INTO activity_logs (activity_type, ip_address, status, user_id, details) 
             VALUES (?, ?, ?, ?, ?)"
        );
        if (!$log_stmt) {
            error_log("Log prepare failed: " . $conn->error);
        } else {
            $activity_type = 'LOGIN_ATTEMPT';
            $status = 'FAILURE';
            $details = '로그인 실패: ' . $failureReason;
            $log_stmt->bind_param("sssss", $activity_type, $ip, $status, $username, $details);
            if (!$log_stmt->execute()) {
                error_log("Log execute failed: " . $log_stmt->error);
            }
        }
        
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => '아이디 또는 비밀번호가 올바르지 않습니다.'
        ]);
    }
} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다: ' . $e->getMessage()
    ]);
}

// 스테이트먼트 정리
if (isset($stmt)) {
    $stmt->close();
}
if (isset($log_stmt)) {
    $log_stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>
