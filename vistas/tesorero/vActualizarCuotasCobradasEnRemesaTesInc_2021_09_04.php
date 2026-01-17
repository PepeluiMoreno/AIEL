<?php
/*----------------------------------------------------------------------------------------
FICHERO: vActualizarCuotasCobradasEnRemesaTesInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

Formulario que muestra los datos de una remesa SEPA XML y pide confirmar para 
actualizar la tabla "CUOTAANIOSOCIOS", SOCIO (FRST->RCUR), "ORDENES_COBRO" 
y "REMESAS_SEPAXML" a partir de las filas de orden de pago de cada cuota en tabla 
"ORDENES_COBRO" de una remesa concreta que se buscará por "NOMARCHIVOSEPAXML"

Se elimina el archivo "NOMARCHIVOSEPAXML" del servidor una vez actualizas las 
tablas antes citadas.

Se pide fecha pago por el banco e importe de gastos y comisiones cobrados por el banco

RECIBE: "$arrOrdenesCobro" con datos de la remesa enviada al banco para mostrar.

LLAMADA: cTesorero.php:actualizarCuotasCobradasEnRemesaTes()	
LLAMA: vistas/tesorero/vistas/tesorero/vCuerpoActualizarCuotasCobradasEnRemesaTes.php
Incluye plantillasGrales
           
OBSERVACIONES: Solo se actualiza una vez que esté cobrada por el banco
------------------------------------------------------------------------------------------*/
function  vActualizarCuotasCobradasEnRemesaTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$arrOrdenesCobro)
{
	//echo "<br /><br />1 vActualizarCuotasCobradasEnRemesaTesInc:arrOrdenesCobro: ";print_r($arrOrdenesCobro);

	require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/tesorero/vCuerpoActualizarCuotasCobradasEnRemesaTes.php';
	
	require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>