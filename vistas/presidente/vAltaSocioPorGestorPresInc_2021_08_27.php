<?php
/*-----------------------------------------------------------------------------
FICHERO: vAltaSocioPorGestorPresInc.php

VERSION: PHP 7.3.21
DESCRIPCION: Es el formulario para el registro de un nuevo socio por un gestor con rol Presidencia
             Incluye subir archivo con firma del socio, y relacionado con esto se establece aquí 
													lo valores fijos para variables relacionadas con ese archivo:
													[MaxArchivoSize], [directorioSubir]="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" ,
													[maxLongNomArchivoSinExtDestino]"="250", [permisosArchivo]"="0444" />solo lectura
														(se podrían haber recibido desde el cPresidente.php:altaSocioPorGestorPres())				
													
LLAMADA: cPresidente.php:altaSocioPorGestorPres()
LLAMA: vistas/presidente/vCuerpoAltaSocioPorGestorPres.php											

OBSERVACIONES: Igual a vAltaSocioPorGestorCoordInc.php, y vAltaSocioPorGestorTesInc.php, 
excepto en título. 
"vAltaSocioPorGestorInc.php" es una alternativa que unifica los tres 
pero es menos flexible		
------------------------------------------------------------------------------*/
function vAltaSocioPorGestorPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosNavegacion,$datosSocio,$parValorComboAltaSocio)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboAltaSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboAltaSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboAltaSocio['domicilioPais']; 
	
  require_once './vistas/presidente/vCuerpoAltaSocioPorGestorPres.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>