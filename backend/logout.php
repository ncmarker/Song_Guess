<?php 
    require '../config/config.php';

    $_SESSION['logged_in'] = false;
    $_SESSION['username'] = "";

    $response = [
        'status' => 'success',
        'message' => 'logged out',
        'redirect' => '../frontend/login.php'
    ];

    echo json_encode($response);
?>
