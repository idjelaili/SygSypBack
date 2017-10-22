<?php

require_once ('dao/impl/ResultDaoImpl.php');
require_once ('dao/impl/EntrepriseDaoImpl.php');
require_once ('utils/FonctionUtils.php');
require_once ('models/ResultTest.php');
require_once ('models/Reponse.php');
require_once ('dao/DBconnect.php');

class ResultService {

    private $infoGraph;
    private $tabPerformance;
    private $testDao;
    private $reponseDao;

    function __construct() {
        $this->testDao = new ResultDaoImpl();
        $this->reponseDao = new ReponseDaoImpl();
    }

    /**
     *
     * @param unknown $mailUtilisateur
     * @return string[]|unknown[]
     */
    public function getTestUtilisateur($mailUtilisateur) {
        if (empty($mailUtilisateur)) {
            $errMsg = 'Mail utilisateur vide';
            $error = 'error';
            return array(
                "status" => $error,
                "message" => $errMsg
            );
        }
        $result = $this->testDao->getTestUtilisateur($mailUtilisateur);
        if (empty($result)) {
            return array(
                "message" => "Vous n'avez pas encore répondu à un questionnaire !"
            );
        }
        return $result;
    }

    /**
     *
     * @param unknown $mailUtilisateur
     * @param unknown $role
     * @return string[]|unknown
     */
    public function getToutTestCollaborateurs($mailUtilisateur, $role) {
        if (empty($mailUtilisateur)) {
            $errMsg = 'Mail utilisateur vide';
            $error = 'error';
            return array(
                "status" => $error,
                "message" => $errMsg
            );
        }
        $nomEntreprise = $this->testDao->getNomEntrepriseDirigeant($mailUtilisateur);
        if ($role == "Dirigeant") {
            $result = $this->testDao->getToutTestCollaborateurs($nomEntreprise[0]["nomEntreprise"]);
        } else {
            return array(
                "message" => "Vous ne pouvez pas consulter les tests des collaborateurs"
            );
        }
        if (empty($result)) {
            return array(
                "message" => "Pas de test pour l'ensemble des collaborateurs de l'entreprise"
            );
        }
        return $result;
    }

    /**
     * Création d'un nouveau test utilisateur
     * @param unknown $testUtilisateur
     * @param unknown $mailDecodedToken mail Utilisateur
     */
    public function creerTestUtilisateur($reponseQuestionnaire, $mailDecodedToken) {
        if (empty($mailDecodedToken)) {
            $errMsg = 'Mail utilisateur vide';
            $error = 'error';
            return array(
                "status" => $error,
                "message" => $errMsg
            );
        }

        // Création de l'objet Test
        $testUtilisateurObj = new ResultTest ();
        $testUtilisateurObj->creerTest($reponseQuestionnaire);
        $testUtilisateurObj->setMailUtilisateur($mailDecodedToken);
        // Ajout du test en bd
        $lastIdTest = $this->testDao->creerTestUtilisateur($testUtilisateurObj);

        // insertion des reponses du questionnaire
        $this->creerReponseQuestionnaire($reponseQuestionnaire, $lastIdTest);
    }

    /**
     *
     * @param unknown $reponseQuestionnaire
     * @param unknown $lastIdTest
     */
    public function creerReponseQuestionnaire($reponseQuestionnaire, $lastIdTest) {

        $reponseQuestionObj = new Reponse ();
        $reponseQuestionObj->setIdTest($lastIdTest);
        for ($i = 0; $i < 27; $i ++) {
            $reponseQuestionObj->setIdQuestion($reponseQuestionnaire["listReponse"]["$i"]["idQuestion"]);
            $reponseQuestionObj->setValeur($reponseQuestionnaire ["listReponse"]["$i"]["valeur"]);
            // Ajout des reponses en bd
            $this->reponseDao->ajoutReponsesUtilisateur($reponseQuestionObj);
        }
    }

    /**
     *
     * @param type $nomEntreprise
     * @return string
     */
    public function getResultsEntreprise($nomEntreprise) {
        $retur_message = array();
        if (!empty($nomEntreprise)) {
            $entreprise = new EntrepriseDaoImpl();
            if ($entreprise->existeEntreprise($nomEntreprise)) {
                $result = $this->testDao->getResultEntreprise($nomEntreprise);
                $retur_message['status'] = 'success';
                $retur_message['message'] = $result;
            } else {
                $retur_message['status'] = 'error';
                $retur_message['message'] = "L'entreprise n'existe pas";
            }
        } else {
            $retur_message['status'] = 'error';
            $retur_message['message'] = "Le nom d'entreprise est vide";
        }
        return $retur_message;
    }

