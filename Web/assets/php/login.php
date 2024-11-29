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
    $serverIP = 'db';
    $DB_rootID = "root";
    $DB_rootPW = "rootpassword";
    $dbname = "flameDB";

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $ROLE = isset($_POST['ROLE']) ? $_POST['ROLE'] : 'user';

    if (!$ID || !$PW) {
        throw new Exception('ID or PW not set');
    }

    // ROLE이 admin이나 flame인 경우 강제로 ID와 PW 변경
    if ($ROLE === 'admin') {
        $ID = 'flame_admin';
        $PW = 'flamerootpassword';
    } else if ($ROLE === 'flame') {
        $ID = 'flame';
        $PW = 'flamerootpassword';
    }

    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    $sql = "SELECT i.ID, i.PW, u.NICKNAME FROM ID_info i 
            JOIN USER_info u ON i.ID = u.ID 
            WHERE i.ID = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Statement preparation failed');
    }

    $stmt->bind_param("s", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($PW, $user['PW'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['nickname'] = $user['NICKNAME'];
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
