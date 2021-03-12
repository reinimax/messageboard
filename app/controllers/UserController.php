<?php

namespace app\controllers;

use app\models\UserModel;
use app\lib\Session;

class UserController
{
    protected $model;
    protected $userId;
    protected $userName;

    public function __construct()
    {
        // For each and everything this controller does there needs to be a logged in user.
        // Therefore I can check for the Login directly each time an object of this class is constructed,
        // so that I don't need to check separately in each function.
        if (Session::init()->checkLogin() === false) {
            header('Location:/logout.php');
            exit;
        }
        $this->model = new UserModel();
        $this->userId = $_SESSION['user_id'];
        $this->userName = $_SESSION['user'];
    }

    /**
     * Loads the data of the current user
     * @return array The user data
     */
    public function settings()
    {
        $data = $this->model->settings($this->userId);
        if (!is_array($data)) {
            header('Location:/logout.php');
            exit;
        }
        return [
            'title' => 'About me',
            'content' => 'settings.php',
            'data' => [
                'data' => $data,
            ]
        ];
    }
}
