<?php
/*-----------------------------------------------------------------------------
FICHERO: vMensajeAltaSocioDatosBancosInc.php
Válido para alta por el propio socio, para solicitar o confirmar, 
     con datos bancos procedentes BBDD y paypal con requiere

VERSION: PHP 5.2.3
DESCRIPCION: En la cabecera incluye solo el links para "Salir"
Llamado: desde controladorSocio:altaSocio(), y confirmarAltaSocio
Llama: vCuerpoMensajeAltaSocioDatosBancos.php
OBSERVACIONES:

------------------------------------------------------------------------------*/
//function vMensajeAltaSocioDatosBancosInc($tituloSecc,$arrayParamMensaje,$enlacesSeccIzda)
//function vMensajeAltaSocioDatosBancosInc($tituloSecc,$arrayParamMensaje,$navegacion)
function vMensajeAltaSocioDatosBancosInc($tituloSeccion,$arrayParamMensaje,$resDatosSocio,$payPalScript,$datosSocioPayPal,$cadBancos,$navegacion)															
{ 
 echo "<br><br>0-1 vMensajeAltaSocioDatosBancosInc.php:vMensajeAltaSocioDatosBancosInc:arrayParamMensaje: ";print_r($arrayParamMensaje);
	echo "<br><br>0-2 vMensajeAltaSocioDatosBancosInc.php:vMensajeAltaSocioDatosBancosInc:resDatosSocio: ";print_r($resDatosSocio);
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
	//$datosSocio  = $resDatosSocio['valoresCampos'];
	$datosSocio  = $resDatosSocio;
	//$botonSubmit = $resDatosSocio['arrMensaje'];
	$botonSubmit = $arrayParamMensaje['arrMensaje'];//acaso no se use		
			

	require_once './vistas/socios/vCuerpoMensajeAltaSocioDatosBancos.php';
	
 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>