<?php
/*--------------------------------------------------------------------------------------------------------
FICHERO: vActualizarAgrupacionPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario en el que se muestran los datos de de una agrupación procedentes de la tabla "AGRUPACIONTERRITORIAL" 
para permitir modificar algunos de ellos. 
Los datos CIF, CUENTAAGRUPIBAN, TELFIJOTRABAJO, TELMOV,  se validan previamente 

RECIBE: array arrDatosAgrupacion con los datos de una agrupación de tabla "AGRUPACIONTERRITORIAL" 
y $navegación

LLAMADA: cPresidente.php:actualizarDatosAgrupacionPres()

LLAMA: vistas/presidente/vCuerpoActualizarAgrupacionPres.php
incluye plantillasGrales

OBSERVACIONES:
--------------------------------------------------------------------------------------------------------*/

function vActualizarAgrupacionPresInc($tituloSeccion,$arrDatosAgrupacion,$navegacion)
{ 
  //echo "<br><br>vActualizarAgrupacionPresInc:arrDatosAgrupacion: ";print_r($arrDatosAgrupacion);
		
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  require_once './vistas/presidente/vCuerpoActualizarAgrupacionPres.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>