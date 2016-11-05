<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 05/11/16
 * Time: 00:26
 */

namespace Matter;


class Postgres {
    public static function execute ($db, $queries) {
        // Open transaction
        pg_query($db, 'begin');

        foreach ($queries as $query) {
            $pgq = @pg_query($db, $query);
            if (Utils::valid($pgq)) {
                return pg_fetch_all($pgq);
            } else {
                $environment = Utils::getEnvironment();
                $e = 'Your query is wrong';
                if ($environment['db_error'] == 'true') $e .= ' (Details : ' . pg_last_error($db) . ')';
                throw new \Exception($e);
            }
        }

        // Close transaction
        pg_query($db, 'commit');
        pg_close($db);
    }

    public static function connect ($host, $user, $passwd, $name, $port) {
        $db = pg_connect("host=$host port=$port dbname=$name user=$user password=$passwd");

        if (Utils::valid($db)) return $db;
        else throw new \Exception('Could not connect to database, check your "database.ini" file please.');
    }
}