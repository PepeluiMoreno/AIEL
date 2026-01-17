<?php
/*-----------------------------------------------------------------------------
FICHERO: datosLinksSeccionesLogin.php
PROYECTO: ong
VERSION: PHP 5.2.3
DESCRIPCION: Contiene valores de parámetros para generar los links en el
             menú de "Secciones" de Login, Recordar contraseña y Registrarse
             como nuevo usuario
OBSERVACIONES: Es llamado desde las vistas de "vCuerpoLogin" y otras ...
-------------------------------------------------------------------------------*/
function datosLinksSeccionesLogin()
{ //echo "dentro datosLinksSeccionesLogin";
	$arrLinksSeccionesLogin = array
	("ayuda"        => array("DESCRIPCIONALT"=>"Ayuda sobre esta aplicación",
	                         "TEXTOMENU"=>"Ayuda",
	                         "CONTROLADOR"=>"controladorLogin",
													 "NOMFUNCION"=>"validarLogin"),
/*	 "documentos"   => array("DESCRIPCIONALT"=>"Documentos relacionados con la FCT",
	                         "TEXTOMENU"=>"Documentos FCT",
	                         "CONTROLADOR"=>"#",
													 "NOMFUNCION"=>"#"),
*/													 
	 );

   return $arrLinksSeccionesLogin;
}
?>

