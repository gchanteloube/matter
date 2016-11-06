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
        $users = $mdl->users();

        return $this->view('MyView')->data(array('users' => $users));
    }

    public function test () {
        $view = $this->view('MyView');
        $view->data(array('users' => array('poney', 'coeur', 'coeur')));

        return $view;
    }
}
?>
