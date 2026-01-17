<?php
/*------------------------------------------------------------------------------------------------
FICHERO: cGestorSimps.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este fichero se encuentran las funciones relacionadas con la 
             gestión de los simpatizantes.
													
Por ahora en el menú General de Rol de Gestor de simpatizantes solo se incluye la 
función "enviarEmailSimpsGes()" para envíar emails a los socios desde "info@europalaica.org" y 
que es un clon de la función "cPresidente.php:enviarEmailSociosPres", y se incluye aquí de forma 
independiente por si se quiere asignar este rol pero no los demás roles de presidencia. 
(Me lo pidió Manuel Navarro)

																										
OBSERVACIONES: El rol de Gestor de Simpatizantes, se incluía en el diseño original para incluir 
la gestío de los simpatizantes de Europa Laica para contactos, pero luego se decidió que 
dependiese de laicismo.org 
--------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()----------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya hay activada una 
sesion, aunque no es un error puede mostrar un "Notice", si warning esta activado. 
Para evitar estos Notices, uso la función is_session_started(), que he creado que controla el 
estado con session_status() para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
----------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()----------------------------------------------------*/

/*--------------------------- Inicio menuGralGesSimps -------------------------------------------------
Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de gestor simpatizantes  (CODROL=8) 
y se muestran en el menú lateral

Se pueden añadir un enlaces a archivos para descargarlo, estaría en el cuerpo debajo de la imagen 
de "ESCUELA LAICA".

LLAMADA: por ser gestor:controladorLogin.php:menuRolesUsuario():login/vRolInc.php 
también al moverse de un rol a otro en menú izdo, o en línea superior de enlaces

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 
Aquí no necesita cambios PDO. Probada PHP 7.3.21
------------------------------------------------------------------------------------------------------*/
function menuGralGesSimps()
{ 
	//echo "<br><br>0-1 cGestorSimps:menuGralGesSimps:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cGestorSimps:menuGralGesSimps:_POST: "; print_r($_POST);	
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_8'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else // if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_8']=='SI')
	{	
		$datosMensaje['textoCabecera'] = "GESTIÓN DE SIMPATIZANTES";
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los el menú correspondiente Gestión de Simpatizantes. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
		$nomScriptFuncionError = ' cGestorSimps.php:menuGralGesSimps(). Error: ';			
		$tituloSeccion = "Simpatizantes";		
		
		/*------------ inicio navegación para socios gestores CODROL >1 ------------*/
		$_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cGestorSimps&accion=menuGralGesSimps";
		$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Gestión Simpatizantes";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 2;					
		//echo "<br><br>2 cGestorSimps:menuGralGesSimps:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);					
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");	
		/*------------ Fin navegación para socios gestores CODROL >1 ---------------*/	
		
		require_once './modelos/modeloUsuarios.php';
		$resFuncionRol = buscarRolFuncion('8');//CODROL=8 para gestor simpatizantes, en modelosUsuarios.php , incluye insertarError()
	
		//echo "<br><br>3 cGestorSimps:menuGralGesSimps:resFuncionRol: ";print_r($resFuncionRol);				
	 	
		if ($resFuncionRol['codError'] !== '00000')		
	 {require_once './modelos/modeloEmail.php';	
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);		
			
			require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	 }			
  else //($resFuncionRol['codError'] == '00000')
	 {			
			//$enlacesSeccId = $resFuncionRol['resultadoFilas'];//acaso no sea necesario			
	  $_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];//Se podrá acceder desde cualquier sitio			
			
			$cabeceraCuerpo = 'GESTIÓN DE SIMPATIZANTES';
			$textoCuerpo = 'Desde el menú puedes acceder a las funciones disponibles para el <strong>Rol de Gestor de Simpatizantes</strong>';

			$enlacesArchivos = array();
			/* Si quisieramos añadir unos enlaces a unos archivos para descargarlos, estarían 
			   en el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente: 
			*/
			$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
			$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
			$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';	
			
			//$enlacesArchivos[2]['link'] = '../documentos/SIMPATIZANTES/EL_MANUAL_GESTOR_Coordinador.pdf';/No disponible por ahora
			//$enlacesArchivos[2]['title'] = 'Descargar el Manual para Gestión de Simpatizantes de la aplicación informática de Gestión de Socios/as';
			//$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Gestión de Simpatizantes de la aplicación informática de Gestión de Socios/as';	

	
			require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);
	 }//else ($resFuncionRol['codError'] == '00000')
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_8']=='SI')	
}
/*------------------------------ Fin menuGralGesSimps -------------------------------------------------*/


