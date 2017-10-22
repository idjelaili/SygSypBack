<?php 

require_once ('dao/DBconnect.php');
require_once ('dao/ReponseDao.php');
require_once('models/Reponse.php');

class ReponseDaoImpl implements ReponseDao {

	private $connection;

	function __construct() {
		$db = new DbConnect ();
		$this->connection = $db->connect ();
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see ReponseDao::ajoutReponsesUtilisateur()
	 */
	public function ajoutReponsesUtilisateur($responsesUtilisateur) {
		$requeteSQL = "INSERT INTO reponse (idQuestion, idTest, valeur) VALUES (?, ?, ?)";
		$statement = $this->connection->prepare ( $requeteSQL );
		$statement->execute ( array (
				$responsesUtilisateur->getIdQuestion (),
				$responsesUtilisateur->getIdTest (),
				$responsesUtilisateur->getValeur () 
		) ) or die ( print_r ( $statement->errorInfo (), true ) );
	}

}
?>