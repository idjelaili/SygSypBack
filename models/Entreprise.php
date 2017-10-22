<?php

class Entreprise {

	private $nomEntreprise;
	private $code;
	private $adresse1;
	private $adresse2;
	private $codePostal;
	private $ville;
	private $telephone;
	private $idQuestionnaire;
	private $mailConsultant;

	function __construct() {
	}

	function creerEntreprise($entreprise) {

		$this->nomEntreprise = $entreprise ['nomEntreprise'];
		$this->code = $entreprise ['code'];
		
	}

	function setNomEntreprise($nomEntreprise) {
		$this->nomEntreprise = $nomEntreprise;
	}

	function setCode($code) {
		$this->code = $code;
	}

	function setAdresse1($adresse1) {
		$this->setAdresse1 = $adresse1;
	}

	function setAdresse2($adresse2) {
		$this->setAdresse2 = $adresse2;
	}

	function setCodePostal($codePostal) {
		$this->codePostal = $codePostal;
	}

	function setVille($ville) {
		$this->ville = $ville;
	}

	function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	function setIdQuestionnaire($idQuestionnaire) {
		$this->idQuestionnaire = $idQuestionnaire;
	}

	function setMailConsultant($mailConsultant) {
		$this->mailConsultant = $mailConsultant;
	}



	function getNomEntreprise() {
		return $this->nomEntreprise;
	}

	function getCode() {
		return $this->code;
	}

	function getAdresse1() {
		return $this->Adresse1;
	}

	function getAdresse2() {
		return $this->Adresse2;
	}

	function getCodePostal() {
		return $this->codePostal;
	}

	function getVille() {
		return $this->ville;
	}

	function getTelephone() {
		return $this->telephone;
	}

	function getIdQuestionnaire() {
		return $this->idQuestionnaire;
	}

	function getMailConsultant() {
		return $this->mailConsultant;
	}
}