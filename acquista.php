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
     echo " <div id='contenuto'> <p>Attenzione! Devi essere autenticato per poter accedere a questa pagina. Effettua il <a href=\"login.php\"> Log In </a> !</p></div>";
	 include_once("utente.php");
	 include_once("footer.php");
     echo"</body>";
     echo"</html>";
     return;
	}
	
?>

<!doctype html>
<html lang='it'>

<head>
<?php require_once("head.html")?>
</head>

<body>

<noscript> Abilitare JavaScript </noscript>

 <?php
   
   require_once("menu.php");
   include_once("utente.php");


  // STAMPO LA TABELLA DEI PRODOTTI ACQUISTABILI/IN VENDITA (TUTTI I PRODOTTI DI QUANTITA' NON NULLA ESCLUSI QUELLI VENDUTI DALL'UTENTE LOGGATO)
	
    $prodotti_acquistabili = array(); 
	
    $errori = array();

    $connessione = mysqli_connect("localhost","uWeak","posso_solo_leggere","comprovendo"); //utente con privilegi di sola lettura
	
    if ( mysqli_connect_errno()!=0){
      $errori[]="errore - collegamento al DB impossibile: ". mysqli_connect_error();
    }
    else{

    $query= "SELECT * from prodotti WHERE qty>0 and vend<>?"; // nella query si vanno a selezionare tutti i prodotti di quantità non nulla esclusi quelll venduti dall'utente stesso
    $stmt = mysqli_prepare( $connessione, $query);

    if($stmt){
      mysqli_stmt_bind_param($stmt,"s",$_SESSION["UTENTE"]["nick"]);

      if(mysqli_stmt_execute($stmt)){

        $prodotto = array();
	   
        mysqli_stmt_bind_result($stmt, $pid, $vid, $vend, $nome, $prezzo, $qty);

        while($row = mysqli_stmt_fetch($stmt)){
           $prodotti_acquistabili[$pid] = array("pid"=>$pid, "vid"=>$vid, "nome"=>$nome, "vend"=>$vend,"prezzo"=>$prezzo, "qty"=>$qty);// array di array: $prodotti_ven[$pid] = $prodotto = array();
        }
        $_SESSION["prodotti_acquistabili"] = $prodotti_acquistabili;
		
        }else{
		   $errori[]="si &egrave; verificato un problema nell'esecuzione del prepared statement";
        }
      }else {
        $errori[]="si &egrave verificato un problema con la  creazione del prepared statement";
      }
    }

  // Se è stata eseguita correttamente e senza errori l'estrazione dei dati dal db...
    if(count($prodotti_acquistabili)==0){
       $errori[]="Non ci sono prodotti in vendita!";
    }
    else{  // Stampiamo il form dei prodotti acquistabili dall'utente loggato
 
      echo "<form  name=\"compra\" method=\"GET\" action=\"conferma.php\">";
      echo "<table>";
      echo "<caption> <p>Prodotti acquistabili:</p> </caption>";
      echo "<thead> <tr> <th> LIBRO </th> <th> QUANTIT&Agrave DISPONIBILE </th>  <th> PREZZO </th> <th> SELEZIONARE QUANTIT&Agrave </th></tr> </thead> 	<tbody>";

      foreach ($prodotti_acquistabili as  $prodotto) {
        
        $pID = $prodotto["pid"];
		$quantita_disp = $prodotto["qty"];
        $nome_prodotto = $prodotto["nome"];
        $prezzo = number_format($prodotto["prezzo"]/100,2);
		
        echo"<tr><td>$nome_prodotto</td> <td> $quantita_disp</td> <td class='prezzo'>$prezzo &euro;</td>
        <td> <select name=\"$pID\"> <option selected>0</option> ";
        for ($i=1; $i <= $quantita_disp; $i++) {
          echo "<option>$i</option>";
        }
        echo "</select> </td> </tr>";
        }
        echo" </tbody> </table> ";
        }
?>
        <div id="input-acquista">
	        <input type="submit" value="PROCEDI" class="button">
			<input type="reset"  value="RESET" class="button">
        </div>
        </form>

		
<?php
require_once("errori.php");
include_once("footer.php");
?>


</body>

</html>