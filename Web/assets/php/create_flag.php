<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['nickname']) || !isset($_POST['flag'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

try {
    // admin 권한으로 직접 userDB에 연결
    $conn = new mysqli('localhost', 'db_admin', 'flamerootpassword', 'userDB');
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $nickname = $_SESSION['nickname'];
    $flag = $_POST['flag'];

    $stmt = $conn->prepare("UPDATE `$nickname` SET FLAG = ? WHERE NICKNAME = ?");
    $stmt->bind_param("ss", $flag, $nickname);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update flag: " . $stmt->error);
    }

    echo json_encode(['success' => true]);
    
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 