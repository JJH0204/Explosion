<?php
$passwords = [
    'flame' => 'flamerootpassword',
    'admin' => 'flamerootpassword'
];

foreach ($passwords as $id => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "-- For user: $id\n";
    echo "INSERT INTO ID_info (ID, PW, NICKNAME) VALUES ('$id', '$hash', '$id');\n";
    echo "INSERT INTO USER_info (ID, NICKNAME) VALUES ('$id', '$id');\n\n";
}
?>