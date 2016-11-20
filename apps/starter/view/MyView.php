<?php

namespace View;

/**
 * Description of MyView
 *
 * @author guich
 */
class MyView extends \Matter\IView {
    public function _default () {
        $render = \Payway\Payway::render('starter.payway');

        $this->html('
            ' . $render . '
        ');
    }
}

?>
