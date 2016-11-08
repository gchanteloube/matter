<?php

namespace Matter;

/**
 * Description of IModel
 *
 * @author guillaumech
 */

abstract class IModel {
    protected function db($dbName = null) {
        $database_ini = parse_ini_file("../conf/database.ini", true);
        if (Utils::valid($dbName)) {
            if (array_key_exists($dbName, $database_ini)) {
                return new Db($database_ini[$dbName]);
            } else {
                throw new \Exception('This database (' . $dbName . ') doesn\'t exist. Check your "database.ini" file please.');
            }
        } else {
            $environment = Utils::getEnvironment();
            if (array_key_exists('db', $environment)) {
                $dbName = $environment['db'];
                if (array_key_exists($dbName, $database_ini)) {
                    return new Db($database_ini[$dbName]);
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
