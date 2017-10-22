<?php

require_once ('dao/DBconnect.php');
require_once ('dao/ResultDao.php');
require_once('models/ResultTest.php');

class ResultDaoImpl implements ResultDao {

	private $connection;

	function __construct() {
		$db = new DbConnect ();
		$this->connection = $db->connect ();
	}

	/**
	 * Récupérer les tests d'un utilisateur
	 * {@inheritDoc}
	 * @see TestDao::getTestUtilisateur()
	 */
	public function getTestUtilisateur($mailUtilisateur) {
		$statement = $this->connection->prepare ( "
											 SELECT t.idTest, t.dateTest, t.idQuestionnaire, t.commentaireUtilisateur, t.commentaireConsultant, q.libelleQuestionnaire
											 FROM test t, questionnaire q
											 WHERE mailUtilisateur = ?
											 AND t.idQuestionnaire = q.idQuestionnaire
											 ORDER BY dateTest DESC
											" );
$statement->execute (array($mailUtilisateur)) or die ( print_r ( $statement->errorInfo (), true ) );
$infoTestUtilisateur = $statement->fetchAll ();
return $infoTestUtilisateur;
	}

	/**
	 * Est-ce que l'utilisateur est un dirigeant
	 * @param unknown $mailUtilisateur
	 * @return boolean
	 */
	public function getNomEntrepriseDirigeant($mailUtilisateur) {
		$statement = $this->connection->prepare ( "SELECT nomEntreprise FROM utilisateur WHERE mailUtilisateur = ?" );
		$statement->execute ( array (
				$mailUtilisateur
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		$nomEntrepriseDirigeant = $statement->fetchAll ();
		return $nomEntrepriseDirigeant;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see ResultDao::getToutTestCollaborateurs()
	 */
	public function getToutTestCollaborateurs($nomEntreprise) {
		$statement = $this->connection->prepare ( "
												   SELECT u.nomUtilisateur, u.prenomUtilisateur, t.dateTest, t.idTest, q.libelleQuestionnaire
												   FROM utilisateur u, test t, questionnaire q
												   WHERE u.mailUtilisateur=t.mailUtilisateur AND t.idQuestionnaire = q.idQuestionnaire
												   AND u.nomEntreprise = ?
												   ORDER BY t.dateTest DESC
												  " );
		$statement->execute ( array (
				$nomEntreprise
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		$testCollaborateurs = $statement->fetchAll ();
		return $testCollaborateurs;
	}

	/**
	 * Insertion d'un nouveau test utilisateur
	 * {@inheritDoc}
	 * @see ResultDao::creerTestUtilisateur()
	 * return le dernier id insere
	 */
	public function creerTestUtilisateur($testUtilisateurObj){

		$requeteSQL = "INSERT INTO test(mailUtilisateur, idQuestionnaire, commentaireUtilisateur)
         			   VALUES (?, ?, ?)";
		$statement = $this->connection->prepare ( $requeteSQL );
		$statement->execute ( array (
				$testUtilisateurObj->getMailUtilisateur(),
				$testUtilisateurObj->getIdQuestionnaire(),
				$testUtilisateurObj->getCommentaireUtilisateur()
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		$id_nouveau = $this->connection->lastInsertId();
		return $id_nouveau;
	}

        /**
         *
         * @param type $nomEntreprise
         * @return type
         */
        public function getResultEntreprise($nomEntreprise){
            $statement = $this->connection->prepare ( "SELECT DISTINCT t.idTest, t.dateTest, t.commentaireUtilisateur, t.commentaireConsultant,"
                                                       ." u.nomUtilisateur, u.prenomUtilisateur, q.libelleQuestionnaire,"
                                                       ." t.idQuestionnaire FROM test t, utilisateur u, questionnaire q"
                                                       ." WHERE u.mailUtilisateur  = t.mailUtilisateur AND u.nomEntreprise = '$nomEntreprise' AND q.idQuestionnaire = t.idQuestionnaire");
            $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
            $resultEntreprise = $statement->fetchAll ();
            return $resultEntreprise;
        }

        /**
         *
         * @param unknown $nomEntreprise
         * @param unknown $idTest
         * @return unknown
         */
        public function getEnteteDoc($nomEntreprise,$idTest){
             $statement = $this->connection->prepare ( "SELECT DISTINCT u.nomUtilisateur, u.prenomUtilisateur, e.nomEntreprise, t.dateTest,"
                                                       ." t.idTest,t.commentaireUtilisateur,t.commentaireConsultant,  q.libelleQuestionnaire"
                                                       ." FROM test t, utilisateur u, entreprise e, questionnaire q"
                                                       ." WHERE t.mailUtilisateur = u.mailUtilisateur AND u.nomEntreprise = '$nomEntreprise' AND e.nomEntreprise = '$nomEntreprise' And t.idTest = '$idTest' AND t.idQuestionnaire = q.idQuestionnaire");
            $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
            $enteteCsv = $statement->fetchAll ();
            return $enteteCsv;
        }

        /**
         *
         * @param unknown $idTest
         * @return unknown
         */
        public function getQuestionsReponses($idTest){
            $statement = $this->connection->prepare ( "SELECT DISTINCT q.libelleQuestion as b, q.axe as c, q.theme as d, r.valeur as g  FROM question q, reponse r"
                                                    . " WHERE r.idTest = '$idTest' AND q.idQuestion = r.idQuestion");
            $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
            $questionReponse = $statement->fetchAll ();
            return $questionReponse;
        }

        /**
         *
         * @param unknown $idTest
         * @return unknown
         */
        public function getAxeTheme($idTest){
            $statement = $this->connection->prepare ( "SELECT DISTINCT q.axe, q.theme, r.valeur FROM question q, reponse r"
                                                    . " WHERE r.idTest = '$idTest' AND q.idQuestion = r.idQuestion");
            $statement->execute () or die ( print_r ( $statement->errorInfo (), true ) );
            $questionReponse = $statement->fetchAll ();
            return $questionReponse;
        }

        /**
         *
         * @param unknown $email
         * @return unknown
         */
		public function getRole($email){
			$sql = "SELECT groupe FROM utilisateur WHERE mailUtilisateur = :mail";
			$statement = $this->connection->prepare ( $sql );
			$statement->execute ( array (
					"mail" => $email
			) );
			$role = $statement->fetch ();
			return $role ['0'];
		}

		/**
		 *
		 * @param unknown $idTest
		 * @param unknown $commentaire
		 */
		public function updateCommentaireAssocie($idTest, $commentaire) {
				$statement = $this->connection->prepare ( "UPDATE test SET commentaireUtilisateur = ? WHERE idTest = ?" );
		$statement->execute ( array (
				$commentaire,
				$idTest
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		}


		/**
		 *
		 * @param unknown $idTest
		 * @param unknown $commentaire
		 */
		public function updateCommentaireConsultant($idTest, $commentaire) {

			$statement = $this->connection->prepare ( "UPDATE test SET commentaireConsultant = ? WHERE idTest = ?" );
		$statement->execute ( array (
				$commentaire,
				$idTest
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
		}
                /**
                 *
                 * @param type $idTest
                 * @return type
                 */
                public function getCommentaire($idTest){
			$sql = "SELECT commentaireUtilisateur, commentaireConsultant from test WHERE idTest = ?";
			$statement = $this->connection->prepare ( $sql );
			$statement->execute ( array (
					$idTest
			) );
			$commentaires = $statement->fetch ();
			return $commentaires;
		}
}

?>
