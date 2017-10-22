<?php

require_once ('dao/impl/QuestionDaoImpl.php');
require_once ('utils/FonctionUtils.php');
require_once ('models/Question.php');
require_once ('dao/DBconnect.php');

class QuestionService {

    private $questionDao;

    function __construct() {
        $this->questionDao = new QuestionDaoImpl();
    }

    /**
     * Récupérer la liste des questions de l'entreprise si elle existe
     * sinon renvoie la liste des questions par défaut
     * 
     * @param string $mailUtilisateur adresse mail de l'Utilisateur
     * @return string[]|liste des questions
     */
    public function getQuestionUtilisateur($mailUtilisateur) {
        if (empty($mailUtilisateur)) {
            $errMsg = 'Mail utilisateur vide';
            $error = 'error';
            return array(
                "status" => $error,
                "message" => $errMsg
            );
        }
        $hasQuestionnaireNull = $this->questionDao->hasQuestionnaireNull($mailUtilisateur);
        if (empty($hasQuestionnaireNull[0]['idQuestionnaire'])) {
            $result = $this->questionDao->defautQuestionnaire();
        } else {
            $result = $this->questionDao->getQuestionUtilisateur($mailUtilisateur);
        }
        if (empty($result)) {
            return array(
                "message" => "Pas de questionnaire pour l'entreprise"
            );
        }
        return $result;
    }

    /**
     * 
     * @param type $listeQuestions
     * @param type $idQuestionnaire
     */
    public function ajouterquestion($listeQuestions, $idQuestionnaire) {
       
        foreach ($listeQuestions as $question) {
            
            if (!empty($question['libelleQuestion']) && !empty($question['Axe']) && !empty($question['Theme'])) {

                $this->questionDao->ajouterQuestion($question['libelleQuestion'], $question['Axe'], $question['Theme'], $idQuestionnaire);
            }
        }
    }

}

?>