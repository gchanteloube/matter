<?php

namespace View;

/**
 * Description of MyView
 *
 * @author guich
 */
class MyView extends \Matter\IView {
    public function _default () {
        $this->html('
            Voici la donnée envoyée : ' . $this->d('pouet3') . '
        ');
        $this->title('toto')->description('Ma description')->image('image.png');
    }
}

?>
