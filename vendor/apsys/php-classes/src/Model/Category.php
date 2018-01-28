<?php

namespace Apsys\Model;

use \Apsys\DB\Sql;
use \Apsys\Model;
use \Apsys\Mailer;

class Category extends Model {

  public static function listAll(){
    $sql = new Sql();
    return $sql->select("SELECT * FROM tb_categories  ORDER BY descategory");

  }

  public function save()
  {

    $sql = new Sql();

    $res = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",
    array(
      ":idcategory"=>$this->getidcategory(),
      ":descategory"=>$this->getdescategory()
    ));

    $this->setData($res[0]);

  }

  public function get($idcategory)
  {
    $sql = new Sql();

    $res = $sql-> select("SELECT * FROM tb_categories WHERE idcategory=:idcategory",
    array(":idcategory"=>$idcategory));

    $this->setData($res[0]);

  }

  public function delete()
  {

        $sql = new Sql();

        $sql->query("DELETE FROM tb_categories WHERE idcategory=:idcategory",
          array(":idcategory"=>$this->getidcategory()
        ));

  }

}

?>
