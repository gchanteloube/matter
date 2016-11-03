<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Select
 *
 * @author guillaumech
 */

require_once 'SDAL/UtilsSDAL.php';

class SelectSDAL {
    public static function selectBrutePostgres ($_base, $_sql, $_args, $_protege, $_UTF8) {
        $sql = UtilsSDAL::replaceArgsSQL($_sql, $_args, $_UTF8);

        $pgq = pg_query ($_base, $sql);
        if ($pgq == false) {
            $response["response"] = "";
            $response["error"] = true;
            (Utils::getEnvironment() != 'Production') ? $response["details"] = pg_last_error($_base) : $response["details"] = "";
        } else {
            $response["response"] = "";
            $response["error"] = false;
            $response["details"] = "";
        }

        $data = null;
        while ($donnees = pg_fetch_array ($pgq)) {
            foreach ($donnees as $key => $value) {
                if ($_protege == true) {
                    $donneesDecode [$key] = htmlspecialchars($value, ENT_QUOTES);
                } else {
                    $donneesDecode [$key] = $value;
                }
            }

            $data [] = $donneesDecode;
        }

        if($pgq == true && Utils::isValid($data)) {
            $response["response"] = $data;
        }

        return $response;
    }
}
?>
