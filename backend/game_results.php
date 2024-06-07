<?php 
    require '../config/config.php';

    // db connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    }

    $curr_username = $_SESSION['username'];

    $high_score = $_POST['score'];


    $sql_init = "SELECT high_score FROM users WHERE username = '$curr_username';";

    $results_init = $mysqli->query($sql_init);

    if (!$results_init) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $row_init = $results_init->fetch_assoc();

    if ($high_score > $row_init['high_score'] || $row_init['high_score'] == null) {
        $sql_update = "UPDATE users SET high_score = '$high_score' WHERE username = '$curr_username';";

        $results_update = $mysqli->query($sql_update);

        if (!$results_update) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }
    }

    $response = [
        'status' => 'success',
        'message' => 'profile updated',
        'redirect' => '../frontend/game.php'
    ];

    $mysqli->close();

    echo json_encode($response);
?>