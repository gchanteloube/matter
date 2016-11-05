<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 05/11/16
 * Time: 00:26
 */

namespace Matter;


class Mysql {
    public static function execute ($db, $queries) {
        // Open transaction
        mysqli_query($db, 'set autocommit =0');
        mysqli_query($db, 'start transaction');

        foreach ($queries as $query) {
            $mq = mysqli_query($db, $query);
        }

        // Close transaction
        mysqli_query($db, 'commit');
        mysqli_close($db);
    }

    public static function connect ($host, $user, $passwd, $name, $port) {
        $db = mysqli_connect($host, $user, $passwd, $name, $port);

        if (Utils::valid($db)) return $db;
        else throw new \Exception('Could not connect to database, check your "database.ini" file please.');
    }
}