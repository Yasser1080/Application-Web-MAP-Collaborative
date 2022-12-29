<?php
require_once "../php/connexionBD.php";
$pdo = seConnecterBD ();

// Si les variables existent et qu'elles ne sont pas vides
if(!empty($_POST['com'])){
    // Patch XSS
    $idCOmpte = htmlspecialchars($_POST['idcompte']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $id = htmlspecialchars($_POST['id']);
    $parc = htmlspecialchars($_POST['parc']);
    $com = htmlspecialchars($_POST['com']);

    // On vérifie si l'utilisateur existe
    $check = $pdo->prepare('SELECT idParc, Nom FROM parc WHERE idParc = ?');
    $check->execute(array($id));
    $data = $check->fetch();
    $row = $check->rowCount();

    // Si la requete renvoie un 0 alors l'utilisateur n'existe pas 
    if($row == 0){ 
      $insert = $pdo->prepare('INSERT INTO parc(idParc, Nom) VALUES(:idparc, :nom)');
      $insert->execute(array(
          'idparc' => $id,
          'nom' => $parc,
      ));

      $insert = $pdo->prepare('INSERT INTO commentaire(idCompte, idParc, Nom, Prenom, Commentaire) VALUES(:idcompte, :idparc, :nom, :prenom, :commentaire)');
      $insert->execute(array(
          'idcompte' => $idCOmpte,
          'idparc' => $id,
          ':nom' => $nom,
          ':prenom' => $prenom,
          'commentaire' => $com,
      ));
      // On redirige avec le message de succès
      //header('Location:creercompte.php?reg_err=success');
      header('Location:map.php?reg_err=success'); 
      die();
    } 
    else{
      $insert = $pdo->prepare('INSERT INTO commentaire(idCompte, idParc, Nom, Prenom, Commentaire) VALUES(:idcompte, :idparc, :nom, :prenom, :commentaire)');
      $insert->execute(array(
          'idcompte' => $idCOmpte,
          'idparc' => $id,
          ':nom' => $nom,
          ':prenom' => $prenom,
          'commentaire' => $com,
      ));
      // On redirige avec le message de succès
      //header('Location:creercompte.php?reg_err=success');
      header('Location:map.php?reg_err=success'); 
      die();
    }  

}