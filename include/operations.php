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

    //Build the categories in the sidebar
    function generate_sidebar_categories($post_category) {
        //Get the global config file from the config file
        global $CATEGORIES;
        $post_category = strtolower($post_category);
        $str = "";
        foreach($CATEGORIES as $k => $v) {
            $item = '<li class="list-item';
            //Assigned selected class
            if($post_category == $k) {
                $item .= ' selected';
            }
            $item .= '"><a href="index.php?category=' . $k .'"';
            $item .= ' class="nav-link">' . $v . '</a></li>';
            $str .= $item;
        }
        return $str;
    }

    //Build the categories in the navbar
    function generate_navbar_categories($post_category) {
        //Get the global config file from the config file
        global $CATEGORIES;
        $post_category = strtolower($post_category);
        $str = "";
        foreach($CATEGORIES as $k => $v) {
            $item = '<li class="nav-item';
            if($post_category == $k) {
                $item .= ' active';
            }
            $item .= '"><a href="index.php?category=' . $k .'"';
            $item .= ' class="nav-link">' . $v . '</a></li>';
            $str .= $item;
        }
        return $str;
    }

    //Categories stored in all lower case with spaces as _
    //Format them to spaces and upper case words
    function format_category($category) {
        if(strrpos($category, "_")) {
            $category = str_replace("_", " ", $category);
        }
        return ucwords($category);
    }


    function hash_equals2($str1, $str2) {
        if(function_exists('hash_equals')) {
            return hash_equals($str1, $str2);
        }
        if(strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }
?> 