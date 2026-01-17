<?php
/* -----------------------------------------------------------------------------------------------
FICHERO: controladorLogin.php 
PROYECTO: Europa Laica
VERSION: php 7.3.21

DESCRIPCIÓN: 				
En "controladorLogin.php" , se encuentran las funciones relacionadas con login de los usuarios
para aplicación, roles, menú dinámico izquierdo, recordar contraseña y logOut.

Para tareas de mantenimiento, cuando se quiere que los usuarios no puedan acceder, se puede hacer 
desde el rol de Administrador en MANTENIMIENTO<->EXPLOTACIÓN  (en cAdmin.php)

OBSRVACION: revisada para PHP 7.3.21 y PDO
-------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()--------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya
hay activada una sesion, aunque no es un error puede mostrar un "Notice", 
si warning esta activado. Para evitar estos Notices, uso la función 
is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
----------------------------------------------------------------------------*/
//echo "<br><br>1_1 controladorLogin.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 controladorLogin.php:session_status: ";echo session_status();echo "<br>";

/*--------------------------- Fin session_start()------------------------------------------------*/

/*-----------------------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
/*-----------------------------------------------------------------------------------------------*/


/* --------------------------- Inicio menuRolesUsuario --------------------------------------------
Se llega a esta función como primer paso después de hacer login desde la función 
"controladorLogin.php:validarLogin()".

La función buscarRolesUsuario(), en la tabla USUARIOTIENEROL busca por CODUSER todos los roles que
 tiene ese usuario.

Si el usuario tiene SOLO UN ROL, se dirige "con call_user_func($funcion)" se llama directamente a 
la función del controlador: menuGralSocio(), menuGralAdmin(), ... sin pasar por index.php		

Si el usuario tiene MAS DE UN ROL, que sea gestor: Presidente, Coordinador,Tesorero, Administrador,
(y los que se puedan añadir) se mostrará el menú de los roles que tenga asignados el usuario en 
la sección izda: Roles del gestor/a: -Socio/a, -Presidencia, vice. y secretaría, -Tesorería,
-Coordinación, -Gestión simpatizantes, ...

Se asigna los valores correspondiente para la navegación en $_SESSION['vs_HISTORIA'] y se tendrá 
en cuanta si 'MODOTRABAJO'  == 'MANTENIMIENTO' o 'EXPLOTACIÓN' 

LLAMADA: controladorLogin.php:validarLogin() con call_user_func('menuRolesUsuario') y desde los
links de la "navegación" horizontal 

LLAMA: modeloUsarios.php:buscarRolesUsuario(),para buscar por CODUSER todos los roles de ese usuario
vistas/login/vRolInc.php:vRolInc()
vistas/mensajes/vMensajeCabInicialInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: 
2021-02-02: probada PHP 7.3.21. No es necesario PDO7,lo incluyen internamente
-------------------------------------------------------------------------------------------------*/
function menuRolesUsuario() 
{   
		//echo "<br /><br />0-1 controladorLogin:menuRolesUsuario:_SESSION: ";print_r($_SESSION);	
		//echo "<br /><br />0-2 controladorLogin:menuRolesUsuario:_POST: ";print_r($_POST);
		
		if ($_SESSION['vs_autentificado'] !== 'SI') 
		{
				header("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
		} 
		else// !if ($_SESSION['vs_autentificado'] !== 'SI') 
		{ 
				require_once './modelos/modeloUsuarios.php';
				require_once './modelos/modeloEmail.php';
				require_once './vistas/mensajes/vMensajeCabInicialInc.php';
				
				$datosMensaje['textoCabecera'] = "MENÚ ROLES GESTOR/A";  
				$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema informático al buscar los roles del del gestor/a. Prueba de nuevo pasado un rato. 
																																									<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a info@europalaica.org";
				$nomScriptFuncionError = ' ControladorLogin.php:menuRolesUsuario(). Error: ';
				$tituloSeccion = 'Roles del gestor/a';					
				
				$rolesUsuario = buscarRolesUsuario($_SESSION['vs_CODUSER']);//en modeloUsarios.php. Probado error OK numFilas = 0 trata como error
									
				//echo "<br /><br />1-1 controladorLogin:menuRolesUsuario:rolesUsuario: ";print_r($rolesUsuario);
				
				if ($rolesUsuario['codError'] !== '00000') 
				{ $rolesUsuario['textoComentarios'] = ": Error en controladorLogin:menuRolesUsuario:()";																																																										
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$rolesUsuario['textoComentarios'].': '.$rolesUsuario['codError']. ": ".$rolesUsuario['errorMensaje']);		
						$enlacesSeccIzda = '';//evita notice
						vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);							
				} 
				elseif ($rolesUsuario['numFilas'] == 1)//solo tiene un rol, por lo que llama al correspondiente controlador y función: controladoSocios.php:menuGralSocio(), cAdmin.phpmenuGralAdmin(),... 
				{ 		
						//echo "<br /><br />2 controladorLogin:menuRolesUsuario:rolesUsuario['resultadoFilas']: ";print_r($rolesUsuario['resultadoFilas']);
						$controlador = $rolesUsuario['resultadoFilas'][0]['CONTROLADOR'];													
						$funcion = $rolesUsuario['resultadoFilas'][0]['NOMFUNCION'];

						require_once "./controladores/".$controlador.".php";
						call_user_func($funcion);//bien: llamada directa a la función del controlador: menuGralSocio(), Admin(), ... sin pasar por index.php		

						//header('Location:./index.php?controlador='.$controlador.'&accion='.$funcion);//bien: otra opción llamada pasando por index.php ¡ojo! ningún echo delante
				}	
				else //elseif ($rolesUsuario['numFilas'] > 1)//Tiene más de un que se mostrarán en el menú izdo "vistas/login/vRolInc.php"
				{ 
						//echo "<br /><br />3-1 controladorLogin:menuRolesUsuario:rolesUsuario['resultadoFilas']: ";print_r($rolesUsuario['resultadoFilas']);
						
						$textoEnlace ='Entrar';//lo mostrado en EXPLOTACION
					
						if ($_SESSION['vs_MODOTRABAJO']  == 'MANTENIMIENTO') 
						{ //echo "<br><br>3-2 controladorLogin:menuRolesUsuario:_SESSION['vs_MODOTRABAJO']: ";print_r($_SESSION['vs_MODOTRABAJO']);
								//$textoCuerpo = "Esta aplicación informática NO ESTARÁ ACCESIBLE para los socios y socias durante unas horas, debido a trabajos de mantenimiento.  <br /><br />Perdonen las molestias";							
								$textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>".$textoEnlace;							
						}
						/*------- Inicio para link "Entrar" --------------------------------*/
						$_SESSION['vs_HISTORIA']['enlaces'][0]['link'] = "index.php?controlador=controladorLogin&accion=validarLogin";
						$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = $textoEnlace;
						$_SESSION['vs_HISTORIA']['pagActual'] = 0;					
						//*------- Fin para link "Entrar" ----------------------------------*/
			
						/*---------- Inicio navegación para gestores con más de un rol -----*/
						$_SESSION['vs_HISTORIA']['enlaces'][1]['link'] = "index.php?controlador=controladorLogin&accion=menuRolesUsuario";
						$_SESSION['vs_HISTORIA']['enlaces'][1]['textoEnlace'] = "Menú roles";
						$_SESSION['vs_HISTORIA']['pagActual'] = 1;
						//echo "<br><br>4 controladorLogin:menuRolesUsuario:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);					
						require_once './controladores/libs/cNavegaHistoria.php';
						$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'], "<< Pag. anterior");
						/*---------- Fin navegación para gestores con más de un rol --------*/
						
						$_SESSION['vs_enlacesSeccIzda'] = $rolesUsuario['resultadoFilas'];
						$_SESSION['vs_autentificadoGestor'] = 'SI';
					
						//echo "<br /><br /5 controladorLogin:menuRolesUsuario:_SESSION['vs_enlacesSeccIzda']: ";print_r($_SESSION['vs_enlacesSeccIzda']);
						
						require_once './vistas/login/vRolInc.php';							
						vRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'], $navegacion);//menú roles usuario						
						
				}//else elseif ($rolesUsuario['numFilas'] > 1
		}//else !if ($_SESSION['vs_autentificado'] !== 'SI') 
		
		//echo "<br /><br />6 controladorLogin:menuRolesUsuario:rolesUsuario: ";print_r($rolesUsuario);	
}
/*--------------------------- Fin menuRolesUsuario -----------------------------------------------*/ 

