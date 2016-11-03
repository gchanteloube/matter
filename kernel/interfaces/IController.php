<?php

namespace Matter;

/**
 * Description of IController
 *
 * @author guillaumech
 */

abstract class IController {
    public $context;
    public $type = null;

    public function _default () {
        return 'Default view';
    }

    public function _before () {}

    public function _after () {}

    public function view ($name = null) {
        /* @var $view IView */
        $view = Factory::get('\\View\\' . $name, '../apps/' . $this->context . '/view');
        if (is_subclass_of($view, '\Matter\IView')) {
            $view->context = $this->context;
            return $view;
        } else {
            throw new \Exception('Your controller try to call an unMatter view object (check extends properties)');
        }
    }

    public function mdl ($name = null) {
        /* @var $view IModel */
        $mdl = Factory::get('\\Model\\' . $name, '../apps/' . $this->context . '/model');
        if (is_subclass_of($mdl, '\Matter\IModel')) {
            return $mdl;
        } else {
            throw new \Exception('Your controller try to call an unMatter model object (check extends properties)');
        }
    }
}
?>
