<?php

namespace Hcode\model;

use Hcode\Db\Sql;
use Hcode\Mailer;
use Hcode\Model;

class Products extends Model
{

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_products ORDER BY desproduct");
    }

    public function save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_products_save(:idproduct,:desproduct,:vlprice,:vlwidth,:vlheight,:vllength,:vlweight,:desurl)", array(
            ":idproduct" => $this->getidproduct(),
            ":desproduct" => $this->getdesproduct(),
            ":vlprice" => $this->getvlprice(),
            ":vlwidth" => $this->getvlwidth(),
            ":vlheight" => $this->getvlheight(),
            ":vllength" => $this->getvlheight(),
            ":vlweight" => $this->getvlweight(),
            ":desurl" => $this->getdesurl()
        ));
        $this->setData($results[0]);
    }

    public function get($idcategory)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct" => $idcategory
        ));
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct" => $this->getidproduct()
        ));
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set" . $key}($value);
        }
    }

    public function checkPhoto()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
            "res" . DIRECTORY_SEPARATOR .
            "site" . DIRECTORY_SEPARATOR .
            "img" . DIRECTORY_SEPARATOR .
            "products" . DIRECTORY_SEPARATOR . $this->getidproduct() .  ".jpg")) {
            $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
        } else {
            $url = "/res/site/img/products/product.jpg";
        }
        //setdesphoto($url) checa se existe a umage e define no obj ou define a umagem defalut se não existir
        return $this->setdesphoto($url);
    }

    public function getValues()
    {
        $this->checkPhoto();
        $values = parent::getValues();
        return $values;
    }

    public function setPhoto($file)
    {
        //resposta em img cria um arquivo temporario
        //$image pega o local do arquivo tem e converte
        $extension = explode('.', $file['name']);
        $extension = end($extension);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file["tmp_name"]);
                break;
            case "gif":
                $image = imagecreatefromgif($file["tmp_name"]);
            case "png":
                $image = imagecreatefrompng($file["tmp_name"]);
        }
        $dir =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
        "res" . DIRECTORY_SEPARATOR .
        "site" . DIRECTORY_SEPARATOR .
        "img" . DIRECTORY_SEPARATOR .
        "products" . DIRECTORY_SEPARATOR . $this->getidproduct() .  ".jpg";
        if($extension !== ''){
            imagejpeg($image,$dir);
            imagedestroy($image);
        }
        $this->checkPhoto();
    }
}
