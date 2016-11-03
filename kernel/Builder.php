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
            $data = simplexml_load_file("../struct/templates.xml");
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

                            // Load others app
                            foreach ($result[1] as $r) {
                                if ($r != 'current') {
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

}

?>