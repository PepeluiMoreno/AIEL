<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: cAdmin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
En este fichero se encuentran las función relacionadas con administración aplicación:

- menuGralAdmin(), muestra el munú para el rol de administrador		
											
- cambiarExplotacion_MantenimientoAdmin(), utiliza la tabla "CONTROLMODOAPLICACION" para determinar 
  el modo actual de trabajo y poder alternar ANTENIMIENTO<->EXPLOTACIÓN.
		
- "cierreAnioPasadoAperturaAnioNuevoAdmin()": para CIERRE AÑO y APERTURA AÑO NUEVO, función 
  que a su vez llama bastantes funciones. Es un proceso crítico que requiere poner la aplicación en 
		modo MANTENIMIENTO y BACKUP  previo.
		
- Como almacén, quedan algunas funciones que se usaron anteriormente por si pudieran servir más adelante:
  encriptarCuentasCCyCEXAdmin(), desEncriptarCuentasCCyCEXAdmin(), 
		importarExcelSociosESad(),importarExcelSociosANad(),	generarTodosOrdenada().
													
OBSERVACIONES: 	
 Solo tiene acceso el rol de Administrador
--------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()----------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya
hay activada una sesión, aunque no es un error puede mostrar un "Notice", 
si warning esta activado. Para evitar estos Notices, uso la función 
is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 cAdmin.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 cAdmin.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()------------------------------------------------*/


/*--------------------------- Inicio menuGralAdmin ------------------------------------------------
Se llega desde la página al entrar con el login como Administrador si solo tiene este rol, 
o si tiene más roles, desde el menú general de roles del usuario, o desde los enlace de la línea 
de link navegación

Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de socio (CODROL = 011) y 
se muestran en el menú lateral.
Se pueden añadir un enlaces a archivos para descargarlo, por ejemplo manuales estaría en el cuerpo 
debajo de la imagen de "ESCUELA LAICA".

LLAMADA: controladorLogin.php:validarLogin():call_user_func(); 
         (llamada directa sin pasar por index.php), y también si es un gestor desde:
									controladorLogin.php:menuRolesUsuario():login/vRolInc.php y además al moverse 
									de un rol a otro en menú izdo, o en línea superior de enlaces

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 2021-01-15: No necesita cambios PDO, probado PHP 7.3.21
-------------------------------------------------------------------------------------------------*/
function menuGralAdmin()
{				
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	else//if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
	{ 
		 //echo "<br><br>0-1 cAdmin:menuGralAdmin:SESSION:";print_r($_SESSION);	
  	//echo "<br><br>0-2 cAdmin:menuGralAdmin:_POST:"; print_r($_POST);
			
			require_once './modelos/modeloUsuarios.php';			
			require_once './modelos/modeloEmail.php';
			require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';		
			
			$cabeceraCuerpo = "ADMINISTRACIÓN DE LA APLICACIÓN DE GESTIÓN DE SOCI@S"."<br /><br />";			
			$textoCuerpo = 	"<br /><br /><br /><br />Para ciertas tareas de administración es necesario poner la aplicación en modo \"MANTENIMIENTO\".
																			<br /><br />También se recomienda hacer un <strong>BACKUP</strong>, antes de realizar tareas de administración y mantenimiento que puedan producir cambios en la BBDD.
																			<br /><br />En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),
																															y se efectuarán las mismas modificaciones en la BBDD MySQL que en modo explotación y quedarán grabadas de forma permanente en la BBDD.";		
			$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
			$datosMensaje['textoComentarios'] = "<br /><br /><br /><br /><strong>ERROR</strong> al mostrar los el menú correspondiente al Rol de Administración. 
																																									Prueba de nuevo pasado un rato. 
																																								<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
			$datosMensaje['textoBoton'] = 'Salir de la aplicación';
			$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';																																								
																			
			$nomScriptFuncionError = ' cAdmin.php:menuGralAdmin(). Error: ';			
			$tituloSeccion  = 'Administración';				

			/*------- Inicio control MODOTRABAJO  ------------------------------*/
			$textoEnlace ='Entrar';

			if ($_SESSION['vs_MODOTRABAJO']  == 'MANTENIMIENTO') 
			{	$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <span class='textoRojo9Right'><strong>".$_SESSION['vs_MODOTRABAJO']."</strong></span>. NO ESTÁ ACCESIBLE a soci@s y gestor@s sin rol de mantenimiento";													
					$textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>".$textoEnlace;						
			}
			else //$_SESSION['vs_MODOTRABAJO'] == 'EXPLOTACION'
			{ $textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>".$_SESSION['vs_MODOTRABAJO']."</strong>. SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";	   
			}		
			/*------- Fin control MODOTRABAJO  ---------------------------------*/
			
			/*------- Inicio para link "Entrar" --------------------------------*/
			$_SESSION['vs_HISTORIA']['enlaces'][0]['link'] = "index.php?controlador=controladorLogin&accion=validarLogin";
			$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = $textoEnlace;
			$_SESSION['vs_HISTORIA']['pagActual'] = 0;					
   /*------- Fin para link "Entrar" -----------------------------------*/
			/*------------ Inicio navegación para gestor rol Admistrador  ------*/
			$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cAdmin&accion=menuGralAdmin";
			$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Administrador";			
			$_SESSION['vs_HISTORIA']['pagActual']=2;	
			//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);					
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
			/*------------ Fin navegación para gestor rol Admistrador  ---------*/

			/*---- Inicio Funciones con links Fin navegación rol Admistrador ---*/
			$resFuncionRol = buscarRolFuncion('011');//en modeloUsuarios.php, probado error OK

			//echo "<br><br>1 cAdmin:menuGralAdmin:resFuncionRol: ";print_r($resFuncionRol);
			
			if ($resFuncionRol['codError'] !== '00000')		
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": buscarRolFuncion()".$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			}			
			else //($resFuncionRol['codError'] == '00000')
			{	
				$_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];//Se podrá acceder desde cualquier sitio			

				$enlacesArchivos = array();
				/* Si quisieramos añadir unos enlaces a unos archivos para descargarlos, estarían 
							en el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente:
				$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
				$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
				$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';	
				$enlacesArchivos[2]['link'] = '../documentos/COORDINACION/EL_MANUAL_GESTOR_Coordinador.pdf';
				$enlacesArchivos[2]['title'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';
				$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';			
				*/			
    $textoCuerpo = $textoEstadoMantenimiento. $textoCuerpo;
				
				require_once './vistas/login/vFuncionRolInc.php';
				vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);
	
			}//else ($resFuncionRol['codError'] == '00000')	 
			/*---- Fin Funciones con links Fin navegación rol Admistrador -------*/
			
 }//else ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
}
/*------------------------------ Fin menuGralAdmin ------------------------------------------------*/ 

