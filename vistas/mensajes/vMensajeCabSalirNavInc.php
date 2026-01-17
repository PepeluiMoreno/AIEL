<?php
/*-----------------------------------------------------------------------------
FICHERO: vMensajeCabSalirNavInc.php 
VERSION: PHP 5.2.3
	Fecha actualización: Agustin 2016-03-31,
		 Cambio $tituloSecc, por $tituloSeccion, si no se pierde en vistas/plantillasGrales/vContent.php
DESCRIPCION: En la cabecera incluye solo el links para "Salir"
OBSERVACIONES:Llamado desde controladores
------------------------------------------------------------------------------*/
//function vMensajeCabSalirNavInc($tituloSecc,$arrayParamMensaje,$enlacesSeccIzda,$navegacion)
function vMensajeCabSalirNavInc($tituloSeccion,$arrayParamMensaje,$enlacesSeccIzda,$navegacion)
{ //echo "dentro vCuerpoMensajeSalirInc ";
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/mensajes/vCuerpoMensajeNav.php';
	//vCuerpoMensaje1($tituloSecc,$arrayParamMensaje,$enlacesSeccIzda);

 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>