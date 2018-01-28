<?php

namespace Apsys\Model;

use \Apsys\DB\Sql;
use \Apsys\Model;
use \Apsys\Mailer;

class User extends Model {
  const SESSION = "User";
  const SECRET =  "CriptoCriptCript";
  //const SECRET =  substr(hash('sha256', "CriptoCriptCript"),0, 16);//
  //$iv = substr(hash('sha256', $secret_iv), 0, 16);


  public static function login($login, $password)
  {

    $sql = new Sql();
    $res = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
      ":LOGIN"=>$login
    ));

    if(count($res)===0){
      throw new \Exception("Usuário inexistente ou senha inválida");
    }

    $data=$res[0];

    if(password_verify($password, $data['despassword'])===true)
    {
        $user = new User();

        $user->setData($data);
        $_SESSION[User::SESSION] = $user->getValues();
          //var_dump($user);
          return $user;
    }
    else{
      throw new \Exception("Usuário inexistente ou senha inválida", 1);
    }

  }

  public static function VerifyLogin($inadmin = true)
  {
    if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !(int)$_SESSION[User::SESSION]['iduser'] > 0  || (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin)
    {
        //print_r($_SESSION[User::SESSION]);
        header('Location: /admin/login');
        exit;
    }
  }

  public static function logout(){
    $_SESSION[User::SESSION] = NULL;
  }

  public static function listAll(){
    $sql = new Sql();
    return $sql->select("SELECT * FROM tb_users as a INNER JOIN tb_persons as b USING(idperson) ORDER BY b.desperson");

  }

  public function save()
  {

    $sql = new Sql();

    $res = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
    array(
      ":desperson"=>$this->getdesperson(),
      ":deslogin"=>$this->getdeslogin(),
      ":despassword"=>$this->getdespassword(),
      ":desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin()
    ));

    $this->setData($res[0]);

  }

  public function get($iduser)
  {
    $sql = new Sql();

    $res = $sql-> select("SELECT * FROM tb_users as a INNER JOIN tb_persons as b USING(idperson) WHERE a.iduser=:iduser",
    array(":iduser"=>$iduser));
    $this->setData($res[0]);

  }

  public function update()
  {

        $sql = new Sql();

        $res = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
        array(
          ":iduser"=>$this->getiduser(),
          ":desperson"=>$this->getdesperson(),
          ":deslogin"=>$this->getdeslogin(),
          ":despassword"=>$this->getdespassword(),
          ":desemail"=>$this->getdesemail(),
          ":nrphone"=>$this->getnrphone(),
          ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($res[0]);

  }

  public function delete()
  {

        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)",
        array(
          ":iduser"=>$this->getiduser()
        ));

  }

  public static function forgot($email)
  {
    $sql = new Sql();
    $res=$sql->select(
      "SELECT * FROM tb_persons as a INNER JOIN tb_users as b USING(idperson) WHERE a.desemail =  :email",
      array(
        ":email"=> $email
      )
    );

    if(count($res)===0){

      throw new \Exception("Não foi possível recuperar a senha.", 1);
    }
    else{
      $data=$res[0];
      $res2 = $sql->select("CALL sp_userspasswordsrecoveries_create (:iduser, :desip)",
      array(
        ":iduser"=>$data['iduser'],
        ":desip"=>$_SERVER['REMOTE_ADDR']
      ));

      if(count($res2)===0){
        throw new \Exception("Não foi possível recuperar a senha.", 1);
      }
      else{

        $dataRecovery =$res2[0];
        //$code = base64_encode(@mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery['idrecovery'], MCRYPT_MODE_ECB)); //mcrypt deprecated
        $code = base64_encode(openssl_encrypt($dataRecovery['idrecovery'], "AES-256-CBC", User::SECRET, 0, User::SECRET));//com ssl
        $link = "http://comercio.com.br/admin/forgot/reset?code=$code";

        $mailer = new Mailer($data['desemail'], $data['desemail'], "Redefinir Senha", "forgot",
        array(
          "name"=>$data['desperson'],
          "link"=>$link
        ));

        $mailer->send();

        return $data;

      }
    }
  }

    public static function validForgotDecrypt($code)
    {
      //$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, mcrypt_decode(), MCRYPT_MODE_ECB); //deprecated
      $idrecovery = openssl_decrypt(base64_decode($code), "AES-256-CBC", User::SECRET, 0, User::SECRET);

      $sql = new Sql();

      $res = $sql->select("
                          SELECT * FROM tb_userspasswordsrecoveries as a
                          INNER JOIN tb_users as b USING(iduser)
                          INNER JOIN tb_persons as c USING(idperson)
                          WHERE a.idrecovery = :idrecovery AND a.dtrecovery IS NULL
                          AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()",
                          array(
                            ":idrecovery"=>$idrecovery
                          ));
      if(count($res)===0){
        throw new \Exception("É... - Não foi possível recuperar a senha", 1);
      }
      else{
        //var_dump($res[0]);
        return $res[0];
      }
    }

    public static function setForgotUsed($idrecovery){
      $sql = new Sql();
      $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
        "idrecovery"=> $idrecovery
      ));
    }


    public function setPassword($password){
      $sql = new Sql();

      $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
        ":password"=> $password,
        ":iduser"=>$this->getiduser()
      ));

    }
}

?>