/*------------------- Inicio cambiarExplotacion_MantenimientoAdmin()  ------------------------------
Cambiar el modo de trabajo de la aplicación de Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN"

Al logarse en "controladorLogin.php:validarLogin()" busca en la tabla 'CONTROLMODOAPLICACION' el 
campo [MODOAPLICACION'] que podrá tener solo los valores "MANTENIMIENTO" o "EXPLOTACIÓN". 
Se asignó a la variable $_SESSION['vs_MODOTRABAJO'] el valor encontrado en la tabla.

Aquí el formulario: "vCambiarExplotacion_MantenimientoAdminInc.php", se elige cambiar el modo 
de trabajo, y una vez cambiado se actualiza la tabla 'CONTROLMODOAPLICACION' al nuevo modo,
y se cambia el 	la variable $_SESSION['vs_MODOTRABAJO'] = $nuevoModoAplicacion, para que sea
efectiva para toda la aplicación y para mostrar en la navegación horizontal en el caso de que el 
nuevo estado sea Mantenimiento se pondrá en color rojo en: 
$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = $textoEnlace;

RECIBE: $_SESSION['vs_MODOTRABAJO'] que se asignan en controladorLogin:validarLogin()
        o en esta función cambiarExplotacion_MantenimientoAdmin()
DEVUELVE: una variable $_SESSION['vs_MODOTRABAJO'] = 'EXPLOTACION' o 'MANTENIMIENTO'	actual.	

LLAMADA: desde el menú lateral del rol de administrador "Mantenimiento<->Explotación"
LLAMA: modeloUsuarios.php:buscarModoAp(),modeloAdmin.php:cambiarControlModoAdmin()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
vistas/admin/vCambiarExplotacion_MantenimientoAdminInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: Probada PHP 7.3.21, aquí no usa PDO directamente, pero si algunas funciones llmadas
--------------------------------------------------------------------------------------------------*/
function cambiarExplotacion_MantenimientoAdmin()
{	
	//echo "<br /><br />0-1 cAdmin:cambiarExplotacion_MantenimientoAdmin:SESSION: ";print_r($_SESSION);	
	//echo "<br /><br />0-2 cAdmin:cambiarExplotacion_MantenimientoAdmin:_POST: "; print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI' )
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }	
	else//if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
	{ 	
		require_once './modelos/modeloAdmin.php';			
		require_once './modelos/modeloUsuarios.php';
		require_once './vistas/admin/vCambiarExplotacion_MantenimientoAdminInc.php'; 	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
				
		$cabeceraCuerpo = "CAMBIAR MODO MANTENIMIENTO<->EXPLOTACIÓN"."<br /><br />";
		$datosMensaje['textoCabecera'] = $cabeceraCuerpo;	

		$textoCuerpo = "<br /><br />El usuario Administrador, es el único que puede cambiar el modo de trabajo de la aplicación de Gestión de soci@s.
																		<br /><br />- El modo normal de funcionamiento, llamado modo \"<i>EXPLOTACIÓN</i>\", permite el acceso a TODOS los usuari@s: Soci@s, Gestor@s y Administrador@s.
																		<br /><br /><br />- El modo de funcionamiento, llamado modo \"<i>MANTENIMIENTO</i>\", sólo permite el acceso al Administador y los usuari@s con rol de mantenimiento. 																
																		<br /><br />.En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),	y se efectuarán las mismas modificaciones en la BBDD que en modo explotación.		
																		<br /><br />.Al entrar en Gestión de Soci@s (la de usuario y contraseña), se mostrará un aviso indicando que está en modo matenimiento.
																		<br /><br />.Antes de realizar ciertas tareas de administración y mantenimiento que puedan producir cambios en la BBDD, se recomienda hacer un <strong>BACKUP</strong> ";																	
		$textoError = "<br /><br />Error al cambiar el modo de trabajo de la aplicación de Gestión de Soci@s. Prueba de nuevo pasado un rato. 
																<br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org
																<br /><br /><br /><strong>No se ha modificado el modo de trabajo de la aplicación. </strong> ";									

		$nomScriptFuncionError = ' cAdmin.php:cambiarExplotacion_MantenimientoAdmin(). Error: ';	
		$tituloSeccion  = 'Administración';
		
		/*------------ Inicio navegación para gestor rol Admistrador  ---*/
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cAdmin&accion=adminPrincipal";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "MANTENIMIENTO-EXPLOTACIÓN";			
		$_SESSION['vs_HISTORIA']['pagActual']=3;			
		//echo "<br><br>1 cAdmin:cambiarExplotacion_MantenimientoAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);	
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");			
		/*------------ Fin navegación para gestor rol Admistrador  -------*/

		if (!isset($_SESSION['vs_MODOTRABAJO']) || ( $_SESSION['vs_MODOTRABAJO'] !== 'MANTENIMIENTO' && $_SESSION['vs_MODOTRABAJO'] !== 'EXPLOTACION') )//Error 
		{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError." Variables de sesión no válidas _SESSION['vs_MODOTRABAJO']: ".$_SESSION['vs_MODOTRABAJO'] );		
				$datosMensaje['textoComentarios'] = $textoError;	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		}
		else	// !if (!isset($_SESSION['vs_MODOTRABAJO']) || ( $_SESSION['vs_MODOTRABAJO'] ....
		{	
				$modoActualAplicacion = $_SESSION['vs_MODOTRABAJO'];
				
				if ($modoActualAplicacion == 'MANTENIMIENTO') 
				{	$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>".$modoActualAplicacion."</strong>. NO ESTÁ ACCESIBLE a soci@s y gestor@s sin rol de mantenimiento";							
				}
				else //$modoActualAplicacion == 'EXPLOTACION'
				{ $textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>".$modoActualAplicacion."</strong>. SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";	   
				}	
				//echo "<br /><br />1 cAdmin:cambiarExplotacion_MantenimientoAdmin:_SESSION: ";print_r($_SESSION);

				if (isset($_POST['SiCambiarModoAp']) || isset($_POST['salirSinCambiarModoAp']))  
				{    			
					if (isset($_POST['salirSinCambiarModoAp']))
					{$datosMensaje['textoComentarios'] = "No se ha modificado el modo de trabajo de la aplicación, sigue en modo:  <strong>".$modoActualAplicacion."</strong>".$textoCuerpo;	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //(!isset($_POST['salirSinCambiarModoAp']))
					{
						//echo "<br /><br />2-1 cAdmin:cambiarExplotacion_MantenimientoAdmin:_SESSION['vs_MODOTRABAJO']: ";print_r($_SESSION['vs_MODOTRABAJO']);
		
						$nuevoModoAplicacion = $_POST['nuevoModoAplicacion'];
						
						if ($nuevoModoAplicacion == 'EXPLOTACION') 
						{	$textoEnlace = 'Entrar';
								$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>".$nuevoModoAplicacion."</strong>. SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";							
						}
						else //$nuevoModoAplicacion =='MANTENIMIENTO'
						{ $textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>Entrar";	
								$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>".$nuevoModoAplicacion."</strong>. NO ESTÁ ACCESIBLE a soci@s y gestor@s sin rol de mantenimiento";
						}				
						
						$resCambiarControlModoAdmin = cambiarControlModoAdmin($nuevoModoAplicacion);//en modeloAdmin.php, probado error OK
						
						//echo "<br /><br />2-2 cAdmin:cambiarExplotacion_MantenimientoAdmin:resCambiarControlModoAdmin: ";print_r($resCambiarControlModoAdmin);
							
						if ($resCambiarControlModoAdmin['codError'] !== "00000")
						{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'cambiarControlModoAdmin()'.$resCambiarControlModoAdmin['codError'].": ".$resCambiarControlModoAdmin['errorMensaje']);															
								$datosMensaje['textoComentarios'] = $textoError."<strong> Sigue en modo: ".$modoActualAplicacion."</strong>";								
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
						}		
						else //($resCambiarControlModoAdmin['codError'] == '00000')
						{
								//--- Navegación: Pone en nivel cero ['textoEnlace'] el correspondiente MODOTRABAJO ---
								$_SESSION['vs_MODOTRABAJO'] = $nuevoModoAplicacion; 
								$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = $textoEnlace;
								$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");			
								//-------------------------------------------------------------------------------------
								
								$datosMensaje['textoComentarios'] = $textoEstadoMantenimiento."<br /><br />".$textoCuerpo;
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);													
						}					
					}//else !isset($_POST['salirSinCambiarModoAp'])				
				}//if (isset($_POST['SiCambiarModoAp']) || isset($_POST['salirSinCambiarModoAp']))  	
					
				else //!$_POST()
				{	//echo "<br /><br />3 cAdmin:cambiarExplotacion_MantenimientoAdmin:_POST: ";print_r($_POST);					
						$textoCuerpo = $textoEstadoMantenimiento."<br /><br />".$textoCuerpo;  
		
						vCambiarExplotacion_MantenimientoAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);										
				}
		}//else  !if (!isset($_SESSION['vs_MODOTRABAJO']) || ( $_SESSION['vs_MODOTRABAJO'] ....
	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
}
/*--------------------------- Fin cambiarExplotacion_MantenimientoAdmin() ------------------------*/


