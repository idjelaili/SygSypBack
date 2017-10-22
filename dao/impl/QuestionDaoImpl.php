<?php

require_once ('dao/DBconnect.php');
require_once ('dao/QuestionDao.php');

class QuestionDaoImpl implements QuestionDao {

    private $connection;

    function __construct() {
        $db = new DbConnect ();
        $this->connection = $db->connect();
    }

    /**
     *
     * @param unknown $mailUtilisateur        	
     * @return unknown
     */
    public function getQuestionUtilisateur($mailUtilisateur) {
        $statement = $this->connection->prepare("SELECT idQuestion, libelleQuestion, axe, theme, idQuestionnaire FROM question
												   WHERE question.idQuestionnaire in 
														(SELECT entreprise.idQuestionnaire FROM entreprise WHERE entreprise.nomEntreprise in 
														(SELECT utilisateur.nomEntreprise FROM utilisateur WHERE utilisateur.mailUtilisateur = ?)) ORDER BY RAND()
												  ");
        $statement->execute(array($mailUtilisateur)) or die(print_r($statement->errorInfo(), true));
        $infoQuestionUtilisateur = $statement->fetchAll();
        return $infoQuestionUtilisateur;
    }

    /**
     * 
     * @param unknown $mailUtilisateur
     * @return unknown
     */
    public function hasQuestionnaireNull($mailUtilisateur) {
        $statement = $this->connection->prepare("SELECT idQuestionnaire
												   FROM entreprise
												   WHERE nomEntreprise in
												  (SELECT nomEntreprise from utilisateur WHERE mailUtilisateur = ? )");
        $statement->execute(array($mailUtilisateur)) or die(print_r($statement->errorInfo(), true));
        $idQuestionnaireDefaut = $statement->fetchAll();
        return $idQuestionnaireDefaut;
    }

    /**
     * 
     * @return unknown
     */
    public function defautQuestionnaire() {
        $statement = $this->connection->prepare("SELECT idQuestion, libelleQuestion, axe, theme, idQuestionnaire 
												   FROM question
												   WHERE question.idQuestionnaire in
												  (SELECT idQuestionnaire FROM questionnaire WHERE questionnaire.defaut = 1)
				                                   ORDER BY RAND();");
        $statement->execute() or die(print_r($statement->errorInfo(), true));
        $infoQuestionUtilisateur = $statement->fetchAll();
        return $infoQuestionUtilisateur;
    }
    /**
     * 
     * @param type $libelleQuestion
     * @param type $axe
     * @param type $theme
     * @param type $idQuestionnaire
     * @return type
     */

    public function ajouterQuestion($libelleQuestion, $axe, $theme,$idQuestionnaire) {
     
      
        $statement = $this->connection->prepare("INSERT INTO question (libelleQuestion, axe, theme, idQuestionnaire)
         			   VALUES (?, ?, ?, ?) ");
        $statement->execute(array(
                    $libelleQuestion,
                    $axe,
                    $theme,
                    $idQuestionnaire
                )) or die(print_r($statement->errorInfo(), true));
        $idQuestion = $statement->fetchAll();
        return $idQuestion;
    }

}

?>