/*----------------------------- Inicio validarLogin Para Mantenimiento -----------------------------
Es el punto de entrada a la aplicación de usuarios ya resgistrados: socios y (simpatizantes ahora no)

Primero se busca en la tabla "CONTROLMODOAPLICACION" el modo de trabajo en que se encuentra, que 
puede ser MODOAPLICACION: MANTENIMIENTO o EXPLOTACIÓN, y se asigna ese valor a la variable 
$_SESSION['vs_MODOTRABAJO'], que se utilizará en otros controladores y formularios de la aplicación

Después con la función  validarUsuario() se valida los campos usuario y password el formato para 
evitar injections y después se busca en las tablas, la existencia del usuario y el rol de usuario 
o roles del usuario (más de uno si es gestor) y se asignan a una variable de sesión que se utilizarán
como filtros para evitar entradas no deseadas en los controladores o formularios.

Si la está en MODOAPLICACION = MANTENIMIENTO, avisa y no dejará entrar a los usuarios excepto al 
rol de Adminirador y usuarios con rol de Mantenimiento. En el caso de que el usuario que entra cumpla
las condiciones, los procesos disponibles serían los mismos que si no estuviese en explotación y 
le servirá para probar los cambios antes de hacerlos disponibles para todos.

Si el usuarios pasa los controles se llama a la función controladorLogin.php:menuRolesUsuario(),
(con call_user_func() o header() ) donde se llama al formulario correspondiente con el menú de roles 
y/o funciones para ese usuarios.

LLAMA: modeloAdmin.php:buscarModoAp(), 
       modeloUsuarios.php:validarUsuario()
       controladores/libs/limpiarVariablesSesion.php:limpiarVariablesSesion()
       vistas/login/vLoginInc.php, y vRecordarLoginInc.php,
							vistas/mensajes/vMantenimientoInc.php
							vistas/mensajes/vMensajeCabInicialInc.php
					  modeloEmail.php:emailErrorWMaster()

OBSERVACIONES:	Probado con PHP 7.3.21
call_user_func() o header() dos opciones, consultar seguridad para elegir la mas conveniente															
--------------------------------------------------------------------------------------------------*/
function validarLogin()
{
	//echo "<br>0-1 controladorLogin:validarLogin:_SESSION: "; print_r($_SESSION);
	//echo "<br>0-2 controladorLogin:validarLogin:_POST: "; print_r($_POST);	

	$datosMensaje['textoCabecera'] = "ACCESO AL ÁREA PRIVADA DE SOCIOS/AS";
	$datosMensaje['textoComentarios'] = "<br /><br />Error de acceso del socio/a en la 'Identificación con usuario y contraseña'. Prueba de nuevo pasado un rato. 
																																						<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a \"info@europalaica.org\" ";	
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
	$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';//Se usa en caso de error	
	$nomScriptFuncionError = ' controladorLogin.php:validarLogin(). Error: ';

	$arrValidarUsuario['codError'] = '00000';
	$arrValidarUsuario['errorMensaje'] = '';
	
	if (isset($_SESSION['vs_autentificado']) && $_SESSION['vs_autentificado'] == 'SI')//si ya está autentificado
 { require_once './controladores/libs/limpiarVariablesSesion.php';
	  limpiarVariablesSesion();  
 }	

 if (!isset($_SESSION['vs_contadorIntentosLogin']))
 { $_SESSION['vs_contadorIntentosLogin'] = 0 ;
 }
	//echo "<br><br>1 controladorLogin:validarLogin:vs_contadorIntentosLogin: ",$_SESSION['vs_contadorIntentosLogin'];	
	
 if ($_SESSION['vs_contadorIntentosLogin'] >= 6 )
 { 
   require_once './vistas/login/vRecordarLoginInc.php';	  
	  vRecordarLoginInc("Has superado el número máximo de intentos para identificarte",'');	
 }
 else //($_SESSION['vs_contadorIntentosLogin'] < 6 )
 { 
	  require_once './modelos/modeloAdmin.php';
			$resBuscarModoAp = buscarModoAp();//en modeloAdmin.php, incluye conexion, probado error OK
				
			//echo "<br><br>2-1 controladorLogin:validarLogin:resBuscarModoAp: ";print_r($resBuscarModoAp);	

			if ($resBuscarModoAp['codError'] !== '00000') //Error OK, numFilas == 0 también lo trata como error
			{	$arrValidarUsuario = $resBuscarModoAp;
			  $arrValidarUsuario['errorMensaje'] .= ': Error al buscar en buscarModoAp() ';
			}				
   else//$resBuscarModoAp['codError'] == '00000'
   {	
					if ($resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] == 'MANTENIMIENTO') 
					{ 
				   $textoCuerpo = "Esta aplicación informática NO ESTÁ ACCESIBLE para los socios y socias debido a trabajos de mantenimiento. <br /><br />
				   Si lo necesita puede contactar con el Equipo Técnico de Europa Laica: <strong>gestion@europalaica.org</strong><br /><br />
							Perdonen las molestias";							
							// anterior $textoCuerpo = "Esta aplicación informática NO ESTARÁ ACCESIBLE para los socios y socias durante unas horas, debido a trabajos de mantenimiento.<br /><br />Perdonen las molestias";							

							$_SESSION['vs_MODOTRABAJO'] = 'MANTENIMIENTO';// se usa en otros controladores y formularios
					}
					else
					{ $textoCuerpo = "";	
				   $_SESSION['vs_MODOTRABAJO'] = 'EXPLOTACION';
     }						
				 //echo "<br><br>2-2 controladorLogin:validarLogin:_SESSION: ";print_r($_SESSION);					
					
					if (!$_POST) //primera vez
					{							
							require_once './vistas/login/vLoginInc.php';
							vLoginInc ($textoCuerpo,"");							
					} 
					else //$_POST
					{					
							$validarUsuario = validarUsuario($_POST['USUARIO'],$_POST['CLAVE']);//en modeloUsuarios.php, devuelve también array roles.	Error OK
							
		     //echo "<br><br>3 controladorLogin:validarLogin:validarUsuario: ";print_r($validarUsuario);					
						 
							unset($_POST);//Para que no se pase esta información a las funciones llamadas ya no es necesaria										

							if ($validarUsuario['codError'] !== '00000')// puede ser error lógico (numFilas == 0 es error) o error del sistema
							{	$arrValidarUsuario = $validarUsuario;	
         $arrValidarUsuario['errorMensaje'] = ': Error al buscar en validarUsuario() ';
							}  
							else	//$validarUsuario['codError'] == '00000'			
							{ 								
									/*----------- Inicio Control Roles ----------------------*/
									$entrarAplicacion = 'NO';
									$arrRoles = $validarUsuario['resultadoFilas'];
									$numFilas = $validarUsuario['numFilas'];									
									$f = 0;

	        while ($f < $numFilas )											
									{
											foreach ($arrRoles[$f] as $campo => $valor) 
											{ 											
											  if ($campo == 'CODROL') 
											  { $_SESSION['vs_ROL_'.$valor] = 'SI';}
											}	

											if ( $arrRoles[$f]['CODROL'] === '011' || $arrRoles[$f]['CODROL'] === '012') //a estos roles les permitirá entrar en la aplicación siempre aunque esté en mantenimiento	
											{	$entrarAplicacion = 'SI';	} 
							
											$f++;						
									}//while ($f < $numFilas )				
         
									//echo "<br><br>4 controladorLogin:validarLogin:entrarAplicacion: ";print_r($entrarAplicacion);	
									/*----------- Fin Control Roles -------------------------*/
																
									if ($_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO' && $entrarAplicacion == 'NO')//Si es $_SESSION['vs_MODOTRABAJO'] =EXPLOTACION todos pueden entrar
									{	          									
											require_once './vistas/mensajes/vMantenimientoInc.php';
											vMantenimientoInc ("","");	//Solo para MANTENIMIENTO, bien						
									}		
									else//$entrarAplicacion == 'SI'//entrarán cuando aplicación está en modo EXPLOTACION o si está en MANTENIMIENTO solo usuarios con rol de Mantenimiento
									{
											$_SESSION['vs_autentificado'] = 'SI';
											$_SESSION['vs_CODUSER'] = $validarUsuario['resultadoFilas'][0]['CODUSER'];											
           //echo "<br><br>5-0 controladorLogin:validarLogin:_SESSION: "; print_r($_SESSION);
											
											call_user_func('menuRolesUsuario');//bien: función controladorLogin.php:menuRolesUsuario(), Llamada directa sin pasar por index.php

											//header('Location:./index.php?controlador=controladorLogin&accion=menuRolesUsuario');//bien: otra opción llamada pasando por index.php ¡ojo! ningún echo delante 
						
									}//else $entrarAplicacion == 'SI'									
							}//else $validarUsuario['codError'] == '00000'			
					}//POST
   }//else $resBuscarModoAp['codError'] == '00000'
				
			//echo "<br><br>5-1 controladorLogin:validarLogin:arrValidarUsuario: ";print_r($arrValidarUsuario);
			//echo "<br><br>5-2 controladorLogin:validarLogin:_SESSION: "; print_r($_SESSION);
			
			/*-------------- Aquí empieza tratamiento errores --------------------------------------------*/

			if ($arrValidarUsuario['codError'] !== '00000')
			{				
				$_SESSION['vs_contadorIntentosLogin']++;		

				if ($arrValidarUsuario['codError'] > '80000')//error lógico probado error ok
				{ 				
						require_once './vistas/login/vLoginInc.php';						
						vLoginInc ($textoCuerpo,$validarUsuario['resultadoFilas']);
				}				
				else // ($arrValidarUsuario['codError'] =< '80000') error BBDD//llega aquí en caso de error sistema en "buscarModoAp()" comprobado OK
				{ $tituloSeccion  = 'Área Privada de Socios/as';
						$enlacesSeccIzda = '';//evita notice
						require_once './vistas/mensajes/vMensajeCabInicialInc.php';
						vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda );
				
						require_once './modelos/modeloEmail.php';	
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrValidarUsuario['codError'].": ".$arrValidarUsuario['errorMensaje'].$datosMensaje['textoComentarios']);	
						//NOTA: las funciones: modeloUsuarios.php: buscarModoAp() y validarUsuario() tratan el error e incluyen insert() en tabla ERRORES
				}																								
			}//if ($validarUsuario['codError'] !== '00000')				
			/*----------- Fin tratamiento de errores lógicos y del sistema ------------------------------*/   

 }//else ($_SESSION['vs_contadorIntentosLogin'] < 6 )
}
/*----------------------- Fin validarLogin -------------------------------------------------------*/


