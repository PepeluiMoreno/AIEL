<?php
/*-----------------------------------------------------------------------------
FICHERO: datosPhpMailerContactar.php
VERSION: PHP 5.2.3
DESCRIPCION: Prepara los datos necesarios para el email del contacto (pie pág) 
   
LLAMADO: desde "cEnlacesPie:contactarEmail()						 
OBSERVACIONES:
------------------------------------------------------------------------------*/
//echo "<br /><br />Dentro de datosEmailContactar.php:";print_r($resValidarCamposForm['datosContactarEmail']);

//$datosEnvioEmail['toAddress'] = 'info@europalaica.org';	
//$datosEnvioEmail['toAddressName'] = 'Europa Laica';
$datosEnvioEmail['toAddress'] = 'adminusers@europalaica.com';	//poner lo que se vea en el formulario
$datosEnvioEmail['toAddressName'] = 'Administrador de Europa Laica';
$datosEnvioEmail['fromAddress'] = $resValidarCamposForm['datosContactarEmail']['EMAIL']['valorCampo'];
$datosEnvioEmail['fromAddressName'] = $resValidarCamposForm['datosContactarEmail']['NOMBRE']['valorCampo'];

$datosEnvioEmail['subject'] = $resValidarCamposForm['datosContactarEmail']['ASUNTO']['valorCampo'];

$datosEnvioEmail['body'] = "Europa Laica: Contactar
 Nombre de la persona que envía email 'Contactar': ".$resValidarCamposForm['datosContactarEmail']['NOMBRE']['valorCampo'].				
	"Texto del mensaje:".$resValidarCamposForm['datosContactarEmail']['TEXTOMENSAJE']['valorCampo'];	
	
/*$datosEnvioEmail['arrMensaje']['textoCabecera'] ='Enviar email';
$datosEnvioEmail['arrMensaje']['textoBoton'] = 'Aceptar';
$datosEnvioEmail['arrMensaje']['textoComentarios'] = 'Te responderemos lo antes posible. Se ha enviado tu email a: <b>'.$datosEnvioEmail['address'].'</b>'; 	
*/
?>