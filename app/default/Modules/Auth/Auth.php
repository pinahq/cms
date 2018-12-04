<?php

namespace Pina\Modules\Auth;

use Pina\Hash;
use Pina\App;

class Auth
{

    private static $expired = 360000;
    private static $userId = 0;

    private static function authId($pnid, $userAgent)
    {
        return md5($pnid . $userAgent);
    }

    private static function dateCreated()
    {
        return date('Y-m-d H:i:s', time());
    }

    private static function timeExpired()
    {
        return time() + self::$expired;
    }

    private static function dateExpired()
    {
        return date('Y-m-d H:i:s', self::timeExpired());
    }

    private static function userAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    private static function clientIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    private static function pnid()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $length = mt_rand(0, 32);

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return uniqid($code . time(), true);
    }

    private static function setCookie($key, $value, $expired)
    {
        if (isset($_ENV['MODE']) && $_ENV['MODE'] == 'test')
            return true;

        return setcookie($key, $value, $expired, '/');
    }

    private static function getUser($data)
    {
        $email = isset($data['email']) ? $data['email'] : '';

        if (!empty($email)) {
            if ($class = App::container()->get(UserInterface::class)) {
                return $class->findByEmail($email);
            }
        }

        return false;
    }

    private static function auth($userId)
    {
        $userId = intval($userId);
        $userAgent = self::userAgent();
        $ip = self::clientIp();
        $pnid = self::pnid();
        $authId = self::authId($pnid, $userAgent);

        $data = array(
            'id' => $authId,
            'user_id' => $userId,
            'user_agent' => $userAgent,
            'ip' => $ip,
            'created' => self::dateCreated(),
            'expired' => self::dateExpired()
        );

        if (!AuthGateway::instance()->add($data)) {
            return false;
        }

        return $pnid;
    }

    private static function removeExpired()
    {
        AuthGateway::instance()
            ->whereExpired(date('Y-m-d H:i:s', time() - self::$expired))
            ->delete();
    }

    private static function dateToTime($date)
    {
        if (preg_match('#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#', $date, $matches) == 1) {
            return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
        }

        return false;
    }

    public static function user()
    {
        self::init();
        if (self::$userId) {
            $class = App::container()->get(UserInterface::class);
            if (empty($class)) {
                return false;
            }
            $user = $class->find(self::$userId);
            if (isset($user['password'])) {
                unset($user['password']);
                return $user;
            }
        }

        return false;
    }

    public static function userId()
    {
        self::init();
        return self::$userId;
    }

    public static function group()
    {
        self::init();
        $user = self::user();
        if (empty($user))
            return '';
        return $user['group'];
    }

    public static function logout()
    {
        self::init();
        $pnid = !empty($_COOKIE['pnid']) ? $_COOKIE['pnid'] : '';
        $userAgent = self::userAgent();
        if (empty($pnid)) {
            AuthGateway::instance()
                ->whereBy('user_id', self::$userId)
                ->whereBy('user_agent', $userAgent)
                ->delete();
        } else {
            self::setCookie('pnid', '', 0);
            AuthGateway::instance()
                ->whereId(self::authId($pnid, $userAgent))
                ->delete();
        }
        self::$userId = 0;
    }

    public static function check()
    {
        self::init();
        if (self::$userId) {
            return true;
        }

        return false;
    }

    public static function init()
    {
        static $started = false;
        if ($started) {
            return;
        } else {
            $started = true;
        }

        $pnid = isset($_COOKIE['pnid']) && !empty($_COOKIE['pnid']) ? $_COOKIE['pnid'] : '';
        if (empty($pnid)) {
            return false;
        }

        $userAgent = self::userAgent();
        $authId = self::authId($pnid, $userAgent);

        $auth = AuthGateway::instance()->find($authId);
        if (!isset($auth['user_id'])) {
            return false;
        }

        self::$userId = $auth['user_id'];

        self::renew($authId, $pnid, $auth['expired']);
    }

    private static function renew($authId, $pnid, $dateExpired)
    {
        self::init();

        self::setCookie('pnid', $pnid, self::timeExpired());

        $timeExpired = self::dateToTime($dateExpired);
        if ($timeExpired < (time() - self::$expired)) {
            return false;
        }

        if ($timeExpired < time()) {
            $data = array(
                'expired' => self::dateExpired()
            );

            AuthGateway::instance()->whereId($authId)->update($data);
        }

        return true;
    }

    public static function attempt($data = array())
    {
        self::init();

        if (!self::validate($data)) {
            return false;
        }

        self::removeExpired();

        $user = self::getUser($data);
        if (!isset($user['id'])) {
            return false;
        }

        $pnid = self::auth($user['id']);
        if (!$pnid) {
            return false;
        }

        self::$userId = $user['id'];

        return self::setCookie('pnid', $pnid, self::timeExpired());
    }

    public static function once($data = array())
    {
        self::init();

        if (!self::validate($data)) {
            return false;
        }

        $user = self::getUser($data);
        if (!isset($user['id'])) {
            return false;
        }

        self::$userId = $user['id'];

        return true;
    }

    public static function validate($data = array())
    {
        $password = isset($data['password']) ? $data['password'] : '';
        $email = isset($data['email']) ? $data['email'] : '';

        if (empty($password) || empty($email)) {
            return false;
        }

        $user = self::getUser($data);
        if (!isset($user['id'])) {
            return false;
        }

        if (!Hash::check($password, $user["password"])) {
            return false;
        }

        return true;
    }

    public static function validateUserId($userId)
    {
        $userId = intval($userId);
        if ($userId == 0) {
            return false;
        }
        
        $class = App::container()->get(UserInterface::class);
        if (empty($class)) {
            return false;
        }

        return $class->exists($userId);
    }

    public static function login($user)
    {
        self::init();

        if (!isset($user['id']) || empty($user['id'])) {
            return false;
        }

        return self::auth($user['id']);
    }

    public static function loginUsingId($userId)
    {
        self::init();

        if (!self::validateUserId($userId)) {
            return false;
        }

        $pnid = self::auth($userId);
        if (!$pnid) {
            return false;
        }

        self::$userId = $userId;

        return self::setCookie('pnid', $pnid, self::timeExpired());
    }

}
