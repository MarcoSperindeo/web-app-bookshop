<?php if(count($errori)!=0) {
  echo"<ul class='errori'>Errori:";
	foreach($errori as $ke=>$error)
	{
	echo"<li>".$error."</li>";
	}
echo"</ul>";
}
 ?>