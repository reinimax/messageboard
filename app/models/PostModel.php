<?php

namespace app\models;

use app\lib\MySql;
use PDO;
use PDOException;

class PostModel
{
    protected $pdo;

    public function __construct()
    {
        $config = require_once ROOT.'/config/database.php';
        $this->pdo = MySql::init($config);
    }

    /**
     * Retrieve all posts, newest first
     */
    public function index()
    {
        // test
        $test = 'I\'m a test!';
        return $test;
    }

    /**
     * Saves a new post in the database
     * @param array $data The validated data from the registration form
     * @return array with the success- or error-message
     */
    public function save($data)
    {
        $savePost = <<<SQL
            INSERT INTO posts (user_id, title, content) VALUES (:user, :title, :content);
        SQL;

        try {
            $statement = $this->pdo->prepare($savePost);
            $statement->bindParam(':user', $_SESSION['user_id'], PDO::PARAM_STR);
            $statement->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $statement->bindParam(':content', $data['message'], PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
        return ['success' => 'You successfully posted a message'];
    }
}
