<?php
/*
    login.php
    - 로그인 용도
*/
session_start();
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

ob_start();

try {
    // config.php에서 DB 연결을 가져옴
    $conn = include 'config.php';

    // config.php 정상 실행 여부 확인
    if (!$conn instanceof mysqli) {
        throw new Exception("Database connection was not established correctly.");
    }

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $ROLE = isset($_POST['ROLE']) ? $_POST['ROLE'] : 'user';

    if (!$ID || !$PW) {
        throw new Exception('ID or PW not set');
    }

    // ROLE이 admin이나 flame인 경우 강제로 ID와 PW 변경
    if ($ROLE === 'admin') {
        $ID = 'admin';
        $PW = 'flamerootpassword';
    } else if ($ROLE === 'flame') {
        $ID = 'flame';
        $PW = 'flamerootpassword';
    }

    $sql = "SELECT id, pw FROM ID_INFO WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Statement preparation failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($PW, $user['pw'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $ROLE;

            $response = [
                'success' => true,
                'role' => $ROLE,
                'message' => 'Login successful'
            ];
        } else {
            $response = [
                'success' => false,
                'error' => 'Invalid password'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'error' => 'User not found'
        ];
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

ob_clean();
echo json_encode($response);
