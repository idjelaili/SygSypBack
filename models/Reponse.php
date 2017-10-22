<?php

class Reponse {

	private $idQuestion;
	private $idTest;
	private $valeur;

	function __construct() {
	}
	
	function creerReponse($reponse) {
		$this->idQuestion = $reponse ['idQuestion'];
		$this->idTest = $reponse ['idTest'];
		$this->valeur = $reponse ['valeur'];
	}

	function setIdQuestion($idQuestion) {
		$this->idQuestion = $idQuestion;
	}

	function setIdTest($idTest) {
		$this->idTest = $idTest;
	}

	function setValeur($valeur) {
		$this->valeur = $valeur;
	}

	function getIdQuestion() {
		return $this->idQuestion;
	}

	function getIdTest() {
		return $this->idTest;
	}

	function getValeur() {
		return $this->valeur;
	}

}