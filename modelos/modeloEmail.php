<?php
/*------------------------------------------------------------------------------------------------
FICHERO: modeloEmail.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye las funciones para enviar emails en diversos procesos:
													altas bajas, socios, email a socios, etc.
													
													-clase PhpMailer 6.5.4
													
             Llamadas desde los controladores 
			
OBSERVACIONES: probado PHP 7.3.21

Las funciones "enviarMultiplesEmailsPhpMailer()" y enviarEmailPhpMailer() 
permiten los emails en modo HTML y también modo texto plano. 

Version PhpMailer 6.5.4

Amplio opción SEXO para que haya una opción "Neutra" distinta de: (H, Hombre) y (M Mujer) 
-------------------------------------------------------------------------------------------------*/

//===== INICIO ENVIAR EMAILS A SOCIOS PARA RECORDAR USUARIOS Y CONTRASEÑAS =======================

/*--- Inicio emailRestablecerPass con enviarEmailPhpMailer ---------------------------------------
Genera un email que se envía al usuario, a petición del mismo, para restablecer una 
nueva contraseña. 

Le llegará un link con el codUser encriptado, para crear otra nueva contraseña

LLAMADA: desde controladorLogin:recordarLogin()
Barra horizontal superior en inicio "Recodar usurio/a y contraseña"

OBSERVACIONES: probado PHP 7.3.21
------------------------------------------------------------------------------------------------*/
function emailRestablecerPass($resulEmailLogin)
{	
 //echo "<br><br>0-1 modeloEmail:emailRestablecerPass:resulEmailLogin: ";print_r($resulEmailLogin);
 //resulEmailLogin=Array([CODUSER]=>2950 [ESTADO]=>alta [USUARIO]=>agus54 [PASSUSUARIO]=>..c937401e.. [EMAIL]=>fulanito@gmail.com)
		
	$reEnvioEmail['nomScript'] = "modeloEmail.php";		
	$reEnvioEmail['nomFuncion'] = "emailRestablecerPass";
 $reEnvioEmail['codError'] = '00000';
	$reEnvioEmail['errorMensaje'] = '';	

	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
 $codUser = encriptarBase64($resulEmailLogin['CODUSER']);
	
	$asunto= "Europa Laica. Recordar datos identificación";
	
	$contenido = "Europa Laica. Petición de restablecimiento de la contraseña
\nPara el restablecimiento de la contraseña debes hacer clic en el siguiente enlace:
<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=restablecerPass&amp;parametro=".$codUser."'>
https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=restablecerPass&amp;parametro=".$codUser."</a>".
"\n\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador.
\n\nSi has recibido este correo electrónico y no has pedido el restablecimiento de la contraseña, otra persona habrá introducido tu dirección de correo electrónico por error. En este caso no es necesario que realices ninguna acción, y puedes ignorar este mensaje con total seguridad.";
	
	$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA.
	\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
https://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie ="\n
	---------------------
	Un saludo,
	Europa Laica
	Gestión de socios/as";

	$datosEnvioEmail = array("fromAddress" 				   	=>'secretaria@europalaica.org',
																										"fromAddressName" 				=>'Secretaría de Europa Laica',
																										"replayToAddress"		  	=>'secretaria@europalaica.org',
																										"replayToAddressName"	=>'Secretaría de Europa Laica',																								
																										"toAddress"  		  					=>$resulEmailLogin['EMAIL'],
																										"subject" 		  								=>$asunto,
																										"body"		 													=>$contenido.$proteccionDatos.$contenidoPie																											
																							  );																						

	//echo "<br><br>1 modeloEmail:emailRestablecerPass:datosEnvioEmail: ";print_r($datosEnvioEmail);			
					
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);//en modeloEmail.php 

 //echo "<br><br>2 modeloEmail:emailRestablecerPass:reEnvioEmail: ";print_r($reEnvioEmail); 	
   																																																																			
	/*------ Inicio tratamiento de errores ----------------------------------------*/		

	if ($reEnvioEmail['codError']=='00000')
	{ 
		 $reEnvioEmail['textoComentarios'] = "Ha sido enviado un email a la dirección <b>".$datosEnvioEmail['toAddress'].
			  "</b> con un enlace para restablecer tu contraseña. <br /><br />					
					Si no te llega en un tiempo razonable, mira en la carpeta 'Correo no deseado' o en 'Spam'. 
					<br /><br /><br /><br />
					NOTA: Outlook y otros navegadores a veces rechazan correos enviados desde Europa Laica.
					<br /><br /> Si no lo has recibido, abre en hotmail la carpeta 'Correo no deseado' 
					y si encuentras un correo enviado por Europa Laica, lo abres y lo marcas como 'Es correo deseado'.
					y en adelante ya recibirás los correos sin problemas";
	}
	else
	{	$reEnvioEmail['textoComentarios'] = 'Restablecimiento de la contraseña. '. $reEnvioEmail['textoComentarios'].
			'<br /><br />Puedes enviar un correo electrónico a \"info@europalaica.org\"  
					para que te ayuden a recuperarlo. Deja también tu teléfono de contacto';
	}
 //echo "<br><br>4 modeloEmail:emailRestablecerPass:reEnvioEmail: ";print_r($reEnvioEmail); 
 
	/*------ Fin tratamiento de errores----------------------------------------------*/
	
 return $reEnvioEmail;	
	
}
/*----------------- FIN emailRestablecerPass() -------------------------------------------------*/

/*--- Inicio emailRecordarUsuario con enviarEmailPhpMailer --------------------------------------
Agustín 2018-08-1: Modifico texto privacidad de datos 
Genera un email que se envía al usuario, a petición del mismo, para 
recordarle el  usuario solo. 
Le llegará también un link que enlaza a index.php

LLAMADA:  desde controladorLogin:recordarLogin()
Barra horizontal superior de en inicio "Recodar usuri@ y contraseña"

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function emailRecordarUsuario($resulEmailLogin)
{
	//echo "<br><br>0-1 modeloEmail:emailRecordarUsuario:resulEmailLogin: ";print_r($resulEmailLogin);	
	//resulEmailLogin=Array([CODUSER]=>2950 [ESTADO]=>alta [USUARIO]=>agus54 [PASSUSUARIO]=>..c937401e.. [EMAIL]=>fulanito@gmail.com)

	$reEnvioEmail['nomScript'] = "modeloEmail.php";		
	$reEnvioEmail['nomFuncion'] = "emailRecordarUsuario";
 $reEnvioEmail['codError'] = '00000';
	$reEnvioEmail['errorMensaje'] = '';		
	
	$asunto= "Europa Laica. Recordar datos identificación";
	
	$contenido = 
	"Europa Laica. Recordar \"usuario\"
	\n\nPara acceder a la aplicación de gestión de socios/as de Europa Laica tienes que utilizar tu contraseña (la que elegiste) y el siguiente usuario: '".
	$resulEmailLogin['USUARIO']. 
	"'\n\nSi tampoco recuerdas tu contraseña, deberás hacer clic en -Recordar usuario y contraseña- y elegir la opción -Restablecer contraseña y recordar usuario-
	\n\nSi quieres entrar ahora ahora, haz clic en el siguiente enlace: <a href='https://www.europalaica.org/usuarios/index.php'>https://www.europalaica.org/usuarios/index.php</a>
	\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador		
	\n\nSi has recibido este correo electrónico y no has pedido recordar tu usuario, otra persona habrá introducido tu dirección de correo electrónico por error. En este caso no es necesario que realices ninguna acción, y puedes ignorar este mensaje con total seguridad.";		

$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie ="\n
---------------------
Un saludo,
Europa Laica
Gestión de socios/as";			

		$datosEnvioEmail=array("fromAddress" 				   	=>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',//quitar para que funcione
																									"replayToAddressName"	=>'Secretaría de Europa Laica',//quitar para que funcione																									
                        	"toAddress"  		  					=>$resulEmailLogin['EMAIL'],
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenido.$proteccionDatos.$contenidoPie																											
																								 );
	

	//echo "<br><br>1 modeloEmail:emailRecordarUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 
	
		$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);//en modeloEmail.php 

 //echo "<br><br>2 modeloEmail:emailRecordarUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 	
   																																																																			
	/*------ Inicio tratamiento de errores ----------------------------------------*/		

	if ($reEnvioEmail['codError']=='00000')
	{ 
		 $reEnvioEmail['textoComentarios']="Ha sido enviado un email a la dirección <b>".$datosEnvioEmail['toAddress'].
			  "</b> para recordar tu usuario. <br /><br />					
					Si no te llega en un tiempo razonable, mira en la carpeta 'Correo no deseado' o en 'Spam'. 
					<br /><br /><br /><br />
					NOTA: Outlook y otros navegadores a veces rechazan correos enviados desde Europa Laica.
					<br /><br /> Si no lo has recibido, abre en hotmail la carpeta 'Correo no deseado' 
					y si encuentras un correo enviado por Europa Laica, lo abres y lo marcas como 'Es correo deseado'.
					y en adelante ya recibirás los correos sin problemas";
	}
	else
	{	$reEnvioEmail['textoComentarios']='Recordar usuario. '. $reEnvioEmail['textoComentarios'].
			'<br /><br />Puedes enviar un correo electrónico a \"info@europalaica.org\"  
					para que te ayuden a recuperarlo. Deja también tu teléfono de contacto';
	}
 //echo "<br><br>3 modeloEmail:emailRecordarUsuario:reEnvioEmail: ";print_r($reEnvioEmail);
 
	/*------ Fin tratamiento de errores -------------------------------------------*/	
	
 return $reEnvioEmail;
}
/*--------------------------- Fin emailRecordarUsuario ----------------------------------------*/

/*--- Inicio emailRecordarUsuarioPass con enviarEmailPhpMailer ---------------------------------
Genera un email que se envía al usuario, a petición del mismo, para 
recordar el usuario y restablecer la contraseña. 

Le llegará un link con el codUser encriptado, para crear otra nueva contraseña

LLAMADA:  desde controladorLogin:recordarLogin(),
Barra horizontal superior de en inicio "Recodar usuri@ y contraseña"

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function emailRecordarUsuarioPass($resulEmailLogin)
{
	//echo "<br><br>0-1 modeloEmail:emailRecordarUsuarioPass:resulEmailLogin: ";print_r($resulEmailLogin);
//resulEmailLogin=Array([CODUSER]=>2950 [ESTADO]=>alta [USUARIO]=>agus54 [PASSUSUARIO]=>..c937401e.. [EMAIL]=>fulanito@gmail.com)	

 $reEnvioEmail['nomScript'] = "modeloEmail.php";		
	$reEnvioEmail['nomFuncion'] = "emailRecordarUsuarioPass";
 $reEnvioEmail['codError'] = '00000';
	$reEnvioEmail['errorMensaje'] = '';			

	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
 $codUser = encriptarBase64($resulEmailLogin['CODUSER']);
	
	//echo "<br><br>2 modeloEmail:emailRecordarUsuarioPass:codUser: ";print_r($codUser);	
	
	$asunto= "Europa Laica. Recordar datos identificación";
	$contenido = 
	"Europa Laica. Petición de recuperación de tu usuario y contraseña para entrar en la aplicación de gestión de socios/as de Europa Laica.
		\nTu usuario es: '".$resulEmailLogin['USUARIO'].
		"'\n\nPara el restablecimiento de la contraseña debes hacer clic en el siguiente enlace: 
		<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=restablecerPass&amp;parametro=".$codUser."'>
https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=restablecerPass&amp;parametro=".$codUser."</a>".
		"\n\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador
		\n\nSi has recibido este correo electrónico y no has pedido recordar tu usuario y el restablecimiento de la contraseña, otra persona habrá introducido introducido tu dirección de correo electrónico por error. En este caso no es necesario que realices ninguna acción, y puedes ignorar este mensaje con total seguridad.";
			
			$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie ="\n
---------------------
Un saludo,
Europa Laica
Gestión de socios/as";			

	$datosEnvioEmail= array("fromAddress" 				   	=>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																			
                        	"toAddress"  		  					=>$resulEmailLogin['EMAIL'],
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenido.$proteccionDatos.$contenidoPie																											
																								 );
	
	//echo "<br><br>3 modeloEmail:emailRecordarUsuarioPass:reEnvioEmail: ";print_r($reEnvioEmail); 
	
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);//en modeloEmail.php 																																																															
		
	//echo "<br><br>4 modeloEmail:emailRecordarUsuarioPass:reEnvioEmail: ";print_r($reEnvioEmail); 

 /*------ Inicio tratamiento de errores ----------------------------------------*/			
	
	if ($reEnvioEmail['codError']=='00000')
	{ 
		 $reEnvioEmail['textoComentarios']="Ha sido enviado un email a la dirección <b>".$datosEnvioEmail['toAddress'].
			  "</b> con un enlace para recordar tu usuario y restablecer tu contraseña. <br /><br />					
					Si no te llega en un tiempo razonable, mira en la carpeta 'Correo no deseado' o en 'Spam'. 
					<br /><br /><br /><br />
					NOTA: Outlook y otros navegadores a veces rechazan correos enviados desde Europa Laica.
					<br /><br /> Si no lo has recibido, abre en hotmail la carpeta 'Correo no deseado' 
					y si encuentras un correo enviado por Europa Laica, lo abres y lo marcas como 'Es correo deseado'.
					y en adelante ya recibirás los correos sin problemas";
	}
	else
	{	$reEnvioEmail['textoComentarios']='Recuperación de usuario y contraseña. '. $reEnvioEmail['textoComentarios'].
			'<br /><br />Puedes enviar un correo electrónico a \"info@europalaica.org\" 
					para que te ayuden a recuperarlo. Deja también tu teléfono de contacto';
	}
 //echo "<br><br>5 modeloEmail:emailRecordarUsuarioPass:reEnvioEmail: ";print_r($reEnvioEmail); 
	/*------ Inicio tratamiento de errores ----------------------------------------*/	
	
 return $reEnvioEmail;
}
/*--------------------------- Fin emailRecordarUsuarioPass ------------------------------------*/
//===== FIN ENVIAR EMAILS A SOCIOS PARA RECORDAR USUARIOS Y CONTRASEÑAS =========================


//===== INICIO ENVIAR EMAILS RELACIONADOS CON ALTAS SOCIOS ======================================

/*----- Inicio emailPeticionConfirmarAltaUsuario   ---------------------------------------------- 
DESCRIPCION: Envía un email al usuario para comunicarle que tiene que confirmar su 
             registro provisional como socio.
Le llegará un link con el codUser encriptado, para confirmar el alta de usuario
Envía nombre y ape1, y también $usuario, pero no envía la contraseña por seguridad

RECIBE : $datosEmailConfirmarAlta (ARRAY)

LLAMADA: controladorSocios.php:altaSocio(),
         cPresidente.php:reenviarEmailConfirmarSocioAltaGestor() 
LLAMA: enviarEmailPhpMailer($datosEnvioEmail);

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function emailPeticionConfirmarAltaUsuario($datosEmailConfirmarAlta)
{
 //echo "<br><br>0-1 modeloEmail:emailPeticionConfirmarAltaUsuario:datosEmailConfirmarAlta: ";print_r($datosEmailConfirmarAlta);		

	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
 $codSocioConfirmar = encriptarBase64($datosEmailConfirmarAlta['CODUSER']);

	//echo "<br><br>2 modeloEmail:emailPeticionConfirmarAltaUsuario:codSocioConfirmar";print_r($codSocioConfirmar);		

 $emailUsuario = $datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['EMAIL']['valorCampo'];

	$nom = $datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['NOM']['valorCampo'];
	$ape1 = $datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['APE1']['valorCampo'];
	$nomApe1 = $nom." ".$ape1;
 
	if ($datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['SEXO']['valorCampo'] == 'H')
	{$asunto = "Europa Laica. Confirmar ahora tu alta como socio en Europa Laica";
	 $contenidoCuerpoSexo = "\nEstimado -".$nomApe1."- muchas gracias por tu deseo de hacerte socio de Europa Laica.\n"; 
	}
	elseif ($datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['SEXO']['valorCampo'] == 'M')
	{$asunto = "Europa Laica. Confirmar ahora tu alta como socia en Europa Laica";
	 $contenidoCuerpoSexo =	"\n\nEstimada ".$nom." ".$ape1.", muchas gracias por tu deseo de hacerte socia de Europa Laica.\n\n"; 
	}	
	else
	{$asunto = "Europa Laica. Confirmar ahora tu alta en la asociación Europa Laica";	
	 $contenidoCuerpoSexo =	"\n\n".$nom." ".$ape1.", muchas gracias por tu deseo de asociarte a Europa Laica.\n\n"; 
	}			
	
	$contenidoCuerpoComun = "\nPara completar el proceso, es necesario que ahora confirmes tu petición de alta y tu email (también puedes eliminar los datos de tu solicitud). Para ello debes hacer clic en el siguiente enlace:
<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarAnularAltaSocio&amp;parametro=".$codSocioConfirmar."'>
https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarAnularAltaSocio&amp;parametro=".$codSocioConfirmar."</a>
\nSi el enlace no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador.
\n\nSi tienes algún problema, puedes enviar un correo a \"info@europalaica.org\" y te ayudaremos a resolverlo.
\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu correo electrónico por error. En este caso para borrar tus datos y no recibir más mensajes haz clic en enlace anterior y encontrarás una opción para anular tus datos.
";

	$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie ="\n
----------------------
Un saludo,
Europa Laica
Gestión de socios/as";			

	$contenido = $contenidoCuerpoSexo.$contenidoCuerpoComun.$proteccionDatos.$contenidoPie;

	$datosEnvioEmail = array("fromAddress" 				   	=>'secretaria@europalaica.org',
																			  					"fromAddressName" 				=>'Secretaría de Europa Laica',
																										"replayToAddress"		  	=>'secretaria@europalaica.org',//quitar para que funcione
																										"replayToAddressName"	=>'Secretaría de Europa Laica',//quitar para que funcione																									
																										"toAddress"  		  					=>$emailUsuario,
																										"toAddressName"  					=>$nomApe1,
																										"subject" 		  								=>$asunto,
																										"body"	 	 												=>$contenido																										
																						 		);
 //echo "<br><br>3 modeloEmail:emailPeticionConfirmarAltaUsuario:datosEnvioEmail: ";print_r($datosEnvioEmail); 	

																				
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

 //echo "<br><br>4 modeloEmail:emailPeticionConfirmarAltaUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 	
	if ($reEnvioEmail['codError']!=='00000')
	{
		 $reEnvioEmail['textoComentarios']="Error en el proceso de alta de socio/a<br /><br/> 
			No se ha podido enviar el email a la dirección de correo electrónico anotada ".$datosEnvioEmail['toAddress'].
			",	por lo que no se podrá completar el proceso de confirmación del alta. 
			<br /><br/>Puede intentarlo de nuevo, o enviar un correo electrónico 
			a \"info@europalaica.org\" indicando el problema";	
	}		                                  				
	
 return $reEnvioEmail;
}
/*--------------------------- Fin emailPeticionConfirmarAltaUsuario ---------------------------*/

/*----- Inicio emailAnuladaAltaPendienteConfirmarUsuario   --------------------------------------
DESCRIPCION: Envía un email al usuario para comunicarle que ha sido anulada su petición 
             de alta como socio (que estaba pendiente de confirmar), por un gestor .             
Envía nombre y ape1, 

RECIBE : $datosEmailConfirmarAlta (ARRAY)

LLAMADA: desde desde cPresidente:anularSocioPendienteConfirmarPres()
LLAMA: enviarEmailPhpMailer($datosEnvioEmail)

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function emailAnuladaAltaPendienteConfirmarUsuario($datosEmailConfirmarAlta)
{
 //echo "<br><br>0-1 modeloEmail:emailPeticionConfirmarAltaUsuario:datosEmailConfirmarAlta: ";print_r($datosEmailConfirmarAlta);		

	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
 $codSocioConfirmar = $datosEmailConfirmarAlta['CODUSER'];

	//echo "<br><br>1 modeloEmail:emailAnuladaAltaPendienteConfirmarUsuario:datosEmailConfirmarAlta:";print_r($datosEmailConfirmarAlta);		

 $emailUsuario = $datosEmailConfirmarAlta['EMAIL'];

	$nom = $datosEmailConfirmarAlta['NOM'];
	$ape1 = $datosEmailConfirmarAlta['APE1'];

	$asunto = "Europa Laica. Borrado de tus datos personales";	

	 $contenidoCuerpoComun =	"Borrado de tus datos personales.
	 \n\n".$nom." ".$ape1."-, se ha anulado tu solicitud de alta en la asociación Europa Laica y se han borrado todos tus datos personales 
  de nuestra base de datos de acuerdo con la ley de protección de datos.
		\n\nSi más adelante en algún momento decides asociarte a Europa Laica de nuevo, tendrás que volver a introducir tus datos otra vez\n
		\n\nMuchas gracias por tu interés en la asociación Europa Laica.";
		
	$contenidoPie ="\n
----------------------
Un saludo,
Europa Laica
Gestión de socios/as";			

	$contenido = $contenidoCuerpoComun.$contenidoPie;

	$datosEnvioEmail=array("fromAddress" 				   	=>'secretaria@europalaica.org',
																								"fromAddressName" 				=>'Secretaría de Europa Laica',
																								"replayToAddress"		  	=>'secretaria@europalaica.org',//quitar para que funcione
																								"replayToAddressName"	=>'Secretaría de Europa Laica',//quitar para que funcione																									
																								"toAddress"  		  					=>$emailUsuario,
																								"toAddressName"							=>'Europa Laica socio/a',																									
																								"subject" 		  								=>$asunto,
																								"body"	 	 												=>$contenido																										
																								);
 //echo "<br><br>2 modeloEmail:emailPeticionConfirmarAltaUsuario:datosEnvioEmail: ";print_r($datosEnvioEmail); 	

																				
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

 //echo "<br><br>3 modeloEmail:emailPeticionConfirmarAltaUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 	
	
 return $reEnvioEmail;
}
//--------------------------- Fin emailAnuladaAltaPendienteConfirmarUsuario ---------------------

/*--------- Inicio emailComunicarAltaUsuario() -------------------------------------------------- 
DESCRIPCION: Genera un email que se envía al socio, para comunicarle que ha sido 
             dado de alta después de la confirmación por el socio de la peticón 
													previa de registrarse. 
Envía nombre y ape1, y también $usuario, pero no envía la contraseña por seguridad
Personaliza según el sexo.

RECIBE : $datosSocio (ARRAY)

LLAMADA: solo desde controladorSocios:confirmarAltaSocio() 
LLAMA: enviarEmailPhpMailer($datosEnvioEmail)

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function emailComunicarAltaUsuario($datosSocio)
{
	//echo "<br><br>0-1 modeloEmail:emailComunicarAltaUsuario:datosSocio: ";print_r($datosSocio); 
 
	$emailUsuario = $datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];	
	$nom  = $datosSocio['datosFormMiembro']['NOM']['valorCampo'];
	$ape1 = $datosSocio['datosFormMiembro']['APE1']['valorCampo'];
	$fechaAlta = $datosSocio['datosFormSocio']['FECHAALTA']['valorCampo'];
	
	if ($emailCoordinacion = $datosSocio['datosFormSocio']['EMAILCOORD']['valorCampo'] !== 'adminusers@europalaica.org')
	{$emailCoordinacion = $datosSocio['datosFormSocio']['EMAILCOORD']['valorCampo'];
	 $nomAgrupacion = $datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'];
	 $contenidoDatosCoordinacion = "\n\n\nPara ponerte en contacto con la agrupación ".$nomAgrupacion." e informarte de las actividades que realizan, envía un correo electrónico a \"".$emailCoordinacion."\"";
	}
	else
	{
		$contenidoDatosCoordinacion = "";
	}	
		
	if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'H')
	{$asunto = "Europa Laica. Ya está confirmada tu alta como socio de Europa Laica";
		
	 $contenidoCuerpoSexo = "Confirmada tu alta como socio de Europa Laica.
		\n\nEstimado -".$nom." ".$ape1.
		"- te comunicamos que a petición tuya, te hemos registrado como socio en la aplicación informática de gestión de socios/as de Europa Laica. 
		\nPara entrar en el ÁREA DE SOCI@S, deberás usar el \"Usuario\" y la \"Contraseña\" que tú elegiste al registrarte como nuevo socio. Por motivos de seguridad no lo enviamos por email.
		";	
		$bienvenida ="\n\n¡Bienvenido a la asociación Europa Laica!";
	}
	elseif ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'M')
	{$asunto = "Europa Laica. Ya está confirmada tu alta como socia de Europa Laica";
	
	 $contenidoCuerpoSexo =	"Confirmada tu alta como socia de Europa Laica.
			\n\nEstimada -".$nom." ".$ape1.
  	"- te comunicamos que a petición tuya, te hemos registrado como socia en la aplicación informática de gestión de socias/os de Europa Laica. 
   \nPara entrar en el ÁREA DE SOCI@S, deberás usar los datos de \"Usuario\" y la \"Contraseña\" que tú elegiste al registrarte como nueva socia. Por motivos de seguridad no lo enviamos por email.
  ";		
		$bienvenida ="\n\n¡Bienvenida a la asociación Europa Laica!";
	}	
	else
	{$asunto = "Europa Laica. Ya está confirmada tu alta en la asociación Europa Laica";
	
	 $contenidoCuerpoSexo =	"Ya está confirmada tu alta en la asociación Europa Laica.
		\n\n-".$nom." ".$ape1.
		"- te comunicamos que a petición tuya, te hemos dado de alta y registrado tus datos en la base de datos de Europa Laica. 
		\nPara entrar en el ÁREA DE SOCI@S, deberás usar el \"Usuario\" y la \"Contraseña\" que tú elegiste al escribir tus datos para solicitar el alta. Por motivos de seguridad no lo enviamos por email.
		";		
		$bienvenida ="\n\n¡Te damos una afectuosa bienvenida a la asociación Europa Laica!";
	}		

	$bienvenida ="\n\n¡Te damos una afectuosa bienvenida a la asociación Europa Laica!";
	
	$contenidoCuerpoComun = "
\nPuedes entrar desde el enlace <a href='http://www.europalaica.org/usuarios'>http://www.europalaica.org/usuarios</a> o desde la página web de Europa Laica http://www.laicismo.org  -AREA DE SOCI@S-	
\nSi hubieses olvidado los datos de tu \"Usuario\" y \"Contraseña\", puedes recuperarlos haciendo clic en el enlace:<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=recordarLogin'>https://www.europalaica.org/usuarios/index.php?controlador=controladorLogin&amp;accion=recordarLogin</a>
\nEn el caso de que el enlace anterior no funcione, prueba a copiarlo y pegarlo en una nueva ventana del navegador.";

	$colabora = "\n\nSi tienes interés en colaborar con la asociación en alguno de los Grupos de Trabajo existentes: Feminismo y Laicismo, Formación y Estudios, Educación, UNI Laica (Universidad), o en cualquier otra área, en cualquier momento puedes envíar un correo a \"info@europalaica.org\"";
	
	$unilaica = "\n\nSi eres miembro de la comunidad universitaria (Estudiante, PDI o PAS), también puedes ponerte en contacto con el grupo de Europa Laica Universidad: \"info@unilaica.org\"";
			
	$cuota =		"\n\nPara ser socio/a de pleno derecho y poder votar en las asambleas tienes que abonar, si aún no lo has hecho, la cuota anual de la asociación Europa Laica.
													\n
													En Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes.";
													
 
	$errorEnvio = "\n\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu dirección de correo por error. Si te siguen llegando mensajes puedes enviar un mensaje para comunicarlo a: \"info@europalaica.org\"";
		
	$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie =$bienvenida."
----------------------
Un saludo,
Europa Laica
Gestión de socios/as";			

	$contenido = $contenidoCuerpoSexo.$contenidoCuerpoComun.$contenidoDatosCoordinacion.$colabora.$unilaica.$cuota.$errorEnvio.$proteccionDatos.$contenidoPie	;

	$datosEnvioEmail=array("fromAddress" 			     =>'secretaria@europalaica.org',
																								"fromAddressName" 				=>'Secretaría de Europa Laica',
																								"replayToAddress"		  	=>'secretaria@europalaica.org',
																								"replayToAddressName"	=>'Secretaría de Europa Laica',																									
																								"toAddress"  		  					=>$emailUsuario,
																								"toAddressName"							=>'Europa Laica socio/a',																									
																								"subject" 		  								=>$asunto,
                        "body"		 													=>$contenido	
																								);		 

 //echo "<br><br>2 modeloEmail:emailComunicarAltaUsuario:datosEnvioEmail: ";print_r($datosEnvioEmail); 																									
																								
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

	//echo "<br><br>3 modeloEmail:emailComunicarAltaUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 
 
 return $reEnvioEmail;
}
/*----------------- FIN emailComunicarAltaUsuario() -------------------------------------------*/

