<?php

namespace View;

/**
 * Description of HeaderView
 *
 * @author guich
 */
class HeaderView extends \Matter\IView {
    public function _default () {
        $this->css('header.css');

        $this->html('
            <div class="test">
                <a href="starter.test">Header !!! ' . $this->d('utils') . '</a>
            </div>
        ');

        $this->js('~header.js');
    }
}

?>
