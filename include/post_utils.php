<?php 
require('session_check.php');
//Create a comment in the database
function create_forum_post($username, $title, $image_url, $category, $content) {
    global $conn;
    $user_id = get_user_id($username);
    $sql = "INSERT INTO forum_posts VALUES (NULL, '$user_id', '$title', '$category', '$content', '$image_url', utc_timestamp)";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $post_id = mysqli_insert_id($conn);
        $post = 'location: post.php?post_id=' . $post_id.'&category='.$category; 
        header($post);
    } else {
        return false;
    }
}

//Create and comment in the database
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

//Generate the forum post based on the info
function generate_post_html($post_id, $username, $title, $category, $content, $user_image, $post_image, $post_date, $is_post_head) {
    $str = '';
    // $is_post_head is the post at the top of comments, or with other posts
    if($is_post_head) {
        $str .= '<section class="forum-section no-cursor" data-post-id='.$post_id.'>';
    } else {
        $str .= '<section class="forum-section" data-post-id='.$post_id.'>';
    }
    $str .= '<div class="forum-header">';
    if(isset($user_image)) {
        $str .= '<a href="user.php?user='.$username.'"><img src="' . $user_image .'" width="70px" height="70px" alt=""></a></div>';
    } else {
        $str .= '<a href="user.php?user='.$username.'"><img src="img/skull_icon.png" width="70px" height="70px" alt=""></a></div>';
    }
    $str .= '<div class="forum-main"><div class="title-wrapper">';
    $str .= '<h4>';
    //Don't make title link when on post page
    if(!$is_post_head) {
        $str .= '<a class="post-link" href="post.php?post_id='.$post_id.'&category='.$category.'">'. $title . '</a></h4></div>';
    } else {
        $str .= $title . '</h4></div>';
    }
    $str .= '<div class="info-wrapper">';
    $str .= '<span class="post-user"><a href="user.php?user='.$username.'">'.$username.'</a></span>';
    $str .= '<span class="post-category">'.format_category($category).'</span>';
    $str .= '<span class="post-date">'.$post_date.'</span></div></div>';
    if($is_post_head) {
        $str .= '<div class="post-content">';
    } else {
        $str .= '<div class="preview-content">';
    }
    $str .= '<img class="post-img" src="'.$post_image.'"></img>';
    $str .= '<p>';
    if(isset($content) && $content) {
        $str .= $content;
    }
    $str .= '</p>';     
    $str .= '</div></section>';
    return $str;
}

//Generate the html for each comment based on info
function generate_comment_html($comment_id, $username, $content, $user_image, $comment_image, $post_date) {
    $str = '<section class="post-comment" data-comment-id='.$comment_id.'>';
    $str .= '<div class="comment-header">';
    if(isset($user_image)) {
        $str .= '<a href="user.php?user='.$username.'"><img src="' . $user_image .'" width="70px" height="70px" alt=""></a>';
    } else {
        $str .= '<a href="user.php?user='.$username.'"><img src="img/skull_icon.png" width="70px" height="70px" alt=""></a>';
    }
    $str .= '<div class="info-wrapper-comment">';
    $str .= '<span class="post-user"><a href="user.php?user='.$username.'">'.$username.'</a></span>';
    $str .= '<span class="post-date">'.$post_date.'</span></div></div>';
    $str .= '<div class="comment-main">';
    if(isset($comment_image) && strlen($comment_image) != 0) {
        $str .= '<img class="post-img" src="'.$comment_image.'"></img>';
    }
    $str .= '<p>'.$content.'</p></div></section>';
    return $str;
}

