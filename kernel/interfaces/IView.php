<?php

namespace Matter;
use MatthiasMullie\Minify\Minify;

/**
 * Description of IView
 *
 * @author guillaumech
 */

abstract class IView {
    public $action = null;
    private $html = null;
    private $dataList = array();
    public $type = '';

    public function json () {
        $this->type = 'json';
        return $this;
    }

    public function data () {
        $args = array_slice(func_get_args(), 0)[0];
        if (Utils::valid($args)) {
            foreach ($args as $key => $val) {
                $this->dataList[$key] = $val;
            }
        }

        return $this;
    }

    protected function d($key) {
        if (array_key_exists($key, $this->dataList)) {
            return $this->dataList[$key];
        }
        return null;
    }

    public function html ($html) {
        $this->html .= $html;
    }

    protected function css() {
        $args = array_slice(func_get_args(), 0);
        foreach ($args as $a) {
            $minifier = new \MatthiasMullie\Minify\CSS('../apps/' . $this->action . '/assets/css/' . $a);
            $this->html .= '<style type="text/css">' . $minifier->minify() . '</style>';
        }
    }

    protected function js() {
        $args = array_slice(func_get_args(), 0);
        foreach ($args as $a) {
            if ($a[0] !== '~') {
                // Lazy load
                $tmp = explode('[', $a);
                $className = trim(explode('=', file_get_contents('../apps/' . $this->action . '/assets/js/' . $tmp[0]))[0]);
                if (count($tmp) > 1) $method = substr($tmp[1], 0, strlen($tmp[1]) - 1);
                else $method = 'init';
                $varJs = strtolower($className);

                $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->action . '/assets/js/' . $tmp[0]);
                $minifier->add('
                var ' . $varJs . ' = new ' . $className . '();
                    ' . $varJs . '.' . $method . '();
                ');
                $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
            } else {
                $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->action . '/assets/js/' . $a);
                $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
            }
        }
    }

    public function render () {
        return $this->html;
    }
}

?>
