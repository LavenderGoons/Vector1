<?php
    require('operations.php');
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(isset($_POST['command'])) {
            process_command($_POST['command']);
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
?>