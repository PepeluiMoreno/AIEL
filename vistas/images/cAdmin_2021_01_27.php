<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: cAdmin.php

PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: En este fichero se encuentran las función relacionadas con administración aplicación:
             Poner en estado MANTENIMIENTO<->EXPLOTACIÓN, "CIERRE DE AÑO", y actualmente quedan
													funciones que se usaron anteriormente (encriptar CC bancos) y algunas de pruebas que
													habría que ir eliminando.

Agustín 2018-12-11: 
menuGralAdmin(): Añado información en texto cuerpo
adminPrincipal(): Añado información texto cuerpo sobre estado MANTENIMIENTO, fechas sistema y BBDD

modoMantenimientoAdmin(): Añadida para poner la aplicación en modo  MANTENIMIENTO
modoExplotacionAdmin(): Añadida para poner la aplicación en modo EXPLOTACIÓN 

Elimino la parte estadísticas que ahora dependen de cPresidente.php: cEstadisticasAdmin(),
cExportarExcelInformeAnualSec(),cExportarExcelEstadisticasSinUso(),cExportarExcelEstadisticasAltasBajas(),
también elimino las correspondientes en archivos modeloAdmin.php y de vistas/tesorero
													
OBSERVACIONES: 

- Para CIERRE AÑO, se usa la función "actualizarCuotasSociosAnioNuevoAdmin()",

SI SE QUIERE PROBAR ANTES DEL AÑO NUEVO en "europalaica_com_copia" hay que engañar al sistema 
con las fechas renombrar líneas 590, 593 de la siguiente forma:

La variable $añoNuevo = date('Y') se pone a $añoNuevo = date('Y')+1, 
después volver a poner $añoNuevo = date('Y') 
	
- Solo tiene acceso el administrador con codRol=0, 
--------------------------------------------------------------------------------------------------*/

/*define('APLICATION_ROOT', getcwd());echo "<br />cAdmin:inicio:".APLICATION_ROOT;*/


/*---------------------------- Inicio session_start()----------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya
hay activada una sesion, aunque no es un error puede mostrar un "Notice", 
si warning esta activado. Para evitar estos Notices, uso la función 
is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 cTesorero.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 cTesorero.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()------------------------------------------------*/


/*--------------------------- Inicio menuGralAdmin ------------------------------------------------
Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de Aministración (CODROL=0) 
y se muestran en el menú lateral, por ahora solo he puesto la función "adminPrincipal", pero los 
links de menús se podríanañadir como funciones en la tabla FUNCIONES y después en ROLTIENEFUNCION

Se pueden añadir un enlaces a archivos para descargarlo, estarían en el cuerpo debajo de la 
imagen de "ESCUELA LAICA".

LLAMADA: controladorLogin.php:validarLogin():call_user_func($cad4); 
         (llamada directa sin pasar por index.php)

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 2021-0115: No necesita cambios PDO, probado PHP 7.3.21
-------------------------------------------------------------------------------------------------*/
function menuGralAdmin()
{		
	echo "<br><br>0-1 cAdmin:menuGralAdmin:SESSION:";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin:menuGralAdmin:_POST:"; print_r($_POST);//tien valor de 
	//echo "<br><br>0-3 cAdmin:menuGralAdmin:_GET:"; print_r($_GET);
	//echo "<br><br>0-4 cAdmin:menuGralAdmin:_REQUEST:"; print_r($_REQUEST);		
	
	require_once './modelos/modeloUsuarios.php';			
	require_once './modelos/modeloEmail.php';	
	
	$cabeceraCuerpo = "ADMINISTRACIÓN DE LA APLICACIÓN DE GESTIÓN DE SOCI@S"."<br /><br />";
	
	$textoCuerpo = 	"<br /><br /><br /><br />Para ciertas tareas de administración es necesario poner la aplicación en modo <strong>MANTENIMIENTO</strong>.
																	<br /><br />También se recomienda hacer un <strong>BACKUP</strong>, antes de realizar tareas de administración y mantenimiento que puedan producir cambios en la BBDD.
																	<br /><br />En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),
																															y se efectuarán las mismas modificaciones en la BBDD MySQL que en modo explotación y quedarán grabadas de forma permanente en la BBDD.															
																	<br /><br /><br /><br />Para ver el menú de opciones del administrador haz clic en el lado izquierdo en
																	<a href='./index.php?controlador=cAdmin&amp;accion=adminPrincipal' title='-Administrar aplicación'> -Administrar aplicación.</a>";																	
	
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
	$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
	$nomScriptFuncionError = ' cAdmin.php:menuGralAdmin(). Error: ';			
	$tituloSeccion  = 'Administración';	
		
 //Nota: se podría buscar tambien modo applicacion para pasarlo	******* y mostrar texto o extra	de parte de string en $_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace']	
	
 //nota cuando entra con solo rol de administrado, viene sin $_SESSION['vs_enlacesSeccIzda'] recbe 
	//SESSION:Array ( [vs_contadorIntentos] => 1 
	//[vs_HISTORIA] => Array ( [enlaces] => Array ( [0] => Array ( [link] => index.php?controlador=controladorLogin&accion=validarLogin [textoEnlace] => xEntrar ) ) [pagActual] => 0 ) 
	//[vs_autentificado] => SI [vs_CODUSER] => 0 [vs_CODROL] => 0 )	
	
	//PARA $_SESSION['vs_autentificadoAdmin'] = 'SI'; 

	//BUSCAR AQUÍ CODROL
 //A: O recibes en $_SESSION['vs_enlacesSeccIzda'] (de validarLogin() o de menuRolesUsuario() ) 
	//B: o buscar aquí en tabla   buscarRolesUsuario($_SESSION['vs_CODUSER'],$codRol);
	//function buscarUnRolUsuario($codUser, $codRol)	  
	 			
		$_SESSION['vs_autentificadoPres'] = 'NO';
		$codRol = '0';
		$resRolesUser =  buscarUnRolUsuario(	$_SESSION['vs_CODUSER'] , $codRol);//en modeloUsuarios.php	probado error
		
		echo "<br><br>1-1 cAdmin:menuGralAdmin:resRolesUser: "; print_r($resRolesUser);		
		
		if ($resRolesUser['codError'] !== '00000' || $resRolesUser['numFilas'] <= 0)
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'buscarRolesUsuario()'.$resRolesUser['codError'].": ".$resRolesUser['errorMensaje']);		
			$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
			$datosMensaje['textoComentarios'] = $textoEstadoMantenimiento."<br /><br /><br /><br /><strong>ERROR</strong> al mostrar los el menú correspondiente al Rol de Administración. 
																																								Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
			
			require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
		}
		else
		{ $_SESSION['vs_autentificadoAdmin'] = 'SI';				
		}			
	/*	if (in_array("0", array_column($_SESSION['vs_enlacesSeccIzda'], 'CODROL')))//bien cuando tiene mas de un rol usuario									
	{//$_SESSION['vs_MODOTRABAJO'] = 'MANTENIMIENTO';// o $_SESSION['vs_CODROL'] = '200';
		$_SESSION['vs_autentificadoAdmin'] = 'SI';
	}	*/
	echo "<br><br>0-5 cAdmin:menuGralAdmin:_SESSION: ";print_r($_SESSION);
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoAdmin'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
																	
	/*------------ Inicio navegación para gestor rol Admistrador CODROL = 0 ---*/
	/*$_SESSION['vs_HISTORIA']['enlaces'][1]['link']="index.php?controlador=cAdmin&accion=menuGralAdmin";
	$_SESSION['vs_HISTORIA']['enlaces'][1]['textoEnlace']="Información general para administrador en menuGralAdmin";			
	$_SESSION['vs_HISTORIA']['pagActual']=1;	
	*/
	$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cAdmin&accion=menuGralAdmin";
	$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Administrador";			
	$_SESSION['vs_HISTORIA']['pagActual']=2;				
	
	//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);					
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
	/*------------ Fin navegación para gestor rol Admistrador CODROL = 0 ------*/
	
	require_once './modelos/modeloUsuarios.php';
	//$resFuncionRol = buscarRolFuncion($_SESSION['vs_CODROL']);//se graban dentro los errores con insertarError() 
	$resFuncionRol = buscarRolFuncion('0');//se graban dentro los errores con insertarError() 

	echo "<br><br>3 cAdmin:menuGralAdmin:resFuncionRol: ";print_r($resFuncionRol);
	
	if ($resFuncionRol['codError'] !== '00000')		
	{
		require_once './modelos/modeloEmail.php';	
		$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);		
		$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
		$datosMensaje['textoComentarios'] = $textoEstadoMantenimiento."<br /><br /><br /><br /><strong>ERROR</strong> al mostrar los el menú correspondiente al Rol de Administración. 
		                                     Prueba de nuevo pasado un rato. 
																																						<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	}			
	else //($resFuncionRol['codError'] == '00000')
	{	
		$enlacesSeccId = $resFuncionRol['resultadoFilas'];			

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

		require_once './vistas/login/vFuncionRolInc.php';
		vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);			
	
	}//else ($resFuncionRol['codError'] == '00000')	 

}
/*------------------------------ Fin menuGralAdmin ------------------------------------------------*/ 

