
<h1 id='titolo'> My Bookshop </h1>

<?php
if(!isset($_SESSION["UTENTE"]))
{
?>
<nav id="navigation">
  <ul>
   <li><a href="home.php"> Home </a></li>
   <li><a href="login.php"> Login </a></li>
   <li><a href="info.php"> Info </a></li>
   <li><a href="cambia.php"> Metti in Vendita </a></li>
   <li><a href="acquista.php"> Acquista </a></li>
   <li>Logout</li>
   </ul>
 </nav> 
<?php
}

else{  // se si è autenticati il link per il login è disattivato
?>
 <nav id="navigation">
    <ul>
     <li><a href="home.php"> Home </a></li>
	 <li>Login</li>
	 <li><a href="info.php"> Info </a></li>
     <li><a href="cambia.php"> Metti in Vendita </a></li>
     <li><a href="acquista.php"> Acquista </a></li>
     <li><a href="logout.php"> Logout </a></li>
     </ul>
   </nav>
<?php
  }
?>
  