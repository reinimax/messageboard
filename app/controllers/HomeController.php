<?php

namespace app\controllers;

use app\lib\Mailer;
use app\lib\Session;
use app\models\HomeModel;

class HomeController
{
    protected $model;
    protected $mailer;

    public function __construct()
    {
        $this->model = new HomeModel();
        $this->mailer =  new Mailer();
    }

    /**
     * Opens the registration form, validates form entries and saves the new user in the DB
     * @return array View and data to be rendered
     */
    public function register()
    {
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                $gump->filter_rules([
                    'user' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'pwd' => 'trim|sanitize_string'
                ]);

                $lowercase = '/[a-z]/';
                $uppercase = '/[A-Z]/';
                $digit = '/[0-9]/';
                $gump->validation_rules([
                    'user' => 'required|alpha_numeric_dash|max_len,100|min_len,2|doesnt_contain_list,default',
                    'email' => 'required|valid_email',
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

                $valid_data = $gump->run($_POST);

                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                    'title' => 'Register',
                    'content' => 'register.php',
                    'data' => ['errors' => $errors]
                ];
                } else {
                    // Save the new user in the DB
                    $result = $this->model->register($valid_data);
                    if (isset($result['error'])) {
                        return [
                            'title' => 'Register',
                            'content' => 'register.php',
                            'data' => $result
                        ];
                    } else {
                        $_POST = [];
                        // Log the user in
                        $result = $this->model->login($valid_data, false);
                        if ($result === false) {
                            return [
                                'title' => 'Login',
                                'content' => 'login.php',
                                'data' => ['error' => 'Login not correct']
                            ];
                        } else {
                            Session::init()->setLogin($result);
                            // send welcome email
                            $recipient = [$valid_data['email'], $valid_data['user']];
                            $subject = 'Welcome to the message board!';
                            $body = '<strong>This is a test.</strong>';
                            $altBody = 'This is a test';
                            $mailStatus = $this->mailer->send($recipient, $subject, $body, $altBody);
                            if (!$mailStatus) {
                                // One could get the errors and do something with them, but this is not really necessary here.
                                // The most useful thing would be to log them instead of displaying them somewhere.
                                // $mailerror = $this->mailer->getErrors();
                            }

                            $success= urlencode('You are succesfully registered!');
                            // go to index
                            header('Location:/index.php?success='.$success);
                            exit;
                        }
                    }
                }
            } else {
                // if CSRF Validation failed
                return [
                    'title' => 'Register',
                    'content' => 'register.php',
                    'data' => ['error' => 'Registration failed']
                ];
            }
        } else {
            // if $_POST is empty
            return [
                'title' => 'Register',
                'content' => 'register.php'
            ];
        }
    }

    /**
     * Opens the login form, validates form entries and logs the user in
     * @return array View and data to be rendered
     */
    public function login()
    {
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                $gump->filter_rules([
                    'user' => 'trim|sanitize_string',
                    'pwd' => 'trim|sanitize_string'
                ]);

                if (strpos($_POST['user'], '@') === false) {
                    $validation = 'required';
                    $isEmail = false;
                } else {
                    $validation = 'required|valid_email';
                    $isEmail = true;
                }

                $gump->validation_rules([
                    'user' => $validation,
                    'pwd' => 'required'
                ]);

                $gump->set_fields_error_messages([
                    'pwd' => ['required' => 'Please enter your password']
                ]);

                $valid_data = $gump->run($_POST);

                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                    'title' => 'Login',
                    'content' => 'login.php',
                    'data' => ['errors' => $errors]
                ];
                } else {
                    // Log the user in
                    $result = $this->model->login($valid_data, $isEmail);
                    if ($result === false) {
                        return [
                            'title' => 'Login',
                            'content' => 'login.php',
                            'data' => ['error' => 'Login not correct']
                        ];
                    } else {
                        $_POST = [];
                        Session::init()->setLogin($result);
                        $success= str_replace('.', '%2E', urlencode('Login was successful. Welcome back!'));
                        // go to index
                        header('Location:/index.php?success='.$success);
                        exit;
                    }
                }
            } else {
                // if CSRF Validation failed
                return [
                    'title' => 'Login',
                    'content' => 'login.php',
                    'data' => ['error' => 'Login failed']
                ];
            }
        } else {
            // if $_POST is empty
            return [
                'title' => 'Login',
                'content' => 'login.php'
            ];
        }
    }

    /**
     * Logs the user out
     */
    public function logout()
    {
        Session::init()->destroySession();
        header('Location:/');
        exit;
    }
}
