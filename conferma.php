<?php
require_once("sessione.php");

if(!isset($_SESSION["UTENTE"])){
	echo "<!doctype html>";
	echo "<html>";
	echo "<head>";
	require_once("head.html");
    echo "</head>";
	echo "<body>";
	require_once("menu.php");
	echo "<div id='contenuto'> <p>Attenzione! Devi essere autenticato per poter accedere a questa pagina. Effettua il <a href=\"login.php\"> Log In </a> !</p></div>";
    include_once("utente.php");
	include_once("footer.php");
	echo "</body>";
	echo "</html>";
	return;
	}
?>

<!doctype html>
<html lang='it'>

<head>
<?php require_once ("head.html")?>
</head>
 
<body>

<?php
require_once("menu.php");
include_once("utente.php");

   
$errori = array();

$stampa_tab = true;
	
if(count($_REQUEST)==0){ // Se non è stato compilato il form
  $stampa_tab = false;

  $errori[] = "<div class='erroreacquisto'> Attenzione! Compilare prima il form degli acquisti alla pagina <a href=\"acquista.php\"> Acquista </a>
             oppure tornare alla Homepage <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";

  include_once("utente.php");
  include_once("footer.php");
       
  echo "</body>";
  echo "</html>";
		
  return;
}



$prodotti_acquistati= array();
$tot = 0;

if (isset($_SESSION["prodotti_acquistabili"])){ // Se ci sono prodotti acquistabili dall'utente (vv. pg. "Acquista")

   foreach ($_SESSION["prodotti_acquistabili"] as  $p) {
      $pID = $p["pid"];
      $max_qty = $p["qty"];

      if(!isset($_REQUEST[$pID]) || $_REQUEST[$pID]>$max_qty ){ // Metodo GET: trasmissione parametri del form tramite URI ==> CONTROLLO MANOMISSIONE URI:  
	                                                            // controllo che non si stia cercando di acquistare un prodotto non disponibile tra i prodotti acquistabili 
                                                                // dall'utente e che non ne se ne sia selezionata una quantità maggiore di quella massima
																// Se il controllo va a buon fine, per ogni prodotto creo l'array associtivo $prodotto[] dove 
																// salvo il nome, la quantità, il prezzo e il prezzo totale.
																// Successivamente salvo ogni $prodotto nell'array di array $prodotti_acquistabili.
        if($stampa_tab == true){
		    $stampa_tab = false;
            $errori[] = "<div class='erroreacquisto'>I parametri nella URI sono stati modificati.
                 Per acquistare torna alla pagina <a href=\"acquista.php\">acquista</a> oppure torna alla home page
                 <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";
        }
			
        } elseif ($_REQUEST[$pID]>0) { // CONTROLLO  che la quantità selezionato del prodotto sia maggiore di 0
			
             $prodotto["pID"] = $p["pid"];
			 $prodotto["vID"] = $p["vid"];
             $prodotto["nome"] = $p["nome"];
             $prodotto["quantita"] = $_REQUEST[$pID];
             $prodotto["prezzo"] = (float) ($p["prezzo"]/100);
             $prodotto["prezzoTOT"] = (float) ($p["prezzo"]*$_REQUEST[$pID]/100);
             $prodotto["venditore"] = $p["vend"];
             $prodotti_acquistati[] = $prodotto;
        }
    }

   foreach ($prodotti_acquistati as $prodotto) {
      $tot =(float)($tot + $prodotto["prezzoTOT"]);
    }

   if($tot > $_SESSION["UTENTE"]["money"]){ // CONTROLLO SE L'UTENTE HA ABBASTANZA SOLDI PER EFFETTURARE IL PAGAMENTO
        if($stampa_tab == true){
           $stampa_tab = false;
           $errori[] = "<div class='erroreacquisto'>Spiacente, non hai abbastanza soldi nel tuo borsellino elettronico <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";
        }
    }

   if(count($prodotti_acquistati) == 0){ 
        if($stampa_tab == true){
           $stampa_tab = false;
           $errori[] = "<div class='erroreacquisto'>Attenzione, non hai selezionato nessun libro! <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";
        }
    }


// SE SONO STATI SUPERATI I CONTROLLI (SE L'UTENTE HA SELEZIONATO DELLE QUANTITA MAGGIORI DI 0 E INFERIORI ALLA DISPONIBILITA', 
// SE NON HA CAMBIATO LA URI E HA ABBASTANZA CREDITO PER PROCEDERE CON L'ACQUISTO) ALLORA STAMPO LA TABELLA RIASSUNTIVA DELL'ORDINE ( CARRELLO)
   if($stampa_tab == true){
      echo "<table>";
      echo "<caption> <p>Riepilogo ordine</p> </caption>";
      echo "<thead> <tr> <th> LIBRO </th>  <th> QUANTIT&Agrave;</th> <th> PREZZO UNITARIO </th> <th> PREZZO </th></tr> </thead> <tbody>";

      foreach ($prodotti_acquistati as  $prodotto) {                                                                            
         printf("<tr> <td>%s</td> <td>%s</td> <td>%s&euro;</td> <td>%s&euro;</td> </tr> ", $prodotto["nome"],$prodotto["quantita"],
		          number_format($prodotto["prezzo"],2),number_format($prodotto["prezzoTOT"],2));
        }
      echo "<tfoot>  <tr> <td colspan='2'>Importo totale</td> <td colspan='2'>".number_format($tot,2)."&euro;</td> </tr>  </tfoot>  </tbody> </table>";
    }
}
// Considero il caso in cui nella URI ci siano tutti prodotti acquistabili ma che non sono stati selezionati dalla pagina acquista.php
else{
	
  if($stampa_tab == true){
      $stampa_tab = false;
      $errori[] = "<div class='erroreacquisto'>Per acquistare devi prima selezionare i libri e le quantit&agrave; nella pagina
                 <a href=\"acquista.php\"> acquista</a> oppure tona alla home page <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";
    }
}

if(count($errori)!=0){
    require_once("errori.php");
}

else if($stampa_tab == true && count($errori) == 0){
    
// Se non si verificano errori, salvo l'array $prodotti_acquistati in una variabile di sessione, in modo da poter delegare
// l'acquisto alla pagina finale.php (che sottrae i soldi al borsellino dell'utente).
// CAMPO NASCOSTO DEL FORM: fa sì che venga inviata a finale.php una variabile che identifica univocamente la transazione, in modo che non sia possibile duplicarla.

    $_SESSION["prodotti_acquistati"] = $prodotti_acquistati;
    $_SESSION["id_transazione"] = uniqid();

    echo "<form name=\"paga\" method=\"GET\" action=\"finale.php\">";
    echo "<input type=\"hidden\" name=\"id_transazione\" value=\"".$_SESSION["id_transazione"]."\">";
    echo "<div class='centered'>";
    echo "<input type=submit name=\"inoltra\" value=\"OK\" class=\"button\">"; 
    echo "<a href=\"home.php\" class=\"button\">ANNULLA</a> </label>";
    echo "</div> ";
    echo "</form> ";
}

include_once("footer.php");
?>

 </body>
 </html>