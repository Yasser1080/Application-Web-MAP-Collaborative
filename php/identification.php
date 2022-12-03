<?php 
    require "connexionCompteBD.php";
    $login = $_POST['email'];
    $mdp = $_POST['pass'];
    $compte = rechercherCompte($login, $mdp);
    
    if($compte != null){
        include "../html/map.html";
    }
    else{
        include "../html/connexionNoOk.html";
    }
?>
