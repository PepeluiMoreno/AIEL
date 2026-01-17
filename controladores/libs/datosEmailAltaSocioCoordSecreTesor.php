<?php
/*---------------------- Se usa con enviarMultiplesEmailsPhpMailer -----------
FICHERO: datosEmailAltaSocioCoordSecreTesor.php
VERSION: PHP 5.2.3
DESCRIPCION: Prepara los datos necesarios para el email a Secretaria,Tesorería,
             y coordinador, comunicando la confirmación de alta de un socio/a
LLAMADO: controladorSocios						 
OBSERVACIONES: Personaliza según el sexo
------------------------------------------------------------------------------*/						
 //$emailUsuarioCoord = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILCOORD']['valorCampo'];
	
	$agrupNomAgrupacion = $reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];
	$agrupNomEstatal = $reDatosAgrupacionEstatal['resultadoFilas'][0]['NOMAGRUPACION'];
		
	$emailSecretaria = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILSECRETARIO'];
	$datosEnvioEmailCoordSecreTesor['AddAddress']['SECRETARIO']['email'] = $emailSecretaria;
	$datosEnvioEmailCoordSecreTesor['AddAddress']['SECRETARIO']['nombre']= 'Secretaría '.$agrupNomEstatal;
	
	//$datosEnvioEmail['AddAddress'][0]['email'] = $emailSecretaria;
	//$datosEnvioEmail['AddAddress'][0]['nombre']= 'Secretaría de '.$agrupNomEstatal;
	
	$emailTesoreria = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILTESORERO'];
 $datosEnvioEmailCoordSecreTesor['AddAddress']['TESORERO']['email'] = $emailTesoreria;
	$datosEnvioEmailCoordSecreTesor['AddAddress']['TESORERO']['nombre'] = 'Tesorería '.	$agrupNomEstatal;		

 /*$emailCoordinacion = $reDatosAgrupacionSocio['resultadoFilas']['EMAILCOORD'];
 $datosEnvioEmailCoordSecreTesor['AddAddress']['COORDINADOR']['email'] = $emailCoordinacion;	
	$datosEnvioEmailCoordSecreTesor['AddAddress']['COORDINADOR']['nombre'] = $agrupNomAgrupacion;		
	*/

	//$datosEnvioEmailCoordSecreTesor['AddBCC'][0]['email'] = 'avillaco@hotmail.com';	
	//$datosEnvioEmailCoordSecreTesor['AddBCC'][0]['nombre'] = 'nombre usuario BCC';	
	
	$datosEnvioEmailCoordSecreTesor['AddCC'][0]['email'] = 'segvilla50@hotmail.com';	
	$datosEnvioEmailCoordSecreTesor['AddCC'][0]['nombre'] = 'nombre usuario CC';	
	
	//$datosEnvioEmailCoordSecreTesor['AddReplyTo'][0]['email'] = 'adminusers@europalaica.com';	
	//$datosEnvioEmailCoordSecreTesor['AddReplyTo'][0]['nombre'] = 'Administrador de usuarios';
 
	$nom  = $reDatosSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo'];
	$ape1 = $reDatosSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo'];
	$emailUsuario = $reDatosSocio['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo'];
 $fechaAlta = $reDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['valorCampo'];	
 
	$asunto = "Europa Laica. Confirmada alta de socio/a.";
		
	$contenidoCuerpoComun = "Europa Laica. Confirmada alta del socio/a ".$nom." ".$ape1.
	" en la agrupación territorial ".$agrupNomAgrupacion .", con fecha de alta ".
	$fechaAlta." \n\nSu email es:".$emailUsuario;	

	$contenidoPie ="
----------------------
Un saludo,
Europa Laica
Administrador de la aplicación de gestión de socios";			
	
$datosEnvioEmailCoordSecreTesor['fromAddress']='adminusers@europalaica.com';
$datosEnvioEmailCoordSecreTesor['fromAddressName'] = 'Europa Laica. Administrador de usuarios';	

$datosEnvioEmailCoordSecreTesor['subject'] = $asunto;	
$datosEnvioEmailCoordSecreTesor['body'] = $contenidoCuerpoComun.$contenidoPie;		
?>