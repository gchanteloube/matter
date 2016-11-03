<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Update
 *
 * @author guich
 */

require_once 'SDAL/UpdateSDAL.php';

class Update {
    private $mSqlBrute;
    private $args = null;
    private $UTF8 = true;

    private function getMSqlBrute() {
        return $this->mSqlBrute;
    }

    private function setMSqlBrute($mSqlBrute) {
        $this->mSqlBrute = $mSqlBrute;
    }

    public function updateBrute ($_sql) {
        $this->setMSqlBrute($_sql);
    }

    public function execute ($_base) {
        if (Utils::isValid($this->mSqlBrute)) {
            return UpdateSDAL::updateBrutePostgres($_base, $this->mSqlBrute, $this->args, $this->UTF8);
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
