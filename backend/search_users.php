<?php 
    require '../config/config.php';

    if (!isset($_POST['username']) || trim($_POST['username']) == '') {
            $response = [
                'status' => 'failed',
                'message' => 'Please search for a username.'
            ];
    } else {
        // db connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($mysqli->connect_error) {
            echo $mysqli->connect_error;
            exit();
        }

        $username = $_POST['username'];
        $username = $mysqli->escape_string($username);

        $sql = "SELECT genre_name, username, fav_song, high_score FROM users LEFT JOIN genres ON genres.genre_id = users.fav_genre_id WHERE username = '$username';";
    
        $results = $mysqli->query($sql);
    
        if (!$results) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }
    
        // $response = [
        //     'status' => 'success',
        //     'message' => 'profile updated',
        //     'content' => $results,
        //     'redirect' => '../frontend/profile.php'
        // ];

        $all_rows = $results->fetch_all(MYSQLI_ASSOC);
    }
    
        $mysqli->close();
    
        echo json_encode($all_rows);
?>