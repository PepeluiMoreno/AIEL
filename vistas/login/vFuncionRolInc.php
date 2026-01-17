<?php
/*-----------------------------------------------------------------------------
FICHERO: vFuncionRolInc.php
PROYECTO: EL
VERSION: php 7.3.21

DESCRIPCION: Contiene menú idz. de funciones según tipo de rol de usuario, 
y la parte central del cuerpo de la página. 
También incluye descargar archivos con manuales o documentos para ese rol, 
si previamente se incluyen en: controladorSocios.php:menuGralSocio(),
cTesorero.php:menuGralTesorero(), etc. como archivos de alta socio u otros

LLAMADA: desde los controladores de cada rol de usuario: 
controladorSocios.php:menuGralSocio(),cCoordinador.php:menuGralCoord(),
cPresidente.php:menuGralPres(),cTesorero.php:menuGralTesorero()
cAdmin.php:menuGralAdmin(), etc

OBSERVACIONES: El parámetro "$enlacesSeccId" recibe los links del menú de usuario
2017_04_23: Añado $enlacesArchivos, para manuales
2016-03-31:Cambio $tituloSecc, por $tituloSeccion, si no se pierde en vistas/plantillasGrales/vContent.php
------------------------------------------------------------------------------*/
function vFuncionRolInc($tituloSeccion,$enlacesSeccId,$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos)
{
 if ($_SESSION['vs_autentificado'] !== 'SI')
 { 
   header('Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin');
 }
 else
 {	 
	  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  		
		
   require_once './vistas/login/vCuerpoFuncionRol.php';  

  
   require_once './vistas/plantillasGrales/vPieFinal.php';
 }
}
?>