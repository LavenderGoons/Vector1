<?php
    require('post_utils.php');
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(isset($_POST['command'])) {
            $options = false;
            //If options set get them
            if(isset($_POST['options'])) {
                $options = $_POST['options'];
            }
            process_command($_POST['command'], $options);
        }

        if(isset($_POST['conf_username']) && $_POST['conf_username'] === $_SESSION['username']) {
            $result = delete_user($_POST['conf_username']);
            if($result) {
                // NEED ../login.php because controller in include dir
                header('location: ../login.php');
            }
        }

        if(isset($_POST['change_password']) && $_POST['pass_username'] === $_SESSION['username']) {
            $result = change_password($_POST['pass_username'], $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['new_password']);
            if($result != 0){$result='success';}else{$result='failed';}
            header('location: ../user.php?result='.$result);
        }
    }
    
    function process_command($command, $options) {
        switch ($command) {
            case "signout":
            echo json_encode(signout());
            break;
            case "load_posts":
            echo json_encode(fetch_more_posts($options));
            break;
            case "get_content":
            echo json_encode(get_preview_content($options));
            break;
        }
    }

    function fetch_more_posts($options) {
        $val = array();
        if($options['page'] == 'user' && strlen($options['user']) == 0) {
            $options['user'] = $_SESSION['username'];
        }
        if($options['page'] == 'index' || ($options['page'] == 'user' && $options['user_sort'] == 'posts')) {
            $val['content'] = get_forum_posts($options['category'], $options);
        } else if($options['page'] == 'post' || ($options['page'] == 'user' && $options['user_sort'] == 'comments')) {
            $val['content'] = get_post_comments($options);
        }
        return $val;
    }

    function get_preview_content($options) {
        return get_post_content($options);
    }

    function signout() {
        session_unset();
        session_destroy();
        setcookie('vector_session', "", time()-3600, '/');
        return array('result'=>'success');
    }

    function delete_user($username) {
        global $conn;
        $user_id = get_user_id($username);
        $sql = "UPDATE users SET username = '[FEDAYKIN]', password = '[DELETED]', email = '[DELETED]', 
            first_name = '[DELETED]', last_name = '[DELETED]', image_url = 'img/skull_icon.png', token = NULL WHERE id = '$user_id'";
        $result = mysqli_query($conn, $sql);
        signout();
        return $result;
    }

    function change_password($username, $email, $first_name, $last_name, $new_password) {
        global $conn;
        $user_id = get_user_id($username);
        //Hash password
        $pass_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "SELECT email, first_name, last_name FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        if(gettype($result) != 'object') {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        if($row['email'] != $email || $row['first_name'] != $first_name || $row['last_name'] != $last_name) {
            return 0;
        }
        $sql = "UPDATE users SET password = '$pass_hash' WHERE email = '$email' AND first_name = '$first_name' AND last_name = '$last_name'";
        $result = mysqli_query($conn, $sql);
        return $result;
    }
?>