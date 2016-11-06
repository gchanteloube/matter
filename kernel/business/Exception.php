<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 06/11/16
 * Time: 20:51
 */

namespace Matter;


class Exception {
    public static function addException (\Exception $e) {
        return '
        <html>
            <body style="background-color: #4a5c69; color: #ffffff">
                <h2>' . $e->getMessage() . '</h2>
                <div style="text-align: center; width: 400px;">
                    ' . Exception::getTrace($e) . '
                </div>
            </body>
        </html>
    ';
    }

    private function getTrace (\Exception $e) {
        $trace = '';
        foreach ($e->getTrace() as $t) {
            $file = explode('/', $t['file']); $file = $file[count($file) - 1];
            if (strpos($t['file'], 'kernel')) $trace .= '
                <div style="border: 1px solid #ffffff; background-color: #f2f2f2; padding: 6px 0 6px 0; color: #4a5c69; opacity: .7;">
                    <strong>' . $file . '</strong><br />
                    <label style="font-style: italic; font-size: 13px;">File: [...]' . substr($t['file'], count($t['file']) - 40) . ' on line ' . $t['line'] . '</label>
                </div>
            ';
            else $trace .= '
                <div style="border: 1px solid #ffffff; background-color: #f2f2f2; padding: 6px 0 6px 0; color: #4a5c69; border-left: 15px solid #e66665;">
                    <strong>' . $file . '</strong><br />
                    <label style="font-style: italic; font-size: 13px;">File: [...]' . substr($t['file'], count($t['file']) - 40) . ' on <b>line ' . $t['line'] . '</b></label>
                </div>
            ';
            $trace .= '<label style="margin: 10px 0 10px 0; display: block; font-weight: 900;">&#8613;</label>';
        }
        $trace .= 'URL';

        return $trace;
    }
}