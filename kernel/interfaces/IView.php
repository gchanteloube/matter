<?php

namespace Matter;

/**
 * Description of IView
 *
 * @author guillaumech
 */

abstract class IView {
    public $context = null;
    private $html = null;
    private $dataList = array();
    private $type = null;

    public function json() {
        $this->type = 'json';
        return $this;
    }

    public function data($data) {
        if (Utils::valid($data)) {
            foreach ($data as $key => $val) {
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

    protected function html($html) {
        $this->html .= $html;
    }

    protected function css($resource) {
        $minifier = new \MatthiasMullie\Minify\CSS('../apps/' . $this->context . '/assets/css/' . $resource);
        $this->html .= '<style type="text/css">' . $minifier->minify() . '</style>';
    }

    protected function js($resource) {
        $this->html .= '<script   src="https://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>';
        if ($resource[0] !== '~') {
            // Lazy load
            $tmp = explode('[', $resource);
            $className = trim(explode('=', file_get_contents('../apps/' . $this->context . '/assets/js/' . $tmp[0]))[0]);
            if (count($tmp) > 1) $method = substr($tmp[1], 0, strlen($tmp[1]) - 1);
            else $method = 'init';
            $varJs = strtolower($className);

            $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->context . '/assets/js/' . $tmp[0]);
            $minifier->add('
                var ' . $varJs . ' = new ' . $className . '();
                ' . $varJs . '.' . $method . '();
            ');
            $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
        } else {
            $minifier = new \MatthiasMullie\Minify\JS('../apps/' . $this->context . '/assets/js/' . substr($resource, 1));
            $this->html .= '<script type="text/javascript">' . $minifier->minify() . '</script>';
        }
    }

    public function title($title) {
        /* @var $kernel \Matter\Conversation */
        $kernel = Conversation::init('KERNEL');
        $kernel->set('title_mr', $title);
        return $this;
    }

    public function description($description) {
        /* @var $kernel \Matter\Conversation */
        $kernel = Conversation::init('KERNEL');
        $kernel->set('desc_mr', $description);
        return $this;
    }

    public function image($path) {
        /* @var $kernel \Matter\Conversation */
        $kernel = Conversation::init('KERNEL');
        $kernel->set('image_mr', $path);
        return $this;
    }

    protected function i($entity, $parameters = null) {
        return I18n::translate($this->context, $entity, $parameters);
    }

    public function _this() {
        return get_object_vars($this);
    }
}

?>