/*========================= INICIO proceso de cierreAnioPasadoAperturaAnioNuevoAdmin()==============
Se realizan el proceso de  "Cierre de año finalizado y apertura de año nuevo"

Se debe	ejecutar solo una vez por año "el día 1 de enero a las 00:01 horas", o lo más próximo posible "
Tardará cierto tiempo en ejecutarse este proceso, recorre y modifica muchas tablas.
No salir del navegador hasta que aparezca un mensaje indicando que le proceso ha finalizado, o un aviso de error

Se podría hacer con un CRON, pero como es proceso de riesgo es mejor hacerlo manualmente
==================================================================================================*/
/*------------------- Inicio cierreAnioPasadoAperturaAnioNuevoAdmin() ------------------------------
Esta función actualiza los datos de las tablas para cierre Año Finalizado e inicio de Año Nuevo

El formulario puede permitir dos posiblidades de ejecución:

- La opción REAL: Se ejecutará para año actual date('Y') el día 1 de enero en BBDD: europalaica_com,
  (o para probar en europalaica_com_copia, o europalaica_com_desarrollo, etc.) 

- La opción de PRUEBA ANTICIPADA ANTES DE AÑO NUEVO: se ejecutará para el año siguiente al actual, 
		es decir se podría probar en NOVIEMBRE o DICIEMBRE.
		Esta opcion de prueba está restrigida y por eso solo aparece como opción en el formulario cuando 
		se está trabajando con que sea las versiones de prueba de la aplicación:
		Cuando sea $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_copia' 
		o $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo'). 
		Así solo sólo se podrá modificar las BBDD: "europalaica_com_copia", o "europalaica_com_desarrollo", 
		ya que si se pudiese hacer con la BBDD real se CORROMPERÍA TODA LA BBDD de modo irreversible.

- Al final se mostrará por pantalla información con el resultado del proceso en campo ['textoComentarios']
  con datos generales sobre las moficación efectuadas, o también información en caso de error. 

En la función modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin() se detallan los procesos a
a realizar. 

RECIBE: $_POST del formulario "vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php"
													
LLAMADA: /vistas/admin/vCierreAnioPasadoAperturaAnioNuevo.php
LLAMA: 
modelos/modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin() y buscarControlActualizadoAnio()
vistas/mensajes/vMensajeCabSalirNavInc.php, detalla filas en tablas actualizadas, o errores
modelos/modeloEmail.php:emailErrorWMaster(), informa si hay errores


REQUISITOS: requiere rol de administrador de la aplicación

OBSERVACIONES: **** OJO SE DEBE EJECUTAR EL DÍA 1 DE ENERO DE CADA AÑO Y UNA SÓLA VEZ ****
               **** ANTES PONER MODO MANTENIMIENTO Y HACER UN BACKUP DE LA BASE DE DATOS *****
															**** PROBAR ANTES EN BBDD "europalaica_com_copia" O "europalaica_com_desarrollo" ***

PARA PRUEBAS: incluso probar antes del 1 de enero, ir a las versiones de prueba de Gestión de Soci@s
URLs: "europalaica.com/usuarios_copia", o en "europalaica.com/usuarios_desarrollo", de esto modo sólo
se pueden modificar las correspondientes BBDD: "europalaica_com_copia" o "europalaica_com_desarrollo"
Para probar antes del 1 de enero eleige el botón correspondiente (Y+1)
----------------------------------------------------------------------------------------------------*/
function cierreAnioPasadoAperturaAnioNuevoAdmin()
{	
	//echo "<br><br>0-1 cAdmin:cierreAnioPasadoAperturaAnioNuevoAdmin:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cAdmin:cierreAnioPasadoAperturaAnioNuevoAdmin:_POST: "; print_r($_POST);echo "<br><br>";
		
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else//if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
	{
		require_once './modelos/modeloAdmin.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php'; 
		require_once './modelos/modeloEmail.php';
			
		$datosMensaje['textoCabecera'] = "CIERRE DE AÑO Y APERTURA DE AÑO NUEVO"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al ejecutar el proceso de 'Cierre de año pasado y apertura de año nuevo'. 
																																							No se ha podido realizar el proceso.
																																							<br /><br />Comprueba el funcionamiento de la aplicación y el ESTADO DE LAS TABLAS IMPLICADAS en el proceso 
																																							(por si hubiese que restauralas de nuevo a partir del BACKUP previo).
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin(). Error: ';	
		$tituloSeccion  = "Administrar";
			
		$reActualizarCuotasSociosAnioNuevo['codError'] = '00000';
		$reActualizarCuotasSociosAnioNuevo['errorMensaje'] = '';		
		$reActualizarCuotasSociosAnioNuevo['textoComentarios'] = '';		
				
		//	require_once './vistas/plantillasGrales/vTemporizadorInc.php'; Para intentar poner un temporizador
		//	vTemporizadorInc($tituloSeccion,$enlacesSeccIzda,$navegacion); //aun no funciona bien
		//------------------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cAdmin&accion=actualizarCuotasSociosAnioNuevoAdmin";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Cierre Año-Inicio Año Nuevo";			
		$_SESSION['vs_HISTORIA']['pagActual']=3;			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");				
		//------------------------------------
				
		if (isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) || isset($_POST['salirSinCierreAnio']))  	
		{
			if (isset($_POST['salirSinCierreAnio']))
			{ $datosMensaje['textoComentarios'] = "No se ha realizado el Cierre de Año Pasado y Apertura de Año Nuevo y no se han modificado ninguna tabla";	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}	
			else //if ( isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) ) 
			{	
				if ( isset($_POST['anioActual']) )//opción año REAL: Se ejecutará para año actual date('Y') en BBDD: europalaica_com, o europalaica_com_copia, o europalaica_com_desarrollo, etc. 
				{ 					 
						$anio = $_POST['anioActual'];// = date('Y'), es la REAL para hacer el día 1 de enero, o después.	
				}		
				elseif ( isset($_POST['anioActualUnoMas']) )//La opción de PRUEBA antes del nuevo año: se ejecutará para el año siguiente al actual, es decir $anioNuevo+1 = date('Y')+1
				{ 
						/*Inicio SOLO PARA PRUEBAS: para probar antes del probar antes del 1 de enero. Esta opción soy aparecerá disponible en los botones del formulario, 
							cuando se ejecute en versiones de prueba URLs: "www.europalaica.org/usuarios_copia", o en "www.europalaica.org/usuarios_desarrollo", 
							ya que será alterada la BBDD de modo irreversible,	por eso sólo se podrá ejecutar en las BBDD: "europalaica_com_copia", o "europalaica_com_desarrollo". 
							por eso el siguiente if para controlar que sea las versiones de prueba con "$dirHome".					
							Nota: el siguiente if de $dirHome, no sería necesario pues ya está controlado en el formulario previo, pero por si hay algún despiste le dejo también aquí*/
						
						$dirHome = getcwd();//devuelve el directorio home desde donde se ha ejecutado el index.php
						
						if (($dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_copia' || $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo') )									
						{ 													   
								$anio = $_POST['anioActualUnoMas'];//= date('Y') +1 para PRUEBAS: fecha simulada para probar antes del 1 enero por ejemplo el 20 de diciembre												
						}	
						/*----- Fin SOLO PARA PRUEBAS -----------------------------------------------------------------------*/
				}//elseif ( isset($_POST['anioActualUnoMas']) )
				
				$reActualizarCuotasSociosAnioNuevo = mCierreAnioPasadoAperturaAnioNuevoAdmin($anio);
				
				//echo "<br><br>2 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:reActualizarCuotasSociosAnioNuevo: ";print_r($reActualizarCuotasSociosAnioNuevo); 

				if ($reActualizarCuotasSociosAnioNuevo['codError'] !== '00000')
				{																																					
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reActualizarCuotasSociosAnioNuevo['codError'].": ".$reActualizarCuotasSociosAnioNuevo['errorMensaje'].": ".
																																															$reActualizarCuotasSociosAnioNuevo['textoComentarios']);				
						$datosMensaje['textoComentarios'] .= "<br /><br />".$reActualizarCuotasSociosAnioNuevo['textoComentarios'];
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				} 
				else //$reActualizarCuotasSociosAnioNuevo['codError']=='00000'
				{ /*-- Mostrará en pantalla un amplio informe sobre algunas de la operaciones realizadas en esta función --*/			
				
						$datosMensaje['textoComentarios'] = "<br /><br />".$reActualizarCuotasSociosAnioNuevo['textoComentarios'];				
						//echo "<br><br>3 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin:datosMensaje: ";print_r($datosMensaje);		
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}		
			}//else if ( isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) ) 
				
		}//if (isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) || isset($_POST['salirSinCierreAnio']))
		
		else // !if (isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) || isset($_POST['salirSinCierreAnio']))  	
		{
			//echo "<br><br>4 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin:_SESSION['vs_MODOTRABAJO']: ";print_r($_SESSION['vs_MODOTRABAJO']);
			
			if ($_SESSION['vs_MODOTRABAJO'] !== 'MANTENIMIENTO')//estará en modo = EXPLOTACION
			{  
					$datosMensaje['textoComentarios'] = "Para realizar Cierre de año pasado y apertura de año nuevo <strong>DEBE ESTAR EN MODO MANTENIMIENTO</strong>
																																										<br /><br /><br /><br />Debes cambiar el modo de trabajo en la función - MANTENIMIENTO<->EXPLOTACIÓN";	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //$_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
			{			
				/*---- Inicio buscar en la tabla "CONTROLES" el campo tiene el valor ACTUALIZADO =SI/NO, para el valor "$anioActual", -----*/			
			
				$anioActual = date('Y');	
			
				$resBuscarControlActualizadoAnio = buscarControlActualizadoAnio('CONTROLES',$anioActual);//en modeloAdmin.php, incluye conexion() solo devolverá una fila [0]
			
				//echo "<br><br>5 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin:resBuscarControlActualizadoAnio: ";print_r($resBuscarControlActualizadoAnio);			
								
				if ($resBuscarControlActualizadoAnio['codError'] !== '00000')// error sistema			
				{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resBuscarControlActualizadoAnio['codError'].": ".$resBuscarControlActualizadoAnio['errorMensaje'].": ".
																																																$resBuscarControlActualizadoAnio['textoComentarios']);				
						$datosMensaje['textoComentarios'] = "<br /><br />".$resBuscarControlActualizadoAnio['textoComentarios'];						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}
				else// $resBuscarControlActualizadoAnio['codError'] == '00000'
				{	
					$estadoActualizacion['anioActual'] = $anioActual;
					$estadoActualizacion['actualizadoAnioY'] = $resBuscarControlActualizadoAnio['resultadoFilas'][0]['ACTUALIZADO'];//para valor de $anioActual
					$datosMensaje['textoComentarios'] ='';	
					
					//echo "<br><br>6 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin:estadoActualizacion: ";print_r($estadoActualizacion);		
		
					require_once './vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php';		 				
					vCierreAnioPasadoAperturaAnioNuevoInc($tituloSeccion,$estadoActualizacion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);								
				}//else $resBuscarControlActualizadoAnio['codError'] == '00000'	
		
			}//else _SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')	
		}//else !if (else // !if (isset($_POST['anioActual']) || isset($_POST['anioActualUnoMas']) || isset($_POST['salirSinCierreAnio'])) )  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_ROL_011']  == 'SI')
}
/*--------------------------- Fin actualizarCuotasSociosAnioNuevoAdmin() ---------------------------*/



/*== INICIO GESTIÓN ROLES ASIGNAR-ELIMINAR Administracion,Mantenimiento Y LISTAS =================*/
/*================================================================================================*/

/*------------------- Inicio menuPermisosRolesAdmin()  ---------------------------------------------
Desde el menú del rol de "Administración", se llama al menú de asignación-eliminación-lista de permisos
de "Roles" a USUARIOS (registrados en la Aplicación de Gestión de Soci@s), que se pueden 
asignar desde el rol de "Administración" y que permite llamar a las páginas: 

- ASIGNAR-ANULAR-LISTAR ROL DE ADMINISTRACIÓN  
- ASIGNAR-ANULAR-LISTAR ROL DE MANTENIMIENTO (permite trabajar en la aplicación aunque esté bloqueada para 
  los demás usuarios )

NOTA: Estos roles se pueden asignar a USUARIOS que además son SOCIOS y también a USUARIOS NO SOCIOS,
por ejemplo el "Administrador de la aplicación" no necesitaría ser socio, solo estar anotado en la tablas 
USUARIO (actualmente CODUSER = 1), y para sus datos en la tabla "MIEMBRO" (CODUSER = 1)

Uso exclusivo desde el rol "Administración" = '011'
													
LLAMADA: desde menú izdo de rol Administración	"Permisos de Roles"					

LLAMA: vistas/admin/vMenuPermisosRolesAdmin.php

Desde ese menú con "href" se podrá llamar a: 
cAdmin.php:asignarCoordinacionAreaBuscar(),asignarAdministracionRolBuscar(),asignarMantenimientoRolBuscar()
cPresidente.php:asignarCoordinacionAreaBuscar(),asignarAdministracionRolBuscar(),asignarMantenimientoRolBuscar()

OBSERVACIONES: no necesita cambio PDO. PHP 7.3.21 
--------------------------------------------------------------------------------------------------*/
function menuPermisosRolesAdmin()
{
	//echo "<br><br>0-1 cAdmin.php:menuPermisosRolesAdmin:_POST: "; print_r($_POST);	
	//echo "<br><br>0-2 cAdmin.php:menuPermisosRolesAdmin:_SESSION: "; print_r($_SESSION);		

 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{				
		$tituloSeccion = "Administración";			

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cAdmin&accion=menuPermisosRolesAdmin";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Permisos de Roles: Administración, Mantenimiento";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		require_once './vistas/admin/vMenuPermisosRolesAdminInc.php'; 	
		vMenuPermisosRolesAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
 }// else if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')	
}
//--------------------------- Fin menuPermisosRolesAdmin() -----------------------------------------


