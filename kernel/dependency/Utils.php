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

    public static function getEnvironment () {
        $environment_ini = parse_ini_file("../conf/environment.ini");
        $environment = $environment_ini['current'];
        $path = $environment_ini[$environment_ini['current']];
        return array('environment' => $environment, 'path' => $path);
    }
}

?>
