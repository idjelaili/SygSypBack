<?php

require ("vendor/autoload.php");

require_once ('dao/config.php');
require_once ('dao/DBconnect.php');
require_once ('dao/UtilisateurDao.php');

require_once ("dao/impl/UtilisateurDaoImpl.php");
require_once ("dao/impl/QuestionDaoImpl.php");
require_once ("dao/impl/ResultDaoImpl.php");
require_once ("dao/impl/EntrepriseDaoImpl.php");
require_once ("dao/impl/ReponseDaoImpl.php");
require_once ("dao/impl/QuestionnaireDaoImpl.php");

require_once ("services/ResultService.php");
require_once ("services/QuestionnaireService.php");
require_once ("services/UtilisateurService.php");
require_once ("services/QuestionService.php");
require_once ("services/ReponseService.php");
require_once ("services/EntrepriseService.php");

require_once ('models/Utilisateur.php');

require_once ("security/Authenticate.php");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
$app = new \Slim\App ( [
		'settings' => [
				'displayErrorDetails' => false,
				'addContentLengthHeader' => false
		]
] );

/**
 * *******************************
 */
/**
 * **** API CODE INTERCEPTOR ****
 */
/**
 * *****************************
 */
$requestInterceptor = function ($request, $response, $next) {
	$token = $request->getHeader ( 'HTTP_AUTHORIZATION' );
	if (empty ( $token )) { // Non authentifie
		$newResponse = $response->withStatus ( 401 );
		return $newResponse;
	}
	$auth = new Authenticate ();

	$auth->setToken ( $token [0] );
	$tokenDecoded = $auth->verifyToken ();
	if (empty ( $tokenDecoded )) { // Non autorise..tentative de hack ?
		$newResponse = $response->withStatus ( 403 );
		$newResponse->getBody ()->write ( 'Error Invalid Token.' );
		return $newResponse;
	}
	if ($tokenDecoded == 'timeout') { // timeout
		$newResponse = $response->withStatus ( 401 );
		$newResponse->getBody ()->write ( 'timeout' );
		return $newResponse;
	}
	// print_r($tokenDecoded);die();
	$request = $request->withAttribute ( 'session', $tokenDecoded );
	$response = $next ( $request, $response );

	return $response;
};

/**
 */
$app->get('/tokenRefresh', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    if (empty($session)) { // Non authentifie
        $newResponse = $response->withJson('No token found.', 401);
        return $newResponse;
    }
    $auth = new Authenticate ();
    $resu = $auth->refreshToken($session);
    if ($resu == 'timeout') {
        $newResponse = $response->withStatus(401);
        $newResponse->getBody()->write('timeout');
        return $newResponse;
    }
    $newResponse = $response->withJson($resu, 200);
    return $newResponse;
});

/**
 */
$app->get('/api', function (Request $request, Response $response, $tokenData) {
    // $tokenData = $response->getParsedBody ();
    print_r($request->getAttribute('session'));
    die();
    $newResponse = $response->withJson($tokenData);
    return $newResponse;
    echo 'coucou';
})->add($requestInterceptor);

/**
 */
