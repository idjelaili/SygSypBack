<?php
define ( "MESSAGE_ERREUR", "" );

class UtilisateurService {

	private $userDao;

	function __construct() {
		$this->userDao = new UtilisateurDaoImpl ();
	}

	/**
	 * controle des champs formulaire utilisateur
	 *
	 * @param unknown $infoUtilisateur
	 * @return string|number
	 */
	public function controleDonneeUtilisateur($infoUtilisateur) {

		$mailUtilisateur = FonctionUtils::escape_errors ( $infoUtilisateur ['mailUtilisateur'] );
		$nomUtilisateur = addslashes ( $infoUtilisateur ['nomUtilisateur'] );
		$prenomUtilisateur = addslashes ( $infoUtilisateur ['prenomUtilisateur'] );
		$mdpUtilisateur = $infoUtilisateur ['mdpUtilisateur'];
		$confirmationDePasse = $infoUtilisateur ['confirmationDePasse'];

		if (! empty ( $mailUtilisateur ) and ! empty ( $nomUtilisateur ) and ! empty ( $prenomUtilisateur ) and ! empty ( $mdpUtilisateur ) and ! empty ( $confirmationDePasse )) {
			if (strlen ( $nomUtilisateur ) <= 45) {
				if (strlen ( $prenomUtilisateur ) <= 45) {
					if (filter_var ( $mailUtilisateur, FILTER_VALIDATE_EMAIL )) {
						if ($mdpUtilisateur == $confirmationDePasse and strlen ( $mdpUtilisateur ) >= 4) {
							return $erreur = MESSAGE_ERREUR;
						} else if ($mdpUtilisateur != $confirmationDePasse) {
							$erreur = 11;
						} else {
							$erreur = 12;
						}
					} else {
						$erreur = 2;
					}
				} else {
					$erreur = 8;
				}
			} else {
				$erreur = 9;
			}
		} else {
			$erreur = 4;
		}
		return $erreur;
	}

	/**
	 * Choix du mail consultant
	 *
	 * @param unknown $nomConsultant
	 * @return string
	 */
	public function choixMailConsultant($nomConsultant) {
		if ($nomConsultant == 'Roland') {
			return "roland.pesty@pollen-innovation.fr";
		} else if ($nomConsultant == 'Michel') {
			return "michel.cezon@cogiteo.net";
		}
		return "";
	}

	/**
	 *
	 * @param unknown $data
	 * @return string|string[]
	 */
	public function getConnectionForUser($data) {
		if (empty ( $data )) {
			$errMsg = 'Connection failed. Data empty';
			$error = 'error';
			return array (
					"status" => $error,
					"message" => $errMsg
			);
		}
		$result = $this->userDao->getUserByMail ( $data );
		if (empty ( $result )) {
			$errMsg = 'User not found.';
			$error = 'error';
			return array (
					"status" => $error,
					"message" => $errMsg
			);
		}
		if ($result [0] ['actif'] == 0) {
			$errMsg = 'This user is not activate.';
			$error = 'error';
			return array (
					"status" => $error,
					"message" => $errMsg
			);
		}
		if (($this->comparePassword ( $data ['pwd'], $result [0] ['mdpUtilisateur'] ) == 0)) {
			$errMsg = 'Wrong password.';
			$error = 'error';
			return array (
					"status" => $error,
					"message" => $errMsg
			);
		}
		$token = $this->generateToken ( $result );
		if (empty ( $token )) {
			$errMsg = '[TOKEN] Internal error';
			$error = 'error';
			return array (
					"status" => $error,
					"message" => $errMsg
			);
		}
		$tokenArray = array (
				'token' => $token,
				'role' => $result [0] ['groupe']
		);
		return $tokenArray;
	}

	/**
	 *
	 * @param unknown $session
	 * @return string[]|string[]|unknown[]
	 */
	public function getUserName($session) {
		$result = array ();
		if (empty ( $session )) {
			$result ['status'] = 'error';
			$result ['message'] = 'internal error';
			return $result;
		}
		$userDAO = new UtilisateurDaoImpl ();
		$userNames = $userDAO->getUserName ( $session ['email'] );
		$result ['status'] = 'success';
		$result ['message'] = $userNames [0];
		return $result;
	}

