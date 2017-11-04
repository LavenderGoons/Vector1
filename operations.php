<?php 
    require('config.php');
    function create_user($username, $password, $email, $first_name, $last_name, $image_url) {
        global $conn;
        if(!user_exists($username)) {
            $sql = "insert into users values(NULL, '$username', '$password', utc_timestamp, '$email', '$first_name', '$last_name', '$image_url')";
            $result = mysqli_query($conn, $sql);
            if($result) {
                $_SESSION['username'] = $username;
                header('location: login.php');
            }
        } else {
            return 'User With This Name Already Exists';
        }
    }

    function user_exists($username) {
        global $conn;
        $sql = "select username from users where username = '$username';";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
?> 