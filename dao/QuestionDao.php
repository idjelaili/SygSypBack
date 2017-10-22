<?php

interface QuestionDao {

    public function getQuestionUtilisateur($mailUtilisateur);

    public function hasQuestionnaireNull($mailUtilisateur);

    public function defautQuestionnaire();

    public function ajouterQuestion($libelleQuestion, $axe, $theme, $idQuestionnaire);
}

?>
