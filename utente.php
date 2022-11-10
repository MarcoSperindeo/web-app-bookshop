<?php

echo"<div id=\"user\">";

if (!isset($_SESSION["UTENTE"]))
{ 
?>
<table class=\"utente\">
<tr><th colspan='2'> UTENTE</th></tr>
<tr><td>Nickname:</td><td> anonimo </td></tr>
<tr><td>Borsellino<br>elettronico:</td><td class="prezzo"> 0.00 &euro; </td></tr>
</table>

<?php
} 
else{  // se si è autenticati
?>
<table class=\"utente\">
<tr><th colspan='2'> UTENTE</th></tr>
<?php 
echo "<tr><td>Nickname:</td><td>".$_SESSION["UTENTE"]["nick"]."</td></tr>";
echo "<tr><td>Borsellino<br>elettronico:</td><td class=\"prezzo\">".number_format($_SESSION["UTENTE"]["money"],2)."&euro; </td></tr>"; // portafogli dell'utente
echo "</table>";
}
?>
</div>