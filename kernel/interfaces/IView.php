<?php

namespace Matter;

/**
 * Description of IView
 *
 * @author guillaumech
 */

abstract class IView {
    public $content;
    private $dataList = array();
    public $type = '';

    public function json () {
        $this->type = 'json';
        return $this;
    }

    public function data () {
        $args = array_slice(func_get_args(), 0)[0];
        if (Utils::valid($args)) {
            foreach ($args as $key => $val) {
                $this->dataList[$key] = $val;
            }
        }

        return $this;
    }

    protected function d($key) {
        if (array_key_exists($key, $this->dataList)) {
            return $this->dataList[$key];
        }
        return null;
    }
    public function _default () {
        return $this->content;
    }

}

?>