/*== INICIO GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL Administracion  ===============================*/

/*-------------------------- Inicio asignarAdministracionRolBuscar ----------------------------------
Es el inicio de la gestión de permisos del rol de Administración 

Se muesta un formulario que permite:

- Con el botón "LISTA ROL Administración" mostrará una tabla con los datos de todos 
  los USUARIOS con rol de Administracion

- "BUSCAR" los datos de un MIEMBRO por los siguientes campos del formulario: email o AP1,AP2,NOM
(que pueden ser tanto de USUARIOS que además son SOCIOS y como USUARIOS NO SOCIOS), 
y a continuación ver si ya tiene rol de Administración "011" en tabla "USUARIOTIENEROL" 

Según la situación se podrá después asignar/eliminar el rol de "Administración" a ese USUARIO

.Si no hay una asignación de rol de Administración para ese USUARIO se envía a "vAsignarAdministracionRolInc .php", 
para asignar rol Administración.	

.Si el USUARIO sí tiene asignado el rol de Administración	lo envía a "vAnularAsignacionAdminRolInc.php" 
(para anular asignación)
														
LLAMADA: cAdmin:menuPermisosRolesAdmin.php desde vMenuPermisosRolesAdminInc.php 

LLAMA: modeloPresCoord.php:buscarMiembroApesEmail(),
modelos/libs/validarCampos.php:validarEmail()
modelos/modeloUsuarios.php:buscarUnRolUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom()
vistas/admin/vAsignaAdminRolBuscarInc.php,vAsignarAdminRolInc.php,vAnularAsignacionAdminRolInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php 	 		
modelos/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)
-------------------------------------------------------------------------------------------------*/								
function asignarAdministracionRolBuscar()//acaso mas adecuado function asignarEliminarAdministracionRolBuscar()
{
	//echo "<br><br>0-1 cAdmin:asignarAdministracionRolBuscar:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cAdmin:asignarAdministracionRolBuscar:_POST: ";print_r($_POST);

 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/admin/vAsignarAdminRolBuscarInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR-ANULAR ROL DE ADMINISTRACIÓN";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Administración. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cAdmin.php:asignarAdministracionRolBuscar(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cAdmin&accion=asignarAdministracionRolBuscar";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Permisos rol Administración";		
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
  //echo "<br><br>1 cAdmin:asignarAdministracionRolBuscar:navegacion: ";print_r($navegacion);  			
		//------------ fin navegacion -----------------------------------------------	

		if (!isset($_POST) || empty($_POST)) 
		{ 
	   $datSocioAsignar = '';
				vAsignarAdminRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
		}
		elseif (isset($_POST['NoAsignar']) ||  isset($_POST['Cancelar']))
		{ 
				$datosMensaje['textoComentarios'] = "Has salido sin modificar el estado previo del rol de Administración"; 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
		elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail']) /*|| isset($_POST['NoAsignar'])*/) 
		{	
			if (isset($_POST['BuscarEmail']))
			{
				require_once  './modelos/libs/validarCampos.php';	
				$resulValidar['datosFormSocio']['EMAIL'] = validarEmail($_POST['datosFormSocio']['EMAIL'],"");
				
				//echo "<br><br>2-1 cAdmin:asignarAdministracionRolBuscar:resulValidar: "; print_r($resulValidar); 
					
				if ($resulValidar['datosFormSocio']['EMAIL']['codError'] !== '00000')	
				{$resulValidar['datosFormSocio']['codError'] = $resulValidar['datosFormSocio']['EMAIL']['codError'];
					$resulValidar['datosFormSocio']['errorMensaje'] ="Error email: ".$resulValidar['datosFormSocio']['EMAIL']['errorMensaje'];						
				}	
				else
				{$resulValidar['datosFormSocio']['codError'] = '00000';				
				}
			}//if (isset($_POST['BuscarEmail']))
			elseif (isset($_POST['BuscarApes']))		
			{
				require_once  './modelos/libs/validarCamposSocioPorGestor.php';
				$resulValidar = validarCamposFormBuscarGestorApesNom($_POST);//solo valida que no estén vacios todos
				
				//echo "<br><br>2-2 cAdmin:asignarAdministracionRolBuscar:resulValidar: "; print_r($resulValidar); 
			}// (isset($_POST['BuscarApes']))
			
			if ($resulValidar['datosFormSocio']['codError'] !=='00000')	
			{
					vAsignarAdminRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
			} 	
			else //$resulValidar['datosFormSocio']['codError']=='00000'
			{					
				$datSocio = buscarMiembroApesEmail($resulValidar['datosFormSocio']);//en modeloPresCoord.php
				
				//echo "<br><br>3 cAdmin:asignarAdministracionRolBuscar:datSocio: "; print_r($datSocio); 
									
				if ($datSocio['codError'] !== '00000')
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarMiembroApesEmail(): '.$datSocio['codError'].": ".$datSocio['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}   
				elseif ($datSocio['numFilas'] === 0)
				{					
					$resulValidar['datosFormSocio']['errorMensaje'] = 'No se ha encontrado ninguna persona registrada con esos datos';							

					vAsignarAdminRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				elseif ($datSocio['numFilas'] >1 )
				{
					$resulValidar['datosFormSocio']['errorMensaje'] ='Hay mas de una persona en la Base de Datos que tienen esos apellidos, buscar por los dos apellidos y nombre o mejor por email';				

					vAsignarAdminRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				else //$datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
				{
					$datSocioAsignar['datosFormSocio'] = $datSocio['resultadoFilas']['datosFormSocio']; 
			 	$codRol = "011"; //rol Administracion
					
		 		require_once './modelos/modeloUsuarios.php';				

			 	$resSocioRol = buscarUnRolUsuario($datSocioAsignar['datosFormSocio']['CODUSER']['valorCampo'],$codRol);//en modeloUsuarios.php';
				
					//echo "<br><br>4-1 cAdmin:asignarAdministracionRolBuscar:resSocioRol: "; print_r($resSocioRol);

					if ($resSocioRol['codError'] !== '00000')
 				{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSocioRol['codError'].": ".$resSocioRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//$resSocioRol['codError']=='00000
					{
						if ($resSocioRol['numFilas'] === 1)// tiene rol Administracion
						{	
       //echo "<br><br>5-1 cAdmin:asignarAdministracionRolBuscar:datSocioAsignar: ";print_r($datSocioAsignar);								

       require_once './vistas/admin/vAnularAsignacionAdminRolInc.php'; 
 						vAnularAsignacionAdminRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);			
							
						}//if ($resSocioRol['numFilas'] === 1)
						else //$resSocioRol['numFilas'] === 0) --> $no tiene Rol Administracion
						{							
       //echo "<br><br>5-2 cAdmin:asignarAdministracionRolBuscar:datSocioAsignar: ";print_r($datSocioAsignar);					

							require_once './vistas/admin/vAsignarAdminRolInc.php';
       vAsignarAdminRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
							
						}//else $resSocioRol['numFilas'] === 0) --> $no tiene Rol Administracion
					}//else $resSocioRol['codError']=='00000
				}//else $datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
			}//else $resulValidar['datosFormSocio']['codError']=='00000' 
		}//elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
}
/*------------------------------- Fin asignarAdministracionRolBuscar ------------------------------*/

/*------------------------------- Inicio mostrarListaAdministracionRol() ----------------------------
Se forma y muestra una tabla con la lista actual de USUARIOS con el rol de Administración 
(tanto de USUARIOS que además son SOCIOS y también a USUARIOS NO SOCIOS).
Se mostrarán alguno datos de contacto (APE Y NOM, email de la tabla MIEMBRO y otros)

Para la búsqueda utiliza las siguientes tablas: MIEMBRO,y USUARIOTIENEROL con CODROL = '011'

LLAMADA: cAdmin&accion:asignarAdministracionRolBuscar() que incluye botón para el formulario
vistas/vMostrarListaAdminRolInc.php que contiene el botón "LISTA ROL Administración" 

LLAMA: modelos/modeloPresCoord.php:buscarDatosGestoresRoles()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
vistas/admin/vMostrarListaAdminRolInc()
vistas/mensajes/vMensajeCabSalirNavInc.php';
modeloEmail.php:emailErrorWMaster()
	
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 	
	--------------------------------------------------------------------------------------------------*/	
function mostrarListaAdministracionRol()
{
	//echo "<br><br>0-1 cAdmin.php:mostrarListaAdministracionRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cAdmin.php:mostrarListaAdministracionRol:_POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{				
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "LISTA CON ROL DE ADMINISTRACIÓN";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido mostrar la lista de personas que tienen asignado el rol de 'Administración'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cAdmin.php:mostrarListaAdministracionRol(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarAdministracionRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=mostrarListaAdministracionRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar lista con rol Administración";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//echo "<br><br>1 cAdmin:mostrarListaAdministracionRol:navegacion: ";print_r($navegacion);	
		//------------ fin navegacion -------------------------------------------------		

		$codRol = "011"; //Administracion
		require_once './modelos/modeloPresCoord.php';			
		
		$resDatosGestoresRol =	buscarDatosGestoresRoles($codRol);//
		
		//echo "<br><br>2 cAdmin.php:mostrarListaAdministracionRol:resDatosGestoresRol: ";print_r($resDatosGestoresRol);
		
		if ($resDatosGestoresRol['codError'] !== '00000')
		{
		 	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosGestoresRol['codError'].": ".$resDatosGestoresRol['errorMensaje']);	
		 	//$resDatosGestoresRol['arrMensaje']['textoComentarios']="Error del sistema al buscar la lista de rol de Administracion, vuelva a intentarlo pasado un tiempo ";			
		 	vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
		}      
		else //$resDatosGestoresRol['codError']=='00000'
		{ 
				$resDatosGestoresRol['navegacion'] = $navegacion;
				
				require_once './vistas/admin/vMostrarListaAdminRolInc.php';
				vMostrarListaAdminRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosGestoresRol,$navegacion);
		}		

	}//	else if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')  		
}
/*--------------------------- Fin mostrarListaAdministracionRol() ---------------------------------*/

