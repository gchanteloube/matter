<?php

namespace Model;

/**
 * Description of MyModel
 *
 * @author guich
 */
class MyModel extends \Matter\IModel {
    public function users () {
        $result = $this->db('master')->query('Select * from user_epicier')->execute();
        //$result = $this->db('second')->query('Select * from ps_access')->execute();

        return $result;
    }
}

?>
