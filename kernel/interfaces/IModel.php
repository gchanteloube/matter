<?php

namespace Matter;

/**
 * Description of IModel
 *
 * @author guillaumech
 */

abstract class IModel {
    protected function db($dbName = null) {
        if (Utils::valid($dbName)) {
            $database_ini = parse_ini_file("../conf/database.ini", true);
            if (array_key_exists($dbName, $database_ini)) {
                return new Db($database_ini[$dbName]);
            } else {
                throw new \Exception('This database (' . $dbName . ') doesn\'t exist. Check your "database.ini" file please.');
            }
        } else {
            throw new \Exception('Database is required.');
        }
    }
}

?>
