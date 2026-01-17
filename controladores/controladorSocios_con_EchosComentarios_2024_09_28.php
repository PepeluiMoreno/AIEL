<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: controladorSocios.php
VERSION: PHP 7.3.19

DESCRIPCION: En este fichero se encuentran las funciones relacionadas con 
             socios
LLAMA: controladores/libs/, modelos/libs
       modelos/modeloSocios.php, /vistas/socios/....
			    vistas/mensajes/vMensaje1Inc.php
       modelos/modeloEmail.php (comunicar alta a socio, presidencia, secretaría, tesorería, coodinadores
							 y errores al webmaster) .......
								
OBSERVACIONES: Incluye acceso a BBDD con PDO												
							
--------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()----------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya
hay activada una sesion, aunque no es un error puede mostrar un "Notice", 
si warning esta activado. Para evitar estos Notices, uso la función 
is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
--------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()-------------------------------------------------*/

/*------------------------------- Inicio menuGralSocio --------------------------------------------
Se llega desde la página al entrar con el login si solo tiene rol de Socio, si no es gestor, 
o si tiene más roles, (coordinación, presidencia, tesorería,)  desde el menú general de roles del 
usuario, o desde los enlace de la línea de link navegación cuando se va al rol de "Socio".

Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de socio (CODROL = 1) y 
se muestran en el menú lateralSe pueden añadir un enlaces a archivos para descargarlo, estaría en 
el cuerpo debajo de la imagen de "ESCUELA LAICA".

LLAMADA: 
en caso de que solo sea socio: controladorLogin.php:validarLogin() 
en caso de ser gestor:controladorLogin.php:menuRolesUsuario():login/vRolInc.php 

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES:
2020-04-24: organizo texto y comentarios, PHP 7.3.21. aquí no necesita cambios PDO.
------------------------------------------------------------------------------------------------*/
function menuGralSocio()//puede ser socio, presidente,etc, para tatar sus propios datos
{
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else//if ($_SESSION['vs_autentificado'] == 'SI')
	{
		//echo "<br><br>0-1 controladorSocios:menuGralSocio:_SESSION: ",print_r($_SESSION);	
	 //echo "<br><br>0-2 controladorSocios:menuGralSocio:_POST: "; print_r($_POST);
		
		$datosMensaje['textoCabecera'] = 'GESTIÓN DE SOCIOS/AS';
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar el menú correspondiente al socio/a. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
		$nomScriptFuncionError = ' controladorSocios.php:menuGralSocio(). Error: ';			
	 $tituloSeccion = 'Área de Socios/as';
		
		if ( !isset($_SESSION['vs_autentificadoGestor']) || $_SESSION['vs_autentificadoGestor'] !== 'SI')//si no es gestor navegación línea de superior no se mostrarán nada 
	 {
		 $navegacion = '';
		}
  else //será presidente,coordinador,tesorero,...(y $_SESSION['vs_CODROL'] sin valor)
  {							
  /*------------ inicio navegación para socios gestores CODROL >1 ------------*/
		 // en este caso no hay ['enlaces'][1] sería el del rol del usuario= socio o simp
   $_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=controladorSocios&accion=menuGralSocio";
   $_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Menú socio/a";			
			$_SESSION['vs_HISTORIA']['pagActual'] = 2;
			//echo "<br><br>1 controladorSocios:menuGralSocio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
	  require_once './controladores/libs/cNavegaHistoria.php';
   $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
			/*------------ Fin navegación para socios gestores CODROL >1 ---------------*/	
		}	
	
  require_once './modelos/modeloUsuarios.php';		
		$resFuncionRol = buscarRolFuncion("1");//SOCIO CODROL=1, tiene conexion(), PDO e insertar error
	
		//echo "<br><br>2 controladorSocios:menuGralSocio:resFuncionRol: ";print_r($resFuncionRol);
				 
  if ($resFuncionRol['codError'] !== '00000')
	 {			
		 require_once './modelos/modeloEmail.php';	
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);		
			
			require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	 }	
  else //$resFuncionRol['codError'] == '00000'
	 {	
   $_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];	
	
	  $cabeceraCuerpo = 'GESTIÓN DE SOCIOS/AS';
			$textoCuerpo = 'Desde el menú puedes acceder a las funciones disponibles para modificar o eliminar tus datos personales en la base de datos de Europa Laica';
						
			$enlacesArchivos = array();
			/*--- Si quisieramos añadir enlaces a archivos para descargarlos, estarían en ----
			el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente: 
			--------------------------------------------------------------------------------*/
			/*
			$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
			$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
			$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio'; 
			*/
	  require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);
			
	 }//else $resFuncionRol['codError'] == '00000'	 
 }//else if ($_SESSION['vs_autentificado'] == 'SI') 
}

/*------------------------------- Fin menuGralSocio ---------------------------------------------*/ 


/*-------------------------- Inicio altaSocio ----------------------------------------------------
En esta función se registran los datos de alta de un socio por el mismo socio, y se insertarán en
la tabla SOCIOSCONFIRMAR y USUARIO, en esta tabla quedará ESTADO=PENDIENTE-CONFIRMAR. 
A la vez se le enviará un email al socio con el link (incluye PK usuario encriptada) para 
confirmar su alta. Es nesario confirmar el alta por el socio (o por un gestor) para que sea efectiva
 
Al final se lleva a una pantalla desde la que se indica la cantidad a pagar y los modos de 
pago: IBAN de bancos de EL, y script con enlace a PayPal que incluye la cuota elegida por el socio
y los datos personales del socio (después del pago con PayPal retorna indicando pago efecuado o cancelación) 

- Si después se confirma alta, se insertará en todas las demás tablas que correspondan los datos
del socio y se pondrán a NULL los datos personales del socio de la tabla SOCIOSCONFIRMAR.
- Si después se anula se pondrán a NULL los datos personales del socio de la tabla SOCIOSCONFIRMAR.

-Si la aplicación está en modo mantenimiento, se bloquea el acceso a la función

NOTA: En la función modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm() se obtienen 
las cuentas bancarias de la ASOCIACION o de la AGRUPACION y el script de PayPal personalizado para 
el cobro de la cuota para ese socio. Estos datos se pasan a vPagarCuotaSocioInc.php
									
En esta función del controlador se puede asignar los valores:									
REAL COBRO: 
$datosSocioPayPal['business'] ='tesoreria@europalaica.com'				
$datosSocioPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr'

PRUEBA CON SANDOX NO COBRA: 
$datosSocioPayPal['business'] ='prueba1@europalaica.com'
$datosSocioPayPal['action']='https://www.sandbox.paypal.com/cgi-bin/webscr'

LLAMA: modeloUsuarios.php:buscarModoAp()
       modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
       modeloSocios.ph:buscarCuotasAnioEL(),altaSocios()
							modeloEmail.php:emailPeticionConfirmarAltaUsuario(),emailErrorWMaster()
							modelos/libs/validarCamposSocio.php:validarCamposAltaSocioSocio()							
							modelos/modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm()							
							vistas/socios/vPagarCuotaSocioInc.php:vPagarCuotaSocioInc()
       vistas/mensajes/vMensajeCabInicialInc.php';					
require_once controladores/libs/inicializaCamposAltaSocio.php, e inicializa cookies 							
							
LLAMADA: desde el link del "Nuevo/a socio/a" del formulario de entrada

OBSERVACIONES: probado PHP 7.3.21
Aquí no necesita cambios para PDO, incluyen las funciones que utiliza 

Se podría hace un tratamiento de errores único al final.

No entrará nunca si está en modo MANTENIMIENTO, para que un socio no pueda darse de alta
-----------------------------------------------------------------------------------------------*/
function altaSocio()
{
	echo "<br><br>0-1 controladorSocios:altaSocio:_POST: ";print_r($_POST);
	require_once './modelos/modeloUsuarios.php';	
	require_once './modelos/modeloSocios.php';	
	require_once './modelos/libs/arrayParValor.php';
	require_once './vistas/socios/vAltaSocioInc.php';
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';
	require_once './modelos/modeloEmail.php';
	
	$datosMensaje['textoCabecera'] = 'ASOCIARSE A EUROPA LAICA';	
	$datosMensaje['textoComentarios'] = '<br /><br />Error al registrarse como socio/a. Pruebe de nuevo pasado un rato. 
																																						<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
	$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';																																						
	$nomScriptFuncionError = ' controladorSocios.php:altaSocio(). Error: ';		
	$tituloSeccion = 'Área de Socios/as';
	$enlacesSeccIzda = '';//como aún no es socio no tendrá ningún menú lateral, después será = $_SESSION['vs_enlacesSeccIzda']	

 /*------------- Inicio control si está MANTENIMIENTO bloquear -------------------------------*/
	require_once './modelos/modeloAdmin.php';
	$resBuscarModoAp = buscarModoAp();//en modeloAdmin.php, incluye conexion, probado error OK
		
	//echo "<br><br>0-2 controladorSocios:altaSocio:resBuscarModoAp: ";print_r($resBuscarModoAp);	

	if ($resBuscarModoAp['codError'] !== '00000')//Error OK, numFilas == 0 también trata como error
	{	
	
	//$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'buscarModoAp()'.$resBuscarModoAp['codError'].": ".$resBuscarModoAp['errorMensaje']);
			vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);				
	}				
	else//$resBuscarModoAp['codError'] == '00000'
	{if ($resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] == 'MANTENIMIENTO')//se bloquea el proceso de alta 
		{	$datosMensaje['textoComentarios'] = "<br /><br /><strong>Esta aplicación informática no estará accesible para los socios y socias durante unas horas, debido a trabajos de mantenimiento.
                                      				<br /><br />Perdonen las molestias</strong>";					
				vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);			
		}
		/*------------- Fin control si está MANTENIMIENTO bloquear ----------------------------------*/
		else //$resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] !== 'MANTENIMIENTO'
		{			
			if (!$_POST) 
			{
				require_once './controladores/libs/inicializaCamposAltaSocio.php';//inicializa algunas variables y SESIONES, cookies también		

				$parValorCombo = parValoresRegistrarUsuario($valorDefectoPaisDoc,$valorDefectoPaisDom,$valorDefectoAgrup);//antes $parValorCombo = parValoresRegistrarUsuario("ES","ES",'00000000');				
				//echo "<br><br>1 controladorSocios:AltaSocio:parValorCombo"; print_r($parValorCombo);
				
				if ($parValorCombo['codError'] !=='00000')
				{
	//				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'parValoresRegistrarUsuario()'.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);
					vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);						
				}	
				else //Si no errores en $parValorCombo llama a formulario vAltaSocioInc
				{
					$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');	//incluye conexionDB()	 está en modeloSocios.php
				
					//echo "<br><br>2 controladorSocios:altaSocio:resCuotasAniosEL:";print_r($resCuotasAniosEL);
					
					if ($resCuotasAniosEL['codError'] !== '00000')
					{
//						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'modeloSocios.php:buscarCuotasAnioEL(): '.$resCuotasAniosEL['codError'].": ".$resCuotasAniosEL['errorMensaje']);				
						vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
					}
					else					
					{$datCuotaAnioEL = $resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];

						$datosInicio['datosFormCuotaSocio']['ANIOCUOTA'] =	$datCuotaAnioEL['General']['ANIOCUOTA'];
						$datosInicio['datosFormCuotaSocio']['CODCUOTAGeneral'] =	$datCuotaAnioEL['General']['CODCUOTA'];	
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'] = $datCuotaAnioEL['General']['IMPORTECUOTAANIOEL'];
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'] =	$datCuotaAnioEL['Joven']['IMPORTECUOTAANIOEL'];
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'] =	$datCuotaAnioEL['Parado']['IMPORTECUOTAANIOEL'];

						vAltaSocioInc($tituloSeccion,$datosInicio,$parValorCombo);
					}	
				}
			}
			else //POST
			{//echo "<br><br>3-1 controladorSocios:altaSocio:POST: ";print_r($_POST);
				
				if (isset($_POST['noGuardarDatosSocio'])) //ha pulsado el botón "noGuardarDatosSocio"
				{  
					$datosMensaje['textoComentarios'] = "Has salido sin haberte registrado como nuevo socio/a";				
					vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
				}	
				else //==(isset($_POST['siGuardarDatosSocio']))Pulsado el botón "siGuardarDatosSocio"
				{		
					require_once './modelos/libs/validarCamposSocio.php';
					$resValidarCamposForm = validarCamposAltaSocioSocio($_POST);
					//echo "<br><br>3-2 controladorSocios:altaSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
			
					if ($resValidarCamposForm['codError'] !== '00000')
					{	if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
							{
								require_once './modelos/libs/arrayParValor.php';//añadido nuevo 20320-03-20
								$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																				$resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																				$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']	);	
			
								//echo "<br><br>4-1 controladorSocios:altaSocio:parValorCombo: ";print_r($parValorCombo);

								if ($parValorCombo['codError'] !== '00000') 
								{ 
//							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);
										vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);				
								}	
								else 
								{ 				
										require_once './vistas/socios/vAltaSocioInc.php';	
										vAltaSocioInc($tituloSeccion,$resValidarCamposForm,$parValorCombo);//para corregir los errores lógicos
								}
							}			
							else //$resValidarCamposForm['codError'] < '80000') = error sistema
							{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
									vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
							}
					}//if ($resValidarCamposForm['codError'] !=='00000')
						
					else //$resValidarCamposForm['codError']=='00000' = NO HAY ERROR
					{
						$resAltaSocio = altaSocios($resValidarCamposForm);//$resAltaSocio devuelve información para mostrar en vPagarCuotaSocioInc(), está en modeloSocios.php';	
					
						echo "<br><br>5-1 controladorSocios:altaSocio:resAltaSocio:	";print_r($resAltaSocio);
						
						if ($resAltaSocio['codError'] !== '00000')//siempre será ($resAltaSocio['codError'] < '80000'))
						{
							
//							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' :modeloSocios.php:altaSocios(): '.$resAltaSocio['codError'].": ".$resAltaSocio['errorMensaje']);
							vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);					
						}	
						else // ($resAltaSocio['codError']=='00000') 
						{$resValidarCamposForm['CODUSER'] = $resAltaSocio['CODUSER'];  
						
							$resultEnviarEmail = emailPeticionConfirmarAltaUsuario($resValidarCamposForm);//en modeloEmail.php
							//echo "<br><br>5-2 controladorSocios:altaSocio:resultEnviarEmail:	";print_r($resultEnviarEmail);	
							
							if ($resultEnviarEmail['codError'] !=='00000')
							{
								
								//$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['textoComentarios']);
								vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
							}	
							else //$resultEnviarEmail['codError'] =='00000'
							{
								/*----------- InicioPreparar Datos Socio Para Pago Cuota con Bancos y Pay -------------------*/

								$resValidarCamposForm['SOCIOSCONFIRMAR']['CODUSER']['valorCampo'] = $resAltaSocio['CODUSER'];				

								require_once './modelos/modeloBancos.php';						
								$datosSocioBancosPayPal =	mPrepararDatosRegSocioPagoCuotaBancosPayForm($resValidarCamposForm);	
								//Esta función incluye el tratamiento de errores y aquí se muestra lo correcto						

								/*---- Inicio Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php ------
									Esto lo pongo aquí en controlador por que es más fácil el acceso, para hacer pruebas pero
									también puede estar en modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm()	
								-----------------------------------------------------------------------------------------*/
								
								/*----Inicio  PARA PRUEBA SIN COBRAR: DESCOMENTAR -------------------------------------
										$datosSocioBancosPayPal['business'] ='prueba1@europalaica.com';
										$datosSocioBancosPayPal['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
								----- fFn PARA PRUEBA SIN COBRAR: DESCOMENTAR ----------------------------------------*/
								
								/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR -----------------------------*/								
								$datosSocioBancosPayPal['business'] = 'tesoreria@europalaica.com'; 			
								$datosSocioBancosPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr';						
								/*----**** OJO Fin **** REAL PARA COBRAR: DESCOMENTAR --------------------------------*/
								
								/*---- Fin Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php ------*/       						

								/*-----------Fin Preparar Datos Socio Para Pago Cuota con Bancos y Pay ----------------------*/						
								//echo "<br><br>6 controladorSocios:altaSocio:datosSocioBancosPayPal: ";print_r($datosSocioBancosPayPal);				
								//echo "<br><br>7 controladorSocios:altaSocio:resAltaSocio: ";print_r($resAltaSocio);										
								
								require_once './vistas/socios/vPagarCuotaSocioInc.php';	
								/*Aquí es necesario el parámetro "$navegación" aunque es cero (0), está para mantener el formato de parámetros
										de esta función "vPagarCuotaSocioInc" para socio que tienen más roles (coordinacion, presidencia, tesoreria)		
										para la línea enlaces zona superior tipo: >>Entrar>>Menú roles>>Menú socio/a>>Pagar cuota anual 
								*/		
								$navegacion = '';			
								vPagarCuotaSocioInc($tituloSeccion,$resAltaSocio['arrMensaje'],$datosSocioBancosPayPal,$navegacion);
								
							}//else $resultEnviarEmail['codError'] =='00000'	
						}//else $resAltaSocio['codError']=='00000'
					}//else $resValidarCamposForm['codError']=='00000'
				}//else if (!isset($_POST['vaciarCampos']))
			}//else post 
  }//else $resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] !== 'MANTENIMIENTO'
	}//else $resBuscarModoAp['codError'] == '00000'
}
/*------------------------------- Fin altaSocio  ----------------------------------------------*/

