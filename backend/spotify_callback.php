<?php
    session_start();

    if ($_GET['state'] !== $_SESSION['spotify_auth_state']) {
        die('State mismatch');
    }

    $code = $_GET['code'];
    $client_id = '3c30dd5d4acc494797da693af19a7eea';
    $client_secret = 'dea195220b6d4986b433e2555737ed2b';
    $redirect_uri = 'http://localhost:3000/backend/spotify_callback.php';
    // $redirect_uri = 'http://304.itpwebdev.com/~ncmarker/Final%20Project/backend/spotify_callback.php';

    $url = 'https://accounts.spotify.com/api/token';
    $headers = [
        'Authorization: Basic ' . base64_encode("$client_id:$client_secret")
    ];
    $body = http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Error: ' . curl_error($ch);
    } else {
        $tokens = json_decode($response, true);
        if ($tokens === null) {
            echo 'Error decoding JSON';
        } else {
            // echo 'Response: ' . $response . '<br>';
            // echo 'Tokens: ' . print_r($tokens, true);
        }
    }


    curl_close($ch);
    $tokens = json_decode($response, true);

    // echo $response;

    // echo $tokens;

    // Save the access token in session
    $_SESSION['spotify_access_token'] = $tokens['access_token'];

    header('Location: ../frontend/game.php');
    exit();
?>