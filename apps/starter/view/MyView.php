<?php

namespace View;

/**
 * Description of MyView
 *
 * @author guich
 */
class MyView extends \Matter\IView {
    public function _default () {
        /*
        $invoice = \Payway\Invoice::create(
            'Titre de ma facture',
            array(
                \Payway\Item::create('Box lancement', 40, 1, 0.200),
                \Payway\Item::create('Box', 60, 1, 0.200)
            )
        );

        \Payway\Payway::invoice($invoice);
        */

        $render = \Payway\Payway::render('starter.payway');

        $this->html('
            ' . $render . '
        ');
    }
}

?>
