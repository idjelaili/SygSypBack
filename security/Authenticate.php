<?php
// require("vendor/autoload.php");
use \Firebase\JWT\JWT;

class Authenticate {

  private $token;
  private $decoded;
  private $key;
  function __construct() {

  }

  public function generateToken($mail, $nom, $prenom, $company, $groupe) {
    $this->key = $this->readKeyFile();
    $now = time();
    $expireIn = $now + 7200;
    $token_array = array(
      "iat" => $now,
      "exp" => $expireIn,
      "email" => $mail,
      "nom" => $nom,
      "prenom" => $prenom,
      "company" => $company,
      "groupe" => $groupe
    );
    $jwt = JWT::encode($token_array, $this->key);
    return $jwt;
  }
  public function readKeyFile(){
    $fp = fopen ("security/hashPass.txt", "r");
    $contenu_du_fichier = fgets ($fp, 255);
    fclose ($fp);
    return $contenu_du_fichier;
  }
  public function verifyToken(){
    $this->key = $this->readKeyFile();
    try{
      $this->decoded = JWT::decode($this->token ,$this->key, array('HS256'));
    }catch(Firebase\JWT\SignatureInvalidException $e){
      $this->decoded = array();
    }catch(\Exception $e){
      $this->decoded = array();
    }
    $this->decoded = (array)$this->decoded;
    $expired = $this->isTokenExpired($this->decoded);
    if($expired == 1) {
      return 'timeout';
    }
    return $this->decoded;

  }

  public function isTokenExpired($decodToken){
    if(!empty($decodToken)){
      $now = time();
      $timeLeft = $decodToken['exp'] - $now;
      if($timeLeft < 0){
        return 1;
      }
    }

    return 0;

  }

  public function refreshToken($token_url){
    $this->token = $token_url;
    $decodToken = $this->verifyToken();
    if(empty($decodToken) || $decodToken == 'timeout'){
      return 'timeout';
    }

    $now = time();
    $expireIn = $now + 3600;
    $decodToken['iat'] = $now;
    $decodToken['exp'] = $expireIn;
    $jwt = JWT::encode($decodToken, $this->key);

    return $jwt;
  }
  public function setToken($token_url){
    $this->token = $token_url;
  }

}


?>
