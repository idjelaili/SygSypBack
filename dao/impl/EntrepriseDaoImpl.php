<?php

require_once('dao/DBconnect.php');
require_once('dao/EntrepriseDao.php');

class EntrepriseDaoImpl implements EntrepriseDao {

    private $connection;

    function __construct() {
        $db = new DbConnect();
        $this->connection = $db->connect();
    }

    /**
     * Créer une entreprise
     * @param type $code
     * @param type $nom
     * @param type $mailConsultant
     */
     public function createEntreprise( $code_gener, $nomEntreprise, $mailConsultant, $adresse, $adresseComplement, $codePostal, $ville, $telephone ) {
		$requeteSQL = "INSERT INTO entreprise ( nomEntreprise, code, mailConsultant, adresse1, adresse2, codePostal, ville, telephone)
         			   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$statement = $this->connection->prepare ( $requeteSQL );
		$statement->execute ( array (
				$nomEntreprise,
				$code_gener,
				$mailConsultant,
				$adresse,
				$adresseComplement,
				$codePostal,
				$ville,
				$telephone
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
	}

    /**
     *
     * {@inheritDoc}
     * @see EntrepriseDao::existeEntreprise()
     */
    public function existeEntreprise($nomEntreprise) {
        $statement = $this->connection->prepare("SELECT nomEntreprise FROM entreprise WHERE nomEntreprise = ?");
        $statement->execute(array($nomEntreprise)) or die(print_r($statement->errorInfo(), true));
        if ($statement->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see EntrepriseDao::setConsultantEntreprise()
     */
    public function setConsultantEntreprise($mailConsultant, $nomEntreprise) {
		$statement = $this->connection->prepare ( "UPDATE entreprise SET mailConsultant = ? WHERE nomEntreprise = ?" );
		$statement->execute ( array (
				$mailConsultant,
				$nomEntreprise
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
	}

    /**
     *
     * {@inheritDoc}
     * @see EntrepriseDao::getEntreprises()
     */
    public function getEntreprises($mailConsultant) {
        $retur_message = array();
        if (empty($mailConsultant)) {
            $retur_message['status'] = 'error';
            $retur_message['message'] = "Le mail de consultant est vide";
            return $retur_message;
        } else {
            $statement = $this->connection->prepare("SELECT nomEntreprise
        									     FROM entreprise
        										 WHERE nomEntreprise != 'sygsyp'
        										 AND (mailConsultant != ?
        										 OR mailConsultant is NULL) Order by nomEntreprise");
            $statement->execute(array($mailConsultant)) or die(print_r($statement->errorInfo(), true));
            $entreprises = $statement->fetchAll();
            return $entreprises;
        }
    }

    /**
     *
     * @param type $mailConsultant
     */
    public function getAllEntrepriseAttribue($mailConsultant) {
        $retur_message = array();
        if (empty($mailConsultant)) {
            $retur_message['status'] = 'error';
            $retur_message['message'] = "Le mail de consultant est vide";
            return $retur_message;
        } else {
            $statement = $this->connection->prepare("SELECT DISTINCT e.nomEntreprise, e.adresse1, e.adresse2, e.code, e.ville, e.codePostal, e.telephone, e.idQuestionnaire,"
                    . " u.prenomUtilisateur, u.nomUtilisateur, u.mailUtilisateur FROM entreprise e, utilisateur u WHERE"
                    . " e.mailConsultant = '$mailConsultant' AND u.nomEntreprise = e.nomEntreprise AND u.groupe = 'Dirigeant' ");

            $statement->execute() or die(print_r($statement->errorInfo(), true));
            $entreprises = $statement->fetchAll();
            return $entreprises;
        }
    }

    /**
     *
     * @param unknown $company
     * @return unknown
     */
  	public function getCompanyInfo($company) {
		$sql = "SELECT * FROM entreprise WHERE nomEntreprise = :company";
		$statement = $this->connection->prepare ( $sql );
		$statement->execute ( array (
				"company" => $company
		) );
		$data = $statement->fetchAll ();
		return $data;
	}

	/**
	 *
	 * @param unknown $company
	 * @param unknown $data
	 * @return unknown
	 */
	public function updateCompanyInfo($company, $data) {
    $sqlUpdateCompany = "UPDATE entreprise SET nomEntreprise = :companyName WHERE nomEntreprise = :company";
    $statement = $this->connection->prepare ( $sqlUpdateCompany );
    $result = $statement->execute ( array ("companyName" => $data["company"], "company" =>$company));

		$sql = "UPDATE entreprise SET adresse1 = :address, adresse2 = :addressComp,
    			codePostal = :postalCode, ville = :city, telephone = :phoneNumber WHERE nomEntreprise = :company";
		$statement = $this->connection->prepare ( $sql );
		$result = $statement->execute ( array (
				"address" => $data ["address"],
				"addressComp" => $data ["addressComp"],
				"postalCode" => $data ["postalCode"],
				"city" => $data ["city"],
				"phoneNumber" => $data ["phoneNumber"],
				"company" => $data ["company"]
		) );
		return $result;
	}


}
