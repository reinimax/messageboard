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
     * Retrieve the newest 10 posts
     * @return array/false
     */
    public function index()
    {
        $getPosts = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.user FROM posts 
            JOIN users ON (posts.user_id = users.id) ORDER BY posts.updated_at DESC LIMIT 10;
        SQL;

        try {
            $statement = $this->pdo->query($getPosts);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

    /**
     * Retrieve all posts of the current user
     * @return array/false
     */
    public function show()
    {
        $getPosts = <<<SQL
            SELECT * FROM posts WHERE user_id=:currentuser
            ORDER BY updated_at DESC;
        SQL;

        try {
            $statement = $this->pdo->prepare($getPosts);
            $statement->bindParam(':currentuser', $_SESSION['user_id'], PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
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

    /**
     * Deletes a post
     * @param int $id The id of the post to be deleted
     * @return array with the success- or error-message
     */
    public function delete($id)
    {
        $deletePost = <<<SQL
            DELETE FROM posts WHERE id=:id;
        SQL;

        try {
            $statement = $this->pdo->prepare($deletePost);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
        return ['success' => 'Message deleted'];
    }
}
