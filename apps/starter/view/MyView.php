<?php

namespace View;

/**
 * Description of MyView
 *
 * @author guich
 */
class MyView extends \Matter\IView {
    public function _default() {
        \Payway\Payway::paypal('
            <input type="hidden" name="hosted_button_id" value="JM36G5TFWTG4Y">
        ');

        \Payway\Payway::customer(
            'Guillaume',
            'Chanteloube',
            '33b rue Bataille',
            'Lyon',
            '69008',
            'France',
            'FR',
            '0603541823',
            'guillaumech@gmail.com',
            true
            //'cus_9beb1QygUS6GUf',
            //'722832'
        );

        $render = \Payway\Payway::render('S/starter.payway');

        $this->html('
            ' . $render . '
        ');
    }
}

?>
