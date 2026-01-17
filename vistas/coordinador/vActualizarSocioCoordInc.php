<?php
/*--------------------------------------------------------------------------------------------
FICHERO: vActualizarSocioCoordInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario el coordinador, actualiza  datos personales de un socio, cuotas, IBAN, 
agrupación,  afecta a varias varias tablas.

RECIBE: array $datSocio con los datos del socio, $parValorComboActualizarSocio

LLAMADA: cCoordinador.php:actualizarSocioCoord(), en lista de socios desde el 
icono Modifica = Pluma

Incluye las plantillas: vistas/plantillasGrales/vCabeceraSalir.php,
vistas/plantillasGrales/vPieFinal.php

OBSERVACIONES:
----------------------------------------------------------------------------------------------*/
function vActualizarSocioCoordInc($tituloSeccion,$datSocio,$parValorComboActualizarSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboActualizarSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboActualizarSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboActualizarSocio['domicilioPais']; 
	
  require_once './vistas/coordinador/vCuerpoActualizarSocioCoord.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>