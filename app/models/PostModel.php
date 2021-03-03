<?php

namespace app\models;

use app\lib\MySql;

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
        return ['index', 'This is the index'];
    }
}
