<?php

namespace Matter;

/**
 * Description of Utils
 *
 * @author guillaumech
 */
class Utils {
    public static function loader ($dir) {
        $flux = opendir($dir);
        if ($flux) {
            while (false != ($file = readdir($flux))) {
                $extension = explode('.', $file);
                if ($file != '.' && $file != '..' && $extension[1] == 'php') {
                    require_once $dir . '/' . $file;
                }
            }
        }
    }

    public static function valid($var) {
        if (!isset($var) || empty($var)) {
            return false;
        } else {
            if (is_array($var) && count($var) == 0) {
                return false;
            }
            return true;
        }
    }

    public static function getEnvironment() {
        $environment_ini = parse_ini_file("../conf/environment.ini", true);
        $environment = $environment_ini['current_environment']['environment'];
        return $environment_ini[$environment];
    }

    public static function getDatabase($dbName = null) {
        $database_ini = parse_ini_file("../conf/database.ini", true);
        if (Utils::valid($dbName)) {
            if (array_key_exists($dbName, $database_ini)) {
                return $database_ini[$dbName];
            } else {
                throw new \Exception('This database (' . $dbName . ') doesn\'t exist. Check your "database.ini" file please.');
            }
        } else {
            $environment = Utils::getEnvironment();
            if (array_key_exists('db', $environment)) {
                $dbName = $environment['db'];
                if (array_key_exists($dbName, $database_ini)) {
                    return $database_ini[$dbName];
                } else {
                    throw new \Exception('This database (' . $dbName . ') doesn\'t exist. Check your "database.ini" file please.');
                }
            } else {
                throw new \Exception('You have to define "db" entity in your "environment.ini", linked to "database.ini" file please.');
            }
        }
    }
}

?>
