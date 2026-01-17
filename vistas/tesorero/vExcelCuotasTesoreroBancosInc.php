<?php

/*---------------------------------------------------------------------------------------------------------
FICHERO: vExcelCuotasTesoreroBancosInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para selección de opciones para generar y exportar a un archivo Excel las órdenes 
de pago de las cuotas de los socios, (se utilizaba para las remesas de órdenes de cobro en B. Tríodos) 
y ahora también es útil para uso interno de tesorería, cuando se genera y descarga a continuación de generar 
el archivo XML SEPA para el B. Santander (con los mismos criterios de selección) y así el Excel puede servir
para contrastar los totales y otros datos y como un listado para anotar las 
devoluciones e incidencias de la remesa

Permitirá elegir:
- Excluir de la orden de cobro a los socios/as con alta después de una fecha	
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España
- Agrupaciones Territariales seleccionadas	 y agrupaciones y otros datos necearios
													
LLAMADA: cTesorero.php:excelCuotasTesoreroBancos()													
LLAMA: vistas/tesorero/vCuerpoExcelCuotasTesoreroBancos.php

OBSERVACIONES:	Probado PHP 7.3.21							
												
----------------------------------------------------------------------------------------------------------*/
function vExcelCuotasTesoreroBancosInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$datosExcelCuotas)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoExcelCuotasTesoreroBancos.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>