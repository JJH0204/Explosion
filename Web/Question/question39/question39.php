<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['success']) && $input['success'] === true) {
    // Here you would implement your flag generation/validation logic
    $response = array(
        'status' => 'success',
        'flag' => 'FLAG{XxXx_h4rd_t0_gu3ss_XxXx_39th_Ch4ll3ng3}'
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid attempt'
    );
}

echo json_encode($response);
