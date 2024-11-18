<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['query'] ?? '';

    // 세미콜론 체크
    if (substr($userInput, -1) !== ';') {
        echo json_encode([
            "status" => "error", 
            "message" => "SQL 구문이 올바르지 않습니다."
        ]);
        exit;
    }

    // userDB.test의 모든 가능한 형식들
    $validDBFormats = [
        'userDB.test',
        '`userDB`.test',
        'userDB.`test`',
        '`userDB`.`test`'
    ];

    // userDB.test 형식 체크
    $dbValid = false;
    foreach ($validDBFormats as $format) {
        if (strpos($userInput, $format) !== false) {
            $dbValid = true;
            break;
        }
    }

    if (!$dbValid) {
        echo json_encode([
            "status" => "error", 
            "message" => "테이블명이 올바르지 않습니다."
        ]);
        exit;
    }

    // test@localhost의 모든 가능한 형식들
    $validHostFormats = [ 
        // 따옴표/백틱 없는 버전
        'test@localhost',
        
        // 한쪽만 따옴표
        'test@\'localhost\'',
        '\'test\'@localhost',
        
        // 한쪽만 백틱
        'test@`localhost`',
        '`test`@localhost',
        
        // 양쪽 모두 같은 기호
        '\'test\'@\'localhost\'',
        '`test`@`localhost`',
        
        // 양쪽 다른 기호 조합
        '\'test\'@`localhost`',
        '`test`@\'localhost\'',
        'test@`localhost`',
        'test@\'localhost\'',
        '\'test\'@localhost',
        '`test`@localhost'
    ];

    // test@localhost 형식 체크
    $hostValid = false;
    foreach ($validHostFormats as $format) {
        if (strpos($userInput, $format) !== false) {
            $hostValid = true;
            break;
        }
    }

    if (!$hostValid) {
        echo json_encode([
            "status" => "error", 
            "message" => "사용자 지정이 올바르지 않습니다."
        ]);
        exit;
    }

    // 기본 구문 구조 체크 (GRANT, SELECT/DELETE, ON, TO 순서와 공백)
    $pattern = '/^grant\s+(select\s*,\s*delete|delete\s*,\s*select)\s+on\s+.*\s+to\s+/i';
    
    if (preg_match($pattern, $userInput)) {
        echo json_encode([
            "status" => "success", 
            "flag" => "FLAG{sql_grant_correct}"
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "SQL 구문이 올바르지 않습니다."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error", 
        "message" => "잘못된 요청 방식입니다."
    ]);
}
?>