/* ----------------------- Inicio recordarLogin  --------------------------------------------------
Cuando un usuario no recuerda su contraseña o su nombre de usuario, en esta función se le pide 
su "email", y si es un usuario registrado y ESTADO = alta, se le envía un email para restablecer 
contraseña o/y recordardar el nombre de usuario, según las opciones elegidas.

Primero se valida el campo email para evitar injections, después llama a la función 
buscarEmailUsuario() para buscar ese email primero en las tabla MIEMBRO y en tabla USUARIO 
si ESTADO = 'alta' le envía email para restablecer contraseña y si es  'alta-sin-password-gestor', 
o 'alta-sin-password-excel' le avisa de su situción.
Si no lo encuentra buscará en la tabla SOCIOSCONFIRMAR por si está registrado pero aún no ha 
confirmado su alta, en este caso le pide confirmar su alta.

Incluye control de num. intentos y bloqueo por Mantenimiento

LLAMADA: desde menú superior barra "Rescordar usuario y contraseña", también desde validarLogin()
cuando se supera el número máximo de intentos.
LLAMA: 
modelos/modeloEmail.php: buscarEmailUsuario(),emailRestablecerPass(),
emailRecordarUsuario(), emailRecordarUsuarioPass(),emailErrorWMaster()
modelos/libs/validarCamposUsuarios.php:validarFormRecordarLogin()
vistas/login/vRecordarLoginInc.php:RecordarLoginInc()
vistas/mensajes/vMensajeCabInicialInc.php:vMensajeCabInicialInc()

OBSERVACIONES:
2021-01-22: PHP 7.3.21. No es necesario PDO7, lo incluyen internamente algunas de las 
que aquí son llamadas. 
Añado control de num. intentos y bloqueo por estado Mantenimiento
--------------------------------------------------------------------------------------------------*/
function recordarLogin() 
{ 
  //echo "<br /><br />0-1 controladorLogin:recordarLogin:_SESSION: "; print_r($_SESSION);	
		//echo "<br /><br />0-2 controladorLogin:recordarLogin:_POST: ";print_r($_POST);		
		
		require_once './vistas/login/vRecordarLoginInc.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/mensajes/vMensajeCabInicialInc.php';
			
		$datosMensaje['textoCabecera'] = " RECORDAR USUARIO Y CONTRASEÑA";  
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema informático al 'Recordar usuario y contraseña'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a info@europalaica.org";

		$datosMensaje['textoBoton'] = '&nbsp; S a l i r &nbsp;';
		
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
																																							
		$nomScriptFuncionError = ' ControladorLogin.php:recordarLogin(). Error: ';
		$tituloSeccion = 'Área Privada de Socios/as';
  $enlacesSeccIzda = "";				
		
		$resulEmailLogin['codError'] = '00000';
		$resulEmailLogin['errorMensaje'] = '';
		$reRecordarLogin = array();
		/* "$reRecordarLogin" para email, y radiobuton de elegir recordar PASS, USUARIO, AMBOS en 
		    function vRecordarLoginInc($textoPrimero,$recordarPassUser)
		*/		
		$textoPrimero = '';
		/* "$textoPrimero" es para para function vRecordarLoginInc($textoPrimero,$recordarPassUser),
		   para mostrarlo al principio del formulario vRecordarLoginInc, aquí ahora no lo utilizo
				 pero es necesario para controladorLogin.php:validarLogin() en caso de que sobrepase
     6 intentos de login
		*/		
 /*------------- Inicio control si está MANTENIMIENTO bloquear -------------------------------*/
	require_once './modelos/modeloAdmin.php';
	$resBuscarModoAp = buscarModoAp();//en modeloAdmin.php, incluye conexion, probado error OK
		
	//echo "<br><br>1-1 controladorLogin:recordarLogin:resBuscarModoAp: ";print_r($resBuscarModoAp);	

	if ($resBuscarModoAp['codError'] !== '00000')//Error OK, numFilas == 0 también trata como error
	{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'buscarModoAp()'.$resBuscarModoAp['codError'].": ".$resBuscarModoAp['errorMensaje']);
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
   /*----------- Inicio Control número intentos -----------------------------------------------*/		
			if (!isset($_SESSION['vs_contadorIntentosRecordar']))
			{ $_SESSION['vs_contadorIntentosRecordar'] = 1 ;
			}
			//echo "<br><br>1-2 controladorLogin:recordarLogin:vs_contadorIntentosRecordar: ",$_SESSION['vs_contadorIntentosRecordar'];	
			
			if ($_SESSION['vs_contadorIntentosRecordar'] >= 6 )
			{ 
					unset($_SESSION['vs_contadorIntentosLogin']);//por si también en el proceso de login había superado el máx.
					
					$datosMensaje['textoComentarios'] = "Has superado el número máximo de intentos, los email que has introducido, no están registrados en la base de datos de Europa Laica.
					                                     <br /><br />Haz clic en el botón salir, trata de recordar el email con el que te diste de alta como socio/a,
																																										y vuelve intentantarlo de nuevo después de unos momentos. 
																																							   <br /><br /><br />Si necesitas ayuda envía un email indicando el problema y tus datos a \"info@europalaica.org\"";	
					vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);		
			}
			else// $_SESSION['vs_contadorIntentosRecordar'] < 6 
			{	$_SESSION['vs_contadorIntentosRecordar']++;
				
				/*----------- Fin Control número intentos -------------------------------------------------*/	
				
				if (!$_POST) 
				{ 							
							vRecordarLoginInc($textoPrimero,$reRecordarLogin); //$reRecordarLogin no tiene nada en la 1º vez
				} 
				else //$_POST
				{  
							require_once './modelos/libs/validarCamposUsuarios.php';
							$reRecordarLogin = validarFormRecordarLogin($_POST['recordarPassUser']);//- Validar formato datos del formulario 

							//echo "<br><br>2 controladorLogin:recordarLogin:reRecordarLogin: ";print_r($reRecordarLogin);echo "<br>";
							
							if ($reRecordarLogin['codError'] !== '00000') 
							{										
										vRecordarLoginInc($textoPrimero,$reRecordarLogin);
							} 
							elseif ($reRecordarLogin['codError'] == '00000') 
							{  								
										$resulEmailLogin = buscarEmailUsuario($_POST['recordarPassUser']['EMAIL']);//en modeloEmail.php acaso estaría mejor en modeloUusarios.php

										//echo "<br /><br />3 ControladorLogin:recordarLogin:resulEmailLogin: ";print_r($resulEmailLogin);

										if ($resulEmailLogin['codError'] !== '00000') 
										{  /*-------------------- Inicio Tratar errores ----------------------*/
													if ($resulEmailLogin['codError'] > '80000') //cuando no se encuentre: error lógico 
													{ 	
																$reRecordarLogin['EMAIL']['codError'] = $resulEmailLogin['codError'];
																$reRecordarLogin['EMAIL']['errorMensaje'] = $resulEmailLogin['errorMensaje'];
															
																vRecordarLoginInc($textoPrimero,$reRecordarLogin); //$reRecordarLogin contiene los error lógicos
													} 
													else//($resulEmailLogin['codError'] <= '80000') error BBDD
													{ 
																$resulEmailLogin['textoComentarios'] = 'Error en el sistema en función modelos/modeloEmail.php:buscarEmailUsuario(): ';
																$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' buscarEmailUsuario()'.$resulEmailLogin['textoComentarios'].': '.
																																																										$resulEmailLogin['errorMensaje'].$resulEmailLogin['codError']);	
																vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);	
													}//else ($resulEmailLogin['codError'] < '80000')
													
												/*----------------------- Fin Tratar errores -----------------------*/
										}//if ($resulEmailLogin['codError'] !== '00000')
										
										else//$resulEmailLogin['codError']=='00000')	
										{ 
												/*------------- Inicio enviar Emails usuario/password --------------*/
												//echo "<br /><br />4-1 ControladorLogin:recordarLogin:resulEmailLogin: ";print_r($resulEmailLogin);
												
												if ($_POST['recordarPassUser']['opcionPassUser'] == 'PASSWORD') 
												{
															$reEnviarEmailLogin = emailRestablecerPass($resulEmailLogin['resultadoFilas'][0]);
												}												
												elseif ($_POST['recordarPassUser']['opcionPassUser'] == 'USUARIO') 
												{
															$reEnviarEmailLogin = emailRecordarUsuario($resulEmailLogin['resultadoFilas'][0]);
												}											
												else //recordar USUARIO Y PASSWORD 
												{ 
															$reEnviarEmailLogin = emailRecordarUsuarioPass($resulEmailLogin['resultadoFilas'][0]);
												}
												//echo "<br /><br />4-2 controladorLogin:recordarLogin:reEnviarEmailLogin: ";print_r($reEnviarEmailLogin );
												
												if ($reEnviarEmailLogin['codError'] !== '00000') 
												{ 
														$datosMensaje['textoComentarios'] = "<strong>Se ha producido un error.</strong> ".$datosMensaje['textoComentarios'];
														$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'emailRecordarUsuarioPass()'.$datosMensaje['textoComentarios'].': '.
																																																					 		$reEnviarEmailLogin['errorMensaje'].$reEnviarEmailLogin['codError']);	
												}
												else
												{	
													$datosMensaje['textoComentarios'] = $reEnviarEmailLogin['textoComentarios'];
												}	
		         
	  	        //echo "<br><br>5-1 controladorLogin:recordarLogin:_SESSION: ";print_r($_SESSION);//aquí no están vacías, pero sí en index
												
												//unset($_SESSION);
												$_SESSION = array();
												session_destroy();
		
		          //echo "<br><br>5-2 controladorLogin:recordarLogin:_SESSION: ";print_r($_SESSION);//aquí no están vacías, pero sí en index				
										
												vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);	
												
											/*---------------- Fin enviar Emails usuario/password ---------------*/											
										}//else $resulEmailLogin['codError']=='00000')	
							}//elseif ($reRecordarLogin['codError']=='00000')//
				}//$_POST	 
			}//else $_SESSION['vs_contadorIntentos'] < 6 		
	 }//else $resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] !== 'MANTENIMIENTO'	
 }//else $resBuscarModoAp['codError'] == '00000'	
}
/*---------------- Fin recordarLogin  ------------------------------------------------------------*/

