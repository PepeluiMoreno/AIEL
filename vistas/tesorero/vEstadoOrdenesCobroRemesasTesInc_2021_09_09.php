<?php
/*-----------------------------------------------------------------------------
FICHERO: vEstadoOrdenesCobroRemesasTesInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que muestra datos de las remesas de órdenes de cobros 
emitidas a los bancos a partir de las tablas REMESAS_SEPAXML y ORDENES_COBRO.
												
Se puede: 
- Ver la lista de órdenes de cobro de una remesa,
- Eliminar la remesa de las tablas REMESAS_SEPAXML y ORDENES_COBRO en caso de se hubiese 
  producido un	error o por otras causas, siempre que aún no se hayan actualizado los pagos
  de la remesa  
- Descargar el Archivo SEPA de remesa para subirlo a la web del B. Santander		
- Actualizar los pagos de una remesa en la tabla la tabla "CUOTAANIOSOCIO" 

LLAMADA: cTesorero.php:estadoOrdenesCobroRemesasTes()
LLAMA: vistas/tesorero/vCuerpoEstadoOrdenesCobroRemesasTes.php
												
OBSERVACIONES: 
2020-12-10: Añado columna para descargar Archivo SEPA-XML 
2017-08-19: creación
------------------------------------------------------------------------------*/
function vEstadoOrdenesCobroRemesasTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$arrOrdenesCobro)
{
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

 require_once './vistas/tesorero/vCuerpoEstadoOrdenesCobroRemesasTes.php';
 
 require_once './vistas/plantillasGrales/vPieFinal.php'; 
	
}
?>