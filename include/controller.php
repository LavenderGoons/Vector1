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
        $val = array();
        $val['content'] = get_post_content($options);
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