<?php

namespace Controller;

/**
 * Description of HeaderCtrl
 *
 * @author guich
 */

class HeaderCtrl extends \Matter\IController {
    public function _default () {
        $t = _u('low', 'pourquoi TOI ?');
        return $this->view('HeaderView')->data(array('utils' => $t));
    }

    public function test () {
        return $this->view('HeaderView');
    }
}
?>