//Get the forum posts for the index page
function get_forum_posts($post_category, $options) {
    global $conn;

    $post_category = strtolower($post_category);

    $sql = "SELECT u.username, u.image_url AS user_image, fp.post_id, fp.title, fp.category, fp.post_date";
    $sql .= " FROM users u JOIN forum_posts fp on u.id = fp.user_id";
    // Filter the posts by category, but not all
    if($post_category != "all") {
        //The space is HERE
        $sql .= " WHERE category = '$post_category'";
    }

    if ($options && isset($options['last_post_id'])) {
        if($post_category != "all") {
            $sql .= " AND fp.post_id < " . $options['last_post_id'];
        } else {
            $sql .= " WHERE fp.post_id < " . $options['last_post_id'];
        }
    }

    if ($options && isset($options['user'])) {
        $sql .= " AND u.id = " . get_user_id($options['user']);
    }

    if($options && isset($options['limit'])) {
        $sql .=  " ORDER BY post_id DESC LIMIT ".$options['limit'];
    } else {
        $sql .=  " ORDER BY post_id DESC LIMIT 10";
    }
    
    $result = mysqli_query($conn, $sql);
    $str = '';
    while($row = mysqli_fetch_assoc($result)) {
        $str .= generate_post_html($row['post_id'], $row['username'], $row['title'], $row['category'], false, $row['user_image'], null, $row['post_date'], false);
    }
    return $str;
}

//Fetch the post and comments for a specific post page
function get_post_and_comments($post_id_in) {
    global $conn;
    $sql = "SELECT u.username, u.image_url AS user_image, fp.post_id, fp.title, fp.category, fp.image_url AS post_image, fp.post_date, fp.content"; 
    $sql .= " FROM users u JOIN forum_posts fp on u.id = fp.user_id WHERE fp.post_id = $post_id_in";
    $result = mysqli_query($conn, $sql);

    $str = '';
    $row = mysqli_fetch_assoc($result);
    $post_id = $row['post_id'];
    $str .= generate_post_html($row['post_id'], $row['username'], $row['title'], $row['category'], $row['content'], $row['user_image'], $row['post_image'], $row['post_date'], true);

    $sql = "SELECT c.comment_id, c.user_id, c.post_id, c.content, c.comment_date, c.image_url AS comment_image, u.username, u.image_url AS user_image";
    $sql .= " FROM comments c JOIN users u ON c.user_id = u.id WHERE post_id = $post_id ORDER BY c.comment_id DESC LIMIT 10";
    $row = null;
    
    $result = mysqli_query($conn, $sql);
    if(gettype($result) == 'object') {
        while($row = mysqli_fetch_assoc($result)) {
            $str .= generate_comment_html($row['comment_id'], $row['username'], $row['content'], $row['user_image'], $row['comment_image'], $row['comment_date']);
        }
    }
    return $str;
}

function get_post_comments($options) {
    global $conn;

    $sql = "SELECT c.comment_id, c.user_id, c.post_id, c.content, c.comment_date, c.image_url AS comment_image, u.username, u.image_url AS user_image";
    $sql .= " FROM comments c JOIN users u ON c.user_id = u.id";

    if($options && isset($options['post_id'])) {
        $sql .= " WHERE post_id = ".$options['post_id'];
    } else if($options && isset($options['user'])) {
        $sql .= " WHERE c.user_id = ".get_user_id($options['user']);
    }
    
    if($options && isset($options['last_comment_id'])) {
        $sql .= " AND c.comment_id < ".$options['last_comment_id'];
    }

    if($options && isset($options['limit'])) {
        $sql .=  " ORDER BY c.comment_id DESC LIMIT ".$options['limit'];
    } else {
        $sql .=  " ORDER BY c.comment_id DESC LIMIT 10";
    }
    $str = '';
    $result = mysqli_query($conn, $sql);
    if(gettype($result) == 'object') {
        while($row = mysqli_fetch_assoc($result)) {
            $str .= generate_comment_html($row['comment_id'], $row['username'], $row['content'], $row['user_image'], $row['comment_image'], $row['comment_date']);
        }
    }
    return $str;
}

function get_post_content($options) {
    global $conn;
    $sql = "SELECT content, image_url FROM forum_posts WHERE post_id = ".$options['post_id'];
    $str = '';
    $val = array();
    $result = mysqli_query($conn, $sql);
    if(gettype($result) == 'object') {
        $row = mysqli_fetch_assoc($result);
        $val['content'] = $row['content'];
        $val['image_url'] = $row['image_url'];

    }
    return $val;
}
?>