<?php 

    require '../config/config.php';

    if (!isset($_POST['email']) || trim($_POST['email']) == ''
        || !isset($_POST['pass']) || trim($_POST['pass']) == '') {
            $response = [
                'status' => 'failed',
                'message' => 'Please fill in all required fields.'
            ];
    } else {
        // db connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($mysqli->connect_error) {
            echo $mysqli->connect_error;
            exit();
        }

        $email = $_POST['email'];
        $email = $mysqli->escape_string($email);

        $username = explode('@', $email)[0];

        $pass = $_POST['pass'];
        $pass = hash('sha256', $pass);

        // new user checked 
        if ($_POST['user_new'] == 'new') {

            $sql_dup = "SELECT * FROM users WHERE username = '$username' OR email = '$email';";
            $results_dup = $mysqli->query($sql_dup);

            if (!$results_dup) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            if ($results_dup->num_rows > 0) {
                $response = [
                    'status' => 'failed',
                    'message' => 'Email already registered.'
                ];
            } else {
                $sql_new = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$pass');";

                $results_new = $mysqli->query($sql_new);

                if (!$results_new) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }

                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;

                $response = [
                    'status' => 'success',
                    'message' => 'new user added',
                    'redirect' => '../frontend/profile.php'
                ];
            }
        } else {
            // Returning User

            $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$pass';";
            $results = $mysqli->query($sql);

            if (!$results) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            if ($results->num_rows > 0) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;

                $response = [
                    'status' => 'success',
                    'message' => 'logged in',
                    'redirect' => '../frontend/profile.php'
                ];

            } else {
                $response = [
                    'status' => 'failed',
                    'message' => 'Invalid credentials'
                ];

            }
        }

        $mysqli->close();
    }

    echo json_encode($response);
?>