/*---------------------------- Inicio emailAltaSocioCoordSecreTesor -----------------------------
DESCRIPCION: Envía emails a gestor de área territorial, coordinador, presidente, 
secretaria, tesoreria, para avisar que un socio confirmó y se dio de alta, (estaba 
pendiente de confirmar por el mismo y ahora ha confirmado su alta definitiva), o bien 
ha sido confirmada su alta, que estaba pendiente, por un gestor con rol adecuado.

También se envían a otros correos de EL que por no estar en la BBDD, los pongo a capón
y son los dos siguientes:'secretaria@europalaica.org',info@europalaica.org, 
web@europalaica.org, adminusers@europalaica.org 
													
LLAMADO: controladorSocios.php:confirmarAltaSocio(),cPresidente.php:confirmarAltaSocioPendientePorGestor()
LLAMA: modeloEmail.php:enviarMultiplesEmailsPhpMailer()

RECIBE : $datosEmailCoSeTe: (array con direcciones de email y nombres de gestor de 
área territorial, coordinador, presidente, secretaria, tesoreria)		
$datosSocio: (array con datos de socio: nombre, email, fecha alta)		 

OBSERVACIONES: probado PHP 7.3.21
															
NOTA: Muy parecida a la función emailAltaSocioGestorCoordSecreTesor($datosEmailCoSeTe,$datosSocio)
El socio que confirma su alta, siempre tendrá un email.							
-----------------------------------------------------------------------------------------------*/	
function emailAltaSocioCoordSecreTesor($datosEmailCoSeTe,$datosSocio)
{        
	//echo "<br><br>0-1 modeloEmail:emailAltaSocioCoordSecreTesor:datosSocio: ";print_r($datosSocio);
 //echo "<br><br>0-2 modeloEmail:emailAltaSocioCoordSecreTesor:datosEmailCoSeTe: ";print_r($datosEmailCoSeTe);	
	
	$agrupNomAgrupacion = $datosEmailCoSeTe['NOMAGRUPACION'];
 //$agrupNomAgrupacion = $datosSocio['datosFormSocio']['NOMAGRUPACION'];//las dos sirven
	

	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['email'] = $datosEmailCoSeTe['SECRETARIO']['email'];
	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['nombre']= "Secretaría ".$datosEmailCoSeTe['SECRETARIO']['nombre'];//añade Europa Laica Estatal e Internacional
	
 $datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['email']   = $datosEmailCoSeTe['TESORERO']['email'];
	$datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['nombre']  = "Tesorería ".$datosEmailCoSeTe['TESORERO']['nombre'];

 if (isset($datosEmailCoSeTe['PRESIDENTE']['email']) && !empty($datosEmailCoSeTe['PRESIDENTE']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['email'] = $datosEmailCoSeTe['PRESIDENTE']['email'];
	 $datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['nombre'] = "Presidencia ".$datosEmailCoSeTe['PRESIDENTE']['nombre'];
	}	

 if (isset($datosEmailCoSeTe['COORDINADOR']['email']) && !empty($datosEmailCoSeTe['COORDINADOR']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['email'] = $datosEmailCoSeTe['COORDINADOR']['email'];
	 $datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['nombre'] = $datosEmailCoSeTe['COORDINADOR']['nombre'];
	}	
	//echo "<br><br>1 modeloEmail:emailAltaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);		
 /*	
		$datosEmailCoSeTe['AREAGESTION'] en principio puede ser una o dos filas: 
		$datosEmailCoSeTe['AREAGESTION'][0]['EMAIL'];$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];			
		$datosEmailCoSeTe['AREAGESTION'][1]['EMAIL'];		$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];	
		
		pero lo dejo en el bucle por si en adelante se necesitase mas	filas, en este bucle se van añadiendo a lo ya existente 
		$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR'] ... los siguiente filas del array $datosEnvioEmailCoSeTe['AddAddress']['0']['email'] ...
		y lo tratará "enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe)"
	*/	
	if (isset($datosEmailCoSeTe['AREAGESTION']) && !empty($datosEmailCoSeTe['AREAGESTION']))
	{	
		foreach ($datosEmailCoSeTe['AREAGESTION'] as  $fila => $contenidoFila)
		{
				if (isset($contenidoFila['EMAIL']) && !empty ($contenidoFila['EMAIL']) 
											&& isset($contenidoFila['NOMAREAGESTION']) && !empty ($contenidoFila['NOMAREAGESTION']))
				{ 
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['email'] = $contenidoFila['EMAIL'];
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['nombre'] = 'Área de gestión '.$contenidoFila['NOMAREAGESTION'];						
				}						
		}
 }		
	//echo "<br><br>2 modeloEmail:emailAltaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);		
		
 //-----------------------------------------------------------------------------------

 $datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'info@europalaica.org';	
 $datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'Información Europa Laica';
	
 $datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'web@europalaica.org';	
 $datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'Web Europa Laica';	

	$datosEnvioEmailCoSeTe['fromAddress'] = 'secretaria@europalaica.org';
 $datosEnvioEmailCoSeTe['fromAddressName'] = 'Secretaría de Europa Laica';

	$datosEnvioEmailCoSeTe['AddBCC'][0]['email'] = 'adminusers@europalaica.org';
	$datosEnvioEmailCoSeTe['AddBCC'][0]['nombre'] = 'Europa Laica. Administrador de usuarios';
	
	//---- las siguientes las dejo para pruebas ------------------------------------------
	//$datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'fulanito@gmail.com';	//copia a información
	//$datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'prueba Información Europa Laica';
	//$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'fulanita@hotmail.com';	//pruebas
	//$datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'email prueba';		
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['email']='adminusers@europalaica.org';//no para evitar respuesta 	
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['nombre']='Administrador de usuarios';	
		
	//-----------------------------------------------------------------------------------

	if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']) && !empty($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))
	{$comentarioSocio = $datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'];
	}  
 else
 {$comentarioSocio ="";
	}			
	
	$nom  = $datosSocio['datosFormMiembro']['NOM']['valorCampo'];
	$ape1 = $datosSocio['datosFormMiembro']['APE1']['valorCampo'];
	$emailUsuario = $datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];
 
 $fechaAlta = $datosSocio['datosFormSocio']['FECHAALTA']['valorCampo'];	
	 
	$asunto = "Europa Laica. Confirmada alta de socio/a.";
		
	$contenidoCuerpoComun = "Confirmada alta del socio/a ".$nom." ".$ape1.
	" en la agrupación territorial ".$agrupNomAgrupacion .", con fecha de alta ".
	$fechaAlta." \n\nComentario del socio/a: ".$comentarioSocio." \n\nSu email es: ".$emailUsuario;	

	$contenidoPie ="
\n----------------------
Un saludo,
Europa Laica
Gestión de socios/as";		
	
 $contenido = $contenidoCuerpoComun.$contenidoPie;				
 //---------------------------------------------------------------------------------------	

 $datosEnvioEmailCoSeTe['subject'] = $asunto;	
 $datosEnvioEmailCoSeTe['body'] = $contenido;	
	
	//echo "<br><br>3 modeloEmail:emailAltaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);

 //******OJO COMENTADO PARA PRUEBAS COMENTAR** -->  
	$reEnviarEmail = enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe);
	
 //echo "<br><br>4 modeloEmail:emailAltaSocioCoordSecreTesor:reEnviarEmail: ";print_r($reEnviarEmail); 
	
 return $reEnviarEmail;				
}
/*------------------------------ Fin emailAltaSocioCoordSecreTesor ----------------------------*/

/*--------- Inicio emailConfirmarEmailAltaSocioPorGestor ---------------------------------------
DESCRIPCION: Se envía email a un socio/as que proceden de alta dada por un gestor: 
(Presidente, Secretaria, Coordinador, Tesorero)
	
El texto con nombre de socio, y texto informativo, incluye nombre de usuario para entrar
en la aplicación y un link a "controladorSocios.php:confirmarEmailPassAltaSocioPorGestor()"
que incluye el parámetro CODUSER (encriptado), para que elija su contraseña 

También se envía una copia oculta BCC a adminusers@europalaica.org, para control																 

LLAMADA: cPresidente:altaSocioPorGestorPres(),reenviarEmailConfirmarSocioAltaGestor()
									cCoordinador:altaSocioPorGestorCoord()
									cTesorero:altaSocioPorGestorTes()
LLAMA: modeloSocios.php: enviarEmailPhpMailer()

RECIBE : $arrDatosSocio (ARRAY)
																
OBSERVACIONES: OBSERVACIONES: probado PHP 7.3.21
---------------------------------------------------------------------------------------------*/
function emailConfirmarEmailAltaSocioPorGestor($arrDatosSocio)
{
	//echo "<br><br>0-1 modeloEmail:emailConfirmarEmailAltaSocioPorGestor:arrDatosSocio: ";print_r($arrDatosSocio);  
	
	$codUser = $arrDatosSocio['CODUSER'];//esta encriptada			
 $usuario = $arrDatosSocio['USUARIO'];
	$nom     = $arrDatosSocio['NOM'];
	$ape1    = $arrDatosSocio['APE1'];	
		
	$asunto = "Europa Laica. Confirmación de tu email y elegir contraseña";
			
	if ($arrDatosSocio['SEXO'] == 'H')
	{
	 $contenidoCuerpoSexo = "Estimado ".$nom." ".$ape1.", "; 
		$bienvenida ="\n\n¡Bienvenido a la asociación Europa Laica!";
	}
	elseif ($arrDatosSocio['SEXO'] == 'M')
	{	
	 $contenidoCuerpoSexo = "Estimada -".$nom." ".$ape1."- ";
		$bienvenida ="\n\n¡Bienvenida a la asociación Europa Laica!";
	}
	else
	{
		$contenidoCuerpoSexo =	$nom." ".$ape1.", ";
		$bienvenida ="\n\n¡Te damos una afectuosa bienvenida a la asociación Europa Laica!";		
	}	

			$contenidoCuerpoComun = 
			"atendiendo tu petición y con tu autorización, un gestor/a de socios/as de Europa Laica te dio de alta en nuestra asociación.
			\n\nPara poder entrar en el Área Soci@s para poder ver, modificar o eliminar tus datos, necesitas tu 'usuario' y la 'contraseña'.		
			\nEl 'Usuario' para entrar en la aplicación es: \"".$arrDatosSocio['USUARIO']."\"".
		"\n\nLa 'contraseña' la tienes que elegir ahora haciendo clic en el siguiente enlace:
		
<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$arrDatosSocio['CODUSER']."'>
https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$arrDatosSocio['CODUSER']."</a>		
\n\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador
	\nCuando entres, además de elegir la contraseña, podrás cambiar tu 'Usuario' por otro que te resulte más fácil de recordar y comprobar si tus datos son correctos, y si no es así podrás modificarlos.
	\nTambién podrás darte de baja de la asociación Europa Laica, y se eliminarán tus datos personales de nuestra base de datos";
		
	$colabora = "\n\nSi tienes interés en colaborar con la asociación en alguno de los Grupos de Trabajo existentes: Feminismo y Laicismo, Formación y Estudios, Educación, UNI Laica (Universidad), o en cualquier otra área, en cualquier momento puedes envíar un correo a \"info@europalaica.org\"";
	
	$unilaica = "\n\nSi eres miembro de la comunidad universitaria (Estudiante, PDI o PAS), también puedes ponerte en contacto con el grupo de Europa Laica Universidad: \"info@unilaica.org\"";
			
	$cuota =		"\n\nPara ser socio/a de pleno derecho tienes que abonar, si aún no lo has hecho, la cuota anual de la asociación Europa Laica.
													En Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes.";
 
	
	$errorEnvio = "\n\n\nSi has recibido este correo electrónico y no has solicitado el alta como socia/o en Europa Laica, otra persona habrá introducido tu dirección de correo por error. Si te siguen llegando mensajes puedes enviar un mensaje para comunicarlo a: \"info@europalaica.org\"";

	$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
	\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
	\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";

	$contenidoPie ="\n---------------------\nUn saludo,\nEuropa Laica\nGestión de socios/as";
		
 $contenido = $contenidoCuerpoSexo.$contenidoCuerpoComun.$colabora.$unilaica.$cuota.$errorEnvio.$proteccionDatos.$contenidoPie;
 		
	$datosEnvioEmail = array("fromAddress" 				   	=>'secretaria@europalaica.org',
																										"fromAddressName" 				=>'Secretaría de Europa Laica',
																										"replayToAddress"		  	=>'secretaria@europalaica.org',
																										"replayToAddressName"	=>'Secretaría de Europa Laica',
																										"toAddress"  		  					=>$arrDatosSocio['EMAIL'],
																										"toAddressName"							=>'Europa Laica socio/a',
																										"toBCC"  		  									=>"adminusers@europalaica.org",
																										"toBCCName"											=>"Europa Laica. Administrador de socios/as",
																										"subject" 		  								=>$asunto,
																										"body"		 													=>$contenido																											
																										);		
	//echo "<br><br>1 modeloEmail:emailConfirmarEmailAltaSocioPorGestor:datosEnvioEmail: "; print_r($datosEnvioEmail); 
																											
	$resulEnviarEmail = enviarEmailPhpMailer($datosEnvioEmail);				

	//echo "<br><br>2 modeloEmail:emailConfirmarEmailAltaSocioPorGestor:resulEnviarEmail:"; print_r($resulEnviarEmail); 
	
	return $resulEnviarEmail; 
}
/*------------------ Fin emailConfirmarEmailAltaSocioPorGestor -------------------------------*/

/*---------------------------- Inicio emailAltaSocioGestorCoordSecreTesor ----------------------
DESCRIPCION: Envía emails a gestor de área, coordinador, presidente, secretaria, tesoreria
para avisar que un gestor: Presidente, secretaria, coordinador, tesorero ha dado de 
alta a un socio .
También se envían a otros correos de EL que por no estar en la BBDD, los pongo a capón
y son los dos siguientes:'secretaria@europalaica.org',info@europalaica.org, web@europalaica.org, 
y adminusers@europalaica.org 

RECIBE : $datosEmailCoSeTe: (array con direcciones de email y nombres de, 
área de gestión, coordinador, presidente, secretaria, tesoreria)		
$datosSocio: (array con datos de socio: nombre, email, fecha alta)		
													
LLAMADA: cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.phph:altaSocioPorGestorPres()
        	y  cTesorero.php:altaSocioPorGestorTes()										
LLAMA: modeloEmail.php:enviarMultiplesEmailsPhpMailer()																	

OBSERVACIONES: probado PHP 7.3.21			
----------------------------------------------------------------------------------------------*/	
function emailAltaSocioGestorCoordSecreTesor($datosEmailCoSeTe,$datosSocio)
{        
	//echo "<br><br>0-1 modeloEmail:emailAltaSocioGestorCoordSecreTesor:datosSocio: ";print_r($datosSocio);
 //echo "<br><br>0-2 modeloEmail:emailAltaSocioGestorCoordSecreTesor:datosEmailCoSeTe: ";print_r($datosEmailCoSeTe);
	
 $agrupNomAgrupacion = $datosEmailCoSeTe['NOMAGRUPACION'];
 //$agrupNomAgrupacion = $datosSocio['datosFormSocio']['NOMAGRUPACION'];//las dos sirven
	

	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['email'] = $datosEmailCoSeTe['SECRETARIO']['email'];
	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['nombre']= "Secretaría ".$datosEmailCoSeTe['SECRETARIO']['nombre'];//añade Europa Laica Estatal e Internacional
	
 $datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['email']   = $datosEmailCoSeTe['TESORERO']['email'];	
 $datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['nombre']  = "Tesorería ".$datosEmailCoSeTe['TESORERO']['nombre'];

 if (isset($datosEmailCoSeTe['PRESIDENTE']['email']) && !empty($datosEmailCoSeTe['PRESIDENTE']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['email'] = $datosEmailCoSeTe['PRESIDENTE']['email'];	
	 $datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['nombre'] = "Presidencia ".$datosEmailCoSeTe['PRESIDENTE']['nombre'];		
	}	
	
 if (isset($datosEmailCoSeTe['COORDINADOR']['email']) && !empty($datosEmailCoSeTe['COORDINADOR']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['email'] = $datosEmailCoSeTe['COORDINADOR']['email'];	 
	 $datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['nombre'] = $datosEmailCoSeTe['COORDINADOR']['nombre'];
	}	
	//echo "<br><br>1 modeloEmail:emailAltaSocioGestorCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);
		
 /*	
		$datosEmailCoSeTe['AREAGESTION'] en principio puede ser una o dos filas: 
		$datosEmailCoSeTe['AREAGESTION'][0]['EMAIL'];$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];			
		$datosEmailCoSeTe['AREAGESTION'][1]['EMAIL'];		$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];	
		
		pero lo dejo en el bucle por si en adelante se necesitase mas	filas, en este bucle se van añadiendo a lo ya existente 
		$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR'] ... los siguiente filas del array $datosEnvioEmailCoSeTe['AddAddress']['0']['email'] ...
		y lo tratará "enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe)"
	*/	
	
	if (isset($datosEmailCoSeTe['AREAGESTION']) && !empty($datosEmailCoSeTe['AREAGESTION']))
	{	
		foreach ($datosEmailCoSeTe['AREAGESTION'] as  $fila => $contenidoFila)
		{
				if (isset($contenidoFila['EMAIL']) && !empty ($contenidoFila['EMAIL']) 
											&& isset($contenidoFila['NOMAREAGESTION']) && !empty ($contenidoFila['NOMAREAGESTION']))
				{ 
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['email'] = $contenidoFila['EMAIL'];
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['nombre'] = 'Área de gestión '.$contenidoFila['NOMAREAGESTION'];						
				}						
		}
 }			
	//echo "<br><br>2 modeloEmail:emailAltaSocioGestorCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);	

 //-----------------------------------------------------------------------------------------
		
 $datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'info@europalaica.org';	
 $datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'Información Europa Laica';
	
	$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'web@europalaica.org';	
 $datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'Web Europa Laica';	
	
	$datosEnvioEmailCoSeTe['AddBCC'][0]['email'] = 'adminusers@europalaica.org';
	$datosEnvioEmailCoSeTe['AddBCC'][0]['nombre'] = 'Administrador de usuarios';
 
 $datosEnvioEmailCoSeTe['fromAddress']='secretaria@europalaica.org';//tambien sirve 
 $datosEnvioEmailCoSeTe['fromAddressName'] = 'Secretaría de Europa Laica';		//tambien sirve 
		
	//---- las siguientes las dejo para pruebas ------------------------------------------
	//$datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'agustin.villacorta@gmail.com';	//copia a información
	//$datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'B Información Europa Laica';
	//$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'segvilla50@hotmail.com';	//pruebas
	//$datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'email prueba';		
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['email']='adminusers@europalaica.org';//no para evitar respuesta 	
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['nombre']='Administrador de usuarios';			
	//-----------------------------------------------------------------------------------
	
	if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']) && !empty($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))
	{$comentarioSocio = $datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'];
	}  
 else
 {$comentarioSocio ="";
	}	
		
	if (isset($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']) && !empty($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']))
	{$observacionesGestor = $datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo'];
	}  
 else
 {$observacionesGestor ="";
	}			
	
	$nom  = $datosSocio['datosFormMiembro']['NOM']['valorCampo'];
	$ape1 = $datosSocio['datosFormMiembro']['APE1']['valorCampo'];
	
 //$fechaAlta = $datosSocio['datosFormSocio']['FECHAALTA']['valorCampo'];	
	$fechaAlta = date('Y-m-d');	

	if ($datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
	{ $emailUsuario = $datosSocio['datosFormMiembro']['EMAIL']['valorCampo']; 
	}	
	else //['EMAILERROR']['valorCampo'] =='FALTA'
	{ $emailUsuario = " NO TIENE EMAIL ";
	}	
	 
	$asunto = "Europa Laica. Alta socio/a por un gestor/a";
		
	$contenidoCuerpoComun =  "Se ha dado de alta por un gestor/a (presidencia, secretaría, tesorería, coordinador/a,...) al socio/a <strong>".$nom." ".$ape1.
	"</strong> en la agrupación territorial - ".$agrupNomAgrupacion ." - con fecha de alta ".
 $fechaAlta." \n\nComentario socio/a:".$comentarioSocio." \n\nObservaciones Gestor/a:".$observacionesGestor." \n\nSu email es: <strong>".$emailUsuario."</strong>";		

	$contenidoPie ="
----------------------
Un saludo,
Europa Laica
Gestión de socios/as";		
	
 $contenido = $contenidoCuerpoComun.$contenidoPie;				
 //---------------------------------------------------------------------------------------	

 $datosEnvioEmailCoSeTe['subject'] = $asunto;	
 $datosEnvioEmailCoSeTe['body'] = $contenido;				
	
 //echo "<br><br>3 modeloEmail:emailAltaSocioGestorCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe); 
	
 // OJO ***** PARA PRUEBAS COMENTAR** 	
	$reEnviarEmail = enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe);
	
 //echo "<br><br>4 modeloEmail:emailAltaSocioGestorCoordSecreTesor:reEnviarEmail: ";print_r($reEnviarEmail); 
	
 return $reEnviarEmail;				
}
/*------------------------------ Fin emailAltaSocioGestorCoordSecreTesor ---------------------*/

/*-------- Inicio emailConfirmarAltaSocioPendientePorGestor ------------------------------------
Se envía email al socio/a pendiente de confirmar su alta USUARIO.ESTADO=PENDIENTE-CONFIRMAR
y que un gestor (con rol de Presidencia, Secretaria, Tesoreria) la ha confirmado ahora 
a petición del socio por tel. o email, u otros modo, debido a alguna dificultad del socio
para hacerlo por el mismo.

También se envía una copia oculta BCC a adminusers@europalaica.org, para control	

El texto con nombre de socio, y texto informativo, incluye nombre de usuario para entrar
en la aplicación y un link "controladorSocios.php:confirmarEmailPassAltaSocioPorGestor()"
que incluye el parámetro CODUSER (encriptado), para que elija su contraseña 
(lo más probable es que no se acuerde).

RECIBE : $arrDatosSocio (ARRAY)

LLAMADA: cPresidente:confirmarAltaSocioPendientePorGestor() y menu tesorero
									
LLAMA: modeloEmail.php:enviarEmailPhpMailer($datosEnvioEmail);
																
OBSERVACIONES: probado PHP 7.3.21		
----------------------------------------------------------------------------------------------*/
function emailConfirmarAltaSocioPendientePorGestor($arrDatosSocio)
{
	//echo "<br><br>0-1 modeloEmail:emailConfirmarAltaSocioPendientePorGestor:datosSocio: ";print_r($datosSocio);  	
	
	$codUser = $arrDatosSocio['CODUSER'];//esta encriptada			
 $usuario = $arrDatosSocio['USUARIO'];
	$nom     = $arrDatosSocio['NOM'];
	$ape1    = $arrDatosSocio['APE1'];

			
	if ($arrDatosSocio['SEXO'] == 'H')
	{$asunto = "Europa Laica. Confirmación de tu alta de socio";
		
	 $contenidoCuerpoSexo = "\nEstimado ".$nom." ".$ape1.", "; 
		$bienvenida ="\n\n¡Bienvenido a la asociación Europa Laica!";
	}
	elseif ($arrDatosSocio['SEXO'] == 'M')
	{$asunto = "Europa Laica. Confirmación de tu alta de socia";
		
	 $contenidoCuerpoSexo = "\nEstimada -".$nom." ".$ape1."- ";
		$bienvenida ="\n\n¡Bienvenida a la asociación Europa Laica!";
	}
	else
	{$asunto = "Europa Laica. Confirmación de tu alta en la asociación";
		
	 $contenidoCuerpoSexo = "\n -".$nom." ".$ape1."- ";
	 $bienvenida ="\n\n¡Te damos una afectuosa bienvenida a la asociación Europa Laica!";
	}
		
			$contenidoCuerpoComun = 
			" hace cierto tiempo iniciaste el proceso de asociarte a Europa Laica mediante el programa informático de gestión de socios/as de Europa Laica.
			\nAhora, a petición tuya, un gestor/a de socios/as de Europa Laica ha confirmado tu deseo de asociarte a Europa Laica.
			\nTe recordamos que para entrar en el ÁREA DE SOCI@S y poder ver, modificar o eliminar tus datos necesitas tu 'usuario' y la 'contraseña'.		
			\nEl 'Usuario' para entrar en la aplicación es: \"".$arrDatosSocio['USUARIO']."\"".		
		"\n\nLa 'contraseña' para entrar en la aplicación la tienes que elegir ahora haciendo clic en el siguiente enlace:
<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$arrDatosSocio['CODUSER']."'>https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$arrDatosSocio['CODUSER']."</a>			
	\n\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en una nueva ventana del navegador.
		\nCuando entres podrás cambiar tu 'Usuario' por otro que te resulte más fácil de recordar, podrás comprobar si tus datos son correctos, y si no es así podrás modificarlos.
		\nTambién podrás darte de baja de la asociación Europa Laica, y se eliminarán tus datos personales de nuestra base de datos";
	
	$colabora = "\n\nSi tienes interés en colaborar con la asociación en alguno de los Grupos de Trabajo existentes: Feminismo y Laicismo, Formación y Estudios, Educación, UNI Laica (Universidad), o en cualquier otra área, en cualquier momento puedes envíar un correo a \"info@europalaica.org\"";
	
	$unilaica = "\n\nSi eres miembro de la comunidad universitaria (Estudiante, PDI o PAS), también puedes ponerte en contacto con el grupo de Europa Laica Universidad: \"info@unilaica.org\"";
			
	$cuota =		"\n\nPara ser socio/a de pleno derecho tienes que abonar, si aún no lo has hecho, la cuota anual de la asociación Europa Laica.
												\nEn Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes.";
		
	
	$errorEnvio = "\n\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu dirección de correo por error. Si te siguen llegando mensajes puedes enviar un mensaje para comunicarlo a: \"info@europalaica.org\"";

	$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
En Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";	
	
	$contenidoPie = "\n---------------------\nUn saludo,\nEuropa Laica\nGestión de socios/as";
		
 $contenido = $contenidoCuerpoSexo.$contenidoCuerpoComun.$colabora.$unilaica.$cuota.$errorEnvio.$proteccionDatos.$contenidoPie;
 		
	$datosEnvioEmail = array("fromAddress" 				   	=>'secretaria@europalaica.org',
																										"fromAddressName" 				=>'Secretaría de Europa Laica',
																										"replayToAddress"		  	=>'secretaria@europalaica.org',
																										"replayToAddressName"	=>'Secretaría de Europa Laica',
																										"toAddress"  		  					=>$arrDatosSocio['EMAIL'],
																										"toAddressName"							=>'Europa Laica socio/a',
																										"toBCC"  		  									=>"adminusers@europalaica.org",
																										"toBCCName"											=>"Europa Laica. Administrador de socios/as",
																										"subject" 		  								=>$asunto,
																										"body"		 													=>$contenido																											
																										);		
	//echo "<br><br>1 modeloEmail:emailConfirmarAltaSocioPendientePorGestor:datosEnvioEmail: "; print_r($datosEnvioEmail); 																												
																												
	$resulEnviarEmail = enviarEmailPhpMailer($datosEnvioEmail);				

	//echo "<br><br>2 modeloEmail:emailConfirmarAltaSocioPendientePorGestor:resulEnviarEmail: "; print_r($resulEnviarEmail); 
		
	return $resulEnviarEmail; 
}
// ------------------ Fin emailConfirmarAltaSocioPendientePorGestor --------------------------*/

/*-------- Inicio emailConfirmarEstablecerPassExcel():PhpMailer --------------------------------
Se envía a un email los socios que proceden de los datos antiguos en Excel, que fueron 
importados a esta BBDD. 
Desde secretaria@europalaica.org, se envía usuario (no la contraseña, por seguridad) y
link para confirmar email y establecer contraseña su contraseña, y un texto explicativo
	
RECIBE : $datExcelTodosNoConf (ARRAY)

LLAMADA: cPresidente:confirmarAltaSocioPendientePorGestor() y menu tesorero									
LLAMA: modeloEmail.php:enviarEmailPhpMailer($datosEnvioEmail);
																
OBSERVACIONES: probado PHP 7.3.21

NOTA: similar a modeloEmail:emailConfirmarAltaSocioPendientePorGestor(), solo cambia el texto 
----------------------------------------------------------------------------------------------*/
function emailConfirmarEstablecerPassExcel($datExcelTodosNoConf)
{
	//echo "<br><br>0-1 modeloEmail:emailConfirmarEstablecerPassExcel:datExcelTodosNoConf: "; print_r($datExcelTodosNoConf); 
   	
	$codUser      = $datExcelTodosNoConf['CODUSER'];	//encriptado		
 $usuario      = $datExcelTodosNoConf['USUARIO'];
	$nom          = $datExcelTodosNoConf['NOM'];
	$ape1         = $datExcelTodosNoConf['APE1'];
			
	if ($datExcelTodosNoConf['SEXO'] == 'H')
	{$asunto = "Europa Laica. Confirmar email del socio";
		
	 $contenidoCuerpoSexo = "\nEstimado ".$nom." ".$ape1.", "; 
	}
	elseif ($datExcelTodosNoConf['SEXO'] == 'M')
	{$asunto = "Europa Laica. Confirmar email de la socia";
		
	 $contenidoCuerpoSexo = "\nEstimada ".$nom." ".$ape1.", "; 
	}
	else
	{$asunto = "Europa Laica. Confirmar tu email";
		
	 $contenidoCuerpoSexo = "\n".$nom." ".$ape1.", "; 
	}		
				
	$contenidoCuerpoComun = "Hace tiempo incorporamos tus datos en una nueva base de datos de Europa Laica.
		\nTu 'Usuario' para entrar en la aplicación es: ".$datExcelTodosNoConf['USUARIO'].			
		
		"\n\nTambién necesitas una contraseña que tienes que elegir ahora haciendo clic en el siguiente enlace:\n
<a href='https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$datExcelTodosNoConf['CODUSER']."'>https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=".$datExcelTodosNoConf['CODUSER']."</a>
\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en	una nueva ventana del navegador	
\n\nUna vez entres en la aplicación podrás comprobrar si tus datos son correctos, y si no es así podrás modificarlos o darte de baja como socia/o. También podrás cambiar el 'Usuario' por otro que te resulte más fácil de recordar.					
\n\nSi no eres socio/a de Europa Laica, te habrá llegado este correo por error. Si te siguen llegando correos puedes enviar un mensaje a \"info@europalaica.org\" y comunicarlo.";		
	
		$proteccionDatos ="\n\nPROTECCIÓN DE DATOS PERSONALES: TUS DATOS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA
En Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679). Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\nhttps://www.europalaica.org/usuarios/index.php?controlador=cEnlacesPie&amp;accion=privacidad";	

	$contenidoPie ="
---------------------
Un saludo,
Europa Laica
Gestión de socios/as";
			
 $contenido =$contenidoCuerpoSexo.$contenidoCuerpoComun.$proteccionDatos.$contenidoPie;
 		
			$datosEnvioEmail=array( "fromAddress" 				   	=>'secretaria@europalaica.org',
																											"fromAddressName" 				=>'Secretaría de Europa Laica',
																											"replayToAddress"		  	=>'secretaria@europalaica.org',
																											"replayToAddressName"	=>'Secretaría de Europa Laica',
                           "toAddress"  		  					=>$datExcelTodosNoConf['EMAIL'],
																											"toAddressName"							=>'Europa Laica socio/a',
                           "toBCC"  		  									=>'adminusers@europalaica.org',
																											"toBCCName"											=>'Europa Laica. Administrador de socios/as',
																											"subject" 		  								=>$asunto,
																											"body"		 													=>$contenido																											
																										);		

			//echo "<br><br>2 modeloEmail:emailConfirmarEstablecerPassExcel:datosEnvioEmail: "; print_r($datosEnvioEmail); 
																												 
		 $resulEnviarEmail = enviarEmailPhpMailer($datosEnvioEmail);				

		 //echo "<br><br>3 modeloEmail:emailConfirmarEstablecerPassExcel:resulEnviarEmail: "; print_r($resulEnviarEmail); 
			
			return $resulEnviarEmail; 
}
/*------------------- Fin emailConfirmarEstablecerPassExcel()---------------------------------*/
//===== FIN ENVIAR EMAILS RELACIONADOS CON ALTAS SOCIOS ========================================


