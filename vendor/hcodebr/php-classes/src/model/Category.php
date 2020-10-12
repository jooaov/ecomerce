<?php
namespace Hcode\model;

use Hcode\Db\Sql;
use Hcode\Mailer;
use Hcode\Model;

class Category extends Model
{

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_categories ORDER BY descategory");
    }

    public function save(){
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_categories_save(:idcategory,:descategory)", array(
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory(),
        ));
        $this->setData($results[0]);
        Category::updateFile();
    }

    public function get($idcategory){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",array(
            ":idcategory"=>$idcategory
        ));
        $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",array(
            ":idcategory"=>$this->getidcategory()
        ));
        Category::updateFile();
    }
    
    public function setData ($data = array()){
        foreach ($data as $key => $value) {
            $this->{"set".$key}($value);
        }
    }

    public static function updateFile(){
        $categories = Category::listAll();
        $html = [];
        foreach ($categories as $row) {
            array_push($html,'<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }
        $file = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'Categories-menu.html';
        file_put_contents($file,implode('',$html));
    }

}
