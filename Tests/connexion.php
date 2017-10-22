<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/sigsipfront/app/#!/home/connection");
  }

  public function testMyTestCase()
  {
    $this->open("/");
    $this->open("/sigsipfront/app/#!/home/about");
    $this->click("link=Connexion");
    $this->type("id=Email", "mzucarogmail.com");
    $this->type("id=pwd", "pass");
    $this->click("css=button.btn.btn-primary");
    $this->verifyTextPresent("Erreur connexion!");
  }
}
?>