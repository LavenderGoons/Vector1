<?php
    //WAMP default creds
    define('DB_SERVER', 'localhost:3306');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'vector1');
    //Categories array for easy change in the future
    $CATEGORIES = array('all'=>'All', 'general'=>'General', 'movies'=>'Movies', 'television'=>'Television', 
        'music'=>'Music', 'video_games'=>'Video Games', 'politics'=>'Politics', 'wasteland'=>'Wasteland');
    //Bad Pratice but this is for school
    define('HMAC', 'vector');
    $conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>