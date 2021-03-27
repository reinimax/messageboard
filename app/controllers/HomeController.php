<?php

namespace app\controllers;

use app\lib\Mailer;
use app\lib\Session;
use app\models\HomeModel;
use DateInterval;
use DateTime;

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
                            // define title and content for the html template
                            $title = 'Welcome to the message board!';
                            $content = 'We\'re glad that you\'re on board!';
                            $tmpBody = file_get_contents(ROOT.'/views/mail/general.html');
                            $body = str_replace(['{title}', '{content}'], [$title, $content], $tmpBody);
                            $altBody = "$title\n$content";
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

    public function forgotpwd()
    {
        if (!empty($_POST)) {
            if (hash_equals(Session::init()->getCsrfToken(), $_POST['_token'])) {
                // Validation
                $gump = new \GUMP();

                $gump->filter_rules(['email' => 'trim|sanitize_email']);

                $gump->validation_rules(['email' => 'required|valid_email']);

                $valid_data = $gump->run($_POST);

                if ($gump->errors()) {
                    $errors = $gump->get_errors_array();
                    return [
                        'title' => 'Forgot Password',
                        'content' => 'forgotpwd.php',
                        'data' => ['errors' => $errors]
                    ];
                } else {
                    $_POST = [];
                    // Check if email exists in the DB (and pull also the username out of the DB to send it)
                    $result = $this->model->forgotpwd($valid_data['email']);

                    if (!$result) {
                        // If the email doesn't exist, send a notification that this email isn't registered here
                        $recipient = $valid_data['email'];
                        $subject = 'Password reset for reinimax\' messageboard';
                        // define title and content for the html template
                        $title = 'Password reset for reinimax\' messageboard';
                        $content = '
                            <p>Hello,</p>
                            <br>
                            <p>We received a request to reset the password associated with the email address '.$valid_data['email'].' here at reinimax\' messageboard. Unfortunately this email address isn\'t registered with us.</p>
                            <p><strong>What does this mean for you?</strong></p>
                            <p>No changes have been made to your account. Proceed as follows:</p>
                            <p>If you didn\'t make this request, please ignore this email.</p>
                            <p>If you requested a password change, you may have registered under a different email address. Please go back to our site and <a href="'.URL.'/forgotpwd.php">request a password change</a> using the email you used to register your account.</p>
                            <p>If you have any questions, please <a href="mailto:'.EMAIL_ADDR.'">contact us</a></p>
                            <br>
                            <p>Regards,</p>
                            <p>reinimax\' messageboard</p>
                            ';
                        $tmpBody = file_get_contents(ROOT.'/views/mail/general.html');
                        $body = str_replace(['{title}', '{content}'], [$title, $content], $tmpBody);
                        $altContent = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n"], $content));
                        // replace multipe spaces and tabs
                        $altContent = preg_replace('/ +|\t+/u', " ", $altContent);
                        // replace spaces at the beginning of a new line
                        $altContent = preg_replace('/\n /u', "", $altContent);
                        $altBody = "$title\n$altContent";
                        $mailStatus = $this->mailer->send($recipient, $subject, $body, $altBody);
                        if (!$mailStatus) {
                            // One could get the errors and do something with them, but this is not really necessary here.
                            // The most useful thing would be to log them instead of displaying them somewhere.
                            // $mailerror = $this->mailer->getErrors();
                        }
                    } else {
                        // If the email exists, send an email with the reset link
                        // Create a hash and save it and the current time in the session
                        $hash = bin2hex(random_bytes(32));
                        $current = new DateTime();
                        $validUntil = new DateInterval('PT30S');
                        $expiration = $current->add($validUntil);

                        $_SESSION['pwd_reset_hash'] = $hash;
                        $_SESSION['pwd_reset_expiration'] = $expiration;

                        //CONTINUE HERE
                        /* $recipient = [$valid_data['email'], $valid_data['user']];
                        $subject = 'Welcome to the message board!';
                        // define title and content for the html template
                        $title = 'Welcome to the message board!';
                        $content = 'We\'re glad that you\'re on board!';
                        $tmpBody = file_get_contents(ROOT.'/views/mail/general.html');
                        $body = str_replace(['{title}', '{content}'], [$title, $content], $tmpBody);
                        $altBody = "$title\n$content";
                        $mailStatus = $this->mailer->send($recipient, $subject, $body, $altBody);
                        if (!$mailStatus) {
                            // One could get the errors and do something with them, but this is not really necessary here.
                            // The most useful thing would be to log them instead of displaying them somewhere.
                            // $mailerror = $this->mailer->getErrors();
                        } */
                    }
                    //Return success message
                    return [
                        'title' => 'Forgot Password',
                        'content' => 'forgotpwd.php',
                        'data' => ['success' => 'An email was sent to '.$valid_data['email'].'. Please check your inbox and follow the instructions in the email.']
                    ];
                }
            } else {
                // if CSRF Validation failed
                return [
                    'title' => 'Forgot Password',
                    'content' => 'forgotpwd.php',
                    'data' => ['error' => 'The action failed. Please try again later.']
                ];
            }
        } else {
            // if $_POST is empty
            return [
                'title' => 'Forgot Password',
                'content' => 'forgotpwd.php'
            ];
        }
    }
}
