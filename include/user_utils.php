<?php
require('post_utils.php');

function get_user_profile_image($username) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "SELECT image_url FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['image_url'];
    }
    return 'img/skull_icon.png';
}

function get_user_profile_email($username) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "SELECT email FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['email'];
    }
    return 'Unknown';
}

function generate_user_image_modal($username) {
    if($username === $_SESSION['username']) {
        return '<div id="user-image-blanket" class="blanket"></div><div class="form-modal" id="image-url-modal">
            <form action="#" method="post">
                <label for="image">Image URL</label>
                <input type="text" name="image" id="profile-image-input">
                <input type="submit" value="Submit">
            </form>
        </div>';
    }
    return false;
}

function generate_delete_user_btn($username) {
    if($username === $_SESSION['username']) {
        return '<button type="button" name="delete-user" id="delete-user">Delete User</button>';
    }
}

function generate_change_pass_btn($username) {
    if($username === $_SESSION['username']) {
        return '<button type="button" name="change-pass" id="change-pass">Change Password</button>';
    }
}

function generate_delete_user_modal($username) {
    if($username === $_SESSION['username']) {
        return '<div id="delete-user-blanket" class="blanket"></div><div class="form-modal" id="delete-user-modal">
            <form action="include/controller.php" method="post">
                <label for="username">Confirm Username</label>
                <input type="text" name="conf_username" id="confirm-user-input">
                <input type="submit" value="Submit">
            </form>
        </div>';
    }
}

function generate_change_pass_modal($username) {
    if($username === $_SESSION['username']) {
        return '<div id="change-pass-blanket" class="blanket"></div><div class="form-modal" id="change-pass-modal">
            <form action="include/controller.php" method="post">
            <label for="pass_username">Username</label>
            <input type="text" name="pass_username">
            <label for="email">Email</label>
            <input type="email" name="email">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password">
            <input type="hidden" name="change_password" value="change_password"/>
            <input type="submit" value="Submit">
        </form>
    </div>';
    }
}

function update_user_image($new_image, $username) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "UPDATE users SET image_url = '$new_image' WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $_SESSION['image_url'] = $new_image;
        return true;
    }
    return false;
}

function get_user_posts($username) {
    global $conn;
    $user_id = get_user_id($username);

    $sql = "SELECT u.username, u.image_url AS user_image, fp.post_id, fp.title, fp.category, fp.image_url AS post_image, fp.post_date, fp.content"; 
    $sql .= " FROM users u JOIN forum_posts fp on u.id = fp.user_id WHERE fp.user_id = $user_id ORDER BY post_id DESC";

    $str = '';
    $result = mysqli_query($conn, $sql);
    if(gettype($result) == 'object') {
        while($row = mysqli_fetch_assoc($result)) {
            $str .= generate_post_html($row['post_id'], $row['username'], $row['title'], $row['category'], false, $row['user_image'], null, $row['post_date'], false);
        }
    }
    return $str;
}
?>