/*--------------------------- Inicio  confirmarAnularAltaSocio ---------------------------------
En esta función sólo es para mostrar el formulario que pide confirmar o anular el alta de un 
socio (pendiente de confirmar), a petición del mismo, desde el link que recibió al registrase 
como nuevo socio. 
Desde el formulario según la elección se llamará a:
-controladorSocios:confirmarAltaSocio() 
-controladorSocios:anularAltaSocioPendienteConfirmar()

Si se elige confirmar, se llama a la función controladorSocios:confirmarAltaSocio()
Si se elige anular, se controladorSocios:anularAltaSocioPendienteConfirmar()

RECIBE: parámetro el $codUserEncriptado desde el link del email recibido por el socio

LLAMADO: desde el link del texto en email "emailPeticionConfirmarAltaUsuario()", 
         recibido por el socio, después de registrase como nuevo socio
LLAMA: desEncriptarBase64(),buscarDatosUsuario(),buscarDatosSocioConfirmar()
controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion(),
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: probado PHP 7.3.21
Aquí no necesita cambios para PDO, incluyen las funciones que utiliza 

Añado limpiarVariablesSesion(), por si se ha registrado previamente en ese el navegador y para 
eliminar las posible variables de SESSION y COOKIES
-----------------------------------------------------------------------------------------------*/
function confirmarAnularAltaSocio($codUserEncriptado)
{	
 //echo "<br><br>0-1 controladorSocios:confirmarAnularAltaSocio:POST: ";print_r($_POST);
	//echo "<br><br>0-2 controladorSocios:confirmarAnularAltaSocio:_SESSION: ";print_r($_SESSION);	
 //echo "<br><br>0-3 controladorSocios:confirmarAnularAltaSocio:_COOKIE: ";var_dump($_COOKIE);	
	
 if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//por si guardó $_SESSION anterior  
	{  
				require_once './controladores/libs/limpiarVariablesSesion.php';
				limpiarVariablesSesion();//Para limpiar Variables SESSION y COOKIES;
	}		
	
	require_once './modelos/modeloSocios.php';
	require_once './vistas/socios/vConfirmarSocioInc.php';	 
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';	//Para que pueda entrar, registrase de nuevo, ...
	require_once './vistas/mensajes/vMensajeCabSalirInc.php';//Para salir sin más opciones
	require_once './modelos/modeloEmail.php';	
	
 $datosMensaje['textoCabecera'] = 'CONFIRMAR O ANULAR ALTA DEL SOCIO/A ';
	$datosMensaje['textoComentarios'] = '<br /><br />Error al - Confirmar o anular alta del socio/a -. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';																																						
	$nomScriptFuncionError = ' controladorSocios.php:confirmarAnularAltaSocio(). Error: ';		
	
	$tituloSeccion = 'Área de Socios/as';
	$enlacesSeccIzda = '';//como aún no es socio no tendrá ningún menú lateral, después será = $_SESSION['vs_enlacesSeccIzda']
	
 //echo "<br><br>3 controladorSocios:confirmarAnularAltaSocio:codUserEncriptado: ";print_r($codUserEncriptado);

 require_once "../../usuariosLibs/encriptar/encriptacionBase64.php"; 

	$codUser = desEncriptarBase64($codUserEncriptado);			

 //echo "<br><br>4 controladorSocios:confirmarAnularAltaSocio:codUser: ";print_r($codUser);

 $datosUsuario = buscarDatosUsuario($codUser);//incluye conexionDB(), en modeloUsuarios.php	
	
	//echo "<br><br>5 controladorSocios:confirmarAnularAltaSocio:datosUsuario: "; print_r($datosUsuario);	 	
			
 if ($datosUsuario['codError'] !== '00000')
 {$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datosUsuario['codError'].": ".$datosUsuario['errorMensaje']);	
 
		$datosMensaje['textoComentarios'] = 'Error datos del socio/a no encontrados. Si has copiado el enlace comprueba que está completo';
		vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
	}      
	else //$datosUsuario['codError']=='00000' ////los que no se dan de alta por su cuenta	 
	{
		if ($datosUsuario['resultadoFilas']['ESTADO'] !== 'PENDIENTE-CONFIRMAR')
	 {	
   switch ($datosUsuario['resultadoFilas']['ESTADO']) 				
			{case 'alta':
			 case 'CONFIRMADO'://Acaso no exista nunca esta situacion, porque sea siempre 'alta'
          $textoComentarios = "Ya habías confirmado anteriormente tu deseo de hacerte socio/a de Europa Laica. 
										                     <br /><br />Si has olvidado tu Usuario/a o contraseña puedes recuperarlos 
																															haciendo clic en \"Recordar usuario/a y contraseña\"";
          break;
				case 'baja':						
				case 'ANULADA-SOCITUD-REGISTRO'://ojo con el texto "ANULADA-SOCITUD-REGISTRO" es lo que se graba en TABLA USUARIO No cambiar afectaría a dotos ya grabados
          $textoComentarios = "Anteriormente habías pedido borrar tus datos y ya fueron eliminados todos tus datos personales.
																															<br /><br />Si más adelante quieres hacerte socio/a de Europa Laica de nuevo, 
																															podrás registrate otra vez haciendo clic en \"Asóciate\"";
          break;
    case 'PLAZO-VENCIDO':
          $textoComentarios = "Ha vencido el plazo de tiempo para confirmar tu deseo de hacerte socio/a de 
										                     Europa Laica y tus datos personales fueron borrados.
																															<br /><br />Si ahora quieres hacerte socio/a de Europa Laica, puedes registrate
																															 otra vez haciendo clic en \"Asóciate\"";
          break;								
   }
   $datosMensaje['textoComentarios'] = $textoComentarios;

   vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);//cabecera con links: Entrar,Nuevo socio,Recordar contraseña y Salir, por si quiere entrar o alta de nuevo	
					
		}//$datosUsuario['resultadoFilas']['ESTADO'] !== 'PENDIENTE-CONFIRMAR')
		else //$datosUsuario['resultadoFilas']['ESTADO'] == 'PENDIENTE-CONFIRMAR')
		{
		 $datSocioConfirmar_Anular = buscarDatosSocioConfirmar($codUser);//en modeloSocios.php, incluye conexionDB() e insertarError()
	  //echo "<br><br>6 controladorSocios:confirmarAnularAltaSocio:datSocioConfirmar_Anular: "; print_r($datSocioConfirmar_Anular); 
	 		 		 
	  if ($datSocioConfirmar_Anular['codError'] !== '00000')
	  {
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioConfirmar_Anular['codError'].": ".$datSocioConfirmar_Anular['errorMensaje']);
				vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);//cabecera con sólo link "Salir", para que tenga vuelva a email de confirmación																												
			}      
			else //$datSocioConfirmar_Anular['codError']=='00000'
			{
    $datSocioConfirmar_Anular['resultadoFilas'][0]['codUserEncrip'] = $codUserEncriptado;
				vConfirmarSocioInc($tituloSeccion,$datSocioConfirmar_Anular['resultadoFilas'][0],$enlacesSeccIzda);//no necesita $navegacion ya al confirmar alta solo será socio
			}	
		}//else $datosUsuario['resultadoFilas']['ESTADO'] == 'PENDIENTE-CONFIRMAR')		
 }//else $datosUsuario['codError']=='00000'
}
/*--------------------------- Fin  confirmarAnularAltaSocio -----------------------------------*/


