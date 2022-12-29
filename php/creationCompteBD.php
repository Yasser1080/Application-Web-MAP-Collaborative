<?php
require_once "connexionBD.php";
$pdo = seConnecterBD ();

// Si les variables existent et qu'elles ne sont pas vides
if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['pass']))
{
    // Patch XSS
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['pass']);

    // On vérifie si l'utilisateur existe
    $check = $pdo->prepare('SELECT Nom, Prenom, Mail, Mdp FROM compte WHERE Mail = ?');
    $check->execute(array($email));
    $data = $check->fetch();
    $row = $check->rowCount();

    $email = strtolower($email); // on transforme toute les lettres majuscule en minuscule pour éviter que Foo@gmail.com et foo@gmail.com soient deux compte différents ..
    
    // Si la requete renvoie un 0 alors l'utilisateur n'existe pas 
    if($row == 0){ 
        if(preg_match('/^[a-zA-Z]+$/', $_POST['nom'])){    
            if (preg_match('/^[a-zA-Z]+$/', $_POST['prenom'])){
                // On insère dans la base de données
                $insert = $pdo->prepare('INSERT INTO compte(Nom, Prenom, Mail, Mdp) VALUES(:nom, :prenom, :email, :password)');
                $insert->execute(array(
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'password' => $password,
                ));
                // On redirige avec le message de succès
                header('Location:connexion.php?reg_err=success'); 
                die();
            }
            else{
                header('Location:creercompte.php?reg_err=prenom'); 
                die();
            }
        }
        else{
            header('Location:creercompte.php?reg_err=nom'); 
            die();
        }
    }
    else{ 
        header('Location:creercompte.php?reg_err=already'); 
        die();
    }
}