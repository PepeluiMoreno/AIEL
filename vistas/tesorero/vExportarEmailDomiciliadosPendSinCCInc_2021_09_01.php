<?php
/*-----------------------------------------------------------------------------------------------------------
FICHERO: vExportarEmailDomiciliadosPendSinCCInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar Emails y otros datos de todos 
las cuotas de los socios y exportar los emails en forma de lista separados por (;)
a un fichero ".txt" para copiar y pegar en el correo de NODO50 (tesoreria@europalica.org), 
y enviar un email a los socios de la lista, con texto libre para avisar a los socios 
que aún "no han abonado la cuota" que están de alta en el momento actual y según
la siguiente selección:

- No tiene cuenta bancaria domiciliada, - Tiene cuenta bancaria de países NO SEPA (o no es IBAN, ya 
  no se permite CUENTAS NO IBAN, este caso devolverá 0 socios), - Cuenta bancaria de países SEPA (distintos
 	de España), junto con "Ordenar cobro banco = NO" (por falta de BIC necesario para otros países SEPA, 
		por eso se envía este email de aviso no pagado)
- FechaAltaExentosPago		
- Agrupaciones seleccionadas

RECIBE: 
- $camposFormDatosElegidos desde controlador: (año cuota, fecha exención pago, cuenta banco ..., agrupaciones)

LLAMADA: cTesorero.php: exportarEmailDomiciliadosPendSinCC()
LLAMA: vistas/tesorero/vCuerpoExportarEmailDomiciliadosPendSinCC.php
													
OBSERVACIONES:
-----------------------------------------------------------------------------------------------------------*/
function vExportarEmailDomiciliadosPendSinCCInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$camposFormDatosElegidos)
{
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoExportarEmailDomiciliadosPendSinCC.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>