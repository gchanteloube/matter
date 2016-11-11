<?php

namespace Matter;

/**
 * Description of IModel
 *
 * @author guillaumech
 */

abstract class IModel {
    protected function db($dbName = null) {
        $dbInfo = Utils::getDatabase($dbName);
        return new Db($dbInfo);
    }
}

?>
