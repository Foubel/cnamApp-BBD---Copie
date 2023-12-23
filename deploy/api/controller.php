<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once '../bootstrap.php';

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
		
		$queryParams = $request->getQueryParams();
		$queryBuilder = $catalogueRepository->createQueryBuilder('c');

	
		if (!empty($queryParams['id'])) {
			$queryBuilder->andWhere('c.id = :id')
						 ->setParameter('id', $queryParams['id']);
		}
		if (!empty($queryParams['name'])) {
			$queryBuilder->andWhere('c.name LIKE :name')
						 ->setParameter('name', '%' . $queryParams['name'] . '%');
		}
		if (!empty($queryParams['description'])) {
			$queryBuilder->andWhere('c.description LIKE :description')
						 ->setParameter('description', '%' . $queryParams['description'] . '%');
		}
		if (!empty($queryParams['price'])) {
			$queryBuilder->andWhere('CAST(c.price AS CHAR) LIKE :price')
						 ->setParameter('price', '%' . $queryParams['price'] . '%');
		}
	
		$catalogueItems = $queryBuilder->getQuery()->getResult();
		
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
	/*
	function getUtilisateur (Request $request, Response $response, $args) {
	    
	    $payload = getJWTToken($request);
	    $login  = $payload->userid;
	    
		$flux = '{"nom":"martin","prenom":"jean"}';
	    
	    $response->getBody()->write($flux);
	    
	    return addHeaders ($response);
	} */

	// APi d'authentification générant un JWT
	function postLogin(Request $request, Response $response, $args) {   
		global $entityManager;
		$data = $request->getParsedBody();

		$login = $data['login'] ?? "";
		$password = $data['password'] ?? "";

		$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
		$utilisateur = $utilisateurRepository->findOneBy(['login' => $login]);

		if ($utilisateur && password_verify($password, $utilisateur->getPassword())) {
			$userData = [
				'id' => $utilisateur->getId(),
				'nom' => $utilisateur->getNom(),
				'prenom' => $utilisateur->getPrenom()
			];

			$response = createJwt($response, $userData);
			$response = $response->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(json_encode($userData));
		} else {
			$response = $response->withStatus(401);
			$response = $response->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(json_encode(['error' => 'Login failed', $utilisateur]));
		}

		return addHeaders($response);
	}

	function postRegister(Request $request, Response $response, $args) {
		global $entityManager;
		$data = $request->getParsedBody();
		
		$nom = $data['nom'] ?? "";
		$prenom = $data['prenom'] ?? "";
		$adresse = $data['adresse'] ?? "";
		$codePostal = $data['codePostal'] ?? "";
		$ville = $data['ville'] ?? "";
		$email = $data['email'] ?? "";
		$sexe = $data['sexe'] ?? "";
		$login = $data['login'] ?? "";
		$password = $data['password'] ?? "";
		$telephone = $data['telephone'] ?? "";

		$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
		$utilisateur = $utilisateurRepository->findOneBy(['login' => $login]);

		if ($utilisateur) {
			$response = $response->withStatus(401);
			$response = $response->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(json_encode(['error' => 'Login already exists']));
		} else {
			$utilisateur = new Utilisateurs();
			$utilisateur->setNom($nom);
			$utilisateur->setPrenom($prenom);
			$utilisateur->setAdresse($adresse);
			$utilisateur->setCodePostal($codePostal);
			$utilisateur->setVille($ville);
			$utilisateur->setEmail($email);
			$utilisateur->setSexe($sexe);
			$utilisateur->setLogin($login);
			$utilisateur->setPassword(password_hash($password, PASSWORD_DEFAULT));
			$utilisateur->setTelephone($telephone);

			$entityManager->persist($utilisateur);
			$entityManager->flush();

			$userData = [
				'id' => $utilisateur->getId(),
				'nom' => $utilisateur->getNom(),
				'prenom' => $utilisateur->getPrenom()
			];

			$response = createJwt($response, $userData);
			$response = $response->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(json_encode($userData));
		}
		return addHeaders($response);
	}

	

