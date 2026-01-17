<?php
/*-----------------------------------------------------------------------------
FICHERO: vEmailAvisarDomiciliadosProximoCobroInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección para buscar los Emails y otros
datos de las cuotas de los socios que tienen domicialiación bancaria	del pago
de las cuotas mediante su cuenta IBAN, para envíar un email personalizado con
su nombre, APE1, CCIBAN, Cuota, a los socios avisando de próximo cobro de cuotas.
También incluye el texto común que se enviará en el email.

La selección que se realiza para estados cuatos: 'PENDIENTE-COBRO','ABONADA-PARTE',
con cuenta en España o en país SEPA (por ahora no porque hay problemas con BIC),
siempre que están de "alta" en el momento actual y NO INCLUYE las cuotas
"abonadas,exentas, y socios dados de baja", y ORDENARCOBROBANCO=SI y de sólo de 
las "AGRUPACIONES" elegidas en el formulario.

A partir de una fecha de alta que se introduce en el formulario se excluye el 
envío a esos socios ( para excluir pagar cuotas altas en noviembre y diciembre)
Se envía con cuenta "tesoreria@europalaica.org"

Para formar la lista de emails, desde formEmailAvisarDomiciliadosProximoCobro.php
se puede elegir:
- Cuenta bancaria en España
- Cuenta bancaria de países SEPA (distintos de España)
		
Más información en formEmailAvisarDomiciliadosProximoCobro.php
		
LLAMADA: cTesorero.php:emailAvisarDomiciliadosProximoCobro()
LLAMA:	vistas/tesorero/vCuerpoEmailAvisarDomiciliadosProximoCobro.php									
													
OBSERVACIONES:
2020-10-11: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza. Cambio Nombre parámetro $camposFormDatosElegidos
------------------------------------------------------------------------------*/
function vEmailAvisarDomiciliadosProximoCobroInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$camposFormDatosElegidos)
{
	 //echo "<br><br>vistas/tesorero:vEmailAvisarDomiciliadosProximoCobroInc:camposFormDatosElegidos: ";print_r($camposFormDatosElegidos);	
	
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoEmailAvisarDomiciliadosProximoCobro.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>