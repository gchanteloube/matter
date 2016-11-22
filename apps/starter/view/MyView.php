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
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
            <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
            <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
            <input name="notify_url" value="S/starter.paypal" type="hidden">
        ');
        \Payway\Payway::customer(
            'Guillaume',
            'Chanteloube',
            '33b rue Bataille',
            'Lyon',
            '69008',
            'France',
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
