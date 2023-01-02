<?php 
  session_start();
  require_once '../php/connexionBD.php'; // ajout connexion bdd 
  $pdo = seConnecterBD();
  
  // si la session existe pas soit si l'on est pas connecté on redirige
  if(!isset($_SESSION['user'])){
      header('Location:../php/connexion.php');
      die();
  }
  // On récupere les données de l'utilisateur
  $req = $pdo->prepare('SELECT * FROM compte WHERE idCompte = ?');
  $req->execute(array($_SESSION['user']));
  $data = $req->fetch();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Avis sur les parcs du monde</title>
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
    />
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/tomik23/autocomplete@1.8.6/dist/js/autocomplete.min.js"></script>
    <!-- <link rel="stylesheet" href="style.css" /> -->


    <script src="../assets/js/jquery-3.6.1.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/Polyline.encoded.js"></script>
    <script src="../assets/js/MarkerClusterGroup.js"></script>
    <script src="https://kit.fontawesome.com/cf00848303.js" crossorigin="anonymous"></script>	

    <link rel="stylesheet" type="text/css" href="../assets/css/style_carte.css">
    <link rel="stylesheet" href="../assets/css/MarkerCluster.css" />
    <link rel="stylesheet" href="../assets/css/MarkeurClusterDefault.css" />
    <link rel="stylesheet" href="../assets/css/menu.css"/> 

    <script type="text/javascript">
      $(function(){
        $("#valideavis").on("click",function (){
              $.post("../php/ajouteravis.php",{idCOmpte : $("#compte").val(), nom : $("#nom").val(), prenom : $("#prenom").val(), id : $("#id").val(), parc : $("#parc").val(), com : $("#com").val()},function(data){
                alert(data);
                $("#com").val("");
                $("#h3avis").html("");
              });
              return false;
            });
        
      })
    </script>
  </head>

  <body>
    <div class="sidebar">
      <button aria-label="close sidebar" type="button" class="close-button">
        <svg>
          <use xlink:href="#icone-menu" />
        </svg>
      </button>

      <ul class="sidebar-menu">
        <li class="menu-item" data-item="menu">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
        </li>
        <li class="menu-item" data-item="person">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0S96 57.3 96 128s57.3 128 128 128zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
        </li>
        <li class="menu-item" data-item="itineraire">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 117.4L109.1 192H402.9l-26.1-74.6C372.3 104.6 360.2 96 346.6 96H165.4c-13.6 0-25.7 8.6-30.2 21.4zM39.6 196.8L74.8 96.3C88.3 57.8 124.6 32 165.4 32H346.6c40.8 0 77.1 25.8 90.6 64.3l35.2 100.5c23.2 9.6 39.6 32.5 39.6 59.2V400v48c0 17.7-14.3 32-32 32H448c-17.7 0-32-14.3-32-32V400H96v48c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V400 256c0-26.7 16.4-49.6 39.6-59.2zM128 288c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zm288 32c17.7 0 32-14.3 32-32s-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32z"/></svg>
        </li>
        <li class="menu-item" data-item="email" id="maiil">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M512 240c0 114.9-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6C73.6 471.1 44.7 480 16 480c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4l0 0 0 0 0 0 0 0 .3-.3c.3-.3 .7-.7 1.3-1.4c1.1-1.2 2.8-3.1 4.9-5.7c4.1-5 9.6-12.4 15.2-21.6c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208z"/></svg>
        </li>
        <li class="menu-item" data-item="settings">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com/ License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96C43 32 0 75 0 128V384c0 53 43 96 96 96h64c17.7 0 32-14.3 32-32s-14.3-32-32-32H96c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32h64zM504.5 273.4c4.8-4.5 7.5-10.8 7.5-17.4s-2.7-12.9-7.5-17.4l-144-136c-7-6.6-17.2-8.4-26-4.6s-14.5 12.5-14.5 22v72H192c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32H320v72c0 9.6 5.7 18.2 14.5 22s19 2 26-4.6l144-136z" /></svg>
        </li>
      </ul>
      <div class="sidebar-content">
        <div class="item-content" id="menu">
          <h2>BIENVENUE SUR MASEIB</h2>
          <div class="content">
            <h3>
              Bonjour, vous retrouverez ici une liste des fonctionnalitées disponibles sur notre
              site :<br><br>
              - naviguation sur la carte<br>
              - recherche d'adresse<br>
              - visualiser votre localisation<br>
              - itinéraire d'un point A à un point B<br>
              - visualiser les parcs d'attraction dans le monde entier<br>
              - laisser un avis sur un parc d'attraction<br>
              - visualiser les avis laissés par d'autres utilisateurs sur un parc<br>
            </h3>
          </div>
        </div>
        <div class="item-content" id="person">
          <h2>Profil</h2>
          <div class="content">
            <h3>Les informations de votre profil dans les services MASEIB</h3><br>
            <h4>N° du compte</h4>
            <input type="text" id="compte" name = "compte" value ="<?php echo $data['idCompte'] ?>" disabled><br><br>
            <h4>Nom</h4>
            <input type="text" id="nom" name = "nom" value ="<?php echo $data['Nom'] ?>" disabled><br><br>
            <h4>Prenom</h4>
            <input type="text" id="prenom" name = "prenom" value ="<?php echo $data['Prenom'] ?>" disabled><br><br>
            <h4>Mail</h4>
            <input type="text" id="mail" value ="<?php echo $data['Mail'] ?>" disabled><br><br>
            <h4>Mot de passe</h4>
            <input type="password" id="mdp" value ="<?php echo $data['Mdp'] ?>" disabled><br><br>
          </div>
        </div>

        <div class="item-content" id="itineraire">
          <h2>Itinéraire</h2>
          <div class="content">
            <h3>Pour calculez votre intinéraire, entrez votre point de depart, votre destination et votre methode de deplacement.</h3>
            <br>
            <h4>Point de départ</h4>
            <input type="text" id="depart" placeholder="Veuillez saisir votre point de départ :" value="" /><br><br>
            <h4>Point de destination</h4>
            <input type="text" id="destination" placeholder="Veuillez saisir votre point de destination :" value="" /><br><br>
            <div class="transport">
            <h4>Mode de transport</h4> <br>
            <form>
            <h4>Voiture</h4>  
            <input  type="radio" name="mode" value="driving" class="fld-mode" checked>
            <h4>Camion</h4>   
            <input type="radio" name="mode" value="trucking" class="fld-mode">
            <h4>Piéton</h4>   
            <input type="radio" name="mode" value="walking" class="fld-mode">
            </form><br>
            <input id="valider" type="submit" value="Itinéraire"><br><br>
            <input id="clear" type="submit" value="Effacer"><br><br>
            <h4 id="km"></h4>
            <br><h4 id="temps"></h4> 
            </div>
          </div>
        </div>
        
          <div class="item-content" id="email">
            <h2>Donner votre avis</h2>
            <div id="emaill" class="content">
              <h3 id="msg">Veuillez sélectionner votre parc</h3>
            </div>
            <div id="emailll" class="content">
              <form method="post">
                <h1 id ="nomparc" name="parc"></h1>
                <input type="text" id="id" name="id" placeholder="ID du parc" value="">
                <input type="text" id="parc" name="parc" placeholder="Nom du parc" value=""> <br>
                <textarea type="text" id="com" name="com" placeholder="Votre avis sur le parc" value="" rows="10" cols="52"></textarea>
                <br><br><input id="valideavis" type="submit" value="Valider"><br><br>
              </form>
              <input id="afficheravis" type="submit" name = "submit" value="Afficher les anciens avis"><br>
              <h3 id="h3avis"></h3>
            </div>
          </div>
        <div class="item-content" id="settings">
          <h2>Paramètre</h2>
          <div class="content">
            <form action="../php/deconnexion.php" method="post">
              <input id="deco" type="submit" value="Deconnexion"><br>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div id="map"></div>
  </body>
</html>
