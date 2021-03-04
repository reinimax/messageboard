<?php

namespace app\controllers;

class HomeController
{
    public function register()
    {
        if (!empty($_POST)) {
            // Validation
            return [
                'title' => 'Successfully registered',
                'content' => 'index.php',
                'data' => 'You are now part of the messageboard!'
            ];
        } else {
            return [
                'title' => 'Register',
                'content' => 'register.php'
            ];
        }
    }
}
