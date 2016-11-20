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
        $pouet = 'Ma bite';

        $mdl = $this->mdl('MyModel');
        $users = $mdl->users();

        return $this->view('MyView')->data(array('users' => $users));
    }

    public function payway () {
        $tot = 12;
        $invoice = \Payway\Invoice::create(
            'Titre de ma facture',
            array(
                \Payway\Item::create('Box lancement', 40, 1, 0.200),
                \Payway\Item::create('Box', 60, 1, 0.200)
            )
        );
/*
        \Payway\Payway::invoice($invoice);
        */
    }
}
?>
