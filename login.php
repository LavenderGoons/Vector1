<?php 
    require('config.php');
    session_start();

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "select id from users where username = '$username' and password = '$password';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if(mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            header("location: index.php");
        } else {
            $error = "Error Loginning In.";
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css"/>
    <link rel="stylesheet" href="css/bootstrap-grid.css"/>
    <link rel="stylesheet" href="css/forms.css"/>
    <link rel="stylesheet" href="css/animation.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>
    
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="img/favicons/favicon.ico">
    <link rel="manifest" href="img/manifest.json">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#1976D2">
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
                            //var title1 = document.getElementById('title1');
                            //title1.innerHTML = '登录';
                            //title1.classList.add('chinese-title');
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
                <form action="" method="">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username-input" class="input"></input>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password-input" class="input"></input>
                    <input type='submit' name='submit' value="Login" class="input"></input>
                    <button type='button' name='signup' class="input"><a class='fake-button' href="signup.php">Sign Up</a></button>
                </form>
            </div>
            <div class="image-wrapper">
                <img src="img/skull_icon.png">
            </div>
        </div>
    </div>
</body>
</html>