	/**
	 *
	 * @param unknown $session
	 * @param unknown $data
	 * @return string[]
	 */
	public function updateUserName($session, $data) {
		$result = array ();
		if (empty ( $session )) {
			$result ['status'] = 'error';
			$result ['message'] = 'internal error';
			return $result;
		}
		if (empty ( $data )) {
			$result ['status'] = 'error';
			$result ['message'] = 'Missing data';
			return $result;
		}
		$userDAO = new UtilisateurDaoImpl ();
		$userNames = $userDAO->updateUserName ( $session ['email'], $data );
		$result ['status'] = 'success';
		$result ['message'] = 'updated';
		return $result;
	}

	/**
	 *
	 * @param unknown $data
	 * @param unknown $session
	 * @return string[]
	 */
	public function updateUserPassword($data, $session) {
		$resultat = array ();
		if (empty ( $session )) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'internal server error, call the admin';
			return $resultat;
		}
		if (empty ( $data )) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'Aucune données envoyés';
			return $resultat;
		}
		if ($this->checkUserUpdateData ( $data ) == 0) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'Données manquantes';
			return $resultat;
		}
		$userDAO = new UtilisateurDaoImpl ();
		$userDB = $userDAO->getUserByMail ( $session );
		if (empty ( $userDB [0] )) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'User not found.';
			return $resultat;
		}
		if ($this->comparePassword ( $data ['oldPwd'], $userDB [0] ['mdpUtilisateur'] ) == 0) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'Ancien mot de passe incorrect.';
			return $resultat;
		}
		if ($this->comparePassword ( $data ['newPwd'], hash ( "sha256", $data ['pwdConfirm'] ) ) == 0) {
			$resultat ['status'] = 'error';
			$resultat ['message'] = 'Les nouveaux mots de passes sont différents.';
			return $resultat;
		}
		$data ['newPwd'] = hash ( "sha256", $data ['newPwd'] );
		$userResult = $userDAO->updatePwd ( $session ['email'], $data );
		$resultat ['status'] = 'success';
		$resultat ['message'] = 'Password updated.';
		return $resultat;
}

