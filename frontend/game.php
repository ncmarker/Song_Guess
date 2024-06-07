<?php 
    require '../config/config.php';

    if (!$_SESSION['logged_in']) {
        header('Location: ../frontend/login.php');
    } else {
        
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
    <link rel="stylesheet" href="../styles/game.css">

	<title>Game | Spotify Guessing Game</title>

    <?php include "../include-require/navbar.html" ?>

</head>
<body> 
    <div class="content">
        <h1>Spotify Guessing Game</h1>
        <button onclick="location.href='../backend/auth.php'; greyBtn();" id="auth-btn" class="auth-btn">Connect to Spotify</button>
        <p id="cnt-msg"></p>

        <h2>How it Works</h2>
        <hr />
        <p>
        Please authenticate with Spotify prior to starting! Pick your desired genre from the list of popular music genres below.
        When ready, click the start button. A song from the selected genre will
        begin to play, and you are to try and guess the name of the song. You
        will have 30 seconds to guess as many songs as you can by name (correct
        spelling is required). After each correct guess, a new song will begin
        to play. Good luck!
        </p>
        <p>* not all songs have a preview URL. if you cannot hear the song, click "skip"!</p>

        <h2>Genre List</h2>
        <hr />
        <div class="genre-btns-container">
            <div class="genre-btn pop-btn" id="pop-btn">Pop</div>
            <div class="genre-btn country-btn" id="country-btn">Country</div>
            <div class="genre-btn rap-btn" id="rap-btn">Rap</div>
            <div class="genre-btn jazz-btn" id="jazz-btn">Jazz</div>
            <div class="genre-btn electronic-btn" id="electronic-btn">Electronic</div>
        </div> 

        <h2>Game</h2>
        <hr />
        <div class="game-container">
            <div class="begin-container">
                <button id="play-btn" class="play-btn">Begin</button>
                <div class="trackers-container">
                    <div class="countdown-container">Time Remaining: <span id="timer">0</span>s</div>
                    <div class="correct-container">Correct Guesses: <span id="correct">0</span></div>
                    <p class="error-message"></p>
                </div>
            </div>
            <audio id="audioPlayer" controls></audio>
            <br />
            <label class="song-guess-label" for="song-guess">Song Guess:</label>
            <input
            type="text"
            id="song-guess"
            name="song-guess"
            placeholder="Song Name"
            />
            <button class="submit-btn">Submit</button>
            <button class="skip-btn">Skip</button>
        </div>  
    </div>

    <!-- NavBar JS -->
    <script src="../javascript/navbar.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Genre Button JS -->
    <script>
        var activeGenre = '';
        let tracks = [];
        let currentTrackIndex = 0;
        let correctGuesses = 0;
        let countdownTimer = 0;
        let gameDuration = 30;

        // button active state
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.genre-btn');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    buttons.forEach(btn => btn.classList.remove('active'));

                    this.classList.add('active');
                    activeGenre = this.innerHTML;
                });
            });
        });

        // play button
        document.querySelector('#play-btn').addEventListener('click', function() {
            if (!activeGenre) {
                document.getElementsByClassName('error-message')[0].textContent = "Select a genre first.";
                return;
            }
            var accessToken = "<?php echo $_SESSION['spotify_access_token']; ?>";

            fetch('https://api.spotify.com/v1/search?q=genre:%22' + activeGenre + '%22&type=track&limit=30', {
                headers: {
                    'Authorization': `Bearer ${accessToken}`
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log("tracks fetched: ");
                console.log(data.tracks.items);
                tracks =  data.tracks.items;

                startGame();
            })
            .catch(error => console.error('Error:', error));
        })

        // start game 
        function startGame() {
            if (tracks.length === 0) {
                console.error("No tracks loaded");
                return;
            }

            correctGuesses = 0;
            currentTrackIndex = 0;
            playTrack(tracks[currentTrackIndex]);

            // Initialize and start the timer
            let timeRemaining = gameDuration;
            document.getElementById('timer').textContent = timeRemaining;
            countdownTimer = setInterval(() => {
                timeRemaining--;
                document.getElementById('timer').textContent = timeRemaining;
                if (timeRemaining <= 0) {
                    endGame();
                }
            }, 1000);
        }

        function endGame() {
            clearInterval(countdownTimer);
            document.getElementById('audioPlayer').pause();
            alert("Game over! You guessed " + correctGuesses + " songs correctly.");
            // Reset game or ask if they want to play again
            $.ajax({
                url: '../backend/game_results.php',
                type: 'POST',
                data: {
                    score: correctGuesses
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                },
                error: function(e) {
                    console.log(e);
                }
            });
        }

        function playTrack(track) {
            const audioPlayer = document.getElementById('audioPlayer');
            audioPlayer.src = track.preview_url;  // Assuming 'preview_url' holds the playable track link
            audioPlayer.play();
            audioPlayer.onended = function() {
                // If the song ends before the time, load the next one or repeat if guess was wrong
                playTrack(tracks[currentTrackIndex]);
            };
        }


        document.querySelector('.submit-btn').addEventListener('click', function() {
            const userGuess = document.getElementById('song-guess').value;
            const currentTrack = tracks[currentTrackIndex];
            if (userGuess.toLowerCase() === currentTrack.name.toLowerCase()) {
                correctGuesses++;
                document.getElementById('correct').textContent = correctGuesses;
                currentTrackIndex++;  // Move to the next track
                document.getElementById('song-guess').value = "";
                if (currentTrackIndex < tracks.length) {
                    playTrack(tracks[currentTrackIndex]);
                } else {
                    endGame();  // No more tracks to play
                }
            } else {
                // If wrong, the current song will continue playing or restart
                playTrack(tracks[currentTrackIndex]);
            }
        });

        function greyBtn() {
            document.querySelector('#cnt-msg').innerHTML = 'connected!';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const skipButton = document.querySelector('.skip-btn');
            skipButton.addEventListener('click', function() {
                console.log('here');
                if (tracks.length === 0) {
                    console.log('No tracks loaded.');
                    return;
                }

                currentTrackIndex = (currentTrackIndex + 1) % tracks.length;

                const nextTrack = tracks[currentTrackIndex];
                if (nextTrack.preview_url) {
                    const audioPlayer = document.getElementById('audioPlayer');
                    audioPlayer.src = nextTrack.preview_url;
                    audioPlayer.play();
                    document.getElementById('song-guess').value = '';
                } else {
                    console.log('No preview available for this track.');
                }
            });
        });
    </script>

    <?php include "../include-require/footer.html" ?>

</body>
</html>