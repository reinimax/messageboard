<?php

namespace app\models;

use app\lib\MySql;
use PDO;
use PDOException;

class HomeModel
{
    protected $pdo;

    public function __construct()
    {
        $config = require_once ROOT.'/config/database.php';
        $this->pdo = MySql::init($config);
    }

    /**
     * Saves a new user in the database
     * @param array $data The validated data from the registration form
     * @return array with the success- or error-message
     */
    public function register($data)
    {
        $registerUser = <<<SQL
            INSERT INTO users (user, email, pwd) VALUES (:user, :email, :pwd);
        SQL;

        $pwd = password_hash($data['pwd'], PASSWORD_BCRYPT);

        try {
            $statement = $this->pdo->prepare($registerUser);
            $statement->bindParam(':user', $data['user'], PDO::PARAM_STR);
            $statement->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $statement->bindParam(':pwd', $pwd, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            // handle error for duplicate entries
            if ($e->getCode() == 23000) {
                return ['error' => 'User or email already exists'];
            }
        }
        return ['success' => 'You are now part of the messageboard!'];
    }
}
