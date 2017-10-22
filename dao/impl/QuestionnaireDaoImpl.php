<?php

require_once('dao/DBconnect.php');
require_once('dao/QuestionnaireDao.php');

class QuestionnaireDaoImpl implements QuestionnaireDao {

    private $connection;

    function __construct() {
        $db = new DbConnect();
        $this->connection = $db->connect();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see QuestionnaireDao::getQuestionnaires()
     */
    public function getQuestionnaires() {
        $statement = $this->connection->prepare("SELECT * FROM questionnaire");
        $statement->execute() or die(print_r($statement->errorInfo(), true));
        $questionnaire = $statement->fetchAll();
        return $questionnaire;
    }

    /**
     * 
     * {@inheritDoc}
     * @see QuestionnaireDao::setQuestionnaire()
     */
    public function setQuestionnaire($idQuestionnaire, $nomEntreprise) {
        if ($idQuestionnaire != null) {
            $statement = $this->connection->prepare("UPDATE entreprise SET idQuestionnaire = ? WHERE nomEntreprise= ? ");
            $statement->execute(array(
                        $idQuestionnaire,
                        $nomEntreprise
                    )) or die(print_r($statement->errorInfo(), true));
        } else {
            $statement = $this->connection->prepare("UPDATE entreprise SET idQuestionnaire = null WHERE nomEntreprise = ?");
            $statement->execute(array(
                        $nomEntreprise
                    )) or die(print_r($statement->errorInfo(), true));
        }
        $questionnaire = $statement->fetchAll();
        return $questionnaire;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see QuestionnaireDao::setDefaut()
     */
    public function setDefaut($idQuestionnaire,$defaut){
        if ($defaut == 1) {
            $statement = $this->connection->prepare("UPDATE questionnaire SET dateMAJ = now(), defaut = ? ");
            $statement->execute(array(
                        0
                    )) or die(print_r($statement->errorInfo(), true));
        }
        $statement = $this->connection->prepare("UPDATE questionnaire SET dateMAJ = now(), defaut = ? WHERE idQuestionnaire= ?");
        $statement->execute(array(
                    $defaut,
                    $idQuestionnaire
                    
                )) or die(print_r($statement->errorInfo(), true));
        $idQuestionnaire = $statement->fetchAll();
        return $idQuestionnaire;
        
    }

    /**
     * 
     * @param type $libelle
     * @param type $defaut
     */
    public function ajouterQuestionnaire($libelle, $defaut) {
       
        if ($defaut == 1) {
            $statement = $this->connection->prepare("UPDATE questionnaire SET dateMAJ = now(), defaut = ? ");
            $statement->execute(array(
                        0
                    )) or die(print_r($statement->errorInfo(), true));
        }
        $statement = $this->connection->prepare("INSERT INTO questionnaire (libelleQuestionnaire, dateMAJ, defaut)
         			   VALUES (?, now(), ?) ");
        $statement->execute(array(
                    $libelle,
                    $defaut
                )) or die(print_r($statement->errorInfo(), true));
        
        $idQuestionnaire = $this->connection->lastInsertId();
        return $idQuestionnaire;
    }
    
    /**
     * 
     * @param unknown $libelle
     * @return boolean
     */
    public function existeQuestionnaire($libelle) {
    	$statement = $this->connection->prepare ( "SELECT * FROM questionnaire WHERE libelleQuestionnaire = ? " );
    	$statement->execute ( array (
    			$libelle
    	) ) or die ( print_r ( $statement->errorInfo (), true ) );
    	if ($statement->rowCount() == 0) {
    		return false;
    	}
    	return true;
    }
    /**
     * 
     * @param type $idTest
     * @return type
     */
      public function getQuestionnaireTest($idTest) {
    	$statement = $this->connection->prepare ( "SELECT q.libelleQuestionnaire, q.defaut FROM questionnaire q, test t WHERE t.idTest = ? AND t.idQuestionnaire = q.idQuestionnaire" );
    	$statement->execute ( array (
    			$idTest
    	) ) or die ( print_r ( $statement->errorInfo (), true ) );
          $questionnaire = $statement->fetchAll();
        return $questionnaire;
    	
    }

}
