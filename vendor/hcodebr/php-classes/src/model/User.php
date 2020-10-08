<?php
namespace Hcode\model;
use Hcode\Db\Sql;
use Hcode\Model;
class User extends Model{

    const SESSION = "User";

    public static function login($login,$password)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN",array(
            ":LOGIN"=>$login
        ));

        if(count($results) === 0)
        {
            throw new \Exception("Usuarios indexistente ou senha invalida");
        }
        $data = $results[0];

        if(password_verify($password , $data["despassword"])){
            $user = new User();
            $user ->setData($data);
            $_SESSION[User::SESSION] = $user->getValues();

        }else{
            throw new \Exception("Usuarios indexistente ou senha invalida");
        }
    }

    public static function verifyLogin($inadmin = true)
    {
        //se der true não entra
        if(
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"]>0
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin

        ){
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout(){
        $_SESSION[USER::SESSION] = null;
    }
}

?>