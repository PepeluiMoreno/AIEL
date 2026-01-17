<?php
/*-----------------------------------------------------------------------------
FICHERO: vInfOrdenCobroCuotasDomiciliadas.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra la información sobre el procedimiento de enviar remesas al B. Santander 
con las órdenes de cobro de las cuotas domiciliadas en bancos de España y bancos SEPA

LLAMADA: cTesorero.php:infOrdenCobroCuotasDomiciliadas()
LLAMA: vistas/tesorero/vCuerpoInfOrdenCobroCuotasDomiciliadas.php

OBSERVACIONES: 2020-11-27 creación script
------------------------------------------------------------------------------*/
function vInfOrdenCobroCuotasDomiciliadas($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoInfOrdenCobroCuotasDomiciliadas.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>