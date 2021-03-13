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
            SELECT id, user, email, descr, location, birthday, created_at FROM users
            WHERE id=$id;
        SQL;

        try {
            $statement = $this->pdo->query($getUser);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

    /**
     * Updates user data
     * @param int $id The id of the logged in user
     * @param array $data The sent data from the settings.php form
     * @return array with the success- or error-message
     */
    public function update($id, $data)
    {
        // if the user wants to change the password, check of the old password is correct
        if ($data['_update'] === 'pwd') {
            $getPwd = <<<SQL
                SELECT pwd FROM users WHERE id=$id;
            SQL;
            $statement = $this->pdo->query($getPwd);
            $pwd = $statement->fetch(PDO::FETCH_COLUMN);
            if (!password_verify($data['confirm'], $pwd)) {
                return ['error' => 'Wrong password'];
            }
        }

        $updateInfo = <<<SQL
            UPDATE users SET descr=:descr, location=:location, birthday=:birthday WHERE id=$id;
        SQL;

        $changePwd = <<<SQL
            UPDATE users SET pwd=:pwd WHERE id=$id;
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
                default:
                    return ['error' => 'Sorry, your data could not be saved'];
            }

            $statement->execute();
            if ($statement->rowCount() === 0) {
                return ['error' => 'Sorry, your data could not be saved'];
            }
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
        return ['success' => 'You successfully updated your profile'];
        ;
    }
}
