<?php
include('operations.php');

function check_session() {
    if(isset($_SESSION['username'])) {
        if(!isset($_COOKIE['vector_session'])) {
            generate_session_hash($_SESSION['username']);
        }
        return true;
    } else if(isset($_COOKIE['vector_session'])) {
        $vector_cookie = $_COOKIE['vector_session'];
        return check_cookie($vector_cookie);
    } 
}

function generate_session_hash($username) {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
    store_token($username, $token);
    $cookie = $username . ':' . $token;
    $hash = hash_hmac("sha256", $cookie, HMAC);
    $cookie .= ':' . $hash;
    setcookie('vector_session', $cookie, time()+60*60*24*30, '/');
}

function store_token($username, $token) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "UPDATE users SET token = '$token' WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
}

function get_user_token($username) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "SELECT token FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['token'];
    }
    return false;
}

function check_cookie($cookie) {
    list($username, $token, $hash) = explode(':', $cookie);
    if(!hash_equals(hash_hmac("sha256", $username .':'.$token, HMAC), $hash)) {
        return false;
    }
    $fetched_token = get_user_token($username);
    if($fetched_token && hash_equals($fetched_token, $token)) {
        $_SESSION['username'] = $username;
        return true;
    }
}
?>