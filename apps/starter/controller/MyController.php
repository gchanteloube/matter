<?php

namespace Controller;

/**
 * Description of MyController
 *
 * @author guich
 */

class MyController extends \Matter\IController {
    public function _default () {
        $pouet = 'Ma bite';

        $mdl = $this->mdl('MyModel');
        $return = $mdl->toto();
        return $this->view('MyView')->data(array('pouet' => $pouet, 'pouet3' => $return));
    }

    public function test () {
        $view = $this->view('MyView');
        $view->data(array('pouet3' => 'poney'));

        return $view;
    }
}
?>
