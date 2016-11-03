<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 16/01/16
 * Time: 15:58
 */

class UtilsSDAL {
    public static function protectSQLValue($_string) {
        UtilsSDAL::getDbConnexion();

        if (is_array($_string)) {
            $protected = array();
            for ($i = 0; $i < count($_string); $i++) {
                $protected[] = pg_escape_string($_string[$i]);
            }
            return $protected;
        } else {
            return pg_escape_string($_string);
        }
    }

    public static function replaceArgsSQL($_sql, $_args, $UTF8 = true) {
        $patterns = array();
        $argsUTF8 = array();
        $args = array();

        // Starting with > 9 (start with 1)
        if (count($_args) > 9) {
            for ($i = 10; $i <= count($_args); $i++) {
                $patterns[] = '/@' . $i . '/';
                if ($UTF8) $argsUTF8[] = $_args[$i - 1];
                else $args[] = $_args[$i - 1];
            }
        }

        for ($i = 1; $i <= count($_args); $i++) {
            $patterns[] = '/@' . $i . '/';
            if ($UTF8) $argsUTF8[] = $_args[$i - 1];
            else $args[] = $_args[$i - 1];
        }

        if ($UTF8) return preg_replace($patterns, UtilsSDAL::protectSQLValue($argsUTF8), $_sql);
        else return preg_replace($patterns, UtilsSDAL::protectSQLValue($args), $_sql);
    }

    public static function getDbConnexion ($db = '') {
        $IHM_ini = parse_ini_file("Config/IHM.ini");
        $environment = $IHM_ini['Environment'];

        $Base_ini = parse_ini_file("Config/Db.ini");
        return pg_connect($Base_ini[$environment . $db]);
    }
} 