//===== INICIO ENVIAR EMAILS RELACIONADOS CON BAJAS DE SOCIOS ==================================

/*---------------- Inicio emailBajaUsuario  ---------------------------------------------------- 
Genera un email que se envía al usuario, para comunicarle que ha sido dado de baja. 
Solo envía el nombre y ape1

RECIBE : $datosEmailBaja (ARRAY)

LLAMADA: desde controladorSocios:eliminarSocio()
               cPresidente:eliminarSocioPres()
               cCordinador:eliminarSocioCoord() 
															cTesorero:eliminarSocioTes()
															
LLAMA: modeloEmail:enviarEmailPhpMailer()

OBSERVACIONES: probado PHP 7.3.21
----------------------------------------------------------------------------------------------*/
function emailBajaUsuario($datosEmailBaja)
{
	//echo "<br><br>0-1 modeloEmail:emailBajaUsuario:resulEmailLogin";print_r($datosEmailBaja);		
	
	$asunto= "Europa Laica. Borrado de tus datos en la asociación de Europa Laica.";
	$nomApe1 = $datosEmailBaja['NOM']." " .$datosEmailBaja['APE1'];
	
	$contenido = 
	"Borrado de tus datos en la asociación de Europa Laica.
	\n\n-".$datosEmailBaja['NOM']." " .$datosEmailBaja['APE1'].
	"- te comunicamos que atendiendo a tu petición, y siguiendo las leyes sobre protección de datos, se han borrado todos tus datos personales de nuestra base de datos, por lo que de ahora en adelante ya no podrás acceder al ÁREA DE SOCI@S de Europa Laica.
	\nGracias por tu interés en participar en la asociación Europa Laica. Puedes enviarnos un correo electrónico a: \"info@europalaica.org\" y comentarnos los motivos de tu baja. 
		\nSi en algún momento, decides asociarte en Europa Laica de nuevo, tendrás que volver a introducir todos tus datos en la aplicación: 
		<a href='https://www.europalaica.org/usuarios'>https://www.europalaica.org/usuarios</a>	o desde la página web de Europa Laica <a href='https://www.laicismo.org'>https://www.laicismo.org</a>	en -Asóciate-
		\n\nSi has recibido este correo electrónico y no has pedido darte de baja en Europa Laica, otra persona habrá introducido tu dirección de correo electrónico por error. En caso de que te sigan llegando más mensajes envía un correo a: \"info@europalaica.org\" indicando la incidencia.";
		
	$contenidoPie = "\n---------------------\nUn saludo,\nEuropa Laica\nGestión de socios/as";		

	$datosEnvioEmail= array("fromAddress" 				   	=>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																								
                        	"toAddress"  		  					=>$datosEmailBaja['EMAIL'],
																									"toAddressName"  		  		=>$datosEmailBaja['NOM']." ".$datosEmailBaja['APE1'],
                         "toBCC"  		  									=>"adminusers@europalaica.org",
																									"toBCCName"											=>"Europa Laica. Administrador de socios/as",																									
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenido.$contenidoPie																										
																								 );
	
	//echo "<br><br>1 modeloEmail:emailBajaUsuario:datosEnvioEmail: ";print_r($datosEnvioEmail); 
		
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);																									

 $reEnvioEmail['textoCabecera'] = 'Eliminar datos socio/a';//??		
	
 //echo "<br><br>2 modeloEmail:emailBajaUsuario:reEnvioEmail: ";print_r($reEnvioEmail); 
	
 return $reEnvioEmail;
}
/*--------------------------- Fin emailBajaUsuario -------------------------------------------*/

/*---------------- Inicio  emailBajaUsuarioFallecido  ----------------------------------------- 
Genera un email que se envía a la dirección de correro electrónico al usuario, 
para comunicar que ha sido dado de baja por fallecimiento.

Solo envía el nombre y ape1

RECIBE : $datosEmailBaja (ARRAY)

LLAMADA: desde cPresidente:eliminarSocioPres()
               cCordinador:eliminarSocioCoord()
               cTesorero:eliminarSocioTes()
															
LLAMA: modeloEmail:enviarEmailPhpMailer()

OBSERVACIONES: probado PHP 7.3.21
----------------------------------------------------------------------------------------------*/
function  emailBajaUsuarioFallecido($datosEmailBaja)
{
	//echo "<br><br>0-1 modeloEmail: emailBajaUsuarioFallecido:resulEmailLogin";print_r($datosEmailBaja);		
	
	$asunto = "Europa Laica. Borrado de datos del socio/a por fallecimiento";
	$nomApe1 = $datosEmailBaja['NOM']." " .$datosEmailBaja['APE1'];
	
	$contenido = 
	"Borrado de datos del socio/a de Europa Laica por fallecimiento.
	\n\nEstimado/a familiar o amiga/o de -".$datosEmailBaja['NOM']." " .$datosEmailBaja['APE1'].
	"- te comunicamos que siguiendo las leyes sobre protección de datos, se han borrado los datos personales de ".$datosEmailBaja['NOM']." en nuestra base de datos, y de ahora en adelante nadie podrá acceder al ÁREA DE SOCI@S de la aplicación informática de gestión de socios/as de Europa Laica con los datos de Usuario y Contraseña de ".$datosEmailBaja['NOM']."
	\nEstamos muy agredecidos a ".$datosEmailBaja['NOM']."  por haber participado en la asociación Europa Laica.
 \nSi quieres enviarnos algún comentario nos puedes enviar un correo electrónico a: \"info@europalaica.org\".	
	\n\nSi has recibido este correo electrónico y crees que es un error, otra persona puede haber introducido la dirección de correo electrónico por error. Por favor envía un correo a: \"info@europalaica.org\" indicando la incidencia.";
		
	$contenidoPie = "\n---------------------\nUn saludo,\nEuropa Laica\nGestión de socios/as";		

	$datosEnvioEmail= array("fromAddress" 				   	=>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																								
                        	"toAddress"  		  					=>$datosEmailBaja['EMAIL'],
																									"toAddressName"  		  		=>$datosEmailBaja['NOM']." ".$datosEmailBaja['APE1'],
                         "toBCC"  		  									=>"adminusers@europalaica.org",
																									"toBCCName"											=>"Europa Laica. Administrador de socios/as",																									
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenido.$contenidoPie																										
																								 );
	//echo "<br><br>1 modeloEmail: emailBajaUsuarioFallecido:datosEnvioEmail: ";print_r($datosEnvioEmail); 																									
																									
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);																									


 $reEnvioEmail['textoCabecera']='Eliminar datos socio/a';		
	
 //echo "<br><br>2 modeloEmail: emailBajaUsuarioFallecido:reEnvioEmail: ";print_r($reEnvioEmail); 
	
 return $reEnvioEmail;
}
/*--------------------------- Fin  emailBajaUsuarioFallecido ---------------------------------*/

/*---------------------------- Inicio emailBajaSocioCoordSecreTesor ----------------------------
DESCRIPCION: Envía emails a gestor de área, coordinador, presidente, secretaria, tesoreria
             para avisar que un socio se ha dado de baja
También se envían a otros correos de EL que por no estar en la BBDD, los pongo a capón
y son los dos siguientes:'secretaria@europalaica.org',info@europalaica.org, 
web@europalaica.org,y adminusers@europalaica.org 													
													
RECIBE : $datosEmailCoSeTe: (array con direcciones de email y nombres de coordinador, presidente,
         secretaria, tesoreria)		
									$datosSocio: (array con datos de socio: nombre, email)															
													
LLAMADO: desde controladorSocios.php:eliminarSocio()	
               cPresidente:eliminarSocioPres()
               cCoordinador:eliminarSocioCoord()
															cTesorero:eliminarSocioTes()
LLAMA: modeloEmail.php:enviarMultiplesEmailsPhpMailer()																			

OBSERVACIONES: probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/	
function emailBajaSocioCoordSecreTesor($datosEmailCoSeTe,$datosSocio)
{
	//echo "<br><br>0-1 modeloEmail:emailBajaSocioCoordSecreTesor:datosSocio: ";print_r($datosSocio); 
 //echo "<br><br>0-2 modeloEmail:emailBajaSocioCoordSecreTesor:datosEmailCoSeTe: ";print_r($datosEmailCoSeTe);
	 
	$agrupNomAgrupacion = $datosEmailCoSeTe['NOMAGRUPACION'];
 //$agrupNomAgrupacion = $datosSocio['datosFormSocio']['NOMAGRUPACION'];
	

	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['email'] = $datosEmailCoSeTe['SECRETARIO']['email'];
	$datosEnvioEmailCoSeTe['AddAddress']['SECRETARIO']['nombre']= "Secretaría ".$datosEmailCoSeTe['SECRETARIO']['nombre'];//añade Europa Laica Estatal e Internacional
	
 $datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['email']   = $datosEmailCoSeTe['TESORERO']['email'];
	$datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['nombre']  = "Tesorería ".$datosEmailCoSeTe['TESORERO']['nombre'];

 if (isset($datosEmailCoSeTe['PRESIDENTE']['email']) && !empty($datosEmailCoSeTe['PRESIDENTE']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['email'] = $datosEmailCoSeTe['PRESIDENTE']['email'];
	 $datosEnvioEmailCoSeTe['AddAddress']['PRESIDENTE']['nombre'] = "Presidencia ".$datosEmailCoSeTe['PRESIDENTE']['nombre'];
	}	

 if (isset($datosEmailCoSeTe['COORDINADOR']['email']) && !empty($datosEmailCoSeTe['COORDINADOR']['email']))
 {$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['email'] = $datosEmailCoSeTe['COORDINADOR']['email'];
	 $datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR']['nombre'] = $datosEmailCoSeTe['COORDINADOR']['nombre'];
	}		

	//echo "<br><br>1 modeloEmail:emailBajaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);		
 /*	lo dejo por si quiero probar pruebas
		$datosEmailCoSeTe['AREAGESTION'] en principio puede ser una o dos filas: 
		$datosEmailCoSeTe['AREAGESTION'][0]['EMAIL'];$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];			
		$datosEmailCoSeTe['AREAGESTION'][1]['EMAIL'];		$datosEmailCoSeTe['AREAGESTION'][0]['NOMAREAGESTION'];	
		
		pero lo dejo en el bucle por si en adelante se necesitase mas	filas, en este bucle se van añadiendo a lo ya existente 
		$datosEnvioEmailCoSeTe['AddAddress']['COORDINADOR'] ... los siguiente filas del array $datosEnvioEmailCoSeTe['AddAddress']['0']['email'] ...
		y lo tratará "enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe)"
	*/	

	if (isset($datosEmailCoSeTe['AREAGESTION']) && !empty($datosEmailCoSeTe['AREAGESTION']))
	{	
		foreach ($datosEmailCoSeTe['AREAGESTION'] as  $fila => $contenidoFila)
		{
				if (isset($contenidoFila['EMAIL']) && !empty ($contenidoFila['EMAIL']) && 
				    isset($contenidoFila['NOMAREAGESTION']) 	&& !empty ($contenidoFila['NOMAREAGESTION']))
				{ 
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['email'] = $contenidoFila['EMAIL'];
							$datosEnvioEmailCoSeTe['AddAddress'][$fila]['nombre'] = 'Área de gestión '.$contenidoFila['NOMAREAGESTION'];						
				}						
		}
 }	
	
 //-----------------------------------------------------------------------------------------
 
	$datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'info@europalaica.org';	//copia a información
 $datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'Información Europa Laica';
	
	$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'web@europalaica.org';	
 $datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'Web Europa Laica';		

 $datosEnvioEmailCoSeTe['AddBCC'][0]['email'] = 'adminusers@europalaica.org';
	$datosEnvioEmailCoSeTe['AddBCC'][0]['nombre'] = 'Europa Laica. Administrador de usuarios';

 $datosEnvioEmailCoSeTe['fromAddress'] = 'secretaria@europalaica.org';
 $datosEnvioEmailCoSeTe['fromAddressName'] = 'Secretaría de Europa Laica';		
		
	//---- las siguientes las dejo para pruebas ------------------------------------------
	//$datosEnvioEmailCoSeTe['AddCC'][0]['email'] = 'fulanita@gmail.com';	//copia a información
	//$datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'B Información Europa Laica';
	//$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'fulanito@hotmail.com';	//pruebas
	//$datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'email prueba';		
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['email']='adminusers@europalaica.org';//no para evitar respuesta 	
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['nombre']='Administrador de usuarios';	
		
 //echo "<br><br>2 modeloEmail:emailBajaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);				
	//-----------------------------------------------------------------------------------
	
	$nom  = $datosSocio['datosFormMiembro']['NOM'];
	$ape1 = $datosSocio['datosFormMiembro']['APE1'];	
	$fechaBaja = date('Y-m-d');	
	
	$emailUsuario = $datosSocio['datosFormMiembro']['EMAIL'];
	
	if ($datosSocio['datosFormMiembro']['EMAILERROR'] =='NO')
	{ $emailUsuario = $datosSocio['datosFormMiembro']['EMAIL']; 
	}	
	else //['EMAILERROR'] =='FALTA'
	{ $emailUsuario = " NO TIENE EMAIL ";
	}

	if (isset($datosSocio['datosFormSocio']['OBSERVACIONES']) && !empty($datosSocio['datosFormSocio']['OBSERVACIONES']))
	{$observaciones = "\n\n".$datosSocio['datosFormSocio']['OBSERVACIONES'];
	}  
 else
 {$observaciones = "\n\nNo se anotaron observaciones al efectuar esta baja";
	}	
	 
	$asunto = "Europa Laica. Confirmada baja de socio/a.";
		
	$contenidoCuerpoComun = "Europa Laica. Confirmada baja del socio/a ".$nom." ".$ape1.
	" en la agrupación territorial ".$agrupNomAgrupacion .", con fecha de baja ".
	$fechaBaja." \n\nSu email es: ".$emailUsuario.$observaciones;

	$contenidoPie ="\n
----------------------
Un saludo,
Europa Laica
Gestión de socios/as";
			
 $contenido = $contenidoCuerpoComun.$contenidoPie;				
//---------------------------------------------------------------------------------------	

 $datosEnvioEmailCoSeTe['subject'] = $asunto;	
 $datosEnvioEmailCoSeTe['body'] = $contenido;
	
 //echo "<br><br>3 modeloEmail:emailBajaSocioCoordSecreTesor:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);			
	
 // ** OJO ** PARA PRUEBAS COMENTAR** 	
	$reEnviarEmail = enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe);
	
 //echo "<br><br>4 modeloEmail:mailBajaSocioCoordSecreTesor:reEnviarEmail: ";print_r($reEnviarEmail); 
	
 return $reEnviarEmail;				
}	
/*------------------------------ Fin emailBajaSocioCoordSecreTesor ---------------------------*/
//===== FIN ENVIAR EMAILS RELACIONADOS CON BAJAS DE SOCIOS =====================================


