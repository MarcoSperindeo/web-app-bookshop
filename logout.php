<!doctype html>
<?php
require_once("sessione.php");

if(isset($_SESSION["UTENTE"])){

        $_SESSION=array(); // Reinizializzaizone array di sessione

        $parametri_cookie= session_get_cookie_params(); // parametri del cookie volatile associato alla sessione
        $nome_sessione= session_name();                 // restituisce il nome del cookie associato ala sessione (default: PHPSESSID)                     
		// distruzione del cookie associato alla sessione
        setcookie($nome_sessione, '', time()-50000, $parametri_cookie["path"], $parametri_cookie["domain"], $parametri_cookie["secure"], $parametri_cookie["httponly"] );

        session_destroy();
		
		header("location: home.php");
        
		
    }else {
        header("location: home.php");
    }

?>