/*----------------------- Inicio asignarAdministracionRol -------------------------------------------
Asigna los permisos de de Administracion (tanto para USUARIOS que además son SOCIOS y como para 
USUARIOS NO SOCIOS), para ello inserta una fila en la tabla "USUARIOTIENEROL" con el CODROL ='011' 
a ese USUARIO

Envía email a USUARIO (email de la tabla MIEMBRO) comunicándole que se ha asignado el rol de Administracion, 
e incluye archivos con manuales y documento protección de datos para firmar.
																										
LLAMADA: vistas/admin/vAsignarAdminRolInc.php 
y a su vez desde cAdmin.php:asignarAdministracionRolBuscar()
		
LLAMA:
modelos/modeloUsuarios.php:insertarUsuarioRol()
NO modeloEmail.php:emailAsignadaAdministracionRol(),emailErrorWMaster()
modeloEmail.php:emailAsignadoRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.phP
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.	

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)						
-------------------------------------------------------------------------------------------------*/								
function asignarAdministracionRol()
{ 
	//echo "<br><br>0-1 cAdmin:asignarAdministracionRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cAdmin:asignarAdministracionRol:_POST: ";print_r($_POST);
 
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR ROL DE ADMINISTRACIÓN";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Administración. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cAdmin.php:asignarAdministracionRol(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarAdministracionRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=asignarAdministracionRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Asignar rol Administración";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
  //echo "<br><br>1 cAdmin:asignarAdministracionRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------	
	
		if ( !isset($_POST)) //No entrará nunca pero lo dejo por si acaso
		{ //echo "<br><br>1 cAdmin:asignarAdministracionRol:_POST: ";print_r($_POST);
		}	
		elseif (isset($_POST['SiAsignar']) || isset($_POST['NoAsignar'])) 
		{		
			if (isset($_POST['NoAsignar']))
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin asignar el rol de Administración";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiAsignar']))
			{
				$arrValoresInser['CODUSER']['valorCampo'] = $_POST['datosFormSocio']['CODUSER'];		
				$arrValoresInser['CODROL']['valorCampo'] = "011";
				
		 	//	echo "<br><br>2 cAdmin:asignarAdministracionRol:arrValoresInser: ";print_r($arrValoresInser);
				
				require_once './modelos/modeloUsuarios.php';
						
				$resAsignarRol = insertarUsuarioRol($arrValoresInser);
				//echo "<br><br>3 cAdmin:asignarAdministracionRol:resAsignarRol: ";print_r($resAsignarRol); 
							
				if ($resAsignarRol['codError'] !== "00000")
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloUsuarios.php:asignarAdministracionRol(): ".$resAsignarRol['codError'].": ".$resAsignarRol['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
				}
				else //$resAsignarRol['codError']=="00000"
				{
					$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
					$email = $_POST['datosFormSocio']['EMAIL'];
				
					$textoPantallaAsignacion = 'Se ha asignado el rol de Administración a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
															Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la asignación.<br /><br />
															En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
															aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de Administración en Europa Laica.<br /><br />
															Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org (o por correo postal al domicilio legal de Europa Laica)';
		
					$nomRol = "Administración";		
					
					//$dirManualRol = "../documentos/PRESIDENCIA/EL_MANUAL_GESTOR_Administracion.pdf";
					//$nomManualRol = "EL_MANUAL_GESTOR_Administracion.pdf";		
					$dirManualRol = NULL;
					$nomManualRol = NULL;					

					$reEmailComunicarAsigna = emailAsignadoRol($_POST['datosFormSocio'],$nomRol,	$dirManualRol,	$nomManualRol);//en modeloEmail.php		
				
					//echo"<br><br>4 cAdmin:asignarAdministracionRol:reEmailComunicarAsigna: ";print_r($reEmailComunicarAsigna);
									
					if ($reEmailComunicarAsigna['codError'] !== '00000')
					{
						$reEmailComunicarAsigna['textoComentarios'] = $textoPantallaAsignacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático
						al socio/a '.$nomApe1.' Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la asignación del rol de Administracion';	
						$datosMensaje['textoComentarios'] = $reEmailComunicarAsigna['textoComentarios'];					

						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarAsigna['codError'].": ".$reEmailComunicarAsigna['errorMensaje'].$reEmailComunicarAsigna['textoComentarios']);	
					}
					else
					{ 
						$datosMensaje['textoComentarios'] = $textoPantallaAsignacion;
					}									
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}//else $resAsignarRol['codError']=="00000"
			}//else isset($_POST['SiAsignar'])  
		}//(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
}
/*------------------------------- Fin asignarAdministracionRol -------------------------------------*/

