<?php

namespace app\controllers;

use app\models\PostModel;
use app\lib\Session;

class PostController
{
    protected $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    /**
     * Displays the posts
     */
    public function index()
    {
        if (!empty($_GET)) {
            $gump = new \GUMP();

            $gump->filter_rules([
                'success' => 'trim|sanitize_string',
                'error' => 'trim|sanitize_string',
            ]);

            $valid_string = $gump->run($_GET);
            $success = urldecode($valid_string['success']);
            $error = urldecode($valid_string['error']);
        }
        // call model and return the retrieved data
        $data = $this->model->index();
        return [
            'title' => 'Index',
            'content' => 'index.php',
            'data' => [
                'data' => $data,
                'success' => $success,
                'error' => $error
            ],
        ];
    }

    protected function checkLogin()
    {
        if (Session::init()->checkLogin() === false) {
            header('Location:/logout.php');
            exit;
        }
    }

    /**
     * Displays the form for writing a new post
     * @return array The view to be loaded
     */
    public function create()
    {
        $this->checkLogin();
        return [
            'title' => 'New post',
            'content' => 'create.php'
        ];
    }

    /**
     * Validate the form entry and save it
     * @return array The view to be loaded
     */
    public function save()
    {
        $this->checkLogin();
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                $gump->filter_rules([
                    'title' => 'trim|sanitize_string',
                    'message' => 'trim|sanitize_string',
                ]);

                $gump->validation_rules([
                    'title' => 'required',
                    'message' => 'required',
                ]);

                $valid_data = $gump->run($_POST);

                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                        'title' => 'New post',
                        'content' => 'create.php',
                        'data' => ['errors' => $errors]
                ];
                } else {
                    // Save the new post in the DB
                    $result = $this->model->save($valid_data);
                    if (isset($result['error'])) {
                        return [
                            'title' => 'New post',
                            'content' => 'create.php',
                            'data' => $result
                        ];
                    } else {
                        $_POST = [];
                        $success= urlencode('Great! You succesfully posted a new message');
                        header('Location:/index.php?success='.$success);
                        exit;
                    }
                }
            } else {
                // if CSRF Validation failed
                return [
                    'title' => 'New post',
                    'content' => 'create.php',
                    'data' => ['error' => 'Post could not be saved']
                ];
            }
        } else {
            // if $_POST is empty
            return [
                'title' => 'New post',
                'content' => 'create.php'
            ];
        }
    }

    /**
     * Displays all posts of the current user
     * @return array The view to be loaded
     */
    public function show()
    {
        $this->checkLogin();
        $result = $this->model->show();
        if ($result === false) {
            $error= urlencode('Sorry, we couldn\'t find any posts');
            header('Location:/index.php?error='.$error);
            exit;
        }
        return [
            'title' => 'Your posts',
            'content' => 'show.php',
            'data' => ['data' => $result]
        ];
    }
}
