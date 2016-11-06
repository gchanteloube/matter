<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 05/11/16
 * Time: 16:05
 */

namespace Matter;


class I18n {
    public static function locale () {
        /* @var $get Conversation */
        $get = Conversation::init("GET");
        $tmp = $get->get("l_mr");
        $environment = Utils::getEnvironment();
        Utils::valid($tmp) ? $locale = $tmp : $locale = $environment['locale'];
        return $locale;
    }

    public static function translate ($context, $entity, $parameters = null) {
        if(Utils::valid($entity)) {
            $i18n_ini = parse_ini_file('../apps/' . $context . '/assets/i18n/' . I18n::locale() . '.ini', true);
            if (array_key_exists($entity, $i18n_ini)) {
                $line = $i18n_ini[$entity];

                if (Utils::valid($parameters)) {
                    $args = array_slice(func_get_args(), 1);
                    $patterns = array();
                    $replace = array();

                    for ($i = count($args); $i > 0; $i--) {
                        $patterns[] = '/@' . $i . '/';
                        array_push($replace, $args[$i - 1]);
                    }

                    return preg_replace($patterns, $replace, $line);
                } else {
                    return $line;
                }
            } else {
                throw new \Exception('Your i18n entity is invalid.');
            }
        } else {
            throw new \Exception('Your i18n entity is invalid.');
        }
    }
}