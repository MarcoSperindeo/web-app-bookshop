<!DOCTYPE html>
<html lang="it">
<head>
<?php
	require_once ("head.html");  /*  due comandi include e require producono il medesimo risultato; l'unica differenza consiste nella gestione 
	                                 di eventuali errori: nel caso il file da includere non si trovato include() genererà un warning 
									 mentre require() un fatal error (bloccando, di fatto, l'esecuzione dello script). */
	require_once("sessione.php"); 
?>
  </head>

  <body>
    <?php
	
	   include_once("utente.php"); /* La funzione include_once è identica a include con l'unica differenza che prima di includere il file verifica   
	                                * che questo non sia già stato precedentemente incluso nella pagina ed, in tal caso, non fa nulla.          */
	   include_once("menu.php"); 
	   
     ?>
	 
     <div id="contenuto">
		<p>Benvenuto su My Bookshop! La nuova piattaforma web per la compravendita di libri fra privati.</p>
		<p>Autenticati tramite la pagina <a href="login.php">Login</a>, quindi esamina i libri in vendita alla pagina <a href="info.php">Info</a>.
		   Ti sarà inoltre possibile, solamente una volta autenticato, <a href="acquista.php">acquistare libri</a>, <a href="cambia.php">mettere in vendita</a>
		   un nuovo libro o <a href="cambia.php">modificare la quantit&agrave;</a>  di un libro da te messo in vendita.
		</p>
		<p>Ricordati di effettuare il <a href="logout.php">Logout</a> al termine dell'utlizzo del sito.
		</p>
     </div>



<?php 
    require_once("menu.php");
    include_once("utente.php");
    include_once("footer.php") ?>
  </body>


  </html>