/*==================== INICIO EMAIL ASIGNAR-ELIMINAR- ROLES:  ==================================
Por Presidencia: COORDINACIÓN, PRESIDENCIA, TESORERÍA. 
Por administración: ADMINISTRACIÓN Y MANTENIMENTO
*/
/*--------- Inicio emailAsignadoRol() ----------------------------------------------------------
DESCRIPCION: Genera un email que se envía al socio, para comunicarle al socio que le ha sido 
             asignado un rol de gestor en la aplicación de gestión de soci@s, actualmente: 
													Coordinación, Presidencia, Tesorería, Administración, Mantenimiento
												
Envía nombre y ape1, y rol asignado.	
También se envían como adjuntos el documento protección de dato para firmar, 
manual correspondiente a ese ROL de la aplicación de gestión de soci@s, 
manual del correo web de Nodo50. Todos en pdf

RECIBE : $datosSocio (ARRAY), $nomRol,	$dirManualRol (directorio y nombre archivo del manual),	
         $nomManualRol (para mostrar en el email) (opcional estos estos dos últimos)
									
LLAMADA:cPresidente.php:asignarCoordinacionArea()(),asignarPresidenciaRol(), asignarTesoreriaRolBuscar()
        cAdmin.php:asignarAdministracionRol(),asignarMantenimientoRol()
LLAMA: enviarEmailPhpMailer($datosEnvioEmail);

----------------------------------------------------------------------------------------------*/
function emailAsignadoRol($datosSocio, $nomRol,	$dirManualRol,	$nomManualRol)
{
	//echo "<br><br>0-1 modeloEmail:emailAsignadaPresidenciaRol:datosSocio: ";print_r($datosSocio); 	 
	
	$emailUsuario = $datosSocio['EMAIL'];	
	$nom  = $datosSocio['NOM'];
	$ape1 = $datosSocio['APE1'];
		
 $asunto = "Europa Laica. Asignación de rol de ". $nomRol;
		
		$contenidoCuerpoComun = "Europa Laica. Asignación de rol de ". $nomRol.
	"\n\n-".$nom." ".$ape1.
	"- desde este momento tienes asignado el rol de <b>". $nomRol."</b> en la aplicación de gestión de socias/os de Europa Laica. 
 \nAl entrar en la aplicación con tu 'Usuario' y 'Contraseña', verás que en el menú izquierdo ahora también tienes disponible el rol de Presidencia.
	\nEntrando en el menú de ". $nomRol." tienes el acceso a las funciones propias de la gestión de ese rol.
	\n\nExisten unos manuales, que en este email te adjuntamos, para que te puedan ayudar en las operaciones que se pueden realizar con el rol de ". $nomRol.". Además te ayudaremos personalmente por correo electrónico y por teléfono en las dificultades que puedas tener. Para ello puedes contactar con \"info@europalaica.org\" o con \"adminusers@europalaica.org\"";

$proteccionDatos ="	\n\nSEGÚN LAS LEYES DE PROTECCIÓN DE DATOS, LOS DATOS PERSONALES DE LOS SOCIOS/AS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA Los ficheros con los datos están registrados en la Agencia Española de Protección de Datos.
\nTe recordamos que según el Reglamento de Régimen Interior de Europa Laica, tienes que firmar un documento si aún no lo has firmado (archivo adjunto \"Compromiso_Proteccion_Datos.pdf\"), aceptando el conocimiento de la ley de Protección de Datos en lo que se refiere al adecuado uso de dichos datos en tus funciones de coordinación de una agrupación del Europa Laica.
\nDespués lo puedes escanear y enviar a Gestión de Soci@s: \"adminusers@europalaica.org\" (o por correo postal al domicilio legal de Europa Laica)
\nTe rogamos el máximo cuidado con los datos, a los que ahora ya puedes acceder. Para evitar que otras personas puedan acceder a ellos elige una contraseña segura.";

$contenidoPie ="\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu dirección de correo electrónico por error. Si te siguen llegando mensajes puedes enviar un mensaje a \"info@europalaica.org\" y comunicarlo.
----------------------\nUn saludo,\nEuropa Laica\nGestión de Socios/as";			

	//-- Asignar archivos a adjuntar: Esto podría venir del formulario de Asignación ------------
	// los archivos deben estar en el servidor en el directorio correspondiente
	//-------------------------------------------------------------------------------------------
 if (isset($dirManualRol) & isset($nomManualRol) )
	{$datosSocio['AddAttachment'][1]['pathFile'] = $dirManualRol;//ejemplo: "../documentos/PRESIDENCIA/EL_MANUAL_GESTOR_Presidencia.pdf";
	$datosSocio['AddAttachment'][1]['nameSend'] = $nomManualRol;//ejemplo: "EL_MANUAL_GESTOR_Presidencia.pdf";
	$datosSocio['AddAttachment'][1]['type'] = "application/pdf";	
	}
	
	$datosSocio['AddAttachment'][2]['pathFile'] = "../documentos/UTILIDADES/Manual_Correo_Web_Nodo50.pdf";
	$datosSocio['AddAttachment'][2]['nameSend'] = "Manual_EMAIL_WEB.pdf";
	$datosSocio['AddAttachment'][2]['type'] = "application/pdf";
	$datosSocio['AddAttachment'][3]['pathFile'] = "../documentos/ASOCIACION/Proteccion_Datos/Compromiso_Confidencialidad_Proteccion_Datos.pdf";
	$datosSocio['AddAttachment'][3]['nameSend'] = "Compromiso_Proteccion_Datos.pdf";
	$datosSocio['AddAttachment'][3]['type'] = "application/pdf";			
	
 // --------------------------------------------------------------------------------------------

		$datosEnvioEmail=array("fromAddress" 			     =>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																									
                        	"toAddress"  		  					=>$emailUsuario,
																									"toAddressName"							=>'Europa Laica cordinador/a',																													
	                        "toBCC"  		  									=>"adminusers@europalaica.org",
																									"toBCCName"											=>"Europa Laica. Administrador de socios/as",																									
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenidoCuerpoComun.$proteccionDatos.$contenidoPie,
	                        //"fileAttachments"   =>$datosSocio['fileAttachments']	nombre anterior
	                        "AddAttachment"       =>$datosSocio['AddAttachment']																												
																								 );		   
																								
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

	//echo "<br><br>1 modeloEmail:emailAsignadaPresidenciaRol:reEnvioEmail: ";print_r($reEnvioEmail); 
 
 return $reEnvioEmail;
}
/*----------------- FIN emailAsignadoRol() ---------------------------------------------------*/

/*--------- Inicio emailEliminadaAsignacionRol() ----------------------------------------------- 
DESCRIPCION: Genera un email que se envía al socio, para comunicarle que le ha sido 
             eliminado un rol de gestor en la aplicación de gestión de soci@s, actualmente: 
													Coordinación, Presidencia, Tesorería, Administración, Mantenimiento
													
             Envía nombre y ape1, y nombre del rol eliminado
													
RECIBE : $datosSocio (ARRAY) Y $nomRol 

LLAMADA: cPresidente:eliminarCoordinacionArea(),eliminarAsignacionPresidenciaRol(),eliminarAsignacionTesoreriaRol()
         cAdmin.php:eliminarAsignacionAdministracionRol(),eliminarAsignacionMantenimientoRol()
LLAMA A: enviarEmailPhpMailer($datosEnvioEmail)
-----------------------------------------------------------------------------------------------*/
function emailEliminadaAsignacionRol($datosSocio, $nomRol)
{
	//echo "<br><br>0-1 modeloEmail:emailEliminadaAsignacionPresidenciaRol:datosSocio: ";print_r($datosSocio); 	 

	$emailUsuario = $datosSocio['datosFormSocio']['EMAIL'];	
	$nom  = $datosSocio['datosFormSocio']['NOM'];
	$ape1 = $datosSocio['datosFormSocio']['APE1'];

 $asunto = "Europa Laica. Eliminación de la asignación del rol de ". $nomRol;
		
		$contenidoCuerpoComun = "Europa Laica. Eliminación de la asignación del rol de ".$nomRol.
	"\n\n-".$nom." ".$ape1.
	"- te comunicamos que ha sido cancelado tu rol de <b>". $nomRol."</b> en la aplicación de gestión de socias/os de Europa Laica. 
 \nAl entrar en la aplicación con tu 'Usuario' y 'Contraseña', verás que en el menú izquierdo ahora ya no tienes asignado el rol ". $nomRol.".
	\nTus datos personales se mantienen igual que antes, y puedes verlos, modificarlos o eliminarlos como todos los socios/as de Europa Laica.";

$proteccionDatos ="	\n\nSEGÚN LAS LEYES DE PROTECCIÓN DE DATOS, LOS DATOS PERSONALES DE LOS SOCIOS/AS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA Los ficheros con los datos están registrados en la Agencia Española de Protección de Datos.
\nTe recordamos que según el Regalmento de Régimen Interior de Europa Laica, firmaste un documento aceptando el conocimiento de la ley de Protección de Datos en lo que se refiere al adecuado uso de dichos datos en tus funciones de coordinación de una agrupación del Europa Laica.";

$contenidoPie ="\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu dirección de correo electrónico por error. Si te siguen llegando mensajes puedes enviar un mensaje a \"info@europalaica.org\" y comunicarlo.
----------------------\nUn saludo,\nEuropa Laica\nGestión de Socios/as";

$datosEnvioEmail = array("fromAddress" 			     =>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																									
                        	"toAddress"  		  					=>$emailUsuario,
																									"toAddressName"							=>'Europa Laica cordinador/a',																														
	                        "toBCC"  		  									=>"adminusers@europalaica.org",
																									"toBCCName"											=>"Europa Laica. Administrador de socios/as",																										
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenidoCuerpoComun.$proteccionDatos.$contenidoPie																									
																								 );		   
																								
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

	//echo "<br><br>1 modeloEmail:emailEliminadaAsignacionPresidenciaRol:reEnvioEmail: ";print_r($reEnvioEmail); 
 
 return $reEnvioEmail;
}
/*----------------- FIN emailEliminadaAsignacionRol() -----------------------------------------*/

/*--------- Inicio emailComunicarCambioAsignCoord() --------------------------------------------- 
DESCRIPCION: Genera un email que se envía al socio, para comunicarle que le ha sido 
             cambiado el rol de Coordinador.
Envía nombre y ape1, y NOMAREAGESTION, también envía los archivos correspondientes

RECIBE : $datosSocio (ARRAY)

LLAMADA: solo desde cPresidente:cambiarCoordinacionArea() 
LLAMA: enviarEmailPhpMailer($datosEnvioEmail);

-----------------------------------------------------------------------------------------------*/
function emailComunicarCambioAsignCoord($datosSocio)
{//echo "<br><br>1 modeloEmail:emailComunicarCambioAsignCoord:datosSocio: ";print_r($datosSocio); 	
 
	$emailUsuario = $datosSocio['EMAIL'];	
	$nom  = $datosSocio['NOM'];
	$ape1 = $datosSocio['APE1'];
	$areaCoord = $datosSocio['NOMAREAGESTION'];
		
$asunto = "Europa Laica. Cambio de agrupación de tu rol de coordinación";
		
		$contenidoCuerpoComun = "Europa Laica. Cambio de agrupación de tu rol de coordinación.
	\n\n".$nom." ".$ape1.
	": se ha cambiado la agrupación territorial para la que anteriormente tenías permisos de coordinación en la aplicación informática de gestión de socios/as de Europa Laica.
	\nDesde este momento, al entrar en la aplicación, al elegir la opción de Coordinador/a, verás que tienes acceso a las funciones de gestión de socios/as del grupo territorial -".$areaCoord.
	"\n\nExisten unos manuales, que en este email te adjuntamos, para que te puedan ayudar en las operaciones que realiza un coordinador/a. Además te ayudaremos personalmente por correo electrónico y por teléfono en las dificultades que puedas tener. Para ello puedes contactar con \"info@europalaica.org\" o con \"adminusers@europalaica.org\"";

$proteccionDatos ="	\n\nSEGÚN LAS LEYES DE PROTECCIÓN DE DATOS, LOS DATOS PERSONALES DE LOS SOCIOS/AS NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA Los ficheros con los datos están registrados en la Agencia Española de Protección de Datos.
\nTe recordamos que según el Reglamento de Régimen Interior de Europa Laica, tienes que firmar un documento si aún no lo has firmado (archivo adjunto \"Compromiso_Proteccion_Datos.pdf\"), aceptando el conocimiento de la ley de Protección de Datos en lo que se refiere al adecuado uso de dichos datos en tus funciones de coordinación de una agrupación del Europa Laica.
\nDespués lo puedes escanear y enviar a Gestión de Soci@s: \"info@europalaica.org\" (o por correo postal al domicilio legal de Europa Laica)
\nTe rogamos el máximo cuidado con los datos, a los que ahora ya puedes acceder. Para evitar que otras personas puedan acceder a ellos elige una contraseña segura.";

$contenidoPie ="\n\nSi has recibido este correo electrónico y no te has registrado en Europa Laica, otra persona habrá introducido tu dirección de correo electrónico por error. Si te siguen llegando mensajes puedes enviar un mensaje a \"info@europalaica.org\" y comunicarlo.
----------------------\nUn saludo,\nEuropa Laica\nGestión de Socios/as";			

	//-- Asignar archivos a adjuntar: Esto podría venir del formulario de cambioAsignCoord -------
	// los archivos deben estar en el servidor en el directorio correspondiente
	//-------------------------------------------------------------------------------------------
	$datosSocio['AddAttachment'][1]['pathFile'] = "../documentos/COORDINACION/EL_MANUAL_GESTOR_Coordinador.pdf";
	$datosSocio['AddAttachment'][1]['nameSend'] = "MANUAL_GESTOR_Coordinacdor.pdf";
	$datosSocio['AddAttachment'][1]['type'] = "application/pdf";	
	$datosSocio['AddAttachment'][2]['pathFile'] = "../documentos/UTILIDADES/Manual_Correo_Web_Nodo50.pdf";
	$datosSocio['AddAttachment'][2]['nameSend'] = "Manual_EMAIL_WEB.pdf";
	$datosSocio['AddAttachment'][2]['type'] = "application/pdf";
	$datosSocio['AddAttachment'][3]['pathFile'] = "../documentos/ASOCIACION/Proteccion_Datos/Compromiso_Confidencialidad_Proteccion_Datos.pdf";
	$datosSocio['AddAttachment'][3]['nameSend'] = "Compromiso_Proteccion_Datos.pdf";
	$datosSocio['AddAttachment'][3]['type'] = "application/pdf";			

	
$datosEnvioEmail=array("fromAddress" 			     =>'secretaria@europalaica.org',
																									"fromAddressName" 				=>'Secretaría de Europa Laica',
																									"replayToAddress"		  	=>'secretaria@europalaica.org',
																									"replayToAddressName"	=>'Secretaría de Europa Laica',																									
                        	"toAddress"  		  					=>$emailUsuario,
																									"toAddressName"							=>'Europa Laica cordinador/a',																														
	                        "toBCC"  		  									=>"adminusers@europalaica.org",
																									"toBCCName"											=>"Europa Laica. Administrador de socios/as",																										
																									"subject" 		  								=>$asunto,
																									"body"		 													=>$contenidoCuerpoComun.$proteccionDatos.$contenidoPie,
																									//"fileAttachments"   =>$datosSocio['fileAttachments']	antes
																									"AddAttachment"       =>$datosSocio['AddAttachment']
																								 );		   
																									
	//echo "<br><br>2-0 modeloEmail:emailComunicarCambioAsignCoord:datosEnvioEmail: ";print_r($datosEnvioEmail); 												
																								
	$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);

	//echo "<br><br>2-1 modeloEmail:emailComunicarCambioAsignCoord:reEnvioEmail: ";print_r($reEnvioEmail); 
 
 return $reEnvioEmail;
}
/*----------------- FIN emailComunicarCambioAsignCoord() --------------------------------------*/

//===== FIN EMAIL ASIGNAR-ELIMINAR- ROLES:  ====================================================


//===== INICIO EMAIL TESORERO PARA NOTIFICAR SOCIOS PROXIMO COBRO CUOTA ========================

/*--------- Inicio emailAvisarDomiciliadosProxCobro() ------------------------------------------ 
Dentro de un bucle que recorre un array con los datos personales de los socios (nombre,
CCIBAN, cuota...) genera los datos de un email personalizado que se envían al socio,
para comunicarle que próximamente se cobrará la cuota en la cuenta que tiene domiciliada.
Envía nombre y ape1, cantidad cuota, IBAN socio recortado, fecha límite cambios, 
e información común que se recibe también del formulario.

Se incluyen siguiente los casos posibles tipo cuenta banco: tener cuenta SEPA IBAN de ES
y SEPA IBAN distinto de ES, y con la condición "ORDENARCOBRO = SI". 
SOCIOS/AS CON ESTADO DE CUOTA: "PENDIENTE-CONFIRMAR, ABONADA-PARTE"

RECIBE : $datosEmailCuotaSocios (ARRAY), con toda la información para body y $URLgastosLaicismo, 

LLAMADA: cTesorero:emailAvisarDomiciliadosProximoCobro() 
LLAMA: enviarEmailPhpMailer($emailAvisarDomiciliadosProximoCobro),
modeloErrores.php:insertarError()		
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES:
2020-10-15: probado PHP 7.3.21
Nota: He cambiado la función, en esta nueva función el texto común de los emails
se recibe en $datosEmailCuotaSocios['textoEmail']['bodyN'],[asunto],[nota] 
procedente del formulario modeloTesero.php:formEmailAvisarDomiciliadosProximoCobro.php  
Antes el texto estaba modeloEmail:emailAvisarDomiciliadosProxCobro(). 
Ahora habrá más consistencia entre lo mostrado en el formulario y lo enviado  
2023-10-01: Cambios para mejorar presentación añade nombre agrupación y otras mejoras de información 
-----------------------------------------------------------------------------------------------*/
function emailAvisarDomiciliadosProxCobro($datosEmailCuotaSocios,$URLgastosLaicismo)//****nuevo
{
	//echo "<br><br>0-1 modeloEmail:emailAvisarDomiciliadosProxCobro:datosEmailCuotaSocio: ";print_r($datosEmailCuotaSocios);echo "<br>";
 //echo "<br><br>0-2 modeloEmail:emailAvisarDomiciliadosProxCobro:URLgastosLaicismo: ";print_r($URLgastosLaicismo);echo "<br>";

	$reEmailAvisarDomiciliadosProxCobro['nomScript'] = "modeloEmail.php";	
 $reEmailAvisarDomiciliadosProxCobro['nomFuncion'] = "emailAvisarDomiciliadosProxCobro";
	$reEmailAvisarDomiciliadosProxCobro['codError'] = '00000';
	$reEmailAvisarDomiciliadosProxCobro['errorMensaje'] = '';	

 $acumuladores['emailEmailTratados'] = 0;	
	$acumuladores['emailEnviados'] = 0;	
 $acumuladores['emailErrorEnvio'] = 0;
 
 $textoFechaPrevistaCobro = $datosEmailCuotaSocios['textoFechaPrevistaCobro'];
	
	$asunto = $datosEmailCuotaSocios['textoEmail']['asunto'];
	
	$body1 = $datosEmailCuotaSocios['textoEmail']['body1'];
	$body2 = $datosEmailCuotaSocios['textoEmail']['body2'];
	$body3 = $datosEmailCuotaSocios['textoEmail']['body3'];
	$body4 = $datosEmailCuotaSocios['textoEmail']['body4'];
	$body5 = $datosEmailCuotaSocios['textoEmail']['body5'];
	$body6 = $datosEmailCuotaSocios['textoEmail']['body6'];//texto antes link usuarios
	$linkAccesoSocios = $datosEmailCuotaSocios['textoEmail']['body7'];//link usuarios
	$body8 = $datosEmailCuotaSocios['textoEmail']['body8'];//Europa Laica NO solicita subvenciones ...
	$body9 = $datosEmailCuotaSocios['textoEmail']['body9'];//texto información sobre ingresos antes URLgastosLaicismo
	$body10 = $datosEmailCuotaSocios['textoEmail']['body10'];//pie y privacidad
	$linkProteccionDatos = $datosEmailCuotaSocios['textoEmail']['body11'];
	
	$nota = $datosEmailCuotaSocios['textoEmail']['nota'];	
	
	if (isset($URLgastosLaicismo) && !empty ($URLgastosLaicismo))
	{
		$URLgastosLaicismo = "<a href='".$URLgastosLaicismo."'>".$URLgastosLaicismo."</a>";	
	}
	else 
	{ $URLgastosLaicismo = "";
	}

		// $body7 = "https://www.europalaica.org/usuarios"
	if (isset($linkAccesoSocios) && !empty ($linkAccesoSocios)) 	
	{
			$linkAccesoSocios = "<a href='".$linkAccesoSocios."'>".$linkAccesoSocios."</a>";	
	}
	else 
	{ $linkAccesoSocios = "";
	}	

	// $body11 = "https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad"
	if (isset($linkProteccionDatos) && !empty ($linkProteccionDatos)) 				
	{
			$linkProteccionDatos = "<a href='".$linkProteccionDatos."'>".$linkProteccionDatos."</a>";	
	}
	else 
	{ $linkProteccionDatos = "";
	}					
	
	$datosEnvioEmail = array ("fromAddress"         =>'tesoreria@europalaica.org',
																										 "fromAddressName" 				=>'Tesorería de Europa Laica',
																									 	"replayToAddress"		  	=>'tesoreria@europalaica.org',
																									 	"replayToAddressName"	=>'Tesorería de Europa Laica',																									
		                         //"toBCC"  		  									=>"adminusers@europalaica.org",
																									 	//"toBCCName"											=>"Europa Laica. Administrador de socios/as",																									
																									 	"subject" 		  								=>$asunto
																							   );	

 foreach ($datosEmailCuotaSocios['datosEmailCuotaSocios'] as  $fila => $contenidoFila)
	{//echo "<br><br>2-1 modeloEmail:emailAvisarDomiciliadosProxCobro:fila: ";print_r($fila);echo "--contenidoFila=";print_r($contenidoFila);	
	 //echo "<br><br>2-2 modeloEmail:emailAvisarDomiciliadosProxCobro:fila: ";print_r($fila);echo "--contenidoFila[Agrupacion_Actual]= ";print_r($contenidoFila['Agrupacion_Actual']);	
 
																					
		$datosEnvioEmail["toAddress"] = $contenidoFila['EMAIL'];
		$datosEnvioEmail["toAddressName"] = $contenidoFila['Apellidos_Nombre'];
				
		if ($contenidoFila['ESTADOCUOTA'] !== 'ABONADA-PARTE')
		{
	  $importeCuotaDonacion = "<strong>".str_replace(".", ",", $contenidoFila['CuotaDonacionPendienteCobro'])." euros </strong>";//cambia el formato de 50.00 a 50,00 , antes Importe_CuotaDonacion
		}
		else
		{
			$importePendienteCobro = str_replace(".", ",", $contenidoFila['CuotaDonacionPendienteCobro']);
			$importeAbonadaParte = str_replace(".", ",", $contenidoFila['IMPORTECUOTAANIOPAGADA']);	
			$importeCuotaDonacionSocioElegida = str_replace(".", ",", $contenidoFila['IMPORTECUOTAANIOSOCIO']);		
			$importeCuotaDonacion = "<strong>".$importePendienteCobro." euros,</strong> que es la cantidad que aún te faltaba por pagar de los ".$importeCuotaDonacionSocioElegida.
			                         " euros de la cuota total elegida, y de la cual ya habías abonado ".$importeAbonadaParte." euros";			
		}	  
		//echo "<br><br>3-1 modeloEmail:emailAvisarDomiciliadosProxCobro:importeCuotaDonacion: ";print_r($importeCuotaDonacion);	
		
		//-- prepara cadena IBAN, con sustitución de dígitos por  " * " 
		$longitud = 	strlen ( $contenidoFila['CUENTAIBAN']);
		$relleno  = $longitud - 8;						
		$cadIBAN1 = substr($contenidoFila['CUENTAIBAN'],0,4);    // devuelve los 4 primeros caracteres ES, o BE, ...						
		$cadIBAN2 = str_pad("",  $relleno, "*");                 // Rellena de " * " 
		$cadIBAN3 = substr($contenidoFila['CUENTAIBAN'], -4);    // devuelve 4 últimos digitos     
		$cadIBAN  = $cadIBAN1.$cadIBAN2.$cadIBAN3;
		
		//echo "<br><br>3-2 modeloEmail:emailAvisarDomiciliadosProxCobro:datosEnvioEmail:cadIBAN: ";print_r($cadIBAN);

		$datosEnvioEmail["body"] = $body1."<strong>".$contenidoFila['Apellidos_Nombre']."</strong> (Agrupación ".$contenidoFila['Agrupacion_Actual']." )".
		                           "\n\n".$body2."\n\n".$body3." *** ".$importeCuotaDonacion." ***".
		                           "\n\n".$body4."<strong>".$cadIBAN."</strong>".
																													"\n\n".$body5." *** ".$textoFechaPrevistaCobro." ***".$body6.$linkAccesoSocios.
																													"\n".$body8.$body9.$URLgastosLaicismo.
																													"\n\n".$nota.$body10.$linkProteccionDatos;																																	

		//echo "<br><br>4-1 modeloEmail:emailAvisarDomiciliadosProxCobro:datosEnvioEmail: ";print_r($datosEnvioEmail);

	
  //------- O J O ----- QUITAR LOS C0MENTARIOS PARA QUE ENVÍE LOS EMAILS -----------	 
  $reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);
		
		//---- O J O ----- des-comentar solo para pruebas ya tiene comentado la llamada del método //$mail->Send();-----------
		//Para activar descomentar línea en: $reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail); ";
		
		//echo "<br><br>4-2 modeloEmail:emailAvisarDomiciliadosProxCobro:***** MODO PRUEBA: DESACTIVADO EL ENVIO ******* NO SE HAN ENVIADO EMAILS A LOS SOCIOS<br/>
		//$reEnvioEmail = enviarEmailPhpMailerParaPruebas($datosEnvioEmail);//no envia nada, está comentado llamada metodo	
	 //echo "<br><br>5 modeloEmail:emailAvisarDomiciliadosProxCobro:reEnvioEmail: ";print_r($reEnvioEmail); 

	 if ($reEnvioEmail['codError'] !== '00000')
	 {
			$acumuladores['emailErrorEnvio']++ ;			

			$reEmailAvisarDomiciliadosProxCobro['codError'] = $reEnvioEmail['codError']; 
		 $reEmailAvisarDomiciliadosProxCobro['errorMensaje'] .= "<br />".$contenidoFila['Apellidos_Nombre'].", ".$contenidoFila['EMAIL'].": ".$reEnvioEmail['errorMensaje'];
	 }
		else
		{$acumuladores['emailEnviados']++ ;
		}
				
		$acumuladores['emailEmailTratados']++ ;	
 }//foreach ()
	
	//echo "<br><br>6-1 modeloEmail:emailAvisarDomiciliadosProxCobro:acumuladores: ";print_r($acumuladores);
	//echo "<br><br>6-2 modeloEmail:emailAvisarDomiciliadosProxCobro:reEmailAvisarDomiciliadosProxCobro: ";print_r($reEmailAvisarDomiciliadosProxCobro); 	

 if (isset($reEmailAvisarDomiciliadosProxCobro['errorMensaje']) && !empty ($reEmailAvisarDomiciliadosProxCobro['errorMensaje']))
 { $erroresEnvioDetectados = "<strong>NO SE HAN PODIDO ENVIAR LOS EMAILS INFORMANDO DEL PRÓXIMO COBRO DE LA CUOTA A LOS SIGUIENTES SOCIOS/AS:</strong><br /><br />".
                             $reEmailAvisarDomiciliadosProxCobro['errorMensaje'];			

   $reEmailAvisarDomiciliadosProxCobro['textoComentarios'] = "Error al enviar email informando próximo cobro la cuota domiciliada al socio/a. Email procesados: ".
																																																												  $acumuladores['emailEmailTratados'].". Errores email detectados: ".$acumuladores['emailErrorEnvio'];			
	
			require_once './modelos/modeloErrores.php';
			$resInsertarErrores = insertarError($reEmailAvisarDomiciliadosProxCobro); 

			$resEmailErrorWMaster = emailErrorWMaster($reEmailAvisarDomiciliadosProxCobro['textoComentarios'].': '.$reEmailAvisarDomiciliadosProxCobro['errorMensaje'].': '.
				                                         $reEmailAvisarDomiciliadosProxCobro['codError']);//en modeloEmail.php';																																																
  	
 }
 else
 { $erroresEnvioDetectados ="";		
	}		

	$reEmailAvisarDomiciliadosProxCobro['arrMensaje']['textoComentarios'] = "<br />Total de emails tratados: <strong>".$acumuladores['emailEmailTratados'].
																																																																									"</strong><br /><br />Emails enviados: <strong>".$acumuladores['emailEnviados'].
																																																																									"</strong> (es probable que algunos de estos email enviados no los reciban por diversas causas)". 
																																																																									"<br /><br />Emails error de envío: <strong>".$acumuladores['emailErrorEnvio'].
																																																																									"</strong><br /><br /><br />".$erroresEnvioDetectados."<br /><br />";
		
	//echo "<br><br>7 modeloEmail:emailAvisarDomiciliadosProxCobro:reEmailAvisarDomiciliadosProxCobro: ";print_r($reEmailAvisarDomiciliadosProxCobro); 
 
 return $reEmailAvisarDomiciliadosProxCobro;
}
/*----------------- FIN emailAvisarDomiciliadosProxCobro() ------------------------------------*/

