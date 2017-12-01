<?php
    require('include/post_utils.php');
    if(!check_session()) {
        header('location: login.php');
    }
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $post_category = 'all';
    if(isset($_GET['category'])) {
        $post_category = $_GET['category'];
    }

    $post_error = $post_result = $image_error = '';
    $title = $category = $content = $image_url = '';
    
    $title_error = $content_error = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        //Cant trim the title or the content
        $title = $_POST['title'];
        $image_url = trim($_POST['image_url']);
        $category = trim($_POST['category']);
        $content = $_POST['content'];

        // Validate all inputs
        if(empty($title)) {
            $title_error = 'Please Enter A Title';
        } else if(strlen($title) > 140) {
            $title_error = 'Title Is Too Long';
        }

        if(empty($content)) {
            $content_error = 'Please Enter Some Content';
        } else if(strlen($content) > 4500) {
            $title_error = 'Content Is Too Long';
        }

        if(!empty($image_url) && strlen($image_url) > 150) {
            $image_error = 'Image URL Is Too Long';
        } else if(!empty($image_url) && substr($image_url, 0, 3) != 'img' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            $image_error = 'Image URL Is Not Valid';
        }

        if(empty($title_error) && empty($content_error) && empty($image_error)) {
            $post_result =  create_forum_post($_SESSION['username'], $title, $image_url, $category, $content);
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
    <link rel="stylesheet" href="./css/index3.css"/>
    <link rel="stylesheet" href="./css/modal.css"/>
    <link rel="stylesheet" href="./css/font-awesome.min.css"/>  
  
    <link rel="apple-touch-icon" sizes="180x180" href="./img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="./img/favicons/favicon.ico">
    <link rel="manifest" href="./img/favicons/manifest.json">
    <link rel="mask-icon" href="./img/favicons/safari-pinned-tab.svg" color="#1976D2">
    <meta name="theme-color" content="#ffffff">
    <title>Home</title>
</head>
<body>
    <nav id="top-nav" class="navbar navbar-dark fixed-top">
        <a href="index.php" class="navbar-brand">
            <!-- Logo Image -->
            <img src="img/skull_icon.png" alt="" width="40" height="40">
        </a>
        <a id="user-ref" href=<?php if(isset($_SESSION['username'])) {echo '"user.php?user='.$_SESSION['username'].'"';} ?>>
            <div id="user-ref">
                <img src=<?php echo get_user_image(); ?> alt="" width="40" height="40">
                <span><?php if(isset($_SESSION['username'])) {echo $_SESSION['username'];} ?></span>
            </div>
        </a>
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
    <div class="content" id="content-index">
        <?php global $content_error; if(strlen($content_error) > 0){echo '<div class="error">'.$content_error.'</div>'; $content_error = '';}?>
        <?php global $title_error; if(strlen($title_error) > 0){echo '<div class="error">'.$title_error.'</div>'; $title_error = '';}?>
        <?php global $image_error; if(strlen($image_error) > 0){echo '<div class="error">'.$image_error.'</div>'; $image_error = '';}?>
        <div id="content-header">
            <button id="new-topic-btn">New</button>
        </div>
        <?php
            global $post_category;
            echo get_forum_posts($post_category, false);
        ?>
    </div>

    <!-- NEW POST MODAL -->
    <div id="post-blanket" class="blanket"></div>
    <div class="form-modal">
        <form action="#" method="post">
            <label for="title">Title</label>
            <input type="text" name="title" id="new-post-title" maxlength="140" required>
            <label for="image_url">Image</label>
            <input type="text" name="image_url" id="new-post-image" maxlength="120">
            <label for="category">Category</label>
            <select name="category" id="new-topic-category">
                <option value="general">General</option>
                <option value="movies">Movies</option>
                <option value="television">Television</option>
                <option value="music">Music</option>
                <option value="video_games">Video Games</option>
                <option value="politics">Politics</option>
                <option value="wasteland">Wasteland</option>
            </select>
            <label for="title">Content</label>
            <textarea name="content" id="new-topic-content" maxlength="4499" required></textarea>
            <input type="submit" value="Submit">
        </form>
    </div>


<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
    integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
    crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>