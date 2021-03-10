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
                'error' => $error,
                'title' => 'The latest posts'
            ],
        ];
    }

    /**
     * Searches for posts
     */
    public function search()
    {
        if (!empty($_GET)) {
            $gump = new \GUMP();

            $gump->filter_rules([
                'query' => 'trim|sanitize_string',
                'limit' => 'trim|sanitize_string',
            ]);

            $valid = $gump->run($_GET);
        } else {
            // without query string go back to the index
            header('Location:/index.php');
            exit;
        }
        // call model and return the retrieved data
        $data = $this->model->search($valid);
        if (!$data) {
            return [
                'title' => 'Index',
                'content' => 'index.php',
                'data' => [
                    'data' => [],
                    'title' => 'No results found',
                    'error' => 'Your search for "'.$valid['query'].'" yielded no results'
                ],
            ];
        }
        $count = count($data);
        return [
            'title' => 'Index',
            'content' => 'index.php',
            'data' => [
                'data' => $data,
                'title' => 'Your results',
                'success' => 'Your search for "'.$valid['query'].'" yielded '.$count.' results'
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
        $tags = $this->model->getTags();
        $this->checkLogin();
        return [
            'title' => 'New post',
            'content' => 'create.php',
            'data' => $tags
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
                    'tag' => 'trim|sanitize_string',
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

    /**
     * Deletes a post
     */
    public function delete()
    {
        $this->checkLogin();
        if (!isset($_GET['id'])) {
            $error= urlencode('Sorry, we couldn\'t find this post');
            header('Location:/index.php?error='.$error);
            exit;
        } else {
            $id = (int) $_GET['id'];
        }
        $result = $this->model->delete($id);
        if (isset($result['error'])) {
            $error= urlencode($result['error']);
            header('Location:/index.php?error='.$error);
            exit;
        } elseif (isset($result['success'])) {
            $success= urlencode($result['success']);
            header('Location:/index.php?success='.$success);
            exit;
        }
    }

    /**
     * Opens the form for editing a post and fills in the appropriate data
     * @return array On success the view to be loaded with the data
     * On failure redirect to index.php with an error message
     */
    public function edit()
    {
        $this->checkLogin();
        if (!isset($_GET['id'])) {
            $error= urlencode('Sorry, we couldn\'t find this post');
            header('Location:/index.php?error='.$error);
            exit;
        } else {
            $id = (int) $_GET['id'];
        }
        $result = $this->model->edit($id);
        $taglist = $this->model->getTags();
        if (isset($result['error'])) {
            $error= urlencode($result['error']);
            header('Location:/index.php?error='.$error);
            exit;
        }
        return [
            'title' => 'Edit post',
            'content' => 'edit.php',
            'data' => [
                'data' => $result,
                'taglist' => $taglist
                ]
        ];
    }

    /**
     * Updates a post
     */
    public function update()
    {
        $this->checkLogin();
        if (!isset($_GET['id'])) {
            $error= urlencode('Sorry, we couldn\'t find this post');
            header('Location:/index.php?error='.$error);
            exit;
        } else {
            $id = (int) $_GET['id'];
        }

        // Validate POST
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                $gump->filter_rules([
                    'title' => 'trim|sanitize_string',
                    'message' => 'trim|sanitize_string',
                    'tag' => 'trim|sanitize_string',
                ]);

                $gump->validation_rules([
                    'title' => 'required',
                    'message' => 'required',
                ]);

                $valid_data = $gump->run($_POST);

                // prepare the parts to be returned on error
                $taglist = $this->model->getTags();
                $contents = [
                    'title' => $_POST['title'],
                    'content' => $_POST['message'],
                ];
                $completeSubarray = [];
                foreach ($_POST['tag'] as $tag) {
                    $temp = ['id' => $tag];
                    $completeSubarray[] = array_merge($contents, $temp);
                }
                // prepare the parts to be returned on error END

                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                        'title' => 'Edit post',
                        'content' => 'edit.php',
                        'data' => [
                            'errors' => $errors,
                            'data' => [
                                $id => $completeSubarray
                            ],
                            'taglist' => $taglist
                        ]
                    ];
                } else {
                    // Update the post
                    $result = $this->model->update($id, $valid_data);
                    if (isset($result['error'])) {
                        return [
                            'title' => 'Edit post',
                            'content' => 'edit.php',
                            'data' => [
                                'error' => $result['error'],
                                'data' => [
                                    $id => $completeSubarray
                                ],
                                'taglist' => $taglist
                            ]
                        ];
                    } else {
                        $_POST = [];
                        $success= urlencode($result['success']);
                        header('Location:/index.php?success='.$success);
                        exit;
                    }
                }
            } else {
                // if CSRF Validation failed
                $error= urlencode('Ups, something went wrong ...');
                header('Location:/index.php?error='.$error);
                exit;
            }
        } else {
            // if $_POST is empty
            $error= urlencode('Ups, something went wrong ...');
            header('Location:/index.php?error='.$error);
            exit;
        }
    }
}
