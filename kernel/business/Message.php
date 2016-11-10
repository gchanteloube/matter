<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 10/11/16
 * Time: 16:57
 */

namespace Matter;


class Message extends IView {
    private $html = null;
    private $type = null;

    public function __construct ($content = null) {
        $this->html = $content;
        return $this;
    }

    public function json() {
        $this->type = 'json';
        return $this;
    }

    public function _default () {

    }

    public function _this() {
        return get_object_vars($this);
    }
}