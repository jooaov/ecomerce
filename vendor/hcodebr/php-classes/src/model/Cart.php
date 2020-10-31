<?php

namespace Hcode\model;

use Hcode\Db\Sql;
use Hcode\Mailer;
use Hcode\Model;
use Hcode\Model\User;

class Cart extends Model
{
    const SESSION = "Cart";

    public static function getFromSession()
    {
        $cart = new Cart();

        if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

            $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
        } else {
            $cart->getFromSessionID();
            if (!(int)$cart->getidcart() > 0) {
                $data = [
                    'dessessionid' => session_id()
                ];
                if (User::checkLogin(false)) {
                    $user = User::getFromSession();
                    $data['iduser'] = $user->getiduser();
                }
                $cart->setData($data);
                $cart->save();
                $cart->setToSession();
            }
        }
        return $cart;
    }


    public function setToSession()
    {
        $_SESSION[Cart::SESSION] = $this->getValues();
    }


    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [
            ':idcart' => $this->getidcart(),
            ':dessessionid' => $this->getdessessionid(),
            ':iduser' => $this->getiduser(),
            ':deszipcode' => $this->getdeszipcode(),
            ':vlfreight' => $this->getvlfreight(),
            ':nrdays' => $this->getnrdays()
        ]);
        $this->setData($results[0]);
    }

    public function get(int $idcart)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
            ':idcart' => $idcart
        ]);
        $this->setData($results[0]);
    }

    public function getFromSessionID()
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
            ':dessessionid' => session_id()
        ]);
        if (count($results) > 0) {
            $this->setData($results[0]);
        } else {
        }
    }

    public function addProduct(Products $product)
    {
        $sql = new Sql();
        $sql->query("INSERT INTO tb_cartsproducts (idcart,idproduct) VALUES(:IDCART,:IDPRODUCT)", [
            ":IDCART" => $this->getidcart(),
            ":IDPRODUCT" => $product->getidproduct(),
        ]);
    }

    public function removeProduct(Products $product, $all = false)
    {
        $sql = new Sql();

        if ($all === true) {
            $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :IDCART AND idproduct = :IDPRODUCT AND dtremoved IS NULL", [
                ":IDCART" => $this->getidcart(),
                ":IDPRODUCT" => $product->getidproduct(),
            ]);
        } else {
            $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :IDCART AND idproduct = :IDPRODUCT AND dtremoved IS NULL LIMIT 1", [
                ":IDCART" => $this->getidcart(),
                ":IDPRODUCT" => $product->getidproduct(),
            ]);
        }
    }

    public function getProducts()
    {
        $sql = new Sql();
        return Products::checkList($sql->select("SELECT b.idproduct ,b.desproduct,b.vlprice,b.vlheight,b.vlweight,b.desurl,COUNT(*) AS nrqtd,SUM(b.vlprice) AS vltotal FROM tb_cartsproducts a INNER JOIN tb_products b ON a.idproduct = b.idproduct WHERE
         a.idcart = :IDCART AND a.dtremoved IS NULL
          GROUP BY b.idproduct ,b.desproduct,b.vlprice,b.vlheight,b.vlweight,b.desurl
          ORDER BY b.desproduct", [
            ":IDCART" => $this->getidcart()
        ]));
    }
}
