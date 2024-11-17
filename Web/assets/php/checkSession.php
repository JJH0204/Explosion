<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nickname' => $_SESSION['nickname']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in'
    ]);
}
?>