/*------------------- Inicio adminPrincipal()  ----------------------------------------------------
Agustín 2018-12-11: Añado más información en texto cuerpo sobre estado de MANTENIMIENTO 
y fechas del sistema y BBDD

Muestra en la vista vAdminPrincipalInc.php, la página de principal de funciones disponibles 
para administración de la aplicación de Gestión de Soci@s: cambiar modo explotación<->mantenimiento
Cierre año, ...

En ella muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello usará la variable
$_SESSION['vs_MODOTRABAJO'] que se asignó o no en controladorLogin(), en el caso de estado de 
mantenimiento.

LLamada: desde el menú lateral del rol de administrador
LLama: vAdminPrincipalInc.php, controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modelos/modeloAdmin.php:fechaHoraServidor_BBDD().
--------------------------------------------------------------------------------------------------*/
function adminPrincipal_bien_old()
{	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }

	//echo "<br><br>0cAdmin:adminPrincipal:SESSION:";print_r($_SESSION);	
	//echo "<br><br>0cAdmin:adminPrincipal:_POST:"; print_r($_POST);
	//echo "<br><br>0 cAdmin:adminPrincipal:_GET:"; print_r($_GET);
		
		
	$datosMensaje['textoCabecera'] = 'Menú principal para administrar la aplicación';	
	$datosMensaje['textoComentarios'] = "";
	/*$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar órdenes de cobro cuotas domiciliadas de socios/as a archivo SEPA XML para B. Santander. 
	                                     No se ha podido exportar orden de cobro cuotas socios/as SEPA XML.
                                      <br /><br />Comprueba el ESTADO DE LAS ÓRDENES DE COBRO CORRESPONDENDIENTES A LAS REMESAS (por si hubiese que anular el registro).
                                      <br /><br />Prueba de nuevo pasado un rato. 
						                                Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
	$nomScriptFuncionError = ' cAdmin.php:adminPrincipal(). Error: ';	*/
	
	//$resExportar['codError'] = '00000';		

	$tituloSeccion  = "Administración";			
	
	$enlacesSeccId = $resFuncionRol['resultadoFilas'];//????	
	
//--	
   $_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cAdmin&accion=adminPrincipal";
			$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Menú principal para administrar la aplicación";			
			$_SESSION['vs_HISTORIA']['pagActual']=2;			
			//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");			
//--			
	
	require_once './modelos/modeloAdmin.php';	
	$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
	//echo "<br><br>1 cAdmin:adminPrincipal:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);
	
	if ($fechaHoraServidor_BBDD['codError'] !=='00000')
	{	
		require_once './modelos/modeloEmail.php';
  $resEmailErrorWMaster = emailErrorWMaster($fechaHoraServidor_BBDD['codError'].": ".$fechaHoraServidor_BBDD['errorMensaje']);	
		
	 //$fechaHoraServidor_BBDD['arrMensaje']['textoCabecera'] ='Menú principal para administrar la aplicación';	
		//$fechaHoraServidor_BBDD['arrMensaje']['textoComentarios'] ="Error del sistema en cAdmin.php:adminPrincipal() al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(),
		//                                     vuelve a intentarlo pasado un tiempo ";
  $datosMensaje['textoComentarios'] = "Error del sistema en cAdmin.php:adminPrincipal() al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(),
		                                     vuelve a intentarlo pasado un tiempo ";
																																							
  require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		//vMensajeCabSalirNavInc($tituloSeccion,$fechaHoraServidor_BBDD['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	}
	else 
	{		
			if (isset($_SESSION['vs_MODOTRABAJO']) && $_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
			{$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO MANTENIMIENTO: Entrar";			
		
 		$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>
																															<br /><br />Solo está accesible para el usuario -adming- y los usuari@s autorizados para realizar tareas de mantenimiento. 
																															En modo de mantenimiento, las funciones usadas los usuari@s autorizados, son las mismas que las usadas en modo normal de explotación,
																															y se efectuarán las mismas modificaciones en la BBDD, que pueden ser irreversibles.";																																									
			}
			else
			{$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO EXPLOTACIÓN: Entrar";			
				$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo nprmal de <strong>EXPLOTACIÓN</strong> - SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s-";		
			}		
/*
   $_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cAdmin&accion=adminPrincipal";
			$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Menú principal para administrar la aplicación";			
			$_SESSION['vs_HISTORIA']['pagActual']=2;			
			//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");				
*/
			$cabeceraCuerpo = "Administración: menú general";	
			
			$textoCuerpo = $textoEstadoMantenimiento."<br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
			"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  
			
			$datosMensaje['textoComentarios'] = $textoCuerpo;

			require_once './vistas/admin/vAdminPrincipalInc.php'; 	
			vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);
			//vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$datosMensaje);
	}
}
//--------------------------- Fin adminPrincipal_bien_old -----------------------------------------
function adminPrincipal_borrar()
{	
	echo "<br><br>0-1 cAdmin:adminPrincipal:SESSION:";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin:adminPrincipal:_POST:"; print_r($_POST);
	//echo "<br><br>0-3 cAdmin:adminPrincipal:_GET:"; print_r($_GET);
	
 //if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoAdmin'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }
		
	$cabeceraCuerpo = "adminPrincipalModo MANTENIMIENTO-EXPLOTACIÓN"."<br /><br />";
	//$textoEstadoMantenimiento = '';//acaso se use
	$textoCuerpo = "<br /><br /><br /><br />Para ciertas tareas de administración es necesario poner la aplicación en modo <strong>MANTENIMIENTO</strong>.
																	<br /><br />También se recomienda hacer un <strong>BACKUP</strong>, antes de realizar tareas de administración y mantenimiento que puedan producir cambios en la BBDD.
																	<br /><br />En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),
																															y se efectuarán las mismas modificaciones en la BBDD MySQL que en modo explotación y quedarán grabadas de forma permanente en la BBDD.															
																	<br /><br /><br /><br />Para ver el menú de opciones del administrador haz clic en el lado izquierdo en
																	<a href='./index.php?controlador=cAdmin&amp;accion=adminPrincipal' title='-Administrar aplicación'> -Administrar aplicación.</a>";

	$datosMensaje['textoComentarios'] = "";
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
	$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';//???
	
	$nomScriptFuncionError = ' cAdmin.php:adminPrincipal(). Error: ';//cambiar			
	$tituloSeccion  = 'Administración';		
 //--	
	require_once './modelos/modeloUsuarios.php';
	$resBuscarModoAp = buscarModoAp();//en modeloUsuarios.php, incluye conexion, probado error OK		
	echo "<br><br>1-1 cAdmin:adminPrincipal:resBuscarModoAp: ";print_r($resBuscarModoAp);	

	if ($resBuscarModoAp['codError'] !== '00000') //Error OK, numFilas == 0 también trata como error
	{	$validarUsuario = $resBuscarModoAp;
				echo "<br><br>1-2 cAdmin:adminPrincipal:resBuscarModoAp: ";print_r($resBuscarModoAp);				
			//$arrayParamMensaje['textoCabecera'] = 'Identificación como usuario/a';
			//$arrayParamMensaje['textoComentarios']='Modo Error del sistema al identificarse, vuelva a intentarlo pasado un tiempo ';				
	}				
	else//$resBuscarModoAp['codError'] == '00000'
	{	
			$textoCuerpo = "";	
			$textoEnlace =	"xEntrar";				
			
			if ($resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] == 'MANTENIMIENTO') 
			{ echo "<br><br>1-3 cAdmin:adminPrincipal:resBuscarModoAp: ";print_r($resBuscarModoAp);
					$textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>".$textoEnlace;		
					//$textoCuerpo = "Esta aplicación informática NO ESTARÁ ACCESIBLE para los socios y socias durante unas horas, debido a trabajos de mantenimiento.  <br /><br />Perdonen las molestias";							

					$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>
																																	<br /><br />Solo está accesible para el usuario -adming- y los usuari@s autorizados para realizar tareas de mantenimiento. 
																																	En modo de mantenimiento, las funciones usadas los usuari@s autorizados, son las mismas que las usadas en modo normal de explotación,
																																	y se efectuarán las mismas modificaciones en la BBDD, que pueden ser irreversibles.";																
					
					$_SESSION['vs_MODOTRABAJO'] = 'MANTENIMIENTO';
			}
			else
			{ $textoEstadoMantenimiento = "Gestión de Soci@s ahora está en modo <strong>EXPLOTACIÓN</strong>.SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";
	    $_SESSION['vs_MODOTRABAJO'] = 'EXPLOTACION';		
			}				
			//-------------------------------------------------------------------
				/*------------ Inicio navegación para gestor rol Admistrador CODROL = 0 ---*/
				$_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cAdmin&accion=adminPrincipal";
				$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "adminPrincipalMANTENIMIENTO-EXPLOTACIÓN";	
				//$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = $textoEnlace;							
				$_SESSION['vs_HISTORIA']['pagActual']=2;			
				//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
				
				require_once './controladores/libs/cNavegaHistoria.php';
				$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");			
				/*------------ Fin navegación para gestor rol Admistrador CODROL = 0 ------*/	
				
			echo "<br><br>1-4 cAdmin:adminPrincipal:_SESSION: ";print_r($_SESSION);
	  //}else//$resBuscarModoAp['codError'] == '00000'	//mover a final	
   //---					
	
			/*if (isset($_SESSION['vs_MODOTRABAJO']) && $_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
			{ $_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO MANTENIMIENTO: Entrar";		
					$textoEstadoMantenimiento = "Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>.Solo está ESTÁ ACCESIBLE al usuario -adming- y los usuari@s con permisos de mantenimiento";
			}
			else
			{ $_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO EXPLOTACIÓN: Entrar";			
					$textoEstadoMantenimiento = "Gestión de Soci@s ahora está en modo <strong>EXPLOTACIÓN</strong>.SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";				
			}*/
			//$enlacesSeccId = $resFuncionRol['resultadoFilas'];//????	

			require_once './modelos/modeloAdmin.php';	
			$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
			//echo "<br><br>1 cAdmin:adminPrincipal:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);

			//if ($resFuncionRol['codError'] !== '00000')	
			if ($fechaHoraServidor_BBDD['codError'] !=='00000')		
			{require_once './modelos/modeloEmail.php';	
				//$datosMensaje['textoComentarios'] = "Error del sistema en cAdmin.php:adminPrincipal() al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(), vuelve a intentarlo pasado un tiempo ";				
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$fechaHoraServidor_BBDD['codError'].": ".$fechaHoraServidor_BBDD['errorMensaje']);		
				$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
				$datosMensaje['textoComentarios'] = $textoEstadoMantenimiento."<br /><br /><br /><br /><strong>ERROR</strong> aal buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(). 
																																									Prueba de nuevo pasado un rato. 
																																								<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
				
				require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			}			
			else 
			{		/*
					if (isset($_SESSION['vs_MODOTRABAJO']) && $_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
					{$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO MANTENIMIENTO: Entrar";			
					$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>
																																	<br /><br />Solo está accesible para el usuario -adming- y los usuari@s autorizados para realizar tareas de mantenimiento. 
																																	En modo de mantenimiento, las funciones usadas los usuari@s autorizados, son las mismas que las usadas en modo normal de explotación,
																																	y se efectuarán las mismas modificaciones en la BBDD, que pueden ser irreversibles.";																																									
					}
					else
					{$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO EXPLOTACIÓN: Entrar";			
						$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo nprmal de <strong>EXPLOTACIÓN</strong> - SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s-";		
					}		
					*/
					
					$textoCuerpo = $textoEstadoMantenimiento."<br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
					"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  
					
					//$datosMensaje['textoComentarios'] = $textoCuerpo;

					require_once './vistas/admin/vAdminPrincipalInc.php'; 	
					vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);
					//vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$datosMensaje);
			}
	}//else $resBuscarModoAp['codError'] == '00000'	//mover a final	
}
//--------------------------- Fin adminPrincipal() ------------------------------------------------

