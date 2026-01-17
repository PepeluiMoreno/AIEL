<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: cMantenimiento.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
En este fichero se encuentran las funciones relacionadas con el rol de Mantenimiento (por ahora 
ninguna específica aunque pudiera ser un rol que se solapase en parte con el de Administración), 
que permite al usuario con este rol, poder logarse en la aplicación de Gestión de Soci@s aunque 
esté en modo MANTENIMIENTO y ejecutar las funciones que le corresponden según los roles que tenga asignado

Especialmente útil si se quiere comprobar adecuadamente el funcionamiento después de ejecutar la 
función: Administración: "Cierre de Año-Apertura NuevoAaño" y también en el rol de Tesorería: 
con funciones de Ordenes de cobro, para generar archivo "SEPA_ISO20022CORE_fecha.xml" y 
operaconciones relacionadas con anotaciones en la en "ORDENES_COBRO" y "CUOTAANIOSOCIO"

			
Es posible que más adelnta se añadan nuevas funciones a este controlador.

OBSERVACIONES: 	
- Es un rolde uso muy restringido
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
------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 cMantenimiento.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 cMantenimiento.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()------------------------------------------------*/

/*--------------------------- Inicio menuGralMantenimiento ---------------------------------------
En esta función de entrada al "rol de Mantenimiento" se le informa al usuario que tiene asignado 
el rol de Mantenimiento y que eso le ha permitido poder logarse en la aplicación de Gestión de Soci@s aunque 
esté en modo MANTENIMIENTO y así poder ejecutar las funciones que le corresponden según los roles 
que tenga asignado, para poder instalar nuevas versiones y probarlas, y también para ciertas tareas 
como en Administración: "Cierre de Año-Apertura NuevoAaño", y también en el rol de Tesorería: 
con funciones de Ordenes de cobro, para generar archivo "SEPA_ISO20022CORE_fecha.xml" y 
operaconciones relacionadas con anotaciones en la en "ORDENES_COBRO" y "CUOTAANIOSOCIO"	

Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de Mantenimiento='012' 
y se muestran en el menú lateral

Se pueden añadir un enlaces a archivos para descargarlo, estaría en el cuerpo debajo de la 
imagen de "ESCUELA LAICA".

LLAMADA: por ser gestor:controladorLogin.php:menuRolesUsuario():login/vRolInc.php 
         y además al moverse de un rol a otro en menú izdo, o en línea superior de enlaces

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 2021-01-30: No necesita cambios PDO, probado PHP 7.3.21
------------------------------------------------------------------------------------------------*/
function menuGralMantenimiento()//podría ser la misma para todos los usuarios,CODROL=SESSION 
{ 		
 //echo "<br><br>0-1 cMantenimiento.php:menuGralMantenimiento:SESSION: ";print_r($_SESSION);
  //echo "<br><br>0-2 cMantenimiento.php:menuGralMantenimiento:_POST: "; print_r($_POST); 
	if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_012'] !== 'SI')
	{	header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
	}			
 else //if ($_SESSION['vs_autentificado'] == 'SI' && $_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_012'] == 'SI')
	{	
		
  require_once './modelos/modeloUsuarios.php';			
	 require_once './modelos/modeloEmail.php';
	 require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';		
		
		$cabeceraCuerpo = "MANTENIMIENTO";
		$textoCuerpo = '<strong>Tienes asignado el Rol de Mantenimiento</strong>
																		<br /><br />Esto te permite utilizar las funciones de aplicación de Gestión de Soci@s que aparecen en el menú de "Roles del gestor/a",	
																		aunque esté en MANTENIMIENTO y bloqueada para todos los usuarios, excepto para administración'.
																		"<br /><br /><br /><br />Para ciertas tareas de administración es necesario poner la aplicación en modo <strong>MANTENIMIENTO</strong>.																	
																	<br /><br />AVISO: En modo mantenimiento, las funciones son las mismas que las usadas en modo normal (explotación),
																													se efectuarán las mismas modificaciones en la BBDD MySQL que en modo explotación y quedarán grabadas de forma permanente en la BBDD.";	
		$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los el menú correspondiente al Rol de Mantenimiento. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
																																							$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';		
		
		$nomScriptFuncionError = ' cMantenimiento.php:menuGralMantenimiento(). Error: ';			
		$tituloSeccion  = 'Mantenimiento';
		
		/*------------ inicio navegación para usuarios con rol de Mantenimiento  ------------*/
		$_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cMantenimiento&accion=menuGralMantenimiento";
		$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Mantenimiento";			
		$_SESSION['vs_HISTORIA']['pagActual']=2;			
		//echo "<br><br>1 cMantenimiento.php:menuGralManteniento:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA'])					
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		/*------------ Fin navegación para usuarios con rol de Mantenimiento ---------------*/				

	 $resFuncionRol = buscarRolFuncion('012');//en modeloUsuarios.php 
		//echo "<br><br>2 cMantenimiento.php:menuGralMantenimiento:resFuncionRol: ";print_r($resFuncionRol);	

		if ($resFuncionRol['codError'] !== '00000')		
	 {
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": buscarRolFuncion() ".$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);		
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	 }			
  else //($resFuncionRol['codError'] == '00000')
	 {		 
	  $_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];//Se podrá acceder desde cualquier sitio
			
			$enlacesArchivos = array();
			/* Si quisieramos añadir unos enlaces a unos archivos para descargarlos, estarían 
			   en el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente: 
			*/
			$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
			$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
			$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';	
			/*
			$enlacesArchivos[2]['link'] = '../documentos/COORDINACION/EL_MANUAL_GESTOR_Coordinador.pdf';
			$enlacesArchivos[2]['title'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';
			$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';			
   */			
	  //echo "<br><br>2 cMantenimiento.php:menuGralMantenimiento:resFuncionRol: ";print_r($resFuncionRol);	
			
			require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);			
	 }//else $resFuncionRol['codError'] == '00000'		 
 }//else ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_012'] == 'SI')	 
}
/*------------------------------ Fin menuGralMantenimiento -------------------------------------*/
?>