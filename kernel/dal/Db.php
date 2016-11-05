<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 04/11/16
 * Time: 22:38
 */

namespace Matter;

class Db {
    private $type = null;
    private $host = null;
    private $port = null;
    private $name = null;
    private $user = null;
    private $passwd = null;
    private $queries = array();

    public function __construct($database) {
        if (Utils::valid($database['type_db']) && Utils::valid($database['host_db']) && Utils::valid($database['name_db']) && Utils::valid($database['user_db']) && Utils::valid($database['passwd_db'])) {
            $this->type = $database['type_db'];
            $this->host = $database['host_db'];
            $this->name = $database['name_db'];
            $this->user = $database['user_db'];
            $this->passwd = $database['passwd_db'];
            if ($database['type_db'] == 'postgres') Utils::valid($database['port_db']) ? $this->port = $database['port_db'] : $this->port = 5432;
            if ($database['type_db'] == 'mysql') Utils::valid($database['port_db']) ? $this->port = $database['port_db'] : $this->port = 3306;
        } else {
            throw new \Exception('Your database information were wrong. Check your "database.ini" file please.');
        }
    }

    public function query ($query) {
        array_push($this->queries, $query);
        return $this;
    }

    public function execute () {
        $db = $this->connect();
        if (Utils::valid($db)) {
            switch ($this->type) {
                case 'postgres':
                    return Postgres::execute($db, $this->queries);
                    break;
                case 'mysql':
                    return Mysql::execute($db, $this->queries);
                    break;
            }
        } else {
            throw new \Exception('Could not connect to database, check your "database.ini" file please.');
        }
    }

    private function connect () {
        switch ($this->type) {
            case 'postgres':
                return Postgres::connect($this->host, $this->user, $this->passwd, $this->name, $this->port);
                break;
            case 'mysql':
                return Mysql::connect($this->host, $this->user, $this->passwd, $this->name, $this->port);
                break;
        }
        return null;
    }
}