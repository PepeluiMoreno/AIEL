<?php
/*-----------------------------------------------------------------------------
FICHERO:vMensajeVolverPayPalInc.php
Mensaje informativo de vuelta despues de Cancelar o Pagar con PayPal
El boton aceptar cierra la ventana informativa

VERSION: PHP 5.2.3
DESCRIPCION: En la barra de cabecera incluye solo  links para "Donar" y "Salir"
OBSERVACIONES:Llamado desde desde cPayPal.php: confirmarPagoPayPalEmail(), confirmadoPagoAltaSocioPayPal_Registrarse()
              cancelacionDonacionPayPal() y otros  como retorno de cancelación, o pago de la donación
------------------------------------------------------------------------------*/
function vMensajeVolverPayPalInc($tituloSeccion,$arrayParamMensaje,$navegacion)	
{ 

 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/PayPal/vCuerpoMensajeVolverPayPal.php';
	
 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>