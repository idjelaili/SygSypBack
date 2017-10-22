<?php

interface UtilisateurDao
{
 public function createUser($utilisateur);

 public function existeUtilisateur($email);

 public function existeCodeEntreprise($codeEntreprise);

 public function recupNomEntreprise($codeEntreprise);

 public function setMdpTemp($mdp_ren, $mail);

 public function getInfoConsultant();

 public function setHeaderFooter($header, $footer, $mail);

 public function existeConsultant($email);
 
 public function getHeaderFooter( $mail );

 // public function updatePwd($email, $data);

}

?>
