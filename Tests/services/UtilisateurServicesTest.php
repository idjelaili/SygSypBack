<?php
include_once("../../dao/config.php");
include_once("../../services/UtilisateurService.php");
require_once ('../../dao/DBconnect.php');
require_once('../../dao/UtilisateurDao.php');
include_once("../../dao/impl/UtilisateurDaoImpl.php");
require_once ("../../security/Authenticate.php");
require_once ('../../utils/FonctionUtils.php');
require_once ('../../models/Utilisateur.php');

//use \PHPUnit\Framework\TestCase;

/**
 * 
 * @covers UtilsateurService
 *
 */

class UtilisateurServicesTest extends  \PHPUnit\Framework\TestCase{

	public function test()
	{
		$this->assertFalse(false);
	}
	
	
	/**
	*@befor
	*/
	
	
	/*
	public function setUpCreerUserInfo()
	{
		$tabInfoUtilisateur_a = array(
			 "nomUtilisateur" => "nom",
			 "prenomUtilisateur" => "prenom",
			 "mailUtilisateur"=>"utilisateur_a@gmail.com",
			 "mdpUtilisateur"=> "mdp1234+",
			 "confirmationDePasse"=>"mdp1234+",
			 "groupe"=>"Associe",
			 "nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec nom utilisateur vide
		$tabInfoUtilisateur_b = array(
				"nomUtilisateur" => "",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec email utilisateur vide
		$tabInfoUtilisateur_c = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec prenom utilisateur vide
		$tabInfoUtilisateur_d = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec nom entreprise et un code entreprise vide 
		$tabInfoUtilisateur_e = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> ""
		);
		
		//inscription avec nom utilisateur invalide
		$tabInfoUtilisateur_f = array(
				"nomUtilisateur" => "12345",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec prenom utilisateur invalide
		$tabInfoUtilisateur_g = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "12345",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec mot de passe invalide composé de 4 espaces
		$tabInfoUtilisateur_h = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "    ",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec un mot de passe trop court
		$tabInfoUtilisateur_i = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "1",
				"confirmationDePasse"=>"1",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec un mot de passe et la confirmation du mot de passe différent
		$tabInfoUtilisateur_j = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234-",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);

		//inscription avec nom utilisateur trop court
		$tabInfoUtilisateur_k = array(
				"nomUtilisateur" => "n",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec un prenom utilisateur trop court
		$tabInfoUtilisateur_l = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "p",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec nom utilisateur trop long
		$tabInfoUtilisateur_m = array(
				"nomUtilisateur" => "abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec un prenom utilisateur trop long
		$tabInfoUtilisateur_n = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		

		//inscription avec email utilisateur invalide
		$tabInfoUtilisateur_o1 = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_agmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec email utilisateur invalide
		$tabInfoUtilisateur_o2 = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmailcom",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec email utilisateur déjà utilisé
		$tabInfoUtilisateur_o2 = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmailcom",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		
		//inscription avec un code entreprise invalide
		$tabInfoUtilisateur_p = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_agmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
	}
	*/
	/**
	 * @after
	 *
	public function deleteDataTests()
	{
		$connectionUserDao = UtilisateurDaoImpl();
		$sqlDeleteUser_a ='DELETE FROM utilisateur WHERE email="utilisateur_a@gmail.com"';
		$statement = $connectionUserDao->connection->prepare ( $sqlDeleteUser_a );
		$statement->execute ( array (
				$utilisateur->getEmail (),
				$utilisateur->getNom (),
				$utilisateur->getPrenom (),
				$utilisateur->getMdp (),
				$utilisateur->getGroupe (),
				$utilisateur->getNomEntreprise (),
				$utilisateur->getActif()
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		die();
	}*/
	
	
	
	public function test_VerifierInscriptionUtilisateurOK(){
		$tabInfoUtilisateur_a = array(
				"nomUtilisateur" => "nom",
				"prenomUtilisateur" => "prenom",
				"mailUtilisateur"=>"utilisateur_a@gmail.com",
				"mdpUtilisateur"=> "mdp1234+",
				"confirmationDePasse"=>"mdp1234+",
				"groupe"=>"Associe",
				"nomEntreprise"=> "sygsyp"
		);
		$fonctionUtil = new FonctionUtils();
		$userService = new UtilisateurService ();
		$test = $userService->controleDonneeUtilisateur($tabInfoUtilisateur_a);
		//$this->assertEquals($fonctionUtil->affiche_erreur($userService->controleDonneeUtilisateur($tabInfoUtilisateur_a)),"Inscription terminee");	
		//$this->assertEquals($userService->controleDonneeUtilisateur($tabInfoUtilisateur_a),2);
		$this->assertEquals($test, "2");
	}
	/*
	public function VerifierInscriptionUtilisateurNomVide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Tous les champs doivent etre complété.");
	}

	public function VerifierInscriptionUtilisateurEmailVide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Tous les champs doivent etre complété.");
	}
	
	public function VerifierInscriptionUtilisateurPrenomVide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Tous les champs doivent etre complété.");
	}
		
	public function VerifierInscriptionUtilisateurNomEntrepriseEtCodeVide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Tous les champs doivent etre complété.");
	}
		
	public function VerifierInscriptionUtilisateurNomInvalide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Ce champ ne doit contenir que des lettres");
	}
	
	public function VerifierInscriptionUtilisateurPrenomInvalide($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Ce champ ne doit contenir que des lettres");
	}
	
	public function VerifierInscriptionUtilisateurMdpEspace($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Ce champ ne doit contenir que des lettres");
	}
	public function VerifierInscriptionUtilisateurMdpTropCourt($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Votre mot de passe doit contenir au minimum 8 caractères.");
	}
		
	public function VerifierInscriptionUtilisateurNomTropCourt($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="");
	}
		
	public function VerifierInscriptionUtilisateurPrenomTropCourt($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="");
	}
		
	public function VerifierInscriptionUtilisateurNomTropLong($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Votre nom est trop grand !");
	}
		
	public function VerifierInscriptionUtilisateurPrenomTropLong($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Votre prénom est trop grand !");
	}
		
	public function VerifierInscriptionUtilisateurMailIncorrecte($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Votre adresse mail est incorrecte !");
	}
		
	public function VerifierInscriptionUtilisateurMailDejaUtilise($mailUtilisateur,$nomUtilisateur,$prenomUtilisateur,$confirmationDePasse,$mdpUtilisateur,$nomEntreprise,$groupe){
		$this->assertEquals(controleDonneeUtilisateur($infoUtilisateur) =="Adresse mail déjà utilisée");
	}*/
}