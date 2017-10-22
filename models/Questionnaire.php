<?php

class Questionnaire {

	private $idQuestionnaire;
	private $libelleQuestionnaire;
	private $dateMAJ;
	private $defaut;

	function __construct() {
	}

	function creerQuestionnaire($questionnaire) {

		$this->libelleQuestionnaire = $questionnaire ['libelleQuestionnaire'];
	}

	function setLibelleQuestionnaire($idQuestionnaire) {
		$this->libelleQuestionnaire = $idQuestionnaire;
	}

	function setDateMAJ($dateMAJ) {
		$this->dateMAJ = $dateMAJ;
	}

	function setDefault($defaut) {
		$this->defaut = $defaut;
	}


	function getIdQuestionnaire() {
		return $this->idQuestionnaire;
	}

	function getLibelleQuestionnaire() {
		return $this->libelleQuestionnaire;
	}

	function getDateMAJ() {
		return $this->dateMAJ;
	}

	function getDefaut() {
		return $this->defaut;
	}
}