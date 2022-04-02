<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;


class User extends Model {

    const SESSION = "User";

    public static function login($login, $password)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
        ));

     if(count($results)===0){
         // a contra barra(\) na Exception é porque está buscando a execption no escopo/namespace principal do php. 
         throw new \Exception("Usuário inexistente ou senha inválida.");
     }
     
     $data = $results[0];

     //Aqui essa função verifica se a senha da variável $password criptofada é igual a senha criptografada do banco
     // returna apenas TRUE ou FALSE     
     if(password_verify($password, $data["despassword"]) === true){

         $user = new User();

         $user->setData($data);

         $_SESSION[User::SESSION] = $user->getValues();

        return $user;

     }else{
        throw new \Exception("Usuário inexistente ou senha inválida.");
        }


    }

    public static function verifyLogin($inadmin = true){

        if(
            !isset($_SESSION[User::SESSION]) // Se a sessão no foi definida
            ||
            !$_SESSION[User::SESSION] //Se a sessão for falsa (Sessão não está ativa)
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0 //Se o IdUser não for maior que zero(se está vazio).
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin //Se o usuario que esta logando é do tipo admin 

        ){
            header("Location: /admin/login");
            exit;

        }
    }
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }

     public static function listAll(){        
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson" );
    }

    public function save(){
        $sql = new Sql(); 
           $results = $sql->select("CALL sp_users_save(:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)", array(
            ":desperson"   =>$this->getdesperson(),
            ":deslogin"    =>$this->getdeslogin(),
            ":despassword" =>$this->getdespassword(),
            ":desemail"    =>$this->getdesemail(),
            ":nrphone"     =>$this->getnrphone(),
            ":inadmin"     =>$this->getinadmin(),
        ));

        $this->setData($results[0]);
    }

    public function get($iduser){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson)
        WHERE a.iduser = :iduser",
          array(":iduser"=>$iduser                                 
        ));

        $this->setData($results[0]);
    }

    public function update(){
        $sql = new Sql(); 
        $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)", array(
         ":iduser"      =>$this->getiduser(),
         ":desperson"   =>$this->getdesperson(),
         ":deslogin"    =>$this->getdeslogin(),
         ":despassword" =>$this->getdespassword(),
         ":desemail"    =>$this->getdesemail(),
         ":nrphone"     =>$this->getnrphone(),
         ":inadmin"     =>$this->getinadmin(),
     ));
     $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)", array(":iduser"=>$this->getiduser()
        ));
    }


}