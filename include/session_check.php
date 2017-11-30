<?php
require('operations.php');

/* 
Do the full token check, generation
*/
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

/* 
Generate the hash token and store it in the DB and as a cookie
*/
function generate_session_hash($username) {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
    store_token($username, $token);
    $cookie = $username . ':' . $token;
    $hash = hash_hmac("sha256", $cookie, HMAC);
    $cookie .= ':' . $hash;
    setcookie('vector_session', $cookie, time()+60*60*24*30, '/');
}

/*
Store the users generated token
*/
function store_token($username, $token) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "UPDATE users SET token = '$token' WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
}

/* 
Get the users token and image
On loging the image_url will be set as a session variable
Since not loggging in, get image in same query as token
*/
function get_user_token_and_image($username) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "SELECT token, image_url FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $row = mysqli_fetch_assoc($result);
        return array('token'=>$row['token'], 'image_url'=>$row['image_url']);
    }
    return false;
}

function check_cookie($cookie) {
    list($username, $token, $hash) = explode(':', $cookie);
    if(!hash_equals2(hash_hmac("sha256", $username .':'.$token, HMAC), $hash)) {
        return false;
    }
    $user_array = get_user_token_and_image($username);
    $fetched_token = $user_array['token'];
    $image_url = $user_array['image_url'];
    if($fetched_token && hash_equals2($fetched_token, $token)) {
        $_SESSION['username'] = $username;
        $_SESSION['image_url'] = $image_url;
        return true;
    }
}
?>