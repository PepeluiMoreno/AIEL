<?php
/*----------------------------------------------------------------------------------------------
FICHERO: vActualizarSocioPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para actualizar datos personales de un socio, cuotas, IBAN, agrupación y afectará 
a varias varias tablas

LLAMADA: cPresidente.php:actualizarSocioPres()

LLAMA: vistas/presidente/vCuerpoActualizarSocioPres.php
incluye plantillasGrales

OBSERVACIONES
----------------------------------------------------------------------------------------------*/
function vActualizarSocioPresInc($tituloSeccion,$datSocio,$parValorComboActualizarSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboActualizarSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboActualizarSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboActualizarSocio['domicilioPais']; 
	
  require_once './vistas/presidente/vCuerpoActualizarSocioPres.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>