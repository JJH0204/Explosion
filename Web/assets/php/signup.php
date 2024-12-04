<?php
/*
    signup.php
    - 회원가입 용도
*/
// 모든 출력 버퍼링 시작
ob_start();

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // ID 검증: 영어 소문자만 허용
    if (!preg_match('/^[a-z]+$/', $ID)) {
        throw new Exception('아이디는 영어 소문자만 사용 가능합니다.');
    }

    // 비밀번호 검증: 영어, 숫자 포함 + 8글자 이상
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $PW)) {
        throw new Exception('비밀번호는 영문자, 숫자를 포함하여 8자 이상이어야 합니다.');
    }

    // 닉네임 검증: 영어,한글,숫자 허용 (숫자로만 이루어진 것은 제외)
    if (!preg_match('/^(?![0-9]+$)[a-zA-Z0-9가-힣]+$/', $Nickname)) {
        throw new Exception('닉네임은 영어, 한글, 숫자를 사용할 수 있으며, 숫자로만 이루어질 수 없습니다.');
    }

    if ($PW !== $PWConfirm) {
        throw new Exception('비밀번호가 일치하지 않습니다.');
    }

    // testDB 처리
    require_once 'signupFlameDB.php';
    $testDB = new signupFlameDB();
    $testDB->process($ID, $PW, $Nickname);

    // userDB 처리
    require_once 'signupSqlDB.php';
    $userDB = new SignupSqlDB();
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
