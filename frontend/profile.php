<?php 

    require '../config/config.php';

    if (!$_SESSION['logged_in']) {
        header('Location: ../frontend/login.php');
    } else {
        // db connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($mysqli->connect_error) {
            echo $mysqli->connect_error;
            exit();
        }

        // get user info
        $username = $_SESSION['username'];

        $sql_user = "SELECT * FROM users WHERE username = '$username';";
        $results_user = $mysqli->query($sql_user);

        if (!$results_user) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $row = $results_user->fetch_assoc();

        // get genre list
        $sql_genres = "SELECT * FROM genres;";
        $results_genres = $mysqli->query($sql_genres);

        if (!$results_genres) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }


        // get profile pic list
        $sql_profiles = "SELECT * FROM profile_pics;";
        $results_profiles = $mysqli->query($sql_profiles);

        if (!$results_profiles) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $image_src = '';

        while ($row_profiles = $results_profiles->fetch_assoc() ) { 
            if ($row['profile_pic_id'] == $row_profiles['profile_pic_id']) { 
                $image_src = $row_profiles['image_src'];
            }
        } 
        $results_profiles->data_seek(0); 

        // close db connection
        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,500;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
    
    <!-- GAME CSS -->
    <link rel="stylesheet" href="../styles/profile.css">

	<title>Profile | Spotify Guessing Game</title>

    <?php include "../include-require/navbar.html" ?>

</head>
<body> 
    <div class="content">
        <h1>Profile</h1>
        <div class="user-container">
            <div class="left-col">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Email" value=<?php if ($row) { echo $row['email']; } ?>>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <label for="fav-genre">Favorite Genre</label>
                    <select name="genre_id" id="genre_id" class="dropdown">
                        <option value="" selected>--Select--</option>
                        <?php while ($row_genres = $results_genres->fetch_assoc() ) : ?>
                            <?php if ($row['fav_genre_id'] == $row_genres['genre_id']) : ?>
                                <option value="<?php echo $row_genres['genre_id']; ?>" selected >
                                    <?php echo $row_genres['genre_name']; ?>
                                </option>
                            <?php else : ?>
                                <option value="<?php echo $row_genres['genre_id']; ?>">
                                    <?php echo $row_genres['genre_name']; ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fav-song">Favorite Song</label>
                    <input type="text" class="form-control" id="fav-song" placeholder="song name" value="<?php if ($row) { echo $row['fav_song']; } ?>">
                </div>
                <p class="error-message"></p>
                <button type="submit" id="save-changes" class="save-changes-btn">Save Changes</button>
            </div>
            <div class="right-col">
                <img id="profile-pic" class="profile-pic" src="<?php echo $image_src; ?>" alt="user profile pic"/>
                <div class="">
                    <label for="fav-genre">Profile Pic</label>
                    <select name="profile_pic_id" id="profile_pic_id" class="pfp-dropdown">
                        <option value="" selected>--Select--</option>
                        <?php while ($row_profiles = $results_profiles->fetch_assoc() ) : ?>
                            <?php if ($row['profile_pic_id'] == $row_profiles['profile_pic_id']) : ?>
                                <option value="<?php echo $row_profiles['profile_pic_id']; ?>" selected >
                                    <?php echo $row_profiles['profile_pic_name']; ?>
                                </option>
                            <?php else : ?>
                                <option value="<?php echo $row_profiles['profile_pic_id']; ?>">
                                    <?php echo $row_profiles['profile_pic_name']; ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                    <p class="disclaimer">Save changes to see new image</p>
                </div>
                <button id="logout" class="logout-btn">Logout</button>
            </div>
        </div>
    </div>

    <?php include "../include-require/footer.html" ?>

    <!-- NavBar JS -->
    <script src="../javascript/navbar.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        // save changes
        document.getElementById('save-changes').addEventListener('click', function(e) {
            e.preventDefault();

            var user_email = document.querySelector('#email').value;
            var user_pass = document.querySelector('#password').value;
            var user_genre = document.querySelector('#genre_id').value;
            var user_song = document.querySelector('#fav-song').value;
            var user_profile = document.querySelector('#profile_pic_id').value;

            if (user_email == "" || user_genre == "" || user_song == "" || user_profile == "") {
                document.getElementsByClassName('error-message')[0].textContent = "Cannot have empty values.";
            }

            $.ajax({
                url: '../backend/profile_changes.php',
                type: 'POST',
                data: {
                    email: user_email,
                    pass: user_pass,
                    genre_id: user_genre,
                    fav_song: user_song,
                    profile_img: user_profile
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    if (response.status == 'success') {
                        window.location.href = response.redirect;
                    } 
                },
                error: function(e) {
                    console.log(e);
                    document.getElementsByClassName('error-message')[0].textContent = "error with submission.";
                }
            });
        });


        // logout
        document.getElementById('logout').addEventListener('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: '../backend/logout.php',
                type: 'POST',
                data: {
                    logout: true
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    if (response.status == 'success') {
                        window.location.href = response.redirect;
                    } 
                },
                error: function(e) {
                    console.log(e);
                    document.getElementsByClassName('error-message')[0].textContent = "error with submission.";
                }
            });
        });        

    </script>
</body>
</html>