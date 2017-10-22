<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/sigsipfront/app/#!/home/about");
  }

  public function testMyTestCase()
  {
    $this->open("/sigsipfront/app/#!/home/inscription");
    $this->click("link=Accueil");
    $this->click("link=Inscription");
    $this->type("id=prenom", "prenomUtilisateur");
    $this->type("id=pwd", "mdp123");
    $this->type("id=email", "utilisateur3@hotmail.fr");
    $this->type("id=nom", "nomUtilisateur");
    $this->type("id=pwdConfirm", "mdp123");
    $this->type("id=company", "Entreprise1");
    $this->click("css=button.btn.btn-primary");
  }
}
?>