/*----------------------------- Inicio enviarEmailSimpsGes  --------------------------------------------
NOTA: Es casi un clon de la función "cPresidente.php:enviarEmailSociosPres" y comparte casi todas 
las funciones. Se mantiene separado por si en un futuro se ampliase para incluir la gestión de 
simpatizantes no socios u otros consideraciones diferentes.

Función para enviar un email personalizado o no según elección por gestor con rol de Gestor Simpatizantes
a una lista de socios que se elige del formulario 
seleccionados por valores de: CODAGRUPACION, CODPAISDOM, CCAA, CODPROV

Sólo se enviará a los que tienen condición MIEMBRO.EMAILERROR='NO' y MIEMBRO.INFORMACIONEMAIL='SI'

Además de subject y body puede anexar hasta dos ficheros con un límite de 4MB cada y sólo 
determinados tipos archivos.

No permite elegir entre los emails de envío, solo se enVía desde "info@europalaica.org"  

Es obligatorio una dirección BCC, para que envíe una copia (puede ser como opción de prueba).

Se validan los campos del formulario en la función ":validarCamposEmailAdjuntosSociosPres()" y 
después en "buscarSeleccionEmailSociosPres()" se buscan los emails nombres y otros datos
correspondientes a la seleción del formulario.

Permite tres opciones de envíos de emails:
"Enviar emails PERSONALIZADOS", "Enviar emails NO PERSONALIZADOS","Enviar email de prueba solo a BCC"

-Enviar emails PERSONALIZADOS: Mediante la función "emailSociosPersonaGestorPresCoord()" los datos de 
cada socio en un foreach se tratarán individualmente y el email llegará personalizado a los socios uno a uno
con el nombre socio y se podrán añadir otros campos procedentes de la función "buscarSeleccionEmailSociosPres()"

-Enviar emails NO PERSONALIZADOS: Mediante la función "enviarMultiplesEmailsPhpMailer()" con las
direcciones email de cada socio se hace un solo envío sin personalizar igual para todos los socios. 
Previamente con la función"validarEmail()" se valida el formato de los emails de la lista, 
y se excluyen los que no sean válidos.

-Enviar email de prueba solo a BCC: 	Envía un solo email a BCC y al final te mostrará en pantalla 
a cuántos socios/as se habría enviado el mismo email en caso de haber las otras opciones.

RECIBE: campos del formulario de selección y además, el formulario tiene cuatro botones 
con los valores: enviarEmailPersonalizado, enviarEmailNoPersonalizado, siPruebaEmail, noEnviarEmail 
												
LLAMA: modeloPresCoord.php:buscarSeleccionEmailSociosPres()
modeloEmail.php:emailSociosPersonaGestorPresCoord(), o enviarMultiplesEmailsPhpMailer()
libs/validarCamposEmailAdjuntosSociosPres.php:validarCamposEmailAdjuntosSociosPres()
arrayParValor.php:parValoresAgrupaPresCoord(),parValorPais(),parValoresCCAA(),parValorProvincia()
vistas/presidente/vEnviarEmailSociosSimpsInc, para introducir los datos, 
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
otras funciones para validar, obtener emails socios y enviar email

LLAMADA: desde menú izdo rol de Gestor Simpatizantes

OBSERVACION: Probado PHP 7.3.21, No es necesario cambios PDO, las funciones que llama sí incluyen

Los adjuntos, se guardan en un directorio temp, que que se borran automáticamente, después de envío

NOTA: Cuando envía emails a los socios PERSONALIZADOS (el nombre del socio va en el body)
-------------------------------------------------------------------------------------------------------*/
function enviarEmailSimpsGes()//personalizada
{
	//echo "<br><br>0-1 cGestorSimps:enviarEmailSimpsGes:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cGestorSimps:enviarEmailSimpsGes:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cGestorSimps:enviarEmailSimpsGes:_FILES: ";print_r($_FILES);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/gestorSimps/vEnviarEmailSociosSimpsInc.php';//es un clon de vistas/presidente/vEnviarEmailSociosPresInc.php
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';			

		$datosMensaje['textoCabecera'] = "ENVIAR EMAIL A SOCIOS/AS DESDE GESTIÓN DE SIMPATIZANTES";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido enviar email a socios/as desde Gestión de Simpatizantes. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cGestorSimps.php:enviarEmailSimpsGes(). Error: ';
		$tituloSeccion = "Simpatizantes";	
				
		//----------------- Inicio fila de navegación ---------------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cGestorSimps&accion=enviarEmailSimpsGes";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Enviar email a socios/as desde G. Simpatizantes";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
			
		//echo "<br><br>1 cGestorSimps:enviarEmailSimpsGes:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- Fin fila de navegación ------------------------------------	
		
		if (!$_POST) //$datosContactarEmail['asunto']
		{//mejor una funcion para los cuatro???	
			$parValorCombo['agrupaSocio']   = parValoresAgrupaPresCoord('%','');//todas: bien para presidente
			$parValorCombo['paisDomicilio'] = parValorPais('');	
			$parValorCombo['CCAADomicilio'] = parValoresCCAA('');
			$parValorCombo['provDomicilio'] = parValorProvincia('');

			//echo "<br><br>2 cGestorSimps:enviarEmailSimpsGes:parValorCombo: ";print_r($parValorCombo);	
			
			if ($parValorCombo['agrupaSocio']['codError'] !==   '00000' || $parValorCombo['paisDomicilio']['codError'] !== '00000' || 
							$parValorCombo['CCAADomicilio']['codError'] !== '00000' ||	$parValorCombo['provDomicilio']['codError'] !== '00000')
			{ 
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError." Error en funciones parValor: 70100"); //probado errores
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
			}
			else
			{ $datosEmail ='';//para evitar warning		
     vEnviarEmailSociosSimpsInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEmail,$parValorCombo);					
			}
		}//if (!$_POST) 
			
		else //POST y se han rellenado los datos del formulario para validar y enviar 
		{
			if (isset($_POST['noEnviarEmail'])) //ha pulsado el botón "noEnviarEmail"
			{
				$datosMensaje['textoComentarios'] = "Has salido sin haber enviado los emails";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
			}	
			else //isset($_POST['siEnviarEmail'] || $_POST['siPruebaEmail'] ) //enviar email a socios o de prueba a CC
			{
					$datosCamposEmailForm = $_POST['datosEmail'];//contiene [camposEmail]:[FROM],[CC],[subject],[body],[pieProteccionDatos] y [datosSelecionEmailSocios]: agrupación, país domicilio, CCAA, y provincia		
					$datosCamposEmailForm['AddAttachment'] = $_FILES;//archivos anexados	
					
					//echo "<br><br>3 cGestorSimps:enviarEmailSimpsGes:datosCamposEmailForm: ";print_r($datosCamposEmailForm); 

     require_once './modelos/libs/validarCamposEmailAdjuntosSociosPres.php';
					$datosEnvioEmailSocios = validarCamposEmailAdjuntosSociosPres($datosCamposEmailForm);//controla ['codError'], no son errores SQL
				
					//echo "<br><br>4-1 cGestorSimps:enviarEmailSimpsGes:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios); 
	
					if ($datosEnvioEmailSocios['codError'] !== '00000') //solo podrían ser errores lógicos >= 80000
					{/*Dentro de buscarSeleccionEmailSociosPres(), se vuelve a validar el campo del formulario "$_POST['datosEmail']['datosSelecionEmailSocios']"
								para "Selección los socios/as" por (agrupación,país,CCAA provincias), aunque en parte ya se validó en la función anterior
						*/	
							$parValorCombo['agrupaSocio']  = parValoresAgrupaPresCoord('%',$datosEnvioEmailSocios['datosSelecionEmailSocios']['CODAGRUPACION'],'');//todas bien para presidente
							$parValorCombo['paisDomicilio']= parValorPais($datosEnvioEmailSocios['datosSelecionEmailSocios']['CODPAISDOM']);	
							$parValorCombo['CCAADomicilio']= parValoresCCAA($datosEnvioEmailSocios['datosSelecionEmailSocios']['CCAA']);
							$parValorCombo['provDomicilio']= parValorProvincia($datosEnvioEmailSocios['datosSelecionEmailSocios']['CODPROV']);	
							
							if ($parValorCombo['agrupaSocio']['codError'] !==  '00000' || $parValorCombo['paisDomicilio']['codError'] !== '00000' || 
											$parValorCombo['CCAADomicilio']['codError'] !=='00000' ||	$parValorCombo['provDomicilio']['codError'] !== '00000')
							{ 
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError." Error en funciones parValor: 70100"); //probado errores
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
							}
							else
							{ vEnviarEmailSociosSimpsInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEnvioEmailSocios,$parValorCombo);
							}						
     }
     else//($datosEnvioEmailSocios['codError'] === '00000') // bien validarCamposEmailAdjuntosSociosPres()
     {				
						require_once './modelos/modeloPresCoord.php';					
						$resSeleccionEmailSocios = buscarSeleccionEmailSociosPres($datosEnvioEmailSocios['datosSelecionEmailSocios']);//en modeloPresCoord.php:probado error ok		
					
 					//echo "<br><br>5 cGestorSimps:enviarEmailSimpsGes:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);
											
						if ($resSeleccionEmailSocios['codError'] !== '00000')
						{ 							
							if ($resSeleccionEmailSocios['codError'] <= '80000') //ERROR sistema
							{ 
							  $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSeleccionEmailSocios['codError'].": ".$resSeleccionEmailSocios['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
							}
							elseif ($resSeleccionEmailSocios['codError'] == '80001' && $resSeleccionEmailSocios['numFilas'] === 0)//no encontrados emails
							{         	
		       $datosMensaje['textoComentarios'] = 'No hay ningún email de socios/as para las condiciones de selección de socios/as que has elegidas (En agrupación, País, CCAA, Provincia )';			
														
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);																				
							}							
						}//if $resSeleccionEmailSocios['codError'] !== '00000')
						
						else //$resSeleccionEmailSocios['codError'] == '00000')// BIEN buscarSeleccionEmailSociosPres()
						{
								//echo "<br><br>6 cGestorSimps:enviarEmailSimpsGes:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);		
								
							 $datosEnvioEmailSocios['emailSociosSeleccionados'] = $resSeleccionEmailSocios['resultadoFilas'];//lista de emails socios para enviar a modeloEmail
								
								if (isset($_POST['enviarEmailPersonalizado']) )	  
								{
									$datosMensaje['textoCabecera'] = "ENVIAR EMAIL PERSONALIZADOS A SOCIOS/AS";  										
							 	//NOTA: Envía emails a los socios PERSONALIZADOS (dentro de bucle envía uno a uno CON nombre en el body) y usa: modeloEmail:enviarEmailPhpMailer();										
						 	 $reEnviarEmail = emailSociosPersonaGestorPresCoord($datosEnvioEmailSocios);
																				
								}/*---- Fin opción $_POST['enviarEmailPersonalizado'])-------------------------------------*/

								elseif (isset($_POST['enviarEmailNoPersonalizado']) )//por ahora esta desactivada en el formulario
								{ 
								  $datosMensaje['textoCabecera'] = "ENVIAR EMAIL NO PERSONALIZADOS A SOCIOS/AS"; 
								  //NOTA: Envía emails a los socios SIN PERSONALIZADOS (en un solo envío sin bucle, SIN nombre en el body) y usa: modeloEmail:enviarMultiplesEmailsPhpMailer();					
								  $reEnviarEmail = emailSociosMultipleGestorPresCoord($datosEnvioEmailSocios);
								  
								}/*---- Fin opción $_POST['enviarEmailNoPersonalizado'])-------------------------------------*/									
 
								elseif (isset($_POST['siPruebaEmail']) )	  
								{	
								  $datosMensaje['textoCabecera'] = "ENVIAR EMAIL DE PRUEBA A BCC"; 			
										/*---- Inicio opción $_POST['siPruebaEmail'])-------------------------------------				
											Se eliminan todos los emails de los socios, para que la función no pueda enviar 
											emails a los socios, pero SÍ una copia a $datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']
										*/								
										$datosEnvioEmailSocios['emailSociosSeleccionados'] = array();
										
										//NOTA: Envía emails solo a BCC (CON subject, body y archivos y número a los que se habría enviado), usa: modeloEmail:enviarEmailPhpMailer();										
										$reEnviarEmail = emailSociosPersonaGestorPresCoord($datosEnvioEmailSocios);//envía solo uno a BCC											 										
								
								}/*---- Fin opción $_POST['siPruebaEmail'])-------------------------------------*/
								/*else No debiera entrar
								{  $reEnviarEmail['codError'] = '80001';
								}		*/        
 							//echo "<br><br>7 cGestorSimps:enviarEmailSimpsGes:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);					
								
								/*----------------------Inicio Control Error en funciones de modeloEmail.php ---*/
								if ($reEnviarEmail['codError'] !== '00000')//Los errores de envío email se guardan en tabla ERRORES en emailSociosMultipleGestorPresCoord()*Error correo electrónico
								{
         $datosMensaje['textoComentarios'] = "<br /><br />".$reEnviarEmail['textoComentarios'];//enviará excluyendo los errores emails	detectados													
								}
								else//$reEnviarEmail['codError'] == '00000'
								{ 
									if (isset($_POST['siPruebaEmail']) )	
									{ 
           $datosMensaje['textoComentarios'] = '<strong>Se ha enviado como PRUEBA UN SOLO EMAIL BCC A: '.$datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo'].
																																															'</strong><br /><br />Si hubieses elegido la opción <i>-Enviar emails PERSONALIZADOS-</i> se habrían envíado los emails 
																																																personalizados con sus nombres<br />Si hubieses elegido la opción <i>-Enviar emails NO PERSONALIZADOS-
																																																</i> los emails se habrían enviado sin	los nombres.
																																																<br /><br />En total se habrían enviado (salvo errores en los emails) <strong>'.$resSeleccionEmailSocios['numFilas'].' a socios/as</strong>																																																
																																																correspondientes a la selección que has elegido y con emails anotados como válidos'; 			
									}
									else //isset($_POST['siEnviarEmail'])
									{					
										$datosMensaje['textoComentarios'] = 		"<br /><br />".$reEnviarEmail['textoComentarios'];					
									}					
								}//else $reEnviarEmail['codError'] == '00000'				
								
								//echo "<br><br>8 cGestorSimps:enviarEmailSimpsGes:reEnviarEmail: ";print_r($reEnviarEmail);
								/*----------------------Fin Control Error en funciones de modeloEmail.php ------*/					

								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']); 
						
					 }//else ($datosEnvioEmailSocios['codError'] === '00000')// BIEN buscarSeleccionEmailSociosPres()
					}//else $datosEnvioEmailSocios['codError']=='00000'// bien validarCamposEmailAdjuntosSociosPres()
			}//else (isset($_POST['siEnviarEmail'] || $_POST['siPruebaEmail'] ) 
		}//else $_POST
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin enviarEmailSimpsGes -------------------------------------------------*/

?>