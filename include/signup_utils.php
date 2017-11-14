<?php 
require('session_check.php');
function create_user($username, $password, $email, $first_name, $last_name, $image_url) {
    global $conn;
    if(!user_exists($username)) {
        $sql = "INSERT INTO users VALUES (NULL, '$username', '$password', utc_timestamp, '$email', '$first_name', '$last_name', '$image_url', NULL)";
        $result = mysqli_query($conn, $sql);
        if($result) {
            $_SESSION['temp_user'] = $username;
            header('location: login.php');
        } else {
            return 'Database Error';
        }
    } else {
        return 'User With This Name Already Exists';
    }
}
?>