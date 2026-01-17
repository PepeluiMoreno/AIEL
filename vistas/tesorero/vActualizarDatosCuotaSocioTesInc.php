<?php
/*----------------------------------------------------------------------------------------------
FICHERO: vActualizarDatosCuotaSocioTesInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario para la actualización de la cantidad y tipo de cuota elegida por el socio, 
los datos bancarios, ORDENARCOBROBANCO y OBSERVACIONES y otros datos personales

LLAMADA: cTesorero.php:actualizarDatosCuotaSocioTes()

LLAMA: /vistas/tesorero/vCuerpoActualizarDatosCuotaSocioTes.php
incluye plantillasGrales

OBSERVACIONES
----------------------------------------------------------------------------------------------*/
function vActualizarDatosCuotaSocioTesInc($tituloSeccion,$datSocio,$parValorComboActualizarSocio,$navegacion)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
	 $parValorComboAgrupaSocio   = $parValorComboActualizarSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboActualizarSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboActualizarSocio['domicilioPais']; 
		
  require_once './vistas/tesorero/vCuerpoActualizarDatosCuotaSocioTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>