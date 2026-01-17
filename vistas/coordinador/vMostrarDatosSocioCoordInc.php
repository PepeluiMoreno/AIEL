<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: vMostrarDatosSocioCoordInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario vMostrarDatosSocioCoordInc se muestran los datos de un socio al coordinador 
sin permitir modificaciones.

RECIBE: array $resDatosSocio con los datos del socio

LLAMADA: cCoordinador.php:mostrarDatosSocioCoord(), en lista de socios desde el icono Ver = Lupa

Incluye las plantillas: vistas/plantillasGrales/vCabeceraSalir.php,
vistas/plantillasGrales/vPieFinal.php

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/
function vMostrarDatosSocioCoordInc($tituloSeccion,$resDatosSocio,$navegacion)	
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 $datosSocio = $resDatosSocio['valoresCampos'];
	
  require_once './vistas/coordinador/vCuerpoMostrarDatosSocioCoord.php';
	
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>