/*--------- Inicio emailAvisarCuotaNoSinCobrarSinCC() ------------------------------------------- 
Dentro de un bucle que recorre un array con los datos personales de los socios (nombre,
cuota a pagar) y genera los datos de un email que se envía al socio, para comunicarle 
que aún no ha sido cobrada la cuota anual de la asociación Europa Laica.
Envía nombre y ape1, cantidad de la cuota que debe, datos bancos EuropaLaica, 
enlace personalizado mediante código socio encriptado a contoladorSocios.php:pagarCuotaSocioSinCC()
para poder pagar con PayPal.  
Información de EL y también enlace a web de laicismo.org con información gastos-ingresos.
Se incluyen siguiente los casos posibles tipo cuenta banco: SIN CUENTA, 
CUENTA-NOIBAN (actualmente ya no hay) y CUENTA-IBAN país SEPA distinto de ES y además con 
condicción "Ordenar cobro banco = NO". 
Incluye ESTADOCUOTA de socios/as:'PENDIENTE-COBRO', 'ABONADA-PARTE','NOABONADA-DEVUELTA',
'NOABONADA-ERROR-CUENTA' siempre que estén de "alta" en el momento actual y NO INCLUYE 
las cuotas "abonadas, exentas, y socios/as que estén de baja" y de sólo de 
las "AGRUPACIONES" elegidas en el formulario. 
													
RECIBE: $datosEmailCuotaSocios (ARRAY), con toda la información para body, bancos EL, 
PayPal, y $URLgastosLaicismo, 

LLAMADA: solo desde cTesorero:emailAvisarCuotaNoCobradaSinCC() 
LLAMA: usuariosLibs/encriptar/encriptacionBase64.php:encriptarBase64($contenidoFila['Referencia_codSocio'])
enviarEmailPhpMailer($datosEnvioEmail),
modeloErrores.php:insertarError()		
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES:
2020-10-15: probado PHP 7.3.21
Nota: He cambiado la función, en esta nueva función el texto común de los emails
se recibe en $datosEmailCuotaSocios['textoEmail']['bodyN'], ,[asunto],[nota] 
procedente del formulario modeloTesero.php:formEmailAvisarSinCobrarSinCC.php  
Antes el texto estaba modeloEmail:emailAvisarCuotaSinCobrarSinCC(). 
Ahora habrá más consistencia entre lo mostrado en el formulario y lo enviado 

2018-02-14: formato con comas para cantidades cuotas 
$importeDebido =str_replace(".", ",", $importeDebido);//cambia formato de 50.00 a 50,00  
2023-10-01: Cambios para mejorar presentación añade nombre agrupación y otras mejoras de información 
-----------------------------------------------------------------------------------------------*/
function emailAvisarCuotaSinCobrarSinCC($datosEmailCuotaSocios,$URLgastosLaicismo)
{
	//echo "<br><br>0-1a modeloEmail:emailAvisarCuotaSinCobrarSinCC:datosEmailCuotaSocio['textoEmail']: ";print_r($datosEmailCuotaSocios['textoEmail']);
 //echo "<br><br>0-1b modeloEmail:emailAvisarCuotaSinCobrarSinCC:datosEmailCuotaSocio['bancosAgrup']: ";print_r($datosEmailCuotaSocios['bancosAgrup']);
	//echo "<br><br>0-1c modeloEmail:emailAvisarCuotaSinCobrarSinCC:datosEmailCuotaSocio: ";print_r($datosEmailCuotaSocios);
 //echo "<br><br>0-2 modeloEmail:emailAvisarCuotaSinCobrarSinCC:URLgastosLaicismo: ";print_r($URLgastosLaicismo);

	$reEmailAvisarCuotaSinCobrarSinCC['nomScript'] = "modeloEmail.php";	
 $reEmailAvisarCuotaSinCobrarSinCC['nomFuncion'] = "emailAvisarDomiciliadosProxCobro";
 $reEmailAvisarCuotaSinCobrarSinCC['codError'] = '00000';
	$reEmailAvisarCuotaSinCobrarSinCC['errorMensaje'] = '';																										
																										
 $acumuladores['emailEmailTratados'] = 0;	
	$acumuladores['emailEnviados'] = 0;	
 $acumuladores['emailErrorEnvio'] = 0;
 
	
	$asunto = $datosEmailCuotaSocios['textoEmail']['asunto'];
	
	$body1  = $datosEmailCuotaSocios['textoEmail']['body1'];
	$body2  = $datosEmailCuotaSocios['textoEmail']['body2'];
	$body3  = $datosEmailCuotaSocios['textoEmail']['body3'];
	$body4  = $datosEmailCuotaSocios['textoEmail']['body4'];
	$body5  = $datosEmailCuotaSocios['textoEmail']['body5'];
	$body6  = $datosEmailCuotaSocios['textoEmail']['body6'];
	$body7  = $datosEmailCuotaSocios['textoEmail']['body7'];
	//$body7="https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&amp;accion=pagarCuotaSocioSinCC&amp;parametro=";
	
	$body8  = $datosEmailCuotaSocios['textoEmail']['body8'];
	$body9  = $datosEmailCuotaSocios['textoEmail']['body9'];
	$body10 = $datosEmailCuotaSocios['textoEmail']['body10'];
	$body11 = $datosEmailCuotaSocios['textoEmail']['body11'];	
	$linkAccesoSocios = $datosEmailCuotaSocios['textoEmail']['body12'];	
	$body13 = $datosEmailCuotaSocios['textoEmail']['body13'];
	$linkProteccionDatos = $datosEmailCuotaSocios['textoEmail']['body14'];
	$nota = $datosEmailCuotaSocios['textoEmail']['nota'];	
	
	if (isset($URLgastosLaicismo) && !empty ($URLgastosLaicismo))
	{
		 $URLgastosLaicismo = "<a href='".$URLgastosLaicismo."'>".$URLgastosLaicismo."</a>";	
	}
	else 
	{ $URLgastosLaicismo = "";
	}		
	
	// $body12 = "https://www.europalaica.org/usuarios"
	if (isset($linkAccesoSocios) && !empty ($linkAccesoSocios)) 	
	{
			$linkAccesoSocios = "<a href='".$linkAccesoSocios."'>".$linkAccesoSocios."</a>";	
	}
	else 
	{ $linkAccesoSocios = "";
	}	

	// $body12 = "https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad"
	if (isset($linkProteccionDatos) && !empty ($linkProteccionDatos)) 				
	{
			$linkProteccionDatos = "<a href='".$linkProteccionDatos."'>".$linkProteccionDatos."</a>";	
	}
	else 
	{ $linkProteccionDatos = "";
	}					

	$datosEnvioEmail = array ("fromAddress"         =>'tesoreria@europalaica.org',
																										 "fromAddressName" 				=>'Tesorería de Europa Laica',
																									 	"replayToAddress"		  	=>'tesoreria@europalaica.org',
																									 	"replayToAddressName"	=>'Tesorería de Europa Laica',																									
		                         //"toBCC"  		  									=>"adminusers@europalaica.org",
																									 	//"toBCCName"											=>"Europa Laica. Administrador de socios/as",																									
																									 	"subject" 		  								=>$asunto
																							   );																										

																						
 foreach ($datosEmailCuotaSocios['datosEmailCuotaSocios'] as  $fila => $contenidoFila)
	{ //echo "<br><br>2-1 modeloEmail:emailAvisarCuotaNoSinCobrarSinCC:fila: ";print_r($fila);echo "--contenidoFila=";print_r($contenidoFila);	

			require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
			$codSocioEncriptado = encriptarBase64($contenidoFila['Referencia_codSocio']);
			
			/*	Para pagar ahora con PayPal, haz clic en el siguiente enlace: ";		
						$body7 = "	https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&amp;accion=pagarCuotaSocioSinCC&amp;parametro=".$codSocioEncriptado.		
   */

			$linkPayPalPagoSocio="<a href='".$body7.$codSocioEncriptado."'>".$body7.$codSocioEncriptado."</a>";				
			
			//echo "<br><br>2-2 modeloEmail:emailAvisarCuotaNoSinCobrarSinCC:fila: ";print_r($fila);echo ": linkPayPalPagoSocio=";print_r($linkPayPalPagoSocio);
				
			
			$datosEnvioEmail["toAddress"] = $contenidoFila['EMAIL'];
			$datosEnvioEmail["toAddressName"] = $contenidoFila['Apellidos_Nombre'];

			if ($contenidoFila['ESTADOCUOTA'] !== 'ABONADA-PARTE')
			{
				$importeCuotaDonacion = str_replace(".", ",", $contenidoFila['CuotaDonacionPendienteCobro'])." euros ";//cambia el formato de 50.00 a 50,00 , antes Importe_CuotaDonacion
			}
			else
			{
				$importePendienteCobro = str_replace(".", ",", $contenidoFila['CuotaDonacionPendienteCobro']);
				$importeAbonadaParte = str_replace(".", ",", $contenidoFila['IMPORTECUOTAANIOPAGADA']);	
				$importeCuotaDonacionSocioElegida = str_replace(".", ",", $contenidoFila['IMPORTECUOTAANIOSOCIO']);		
				$importeCuotaDonacion = $importePendienteCobro." euros, que es la cantidad que aún te faltaba por pagar de los ".$importeCuotaDonacionSocioElegida.
																													" euros de la cuota total elegida, y de la cual ya habías abonado ".$importeAbonadaParte." euros";			
		 }	  
		
		 //echo "<br><br>3-1 modeloEmail:emailAvisarDomiciliadosProxCobro:importeCuotaDonacion: ";print_r($importeCuotaDonacion);		
			//echo "<br><br>3-2 modeloEmail:emailAvisarDomiciliadosProxCobro:datosEmailCuotaSocios['bancosAgrup'][contenidoFila['CODAGRUPACION']]: ";print_r($datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]);		
	  
			//--- Inicio para los bancos de cada agrupación (a fecha 2023-10-01 excepto Asturias, para las demaás agrupaciones será el banco/s de Europa Laica Estatal)---------------------
			
			$titularCuentasBancos = $datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['TITULARCUENTASBANCOS'];
			
			$agrupacionTerritorialNombre = $datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMAGRUPACION'];
			
			if (isset($datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN1']) && !empty ($datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN1']))
			{
				$datosBanco1 = $datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN1'].": <strong>".$datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['CUENTAAGRUPIBAN1_PAPEL']."</strong>";    
			}
			else 
			{ $datosBanco1 = "";
			}							
			if (isset($datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN2']) && !empty ($datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN2']))
			{			
			 $datosBanco2 = $datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['NOMBREIBAN2'].": <strong>".$datosEmailCuotaSocios['bancosAgrup'][$contenidoFila['CODAGRUPACION']]['CUENTAAGRUPIBAN2_PAPEL']."</strong>";
			}
			else 
			{ $datosBanco2 = "";
			}		
			//--- Fin para los bancos de cada agrupación ------------------------------------------------------------------------
   
			//------ Inicio formar el contenido del body ------------
			$datosEnvioEmail["body"] = $body1."<strong>".$contenidoFila['Apellidos_Nombre']."</strong> (Agrupación ". $agrupacionTerritorialNombre." )".
			                           "\n\n".$body2."<strong>".$importeCuotaDonacion."</strong>".$body3./*en $contenidoFila['ANIOCUOTA'].".".*/
																														"\n\n".$body4.
                              $titularCuentasBancos.																															
																														"\n".$datosBanco1.
																														"\n".$datosBanco2.																														
																														"\n".$body5.
																														//"\n\n".$body6.$body7.$codSocioEncriptado.
																														"\n\n".$body6.$linkPayPalPagoSocio.																														
																														"\n\n".$body8.
																														"\n".$body9.
																														"\n".$body10.$URLgastosLaicismo.
																														"\n\n".$body11.$linkAccesoSocios.
																														"\n\n".$nota.$body13.$linkProteccionDatos;																														

			//echo "<br><br>3-3 modeloEmail:emailAvisarCuotaNoSinCobrarSinCC:datosEnvioEmail: ";print_r($datosEnvioEmail);
			//------ Fin formar el contenido del body ------------
		
			//--- O J O ----- QUITAR LOS C0MENTARIOS PARA QUE ENVÍE LOS EMAILS de la siguiente línea -----------	 
   $reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);//probado error ok
			
			//--- O J O ----- des-comentar solo para pruebas ya tiene comentado la llamada del método//$mail->Send();------
			//echo "<br><br>***** MODO PRUEBA: DESACTIVADO EL ENVIO ******* NO SE HAN ENVIADO EMAILS A LOS SOCIOS<br/> 
			//Para activar descomentar línea en: 4 modeloEmail.php:emailAvisarCuotaNoSinCobrarSinCC():datosEnvioEmail:<br /> ";	
			//$reEnvioEmail = enviarEmailPhpMailerParaPruebas($datosEnvioEmail);//no envia nada, esta comentado llamada metodo		
   //echo "<br><br>4 modeloEmail:emailAvisarCuotaSinCobrarSinCC:reEnvioEmail:<br />";print_r($reEnvioEmail); 
 
			if ($reEnvioEmail['codError'] !== '00000')
			{
				$acumuladores['emailErrorEnvio']++ ;

				$reEmailAvisarCuotaSinCobrarSinCC['codError'] = $reEnvioEmail['codError']; 
		  $reEmailAvisarCuotaSinCobrarSinCC['errorMensaje'] .= "<br />".$contenidoFila['Apellidos_Nombre'].", ".$contenidoFila['EMAIL'].": ".$reEnvioEmail['errorMensaje'];					
			}
			else // $reEnvioEmail['codError'] == '00000'
			{
				$acumuladores['emailEnviados']++ ;
			}	
		
			$acumuladores['emailEmailTratados']++ ;	
			
	}//foreach ($datosEmailCuotaSocios['datosEmailCuotaSocios'] as  $fila => $contenidoFila)
	
	//echo "<br><br>5-1 modeloEmail:emailAvisarCuotaSinCobrarSinCC:acumulador: ";print_r($acumuladores);
	//echo "<br><br>5-2 modeloEmail:emailAvisarCuotaSinCobrarSinCC:reEmailAvisarCuotaSinCobrarSinCC: ";print_r($reEmailAvisarCuotaSinCobrarSinCC);
	
 if (isset($reEmailAvisarCuotaSinCobrarSinCC['errorMensaje']) && !empty($reEmailAvisarCuotaSinCobrarSinCC['errorMensaje']))
 { $erroresEnvioDetectados = "<strong>NO SE HAN PODIDO ENVIAR LOS EMAILS INFORMANDO QUE AÚN TIENE PENDIENTE EL PAGO DE LA CUOTA ANUAL A LOS SIGUIENTES SOCIOS/AS:</strong><br /><br />".
                              $reEmailAvisarCuotaSinCobrarSinCC['errorMensaje'];	
   
   $reEmailAvisarCuotaSinCobrarSinCC['textoComentarios'] = "Error al enviar email a socio/a informando que aún tiene pendiente el pago de la cuota anual no domiciliada. Email procesados: ".
																																																												$acumuladores['emailEmailTratados'].". Errores email detectados: ".$acumuladores['emailErrorEnvio'];			

   require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($reEmailAvisarCuotaSinCobrarSinCC);				
			
		 $resEmailErrorWMaster = emailErrorWMaster($reEmailAvisarCuotaSinCobrarSinCC['textoComentarios'].': '.$reEmailAvisarCuotaSinCobrarSinCC['errorMensaje'].': '.
	 	                                          $reEmailAvisarCuotaSinCobrarSinCC['codError']);//en modeloEmail.php';																																											
	}
 else
 { $erroresEnvioDetectados = "";		
	}		

	$reEmailAvisarCuotaSinCobrarSinCC['arrMensaje']['textoComentarios'] = "<br />Total de emails tratados: <strong>".$acumuladores['emailEmailTratados'].
																																																																							"</strong><br /><br />Emails enviados: <strong>".$acumuladores['emailEnviados'].
																																																																							"</strong> (es probable que algunos de estos no los reciban por diversas causas)". 
																																																																							"<br /><br />Emails error de envío: <strong>".$acumuladores['emailErrorEnvio'].
																																																																							"</strong><br /><br /><br />".$erroresEnvioDetectados."<br /><br />";

	//echo "<br><br>6 modeloEmail:emailAvisarCuotaSinCobrarSinCC:reEmailAvisarCuotaSinCobrarSinCC: ";print_r($reEmailAvisarCuotaSinCobrarSinCC); 
 
 return $reEmailAvisarCuotaSinCobrarSinCC;
}
/*----------------- FIN emailAvisarCuotaNoSinCobrarSinCC() ------------------------------------*/

/*---------------------------- Inicio emailPagoPayPalTesoria ------------------------------------
DESCRIPCION: Envía emails a tesorería para avisar que un socio que ha realizado un pago por medio de PayPal,
             Pega: si el socio cierra la ventana de PayPal, sin pulsar el link de volver a la aplicación, 
													 no se actualizrá la tabla CUATAANIOSOCIOS ni llegará este email, por lo que el tesorero deberá revisar siempre la pago de PayPal)
LLAMADO: solo desde cPayPal.php:confirmadoPagoAltaSocioPayPal_Registrarse(),confirmarPagoPayPal(),	
RECIBE : $datosEmailCoSeTe: (array con direcciones de email y nombres de coordinador, presidente,
         secretaria, tesoreria)		
									$datosPagoPayPal: (array con datos del pago del socio, procedentes del $_POST que deuelve PayPal, 
									después de anotar el pago: nombre, email, fecha alta, importe, ...		 
OBSERVACIONES: 
															
NOTA: ACASO SEA MEJOR CON enviarEmailPhpMailer() EN LUGAR DE CON enviarMultiplesEmailsPhpMaileR()
-----------------------------------------------------------------------------------------------*/	
function emailPagoPayPalTesoria($datosEmailCoSeTe,$datosPagoPayPal)
{
 //echo "<br><br>1 modeloEmail:emailPagoPayPalTesoria:datosEmailCoSeTe: ";print_r($datosEmailCoSeTe);
 //echo "<br><br>2 modeloEmail:emailPagoPayPalTesoria:cadenaDatosAnotadosPorPaypal: ";print_r($datosPagoPayPal);	
	
	/*************** OJO para explotación descomentar lo siguiente ******************************/
 $datosEnvioEmailCoSeTe['AddAddress']['EMAILTESORERO']['email'] = $datosEmailCoSeTe['EMAILTESORERO'];
	$datosEnvioEmailCoSeTe['AddAddress']['EMAILTESORERO']['nombre'] = 'Tesorería de '.$datosEmailCoSeTe['NOMAGRUPACION'];
	/**********************************************************************************************/
	
	/*************** OJO para pruebas descomentar lo siguiente ******************************/
	//$datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['email'] = 'adminusers@europalaica.org';
	//$datosEnvioEmailCoSeTe['AddAddress']['TESORERO']['nombre'] = 'Prueba Gestión de Socios/as de Europa Laica';//bien	
 /*************************************************************************************************************************/
	// otras posibilidades:
	//$datosEnvioEmailCoSeTe['AddAddress']['EMAILCOORD']['email'] = $datosEmailCoSeTe['EMAILCOORD'];
	//$datosEnvioEmailCoSeTe['AddAddress']['EMAILCOORD']['nombre'] = 'Coordinación de '.$datosEmailCoSeTe['NOMAGRUPACION'];
	//$datosEnvioEmailCoSeTe['AddCC'][0]['email'] = $datosEmailCoSeTe['EMAILCOORD'];
	//$datosEnvioEmailCoSeTe['AddCC'][0]['nombre'] = 'Coordinación de '.$datosEmailCoSeTe['NOMAGRUPACION'];
	//$datosEnvioEmailCoSeTe['AddCC'][1]['email'] = 'segvilla50@hotmail.com';	//pruebas
	//$datosEnvioEmailCoSeTe['AddCC'][1]['nombre'] = 'email prueba';		
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['email'] = 'adminusers@europalaica.org';//no para evitar respuesta 	
	//$datosEnvioEmailCoSeTe['AddReplyTo'][0]['nombre'] = 'Administrador de usuarios';
 $datosEnvioEmailCoSeTe['AddBCC'][0]['email'] = 'adminusers@europalaica.org';
	$datosEnvioEmailCoSeTe['AddBCC'][0]['nombre'] = 'Administrador de usuarios';
 //-------------------------------------------------------------------------------------------
	
	$datosEnvioEmailCoSeTe['fromAddress'] = 'adminusers@europalaica.org';//bien
 $datosEnvioEmailCoSeTe['fromAddressName'] = 'Gestión de Socios/as de Europa Laica';//bien	
		
	//-----------------------------------------------------------------------------------
	//$comentarioSocio = $datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'];
 
 $fechaPago = $datosPagoPayPal['fechaPago'];
	
	//$conceptoPago = $datosPagoPayPal['conceptoPago'];
	$nom = $datosPagoPayPal['nombre'];
	$apellidos = $datosPagoPayPal['apellidos'];
	$emailUsuario = $datosPagoPayPal['emailPagadorPayPal'];	
	$numReciboFactura = $datosPagoPayPal['numReciboFactura'];	
	$identificadorTransacción = $datosPagoPayPal['IdentificadorTransaccion'];
	$producto = $datosPagoPayPal['producto'].": ".$datosPagoPayPal['identificadorProducto'];		
	$importe = $datosPagoPayPal['importe'];
	$iva = $datosPagoPayPal['IVA'];	
	$totalPagos = $datosPagoPayPal['totalPagos'];	
	$gastosPayPal = $datosPagoPayPal['gastosPayPal'];
	
	$asunto = "Europa Laica. Confirmado pago cuota con PayPal";
		
		
	$contenidoCuerpoComun = "A Europa Laica Tesorería: comprobar en PayPal y anotar el pago en la aplicación de Gestión de Socios/as con los siguientes datos:\n".
	"\nPago de la cuota del socio/a: ".$datosPagoPayPal['apeNomSocio'].
	"\nDocumento del socio/a ".$datosPagoPayPal['tipoDocumento'].": ". $datosPagoPayPal['numeroDocumento'].
	"\n\nCon fecha:  ".$fechaPago. " se ha recibido una notificación de pago en PayPal por:".
	$nom." ".$apellidos.	
		"\nemail: ".$emailUsuario.
	"\n\nen concepto de ".$producto.
	"\n\nNúmero de recibo de PayPal: ".$numReciboFactura.
	"\nIdentificador de la transacción:".$identificadorTransacción. 
	"\n\n\nDETALLES DEL PAGO:
	\nGastos cobrados por PayPal a Europa Laica: ".$gastosPayPal. 
	"\n\nImporte: ".$importe. 
	"\nIVA: ".$iva.	
	"\n-----------------".	
	"\nTotal pagado: ".$totalPagos.
"\n\n\nOBSEVACIONES: ".$datosPagoPayPal['observaciones'];


	$contenidoPie ="
\n********************
Un saludo,
Gestión de Socios/as
Europa Laica

Nota: este email es enviado automáticamente desde el programa de gestión de socios/as y no requiere respuesta";			
 $contenido = $contenidoCuerpoComun.$contenidoPie;				
//---------------------------------------------------------------------------------------	

 $datosEnvioEmailCoSeTe['subject'] = $asunto;	
 $datosEnvioEmailCoSeTe['body'] = $contenido;				
	
	//echo "<br><br>3 modeloEmail:emailPagoPayPalTesoria:datosEnvioEmailCoSeTe: ";print_r($datosEnvioEmailCoSeTe);

 $reEnviarEmail = enviarMultiplesEmailsPhpMailer($datosEnvioEmailCoSeTe);
	
 //echo "<br><br>4 modeloEmail:emailPagoPayPalTesoria:reEnviarEmail: ";print_r($reEnviarEmail); 
 return $reEnviarEmail;				
}			

//------------------------------ Fin emailPagoPayPalTesoria -------------------------------------

//===== FIN EMAIL TESORERO PARA NOTIFICAR SOCIOS PROXIMO COBRO CUOTA ============================


