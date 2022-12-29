<?php
session_start(); // Démarrage de la session
require "connexionBD.php";
$pdo = seConnecterBD();

if(!empty($_POST['email']) && !empty($_POST['pass'])) // Si il existe les champs email, password et qu'il sont pas vident
{
	// Patch XSS
	$email = htmlspecialchars($_POST['email']); 
	$password = htmlspecialchars($_POST['pass']);
	
	$email = strtolower($email); // email transformé en minuscule
	
	// On regarde si l'utilisateur est inscrit dans la table utilisateurs
	$check = $pdo->prepare('SELECT * FROM compte WHERE Mail = ?');
	$check->execute(array($email));
	$data = $check->fetch();
	$row = $check->rowCount();
	
	// Si > à 0 alors l'utilisateur existe
	if($row > 0)
	{
		// Si le mot de passe est le bon
		if($password === $data['Mdp'])
		{
			// On créer la session et on redirige sur landing.php
			$_SESSION['user'] = $data['idCompte'];
			header('Location: ../html/map.php');
			die();
		}
		else
		{ 
			header('Location: connexion.php?login_err=mailormdp'); 
			die(); 
		}
	}
	else
	{ 
		header('Location: connexion.php?login_err=noncompte'); 
		die(); 
	}
}
else
{ 
	header('Location: connexion.php'); 
	die();
} // si le formulaire est envoyé sans aucune données



































// function rechercherCompte($login,$mdp) {	
// 	try {
// 		// se connecter à la base de données
// 		$pdo = seConnecterBD();
// 		// préparer (compiler => générer code exécutable) la requête
// 		$sql="SELECT * FROM compte where Mail =:vallogin and Mdp =:valmdp"; 
// 		$stmt = $pdo->prepare($sql);		
// 		// initaliser la valeur des paramètres de la requête (avant son exécution)
// 		$stmt->bindParam(":vallogin", $login);
// 		$stmt->bindParam(":valmdp", $mdp);
// 		// exécuter la requête (par le au SGBD)
// 		$bool = $stmt->execute();	
// 		// récuéprer le résultat de la requête dans un tabeau associatif
// 		$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC); 
// 		// calculer le résultat de fonction (compte) à partir du résultat de la requête
// 		$compte = null;
		
// 		if (count($resultats) > 0){
// 			$compte = $resultats[0];	
// 		}		
// 		// fermer le curseur (libérer de la mémoire)
// 		$stmt->closeCursor();
// 		// retourner la réponse calculée (compte)
// 		return $compte;
// 	}	
// 	catch (PDOException $e) {
// 		// Erreur à l'exécution de la requête 
// 		$erreur = $e->getMessage();
// 		echo utf8_encode("Erreur d'accès à la base de données : $erreur \n");		
// 		return null;
// 	}
// }
// ?>