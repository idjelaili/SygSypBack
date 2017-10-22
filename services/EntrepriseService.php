<?php

require_once('dao/DBconnect.php');
require_once('models/Entreprise.php');
require_once('dao/impl/EntrepriseDaoImpl.php');
require_once('dao/impl/UtilisateurDaoImpl.php');
require_once('utils/FonctionUtils.php');

class EntrepriseService {

	private $entrepriseDao;

	function __construct() {
		$this->entrepriseDao = new EntrepriseDaoImpl();
	}

	/**
	 * Créer une entreprise et envoie de mail
	 *
	 * @param type $mailDiregeant
	 * @param type $nomEntreprise
	 * @param type $mailConsultant
	 * @return string
	 */
	public function createEntreprise($prenomUtilisateur, $mailDiregeant, $nomEntreprise, $mailConsultant, $adresse, $adresseComplement, $codePostal, $ville, $telephone) {

		$return_message = array ();

		if (filter_var ( $mailConsultant, FILTER_VALIDATE_EMAIL ) || empty ( $mailConsultant )) {

			// Géniration du code
			$code_gener = uniqid ( rand ( 1234, 9876 ) );

			// Message du email.
			$message = "Bonjour $prenomUtilisateur,"."\n\n";
			$message .= "*** Ne pas répondre à cet email envoyé automatiquement ***"."\n\n";
			$message .= "Nous avons enregistré votre Entreprise : $nomEntreprise."."\n";
			$message .= "Merci de communiquer le code $code_gener à vos collaborateurs pour qu'ils puissent s'inscrire.";
			
			// evoie mail
			$sujet_mail = "Nouveau compte entreprise";
			$return_message = FonctionUtils::envoie_mail ( $mailDiregeant, $sujet_mail, $message );
				
			if($return_message ['status'] != 'error'){
				$this->entrepriseDao->createEntreprise ( $code_gener, $nomEntreprise, $mailConsultant, $adresse, $adresseComplement, $codePostal, $ville, $telephone );
			}
		} else {
			$return_message ['status'] = 'error';
			$return_message['message'] = 2 ;
		}
		return $return_message;
	}

	/**
	 * affecter un consultant a une entreprise
	 * @param type $mailConsultant
	 * @param type $nomEntreprise
	 * @return string
	 */
	public function setConsultantEntreprise($mailConsultant, $nomEntreprise) {

		$return_message = array();
		if (!empty($mailConsultant) && !empty($nomEntreprise)) {

			$utilisateurDao = new UtilisateurDaoImpl();
			$entrepriseDao = new EntrepriseDaoImpl();
			if ($utilisateurDao->existeConsultant($mailConsultant)) {

				$utilisateurDao = new UtilisateurDaoImpl();
				if ($utilisateurDao->existeConsultant($mailConsultant)) {
					if ($this->entrepriseDao->existeEntreprise($nomEntreprise)) {
						$this->entrepriseDao->setConsultantEntreprise($mailConsultant, $nomEntreprise);
						$retur_message['status'] = 'success';
						$retur_message['message'] = 17;
					} else {
						$retur_message['status'] = 'error';
						$retur_message['message'] = 19;
					}
				} else {
					$retur_message['status'] = 'error';
					$retur_message['message'] = 7;
				}
			} else {

				$retur_message['status'] = 'error';
				$retur_message['message'] = 4;
			}
			// var_dump($retur_message);
			$retur_message['message'] = FonctionUtils::affiche_erreur($retur_message['message']);

			return $retur_message;
		}
	}

	/**
	 *
	 * @param unknown $idTest
	 * @param unknown $nomEntreprise
	 */
	public function getCsv($idTest, $nomEntreprise) {
		$csvResult = new ResultDaoImpl ();

		$result = $csvResult->getQuestionsReponses ( $idTest );

		$csvEntete = $csvResult->getEnteteCsv ( $nomEntreprise, $idTest );

		$entete = array ();
		$entete [0] = array (
				'a' => 'Nom Entrepreneur',
				'b' => $csvEntete [0] ['nomUtilisateur'] . ' ' . $csvEntete [0] ['prenomUtilisateur']
		);
		$entete [1] = array (
				'a' => 'Nom Société',
				'b' => $csvEntete [0] ['nomEntreprise']
		);
		$entete [3] = array (
				'a' => 'Date du test',
				'b' => $csvEntete [0] ['dateTest']
		);
		$entete [3] = array (
				'a' => 'Référence du test',
				'b' => $csvEntete [0] ['idTest']
		);
		$entete [4] = array (
				'a' => '',
				'b' => ''
		);
		$entete [5] = array (
				'a' => 'Questionnaire',
				'b' => 'Note de 0 à 10*'
		);

		foreach ( $result as $key => $value ) {
			foreach ( $value as $key2 => $value2 ) {

				if ($key2 == '0' || $key2 == '1') {

					unset ( $result [$key] [$key2] );
				}
			}
		}

		$footer = array ();
		$footer [0] = array (
				'a' => '',
				'b' => ''
		);
		$footer [1] = array (
				'a' => '* : La note 0 correspond à non, très mauvais, très insatisfaisant, pas du tout… La note 10 correspond à oui, très bien, très insatisfaisant, tout à fait...',
				'b' => ''
		);
		$return = array_merge ( $entete, $result, $footer );
	}

	/**
	 *
	 * @param unknown $session
	 * @return string[]|string[]|unknown[]
	 */
	public function getCompanyInfo($session) {
		$result = array ();
		if (empty ( $session )) {
			$result ['status'] = 'error';
			$result ['message'] = 'internal error';
			return $result;
		}
		$userDAO = new EntrepriseDaoImpl ();
		$companyInfo = $userDAO->getCompanyInfo ( $session ['company'] );
		$result ['status'] = 'success';
		$result ['message'] = $companyInfo [0];
		return $result;
	}

	/**
	 *
	 * @param unknown $session
	 * @param unknown $data
	 * @return string[]
	 */
	public function updateCompanyInfo($session, $data) {
		$result = array ();
		if (empty ( $session )) {
			$result ['status'] = 'error';
			$result ['message'] = 'internal error';
			return $result;
		}
		if (empty ( $data ['company'] )) {
			$result ['status'] = 'error';
			$result ['message'] = 'company name has to be not null';
			return $result;
		}
		$companyDAO = new EntrepriseDaoImpl ();
		$company = $companyDAO->updateCompanyInfo ( $session ['company'], $data );

		$auth = new Authenticate ();
		$token = $auth->generateToken ( $session ['email'], $session ['nom'], $session ['prenom'], $data ['company'], $session ['groupe'] );
		$result ['status'] = 'success';
		$result ['message'] = $token;
		return $result;
	}

}