function cambiarExplotacion_MantenimientoAdmin()
{	
	echo "<br><br>0-1 cAdmin:cambiarExplotacion_MantenimientoAdmin:SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin:cambiarExplotacion_MantenimientoAdmin:_POST: "; print_r($_POST);
	//echo "<br><br>0-3 cAdmin:cambiarExplotacion_MantenimientoAdmin:_GET: "; print_r($_GET);
	
 //if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoAdmin'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }
		
	$cabeceraCuerpo = "adminModo MANTENIMIENTO-EXPLOTACIÓN"."<br /><br />";
	//$textoEstadoMantenimiento = '';//acaso se use
	$textoCuerpo = "<br /><br /><br /><br />Para ciertas tareas de administración es necesario poner la aplicación en modo <strong>MANTENIMIENTO</strong>.
																	<br /><br />También se recomienda hacer un <strong>BACKUP</strong>, antes de realizar tareas de administración y mantenimiento que puedan producir cambios en la BBDD.
																	<br /><br />En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),
																															y se efectuarán las mismas modificaciones en la BBDD MySQL que en modo explotación y quedarán grabadas de forma permanente en la BBDD.															
																	<br /><br /><br /><br />Para ver el menú de opciones del administrador haz clic en el lado izquierdo en
																	<a href='./index.php?controlador=cAdmin&amp;accion=adminPrincipal' title='-Administrar aplicación'> -Administrar aplicación.</a>";

	$datosMensaje['textoComentarios'] = "";
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
	$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';//???
	
	$nomScriptFuncionError = ' cAdmin.php:cambiarExplotacion_MantenimientoAdmin(). Error: ';//cambiar			
	$tituloSeccion  = 'Administración';		
 //--	
	require_once './modelos/modeloUsuarios.php';
	$resBuscarModoAp = buscarModoAp();//en modeloUsuarios.php, incluye conexion, probado error OK		
	echo "<br><br>1-1 cAdmin:cambiarExplotacion_MantenimientoAdmin:resBuscarModoAp: ";print_r($resBuscarModoAp);	

	if ($resBuscarModoAp['codError'] !== '00000') //Error OK, numFilas == 0 también trata como error
	{	$validarUsuario = $resBuscarModoAp;
				echo "<br><br>1-2 cAdmin:cambiarExplotacion_MantenimientoAdmin:resBuscarModoAp: ";print_r($resBuscarModoAp);				
			//$arrayParamMensaje['textoCabecera'] = 'Identificación como usuario/a';
			//$arrayParamMensaje['textoComentarios']='Modo Error del sistema al identificarse, vuelva a intentarlo pasado un tiempo ';				
	}				
	else//$resBuscarModoAp['codError'] == '00000'
	{	
			$textoCuerpo = "";	
			$textoEnlace =	"xEntrar";				
			
			if ($resBuscarModoAp['resultadoFilas'][0]['MODOAPLICACION'] == 'MANTENIMIENTO') 
			{ echo "<br><br>1-3 cAdmin:cambiarExplotacion_MantenimientoAdmin:resBuscarModoAp: ";print_r($resBuscarModoAp);
					$textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>".$textoEnlace;		
					//$textoCuerpo = "Esta aplicación informática NO ESTARÁ ACCESIBLE para los socios y socias durante unas horas, debido a trabajos de mantenimiento.  <br /><br />Perdonen las molestias";							

					$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>
																																	<br /><br />Solo está accesible para el usuario -adming- y los usuari@s autorizados para realizar tareas de mantenimiento. 
																																	En modo de mantenimiento, las funciones usadas los usuari@s autorizados, son las mismas que las usadas en modo normal de explotación,
																																	y se efectuarán las mismas modificaciones en la BBDD, que pueden ser irreversibles.";																
					
					$_SESSION['vs_MODOTRABAJO'] = 'MANTENIMIENTO';
			}
			else
			{ $textoEstadoMantenimiento = "Gestión de Soci@s ahora está en modo <strong>EXPLOTACIÓN</strong>.SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s";
	    $_SESSION['vs_MODOTRABAJO'] = 'EXPLOTACION';		
			}
				/*------------ Inicio navegación para gestor rol Admistrador CODROL = 0 ---*/
				$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cAdmin&accion=adminPrincipal";
				$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "adminMANTENIMIENTO-EXPLOTACIÓN";	
				//$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = $textoEnlace;							
				$_SESSION['vs_HISTORIA']['pagActual']=3;			
				//echo "<br><br>2 cAdmin:cambiarExplotacion_MantenimientoAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
				
				require_once './controladores/libs/cNavegaHistoria.php';
				$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");			
				/*------------ Fin navegación para gestor rol Admistrador CODROL = 0 ------*/	
				
			echo "<br><br>1-4 cAdmin:cambiarExplotacion_MantenimientoAdmin:_SESSION: ";print_r($_SESSION);

			//$enlacesSeccId = $resFuncionRol['resultadoFilas'];//????	

			require_once './modelos/modeloAdmin.php';	
			$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
			//echo "<br><br>1 cAdmin:cambiarExplotacion_MantenimientoAdmin:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);
		
			if ($fechaHoraServidor_BBDD['codError'] !=='00000')		
			{require_once './modelos/modeloEmail.php';	
				//$datosMensaje['textoComentarios'] = "Error del sistema en cAdmin.php:adminPrincipal() al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(), vuelve a intentarlo pasado un tiempo ";				
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$fechaHoraServidor_BBDD['codError'].": ".$fechaHoraServidor_BBDD['errorMensaje']);		
				$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
				$datosMensaje['textoComentarios'] = $textoEstadoMantenimiento."<br /><br /><br /><br /><strong>ERROR</strong> aal buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(). 
																																									Prueba de nuevo pasado un rato. 
																																								<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
				
				require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			}			
			else 
			{						
					$textoCuerpo = $textoEstadoMantenimiento."<br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
					"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  
					
					//$datosMensaje['textoComentarios'] = $textoCuerpo;

					//require_once './vistas/admin/vAdminPrincipalInc.php'; 	
					//vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);
					require_once './vistas/admin/vCambiarExplotacion_MantenimientoAdminInc.php'; 	
					vCambiarExplotacion_MantenimientoAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);										
			}
	}//else $resBuscarModoAp['codError'] == '00000'	//mover a final	
}
//--------------------------- Fin cambiarExplotacion_MantenimientoAdmin() ------------------------------------------------


