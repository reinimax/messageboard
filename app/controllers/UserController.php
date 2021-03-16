<?php

namespace app\controllers;

use app\lib\Image;
use app\models\UserModel;
use app\lib\Session;
use app\traits\Avatar;

class UserController
{
    use Avatar;
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
        $avatar = ['avatar' => $this->getAvatar($data['avatar'])];
        return [
            'title' => 'About me',
            'content' => 'settings.php',
            'data' => [
                'data' => array_merge($data, $avatar),
            ]
        ];
    }

    /**
     * Update user data
     * @return array The data and view to be loaded
     */
    public function update()
    {
        // Validate POST
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                if ($_POST['_update'] === 'info') {
                    $gump->filter_rules([
                        'birthday' => 'trim|sanitize_string',
                        'location' => 'trim|sanitize_string',
                        'description' => 'trim|sanitize_string'
                    ]);

                    $gump->validation_rules([
                        'birthday' => 'date',
                        'location' => 'max_len,100',
                        'description' => 'max_len,255'
                    ]);
                } elseif ($_POST['_update'] === 'pwd') {
                    $gump->filter_rules([
                        'pwd' => 'trim|sanitize_string',
                        'pwdrepeat' => 'trim|sanitize_string',
                        'confirm' => 'trim|sanitize_string'
                    ]);
                    $lowercase = '/[a-z]/';
                    $uppercase = '/[A-Z]/';
                    $digit = '/[0-9]/';
                    $gump->validation_rules([
                        'pwd' => 'required|max_len,64|min_len,8|equalsfield,pwdrepeat|regex,'.$lowercase.
                        '|regex,'.$uppercase.'|regex,'.$digit
                    ]);

                    $gump->set_fields_error_messages([
                        'pwd' => [
                            'required' => 'The Password field is required',
                            'max_len' => 'The Password must not be longer than {param} characters',
                            'min_len' => 'The Password must have at least {param} characters',
                            'regex' => 'The Password must contain uppercase and lowercase letters and one digit'
                        ]
                    ]);
                } elseif ($_POST['_update'] === 'avatar') {
                    $gump->validation_rules(['avatar' => 'extension,png;jpg;gif']);
                } else {
                    $error= str_replace('.', '%2E', urlencode('Ups, something went wrong ...'));
                    header('Location:/index.php?error='.$error);
                    exit;
                }

                $valid_data = $gump->run(array_merge($_POST, $_FILES));
                $data = $this->model->settings($this->userId);
                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                        'title' => 'About me',
                        'content' => 'settings.php',
                        'data' => [
                            'data' => $data,
                            'errors' => $errors,
                        ]
                    ];
                } else {
                    // Update userdata
                    $result = $this->model->update($this->userId, $valid_data);
                    if (isset($result['error'])) {
                        return [
                            'title' => 'About me',
                            'content' => 'settings.php',
                            'data' => [
                                'error' => $result['error'],
                                'data' => $data
                            ]
                        ];
                    } else {
                        $_POST = [];
                        // retrieve the actualized data to display it
                        $data = $this->model->settings($this->userId);
                        if (!is_array($data)) {
                            header('Location:/logout.php');
                            exit;
                        }

                        // if an avatar was uploaded, resize and save it
                        if (isset($valid_data['avatar'])) {
                            $tmp = $valid_data['avatar']['tmp_name'];
                            $imageObj = new Image($tmp);
                            $imageObj->square(200)->save(ROOT.'/uploads/avatars/'.$data['avatar']);
                        }
                        // load avatar image
                        $avatar = ['avatar' => $this->getAvatar($data['avatar'])];
                        // return the actualized data with a successmessage
                        return [
                            'title' => 'About me',
                            'content' => 'settings.php',
                            'data' => [
                                'success' => $result['success'],
                                'data' => array_merge($data, $avatar)
                            ]
                        ];
                    }
                }
            } else {
                // if CSRF Validation failed
                $error= str_replace('.', '%2E', urlencode('Ups, something went wrong ...'));
                header('Location:/index.php?error='.$error);
                exit;
            }
        } else {
            // if $_POST is empty
            $error= str_replace('.', '%2E', urlencode('Ups, something went wrong ...'));
            header('Location:/index.php?error='.$error);
            exit;
        }
    }

    /**
     * Delete the user
     * @return void/array
     */
    public function delete()
    {
        // Validate POST
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();
                $gump->filter_rules(['confirmdelete' => 'trim|sanitize_string']);
                $gump->validation_rules(['confirmdelete' => 'required']);
                $gump->set_fields_error_messages([
                        'confirmdelete' => ['required' => 'You must enter your password to confirm the action']
                ]);
                $valid_data = $gump->run($_POST);
                $data = $this->model->settings($this->userId);
                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                        'title' => 'About me',
                        'content' => 'settings.php',
                        'data' => [
                            'data' => $data,
                            'errors' => $errors,
                        ]
                    ];
                } else {
                    // Delete user
                    $result = $this->model->delete($this->userId, $valid_data);
                    if (isset($result['error'])) {
                        return [
                            'title' => 'About me',
                            'content' => 'settings.php',
                            'data' => [
                                'error' => $result['error'],
                                'data' => $data
                            ]
                        ];
                    } else {
                        $_POST = [];
                        Session::init()->destroySession();
                        $success= str_replace('.', '%2E', urlencode($result['success']));
                        header('Location:/index.php?success='.$success);
                        exit;
                    }
                }
            } else {
                // if CSRF Validation failed
                $error= str_replace('.', '%2E', urlencode('Ups, something went wrong ...'));
                header('Location:/index.php?error='.$error);
                exit;
            }
        } else {
            // if $_POST is empty
            $error= str_replace('.', '%2E', urlencode('Ups, something went wrong ...'));
            header('Location:/index.php?error='.$error);
            exit;
        }
    }

    /**
     * Display the profile of a user
     * @return array/void Either returns the array with the data or redirects to index.php in case of an error
     */
    public function user()
    {
        if (!isset($_GET['id'])) {
            $error= str_replace('.', '%2E', urlencode('User profile not found'));
            header('Location:/index.php?error='.$error);
            exit;
        }
        $id = (int) $_GET['id'];
        $data = $this->model->user($id);
        if (!is_array($data)) {
            $error= str_replace('.', '%2E', urlencode('User profile not found'));
            header('Location:/index.php?error='.$error);
            exit;
        }
        $avatar = ['avatar' => $this->getAvatar($data['avatar'])];
        return [
            'title' => $data['user'].'\'s profile',
            'content' => 'user.php',
            'data' => [
                'data' => array_merge($data, $avatar),
            ]
        ];
    }
}
