<?php 
require('session_check.php');
function login($username, $password, $email) {
    global $conn;
    $sql = "SELECT password, image_url FROM users WHERE username = '$username' AND email = '$email';";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if(mysqli_num_rows($result) == 1) {
        $hash = $row['password'];
        if(password_verify($password, $hash)) {
            $_SESSION['username'] = $username;
            $_SESSION['image_url'] = $row['image_url'];
            generate_session_hash($username);
            header("location: index.php");
        } else {
            return "Login Information Incorrect";
        }
    } else {
        return "User Does Not Exist";
    }
}
?>