/* ------------------------------- Inicio restablecerPass -----------------------------------------
Es llamada desde el link del en email recibido por el usuario, enviado autómaticamente por una 
petición de restablecer contraseña, en el menú superior barra horizontal "Recordar usuario y 
contraseña" (que dirige a la función controladorLogin.php:recordarLogin).
El usuario tuvo que introducir su email que tiene registrado en la BBDD y la opción "Restablecer
contraseña" y su estado ='alta' recibirá el email que le permitirá crear una nueva contraseña.
Recibe como parámetro el $codUserEncriptado, que viene encriptado, y en esta función se desencripta,
para poder grabar la nueva contraseña del usuario Sirve para socios y simpatizantes ya registrados

RECIBE: $codUserEncriptado en la URL del link encriptado, y en esta función se desencripta 

LLAMADA: link del texto en email recibido por el usuario, por una petición de Restablecer contraseña,
en el menú superior barra horizontal "Recordar usuario y contraseña"
LLAMA:
usuariosLibs/encriptar/encriptacionBase64.php:desEncriptarBase64()
modelos/libs/validarCamposUsuarios.ph:validarRestaurarPass()
modeloUsuarios.php:actualizarPass()
modelos/modeloEmail.php:emailErrorWMaster()
vistas/login/vRestablecerPassInc.php
vistas/mensajes/vMensajeCabInicialInc.php
 
OBSERVACIONES:
2020-06-28: No es necesario PDO7, lo incluyen internamente algunas de las que aquí son llamadas.
---------------------------------------------------------------------------------------------------*/
function restablecerPass($codUserEncriptado) 
{
	 //echo "<br><br>0-1 controladorLogin:restablecerPass:codUserEncriptado: ";print_r($codUserEncriptado);
		//echo "<br><br>0-2 controladorLogin:restablecerPass:_POST: ";print_r($_POST);
	
		require_once './vistas/login/vRestablecerPassInc.php';
		require_once './vistas/mensajes/vMensajeCabInicialInc.php';
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "RESTABLECER CONTRASEÑA";  
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema informático al 'Restablecer la contraseña'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
																																							
		$nomScriptFuncionError = '  ControladorLogin.php:restablecerPass(). Error: ';
		$tituloSeccion = 'Área Privada de Socios/as';
  $enlacesSeccIzda = "";				

		if (isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
		{
			  //echo "<br><br>1 controladorLogin:restablecerPass:_POST: ";print_r($_POST);
					
					if (isset($_POST['cancelarCambiarPass'])) 
					{							
								$datosMensaje['textoComentarios'] = "Has salido sin modificar la contraseña";
								vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
					} 
					else //isset($_POST['cambiarPass']
					{ 
								require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
								$codUser = desEncriptarBase64($codUserEncriptado);

								require_once './modelos/libs/validarCamposUsuarios.php';
								$resValidarCamposForm = validarRestaurarPass($_POST['datosFormUsuario'], $codUser);
								
								//validarRestaurarPass(): incluye función modeloUsuarios.php:validarCodUser() para buscar "$codUser" 
								//en tabla USUARIO.CODUSER, y comprobar que sí existe ese CODUSER antes de modificar contraseña en modeloUsuarios.php:actualizarPass()
								
								//echo "<br><br>2 controladorLogin:restablecerPass:resValidarCamposForm: ";print_r($resValidarCamposForm);

								if ($resValidarCamposForm['codError'] !== '00000') 
								{
											if ($resValidarCamposForm['codError'] >= '80000')//error lógico 
											{
														$resValidarCamposForm['datosFormUsuario']['CODUSER'] = $codUserEncriptado;														
														vRestablecerPassInc($tituloSeccion,$resValidarCamposForm);
											} 
											else //if ($resValidarCamposForm['codError'] < '80000') //error sistema
											{  
											   //echo "<br><br>3 controladorLogin:restablecerPass:resValidarCamposForm: ";print_r($resValidarCamposForm);	
													
														$resValidarCamposForm['textoComentarios'] = ": Error en validarCamposUsuarios.php:validarRestaurarPass()";																																																										
														$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['textoComentarios'].': '.
														                                          $resValidarCamposForm['codError']. ": " . $resValidarCamposForm['errorMensaje']);		
														vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
											}
								}//if ($resValidarCamposForm['codError'] !== '00000') 
									
								else //$resValidarCamposForm['codError'] == '00000')
								{  
								   /*-------- Iniciar actualizarPass() -------------------------------*/ 
											
											$resActPass = actualizarPass('USUARIO', $codUser, $resValidarCamposForm);//en modeloUsuarios.php

											//echo "<br><br>4 controladorLogin:restablecerPass:resActPass: ";print_r($resActPass);	

											if ($resActPass['codError'] !== "00000") 
											{  
										    $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActPass['codError']. ": " . $resActPass['errorMensaje']);
											} 
											elseif ($resActPass['numFilas'] == 0)	
           { 	
											   $datosMensaje['textoComentarios'] = "<strong>Aviso:</strong> has dejado la misma contraseña que anteriormente ya tenías. 
              <br /><br /><br />Deberás utilizarla la próxima vez que quieras entrar en la aplicación de gestión de usuarios de Europa Laica
														<br /><br />Si quieres cambiarla, también puedes hacerlo entrando en la aplicación, en la opción -Cambiar contraseña-";
											}
											else//$resActPass['codError'] == '00000'
											{  											
														$datosMensaje['textoComentarios'] = $resActPass['arrMensaje']['textoComentarios'];
											}
											
											vMensajeCabInicialInc($tituloSeccion,$datosMensaje,$enlacesSeccIzda);
           /*------------ Fin actualizarPass() -------------------------------*/ 
											
								}//else $resValidarCamposForm['codError'] == '00000')
					}//else isset($_POST['cambiarPass']
		}//(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) 
		
		else //!(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass'])) //al entrar  
		{  
		   /*------------ Iniciar datos para formulario() --------------------------*/ 
		   //echo "<br><br>6-1 controladorLogin:restablecerPass:_POST: ";print_r($_POST); 
					//echo "<br><br>6-2 controladorLogin:restablecerPass:_SESSION: ";print_r($_SESSION); 
					
					$resActPass['datosFormUsuario']['CODUSER'] = $codUserEncriptado;

					$resActPass['datosFormUsuario']['PASSUSUARIO']['valorCampo'] = "";
					$resActPass['datosFormUsuario']['PASSUSUARIO']['codError'] = '00000';
					$resActPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'] = '';
					$resActPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo'] = "";
					$resActPass['datosFormUsuario']['RPASSUSUARIO']['codError'] = '00000';
					$resActPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'] = '';
					
					//echo "<br><br>6-3 controladorLogin:restablecerPass:resActPass: ";print_r($resActPass);   

					vRestablecerPassInc($tituloSeccion, $resActPass);
     /*------------ Fin iniciar datos para formulario ------------------------*/ 
					
		}//!(isset($_POST['cambiarPass']) || isset($_POST['cancelarCambiarPass']))  
}
/*---------------------------- Fin restablecerPass -----------------------------------------------*/

