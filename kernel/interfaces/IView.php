<?php

namespace Matter;
use MatthiasMullie\Minify\Minify;

/**
 * Description of IView
 *
 * @author guillaumech
 */

abstract class IView {
    public $context = null;
    private $html = null;
    private $dataList = array();
    private $type = '';

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

    protected function html ($html) {
        $this->html .= $html;
    }

    protected function css($resource) {
        $minifier = new \MatthiasMullie\Minify\CSS('../apps/' . $this->context . '/assets/css/' . $resource);
        $this->html .= '<style type="text/css">' . $minifier->minify() . '</style>';
    }

    protected function js($resource) {
        if ($resource !== '~') {
            // Lazy load
            $resource = substr($resource, 1);
            $tmp = explode('[', $resource);
            $className = trim(explode('=', file_get_contents('../apps/' . $this->context . '/assets/js/' . $tmp[0]))[0]);
            if (count($tmp) > 1) $method = substr($tmp[1], 0);
            else $method = 'init';
            $varJs = strtolower($className);

            $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->context . '/assets/js/' . $tmp[0]);
            $minifier->add('
            var ' . $varJs . ' = new ' . $className . '();
                ' . $varJs . '.' . $method . '();
            ');
            $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
        } else {
            $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->context . '/assets/js/' . $resource);
            $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
        }
    }

    public function _this() {
        return get_object_vars($this);
    }
}

?>
