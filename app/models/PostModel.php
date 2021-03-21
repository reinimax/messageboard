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
     * Retrieve the posts, newest first
     * @return array/false The posts grouped by post id on success, false on failure
     */
    public function index()
    {
        $getPosts = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.id, users.user, users.avatar, tags.tag FROM posts
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id)
            JOIN users ON (posts.user_id = users.id) 
            ORDER BY posts.updated_at DESC;
        SQL;

        try {
            $statement = $this->pdo->query($getPosts);
            $result = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

    /**
     * Retrieve posts that fulfill the passed parameters
     * @param array $parameters The query and limit
     * @return array/false FALSE on failure, an array with the found rows and rowcount on success
     */
    public function search($parameters)
    {
        $getPostsByTitle = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.id, users.user, users.avatar, tags.tag FROM posts
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id) 
            JOIN users ON (posts.user_id = users.id) WHERE MATCH(posts.title) AGAINST(:query)
            ORDER BY posts.updated_at DESC;
        SQL;

        $getPostsByUser = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.id, users.user, users.avatar, tags.tag FROM posts
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id) 
            JOIN users ON (posts.user_id = users.id) WHERE MATCH(users.user) AGAINST(:query)
            ORDER BY posts.updated_at DESC;
        SQL;

        $getPostsAll = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.id, users.user, users.avatar, tags.tag FROM posts
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id) 
            JOIN users ON (posts.user_id = users.id) WHERE MATCH(posts.title, posts.content) AGAINST(:query) OR
            MATCH(users.user) AGAINST(:query) OR posts.id IN (
                SELECT posts.id FROM posts
                LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
                LEFT JOIN tags ON (pt.tag_id = tags.id) 
                WHERE MATCH(tags.tag) AGAINST(:query) 
            ) ORDER BY posts.updated_at DESC;
        SQL;

        $getPostsTags = <<<SQL
            SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, users.id, users.user, users.avatar, tags.tag FROM posts
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id) 
            JOIN users ON (posts.user_id = users.id) WHERE posts.id IN (
                SELECT posts.id FROM posts
                LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
                LEFT JOIN tags ON (pt.tag_id = tags.id) 
                WHERE MATCH(tags.tag) AGAINST(:query) 
            ) ORDER BY posts.updated_at DESC;
        SQL;

        try {
            switch ($parameters['limit']) {
                case 'title': $statement = $this->pdo->prepare($getPostsByTitle); break;
                case 'user': $statement = $this->pdo->prepare($getPostsByUser); break;
                case 'tag': $statement = $this->pdo->prepare($getPostsTags); break;
                default: $statement = $this->pdo->prepare($getPostsAll);
            }
            $statement->bindParam(':query', $parameters['query'], PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
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
     * @return array/false An array of the posts grouped by post id on success, FALSE on failure
     */
    public function show()
    {
        $getPosts = <<<SQL
            SELECT posts.*, tags.tag FROM posts 
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id)
            WHERE user_id=:currentuser
            ORDER BY updated_at DESC;
        SQL;

        try {
            $statement = $this->pdo->prepare($getPosts);
            $statement->bindParam(':currentuser', $_SESSION['user_id'], PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
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
        // save the post
        $savePost = <<<SQL
            INSERT INTO posts (user_id, title, content) VALUES (:user, :title, :content);
        SQL;

        try {
            $statement = $this->pdo->prepare($savePost);
            $statement->bindParam(':user', $_SESSION['user_id'], PDO::PARAM_INT);
            $statement->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $statement->bindParam(':content', $data['message'], PDO::PARAM_STR);
            $statement->execute();
            $post_id = $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }

        // save the connection between the post and the tag
        if (!empty($data['tag'])) {
            $savePostTag = <<<SQL
            INSERT INTO posts_tags (post_id, tag_id) VALUES ($post_id, :tag_id);
            SQL;
            try {
                $statement = $this->pdo->prepare($savePostTag);
                $statement->bindParam(':tag_id', $item, PDO::PARAM_INT);
                foreach ($data['tag'] as $item) {
                    $statement->execute();
                }
            } catch (PDOException $e) {
                return ['error' => $e->getMessage()];
            }
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

    /**
     * Gets the data of a post
     * @param int $id The id of the post
     * @return array
     */
    public function edit($id)
    {
        $getPost = <<<SQL
            SELECT posts.*, tags.* FROM posts 
            LEFT JOIN posts_tags AS pt ON (posts.id = pt.post_id)
            LEFT JOIN tags ON (pt.tag_id = tags.id) 
            WHERE posts.id=:id;
        SQL;

        try {
            $statement = $this->pdo->prepare($getPost);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
        if ($result === false) {
            return ['error' => 'Sorry, we couldn\'t find this post'];
        }
        return $result;
    }

    /**
     * Update a post
     * @param int $id The id of the post
     * @param $data The submitted data
     * @return array with the success- or error-message
     */
    public function update($id, $data)
    {
        $updatePost = <<<SQL
            UPDATE posts SET title=:title, content=:content WHERE id=:id;
        SQL;

        try {
            $statement = $this->pdo->prepare($updatePost);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $statement->bindParam(':content', $data['message'], PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->rowCount();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
        if ($result === 0) {
            return ['error' => 'Sorry, we couldn\'t find this post'];
        }

        // delete all connections of the updated post in the pivot table
        if (!empty($data['tag'])) {
            $deletePostTags = <<<SQL
            DELETE FROM posts_tags WHERE post_id=:id
            SQL;
            try {
                $statement = $this->pdo->prepare($deletePostTags);
                $statement->bindParam(':id', $id, PDO::PARAM_INT);
                $statement->execute();
            } catch (PDOException $e) {
                return ['error' => $e->getMessage()];
            }

            // save all connections of the updated post in the pivot table
            $savePostTag = <<<SQL
            INSERT INTO posts_tags (post_id, tag_id) VALUES (:post_id, :tag_id);
            SQL;
            try {
                $statement = $this->pdo->prepare($savePostTag);
                $statement->bindParam(':post_id', $id, PDO::PARAM_INT);
                $statement->bindParam(':tag_id', $item, PDO::PARAM_INT);
                foreach ($data['tag'] as $item) {
                    $statement->execute();
                }
            } catch (PDOException $e) {
                return ['error' => $e->getMessage()];
            }
        }

        return ['success' => 'You succesfully updated your post'];
    }

    /**
     * Get a list of all tags
     * @return array
     */
    public function getTags()
    {
        $getTags = <<<SQL
            SELECT * FROM tags;
        SQL;

        try {
            $statement = $this->pdo->query($getTags);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
        if ($result === 0) {
            return [];
        }
        return $result;
    }

    /**
     * Save a new tag
     * @param array $data The validated form entry
     * @return array with the success- or error-message
     */
    public function createtag($data)
    {
        $saveTag = <<<SQL
            INSERT INTO tags (tag) VALUES (:tag);
        SQL;

        try {
            $statement = $this->pdo->prepare($saveTag);
            $statement->bindParam(':tag', $data['newtag'], PDO::PARAM_STR);
            $statement->execute();
            if ($statement->rowCount() === 0) {
                return ['error' => 'Could not create new tag'];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['error' => 'This tag already exists!'];
            }
            return ['error' => $e->getMessage()];
        }
        return ['success' => 'Tag sucessfully created'];
    }
}
