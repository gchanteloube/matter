<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 08/01/17
 * Time: 19:53
 */

namespace Matter;


class Crypt {
    public static function encrypt ($string) {
        return self::_action('encrypt', $string);
    }

    public static function decrypt ($string) {
        return self::_action('decrypt', $string);
    }

    private static function _action($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'QiX7rJh?946_)';
        $secret_iv = '8Yt8EY84)!.et';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if($action == 'decrypt'){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}