<?php
session_start();
require_once '../auth/check_admin.php';

header('Content-Type: application/json');

$uploadDir = '../../image/share/';
$response = ['success' => false, 'message' => ''];

try {
    // 업로드 디렉토리가 없으면 생성
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!isset($_FILES['images'])) {
        throw new Exception('업로드된 파일이 없습니다.');
    }

    $files = $_FILES['images'];
    $uploadedFiles = [];

    // 다중 파일 업로드 처리
    foreach ($files['name'] as $key => $name) {
        $tmpName = $files['tmp_name'][$key];
        $error = $files['error'][$key];

        if ($error === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($name);
            $extension = strtolower($fileInfo['extension']);
            
            // 이미지 파일 검증
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('허용되지 않는 파일 형식입니다.');
            }

            // 고유한 파일명 생성
            $newFileName = uniqid() . '.' . $extension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFiles[] = $newFileName;
            } else {
                throw new Exception('파일 업로드에 실패했습니다.');
            }
        } else {
            throw new Exception('파일 업로드 중 오류가 발생했습니다.');
        }
    }

    $response['success'] = true;
    $response['message'] = '파일이 성공적으로 업로드되었습니다.';
    $response['files'] = $uploadedFiles;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response); 