/*-------------------- Inicio confirmarAltaSocio ------------------------------------------------
En esta función se confirma el alta de un socio (pendiente de confirmar) a petición del mismo. 
Si se confirma, se insertarán en todas las tablas que correspondan los datos del socio y se 
enviará un email al socio y también a secretaría,pres, tesorería y coordinador comunicándolo.

Después se borrarán los datos personales de la tabla SOCIOSCONFIRMAR, aunque se deja el coduser 
(POSIBLEMENTE FUESE MEJOR ELIMINAR TODA LA FILA, COMO SE HACE CON LA CONFIRMACIÓN DE LOS 
PENDIENTES POR LOS GESTORES)

NOTA: En la función modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	se obtienen
las cuentas bancarias de la ASOCIACION o de la AGRUPACION y el script de PayPal personalizado 
para el cobro de la cuota para ese socio. Estos datos se pasan a vPagarCuotaSocioInc.php
									
En esta función del controlador se puede asignar los valores:									
REAL COBRO: 
$datosSocioPayPal['business'] ='tesoreria@europalaica.com'				
$datosSocioPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr'

PRUEBA CON SANDOX NO COBRA: 
$datosSocioPayPal['business'] ='prueba1@europalaica.com'
$datosSocioPayPal['action']='https://www.sandbox.paypal.com/cgi-bin/webscr'

RECIBE: como hidden (POST) el $codUserEncrip, que viene encriptado.

LLAMADA: controladorSocios:confirmarAnularAltaSocio():
vConfirmarSocioInc(), el botón de Confirmar alta socio

LLAMA: /usuariosLibs/encriptar/encriptacionBase64.php:desEncriptarBase64()
controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion(),
modeloSocios.php:buscarDatosSocio(),altaSociosConfirmada(),buscarEmailCoordSecreTesor()
modelos/modeloEmail.php:emailComunicarAltaUsuario(),emailComunicarAltaUsuario(),emailErrorWMaster()
modelos/modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()							
vistas/socios/vPagarCuotaSocioInc.php:vPagarCuotaSocioInc()	

OBSERVACIONES: probado PHP 7.3.21
Aquí no necesita cambios para PDO, incluyen las funciones que utiliza
Añado limpiarVariablesSesion(), por si se ha registrado previamente en ese el navegador y para 
eliminar las posible variables de SESSION y COOKIES
-----------------------------------------------------------------------------------------------*/
function confirmarAltaSocio()
{  
 //echo "<br><br>0-1 controladorSocios:confirmarAltaSocio:POST: ";print_r($_POST);	
	//echo "<br><br>0-2 controladorSocios:confirmarAltaSocio:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-3 controladorSocios:confirmarAnularAltaSocio:_COOKIE: ";var_dump($_COOKIE);
		
	if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//por si guardó $_SESSION anterior  
	{  
				require_once './controladores/libs/limpiarVariablesSesion.php';
				limpiarVariablesSesion();//Para limpiar Variables SESSION y COOKIES;
	}		
	
	require_once './modelos/modeloSocios.php';
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';		
	require_once './modelos/modeloEmail.php';

	$datosMensaje['textoCabecera'] = 'CONFIRMACIÓN DE ALTA DEL SOCIO/A';
	$datosMensaje['textoComentarios'] = "<br /><br />Error al confirmar el alta del socio/a. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
	$nomScriptFuncionError = ' controladorSocios.php:confirmarAltaSocio(). Error: ';	
	$tituloSeccion = 'Área de Socios/as';
	$enlacesSeccIzda ='';

	if (!isset($_POST['confirmarAltaSocio']))//siempre entrará por aquí, si no es intento directo de entrar
	{ vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
	}	
 else // (isset($_POST['confirmarAltaSocio']))//siempre entrara por aqui, si no intento directo 
 {
		//-- Inicio insertar en tablas y actualizar tabla de confirmación alta socio--	
	 
		require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
  $codUserConfirmar = desEncriptarBase64($_POST['codUserEncrip']);	 
  //echo "<br><br>1 controladorSocios:confirmarAltaSocio:codUserConfirmar: ";print_r($codUserConfirmar);
			
  $reAltaSocioConfirmar = altaSociosConfirmada($codUserConfirmar);//devuelve $reAltaSocioConfirmar[CODUSER], en modeloSocios.php	
	 //echo"<br><br>2 controladorSocios:confirmarAltaSocio:reAltaSocioConfirmar: ";print_r($reAltaSocioConfirmar);
		
		if ($reAltaSocioConfirmar['codError'] !== "00000")
		{vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);						
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'. modeloSocios.php:altaSociosConfirmada(): '.
			                                          $reAltaSocioConfirmar['codError'].": ".$reAltaSocioConfirmar['errorMensaje']);
									 
		}//-- Fin insertar en tablas y actualizar tabla de confirmación alta socio ---	
		
  else //($reAltaSocioConfirmar['codError'] == '00000')
	 {
			//---- Inicio buscar datos socio --------------------------------------------
		 $resDatosSocio = buscarDatosSocio($reAltaSocioConfirmar['CODUSER'],date('Y'));//PARA ENVIAR EMAILS y para bancos y paypal
		 
			//echo "<br><br>3-1 controladorSocios:confirmarAltaSocio:resDatosSocio: ";print_r($resDatosSocio);
			
			if ($resDatosSocio['codError'] !== '00000')
			{/* El socio ya ha confirmado y se le indica por pantalla con el siguiente comentario, pero no habrá recibido el email de confirmación
		   No se enviará email a buscarEmailCoordSecreTesor, pues faltarán datos del socio, tampoco se mostrará forma de pago porque 
					también faltarán datos. Nota: Sería mejor incluir el control en "buscarDatosSocio()" dentro de la función "altaSociosConfirmada" 
					para tratar en el posible error 
				*/
				$datosMensaje['textoComentarios'] = 'Se ha confirmado el alta del socio/a. Pero por un error no se ha enviado al email de socio/a la información de está confirmación del alta.';
																																									
    $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'. modeloSocios.php:buscarDatosSocio(): '.
				                                          $resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje'].$datosMensaje['textoComentarios']);																																									
	   vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
			}	//---- Fin  buscar datos socio --------------------------------------------
			
			else //if ($resDatosSocio['codError'] == '00000')	
			{$textoComentariosEmail ='';	
    //--------------------- Inicio Email a socio -----------------------------			
			 $resEnviarEmailSocio = emailComunicarAltaUsuario($resDatosSocio['valoresCampos']);//envía solo a socio	
		  //echo "<br><br>3-2 controladorSocios:confirmarAltaSocio:reEnviarEmailSocio: ";print_r($reEnviarEmailSocio);
				
				if ($resEnviarEmailSocio['codError'] !== '00000')
				{       
						$textoComentariosEmail = '<br /><br />Por un error no se ha podido enviar el email con la información de esta alta como socio/a.';									
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resEnviarEmailSocio['codError'].": ".$resEnviarEmailSocio['errorMensaje'].$textoComentariosEmail);
				}		
			 //--------------------- Fin Email a socio -------------------------------
				
				//--------- Inicio Email a Coordinador,Secretario,Tesororero agrupacion ----		
				$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($reAltaSocioConfirmar['CODUSER']);
	
			 //echo"<br><br>3-3 controladorSocios:confirmarAltaSocio:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);
				
				if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
				{//$reDatosEmailCoSeTe['textoComentarios'] = 'Se ha confirmado el alta del socio/a. Pero por un error: coordinación y secretaría no han recibido 
		   //                                           el email con la información de esta confirmación del alta. ';
     $textoComentariosEmail .= 'Se ha confirmado el alta del socio/a. Pero por un error: coordinación y secretaría no han recibido 
		                              el email con la información de esta confirmación del alta. ';																																																	
					//$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].$reDatosEmailCoSeTe['textoComentarios']);	
     $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].$textoComentariosEmail);	
										 										 
				}//---- Fin  buscar Email Coordinador,Secretario,Tesororero agrupacion -----	
				
	   else// ($reDatosEmailCoSeTe['codError'] == '00000')
		  {
//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
//****************************************************************************************************************
     $reEnviarEmailCoSeTe = emailAltaSocioCoordSecreTesor($reDatosEmailCoSeTe,$resDatosSocio['valoresCampos']);
					
//FIN COMENTAR ****************************************************************************************************					
//echo"<br><br>3-4 controladorSocios:confirmarAltaSocio:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
	
					if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
					{						
						$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
					}		
				}//--------- Fin Email a Coordinador,Secretario,Tesororero agrupacion -----	
				
    //****************************************************************************************************************	

				/*----------- InicioPreparar Datos Socio Para Pago Cuota con Bancos y PayPal -----------------*/
				
				$resDatosSocio['valoresCampos']['datosFormCuotaSocio'] = $resDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')];				
    
				require_once './modelos/modeloBancos.php';
				$datosSocioBancosPayPal = mPrepararDatosSocioPagoCuotaBancosPayForm($resDatosSocio['valoresCampos']);				
				//Esta función incluye el tratamiento de errores y aquí se muestra lo correcto				
		
			 /*---- Inicio Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php --------
					Esto lo pongo aquí en controlador por que es más fácil el acceso, para hacer pruebas pero
					también puede estar en modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	
				-------------------------------------------------------------------------------------------*/
				/*----Inicio  PARA PRUEBA SIN COBRAR: DESCOMENTAR ----------------------------------------
		   $datosSocioBancosPayPal['business'] ='prueba1@europalaica.com';
				 $datosSocioBancosPayPal['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			  ----- fin PARA PRUEBA SIN COBRAR: DESCOMENTAR ------------------------------------------*/
				
				/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR --------------------------------*/
		  $datosSocioBancosPayPal['business'] = 'tesoreria@europalaica.com'; 			
				$datosSocioBancosPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr';
				/*----**** OJO fin **** REAL PARA COBRAR: DESCOMENTAR -----------------------------------*/			
    
				/*---- Fin Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php ----------*/
				/*-----------Fin Preparar Datos Socio Para Pago Cuota con Bancos y PayPal  --------------------*/		
			
					//echo"<br><br>4-1 controladorSocios:confirmarAltaSocio:reAltaSocioConfirmar: ";print_r($reAltaSocioConfirmar);
					//echo"<br><br>4-3 controladorSocios:confirmarAltaSocio:datosSocioBancosPayPal: ";print_r($datosSocioBancosPayPal);

					require_once './vistas/socios/vPagarCuotaSocioInc.php';	
					/*Aquí es necesario el parámetro "$navegación" aunque es cero (0), está para mantener el formato de parámetros
							de esta función "vPagarCuotaSocioInc" para socio que tienen más roles (coordinacion, presidencia, tesoreria)		
							para la línea enlaces zona superior tipo: >>Entrar>>Menú roles>>Menú socio/a>>Pagar cuota anual 
					*/		
					$navegacion = '';	
					
				 //$reAltaSocioConfirmar['arrMensaje']['textoCabecera'] = $datosMensaje['textoCabecera'];		
	    //vPagarCuotaSocioInc($tituloSeccion,$reAltaSocioConfirmar['arrMensaje'],$datosSocioBancosPayPal,$navegacion);	
					
					$datosMensaje['textoComentarios'] = $reAltaSocioConfirmar['arrMensaje']['textoComentarios']; 
	    vPagarCuotaSocioInc($tituloSeccion,$datosMensaje,$datosSocioBancosPayPal,$navegacion);			
			}//if ($resDatosSocio['codError'] == '00000')							 
		}//else ($reAltaSocioConfirmar['codError'] == '00000') 
 }//isset($_POST['confirmarAltaSocio'])) 
}
/*--------------------------- Fin  confirmarAltaSocio ----------------------------------------*/


/*-------------------- Inicio anularAltaSocioPendienteConfirmar---------------------------------
En esta función se se anula, por el mismo casi socio, el inicio de alta provisonal 
de un socio (pendiente de confirmar), desde el link que recibió al registrase 
como nuevo socio.

Si se anula, se actualizará tabla USUARIO el campo ESTADO= 'ANULADA-SOCITUD-REGISTRO'
y SOCIOSCONFIRMAR poniendo a NULL todos los campos de datos personales 
Se enviará un email al socio comunicándolo.
No envía email, solo muestra la anulación de datos en pantalla.

RECIBE: "codUserEncrip" en el POST 

LLAMADA: controladorSocios.php:confirmarAnularAltaSocio()
vConfirmarSocioInc(), el botón de Anular alta socio

LLAMA: usuariosLibs/encriptar/encriptacionBase64.php:desEncriptarBase64()
controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion(),
modeloSocios.php:anularSocioPendienteConfirmar()
modeloEmail.php:emailErrorWMaster()
vistas/mensajes/vMensajeCabInicialInc.php';

OBSERVACIONES: Probado PHP 7.3.21
2020-05-05:Aquí no necesita cambios para PDO, incluyen las funciones que utiliza 
Añado limpiarVariablesSesion(), por si se ha registrado previamente en ese el navegador y para 
eliminar las posible variables de SESSION y COOKIES
---------------------------------------------------------------------------------------------*/
function anularAltaSocioPendienteConfirmar()
{
	//echo "<br><br>1 controladorSocios:anularAltaSocioPendienteConfirmar:SESSION: ";print_r($_SESSION);  
 //echo "<br><br>2 controladorSocios:anularAltaSocioPendienteConfirmar:POST: ";print_r($_POST);
 //echo "<br><br>0-3 controladorSocios:confirmarAnularAltaSocio:_COOKIE: ";var_dump($_COOKIE);	
	
 if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//por si guardó $_SESSION anterior  
	{  
				require_once './controladores/libs/limpiarVariablesSesion.php';
				limpiarVariablesSesion();//Para limpiar Variables SESSION y COOKIES;
	}		

	require_once './modelos/modeloSocios.php';
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';			 		
	require_once './modelos/modeloEmail.php';
	
	$datosMensaje['textoCabecera'] = 'ANULAR ALTA DEL SOCIO/A PENDIENTE DE CONFIRMAR';		
	$datosMensaje['textoComentarios'] = '<br /><br />Error en el sistema, no se ha podido anular el alta del socio/a pendiente de confirmar. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
	$datosMensaje['textoBoton']    = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton']   = './index.php?controlador=controladorLogin&amp;accion=logOut';	
 $nomScriptFuncionError = ' controladorSocios.php:anularAltaSocioPendienteConfirmar(). Error: ';	
	
	$tituloSeccion = 'Área de Socios/as';
	$enlacesSeccIzda = '';//como aún no es socio no tendrá ningún menú lateral, después será = $_SESSION['vs_enlacesSeccIzda']
	
	if (!isset($_POST['anularAltaSocio']))// intento acceso directo 
	{ vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
	}	
 else //(isset($_POST['anularAltaSocio'])) //siempre entrara por aqui, si no intento directo 		
 {
		require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";  
		$codUserAnular = desEncriptarBase64($_POST['codUserEncrip']);		
		
	 //echo "<br><br>3 controladorSocios:anularAltaSocioPendienteConfirmar:codUserAnular: ";print_r($codUserAnular);
			 
		$reSocioAnular = anularSocioPendienteConfirmar($codUserAnular);//en modeloSocios.php		
		
		//echo "<br><br>4 controladorSocios:anularAltaSocioPendienteConfirmar:reSocioAnular: ";print_r($reSocioAnular);			
  
		if ($reSocioAnular['codError'] !== "00000")
		{
			 $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'modeloSocios.php:anularSocioPendienteConfirmar()'.$reSocioAnular['codError'].": ".$reSocioAnular['errorMensaje']);	
			 vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
		}
  else //($reSocioAnular['codError'] == '00000')
	 { 
	  /* En "$reSocioAnular" está: 
			$reSocioAnular['arrMensaje']['textoCabecera']="Anular alta de socio/a pendiente de confirmar"; 
   $reSocioAnular['arrMensaje']['textoComentarios']="Atendiendo a tu petición, hemos anulado tu solicitud alta de como socio/a 
			de Europa Laica y se han borrado todos tus datos personales de nuestra base de datos.
   Muchas gracias por tu interés en la asociación Europa Laica.
   Si en algún momento, decides hacerte socio/a de Europa Laica de nuevo, tendrás que volver a registrate de nuevo como nuevo/a socio/a
			*/
			$datosMensaje['textoComentarios'] = $reSocioAnular['arrMensaje']['textoComentarios'];
			
			require_once './vistas/mensajes/vMensajeCabSalirInc.php';
			vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);																														
 	}        
 }//(isset(isset($_POST['anularAltaSocio'])) 
}
/*--------------------------- Fin anularAltaSocioPendienteConfirmar --------------------------*/


