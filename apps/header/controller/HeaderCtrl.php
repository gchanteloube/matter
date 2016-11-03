<?php

namespace Controller;

/**
 * Description of HeaderCtrl
 *
 * @author guich
 */

class HeaderCtrl extends \Matter\IController {
    public function _default () {
        return $this->view('HeaderView');
    }

    public function test () {
        return $this->view('HeaderView');
    }
}
?>
