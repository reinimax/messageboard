<?php

namespace app\models;

use app\lib\MySql;
use DateTime;
use PDO;
use PDOException;

class UserModel
{
    protected $pdo;

    public function __construct()
    {
        $config = require_once ROOT.'/config/database.php';
        $this->pdo = MySql::init($config);
    }

    protected function checkPwd($id, $pwd)
    {
        $getPwd = <<<SQL
            SELECT pwd FROM users WHERE id=$id;
        SQL;
        $statement = $this->pdo->query($getPwd);
        $hash = $statement->fetch(PDO::FETCH_COLUMN);
        return password_verify($pwd, $hash);
    }

    /**
     * Get the number of posts from a user
     * @param int $id The id of the user
     * @return int The number of posts. A PDOException also returns 0.
     */
    public function getNumOfPosts($id)
    {
        // Here, $id may come from $_GET or so, therefore the statement needs to be prepared
        $getNum = <<<SQL
            SELECT COUNT(*) FROM posts WHERE user_id=:id;
        SQL;

        try {
            $statement = $this->pdo->prepare($getNum);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return (int) $statement->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Loads the data of the current user
     * @param int $id The id of the current user
     * @return array/false The user data on success, false on failure
     */
    public function settings($id)
    {
        // Since $id comes from the Session, and the Session gets it directly from the
        // database, it's not necessary to check it again here.
        $getUser = <<<SQL
            SELECT id, user, email, descr, location, birthday, created_at, avatar FROM users
            WHERE id=$id;
        SQL;

        $postcount = ['count' => $this->getNumOfPosts($id)];

        try {
            $statement = $this->pdo->query($getUser);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return array_merge($result, $postcount);
    }

    /**
     * Updates user data
     * @param int $id The id of the logged in user
     * @param array $data The sent data from the settings.php form
     * @return array with the success- or error-message
     */
    public function update($id, $data)
    {
        // Since $id comes from the Session, and the Session gets it directly from the
        // database, it's not necessary to check it again here.

        // if the user wants to change the password, check if the old password is correct
        if ($data['_update'] === 'pwd') {
            if ($this->checkPwd($id, $data['confirm']) === false) {
                return ['error' => 'Wrong password'];
            }
        }

        $updateInfo = <<<SQL
            UPDATE users SET descr=:descr, location=:location, birthday=:birthday WHERE id=$id;
        SQL;

        $changePwd = <<<SQL
            UPDATE users SET pwd=:pwd WHERE id=$id;
        SQL;

        $updateAvatar = <<<SQL
            UPDATE users SET avatar=:avatar WHERE id=$id;
        SQL;

        try {
            // I prefer a switch here, in case other options are added later
            switch ($data['_update']) {
                case 'info':
                    $date = ($data['birthday'] === '') ? null : DateTime::createFromFormat('Y-m-d', $data['birthday'])->format('Y-m-d');
                    $statement = $this->pdo->prepare($updateInfo);
                    $statement->bindParam(':descr', $data['description'], PDO::PARAM_STR);
                    $statement->bindParam(':location', $data['location'], PDO::PARAM_STR);
                    $statement->bindParam(':birthday', $date, PDO::PARAM_STR);
                    break;
                case 'pwd':
                    $pwd = password_hash($data['pwd'], PASSWORD_BCRYPT);
                    $statement = $this->pdo->prepare($changePwd);
                    $statement->bindParam(':pwd', $pwd, PDO::PARAM_STR);
                    break;
                case 'avatar':
                    $extension = (pathinfo($data['avatar']['name']))['extension'];
                    $path = $_SESSION['user'].'.'.$extension;
                    $statement = $this->pdo->prepare($updateAvatar);
                    $statement->bindParam(':avatar', $path, PDO::PARAM_STR);
                    break;
                default:
                    return ['error' => 'Sorry, your data could not be saved'];
            }
            $statement->execute();

            if ($statement->rowCount() === 0) {
                return ['error' => 'Sorry, your data could not be saved'];
            }
        } catch (PDOException $e) {
            $error = (PRODUCTION === false) ? $e->getMessage() : 'Sorry, your data could not be saved';
            return ['error' => $error];
        }
        return ['success' => 'You successfully updated your profile'];
    }

    /**
     * Delete the user
     * @param int $id The id of the logged in user
     * @param array $data The sent data from the settings.php form
     * @return array with the success- or error-message
     */
    public function delete($id, $data)
    {
        // Since $id comes from the Session, and the Session gets it directly from the
        // database, it's not necessary to check it again here.

        // check if the password is correct
        if ($this->checkPwd($id, $data['confirmdelete']) === false) {
            return ['error' => 'Wrong password'];
        }

        $deleteUser = <<<SQL
            DELETE FROM users WHERE id=$id;
        SQL;

        try {
            $statement = $this->pdo->query($deleteUser);
            if ($statement->rowCount() === 0) {
                return ['error' => 'Sorry, something went wrong'];
            }
        } catch (PDOException $e) {
            $error = (PRODUCTION === false) ? $e->getMessage() : 'Sorry, something went wrong';
            return ['error' => $error];
        }
        return ['success' => 'Your profile has been successfully deleted'];
    }

    /**
     * Get the data of a user for display
     * @param int $id The id of the user
     * @return array/false On succes an array with the user data, on failure false
     */
    public function user($id)
    {
        $getUser = <<<SQL
            SELECT id, user, descr, location, birthday, created_at, avatar FROM users
            WHERE id=:id;
        SQL;

        $postcount = ['count' => $this->getNumOfPosts($id)];

        try {
            $statement = $this->pdo->prepare($getUser);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return array_merge($result, $postcount);
    }
}
