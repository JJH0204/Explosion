<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();

require_once('../login/check_session.php');

if (!isValidAdminSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    require_once('../db_connect.php');
    $conn = connectDB();
    
    // 활성 사용자 수 조회 (최근 24시간 내 로그인)
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) as active_users 
        FROM activity_logs 
        WHERE activity_type = 'LOGIN' 
        AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $activeUsers = $result->fetch_assoc()['active_users'];
    
    // 오늘의 총 방문자 수
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) as today_visitors 
        FROM activity_logs 
        WHERE DATE(timestamp) = CURDATE()
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $todayVisitors = $result->fetch_assoc()['today_visitors'];
    
    // 전체 방문자 수
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) as total_visitors 
        FROM activity_logs
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $totalVisitors = $result->fetch_assoc()['total_visitors'];
    
    // 최근 7일간의 일일 방문자 데이터
    $stmt = $conn->prepare("
        SELECT 
            DATE(timestamp) as date,
            COUNT(DISTINCT user_id) as visitors
        FROM activity_logs
        WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(timestamp)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $dates = [];
    $visitors = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = date('n/j', strtotime($row['date']));
        $visitors[] = (int)$row['visitors'];
    }
    
    // 활동 유형별 통계
    $stmt = $conn->prepare("
        SELECT 
            activity_type,
            COUNT(*) as count
        FROM activity_logs
        WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY activity_type
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activityData = [
        'logins' => 0,
        'posts' => 0,
        'comments' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        switch ($row['activity_type']) {
            case 'LOGIN':
                $activityData['logins'] = (int)$row['count'];
                break;
            case 'POST':
                $activityData['posts'] = (int)$row['count'];
                break;
            case 'COMMENT':
                $activityData['comments'] = (int)$row['count'];
                break;
        }
    }
    
    $response = [
        'success' => true,
        'totalVisitors' => $totalVisitors,
        'todayVisitors' => $todayVisitors,
        'activeUsers' => $activeUsers,
        'visitorData' => [
            'labels' => $dates,
            'visitors' => $visitors
        ],
        'activityData' => $activityData
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Dashboard data error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load dashboard data'
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
