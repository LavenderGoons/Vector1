<?php 
    require('include/login_utils.php');
    if(check_session()) {
        header('location: index.php');
    }
    $login_error = '';
    $username = $password = $email = '';
    $user_error = $pass_error = $email_error = '';

    if(isset($_SESSION['temp_user'])) {
        $username = $_SESSION['temp_user'];
    }

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        if(empty($username)) {
            $user_error = 'Please Enter A Username';
        } else if(strlen($username) > 20) {
            $user_error = 'Username Is Too Long';
        }

        if(empty($password)) {
            $pass_error = 'Please Enter A Password';
        }

        if(empty($email)) {
            $email_error = 'Please Enter A Email';
        } else if(strlen($email) > 50) {
            $user_error = 'Email Is Too Long';
        }

        if(empty($user_error) && empty($pass_error) && empty($email_error)) {
            $login_error = login($username, $password, $email);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./css/bootstrap.min.css"/>
    <!-- <link rel="stylesheet" href="./css/bootstrap-grid.css"/> -->
    <link rel="stylesheet" href="./css/forms.css"/>
    <link rel="stylesheet" href="./css/animation.css"/>
    <link rel="stylesheet" href="./css/font-awesome.min.css"/>
    
    <link rel="apple-touch-icon" sizes="180x180" href="./img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="./img/favicons/favicon.ico">
    <link rel="manifest" href="./img/favicons/manifest.json">
    <link rel="mask-icon" href="./img/favicons/safari-pinned-tab.svg" color="#1976D2">
    <meta name="theme-color" content="#ffffff">
    
    <title>Login</title>
</head>
<body>
    <div class="content-login">
        <div id="login-box">
            <div id="login-header">
                <h1 id="title1" class="glitch-title">Login</h1>
                <h1 id="title2" class="glitch-title">Login</h1>
                <h1 id="title3" class="glitch-title">Login</h1>
                <script>
                    var charCounter = 0;
                    window.setInterval(function(){
                        charCounter++;
                        if (charCounter % 8 == 0) {
                            document.getElementById('title2').innerHTML = '登录';
                            document.getElementById('title3').innerHTML = '登录';
                        } else if (document.getElementById('title2').innerHTML != 'Login') {
                            var title1 = document.getElementById('title1');
                            title1.innerHTML = 'Login';
                            title1.classList.remove('chinese-title');
                            document.getElementById('title2').innerHTML = 'Login';
                            document.getElementById('title3').innerHTML = 'Login';
                        }
                    }, 500);
                </script>
            </div>
            <div id="login-content">
                <form action="" method="post">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username-input" class="input" maxlength="20" <?php if(!empty($username)){echo "value='$username'";} ?>></input>
                    <?php global $user_error; if(!empty($user_error)){echo '<span id="user-error" class="input-error">'.$user_error.'</span>';}?>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email-input" class="input" maxlength="50"></input>
                    <?php global $email_error; if(!empty($email_error)){echo '<span id="email-error" class="input-error">'.$email_error.'</span>';}?>

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password-input" class="input"></input>
                    <?php global $pass_error; if(!empty($pass_error)){echo '<span id="password-error" class="input-error">'.$pass_error.'</span>';}?>

                    <input type='submit' name='submit' value="Login" class="input"></input>
                    <a class='fake-button' href="signup.php">Sign Up</a>
                </form>
                <?php
                //display error when logging in
                global $login_error;
                if(!empty($login_error)) {
                    echo '<span class="input-error" id="login-error">'. $login_error .'</span>';
                }
                ?>
            </div>
            <div class="image-wrapper">
                <img src="img/skull_icon.png">
            </div>
        </div>
    </div>
</body>
</html>