<?php
/*-----------------------------------------------------------------------------
FICHERO: datosEmailSimpAlta.php con phpMailer
VERSION: PHP 5.2.3
DESCRIPCION: Prepara los datos necesarios para el email al simpatizante dado 
             de alta en (BBDD)
LLAMADO: desde "controladorSimpatizantes:altaSimpatizante()						 
OBSERVACIONES: Personaliza según el sexo
------------------------------------------------------------------------------*/

	//$datosEnvioEmail['from']="EuropaLaica <adminusers@europalaica.com>";
 //$datosEnvioEmail['replayTo']="avillaco@hotmail.com";	
 $datosEnvioEmail['emailUsuario']=$resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo'];
					
	$nom = $resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];
	$ape1 = $resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];
	$usuario = $resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];	
					
	if ($resValidarCamposForm['datosFormMiembro']['SEXO']['valorCampo']=='H')
	{$datosEnvioEmail['asunto']= "Europa Laica. Nuevo simpatizante";
	
	 $contenido=	"Europa Laica.
	\n\nEstimado ".$nom." ".$ape1.
	" te comunicamos que te hemos registrado como nuevo simpatizante en la aplicación de gestión de usuarios de Europa Laica. 
 \nPara entrar en la aplicación deberás usar el nombre de usuario '".$usuario."' y la contraseña que tú elegiste al registrarte como nuevo simpatizante. Por motivos de seguridad no te la enviamos por email.
 ";	
	}
	else
	{$datosEnvioEmail['asunto']= "Europa Laica. Nueva simpatizante";
	 $contenido=	"Europa Laica.
	\n\nEstimada ".$nom." ".$ape1.
	" te comunicamos que te hemos registrado como nueva simpatizante en la aplicación de gestión de usuarios de Europa Laica. 
 \nPara entrar en la aplicación deberás usar el nombre de usuaria '".$usuario."' y la contraseña que tú elegiste al registrarte como nueva simpatizante. Por motivos de seguridad no te la enviamos por email.
 ";	
	}	
	$datosEnvioEmail['contenido'] =$contenido. 
"	\nSi hubieses olvidadado la contraseña, puedes recuperarla haciendo clic en el enlace https://www.europalaica.com/usuarios 
	y después eligiendo la opción 'Recordar usuari@ y contraseña'
\n\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en	una nueva ventana del navegador	
		\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, es probable que otro usuario haya introducido tu dirección de correo electrónico por error. En este caso no es necesario que realices ninguna acción, y puedes ignorar este mensaje con total seguridad.		
		
---------------------
Un saludo,
Europa Laica
Administrador de la aplicación informática";	
?>