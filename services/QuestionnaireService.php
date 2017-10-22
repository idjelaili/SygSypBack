<?php
require_once ('dao/impl/QuestionnaireDaoImpl.php');
require_once ('dao/impl/EntrepriseDaoImpl.php');
require_once ('utils/FonctionUtils.php');
require_once ('dao/DBconnect.php');

class QuestionnaireService {
	
	private $questionDao;
	
	function __construct() {
		$this->questionDao = new QuestionnaireDaoImpl ();
	}
	
	/**
	 *
	 * @param type $idQuestionnaire        	
	 * @param type $nomEntreprise        	
	 * @return string
	 */
	public function setQuestionnaire($idQuestionnaire, $nomEntreprise) {
		$retur_message = array ();
		if (! empty ( $nomEntreprise )) {
			$entreprise = new EntrepriseDaoImpl ();
			if ($entreprise->existeEntreprise ( $nomEntreprise )) {
				$this->questionDao->setQuestionnaire ( $idQuestionnaire, $nomEntreprise );
				$retur_message ['status'] = 'success';
				$retur_message ['message'] = 'affectation de questionnaire effectuée avec succes';
			} else {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = "L'entreprise n'existe pas";
			}
		} else {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = "Le nom d'entreprise est vide";
		}
		return $retur_message;
	}
	
	/**
	 *
	 * @param type $infoQuestionnaire        	
	 * @return boolean
	 */
	public function ajouterQuestionnaire($infoQuestionnaire) {
		$retur_message = array ();
		if (! empty ( $infoQuestionnaire ['libelleQuestionnaire'] )) {
			
			if (empty ( $infoQuestionnaire ['defaut'] )) {
				$defaut = 0;
			} else {
				$defaut = $infoQuestionnaire ['defaut'];
			}
			if (!$this->questionDao->existeQuestionnaire ( $infoQuestionnaire ['libelleQuestionnaire'] )) {
			
                            $idQuestionnaire = $this->questionDao->ajouterQuestionnaire ( $infoQuestionnaire ['libelleQuestionnaire'], $defaut );
				return $idQuestionnaire;
			} else {
				$retur_message ['status'] = 'error';
				$retur_message ['message'] = "Vous avez déja renseigné ce questionnaire";
				return $retur_message;
			}
		} else {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = "veillez renseigner le libelle du questionnaire";
			return $retur_message;
		}
	}
	
	/**
	 *
	 * @param type $idQuestionnaire        	
	 * @param type $defaut        	
	 * @return string
	 */
	public function setDefautQuestionnaire($idQuestionnaire, $defaut) {
		$retur_message = array ();
		
		if (! empty ( $idQuestionnaire ) && ($defaut == 0 || $defaut == 1)) {
			$this->questionDao->setDefaut ( $idQuestionnaire, $defaut );
			$retur_message ['status'] = 'success';
			$retur_message ['message'] = 'Affectation du questionnaire par defaut effectuée avec succes';
		} else {
			$retur_message ['status'] = 'error';
			$retur_message ['message'] = "Veillez introduire tous les champs";
		}
		return $retur_message;
	}
}
