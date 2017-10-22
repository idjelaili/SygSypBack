<?php
require_once ('dao/impl/ReponseDaoImpl.php');
require_once ('utils/FonctionUtils.php');
require_once ('models/Reponse.php');
require_once ('dao/DBconnect.php');

class ReponseService {

	private $reponseDao;

	function __construct() {
		$this->reponseDao = new ReponseDaoImpl ();
	}

	/**
	 * 
	 * @param unknown $ResultTest
	 */
	public function creerReponseUtilisateur($reponsesUtilisateur){
		
		$reponse = new Reponse();
		// il faut une boucle pour l'affichage
		$reponse->creerReponse($reponsesUtilisateur);
		//
	}
	/*  `idTest` int(11) NOT NULL AUTO_INCREMENT,
  `mailUtilisateur` varchar(45) NOT NULL,
  `dateTest` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idQuestionnaire` int(11) NOT NULL,
  `commentaireUtilisateur` varchar(255) DEFAULT NULL,
  `commentaireConsultant` varchar(255) DEFAULT NULL,*/
	
	/*public function creerReponseQuestion($reponseQuestion) {
		
		$this->idQuestion = $reponse ['idQuestion'];
		$this->idTest = $reponse ['idTest'];
		$this->valeur = $reponse ['valeur'];

		$retur_message = array ();

		$messageErreur = $this->controleDonneeUtilisateur ( $reponseQuestion );
		if (! empty ( $messageErreur )) {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = $messageErreur;
		} else {
			$mailUtilisateur = FonctionUtils::escape_errors ( $reponseQuestion ['mailUtilisateur'] );
			$nomUtilisateur = addslashes ( $reponseQuestion ['nomUtilisateur'] );
			$prenomUtilisateur = addslashes ( $reponseQuestion ['prenomUtilisateur'] );
			$mdpUtilisateur = hash ( "sha256", $reponseQuestion ['mdpUtilisateur'] );
			$nomEntreprise = addslashes ( $reponseQuestion ['nomEntreprise'] );
			$codeEntreprise = addslashes ( $reponseQuestion ['code'] );
				
			$existeEntreprise = $this->reponseDao->existeCodeEntreprise ( $codeEntreprise );
			$mailConsultant = '';
				
			if (! empty ( $reponseQuestion ['mailConsultant'] )) {
				$mailConsultant = addslashes ( $reponseQuestion ['mailConsultant'] );
			}
				
			if (! $this->reponseDao->existeUtilisateur ( $mailUtilisateur )) {
				$nomEntrepriseBD = $this->reponseDao->recupNomEntreprise ( $codeEntreprise );
				$groupe = $this->choixGroupe ( $nomEntreprise, $codeEntreprise );

				$utilisateur = new Utilisateur ();
				if ($groupe == 'Associe' && $existeEntreprise) {
					$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, $groupe, $nomEntrepriseBD );
					$this->reponseDao->createUser ( $utilisateur );
					$retur_message ['status'] = 'success';
					$retur_message ['message'] = 10;
						
				} else if ($groupe == 'Dirigeant' && ! empty ( $mailConsultant )) {
					$entreprise = new EntrepriseService ();
					$return = $entreprise->createEntreprise ( $mailUtilisateur, $nomEntreprise, $mailConsultant );
					$utilisateur->creertUtilisateurInfo ( $mailUtilisateur, $nomUtilisateur, $prenomUtilisateur, $mdpUtilisateur, $groupe, $nomEntreprise );
						
					$this->reponseDao->createUser ( $utilisateur );
					if ($return ['status'] == 'success') {
						$retur_message ['status'] = 'success';
						$retur_message ['message'] = 28;
					}
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
				$retur_message ['message'] = 7;
			}
		}
		$retur_message ['message'] = FonctionUtils::affiche_erreur ( $retur_message ['message'] );
		return $retur_message;
	}*/
}
?>
