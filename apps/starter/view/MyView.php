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
            Voici les données envoyées :
        ');

        foreach ($this->d('users') as $user) {
            $this->html('- ' . $user['email'] . '<br />');
        }

        $this->title('toto')->description('Ma description')->image('image.png');
    }
}

?>
