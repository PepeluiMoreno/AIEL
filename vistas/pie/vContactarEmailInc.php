<?php
/*-----------------------------------------------------------------------------
FICHERO: vContactarEmailInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

Contiene los "requires" para formar la página que muestra un ventana pop-up y blank
con un formulario para que el usuario introduzca y envíe un 
email a "info@europalaica.com" para pedir información

También muestra mensajes de error

RECIBE: $datosContactarEmail, la primera vez vacío después con los datos si
hubiese error, o incompletos

LLAMADA: cEnlacesPie.php:contactarEmail() 
(desde el "pie", abajo de toda la pantalla, la barra horizontal "Contactar")
-------------------------------------------------------------------------------*/
function vContactarEmailInc($datosContactarEmail) 
{
		//echo "<br><br>0-1  vContactarEmailInc.php:datosContactarEmail: ";print_r($datosContactarEmail); 

		require_once './vistas/plantillasGrales/vCabeceraBlank.php';

		require_once './vistas/pie/vCuerpoContactarEmail.php';

		require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>