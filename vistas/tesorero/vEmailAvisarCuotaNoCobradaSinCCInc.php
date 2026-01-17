<?php
/*-----------------------------------------------------------------------------
FICHERO: vEmailAvisarCuotaNoCobradaSinCCInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección de campos para buscar Emails y 
otros datos de todos las cuotas de los socios, que tienen NO domicialiación 
bancaria	del pago	de las cuotas mediante su cuenta IBAN, para envíar un email
personalizado a los socios comunicándoles que aún no han pagado la cuota anual
de la asociación Europa Laica.
Envía nombre y APE1, cuota, datos bancos Europa Laica, enlace a PayPal con 
cuota abierta a pagar y también enlace a web de laicismo.org con informacion 
gastos-ingresos.

INCLUYE las cuotas de los socios/as:'PENDIENTE-COBRO','ABONADA-PARTE',
'NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA',['ORDENARCOBROBANCO']='todos los casos'
siempre que están de "alta" en el momento actual y NO INCLUYE las cuotas "abonadas,
exentas, y socios/as que estén de baja" y de sólo de las "AGRUPACIONES" elegidas 
en el formulario. 

A partir de una fecha de alta que se introduce en el formulario se excluye el 
envío a esos socios ( para excluir pagar cuotas altas en noviembre y diciembre)
Se envía con cuenta "tesoreria@europalaica.org"

Para formar la lista de emails, desde formEmailAvisarCuotaNoCobradaSinCC.php 
se puede elegir:
-No tiene cuenta bancaria domiciliada
-Tiene cuenta bancaria de países NO SEPA (o no está en formato IBAN)
-Cuenta bancaria de países SEPA (distintos de España).
						
Más información en formEmailAvisarCuotaNoCobradaSinCC.php
						
LLAMADA: cTesorero.php:emailAvisarCuotaNoCobradaSinCC()
LLAMA:	vistas/tesorero/vCuerpoEmailAvisarCuotaNoCobradaSinCC.php									
													
OBSERVACIONES:
2020-10-11: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza. Cambio Nombre parámetro $camposFormDatosElegidos														
------------------------------------------------------------------------------*/
function vEmailAvisarCuotaNoCobradaSinCCInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$camposFormDatosElegidos)
{
	 //echo "<br><br>vistas/tesorero:vEmailAvisarDomiciliadosProximoCobroInc:camposFormDatosElegidos: ";print_r($camposFormDatosElegidos);	
	
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoEmailAvisarCuotaNoCobradaSinCC.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>