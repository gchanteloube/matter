<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UpdateSDAL
 *
 * @author guich
 */

require_once 'SDAL/UtilsSDAL.php';

class UpdateSDAL {
    public static function updateBrutePostgres ($_base, $_sql, $_args, $_UTF8) {
        $sql = UtilsSDAL::replaceArgsSQL($_sql, $_args, $_UTF8);

        $pgq = pg_query ($_base, $sql);
        if ($pgq == false) {
            $response["response"] = "";
            $response["error"] = true;
            $response["details"] = pg_last_error($_base);
        } else {
            $response["response"] = pg_fetch_array($pgq);
            $response["error"] = false;
            $response["details"] = "";
        }
        return $response;
    }
}
?>
