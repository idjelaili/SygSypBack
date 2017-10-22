<?php

class Utilisateur {
	
	private $mailUtilisateur;
	private $nomUtilisateur;
	private $prenomUtilisateur;
	private $mdpUtilisateur;
	private $groupe;
	private $actif;
	private $nomEntreprise;
	private $codeActifMail;
	
	function __construct() {
	}
	
	function creerUtilisateur($utilisaeur) {
	
		$this->mailUtilisateur = $utilisaeur ['mailUtilisateur'];
		$this->nomUtilisateur = $utilisaeur ['nomUtilisateur'];
		$this->prenomUtilisateur = $utilisaeur ['prenomUtilisateur'];
		$this->mdpUtilisateur = $utilisaeur ['mdpUtilisateur'];
		$this->groupe = $utilisaeur ['groupe'];
		$this->nomEntreprise = $utilisaeur ['nomEntreprise'];
		$this->actif = 0;
	}
	
	function creertUtilisateurInfo($email, $nom, $prenom, $mdp, $groupe, $nomEntreprise) {
	
		$this->mailUtilisateur = $email;
		$this->nomUtilisateur = $nom;
		$this->prenomUtilisateur =$prenom;
		$this->mdpUtilisateur = $mdp;
		$this->groupe = $groupe;
		$this->nomEntreprise = $nomEntreprise;
		$this->actif = 1;
	}
	
	function setEmail($email) {
		$this->mailUtilisateur = $email;
	}
	
	function setNom($nom) {
		$this->nomUtilisateur = $nom;
	}
	
	function setPrenom($prenom) {
		$this->prenomUtilisateur = $prenom;
	}
	
	function setMdp($mdp) {
		$this->mdpUtilisateur = $mdp;
	}
	
	function setGroupe($groupe) {
		$this->groupe = $groupe;
	}
	
	function setNomEntreprise($nomEntreprise) {
		$this->nomEntreprise = $nomEntreprise;
	}
	
	function setActif($actif) {
		$this->actif = $actif;
	}
	
	function setCodeActifMail($codeActifMail){
		$this->codeActifMail = $codeActifMail;
	}
	
	function getEmail() {
		return $this->mailUtilisateur;
	}
	
	function getNom() {
		return $this->nomUtilisateur;
	}
	
	function getPrenom() {
		return $this->prenomUtilisateur;
	}
	
	function getMdp() {
		return $this->mdpUtilisateur;
	}
	
	function getGroupe() {
		return $this->groupe;
	}
	
	function getNomEntreprise() {
		return $this->nomEntreprise;
	}
	
	function getActif() {
		return $this->actif;
	}
	
	function getCodeActifMail(){
		return $this->codeActifMail;
	}
	
}
