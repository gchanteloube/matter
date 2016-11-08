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
            ' . $this->i('welcome', 'www.clozly.com') . ' :
            fhdfghdf fdhgdfg<br />fhtrt ' . _u('dam', $this->i('text')) . '
        ');

        foreach ($this->d('users') as $user) {
            $this->html('- ' . $user['email'] . '<br />');
            //$this->html('- ' . $user['id_profile'] . '<br />');
        }

        $this->title('toto')->description('Ma description')->image('image.png');
    }
}

?>
