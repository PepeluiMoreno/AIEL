<?php
/*-----------------------------------------------------------------------------
FICHERO: limpiarVariablesSesion.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Limpia las variable de sesion 
 
OBSERVACIONES:
------------------------------------------------------------------------------*/
function limpiarVariablesSesion()
{ //session_start();
	$_SESSION = array(); //Unset all of the session variables.

	if (isset($_COOKIE[session_name()]))
	{
    setcookie(session_name(), '', time()-42000, '/');//borra cookies
	}
	session_destroy(); // destroy the session.

	//echo "cookie1: ";print_r($_COOKIE);
	//echo "sesion1: ";print_r($_SESSION);/aquí no están vacías, pero sí en indexI

	//header ("Location: ./index.php");
	//header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
}
?>