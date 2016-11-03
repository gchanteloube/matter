<?php

namespace View;

/**
 * Description of MyView
 *
 * @author guich
 */
class MyView extends \Matter\IView {
    public function _default () {
        $this->content = '
            Voici la donnée envoyée : ' . $this->d('pouet3') . '
        ';
    }
}

?>
