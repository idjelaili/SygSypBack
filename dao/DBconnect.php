<?php

/**
 * Gérer la connexion à la base
 *
 */

class DbConnect {

    private $connection;

    function __construct() {
    }

    /**
     * établissement de la connexion
     * @return gestionnaire de connexion de base de données
     */
    function connect() {
        $this->connection = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',DB_USERNAME,DB_PASSWORD );
        return $this->connection;
    }
}

?>