/*------------------- Inicio modoMantenimientoAdmin()  --------------------------------------------
Agustín 2018-12-11: Añadida para poner la aplicación en modo MANTENIMIENTO

La aplicación está previamente en estado EXPLOTACIÓN y en esta función se pondrá la aplicación 
en modo MANTENIMIENTO (en el que SOLO se permite el acceso a todos los usuarios con permiso para 
tareas de mantenimiento) e informa del nuevo estado de trabajo, y fechas del sistema y BBDD y 
al final muestra las funciones disponibles para el administrador:cambiar modo ->Explotación, 
Cierre año, ...

En esta función renombra el archivo "controladorLogin.php" como archivo "controladorLogin.php.bak"
(el controladorLogin.php en realidad es la versión de explotación) y se guarda copia para que no 
se pierda y en la opción de poner en modo Explotación se pueda renombrar en proceso inverso.
Después hace una copia del archivo "controladorLoginMantenimiento.php" como archivo "controladorLogin.php"
(este nuevo controladorLogin.php en realidad es la versión de controladorLoginMantenimiento.php 
cambiada de nombre )

LLamada: desde los menús de vAdminPrincipalInc.php
LLama: 
vAdminPrincipalInc.php que muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello 
usará la variable $_SESSION['vs_MODOTRABAJO'] 
 
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modelos/modeloAdmin.php:fechaHoraServidor_BBDD().

NOTA: MEJORA ....
Estaría bien no necesitar los dos controladores: controladorLogin.php y 
controLoginMantenimiento.php, y que sólo fuese un único  controladorLogin.php, ver la posibilidad 
de hacerlo con $_SESSION['vs_MODOTRABAJO'], y guardando el estado de MODOTRABAJO en la 
tabla CONTROLES que habría que leer al hacer login en validarUsuario(), o antes al mostrar la 
pantalla de login.
-------------------------------------------------------------------------------------------------*/
function modoMantenimientoAdmin_Version_renombrar()
{	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }	

	echo "<br><br>0-1cAdmin.php:modoMantenimientoAdmin():SESSION:";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin.php:modoMantenimientoAdmin():_POST:"; print_r($_POST);

 require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 

	$tituloSeccion  = "Administración";
	$enlacesSeccId = $resFuncionRol['resultadoFilas'];		//notice no definida creo que sobra

	require_once './modelos/modeloAdmin.php';	
	$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
	
	//echo "<br><br>0-4 cAdmin:adminPrincipal:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);
	
	if ($fechaHoraServidor_BBDD['codError'] !=='00000')
	{
 	$resControladorLoginBAK['codError'] = $fechaHoraServidor_BBDD['codError'];
		$resControladorLoginBAK['errorMensaje'] = $fechaHoraServidor_BBDD['codError']." ERROR en cAdmin.php:modoExplotacionAdmin(), en la función fechaHoraServidor_BBDD() al buscar SYSDATE de la BBDD, ,vuelva a intentarlo pasado un tiempo";			
			
		require_once './modelos/modeloEmail.php';
  $resEmailErrorWMaster = emailErrorWMaster($resControladorLoginBAK['codError'].": ".$resControladorLoginBAK['errorMensaje']);	
	}
	else //($fechaHoraServidor_BBDD['codError'] =='00000')
	{
			$directorioActual = getcwd();	
			echo "<br /><br />1- cAdmin.php:modoMantenimientoAdmin():directorio actual: ".$directorioActual;
			
			$directorioControladorLogin = $directorioActual."/controladores";
			echo "<br /><br />2- cAdmin.php:modoMantenimientoAdmin():directorio ControladorLogin: ".$directorioControladorLogin;
			
			$archivoControladorLoginPHP = $directorioControladorLogin."/controladorLogin.php";
			$archivoControladorLoginBAK = $directorioControladorLogin."/controladorLogin.php.bak";
			$archivoControladorLoginMantenimientoPHP = $directorioControladorLogin."/controladorLoginMantenimiento.php";
			//$archivoControladorLoginMantenimientoBAK = $directorioControladorLogin."/controladorLoginMantenimiento.php.bak";
			
			if (!isset($directorioControladorLogin ) || empty($directorioControladorLogin))
			{	echo "<br /><br />3- cAdmin.php:modoMantenimientoAdmin(): No existe o está vacio directorioControladorLogin: ".$directorioControladorLogin;
					$resControladorLogin['codError'] = '82060';
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin().  El directorio  ".$directorioControladorLogin. "  no existe";					
			}			
			elseif (!realpath($directorioControladorLogin))	
			{ echo " <br /><br />4- No existe o está vacio directorioControladorLogin: ".$directorioControladorLogin;
					$resControladorLogin['codError'] = '82060';
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin().  El directorio  ".$directorioControladorLogin. "  no existe";			
			}	
			elseif (!is_dir(realpath($directorioControladorLogin) ))
			{	echo "<br /><br />5- No es un directorio directorioControladorLogin: ".$directorioControladorLogin;					
					$resControladorLogin['codError'] = '82060'; 
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin().  El directorio  ".$directorioControladorLogin. " no es un directorio";
			}			
			elseif (!is_writable(realpath($directorioControladorLogin)) )
			{	echo "<br /><br />6- No hay permisos de escritura en el directorioControladorLogin: ".$directorioControladorLogin;					
					$resControladorLogin['codError'] = '82060'; 
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin().  El directorio  ".$directorioControladorLogin. " no tiene permisos de escritura";						
			}		
			/*  
			elseif (!chmod($archivoControladorLoginPHP, octdec($permisosArchivo)))//por seguridad chmod cambia los persmisos, por defecto a solo lectura =0444 en octal 
			{	//echo "<br><br>1--permisosArchivo:  ".$permisosArchivo;			
					$resSubirValidarMoverArchivo["codError"] = '82040';
					$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -". 
					"No se pudo asignar los permisos indicados para el archivo subido";				
			}
			//echo "<br><br>2-permisos archivo: ".substr(decoct(fileperms($$archivoControladorLoginPHP)), -4); //tipo 0444	
			*/
			elseif(!rename($archivoControladorLoginPHP, $archivoControladorLoginBAK))//Renombra archivo controladorLogin.php como controladorLogin.php.bak 
			{	echo "<br /><br />7- cAdmin.php:modoMantenimientoAdmin():Error al renombrar archivoControladorLoginPHP: ".$archivoControladorLoginPHP;						
					$resControladorLogin['codError'] = '82060'; 
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin(): Error al renombrar archivoControladorLoginPHP: ".$archivoControladorLoginPHP;								
			}
			elseif(!copy($archivoControladorLoginMantenimientoPHP, $archivoControladorLoginPHP))//copia archivo controladorLoginMantenimiento.php con nombre controladorLogin.php
			{	echo "<br><br>8- cAdmin.php:modoMantenimientoAdmin():Error al copiar archivoControladorLoginMantenimientoPHP: ".$$archivoControladorLoginMantenimientoPHP;	
					$resControladorLogin['codError'] = '82060'; 
					$resControladorLogin['errorMensaje'] = " ERROR en cAdmin.php:modoMantenimientoAdmin(): Error al copiar archivoControladorLoginMantenimientoPHP: ".$$archivoControladorLoginMantenimientoPHP;		
			}
			else
			{ $resControladorLogin['codError'] = '00000';
		
		   $_SESSION['vs_MODOTRABAJO'] = 'MANTENIMIENTO';
					$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "MODO MANTENIMIENTO: Entrar";		
					
					$textoEstadoMantenimiento = "MMMMLa aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong> - NO ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s-
																																			<br /><br />Solo está accesible para el usuario -adming- y los usuari@s al cargo de tareas de mantenimiento
																																			<br /><br />En las tareas de mantenimiento, las funciones son las mismas a las que se usan en modo normal de explotación,
																																			y se efectuarán las mismas modificaciones en la BBDD MySQL, que pueden ser irreversibles. Ojo hacer previo BACKUP";

			}	
	}	//else $fechaHoraServidor_BBDD['codError'] =='00000')	
	
	if ($resControladorLogin['codError'] !== '00000')
	{	
   $_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace']="MODO EXPLOTACIÓN: Entrar";	
	  
			$textoEstadoMantenimiento = $resControladorLogin['errorMensaje']."<br /><br /><br />La aplicación de Gestión de Soci@s, continua en modo <strong>EXPLOTACIÓN</strong>";	
 }	
	
	$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cAdmin&accion=adminPrincipal";
	$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Menú principal para administrar la aplicación";			
	$_SESSION['vs_HISTORIA']['pagActual']=2;			
	echo "<br><br>9 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");				

	$cabeceraCuerpo = "Administración: menú general";	
	$textoCuerpo =	$textoEstadoMantenimiento."<br /><br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
	"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  

 //echo "<br><br>10 cAdmin.php:modoMantenimientoAdmin():SESSION:";print_r($_SESSION);		

	require_once './vistas/admin/vAdminPrincipalInc.php'; 	
	vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);		
	//require_once './vistas/admin/vModoAplicacionAdminInc.php';
	//vModoAplicacionAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);		
}
//--------------------------- Fin modoMantenimientoAdmin_Version_renombrar() ----------------------------------------

