<?php
require_once("sessione.php");
?>
 <!doctype html>
 <html lang='it'>
   <head>
   <?php require_once ("head.html")?>
   </head>

   <body>
      <?php
	      require_once("menu.php");

			if(!isset($_SESSION["UTENTE"])){

				echo " <div id='contenuto'>Attenzione! E' necessario essere autenticati per poter accedere a questa pagina.</div>";

            } else{
                echo"<div id='contenuto'><p>Il libro è stato messo in vendita con successo! Torna alla pagina <a href=\"cambia.php\"> Cambia </a></p></div>";
            }
  	      include_once("utente.php");
          include_once("footer.php");
	?>

   </body>
   </html>