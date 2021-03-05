<?php

namespace app\lib;

use PDO;
use PDOException;

class MySql
{
    private static $pdo = null;

    private function __construct()
    {
    }

    /**
     * Creates a new database connection if it does not already exist
     * @param array $config The configuration for the database connection
     * @return object The database connection
     */
    public static function init(array $config)
    {
        if (
            !isset($config['host']) || !isset($config['dbname']) ||
            !isset($config['user']) || !isset($config['pwd'])
        ) {
            throw new PDOException('Database configuration error');
        }
        $port = ($config['port']) ?? 3306;
        if (self::$pdo === null) {
            $dsn = 'mysql:host='.$config['host'].';port='.$port.';dbname='.$config['dbname'];
            self::$pdo = new PDO($dsn, $config['user'], $config['pwd']);
        }
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$pdo;
    }
}
