<?php
// 가상 파일 시스템 설정
$fileSystem = [
    "/" => ["readme.txt", "logs", "config", "root"],
    "logs" => ["info.txt", "error.txt"],
    "config" => ["settings.json"],
    "root" => ["flag.txt"],
    "logs/info.txt" => "로그 파일 내용: 정상 작동 중...",
    "logs/error.txt" => "오류 로그 내용: 디스크 공간 부족.",
    "config/settings.json" => '{"appName": "VulnerableApp", "version": "1.0"}',
    "root/flag.txt" => "축하합니다! 플래그: flag{directory_traversal_master}"
];

// 세션 초기화
session_start();
if (!isset($_SESSION['currentDirectory'])) {
    $_SESSION['currentDirectory'] = "/";
}

// 명령어 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $command = trim($_POST["command"]);
    $currentDirectory = $_SESSION['currentDirectory'];

    if (preg_match("/^ls$/", $command)) {
        // ls 명령어 처리: 현재 디렉터리의 내용 표시
        $contents = isset($fileSystem[$currentDirectory]) ? implode("\n", $fileSystem[$currentDirectory]) : "디렉터리를 찾을 수 없습니다.";
        echo "디렉터리 목록 ($currentDirectory):\n$contents";
    } elseif (preg_match("/^cd (.+)$/", $command, $matches)) {
        // cd 명령어 처리: 디렉터리 변경
        $targetDir = $matches[1];
        if ($targetDir === "..") {
            // 상위 디렉터리로 이동
            $pathParts = explode("/", trim($currentDirectory, "/"));
            array_pop($pathParts);
            $_SESSION['currentDirectory'] = "/" . implode("/", $pathParts);
            echo "상위 디렉터리로 이동: " . ($_SESSION['currentDirectory'] ?: "/");
        } else {
            // 새 경로 계산
            $newPath = rtrim($currentDirectory, "/") . "/" . $targetDir;
            if (isset($fileSystem[$newPath]) && is_array($fileSystem[$newPath])) {
                $_SESSION['currentDirectory'] = $newPath;
                echo "디렉터리를 변경했습니다: $newPath";
            } else {
                echo "$targetDir 디렉터리를 찾을 수 없습니다.";
            }
        }
    } elseif (preg_match("/^cat (.+)$/", $command, $matches)) {
        // cat 명령어 처리: 파일 내용 출력
        $targetFile = $matches[1];
        $filePath = rtrim($currentDirectory, "/") . "/" . $targetFile;
        if (isset($fileSystem[$filePath]) && !is_array($fileSystem[$filePath])) {
            echo "파일 내용:\n" . $fileSystem[$filePath];
        } else {
            echo "$targetFile 파일을 찾을 수 없습니다.";
        }
    } else {
        // 유효하지 않은 명령 처리
        echo "유효하지 않은 명령어입니다. 사용 가능한 명령: ls, cd, cat";
    }
}