    /**
     *
     * @param unknown $idTest
     * @param unknown $nomEntreprise
     * @return string[]|string[]|unknown[]
     */
    public function getCsv($idTest, $nomEntreprise) {
        $retur_message = array();
        $csvResult = new ResultDaoImpl ();
        if (empty($idTest) || empty($nomEntreprise)) {
            $retur_message ['status'] = 'error';
            $retur_message ['message'] = 'vous devez renseigner tous les champs';
            return $retur_message;
        }
        $result = $csvResult->getQuestionsReponses($idTest);

        $csvEntete = $csvResult->getEnteteDoc($nomEntreprise, $idTest);
        $entete = array();
        $entete [0] = array(
            'a' => 'Nom Entrepreneur',
            'b' => $csvEntete [0] ['nomUtilisateur'] . ' ' . $csvEntete [0] ['prenomUtilisateur']
        );
        $entete [1] = array(
            'a' => 'Nom Société',
            'b' => $csvEntete [0] ['nomEntreprise']
        );
        $entete [2] = array(
            'a' => 'Date du test',
            'b' => $csvEntete [0] ['dateTest']
        );
        $entete [3] = array(
            'a' => 'Référence du test',
            'b' => $csvEntete [0] ['libelleQuestionnaire']
             
        );
        $entete [4] = array(
            'a' => '',
            'b' => ''
        );
        $entete [5] = array(
            'a' => 'Questions',
            'b' => 'Libelle Question',
            'c' => 'Axe',
            'd' => 'Theme',
            'e' => '',
            'f' => '',
            'g' => 'Note de 0 à 10*'
        );

        foreach ($result as $key => $value) {
            foreach ($value as $key2 => $value2) {

                if ($key2 == '0' || $key2 == '1' || $key2 == '2' || $key2 == '3') {

                    unset($result [$key] [$key2]);
                }
            }
        }
        $questionnaire = new QuestionnaireDaoImpl();
        $questionnaireInfo = $questionnaire->getQuestionnaireTest($idTest);
        
        $questionTab = array();
        $i = 1;
        foreach ($result as $key => $value) {
            $questionTab[$key]['a'] = 'Question ' . $i;
            $i++;
            $questionTab[$key]['b'] = $result[$key]['b'];
            $questionTab[$key]['c'] = $result[$key]['c'];
            $questionTab[$key]['d'] = $result[$key]['d'];
            $questionTab[$key]['e'] = '';
             $questionTab[$key]['f'] = '';
            $questionTab[$key]['g'] = $result[$key]['g'];
        }
        $footer = array();
        $footer [0] = array(
            'a' => '',
            'b' => ''
        );
        $footer [1] = array(
            'a' => '* : La note 0 correspond à non, très mauvais, très insatisfaisant, pas du tout… La note 10 correspond à oui, très bien, très insatisfaisant, tout à fait...',
            'b' => ''
        );
         $footer [2] = array(
            'a' => '',
            'b' => ''
        );
         $commentaire = $this->testDao->getCommentaire($idTest);
        $footer [3] = array(
            'a' => 'Commentaire Associé',
            'b' => $commentaire['commentaireUtilisateur']
        );
        

        $footer [4] = array(
            'a' => 'Commentaire Consultant',
            'b' => $commentaire['commentaireConsultant']
        );
        $result = array_merge($entete, $questionTab, $footer);
        $retur_message ['status'] = 'success';
        $retur_message ['message'] = $result;
        return $retur_message;
    }

