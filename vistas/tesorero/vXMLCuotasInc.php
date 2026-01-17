<?php
/*-------------------------------------------------------------------------------------------------------------
FICHERO: vXMLCuotasInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para introducir los datos necesarios para generar un archivo "SEPA_ISO20022CORE_fecha_orden_cobro.xml" 
para una remesa con las órdenes de cobro de cuotas domiciliadas para después descargarlo y subirlo a la web
del B. Santander.

A la vez se anotarán esas órdenes de pagos en tabla "ORDENES_COBRO", y en "REMESAS_SEPAXML" que posteriormente 
servirán para actualizar el campo		ESTADOCUOTA =ABONADA, en la tabla "CUOTAANIOASOCIO" una vez que el banco 
haya cobrado esa remesa. 

El formulario permite elegir: 
- Fecha cobro, Fecha excluir de orden de cobro a altas posteriores, 
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España (en este caso por la necesidad de
  BICs, actualmente no puede generar el archivo pero muestra un listado para incluilos en remesas manualmente)
- Agrupaciones Territariales seleccionadas				
- También incluye una grupo de datos fijos, relacionados con la cuenta del B. Santander, necesarios para generar 
  el archivo (están en el formulario como campos "readonly") 
													
LLAMADA: cTesorero.php:XMLCuotasTesoreroSantander()													
LLAMA: vistas/tesorero/vCuerpoXMLCuotas.php

OBSERVACIONES:
------------------------------------------------------------------------------*/
function vXMLCuotasInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$datosFormRemesaBanco)
{			
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoXMLCuotas.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>