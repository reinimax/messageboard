<?php

namespace app\models;

use app\lib\MySql;
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
     * @return array/string The user data on success, the error message on failure
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
            return $e->getMessage();
        }
        return $result;
    }
}
