<?php

namespace Matter;

/**
 * Description of IModel
 *
 * @author guillaumech
 */

abstract class IModel {
    public function _this() {
        return get_object_vars($this);
    }
}

?>