$app->get('/connection', function (Request $request, Response $response) {
    $data = $request->getQueryParams();
    $userService = new UtilisateurService ();
    $result = $userService->getConnectionForUser($data);
    if (null != isset($result ['status'])) {
        $newResponse = $response->withJson($result ['message'], 401);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
});

/** get user email
 */
$app->get('/user', function (Request $request, Response $response) {
		$session = $request->getAttribute('session');
		if(empty($session)){
			$newResponse = $response->withJson('Error token', 401);
			return $newResponse;
		}
    $newResponse = $response->withJson($session['email']);
    return $newResponse;
})->add($requestInterceptor);

/** Update user email
 */
$app->post('/user', function (Request $request, Response $response) {
		$session = $request->getAttribute('session');
		$data = $request->getParsedBody();
		$userService = new UtilisateurService ();
		$result = $userService->updateUserMail($data, $session);
		if ($result ['status'] == 'error') {
				$newResponse = $response->withJson($result ['message'], 400);
				return $newResponse;
		}
		$newResponse = $response->withJson($result ['message'], 200);
		return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->post('/parameter/password', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getParsedBody();
    $userService = new UtilisateurService ();
    $result = $userService->updateUserPassword($data, $session);
    if ($result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result ['message'], 200);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->get('/parameter/names', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $userService = new UtilisateurService ();
    $result = $userService->getUserName($session);
    if ($result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result ['message'], 200);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->post('/parameter/names', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getParsedBody();
    $userService = new UtilisateurService ();
    $result = $userService->updateUserName($session, $data);
    if ($result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result ['message'], 200);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->get('/parameter/company', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $userService = new EntrepriseService ();
    $result = $userService->getCompanyInfo($session);
    if ($result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result ['message'], 200);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->post('/parameter/company', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getParsedBody();
    $userService = new EntrepriseService ();
    $result = $userService->updateCompanyInfo($session, $data);
    if ($result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result ['message'], 200);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->post('/questionnaire', function (Request $request, Response $response) {
    $tokenData = $request->getParsedBody();
    $newResponse = $response->withJson($tokenData);
    return $newResponse;
    $userService = new UtilisateurService ();
    $result = $userService->createUser($data);
    return $result;
})->add($requestInterceptor);

/**
 * Route inscription
 */
$app->post('/inscription', function (Request $request, Response $response) {
    $infoUtilisateur = $request->getParsedBody();
    $userService = new UtilisateurService ();
    $result = $userService->creerUtilisateurService($infoUtilisateur);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
});

/**
 * Route createEntreprise
 */
$app->post ( '/createEntreprise', function (Request $request, Response $response) {
	$session = $request->getAttribute ( 'session' );
	$infoUtilisateur = $request->getParsedBody ();
	$userService = new UtilisateurService ();
	$result = $userService->creerCompteDirigeant ( $infoUtilisateur, $session["email"] );
	if (null != isset ( $result ['status'] ) && $result ['status'] != 'success') {
		$newResponse = $response->withJson ( $result ['message'], 400 );
		return $newResponse;
	}
	$newResponse = $response->withJson ( $result );
})->add ( $requestInterceptor );

/**
 * Route commentaire
 */
$app->post ( '/commentaire', function (Request $request, Response $response) {
	$session = $request->getAttribute ( 'session' );
	$infoCommentaire = $request->getParsedBody ();
	$resultService = new ResultService ();
	$role = $resultService->updateCommentaireByRole ( $session , $infoCommentaire );
	if ($role['status'] == 'error') {
		$newResponse = $response->withJson ( $role ['message'], 400 );
		return $newResponse;
	}
	$newResponse = $response->withJson ( $role ['message'], 200 );
	return $newResponse;
} )->add ( $requestInterceptor );


/**
 * Route gérer consultant
 */
$app->post ( '/gererConsultant', function (Request $request, Response $response) {
	//$session = $request->getAttribute('session');
	$infoUtilisateur = $request->getParsedBody ();
	$userService = new UtilisateurService ();
	$result = $userService->updateConsultantActive ( $infoUtilisateur );
	if (null != isset ( $result ['status'] ) && $result ['status'] != 'success') {
		$newResponse = $response->withJson ( $result ['message'], 400 );
		return $newResponse;
	}
	$newResponse = $response->withJson ( $result );
	return $newResponse;
} );

/**
 *
 */
$app->get('/visualiseResult', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $resultService = new ResultService ();
    $result = $resultService->getTestUtilisateur($session ["email"]);
    if (null != isset($result ['status'])) {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 * question
 */
$app->get('/question', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $questionService = new QuestionService ();
    $result = $questionService->getQuestionUtilisateur($session ["email"]);
    if (null != isset($result ['status'])) {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 * Route question
 */
$app->post('/question', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $testQuestion = $request->getParsedBody();
    $resultService = new ResultService ();
    $result = $resultService->creerTestUtilisateur($testQuestion, $session ["email"]);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 * Liste déroulante consultants formulaire inscription
 */
$app->get('/inscription', function (Request $request, Response $response) {
    $utilisateurDaoImpl = new UtilisateurDaoImpl ();
    $result = $utilisateurDaoImpl->getInfoConsultant();
    return $response->withJson($result);
});

/**
 * Liste déroulante consultants formulaire inscription
 */
$app->get('/getConsultantActif', function (Request $request, Response $response) {
    $utilisateurDaoImpl = new UtilisateurDaoImpl ();
    $result = $utilisateurDaoImpl->getInfoConsultantActif();
    return $response->withJson($result);
});

/**
 * Récupérer l'ensemble des tests entreprise
 */
$app->get('/testsCollaborateurs', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $resultService = new ResultService ();
    $result = $resultService->getToutTestCollaborateurs($session ["email"], $session ["groupe"]);
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 */
$app->get('/initialiserMdp', function (Request $request, Response $response) {
    $data = $request->getQueryParams();
    $userService = new UtilisateurService ();
    $result = $userService->initialiserMdp($data ['email']);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
});

/**
 */
$app->post('/attacherEntreprise', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getParsedBody();
    $entrepriseService = new EntrepriseService ();
    $result = $entrepriseService->setConsultantEntreprise($session ["email"], $data ['nomEntreprise']);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->get('/getHeaderFooter', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $userService = new UtilisateurService ();
    $result = $userService->getHeaderFooter($session ["email"]);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->post('/setHeaderFooter', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getParsedBody();

    $userService = new UtilisateurService ();
    $result = $userService->setHeaderFooter($data ['header'], $data ['footer'], $session ["email"]);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }

    $newResponse = $response->withJson($result);
    return $newResponse;
})->add($requestInterceptor);

/**
 */
$app->get('/getEntreprises', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $entrepriseDaoImpl = new EntrepriseDaoImpl ();
    $result = $entrepriseDaoImpl->getEntreprises($session ["email"]);
     if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 */
$app->get('/getAllEntreprisesAtribue', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $entrepriseDaoImpl = new EntrepriseDaoImpl ();
    $result = $entrepriseDaoImpl->getAllEntrepriseAttribue($session ["email"]);
     if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 *
 */
$app->get('/questionnaires', function (Request $request, Response $response) {
    $questionnaireDaoImpl = new QuestionnaireDaoImpl ();
    $result = $questionnaireDaoImpl->getQuestionnaires();
    return $response->withJson($result);
});

/**
 *
 */
$app->get('/getResultsEntreprise', function (Request $request, Response $response) {
    $data = $request->getQueryParams();
    $resultService = new ResultService ();
    $result = $resultService->getResultsEntreprise($data ['nomEntreprise']);
     if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 *
 */
$app->post('/setQuestionnaireEntreprise', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $questionnaireService = new QuestionnaireService ();
    $result = $questionnaireService->setQuestionnaire($data ['idQuestionnaire'], $data ['nomEntreprise']);
     if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 * Récupération CSV
 */
$app->get('/getCsv', function (Request $request, Response $response) {
    $data = $request->getQueryParams();
    $idTest = $data ['idTest'];
    $nomEntreprise = $data ['nomEntreprise'];
    $csvResult = new ResultService($idTest, $nomEntreprise);
    $result = $csvResult->getCsv($idTest, $nomEntreprise);
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 *
 */
$app->get('/getPdf', function (Request $request, Response $response) {
    $session = $request->getAttribute('session');
    $data = $request->getQueryParams();
    if($session['groupe'] == 'Consultant')
    {
        $mailConsultant =  $session ['email'];
        $nomEntreprise = $data ['nomEntreprise'];
    }
    else{
        $user = new UtilisateurDaoImpl();
       // var_dump($session);die();
        $nomEntreprise = $session ['company'];
        $mailConsultant = $user->getConsultantUser($session ['email']);
    }
    $resultService = new ResultService ();

    $result = $resultService->infoPDF($data ['idTest'],$nomEntreprise , $mailConsultant);
     if (null != isset($result ['status']) && $result ['status'] == 'error') {
        $newResponse = $response->withJson($result ['message'], 404);
        return $newResponse;
    }
    return $response->withJson($result);
})->add($requestInterceptor);

/**
 *
 */
$app->post('/chargerQuestionnaire', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $keysData = array_keys($data);
    $firstData = $keysData[0];
    $keyClasseur = array_keys($data[$firstData]);
    $firstClasseur = $keyClasseur[0];
    $infoQuestionnaire = $data[$firstData][$firstClasseur][0];

    $questionnaireService = new QuestionnaireService($infoQuestionnaire);
    $idQuestionnaire = $questionnaireService->ajouterQuestionnaire($infoQuestionnaire);
    $result = array();
    if ($idQuestionnaire > 0 && ! isset($idQuestionnaire['status'])) {
        $questionService = new QuestionService ();
        $questionService->ajouterquestion($data[$firstData][$firstClasseur], $idQuestionnaire);
        $result ['status'] = 'success';
        $result ['message'] = 'Upload Completé';
    } else {
        $result = $idQuestionnaire;
    }
    if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }
    return $response->withJson($result['message']);
})->add($requestInterceptor);

/**
 *
 */
$app->post('/defautQuestionnaire', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $questionnaireService = new QuestionnaireService ();
    $result = array();
    $result =  $questionnaireService->setDefautQuestionnaire($data['idQuestionnaire'], $data['defaut']);

     if (null != isset($result ['status']) && $result ['status'] != 'success') {
        $newResponse = $response->withJson($result ['message'], 400);
        return $newResponse;
    }

    return $response->withJson($result);
})->add($requestInterceptor);


$app->run();
?>
