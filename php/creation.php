<?php
    require "creationCompteBD.php";
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['email'];
    $mdp = $_POST['pass'];

    newCompte($nom, $prenom, $mail, $mdp);

    // if($newcompte == true){
    //     include "../html/map.html";
    // }
    // else{
    //     echo "ok";
    // }

?>