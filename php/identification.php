<?php 
    require "connexionCompteBD.php";
    $login = $_POST['email'];
    $mdp = $_POST['pass'];
    $compte = rechercherCompte($login, $mdp);
    
    if($compte != null){
        include "../html/map.php";
    }
    else{
        header('Location:connexion.php?reg_err=mailormdp');
    }
?>
