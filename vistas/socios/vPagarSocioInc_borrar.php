<?php
/*-----------------------------------------------------------------------------
FICHERO: vPagarSocioInc.php


VERSION: PHP 5.2.3
DESCRIPCION: En la cabecera incluye solo el links para "Salir"
OBSERVACIONES:Llamado desde controladores
------------------------------------------------------------------------------*/
function vPagarSocioInc($tituloSecc,$arrayParamMensaje,$enlacesSeccIzda)
{ 
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/socios/vCuerpoPagarSocio.php';
	
 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>