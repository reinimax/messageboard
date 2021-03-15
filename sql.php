<?php

use app\lib\MySql;

require_once './vendor/autoload.php';
$config = require_once './config/database.php';

try {
    $pdo = MySql::init($config);
} catch (PDOException $e) {
    echo $e;
}


// These are the SQL statements with which I created my database and tables.
$createDB = <<<SQL
    CREATE DATABASE IF NOT EXISTS messageboard;
SQL;

$createUsers = <<<SQL
    CREATE TABLE IF NOT EXISTS users(
        id INT UNSIGNED AUTO_INCREMENT,
        user VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        pwd VARCHAR(255) NOT NULL,
        descr VARCHAR(255),
        location VARCHAR(100),
        birthday DATE,
        avatar VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id),
        FULLTEXT(user)
    )
SQL;

try {
    $pdo->query($createUsers);
} catch (PDOException $e) {
    echo $e;
}

$createPosts = <<<SQL
    CREATE TABLE IF NOT EXISTS posts(
        id INT UNSIGNED AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        title VARCHAR(100) NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
        FULLTEXT(title,content)
    )
SQL;

try {
    $pdo->query($createPosts);
} catch (PDOException $e) {
    echo $e;
}

$createTags = <<<SQL
    CREATE TABLE IF NOT EXISTS tags(
        id INT UNSIGNED AUTO_INCREMENT,
        tag VARCHAR(100) NOT NULL UNIQUE,
        PRIMARY KEY(id),
        FULLTEXT(tag)
    )
SQL;

try {
    $pdo->query($createTags);
} catch (PDOException $e) {
    echo $e;
}

$createPivot = <<<SQL
    CREATE TABLE IF NOT EXISTS posts_tags(
        post_id INT UNSIGNED NOT NULL,
        tag_id INT UNSIGNED NOT NULL,
        PRIMARY KEY(post_id,tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON UPDATE CASCADE ON DELETE CASCADE
    )
SQL;

try {
    $pdo->query($createPivot);
} catch (PDOException $e) {
    echo $e;
}
