<?php

namespace Model;

/**
 * Description of MyModel
 *
 * @author guich
 */
class MyModel extends \Matter\IModel {
    public function users () {
        //$result = $this->db('master')->query('Select * from user_epicier limit @1', 13)->execute();
        $result = $this->db('second')->query('Select * from ps_access limit @1', 13)->execute();

        return $result;
    }
}

?>
