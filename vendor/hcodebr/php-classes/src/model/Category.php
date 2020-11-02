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

    public function save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_categories_save(:idcategory,:descategory)", array(
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory(),
        ));
        $this->setData($results[0]);
        Category::updateFile();
    }

    public function get($idcategory)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
            ":idcategory" => $idcategory
        ));
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
            ":idcategory" => $this->getidcategory()
        ));
        Category::updateFile();
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set" . $key}($value);
        }
    }

    public static function updateFile()
    {
        $categories = Category::listAll();
        $html = [];
        foreach ($categories as $row) {
            array_push($html, '<li><a href="/categories/' . $row['idcategory'] . '">' . $row['descategory'] . '</a></li>');
        }
        $file = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'Categories-menu.html';
        file_put_contents($file, implode('', $html));
    }

    public function getProducts($related = true)
    {
        $sql = new Sql();
        
        if ($related === true) {
            return $sql->select("
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			", [
                ':idcategory' => $this->getidcategory()
            ]);
        } else {
            return $sql->select("
            SELECT * FROM tb_products WHERE idproduct NOT IN(
                SELECT a.idproduct
                FROM tb_products a
                INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                WHERE b.idcategory = :idcategory
            );
        ", [
                ':idcategory' => $this->getidcategory()
            ]);
        }
    }

    public function addProduct(Products $product){
        $sql = new Sql();
        $sql->query("INSERT INTO tb_productscategories (idcategory,idproduct) VALUES (:idcategory,:idproduct)",[
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ]);
    }

    
    public function removeProduct(Products $product){
        $sql = new Sql();

        $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",[
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ]);
    }

    public function getProductsPage($page = 1,$itensPerPage = 8){
        $start = ($page -1)*$itensPerPage;

        $sql = new Sql();

        $results = $sql->select('SELECT sql_calc_found_rows * FROM  tb_products a 
        INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
        INNER JOIN tb_categories c ON c.idcategory = b.idcategory
        WHERE c.idcategory = :idcategory
        LIMIT '.$start.','.$itensPerPage.';',[
            ":idcategory"=>$this->getidcategory()
        ]);
        $resultsTotal = $sql->select('SELECT found_rows() AS nrtotal;');
        return [
            "data"=>Products::checkList($results),
            "total"=>$resultsTotal[0]['nrtotal'],
            "pages"=>ceil((int)$resultsTotal[0]['nrtotal']/$itensPerPage) 

        ];
    }

    
	public static function getPage($page = 1, $itemsPerPage = 10)
	{
		$start = ($page - 1) * $itemsPerPage;
		$sql = new Sql();
		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_categories 
            ORDER BY descategory
			LIMIT $start, $itemsPerPage;
		");
		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");
		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10)
	{
		$start = ($page - 1) * $itemsPerPage;
		$sql = new Sql();
		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_categories 
			WHERE descategory LIKE :search
			ORDER BY descategory
			LIMIT $start, $itemsPerPage;
		", [
			':search'=>'%'.$search.'%'
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}
}
