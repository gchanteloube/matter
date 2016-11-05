<?php

// Class auto loaded
require_once 'dependency/composer/vendor/autoload.php';
require_once 'dependency/Utils.php';
\Matter\Utils::loader('interfaces');
\Matter\Utils::loader('business');
\Matter\Utils::loader('dal');
\Matter\Utils::loader('../struct/dependency/business');
\Matter\Utils::loader('../struct/dependency/composer');
\Matter\Utils::loader('../struct/dependency/utils');

/* @var $get Conversation */
$get = \Matter\Conversation::init("GET");
$action = $get->get("a_mr");

// Check page
if (\Matter\Utils::valid($action)) {
    /* @var $builder Builder */
    $builder = \Matter\Factory::get('\\Matter\\Builder', '.', $action);

    // Test script value
    if (\Matter\Utils::valid($get->get("s_mr"))) {
        echo $builder->alone();
    } else {
        try {
            echo $builder->friends();
        } catch (\Exception $e) {
            echo addException($e);
        }
    }
}

function addException (\Exception $e) {
    return '
        Matter return join exceptions:<br />
        <ul>
            <li>' . $e->getMessage() . '</li>
            <li>' . getTrace($e) . '</li>
        </ul>
    ';
}

function getTrace (\Exception $e) {
    $trace = '';
    foreach ($e->getTrace() as $t) {
        $file = explode('/', $t['file']); $file = $file[count($file) - 1];
        $trace .= $file . ' (l. ' . $t['line'] . ') <-- ';
    }

    return $trace;
}

function _u($method, $parameters) {
    $flux = opendir('../struct/dependency/utils');
    if ($flux) {
        while (false != ($file = readdir($flux))) {
            $extension = explode('.', $file);
            if ($file != '.' && $file != '..' && $extension[1] == 'php') {
                if (strpos(file_get_contents('../struct/dependency/utils/' . $file), $method) !== false) {
                    $args = array_slice(func_get_args(), 1);
                    return call_user_func_array($extension[0] . '::' . $method, $args);
                }
            }
        }
    }
}

?>
