<?php



class UtilisateurDaoImpl implements UtilisateurDao {

  private $connection;

  function __construct() {
    $db = new DbConnect();
    $this->connection = $db->connect();
  }

  /**
  * Creation nouvel utilisateur
  * {@inheritDoc}
  * @see UtilisateurDao::createUser()
  */
  public function createUser($utilisateur) {

    $requeteSQL = "INSERT INTO utilisateur(mailUtilisateur, nomUtilisateur, prenomUtilisateur, mdpUtilisateur, groupe, nomEntreprise, actif)
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $statement = $this->connection->prepare ( $requeteSQL );
    $statement->execute ( array (
    $utilisateur->getEmail (),
    $utilisateur->getNom (),
    $utilisateur->getPrenom (),
    $utilisateur->getMdp (),
    $utilisateur->getGroupe (),
    $utilisateur->getNomEntreprise (),
    $utilisateur->getActif()
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
  }

  /**
  * Creation nouvel utilisateur
  * {@inheritDoc}
  * @see UtilisateurDao::createHeaderFooter()
  */
  public function createHeaderFooter($mailUtilisateur, $entrepriseConsultant) {
    $requeteSQL = "INSERT INTO headerfooter(mailConsultant, header, footer, entrepriseConsultant)
    VALUES (?,null,null,?)";
    $statement = $this->connection->prepare ( $requeteSQL );
    $statement->execute ( array (
    $mailUtilisateur,
    $entrepriseConsultant
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
  }

  /**
  * Récupérer info utilisateur par son email
  * @param unknown $data
  * @return unknown
  */
  public function getUserByMail($data) {
    $statement = $this->connection->prepare ( "SELECT * FROM utilisateur WHERE mailUtilisateur = ?" );
    $statement->execute ( array (
    $data ['email']
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
    $user = $statement->fetchAll ();
    return $user;
  }

  /**
  * Vérifier l'existance de l'utilisateur
  * {@inheritDoc}
  * @see UtilisateurDao::existeUtilisateur()
  */
  public function existeUtilisateur($email) {
    $statement = $this->connection->prepare ( "SELECT nomUtilisateur FROM utilisateur WHERE mailUtilisateur = ?" );
    $statement->execute ( array (
    $email
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
    if ($statement->rowCount () == 0) {
      return false;
    }
    return true;
  }

  /**
  *
  * {@inheritDoc}
  * @see UtilisateurDao::existeConsultant()
  */
  public function existeConsultant($email) {
    $statement = $this->connection->prepare ( "SELECT nomUtilisateur FROM utilisateur WHERE mailUtilisateur = ? and groupe ='Consultant'" );
    $statement->execute ( array (
    $email
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
    if ($statement->rowCount () == 0) {
      return false;
    }
    return true;
  }

  /**
  *
  * {@inheritDoc}
  * @see UtilisateurDao::existeCodeEntreprise()
  */
  public function existeCodeEntreprise($codeEntreprise) {
    $statement = $this->connection->prepare ( "SELECT nomEntreprise FROM entreprise WHERE code = ?" );
    $statement->execute ( array (
    $codeEntreprise
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
    if ($statement->rowCount () == 0) {
      return false;
    }
    return true;
  }

  /**
  *
  * {@inheritDoc}
  * @see UtilisateurDao::recupNomEntreprise()
  */
  public function recupNomEntreprise($codeEntreprise) {
    $statement = $this->connection->prepare ( "SELECT nomEntreprise FROM entreprise WHERE code = ?" );
    $statement->execute ( array (
    $codeEntreprise
    ) ) or die ( print_r ( $statement->errorInfo (), true ) );
    $nomEntreprise = $statement->fetch ();
    return $nomEntreprise ['0'];
  }

  /**
  *
  * {@inheritDoc}
  * @see UtilisateurDao::setMdpTemp()
  */
  public function setMdpTemp($mdp_ren, $mail) {
    $sql = "UPDATE utilisateur SET mdpUtilisateur='$mdp_ren' WHERE mailUtilisateur='$mail'";
    $statement = $this->connection->exec ( $sql );
  }

  /**
  * Récupérer les informations des consultants
  * {@inheritDoc}
  * @see UtilisateurDao::getInfoConsultant()
  */
  public function getInfoConsultant(){
    $statement = $this->connection->prepare ( "SELECT prenomUtilisateur, header, footer, mailUtilisateur, nomUtilisateur, actif, entrepriseConsultant
      FROM utilisateur u, headerfooter h
      WHERE u.groupe = 'Consultant' and u.mailUtilisateur = h.mailConsultant;" );
      $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
      $infoConsultant = $statement->fetchAll();
      return $infoConsultant;
    }

    /**
  * Récupérer les informations des consultants
  * {@inheritDoc}
  * @see UtilisateurDao::getInfoConsultantActif()
  */
  public function getInfoConsultantActif(){
    $statement = $this->connection->prepare ( "SELECT prenomUtilisateur, header, footer, mailUtilisateur, nomUtilisateur, actif, entrepriseConsultant
      FROM utilisateur u, headerfooter h
      WHERE u.groupe = 'Consultant' and u.actif = 1 and u.mailUtilisateur = h.mailConsultant;" );
      $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
      $infoConsultant = $statement->fetchAll();
      return $infoConsultant;
    }

    /**
    *
    * @param unknown $header
    * @param unknown $footer
    * @param unknown $mail
    */
    public function setHeaderFooter($header, $footer, $mail) {
      if (! empty($header)){
        $statement = $this->connection->prepare ( "UPDATE headerfooter SET header = ? , footer = ?  WHERE mailConsultant = ?" );
        $statement->execute ( array (
        $header,
        $footer,
        $mail
        ) ) or die ( print_r ( $statement->errorInfo (), true ) );
      } 
      else if (empty($header)){
        $statement = $this->connection->prepare ( "UPDATE headerfooter SET footer = ? WHERE mailConsultant = ?" );
        $statement->execute ( array (
        $footer,
        $mail
        ) ) or die ( print_r ( $statement->errorInfo (), true ) );
      }
    }

    /**
    *
    * {@inheritDoc}
    * @see UtilisateurDao::getHeaderFooter()
    */
    public function getHeaderFooter( $mail ) {

      $statement = $this->connection->prepare ( "SELECT header, footer FROM headerfooter WHERE mailConsultant = '$mail'" );
      $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
      $hederFooter = $statement->fetchAll();
      return $hederFooter;
    }

    /**
    *
    * @param unknown $email
    * @param unknown $data
    * @return unknown
    */
    public function updatePwd($email, $data) {
      $sql = "UPDATE utilisateur SET mdpUtilisateur = :newPdw WHERE mailUtilisateur = :mail";
      $statement = $this->connection->prepare ( $sql );
      $result = $statement->execute ( array (
      "newPdw" => $data ['newPwd'],
      "mail" => $email
      ) );
      return $result;
    }
    /**
    *
    * @param unknown $data
    * @param unknown $session
    * @return unknown
    */
    public function updateEmailForUser($data, $session) {
      $sql = "UPDATE utilisateur SET mailUtilisateur = :newMail WHERE mailUtilisateur = :oldMail";
      $statement = $this->connection->prepare ( $sql );
      $result = $statement->execute ( array (
      "newMail" => $data['mailUtilisateur'],
      "oldMail" => $session['email']
      ) );
      return $result;
    }

    /**
    *
    * @param unknown $email
    * @return unknown
    */
    public function getUserName($email) {
      $sql = "SELECT nomUtilisateur, prenomUtilisateur FROM utilisateur WHERE mailUtilisateur = :mail";
      $statement = $this->connection->prepare ( $sql );
      $statement->execute ( array (
      "mail" => $email
      ) );
      $names = $statement->fetchAll ();
      return $names;
    }

    /**
    *
    * @param unknown $email
    * @param unknown $data
    * @return unknown
    */
    public function updateUserName($email, $data) {
      $sql = "UPDATE utilisateur SET nomUtilisateur = :nom, prenomUtilisateur = :prenom WHERE mailUtilisateur = :mail";
      $statement = $this->connection->prepare ( $sql );
      $result = $statement->execute ( array (
      "nom" => $data ["nom"],
      "prenom" => $data ["prenom"],
      "mail" => $email
      ) );
      return $result;
    }

    /**
    *
    * @param unknown $data
    * @return unknown
    */
    public function updateConsultantActive($data) {
      $sql = "UPDATE utilisateur SET actif = :actif WHERE mailUtilisateur = :mail";
      $statement = $this->connection->prepare ( $sql );
      $result = $statement->execute ( array (
      "actif" => $data ["actif"],
      "mail" => $data ["mailUtilisateur"]
      ) );
      return $result;
    }
    /**
    *
    * @param type $mail
    * @return type
    */
    public function getConsultantUser($mail){
      $sql = "SELECT e.mailConsultant FROM entreprise e, utilisateur u  WHERE u.mailUtilisateur = :mail AND u.nomEntreprise = e.nomEntreprise";
      $statement = $this->connection->prepare ( $sql );
      $statement->execute ( array (
      "mail" => $mail
      ) );
      $names = $statement->fetchAll ();
      return  $names[0]['mailConsultant'];

    }


  }
