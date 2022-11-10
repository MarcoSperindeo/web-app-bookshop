<?php

   require_once("sessione.php");

   if(isset($_SESSION["UTENTE"])) 
   {
    //se autenticato ridirigo sulla pagina INFO
    header("location: info.php");
   }
   
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        
    <?php require_once("head.html"); ?>
		
    </head>
    
    <body>
	
    <noscript>Abilitare Java Script. </noscript>

      <?php 
	  
	     include_once("menu.php");
		 include_once("utente.php");

	  
	     $errori=array();
		 
         if( isset($_REQUEST['username']) && isset($_REQUEST['password']) && strlen($_REQUEST['username'])>0 && strlen($_REQUEST['password'])>0) // validaizione dati server-side
         {
			 // check lato server sui dati in input
            $username = trim($_REQUEST['username']);
            $password = trim($_REQUEST['password']);

            $regUsername = "/^[A-Za-z\$](?=(.*[\d]))[A-Za-z\d\$]{2,7}$/";
            $regPassword = "/^[0-9]{4,6}$/";                                            /*(?=regex_here) is a positive lookahead. It is a zero-width assertion, meaning that 
			                                                                             * it matches a location that is followed by the regex contained within (?= and ). 
																						 * .*(regex_here) means that zero or more of any character are allowed */
          
            
            if( !preg_match($regUsername,$username) )  
				// aggiunta di un elemento all'array $errori in posizione max+1
			    $errori[]= "Formato nickname non valido! Deve contenere minimo 3 caratteri e massimo 8 caratteri. I caratteri ammissibili sono quelli alfanumerici ed il
             		        carattere $. Lo username deve iniziare con un carattere alfabetico o con $. Deve contenere almeno un carattere non numerico ed uno numerico.";
					 
            if( !preg_match($regPassword,$password) )
			    $errori[]= "La password inserita non rispetta gli standard di sicurezza! Deve contenere minimo 4 e massimo 8 caratteri, scelti tra quelli numerici.";
            
			// superato il controllo su lessico e sinattasi si confronta con il DB il nick e la pwd inseriti
           
		   if(count($errori)==0){
			   
			    $con = mysqli_connect("localhost", "uStrong", "SuperPippo!!!", "comprovendo"); // utente con privilegi di scrittura e lettura
             
                if (mysqli_connect_errno()) 
                    echo "<p>Errore connessione al DBMS: ".mysqli_connect_error()."</p>\n";
                else
                {
					//siccome per fare la query non devo usare parametri passati dall'utente non &egrave necessario usare un prepared statement
                    $query= "SELECT * from utenti where nick=?";
                    $stmt = mysqli_prepare( $con, $query);

                    if($stmt){ 
                       mysqli_stmt_bind_param($stmt,"s", $username);

                       if(mysqli_stmt_execute($stmt)){

                          mysqli_stmt_bind_result( $stmt, $userid_DB, $nick_DB, $password_DB, $money_DB); //associo ogni campo di una row del result set ad una variabiale
                          $counter = 0;
                          while($row = mysqli_stmt_fetch($stmt)){     //creo un array a partire dalle associazioni corrispondente ad una riga del result set
                            $counter ++;
                            $utente["userid"]= $userid_DB;
                            $utente["nick"] = $nick_DB;
                            $utente["money"] = $money_DB/100;
                            $_SESSION["UTENTE"] = $utente;

                            if($password_DB !== $password){
                               $errori[]="La password inserita non è corretta!";
                            }
                          }
                       if($counter == 0){
                          $errori[]="il nickname inserito non esiste!";
                          }
                     }else {
                        $errori[]="si &egrave; verificato un problema nell'esecuzione del prepared statement";
                       }
                 }
				 else{
                    $errori[]="si &egrave verificato un problema con la  creazione del prepared statement";
                 }
                 mysqli_stmt_close($stmt);
                 mysqli_close($con);
                }
            }
		
		// GESTIONE DEL COOKIE PER RICORDARE IL NICK 
			if(isset($_SESSION["UTENTE"]) && count($errori) == 0){

               $scadenza= time()+48*3600;                       // impostazione del cookie del nickname
               setcookie("nick", $_REQUEST["username"], $scadenza);
	
               header("location: info.php"); // reindirizzamento a info.php nel caso siano superati tutti i controlli e dopo aver registrato i cookie 
			                                 // e le variabili di sessione
            }

        }
			   
        else{
			if(count($_REQUEST)!=0)
               $errori[]="Dati Mancanti";
		}
			
          
      ?>
	  
	 
	  <form name="f1" action="login.php" method="GET" onSubmit="return validateForm(username.value,password.value);">
		
          <p>Inserire informazioni utente:</p>
           
           <p> 
		   <label>Nickname: <!--(Deve contenere minimo 3 caratteri e massimo 8 caratteri. I caratteri ammissibili sono quelli alfanumerici ed il carattere $. Lo username deve iniziare con un carattere alfabetico o con $. Deve contenere almeno un carattere non numerico ed uno numerico.)-->
		   <input type="text" name="username" maxlength="20">
		   </label>
		   </p> 
		   <p>
            <label>Password: <!--( Deve contenere minimo 4 e massimo 8 caratteri, scelti tra quelli numerici.)-->
			<input type="password" name="password" maxlength="16"></label>
          </p>
            
          <p>
            <label><input id="ok-login" type="submit" value="Ok"></label>
            <label><input id="reset-login" type="reset" value="Reset"></label>
          </p>
        </form>
		
		
		<?php
         if($errori!=0){
          require_once("errori.php");
        }
         include_once("footer.php"); 
        ?>
		
    </body>
	
	
</html>