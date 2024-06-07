<?php 
    require '../config/config.php';

    // db connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    }

    $curr_username = $_SESSION['username'];

    $email = $_POST['email'];
    $email = $mysqli->escape_string($email);

    $username = explode('@', $email)[0];

    $genre_id = $_POST['genre_id'];

    $fav_song = $_POST['fav_song'];
    $fav_song = $mysqli->escape_string($fav_song);

    $profile_img_id = $_POST['profile_img'];

    if (isset($_POST['pass']) && trim($_POST['pass']) != "") {
        $pass = $_POST['pass'];
        $pass = hash('sha256', $pass);
        $sql = "UPDATE users SET email = '$email', password = '$pass', username = '$username', fav_song = '$fav_song', fav_genre_id = '$genre_id', profile_pic_id = '$profile_img_id' WHERE username = '$curr_username';";
    } else {
        $sql = "UPDATE users SET email = '$email', username = '$username', fav_song = '$fav_song', fav_genre_id = '$genre_id', profile_pic_id = '$profile_img_id' WHERE username = '$curr_username';";
    }

    $results = $mysqli->query($sql);

    if (!$results) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $response = [
        'status' => 'success',
        'message' => 'profile updated',
        'redirect' => '../frontend/profile.php'
    ];

    $mysqli->close();

    echo json_encode($response);
?>