/*-------------------- Inicio confirmarEmailPassAltaSocioPorGestor -----------------------------
Confirma email y establece su contraseña un socio que ha sido dado de alta por un gestor, 
o bien un gestor (solo presidente,secretaría,tesorería) ha confirmado el alta de un socio 
que estaba pendiente de confirmar y el socio ha recibido un email pidiendo confirmación de 
su email y nueva contraseña.

Mientras ESTADO ='alta-sin-password-gestor' el socio tiene bloqueado su acceso 
a la aplicación de Gestión de Soci@s, y mientras no confirme sigue bloqueado.
Actualiza la tabla CONFIRMAREMAILALTAGESTOR, y USUARIO.ESTADO = 'alta'.

Gestores: presidencia, vice, secretaría, tesorería, puede reenviar nuevos emails
a socio para pedirle confirmar su email.

RECIBE: Como parámetro el $codUserEncriptado, el CODUSER, de la tabla USUARIO, 
que se desencripta
DEVUELVE: un mensaje en pantalla indicado que ya pueden entrar en la aplicación.
													
LLAMADA: Desde el email recibido por el socio	después de darle de alta un gestor
(el email contiene el link a esta función con el parámetro de CODUSER encriptado)

LLAMA: usuariosLibs/encriptar/encriptacionBase64.php:desEncriptarBase64()
controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion(),
modelos/libs/validarCamposUsuarios.php:validarRestaurarPass(),
modeloSocios.php:buscarDatosSocio(),mConfirmarEmailPassAltaGestor(),
vistas/socios/vConfirmarEmailPassAltaSocioPorGestorInc.php,
vMensajeCabSalirInc.php:vMensajeCabSalirInc()

OBSERVACIONES: Probado PHP 7.3.21
2020-05-05:Aquí no necesita cambios para PDO, incluyen las funciones que utiliza 
Añado limpiarVariablesSesion(), por si se ha registrado previamente en ese el navegador y para 
eliminar las posible variables de SESSION y COOKIES
------------------------------------------------------------------------------*/
function confirmarEmailPassAltaSocioPorGestor($codUserEncriptado)
{
	//echo "<br><br>0-1 controladorSocios:confirmarEmailPassAltaSocioPorGestor:codUserEncriptado: ";print_r($codUserEncriptado);
	//echo "<br><br>0-2 controladorSocios:confirmarEmailPassAltaSocioPorGestor:_POST: ";print_r($_POST);
	//echo "<br><br>0-3 controladorSocios:confirmarEmailPassAltaSocioPorGestor:_COOKIE: ";var_dump($_COOKIE);
	
 if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//por si guardó $_SESSION anterior  
	{  
				require_once './controladores/libs/limpiarVariablesSesion.php';
				limpiarVariablesSesion();//Para limpiar Variables SESSION y COOKIES;
	}		

 require_once './modelos/modeloPresCoord.php'; 
	require_once './modelos/modeloEmail.php';		
 require_once './modelos/modeloSocios.php';	
	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";  
	require_once './vistas/socios/vConfirmarEmailPassAltaSocioPorGestorInc.php';
	require_once './vistas/mensajes/vMensajeCabSalirInc.php';

	$datosMensaje['nomScript'] = 'modeloSocios.php';
	$datosMensaje['nomFuncion'] = 'confirmarEmailPassAltaSocioPorGestor';
	$datosMensaje['textoCabecera'] = 'ELEGIR CONTRASEÑA Y CONFIRMAR EMAIL';	
	$datosMensaje['textoComentarios'] = '<br /><br />Error en el sistema, no se han podido confirmar el email del socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
	$datosMensaje['textoBoton']    = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton']   = './index.php?controlador=controladorLogin&amp;accion=logOut';
	$nomScriptFuncionError = ' controladorSocios.php:confirmarEmailPassAltaSocioPorGestor(). Error: ';	
	
	$tituloSeccion = 'Área de Socios/as';
	$enlacesSeccIzda ='';
		
 if (isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
 {//echo "<br><br>3 controladorSocios:confirmarEmailPassAltaSocioPorGestor:POST: ";print_r($_POST);
		
  if (isset($_POST['cancelarCambiarPass']))
  { $datosMensaje['textoComentarios'] = "Has salido sin confirmar tu email ni guardar contraseña";				
				vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);			
		}
		else //isset($_POST['cambiarPass']
		{		
			$codUserDesEncriptado = desEncriptarBase64($codUserEncriptado);//en usuariosLibs/encriptar/encriptacionBase64.php";  			
			//echo "<br><br>4-1 controladorSocios:confirmarEmailPassAltaSocioPorGestor:codUserDesEncriptado: ";print_r($codUserDesEncriptado);
			
		 require_once './modelos/libs/validarCamposUsuarios.php';				
			$resValidarCamposForm = validarRestaurarPass($_POST['datosFormUsuario'],$codUserDesEncriptado);//incluye buscar en tablas USUARIO					
			
			//echo "<br><br>4-2 controladorSocios:confirmarEmailPassAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);echo "<br>";
		
			if ($resValidarCamposForm['codError'] !== '00000')
			{	if ($resValidarCamposForm['codError'] >= '80000')//error lógico en password 
					{	
					 $resValidarCamposForm['datosFormUsuario']['CODUSER'] = $codUserEncriptado;

				   vConfirmarEmailPassAltaSocioPorGestorInc($tituloSeccion,$resValidarCamposForm);//en el cuerpo incluye incluye vContent.php que icluye escribirLinksSeccionIzda($_SESSION['vs_enlacesSeccIzda'])					
					}			
					else //if ($resValidarCamposForm['codError'] < '80000') //error sistema
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);					
							vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);		
					}		
		 }				
			else//$resValidarCamposForm['codError'] == '00000'
			{ 
		  $passNoEncriptada = $resValidarCamposForm['datosFormUsuario']['PASSUSUARIO']['valorCampo'];				
    
				$resConfirmarEmailPass = mConfirmarEmailPassAltaGestor($codUserDesEncriptado,$passNoEncriptada);//en modelos/modeloSocios.php
				
				//echo "<br><br>5-1 controladorSocios:confirmarEmailPassAltaSocioPorGestor:resConfirmarEmailPass: ";print_r($resConfirmarEmailPass);

				if ($resConfirmarEmailPass['codError'] !== '00000')
				{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resConfirmarEmailPass['codError'].": ".$resConfirmarEmailPass['errorMensaje']);
      vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);								
				}
				else 
				{ $datosMensaje['textoComentarios'] = $resConfirmarEmailPass['arrMensaje']['textoComentarios'];//viene de mConfirmarEmailPassAltaGestor()
						
				  vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);								
				}				
	  }//else $resValidarCamposForm['codError'] == '00000'			
		}//else isset($_POST['cambiarPass']		 			  
 }//if (isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass']))	
		
 else//!(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass']))	 
 {
		 $codUserDesEncriptado = desEncriptarBase64($codUserEncriptado);//en usuariosLibs/encriptar/encriptacionBase64.php";  						
		 
			//echo "<br><br>6 controladorSocios:confirmarEmailPassAltaSocioPorGestorl:codUserDesEncriptado:"; print_r($codUserDesEncriptado);

			$resDatosSocio = buscarDatosSocio($codUserDesEncriptado,$anioCuota = "%");//$anioCuota = "%"; en modelos/modeloSocios.php, contiene conexionDB()			
   
			//echo "<br><br>7-1 controladorSocios:confirmarEmailPassAltaSocioPorGestor:resDatosSocio: "; print_r($resDatosSocio);		
			
			$datosSocio['datosFormUsuario']['ESTADO'] = $resDatosSocio['valoresCampos']['datosFormUsuario']['ESTADO']['valorCampo'];
			$datosSocio['datosFormUsuario']['USUARIO'] = $resDatosSocio['valoresCampos']['datosFormUsuario']['USUARIO']['valorCampo'];
			//$datosSocio['datosFormUsuario']['PASSUSUARIO'] = $resDatosSocio['valoresCampos']['datosFormUsuario']['PASSUSUARIO']['valorCampo'];//la que está guardada en la BBDD
			$datosSocio['datosFormMiembro']['APE1'] = $resDatosSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo'];
			$datosSocio['datosFormMiembro']['APE2'] = $resDatosSocio['valoresCampos']['datosFormMiembro']['APE2']['valorCampo'];
			$datosSocio['datosFormMiembro']['NOM'] = $resDatosSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo'];			
   
			//echo "<br><br>7-2 controladorSocios:confirmarEmailPassAltaSocioPorGestor:datosSocio: "; print_r($datosSocio);
	
			if ($resDatosSocio['codError' ] !== '00000')//si  devuelve 0 filas da error: 80001					
			{ 
			  $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);			 
					vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);		
			}       		
			elseif ($datosSocio['datosFormUsuario']['ESTADO'] !== 'alta-sin-password-gestor' && 
			        $datosSocio['datosFormUsuario']['ESTADO'] !== 'alta-sin-password-excel'
			       )//son los estados cuando un gestor realiza el alta, excel fue en la importación al princio de la aplicación
 		{
				switch ($datosSocio['datosFormUsuario']['ESTADO'])					
				{case 'alta': $textoComentarios = "<strong>".$datosSocio['datosFormMiembro']['NOM']." ".$datosSocio['datosFormMiembro']['APE1']."</strong>". 
				                                   ", ya habías confirmado la recepción de este email<br /><br /><br /><br />
																																							Si has olvidado tu nombre de usuario o contraseña, para recuperarlos haz clic en ".
																																						"<a href='./index.php?controlador=controladorLogin&amp;accion=recordarLogin'>¿Recordar contraseña?</a>";
	          break;
					case 'baja':	$textoComentarios = "<strong>".$datosSocio['datosFormMiembro']['NOM']." ".$datosSocio['datosFormMiembro']['APE1']."</strong>". 
					                                 ", anteriormente habías pedido borrar tus datos y ya fueron eliminados todos tus datos personales.
																																						<br /><br /><br />Si quieres hacerte socio/a de Europa Laica de nuevo, 
																																						puedes registrate otra vez haciendo clic en ".
																																						"<a href='./index.php?controlador=controladorSocios&amp;accion=altaSocio'>Nuev@ Soci@</a>";
		          break;
	   }
    $datosMensaje['textoComentarios'] = $textoComentarios;				 
				//echo "<br><br>8-1 controladorSocios:confirmarEmailPassAltaSocioPorGestor:datosSocio: ";print_r($datosSocio);		
				
				vMensajeCabSalirInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);						
  	}	
			else //$datosSocio['datosFormUsuario']['ESTADO'] !== 'alta-sin-password-gestor' || $datosUsuario['resultadoFilas']['ESTADO']=='alta-sin-password-excel')
	  {
				$datosSocio['datosFormUsuario']['CODUSER'] = $codUserEncriptado;//para enviarlo como parámetro	
				$datosSocio['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'] = '';
		  $datosSocio['datosFormUsuario']['PASSUSUARIO']['valorCampo'] = "";
			 $datosSocio['datosFormUsuario']['PASSUSUARIO']['codError'] = '00000';
			 $datosSocio['datosFormUsuario']['PASSUSUARIO']['errorMensaje'] = '';
			 $datosSocio['datosFormUsuario']['RPASSUSUARIO']['valorCampo'] = "";
			 $datosSocio['datosFormUsuario']['RPASSUSUARIO']['codError'] = '00000';
			 $datosSocio['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'] = '';				

				//echo "<br><br>8-2 controladorSocios:confirmarEmailPassAltaSocioPorGestor:datosSocio: ";print_r($datosSocio);
			
				vConfirmarEmailPassAltaSocioPorGestorInc($tituloSeccion,$datosSocio);//en el cuerpo incluye incluye vContent.php que incluye escribirLinksSeccionIzda($_SESSION['vs_enlacesSeccIzda'])					
			}
 }//else !(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 

}
/*--------------------------- Fin confirmarEmailPassAltaSocioPorGestor-------------------------*/

