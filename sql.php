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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id)
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
        title VARCHAR(100),
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE 
    )
SQL;

try {
    $pdo->query($createPosts);
} catch (PDOException $e) {
    echo $e;
}
