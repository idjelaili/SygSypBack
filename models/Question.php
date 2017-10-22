<?php

class Question {

	private $idQuestion;
	private $libelleQuestion;
	private $axe;
	private $theme;
	private $idQuestionnaire;

	function __construct() {
	}

	function creerQuestion($question) {

		$this->libelleQuestion = $question ['libelleQuestion'];
		$this->axe = $question ['axe'];
		$this->theme = $question ['$theme'];
		$this->idQuestionnaire = $question ['idQuestionnaire'];
	}

	function setLibelleQuestion($libelleQuestion) {
		$this->libelleQuestion = $libelleQuestion;
	}

	function setAxe($axe) {
		$this->axe = $axe;
	}

	function setTheme($theme) {
		$this->theme = $theme;
	}

	function setIdQuestionnaire($idQuestionnaire) {
		$this->idQuestionnaire = $idQuestionnaire;
	}


	function getLibelleQuestion() {
		return $this->libelleQuestion;
	}

	function getAxe() {
		return $this->axe;
	}

	function getTheme() {
		return $this->theme;
	}

	function getIdQuestionnaire() {
		return $this->idQuestionnaire;
	}

}