/*------------------- Inicio modoExplotacionAdmin()  ----------------------------------------------
Agustín 2018-12-11: Añadida para poner  la aplicación en modo EXPLOTACIÓN 

La aplicación está previamente en estado MANTENIMIENTO y en esta función se pondrá la aplicación 
en modo EXPLOTACIÓN (en el que se permite el acceso a todos los usuarios según sus roles) e informa 
del nuevo estado de trabajo, y fechas del sistema y BBDD y al final muestra las funciones 
disponibles para el administrador:cambiar modo ->Mantenimiento, Cierre año, ...

Se renombra el archivo "controladorLogin.php.bak" para reescribirlo como archivo "controladorLogin.php"
(el previo controladorLogin.php en realidad es la versión de controLoginMantenimiento.php cambiada de
nombre que previamente había sido renombrado como controladorLogin.php para el estado Mantenimiento

En ella muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello usará la variable
$_SESSION['vs_MODOTRABAJO'] que se asignó o no en controladorLogin(), o en 
cAdmin.php:modoMantenimientoAdmin()

LLamada: desde los menús de vAdminPrincipalInc.php
LLama: 
vAdminPrincipalInc.php que muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello 
usará la variable $_SESSION['vs_MODOTRABAJO'] 
 
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modelos/modeloAdmin.php:fechaHoraServidor_BBDD().

NOTA: MEJORA ....
Estaría bien no necesitar los dos controladores: controladorLogin.php y 
controLoginMantenimiento.php, y que sólo fuese un único  controladorLogin.php, ver la posibilidad 
de hacerlo con $_SESSION['vs_MODOTRABAJO'], y guardando el estado de MODOTRABAJO en la 
tabla CONTROLES que habría que leer al hacer login en validarUsuario(), o antes al mostrar la 
pantalla de login.
--------------------------------------------------------------------------------------------------*/
function modoExplotacionAdmin_Version_renombrar()
{	
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }
	echo "<br><br>0-1 cAdmin.php:modoExplotacionAdmin():SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin.php:modoExplotacionAdmin():_POST: "; print_r($_POST);
 				
	$tituloSeccion  = "Administración";
	$enlacesSeccId = $resFuncionRol['resultadoFilas'];// notice undefine ...	
	
	require_once './modelos/modeloAdmin.php';	
	$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
	
	//echo "<br><br>0-4 cAdmin:adminPrincipal:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);
	
	if ($fechaHoraServidor_BBDD['codError'] !=='00000')
	{
 	$resControladorLoginBAK['codError'] = $fechaHoraServidor_BBDD['codError'];
		$resControladorLoginBAK['errorMensaje'] = $fechaHoraServidor_BBDD['codError']." ERROR en cAdmin.php:modoExplotacionAdmin(), en la función fechaHoraServidor_BBDD() al buscar SYSDATE de la BBDD, ,vuelva a intentarlo pasado un tiempo";			
			
		require_once './modelos/modeloEmail.php';
  $resEmailErrorWMaster = emailErrorWMaster($resControladorLoginBAK['codError'].": ".$resControladorLoginBAK['errorMensaje']);	
	}
	else //$fechaHoraServidor_BBDD['codError'] =='00000')
	{	
			$directorioActual = getcwd();	
			echo "<br /><br />1 directorio actual: ".$directorioActual;
			
			$directorioControladorLogin = $directorioActual."/controladores";
			echo "<br /><br />2 directorio ControladorLogin: ".$directorioControladorLogin;
			
			$archivoControladorLoginPHP = $directorioControladorLogin."/controladorLogin.php";
			$archivoControladorLoginBAK = $directorioControladorLogin."/controladorLogin.php.bak";			
			//$archivoControladorLoginMantenimientoPHP = $directorioControladorLogin."/controladorLoginMantenimiento.php";
			//$archivoControladorLoginMantenimientoBAK = $directorioControladorLogin."/controladorLoginMantenimiento.php.bak";
			
			if (!isset($directorioControladorLogin ) || empty($directorioControladorLogin))
			{ echo "<br /><br />3 No existe o está vacio directorioControladorLogin: ".$directorioControladorLogin;
					$resControladorLoginBAK['codError'] = '82060'; //falta asignar un codigo error para operación archivos
					$resControladorLoginBAK['errorMensaje'] = " ERROR en cAdmin.php:modoExplotacionAdmin().  El directorio  ".$directorioControladorLogin. "  no existe";
			}	
			elseif (!realpath($directorioControladorLogin))	
			{ echo "<br /><br />4 No existe o está vacio directorioControladorLogin: ".$directorioControladorLogin;
					$resControladorLoginBAK['codError'] = '82060'; //falta asignar un codigo error para operación archivos
					$resControladorLoginBAK['errorMensaje'] = " ERROR en cAdmin.php:modoExplotacionAdmin().  El directorio  ".$directorioControladorLogin. "  no existe";			
			}	
			elseif (!is_dir(realpath($directorioControladorLogin) ))
			{	echo "<br /><br />5 No es un directorio directorioControladorLogin: ".$directorioControladorLogin;					
					$resControladorLoginBAK['codError'] = '82060'; //falta asignar un codigo error para operación archivos
					$resControladorLoginBAK['errorMensaje'] = " ERROR en cAdmin.php:modoExplotacionAdmin().  El directorio  ".$directorioControladorLogin. " no es un directorio";
			}	
			elseif (!is_writable(realpath($directorioControladorLogin)) )
			{ echo "<br /><br />6 No hay permisos de escritura en el directorioControladorLogin: ".$directorioControladorLogin;					
					$resControladorLoginBAK['codError'] = '82060'; //falta asignar un codigo error para operación archivos
					$resControladorLoginBAK['errorMensaje'] = " ERROR en cAdmin.php:modoExplotacionAdmin().  El directorio  ".$directorioControladorLogin. " no tiene permisos de escritura";	
			}	
			/*  
			elseif (!chmod($archivoControladorLoginPHP, octdec($permisosArchivo)))//por seguridad chmod cambia los persmisos, por defecto a solo lectura =0444 en octal 
			{	//echo "<br><br>1--permisosArchivo:  ".$permisosArchivo;			
					$resSubirValidarMoverArchivo["codError"] = '82040';
					$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -". 
					"No se pudo asignar los permisos indicados para el archivo subido";				
			}
			//echo "<br><br>2-permisos archivo: ".substr(decoct(fileperms($$archivoControladorLoginPHP)), -4); //tipo 0444	
			*/
			elseif(!rename($archivoControladorLoginBAK,$archivoControladorLoginPHP))//Renombra archivo controladorLogin.php.bak como controladorLogin.php
			{	echo "<br /><br />7 Error al renombrar archivoControladorLoginPHP: ".$archivoControladorLoginBAK;				
					$resControladorLoginBAK['codError'] = '82060';//falta asignar un codigo error para operación archivos
					$resControladorLoginBAK['errorMensaje'] = " ERROR en cAdmin.php:modoExplotacionAdmin() al renombrar archivo ".
																																															$archivoControladorLoginBAK. "como archivo :".$archivoControladorLoginPHP;	
			}
			else //renombrado OK
			{ $resControladorLoginBAK['codError'] = '00000';
					
					unset($_SESSION['vs_MODOTRABAJO']);//o poner =$_SESSION['vs_MODOTRABAJO'] = 'EXPLOTACION';
					$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = "EN MODO EXPLOTACIÓN: Entrar";			
					
					$textoEstadoMantenimiento ="EXEXEXLa aplicación de Gestión de Soci@s ahora está en modo <strong>EXPLOTACION</strong> - SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s-";
			}
	}//else $fechaHoraServidor_BBDD['codError'] =='00000')
	
 if ($resControladorLoginBAK['codError'] !== '00000')
	{	$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace']="MODO EXplo-MANTENIMIENTO: Entrar";
	  $textoEstadoMantenimiento = $resControladorLoginBAK['errorMensaje']."<br /><br /><br />La aplicación de Gestión de Soci@s, continua en modo <strong>MANTENIMIENTO</strong>";	
 }
	
	$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cAdmin&accion=adminPrincipal";
	$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Menú principal para administrar la aplicación";			
	$_SESSION['vs_HISTORIA']['pagActual']=2;			
	//echo "<br><br>8 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");	


	$cabeceraCuerpo = "Administración: menú general";	
	$textoCuerpo =	$textoEstadoMantenimiento."<br /><br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
	"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  

	//echo "<br><br>9 cAdmin.php:modoExplotacionAdmin():SESSION: ";print_r($_SESSION);	
	
	require_once './vistas/admin/vAdminPrincipalInc.php';
	vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);

	//require_once './vistas/admin/vModoAplicacionAdminInc.php';
	//vModoAplicacionAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);			
}
//--------------------------- Fin modoExplotacionAdmin_Version_renombrar() ------------------------------------------


