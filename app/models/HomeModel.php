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

    /**
     * Checks username and password against the DB
     * @param array $data The validated data from the login form
     * @param bool $isEmail If TRUE the method checks for the email, if FALSE it checks for the username
     * @return array/bool Returns the username if username and password are correct, FALSE otherwise
     */
    public function login($data, $isEmail)
    {
        // Check if username or email exists
        $loginUserName = <<<SQL
            SELECT id, user, email, pwd FROM users WHERE user=:user;
        SQL;

        $loginUserEmail = <<<SQL
            SELECT user, email, pwd FROM users WHERE email=:user;
        SQL;

        try {
            if ($isEmail) {
                $statement = $this->pdo->prepare($loginUserEmail);
            } else {
                $statement = $this->pdo->prepare($loginUserName);
            }
            $statement->bindParam(':user', $data['user'], PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }

        // If a user was found, check the password
        if ($result !== false) {
            if (password_verify($data['pwd'], $result['pwd'])) {
                return [
                    'user' => $result['user'],
                    'id' => $result['id'],
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
        // per default, return false
        return false;
    }

    /**
     * Checks if an email exists in table users
     * @param string $email The email the user entered
     * @return string/bool Returns the associated username if the email exists, otherwise FALSE
     */
    public function forgotpwd(string $email)
    {
        $checkEmail = <<<SQL
            SELECT user FROM TABLE users WHERE email=:email;
        SQL;

        try {
            $statement = $this->pdo->prepare($checkEmail);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
    }
}
