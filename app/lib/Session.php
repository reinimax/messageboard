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
        if (session_id() === '') {
            session_start();
        }
        return self::$session;
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
