<?php
// 모든 출력 버퍼링 시작
ob_start();

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 0);

// JSON 헤더 설정
header('Content-Type: application/json');

try {
    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $PWConfirm = isset($_POST['PWConfirm']) ? $_POST['PWConfirm'] : null;
    $Nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;

    if (!($ID && $PW && $PWConfirm && $Nickname)) {
        throw new Exception('필수 입력값이 누락되었습니다.');
    }

    if ($PW !== $PWConfirm) {
        throw new Exception('비밀번호가 일치하지 않습니다.');
    }

    // testDB 처리
    require_once 'signup_testDB.php';
    $testDB = new SignupTestDB();
    $testDB->process($ID, $PW, $Nickname);

    // userDB 처리
    require_once 'signup_userDB.php';
    $userDB = new SignupUserDB();
    $userDB->process($ID, $PW, $Nickname);

    // 성공 응답
    echo json_encode([
        'success' => true, 
        'message' => 'User registered successfully'
    ]);

} catch (Exception $e) {
    // 실패 응답
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} finally {
    // 출력 버퍼 정리
    ob_end_flush();
}
