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

        $sql = "SELECT username, high_score, genre_name, fav_song FROM users LEFT JOIN genres ON users.fav_genre_id = genres.genre_id ORDER BY high_score IS NULL, high_score DESC;";

        $results = $mysqli->query($sql);

        if (!$results) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

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
    <link rel="stylesheet" href="../styles/leaderboard.css">

	<title>Leaderboard | Spotify Guessing Game</title>

    <?php include "../include-require/navbar.html" ?>

</head>
<body> 
    <div class="content">
        <h1>Leaderboard</h1>
        <form class="search-container">
            <label for="search-input"></label>
            <input type="text" class="search-input" id="search-input" placeholder="search any username...">
            <button type="submit" class="search-btn">Search</button>
        </form>
        <p class="error-message"></p>
        <table class="leaderboard-table"> 
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Highscore</th>
                    <th>Favorite Genre</th>
                    <th>Favorite Song</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo ($row['high_score'] ? $row['high_score'] : '--'); ?></td>
                        <td><?php echo ($row['genre_name'] ? $row['genre_name'] : '--'); ?></td>
                        <td><?php echo ($row['fav_song'] ? $row['fav_song'] : '--'); ?></td>
                    </tr>
                <?php endwhile; ?>
        </tbody>    
        </table>
        <!-- <div class="paging">
            <button class="prev-btn"><</button>
            <p>page: <span id="page-num">1</span></p>
            <button class="next-btn">></button>
        </div> -->
    </div>

    <?php include "../include-require/footer.html" ?>

    <!-- NavBar JS -->
    <script src="../javascript/navbar.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        // search
        document.querySelector('form').onsubmit = function(e) {
            e.preventDefault();

            var $username = document.querySelector('#search-input').value;

            $.ajax({
                url: '../backend/search_users.php',
                type: 'POST',
                data: {
                    username: $username
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    document.querySelector('tbody').innerHTML = '';

                    for (var i=0; i<response.length; i++) {
                        displayRow(response[i].username, response[i].fav_song, response[i].high_score,response[i].genre_name);
                    }
                },
                error: function(e) {
                    console.log(e);
                    document.getElementsByClassName('error-message')[0].textContent = "error with search.";
                }
            });
        };

        function displayRow(username, fav_song, high_score, genre_name) {
            var tr = document.createElement('tr');
            var td_username = document.createElement('td');
            var td_song = document.createElement('td');
            var td_score = document.createElement('td');
            var td_genre = document.createElement('td');

            td_username.innerHTML = username;
            td_song.innerHTML = (fav_song ? fav_song : '--');
            td_score.innerHTML = (high_score ? high_score : '--');
            td_genre.innerHTML = (genre_name ? genre_name : '--');

            tr.appendChild(td_username);
            tr.appendChild(td_score);
            tr.appendChild(td_genre);
            tr.appendChild(td_song);

            document.querySelector('tbody').appendChild(tr);
        }
    </script>
</body>
</html>