/*------------------------------- Inicio eliminarAsignacionAdministracionRol ------------------------
Elimina la asignación del rol de Administración para ello elimina la fila en la tabla "USUARIOTIENEROL" 
con el CODROL ='011' a ese USUARIO, y que se ha buscado desde cAdmin.php:asignarAdministracionRolBuscar().

Elimina el Rol= 011 de Administración en tabla USUARIOTIENEROL para el CODUSER correspondiente

Envía email al USUARIO (email de la tabla MIEMBRO) comunicando anulación de Rol Administración

LLAMADA: vistas/admin/vAnularAsignacionAdminRolInc.php	 desde botón "Eliminar asignación"	
y a su vez desde cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: 
modelos/modeloUsuarios:eliminarUsuarioTieneRol()
modeloEmail.php:emailEliminadaAsignacionRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)
-------------------------------------------------------------------------------------------------*/
function eliminarAsignacionAdministracionRol()
{
 //echo "<br><br>0-1 cAdmin:eliminarAsignacionAdministracionRol:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-2 cAdmin:eliminarAsignacionAdministracionRol:_POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ELIMINAR ASIGNACIÓN DE ROL DE ADMINISTRACIÓN";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido eliminar la asignación del rol de Administración. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cAdmin.php:eliminarAsignacionAdministracionRol(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarAdministracionRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=eliminarAsignacionAdministracionRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar asignación rol Administración";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cAdmin:eliminarAsignacionAdministracionRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------	

		if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar'])) 
		{		
			if (isset($_POST['Cancelar']))//no entrará nunca pero lo dejo por si decido un tratamiento aislado
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin eliminar la asignación del rol de Administración";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminar']))
			{	
			  //echo "<br><br>1 cAdmin:eliminarAsignacionAdministracionRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);				
     				
     $codRol = "011";     
     $conexionLinkDB=NULL;			
					
     require_once './modelos/modeloUsuarios.php';	
					
					$reEliminarAdministracionRol = eliminarUsuarioTieneRol('USUARIOTIENEROL',$_POST['datosFormSocio']['CODUSER'],$codRol,$conexionLinkDB);//en modeloUsuarios.php, error:ok, incluye $conexion; 	
					//echo "<br><br>2 cAdmin:eliminarAsignacionAdministracionRol:reEliminarAdministracionRol: "; print_r($reEliminarAdministracionRol);			
     
					if ($reEliminarAdministracionRol['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarAdministracionRol['codError'].": ".$reEliminarAdministracionRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //($reEliminarAdministracionRol['codError'] == '00000')
					{
						$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
				 	$email = $_POST['datosFormSocio']['EMAIL'];

					 $textoPantallaAnulacion = 'Se ha anulado el rol de Administracion a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
														                  	Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la anulación del rol de Administración.';							

						$datosSocio = $_POST;
      $nomRol = "Administración";								

      $reEmailComunicarElimina = emailEliminadaAsignacionRol($datosSocio, $nomRol);//modeloEmail.php, probado error ok				
						
						//echo"<br><br>3 cAdmin:eliminarAsignacionAdministracionRol:reEmailComunicarElimina: ";print_r($reEmailComunicarElimina);
								
						if ($reEmailComunicarElimina['codError'] !== '00000')
						{
							$reEmailComunicarElimina['textoComentarios'] = $textoPantallaAnulacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático a '.$nomApe1.' . 
							Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la eliminación de la asignación del rol de Administración';	
							$datosMensaje['textoComentarios'] = $reEmailComunicarElimina['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarElimina['codError'].": ".$reEmailComunicarElimina['errorMensaje'].$reEmailComunicarElimina['textoComentarios']);					
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $textoPantallaAnulacion;
						}				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}//else $reEliminarAdministracionRol['codError'] == '00000'
			}//elseif (isset($_POST['SiEliminar']))		
		}//if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar']))
 }//else if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')

}
/*---------------------------- Fin eliminarAsignacionAdministracionRol -----------------------------*/

/*==== FIN GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL Administracion ==================================*/


/*==== INICIO GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL MANTENIMIENTO ================================*/

/*-------------------------- Inicio asignarMantenimientoRolBuscar ------------------------------------
Es el inicio de la gestión de permisos de rol de Mantenimiento

Se muesta un formulario que permite:

- Con el botón "LISTA CON ROL Mantenimiento" mostrará una tabla con los datos de todos 
  los socios con rol de Mantenimiento = "012"

- "BUSCAR" los datos de un MIEMBRO por los siguientes campos del formulario: email o AP1,AP2,NOM 
(que pueden ser tanto de USUARIOS que además son SOCIOS y como USUARIOS NO SOCIOS), 
para  ver si ya tiene rol de Mantenimiento "012" en tabla "USUARIOTIENEROL" 
Según la situación se podrá después asignar/eliminar el rol de Mantenimiento

.Si no hay una asignación de rol de Mantenimiento para ese socio se envía a "vAsignarMantenimientoRolInc .php" 
para asignar rol

.Si el socio sí tiene asignado el rol de Mantenimiento	lo envía a "vAnularAsignacionMantenimientoRolInc.php" 
(para anular asignación)
														
LLAMADA: cAdmin:menuPermisosRolesAdmin.php desde vMenuPermisosRolesPresInc.php 

LLAMA: modeloPresCoord.php:buscarMiembroApesEmail(),
modelos/libs/validarCampos.php:validarEmail()
modelos/modeloUsuarios.php:buscarUnRolUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom()
vistas/admin/vAsignarMantenimientoRolBuscarInc.php,vAsignarMantenimientoRolInc.php,vAnularAsignacionMantenimientoRolInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php 	 		
modelos/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)
-------------------------------------------------------------------------------------------------*/								
function asignarMantenimientoRolBuscar()
{
	//echo "<br><br>0-1 cAdmin:asignarMantenimientoRolBuscar:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cAdmin:asignarMantenimientoRolBuscar:_POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/admin/vAsignarMantenimientoRolBuscarInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR-ANULAR ROL DE MANTENIMIENTO";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Mantenimiento. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cAdmin.php:asignarMantenimientoRolBuscar(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cAdmin&accion=asignarMantenimientoRolBuscar";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Permisos rol Mantenimiento";	
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------	

		if (!isset($_POST) || empty($_POST)) 
		{ $datSocioAsignar ='';
				vAsignarMantenimientoRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
		}
		elseif (isset($_POST['NoAsignar']) ||  isset($_POST['Cancelar']))
		{ 
				$datosMensaje['textoComentarios'] = "Has salido sin modificar el estado previo del rol de Mantenimiento"; 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
		elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail']) /*|| isset($_POST['NoAsignar'])*/) 
		{				
			if (isset($_POST['BuscarEmail']))
			{
				require_once  './modelos/libs/validarCampos.php';	
				$resulValidar['datosFormSocio']['EMAIL'] = validarEmail($_POST['datosFormSocio']['EMAIL'],"");
				
				//echo "<br><br>2-1 cAdmin:asignarMantenimientoRolBuscar:resulValidar: "; print_r($resulValidar); 
					
				if ($resulValidar['datosFormSocio']['EMAIL']['codError'] !== '00000')	
				{$resulValidar['datosFormSocio']['codError'] = $resulValidar['datosFormSocio']['EMAIL']['codError'];
					$resulValidar['datosFormSocio']['errorMensaje'] ="Error email: ".$resulValidar['datosFormSocio']['EMAIL']['errorMensaje'];						
				}	
				else
				{$resulValidar['datosFormSocio']['codError'] = '00000';				
				}
			}//if (isset($_POST['BuscarEmail']))
			elseif (isset($_POST['BuscarApes']))		
			{
				require_once  './modelos/libs/validarCamposSocioPorGestor.php';
				$resulValidar = validarCamposFormBuscarGestorApesNom($_POST);//solo valida que no estén vacios todos
				
				//echo "<br><br>2-2 cAdmin:asignarMantenimientoRolBuscar:resulValidar: "; print_r($resulValidar); 
			}// (isset($_POST['BuscarApes']))
			
			if ($resulValidar['datosFormSocio']['codError'] !=='00000')	
			{
					vAsignarMantenimientoRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
			} 	
			else //$resulValidar['datosFormSocio']['codError']=='00000'
			{					
				$datSocio = buscarMiembroApesEmail($resulValidar['datosFormSocio']);//en modeloPresCoord.php
				
				//echo "<br><br>3 cAdmin:asignarMantenimientoRolBuscar:datSocio: "; print_r($datSocio); 
									
				if ($datSocio['codError'] !== '00000')
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarMiembroApesEmail(): '.$datSocio['codError'].": ".$datSocio['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}   
				elseif ($datSocio['numFilas'] === 0)
				{				
					$resulValidar['datosFormSocio']['errorMensaje'] = 'No se ha encontrado ninguna persona registrada con esos datos';							

					vAsignarMantenimientoRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				elseif ($datSocio['numFilas'] >1 )
				{
					$resulValidar['datosFormSocio']['errorMensaje'] ='Hay mas de una persona en la Base de Datos que tienen esos apellidos, buscar por los dos apellidos y nombre o mejor por email';				

					vAsignarMantenimientoRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				else //$datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
				{
					$datSocioAsignar['datosFormSocio'] = $datSocio['resultadoFilas']['datosFormSocio']; 
			 	$codRol = "012";//Mantenimiento
					
		 		require_once './modelos/modeloUsuarios.php';				

			 	$resSocioRol = buscarUnRolUsuario($datSocioAsignar['datosFormSocio']['CODUSER']['valorCampo'],$codRol);//en modeloUsuarios.php';
				
					//echo "<br><br>4-1 cAdmin:asignarMantenimientoRolBuscar:resSocioRol: "; print_r($resSocioRol);

					if ($resSocioRol['codError'] !== '00000')
 				{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSocioRol['codError'].": ".$resSocioRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//$resSocioRol['codError']=='00000
					{
						if ($resSocioRol['numFilas'] === 1)// tiene rol Mantenimiento
						{						
       require_once './vistas/admin/vAnularAsignacionMantenimientoRolInc.php'; 
 						vAnularAsignacionMantenimientoRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);							
							
						}//if ($resSocioRol['numFilas'] === 1)
						else //$resSocioRol['numFilas'] === 0) --> $no tiene Rol Mantenimiento
						{		
							require_once './vistas/admin/vAsignarMantenimientoRolInc.php';
       vAsignarMantenimientoRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
							
						}//else $resSocioRol['numFilas'] === 0) --> $no tiene Rol Mantenimiento
					}//else $resSocioRol['codError']=='00000
				}//else $datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
			}//else $resulValidar['datosFormSocio']['codError']=='00000' 
		}//elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
}
/*------------------------------- Fin asignarMantenimientoRolBuscar --------------------------------*/

/*------------------------------- Inicio mostrarListaMantenimientoRol() ------------------------------
Se forma y muestra una tabla con la lista actual de USUARIOS con el rol de Mantenimiento 
(tanto de USUARIOS que además son SOCIOS y como de USUARIOS NO SOCIOS).
Se mostrarán alguno datos de contacto (APE Y NOM, email de la tabla MIEMBRO y otros)

Para la búsqueda utiliza las siguientes tablas:
MIEMBRO,USUARIOTIENEROL y CODROL = "012"

LLAMADA: cAdmin&accion:asignarMantenimientoRolBuscar() que incluye botón para el formulario
vistas/vMostrarListaMantenimientoRolInc.php que contiene el botón "LISTA DE SOCIOS CON ROL Mantenimiento" 

LLAMA: modelos/modeloPresCoord.php:buscarDatosGestoresRoles()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
vistas/admin/vMostrarListaMantenimientoRolInc()
vistas/mensajes/vMensajeCabSalirNavInc.php';
modeloEmail.php:emailErrorWMaster()
	
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 	
	--------------------------------------------------------------------------------------------------*/	
function mostrarListaMantenimientoRol()
{
	//echo "<br><br>0-1 cAdmin.php:mostrarListaMantenimientoRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cAdmin.php:mostrarListaMantenimientoRol:_POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{				
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "LISTA CON ROL DE MANTENIMIENTO";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido mostrar la lista de personas que tienen asignado el rol de 'Mantenimiento'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cAdmin.php:mostrarListaMantenimientoRol(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarMantenimientoRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=mostrarListaMantenimientoRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar lista con rol Mantenimiento";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cAdmin:mostrarListaMantenimientoRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------			

		$codRol = "012"; //Mantenimiento
		require_once './modelos/modeloPresCoord.php';			
		
		$resDatosGestoresRol =	buscarDatosGestoresRoles($codRol);
		
		//echo "<br><br>2 cAdmin.php:mostrarListaMantenimientoRol:resDatosGestoresRol: ";print_r($resDatosGestoresRol);
		
		if ($resDatosGestoresRol['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosGestoresRol['codError'].": ".$resDatosGestoresRol['errorMensaje']);	
			//$resDatosGestoresRol['arrMensaje']['textoComentarios']="Error del sistema al buscar la lista de rol de Mantenimiento, vuelva a intentarlo pasado un tiempo ";			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
			
		}      
		else //$resDatosGestoresRol['codError']=='00000'
		{ 
		  $resDatosGestoresRol['navegacion'] = $navegacion;				
		
				require_once './vistas/admin/vMostrarListaMantenimientoRolInc.php';	
		
				vMostrarListaMantenimientoRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosGestoresRol,$navegacion);
		}	 	

	}//	else if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')  		
}
/*--------------------------- Fin mostrarListaMantenimientoRol() -----------------------------------*/

/*----------------------- Inicio asignarMantenimientoRol ---------------------------------------------
Asigna los permisos de de Mantenimiento (tanto para USUARIOS que además son SOCIOS y como para 
USUARIOS NO SOCIOS), para ello inserta una fila en la tabla "USUARIOTIENEROL" con el CODROL ='012' 
a ese USUARIO

Envía email a USUARIO (email de la tabla MIEMBRO) comunicándole que se ha asignado el rol de Mantenimiento, 
e incluye archivos con manuales y documento protección de datos para firmar.
																										
Envía email a socio comunicándole que se ha asignado el rol de Mantenimiento
																										
LLAMADA: vistas/admin/vAsignarMantenimientoRolInc.php 
y a su vez desde cAdmin.php:asignarMantenimientoRolBuscar()
		
LLAMA: 
modelos/modeloUsuarios.php:insertarUsuarioRol()
modeloEmail.php:emailAsignadoRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.phP
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.		

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)
-------------------------------------------------------------------------------------------------*/								
function asignarMantenimientoRol()
{ 
	//echo "<br><br>0-1 cAdmin:asignarMantenimientoRol:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cAdmin:asignarMantenimientoRol:_POST: ";print_r($_POST);
 
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR ROL DE MANTENIMIENTO";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Mantenimiento. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cAdmin.php:asignarMantenimientoRol(). Error: ';
		$tituloSeccion  = "Administración";	
		
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarMantenimientoRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=asignarMantenimientoRol";
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Asignar rol de Mantenimiento";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cAdmin:asignarMantenimientoRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------					

		if ( !isset($_POST)) //No entrará nunca pero lo dejo por si acaso
		{ //echo "<br><br>1 cAdmin:asignarMantenimientoRol:_POST: ";print_r($_POST);
		}	
		elseif (isset($_POST['SiAsignar']) || isset($_POST['NoAsignar'])) 
		{		
			if (isset($_POST['NoAsignar']))
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin asignar el rol de Mantenimiento al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiAsignar']))
			{			    
				//echo "<br><br>2 cAdmin:asignarMantenimientoRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);
				
				$arrValoresInser['CODUSER']['valorCampo'] = $_POST['datosFormSocio']['CODUSER'];		
				$arrValoresInser['CODROL']['valorCampo'] = "012";//Mantenimiento
				
				require_once './modelos/modeloUsuarios.php';
						
				$resAsignarRol = insertarUsuarioRol($arrValoresInser);
				//echo "<br><br>3 cAdmin:asignarMantenimientoRol:resAsignarRol: ";print_r($resAsignarRol); 
							
				if ($resAsignarRol['codError'] !== "00000")
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloUsuarios.php:asignarMantenimientoRol(): ".$resAsignarRol['codError'].": ".$resAsignarRol['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
				}
				else //$resAsignarRol['codError']=="00000"
				{
					$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
					$email = $_POST['datosFormSocio']['EMAIL'];
				
					$textoPantallaAsignacion = 'Se ha asignado el rol de Mantenimiento a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
															Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la asignación.<br /><br />
															En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
															aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de rol de Mantenimiento en Europa Laica.<br /><br />
															Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org (o por correo postal al domicilio legal de Europa Laica)';					
					
					$nomRol = "Mantenimiento";		
					
					//$dirManualRol = "../documentos/PRESIDENCIA/EL_MANUAL_GESTOR_Mantenimiento.pdf";
					//$nomManualRol = "EL_MANUAL_GESTOR_Mantenimiento.pdf";
					$dirManualRol = NULL;
					$nomManualRol = NULL;					
					
					$reEmailComunicarAsigna = emailAsignadoRol($_POST['datosFormSocio'],$nomRol,	$dirManualRol,	$nomManualRol);//en modeloEmail.php							
				
					//echo"<br><br>4 cAdmin:asignarMantenimientoRol:reEmailComunicarAsigna: ";print_r($reEmailComunicarAsigna);
									
					if ($reEmailComunicarAsigna['codError'] !== '00000')
					{
						$reEmailComunicarAsigna['textoComentarios'] = $textoPantallaAsignacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático a '.$nomApe1.
						                                            '. Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la asignación del rol de Mantenimiento';	
						$datosMensaje['textoComentarios'] = $reEmailComunicarAsigna['textoComentarios'];					

						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarAsigna['codError'].": ".$reEmailComunicarAsigna['errorMensaje'].$reEmailComunicarAsigna['textoComentarios']);	
					}
					else
					{ 
						$datosMensaje['textoComentarios'] = $textoPantallaAsignacion;
					}									
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}//else $resAsignarRol['codError']=="00000"
			}//else isset($_POST['SiAsignar'])  
		}//(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
}
/*------------------------------- Fin asignarMantenimientoRol --------------------------------------*/

/*------------------------------- Inicio eliminarAsignacionMantenimientoRol --------------------------
Elimina la asignación del rol de Mantenimiento para ello elimina la fila en la tabla "USUARIOTIENEROL" 
con el CODROL ='012' a ese USUARIO, y que se ha buscado desde cAdmin.php:asignarAdministracionRolBuscar().

Elimina el Rol= 012 de Mantenimiento en tabla USUARIOTIENEROL para el CODUSER correspondiente

Envía email al USUARIO (email de la tabla MIEMBRO) comunicando anulación de Rol Mantenimiento

vistas/admin/vAnularAsignacionMantenimientoRolInc.php'; 
				
LLAMADA: vistas/admin/vAnularAsignacionMantenimientoRolInc.php	 desde botón "Eliminar asignación"	
y a su vez desde cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA:
modelos/modeloUsuarios:eliminarUsuarioTieneRol() 
modeloEmail.php:emailEliminadaAsignacionRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.

NOTA: aunque se recibe y trabaja con el formulario $_POST['datosFormSocio'] en realidad hubiese 
sido más adecuado que se llamase $_POST['datosFormMiembro'], ya que los datos proceden 
de la tabla 'MIEMBRO' (y podrían no ser socios)
-------------------------------------------------------------------------------------------------*/
function eliminarAsignacionMantenimientoRol()
{
 //echo "<br><br>0-1 cAdmin:eliminarAsignacionMantenimientoRol:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cAdmin:eliminarAsignacionMantenimientoRol:_POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI') 	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')
	{		
		require_once "./modelos/modeloPresCoord.php";		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ELIMINAR ASIGNACIÓN DE ROL DE MANTENIMIENTO";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido eliminar la asignación del rol de Mantenimiento. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cAdmin.php:eliminarAsignacionMantenimientoRol(). Error: ';
		$tituloSeccion  = "Administración";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cAdmin&accion=asignarMantenimientoRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cAdmin&accion=eliminarAsignacionMantenimientoRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar asignación rol de Mantenimiento";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cAdmin:eliminarAsignacionMantenimientoRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------						

		if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar'])) 
		{		
			if (isset($_POST['Cancelar']))//no entrará nunca pero lo dejo por si decido un tratamiento aislado
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin eliminar la asignación del rol de Mantenimiento";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminar']))
			{	
			  //echo "<br><br>1 cAdmin:eliminarAsignacionMantenimientoRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);

     $codRol = "012";//Mantenimiento     
     $conexionLinkDB=NULL;		

     require_once './modelos/modeloUsuarios.php';										

					$reEliminarMantenimientoRol = eliminarUsuarioTieneRol('USUARIOTIENEROL',$_POST['datosFormSocio']['CODUSER'],$codRol,$conexionLinkDB);//en modeloUsuarios.php, error:ok, incluye $conexion; 	
					//echo "<br><br>2 cAdmin:eliminarAsignacionMantenimientoRol:reEliminarMantenimientoRol: "; print_r($reEliminarMantenimientoRol);			
     
					if ($reEliminarMantenimientoRol['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarMantenimientoRol['codError'].": ".$reEliminarMantenimientoRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //($reEliminarMantenimientoRol['codError'] == '00000')
					{
						$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
				 	$email = $_POST['datosFormSocio']['EMAIL'];

					 $textoPantallaAnulacion = 'Se ha anulado el rol de Mantenimiento del a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
														                  	Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la anulación del rol de Mantenimiento.';							
						
						$datosSocio = $_POST;
      $nomRol = "Mantenimiento";		

      $reEmailComunicarElimina = emailEliminadaAsignacionRol($datosSocio, $nomRol);//modeloEmail.php, probado error ok								
						
						//echo"<br><br>3 cAdmin:eliminarAsignacionAdministracionRol:reEmailComunicarElimina: ";print_r($reEmailComunicarElimina);
								
						if ($reEmailComunicarElimina['codError'] !== '00000')
						{
							$reEmailComunicarElimina['textoComentarios'] = $textoPantallaAnulacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático a '.$nomApe1.'
							Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la eliminación de la asignación del rol de Mantenimiento al socio/a';	
							$datosMensaje['textoComentarios'] = $reEmailComunicarElimina['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarElimina['codError'].": ".$reEmailComunicarElimina['errorMensaje'].$reEmailComunicarElimina['textoComentarios']);					
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $textoPantallaAnulacion;
						}				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						
					}//else $reEliminarMantenimientoRol['codError'] == '00000'
			}//elseif (isset($_POST['SiEliminar']))		
		}//if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar']))
 }//else if ($_SESSION['vs_autentificado']=='SI'  && $_SESSION['vs_ROL_011']=='SI')

}
/*---------------------------- Fin eliminarAsignacionMantenimientoRol ------------------------------*/

/*==== FIN GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL Mantenimiento ===================================*/

/*=== FIN ROLES ASIGNAR-ELIMINAR Administracion, Mantenimiento Y LISTAS ============================*/
/*==================================================================================================*/


/**************** INICIO ALMACÉN ANTIGUAS FUNCIONES AHORA NO UTILIZADAS ***************************
***************************************************************************************************
***************************************************************************************************/

/******** INICIO ENCRIPTACIÓN-DESENCRIPTACIÓN de cuentas bancarias  - CONSERVAR - *****************/

/*Inicio encriptarCuentasCCyCEXAdmin() ENCRIPTACIÓN-DESENCRIPTACIÓN de cuentas bancarias de tabla SOCIO 
Se hizo temporalmente para aumentar la seguridad en la base de datos, pero debido a que se debía permitir 
el acceso a los datos completos por parte de coordinares/as, lo quité porque pensé ya no merecía la pena
pero acaso fuese conveniente poner de nuevo como protección en la BBDD de las cuentas.
Estas funciones y otras están disponibles también en antiguas versiones de modeloSocios, modeloPresCood,
modeloTesorero,

Descripción:-Lee de la tabla SOCIO las cuentas bancarias, encripta los 
             campos CCEXTRANJERA, NUMCUENTA, y los graba de nuevo encriptados 
													en la tabla SOCIO 
												
Llama a: modelAdmin.php:encriptarCCyCEXAdmin()
Envía: 
Recibe: $reEncriptarCCyCEXAdmin, con los resultado de las operaciones

OBSERVACIONES: **** OJO SE EJECUTÓ UNA VEZ Y NO SE DEBE EJECUTAR NUNCA MAS
No se utiliza
-------------------------------------------------------------------------------------------*/
function encriptarCuentasCCyCEXAdmin()
{if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 /*	
	 echo "<br><br>0 cAdmin:encriptarCuentasCCyCEXAdmin:SESSION:";print_r($_SESSION);	
	 echo "<br><br>0 cAdmin:encriptarCuentasCCyCEXAdmin:_POST:"; print_r($_POST);
	*/
 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';

 $tituloSeccion  = "Administrar";	
 		
	$reEncriptarCCyCEXAdmin =  encriptarCCyCEXAdmin();
 echo "<br><br>1 cAdmin:encriptarCuentasCCyCEXAdmin:reEncriptarCCyCEXAdmin:";print_r($reEncriptarCCyCEXAdmin); 
 
 if ($reEncriptarCCyCEXAdmin['codError']!=='00000')
 {
	 $resEmailErrorWMaster = emailErrorWMaster($reEncriptarCCyCEXAdmin['codError'].": ".
		                                          $reEncriptarCCyCEXAdmin['errorMensaje']);	
		vMensajeCabSalirNavInc($tituloSeccion,$reEncriptarCCyCEXAdmin['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
 else //$reEncriptarCCyCEXAdmin['codError']=='00000'
 { 
  vMensajeCabSalirNavInc($tituloSeccion,$reEncriptarCCyCEXAdmin['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
}
//--------------------------- Fin encriptarCuentasCCyCEXAdmin ------------------------

/*------------------- Inicio desEncriptarCuentasCCyCEXAdmin()    -------------------
Descripción:-Lee de la tabla SOCIO  las cuentas bancarias, y desencripta los 
             campos CCEXTRANJERA, NUMCUENTA, y los obtine como un array 
												
Llama a: modeloAdmin:desEncriptarCCyCEXAdmin()
Envía: 
Recibe: $reDesEncriptarCCyCEXAdmin, con los resultado de las operaciones

OBSERVACIONES: SE TENDRÁ QUE EJECURTA UNA FUNCIÓN PARECIDA PARA BANCO SANTANDER NA19
No se utiliza
-------------------------------------------------------------------------------------------*/
function desEncriptarCuentasCCyCEXAdmin()
{if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 /*	
	 echo "<br><br>0 cAdmin:desEncriptarCuentasCCyCEXAdmin:SESSION:";print_r($_SESSION);	
	 echo "<br><br>0 cAdmin:desEncriptarCuentasCCyCEXAdmin:_POST:"; print_r($_POST);
	*/
 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';

 $tituloSeccion  = "Administrar";	
 		
	$reDesEncriptarCCyCEXAdmin =  desEncriptarCCyCEXAdmin();
 echo "<br><br>1 cAdmin:desEncriptarCuentasCCyCEXAdmin:reDesEncriptarCCyCEXAdmin:";print_r($reDesEncriptarCCyCEXAdmin); 
 
 if ($reDesEncriptarCCyCEXAdmin['codError']!=='00000')
 {
	 $resEmailErrorWMaster = emailErrorWMaster($reDesEncriptarCCyCEXAdmin['codError'].": ".
		                                          $reDesEncriptarCCyCEXAdmin['errorMensaje']);	
		vMensajeCabSalirNavInc($tituloSeccion,$reDesEncriptarCCyCEXAdmin['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
 else //$reDesEncriptarCCyCEXAdmin['codError']=='00000'
 { 
  vMensajeCabSalirNavInc($tituloSeccion,$reDesEncriptarCCyCEXAdmin['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
}
//--------------------------- Fin desEncriptarCuentasCCyCEXAdmin ------------------------

/******************* FIN ENCRIPTACIÓN-DESENCRIPTACIÓN de cuentas bancarias de tabla SOCIO *********/



/********* INICIO PROCESOS IMPORTAR LOS ANTIGUOS DATOS DE SOCIOS DE EL DESDE EXCEL  ****************
- conservar por si alguna vez hubiese que importar pudiera servir de ejemplo  

SE UTILIZÓ PARA IMPORTAR LOS DATOS QUE HABÍA EN EXCEL DE Estal y de Andalucía PREVIO A LA CREACIÓN 
DE ESTA APLICACIÓN DE GESTIÓN DE SOCI@S
***************************************************************************************************/
/*------- Inicio importarExcelSociosESad()  Vienen del fichero Excel Estatal ---
Importa la tabla EXCELESTATAL, que procede del antiguo archivo excel 
correspondiente a ESTATAL, a la tabla de tabajo EXCELTODOS

Llama: modeloAdmin.php:importarExcelSociosES()
------------------------------------------------------------------------------*/
function importarExcelSociosESad()
{$cad1 ="Location:./index.php?controlador=";
	$cad2 ="controladorLogin";
	$cad3 ="&amp;accion=";
	$cad4 ="validarLogin";
	$cad  =$cad1.$cad2.$cad3.$cad4;
	//$cad="Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin";
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { //header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
   header ($cad);
 }
 /*	
	 echo "<br><br>0cAdmin:importarExcelSociosESad:SESSION:";print_r($_SESSION);	
	 echo "<br><br>0cAdmin:importarExcelSociosESad:_POST:"; print_r($_POST);
	*/
 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';
		
 $tituloSeccion  = "Administrar";
	
	$reImportar = importarExcelSociosES();
 //echo "<br><br>1cAdmin:importarExcelSociosESad:reImportar:"; print_r($reImportar); 
 
 if ($reImportar['codError']!=='00000')
 {	
	 $resEmailErrorWMaster = emailErrorWMaster($reImportar['codError'].": ".$reImportar['errorMensaje']);	
	 $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
  $reImportarDatosSocio['arrMensaje']['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
		vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
 else //$reImportar['codError']=='00000'
 { 	
	  $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
   $reImportarDatosSocio['arrMensaje']['textoComentarios']="Se han importado los socios de la BBDD Excel";			
	  vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } //$reImportar['codError']=='00000'
}
//--------------------------- Fin importarExcelSociosESad() --------------------

/*----- Inicio importarExcelSociosANad() Vienen del fichero Excel Andalucía ----
Importa la tabla EXCELANDALUCIA, que que procede del antiguo archivo excel 
correspondiente a ANDALUCIA, a la tabla de tabajo 'EXCELTODOS'

Llama: modeloAdmin.php:importarExcelSociosAN();
------------------------------------------------------------------------------*/
function importarExcelSociosANad()
{$cad1 ="Location:./index.php?controlador=";
	$cad2 ="controladorLogin";
	$cad3 ="&amp;accion=";
	$cad4 ="validarLogin";
	$cad  =$cad1.$cad2.$cad3.$cad4;
	//$cad="Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin";
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { //header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
   header ($cad);
 }
 /*	
	 echo "<br><br>0cAdmin:importarExcelSociosANad:SESSION:";print_r($_SESSION);	
	 echo "<br><br>0cAdmin:importarExcelSociosANad:_POST:"; print_r($_POST);
	*/
 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';

 $tituloSeccion  = "Administrar";

	$reImportar = importarExcelSociosAN();
 //echo "<br><br>1cAdmin:importarExcelSociosANad:reImportar:"; print_r($reImportar); 
 
 if ($reImportar['codError']!=='00000')
 { 
	 $resEmailErrorWMaster = emailErrorWMaster($reImportar['codError'].": ".$reImportar['errorMensaje']);	
	 $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
  $reImportarDatosSocio['arrMensaje']['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
		vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
 else //$reImportar['codError']=='00000'
 { 
	  $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
   $reImportarDatosSocio['arrMensaje']['textoComentarios']="Se han importado los socios de la BBDD Excel";			
	  vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } //$reImportar['codError']=='00000'
}
//--------------------------- Fin importarExcelSociosANad ----------------------

/*------------------- Inicio generarTodosOrdenada()    -------------------------
Descripción: Busca todos los datos de EXCELTODOS y los inserta en USUARIO, SOCIO, ...
Llama a: Funcion modeloAdmin:generarTodosOrd()
OBSERVACIONES: NOMBRE DE LA FUNCIÓN POCO ADECUADO
------------------------------------------------------------------------------*/
function generarTodosOrdenada()
{$cad1 ="Location:./index.php?controlador=";
	$cad2 ="controladorLogin";
	$cad3 ="&amp;accion=";
	$cad4 ="validarLogin";
	$cad  =$cad1.$cad2.$cad3.$cad4;
	//$cad="Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin";
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_ROL_011'] !== 'SI')
 { //header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
   header ($cad);
 }
 /*	
	 echo "<br><br>0cAdmin:generarTodosOrdenada:SESSION:";print_r($_SESSION);	
	 echo "<br><br>0cAdmin:generarTodosOrdenada:_POST:"; print_r($_POST);
	*/
 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';

 $tituloSeccion  = "Administrar";

	$reImportar = generarTodosOrd();
 //echo "<br><br>1cAdmin:importarExcelSociosAdmin:reImportar:"; print_r($reImportar); 
 
 if ($reImportar['codError']!=='00000')
 {
	 $resEmailErrorWMaster = emailErrorWMaster($reImportar['codError'].": ".$reImportar['errorMensaje']);	
	 $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
  $reImportarDatosSocio['arrMensaje']['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
		vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 
 else //$reImportar['codError']=='00000'
 { 
  $reImportarDatosSocio['arrMensaje']['textoCabecera']='Importar desde la BBDD Excel';		
  $reImportarDatosSocio['arrMensaje']['textoComentarios']="Se han importado los socios de la BBDD Excel";			
  vMensajeCabSalirNavInc($tituloSeccion,$reImportarDatosSocio['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } //$reImportar['codError']=='00000'
}
//--------------------------- Fin generarTodosOrdenada -------------------------

/********* FIN PROCESOS IMPORTAR LOS ANTIGUOS DATOS DE SOCIOS DE EL DESDE EXCEL  ******************/
/**************************************************************************************************/



/**************** FIN ALMACÉN ANTIGUAS FUNCIONES AHORA NO UTILIZADAS ******************************
***************************************************************************************************
***************************************************************************************************/
?>