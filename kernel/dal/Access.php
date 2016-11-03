<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Access
 *
 * @author chanteloube
 */

require_once 'SDAL/UtilsSDAL.php';

class Access {
    private $mDb;
    private $mTransations;

    public function  __construct($db = "") {
        $this->mDb = UtilsSDAL::getDbConnexion($db);
    }

    public function getMTransations() {
        return $this->mTransations;
    }

    public function setMTransations($mTransations) {
        $this->mTransations = $mTransations;
    }

    public function addTransaction ($_transaction) {
        $this->mTransations[] = $_transaction;
    }

    public function executeTransactionMySQL () {
        foreach ($this->getMTransations() as $transaction) {
            $resultats = $transaction->execute($this->mDb);
        }

        return $resultats;
    }

    public function executeTransaction () {
        try {
            foreach ($this->getMTransations() as $transaction) {
                $resultats = $transaction->execute($this->mDb);
            }

            $sql = 'COMMIT';
            $req = pg_query($this->mDb, $sql);

            return $resultats;
        } catch (Exception $e) {
            $sql = 'ROLLBACK';
            $req = pg_query ($this->mDb, $sql);
        }
    }
}
?>
