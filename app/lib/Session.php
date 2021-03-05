<?php

namespace app\lib;

class Session
{
    private static $session = null;

    private function __construct()
    {
    }

    /**
     * Starts a session
     * @return obj The Session object
     */
    public static function init()
    {
        if (self::$session === null) {
            self::$session = new Session();
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return self::$session;
    }

    /**
     * Sets a CSRF Token
     * @return string The CSRF Token
     */
    public function setCsrfToken()
    {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_token'];
    }

    /**
     * Gets the CSRF Token
     * @return string The CSRF Token
     */
    public function getCsrfToken()
    {
        return $_SESSION['_token'];
    }

    /**
     * Sets the login
     */
    public function setLogin($data)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $data['user'];
    }

    /**
     * Checks if the user is logged in
     * @return bool
     */
    public function checkLogin()
    {
        if (!isset($_SESSION['user'])) {
            $this->destroySession();
            return false;
        }
        return true;
    }

    /**
     * Destroys the session
     */
    public function destroySession()
    {
        $_SESSION = [];
        session_destroy();
    }
}
