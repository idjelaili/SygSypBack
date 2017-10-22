<?php
interface QuestionnaireDao {
	public function getQuestionnaires();
	public function setQuestionnaire($idQuestionnaire, $nomEntreprise);
	public function ajouterQuestionnaire($libele, $defaut);
	public function setDefaut($idQuestionnaire, $defaut);
	public function existeQuestionnaire($libelle);
        public function getQuestionnaireTest($idTest);
}

?>
