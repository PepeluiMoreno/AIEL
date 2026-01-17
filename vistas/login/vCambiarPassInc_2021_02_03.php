<?php
/*-----------------------------------------------------------------------------
FICHERO: vCambiarPassInc.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene los includes necesarios para formar la página de cambiar
              contraseña. 
						       Recibe como parámetro "$tituloSeccion" un texto que se podría usar
             para alguna cabecera, y "$datosPass" que contiene los mensajes error
             después de la validación y que se utilizarán en "vCuerpoCambiarPass.php"
             y más concretamente en "formCambiarPassUser.php"
													
OBSERVACIONES: Es llamado desde las controladorLogin:cambiarPassUser()             
------------------------------------------------------------------------------*/
function  vCambiarPassInc($tituloSeccion,$datosPass)
{
	  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	  require_once  './vistas/login/vCuerpoCambiarPass.php';
	 
	  require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>