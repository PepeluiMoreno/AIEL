<?php
/*------------------------------------------------------------------------------
FICHERO: cEnlacesPie.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este fichero se encuentran las funcion relacionada con el 
             contenido del pie de página	de Gestión de Soci@s		 
OBSERVACIONES: Se abre en ventanas pop-up
               Las vistas están en la carpeta: pie
-------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()---------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya
hay activada una sesion, aunque no es un error puede mostrar un "Notice", 
si warning esta activado. Para evitar estos Notices, uso la función 
is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: Creo que por ahora no se necesita en este script
2020-07-29: creo la función "is_session_started()" para evitar Notices
-------------------------------------------------------------------------------*/
//echo "<br><br>1_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()------------------------------*/

/*------------------- Inicio infAplicacion()    --------------------------------
Muestra un ventana pop-up y blank con información general sobre la aplicación
-------------------------------------------------------------------------------*/

/*------------------- Inicio privacidad() --------------------------------------
Muestra un ventana pop-up y blank con información sobre esta aplicación de 
Gestión de Soci@s. Dentro del texto hay algunos links y un "mailto"
No hay tratamiento de errores.

LLAMADA: desde el "pie", abajo la pantalla, en barra horizontal "Sobre esta aplicación"
         formulario "vInfAplicacionInc.php"
LLAMA: vistas/pie/vInfAplicacionInc.php
-------------------------------------------------------------------------------*/
function infAplicacion()
{ 
  // echo "<br><br>0-1 cEnlacesPie.php:infAplicacion:SESSION: ";print_r($_SESSION);	

	 require_once './vistas/pie/vInfAplicacionInc.php'; 	
	 vInfAplicacionInc();

}
/*--------------------------- Fin  infAplicacion() -----------------------------*/

/*------------------- Inicio privacidad() --------------------------------------
Muestra un ventana pop-up y blank con información sobre privacidad y protección
de datos. Dentro del texto hay algunos links y un "mailto"
No hay tratamiento de errores.

LLAMADA: desde el "pie", abajo la pantalla, la barra horizontal "Protección de Datos"
         formulario "vistas/pie/vPrivacidadInc.php"
LLAMA: vistas/pie/vPrivacidadInc.php
-------------------------------------------------------------------------------*/
function privacidad()
{ //	echo "<br><br>0-1 cEnlacesPie.php:privacidad:SESSION: ";print_r($_SESSION); 

	 require_once './vistas/pie/vPrivacidadInc.php';
	 vPrivacidadInc();

}
/*--------------------------- Fin  privacidadDatos() ---------------------------*/


/*--------------- Inicio contactarEmail (con Clase phpMailer 5.1) ---------------
Muestra un ventana pop-up y blank, con un formulario para que el usuario 
introduzca y envíe un email a "info@europalaica.com" para pedir información

También muestra mensajes de error, en caso de error se envía email adminusers 

LLAMADA: desde el "pie", abajo de toda la pantalla, la barra horizontal "Contactar"
         formulario "vContactarEmailInc.php"
LLAMA: modelos/libs/validarCamposContactarEmail.php:validarCamposContactarEmail(), 
modeloEmail.php:enviarEmailPhpMailer(),emailErrorWMaster()
vistas/pie/vContactarEmailInc.php, vistas/mensajes/vMensajeCabSalirPopUpInc.php
-------------------------------------------------------------------------------*/
function contactarEmail()
{
	require_once './vistas/pie/vContactarEmailInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirPopUpInc.php';	
	require_once './modelos/modeloEmail.php';
	
 $datosMensaje['textoCabecera'] = "CONTACTAR CON EUROPA LAICA";
 $datosMensaje['textoComentarios'] = "<br /><br />Error al enviar el email de contactar.<br /><br /> 
	                                    Prueba de nuevo pasado un rato, o si es urgente inténtalo por otros medios. Tel. +34 670 556 012";																										
	$datosMensaje['textoBoton'] = 'Aceptar';
	$textoSpam = 'En caso de no recibir respuesta a tu email, mira en la carpeta de correo no deseado,<br /><br />pudiera suceder que los filtros antispam bloqueen la recepción del email';												
	$nomScriptFuncionError = "cEnlacesPie.php:contactarEmail() ";
	
	$emailDestino = 'info@europalaica.com'; 
	$nombreEmailDestino = 'Información de Europa Laica';
	
 if ( !$_POST) 
 { $datosContactarEmail['emailDestino']['valorCampo'] = $emailDestino;
			$datosContactarEmail['nombreEmailDestino']['valorCampo'] = $nombreEmailDestino;
 	 vContactarEmailInc($datosContactarEmail);		
	}
	else //POST
	{
		if (isset($_POST['noEnviarEmail']))//ha pulsado el botón "noEnviarEmail", actualmente no entra en este if, en su lugar cierra ventana
		{  
   $datosMensaje['textoComentarios'] = "Ha salido sin enviar el email"; 		
		 vMensajeCabSalirPopUpInc($datosMensaje);				 
	 }	
		else //==(isset($_POST['siEnviarEmail']))Pulsado el botón "siEnviarEmail"
		{
		 require_once './modelos/libs/validarCamposContactarEmail.php';			
			$resValidarCamposForm = validarCamposContactarEmail($_POST);

		 //echo "<br><br>2 cEnlacesPie:contactarEmail:resValidarCamposForm: ";print_r($resValidarCamposForm);
   
			if ($resValidarCamposForm['codError'] !== '00000')
			{
					vContactarEmailInc($resValidarCamposForm['datosContactarEmail']);
			}	
			else //$resValidarCamposForm['codError']=='00000'
			{
    $arrDatosEnvioEmail	= array 
				 ('toAddress' => $emailDestino, 
						'toAddressName' => $nombreEmailDestino,
						'fromAddress' => $resValidarCamposForm['datosContactarEmail']['EMAIL']['valorCampo'],
						'fromAddressName' => $resValidarCamposForm['datosContactarEmail']['NOMBRE']['valorCampo'],
						'subject' => $resValidarCamposForm['datosContactarEmail']['ASUNTO']['valorCampo'],
						'body' => "Nombre de la persona que envía el email: ".$resValidarCamposForm['datosContactarEmail']['NOMBRE']['valorCampo'].
                 ". Dirección email: ".$resValidarCamposForm['datosContactarEmail']['EMAIL']['valorCampo'].
																	". Texto del mensaje:".$resValidarCamposForm['datosContactarEmail']['TEXTOMENSAJE']['valorCampo']
					);																																	

    $resulEnviarEmail = enviarEmailPhpMailer($arrDatosEnvioEmail);						
				//echo "<br><br>3 cEnlacesPie:contactarEmail:resulEnviarEmail: ";print_r($resulEnviarEmail); 
			
				if ($resulEnviarEmail['codError'] !== '00000')
				{ 
      $datosMensaje['textoComentarios'] .= $resulEnviarEmail['textoComentarios'];
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': '.$datosMensaje['textoComentarios'].': '.$resulEnviarEmail['errorMensaje'] );
				}	
				else //$resValidarCamposForm['codError']=='00000'
				{	
  				$datosMensaje['textoComentarios'] = 'Se ha enviado tu email, te responderemos lo antes posible.<br /><br />'.$textoSpam;							
				}
		
		  vMensajeCabSalirPopUpInc($datosMensaje);				
				
	  } //$resValidarCamposForm['codError']=='00000'		
	 }//==(isset($_POST['siEnviarEmail']))Pulsado el botón "siEnviarEmail"
 }//else post 
}
/*------------------------------- Fin contactarEmail -------------------------*/

?>