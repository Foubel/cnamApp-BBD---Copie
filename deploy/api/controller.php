<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once 'bootstrap.php';

	function optionsCatalogue (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	function hello(Request $request, Response $response, $args) {
	    $array = [];
	    $array ["nom"] = $args ['name'];
	    $response->getBody()->write(json_encode ($array));
	    return $response;
	}

	// API Nécessitant un Jwt valide
	// API pour obtenir le catalogue
	function getCatalogue(Request $request, Response $response, $args) {
		global $entityManager;
		$catalogueRepository = $entityManager->getRepository('Catalogue');
		$catalogueItems = $catalogueRepository->findAll();

		$catalogueArray = [];
		foreach ($catalogueItems as $item) {
			$catalogueArray[] = [
				'id' => $item->getId(),
				'name' => $item->getName(),
				'description' => $item->getDescription(),
				'price' => $item->getPrice(),
			];
		}

		$response->getBody()->write(json_encode($catalogueArray));
		return addHeaders($response);
	}

	
	function optionsUtilisateur (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	// API Nécessitant un Jwt valide
	function getUtilisateur (Request $request, Response $response, $args) {
	    
	    $payload = getJWTToken($request);
	    $login  = $payload->userid;
	    
		$flux = '{"nom":"martin","prenom":"jean"}';
	    
	    $response->getBody()->write($flux);
	    
	    return addHeaders ($response);
	}

	// APi d'authentification générant un JWT
	function postLogin (Request $request, Response $response, $args) {   
	    
		$flux = '{"nom":"martin","prenom":"jean"}';
	    
	    $response = createJwT ($response);
	    $response->getBody()->write($flux );
	    
	    return addHeaders ($response);
	}

