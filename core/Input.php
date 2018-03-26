<?php

/**
 * 读取$_GET $_POST $_COOKIES $_SESSION的类
 * @author bluedoge
 */
class Input
{
    private static $_self = null;
    private $_get = array();
    private $_post = array();

    private function __construct()
    {
        session_start();
        foreach ($_GET as $key => $value)
        {
            $value = $this->removeHTMLLabel($value);
            $this->_get[$key] = $value;
        }
        foreach ($_POST as $key => $value) {
            $value = $this->removeHTMLLabel($value);
            $this->_post[$key] = $value;
        }
    }
    /**
     * 清除变量中的 script|i?frame|style|html|body|title|meta 等标签
     * @param  $value
     * @return
     */
    private function removeHTMLLabel($value)
    {
        if (is_string($value)) {
            $pattern = "/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU";
            return preg_replace($pattern, '', $value);
        } else if (is_array($value)) {
            $rv = [];
            foreach ($value as $kk=>$vv) {
                $rv[$kk] = $this->removeHTMLLabel($vv);
            }
            return $rv;
        }
    }

    /**
     * 获得Input类的一个单例
     * @return Input
     */
    public static function getInstance ()
    {
        if (null === self::$_self)
            self::$_self = new self();
        return self::$_self;
    }

    /**
     * 获得$_GET[$key], 如果不传入参数或者传入null就返回整个$_GET
     * @param null|string $key
     * @return array|string|null
     */
    public function get ($key = null)
    {
        if (null === $key)
            return $this->_get;
        return $this->_get[$key] ?? null;
    }
    /**
     * 获得$_POST[$key], 如果不传入参数或者传入null就返回整个$_POST
     * @param null|string $key
     * @return array|string|null
     */
    public function post ($key = null)
    {
        if (null === $key)
            return $this->_post;
        return $this->_post[$key] ?? null;
    }

    /**
     * cookie操作
     * 如果$key和$value都为空的话清除所有cookie
     * 如果$value为空且$key不为空的话就读取key为$key的cookie的值，Cookie不存在返回null
     * 如果$value和$key都不为空的话就设置|更新Cookie
     * @param string $key
     * @param string $value
     * @param int $time
     * @return null
     */
    public function cookie($key = '', $value = '', $time = 86400)
    {
        // 清楚所有的cookie
        if (empty($key) && empty($value)) {
            foreach ($_COOKIE as $key=>$value) {
                setcookie($key, '', time() - 100);
            }
        } else if (!empty($key) && empty($value)) {
            if (null === $value) {
                setcookie($key, '', time() - 100);
            } else {
                return $_COOKIE[$key] ?? null;
            }
        } else if (!empty($key) && !empty($value)) { // 设置cookie
            setcookie($key, $value, $time > 0 ? time() + $time : time() + 86400);
        }
    }
    /**
     * Session操作
     * 如果$key和$value都为空的话清除所有cookie
     * 如果$value为空且$key不为空的话就读取key为$key的Session的值，Session不存在返回null
     * 如果$value和$key都不为空的话就设置|更新Session
     * @param string $key
     * @param string $value
     * @param int $time
     * @return null
     */
    public function session($key = '', $value = '', $time = 3600)
    {
        // 清楚所有的session
        if (empty($key) && empty($value)) {
            session_unset();
            session_destroy();
        } else if (!empty($key) && empty($value)) { // 清楚key为$key的cookie
            if (null === $value && isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            } else {
                return $_SESSION[$key] ?? null;
            }
        } else if (!empty($key) && !empty($value)) { // 设置cookie
            $_SESSION[$key] = $value;
        }
    }
}
