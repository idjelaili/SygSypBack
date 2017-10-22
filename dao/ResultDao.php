<?php

interface ResultDao {

    public function getTestUtilisateur($mailUtilisateur);

    public function creerTestUtilisateur($testUtilisateur);

    public function getResultEntreprise($nomEntreprise);

    public function getToutTestCollaborateurs($nomEntreprise);

    public function getNomEntrepriseDirigeant($mailUtilisateur);

    public function getQuestionsReponses($idTest);

    public function getEnteteDoc($nomEntreprise, $idTest);

    public function updateCommentaireAssocie($idTest, $commentaire);

    public function updateCommentaireConsultant($idTest, $commentaire);

    public function getRole($email);
    public function getCommentaire($idTest);
}

?>