public function updateConsultantActive( $data){
	$result = array();
	if(empty($data)){
		$result['status'] = 'error';
		$result['message'] = 'Missing data';
		return $result;
	}
	$userDAO = new UtilisateurDaoImpl();
	$userNames = $userDAO->updateConsultantActive($data);
	$result['status'] = 'success';
	$result['message'] = 'updated';
	return $result;

}
public function updateUserMail($data, $session){
	$result = array();
	if(empty($data)){
		$result['status'] = 'error';
		$result['message'] = 'Missing data';
		return $result;
	}
	$userDAO = new UtilisateurDaoImpl();
	$userNames = $userDAO->updateEmailForUser($data, $session);
	$auth = new Authenticate ();
	$token = $auth->generateToken ( $data ['mailUtilisateur'], $session ['nom'], $session ['prenom'], $session ['company'], $session ['groupe'] );
	$result ['status'] = 'success';
	$result ['message'] = $token;
	return $result;

}


	/**
	 *
	 * @param unknown $inputData
	 * @return number
	 */
	public function checkUserUpdateData($inputData) {
		if (! isset ( $inputData ['oldPwd'] )) {
			return 0;
		}
		if (isset ( $inputData ['oldPwd'] ) && empty ( $inputData ['oldPwd'] )) {

			return 0;
		}
		if (! isset ( $inputData ['newPwd'] )) {

			return 0;
		}
		if (isset ( $inputData ['newPwd'] ) && empty ( $inputData ['newPwd'] )) {

			return 0;
		}
		if (! isset ( $inputData ['pwdConfirm'] )) {

			return 0;
		}
		if (isset ( $inputData ['pwdConfirm'] ) && empty ( $inputData ['pwdConfirm'] )) {

			return 0;
		}
		return 1;
	}

	/**
	 *
	 * @param unknown $dataBD
	 * @return string
	 *
	 */
	public function generateToken($dataBD) {
		$auth = new Authenticate ();
		$token = $auth->generateToken ( $dataBD [0] ['mailUtilisateur'], $dataBD [0] ['nomUtilisateur'], $dataBD [0] ['prenomUtilisateur'], $dataBD [0] ['nomEntreprise'], $dataBD [0] ['groupe'] );
		return $token;
	}

	/**
	 *
	 * @param unknown $inputPwd
	 * @param unknown $bdPwd
	 * @return number
	 *
	 */
	public function comparePassword($inputPwd, $bdPwd) {
		$inputPwdHash = hash ( "sha256", $inputPwd );
		if ($inputPwdHash !== $bdPwd) {
			return 0;
		}
		return 1;
	}

	/**
	 * Creer une entreprise par un consultant
	 *
	 * @param unknown $infoUtilisateur
	 * @param unknown $mailConsultant
	 * @return string[]|number[]
	 */
	public function creerCompteDirigeant($infoUtilisateur, $mailConsultant) {

		$retur_message = array ();

		$messageErreur = $this->controleDonneeUtilisateur ( $infoUtilisateur );

		if (! empty ( $messageErreur )) {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = $messageErreur;
		} else {

			$telephone = $infoUtilisateur ['telephone'];
			$codePostal = $infoUtilisateur ['codePostal'];

			if (FonctionUtils::regex_num_tel ( $telephone ) == 0) {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = FonctionUtils::affiche_erreur ( 15 );
				return $retur_message;
			} else if (FonctionUtils::regex_code_postal ( $codePostal ) == 0) {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = FonctionUtils::affiche_erreur ( 20 );
				return $retur_message;
			}

			$mailConsultant = addslashes ( $mailConsultant );
			if ( ! $this->userDao->existeConsultant ( $mailConsultant ) ) {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = FonctionUtils::affiche_erreur ( 32 );
				return $retur_message;
			}

			$mailUtilisateur = FonctionUtils::escape_errors ( $infoUtilisateur ['mailUtilisateur'] );
			$nomUtilisateur = addslashes ( $infoUtilisateur ['nomUtilisateur'] );
			$prenomUtilisateur = addslashes ( $infoUtilisateur ['prenomUtilisateur'] );
			$mdpUtilisateur = hash ( "sha256", $infoUtilisateur ['mdpUtilisateur'] );
			$nomEntreprise = addslashes ( $infoUtilisateur ['nomEntreprise'] );
			$adresse = FonctionUtils::escape_errors ( $infoUtilisateur ['adresse1'] );
			$adresseComplement = addslashes ( $infoUtilisateur ['adresse2'] );
			$ville = addslashes ( $infoUtilisateur ['ville'] );

			if (! $this->userDao->existeUtilisateur ( $mailUtilisateur )) {
				$entreprise = new EntrepriseService ();
				$return = $entreprise->createEntreprise ( $prenomUtilisateur, $mailUtilisateur, $nomEntreprise, $mailConsultant, $adresse, $adresseComplement, $codePostal, $ville, $telephone );
				$utilisateur = new Utilisateur ();
				$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, "Dirigeant", $nomEntreprise );
				$this->userDao->createUser ( $utilisateur );
				$retur_message ['status'] = 'success';
				$retur_message ['message'] = 28;
			} else {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = 6;
			}
		}
		$retur_message ['message'] = FonctionUtils::affiche_erreur ( $retur_message ['message'] );
		return $retur_message;
	}

	/**
	 * Choix du groupe utilisateur
	 *
	 * @param unknown $nomEntreprise
	 * @param unknown $codeEntreprise
	 * @return string
	 *
	 */
	public function choixGroupe($nomEntreprise, $codeEntreprise) {
		if (! empty ( $nomEntreprise ) && empty ( $codeEntreprise )) {
			return "Dirigeant";
		} else if ($codeEntreprise == "20170101125359789" && empty ( $nomEntreprise )) {
			return "Consultant";
		} else if (! empty ( $codeEntreprise ) && empty ( $nomEntreprise )) {
			return "Associe";
		}
	}

	/**
	 * Création d'un utilisateur
	 *
	 * @param unknown $infoUtilisateur
	 * @return number
	 */
	public function creerUtilisateurService($infoUtilisateur) {
		$retur_message = array ();

		$messageErreur = $this->controleDonneeUtilisateur ( $infoUtilisateur );

		if (! empty ( $messageErreur )) {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = $messageErreur;
		} else {

			$mailUtilisateur = FonctionUtils::escape_errors ( $infoUtilisateur ['mailUtilisateur'] );
			$nomUtilisateur = addslashes ( $infoUtilisateur ['nomUtilisateur'] );
			$prenomUtilisateur = addslashes ( $infoUtilisateur ['prenomUtilisateur'] );
			$mdpUtilisateur = hash ( "sha256", $infoUtilisateur ['mdpUtilisateur'] );
			$nomEntreprise = addslashes ( $infoUtilisateur ['nomEntreprise'] );
			$codeEntreprise = addslashes ( $infoUtilisateur ['code'] );

			$existeEntreprise = $this->userDao->existeCodeEntreprise ( $codeEntreprise );
			$mailConsultant = '';

			if (! empty ( $infoUtilisateur ['mailConsultant'] )) {
				$mailConsultant = addslashes ( $infoUtilisateur ['mailConsultant'] );
			}

			if (! empty ( $infoUtilisateur ['entrepriseConsultant'] )) {
				$entrepriseConsultant = addslashes ( $infoUtilisateur ['entrepriseConsultant'] );
			}

			if (! $this->userDao->existeUtilisateur ( $mailUtilisateur )) {
				$nomEntrepriseBD = $this->userDao->recupNomEntreprise ( $codeEntreprise );
				$groupe = $this->choixGroupe ( $nomEntreprise, $codeEntreprise );

				$utilisateur = new Utilisateur ();
				if ($groupe == 'Associe' && $existeEntreprise) {
					$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, $groupe, $nomEntrepriseBD );
					$this->userDao->createUser ( $utilisateur );
					$retur_message ['status'] = 'success';
					$retur_message ['message'] = 10;
				} else if ($groupe == 'Dirigeant') {

					$entreprise = new EntrepriseService ();

					if (! empty ( $mailConsultant )) {
						$return = $entreprise->createEntreprise ( $prenomUtilisateur, $mailUtilisateur, $nomEntreprise, $mailConsultant, null, null, null, null, null );
					} else {
						$return = $entreprise->createEntreprise ( $prenomUtilisateur, $mailUtilisateur, $nomEntreprise, null, null, null, null, null, null );
					}
					if ($return ['status'] == "error") {
						$return ['message'] = FonctionUtils::affiche_erreur ( $return ['message'] );
						return $return;
					}
					$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, $groupe, $nomEntreprise );
					$this->userDao->createUser ( $utilisateur );
					$retur_message ['status'] = 'success';
					$retur_message ['message'] = 28;
				} else if ($groupe == "Consultant") {
					$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, $groupe, $nomEntrepriseBD );
					$this->userDao->createUser ( $utilisateur );
					$this->userDao->createHeaderFooter ( $mailUtilisateur, $entrepriseConsultant );
					$retur_message ['status'] = 'success';
					$retur_message ['message'] = 10;
					$mdpConsultant = $infoUtilisateur ['mdpUtilisateur'];
					$message = "Bonjour,"."\n\n";
					$message .= "Un compte consultant a été créé avec cette adresse email."."\n";
					$message .= "Voici votre mot de passe temporaire : '$mdpConsultant'."."\n";
					$message .= "Merci de modifier votre mot de passe via Compte/Paramètres.";
					// evoie mail
					$sujet_mail = "Compte consultant SygSyp";
					FonctionUtils::envoie_mail ( $mailUtilisateur, $sujet_mail, $message );
				} elseif (! $existeEntreprise) {
					$retur_message ['status'] = 'error';
					$retur_message ['message'] = 27;
				} elseif (empty ( $mailConsultant )) {
					$retur_message ['status'] = 'error';
					$retur_message ['message'] = 4;
				} else {
					$retur_message ['status'] = 'error';
					$retur_message ['message'] = 6;
				}
			} else {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = 34;
			}
		}
		$retur_message ['message'] = FonctionUtils::affiche_erreur ( $retur_message ['message'] );
		return $retur_message;
	}

	/**
	 *
	 * @param unknown $mail
	 * @return string[]|number[]
	 *
	 */
	public function initialiserMdp($mail) {
		$return_message = array ();
		// vérification champ mail
		if (! empty ( $mail )) {
			if (filter_var ( $mail, FILTER_VALIDATE_EMAIL )) {
				$utilisateur = new Utilisateur ();
				$utilisateur->setEmail ( $mail );
				if ($this->userDao->existeUtilisateur ( $utilisateur->getEmail () )) {
					// Géniration de la clé de rénitialisation
					$mdp_gener = uniqid ( rand ( 1234, 9876 ) );
					$mdp_gener_hash = hash ( "sha256", $mdp_gener );

					$this->userDao->setMdpTemp ( $mdp_gener_hash, $mail );
					// Message du email.
					$message = "Bonjour,"."\n\n";
					$message .= "Voici votre nouveau mot de passe temporaire : "."$mdp_gener"."\n";
					$message .= "Merci de modifier votre mot de passe via Compte/Paramètres.";
					// evoie mail
					$sujet_mail = "Changement de mot de passe";
					FonctionUtils::envoie_mail ( $mail, $sujet_mail, $message );
					$return_message ['status'] = 'success';
					$return_message ['message'] = 26;
				} else {
					$return_message ['status'] = 'error';
					$return_message ['message'] = 1;
				}
			} else {
				$return_message ['status'] = 'error';
				$return_message ['message'] = 2;
			}
		} else {
			$return_message ['status'] = 'error';
			$return_message ['message'] = 4;
		}
		$return_message ['message'] = FonctionUtils::affiche_erreur ( $return_message ['message'] );
		return $return_message;
	}

	/**
	 *
	 * @param unknown $header
	 * @param unknown $footer
	 * @param unknown $mail
	 * @return string[]|number[]
	 *
	 */
	public function setHeaderFooter($header, $footer, $mail) {

		if (! empty ( $header ) || ! empty ( $footer )) {
			if ($this->userDao->existeConsultant ( $mail )) {
				if (! empty ( $header ) && ! empty ( $footer )){
					if ($header['filename'] < 150000){
						file_put_contents('../images/logosHeaders/'.$header['filename'], base64_decode($header['base64']));
						$this->userDao->setHeaderFooter ( $header['filename'], $footer, $mail );	
					}
					else {
						$this->userDao->setHeaderFooter ( null, $footer, $mail );
					}
					
				}
				else if (! empty ( $header )){
					if ($header['filename'] < 150000){
						file_put_contents('../images/logosHeaders/'.$header['filename'], base64_decode($header['base64']));
						$this->userDao->setHeaderFooter ( $header['filename'], null, $mail );	
					}
					else {
						$return_message ['status'] = 'error';
						$return_message ['message'] = 33;
					}
				}
				else {
					$this->userDao->setHeaderFooter ( null, $footer, $mail );
				}

				$return_message ['status'] = 'success';
				$return_message ['message'] = 30;
			} else {
				$return_message ['status'] = 'error';
				$return_message ['message'] = 7;
			}
		} else {
			$this->userDao->setHeaderFooter ( null, null, $mail );
			$return_message ['status'] = 'success';
			$return_message ['message'] = 30;
		}
		$return_message ['message'] = FonctionUtils::affiche_erreur ( $return_message ['message'] );
		return $return_message;

	}

	/**
	 *
	 * @param unknown $mailConsultant
	 * @return string[]|number[]|unknown[]
	 */
	public function getHeaderFooter($mailConsultant) {
		$return_message = array ();
		if (! empty ( $mailConsultant )) {

			if ($this->userDao->existeConsultant ( $mailConsultant )) {
				$headerFooter = $this->userDao->getHeaderFooter ( $mailConsultant );
				$return_message ['status'] = 'success';
				$return_message ['message'] = $headerFooter;
			} else {
				$return_message ['status'] = 'error';
				$return_message ['message'] = 7;
			}
		} else {
			$return_message ['status'] = 'error';
			$return_message ['message'] = 4;
		}
		return $return_message;
	}
}
?>
