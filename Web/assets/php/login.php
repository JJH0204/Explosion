<?php
session_start();
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
    
    error_log("Received ID: " . print_r($ID, true));
    error_log("Received PW: " . print_r($PW, true));

    if ($ID && $PW) {
        $sql = "SELECT i.ID, i.PW, u.NICKNAME FROM ID_info i 
                JOIN USER_info u ON i.ID = u.ID 
                WHERE i.ID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Statement preparation failed: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed']);
            exit;
        }

        $stmt->bind_param("s", $ID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        error_log("Query result rows: " . $result->num_rows);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($PW, $user['PW'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['nickname'] = $user['NICKNAME'];
                $_SESSION['logged_in'] = true;
                
                echo json_encode(['success' => true]);
            } else {
                error_log("Password verification failed for user: " . $ID);
                echo json_encode(['success' => false, 'error' => 'Invalid password']);
            }
        } else {
            error_log("No user found with ID: " . $ID);
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }

        $stmt->close();
    } else {
        error_log("Missing ID or PW in POST data");
        echo json_encode(['success' => false, 'error' => 'ID or PW not set']);
    }
    
    $conn->close();
} catch (Exception $e) {
    error_log("Login exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
