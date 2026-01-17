<?php
/*----------------------------------------------------------------------------------------------
FICHERO: vActualizarIngresoCuotaInc.php
VERSION: PHP 7.3.21

Se actualiza el ingreso de una cuota del socio de ese año por parte del tesorero 

Se dan dos situaciones que requieren un tratamiento distinto según exista en ese momento para ese socio, 
una orden de cobro de remesa en el banco pendiente de efectuar que dará lugar a dos forms distintos

Desde vActualizarIngresoCuotaInc.php-->vCuerpoActualizarIngresoCuotaInc.php se dirige a dos posibles forms: 

A- formActualizarIngresoCuotaTodos.php: "caso de NO tener una orden de cobro de remesa pendiente en el banco"

			Es el form para actualizar ingreso de cuota para año actual y anteriores donde se puede añadir y modificar datos 
			de Ingreso Cuota en CUOTAANIOSOCIO, (pero sólo cuando en tabla "ORDENES_COBRO" 
			el ESTADOCUOTA !=PENDIENTE-COBRO), para ese socio y año
			
B- formActualizarIngresoCuotaObservaciones.php: "caso de SÍ tener un cobro de remesa en el banco pendiente"

			Es el form alternativo y es para pendientes de cobro de remesa en el banco, ya emitida 
			(sólo cuando en tabla "ORDENES_COBRO" el ESTADOCUOTA =PENDIENTE-COBRO), para ese socio y año 
			y por eso solo se permite	cambiar los campos "Observaciones y Motivo devolución" para evitar 
			cambios en	cuotas, pagos y gastos producir inconsistencias respecto a la remesa ya enviada al banco. 


En cuerpo vCuerpoActualizarIngresoCuota.php, se hace la selección del form correspondiente:
"formActualizarIngresoCuotaTodos.php" o "formActualizarIngresoCuotaObservaciones.php",
según el valor de ESTADOCUOTA en la tabla ORDENES_COBRO, es decir según el valor de: 
$datSocio['formOrdenesCobro']['ESTADOCUOTA']

LLAMADA: cTesorero.php:actualizarIngresoCuota()		
LLAMA: /vistas/tesorero/vCuerpoActualizarIngresoCuota.php';

OBSERVACIONES
----------------------------------------------------------------------------------------------*/
function vActualizarIngresoCuotaInc($tituloSeccion,$datSocio,$navegacion)
{ 
  //echo "<br><br>1-vActualizarIngresoCuotaInc:datSocio: ";print_r($datSocio);
  
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
	
  require_once './vistas/tesorero/vCuerpoActualizarIngresoCuota.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>