//----------------------------- Inicio cambiarControlModo --------------------
function cambiarControlModoAdmin($tablaAct,$modoAplicacion)
{ 
 $tablaAct = 'CONTROLMODOAPLICACION';

 $arrCambiarControlModo['nomScript'] = 'modeloAdmin.php';
 $arrCambiarControlModo['nomFuncion'] = 'cambiarControlModo';	
 $arrCambiarControlModo['codError'] = '00000';
 $arrCambiarControlModo['errorMensaje'] = '';	
	//$arrMensaje['textoBoton'] = 'Salir';
	//$arrMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	$arrMensaje['textoCabecera'] = 'Cambiar Modo Aplicación';	//sobrar´á
	$arrMensaje['textoComentarios'] .= "Se ha Cambiado el Modo Aplicación"; 		

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !== '00000')	
	{ $arrCambiarControlModo = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'	
	{	//echo '<br><br>1 modeloAdmin.php:cambiarControlModo:conexionDB: ';var_dump($conexionDB);	
		
		$arrayCondiciones['MODOAPLICACION']['valorCampo'] = '********';
		$arrayCondiciones['MODOAPLICACION']['operador'] = '!= ';
		$arrayCondiciones['MODOAPLICACION']['opUnir'] = ' ';
		
		$arrayDatos['MODOAPLICACION'] = $modoAplicacion;				
		$arrayDatos['FECHACAMBIOMODO'] = date("Y-m-d H:i:s"); 
		$arrayDatos['OBSERVACIONES'] = ''; 

		//if (!isset($arrayCondiciones) || empty($arrayCondiciones) || $arrayCondiciones == NULL)//Para una información más especifica del error 

		$resActTabla = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']); 																					
		
		//function ejecutarCadSql($cadSql,$conexionDB,$arrBindValues = NULL)//probar
		
		echo '<br><br>2 modeloAdmin.php:cambiarControlModo:resActTabla: ';print_r($resActTabla);
		
		if ($resActTabla['codError'] !== '00000')
		{
			$arrCambiarControlModo = $resActTabla;
	
	  $arrMensaje['textoComentarios'] = "Error del sistema al actualizar rol usuario, vuelva a intentarlo pasado un tiempo ";
		
			require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($resActTabla['codError'],$resActTabla['errorMensaje'],$conexionDB['conexionLink']); 
			
			if ($resInsertarErrores['codError'] !== '00000')
	  {
				$arrCambiarControlModo = $resInsertarErrores;
				$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resActTabla['codError']!=='00000'
	}// else $conexionDB['codError']=='00000'	
	
	$arrCambiarControlModo['arrMensaje'] = $arrMensaje;	
	echo "<br><br>3 modeloAdmin.php:cambiarControlModo:arrCambiarControlModo: ";print_r($arrCambiarControlModo);
	
	return $arrCambiarControlModo;
} 
//----------------------------- Fin cambiarControlModo -----------------------
//*******----------------------------------------------------------------------------------

/*------------------- Inicio modoExplotacionAdmin()  ----------------------------------------------
La aplicación está previamente en estado MANTENIMIENTO y en esta función se pondrá la aplicación 
en modo EXPLOTACIÓN (en el que se permite el acceso a todos los usuarios según sus roles) e informa 
del nuevo estado de trabajo, y fechas del sistema y BBDD y al final muestra las funciones 
disponibles para el administrador:cambiar modo ->Mantenimiento, Cierre año, ...

En ella muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello usará la variable
$_SESSION['vs_MODOTRABAJO'] que se asignó o no en controladorLogin(), o en 
cAdmin.php:modoMantenimientoAdmin()

LLamada: desde los menús de vAdminPrincipalInc.php
LLama: 
vAdminPrincipalInc.php que muestra si está en modo MANTENIMIENTO o EXPLOTACIÓN, para ello 
usará la variable $_SESSION['vs_MODOTRABAJO'] 
 
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modelos/modeloAdmin.php:fechaHoraServidor_BBDD().

NOTA: MEJORA ....
único  controladorLogin.php, ver la posibilidad 
de hacerlo con $_SESSION['vs_MODOTRABAJO'], y guardando el estado de MODOTRABAJO en la 
tabla CONTROLES que habría que leer al hacer login en validarUsuario(), o antes al mostrar la 
pantalla de login.
--------------------------------------------------------------------------------------------------*/
function modoExplotacionAdmin()
{	
	echo "<br><br>0-1 cAdmin.php:modoExplotacionAdmin():SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin.php:modoExplotacionAdmin():_POST: "; print_r($_POST);
	
 //if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoAdmin'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");   
 }
	
	$arrModoExplotacionAdmin['codError'] = '00000';
	$arrModoExplotacionAdmin['errorMensaje'] = '';
 				
	$tituloSeccion  = "Administración";
	//$enlacesSeccId = $resFuncionRol['resultadoFilas'];// notice undefine ...	
	
	require_once './modelos/modeloAdmin.php';	
	$fechaHoraServidor_BBDD = fechaHoraServidor_BBDD();
	
	//echo "<br><br>0-4 cAdmin:adminPrincipal:fechaHoraServidor_BBDD: "; print_r($fechaHoraServidor_BBDD);
	
	if ($fechaHoraServidor_BBDD['codError'] !== '00000')
	{
 	$arrModoExplotacionAdmin['codError'] = $fechaHoraServidor_BBDD['codError'];
		$arrModoExplotacionAdmin['errorMensaje'] = $fechaHoraServidor_BBDD['codError']." ERROR en cAdmin.php:modoExplotacionAdmin(), en la función fechaHoraServidor_BBDD() al buscar SYSDATE de la BBDD, ,vuelva a intentarlo pasado un tiempo";			
			
		require_once './modelos/modeloEmail.php';
  $resEmailErrorWMaster = emailErrorWMaster($fechaHoraServidor_BBDD['codError'].": ".$fechaHoraServidor_BBDD['errorMensaje']);	
	}
	else //$fechaHoraServidor_BBDD['codError'] =='00000')
	{	
			//$directorioActual = getcwd();	
			//echo "<br /><br />1 directorio actual: ".$directorioActual;			
			//$directorioControladorLogin = $directorioActual."/controladores";
			//echo "<br /><br />2 directorio ControladorLogin: ".$directorioControladorLogin;
			
			if ($_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
			{
				 $modoAplicacion = 'EXPLOTACION';
					
					$textoEnlace = 'Entrar';
					$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace'] = $textoEnlace;	
					$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo nprmal de <strong>EXPLOTACIÓN</strong> - SÍ ESTÁ ACCESIBLE a TOD@S soci@s y gestor@s-";		
			}
			elseif ($_SESSION['vs_MODOTRABAJO'] == 'EXPLOTACION')
			{ 
			  $modoAplicacion = 'MANTENIMIENTO';
					
					$textoEnlace = 'Entrar';
					$textoEnlace ="<span class='textoRojo9Right'><strong>MODO MANTENIMIENTO: </strong></span>".$textoEnlace;		
					//$textoCuerpo = "Esta aplicación informática NO ESTARÁ ACCESIBLE para los socios y socias durante unas horas, debido a trabajos de mantenimiento.  <br /><br />Perdonen las molestias";							
     	$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace']=$textoEnlace;			
					
					$textoEstadoMantenimiento = "La aplicación de Gestión de Soci@s ahora está en modo <strong>MANTENIMIENTO</strong>
																																	<br /><br />Solo está accesible para el usuario -adming- y los usuari@s autorizados para realizar tareas de mantenimiento. 
																																	En modo de mantenimiento, las funciones usadas los usuari@s autorizados, son las mismas que las usadas en modo normal de explotación,
																																	y se efectuarán las mismas modificaciones en la BBDD, que pueden ser irreversibles.";	
			}
			else
			{  //error
			}				
   echo "<br><br>3-1 cAdmin.php:modoExplotacionAdmin:modoAplicacion: "; print_r($modoAplicacion);
			
			$tablaAct = 'CONTROLMODOAPLICACION';
			$resCambiarControlModo = cambiarControlModo($tablaAct,$modoAplicacion);
			
			echo "<br><br>3-2 cAdmin.php:modoExplotacionAdmin:modoAplicacion: "; print_r($resCambiarControlModo);
			
   if ($resCambiarControlModo['codError'] !== '00000')
			{ $arrModoExplotacionAdmin = $resCambiarControlModo;
			}				
			else //renombrado OK
			{ $_SESSION['vs_MODOTRABAJO'] = $modoAplicacion;			  
					
					if ($resCambiarControlModo['numFilas'] == 0)
					{//$rolesYfuncionesUser['codError'] = '80004'; //no tiene roles
						$rolesYfuncionesUser['errorMensaje'] = "No ha cambiado estado aplicacion,sigue en modo: '.$modoAplicacion";					
					}
					else
					{	$resControladorLoginBAK['codError'] = '00000';						
								
					}					
					
			}
	}//else $fechaHoraServidor_BBDD['codError'] =='00000')
	/*
 if ($resControladorLoginBAK['codError'] !== '00000')
	{	$_SESSION['vs_HISTORIA']['enlaces'][0]['textoEnlace']="MODO EXplo-MANTENIMIENTO: Entrar";
	  $textoEstadoMantenimiento = $resControladorLoginBAK['errorMensaje']."<br /><br /><br />La aplicación de Gestión de Soci@s, continua en modo <strong>MANTENIMIENTO</strong>";	
 }*/
	//--
	$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cAdmin&accion=adminPrincipal";
	$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Para poner Modo Explotacion";			
	$_SESSION['vs_HISTORIA']['pagActual']=2;			
	//echo "<br><br>8 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
	//--

	$cabeceraCuerpo = "poner Modo Explotacion";	
	$textoCuerpo =	$textoEstadoMantenimiento."<br /><br /><br />Fecha y hora de la BBDD MySQL: <strong>".$fechaHoraServidor_BBDD['fechaHoraBBDD'].
	"</strong> Fecha y hora y zona horaria del servidor PHP: <strong>".$fechaHoraServidor_BBDD['fechaHoraServidor']."</strong>";  

	//echo "<br><br>9 cAdmin.php:modoExplotacionAdmin():SESSION: ";print_r($_SESSION);	
	
	require_once './vistas/admin/vAdminPrincipalInc.php';
	vAdminPrincipalInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);

	//require_once './vistas/admin/vModoAplicacionAdminInc.php';
	//vModoAplicacionAdminInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo);			
}
//--------------------------- Fin modoExplotacionAdmin_Version_renombrar() ------------------------------------------



/*========================= INICIO procesos de actualizarCuotasSociosAnioNuevo()====================
Se realizan los procesos de  "Cierre de año y apertura de año nuevo"

Se debe	ejecutar solo una vez por año "el día 1 de enero a las 00:01 horas", o lo más próximo posible "
Tardará cierto tiempo en ejecutarse este proceso, recorre y modifica muchas tablas.
No salir del navegador hasta que aparezca un mensaje indicando que le proceso ha finalizado, o un aviso de error

Se podría hacer con un CRON, pero como es proceso de riesgo es mejor hacerlo manualmente
==================================================================================================*/
/*------------------- Inicio actualizarCuotasSociosAnioNuevoAdmin()    -----------------------------
Esta función actualiza los datos de las tablas para cierre Año Finalizado e inicio de Año Nuevo

-Actualiza las cuotas socios en la tabla CUOTAANIOSOCIO para el nuevo año: 
	al cambiar de un año al siguiente hay que insertar las cuotas de los socios 
	desde el año Anterior al Nuevo, con la cuota elegida por el socio (IMPORTECUOTAANIOSOCIO) en el año.
	Pero no se insertan si en el AnioNuevo ya figurase una cuota para ese socio.

-Primero controla en la tabla CONTROLES, si previamente ha sido ejecutada con éxito esta función, 
	para el nuevo año. Si ya está ejecutada, se sale del proceso con un mensaje de aviso de que ya están 
	actualizadas las tablas.	
-En el año Nuevo se comprueba si las cuotas del socio IMPORTECUOTAANIOSOCIO son 
	iguales o superiores a las correspondientes a ese año Nuevo IMPORTECUOTAANIOEL
	(se han podido incrementar la cuota anual del nuevo año), y en ese caso se actualizan 
	todas las cuotas a los nuevos importes de EL (ya que no pueden ser inferiores las 
	cuotas elegidas previamente por los socios a las nuevas cuotas para ese año establecidas por EL).
-Si se hubiese dado de baja en el año Anterior pero ya tuviese datos de las cuotas 
	en año Nuevo	se borrarán los datos de las cuotas del año Nuevo, aunque se mantienen las del 
	anterior a efectos de tesorería.
-A la vez en el año Anterior, se actualizan datos de pagos,por ejemplo: PENDIENTE-COBRO->NOABONADA
-En la tabla SOCIO, se actualizan las cuotas elegidas por los socios, si fuesen inferiores a 
	las nuevas de EL
-En la tabla 'MIEMBROELIMINADO5ANIOS' de socios con baja hace 5 años (se guardaban por contabilidad), 
	se eliminan las filas de los socios con bajas de hace más de 5 años.	
	Se eliminar datos privados de socios con baja hace 5 años o más en tablas 'MIEMBROELIMINADO5ANIOS',
 el campo "CUENTAPAGO de tabla CUOTAANIOSOCIO" y el campo "CUENTAIBAN de la tabla ORDENES_COBRO".
-En en tabla "SOCIOSCONFIRMAR se pone NULL a los datos personales, y en la  tabla "USUARIO"	 
 se pone USUARIO.ESTADO = ANULADA-SOCITUD-REGISTRO
-Se actualiza la tabla "CONTROLES" y se pone el campo de cambio año  "ACTUALIZADO ='SI'"

RECIBE: $_POST del formulario "vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php"
													
LLAMADA: /vistas/admin/vCierreAnioPasadoAperturaAnioNuevo.php
LLAMA: modelos/modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin(),
        vistas/mensajes/vMensajeCabSalirNavInc.php, detalla filas en tablas que se han actualizado, o errores
	       modelos/modeloEmail.php:emailErrorWMaster(), informa si hay errores


REQUISITOS: ejecutar el día 1 de enero y se requiere rol de administrador de la aplicación

OBSERVACIONES: **** OJO SE DEBE EJECUTAR EL DÍA 1 DE ENERO DE CADA AÑO Y UNA SÓLA VEZ ****
               **** ANTES PONER MODO MANTENIMIENTO Y HACER UN BACKUP DE LA BASE DE DATOS *****
															**** PROBAR ANTES EN BBDD "europalaica_com_copia" O "europalaica_com_desarrollo" ***

PARA PRUEBAS: incluso probar antes del 1 de enero, ir a las versiones de prueba de Gestión de Soci@s
URLs: "europalaica.com/usuarios_copia", o en "europalaica.com/usuarios_desarrollo", de esto modo sólo
se pueden modificar las correspondientes BBDD: "europalaica_com_copia" o "europalaica_com_desarrollo"
Para probar antes del 1 de enero eleige el botón correspondiente Y+1
	
VER LOS COMENTARIOS TAMBIÉN EN: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
----------------------------------------------------------------------------------------------------*/
//function actualizarCuotasSociosAnioNuevoAdmin()
function cierreAnioPasadoAperturaAnioNuevoAdmin()
{	
	echo "<br><br>0-1 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:_POST: "; print_r($_POST);echo "<br><br>";
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoAdmin'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }

	require_once './modelos/modeloAdmin.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php'; 
	require_once './modelos/modeloEmail.php';
	
	$reActualizarCuotasSociosAnioNuevo['codError'] = '00000';
	$reActualizarCuotasSociosAnioNuevo['errorMensaje'] = '';		
	 
	$datosMensaje['textoCabecera'] = "Cierre de año pasado y apertura de año nuevo"; 
	$datosMensaje['textoComentarios'] = "<br /><br />Error al ejecutar el proceso de 'Cierre de año pasado y apertura de año nuevo'. 
	                                     No se ha podido realizar el proceso.
                                      <br /><br />Comprueba el funcionamiento de la aplicación y el ESTADO DE LAS TABLAS IMPLICADAS en el proceso 
																																						(por si hubiese que restauralas de nuevo a partir del BACKUP previo).
                                      <br /><br />Prueba de nuevo pasado un rato. 
						                                Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
	$nomScriptFuncionError = ' cAdmin.php:actualizarCuotasSociosAnioNuevoAdmin(). Error: ';	
	$tituloSeccion  = "Administrar";
			
	//	require_once './vistas/plantillasGrales/vTemporizadorInc.php'; Para intentar poner un temporizador
	//	vTemporizadorInc($tituloSeccion,$enlacesSeccIzda,$navegacion); //aun no funciona bien
	//-------------------------
	$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cAdmin&accion=actualizarCuotasSociosAnioNuevoAdmin";
	$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Cierre de año pasado y apertura de año nuevo";			
	$_SESSION['vs_HISTORIA']['pagActual']=3;			
	//echo "<br><br>2 cAdmin:menuGralAdmin:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);	
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");				
	//------------------------------------

	$dirHome = getcwd();
	/* getcwd(): devuelve el directorio home desde donde se ha ejecutado el index.php:  '/home/virtualmin/europalaica.com/public_html/usuarios' 
	   '/home/virtualmin/europalaica.com/public_html/usuarios_copia' o '/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo' 
	*/
		 
	if (isset($_POST['cierreAnio']) || isset($_POST['cierreAnioPruebaYmas1']) || isset($_POST['salirSinCierreAnio']))  	
 {
		echo "<br><br>3-0";

		if (isset($_POST['salirSinCierreAnio']))
  { 
	   echo "<br><br>3-1 cAdmin:actualizarCuotasSociosAnioNuevoAdmin::dirHome: ".$dirHome;
	   $datosMensaje['textoComentarios'] = "No se ha realizado el Cierre de año pasado y apertura de año nuevo y no se han modificado ninguna tabla";	
		  vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}	
  elseif ( isset($_POST['cierreAnio']) )//Se ejecutará para año actual date('Y') en BBDD: europalaica_com, o europalaica_com_copia, o europalaica_com_desarrollo, etc. 
		{	echo "<br><br>3-2 cAdmin:actualizarCuotasSociosAnioNuevoAdmin::dirHome: ".$dirHome;	
		  
				$anioNuevo = $_POST['anioActual'];// = date('Y'), es la REAL para hacer el día 1 de enero, o después.
				
				//$reActualizarCuotasSociosAnioNuevo = actualizarCuotasSociosAnioNuevo($anioNuevo);
    $reActualizarCuotasSociosAnioNuevo = mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo);				
		}		
		elseif ( isset($_POST['cierreAnioPruebaYmas1']) )//Para pruebas, se ejecutará para el año siguiente al actual, es decir $anioNuevo+1 = date('Y')+1
	 { 
		  //Como es para pruebas, sólo se podrá ejecutar en BBDD: "europalaica_com_copia", o "europalaica_com_desarrollo" 
    				    
    if (($dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_copia' || $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo') )									
				{ echo "<br><br>3.3a cAdmin:actualizarCuotasSociosAnioNuevoAdmin::SE PUEDE ".$dirHome;
			
			   $anioPruebaYmas1 = $_POST['anioPruebaYmas1'];//= date('Y') +1 para PRUEBAS: fecha simulada para probar antes del 1 enero por ejemplo el 20 de diciembre 
						
						if ( $anioPruebaYmas1 == (date('Y') +1) )			    
						{//$reActualizarCuotasSociosAnioNuevo = actualizarCuotasSociosAnioNuevo($anioPruebaYmas1);
				  	 $reActualizarCuotasSociosAnioNuevo = mCierreAnioPasadoAperturaAnioNuevoAdmin($anioPruebaYmas1);
						}
						else
						{ echo "<br><br>3.3b cAdmin:actualizarCuotasSociosAnioNuevoAdmin:: ****NO SE PUEDE ".$dirHome;
					   $reActualizarCuotasSociosAnioNuevo['codError'] = '80000'; // u otro valor
				    //$reActualizarCuotasSociosAnioNuevo['arrMensaje']['textoComentarios'] = 'Error: en selección de formulario para simular simula estar en el año '.$anioNuevo; 	
        $reActualizarCuotasSociosAnioNuevo['textoComentarios'] = 'Error: en selección de formulario para simular simula estar en el año '.$anioNuevo; 									
						}								
				}	
  }
  else//seía un error del formulario
  { echo "<br><br>3-4 cAdmin:actualizarCuotasSociosAnioNuevoAdmin::dirHome: ".$dirHome;
	
	   $reActualizarCuotasSociosAnioNuevo['codError'] = '80000'; // u otro valor
				//$reActualizarCuotasSociosAnioNuevo['arrMensaje']['textoComentarios'] = 'Error: en selección en formulario'; // u otro valor
				$reActualizarCuotasSociosAnioNuevo['textoComentarios'] = 'Error: en selección en formulario'; // u otro valor
  }	
	
	 echo "<br><br>3-7 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:reActualizarCuotasSociosAnioNuevo: ";print_r($reActualizarCuotasSociosAnioNuevo); 
		
		if ($reActualizarCuotasSociosAnioNuevo['codError'] !== '00000')
		{
				//$resEmailErrorWMaster = emailErrorWMaster("actualizarCuotasSociosAnioNuevoAdmin:codError:".$reActualizarCuotasSociosAnioNuevo['codError'].": ".
				//																																										$reActualizarCuotasSociosAnioNuevo['errorMensaje'].": ".$reActualizarCuotasSociosAnioNuevo['arrMensaje']['textoComentarios']);
																																														
			$resEmailErrorWMaster = emailErrorWMaster("actualizarCuotasSociosAnioNuevoAdmin:codError:".$reActualizarCuotasSociosAnioNuevo['codError'].": ".
																																														$reActualizarCuotasSociosAnioNuevo['errorMensaje'].": ".$reActualizarCuotasSociosAnioNuevo['textoComentarios']);																																														
																																													
				//vMensajeCabSalirNavInc($tituloSeccion,$reActualizarCuotasSociosAnioNuevo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
				echo "<br><br>2-1 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:datosMensaje: ";print_r($datosMensaje); 				
				//$datosMensaje['textoComentarios'] .= "<br /><br />".$reActualizarCuotasSociosAnioNuevo['arrMensaje']['textoComentarios'];	
				$datosMensaje['textoComentarios'] .= "<br /><br />".$reActualizarCuotasSociosAnioNuevo['textoComentarios'];	
			
				echo "<br><br>2-2 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:datosMensaje: ";print_r($datosMensaje); 
			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		} 
		else //$reActualizarCuotasSociosAnioNuevo['codError']=='00000'
		{ 
				//$datosMensaje['textoComentarios'] = "<br /><br />".$reActualizarCuotasSociosAnioNuevo['arrMensaje']['textoComentarios'];	
				$datosMensaje['textoComentarios'] = "<br /><br />".$reActualizarCuotasSociosAnioNuevo['textoComentarios'];	
				echo "<br><br>3 cAdmin:actualizarCuotasSociosAnioNuevoAdmin:datosMensaje: ";print_r($datosMensaje); 
				
				//vMensajeCabSalirNavInc($tituloSeccion,$reActualizarCuotasSociosAnioNuevo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		} 

	}//else (isset($_POST['cierreAnioPasadoAperturaAnioNuevo']))	
	else // !if (isset($_POST['cierreAnioPasadoAperturaAnioNuevo']) || isset($_POST['salirSinCierreAnioPasadoAperturaAnioNuevo']))  
	{
		//echo "<br><br>0-1vvvvvv cAdmin:actualizarCuotasSociosAnioNuevoAdmin:_SESSION['vs_MODOTRABAJO']: ";print_r($_SESSION['vs_MODOTRABAJO']);	
		if ($_SESSION['vs_MODOTRABAJO'] !== 'MANTENIMIENTO')
		{ echo "<br><br>3xxxxx";
	   $datosMensaje['textoComentarios'] = "Para realizar Cierre de año pasado y apertura de año nuevo debe estar en modo MANTENIMIENTO".	$textoEstadoMantenimiento ;	
		  vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}
		else
		{//falta conexión, se podríar incluir dentro de la funcíon o no poner esta función aquí ya que se controla en modeloAdmin	
   //$resBuscarControlActualizadoAnio = buscarControlActualizadoAnio($tControles,$anioNuevo,$conexionDB['conexionLink']);//en modeloAdmin.php error   
			//echo "<br><br>2-2yyyyyy cAdmin:actualizarCuotasSociosAnioNuevoAdmin:resBuscarControlActualizadoAnio: ";print_r($resBuscarControlActualizadoAnio);
			$datosMensaje['textoComentarios'] = 	$textoEstadoMantenimiento;	
			
			require_once './vistas/admin/vCierreAnioPasadoAperturaAnioNuevoInc.php';		 
			vCierreAnioPasadoAperturaAnioNuevoInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}	
	}	
	
}
//--------------------------- Fin actualizarCuotasSociosAnioNuevoAdmin() ----------------------------


/************************** FUNCIONES ANTERIORES AHORA NO SE UTILIZAN ******************************
 Las dejo por ahora, por si sirvieran para más adelante, aunque conviene limpiar muchas de ellas 
***************************************************************************************************/ 

/*============================== INICIO proceso convertirCCaIBAN()==================================
Se realiza un sola vez en 2014 para convertir las CC a IBAN 
Dejar por si fuese útil
==================================================================================================*/
/*------------------- Inicio actualizarCuotasSociosAnioNuevoAdmin()    -------------------
Descripción:-Actualiza las cuotas socios en la tabla SOCIO para tranformando CC en IBAN: 
             												
												-Primero busca en la tabla SOCIO las CC distintas de NULL	
										
												-Recorre el array y cambia CC a IBAN y despueas actualiza la tabla SOCIO
												
 											-En tablas CONFIRMASOCIO y 	
													
Llama a: Función modeloAdmin:actualizarCuotasSociosAnioNuevo()//Muy grande
Envía: $añoNuevo = date('Y'); //
OJO: Para pruebas antes de que el nuevo año haya comenzado se pone: $añoNuevo = date('Y'+1)

Recibe: $reActualizarCuotasSociosAnioNuevo, con los resultado de las operaciones

REQUISITOS: dia 1 de enero, si hubiese cambios de cuota, también hay que cambiar paypal
OBSERVACIONES: **** OJO SE DEBE EJECUTAR EL DÍA 1 O 2 DE ENERO DE CADA AÑO Y UNA SÓLA VEZ *****

PARA PRUEBA GENERAR TABLAS: CUOTAANIOSOCIO2,SOCIO2,IMPORTEDESCUOTAANIO2,MIEMBROELIMINADO5ANIOS2,SOCIOSCONFIRMAR2,USUARIO2 
		
VER LOS COMENTARIOS DE modeloAdmin.php:actualizarCuotasSociosAnioNuevo()
-------------------------------------------------------------------------------------------*/
function convertirCCaIBANadmin()
{//echo "<br><br>0modeloSocios:eliminarDatosSocios:datosUsuarioEliminar: ";print_r($datosUsuarioEliminar);
	$resEliminarSocio['nomFuncion'] = "convertirCCaIBANadmin";
	$resEliminarSocio['nomScript'] = "modeloAdmin.php";	
 $resEliminarSocio['codError'] = '00000';
 $resEliminarSocio['errorMensaje'] = '';
	//$arrMensaje['textoBoton']='Salir';	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
	$tituloSeccion  = "Administrar";
	
 //$arrMensaje['textoCabecera'] = 'Convertir CC a IBAN';
	
	
	require_once './modelos/modeloAdmin.php';	 
	$resconvertirCCaIBAN =	convertirCCaIBAN();
	
	$resconvertirCCaIBAN['textoCabecera'] = 'Convertir CC a IBAN';
 echo "<br><br>1 cAdmin:convertirCCaIBANadmin:resconvertirCCaIBAN:";print_r($resconvertirCCaIBAN); 
	
 require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	
 if ($resconvertirCCaIBAN['codError']!=='00000')
 {require_once './modelos/modeloEmail.php';
	 $resEmailErrorWMaster = emailErrorWMaster("convertirCCaIBANadmin:codError:".$resconvertirCCaIBAN['codError'].": ".
		                                          $resconvertirCCaIBAN['errorMensaje'].": ".
																																												//$resconvertirCCaIBAN['arrMensaje']['textoComentarios']);																																													
																																												$resconvertirCCaIBAN['textoComentarios']);	
		//vMensajeCabSalirNavInc($tituloSeccion,$resconvertirCCaIBAN['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	 vMensajeCabSalirNavInc($tituloSeccion,$resconvertirCCaIBAN,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		
 } 
 else //$reActualizarCuotasSociosAnioNuevo['codError']=='00000'
 { 
  vMensajeCabSalirNavInc($tituloSeccion,$resconvertirCCaIBAN ,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 } 	


}
//------------------------------ Fin convertirCCaIBANadmin -------------------------------

/*============================== FIN procesos de convertirCCaIBANadmin()==========================*/
/*================================================================================================*/


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
{if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
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
{if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
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
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
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
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
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
 if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_CODROL'] !== '0')
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