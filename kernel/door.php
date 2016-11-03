<?php

namespace Matter;

// Class auto loaded
require_once 'business/Utils.php';
Utils::loader('interfaces');
Utils::loader('business');

/* @var $get Conversation */
$get = Conversation::init("GET");
$action = $get->get("a_mr");

// Check page
if (Utils::valid($action)) {
    /* @var $builder Builder */
    $builder = Factory::get('\\Matter\\Builder', '.', $action);

    // Test script value
    if (Utils::valid($get->get("s_mr"))) {
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

?>
