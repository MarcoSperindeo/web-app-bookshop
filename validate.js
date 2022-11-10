

function validateForm(username,password){
	
var regUsername = /^[A-Za-z\$](?=(.*[\d]))[A-Za-z\d\$]{2,7}$/
var regPassword = /^[0-9]{4,6}$/;
                
                
if( !regUsername.test(username) )
{
    alert("Formato username non valido! Deve contenere minimo 3 caratteri e massimo 8 caratteri. I caratteri ammissibili sono quelli alfanumerici ed il carattere $. Lo username deve iniziare con un carattere alfabetico o con $. Deve contenere almeno un carattere non numerico ed uno numerico.");
    return false;
}
else if( !regPassword.test(password) )
{
     alert("La password inserita non rispetta gli standard di sicurezza! Deve contenere minimo 4 e massimo 8 caratteri, scelti tra quelli numerici.");
     return false;
}
                
 return true;
}
	
	
			
function validateProd(nome, prezzo, quantita){

var regPrezzo =  /^[0-9]{1,7}[.,]{1}[0-9]{1,2}$/;
var regPrezzoIntero=/^[0-9]{1,7}$/;

var regQuantita =  /^[0-9]{1,5}$/;

if(nome==""){
  window.alert("Impossibile creare un prodotto, il prodotto deve avere un nome.");
  return false;
}else{
   if(!regPrezzo.test(prezzo) && !regPrezzoIntero.test(prezzo)){
     window.alert("Impossibile creare un prodotto, il prezzo deve essere a due decimali.");
     return false;

   }else{
     if(!regQuantita.test(quantita)){
       window.alert("Impossibile creare un prodotto, la quantit&agrave; deve essere un numero intero")
       return false;
     }
   }
}

return true;
}
     