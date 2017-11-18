<?php
    require('operations.php');
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(isset($_POST['command'])) {
            process_command($_POST['command']);
        }
        if(isset($_POST['conf-username']) && $_POST['conf-username'] === $_SESSION['username']) {
            $result = delete_user($_POST['conf-username']);
            if($result) {
                // NEED ../login.php because controller in include dir
                header('location: ../login.php');
            }

        }
    }

    function process_command($command) {
        switch ($command) {
            case "signout":
            echo json_encode(signout());
            break;
        }
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