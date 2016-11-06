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
    private $title = null;
    private $description = null;
    private $favicon = null;

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
                        $ctrl = Factory::get('\\Controller\\' . (string) $subVal->controller, '../apps/' . (string) $subVal->name . '/controller');
                        $this->render = Dispatcher::Forward($ctrl);
                        $templateName = (string) $subVal->template;
                        $this->title = (string) $subVal->title;
                        $this->description = (string) $subVal->description;
                        $this->favicon = (string) $subVal->favicon;
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
                            $this->render = str_replace('{{_meta_}}', $this->addMeta(), $this->render);

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

    private function addMeta() {
        /* @var $kernel \Matter\Conversation */
        $kernel = Conversation::init('KERNEL');

        // Master meta
        $meta = '<base href="' . Utils::getEnvironment()['site'] . '"><!--[if lte IE 6]></base><![endif]-->';
        if (Utils::valid($kernel->get('title_mr'))) $meta .= '<title>' . $kernel->get('title_mr') . '</title>';
        else if (Utils::valid($this->title)) $meta .= '<title>' . $this->title . '</title>';
        if (Utils::valid($kernel->get('desc_mr'))) $meta .= '<meta name="description" content="' . $kernel->get('desc_mr') . '">';
        else if (Utils::valid($this->description)) $meta .= '<meta name="description" content="' . $this->description . '">';
        if (Utils::valid($kernel->get('fav_mr'))) $meta .= '<link rel="icon" type="image/png" href="struct/assets/img/' . $kernel->get('fav_mr') . '" />';
        else if (Utils::valid($this->favicon)) $meta .= '<link rel="icon" type="image/png" href="struct/assets/img/' . $this->favicon . '" />';

        $social_ini = parse_ini_file("../conf/social.ini", true);
        $general = $social_ini['general'];

        // FB meta
        $facebook = $social_ini['facebook'];
        Utils::valid($facebook['fb_app_id']) ? $meta .= '<meta property="fb:app_id" content="' . $facebook['fb_app_id'] . '">' : false;
        Utils::valid($facebook['fb_page_id']) ? $meta .= '<meta property="fb:page_id" content="' . $facebook['fb_page_id'] . '">' : false;
        Utils::valid($general['site_name']) ? $meta .= '<meta property="og:site_name" content="' . $general['site_name'] . '">' : false;
        if (Utils::valid($kernel->get('title_mr'))) $meta .= '<meta property="og:title" content="' . $kernel->get('title_mr') . '">';
        else if (Utils::valid($general['title'])) $meta .= '<meta property="og:title" content="' . $general['title'] . '">';
        if (Utils::valid($kernel->get('desc_mr'))) $meta .= '<meta property="og:description" content="' . $kernel->get('desc_mr') . '">';
        else if (Utils::valid($general['description'])) $meta .= '<meta property="og:description" content="' . $general['description'] . '">';
        if (Utils::valid($kernel->get('image_mr'))) $meta .= '<meta property="og:image" content="' . $kernel->get('image_mr') . '">';
        else if (Utils::valid($general['image'])) $meta .= '<meta property="og:image" content="' . $general['image'] . '">';
        $meta .= '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
        $meta .= '<meta property="og:image:width" content="644" />';
        $meta .= '<meta property="og:image:height" content="322" />';
        $meta .= '<meta property="og:image:type" content="image/jpeg" />';
        $meta .= '<meta property="og:type" content="article" />';
        $meta .= '<meta property="al:web:url" content="http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . '">';
        $meta .= '<meta property="al:web:should_fallback" content="true">';

        // Twitter meta
        $meta .= '<meta name="twitter:card" content="summary_large_image">';
        Utils::valid($general['site_name']) ? $meta .= '<meta property="twitter:site_name" content="' . $general['site_name'] . '">' : false;
        $meta .= '<meta property="twitter:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
        if (Utils::valid($kernel->get('title_mr'))) $meta .= '<meta property="twitter:title" content="' . $kernel->get('title_mr') . '">';
        else if (Utils::valid($general['title'])) $meta .= '<meta property="twitter:title" content="' . $general['title'] . '">';
        if (Utils::valid($kernel->get('desc_mr'))) $meta .= '<meta property="twitter:description" content="' . $kernel->get('desc_mr') . '">';
        else if (Utils::valid($general['description'])) $meta .= '<meta property="twitter:description" content="' . $general['description'] . '">';
        if (Utils::valid($kernel->get('image_mr'))) $meta .= '<meta property="twitter:image" content="' . $kernel->get('image_mr') . '">';
        else if (Utils::valid($general['image'])) $meta .= '<meta property="twitter:image" content="' . $general['image'] . '">';

        return $meta;
    }
}

?>