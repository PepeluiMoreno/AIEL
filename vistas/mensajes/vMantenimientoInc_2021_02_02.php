<?php
/*-----------------------------------------------------------------------------
FICHERO: vMantenimientoInc.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene los includes necesarios para formar la página 
             de mensaje de Mantenimiento.
													
LLAMADA: controladorLogin.php:recordarLogin(),establecerPass(),cambiarPassUser(),													

OBSERVACIONES: 
SOLO PARA OPERACIONES DE MANTENIMIENTO               
------------------------------------------------------------------------------*/
function  vMantenimientoInc($texto1,$error)
{			
			require_once './vistas/plantillasGrales/vCabeceraSalir.php';//solo tiene enlace salir			

	  require_once  './vistas/mensajes/vCuerpoMantenimiento.php';

	  require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>