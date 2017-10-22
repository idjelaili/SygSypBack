<?php

class ResultTest {

	private $idTest;
	private $mailUtilisateur;
	private $dateTest;
	private $idQuestionnaire;
	private $commentaireUtilisateur;
	private $commentaireConsultant;

	function __construct() {
	}

	function creerTest($test) {
		$this->commentaireUtilisateur = $test ['commentaireUtilisateur'];
		$this->idQuestionnaire = $test ['idQuestionnaire'];
	}

	function setMailUtilisateur($mailUtilisateur) {
		$this->mailUtilisateur = $mailUtilisateur;
	}

	function setDateTest($dateTest) {
		$this->dateTest = $dateTest;
	}

	function setIdQuestionnaire($idQuestionnaire) {
		$this->idQuestionnaire = $idQuestionnaire;
	}

	function setCommentaireUtilisateur($commentaireUtilisateur) {
		$this->commentaireUtilisateur = $commentaireUtilisateur;
	}

	function setCommentaireConsultant($commentaireConsultant) {
		$this->commentaireConsultant = $commentaireConsultant;
	}

	function getIdTest() {
		return $this->idTest;
	}

	function getMailUtilisateur() {
		return $this->mailUtilisateur;
	}

	function getDateTest() {
		return $this->dateTest;
	}

	function getCommentaireUtilisateur() {
		return $this->commentaireUtilisateur;
	}

	function getCommentaireConsultant() {
		return $this->commentaireConsultant;
	}
	
	function getIdQuestionnaire(){
		return $this->idQuestionnaire;
	}
}