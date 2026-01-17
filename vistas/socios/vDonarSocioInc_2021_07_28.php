<?php
/*-----------------------------------------------------------------------------
FICHERO: vDonarSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Se muestran los datos de los bancos ($cadBancos) donde puede donar un socio 
(los correspondientes a sus agrupaciones de cobro de cuotas) y un enlace a 
un botón de PayPal para donar (por ahora solo el de EL).

$payPalScriptDona: incluye la dirección del script para hacer donación con PayPal 
(botón estandar para ESTATAL)

En la cabecera incluye solo el links para "Salir"

LLAMADA:  controladoSocios.php:donarSocio()

OBSERVACIONES:Válido para donaciones por el propio socio
------------------------------------------------------------------------------*/
function vDonarSocioInc($tituloSeccion,$payPalScriptDona,$cadBancos,$navegacion)
{ 
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/socios/vCuerpoDonarSocio.php';
	
 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>