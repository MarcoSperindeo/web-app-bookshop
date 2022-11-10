<?php

   if(session_status()=== PHP_SESSION_DISABLED)
      echo "<p> Su questo sito le sessioni non sono abilitate </p>";  // Se la sessione non è abilitata

   else if(session_status()!==PHP_SESSION_ACTIVE) // Se la sessione non è attiva
	  session_start();
  
   return;

?>