<?php

namespace Matter;

/**
 * Description of IController
 *
 * @author guillaumech
 */

abstract class IController {
    public $action;
    public $type = null;

    public function _default () {
        return 'Default view';
    }

    public function _before () {}

    public function _after () {}

    public function view ($name = null) {
        /* @var $view IView */
        $view = Factory::get('\\View\\' . $name, '../apps/' . $this->action . '/view');
        if (is_subclass_of($view, '\Matter\IView')) {
            return $view;
        } else {
            throw new \Exception('Your controller try to call an unMatter view object (check extends properties)');
        }
    }

    public function mdl ($name = null) {
        /* @var $view IModel */
        $mdl = Factory::get('\\Model\\' . $name, '../apps/' . $this->action . '/model');
        if (is_subclass_of($mdl, '\Matter\IModel')) {
            return $mdl;
        } else {
            throw new \Exception('Your controller try to call an unMatter model object (check extends properties)');
        }
    }

    public function generate () {
        if ($this->type == 'JSON') {
            return json_encode($this->view);
        } else {
            return $this->view;
        }
    }
}
?>
