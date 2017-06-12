<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 06/01/17
 * Time: 18:20
 */

namespace Matter;


class Forward {
    public static function to ($path, $array = null) {
        $session = Conversation::init('SESSION');
        $data = $session->get('mr_7*a9qPa2#3EB>', false);

        $lifetime = array();
        if (Utils::valid($array)) {
            foreach ($array as $key => $value) {
                $lifetime[$key] = array($value, 1);
            }
        }
        if (Utils::valid($data)) {
            $decrypt = unserialize(Crypt::decrypt($data));
            foreach ($decrypt as $key => $value) {
                if ($value[1] < 2) $lifetime[$key] = $value;
            }
        }
        if (Utils::valid($lifetime)) {
            $crypt = Crypt::encrypt(serialize($lifetime));
            $session->set('mr_7*a9qPa2#3EB>', $crypt, false);
        }
        header('Location: ' . $path); exit();
    }

    public static function get ($param) {
        $session = Conversation::init('SESSION');
        $data = $session->get('mr_7*a9qPa2#3EB>', false);
        $decrypt = unserialize(Crypt::decrypt($data));

        if (Utils::valid($decrypt) && array_key_exists($param, $decrypt)) {
            return $decrypt[$param][0];
        }
        return null;
    }

    public static function garbadge () {
        $session = Conversation::init('SESSION');
        $data = $session->get('mr_7*a9qPa2#3EB>', false);
        $decrypt = unserialize(Crypt::decrypt($data));

        if (Utils::valid($decrypt)) {
            $lifetime = array();
            foreach ($decrypt as $key => $value) {
                if ($value[1] < 2) $lifetime[$key] = array($value[0], ++$value[1]);
            }
            if (Utils::valid($lifetime)) {
                $crypt = Crypt::encrypt(serialize($lifetime));
                $session->set('mr_7*a9qPa2#3EB>', $crypt, false);
            } else {
                $session->destroy('mr_7*a9qPa2#3EB>');
            }
        }
    }
}