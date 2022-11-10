<?php
require_once("sessione.php");

if(!isset($_SESSION["UTENTE"])){
  echo "<!doctype html>";
  echo "<head>";
  require_once("head.html");
  echo "</head>";
  echo "<body>";
  require_once("menu.php");
  echo "<div id='contenuto'><p>Attenzione! Devi essere autenticato per poter accedere a questa pagina. Effettua il <a href=\"login.php\"> Log In </a> !</p></div>";
  include_once("utente.php");
  include_once("footer.php");
  echo "</body>";
  echo "</html>";
  return;
}
?>


<!DOCTYPE html>
<html lang='it'>

<head>
<?php require_once ("head.html")?>
</head>
 
<body>

<?php
	require_once("menu.php"); 
	
    $errori = array();
// SE NON E' STATO COMPILATO IL FORM ALLA PAGINA acquista.php
	if(!isset($_SESSION["prodotti_acquistati"]))
	{
	    echo "<div id='contenuto'>Attenzione! Per accedere a questa pagina devi prima avere compilato il form  alla pagina <a href=\"acquista.php\"> acquista </a>.</div>";
        	  
		include_once("utente.php");
		include_once("footer.php");
		echo "</body>";
		echo "</html>";
		return;
	}

// SE L'ID DI TRANSAZIONE RICEVUTO NELLA REQUEST COINCIDE CON QUELLO ASSEGNATO ALL'UTENTE ALLA PAGINA conferma.php E TRASMESSO TRAMITE IL CAMPO NASCOSTO DEL FORM 
    if(isset($_REQUEST["id_transazione"]) &&  $_REQUEST["id_transazione"] === $_SESSION["id_transazione"]) {
		
       foreach ($_SESSION["prodotti_acquistati"] as $prodotto) {

           $pID = $prodotto["pID"];
		   $vID = $prodotto["vID"];
		   $nome_prodotto = $prodotto["nome"];
           $venditore = $prodotto["venditore"];
		   $qty_comprata = $prodotto["quantita"];
           $totale_prodotto = (float) $prodotto["prezzoTOT"];
           $totale_prodotto_DB = round ($totale_prodotto*100); // Funzione round() = arrotondamento
		   $uID = $_SESSION["UTENTE"]["userid"];
		   $nick_utente = $_SESSION["UTENTE"]["nick"];
           $compro_miei_prodotti = false;


// AUMENTO IL BORSELLINO DEL VENDITORE SUL DB
           $con = mysqli_connect("localhost","uStrong","SuperPippo!!!","comprovendo"); // privilegi di scrittura/lettura

           if ( mysqli_connect_errno() != 0){
              $errori[] = "Collegamento al DB non riuscito: ".mysqli_connect_error();
            }
           else{

              $query = "UPDATE utenti SET utenti.saldo = (utenti.saldo + ?) WHERE uid=?";
              $stmt = mysqli_prepare( $con, $query);
			  
              if($stmt){
                  mysqli_stmt_bind_param($stmt,"is", $totale_prodotto_DB, $vID);

                    if(mysqli_stmt_execute($stmt)){
                            mysqli_stmt_free_result($stmt);
                        }else{
                            $errori[]="Il pagamento non &egrave; avvenuto correttamente, si &egrave; verificato un errore nell'esecuzione del prepared statement";
                        }
                    mysqli_stmt_close($stmt);
                }else {
                    $errori[]="Borsellino non incrementato, si sono verificati problemi con la  creazione del prepared statement";
                    mysqli_stmt_close($stmt);
                }


// DECREMENTO IL BORSELLINO DELL'UTENTE SUL DB
              $query = "UPDATE utenti SET utenti.saldo = (utenti.saldo - ?)  WHERE uid=?";
              $stmt = mysqli_prepare( $con, $query);

              if($stmt){
                     mysqli_stmt_bind_param($stmt,"is", $totale_prodotto_DB, $uID );

                     if(mysqli_stmt_execute($stmt)){
                          mysqli_stmt_free_result($stmt);
                        }else{
                          $errori[]="Il pagamento non &egrave; avvenuto correttamente, si &egrave; verificato un problema nell'esecuzione del prepared statement";
                        }
                     mysqli_stmt_close($stmt);
				
                }else {
                     $errori[]="Borsellino dell'utente non aggiornato, si sono verificati problemi con la  creazione del prepared statement";
                     mysqli_stmt_close($stmt);
                }


// AGGIORNO LA VARIABILE DI SESSIONE DEL BORSELLINO DELL'UTENTE
               $_SESSION["UTENTE"]["money"] = $_SESSION["UTENTE"]["money"] - $totale_prodotto;
			   

// ESTRAGGO IL MAX ID DALLA TABELLA DELLE TRNASAZIONI E LO INCREMENTO DI UNO
			   $query= "SELECT MAX(tid) AS tid from transazioni";
			   $result = mysqli_query($con,$query);

				if (!$result){
				  $errori[]="query fallita:".mysqli_error($con);
				} else {
					// operazioni sul result set
					$row = mysqli_fetch_assoc($result);
					if ($row){
						(int) $tID_new = (int) $row["tid"]+1;
					}
					else {
						$tID_new=1;
					}
				}
				// rilascio della memoria associata al res.set e chiudo la connessione al DB
				mysqli_free_result($result);
				
				
// SALVO SUL DB LA TRANSAZIONE
			   $query= "INSERT INTO transazioni (tid, sid, did, src, dst, pid, qty, importo) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ";
			   $stmt = mysqli_prepare($con, $query);

			   if($stmt){
				     mysqli_stmt_bind_param($stmt,"iiissiii", $tID_new, $uID, $vID, $nick_utente, $venditore, $pID, $qty_comprata, $totale_prodotto_DB);
					 if(mysqli_stmt_execute($stmt)){                            
						mysqli_stmt_free_result($stmt);
					 } else {
						 $errori[]="Quantit&agrave; non aggiornata: si &egrave; verificato un problema nell'esecuzione del prepared statement";
					 }
					 mysqli_stmt_close($stmt);
				} else {
				   $errori[]="prodotto non aggiunto, si sono verificati problemi con la  creazione del prepared statement";
				   	mysqli_stmt_close($stmt);
				}
				
				
// AGGIORNO LA QUANTITA DISPONIBILE DEI PRODOTTI SUL DB
               $query= "UPDATE prodotti SET qty = qty - ? WHERE pid=?";                                                          
               $stmt = mysqli_prepare( $con, $query);

               if($stmt){                                                       
                     mysqli_stmt_bind_param($stmt,"ii", $qty_comprata , $pID ); // mysqli_stmt_bind_param(stmt,types,vars) vars è l'elenco delle variabili da 
                     if(mysqli_stmt_execute($stmt)){                            // associare e types è una stringa che ne specifica ordinatamente il tipo
                        mysqli_stmt_free_result($stmt);
                     } else {
                         $errori[]="Quantit&agrave; non aggiornata: si &egrave; verificato un problema nell'esecuzione del prepared statement";
                     }
                     mysqli_stmt_close($stmt);
                     mysqli_close($con);
                } else {
                     $errori[]="Quantit&agrave; non aggiornata: si sono verificati problemi con la  creazione del prepared statement";
                     mysqli_stmt_close($stmt);
                     mysqli_close($con);
                }


// STAMPO SU FILE IL LOG DELLA TRANSAZIONE IN FORMATO TABULARE
				$log_timestamp = date_create();
				$log_timestamp = date_format($log_timestamp,"Y-m-d H:iP");
				$br = "+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+";
				$he = "| TRANSAZIONE_ID | ACQUIRENTE_ID | VENDITORE_ID | ACQUIRENTE_NICK | VENDITORE_NICK | PRODOTTO_ID | PRODOTTO_NOME                                      | QUANTITÀ | IMPORTO | TIMESTAMP              |";
				$log_row = sprintf("| %-14u | %-13u | %-12u | %-15s | %-14s | %-11u | %-50s | %-8u | %-7u | %-9s |",
							$tID_new, $uID, $vID, $nick_utente, $venditore, $pID, $nome_prodotto, $qty_comprata, $totale_prodotto_DB, $log_timestamp);
				$log_msg = sprintf("%s\n%s\n%s\n%s\n%s\n", $br, $he, $br, $log_row, $br);
				
				if (!file_exists("logs")) 
				{
					// create directory/folder uploads.
					mkdir("logs", 0777, true);
				}
				$log_file_name = 'logs/log_transazioni' . date('d-M-Y') . '.log';
				file_put_contents($log_file_name, $log_msg . "\n", FILE_APPEND);
            }
        }

		
// REINIZIALIZZO LE VARIABILI DI SESSIONE RELATIVO ALLA TRANSAZIONE E ALL'ACQUISTO
        $_SESSION["id_transazione"] = NULL;
        $_SESSION["prodotti_acquistati"] = NULL;
        $_SESSION["prodotti_acqistabili"] = NULL;
		 
        if(count($errori)==0){
           echo "<div id='contenuto'><p>Congratulazioni! Il tuo ordine &egrave; stato processato correttamente 
                  ed il relativo importo ti &egrave stato detratto dal borsellino elettronico.</p>
                  <p>Continua a comprare sulla pagina <a href=\"acquista.php\">acquista</a> 
				  oppure torna alla <a href=\"home.php\">homepage</a> .</p></div>";
        }else{
           require_once("errori.php");
        }
    }
else
    {
 //se l'id della transazione non corrisponde
     echo "<div id=\"contenuto\"> <p> La transazione NON &egrave avvenuta correttamente! Torna alla pagina
           <a href=\"acquista.php\"> acquista</a> oppure  torna alla homepage <a href=\"home.php\" class=\"button\">ANNULLA</a></div>";
     echo "</body>";
     echo "</html>";
     return;
    }

include_once("utente.php");
include_once("footer.php"); ?>

</body>
</html>