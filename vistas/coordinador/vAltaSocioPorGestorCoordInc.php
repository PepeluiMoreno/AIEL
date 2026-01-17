<?php
/*-----------------------------------------------------------------------------
FICHERO: vAltaSocioPorGestorCoordInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para el registro de un nuevo socio por un gestor con rol Tesorería
             Incluye subir archivo con firma del socio, y relacionado con esto se establece aquí 
													lo valores fijos para variables relacionadas con ese archivo:
													[MaxArchivoSize], [directorioSubir]="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" ,
													[maxLongNomArchivoSinExtDestino]"="250", [permisosArchivo]"="0444" />solo lectura 
             	(se podrían haber recibido desde el cCoordinador.php:altaSocioPorGestorcoord())									

LLAMADA: cCoordinador.php:altaSocioPorGestorCoord()
LLAMA: vistas/coordinador/vCuerpoAltaSocioPorGestorCoord.php

OBSERVACIONES: Igual a vAltaSocioPorGestorTesInc.php, y vAltaSocioPorGestorPresInc.php, 
excepto en título. "vAltaSocioPorGestorInc.php" es una alternativa que unifica los tres 
pero es menos flexible		
------------------------------------------------------------------------------*/
function vAltaSocioPorGestorCoordInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosNavegacion,$datosSocio,$parValorComboAltaSocio)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboAltaSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboAltaSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboAltaSocio['domicilioPais']; 
	
  require_once './vistas/coordinador/vCuerpoAltaSocioPorGestorCoord.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>