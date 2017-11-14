<?php 
    session_start();
    require('config.php');
    function user_exists($username) {
        global $conn;
        $sql = "SELECT username FROM users WHERE username = '$username';";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_user_id($username) {
        global $conn;
        $sql = "SELECT id FROM users WHERE Username = '$username';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    function get_user_image() {
        if(isset($_SESSION['username'])) {
            if(isset($_SESSION['image_url'])) {
                return '"' . $_SESSION['image_url'] . '"';
            } else {
                return '"./img/skull_icon.png"';
            }
        }
    }
?> 