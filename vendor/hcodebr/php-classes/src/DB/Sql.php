<?php 

namespace Hcode\DB;

use function PHPSTORM_META\type;

class Sql {

	const HOSTNAME = "127.0.0.1:3308";
	const USERNAME = "root";
	const PASSWORD = "";
	const DBNAME = "db_ecommerce";

	private $conn;

	public function __construct()
	{
		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD
		);

	}

	public function getErros(){
		return $this->conn->error;
	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{
		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$resp=$stmt->execute();
		
		if(!$resp){
			var_dump($stmt->errorInfo());
		}
	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);

		$resp=$stmt->execute();
		if($resp){
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}else{
			 var_dump($stmt->errorInfo());
		}
	}
}

 ?>