<?php 
    session_start();
    require('config.php');
    function create_user($username, $password, $email, $first_name, $last_name, $image_url) {
        global $conn;
        if(!user_exists($username)) {
            $sql = "INSERT INTO users VALUES (NULL, '$username', '$password', utc_timestamp, '$email', '$first_name', '$last_name', '$image_url')";
            $result = mysqli_query($conn, $sql);
            if($result) {
                $_SESSION['username'] = $username;
                header('location: login.php');
            } else {
                return 'Database Error';
            }
        } else {
            return 'User With This Name Already Exists';
        }
    }

    function create_forum_post($username, $title, $image_url, $category, $content) {
        global $conn;
        $user_id = get_user_id($username);
        $sql = "INSERT INTO forum_posts VALUES (NULL, '$user_id', '$title', '$category', '$content', '$image_url', utc_timestamp)";
        $result = mysqli_query($conn, $sql);
        if($result) {
            $post_id = mysqli_insert_id($conn);
            $post = 'location: post.php?post_id=' . $post_id; 
            header($post);
        } else {
            return false;
        }
    }

    function create_comment($username, $post_id, $image_url, $content) {
        global $conn;
        $user_id = get_user_id($username);
        $sql = "INSERT INTO comments VALUES(NULL, $user_id, $post_id, '$content', utc_timestamp, '$image_url');";
        $result = mysqli_query($conn, $sql);
        if($result) {
            return true;
        } else {
            return false;
        }
    }

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

    function login($username, $password, $email) {
        global $conn;
        $sql = "SELECT password, image_url FROM users WHERE username = '$username' AND email = '$email';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if(mysqli_num_rows($result) == 1) {
            $hash = $row['password'];
            if(password_verify($password, $hash)) {
                $_SESSION['username'] = $username;
                $_SESSION['image_url'] = $row['image_url'];
                header("location: index.php");
            } else {
                return "Login Information Incorrect";
            }
        } else {
            return "User Does Not Exist";
        }
    }

    function generate_post_html($post_id, $username, $title, $category, $content, $image_url, $post_date, $is_post_head) {
        $str = '';
        // $is_post_head is the post at the top of comments, or with other posts
        if($is_post_head) {
            $str .= '<section class="forum-section no-cursor" data-post-id='.$post_id.'>';
        } else {
            $str .= '<section class="forum-section" data-post-id='.$post_id.'>';
        }
        $str .= '<div class="forum-header">';
        if(isset($image_url)) {
            $str .= '<img src="' . $image_url .'" width="70px" height="70px" alt=""></div>';
        } else {
            $str .= '<img src="img/skull_icon.png" width="70px" height="70px" alt=""></div>';
        }
        $str .= '<div class="forum-main"><div class="title-wrapper">';
        $str .= '<h4>';
        //Don't make title link when on post page
        if(!$is_post_head) {
            $str .= '<a class="post-link" href="post.php?post_id='.$post_id.'">'. $title . '</a></h4></div>';
        } else {
            $str .= $title . '</h4></div>';
        }
        $str .= '<div class="info-wrapper">';
        $str .= '<span class="post-user">'.$username.'</span>';
        $str .= '<span class="post-category">'.$category.'</span>';
        $str .= '<span class="post-date">'.$post_date.'</span></div></div>';
        if($is_post_head) {
            $str .= '<div class="post-content">';
        } else {
            $str .= '<div class="preview-content">';
        }
        if(isset($content)) {
            $str .= '<p>'.$content.'</p>';
        }        
        $str .= '</div></section>';
        return $str;
    }

    function generate_comment_html($comment_id, $username, $content, $image_url, $post_date) {
        $str = '<section class="post-comment" data-comment-id='.$comment_id.'>';
        $str .= '<div class="comment-header">';
        if(isset($image_url)) {
            $str .= '<img src="' . $image_url .'" width="70px" height="70px" alt="">';
        } else {
            $str .= '<img src="img/skull_icon.png" width="70px" height="70px" alt="">';
        }
        $str .= '<div class="info-wrapper-comment">';
        $str .= '<span class="post-user">'.$username.'</span>';
        $str .= '<span class="post-date">'.$post_date.'</span></div></div>';
        $str .= '<div class="comment-main"><p>'.$content.'</p></div></section>';
        return $str;
    }

    function get_forum_posts() {
        global $conn;
        //TODO Remove the content column and do an AJAX request on the client side.
        $sql = "SELECT u.username, u.image_url AS user_image, fp.post_id, fp.title, fp.category, fp.image_url AS post_image, fp.post_date, fp.content"; 
        $sql .= " FROM users u JOIN forum_posts fp on u.id = fp.user_id ORDER BY post_id DESC LIMIT 25";
        $result = mysqli_query($conn, $sql);
        $str = '';
        while($row = mysqli_fetch_assoc($result)) {
            $str .= generate_post_html($row['post_id'], $row['username'], $row['title'], $row['category'], $row['content'], $row['user_image'], $row['post_date'], false);
        }
        return $str;
    }

    function get_post_and_comments($post_id_in) {
        global $conn;
        $sql = "SELECT u.username, u.image_url AS user_image, fp.post_id, fp.title, fp.category, fp.image_url AS post_image, fp.post_date, fp.content"; 
        $sql .= " FROM users u JOIN forum_posts fp on u.id = fp.user_id WHERE fp.post_id = $post_id_in";
        //echo $sql;
        $result = mysqli_query($conn, $sql);
        $str = '';
        $row = mysqli_fetch_assoc($result);
        $post_id = $row['post_id'];
        $str .= generate_post_html($row['post_id'], $row['username'], $row['title'], $row['category'], $row['content'], $row['user_image'], $row['post_date'], true);

        $sql = "SELECT c.comment_id, c.user_id, c.post_id, c.content, c.comment_date, c.image_url AS comment_image, u.username, u.image_url AS user_image";
        $sql .= " FROM comments c JOIN users u ON c.user_id = u.id WHERE post_id = $post_id ORDER BY c.comment_id DESC";

        $result = mysqli_query($conn, $sql);
        if(gettype($result) == 'object') {
            while($row = mysqli_fetch_assoc($result)) {
                $str .= generate_comment_html($row['comment_id'], $row['username'], $row['content'], $row['user_image'], $row['comment_date']);
            }
        }
        return $str;
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