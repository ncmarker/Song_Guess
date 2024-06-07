<?php
    require '../config/config.php';

    $client_id = '3c30dd5d4acc494797da693af19a7eea';
    // $redirect_uri = 'http://localhost:3000/backend/spotify_callback.php';
    $redirect_uri = 'http://304.itpwebdev.com/~ncmarker/Final%20Project/backend/spotify_callback.php';
    $scope = 'streaming user-read-email user-read-private';

    // Generate a random state value for security
    $state = bin2hex(random_bytes(16));
    $_SESSION['spotify_auth_state'] = $state;

    $authorizeURL = 'https://accounts.spotify.com/authorize';
    $params = [
        'response_type' => 'code',
        'client_id' => $client_id,
        'scope' => $scope,
        'redirect_uri' => $redirect_uri,
        'state' => $state
    ];

    $url = $authorizeURL . '?' . http_build_query($params);
    header("Location: $url");
    exit();
?>