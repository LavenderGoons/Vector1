<?php 
    require('include/signup_utils.php');
    if(check_session()) {
        header('location: index.php');
    }
    $signup_error = '';
    $username = $password = $email = $first_name = $last_name = $image_url = '';
    $user_error = $pass_error = $email_error = $first_name_error = $last_name_error = $image_error = '';

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $image_url = $_POST['image_url'];
        
        // Validate all inputs
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
            $email_error = 'Email Is Too Long';
        }

        if(empty($first_name)) {
            $first_name_error = 'Please Enter A First Name';
        } else if(strlen($first_name) > 30) {
            $first_name_error = 'First Name Is Too Long';
        }

        if(empty($last_name)) {
            $last_name_error = 'Please Enter A Last Name';
        } else if(strlen($last_name) > 40) {
            $last_name_error = 'Last Name Is Too Long';
        }

        if(!empty($image_url) && strlen($image_url) >= 150) {
            $image_error = 'Image URL Is Too Long';
        } else if(!empty($image_url) && substr($image_url, 0, 3) != 'img' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            $image_error = 'Image URL Is Not Valid';
        }

        //Hash password
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);

        //Check and create user
        if(empty($user_error) && empty($pass_error) && empty($email_error) && empty($first_name_error) && empty($last_name_error) && empty($image_error)) {
            $signup_error = create_user($username, $pass_hash, $email, $first_name, $last_name, $image_url);   
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
        <link rel="stylesheet" href="./css/modal.css"/>
        <link rel="stylesheet" href="./css/animation.css"/>
        <link rel="stylesheet" href="./css/font-awesome.min.css"/>
        
        <link rel="apple-touch-icon" sizes="180x180" href="./img/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./img/favicons/favicon-16x16.png">
        <link rel="icon" type="image/x-icon" href="../img/favicons/favicon.ico">
        <link rel="manifest" href="./img/favicons/manifest.json">
        <link rel="mask-icon" href="./img/favicons/safari-pinned-tab.svg" color="#1976D2">
        <meta name="theme-color" content="#ffffff">
        
        <title>Sign Up</title>
</head>
<body>
	<div class="content">
		<div id="signup-box">
			<div id="signup-header">
				<h1 id="title1" class="glitch-title">Sign Up</h1>
				<h1 id="title2" class="glitch-title">Sign Up</h1>
				<h1 id="title3" class="glitch-title">Sign Up</h1>
				<script>
					var charCounter = 0;
					window.setInterval(function(){
						charCounter++;
						if (charCounter % 8 == 0) {
							document.getElementById('title2').innerHTML = '寄存器';
							document.getElementById('title3').innerHTML = '寄存器';
						} else if (document.getElementById('title2').innerHTML != 'Sign Up') {
							var title1 = document.getElementById('title1');
							title1.innerHTML = 'Sign Up';
							title1.classList.remove('chinese-title');
							document.getElementById('title2').innerHTML = 'Sign Up';
							document.getElementById('title3').innerHTML = 'Sign Up';
						}
					}, 500);
				</script>
			</div>
			<div id="signup-main">
				<div class="profile-img-wrapper">
					<img src="img/skull_icon.png" alt="">
				</div>
				<form action="" method="post" id="signup-form">
					<div class="form-column">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username-input" class="input-small" maxlength="20"></input>
                        <?php global $user_error; if(!empty($user_error)){echo '<span id="user-error" class="input-error">'.$user_error.'</span>';}?>
                        
                        <label for="first-name">First Name</label>
                        <input type="text" name="first_name" id="first-name-input" class="input-small" maxlength="30"></input>
                        <?php global $first_name_error; if(!empty($first_name_error)){echo '<span id="first-name-error" class="input-error">'.$first_name_error.'</span>';}?>

                        <label for="last-name">Last Name</label>
                        <input type="text" name="last_name" id="last-name-input" class="input-small" maxlength="40"></input>
                        <?php global $last_name_error; if(!empty($last_name_error)){echo '<span id="last-name-error" class="input-error">'.$last_name_error.'</span>';}?>
					</div>
					<div class="form-column">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password-input" class="input-small"></input>
                        <?php global $pass_error; if(!empty($pass_error)){echo '<span id="password-error" class="input-error">'.$pass_error.'</span>';}?>
                        
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email-input" class="input-small" maxlength="50"></input>
                        <?php global $email_error; if(!empty($email_error)){echo '<span id="email-error" class="input-error">'.$email_error.'</span>';}?>
                        <input type="hidden" id="image_url" name="image_url" value="img/skull_icon.png" maxlength="120"></input>
                        
                        <label for="submit">&nbsp;</label>
                        <input type='submit' name='submit' value="Sign Up" class="input-small"></input>
					</div>
				</form>
                <?php
                //display error when signing up with username already existing
                global $signup_error;
                if(!empty($signup_error)) {
                    echo '<span class="input-error" id="signup-error">'. $signup_error .'</span>';
                }
                if(!empty($image_error)) {
                    echo '<span class="input-error" id="signup-error">'. $image_error .'</span>';
                }
                ?>
			</div>
        </div>
    </div>
    <div id="profile-image-blanket" class="blanket"></div>
    <div class="form-modal" id="image-url-modal">
        <form action="#" method="post">
            <label for="image">Image URL</label>
            <input type="text" name="image" id="profile-image-input">
            <button type='button' id='image-url-btn' class="input">Submit</button>
        </form>
    </div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
    integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
    crossorigin="anonymous"></script>
<script src="./js/bootstrap.min.js"></script>
<script src="./js/scripts.js"></script>
</body>
</html>