/* ----------------------- Inicio logOut ----------------------------------------------------------
Es la salida de la aplicación: destruye variables de sesión y cookies y redirije al sitio web de 
Europa Laica Estatal e Internacional 

LLAMADA: link "SALIR" de la barra horizontal de cabecer, y algunos botones en los formularios

LLAMA: modeloSocios.php:buscarDatosAgrupacion() para obtener WEB de Europa Laica Estatal e Internacional 
./vistas/mensajes/vMensajeCabSalirInc.php';
./modelos/modeloEmail.php:emailErrorWMaster();

NOTA: En caso de cambiar la URL de Europa Laica habría que cambiarla 
en la columna [WEB] de Europa Laica de tabla "AGRUPACIONES"
--------------------------------------------------------------------------------------------------*/
function logOut() 
{  
		//echo "<br><br>0-1 controladorLogin:logOut:cookie: ";print_r($_COOKIE);
		//echo "<br><br>0-2 controladorLogin:logOut:_SESSION: ";print_r($_SESSION);	echo "<br><br>";//aquí no están vacías, pero sí en index
		
		$_SESSION = array(); //Unset all of the session variables.

		if (isset($_COOKIE[session_name()])) 
		{
						setcookie(session_name(), '', time() - 42000, '/'); //borra cookies						
		}
		session_destroy(); // destroy the session.
		//echo "<br><br>1-1 controladorLogin:logOut:cookie: ";print_r($_COOKIE);
		//echo "<br><br>1-2 controladorLogin:logOut:_SESSION: ";print_r($_SESSION);//aquí no están vacías, pero sí en index

		if (defined('URL_WEB_EUROPALAICA')) 
		{
				header("Location: " .URL_WEB_EUROPALAICA);
		} 
		else // !if (defined('URL_WEB_EUROPALAICA'))//Constante: URL_WEB_EUROPALAICA no está definida 
		{
				require_once './modelos/modeloSocios.php';				
				require_once './vistas/mensajes/vMensajeCabSalirInc.php';
				require_once './modelos/modeloEmail.php';
				
				$codAgrupacion = '00000000';// Europa Laica Estatal e Internacional
				
				$arrDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);//modeloSocios.php, incluye conexion, columna [WEB] en tabla "AGRUPACIONES"
				
				//echo "<br><br>1-1 controladorLogin:logOut:arrDatosAgrupacion: ";print_r($arrDatosAgrupacion);
				
				if ($arrDatosAgrupacion['codError'] !== '00000')
				{ 
						//echo "<br><br>1-2 controladorLogin:logOut:arrDatosAgrupacion:['resultadoFilas'][0]['WEB']: ";print_r($arrDatosAgrupacion['resultadoFilas'][0]['WEB']);					
					
						$datosMensaje['textoCabecera'] = "SALIR DE LA APLICACIÓN INFORMÁTICA -Gestión de Soci@s-";  
						$datosMensaje['textoBoton'] = 'Salir de la aplicación';						
						$datosMensaje['enlaceBoton'] = "https://europalaica.org";

						$tituloSeccion = 'Área Privada de Socios/as';	

						$resLogOut['codError'] = '81000';
						$resLogOut['errorMensaje'] = "Error en constante no definida URL_WEB_EUROPALAICA. Salió de la aplicación sin redirigir a web laicismo";
						
						$resEmailErrorWMaster = emailErrorWMaster("controladorLogin.php:logOut(): ".$resLogOut['codError']. ": " .$resLogOut['errorMensaje']);					

						vMensajeCabSalirInc($tituloSeccion, $datosMensaje, $_SESSION['vs_enlacesSeccIzda']);											
				}
				else
				{ //echo "<br><br>1-3 controladorLogin:logOut:arrDatosAgrupacion:['resultadoFilas']: ";print_r($arrDatosAgrupacion['resultadoFilas']);								 
						
						//arrDatosAgrupacion['resultadoFilas'][0]['WEB']) = www.europalaica.org;
						define("URL_WEB_EUROPALAICA", "https://".$arrDatosAgrupacion['resultadoFilas'][0]['WEB']);
											
						header("Location: " .URL_WEB_EUROPALAICA);		
				}						
						
		}//else  !if (defined('URL_WEB_EUROPALAICA')) 
		
		//echo "<br><br>2-1 controladorLogin:logOut:cookie: ";print_r($_COOKIE);
		//echo "<br><br>2-2 controladorLogin:logOut:_SESSION: ";print_r($_SESSION);//aquí no están vacías, pero sí en index			
}
/*----------------------- Fin logOut  ------------------------------------------------------------*/


?>