<footer id="footer">
	<?php $file=preg_split('+/+', $_SERVER['PHP_SELF']);
	      $count=count($file);
		echo " <p>File: ".$file[$count-1]."</p>";?>

		<p>My Bookshop - Marco Sperindeo, e-mail: <a href="mailto:marco.sperindeo@gmail.com">marco.sperindeo@gmail.com</a> </p>
		<p>
		<a href="http://www.w3schools.com" target="_blank"><img src="./img/w3schools-icon.png"  alt="Logo di w3schools" width="50" height="50"></a>
		<a href="http://www.polito.it" target="_blank"><img src="./img/polito-icon.png"  alt="Logo di Polito" width="50" height="50"></a>
		<a href="http://validator.w3.org/check?uri=referer"><img src="./img/HTML5_Logo_512.png" alt="Valid HTML 5" height="88" width="88"></a>
		</p>
</footer>