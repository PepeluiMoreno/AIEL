<?php

/*--------------------------------------------------------------------------------------------------
FICHERO: vMostrarDatosAgrupacionPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario se muestran los datos de una AGRUPACION TERRITORIAL procedentes de la 
tabla "AGRUPACIONTERRITORIAL".

RECIBE: array "$arrDatosAgrupacion" con los datos de la agrupación

LLAMADA: cPresidente.php:mostrarDatosAgrupacionPres(), vistas/presidente/vMostrarDatosAgrupacionPresInc.php 
Previamente se llama desde "LISTADO DE AGRUPACIONES ", al hacer clic en el icono "Ver = Lupa"
Para solo lectura 

LLAMA: vistas/presidente/vCuerpoMostrarDatosAgrupacionPres.php
Incluye las plantillas: vistas/plantillasGrales/vCabeceraSalir.php,
vistas/plantillasGrales/vPieFinal.php

OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/

function vMostrarDatosAgrupacionPresInc($tituloSeccion,$arrDatosAgrupacion,$navegacion)
{ 
  //echo "<br /><br />vMostrarDatosAgrupacionPresInc.php.arrDatosAgrupacion: ";print_r($arrDatosAgrupacion);

  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  require_once './vistas/presidente/vCuerpoMostrarDatosAgrupacionPres.php';
	
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>