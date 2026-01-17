<?php
/*------------------------------------------------------------------------------
FICHERO: vCambiarPassSocioInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene los includes necesarios para formar la página de cambiar
             contraseña.
						       Recibe como parámetro "$tituloSeccion" un texto que se podría usar
             para alguna cabecera, y "$datosPass" que contiene los mensajes error
             después de la validación y que se utilizarán en "vCuerpoCambiarPassSocio.php"
             y más concretamente en "formCambiarPassSocio.php"
													
LLAMADA: controladorSocios.php:cambiarPassSocio()  		
LLAMA: vCuerpoCambiarPassSocio.php y plantillas generales						
													
OBSERVACIONES: Es llamado desde las controladorSocios.php:cambiarPassSocio()             
------------------------------------------------------------------------------*/
function  vCambiarPassSocioInc($tituloSeccion,$datosPass,$navegacion)
{
	  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	  require_once  './vistas/socios/vCuerpoCambiarPassSocio.php';
	 
	  require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>