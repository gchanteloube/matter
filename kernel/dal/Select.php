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

require_once 'SDAL/SelectSDAL.php';

class Select {
    private $mSqlBrute;
    private $mProtege;
    private $args = null;
    private $UTF8 = true;

    private function getMSqlBrute() {
        return $this->mSqlBrute;
    }

    private function setMSqlBrute($mSqlBrute) {
        $this->mSqlBrute = $mSqlBrute;
    }

    public function selectBrute ($_sql, $_protege = true) {
        $this->mProtege = $_protege;
        $this->setMSqlBrute($_sql);
    }

    public function execute ($_base) {
        if (Utils::isValid($this->mSqlBrute)) {
            return SelectSDAL::selectBrutePostgres($_base, $this->mSqlBrute, $this->args, $this->mProtege, $this->UTF8);
        }
    }

    public function args ($_args) {
        $this->args = array_slice(func_get_args(), 0);
    }

    public function setUTF8($UTF8) {
        $this->UTF8 = $UTF8;
    }

    public function getUTF8() {
        return $this->UTF8;
    }
}
?>
