<?php
session_start();

// 세션에서 사용자 정보 확인
if (isset($_SESSION['user_id']) && isset($_SESSION['nickname'])) {
    $_SESSION['is_game_request'] = true; // 게임 요청 세션 값 설정
    echo json_encode(["message" => "게임 요청이 설정되었습니다."]);
} else {
    echo json_encode(["error" => "로그인 상태가 아닙니다."]);
}
?>
