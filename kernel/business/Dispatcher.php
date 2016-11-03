<?php

namespace Matter;

class Dispatcher {
    static function Forward(IController $ctrl, $action = null, $method = null) {
        /* @var $get Conversation */
        $get = Conversation::init("GET");
        $specificAction = true;
        if (!Utils::valid($action)) {
            $action = $get->get('a_mr');
            $specificAction = false;
        }
        $ctrl->action = $action;
        if (!Utils::valid($method) && !$specificAction) {
            $method = $get->get('m_mr');
        }

        $ctrl->_before();

        // Execute parameter method
        /* @var $view IView */
        $view = null;
        if (Utils::valid($method)) {
            $classRef = new \ReflectionClass($ctrl);
            $methods = $classRef->getMethods();
            foreach ($methods as $m) {
                if ($m->name === $method) {
                    $view = $ctrl->$method();
                    break;
                }
            }
        }

        if (!Utils::valid($view)) {
            $view = $ctrl->_default();
        }

        if (is_subclass_of($view, '\Matter\IView')) {
            $view->_default();
            if ($view->type == 'json') $content = json_encode($view->render());
            else $content = $view->render();
        } else {
            throw new \Exception('Your controller try to call an unMatter view object (check extends properties)');
        }

        $ctrl->_after();
        return $content;
    }
}