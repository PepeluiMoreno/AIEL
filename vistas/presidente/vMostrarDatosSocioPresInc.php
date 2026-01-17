<?php

/*--------------------------------------------------------------------------------------------------
FICHERO: vMostrarDatosSocioPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario vMostrarDatosSocioPresInc se muestran los datos de un socio al rol Presidente 
sin permitir modificaciones.

RECIBE: array $resDatosSocio con los datos del socio

LLAMADA: cPresidente.php:mostrarDatosSocioPres(), en lista de socios desde el icono Ver = Lupa

Incluye las plantillas: vistas/plantillasGrales/vCabeceraSalir.php,
vistas/plantillasGrales/vPieFinal.php

OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/
function vMostrarDatosSocioPresInc($tituloSeccion,$resDatosSocio,$navegacion)	
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 $datosSocio=$resDatosSocio['valoresCampos'];
		
  require_once './vistas/presidente/vCuerpoMostrarDatosSocioPres.php';
	
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>