<?php

namespace app\controllers;

use app\lib\Session;

class HomeController
{
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
                    'user' => 'required|alpha_numeric_dash|max_len,100|min_len,2',
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
                    // call to model to save the user data in the DB
                    $_POST = [];
                    return [
                        'title' => 'Successfully registered',
                        'content' => 'index.php',
                        'data' => 'You are now part of the messageboard!'
                    ];
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
}
