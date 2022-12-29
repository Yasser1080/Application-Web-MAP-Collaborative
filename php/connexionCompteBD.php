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
	
	// On regarde si l'utilisateur est inscrit dans la table compte
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
			// On créer la session et on redirige sur map.php
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