<?php
/*-----------------------------------------------------------------------------------------------
FICHERO: vExportarEmailDomiciliadosPendInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar Emails y otros datos de todos 
las cuotas de los socios y exportar los emails en forma de lista separados por (;)
a un fichero ".txt" para copiar y pegar en el correo de NODO50 (tesoreria@europalica.org), 
y enviar un email a los socios de la lista, con texto libre para avisar a los socios 
para avisar a los socios que se van enviar "las órdenes de cobro de las cuotas domiciliadas"  
para el cobro por el banco (actualmente B.Santander norma SEPA-XML), 
de las agrupaciones elegidas y que están de alta en el momento actual y según la 
siguiente selección:

- Cuenta bancaria en España
- Cuenta bancaria países SEPA (distintos de España) y "Ordenar cobro banco = SI", 
  POR AHORA NO SE PUEDE GENERAR EL SEPA-XML CON ESTA APLICACIÓN por falta cálculo BIC 
  otros países SEPA, pero se podría hacer manualmente en la web del B.Santander si se 
  consiguiesen esos BICs.	

LLAMADA: cTesorero.php: exportarEmailDomiciliadosPend()
LLAMA: vistas/tesorero/vCuerpoExportarEmailDomiciliadosPend.php
													
OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/
function vExportarEmailDomiciliadosPendInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$camposFormDatosElegidos)
{
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoExportarEmailDomiciliadosPend.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>