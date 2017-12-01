<?php 
    require('include/post_utils.php');
    if(!check_session()) {
        header('location: login.php');
    }

    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    if(!isset($_GET['post_id'])) {
        header('location: index.php');
    }
    $post_id = $_GET['post_id'];

    $post_category = 'all';
    if(isset($_GET['category'])) {
        $post_category = $_GET['category'];
    }

    //Create comment
    $content = $image_url = '';
    
    $content_error = $image_error = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $image_url = trim($_POST['image_url']);
        $content = $_POST['content'];

        if(empty($content)) {
            $content_error = 'Please Enter Content';
        } else if(strlen($content) > 4500) {
            $content_error = 'Content Is Too Long';
        }

        if(!empty($image_url) && strlen($image_url) > 150) {
            $image_error = 'Image URL Is Too Long';
        } else if(!empty($image_url) && substr($image_url, 0, 3) != 'img' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            $image_error = 'Image URL Is Not Valid';
        }

        if(empty($content_error) && empty($image_error)) {
            $comment_result = create_comment($_SESSION['username'], $post_id, $image_url, $content);
            //Reset values
            if($comment_result) {
                header('location: post.php?post_id='.$post_id.'&category='.$post_category);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./css/bootstrap.min.css"/>
    <!-- <link rel="stylesheet" href="./css/bootstrap-grid.css"/> -->
    <link rel="stylesheet" href="css/index3.css"/>
    <link rel="stylesheet" href="css/modal.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>  
  
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="img/favicons/favicon.ico">
    <link rel="manifest" href="img/favicons/manifest.json">
    <link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg" color="#1976D2">
    <meta name="theme-color" content="#ffffff">
    <title>Forum Post</title>
</head>
<body>
    <nav id="top-nav" class="navbar navbar-dark fixed-top">
        <a href="index.php" class="navbar-brand">
            <img src="img/skull_icon.png" alt="" width="40" height="40">
        </a>
        <div id="user-ref">
            <img src=<?php echo get_user_image(); ?> alt="" width="40" height="40">
            <span><?php if(isset($_SESSION['username'])) {echo $_SESSION['username'];} ?></span>
        </div>
        <button class="navbar-toggler navbar-toggler-right" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <?php
                    global $post_category;
                    echo generate_navbar_categories($post_category);
                ?>
                <li class="nav-item signout-item"><a href="" class="nav-link">Sign Out</a></li>
            </ul>
        </div>
    </nav>
    <div id="sidebar-wrapper">
        <div id="sidebar">
                <div id="sidebar-header">
                    <img src=<?php echo get_user_image(); ?> width="80px" height="80px" alt="" id="sidebar-img">
                    <span><?php if(isset($_SESSION['username'])) {echo $_SESSION['username'];} ?></span>
                </div>
                <div id="sidebar-content">
                    <ul id="sidebar-nav">
                        <?php
                            global $post_category;
                            echo generate_sidebar_categories($post_category);
                        ?>
                    </ul>
                    <div id="signout">
                        Sign Out
                    </div>
                </div>
            </div>
    </div>
    <div class="content" id="content-post">
        <?php global $content_error; if(strlen($content_error) > 0){echo '<div class="error">'.$content_error.'</div>'; $content_error = '';}?>
        <?php global $image_error; if(strlen($image_error) > 0){echo '<div class="error">'.$image_error.'</div>'; $image_error = '';}?>
        <div id="content-header">
            <button id="new-comment-btn">Reply</button>
        </div>
        <?php 
            global $post_id;
            echo get_post_and_comments($post_id);
        ?>
    </div>

    <!-- NEW COMMENT MODAL -->
    <div id="comment-blanket" class="blanket"></div>
    <div id="comment-modal" class="form-modal">
        <form action="" method="post">
            <label for="image_url">Image</label>
            <input type="text" name="image_url" id="new-comment-image" maxlength="120">
            <label for="title">Content</label>
            <textarea name="content" id="new-comment-content" maxlength="4499" required></textarea>
            <input type="submit" value="Submit">
        </form>
    </div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
    integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
    crossorigin="anonymous"></script>
    <script src="./js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>