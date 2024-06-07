
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
    
    <!-- Login CSS -->
    <link rel="stylesheet" href="../styles/login.css">

	<title>Login | Spotify Guessing Game</title>

    <?php include "../include-require/navbar.html" ?>

</head>
<body> 
    <div class="content">
        <!-- <form action="profile.php" method="POST">  -->
        <form> 
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Password">
            </div>
            <!-- <div class="form-check new-user-check">
                <input type="checkbox" value="new" class="form-check-input" id="new-user-check">
                <label class="form-check-label" for="new-user-check">Check me if you are a new user!</label>
                <input type="hidden" id="user-check-hidden" value="">
            </div> -->
            <p class="error-message"></p>
            <button id="login" type="submit" class="submit-btn">Login</button>
            <p class="inline-txt">OR</p>
            <button id="sign-up" type="button" class="guest-btn">Sign Up</button>
        </form>
    </div>

    <?php include "../include-require/footer.html" ?>

    <!-- NavBar JS -->
    <script src="../javascript/navbar.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>

        document.querySelector('form').onsubmit = function(e) {
            e.preventDefault();

            var user_email = document.querySelector('#email').value;
            var user_pass = document.querySelector('#password').value;

            // sign in
            $.ajax({
                url: '../backend/login_confirmation.php',
                type: 'POST',
                data: {
                    email: user_email,
                    pass: user_pass,
                    user_new: "old"
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    if (response.status == 'success') {
                        console.log(response.redirect);
                        window.location.href = response.redirect;
                    } else {
                        document.getElementsByClassName('error-message')[0].textContent = response.message;
                    }
                },
                error: function(e) {
                    console.log(e);

                }
            });
        }

        // sign up
        document.getElementById('sign-up').addEventListener('click', function(e) {
            e.preventDefault();

            var user_email = document.querySelector('#email').value;
            var user_pass = document.querySelector('#password').value;

            $.ajax({
                url: '../backend/login_confirmation.php',
                type: 'POST',
                data: {
                    email: user_email,
                    pass: user_pass,
                    user_new: "new"
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    if (response.status == 'success') {
                        window.location.href = response.redirect;
                    } else {
                        document.getElementsByClassName('error-message')[0].textContent = response.message;
                    }
                },
                error: function(e) {
                    console.log(e);

                }
            });

        });

    </script>
</body>
</html>
