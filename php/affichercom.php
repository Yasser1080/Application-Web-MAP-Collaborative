<?php
    extract($_POST);
    require "../php/connexionBD.php";
    $pdo = seConnecterBD();
    $commentaires = $pdo->prepare('SELECT * FROM commentaire WHERE idParc = ? ORDER BY idCom DESC');
    $commentaires->execute(array($id));

    $row = $commentaires->rowCount();

    if($row > 0){ 
        echo "Les avis du parc sont :<br>";
        while($data = $commentaires->fetch()){
            echo "\n\n",$data['Nom'], ' ', $data['Prenom'], ': ' , $data['Commentaire'],'<br>';
        }
        
    }
    else{
        echo "Pas d'avis sur le parc <br>Soyez le 1er a donner votre avis sur le parc";             
    }
