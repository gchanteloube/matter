<?php

namespace Matter;

/**
 * Description of Factory
 *
 * @author guich
 */

class Factory {
    public static function get($class, $path) {
        $tmp = explode('\\', $class); if (count($tmp) > 1) $tmp = $tmp[2]; else $tmp = $tmp[0];
        $file = $path . '/' . $tmp . '.php';
        if (file_exists($file) && is_readable($file)) {
            require_once $file;

            $args = array_slice(func_get_args(), 2);
            $classRef = new \ReflectionClass($class);

            return (empty($args) ? new $class : $classRef->newInstanceArgs($args));
        } else {
            throw new \Exception('The ' . $file . ' doesn\'t exist... Check your directory');
        }
    }
}
?>
