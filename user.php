<?php 
    require('include/user_utils.php');
    
    if(!check_session()) {
        header('location: login.php');
    }

    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $post_category = 'none';

    $user = $_SESSION['username'];
    if(isset($_GET['user'])) {
        $user= $_GET['user'];
    }

    $result = '';
    if(isset($_GET['result']) && $_GET['result'] == 'failed') {
        $result = 'Password Change Failed';
    }
    
    $image_error = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if($_SESSION['username'] == $user) {
            $new_image = $_POST['image'];
            if(!empty($new_image) && strlen($new_image) > 150) {
                $image_error = 'New Image URL Is Too Long';
                exit();
            } else if(!empty($image_url) && substr($image_url, 0, 3) != 'img' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
                $image_error = 'Image URL Is Not Valid';
                exit();
            }
            $result = update_user_image($new_image, $user);
            if(!$result) {
                $image_error = 'Problem Updating Image';
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
    <link rel="stylesheet" href="./css/index3.css"/>
    <link rel="stylesheet" href="./css/modal.css"/>
    <link rel="stylesheet" href="./css/user.css"/>
    <link rel="stylesheet" href="./css/font-awesome.min.css"/>  
  
    <link rel="apple-touch-icon" sizes="180x180" href="./img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./img/favicons/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../img/favicons/favicons/favicon.ico">
    <link rel="manifest" href="./img/favicons/manifest.json">
    <link rel="mask-icon" href="./img/favicons/safari-pinned-tab.svg" color="#1976D2">
    <meta name="theme-color" content="#ffffff">
    <title>User | <?php if(isset($_SESSION['username'])) {echo $_SESSION['username'];} ?></title>
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
    <div class="content" id="content-user">
        <div class="error"><span><?php global $result; if(strlen($result) > 0){echo $result;}?></span></div>
        <div class="user-outer-wrapper">
            <div class="user-wrapper">
                <div class="user-img-wrapper">
                    <img src=<?php 
                        global $user; 
                        echo get_user_profile_image($user); 
                        ?> alt="">
                </div>
                <div class="user-container">
                    <h3 id="username-display"><?php 
                        global $user; 
                        echo $user; 
                        ?></h3>
                    <h3 id="email-display"><?php 
                        global $user; 
                        echo get_user_profile_email($user); 
                        ?></h3>
                        <?php
                            global $user; 
                            echo generate_delete_user_btn($user);
                            echo generate_change_pass_btn($user);
                        ?>
                        
                    <?php
                        global $image_error; 
                        if(!empty($image_error)) {
                            echo '<h3 class="error">'.$image_error.'</h3>';
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="user-content-sort">
        <select name="sort_content" id="select-user-content">
                <option value="comments">Comments</option>
                <option value="posts" selected="selected">Posts</option>
            </select>
        </div>
        <div class="user-content-box">
            <?php 
                global $user;
                echo get_user_posts($user);
            ?>
        </div>
    </div>

    <?php 
        global $user;
        $val = generate_user_image_modal($user);
        if($val) {
            echo $val;
        }

        $del = generate_delete_user_modal($user);
        if($del) {
            echo $del;
        }

        $pass = generate_change_pass_modal($user);
        if($pass) {
            echo $pass;
        }
    ?>
    

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
    integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
    crossorigin="anonymous"></script>
    <script src="./js/bootstrap.min.js"></script>
<script src="./js/scripts.js"></script>
</body>
</html>