<?php
require "connexionBD.php";
function newCompte($nom, $prenom, $mail, $mdp) {	
    //On définit le handler d'erreur
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	try {
        // se connecter à la base de données
        $pdo = seConnecterBD();
        // préparer (compiler => générer code exécutable) la requête
        $sql="INSERT INTO compte (Nom,Prenom,Mail,Mdp) VALUES (:valNom, :valPrenom, :valMail, :valmdp)"; 
        $stmt = $pdo->prepare($sql);		
        // initaliser la valeur des paramètres de la requête (avant son exécution)
        $stmt->bindParam(":valNom", $nom);
        $stmt->bindParam(":valPrenom", $prenom);
        $stmt->bindParam(":valMail", $mail);
        $stmt->bindParam(":valmdp", $mdp);
        // exécuter la requête (par le au SGBD)
        // $bool = $stmt->execute();	
        $dbh->exec($sql); 

        // $errors = array('nom'=>'', 'prenom'=>'', 'mail'=>'' 'mdp'=>'');

        // if(empty($_POST['nom'])){
        //     echo 'A title is required <br />';
        // } 
        // else {
        //     $title = $ POST['nom'];
        //         if(!preg_match('/*[a-zA-Z\s]+$/', $title)){
        //             $errors['title'] = 'Title must be letters and spaces only';
        //         }
        // }

        // if(empty($_POST['prenom'])){
        //     echo 'A title is required <br />';
        // } 
        // else {
        //     $title = $ POST['prenom'];
        //         if(!preg_match('/*[a-zA-Z\s]+$/', $title)){
        //             $errors['title'] = 'Title must be letters and spaces only';
        //         }
        // }

	}	
	catch (PDOException $e) {
		// Erreur à l'exécution de la requête 
		$erreur = $e->getMessage();
		echo utf8_encode("Erreur d'accès à la base de données : $erreur \n");		
		return null;
	}
}
?>