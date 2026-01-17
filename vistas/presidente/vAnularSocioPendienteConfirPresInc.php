<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnularSocioPendienteConfirPresInc.php
VERSION: PHP 7.3.21

En este formulario se algunos datos personales de un "casi" socio que inició el alta por él mismo 
y aún está "PENDIENTE-CONFIRMACION" su alta por él mismo. 
En el formulario en un botón se pide confirmación para anular el intento de alta del socio. 
También botón "No eliminar", pide segunda confimación 

LLAMADA: cPresidente.php:anularSocioPendienteConfirmarPres():
y previamente desde cPresidente.php:mostrarEstadoConfirmacionSocios() 

LLAMA: vistas/presidente/vCuerpoAnularSocioPendienteConfirPres.php e incluye plantillasGrales. 

OBSERVACIONES: 
-----------------------------------------------------------------------------------------------------*/
function vAnularSocioPendienteConfirPresInc($tituloSeccion,$datSocioPendienteConfirmar,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 
  require_once './vistas/presidente/vCuerpoAnularSocioPendienteConfirPres.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>