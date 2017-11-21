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

        if(isset($_POST['conf-username']) && $_POST['conf-username'] === $_SESSION['username']) {
            $result = delete_user($_POST['conf-username']);
            if($result) {
                // NEED ../login.php because controller in include dir
                header('location: ../login.php');
            }
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
        }
    }

    function fetch_more_posts($options) {
        // OPTION examples
        // page: index || post || user
        // category: all
        // post_id: 2 (if post)
        // last_post_id: 1 (if index || user)
        // last_comment_id: 1 (if post || user)
        $val = array();
        if($options['page'] == 'index') {
            $val['content'] = get_forum_posts($options['category'], $options);
        } else if($options['page'] == 'post') {
            $val['content'] = get_post_comments($options);
        }
        return $val;
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
?>