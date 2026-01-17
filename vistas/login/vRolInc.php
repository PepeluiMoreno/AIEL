<?php
/*-----------------------------------------------------------------------------
FICHERO: vRolInc.php
PROYECTO: EL		
VERSION: PHP 7.3.19
DESCRIPCION: ontiene menú idz de roles según tipo de usuario y y la parte central del cuerpo
 de la página incluido descargar archivo de documento alta socio

LLAMADA: controladorLogin.php:menuRolesUsuario()
OBSERVACIONES: El parámetro "$enlacesRolSeccId" recibe los links del menú de cada
              usuario según roles
------------------------------------------------------------------------------*/
function vRolInc($tituloSeccion,$enlacesRolSeccId,$navegacion)
{
	//echo '<br><br>0-1 vRolInc.php:_SESSION: '; print_r($_SESSION);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoGestor'] !== 'SI')
 { header('Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin');
 }
 else
 {
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
   
		require_once './vistas/login/vCuerpoRol.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
 }
}
?>