/*===== INICIO ENVIAR EMAILS SIN PERSONALIZADOS A SOCIOS DESDE PRESIDENCIA, COORDINACIÓN ========
 Esta es la función de envío SIN personalizar con los nombres de socios, pero es más rápida que la
 otra función "emailSociosPersonaGestorPresCoord()" que personaliza los emails con sus nombres
===============================================================================================*/
/*--------- Inicio emailSociosMultipleGestorPresCoord() ---------------------------------------- 
Para envíar emails múltiples a los socios SIN PERSONALIZAR (sin nombre socios en el body)

Con los datos del array "$datosEnvioEmailSocios" recibidos del controlador correspondiente,
se crea el array "$datosEnvioEmail", en primer lugar con los datos comunes, incluidos
los archivos adjuntos si los hubiese.

Después se hace una validación previa con la función validarEmail($contenidoFila['EMAIL'],"");
aunque se supone que al introducir el email ya se validó, por si hubiese algún cambio 
directamente en la Tabla MIEMBRO (NO DEBIERA).  

Después dentro del bucle foreach ($datosEnvioEmailSocios['emailSociosSeleccionados']) 
se preparan los emails de los socios (excluyendo emails con errores de formato detectados previamente)
para ser enviados todos como "AddBCC" (ocultos), y el [BCC] procedente del formulario para copia 
se añade al final del array y también como se envía como AddBCC oculto. 

Se llama a la función "enviarMultiplesEmailsPhpMailer()" para enviar de una sola vez a todos 
los socios que se han seleccionado en el formulario, el mismo email sin personalizar. 
La lista de los emails de los socios se formará con $mail->AddBCC().

Si se detecta un error en un ['AddBCC'] (formato email incorrecto, o vacío), o de otro tipo 
en alguno de los métodos de PHPMailer(), se indicará el tipo de error y el email que causa el error
pero se enviarán a los emails que no den error.

Mediante acumuladores, se controlan el num. de emails enviados y el num. de errores
y se hace la lista de los email y nombres de socios con error.
													
RECIBE: array "$datosEnvioEmailSocios", que contiene todos los datos comunes y los 
individuales (nombre, email) de cada socio y BCC, además de los comunes procedentes 
del formulario(el array "$datosEnvioEmailSocios" que llega se describe dentro de esta 
función por campos).

LLAMADA: cPresidente.php:enviarEmailSociosPres(),
Antes también cCoordinador.php:enviarEmailSociosCoord(), ahora solo usa emailSociosPersonaGestorPresCoord() 
LLAMA: modeloEmail.php:enviarMultiplesEmailsPhpMailer()(Que utiliza la clase PHPMailer.
modeloErrores.php:insertarError()		
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES:
2022-02-28: probado PHP 7.3.21

NOTA: Alternativa a esta función es emailSociosPersonaGestorPresCoord(), que envía emails 
personalizados con nombre de socios (aunque es más lenta y podría dar problemas con TIMEOUT)  
----------------------------------------------------------------------------------------------*/
function emailSociosMultipleGestorPresCoord($datosEnvioEmailSocios)
{	
 //$tiempo_inicio = microtime(true);//fecha Unix actual en microsegundos
	//echo "<br><br>0-1 modeloEmail:emailSociosMultipleGestorPresCoord:tiempo_inicio: ";print_r($tiempo_inicio);	
	//echo "<br><br>0-2 modeloEmail:emailSociosMultipleGestorPresCoord:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);

	$resEmailSociosMultiple['nomScript'] = "modeloEmail.php";	
	$resEmailSociosMultiple['nomFuncion'] = "emailSociosMultipleGestorPresCoord";
 $resEmailSociosMultiple['codError'] = '00000';
	$resEmailSociosMultiple['errorMensaje'] = '';																										
																										
 $acumuladores['emailEmailTratados'] = 0;//no es necesario
	$acumuladores['emailEmailDetectados'] = 0;
	$acumuladores['emailEnviados'] = 0;	

	/*Ejemplo array: $datosEnvioEmailSocios[emailSociosSeleccionados] => Array ([0]=>Array ([CODUSER]=>98 [CODSOCIO]=>98 
	[EMAIL]=>segvilla50@hotmail.com [SEXO]=>H [NOM]=>CARLOS [APE1]=>AVEDA [APE2]=>MARTINEZ [apeNom]=>AVEDA MARTINEZ, CARLOS 
	[TELMOVIL]=>666666666 [TELFIJOCASA] => )
	*/
	
	// Obtener nombre de FROM a partir del email del formulario de email de envío:
	
	$posArroba = strpos($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'],'@');
	$nombre = substr($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'], 0,$posArroba);	
	$nombreFromMayusculas = mb_strtoupper(substr($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'], 0,$posArroba))." EUROPA LAICA";	
		
	$datosEnvioEmail = array (
						"fromAddress"         => $datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'],//"fromAddress" => "aviñññññ@hotmail.com",//para pruebas	
						"fromAddressName" 				=> $nombreFromMayusculas,									
						//"replayToAddress"		  	=> "dirección de respuesta distinta dirección de fromAddress, opcional",
						//"replayToAddressName"	=> "nombre de respuesta distinta dirección de fromAddress, opcional",			
						"subject" 		  								=> $datosEnvioEmailSocios['camposEmail']['subject']['valorCampo'],
						"body" 		  								   => $datosEnvioEmailSocios['camposEmail']['body']['valorCampo'].$datosEnvioEmailSocios['camposEmail']['pieProteccionDatos']['valorCampo']																				
																										);																											

	//echo "<br><br>1 modeloEmail:emailSociosMultipleGestorPresCoord:datosEnvioEmail: ";print_r($datosEnvioEmail);
	
	/*--- Inicio Preparar valores recibidos de ARCHIVOS ADJUNTOS para el envio email -------------*/	
	/*Nota: Es opcional enviar archivos, los emails pueden no tener archivos adjuntos
			Los datos vendrán en el array y ya controlados los tamañanos y tipos permitidos, ejemplo:
			$datosEnvioEmailSocios['AddAttachment'] => Array ([codError] => 00000 [errorMensaje] => 
			[0]=>Array([name]=>Surface_Applicaciones_2020_05_29.odt [type]=>application/vnd.oasis.opendocument.text [tmp_name]=>/tmp/phpSjbySf [error]=>0 [size]=>10224 [codError]=>00000 ) 
			[1] ...)
	*/		
	if (isset($datosEnvioEmailSocios['AddAttachment'])	&& !empty($datosEnvioEmailSocios['AddAttachment']))
	{ 	
			//------- Adaptación de valores archivos adjunto recibidos ----------------------------------	
			if (isset($datosEnvioEmailSocios['AddAttachment']['codError']))
			{	unset($datosEnvioEmailSocios['AddAttachment']['codError']);}//para que no de error en foreach	siguiente u otros en enviarMultiplesEmailsPhpMailer()	
			if (isset($datosEnvioEmailSocios['AddAttachment']['errorMensaje']))
			{	unset($datosEnvioEmailSocios['AddAttachment']['errorMensaje']);}
		
			foreach ($datosEnvioEmailSocios['AddAttachment'] as $f => $contenidoF)
			{ 
					$datosEnvioEmail['AddAttachment'][$f]['pathFile'] = $contenidoF['tmp_name'];//nombre archivo temporal con directorio incluido
					$datosEnvioEmail['AddAttachment'][$f]['nameSend'] = $contenidoF['name'];//nombre que se mostrá para el archivo (no incluye directorio)
			}			
	}		
	//echo "<br><br>2-1 modeloEmail:emailSociosMultipleGestorPresCoord:datosEnvioEmail['AddAttachment']: ";print_r($datosEnvioEmail['AddAttachment']);		
			
	/*-------------------- Fin Preparar archivos adjuntos ----------------------------------------*/				
		
	/*---- Inicio bucle para preparar los emails socios para después hacer un solo envío ----------	
	Al no ser personalizado, no se ponen los nombres en el body, pero sirven para detectar errores
	y después poderlos corregir por el gestor en Gestión de Soci@s en su Lista de soci@s
	---------------------------------------------------------------------------------------------*/ 
	
	/*----- Inicio Validación previa de formato de emails socios  -------------------------------*/
	$resulValidar = array();
	$erroresEmail = '';
	$textoEmailErrorFormatoDetectados = '';
 $textoCorregirEmailError	= '';
	
	foreach ($datosEnvioEmailSocios['emailSociosSeleccionados']  as  $numFila => $contenidoFila)
	{
		$nomApeSocio = mb_strtoupper($contenidoFila['APE1']." ".$contenidoFila['APE2'].", ".$contenidoFila['NOM']);
		
		$resulValidar['email'] = validarEmail($contenidoFila['EMAIL'],"");//esta función no valida dominio DNS con checkdnsrr($domain, 'MX'); Para evitar consumo recursos 	
	
	 //echo "<br><br>2-2 modeloEmail:emailSociosMultipleGestorPresCoord:resulValidar: ";print_r($resulValidar);		
		
		if ($resulValidar['email']['codError'] !== '00000')
		{ 		   
    $acumuladores['emailEmailDetectados']++;									
    $erroresEmail .= $resulValidar['email']['errorMensaje'].": ".$nomApeSocio.": ".$contenidoFila['EMAIL']."<br />";
							
				$textoEmailErrorFormatoDetectados = "<br /><br /><br /><strong>NO SE HAN PODIDO ENVIAR A LOS SIGUIENTES EMAILS</strong><br /><br />".$erroresEmail;
    $textoCorregirEmailError = "<br />Anota estos datos y después busca a estos socios/as para corregir su email, o si no en el campo 
																															<i>*Error correo electrónico</i>, elige: <strong>ERROR-FORMATO</strong>.
																															<br /><br />Cuando conozcas sus emails correctos, para evitar envíos repetidos, podrías enviarles individualmente
																															un email con el contenido del anterior email desde el correo de -". $datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'];
		}
		/*----- Fin Validación previa de formato de emails socios  ----------------------------------*/
		else //sin error email detectado
		{
			 $datosEnvioEmail['AddBCC'][$numFila]['email']  = $contenidoFila['EMAIL'];//AddBCC para que la lista de direcciones de email no sea visible	
				$datosEnvioEmail['AddBCC'][$numFila]['nombre'] = $nomApeSocio;			
 	}
	}//foreach()
	/*----- Fin bucle para preparar los emails socios para después enviar ------------------------*/
		
	/*-- Inicio preparar email BCC para enviar al final de la lista de los emails a socios -------*/		
	/* El email campo BCC para enviar una copia a un BCC especifico, se introduce en el formulario y después se valida. 
	   Lo añadiré al final de los emails de los socios,	nombre "- COPIA DEL EMAIL ENVIADO -" para localizar si hay un error,				 
				(También podría enviarlo como AddCC si se quiere mostrar a quien llega el email de copia CC.) 
	*/	
	
	if (isset($datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']) && !empty($datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']))//el BCC (uno solo) viene del formulatio tiene eliminador blancos 
	{   			
			$datosEnvioEmail['AddBCC'][] = array ('email' => $datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo'], 'nombre' =>  "- COPIA OCULTA DEL BCC DEL EMAIL A SOCIOS/AS-");	
	}	
	//echo "<br><br>3 modeloEmail:emailSociosMultipleGestorPresCoord:datosEnvioEmail['AddBCC']: ";print_r($datosEnvioEmail['AddBCC']); 
	
	/*-- Fin preparar email BCC para enviar al final de la lista de los emails a socios ---------*/	
	
 //Al menos 1 email es válido BCC que ya ha sido validado el formato previamente con validarEmail() desde el controlador
	
	//************ PARA PRUEBAS COMENTAR SIGUIENTE LINEA PARA NO ENVIAR ***************************		
	
	$resEmailMultiple = enviarMultiplesEmailsPhpMailer($datosEnvioEmail);//para pruebas comentar		

	//echo "<br><br>4 modeloEmail:emailSociosMultipleGestorPresCoord:resEmailMultiple: ";print_r($resEmailMultiple);	
			
	/*------ Inicio tratamiento de errores y resultados totales ----------------------------------
		Si se detecta un error PHPMailer en un ['AddBCC'] (formato email incorrecto), o de otro tipo en alguno
		de los métodos de PHPMailer(), se indicará el tipo de error del primer email que causa el error, 
		y no se enviará ningún email de todos los incluidos en  el array $datosEnvioEmail.
		Al detectar un error se detiene el control de errores de los demás emails, por lo que el 
		array $datosEnvioEmail['AddBCC'] podría tener más de un error aunque solo muestre el primero.
	*/	
	if ($resEmailMultiple['codError'] !== '00000')//si hay un error en un ['AddBCC'], "from" o de otro tipo PHPMailer indicará el tipo de error
	{
		$resEmailSociosMultiple['codError'] = $resEmailMultiple['codError']; 		  				
		$resEmailSociosMultiple['errorMensaje'] = $resEmailMultiple['errorMensaje'];		
		$resEmailSociosMultiple['textoComentarios'] = "<strong>NO SE HA ENVIADO NINGÚN EMAIL</strong><br /><br />".$resEmailMultiple['errorMensaje'];
  
		//--- Inicio Buscar el nombre del socio cuyo email da error en PHPMailer, pero no detectado por validarEmail(),al primer error suspende todo el envio
		foreach ($datosEnvioEmail['AddBCC']  as  $numFila => $contenidoFila)
		{ 			
				if (substr_count($resEmailMultiple['errorMensaje'], $contenidoFila['email']) > 0)//encontrada subcadena $contenidoFila['email'] una o mas veces dentro de cadena $resEmailMultiple['errorMensaje']
				{    
						$resEmailSociosMultiple['textoComentarios'] = "<strong>NO SE HA ENVIADO NINGÚN EMAIL</strong><br /><br /><br />".$resEmailMultiple['errorMensaje'].
																																																				"<br />este email pertenece a <strong>". mb_strtoupper($contenidoFila['nombre'])."</strong>".
																																																				"<br /><br /><br />Después de corregir el error puedes volver a envíar los emails";	
				} 					
		}		
		//--- Fin Buscar el nombre del socio cuyo email da error en PHPMailer 
		
		require_once './modelos/modeloErrores.php';
		$resInsertarErrores = insertarError($resEmailSociosMultiple);					
		$resEmailErrorWMaster = emailErrorWMaster($resEmailSociosMultiple['nomScript'].": ". $resEmailSociosMultiple['nomFuncion']. $resEmailSociosMultiple['textoComentarios'].'
																																												: '. $resEmailSociosMultiple['errorMensaje'].': '. $resEmailSociosMultiple['codError']);	
			
	}//if ($resEmailMultiple['codError'] !== '00000')
	else //resEmailSociosMultiple['codError'] == '00000'//Ningún PHPMailer error envío detectado, pero previamente se filtraron errores de formato email, se mostrarán para poderlos corregir 
	{			
		$resEmailSociosMultiple['textoComentarios'] = 	"<strong>Total de emails enviados a socios/as incluido copia a BCC: ". 
																																																	($numFila +2 - $acumuladores['emailEmailDetectados'])."</strong>".
                                                 "<br /><br />Total de emails seleccionados incluido BCC: <strong>".($numFila +2). 
																																								         "</strong><br /><br />Total de errores de formato en email detectados: <strong>".$acumuladores['emailEmailDetectados']."</strong>".																																																	
																																																	"<br /><br />Puede suceder que algunas/os de estas socias/os no los reciban por causas diversas: 
																																																	filtros antiespan, buzones llenos, cuentas caducadas, etc.".
																																																	"<br />".$textoEmailErrorFormatoDetectados."<br />".$textoCorregirEmailError;					
	}//else resEmailSociosMultiple['codError'] == '00000'																														
																																																																			
	/*------ Fin tratamiento de errores y resultados totales -------------------------------------*/																																																																							

	//echo "<br><br>5 modeloEmail:emailSociosMultipleGestorPresCoord:resEmailSociosMultiple: ";print_r($resEmailSociosMultiple); 
		
 //$tiempo_fin = microtime(true);
 //echo "<br><br>6 modeloEmail:emailSociosMultipleGestorPresCoord:Tiempo de ejecución -en microsegundos- redondeado primeros 4 decimales: ".round($tiempo_fin - $tiempo_inicio, 4);	
 
 return $resEmailSociosMultiple;
}
/*----------------- FIN emailSociosMultipleGestorPresCoord() ---------------------------------*/

//=== FIN ENVIAR EMAILS SIN PERSONALIZADOS A SOCIOS DESDE PRESIDENCIA, COORDINACIÓN ============


/*===== INICIO FUNCIONES QUE INSTANCIAN DIRECTAMENTE LA CLASE PHPMAIL ==========================
emailSociosPersonaGestorPresCoord()
enviarEmailPhpMailer(): Se llama desde otras funciones de email.php
enviarMultiplesEmailsPhpMailer(): Se llama desde otras funciones de email.php
===============================================================================================*/

/*--------- Inicio Declarar "namespaces PHPMailer" con "use" ------------------------------------ 
Declarar "namespaces" con "use" es necesario para require_once '../../usuariosLibs/classes/PHPMailer6 
para utilizar new PHPMailer() e instanciar las clases de PHPMailer  

"use" deben estar fuera de las funciones y antes de require_once '../../usuariosLibs/classes/PHPMailer 
ya que la importación se realiza durante la compilación y no durante la ejecución

Necesario para:
require_once '../../usuariosLibs/classes/PHPMailer6/src/Exception.php';
require_once '../../usuariosLibs/classes/PHPMailer6/src/PHPMailer.php';//versión 6:5:4

"requiere" pueden estar dentro o fuera de las funciones de modeloEmail.php, 
yo lo pongo dentro de las funciones que lo necesitan, para no cargarlo si no es necesario
----------------------------------------------------------------------------------------------*/

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;//No lo necesito	
use PHPMailer\PHPMailer\Exception;

/*----------- Fin Declarar "namespaces PHPMailer" con "use -----------------------------------*/

/*=== INICIO ENVIAR EMAILS PERSONALIZADO A SOCIOS POR PRESIDENCIA, COORD, SIMPATIZANTES ========
 Esta es la función que personaliza los emails con nombres de los socios, que pero consume más 
	recursos y es mucho mas lenta que la función de utilizar "emailSociosMultipleGestorPresCoord()"
==============================================================================================*/
/*--------- Inicio emailSociosPersonaGestorPresCoord() ----------------------------------------- 
Para envíar emails a los socios PERSONALIZADOS (con nombre de socio en el body), 
o también un solo email BCC de prueba.

Con los datos array "$datosEnvioEmailSocios" recibidos del controlador correspondiente, 
se crea el array "$datosEnvioEmail", en primer lugar con los datos comunes, incluidos
los archivos adjuntos si los hubiese, y después dentro del bucle foreach se preparan 
los datos del email de cada socio y body,	que incluirá datos personales de cada socio
como "nombre", opcionalmente se podría incluir "TELMOVIL", "CODUSER","CODSOCIO" 
encriptado. Además se incluye la parte común del body recibido.

Dentro del bucle se llama al método "$mail->Send()" para enviar cada email individualmente 
y se capturan los posibles errores en los emails a cada socio que se mostrarán
listados en pantalla con email y nombre de socio y tipo de error.
PHPMailer valida el formato email por ejemplo "zbÑÑasura@eu ropa laica.org" no acepta, 
no es muy estricto, pero no valida dominios por ejemplo admite DNS "orj", que no existe 
(en lugar de "org")
Los email que no generen error SÍ se enviarán.

Mediante acumuladores, se controlan el num. de emails enviados y el num. de errores
y se hace la lista de email y nombre de socio con error.
													
RECIBE: array "$datosEnvioEmailSocios", que contiene todos los datos comunes y los 
individuales (nombre, email) de cada socio, además de los procedentes del formulario
incluido el BCC, (el contenido del array que llega se describe dentro de la función por campos)

LLAMADA: cPresidente.php:enviarEmailSociosPres(),cCoordinador.php:enviarEmailSociosCoord()
y cGestorSimps:enviarEmailSimpsGes() 

LLAMA: clase PHPMailer: '../../usuariosLibs/classes/PHPMailer6/src/Exception.php';
	                       '../../usuariosLibs/classes/PHPMailer6/src/PHPMailer.php';
modeloErrores.php:insertarError()		
modeloEmail.php:emailErrorWMaster()
Y (por ahora no uso) usuariosLibs/encriptar/encriptacionBase64.php:encriptarBase64()

OBSERVACIONES: probado PHP 7.3.21 y PHPMailer6
2023-02-28: Para optimizar el tiempo de ejecución, instancio directamente la clase PHPMailer,
para separar y montar al principio la preparación de la parte común incluido los archivos adjuntos
de la parte del email personalizado para cada socio, y controlando a la vez la detección de errores
Antes se hacía con un bucle de llamadas a la función "enviarEmailPhpMailer()" 

NOTA: esta función al enviarse los emails individualmente dentro de un bucle, 
si fuesen muchos los emails a enviar y con archivos adjuntos grandes requiere muchos recursos
y udiera producir un error de TIMEOUT en el servidor, es aconsejable fraccionar el envío, 
por Agrupaciones, o por CCAA, Provincias, en este caso habría que envíar el email al final 
la agrupación "Estatal e Internacional"
----------------------------------------------------------------------------------------------*/
function emailSociosPersonaGestorPresCoord($datosEnvioEmailSocios)
{ 
 //echo "<br><br>0-1 modeloEmail:emailSociosPersonaGestorPresCoord_SinSleep:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);
	
	//$tiempo_inicio = microtime(true);//fecha Unix actual en microsegundos para pruebas

	$resEmailSociosPersonalizado['nomScript'] = "modeloEmail.php";	
	$resEmailSociosPersonalizado['nomFuncion'] = "emailSociosPersonaGestorPresCoord";
 $resEmailSociosPersonalizado['codError'] = '00000';
	$resEmailSociosPersonalizado['errorMensaje'] = '';																										
																										
 $acumuladores['emailEmailTratados'] = 0;	
	$acumuladores['emailEnviados'] = 0;	
 $acumuladores['emailErrorEnvio'] = 0; 
	$textoTratarError = "";
	
	/*Ejemplo array: $datosEnvioEmailSocios[emailSociosSeleccionados] => Array ([0]=>Array ([CODUSER]=>98 [CODSOCIO]=>98 
	[EMAIL]=>segvilla50@hotmail.com [SEXO]=>H [NOM]=>CARLOS [APE1]=>AVEDA [APE2]=>MARTINEZ [apeNom]=>AVEDA MARTINEZ, CARLOS 
	[TELMOVIL]=>666666666 [TELFIJOCASA] => )
	*/

	//Obtener NOMBRE de FROM a partir del email del formulario de envío:
	
	$posArroba = strpos($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'],'@');
	$nombre = substr($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'], 0,$posArroba);
	$nombreFromMayusculas = mb_strtoupper(substr($datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'],0,$posArroba))." EUROPA LAICA"; 	
	
	// Array con datos COMUNES :
	$datosEnvioEmail = array ("fromAddress"         => $datosEnvioEmailSocios['camposEmail']['FROM']['valorCampo'],//prueba error "fromAddress" =>"segvillaÑÑÑÑ@europalaica.com",
                           "fromAddressName" 				=> $nombreFromMayusculas,																												
																									 	//"replayToAddress"		  	=> "dirección de respuesta distinta dirección de fromAddress, opcional",
																											//"replayToAddressName"	=> "nombre de respuesta distinta dirección de fromAddress, opcional",																								
																									  "subject" 		  								=> $datosEnvioEmailSocios['camposEmail']['subject']['valorCampo']
																									 );																											

	//echo "<br><br>1 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmail: ";print_r($datosEnvioEmail);
		
	require_once '../../usuariosLibs/classes/PHPMailer6/src/Exception.php';
	require_once '../../usuariosLibs/classes/PHPMailer6/src/PHPMailer.php';//versión 6:5:4
	//require_once  '../../usuariosLibs/classes/PHPMailer6/src/SMTP.php';	 //versión 6:5:4: No la necesito			

	$mail = new PHPMailer(true); //defaults to using php "mail()"; true param means it will throw exceptions on errors, which we need to catch			
																														
	try //para datos comunes del email (error messages de PHPMailer) 
	{			
		 $mail->CharSet = "utf-8";//para sustituir el juego de caracteres 'iso-8859-1' por "utf-8"	  

	  $mail->SetLanguage($langcode = 'es', $lang_path = '../../usuariosLibs/classes/PHPMailer6/language/');//para mostrar errores en castellano 			
			
			$mail->IsHTML(true);	//Se pone a true para que envíe y se vea en modo html el body
			
			/*$mail->msgHTML(file_get_contents('./modelos/class/contents.html')); es otra opción para usar un archivo HTML con el mensaje 
					que queremos enviar (Bien en el caso de mensajes fijos)
			*/
					
	  /*------------------- Inicio datos CAMPOS COMUNES email -----------------------------------*/
				
			$mail->SetFrom($datosEnvioEmail['fromAddress'],$datosEnvioEmail['fromAddressName']);//dirección y nombre desde la que se envía
			
			/*--- Inicio poner responder distinto a ['fromAddress'], es opcional -------------------*/
			if (isset($datosEnvioEmail['replayToAddress']) && !empty ($datosEnvioEmail['replayToAddress']) )
			{	
					if (isset($datosEnvioEmail['replayToAddressName'])) 
					{ $mail->addReplyTo($datosEnvioEmail['replayToAddress'], $datosEnvioEmail['replayToAddressName']);				   			
					}
					else
					{ $mail->addReplyTo($datosEnvioEmail['replayToAddress']);				   
					}
			}			
			/*--- Fin poner responder distinto a ['fromAddress'], es opcional ----------------------*/
			
			/*--- Inicio ['toBCC'] Tratar BCC para enviar como copia al final ----------------------*/	
			/*Como los email se envía dentro de un bucle, no lo enviamos como $mail->AddBCC, pues la copia 
			se enviaría repetida tantas veces como número de emails se han enviado.
			Por eso el campo BCC de copia del email, se añadirá como otro email más a enviar al final 
			de los emails 	de los socios elegidos	(como nombre pongo - COPIA DEL EMAIL A SOCIOS/AS -)
			en el array $datosEnvioEmailSocios['emailSociosSeleccionados'][], sin ocultar
		
			$datosEnvioEmailSocios[emailSociosSeleccionados] => Array ( 
			[0]=>Array([CODUSER]=>1085 [CODSOCIO]=>1000 [EMAIL]=>belenblancoXX@hotmail.com [NOM]=>MARÍA BELÉN [APE1]=>BLANCO [APE2]=>GARCÍA [apeNom]=>BLANCO GARCÍA, MARÍA BELÉN [TELMOVIL]=>699999983[TELFIJOCASA]=>914444444) 
			[1]... [n] )			
		 */	

		 /*--- Inicio ['toBCC'], ----------------------------------------------------------------*/		
			if (isset($datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']) && !empty ($datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']) )
			{	
    $emailCopia = array ('EMAIL' => $datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo'], 
				                     'APE1' => "- COPIA OCULTA BCC DEL EMAIL A SOCIOS/AS  -", 'APE2' => "", 'NOM' => "", 'SEXO' => ""		);  			
		  //$emailCopia = array('EMAIL'=>"segvillaÑÑÑÑ%%@europalaica.com",'APE1'=>"-MAL COPIA OCULTA BCC DEL EMAIL A SOCIOS/AS -",'APE2'=>"",'NOM'=>"",'SEXO'=>"");//prueba error  			

    $datosEnvioEmailSocios['emailSociosSeleccionados'][] = $emailCopia;//se añade al final del array				
			}				
			/*--- Fin ['toBCC'] --------------------------------------------------------------------*/		

			/*--- Inicio ['toCC'], En realidad todos serán BCC, al enviar en un bucle BCC ----------*/		
			if (isset($datosEnvioEmailSocios['camposEmail']['CC']['valorCampo']) && !empty ($datosEnvioEmailSocios['camposEmail']['CC']['valorCampo']) )
			{	
    $emailCopia = array ('EMAIL' => $datosEnvioEmailSocios['camposEmail']['CC']['valorCampo'], 
				                     'APE1' => "- COPIA NO OCULTA CC DEL EMAIL A SOCIOS/AS  -", 'APE2' => "", 'NOM' => "", 'SEXO' => "" );  			
    $datosEnvioEmailSocios['emailSociosSeleccionados'][] = $emailCopia;//se añade al final del array								
			}
   /*--- Fin  ['toCC'], -------------------------------------------------------------------*/				
   //echo "<br><br>2 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmailSocios['emailSociosSeleccionados']: ";print_r($datosEnvioEmailSocios['emailSociosSeleccionados']);		
					
			
	  /*--- Inicio ['AddAttachment'] ARCHIVOS ADJUNTOS ---------------------------------------*/		

	  /*Nota: Es opcional enviar archivos, los emails pueden no tener archivos adjuntos
   Los datos vendrán en el array y ya controlados los tamañanos y tipos permitidos, ejemplo:
	  $datosEnvioEmailSocios['AddAttachment'] => Array ([codError] => 00000 [errorMensaje] => 
		 [0]=>Array([name]=>Surface_Applicaciones_2020_05_29.odt [type]=>application/vnd.oasis.opendocument.text [tmp_name]=>/tmp/phpSjbySf [error]=>0 [size]=>10224 [codError]=>00000 ) 
		 [1] ...)
	  */		
  	//echo "<br><br>3 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmailSocios['AddAttachment']: ";print_r($datosEnvioEmailSocios['AddAttachment']);
			
			if (isset($datosEnvioEmailSocios['AddAttachment'])	&& !empty($datosEnvioEmailSocios['AddAttachment']))//adaptación de valores recibidos	
			{  
					if (isset($datosEnvioEmailSocios['AddAttachment']['codError']))
					{	unset($datosEnvioEmailSocios['AddAttachment']['codError']);}//para evitar error en foreach	
					if (isset($datosEnvioEmailSocios['AddAttachment']['errorMensaje']))
					{	unset($datosEnvioEmailSocios['AddAttachment']['errorMensaje']);}//para evitar error en foreach	
				
					foreach ($datosEnvioEmailSocios['AddAttachment'] as $f => $contenidoF)
					{ 							
							$mail->AddAttachment($contenidoF['tmp_name'],$contenidoF['name']);							
					}				
			}		
			/*--- Fin ['AddAttachment'] ARCHIVOS ADJUNTOS ------------------------------------------*/	
			
			
			$mail->Subject = $datosEnvioEmail['subject'];//"$mail->Subject": Se podría poner en la PARTE VARIABLE si se quiere personalizar con nombre socio u otros datos			
			
			/*------------------- Fin datos CAMPOS COMUNES email email --------------------------------*/			
			
			
			/*--- Inicio PARTE VARIABLE: bucle que preparar y enviar emails UNO a UNO cada socio ------*/	
			
			//echo "<br><br>4-0 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmailSocios['emailSociosSeleccionados']: ";print_r($datosEnvioEmailSocios['emailSociosSeleccionados']);
			
		 //foreach ($datosEnvioEmailSocios['emailSociosSeleccionados']  as  $fila => $contenidoFila)
			foreach ($datosEnvioEmailSocios['emailSociosSeleccionados'] as $contenidoFila)
			{			
				try //interior bucle 
			 {	
 				//Al ser un envío de email uno a uno a cada socio, no se ocultará email y nombre del socio, sólo lo podrá ver la persona a quien se envía
					$datosEnvioEmail['toAddress'] = $contenidoFila['EMAIL'];			
					$datosEnvioEmail['toAddressName'] = mb_strtoupper($contenidoFila['APE1']." ".$contenidoFila['APE2'].", ".$contenidoFila['NOM']);//prodría ser = $contenidoFila['apeNom'];				
     
     //echo "<br><br>4-1 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmail: ";print_r($datosEnvioEmail);

					if(isset($datosEnvioEmail['toAddress']) && !empty($datosEnvioEmail['toAddress']))
					{	
							if (isset($datosEnvioEmail['toAddressName'])) 
							{ $mail->AddAddress($datosEnvioEmail['toAddress'],$datosEnvioEmail['toAddressName']);//en caso de error lo captura catch	bucle interior, y seguirá con el envío al siguiente socio en $datosEnvioEmail['toAddress']	
							}
							else
							{ $mail->AddAddress($datosEnvioEmail['toAddress']);//en caso de error lo captura catch	bucle interior, y seguirá con el envío al siguiente socio en $datosEnvioEmail['toAddress']	
							}			
					}
     				
				 /*Nota: en ocasiones podría ser útil incluir un enlace a un formulario en la aplicación 
													o para rellenar campos, u otras situaciones, por seguridad debiera ir encriptado:
						require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
						$codUserEncriptado = encriptarBase64($contenidoFila['CODUSER']); o $codSocioEncriptado = encriptarBase64($contenidoFila['CODSOCIO']);	
						$body1 = Para pagar ahora con PayPal, haz clic en el siguiente enlace: ";		
						$body2="	https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&amp;accion=pagarCuotaSocioSinCC&amp;parametro=".$codUserEncriptado.			
						$datosEnvioEmail["body"] = $body1."\nEnlace encriptado: ".$body2."\n\n".$body3.$body4;	
					*/
					
					/*--- Inicio teléfono, no se incluye normalmente (para incluir descomentar) -------*/
					$telMovil = '';
					$telFijo  = '';
					
					/*	if (isset($contenidoFila['TELMOVIL']) && !empty($contenidoFila['TELMOVIL']))
					{ $telMovil = "&nbsp;&nbsp;&nbsp;Teléfono móvil: ".$contenidoFila['TELMOVIL'];}
					else
					{ $telMovil = "&nbsp;&nbsp;&nbsp;Teléfono móvil: desconocido";	} 				
					
					/*	if (isset($contenidoFila['TELFIJOCASA']) && !empty($contenidoFila['TELFIJOCASA']))
					{ $telFijo = "&nbsp;&nbsp;&nbsp;Teléfono fijo: ".$contenidoFila['TELFIJOCASA'];}
					else
					{ $telFijo = "&nbsp;&nbsp;&nbsp;Teléfono móvil: desconocido";	} 				
     				[TELFIJOCASA]
					*/		
					/*--- Fin teléfono, no se incluirá normalmente -------------------------------------*/
					
					/*--- Inicio dedicatoria personalizada ---------------------------------------------*/
										
					if (isset($contenidoFila['SEXO']) )
					{ 
							if ($contenidoFila['SEXO'] == 'H')
							{ $dedicatoria = 'Estimado socio ';}
							elseif ($contenidoFila['SEXO'] == 'M')
							{ $dedicatoria = 'Estimada socia ';}
							else // = otros por ahora no está incluida esta opción en la BBDD
							{ $dedicatoria = '';}
					}	
					/*--- Fin dedicatoria personalizada ------------------------------------------------*/		     
     					
					/*-------------- Inicio para el body en formato en HTML ----------------------------*/	
					$datosEnvioEmail['body'] = $dedicatoria."<strong>".$datosEnvioEmail['toAddressName']."</strong>".$telMovil.$telFijo."<br /><br />".
																																$datosEnvioEmailSocios['camposEmail']['body']['valorCampo'].$datosEnvioEmailSocios['camposEmail']['pieProteccionDatos']['valorCampo'];

					//echo "<br><br>4-2 modeloEmail:emailSociosPersonaGestorPresCoord:datosEnvioEmail: ";print_r($datosEnvioEmail);
					
					$mail->Body = nl2br($datosEnvioEmail['body']);//Para mostar en HTML, PHP convierte \n y saltos de línea <br> que puede haber en texto del input	textarea. 		
					
					/*-------------- Fin para el body para el body en formato en HTML -------------------*/
					
					/*--- Inicio body enviar como texto plano -------------------------------------------*/
					/*Si el cliente de correo solo admite texto plano lo siguiente lo pone en texto plano*/
					
					$textoAltBody = strip_tags($datosEnvioEmail['body']);//Para eliminar todas las posibles etiquetas tags de html, antes de enviar como texto plano,
					$mail->AltBody = $textoAltBody;		
					
					/*--- Fin body enviar como texto plano  ---------------------------------------------*/						

					//-- O J O: QUITAR BARRAS COMENTAR PARA QUE ENVÍE DE VERDAD LOS EMAILS, (comentar para probar: que no se envíen) ---										
								
					$mail->Send();//Para pruebas: se comentará esta línea para cuando no queremos enviar email		
					
     $acumuladores['emailEnviados']++ ;//sin errores detectados y enviados		
				
				}// Fin try interior	bucle					

				catch (Exception $e) //para interior	bucle	capturar error messages de PHPMailer, incluye error formato o email vacío
				{		
      $resEmailSociosPersonalizado['codError'] =	'81000';	
					 $resEmailSociosPersonalizado['errorMensaje'] .= "<br />- <strong>".$datosEnvioEmail["toAddressName"]."</strong>, (".$datosEnvioEmail["toAddress"]."): ".
																																																							$e->errorMessage();		
				
				  //Nota: envíar un email con valor campo email vacio es capturado con catch, lo indica en el mensaje de error que falta dirección de email de destino

						$textoTratarError = "<strong>NO SE HA PODIDO ENVIAR EL EMAIL A LAS PERSONAS QUE SE MUESTRAN A CONTINUACIÓN</strong>
																										<br /><br />Anota estos datos y después busca a estos socios/as para corregir su email, o si no en el campo 
																										<i>*Error correo electrónico</i>, elige: <strong>ERROR-FORMATO</strong>
																										<br /><br />Cuando conozcas su email correcto, para evitar envíos repetidos podrías enviarles individualmente
																										el contenido del anterior email desde el correo de -". $datosEnvioEmail['fromAddress'];																												

						$acumuladores['emailErrorEnvio']++ ;//con errores detectados y no enviados												

				  //echo "<br><br>4-3 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado); 										
				}// End	catch para interior bucle(Exception $e)						
				
				//echo "<br><br>4-4 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado); 

				$acumuladores['emailEmailTratados']++ ;				
				
				$mail->clearAddresses();	//Clear "AddAddress" for the next iteration			
					
			}//foreach ($datosEnvioEmailSocios['emailSociosSeleccionados']//se enviarán los que se puedan envíar y de los otros mostrará el email que produjo el error
			
		 /*--- Fin parte variable: bucle que preparar y envia emails uno a uno a cada socio -----*/	
			//echo "<br><br>5 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado);
   
	}//Fin try para datos comunes del email (error messages de PHPMailer)	
	
	catch (Exception $e) //Para error en datos en la partes comunes del email: error messages de PHPMailer 
	{	
		$resEmailSociosPersonalizado['codError'] = '81000';// antes $reEnvioEmail['codError'] = '81000';		
		$resEmailSociosPersonalizado['errorMensaje'] .= "<br />" .$e->errorMessage();	

		$textoTratarError = "<strong>NO SE HA PODIDO ENVIAR NINGÚN EMAIL</strong> 
		                    <br /><br />Error en datos comunes: prueba de nuevo pasado un rato, y si sigue el error lo anotas y se lo comunicas a - adminusers@europalaica.org - para resolver el problema";																					
		
		$acumuladores['emailErrorEnvio']++ ;//con errores detectados y no enviados		  

  //echo "<br><br>6 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado); 
	}	//fin catch (Exception $e) para error en datos comunes del email: error messages de PHPMailer 								
	
	//echo "<br><br>7-1 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado); 
	
	/*-- Inicio tratamiento de errores y resultados totales  ------------------------------------*/
 if ( $resEmailSociosPersonalizado['codError'] !== '00000')	
 {
   require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($resEmailSociosPersonalizado);				
			$resEmailErrorWMaster = emailErrorWMaster($resEmailSociosPersonalizado['nomScript'].":".$resEmailSociosPersonalizado['nomFuncion'].
			                                          $resEmailSociosPersonalizado['textoComentarios'].': '.$resEmailSociosPersonalizado['errorMensaje'].': '.
				                                         $resEmailSociosPersonalizado['codError']);//en modeloEmail.php';   																																													
	}
	else //$resEmailSociosPersonalizado['codError'] == '00000'//Ningún error detectado
	{			
		 $textoTratarError = "No se han detectado errores en el envío de los emails, pero puede suceder que algunos no los reciban
                     			por causas diversas: filtros antiespan, buzones llenos, cuentas caducadas, etc.";
	}		

 $resEmailSociosPersonalizado['textoComentarios'] = "<strong>Emails enviados incluido copia BCC: ".$acumuladores['emailEnviados']."</strong>".	
	                                                   "<br /><br />Total de emails socios/as tratados incluido copia BCC: <strong>".$acumuladores['emailEmailTratados']."</strong>".																																																																																																							 
																																																				"<br /><br />Emails no enviados por errores detectados: <strong>".$acumuladores['emailErrorEnvio']."</strong>".
																																																				"<br /><br /><br />".$textoTratarError."<br /><br />". $resEmailSociosPersonalizado['errorMensaje'];	
																																																				
	/*------ Fin tratamiento de errores y resultados totales ------------------------------------*/																																																																							

	//echo "<br><br>7-2 modeloEmail:emailSociosPersonaGestorPresCoord:resEmailSociosPersonalizado: ";print_r($resEmailSociosPersonalizado); 
	
 //$tiempo_fin = microtime(true);//para pruebas tiempo empleado

 //echo "<br><br>8-1 modeloEmail:emailSociosPersonaGestorPresCoord: Tiempo ejecución: " . ($tiempo_fin - $tiempo_inicio);
 //echo "<br><br>8-2 modeloEmail:emailSociosPersonaGestorPresCoord: Tiempo de ejecución -en microsegundos- redondeado primeros 4 decimales: ".round($tiempo_fin - $tiempo_inicio, 4);	
 
 return $resEmailSociosPersonalizado;
}
/*----------------- FIN emailSociosPersonaGestorPresCoord ------------------------------------*/

//=== FIN ENVIAR EMAILS PERSONALIZADOS A SOCIOS DESDE PRESIDENCIA, COODINACIÓN =================

/*---------------- Inicio enviarEmailPhpMailer --------------------------------------------------
DESCRIPCIÓN:
Envía un email (solo uno y si recibe también envía un CC y un BCC si recibe esos datos) 
Admite 0, 1 o VARIOS archivos adjuntos (antes habrán sido validados tamaños y tipos )

Por defecto envía los emails para que al recibirlos el body se vea como HTML, pero si el 
cliente de correo solo admite texto plano, pone el body en texto plano. 
 
LLAMADA: desde varias funciones de modeloEmail, a su vez llamadas desde diversos
controladores: controladorSocios: altaSocio(), cPresidencia, cCoordinacion, cTesorería 
cPie: contactarEmail(), ... ,

LLAMA: varios metodos de la clase 'usuariosLibs/classes/phpMailer/class.phpmailer.php'
new PHPMailer(true), ...

OBSERVACIONES: Probado PHP 7.3.33 (no necesita PDO aquí)
-----------------------------------------------------------------------------------------------*/
function enviarEmailPhpMailer($datosEnvioEmail)
{ 
 	//echo "<br><br>0-1 modeloEmail:enviarEmailPhpMailer:datosEnvioEmail: ";print_r($datosEnvioEmail); 	
  
		//Mejor "requiere_once", para evitar repetidas  llamadas a clase Exception ya instanciada 
		//que puede generar mensajes de error (pueden estar dentro o fuera de las funciones)

		require_once '../../usuariosLibs/classes/PHPMailer6/src/Exception.php';
		require_once '../../usuariosLibs/classes/PHPMailer6/src/PHPMailer.php';//versión 6:5:4
		//require_once  '../../usuariosLibs/classes/PHPMailer6/src/SMTP.php';	//versión 6:5:4: No la necesito	

 	$mail = new PHPMailer(true); //defaults to using php "mail()"; 
	                              //the true param means it will throw exceptions on errors, which we need to catch																														
		try 
		{			
			$reEnvioEmail['codError'] = '00000';
			
		 $mail->CharSet = "utf-8";//para sustituir el juego de caracteres 'iso-8859-1' por "utf-8"	  

	  $mail->SetLanguage($langcode = 'es', $lang_path = '../../usuariosLibs/classes/PHPMailer6/language/');//para errores en castellano 			
			
			/*-------------- Inicio para que se vea como html el body y subject -----------------*/

			$mail->IsHTML(true);	//Se pone a true para que envíe en modo HTML
			
			/*$mail->msgHTML(file_get_contents('./modelos/class/contents.html')); es otra opción para usar un archivo HTML con el mensaje 
					que queremos enviar (Bien en el caso de mensajes fijos)
					PHPMailer provides an option to read an HTML "message body" from an external file, "pero dentro del servidor"
					convert referenced images to embedded as well as convert HTML into a basic plain-text alternative body. 
					This way, you will not overload your message sending code with HTML and will be able to update your HTML template independently. 
					To include a separate HTML file, add these attributes: $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
			*/
			//nl2br()	de PHP convierte \n y saltos de línea que hay en texto que viene del input	textarea en <br> para el formato HTML. 	
			
			$mail->Body = nl2br($datosEnvioEmail['body']);
			
			$mail->Subject = $datosEnvioEmail['subject'];	
   
			/*-------------- Fin para que se vea como html el body y subject --------------------*/
			
			/*--- Inicio body enviar como texto plano -------------------------------------------*/
			/*Si el cliente de correo solo admitE texto plano lo siguiente lo pone en texto plano*/
			
			$textoAltBody = strip_tags($datosEnvioEmail['body']);//Para eliminar todas las posibles etiquetas tags de html, antes de enviar como texto plano,
			$mail->AltBody = $textoAltBody;		
			
   /*--- Fin body enviar como texto plano  ----------------------------------------------*/	

	  $mail->SetFrom($datosEnvioEmail['fromAddress'],$datosEnvioEmail['fromAddressName']);//dirección desde la que se envía 

			/*--- Inicio Alternativa a	respuesta a ['fromAddress'], es opcional ------------------*/
			if (isset($datosEnvioEmail['replayToAddress']) && !empty ($datosEnvioEmail['replayToAddress']) )
			{	
					if (isset($datosEnvioEmail['replayToAddressName'])) 
					{ $mail->addReplyTo($datosEnvioEmail['replayToAddress'], $datosEnvioEmail['replayToAddressName']);					
					}
					else
					{ $mail->addReplyTo($datosEnvioEmail['replayToAddress']);
					}
			}
		 /*--- Fin Alternativa a	respuesta a ['fromAddress'], es opcional ----------------------*/

		 /*--- Inicio ['toCC'], es opcional ----------------------------------------------------*/		
			if (isset($datosEnvioEmail['toCC']) && !empty ($datosEnvioEmail['toCC']) )
			{	
					if (isset($datosEnvioEmail['toCCName'])) 
					{ $mail->AddBCC($datosEnvioEmail['toCC'], $datosEnvioEmail['toCCName']);
					}
					else
					{ $mail->AddBCC($datosEnvioEmail['toCC']);
					}				
			}
		 /*--- Fin  ['toCC'], es opcional -------------------------------------------------------*/									

			/*--- Inicio ['toBCC'], es opcional ----------------------------------------------------*/		
			if (isset($datosEnvioEmail['toBCC']) && !empty ($datosEnvioEmail['toBCC']) )
			{	
					if (isset($datosEnvioEmail['toBCCName'])) 
					{ $mail->AddBCC($datosEnvioEmail['toBCC'], $datosEnvioEmail['toBCCName']);
					}
					else
					{ $mail->AddBCC($datosEnvioEmail['toBCC']);
					}				
			}
		 /*--- Fin  ['toBCC'], es opcional ------------------------------------------------------*/				

			if (isset($datosEnvioEmail['toAddress']) && !empty($datosEnvioEmail['toAddress']))//toAddress
  	{	
			  if (isset($datosEnvioEmail['toAddressName'])) 
					{ $mail->AddAddress($datosEnvioEmail['toAddress'],$datosEnvioEmail['toAddressName']);
					}
					else
					{ $mail->AddAddress($datosEnvioEmail['toAddress']);
					}			
			}			

			/*-----	INICIO $mail->AddAttachment() ---------------------------------------------------*/ 
   /*$datosEnvioEmail['AddAttachment'][0]['pathFile'] ="../documentos/Ayuda_Correo_Web_Nodo50_2013_09_25.pdf";//si
			$datosEnvioEmail['AddAttachment'][0]['nameSend'] ="Ayuda_Correo_Web.pdf"; //si
			$datosEnvioEmail['AddAttachment'][0]['type'] ="application/pdf";//no se usa en nuestro caso			
			$datosEnvioEmail['AddAttachment'][1]['pathFile'] ="../../tmp/agrupacionesTrabajo_2012_04_17.txt";
			$datosEnvioEmail['AddAttachment'][1]['nameSend'] ="agrupacionesTrabajo.txt";
			$datosEnvioEmail['AddAttachment'][1]['type']  ="text/plain";........			
			*/	
			//echo "<br><br>2 modeloEmail:enviarEmailPhpMailer:datosEnvioEmail['AddAttachment']: ";print_r($datosEnvioEmail['AddAttachment']);			
			
			if (isset($datosEnvioEmail['AddAttachment'])	&& !empty($datosEnvioEmail['AddAttachment']))
			{					
					foreach ($datosEnvioEmail['AddAttachment'] as $f => $contenidoF)
					{ 	
							$mail->AddAttachment($contenidoF['pathFile'],$contenidoF['nameSend']);
					}
			}	
			/*------------------	FIN $mail->AddAttachment --------------------------------------------*/
			
   /*---- Con este método se hace el envío --------------------------------------------------*/	
			
			$mail->Send();//Para pruebas: se comentará esta línea para cuando no queremos enviar email				
		}// end try	
  
		/*---- Inicio capturar errores -----------------------------------------------------------*/		

		catch (Exception $e) //error messages de PHPMailer 
		{							
			$reEnvioEmail['codError'] = '81000';
			
			$reEnvioEmail['errorMensaje'] = "Error email: ".$e->errorMessage();	
						
			//$reEnvioEmail['errorMensaje'] ="No se ha enviado el email a: ".$datosEnvioEmail['toAddress'].','.$datosEnvioEmail['toBCC'].','.$datosEnvioEmail['toCCName']. 
			//																																			" Error: ".$e->errorMessage();	
			//	$reEnvioEmail['textoComentarios']="No se ha enviado el email a: ".$datosEnvioEmail['toAddress'].','.$datosEnvioEmail['toBCC'].','.$datosEnvioEmail['toCCName']. 
			//																																			" Error: ".$e->errorMessage();			
		}
			
		/*---- Fin capturar errores --------------------------------------------------------------*/

		//Clear all addresses and attachments for the next iteration
		$mail->clearAddresses();
		$mail->clearAttachments();			

		//echo "<br><br>3 modeloEmail:enviarEmailPhpMailer:reEnvioEmail: ";print_r($reEnvioEmail); 

		return $reEnvioEmail; 
}
/*---------------- Fin enviarEmailPhpMailer ---------------------------------------------------*/

/*---------------- Inicio enviarMultiplesEmailsPhpMailer ----------------------------------------
DESCRIPCIÓN: Utiliza la clase phpMailer
Envía emails a multiples destinatarios, con "subject,body,fromAddress,replayToAddress,y 
"AddAttachment" comunes, y "replayToAddress" y "AddAttachment, son opcionales.
Dentro del bucle foreach se añaden los email para "AddAddress","AddBCC","AddCC", que vengan 
dentro de array "$datosEnvioEmail".

Envía  en modo HTML, y en texto plano si el correo cliente no soporta HTML.
Captura los errores y se envía a la función de llamada. A la primera dirección errónea 
que detecta, ya sale sin seguir con otras direcciones. 

No envía emails con contenidos personalizados ya que 'subject', 'body', y 'AddAttachment'
son comunes a todos los emails	

RECIBE: array "$datosEnvioEmail":
                 //---- Comunes ---------------------------------------
															  $datosEnvioEmail['fromAddress']
									        $datosEnvioEmail['fromAddressName']
																	$datosEnvioEmail['subject']
									        $datosEnvioEmail['body']																	
																	//---- Opcionales  -------------------------------------
															  $datosEnvioEmail['replayToAddress']
									        $datosEnvioEmail['replayToAddressName']
                 //----Archivos ----------------------------------------
 																$datosEnvioEmail['AddAttachment'][0]['pathFile']
																	$datosEnvioEmail['AddAttachment'][0]['nameSend']	
																	//--- Para dentro bucle foreach ------------------------
																	Pueden venir emails solo de campos "AddBCC" (lo normal y aconsejable)
																	o de cualquiera o mezcla de  "AddAddress","AddBCC","AddCC":																	
                 $datosEnvioEmail['AddAddress'][0]['email'] 
									        $datosEnvioEmail['AddAddress'][0]['nombre']		
																	$datosEnvioEmail['AddAddress'][1]['email'] 																	
                 $datosEnvioEmail['AddAddress'][1]['nombre'] 
									        $datosEnvioEmail['AddCC'][0]['nombre']		
																	$datosEnvioEmail['AddCC'][1]['email'] 
									        $datosEnvioEmail['AddCC'][1]['nombre']		
									        $datosEnvioEmail['AddBCC'][0]['email'] 
									        $datosEnvioEmail['AddBCC'][0]['nombre']
																	.......................................
LLAMADA: desde varias funciones modeloEmail.php: emailSociosMultipleGestorPresCoord(), 
previo: cPresidente.php:enviarEmailSociosPres(), y cCoordinador.php:enviarEmailSociosCoord()	
y otras relaccionadas con altas bajas, ...	

LLAMA: varios metodos de la clase 'usuariosLibs/classes/phpMailer/class.phpmailer.php'
new PHPMailer(true), ...															

OBSERVACIONES: Probado PHP 7.3.21
-----------------------------------------------------------------------------------------------*/
function enviarMultiplesEmailsPhpMailer($datosEnvioEmail)
{
	//echo "<br><br>0-1 modeloEmail:enviarMultiplesEmailsPhpMailer:datosEnvioEmail: ";print_r($datosEnvioEmail); 

	require_once '../../usuariosLibs/classes/PHPMailer6/src/Exception.php';
	require_once '../../usuariosLibs/classes/PHPMailer6/src/PHPMailer.php';
	//require_once '../../usuariosLibs/classes/PHPMailer6/src/SMTP.php';//no se usa		

	$mail = new PHPMailer(true);//the true param means it will throw exceptions on errors, which we need to catch
	
	try 
	{
		$reEnvioEmail['codError'] = '00000';
	
	 $mail->CharSet="utf-8";//para sustituir el juego de caracteres 'iso-8859-1' por "utf-8"
		
	 $mail->SetLanguage($langcode = 'es', $lang_path = '../../usuariosLibs/classes/PHPMailer6/language/');//para errores en castellano 			
		
 	/*-------------- Inicio para que se vea como html el body y subject -----------------*/

		$mail->IsHTML(true);	//Se pone a true para que envíe en modo HTML
		
		/*$mail->msgHTML(file_get_contents('./modelos/class/contents.html')); es otra opción para usar un archivo HTML con el mensaje 
				que queremos enviar (Bien en el caso de mensajes fijos)
				PHPMailer provides an option to read an HTML "message body" from an external file, "pero dentro del servidor"
				convert referenced images to embedded as well as convert HTML into a basic plain-text alternative body. 
				This way, you will not overload your message sending code with HTML and will be able to update your HTML template independently. 
				To include a separate HTML file, add these attributes: $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
		*/
		//nl2br()	de PHP convierte \n y saltos de línea que hay en texto que viene del input	textarea en <br> para el formato HTML. 	
		
		$mail->Body = nl2br($datosEnvioEmail['body']);
		
		$mail->Subject = $datosEnvioEmail['subject'];	
		
		/*-------------- Fin para que se vea como html el body y subject --------------------*/
		
		/*--- Inicio body enviar como texto plano -------------------------------------------*/
		/*Si el cliente de correo solo admita texto plano lo siguiente lo pone en texto plano*/
		
		$textoAltBody = strip_tags($datosEnvioEmail['body']);//Para eliminar todas las posibles etiquetas tags de html, antes de enviar como texto plano,
		$mail->AltBody = $textoAltBody;		
		
		unset($datosEnvioEmail['subject']);unset($datosEnvioEmail['body']);//para que no de problemas en el foreach de más adelante	
		
		/*--- Fin body enviar como texto plano  ----------------------------------------------*/				
	
		$mail->SetFrom($datosEnvioEmail['fromAddress'],$datosEnvioEmail['fromAddressName']);//dirección desde la que se envía obligatorio
	 unset($datosEnvioEmail['fromAddress']);	unset($datosEnvioEmail['fromAddressName']);//para evitar problemas en el foreach de más adelante	

		/*--- Inicio Alternativa a	respuesta a ['fromAddress'], es opcional ------------------*/
	 if (isset($datosEnvioEmail['replayToAddress']) && !empty ($datosEnvioEmail['replayToAddress']) )
		{	
    if (isset($datosEnvioEmail['replayToAddressName'])) 
		  { $mail->addReplyTo($datosEnvioEmail['replayToAddress'], $datosEnvioEmail['replayToAddressName']);
			   unset($datosEnvioEmail['replayToAddressName']);
			 }
			 else
				{ $mail->addReplyTo($datosEnvioEmail['replayToAddress']);
			 }	
		  unset($datosEnvioEmail['replayToAddress']);//para que no de problemas en el foreach de más adelante	
		}
		/*--- Fin Alternativa a	respuesta a ['fromAddress'], es opcional -----------------------*/

 	/*--- Inicio archivos adjuntos (Los mismos se enviarán a todos --------------------------
		 'pathFile': directorio y nombre del archivo (pudiera se el archivo temporal), 
		 'nameSend': nombre y del archivo que llegará al cliente 
			---------------------------------------------------------------------------------------*/
		//echo "<br><br>1 modeloEmail:enviarMultiplesEmailsPhpMailer:datosEnvioEmail['AddAttachment']: ";print_r($datosEnvioEmail['AddAttachment']);			
		
		if (isset($datosEnvioEmail["AddAttachment"]) && !empty ($datosEnvioEmail["AddAttachment"]) ) 
		{foreach ($datosEnvioEmail["AddAttachment"] as $campoGrupoAtt => $contenidoAttachment)
			{
					if (isset($contenidoAttachment['pathFile']) && !empty ($contenidoAttachment['pathFile']) && 
									isset($contenidoAttachment['nameSend']) && !empty ($contenidoAttachment['nameSend'])	)
					{ 
							$mail->AddAttachment($contenidoAttachment['pathFile'],$contenidoAttachment['nameSend']);			
					}	
			}	 	
			unset($datosEnvioEmail["AddAttachment"]);//evitará notice o recorrido en siguiente bucle
		}//	if (isset($datosEnvioEmail["AddAttachment"])... )
			
		/*--- Fin archivos adjuntos (Los mismos para todos) -------------------------------------*/
	
	 /*---- Incio bucle foreach para preparar los emails de destino ----------------------------
   El "$campoGrupoEmail" tendrá los valores de con los que venga "$datosEnvioEmail" para ser enviados
			como "AddAddress","AddBCC","AddCC" o mezcla de ellos, para los email de los socios debieran ser 
			como "AddBCC" (ocultos)
 	------------------------------------------------------------------------------------------*/
		foreach ($datosEnvioEmail as $campoGrupoEmail => $contenidoGrupoEmail)//$campoGrupoEmail será ="AddAddress","AddBCC","AddCC"
		{//echo "<br><br>2-0 modeloEmail:enviarMultiplesEmailsPhpMailer:campoGrupoEmail: ";print_r($campoGrupoEmail);
			
			foreach ($contenidoGrupoEmail as $campoEmail => $contenidoCampoEmail)
			{ //echo "<br><br>2-1 modeloEmail:enviarMultiplesEmailsPhpMailer:campoEmail: ";print_r($campoEmail);		
				 
 				if (isset($contenidoCampoEmail['email']) && !empty ($contenidoCampoEmail['email']))								
				 { 
				   //echo "<br><br>2-2 modeloEmail:enviarMultiplesEmailsPhpMailer:contenidoCampoEmail['email']: ";print_r($contenidoCampoEmail['email']);									
							
							/*Según los valores que vengan con $campoGrupoEmail se irán llamando al los métodos correspondientes, 
							  y pueden venir las tres opciones o al menos una:
							  $mail->AddAddress($contenidoCampoEmail['email'],$contenidoCampoEmail['nombre']);
							  $mail->AddCC($contenidoCampoEmail['email'],$contenidoCampoEmail['nombre']);
							  $mail->AddBCC($contenidoCampoEmail['email'],$contenidoCampoEmail['nombre']);
							*/
														
							if (isset($contenidoCampoEmail['nombre']))
							{ $mail->$campoGrupoEmail($contenidoCampoEmail['email'],$contenidoCampoEmail['nombre']);//$mail->AddBCC($contenidoCampoEmail['email'],...);	
						 }
							else
							{ $mail->$campoGrupoEmail($contenidoCampoEmail['email']);		
						 }		 
					}
			}
		}//foreach ($datosEnvioEmail as $campoGrupoEmail => $contenidoGrupoEmail)
		
		/*---- Con este método se hace el envío --------------------------------------------------*/	
		
  $mail->Send();//ejecuta el método de enviar:¡¡¡ comentado no se enviaría nada !!!		
	}		

	/*---- Inicio capturar errores -----------------------------------------------------------*/

	catch (Exception $e) //error messages no de PHPMailer 
	{ 		
	  $reEnvioEmail['codError'] ='81000';
			$reEnvioEmail['errorMensaje'] = $e->errorMessage();
			$reEnvioEmail['textoComentarios'] ="Has salido sin enviar los emails<br /><br />Se ha detectado al menos un error: ".$e->errorMessage();	
	}
	//echo "<br><br>3 modeloEmail:enviarEmailPhpMailer:reEnvioEmail: ";print_r($reEnvioEmail); 
	
	/*---- Fin capturar errores --------------------------------------------------------------*/				
	
	//Clear all addresses and attachments for the next iteration
	
	$mail->clearAddresses();
	$mail->clearAttachments();
	
 //echo "<br><br>4 modeloEmail:enviarEmailPhpMailer:reEnvioEmail: ";print_r($reEnvioEmail); 
	
	return $reEnvioEmail; 
}
/*----------------------- Fin enviarMultiplesEmailsPhpMailer ----------------------------------*/

//===== FIN FUNCIONES INSTANCIAN DIRECTAMENTE DE PARA CLASE PHPMAIL =============================



//===== INICIO FUNCIONES COMUNES CON FUNCIÓN con mail() DE PHP ==================================

// Son llamadas desde algunas funciones de controladores o modelos, 
// Nota:  se podrían sustituir por clase PhpMailer 

/*----------- Inicio enviarEmail emailLogin (Sin phpMailer) -------------------------------------
Función antigua de enviarEmail solo la utiliza para uso interno en emailErrorWMaster($contenido)
Ha sido sustituida por la clase PhpMailer, pero la dejo por si interesase en algún caso
De momento, hasta que la sustituya solo la utiliza emailErrorWMaster($contenido)
-----------------------------------------------------------------------------------------------*/
function enviarEmail($datosEnvioEmail) //devuelve 00000 si es correcto y 83000 si es erróneo
{ 
	$resultEnviarEmail['codError']='00000';
	$resultEnviarEmail['errorMensaje']='Se ha enviado el correo electrónico';

	$headers = "MIME-Version: 1.0\r\n"; //Para info del MIME -> http://es.wikipedia.org/wiki/Multipurpose_Internet_Mail_Extensions
	$headers .= "Content-Type:text/html; charset=utf-8". "\r\n";
	$headers .= "From: ".$datosEnvioEmail['from']. "\r\n";
	$headers .= "Reply-to: ".$datosEnvioEmail['replayTo']. "\r\n";
	
	if(!mail($datosEnvioEmail['emailUsuario'],$datosEnvioEmail['asunto'],$datosEnvioEmail['contenido'],$headers)) 
	{
		$resultEnviarEmail['codError'] = '81000';//lo pondré como error logico, mail solo devuelve boolean
		$resultEnviarEmail['errorMensaje'] = 'No se ha podido enviar el email';
	}
	return $resultEnviarEmail; //si el email fue otherwise
}
//--------------------------- Fin enviarEmail ---------------------------------------------------

/*---------------- Inicio emailErrorWMaster------------------------------------------------------ 
Sin phpMailer
LLamada cuando se produce un error del sistema <80000 (no puede conectarse o 
no puede acceder a una tabla para leer o insertar)
Se envía un email adminusers@europalaica.org
--------------------------------------------------------------------------------------*/
function emailErrorWMaster($contenido)
{ //echo emailErrorWMaster:asunto;print_r($contenido);
	//$asunto = "EuropaLaica. Error en la aplicación";
	$asunto = "EuropaLaica. Error en la aplicación servidor: ".$_SERVER['SCRIPT_NAME'];//ver en qué servidor usuarios, copia, ...
	//$from = "EuropaLaica <europalaica.com@europalaica.org>";
	$from = "adminusers@europalaica.org";
	$replayTo = "adminusers@europalaica.org";

	$datosEnvioEmail=array( "nombre"  		  =>"Error. EL",
																									"emailUsuario"=>"adminusers@europalaica.org",
																									"asunto" 			  =>$asunto,
																									"contenido"	 	=>"Fecha: ".date("Y-m-d:H:i:s").". ".$contenido,
																									"from" 			   	=>$from,
																									"replayTo"	  	=>$replayTo
																								 );

 $resEmailErrorWMaster = enviarEmail($datosEnvioEmail);			

	if ($resEmailErrorWMaster['codError']=='00000')
	{ 
		$resEmailErrorWMaster['textoComentarios'] = 'Se le ha enviado un email con información de errores';
	}
	else
	{	$resEmailErrorWMaster['textoComentarios'] = 'NO se le ha podido enviar un un email con información de errores';
	}
 //echo "<br>modeloEmail.php: resEmailErrorWMaster";	print_r($resEmailErrorWMaster);
 return $resEmailErrorWMaster;
}
//--------------------------- Fin emailErrorWMaster ---------------------------------------------

//===== FIN FUNCIONES COMUNES CON FUNCIÓN mail() DE PHP =========================================

//===== INICIO FUNCIONES BUSCAR emails  =========================================================
/*---------- Inicio buscarEmailUsuarioTodos -----------------------------------------------------
Llamada desde: controladorLogin.php-->recordarLogin() 
para buscar si un determinado email está guardado en la BBDD, 
para cualquier valor de USUARIO.ESTADO
OBSERVACIONES: ----YA NO SE USA----
-----------------------------------------------------------------------------------------------*/
function buscarEmailUsuarioTodos($valorEmail)
{
	//require_once "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionUsuariosDB['codError']!=='00000')		
	{ $resultadoBuscarEmail=$conexionUsuariosDB;
	}
	else
	{$tablasBusqueda='USUARIO, MIEMBRO';
		$camposBuscados='USUARIO.CODUSER,USUARIO.USUARIO,USUARIO.PASSUSUARIO,MIEMBRO.EMAIL';
		$cadCondicionesBuscar="WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                           AND MIEMBRO.EMAIL=\"".$valorEmail."\"";

		$resultadoBuscarEmail = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
		                        $camposBuscados,$conexionUsuariosDB['conexionLink']);
																										
		if ($resultadoBuscarEmail['numFilas']==0) //si es encontrado siempre devolverá 1
		{$resultadoBuscarEmail['codError']='80005';
			$resultadoBuscarEmail['errorMensaje']="No está registrado ese email";
		}
	}
	//echo "<br><br>1modeloEmail:buscarEmailUsuarioTodos:resultadoBuscarEmail: ";print_r($resultadoBuscarEmail); 

	return $resultadoBuscarEmail;
}
//-------------------------- Fin buscarEmailUsuarioTodos ----------------------------------------

/*---------- Inicio buscarEmailUsuario ----------------------------------------------------------
Busca el email de un usuario, primero buscándolo en la tabla "MIEMBRO" 
(si ESTADO = alta, o 'alta-sin-password-gestor', o 'alta-sin-password-excel')
y si no en la tabla "SOCIOSCONFIRMAR"  (si estado= PENDIENTE-CONFIRMAR) y si no 
lo encuenctra, no existirá ese email en la BBDD.
Se utiliza para enviar un email para restaurar password e informar del usuario/a

RECIBE: $valorEmail: cadena con email usuario a buscar para confirmar		
DEVUELVE: array $resultadoBuscarEmail con el email de usuario, si existe, y 
          y códigos error.				

LLAMADA: controladorLogin.php:recordarLogin() 
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
       usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),
       modeloMySQL.php/buscarCadSql()
							modelos/modeloErrores.php:insertarError()

OBSERVACIONES:															
Agustín 2020-06-28: modifico para incluir PHP: PDOStatement::bindParamValue	
NOTA: se podría llevar a modeloUsuarios.
-----------------------------------------------------------------------------------------------*/
function buscarEmailUsuario($valorEmail)
{ 
 //echo "<br><br>0-1 modeloEmail:buscarEmailUsuario:valorEmail: ";print_r($valorEmail); 
	
	require_once './modelos/modeloErrores.php';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		

	if ($conexionDB['codError'] !== '00000')		
	{ $resultadoBuscarEmail = $conexionDB;
	}
	else//$conexionDB['codError'] == '00000'		
	{/*-------------- Incio buscar Email en tabla MIEMBRO -----------------------*/
		
		$tablasBusqueda = 'USUARIO, MIEMBRO';
		$camposBuscados = 'USUARIO.CODUSER,USUARIO.ESTADO,USUARIO.USUARIO,USUARIO.PASSUSUARIO,MIEMBRO.EMAIL';
		$cadCondicionesBuscar = "WHERE USUARIO.CODUSER = MIEMBRO.CODUSER AND MIEMBRO.EMAIL = :valorEmail ";
		
		$arrBind = array(':valorEmail' => $valorEmail);
	
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		$resBuscarEmailMiembro = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind); 		

		//echo "<br><br>1-1 modeloEmail:resBuscarEmailMiembro:resBuscarEmailMiembro: ";print_r($resBuscarEmailMiembro); 
					
		if ($resBuscarEmailMiembro['codError'] !== '00000')//error sistema SELECT al buscar en USUARIO, MIEMBRO, no es error lógico		
	 {$resultadoBuscarEmail = $resBuscarEmailMiembro;
		 $resInsertarErrores = insertarError($resBuscarEmailMiembro,$conexionDB['conexionLink']);
	 }
	 elseif($resBuscarEmailMiembro['numFilas'] > 1)//no se debiera dar nunca
		{
			$resultadoBuscarEmail['codError'] = '80007';
			$resultadoBuscarEmail['errorMensaje'] = "Error: hay más de un socio/a con el mismo email: ".
			                                         $resBuscarEmailMiembro['resultadoFilas'][0]['EMAIL']. 
			                                         ". Por favor avise a <strong>adminusers@europalaica.org</strong>
																																										 	con su nombre y apellidos indicando que hay una repetición de su email";
  }		
		elseif($resBuscarEmailMiembro['numFilas'] == 1) //email encontrado
  {
			//echo "<br><br>1-1 modeloEmail:resBuscarEmailMiembro:resBuscarEmailMiembro: ";print_r($resBuscarEmailMiembro); 
			
			if ($resBuscarEmailMiembro['resultadoFilas'][0]['ESTADO'] == 'alta-sin-password-gestor' )//$resBuscarEmailMiembro['numFilas']==1 encontrado siempre devolverá 1
			{		
				$resultadoBuscarEmail['codError'] = '80101';
				$resultadoBuscarEmail['errorMensaje'] = "El socio/a tiene bloqueado el acceso. 
				Está pendiente confirmar su email, (anteriormente habrá recibido un email para confirmar su dirección de correo electrónico). 
				Por favor confirme su email o avise a <strong>secretaria@europalaica.org</strong> indicando que está bloqueado para resolver el problema";	

					$resultadoBuscarEmail['errorMensaje'] =  "Por seguridad tienes bloqueado el acceso a la aplicación de Gestión de Soci@s, 
					hasta que confirmes tu email. 
					<br /><br />Anteriormente, a petición tuya, un gestor de Europa Laica, te dio de alta como socio/a.
     <br /><br />Habrás recibido un email cuando el gestor realizó tu alta (si no, mira en la carpeta de correo no deseado -spam-), 
					<br /><br />Por favor confirma tu alta o avisa a <strong>secretaria@europalaica.org</strong> indicando que tienes bloqueado el acceso";
			}
			elseif ($resBuscarEmailMiembro['resultadoFilas'][0]['ESTADO'] == 'alta-sin-password-excel')
			{
				$resultadoBuscarEmail['codError'] = '80101';
				$resultadoBuscarEmail['errorMensaje'] = "Por seguridad tienes bloqueado el acceso a la aplicación de Gestión de Soci@s. 
				<br /><br />Hace tiempo que estás registrado como socio/a de Europa Laica, envía ahora un email a <strong>secretaria@europalaica.org</strong> con tu nombre y apellidos, 
				para que te envíe un correo electrónico para desbloquear el acceso.";			                        														
			}				
			elseif ($resBuscarEmailMiembro['resultadoFilas'][0]['ESTADO'] == 'alta' )//$resBuscarEmailMiembro['numFilas']==1 encontrado siempre devolverá 1
			{		
				$resultadoBuscarEmail = $resBuscarEmailMiembro;
			}
			
			//echo "<br><br>1-2 modeloEmail:resBuscarEmailMiembro:resultadoBuscarEmail: ";print_r($resultadoBuscarEmail);
			
		}//elseif($resBuscarEmailMiembro['numFilas'] == 1) //email encontrado			
		
		/*---------------- Fin buscar Email en tabla MIEMBRO -----------------------*/
		
		elseif($resBuscarEmailMiembro['numFilas'] == 0) //no encontrado, en tablas USUARIO, MIEMBRO', 
		{ 
		  /*-------------- Incio buscar Email en tabla SOCIOSCONFIRMAR -------------*/

				$tablasBusqueda = 'USUARIO, SOCIOSCONFIRMAR';
			 $camposBuscados = 'USUARIO.CODUSER,USUARIO.ESTADO,USUARIO.USUARIO,USUARIO.PASSUSUARIO,SOCIOSCONFIRMAR.EMAIL';
			 $cadCondicionesBuscar = "WHERE USUARIO.CODUSER = SOCIOSCONFIRMAR.CODUSER AND SOCIOSCONFIRMAR.EMAIL = :valorEmail ";
				
				$arrBind = array(':valorEmail' => $valorEmail);
				
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
				$resBuscarEmailSociosconfirmar = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind); 																											

			 //echo "<br><br>2-1 modeloEmail:resBuscarEmailMiembro:resBuscarEmailSociosconfirmar: ";print_r($resBuscarEmailSociosconfirmar); 
			
				if ($resBuscarEmailSociosconfirmar['codError'] !== '00000')//error sistema SELECT al buscar en USUARIO, SOCIOSCONFIRMAR, no es error lógico					
			 {
					$resultadoBuscarEmail = $resBuscarEmailSociosconfirmar;
				 $resInsertarErrores = insertarError($resBuscarEmailSociosconfirmar,$conexionDB['conexionLink']);
			 }
			 elseif($resBuscarEmailSociosconfirmar['numFilas'] > 1)
				{
					$resultadoBuscarEmail['codError'] = '80007';
					$resultadoBuscarEmail['errorMensaje'] = "Error: hay más de un socio/a con el mismo email: ".
																																														$resBuscarEmailSociosconfirmar['resultadoFilas'][0]['EMAIL']. 
																																														". Por favor avise a <strong>adminusers@europalaica.org</strong>
																																														con su nombre y apellidos indicando que hay una repetición de su email";
		  }
				elseif ($resBuscarEmailSociosconfirmar['numFilas'] == 1 && $resBuscarEmailSociosconfirmar['resultadoFilas'][0]['ESTADO'] == 'PENDIENTE-CONFIRMAR')
				//elseif ($resBuscarEmailSociosconfirmar['resultadoFilas'][0]['ESTADO'] == 'PENDIENTE-CONFIRMAR')
				{ 						
					$resultadoBuscarEmail['codError'] = '80101';
					$resultadoBuscarEmail['errorMensaje'] = "Por seguridad tienes bloqueado el acceso hasta que confirmes tu alta como socio/a. 
					<br /><br />Anteriomente iniciaste el proceso de alta como socio/a, y habrás recibido un email para confirmar el alta. 
					Si no lo has recibido, mira en la carpeta de correo no deseado -spam-.					
				 <br /><br />Por favor confirma tu alta o avisa a <strong>secretaria@europalaica.org</strong> indicando que quieres confirmar tu alta";
				}
				elseif($resBuscarEmailSociosconfirmar['numFilas'] == 0)//no encontrado,si es encontrado siempre devolverá 1
				{
					$resultadoBuscarEmail['codError'] = '80005';
				 $resultadoBuscarEmail['errorMensaje'] = "No está registrado ese email";
				}    				
				else //creo que esta situacion no se dará nunca
			 { 
				 $resultadoBuscarEmail = $resBuscarEmailSociosconfirmar;
			 }
				
				//echo "<br><br>2-2 modeloEmail:resBuscarEmailMiembro:resultadoBuscarEmail: ";print_r($resultadoBuscarEmail);
				
    /*-------------- Fin buscar Email en tabla SOCIOSCONFIRMAR ---------------*/
		
		}//elseif($resBuscarEmailMiembro['numFilas']==0) 
		
		else//ESTADO !=='alta',!=='alta-sin-password-gestor',!=='alta-sin-password-excel',!=='PENDIENTE-CONFIRMAR' nunca entrará
		{ //echo "<br><br>3 modeloEmail:resBuscarEmailMiembro:resBuscarEmailMiembro: ";print_r($resBuscarEmailMiembro);		
		}
		
	}//else $conexionDB['codError'] == '00000'
	
//	echo "<br><br>4 modeloEmail:buscarEmailUsuario:resultadoBuscarEmail: ";print_r($resultadoBuscarEmail); 

	return $resultadoBuscarEmail;
}
/*-------------------------- Fin buscarEmailUsuario -------------------------------------------*/
//===== FIN FUNCIONES BUSCAR emails  ===========================================================
?>