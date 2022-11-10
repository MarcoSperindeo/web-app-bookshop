
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
		  include_once("utente.php");
		  
		  
$errori=array();
$prodotti=array();

$connessione = mysqli_connect("localhost","uWeak","posso_solo_leggere","comprovendo"); // utente con privilegi di sola lettura


if ( mysqli_connect_errno()!=0){
  $errori[]=" errore - collegamento al DB impossibile: ".mysqli_connect_error();
}
else 
{
// Se l'utente è autenticato estraggo dal db i prodotti di quantità NON nulla e ne visualizzo nome del prodotto, del venditore, quantità e prezzo unitario.
if(isset($_SESSION["UTENTE"])){

$query= "SELECT * from prodotti WHERE qty>0 ";

$result = mysqli_query($connessione, $query); 

if (!$result){
$errori[]="errore – query fallita: ".mysqli_error($con);
}
else{

     while ($riga = mysqli_fetch_assoc($result)){
     $prodotti[]=$riga;  // aggiungo la riga del result set all'array $prodotti in posizione max+1
       }
   }
   mysqli_free_result($result); 

  if(count($prodotti)==0){
    $errori[]="Non ci sono prodotti da visualizzare";
  }
  else{
	echo "<div id='contenuto'>";
    echo "<table>";
    echo "<caption> <p>Prodotti disponibili</p> </caption>";
    echo "<thead> <tr> <th> LIBRO </th> <th> VENDITORE </th> 
	     <th> QUANTIT&Agrave; DISPONIBILE </th> <th> PREZZO </th> </tr> </thead> <tbody>";
	
  foreach ($prodotti as  $prodotto) {
    $nome_prodotto = $prodotto["nome"];
    $nome_venditore = $prodotto["vend"];
    $quantita_disponibile = $prodotto["qty"];
    $prezzo_unitario = number_format($prodotto["prezzo"]/100,2);
    echo"<tr><td>$nome_prodotto</td> <td>$nome_venditore</td> 
	     <td > $quantita_disponibile</td> <td class='prezzo'>$prezzo_unitario&euro;</td> </tr>";
    }
    echo" </tbody> </table> </div> ";
  }
}

// Se l'utente NON è autenticato visualizzo solamente nome e quantità dei prodotti di quantità non nulla
else{


  $query= "SELECT * from prodotti WHERE qty>0";

  $result = mysqli_query($connessione, $query);

  if (!$result){
  $errori[]="errore – query fallita: ".mysqli_error($connessione);
  }
  else{
       while ($riga = mysqli_fetch_assoc($result)){
           $prodotti[]=$riga;
         }
     }
     mysqli_free_result($result);
     mysqli_close($connessione);
     //stampo $prodotti

     if(count($prodotti)==0){
       $errore[]= "Non ci sono prodotti da visualizzare";
     }
     else{
	   echo "<div id='contenuto'>";
       echo "<table>";
       echo "<caption> <p>Libri disponibili</p> </caption>";
       echo "<thead> <tr> <th> LIBRO </th>  <th> QUANTIT&Agrave; DISPONIBILE </th> </tr> </thead> <tbody>";
     foreach ($prodotti as  $prodotto) {
		 
       $quantita_disponibile=$prodotto["qty"];
       $nome_prodotto=$prodotto["nome"];
       
       echo"<tr><td>$nome_prodotto</td>  <td> $quantita_disponibile</td></tr>";
     }
       echo" </tbody> </table> </div> ";
     }
   }
}

require_once("errori.php");
include_once("footer.php");
?>

  </body>
  </html>