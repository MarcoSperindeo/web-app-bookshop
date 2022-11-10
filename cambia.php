
<?php
   require_once("sessione.php");

   if(!isset($_SESSION["UTENTE"])){  // SE NON SI E' AUTENTICATI
     echo "<!doctype html>";
	 echo "<html>";
     echo "<head>";
     require_once("head.html");
     echo "</head>";
     echo "<body>";
     require_once("menu.php");                                     
     echo "<div id=\"contenuto\"> <p>Attenzione! Devi essere autenticato per poter accedere a questa pagina. Effettua il <a href=\"login.php\"> Log In </a> !</p> </div>"; 
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
<?php require_once ("head.html"); ?>
</head>

<body>
<noscript> Abilitare Java Script.</noscript>

<?php
        include_once("utente.php");
        require_once("menu.php") ;
		
		
	
	
// SEZIONE 1: MODIFICARE SUL DB LA QUANTITA' DEI PRODOTTI INSERITI ALL'INTERNO DEL SISTEMA DALL'UTENTE 	
		
// SE L'UTENTE HA COMPILATO IL FORM PER MODIFICARE LE QUANTITA' DEI PRODOTTI DA LUI MESSI IN VENDITA
		// CONTROLLI:
		// CHECK SU ID (DEVE ESSERE UN PRODOTTO INSERITO NEL DB DALL'UTENTE AUTENTICATO ) E SU QUANTITA' (DEVE ESSERE UN NUMERO INTERO  
		// MINORE DI 65 535 E DI VALORE NON NULLO) DEI PRODOTI CHE L'UTENTE CHE VUOLE MODIFICARE
		// CREAZIONE DI UN VETTORE CHE CONTENGA L'ID E LA NUOVA QUANTITA' DI QUEI PRODOTTI CHE HANNO SUPERATO I CONTROLLI (array $qty_da_modificare)

		// se l'utente ha inserito almeno un prodotto nel sistema => $_SESSION["prodotti_utente"] è settata
	    // se è stato compilato il form per modificare la quantità dei prodotti dell'utente => $_REQUEST["modifica"] è settata   
	   if( isset($_SESSION["prodotti_utente"]) && isset($_REQUEST["modifica"])){ 
		   
		   
		   $prodotti = array(); // nell'array $prodotti memorizzo id e qty da modficare dei prodotti dopo aver fatto i controlli sull'id ma non sulla qty
		   $qty_da_modificare = array(); // nell'array $qty_da_modificare memorizzo id e qty da modificare dei prodotti dopo aver superato i controlli sia su id che su qty
		   
           $errori=array();
			
           $null=false;

           // CHECK SU ID PRODOTTO: se l'utente provasse a inserire dei parametri nella URI per modificare prodotti non suoi NON può farlo,           
           foreach ($_SESSION["prodotti_utente"] as $pID) {
               if(!isset($_REQUEST[$pID])){    
                 $errori[]="Errore! I parametri nella URI sono stati modificati. ";
               }
               else{
                 $prodotti[$pID] = $_REQUEST[$pID];  // salvo in questo array l'ID del PRODOTTO come CHIAVE e la NUOVA QUANTITA' come VALORE (non ancora controllata)	
				 
                 if($_REQUEST[$pID]!=""){
                    $null=true; 
				 }				
                }
            }
			
            // CONTROLLO SU QTY: QTY DEVE AVERE VALORE NON NULLO
            if( $null==false){
              $errori[]="seleziona almeno una quantit&agrave; non nulla";
            }
			 
            // UNA VOLTA SUPERATI I PRIMI CONTROLLI (una volta verificato che sto modificando solo i prodotti dell'utente loggato (CHECK ID PRODOTTO) 
            // e che non ci sono quantità nulle) VADO A VERIFICARE CHE LE QUANTITA DA AGGIORNARE SIANO DEGLI INTERI, ZERO INCLUSO ( CHECK SU QTY)
			 
            else if(count($errori)==0){  // se non ci sono errori nel check sull'id di prodotto e sulla quantità maggiore non nulla

              foreach ($prodotti as $key => $value) {

                 if(strlen($value)>0 && strlen($value)<5) {
                    if($value){ 
                      //preg_match('/\D/',$value)                                     // numero intero da 1 a 5 cifre, minore di 65 535:
					                                                                  // max valore inseribile per non avere overflow su small int (memorizzato su 16 bit)
                       if( !filter_var($value, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,5}$/"))) ||  (int)$value>65535) 
                       {
                          if(count($errori)==0) // se si sono superati tutti i controlli precedenti
                          $errori[]="Puoi inserire solo un valore numerico inferiore a 65535";
                      
                       } else {
                           $qty_da_modificare[$key]= $value;
                        }
                    }
                   else{
                     $qty_da_modificare [$key]= $value;
                    }
                  }
                }
            }

		// FINE CONTROLLI: VADO A IMPOSTARE LA NUOVA QUANTITA SUL DB 
           if(count($errori)==0){ // SE NON CI SONO ERRORI E QUINDI SONO STATI SUPERATI TUTTI I CONTROLLI PRECDENTI

               $connessione = mysqli_connect("localhost","uStrong","SuperPippo!!!","comprovendo"); // Utente con privileggi di scrittura e lettura
			   
               if ( mysqli_connect_errno()!=0){
                          $errori[]= "errore - collegamento al DB impossibile:".mysqli_connect_error();
                }
                  else
                {
                   foreach ($qty_da_modificare as $key => $value) {

                      $query= "UPDATE prodotti SET qty=? where pid=?";
                      $stmt = mysqli_prepare( $connessione, $query);

                       if($stmt){
                           mysqli_stmt_bind_param($stmt,"ii", $value, $key);

                           if(!mysqli_stmt_execute($stmt)){
                               $errori[]="quantit&agrave; non modificata, si &egrave; verificato un problema nell'esecuzione del prepared statement";
                            } 
						
                        }else {
                           $errori[]="quantit&agrave; non modificata, si &egrave; verificato un problema nella creazione del prepared statement";
                        }
                      mysqli_stmt_close($stmt);
                    }
                }
				
            mysqli_close($connessione);
           }

		// SE NON SI VERIFICANO ERRORI DURANTE L'IMPOSTAZIONE DELLA NUOVA QUANTITA' SUL DB
            if(count($errori)==0){
              header("location: qty_modificata.php");
            }else{
              require_once("errori.php");
            }
        }
		
		
		   
// SEZIONE 2: CREARE NUOVI PRODOTTI E AGGIUNGERLI AL DATABASE

    //SE L'UTENTE HA COMPILATO IL FORM PER INSERIRE UN NUOVO PRODOTTO, CONTROLLO I DATI CHE MI HA FORNITO E SE SONO VALIDI CREO UN NUOVO PRODOTTO
    if(isset($_REQUEST["crea_prodotto"])){ // Campo hidden del form di creazione del prodotto
        if(isset($_REQUEST["nome_prodotto"]) && isset($_REQUEST["prezzo"]) && isset($_REQUEST["quantita"])) {

            $ok=true;
            $errori=array();
			
            $nome = trim($_REQUEST["nome_prodotto"]);
            $prezzo = str_replace(",",".",trim($_REQUEST["prezzo"]));
            $quantita = trim($_REQUEST["quantita"]);
			$vID = $_SESSION["UTENTE"]["userid"];
            $venditore = $_SESSION["UTENTE"]["nick"];
            $pID_new = 0;

            //controllo che non ci siano stringhe vuote
            if(strlen($_REQUEST["nome_prodotto"])===0 || strlen($_REQUEST["prezzo"])===0 || strlen($_REQUEST["quantita"])===0) {
               $ok=false;
               $errori[]="<p> I dati inseriti non sono sufficienti per creare un nuovo prodotto: specificare nome, quantit&aegrave e prezzo. </p>";
            } 

            //verifico la validità dei parametri, considerando valido un prezzo unitario pari a 0 e una quantità nulla
            if($ok==true && $quantita && $prezzo){
                if( !filter_var($quantita, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,7}$/"))) ||
                    !filter_var($prezzo, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]+(\.[0-9]{1,2})?$/")))){
						
                     if($ok==true){
                        $errori[]= "Attenzione, il prezzo pu&ograve; avere due numeri decimali e la quantit&agrave deve essere un numero intero!";
                    } 
                    $ok=false;
                }
            }

            // SE TUTTI I PARAMETRI CHE MI HA FORNITO L'UTENTE SONO CORRETTI (SUPERATI I CONTROLLI) PROCEDO A INSERIRE UN NUOVO PRODOTTO NEL DB,
            // MA PRIMA DEVO RECUPERARE L'ID DEL NUOVO PRODOTTO QUINDI ESTRAGGO DAL DB IL MASSIMO PID, QUINDI LO INCREMENTO DI UNO
            if($ok==true){
				
               $pID_new=0;
			   
               $connessione = mysqli_connect("localhost","uStrong","SuperPippo!!!","comprovendo");
			   
                if ( mysqli_connect_errno()!=0){
                   $errori[]= "errore - collegamento al DB impossibile:".mysqli_connect_error();
                }
                else{               
                   $query= "SELECT MAX(pid) AS pid from prodotti";
                   $result = mysqli_query($connessione,$query);

                    if (!$result){
                      $errori[]="query fallita:".mysqli_error($connessione);
                    } else
                    {
                    // operazioni sul result set
                      $row = mysqli_fetch_assoc($result);
                      if($row){
							(int)$pID_new = (int)$row["pid"]+1;
                      }
                      else{
                          $pID_new=1;
                      }
                    }
                    // rilascio della memoria associata al res.set e chiudo la connessione al DB
                    mysqli_free_result($result);
					

                   //AGGIUNGO UN NUOVO PRODOTTO NEL DB
                   $query= "INSERT INTO prodotti (pid, vid, vend, nome, prezzo, qty) VALUES (?, ?, ?, ?, ?*100, ?) ";
                   $stmt = mysqli_prepare($connessione, $query);

                   if($stmt){
                       mysqli_stmt_bind_param($stmt,"iissii", $pID_new, $vID, $venditore, $nome, $prezzo, $quantita);

                       if(mysqli_stmt_execute($stmt)===FALSE){
                           $errori[]="prodotto non aggiunto, si &egrave; verificato un problema nell'esecuzione del prepared statement";
                        }else{
                           $_SESSION["nuovo_prodotto_creato"] == TRUE;
                           $_SESSION["uniqid"] = "";
                        }
                    }else {
                       $errori[]="prodotto non aggiunto, si sono verificati problemi con la  creazione del prepared statement";
                    }
                    mysqli_stmt_close($stmt);
                    mysqli_close($connessione);
                }
            }
        }
        else{
           $errori[]=" Dati insufficienti per creare un nuovo prodotto ";
		   # echo "<p>Max Id Prodotto: ".$pID_new."</p>";
        }
		
		
        if(count($errori)==0){
           header("location: prodotto_creato.php");
        }else{
           require_once("errori.php");
        }
		
    }
	
	
	
		//APRO UNA CONNESSIONE VERSO IL DB E VADO A PRENDERE TUTTE LE INFO SUI PRODOTTI DELL'UTENTE LOGGATO PER STAMPARE LA TABELLA DEI PRODOTTI DELL'UTENTE

        $connessione = mysqli_connect("localhost","uWeak","posso_solo_leggere","comprovendo"); // Utente con privilegi di sola lettura

        if ( mysqli_connect_errno()!=0){
          $errori[]= "errore - collegamento al DB impossibile:".mysqli_connect_error();
        }
        else
        {
          $query= "SELECT * from prodotti where vend=?";

          $stmt = mysqli_prepare( $connessione, $query);

          if(!$stmt){
            $errori[]=" impossibile visualizzare i prodotti, si &egrave; verificato un errore nella creazione del prepared statement ";
          }
          else{
             mysqli_stmt_bind_param($stmt,"s",$_SESSION["UTENTE"]["nick"]); // mysqli_stmt_bind_param(stmt,types,vars) vars è l'elenco delle variabili da 
                                                                            // associare e types è una stringa che ne specifica ordinatamente il tipo
             if(!mysqli_stmt_execute($stmt)){
                $errori[]="L'elenco dei prodotti non &egrave disponibile, si &egrave; verificato un problema nell'esecuzione del prepared statement";
              }
             else{

                mysqli_stmt_bind_result( $stmt, $pID, $vID, $vend, $nome, $prezzo, $qty );   //associo ogni campo di una riga del result set ad una variabiale
               // STAMPO IL FORM DI AGGIORNAMENTO QUANTITA' PRODOTTI
            
                echo "<form name=\"f1\" method=\"GET\" action=\"cambia.php\">";
                echo "<table>";
                echo "<caption> Aggiornamento della quantit&agrave di un libro </caption>";
                echo "<thead> <tr> <th> LIBRO </th>  <th> PREZZO </th> <th> QUANTIT&Agrave; </th> <th> MODIFICA QUANTIT&Agrave; </th></tr> </thead> <tbody>";
 
                $prodotti_utente=array(); // Inizializzo un vettore $prodotti_utente contenente gli ID dei prodotti venduti dall'utente loggato

                while($row = mysqli_stmt_fetch($stmt)){    //creo un array a partire dalle associazioni corrispondenti ad una riga del result set
				  
                    $prodotti_utente[] = $pID;  // Assegnazione a $prodotti_utente degli ID dei prodotti corrispondenti all'utente loggato
                    $prezzo = (float)($prezzo/100);
					// STAMPO LA TABELLA DEI PRODTTI CORRISPONDENTI ALL'UTENTE (INSERITI DALL'UTENTE NEL DB E QUINDI MESSI IN VENDITA DAL'UTENTE)
                    echo "<tr> <td>$nome</td> <td>".number_format($prezzo, 2, '.', '')." &euro;</td> <td>$qty</td>
                    <td><input type=\"text\" name=\"$pID\" size=\"10\" maxlength=\"5\"> </td> </tr>";
					  
                }

				//CREO UN ARRAY DI SESSIONE DOVE MEMORIZZO TUTTI I PRODOTTI DELL'UTENTE A PARTIRE DALL'ARRAY $elenco

                $_SESSION["prodotti_utente"] = $prodotti_utente;
                mysqli_stmt_close($stmt);
                mysqli_close($connessione); 
				 
				 
                echo" </tbody> </table> ";
                echo "<input type=\"hidden\" name=\"modifica\" value=\"modifica\">"; 
				// "modifica" è un campo nascosto per distinguere il form per modificare le quantità da quello per creare un nuovo prodotto
                echo "<p id=\"submit-aggiorna\"><input  type=\"submit\" value=\"AGGIORNA\" class='button'></p>";
                echo"</form>"; 
                }
            }
        }
?>



	<p id="prod-insert-title">Inserimento di un nuovo libro</p>
	
	<form name="f2" method="GET" action="cambia.php" onsubmit="return validateProd(nome_prodotto.value, prezzo.value, quantita.value)">
	
	<p id="prod-insert-nome" class="f2"><label> Nome </label>&emsp;&nbsp;<input type="text" name="nome_prodotto" size="30"></p>
	<p id="prod-insert-prezzo"class="f2"><label> Prezzo </label>&ensp;&nbsp;<input type="text" name="prezzo" size="30"></p>
	<p id="prod-insert-quantita"><label> Quantit&agrave; </label><input type="text" name="quantita" size="30"></p>
	<input type="hidden" name="crea_prodotto" value="crea_prodotto">
	<p id="prod-insert-submit"><label><input id="submit-inserisci" type="submit" value="INSERISCI" class="button"></label></p>
	
	</form>
</div>


<?php 
      include_once("footer.php"); ?>

</body>

</html>
