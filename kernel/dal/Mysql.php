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
            $mq = @mysqli_query($db, $query);
            if (Utils::valid($mq)) {
                //return mysqli_fetch_all($mq); Not available in standard lib
                $data = array();
                while ($r = $mq->fetch_assoc()) {
                    array_push($data, $r);
                }
                return $data;
            } else {
                $environment = Utils::getEnvironment();
                $e = 'Your query is wrong';
                if ($environment['db_error'] == 'true') $e .= ' (Details : ' . mysqli_error($db) . ')';
                throw new \Exception($e);
            }
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

    public static function args($db, $query, $args) {
        $patterns = array();
        $replace = array();

        for ($i = count($args); $i > 0; $i--) {
            $patterns[] = '/@' . $i . '/';
            array_push($replace, mysqli_real_escape_string($db, $args[$i - 1]));
        }

        return preg_replace($patterns, $replace, $query);
    }
}