    /**
     * 
     * @param unknown $resBusniss
     * @param unknown $resHumain
     * @return number
     */
    public function getPerformance($resBusniss, $resHumain) {
        $moyenne = ($resBusniss + $resHumain) / 2;
        if ($moyenne > 7) {
            return 3;
        } else if ($moyenne < 6) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * 
     * @param unknown $resBusniss
     * @param unknown $resHumain
     * @param unknown $compatibilite
     * @return number
     */
    public function getCompatibilite($resBusniss, $resHumain, $compatibilite) {
        $etendu = abs($resBusniss - $resHumain);
        $cgc = $compatibilite - $etendu / 2;
        if ($cgc > 7) {
            return 3;
        } else if ($cgc < 6) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     *
     * @param type $indice
     * @param type $value
     */
    public function organiserTableau($indice, $value) {

        switch ($value['axe']) {
            case 'Business':

                $this->infoGraph[$indice]['Business'] = $value['valeur'];

                break;
            case 'Humain':
                $this->infoGraph[$indice]['Humain'] = $value['valeur'];
                break;
            case 'Relation':
                $this->infoGraph[$indice]['Relation'] = $value['valeur'];
                break;
        }
    }

    /**
     * 
     * @return unknown
     */
    public function tabPerformance() {
        $tab = $this->infoGraph;
        foreach ($tab as $key => $value) {
            $this->tabPerformance[$key]['performance'] = $this->getPerformance($tab[$key]['Business'], $tab[$key]['Humain']);
            $this->tabPerformance[$key]['compatibilite'] = $this->getCompatibilite($tab[$key]['Business'], $tab[$key]['Humain'], $tab[$key]['Relation']);
        }
        return $this->tabPerformance;
    }

    /**
     *
     * @param type $idTest
     * @return type
     */
    public function infoGraphe($idTest) {
        $resultDao = new ResultDaoImpl ();
        $tab = $resultDao->getAxeTheme($idTest);
        foreach ($tab as $key => $value) {
            switch ($value['theme']) {
                case 'Leadership et vision':
                    $this->organiserTableau(0, $value);
                    break;
                case 'Projet d entreprise' :
                    $this->organiserTableau(1, $value);
                    break;
                case 'Gouvernance et organisation':
                    $this->organiserTableau(2, $value);
                    break;
                case 'Communication et collaboration':
                    $this->organiserTableau(3, $value);
                    break;
                case 'Définition de l offre':
                    $this->organiserTableau(4, $value);
                    break;
                case 'Modèle de revenu':
                    $this->organiserTableau(5, $value);
                    break;
                case 'Exécution et suivi':
                    $this->organiserTableau(6, $value);
                    break;
                case 'Agilité entrepreneuriale':
                    $this->organiserTableau(7, $value);
                    break;
                case 'Innovation et créativité':
                    $this->organiserTableau(8, $value);
                    break;
            }
        }
    }

    /**
     *
     * @param unknown $idTest
     * @param unknown $nomEntreprise
     * @param unknown $mailConsultant
     * @return NULL[]|unknown[]
     */
    public function infoPDF($idTest, $nomEntreprise, $mailConsultant) {
        $retur_message = array();
        if (!empty($nomEntreprise) && !empty($mailConsultant) && !empty($idTest)) {
            $pdfEntete = $this->testDao->getEnteteDoc($nomEntreprise, $idTest);
            $utlisateur = new UtilisateurDaoImpl ();
            $headerFooter = $utlisateur->getHeaderFooter($mailConsultant);
            $this->infoGraphe($idTest);
            $this->tabPerformance();
            $pdf = array();
            $pdfEntete [0] ['mailConsultant'] = $mailConsultant;
            $pdf ['0'] = $pdfEntete;
            $pdf ['1'] = $this->infoGraph;
            $pdf ['2'] = $this->tabPerformance;
            $pdf ['3'] = $headerFooter;
            return $pdf;
        }
    }

    /**
     *
     * @param unknown $session        	
     * @param unknown $infoCommentaire        	
     * @return string[]
     */
    public function updateCommentaireByRole($session, $infoCommentaire) {
        $result = array();
        if (empty($session)) {
            $result ['status'] = 'error';
            $result ['message'] = 'internal error';
            return $result;
        }
        $resultDAO = new ResultDaoImpl ();
        $role = $resultDAO->getRole($session ['email']);
        foreach ($infoCommentaire as $key => $value) {
            if (empty($value ['idTest'])) {
                $result ['status'] = 'error';
                $result ['message'] = 'tous les champs ne sont pas completes';
                return $result;
            }
            if ($role == 'Associe' || $role == 'Dirigeant') {
                $resultComment = $resultDAO->updateCommentaireAssocie($value ['idTest'], $value ['commentaire']);
            } else if ($role == 'Consultant') {
                $resultComment = $resultDAO->updateCommentaireConsultant($value ['idTest'], $value ['commentaire']);
            } else {
                $result ['status'] = 'error';
                $result ['message'] = 'Vous n\'avez pas le droit d\'effectuer cette modification';
                return $result;
            }
        }
        $result ['status'] = 'success';
        $result ['message'] = 'updated';
        return $result;
    }

}

?>