/*-------------------------------- Inicio cambiarPassSocio  ------------------------------------
Se llama desde el menú izdo de socios -Cambiar contrasaña- 
Utiliza $_SESSION['vs_CODUSER'] para poder grabar la nueva contraseña. 
Sirve para socios ya de alta.

LLAMADA: desde el menú izdo de socios -Cambiar contrasaña-
LLAMA:
modelos/libs/validarCamposUsuarios.php:validarCambiarPass()
modeloUsuarios.php:actualizarPass()
modelos/modeloEmail.php:emailErrorWMaster()
vistas/login/vCambiarPassInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php

OBSERVACIONES:
2020-07-03: No es necesario PDO7, lo incluyen internamente algunas de las 
que aquí son llamadas. 
Traslado desde controladorLogin.php:cambiarPassUser() a 
controladorSocios.php:cambiarPassSocio(), para facilitar sección de navegación
en caso de que el socio sea también gestor.
------------------------------------------------------------------------------*/
function cambiarPassSocio() 
{				
	//echo "<br><br>0-1 controladorSocios:cambiarPassSocio:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 controladorSocios:cambiarPassSocio:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
	{	header("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
	}
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
 {				
		require_once './vistas/socios/vCambiarPassSocioInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "CAMBIAR CONTRASEÑA";  
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema informático al 'Cambiar la contraseña'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorSocios&amp;accion=logOut';
																																							
		$nomScriptFuncionError = '  controladorSocios.php:cambiarPassSocio(). Error: ';
	 $tituloSeccion = 'Área de Socios/as';
  $enlacesSeccIzda = "";				

		//------------ inicio navegación para socios gestores CODROL >2 ---------------	
		if (isset($_SESSION['vs_autentificadoGestor']))
		{	
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio")			
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=controladorSocios&accion=cambiarPassSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Cambiar contraseña<br />";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
		}
		else
		{
			$navegacion = "";
		}		
		//echo "<br><br>1-0 controladorSocios:cambiarPassSocio:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		//------------ fin navegación -------------------------------------------------	
		
		if (isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
		{
					//echo "<br><br>1-1 controladorSocios:cambiarPassSocio:_SESSION: ";print_r($_SESSION);  
					//echo "<br><br>1-2 controladorSocios:cambiarPassSocio:_POST: ";print_r($_POST);
					
					if (isset($_POST['cancelarCambiarPass'])) 
					{
								$datosMensaje['textoComentarios'] = "Has salido sin modificar la contraseña";							
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					} 
					else //isset($_POST['cambiarPass']
					{
								require_once './modelos/libs/validarCamposUsuarios.php';
								$resValidarCamposForm = validarCambiarPass($_SESSION['vs_CODUSER'], $_POST['datosFormUsuario']);
								
								//llama modeloUsuarios.php:validarPass(), valida formatos permitidos, 
								//y busca en tabla USUARIO para validar la contraseña actual antes de permitir poner la nueva
								
								//echo "<br><br>2 controladorSocios:cambiarPassSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);

								if ($resValidarCamposForm['codError'] !== '00000') 
								{
											if ($resValidarCamposForm['codError'] >= '80000')//error lógico 
											{
														vCambiarPassSocioInc($tituloSeccion, $resValidarCamposForm,$navegacion);
											} 
											else //if ($resValidarCamposForm['codError'] < '80000') //error sistema
											{  
														$resValidarCamposForm['textoComentarios'] = ": Error en validarCamposUsuarios.php:validarCambiarPass()";																																																										
														$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['textoComentarios'].': '.
														                                          $resValidarCamposForm['codError']. ": " . $resValidarCamposForm['errorMensaje']);
														vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
											}
								}//if ($resValidarCamposForm['codError'] !== '00000')						
							
							 else// $resValidarCamposForm['codError'] == '00000' 
								{  
								   /*------------ Incio actualizarPass() -----------------------------*/ 
											
											$resActPass = actualizarPass('USUARIO', $_SESSION['vs_CODUSER'], $resValidarCamposForm);//en modeloUsuarios.php, inserta en tabla ERRORES
											
											//echo "<br><br>3 controladorSocios:cambiarPassSocio:resActPass: ";print_r($resActPass);	

											if ($resActPass['codError'] !== "00000") 
											{  
										    $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActPass['codError']. ": ".$resActPass['errorMensaje']);
											}
											elseif ($resActPass['numFilas'] === 0)	
           { 	
											   $datosMensaje['textoComentarios'] = "<strong>Aviso:</strong> has dejado la misma contraseña que anteriormente tenías. 
              <br /><br /><br />Deberás utilizarla la próxima vez que quieras entrar en la aplicación de gestión de usuarios de Europa Laica
														<br /><br />Si quieres cambiarla, debes hacerlo en la opción -Cambiar contraseña-";
											}
											else //($resActPass['codError'] == '00000')
											{  
											   $datosMensaje['textoComentarios'] = $resActPass['arrMensaje']['textoComentarios'];
											}
											
											vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
											
           /*------------ Fin actualizarPass() -------------------------------*/ 
											
								}//elseif ($resValidarCamposForm['codError'] == '00000')
					}//else isset($_POST['cambiarPass']
		}//(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
		
		else //!(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass']))//al entrar  
		{ 
			//echo "<br><br>4-1 controladorSocios:cambiarPassSocio:_ SESSION: ";print_r($_SESSION); 
			//echo "<br><br>4-2 controladorSocios:cambiarPassSocio:_POST: ";print_r($_POST);				
			
				/*------------ Iniciar datos para formulario ----------------------------*/ 
				
				$resActPass['datosFormUsuario']['actPASSUSUARIO']['valorCampo'] = "";
				$resActPass['datosFormUsuario']['actPASSUSUARIO']['codError'] = '00000';
				$resActPass['datosFormUsuario']['actPASSUSUARIO']['errorMensaje'] = '';

				$resActPass['datosFormUsuario']['PASSUSUARIO']['valorCampo'] = "";
				$resActPass['datosFormUsuario']['PASSUSUARIO']['codError'] = '00000';
				$resActPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'] = '';
				$resActPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo'] = "";
				$resActPass['datosFormUsuario']['RPASSUSUARIO']['codError'] = '00000';
				$resActPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'] = '';
				
				//echo "<br><br>4-3 controladorSocios:cambiarPassSocio:resDatosSocioActualizar: ";print_r($resActPass);
				
				/*------------ Fin inciar datos para formulario -------------------------*/ 
				
				/* NOTA: CREO QUE YA NO ES NECESARIO CON OPCIÓN AÑADIDA cAdmin.php:modoMantenimientoAdmin() */
				
				/* ----INICIO DESCOMENTAR EN CASO DE MANTENIMIENTO DEL SITIO Y  COMENTAR PARA EXPLOTACIÓN ---  
						require_once './vistas/mensajes/vMantenimientoInc.php';//ACTIVAR EN ETAPAS DE MANTENIMIENTO
						vMantenimientoInc ("","");
						//----FIN DESCOMENTAR EN CASO DE MANTENIMIENTO DEL SITIO Y  COMENTAR PARA EXPLOTACIÓN -----*/

				vCambiarPassSocioInc($tituloSeccion,$resActPass,$navegacion);
		}//!(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
 }//	else if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')		
}
/*-------------------------- Fin cambiarPassSocio --------------------------------------------*/


