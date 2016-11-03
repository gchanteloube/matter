<?php

namespace Matter;

/**
 * Description of Builder
 *
 * @author guillaumech
 */

class Builder {
    private $action = null;
    private $render = null;

    public function __construct($action) {
        ini_set('session.use_trans_sid', 0);
        $this->action = $action;
    }

    public function friends() {
        $this->build();
        if (Utils::valid($this->render)) {
            return $this->render;
        } else {
            throw new \Exception('Your action is unknown');
        }
    }

    public function alone() {
        $this->build();
        if (Utils::valid($this->render)) {
            return $this->render;
        } else {
            throw new \Exception('Your action is unknown');
        }
    }

    public function build() {
        if (Utils::valid($this->action)) {
            $data = simplexml_load_file("../struct/apps.xml");
            $templateName = null;

            foreach ($data as $key => $val) {
                if ((string) $val->attributes()->name == $this->action) {
                    foreach ($val as $subKey => $subVal) {
                        $ctrl = Factory::get('\\Controller\\' . (string) $subVal->Controller, '../apps/' . (string) $subVal->Name . '/controller');
                        $this->render = Dispatcher::Forward($ctrl);
                        $templateName = (string) $subVal->Template;
                    }

                    // Load friendly apps
                    /* @var $get Conversation */
                    $get = Conversation::init("GET");
                    if (Utils::valid($templateName) && !Utils::valid($get->get("s_mr"))) {
                        $template = file_get_contents('../struct/templates/' . $templateName . '.html');
                        preg_match_all("|{{(.*)}}|U", $template, $result, PREG_PATTERN_ORDER);

                        if (Utils::valid($result)) {
                            // Load master app
                            $this->render = str_replace('{{current}}', $this->render, $template);

                            // Load meta data
                            $this->render = str_replace('{{_meta_}}', $this->addMeta(), $template);

                            // Load others app
                            foreach ($result[1] as $r) {
                                if ($r != 'current' && $r != '_meta_') {
                                    $tmp = explode('.', $r);
                                    $ctrl = Factory::get('\\Controller\\' . $tmp[1], '../apps/' . $tmp[0] . '/controller');
                                    if (count($tmp) == 3) $content = Dispatcher::Forward($ctrl, $tmp[0], $tmp[2]);
                                    else $content = Dispatcher::Forward($ctrl, $tmp[0]);
                                    $this->render = str_replace('{{' . $r . '}}', $content, $this->render);
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
    }

    private function addMeta () {
        $meta = '
            <base href="' . Utils::getEnvironment()['path'] . '"><!--[if lte IE 6]></base><![endif]-->
        ';

        return $meta;

        /*
        $header = '';

        // MASTER
        if (Utils::isValid($kernel->getValue('title'))) $header .= '<title>' . $kernel->getValue('title') . '</title>';
        else $header .= '<title>' . $this->title . '</title>';
        if (Utils::isValid($kernel->getValue('description'))) $header .= '<meta name="description" content="' . $kernel->getValue('description') . '">';
        else $header .= '<meta name="description" content="' . $this->description . '">';

        // FB
        if (Utils::isValid($this->fb_app_id)) $header .= '<meta property="fb:app_id" content="' . $this->fb_app_id . '">';
        if (Utils::isValid($this->fb_page_id)) $header .= '<meta property="fb:page_id" content="' . $this->fb_page_id . '">';
        if (Utils::isValid($this->fb_site_name)) $header .= '<meta property="og:site_name" content="' . $this->fb_site_name . '">';
        $header .= '<meta property="og:locale" content="fr_FR" />';
        if (Utils::isValid($kernel->getValue('title'))) $header .= '<meta property="og:title" content="' . $kernel->getValue('title') . '">';
        else $header .= '<meta property="og:title" content="' . $this->title . '">';
        if (Utils::isValid($kernel->getValue('description'))) $header .= '<meta property="og:description" content="' . $kernel->getValue('description') . '">';
        else $header .= '<meta property="og:description" content="' . $this->description . '">';
        $header .= '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
        if (Utils::isValid($kernel->getValue('image'))) $header .= '<meta property="og:image" content="' . $kernel->getValue('image') . '">';
        else $header .= '<meta property="og:image" content="' . Utils::getEnvironmentUrl() . $this->image . '">';
        $header .= '<meta property="og:image:width" content="644" />';
        $header .= '<meta property="og:image:height" content="322" />';
        $header .= '<meta property="og:image:type" content="image/jpeg" />';
        $header .= '<meta property="og:type" content="article" />';

        $header .= '<meta property="al:web:url" content="http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . '">';
        $header .= '<meta property="al:web:should_fallback" content="true">';

        // TWITTER
        $header .= '<meta name="twitter:card" content="summary_large_image">';
        if (Utils::isValid($this->twitter_site_name)) $header .= '<meta property="twitter:site_name" content="' . $this->twitter_site_name . '">';
        $header .= '<meta property="twitter:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
        if (Utils::isValid($kernel->getValue('title'))) $header .= '<meta property="twitter:title" content="' . $kernel->getValue('title') . '">';
        else $header .= '<meta property="twitter:title" content="' . $this->title . '">';
        if (Utils::isValid($kernel->getValue('description'))) $header .= '<meta property="twitter:description" content="' . $kernel->getValue('description') . '">';
        else $header .= '<meta property="twitter:description" content="' . $this->description . '">';
        if (Utils::isValid($kernel->getValue('image'))) $header .= '<meta property="twitter:image" content="' . $kernel->getValue('image') . '">';
        else $header .= '<meta property="twitter:image" content="' . $this->image . '">';

        // FAVICON
        if (Utils::isValid($kernel->getValue('favicon'))) $header .= '<link rel="icon" type="image/png" href="' . $kernel->getValue('favicon') . '" />';
        else $header .= '<link rel="icon" type="image/png" href="' . $this->favicon . '" />';

        $header .= '<meta name="viewport" content="width=device-width">';
        $header .= $this->baseUrl;
        $header .= '<meta name="robots" content="index, follow, noarchive">';

        // CSS
        $header .= '<style type="text/css">' . $this->getCssBlock() . '</style>';
        if ($this->cache) {
            $header .= "#{CSS}";
        }

        // JS
        $header .= '<script type="text/javascript">' . $this->getJsBlock() . '</script>';
        foreach ($this->jsInclude as $js) {
            $header .= $js;
        }
        if ($this->cache) {
            $header .= "#{Js}";
        }

        return str_replace("%HEADER%", $header, $text);
        */
    }
}

?>