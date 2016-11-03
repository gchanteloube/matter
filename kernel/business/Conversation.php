<?php

namespace Matter;

/**
 * Description of Session
 *
 * @author guich
 */
class Conversation {
    private static $instance = null;
    private static $type;
    private $kernel = null;

    public static function init($type) {
        if ($type == "SESSION") {
            if (!isset(self::$instance)) {
                self::$instance = new Conversation();
            }
            self::$type = "SESSION";
            return new Conversation();
        }
        if ($type == "KERNEL") {
            if (!isset(self::$instance)) {
                self::$instance = new Conversation();
            }
            self::$type = "KERNEL";
            return self::$instance;
        }
        if ($type == "POST") {
            self::$type = "POST";
            return new Conversation();
        }
        if ($type == "GET") {
            self::$type = "GET";
            return new Conversation();
        }
        if ($type == "FILES") {
            self::$type = "FILES";
            return new Conversation();
        }
        if ($type == "COOKIE") {
            self::$type = "COOKIE";
            return new Conversation();
        }
    }

    public function destroy() {
        session_unset();
        session_destroy();
    }

    public function get($key, $protected = true) {
        if (self::$type == "POST") {
            if ($protected) return isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES) : false;
            else return isset($_POST[$key]) ? $_POST[$key] : false;
        }
        if (self::$type == "GET") {
            if ($protected) return isset($_GET[$key]) ? htmlspecialchars($_GET[$key], ENT_QUOTES) : false;
            else return isset($_GET[$key]) ? $_GET[$key] : false;
        }
        if (self::$type == "SESSION") {
            if ($protected) return isset($_SESSION[$key]) ? htmlspecialchars($_SESSION[$key], ENT_QUOTES) : false;
            else return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
        }
        if (self::$type == "KERNEL") {
            if ($protected) return isset($this->kernel[$key]) ? htmlspecialchars($this->kernel[$key], ENT_QUOTES) : false;
            else return isset($this->kernel[$key]) ? $this->kernel[$key] : false;
        }
        if (self::$type == "COOKIE") {
            if ($protected) return isset($_COOKIE[$key]) ? htmlspecialchars($_COOKIE[$key], ENT_QUOTES) : false;
            else return isset($_COOKIE[$key]) ? $_COOKIE[$key] : false;
        }
    }

    public function set($key, $value, $protected = true) {
        if (self::$type == "POST") {
            if ($protected) $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
            else $_POST[$key] = $value;
        }
        if (self::$type == "GET") {
            if ($protected) $_GET[$key] = htmlspecialchars($value, ENT_QUOTES);
            else $_GET[$key] = $value;
        }
        if (self::$type == "SESSION") {
            if ($protected) $_SESSION[$key] = htmlspecialchars($value, ENT_QUOTES);
            else $_SESSION[$key] = $value;
        }
        if (self::$type == "KERNEL") {
            if ($protected) $this->kernel[$key] = htmlspecialchars($value, ENT_QUOTES);
            else $this->kernel[$key] = $value;
        }
    }

}

?>