/*------------------------------- Inicio mostrarDatosSocio -------------------------------------
Se muestran los datos de un socio al propio socio (incluidos los propios gestores), 
sin permitir modificaciones.
La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, si no es gestor no se muestra ninguna barra.

LLAMADA: desde menú lateral vista socios.
LLAMA: modeloSocios.php:buscarDatosSocio(),modeloEmail.php:emailErrorWMaster()
require_once './controladores/libs/cNavegaHistoria.php', 
para historia navegación de la fila superior de link en el caso de que el socio sea gestor

OBSERVACIONES: Probado PHP 7.3.21
2020-05-05:Aquí no necesita cambios para PDO, incluye las funciones que utiliza 
----------------------------------------------------------------------------------------------*/
function mostrarDatosSocio()
{
		/*
		echo "<br><br>0-1 controladorSocios:mostrarDatosSocio:_GET: "; print_r($_GET);
		echo "<br><br>0-2 controladorSocios:mostrarDatosSocio:_REQUEST: "; print_r($_REQUEST);
		echo "<br><br>0-3 controladorSocios:mostrarDatosSocio:POST: ";print_r($_POST);	 
		echo "<br><br>0-4 controladorSocios:mostrarDatosSocio:SESSION: ";print_r($_SESSION);
	*/	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
	{		
		require_once './modelos/modeloSocios.php';
		require_once './vistas/socios/vMostrarDatosSocioInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['nomScript'] = 'modeloSocios.php';
		$datosMensaje['nomFuncion'] = 'mostrarDatosSocio';
		$datosMensaje['textoCabecera'] = 'MOSTRAR DATOS SOCIO/A';	
		$datosMensaje['textoComentarios'] = '<br /><br />Error en el sistema, no se han podido mostrar datos del socio/a. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
																																							
		$nomScriptFuncionError = ' controladorSocios.php:mostrarDatosSocio(). Error: ';	
		
		$tituloSeccion = 'Área de Socios/as';

		//------------ inicio navegación para socios gestores CODROL >2 ---------------	
		if (isset($_SESSION['vs_autentificadoGestor']))
		{	
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio")			
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=controladorSocios&accion=mostrarDatosSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar datos socio/a<br />";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
		}
		else
		{
			$navegacion = "";
		}		
		//echo "<br><br>1 controladorSocios:mostrarDatosSocios:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		//------------ fin navegación -------------------------------------------------	

		$anioCuota = '%';//se necesita que sean todos para ver la cuota actual y la anterior
		$usuarioBuscado = $_SESSION['vs_CODUSER'];		

		$resDatosSocio = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php
			
		//echo "<br><br>2 controladorSocios:mostrarDatosSocio:resDatosSocio: "; print_r($resDatosSocio); 
		
		if ($resDatosSocio['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'modeloSocios.php:buscarDatosSocio(): '.$usuarioBuscado.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);		
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}      
		else //$resDatosSocio['codError']=='00000'
		{	
				//echo "<br><br>3 controladorSocios:mostrarDatosSocio:resDatosSocio: "; print_r($resDatosSocio); 
				
				vMostrarDatosSocioInc($tituloSeccion,$resDatosSocio,$navegacion);	
				
		} //else $resDatosSocio['codError']=='00000'  

	} //else  if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
}
/*--------------------------- Fin mostrarDatosSocio ------------------------------------------*/

/*------------------------------- Inicio actualizarSocio ---------------------------------------
Se actualizan los datos de un socio por el propio socio, varias tablas  (incluidos los propios 
gestores como socios), 

La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, si no es gestor no se muestra ninguna barra.

RECIBE:$_SESSION, $_POST[], $_POST['campoHide'] este contiene datos 
de require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php';	

LLAMA: modeloSocios.php:buscarDatosSocio(),actualizarDatosSocio()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario
modelos/libs/validarCamposSocio.php:validarCamposActualizarSocio(T);							
controladores/libs/inicializaCamposActualizarSocio.php
(contiene mucho código, que acaso se pueda simplificar )
controladores/libs/arrayEnviaRecibeUrl.php:arrayRecibeUrl(),arrayEnviaUrl()
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php: navegación fila superior links
vistas/socios/vActualizarSocioInc.php	

LLAMADA: desde menú lateral de socios

OBSERVACIONES: Probado PHP 7.3.21
2020-05-05:Aquí no necesita cambios para PDO, incluye las funciones que utiliza				

NOTA:similar a cCoordinador.php:mostrarSociosCoord(),cPresidente.php:actualizarSocioPres(),
cPresidente.php:actualizarSocioTes(),

arrayEnviaUrl() y arrayRecibeUrl($_POST['campoHide']) envía y recibe un array a post con 
['anteriorUSUARIO']['anteriorEMAIL']['anteriorCODPAISDOC']['anteriorTIPODOCUMENTOMIEMBRO']['anteriorNUMDOCUMENTOMIEMBRO']
----------------------------------------------------------------------------------------------*/
function actualizarSocio()
{
	//echo "<br><br>0-1 controladorSocios:actualizarSocio:_POST: ";print_r($_POST);
	//echo "<br><br>0-2 controladorSocios:actualizarSocio:_SESSION: ";print_r($_SESSION);
		
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
	{		
		require_once './controladores/libs/arrayEnviaRecibeUrl.php'; 
		require_once './modelos/libs/arrayParValor.php';//añadido nuevo 2020-04-13	
		require_once './modelos/modeloSocios.php';
		require_once './vistas/socios/vActualizarSocioInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['nomScript'] = 'modeloSocios.php';
		$datosMensaje['nomFuncion'] = 'actualizarSocio';
		$datosMensaje['textoCabecera'] = "ACTUALIZAR DATOS DEL SOCIO/A"; 
		$datosMensaje['textoComentarios'] = '<br /><br />Error en el sistema, no se han podido actualizar datos del socio/a. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org';	
																																							
		$nomScriptFuncionError = ' controladorSocios.php:actualizarSocio(). Error: ';		
		$tituloSeccion = 'Área de Socios/as';

		//------------ inicio navegación para socios gestores CODROL >2 ---------------	
		if (isset($_SESSION['vs_autentificadoGestor']))
		{		
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio")		
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=controladorSocios&accion=actualizarSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar datos socio/a<br />";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		}
		else
		{
			$navegacion = '';
		}
		//echo "<br><br>2 controladorSocios:actualizarSocio:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);				
		
		//---- fin navegación para socios gestores CODROL >2 --------------------------
			
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			if (isset($_POST['salirSinActualizar']))
			{
				$datosMensaje['textoComentarios'] = "No se han modificado los datos del socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['comprobarYactualizar']))
			{
				$_POST['campoHide'] = arrayRecibeUrl($_POST['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php, convierte un string (obtenido con arrayEnviaUrl) en array	 
				//echo "<br><br>3 controladorSocios:actualizarSocio:POST: ";print_r($_POST);

				require_once './modelos/libs/validarCamposSocio.php';		
				$resValidarCamposForm = validarCamposActualizarSocio($_POST);			
				//echo "<br><br>4-1 controladorSocio:actualizarSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				if (($resValidarCamposForm['codError'] !== '00000') && ($resValidarCamposForm['codError'] > '80000'))//error lógico para corregir
				{	
					$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['campoActualizar']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resValidarCamposForm['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],			
																																																	$resValidarCamposForm['campoActualizar']['datosFormSocio']['CODAGRUPACION']['valorCampo']);			
					if ($parValorCombo['codError'] !== '00000') 
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);	
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}	
					else //$parValorCombo['codError'] == '00000'
					{ 
							$resValidarCamposForm['campoHide'] = arrayEnviaUrl($resValidarCamposForm['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php,	convierte un array en string	 
							//echo "<br><br>4-2 controladorSocio:actualizarSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
								
							vActualizarSocioInc($tituloSeccion,$resValidarCamposForm,$parValorCombo,$navegacion);// para corregir	datos	
					}			
				}// if (($resValidarCamposForm['codError']!=='00000') && ($resValidarCamposForm['codError']>'80000'))													
				else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
				{			
					$resActDatosSocio = actualizarDatosSocio($resValidarCamposForm['campoActualizar']);// en  modeloSocios.php
					
					//echo "<br><br>5 controladorSocios:actualizarDatosSocio:resActDatosSocio: ";print_r($resActDatosSocio);		
								
					if ($resActDatosSocio['codError'] !== "00000")
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloSocios.php:actualizarDatosSocio(): '.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
					}		
					else //($resActDatosSocio['codError'] == '00000')
					{	$datosMensaje['textoComentarios'] = $resActDatosSocio['arrMensaje']['textoComentarios'];				 
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}	        
				}//else $resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
			}//else isset($_POST['comprobarYactualizar'])			  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
			
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			//echo "<br><br>6 controladorSocios:actualizarSocio:SESSION: ";print_r($_SESSION);       
			$anioCuota = '%';
			$usuarioBuscado = $_SESSION['vs_CODUSER'];
			
			$resDatosSocioActualizar = buscarDatosSocio($usuarioBuscado,$anioCuota);
				
			//echo "<br><br>7 controladorSocio:actualizarSocios:resDatosSocioActualizar: ";print_r($resDatosSocioActualizar);
		
			if ($resDatosSocioActualizar['codError'] !== '00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloSocios.php:buscarDatosSocio(): '.$resDatosSocioActualizar['codError'].": ".$resDatosSocioActualizar['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
			}      
			else //$resDatosSocioActualizar['codError']=='00000'
			{$parValorCombo = parValoresRegistrarUsuario($resDatosSocioActualizar['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																$resDatosSocioActualizar['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																$resDatosSocioActualizar['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo']);				
					
				if ($parValorCombo['codError'] !== '00000') 
				{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloSocios.php:parValoresRegistrarUsuario(): '.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}	
				else //$parValorCombo['codError']=='00000'
				{
					//require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php';			//usada anteriormente			
					//$datMostrarActualizarCuotaSocio = prepMostrarActualizarCuotaSocio($resDatosSocioActualizar);
					require_once './controladores/libs/inicializaCamposActualizarSocio.php';
					$datMostrarActualizarCuotaSocio = inicializaCamposActualizarSocio($resDatosSocioActualizar);
					
					//echo "<br><br>8 controladorSocio:actualizarSocios:datMostrarActualizarCuotaSocio: ";	print_r($datMostrarActualizarCuotaSocio);
									
					if ($datMostrarActualizarCuotaSocio['codError'] !== '00000')
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloSocios.php:prepMostrarActualizarCuotaSocio(): '.
																																																	$datMostrarActualizarCuotaSocio['codError'].": ".$datMostrarActualizarCuotaSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);																																																	
					}	
					else //$datMostrarActualizarCuotaSocio['codError']=='00000'
					{
						$datosSocioFormActualizar['campoActualizar'] = $datMostrarActualizarCuotaSocio['campoActualizar'];
						$datosSocioFormActualizar['campoVerAnioActual'] = $datMostrarActualizarCuotaSocio['cuotaSocioAnioActual'];
						$datosSocioFormActualizar['campoHide'] = arrayEnviaUrl($datMostrarActualizarCuotaSocio['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php
						//arrayEnviaUrl() recibe un array, lo prepara y convierte en un string serializado, para enviarlo por URL, y después con arrayRecibeUrl() volverlo al array original						
						
						//echo "<br><br>9 controladorSocio:actualizarSocios:datosSocioFormActualizar: ";	print_r($datosSocioFormActualizar);
						
						vActualizarSocioInc($tituloSeccion,$datosSocioFormActualizar,$parValorCombo,$navegacion);	//la primera vez entra por aquí						
					}	
				}//else $parValorCombo['codError']=='00000'
			}//else $resDatosSocioActualizar['codError']=='00000'   
		}//else (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar'])) 
 }//else if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI') 
}
/*--------------------------- Fin actualizarSocio --------------------------------------------*/

/*------------------------------- Inicio eliminarSocio -----------------------------------------
Se eliminan datos identificativos del socio, se ponen a NULL (privacidad datos) y se insertan 
algunos datos en la tabla "MIEMBROELIMINADO5ANIOS", para mantener durante 5 años ciertos datos, 
que podrían ser de interés para justificación fiscal de ingresos de cuotas.

En caso de que hubiese un archivo con la firma de un socio debido a lata por gestor, también
se eliminaría el archivo del servidor.
Se envía email al usuario, para comunicarle que ha sido dado de baja, (envía el nombre y ape1) 
y también a presidencia, secretaría, tesorería y al correspondiente coordinador/a

La parte de navegación se añade, para que cuando un socio gestor CODROL >2 (presidente, coordinador,
secretaria, tesoreria, etc....) accede a sus propios datos mantenga la navegación,
si no es gestor no se muestra ninguna barra.

Se borran las variables SESSION Y COOKIES con la función "limpiarVariablesSesion()", para evitar 
posibles mensaje de error si el socio pulsa atrás en el navegador o al hacer F5.

RECIBE : datos socio Baja (array en POST)

LLAMADA: desde controladorSocios:eliminarSocio(),mediante formulario:vEliminarSocioInc()  

LLAMA: 
modelosSocios.php:eliminarDatosSocios(),buscarDatosSocio(),buscarEmailCoordSecreTesor()
modeloEmail.php:emailBajaUsuario(),emailBajaSocioCoordSecreTesor(),emailErrorWMaster()
modelos/libs/validarCamposSocio.php:validarEliminarSocio()
controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion(); 	
									
OBSERVACIONES: 
2020-05-05: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza 						
----------------------------------------------------------------------------------------------*/
function eliminarSocio()
{
	//echo "<br /><br />0-1 controladorSocios:eliminarSocio:_SESSION: ";print_r($_SESSION);  
 //echo "<br /><br />0-2 controladorSocios:eliminarSocio:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
	{		
		require_once './modelos/modeloSocios.php';
		require_once './vistas/socios/vEliminarSocioInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'DARSE DE BAJA COMO SOCIA/O';				
		$datosMensaje['textoComentarios'] = '<br /><br />Error en el sistema, no se ha podido dar de baja socio/a. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email de contacto indicando el problema y tus datos a adminusers@europalaica.org';	
		$datosMensaje['textoBoton']    = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton']   = './index.php?controlador=controladorLogin&amp;accion=logOut';	
		
		$nomScriptFuncionError = ' controladorSocios.php:eliminarSocio(). Error: ';		
		$tituloSeccion = 'Área de Socios/as';		
		
		//------------ inicio navegación para socios gestores CODROL >2 ---------------	
		if (isset($_SESSION['vs_autentificadoGestor']))
		{
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];		
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio")		
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;}
			
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=eliminarSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Dar de baja socio/a<br />";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		}
		else
		{
			$navegacion = '';
		}
		//echo "<br /><br />1 controladorSocios:eliminarSocio:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			

		//---- fin navegación para socios gestores CODROL >2 --------------------------	

		if (isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
		{
			if (isset($_POST['SiEliminar']))
			{
				$datosSocio = $_POST;		
				$datosSocio['datosFormUsuario']['CODUSER'] = $_SESSION['vs_CODUSER'];

				require_once './modelos/libs/validarCamposSocio.php';
				$resValidarCamposForm = validarEliminarSocio($_POST);
				
				//echo "<br /><br />2-1 controladorSocios:eliminarSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				if ($resValidarCamposForm['codError'] !=='00000')
				{	
						if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
						{
								//echo "<br><br>2-2 controladorSocios:eliminarSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
								
								require_once './vistas/socios/vEliminarSocioInc.php';										
								vEliminarSocioInc($tituloSeccion,$resValidarCamposForm,$navegacion);						
						}			
						else //$resValidarCamposForm['codError']< '80000') = error sistema
						{
							//echo "<br /><br />2-3 controladorSocios:eliminarSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
							vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda']);						
						}
				}	
				else //$resValidarCamposForm['codError']=='00000' = NO HAY ERROR
				{
						//echo "<br /><br />3-1 controladorSocios:eliminarSocio:datosSocio: ";print_r($datosSocio);

						$reEliminarSocio = eliminarDatosSocios($datosSocio);//en modeloSocios.php
						
						//echo "<br><br>3-2 controladorSocios:eliminarSocio:reEliminarSocio: "; print_r($reEliminarSocio);			

						if ($reEliminarSocio['codError'] !== "00000")
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloSocios.php:eliminarDatosSocios(): '.$reEliminarSocio['codError'].": ".$reEliminarSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						}
						else //($reEliminarSocio['codError'] == '00000')
						{//echo "<br /><br />4-1 controladorSocios:eliminarSocio:sesion:";print_r($_SESSION);						
							//echo "<br /><br />5-1 controladorSocios:eliminarSocio:datosSocio['datosFormMiembro']: ";print_r($datosSocio['datosFormMiembro']);
							
							if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	//si falta, o error
							{							  
									$resultEnviarEmail = emailBajaUsuario($datosSocio['datosFormMiembro']);//email a socio		
							}
							
							require_once './controladores/libs/limpiarVariablesSesion.php';//borra SESSION Y COOKIES
							limpiarVariablesSesion(); 							
							
							//echo "<br><br>5-2 controladorSocios:eliminarSocio:_SESSION: ";print_r($_SESSION);							
							
							$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($datosSocio['datosFormUsuario']['CODUSER']);//en modeloSocios.php

							//echo "<br><br>6-1 controladorSocios:eliminarSocio:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);
							
							if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
						 {$textoComentariosEmail = "<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email para informar de esta baja";																															
					 		$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);    
				 		}							
					 	else// ($reDatosEmailCoSeTe['codError'] == '00000')
							{				 
								if (isset($datosSocio['datosFormSocio']['OBSERVACIONES']) && !empty($datosSocio['datosFormSocio']['OBSERVACIONES']))
								{$datosSocio['datosFormSocio']['OBSERVACIONES'] = "Observaciones del socio/a: ".$datosSocio['datosFormSocio']['OBSERVACIONES'];
								}	
								//se añade este texto a los comentarios que ya vienen de modeloSocios.php:eliminarDatosSocios()
								$reEliminarSocio['arrMensaje']['textoComentarios'] .="<br /><br /><br /><br />Se ha enviado un email a Presidencia, Secretaría, Tesorería y Coordinación de la agrupación para informar de esta baja";
				
			//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
			//****************************************************************************************************************
			     $reEnviarEmailCoSeTe = emailBajaSocioCoordSecreTesor($reDatosEmailCoSeTe,$datosSocio);
			//FIN COMENTAR ****************************************************************************************************************
								//echo "<br><br>6-2 controladorSocios:eliminarSocio:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
								
								if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
								{$textoComentariosEmail = "<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el para informar de esta baja";					
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
								}								
							}															
							//echo "<br><br>6-3 controladorSocios:eliminarSocio:reEliminarSocio['arrMensaje']: ";print_r($reEliminarSocio['arrMensaje']);	
							
							$datosMensaje['textoComentarios'] = $reEliminarSocio['arrMensaje']['textoComentarios'];//para mostrar en pantalla							
							$enlacesSeccIzda ="";
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda,$navegacion);										
							
						}//else $reEliminarSocio['codError'] == '00000'
				}//else $resValidarCamposForm['codError']=='00000' = NO HAY ERROR		
			}//if (isset($_POST['SiEliminar']))     
			else //!(isset($_POST['SiEliminar'])) == isset($_POST['NoEliminar'])) 
			{	
				$datosMensaje['textoComentarios'] = "Has elegido la opción de no darte de baja como socio/a, y no se ha realizado tu baja ";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}    
		}//if (isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
			
		else //!(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
		{		
			$anioCuota = '%';
			$usuarioBuscado = $_SESSION['vs_CODUSER'];
			
			$datSocioEliminar = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php
		
	  //echo "<br><br>7 controladorSocios:eliminarSocio:datSocioEliminar: "; print_r($datSocioEliminar); 
										
			if ($datSocioEliminar['codError'] !== '00000')
			{
			 $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioEliminar['codError'].": ".$datSocioEliminar['errorMensaje']);	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioEliminar['codError']=='00000'
			{//echo "<br><br>8 controladorSocios:eliminarSocio:datSocioEliminar:";print_r($datSocioEliminar);	

				vEliminarSocioInc($tituloSeccion,$datSocioEliminar,$navegacion);			
			}   
		}//else !(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
	}//else  if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')		
}
/*--------------------------- Fin eliminarSocio ----------------------------------------------*/



// ******************************INICIO PAGAR CUOTA  *******************************************

/*------------------------------- Inicio pagarCuotaSocio ---------------------------------------
Se buscan los datos del socio, y los datos de las cuentas de cobro de la agrupación del socio.
En modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm se busca en la tabla 
tabla de AGRUPACIONTERRITORIAL y se prepara la cuenta IBAN de la agrupación para después mostrar
en el formulario.

En el formulario, "si la cuota anual NO está pagada", se muestran la cuota del socio,los datos 
bancarios (si los hay), y otra información del socio y se le indica los modos de pagar la cuota anual:
- Se muestran las cuentas bancarias de donde se cobran a las distintas agrupaciones, 
se leen de las tablas de AGRUPACIONTERRITORIAL (a fecha 01_08_2021 todas menos Asturias
están centralizadas y comparten la misma cuenta bancaria, Asturias muestra su cuenta) 
- Además hay un botón de enlace a PayPal (a fecha 01_08_2021 todas menos Asturias), 
donde ya se incluye la cantidad a pagar y demás datos del socio. 

Si la cuota anual ya está pagada se indica y se ofrece la opción de hacer una donación		

NOTA: En la función modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	
se obtienen las cuentas bancarias de de la AGRUPACION y el script de PayPal personalizado 
con botón para el cobro de la cuota para ese socio. Estos datos se
pasan a vPagarCuotaSocioInc.php
									
En esta función del controlador se puede asignar los valores:									
REAL COBRO: 
$datosSocioPayPal['business'] ='tesoreria@europalaica.com'				
$datosSocioPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr'

PRUEBA CON SANDOX NO COBRA: 
$datosSocioPayPal['business'] ='prueba1@europalaica.com'
$datosSocioPayPal['action']='https://www.sandbox.paypal.com/cgi-bin/webscr'

LLAMADA: desde menú lateral socios,  y utiliza $_SESSION['vs_CODUSER'] para 
buscar los datos del socio.

LLAMA: require_once './controladores/libs/cNavegaHistoria.php',
modeloSocios.php:buscarDatosSocio(),modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()
vistas/socios/vPagarCuotaSocioInc.php:

OBSERVACIONES:
2020-04-14: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza.
NOTA: ya no se usa: constBancosPagoCuotas.php ahora se utiliza, modeloBancos.php	y se usa BBDD 
para buscar la cuenta de pago correspondiente a cada agrupación territorial, actualmente solo  
Asturias Laica tiene cuenta propia para cuotas (en este caso no se mostrará botón de PayPal)
-----------------------------------------------------------------------------------------------*/
function pagarCuotaSocio()
{	
	//echo "<br><br>0-1 controladorSocios:pagarCuotaSocio:SESSION:";print_r($_SESSION);	
	//echo "<br><br>0-2 controladorSocios:pagarCuotaSocio:_POST:"; print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");  
 }
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
	{
		//require_once './constantes/constBancosPagoCuotas.php';Actualmente no se utiliza, se usa BBDD y modeloBancos.php
		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "PAGAR CUOTA ANUAL DE SOCIO/A";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al intentar pagar cuota anual. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$nomScriptFuncionError = ' controladorSocios.php:pagarCuotaSocio(). Error: ';		
		
		$tituloSeccion = 'Área de Socios/as';
		
		$arrayParamMensaje['arrMensaje']['textoCabecera'] = 'PAGAR CUOTA ANUAL DEL SOCIO/A';		

		//------------ inicio navegación para socios gestores CODROL >2 ---------------
		if (isset($_SESSION['vs_autentificadoGestor']))
		{	
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=controladorSocios&accion=menuGralSocio" 
			/* || $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas" */)			
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] ="index.php?controlador=controladorSocios&accion=pagarCuotaSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Pagar cuota anual";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
		}
		else
		{$navegacion = '';
		} 
		//echo "<br><br>1 controladorSocios:pagarCuotaSocio:navegacion:";print_r($navegacion);
		//------------ fin navegación -------------------------------------------------
		
		//----Inicio  buscar datos socio ----------------------------------------------		
		$usuarioBuscado = $_SESSION['vs_CODUSER'];
		
		$anioCuota = date('Y');//Para que busque el estado de cuotas del año actual		
		
		require_once './modelos/modeloSocios.php';
		$resDatosSocio = buscarDatosSocio($usuarioBuscado,$anioCuota);/*incluye GESTIONCUOTAS y bancos de la AGRUPACIÓN */
		
		//echo "<br><br>2 controladorSocios:pagarCuotaSocio:resDatosSocio:"; print_r($resDatosSocio); 

		if ($resDatosSocio['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);		 
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		} 
		//----Fin  buscar datos socio -------------------------------------------------	    
		else //$resDatosSocio['codError']=='00000'
		{	 
			/*--------- Inicio Preparar Datos Socio Para Pago Cuota con Bancos y Pay ----------------*/	
			
			$arrayParamMensaje['arrMensaje']['textoComentarios'] =	"Cuota anual del socio/a año: ".date('Y');					
										
			$resDatosSocio['valoresCampos']['datosFormCuotaSocio'] = $resDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')];		
			
			require_once './modelos/modeloBancos.php';
			$datosSocioBancosPayPal = mPrepararDatosSocioPagoCuotaBancosPayForm($resDatosSocio['valoresCampos']);
			//devuelve cuentas bancarias EL y './vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php con la parte de PayPal para mostrar en vPagarCuotaSocioIn()
			
			/*---- Inicio Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php --------
				Esto lo pongo aquí en controlador por que es más fácil el acceso, para hacer pruebas pero
				también puede estar en modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	
			-------------------------------------------------------------------------------------------*/		
			/*---------- Inicio  PARA PRUEBA SIN COBRAR: DESCOMENTAR -------------------------------
				$datosSocioBancosPayPal['business'] ='prueba1@europalaica.com';
				$datosSocioBancosPayPal['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			----- fin PARA PRUEBA SIN COBRAR: DESCOMENTAR -----------------------------------------*/
			
			/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR ------------------------------*/
			$datosSocioBancosPayPal['business'] = 'tesoreria@europalaica.com';	
			$datosSocioBancosPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr';
			/*----**** OJO fin **** REAL PARA COBRAR: DESCOMENTAR ---------------------------------*/		
			
			/*---- Fin Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php ---------*/
			/*----------- Fin Preparar Datos Socio Para Pago Cuota con Bancos y Pay --------------------*/
			
			//echo "<br><br>3 controladorSocios:pagarCuotaSocio:datosSocioBancosPayPal: "; print_r($datosSocioBancosPayPal);		
			//echo "<br><br>4 controladorSocios:pagarCuotaSocio:arrayParamMensaje: "; print_r($arrayParamMensaje);
				
			require_once './vistas/socios/vPagarCuotaSocioInc.php'; 			 
			vPagarCuotaSocioInc($tituloSeccion,$arrayParamMensaje['arrMensaje'],$datosSocioBancosPayPal,$navegacion);

		}//else $resDatosSocio['codError']=='00000' 
 }//else if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI') 
}
/*--------------------------- Fin pagarCuotaSocio----------------------------------------------*/


/*---- Inicio pagarCuotaSocioSinCC (Se llama solo desde email tesorería) -----------------------

A esta función se LLEGA DESDE EL EMAIL RECIBIDO POR EL SOCIO y ENVIADO POR "TESORERÍA"
a los SOCIOS QUE NO TIENE CUENTA IBAN, para pedirles que paguen la cuota. 
En el email, personalizado, se hace clic en un enlace que trae direcmente a esta
función para el socio de cada email recibido con datos encriptados de $codSocioEncriptado.

Esta función llama a un formulario, se muestran la cuota de un socio y otra 
información del socio y se le indica los modos de pagar la cuota anual:
- Se muestran las cuentas bancarias de donde se cobran a los distintas agrupaciones,
 se leen de las tablas de AGRUPACION (todas menos Asturias estan centralizadas 
	a fecha 2018-01-04) 
- Además hay un botón de enlace a PayPal, donde ya se incluye la cantidad a 
pagar y demás datos del socio. 

NOTA: En la función modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	
se obtienen las cuentas bancarias de la ASOCIACION o de la AGRUPACION y el script
de PayPal personalizado para el cobro de la cuota para ese socio. Estos datos se
pasan a vPagarCuotaSocioInc.php
									
En esta función del controlador se puede asignar los valores:									
REAL COBRO: 
$datosSocioPayPal['business'] ='tesoreria@europalaica.com'				
$datosSocioPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr'

PRUEBA CON SANDOX NO COBRA: 
$datosSocioPayPal['business'] ='prueba1@europalaica.com'
$datosSocioPayPal['action']='https://www.sandbox.paypal.com/cgi-bin/webscr'

RECIBE: Parámetro con el "CODUSER encriptado", (desde el emil recibido por el socio).

LLAMADA: desde link en email recibido por el socio sin CC "modeloEmail.php:emailAvisarCuotaSinCobrarSinCC()" 
y que es enviado por el tesorero recordando que no ha abonado la cuota 
llamado desde "cTesorero.php:emailAvisarCuotaNoCobradaSinCC()"

LLAMA: require_once './controladores/libs/cNavegaHistoria.php', 
modeloSocios.php:buscarDatosSocioCodSocio(),./modelos/modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()
vistas/socios/vPagarCuotaSocioInc.php
usuariosLibs/encriptar/encriptacionBase64.php:desEncriptarBase64()					

OBSERVACIONES:	Es casi igual a controladorSocios.php:pagarCuotaSocio(), 
pero usa la función modeloSocios.php:buscarDatosSocioCodSocio() en lugar de 
modeloSocios.php:buscarDatosSocio(), recibe el parámetro de CODSOCIO, y lo desencripta.		

2020-04-14: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza.
--------------------------------------------------------------------------------------------------*/
function pagarCuotaSocioSinCC($codSocioEncriptado)
{	 
 /*echo "<br><br>0-1 controladorSocios:pagarCuotaSocioSinCC:SESSION: ";print_r($_SESSION);	
	 echo "<br><br>0-2 controladorSocios:pagarCuotaSocioSinCC:_POST: "; print_r($_POST);
		echo "<br><br>0-3 controladorSocios:pagarCuotaSocioSinCC:_GET: "; print_r($_GET);
		echo "<br><br>0-4 controladorSocios:pagarCuotaSocioSinCC:_REQUEST: "; print_r($_REQUEST);
		echo "<br><br>0-5 controladorSocios:pagarCuotaSocioSinCC:codSocioEncriptado: ";print_r($codSocioEncriptado);
 */	
	if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//por si guardó $_SESSION anterior  
	{ require_once './controladores/libs/limpiarVariablesSesion.php';
			limpiarVariablesSesion();//Para limpiar Variables SESSION y COOKIES;
	}		

 require_once './modelos/modeloSocios.php';
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';	
	
	$datosMensaje['textoCabecera'] = "PAGAR CUOTA ANUAL DE SOCIO/A";	
	$datosMensaje['textoComentarios'] = "<br /><br />Error al intentar pagar cuota anual. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	 
	
	$nomScriptFuncionError = ' controladorSocios.php:pagarCuotaSocioSinCC(). Error: ';		
 $tituloSeccion = 'Área de Socios/as';

	//require_once './constantes/constBancosPagoCuotas.php';
	
 $arrayParamMensaje['arrMensaje']['textoCabecera'] = 'Pagar cuota anual socio/a';//*** OJO:POSIBLEMENTE SE PUEDA SUSTIR POR 	$datosMensaje['textoCabecera']
	
	/*----Inicio  buscar datos socio -----------------------------------------------------------------*/				
	//$usuarioBuscado = $_SESSION['vs_CODUSER'];
	
	require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";  
	$codSocio = desEncriptarBase64($codSocioEncriptado);	 
		
	//echo "<br><br>1 controladorSocios:pagarCuotaSocioSinCC:codSocio:"; print_r($codSocio); 

	$anioCuota = date('Y');//Para que busque el estado de cuotas del año actual		

	require_once './modelos/modeloSocios.php';
	
	$resDatosSocio = buscarDatosSocioCodSocio($codSocio,$anioCuota);//incluye conexionDB() y PDO/*¿¿¿¿incluye GESTIONCUOTAS y bancos de la AGRUPACIÓN */
	
 //echo "<br><br>2 controladorSocios:pagarCuotaSocioSinCC:resDatosSocio: "; print_r($resDatosSocio); 

 if ($resDatosSocio['codError'] !== '00000')
 {
	 $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);		 
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
	/*----Fin  buscar datos socio ---------------------------------------------------------------------*/		    
 else //$resDatosSocio['codError']=='00000'
 {	 
  /*----------- Inicio Preparar Datos Socio Para Pago Cuota con Bancos y Pay ------------------------------*/
		
  $arrayParamMensaje['arrMensaje']['textoComentarios'] = "Cuota anual del socio/a año: ".date('Y');//OJO:POSIBLE SUSTUIR POR $datosMensaje['textoComentarios']="Cuota anual del socio/a año:".date('Y');		
									
		$resDatosSocio['valoresCampos']['datosFormCuotaSocio'] = $resDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')];
		  
		require_once './modelos/modeloBancos.php';
		$datosSocioBancosPayPal = mPrepararDatosSocioPagoCuotaBancosPayForm($resDatosSocio['valoresCampos']);	
	
		/*---- Inicio Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php --------
			Esto lo pongo aquí en controlador por que es más fácil el acceso, para hacer pruebas pero
			también puede estar en modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm()	
		-------------------------------------------------------------------------------------------*/			
		/*---- Inicio  PARA PRUEBA SIN COBRAR: DESCOMENTAR ---------------------------------------
		 $datosSocioBancosPayPal['business'] ='prueba1@europalaica.com';
		 $datosSocioBancosPayPal['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		----- fin PARA PRUEBA SIN COBRAR: DESCOMENTAR -------------------------------------------*/
		
		/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR --------------------------------*/	
		$datosSocioBancosPayPal['business'] = 'tesoreria@europalaica.com'; 			
		$datosSocioBancosPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr';
		/*----**** OJO fin **** REAL PARA COBRAR: DESCOMENTAR -----------------------------------*/	
		
		/*---- Fin Para script paypal vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php ---------*/	
		/*----------- Fin Preparar Datos Socio Para Pago Cuota con Bancos y Pay --------------------------------*/
		
		//echo "<br><br>3 controladorSocios:pagarCuotaSocioSinCC:datosSocioBancosPayPal: "; print_r($datosSocioBancosPayPal);
		//echo "<br><br>4 controladorSocios:pagarCuotaSocioSinCC:arrayParamMensaje: "; print_r($arrayParamMensaje);
			
		require_once './vistas/socios/vPagarCuotaSocioInc.php'; 			 
		vPagarCuotaSocioInc($tituloSeccion,$arrayParamMensaje['arrMensaje'],$datosSocioBancosPayPal,$navegacion);

 }//else $resDatosSocio['codError']=='00000'  
}
/*--------------------------- Fin pagarCuotaSocioSinCC -------------------------------------------*/

// ********************************************FIN PAGAR CUOTA  ********************************


// ****************************** DONACION  ****************************************************

/*------------------------------- Inicio donarSocio() --------------------------------------------
Se muestran los datos de los bancos donde puede donar un socio (los correspondientes
a sus agrupaciones de cobro de cuotas) y un enlace a un botón de PayPal 
para donar (por ahora solo el de EL).
Se buscan las cuentas bancarias de las agrupaciones territoriales correspondiente
a cada socio, y se preparan para mostrarlas en vDonarSocioInc()

Se incluye la dirección del script para hacer donación con PayPal 
(botón estandar para ESTATAL)

NOTA: En esta función del controlador se puede asignar los valores:									
REAL COBRO: $payPalScriptDona = './vistas/PayPal/scriptPayPalDonaAhora.php';
PRUEBA CON SANDOX NO COBRA:$payPalScriptDona='./vistas/PayPal/scriptPayPalDonaAhoraSANDBOX.php';

LLAMA: modeloSocios.php:buscarDatosSocio(), 
       modeloBancos.php:prepararCadIBANAgrupMostrar($codAgrupacion);	
							vistas/socios/vDonarSocioInc.php'
       controladores/libs/cNavegaHistoria.php', 

LLAMADA: desde menú lateral vista socios, y desde la barra horizontal superior

OBSERVACIONES:
2020-05-10: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza 
----------------------------------------------------------------------------------------------*/
function donarSocio()
{			
	/*echo "<br><br>0-1 controladorSocios:donarSocio:SESSION:";print_r($_SESSION);	
	echo "<br><br>0-2 controladorSocios:donarSocio:_POST:"; print_r($_POST);
	*/
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_1'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");  
 }
	else  //if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
	{		
		require_once './modelos/modeloSocios.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	
		
		$datosMensaje['textoCabecera'] = "Hacer una donación";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al intentar hacer una donación a la asociación Europa Laica. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	 
		
		$nomScriptFuncionError = ' controladorSocios.php:donarSocio(). Error: ';
		$tituloSeccion = 'Área de Socios/as';


		//------------ inicio navegación para socios gestores CODROL >2 ---------------
		if (isset($_SESSION['vs_autentificadoGestor']))
		{$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" )			
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=donarSocio";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Hacer una donación";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior"); 
			//echo "<br><br>1 controladorSocios:donarSocio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
			}
		else
		{
			$navegacion ='';
		}
		//------------ fin navegación para socios gestores CODROL >2 ------------------	

		//$anioCuota ='%';
		$anioCuota = date ('Y');
		$usuarioBuscado = $_SESSION['vs_CODUSER'];
		
		//se necesita para ver el Código de la agrupación del socio y de ahí buscar Cuenta bancaria de la agrupación a la que pertenece
		$resDatosSocio = buscarDatosSocio($usuarioBuscado,$anioCuota);//incluye conexionDB() y PDO
		
		//echo "<br><br>2 controladorSocios:donarSocio:resDatosSocio: "; print_r($resDatosSocio); 
		
		if ($resDatosSocio['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}      
		else //$resDatosSocio['codError']=='00000'
		{		
			//$codAgrupacion = '00300000';//asturias
			//$codAgrupacion = '00129000';//mALAGA		
			//$codAgrupacion = '00000000';//Estatal Cuenta bancaria estatal para todos
			
			$codAgrupacion = $resDatosSocio['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo'];
			
			require_once './modelos/modeloBancos.php';
			$cadBancos = prepararCadIBANAgrupMostrar($codAgrupacion);	
			
			//echo "<br><br>3 controladorSocios:donarSocio:cadBancos: ";print_r($cadBancos);
			
			if ($cadBancos['codError'] !== '00000')
			{
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$cadBancos['codError'].": ".$cadBancos['errorMensaje']);	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			else //($cadBancos['codError']=='00000')
			{ 
				/*	Nota, posibilidad de almacener el script en la BBDD, distintos scripts de PayPal para cada agrupación ???
				$payPalScript = $payPalPagosCuotasElegirScript['payPalScript'];	
				*/		
					
				// ********* ¡OJO! INCIO LA SIGUIENTE SOLO PARA PRUEBAS: NO COBRA ***********************
				//$payPalScriptDona = './vistas/PayPal/scriptPayPalDonaAhoraSANDBOX.php';
				//*********** FIN PRUEBAS ***************************************************************
				
				// ********* ¡OJO! LO SIGUIENTE PARA COBRAR REALMENTE ***********************************		
				$payPalScriptDona = './vistas/PayPal/scriptPayPalDonaAhora.php';
				//*********** FIN LO SIGUIENTE PARA COBRAR REALMENTE ************************************

				//echo "<br><br>4 controladorSocios:donarSocio:payPalScriptDona: "; print_r($payPalScriptDona); 
				//echo "<br><br>5 controladorSocios:donarSocio:cadBancos: "; print_r($cadBancos); 			

					require_once './vistas/socios/vDonarSocioInc.php'; 	//mejor sería agrupar var en un array		 
					vDonarSocioInc($tituloSeccion,$payPalScriptDona,$cadBancos,$navegacion);		
					
				}//else $cadBancos['codError']=='00000'
		}//else $resDatosSocio['codError']=='00000'
	}//else if ($_SESSION['vs_autentificado'] == 'SI' || $_SESSION['vs_ROL_1'] == 'SI')
}
/*--------------------------- Fin donarSocio ---------------------------------------------------*/

/*====== INICIO PARA PRUEBAS DESCARGAR ARCHIVOS DEL SERVIDOR (EN DESARROLLO) ======================*/
/*=================================================================================================*/

/*------------------------------- Inicio descargarDocsSocio ----------------------------------------
Lleva a una formulario donde se muestran los archivos para descargar  

LLAMA: modeloArchivos.php:obtenerListadoArchivosUnDirectorio(), o obtenerListadoDeArchivosRecur()
       vistas/socios/vDescargarDocsRolSocioInc.php'; 
       require_once './controladores/libs/cNavegaHistoria.php 

LLAMADA: desde menú lateral de socios

OBSERVACIONES: 
EN DESARROLLO habría que añadir las filas correspondientes que ahora están en las tablas:
FUNCION_descar_Arch_2020_07_29_noBorrar  y ROLTIENEFUNCION_con_descargar_Arch_2020_07_29_noBorrar,
para que aparezcan en el menú izdo según los roles.

2020-04-21: Añado y puede que sirva como plantilla par a los demás roles
NOTA: existe la clase La clase SplFileInfo, que se podría utilizar como alternativa a este desarrollo
Agustin: 2020-04-21 añado para pruebas
--------------------------------------------------------------------------------------------------*/
function descargarDocsSocio()
{
 if ($_SESSION['vs_autentificado'] !== 'SI') 
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	//echo "<br><br>0-1 controladorSocios:descargarDocsSocio:_GET: ";print_r($_GET);
	//echo "<br><br>0-2 controladorSocios:descargarDocsSocio:_REQUEST: ";print_r($_REQUEST);
	//echo "<br><br>0-3 controladorSocios:descargarDocsSocio:_POST: ";print_r($_POST);	 
	//echo "<br><br>0-4 controladorSocios:descargarDocsSocio:_SESSION: ";print_r($_SESSION);

	require_once './modelos/modeloArchivos.php';
 require_once './vistas/socios/vDescargarDocsRolSocioInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';
	
	$datosMensaje['textoCabecera'] = 'Documentos y manuales para el socio/a';
	$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los \"Documentos y manuales\" para el socio/a. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
	$nomScriptFuncionError = ' controladorSocios.php:descargarDocsSocio(). Error: ';		
	
 $tituloSeccion = 'Área de Socios/as';

 //------------ inicio navegación para socios gestores CODROL > 2 --------------	
	if (isset($_SESSION['vs_autentificadoGestor']))
 {
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=controladorSocios&accion=descargarDocsSocio";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Documentos y manuales para el socio/a<br />";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
 }
	else
	{$navegacion = "";
	}		
	//echo "<br><br>3 controladorSocios:descargarDocsSocio:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
	//------------ fin navegación -------------------------------------------------	
	
	//echo "<br><br>4-0 controladorSocios:descargarDocsSocio:directorio actual: "; echo getcwd();
	echo "<br><br>4-1-1 controladorSocios:descargarDocsSocio:_SERVER['DOCUMENT_ROOT']: "; print_r($_SERVER['DOCUMENT_ROOT']);

 //$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto 
 //$directorioArchivos = "documentos/UTILIDADES";//para pruebas multinivel
 
	//$directorioArchivos = "/DOCUMENTOS_PRUEBA/SOCIOS/";
	$directorioArchivos = "/DOCUMENTOS_PRUEBA/GESTORES/";
	
 $directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto 
	$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto 
		
		echo "<br><br>4-1-2 controladorSocios:descargarDocsSocio:directorioPathArchivosAbrir: "; print_r($directorioPathArchivosAbrir);
	
 //$arrListaArchivos = obtenerListadoArchivosUnDirectorio($directorioPathArchivosAbrir);//Devuelve $arrListadoDeArchivos['listaArchivos'] ordenados por nombre	
 //$arrLista = obtenerListadoArchivosUnDirectorio($directorioPathArchivosAbrir);//Funciona Devuelve $arrListadoDeArchivos['listaArchivos'] ordenados por nombre		

	//$recursivo = true;	//funciona
	$recursivo = false;	//funciona
	$arrLista  = obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo);//funciona
	echo "<br><br>4-2 controladorSocios:descargarDocsSocio:arrLista: "; print_r($arrLista);

	//function obtenerListadoDeArchivosRecur($directorioArchivos, $recursivo = false)
	//$arrListadoArchivos = obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo);
	//$arrListaArchivos['listaArchivos'] = obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo);
	//$arrListaArchivos['listaArchivos'] = dirToArray($directorioPathArchivosAbrir);
	//$arrListaArchivos['listaArchivos'] = dirToArrayMio($directorioPathArchivosAbrir);	
	//echo "<br><br>4-3 controladorSocios:descargarDocsSocio:arrListaArchivos: "; print_r($arrListaArchivos); 
	
	//$arrListaArchivos['directorio'] = $directorioArchivos;
	
	echo "<br><br>4-4 controladorSocios:descargarDocsSocio:arrLista: "; print_r($arrLista); 	
 	
 //if ($arrListaArchivos['codError'] !== '00000')
	if (isset($arrLista['codError']) && $arrLista['codError'] !== '00000')	
 {
		//$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrListaArchivos['codError'].": ".$arrListaArchivos['errorMensaje']);
		$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrLista['codError'].": ".$arrLista['errorMensaje']);
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 }      
 else 
 { 
			$arrListaArchivos['listaArchivos'] = $arrLista;
   $arrListaArchivos['directorio'] = $directorioArchivos;	
			
			echo "<br><br>5 controladorSocios:descargarDocsSocio:arrListaArchivos: ";print_r($arrListaArchivos);
			
			vDescargarDocsRolSocioInc($tituloSeccion,$arrListaArchivos,$navegacion);	 	
 } 
}
//--------------------------- Fin descargarDocsSocio() ---------------------------------------------

/*====== FIN PARA PRUEBAS DESCARGAR ARCHIVOS DEL SERVIDOR (EN DESARROLLO) =========================*/

?>