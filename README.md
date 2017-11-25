# Vector1

A project website for Advanced Web Development class.

## Description
Vector1 is a simple forum website. User's must have an account and be logged into the website to view or post forum content. 
User's can browse and submit posts to the forum in 1 of the eight categories. User's can commment on forum post's. 
User's can browse profile's and change their profile image.

## Info
The site is based on PHP 5.6 backend, using MySQL for the database.

### Tables
User Table
```
CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(20) NOT NULL,
 `password` varchar(100) NOT NULL,
 `created_date` datetime NOT NULL,
 `email` varchar(50) NOT NULL,
 `first_name` varchar(30) NOT NULL,
 `last_name` varchar(40) NOT NULL,
 `image_url` varchar(150) DEFAULT NULL,
 `token` varchar(64) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1
```

Forum Posts Table
```
CREATE TABLE `forum_posts` (
 `post_id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `title` varchar(140) NOT NULL,
 `category` varchar(20) NOT NULL,
 `content` varchar(4500) NOT NULL,
 `image_url` varchar(150) DEFAULT NULL,
 `post_date` datetime NOT NULL,
 PRIMARY KEY (`post_id`),
 KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1
```

Comments Table
```
CREATE TABLE `comments` (
 `comment_id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `post_id` int(11) NOT NULL,
 `content` varchar(4500) NOT NULL,
 `comment_date` datetime NOT NULL,
 `image_url` varchar(150) DEFAULT NULL,
 PRIMARY KEY (`comment_id`),
 KEY `user_id` (`user_id`),
 KEY `post_id` (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1
```

### Chmod Commands
```
find ./ -type f -exec chmod 644 {} \;
find ./ -type d -exec chmod 711 {} \;
```