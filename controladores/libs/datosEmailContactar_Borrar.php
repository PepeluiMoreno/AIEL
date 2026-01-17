<?php
/*-----------------------------------------------------------------------------
FICHERO: datosEmailContactar.php
VERSION: PHP 5.2.3
DESCRIPCION: Prepara los datos necesarios para el email del contacto (pie pág) 
   
LLAMADO: desde "cEnlacesPie:contactarEmail()						 
OBSERVACIONES:
------------------------------------------------------------------------------*/
$datosEnvioEmail['from'] = $resValidarCamposForm['datosContactarEmail']['EMAIL']['valorCampo'];
$datosEnvioEmail['replayTo'] = "secretaria@europalaica.com";	
$datosEnvioEmail['emailUsuario'] = "secretaria@europalaica.com";

$datosEnvioEmail['asunto']= $resValidarCamposForm['datosContactarEmail']['ASUNTO']['valorCampo'];

$datosEnvioEmail['contenido']="EuropaLaica. Contactar.<br /><br /> Nombre de la persona que envía email 'Contactar': <b>".
			 $resValidarCamposForm['datosContactarEmail']['NOMBRE']['valorCampo'].
				"</b><br /><br /> Texto del mensaje:<br /><br />". $resValidarCamposForm['datosContactarEmail']['TEXTOMENSAJE']['valorCampo'];	

?>