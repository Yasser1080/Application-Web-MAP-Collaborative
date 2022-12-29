<?php
    extract($_POST);
    require "../php/connexionBD.php";
    $pdo = seConnecterBD();

    if(!empty($com)){

        $check = $pdo->prepare('SELECT idParc, Nom FROM parc WHERE idParc = ?');
        $check->execute(array($id));
        $data = $check->fetch();
        $row = $check->rowCount();

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

            echo "Avis enregistrer :)";
        
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
            echo "Avis enregistrer :)";

        }
    }
    else{
        echo "Veuillez remplir le champ pour émettre un avis";
    }

