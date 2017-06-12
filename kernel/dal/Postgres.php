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

        $queriesData = array();
        foreach ($queries as $query) {
            $pgq = @pg_query($db, $query);
            if (Utils::valid($pgq)) {
                array_push($queriesData, pg_fetch_all($pgq));
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

        if (count($queriesData) > 1) return $queriesData;
        else return $queriesData[0];
    }

    public static function connect ($host, $user, $passwd, $name, $port) {
        $db = pg_connect("host=$host port=$port dbname=$name user=$user password=$passwd");

        if (Utils::valid($db)) return $db;
        else throw new \Exception('Could not connect to database, check your "database.ini" file please.');
    }

    public static function args($db, $query, $args) {
        $patterns = array();
        $replace = array();

        $encrypt = Utils::getDatabase('master')['encrypt_db'];
        for ($i = count($args); $i > 0; $i--) {
            $patterns[] = '/\'~@' . $i . '\'/';
            $d = pg_escape_string($db, $args[$i - 1]);
            array_push($replace, 'encrypt(\'' . $d . '\', \'' . $encrypt . '\', \'aes\')');
        }
        $query = preg_replace($patterns, $replace, $query);

        for ($i = count($args); $i > 0; $i--) {
            $patterns[] = '/@' . $i . '/';
            array_push($replace, pg_escape_string($db, $args[$i - 1]));
        }
        $query = preg_replace($patterns, $replace, $query);

        // Decrypt for select query
        $query = preg_replace('/~(\w+\.\w+)/i', 'convert_from(decrypt(${1}::bytea, \'' . $encrypt . '\', \'aes\'), \'UTF-8\')', $query);
        $query = preg_replace('/~(\w+)/i', 'convert_from(decrypt(${1}::bytea, \'' . $encrypt . '\', \'aes\'), \'UTF-8\')', $query);

        return $query;
    }
}