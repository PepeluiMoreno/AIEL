<?php

/*--------------------------------------------------------------------------------------------------
FICHERO: cCoordinador.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: En este fichero se encuentran las función relacionadas con la gestión de los usuarios 
             por los coordinadores	de agrupaciones		 
													
OBSERVACIONES:Este controlador tiene mucho en común a cPresidente.php. Lo separé por si en un futuro
              se deciden diferenciar más las acciones que puede realizar el presidente respecto 
														del coordinador
***NOTA: 						
Para las funciones "Alta socios por Gestores" y "Baja socios por Gestores" hay dos opciones a elegir: 
- una que en que no se comparte el código en los controladore y en las vistas (más flexible para 
  posibles cambios según los roles de gestor)
		.altaSocioPorGestorTes_SinCompatirConOtrosGestores()
		.eliminarSocioPres_SinCompatirConOtrosGestores()
		
- otra en la que se comparte el código por los gestores en cotroladores y vistas mejor reutilización
  de código pero menos flexible y más difícil de seguir).
		.altaSocioPorGestorTes_CompartiendoConOtrosGestores()
  .eliminarSocioCoord_CompartiendoConOtrosGestores()
													
--------------------------------------------------------------------------------------------------*/

//session_start();
/*---------------------------- Inicio session_start()----------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya hay activada una 
sesion, aunque no es un error puede mostrar un "Notice", si warning esta activado. 
Para evitar estos Notices, uso la función is_session_started(), que he creado que controla el 
estado con session_status() para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
--------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 controladorSocios.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()-------------------------------------------------*/

/*--------------------------- Inicio menuGralCoord -------------------------------------------------
Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de coordinador (CODROL=6) 
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
2020-04-24: aquí no necesita cambios PDO. Probada PHP 7.3.21
2017-04-23: Añado $enlacesArchivos, para manuales
----------------------------------------------------------------------------------------------------*/
function menuGralCoord()
{ 
	//echo "<br><br>0-1 cCoordinador:menuGralCoord:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cCoordinador:menuGralCoord:_POST: "; print_r($_POST);	
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else // if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{	
		$datosMensaje['textoCabecera'] = "COORDINACIÓN DE AGRUPACIÓN TERRITORIAL";
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los el menú correspondiente al Coordinación. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
		$nomScriptFuncionError = ' cCoordinador.php:menuGralCoord(). Error: ';			
		$tituloSeccion = "Coordinación";		
		
		/*------------ inicio navegación para socios gestores CODROL >1 ------------*/
		$_SESSION['vs_HISTORIA']['enlaces'][2]['link'] = "index.php?controlador=cCoordinador&accion=menuGralCoord";
		$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace'] = "Coordinación";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 2;					
		//echo "<br><br>2 cCoordinador:menuGralCoord:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);					
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");	
		/*------------ Fin navegación para socios gestores CODROL >1 ---------------*/	
		
		require_once './modelos/modeloUsuarios.php';
		$resFuncionRol = buscarRolFuncion('6');//CODROL=6 para coordinador, en modelosUsuarios.php , incluye insertarError()
	
		//echo "<br><br>3 cCoordinador:menuGralCoord:resFuncionRol: ";print_r($resFuncionRol);				
	 	
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
			
			$cabeceraCuerpo = 'COORDINACIÓN DE AGRUPACIÓN TERRITORIAL';
			$textoCuerpo = 'Desde el menú puedes acceder a las funciones disponibles para el <strong>Rol de Coordinación</strong> dentro del área de gestión que tienes asignada.';

			$enlacesArchivos = array();
			/* Si quisieramos añadir unos enlaces a unos archivos para descargarlos, estarían 
			   en el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente: 
			*/
			$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
			$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
			$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';	
			
			$enlacesArchivos[2]['link'] = '../documentos/COORDINACION/EL_MANUAL_GESTOR_Coordinador.pdf';
			$enlacesArchivos[2]['title'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';
			$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Coordinadores/as de la aplicación informática de Gestión de Soci@s';	

	
			require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);
	 }//else ($resFuncionRol['codError'] == '00000')
 }//else !if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI')	 
}
/*------------------------------ Fin menuGralCoord -------------------------------------------------*/

/*------------------------------- Inicio mostrarSociosCoord ------------------------------------------
Se forma y muestra una tabla "LISTA DE SOCI@S" con algunos campos de información de los socios de esa
agrupación territorial (en realidad es el área de gestión, que casi siempre se corresponde con una 
sóla agrupación territorial, pero en algunos casos incluye más de una agrup. como Andalucía y en 
es caso se puede elgir una agrupación concreta)
Sólo se muestran los socios que están dados de alta

Permite buscar por Apellidos socios. 
Aquí se incluye la paginación de la lista de socios. En la parte inferior se muestran número de páginas
para poder ir directamente a un página, anterior, siguiente, priemra, última.
La vista correspondiente "vMostrarSociosCoordInc" en forma de tabla, además de de mostrar algunos datos
de cada socio, al final para cada fila, hay iconos links para: ver detalles de ese socio, 
modificar datos, borrar datos socio 	
	
LLAMADA: desde Menú izquierdo: Lista soci@s (cCoordinador.php:menuGralCoord())
LLAMA: require_once 'controladores/libs/cPresCoordSociosApeNomPaginarInc.php' que incluye mucho:
el control de las búsquedas (APE1, APE"), formación  select para pasarlo "mPaginarLib".
modelos/libs/mPaginarLib.php:mPaginarLib(): llama a buscar y paginar, algo antigua pero funciona bien
añadido $arrBindValues para PDO.
modeloPresCoor.php:buscarAreaGestionCoord()
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: En esta función se incluye $arrBindValues para PDO.
En esta función se incluye $arrBindValues para PDO. Probada PHP 7.3.21

cPresCoordSociosApeNomPaginarInc.php es de uso compartido por cPresidente.php:mostrarSociosPres()
---------------------------------------------------------------------------------------------------*/	
function mostrarSociosCoord()
{
	//echo "<br><br>0-1 cCoordinador:mostrarSociosCoord:SESSION: ";print_r($_SESSION);  	
	//echo "<br><br>0-2 cCoordinador:mostrarSociosCoord:_POST: "; print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';		
		require_once './vistas/coordinador/vMostrarSociosCoordInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	
		require_once './controladores/libs/cNavegaHistoria.php';
		
		$datosMensaje['textoCabecera'] = 'LISTA DE SOCIOS/AS (no incluye las bajas)';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Lista de socios/as'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cCoordinador.php:mostrarSociosCoord(). Error: ';	
		$tituloSeccion = "Coordinación";			

		$areaAgrupCoord = buscarAreaGestionCoord($_SESSION['vs_CODUSER']);//en modeloPresCoord.php	incluye conexionDB()	probado error	
					
		//echo "<br><br>2 cCoordinador:mostrarSociosCoord:areaAgrupCoord: ";print_r($areaAgrupCoord);	
			
		if ($areaAgrupCoord['codError'] !== '00000') 
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$areaAgrupCoord['codError'].": ".$areaAgrupCoord['errorMensaje']);
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);  
		}	
		else//$areaAgrupCoord['codError'] == '00000'
		{
			/* Se inicia "$codAreaCoordinacion" y otras variables para utilizarlas dentro
				de require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php'
				Las variables $NomFuncion...	se incluyen para asignar los valores a 
				$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']= ... 		
			*/
			$codAreaCoordinacion = $areaAgrupCoord['resultadoFilas']['CODAREAGESTIONAGRUP'];	 	
			$NomFuncionActualMostrarSocios ="cCoordinador&accion=mostrarSociosCoord";	
			$NomFuncionMostrarDatosSocio = "cCoordinador&accion=mostrarDatosSocioCoord";
			$NomFuncionActualizarSocio = "cCoordinador&accion=actualizarSocioCoord";
			$NomFuncionEliminarSocio =	"cCoordinador&accion=eliminarSocioCoord";		
			
			require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php';

			/*--- Hay mucho dentro, se crean las cadSelect "$_pagi_sql", $arrBindValues, 
				(que a su vez se forman en modeloTesorero:cadBuscarCuotasSocios,...)	
				que serán necesarios para la función siguiente "mPaginarLib()	
				y también $datosFormMiembro, si se busca por APE, APE2 --------------------*/
				
			//echo 	"<br><br>3-1 cCoordinador:mostrarSociosCoord:_pagi_sql: ";print_r($_pagi_sql);//$_pagi_sql: es una cadena SELECT
			//echo 	"<br><br>3-2 cCoordinador:mostrarSociosCoord:arrBindValues: ";print_r($arrBindValues);//arrBindValues para cadena SELECT $_pagi_sql
			
			//-------------------------------- inicio navegación -------------------------
			//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" puede variar dentro de "require_once ./controladores/libs/cCoordinadorSociosApeNomPaginarInc.php"
			$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cCoordinador&accion=mostrarSociosCoord";
			$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Lista socios/as";
			$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
			//echo "<br><br>3-3cCoordinador:mostrarSociosCoord:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//--------------------------- fin navegación ----------------------------------		
		
			//configurar parámetros para la función mPaginarLib(), más detalles en función	 mPaginarLib()	
			$_pagi_cuantos = 8; //nº de filas de socios a mostrar 
			$_pagi_nav_num_enlaces = 14; //los nº de enlaces de páginas a mostrar abajo en la paginación
			$_pagi_mostrar_errores = true;
			//$_pag_propagar_opcion_buscar = '';
			//$_pag_conteo_alternativo = true;
			$conexionLink ='';
				
			/*mPaginarLib.php(): incluye conexionDB controla errores, pero ponemos aquí 
					$conexionLink, aunque aquí no existe, pero se debe dejar, para mantener 
					compatibilidad de formato de parámetros con otras llamadas a esta función 
					desde otros lugares (posiblemente se podría quitar)
			*/	
			require_once './modelos/libs/mPaginarLib.php';//lib. de modelo para llamar a buscar y paginar		
			$resDatosSocios = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error	
			
			//echo "<br><br>4 cCoordinador:mostrarSociosCoord:resDatosSocios: ";print_r($resDatosSocios);
			
			if ($resDatosSocios['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocios['codError'].": ".$resDatosSocios['errorMensaje']);			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
			}      
			else //$resDatosSocios['codError']=='00000'
			{
				//echo "<br><br>5 cCoordinador:mostrarSociosCoord:_SESSION['vs_CODAGRUPACION']: ";print_r($_SESSION['vs_CODAGRUPACION']);
				
				$codAgrup = '%';//todas las agrupaciones dentro de cada área de gestión geográfica			
							
				require_once './modelos/libs/arrayParValor.php';//añadido nuevo 2020-03-28			
				$parValorCombo = parValoresAgrupaAreaGestionCoord($codAreaCoordinacion,$codAgrup,$_SESSION['vs_CODAGRUPACION']);// probado error
				
				//echo "<br><br>6 cCoordinador:mostrarSociosCoord:parValorCombo: ";print_r($parValorCombo);
				
				if ($parValorCombo['codError'] !== '00000')
				{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);			
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
				}	
				else //Si no errores en $parValorCombo 
				{
					$resDatosSocios['navegacion'] = $navegacion;
					
					//echo "<br><br>7 cCoordinador:mostrarSociosCoord:resDatosSocios: ";print_r($resDatosSocios);				
					//echo "<br><br>8 cCoordinador:mostrarSociosCoord:datosFormMiembro: ";print_r($datosFormMiembro);
					
					/* $datosFormMiembro contendrá los datos APE1 o APE2 de ese socio si se busca por APE1 o APE2
								si no será $datosFormMiembro ='', este valor se forma en cPresCoordSociosApeNomPaginarInc.php
					*/
					vMostrarSociosCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosSocios,$parValorCombo,$areaAgrupCoord['resultadoFilas']['NOMBREAREAGESTION'],$datosFormMiembro);
				}
			}//else $resDatosSocios['codError']=='00000'  
		}//else $areaAgrupCoord['codError'] == '00000'	
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
//--------------------------------- Fin mostrarSociosCoord -------------------------------------------

/*------------------------------- Inicio mostrarDatosSocioCoord --------------------------------------
Se muestran algunos datos personales de un socio
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono "Ver = Lupa"
 
La parte de navegación se añade, para mantener la fila superior la navegación de opciones enlaces 
para el coordinador 

RECIBE: $_POST['datosFormUsuario']['CODUSER']
	
LLAMADA: desde "LISTA DE SOCIO/AS", para coordinador al hacer clic en icono Ver.
LLAMA:  /modelos/modeloPresCoord.php:buscarDatosSocioPorCoord()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: Probado PHP 7.3.21
En esta función no se precisa modificar $arrBindValues para PDO, la funciones ya lo 
incluyen internamente.
----------------------------------------------------------------------------------------------------*/
function mostrarDatosSocioCoord()
{ 
	//echo "<br><br>0-1 cCoordinador:mostrarDatosSocioCoord:POST: ";print_r($_POST);	 
 //echo "<br><br>0-2 cCoordinador:mostrarDatosSocioCoord:SESSION: ";print_r($_SESSION);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{ 
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/coordinador/vMostrarDatosSocioCoordInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'MOSTRAR DATOS DEL SOCIO/A';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al mostrar los datos de un socio/a'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$nomScriptFuncionError = ' cCoordinador.php:mostrarSocioCoord(). Error: ';	
		
		$tituloSeccion = "Coordinación";		

		//-------------------------------- inicio navegación --------------------------
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] =="index.php?controlador=cCoordinador&accion=mostrarSociosCoord")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cCoordinador&accion=mostrarDatosSocioCoord";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar datos socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cCoordinador:mostrarDatosSocioCoord:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior"); 
		//--------------------------- fin navegación ----------------------------------

		$anioCuota = '%';//TODOS
		$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
			
		$resDatosSocio = buscarDatosSocioPorCoord($usuarioBuscado,$anioCuota);//en modeloPresCoord.php, llama a modeloSocios.php:buscarDatosSocio()
		
		//echo "<br><br>2 cCoordinador:mostrarDatosSocioCoord:resDatosSocio: "; print_r($resDatosSocio); 

		if ($resDatosSocio['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);	
			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}      
		else //$resDatosSocio['codError']=='00000'
		{
			//echo "<br><br>3 cCoordinador:mostrarDatosSocioCoord:resDatosSocio: "; print_r($resDatosSocio); 				 
			vMostrarDatosSocioCoordInc($tituloSeccion,$resDatosSocio,$navegacion);	
			
		}//else $resDatosSocio['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
/*--------------------------- Fin mostrarDatosSocioCoord -------------------------------------------*/

/*------------------------------ Inicio actualizarSocioCoord -----------------------------------------
El coordinador, actualiza  datos personales de un socio, cuotas, IBAN, agrupación y afecta a 
varias varias tablas.
Necesita varias funciones entre otras 'modelos/libs/prepMostrarActualizarCuotaSocio.php' que es 
compartida con otros controladores de gestor.		
En ella se preparar campos de cuota del socio, para mostrar y actualizar la cuota en formularios 
de actualización socioSocios, y hay que guardar los valores anteriores porque algunos 
no los modifique (es común) o hay error al introducir los nuevos datos.

RECIBE: $_SESSION, $_POST[], $_POST['campoHide'] este campo contiene datos de 
require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php';									

LLAMADA: desde tabla de "LISTA DE SOCIo/AS", en "formMostrarDatosSocioCoord.php" 
al hacer clic en el icono Modifica (Pluma).

LLAMA: modeloPresCoord.php:buscarDatosSocioPorCoord(),
modeloSocios.php:actualizarDatosSocio()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor()
controladores/libs/inicializaCamposActualizarSocio.php
(contiene mucho código, que acaso se pueda simplificar)
controladores/libs/arrayEnviaRecibeUrl.php:arrayRecibeUrl(),arrayEnviaUrl()
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php: navegación fila superior links
vistas/coordinador/vActualizarSocioCoordInc.php:vActualizarSocioCoordInc()

La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, según los roles

OBSERVACIONES: 
2020-04-14: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza. Probado PHP 7.3.21

OJO: LLAMA A LA FUNCIÓN actualizarDatosSocio() está en modeloSocios.php
NOTA: muy similar a controladorSocios.php:actualizarSocio() 
y cPresidente.php:actualizarSocioPres(),cTesorero.php:actualizarSocioTes()
----------------------------------------------------------------------------------------------------*/
function actualizarSocioCoord()
{
	//echo "<br><br>0-1 cCoordinador:actualizarSocioCoord:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-2 cCoordinador:actualizarSocioCoord:POST: ";print_r($_POST);	
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{		
		require_once './controladores/libs/arrayEnviaRecibeUrl.php';
  require_once './modelos/libs/validarCamposSocioPorGestor.php'; 	
		require_once './modelos/libs/arrayParValor.php';	
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/coordinador/vActualizarSocioCoordInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ACTUALIZAR DATOS SOCIA/O	";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al actualizar datos de la socia/o. No se han podido actualizar los datos del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cCoordinador.php:actualizarSocioCoord(). Error: ';
		$tituloSeccion = "Coordinación";		
		
		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cCoordinador&accion=mostrarSociosCoord")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cCoordinador&accion=actualizarSocioCoord";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar datos socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	

		//echo "<br><br>2 cCoordinador:actualizarSocioCoord:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion ------------------------------------------------	
			
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			if (isset($_POST['salirSinActualizar']))
			{$nomApe1 = strtoupper($_POST['campoActualizar']['datosFormMiembro']['NOM']." ".$_POST['campoActualizar']['datosFormMiembro']['APE1']);
				$datosMensaje['textoComentarios'] = "No se han modificado los datos de ".$nomApe1;	 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['comprobarYactualizar']))
			{$_POST['campoHide'] = arrayRecibeUrl($_POST['campoHide']);//pasa un string (obtenido con arrayEnviaUrl) a array en /controladores/libs/arrayEnviaRecibeUrl.php	 
				
				//echo "<br><br>3 cCoordinador:actualizarSocioCoord:POST: ";print_r($_POST);

				$resValidarCamposForm = validarCamposActualizarSocioPorGestor($_POST);//en modelos/libs/validarCamposSocioPorGestor.php
				//echo "<br><br>4 cCoordinador:actualizarSocioCoord:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				if (($resValidarCamposForm['codError'] !=='00000') && ($resValidarCamposForm['codError'] >'80000'))//error lógico
				{$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['campoActualizar']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resValidarCamposForm['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],			
																																																	$resValidarCamposForm['campoActualizar']['datosFormSocio']['CODAGRUPACION']['valorCampo']);	
																																												
					if ($parValorCombo['codError'] !== '00000') 
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}	
					else
					{ $resValidarCamposForm['campoHide'] = arrayEnviaUrl($resValidarCamposForm['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php	
							vActualizarSocioCoordInc($tituloSeccion,$resValidarCamposForm,$parValorCombo,$navegacion);		
					}			
				}													
				else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
				{//echo "<br><br>5-1 cCoordinador:actualizarSocioCoord:$resValidarCamposForm: ";print_r($resValidarCamposForm);			
					
					$resActDatosSocio = actualizarDatosSocio($resValidarCamposForm['campoActualizar']);//en modeloSocios.php, incluye conexion(), //probado error
					
					//echo "<br><br>5-2 cCoordinador:actualizarSocioCoord:resActDatosSocio: ";print_r($resActDatosSocio);
										
					if ($resActDatosSocio['codError'] !=="00000")
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}		
					else //($resActDatosSocio['codError'] == '00000')
					{	$datosMensaje['textoComentarios'] = $resActDatosSocio['arrMensaje']['textoComentarios'];				
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}	        
				}//else $resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
			}//else (isset($_POST['comprobarYactualizar']))	 				  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
				//echo "<br><br>6 cCoordinador:actualizarSocioCoord:SESSION: ";print_r($_SESSION);       
				$anioCuota = '%';		
				$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];	
				
				$resDatosSocioActualizar = buscarDatosSocioPorCoord($usuarioBuscado,$anioCuota);//está en modeloPresCoord.php';	se puede tratar IBAN para ***,
							
				//echo "<br><br>7 cCoordinador:actualizarSocioCoord:resDatosSocioActualizar: ";print_r($resDatosSocioActualizar);
			
				if ($resDatosSocioActualizar['codError'] !== '00000')
				{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocioActualizar['codError'].": ".$resDatosSocioActualizar['errorMensaje']);							 
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
				}      
				else //$resDatosSocioActualizar['codError']=='00000'
				{$parValorCombo = parValoresRegistrarUsuario($resDatosSocioActualizar['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resDatosSocioActualizar['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																	$resDatosSocioActualizar['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo']);				
					
					if ($parValorCombo['codError'] !== '00000') 
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
					}	
					else //$parValorCombo['codError']=='00000'
					{
						//require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php';			//usada anteriormente		
						//$datMostrarActualizarCuotaSocio = prepMostrarActualizarCuotaSocio($resDatosSocioActualizar);//incluye consultas SQL y controladores/libs/arrayEnviaRecibeUrl.php
				 	require_once './controladores/libs/inicializaCamposActualizarSocio.php';
				 	$datMostrarActualizarCuotaSocio = inicializaCamposActualizarSocio($resDatosSocioActualizar);						
						
						//echo "<br><br>8 cCoordinador:actualizarSocioCoord:datMostrarActualizarCuotaSocio";print_r($datMostrarActualizarCuotaSocio);
										
						if ($datMostrarActualizarCuotaSocio['codError'] !== '00000')
						{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datMostrarActualizarCuotaSocio['codError'].": ".$datMostrarActualizarCuotaSocio['errorMensaje']);			
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						}	
						else //$datMostrarActualizarCuotaSocio['codError'] == '00000'
						{ 
								$datosSocioFormActualizar['campoActualizar'] = $datMostrarActualizarCuotaSocio['campoActualizar'];					
								$datosSocioFormActualizar['campoVerAnioActual'] = $datMostrarActualizarCuotaSocio['cuotaSocioAnioActual'];
								$datosSocioFormActualizar['campoHide'] = arrayEnviaUrl($datMostrarActualizarCuotaSocio['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php
						  //arrayEnviaUrl() recibe un array, lo prepara y convierte en un string serializado, para enviarlo por URL, y después con arrayRecibeUrl() volverlo al array original
							
							//echo "<br><br>9 cCoordinador:actualizarSocioCoord:datosSocioFormActualizar";	print_r($datosSocioFormActualizar);	
								vActualizarSocioCoordInc($tituloSeccion,$datosSocioFormActualizar,$parValorCombo,$navegacion);
						}	
					}//else $parValorCombo['codError']=='00000'	
				}//else $resDatosSocioActualizar['codError']=='00000'   
		}//else !(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion']))  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
/*--------------------------- Fin actualizarSocioCoord ---------------------------------------------*/


/*------------ Inicio eliminarSocioCoord_SinCompatirConOtrosGestores ---------------------------------
Se eliminan datos identificativos del socio (privacidad de datos), y se insertan algunos datos 
en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.

En caso de que hubiese un archivo con la firma de un socio debido a lata por gestor, también
se eliminaría el archivo del servidor.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos 
datos personales en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.

La parte de navegación se añade, para mantener la fila superior la navegación 

LLAMADA:Menú coordinación>>Lista socios/as-->Baja	(al hacer clic en el icono Baja.)
(vistas/coordinacion/formEliminarSocioCoord.php)

LLAMA: validarCamposSocio.php:validarEliminarSocio(),
modeloSocios.php:eliminarDatosSocios();modeloPresCoord.php:bajaSocioFallecido(),
modeloSocios.php:buscarDatosSocio(),buscarEmailCoordSecreTesor()
modeloEmail.php:emailBajaUsuario(),emailBajaUsuarioFallecido(),$reEnviarEmailCoSeTe(),
emailErrorWMaster()
vistas/presidente/vEliminarSocioCoordInc.php
modelos/libs/validarCamposSocio.php:validarEliminarSocio()
controladores/libs/cNavegaHistoria.php

OBSERVACIÓN: Es casi idéntico a cPresidente:eliminarSocioPres(), ycTesorero:eliminarSocioTes() 
excepto por la navegación y menú izdo y se podría compartir código entre ellos, pero con el
inconveniente menos claro de seguir, y más rigidez para posibles modificaciones.

2020-09-10: probada PHP 7.3.21, Aquí no necesita cambios para PDO, lo incluyen 
internamente las funciones						
-----------------------------------------------------------------------------------------------------*/
function eliminarSocioCoord()
{
 //echo "<br /><br />0-1 cCoordinador:eliminarSocioCoord:SESSION: ";print_r($_SESSION);  
	//echo "<br /><br />0-2 cCoordinador:eliminarSocioCoord:POST: ";print_r($_POST);
 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{		
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/coordinador/vEliminarSocioCoordInc.php';	
		require_once './modelos/libs/validarCamposSocio.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
		$nomScriptFuncionError = ' cCoordinador.php:eliminarSocioCoord(). Error: ';	
		$tituloSeccion = "Coordinación";		
		
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cCoordinador&accion=mostrarSociosCoord")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cCoordinador&accion=eliminarSocioCoord";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ Fin navegacion -------------------------------------------------
		
		if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']) ) 
		{
			$datosSocio = $_POST;
			
			$nomApe = strtoupper($_POST['datosFormMiembro']['NOM']." ".$_POST['datosFormMiembro']['APE1']);
			
			if (isset($_POST['NoEliminar'])) //ha pulsado el botón "noGuardarDatosSocio"
			{
					$datosMensaje['textoComentarios'] = "Ha salido sin dar de baja al socio/a ".$nomApe;
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}				
			elseif (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) ) 
			{ 	
					$resValidarCamposForm = validarEliminarSocio($_POST);//en modelos/libs/validarCamposSocio.php
					
					//echo "<br><br>3 cCoordinador:eliminarSocioCoord:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
					if ($resValidarCamposForm['codError'] !=='00000')
					{	
							if ($resValidarCamposForm['codError'] >= '80000')//Error lógico		probado		
							{
									vEliminarSocioCoordInc($tituloSeccion,$resValidarCamposForm,$navegacion); 
							}			
							else //$resValidarCamposForm['codError']< '80000') = error sistema creo que no se producirá
							{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);												
							}
					}	
					else //$resValidarCamposForm['codError']=='00000' = NO HAY ERROR
					{				
							if (isset($_POST['SiEliminar']))
							{						
									$reEliminarSocio = eliminarDatosSocios($_POST);	//en modeloSocios.php probado error 
									//echo "<br><br>4-1 cCoordinador:eliminarSocioCoord:reEliminarSocio "; print_r($reEliminarSocio);			
							}
							elseif (isset($_POST['SiEliminarFallecimiento']))
							{	
									$reEliminarSocio = bajaSocioFallecido($_POST);	//en modeloPresCoord.php probado error
									//echo "<br><br>4-2 cCoordinador:eliminarSocioCoord:reEliminarSocio "; print_r($reEliminarSocio);
							}	
							
							if ($reEliminarSocio['codError'] !== "00000")
							{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarSocio['codError'].": ".$reEliminarSocio['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
							}
							else //($reEliminarSocio['codError'] == '00000')
							{						
									//echo"<br><br>5-1 cCoordinador:eliminarSocioCoord:sesion: ";print_r($_SESSION);
									$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
									
									if 	( $usuarioBuscado == $_SESSION['vs_CODUSER'] )//solo si el Gestor se borra a él mismo	
									{	unset($_SESSION);//para que ya no esté como autorizado
										//echo"<br><br>5-2 cCoordinador:eliminarSocioCoord:sesion: ";print_r($_SESSION);
									}	
									
									$textoComentariosEmail = '';					
									//-------------------- Inicio email a socio -------------------------------		
									if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	//si falta, no se envía email
									{
											if (isset($_POST['SiEliminarFallecimiento']))
											{ $resultEnviarEmail = emailBajaUsuarioFallecido($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-1 cCoordinador:eliminarSocioCoord:resultEnviarEmail: ";print_r($resultEnviarEmail);	
											}	
											else
											{	$resultEnviarEmail = emailBajaUsuario($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-2 cCoordinador:eliminarSocioCoord:resultEnviarEmail: ";print_r($resultEnviarEmail);
											}	

											if ($resultEnviarEmail['codError'] !== '00000')//probado error
											{ $textoComentariosEmail = '<br /><br />Por un error no se ha podido envíar el email con la información de la baja, a la dirección de correo que está anotada para el socio.';									
													$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['errorMensaje'].$textoComentariosEmail);
											}			
									}//if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	
									//----------------------- Fin	email a socio -------------------------------						
												
									//-------------------- Inicio email a Coord Secre Tes Pres ----------------	
									$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($datosSocio['datosFormUsuario']['CODUSER']);
						
									//echo"<br><br>6-3 cCoordinador:eliminarSocioCoord:reDatosEmailCoSeTe:";print_r($reDatosEmailCoSeTe);
									if ($reDatosEmailCoSeTe['codError'] !== '00000')
									{ $textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';																																						
											$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);									
									}
									else 
									{
									 	if (isset($datosSocio['datosFormSocio']['OBSERVACIONES']) && !empty($datosSocio['datosFormSocio']['OBSERVACIONES']))
											{$datosSocio['datosFormSocio']['OBSERVACIONES'] = "Observaciones desde Coordinación: ".$datosSocio['datosFormSocio']['OBSERVACIONES'];
											}	
											//se añade este texto a los comentarios que ya vienen de modeloSocios.php:eliminarDatosSocios()
											$reEliminarSocio['arrMensaje']['textoComentarios'] .="<br /><br /><br />Se ha enviado un email a Presidencia, Secretaría, Tesorería y Coordinación de la agrupación para informar de esta baja";
																								
									//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
				//****************************************************************************************************************
	 			     $reEnviarEmailCoSeTe = emailBajaSocioCoordSecreTesor($reDatosEmailCoSeTe,$datosSocio);
				//FIN COMENTAR ****************************************************************************************************************

											//echo"<br><br>7 cCoordinador:eliminarSocioCoord:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
											if ($reEnviarEmailCoSeTe['codError'] !=='00000')//
											{	$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';										
													$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
											}		
									}	
									//-------------------- Fin email a Coord Secre Tes Pres ------------------	 
											
								$datosMensaje['textoComentarios'] = $reEliminarSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
								//echo "<br><br>8 controladores/libs/altaSocioPorGestor.php:datosMensaje: ";print_r($datosMensaje);

								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
							}// else $reEliminarSocio['codError'] == '00000'			
					}// else $resValidarCamposForm['codError']=='00000' = NO HAY ERROR
			}// if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimeinto']) )      
		}// if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar'])) 
			
		else //!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar'])) 
		{
			$anioCuota = '%';
			$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
				
			$datSocioEliminar = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php probado error
			//echo "<br><br>9 cCoordinador:eliminarSocioCoord:datSocioEliminar: "; print_r($datSocioEliminar); 
										
			if ($datSocioEliminar['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioEliminar['codError'].": ".$datSocioEliminar['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioEliminar['codError']=='00000'
			{//echo "<br><br>10 cCoordinador:eliminarSocioCoord:datSocioEliminar:";print_r($datSocioEliminar);	

				vEliminarSocioCoordInc($tituloSeccion,$datSocioEliminar,$navegacion); 
			}   
		}//!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']))
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
/*------------- Fin eliminarSocioCoord_SinCompatirConOtrosGestores ----------------------------------*/

/*-------------- Inicio eliminarSocioCoord_CompartiendoConOtrosGestores ------------------------------
Se eliminan datos identificativos del socio (privacidad de datos), y se insertan
algunos datos en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Baja.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos datos personales
en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.
La parte de navegación se añade, para mantener la fila superior la navegación de opciones 

LLAMADA:  Menú coordinación>>Lista socios/as-->Baja	
(vistas/coordinacion/formEliminarSocioCoord.php)

LLAMA: require_once './controladores/libs/eliminarSocioPorGestor.php';
						 que es la parte común de bajas de socios por gestores y que a su vez incluye 
						 varias scripts		
							
							controladores/libs/cNavegaHistoria.php
							
OBSERVACIÓN: Es casi idéntico a cTesoreria:eliminarSocioTes(),Presidencia:eliminarSocioPres(),
excepto por la nevegación y menú izdo por eso comparto código entre ellos.
Inconveniente menos claro de seguir y comparto un único formulario de baja:
vistas/gestoresComun/vEliminarSocioGestorInc.php

2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones	
--------------------------------------------------------------------------------------*/
function eliminarSocioCoord_CompartiendoConOtrosGestores()
{ 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	//echo "<br /><br />0-1 cCoordinador:eliminarSocioCoord:SESSION: ";print_r($_SESSION);  
	//echo "<br /><br />0-2 cCoordinador:eliminarSocioCoord:POST: ";print_r($_POST);

	$datosMensaje['textoCabecera'] = "DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)";	
	$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
	$nomScriptFuncionError = ' cCoordinador.php:eliminarSocioCoord(): ';	
 $tituloSeccion = "Coordinación";		
	
 //------------ inicio navegacion ----------------------------------------------	
 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];	
	
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cCoordinador&accion=mostrarSociosCoord")		
	{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
	}	
	$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cCoordinador&accion=eliminarSocioCoord";	
	$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a ";
	$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
	require_once './controladores/libs/cNavegaHistoria.php';
	$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
	//------------ fin navegacion -------------------------------------------------

 //NOTA: este es el include que se podría compartir con cTesorero:eliminarSocioTes(),cPresidencia:eliminarSocioPres(),
 require_once './controladores/libs/eliminarSocioPorGestor.php';//aquí está la parte común de altas por gestores

}
//------------ Fin eliminarSocioCoord_CompartiendoConOtrosGestores -----------------------------------


/*-------- Inicio altaSocioPorGestorCoord (SinCompatirConOtrosGestores) ------------------------------									
Función utilidad para aquellos socios que no tienen email o que no se manejan bien.

En esta función se dan de alta los socios, por parte de un gestor con rol Coordinación,
a petición de una persona que desea registrarse como socio.
Se sube un archivo al servidor (obligatorio) con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo
Genera un socio con el usuario = NOM.$codUser+125 y añade un digito rand;
una contraseña que es: sha1($codUser.$usuario) (y estará encriptada)
En la tabla USUARIO, el estado quedará: 'alta-sin-password-gestor'

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero		

LLAMADA: desde Menú izquierdo: Alta soci@s cCoordinador:menuGralCoord())

LLAMA:  modelosSocios.php:buscarCuotasAnioEL(), buscarEmailCoordSecreTesor()       
modeloPresCoord.php:mAltaSocioPorGestor()    
modeloArchivos.php:arrMimeExtArchAltaSocioFirmaPermitidas(),cadExtensionesArchivos()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php:
validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor()			
modeloEmail.php:emailErrorWMaster(),emailConfirmarEmailAltaSocioPorGestor(),
emailAltaSocioGestorCoordSecreTesor()							
vistas/coordinador/vAltaSocioPorGestorCoordInc.php
usuariosLibs/encriptar/encriptacionBase64.php
controladores/libs/cNavegaHistoria.php	
require_once ./controladores/libs/inicializaCamposAltaSocioGestor.php;								
																						
OBSERVACIONES: 
Gran parte de esta función es casi idéntica a cPresidente:altaSocioPorGestorPres(), 
y cTesorero:altaSocioPorGestorTes(), que es casi el mismo código 
excepto	la parte de navegación y $tituloSeccion y link, y socio honorario 

***NOTA: 
Esta es la opción de altas de socio por gestor, sin campartir script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y aunque se podría evitar una repetición de parte del código, pueder ser más sencilla 
de seguir y más flexible para cambios según el rol del gestor.
			
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones																		
-----------------------------------------------------------------------------------------------------*/		
function altaSocioPorGestorCoord()
{
	//echo "<br><br>0-1 cCoordinador:altaSocioPorGestorCoord:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cCoordinador:altaSocioPorGestorCoord:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cCoordinador:altaSocioPorGestorCoord:_FILES: ";print_r($_FILES);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{	
		require_once './modelos/modeloArchivos.php';
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './modelos/libs/arrayParValor.php';
		require_once './vistas/coordinador/vAltaSocioPorGestorCoordInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ALTA NUEVO/A SOCIO/A POR COORDINADOR/A"; 
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cCoordinador.php:altaSocioPorGestorCoord(). Error: ';
		$tituloSeccion = "Coordinación";		
		
		//----------------- inicio fila de navegación ---------------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cCoordinador&accion=altaSocioPorGestorCoord";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a por coordinador/a";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>1-1 cCoordinador:altaSocioPorGestorCoord:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion'] = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");		
		//echo "<br><br>1-2 cCoordinador:altaSocioPorGestorCoord:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- fin fila de navegación ------------------------------------
				
		if (!$_POST) 
		{
			require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';//inicializa algunas variables 	

			$parValorCombo = parValoresRegistrarUsuario($valorDefectoPaisDoc,$valorDefectoPaisDom,$valorDefectoAgrup);
							
			//echo "<br><br>2 cCoordinador:altaSocioPorGestorCoord:parValorCombo: "; print_r($parValorCombo); echo "<br><br>";
			
			if ($parValorCombo['codError'] !== '00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
			}	
			else //$parValorCombo['codError'] ==' 00000'
			{ 
					$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');//en modeloSocios.php, incluye conexion(), probado error
					
					//echo "<br><br>3 cCoordinador:altaSocioPorGestorCoord:resCuotasAniosEL: ";print_r($resCuotasAniosEL);
					
					if ($resCuotasAniosEL['codError'] !=='00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resCuotasAniosEL['codError'].": ".$resCuotasAniosEL['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
					}
					else
					{$datCuotaAnioEL = $resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];
				
						$datosInicio['datosFormCuotaSocio']['ANIOCUOTA'] =	$datCuotaAnioEL['General']['ANIOCUOTA'];
						$datosInicio['datosFormCuotaSocio']['CODCUOTAGeneral'] =	$datCuotaAnioEL['General']['CODCUOTA'];	
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'] = $datCuotaAnioEL['General']['IMPORTECUOTAANIOEL'];
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'] =	$datCuotaAnioEL['Joven']['IMPORTECUOTAANIOEL'];
						$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'] =	$datCuotaAnioEL['Parado']['IMPORTECUOTAANIOEL'];
						
						/* [cadExtPermitidas], es un string con las extensiones permitidas para los archivos a subir con firma del socio, se obtiene a 
						partir del array "arrMimeExtArchivoFirmasPermitidas()", esto podría incluirse en "controladores/libs/inicializaCamposAltaSocioGestor.php"
						o ponerlo directamente en el formulario (['cadExtPermitidas'] = "doc,docx,odt,odi,gif,jpg,jpeg,pdf").
					*/
						$arrMimeExtArchivoFirmasPermitidas = arrMimeExtArchAltaSocioFirmaPermitidas();//en modeloArchivos.php			
						$datosInicio['ficheroAltaSocioFirmado']['cadExtPermitidas'] = cadExtensionesArchivos($arrMimeExtArchivoFirmasPermitidas);//en modeloArchivos.php

						//echo "<br><br>4 cCoordinador:altaSocioPorGestorCoord:datosInicio: ";print_r($datosInicio);		
						
						vAltaSocioPorGestorCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$datosInicio,$parValorCombo);	
					}			
			}//else $parValorCombo['codError']=='00000'
		}//if (!$_POST)
		else//if ($_POST) 
		{if (isset($_POST['noGuardarDatosSocio'])) //ha pulsado el botón "noGuardarDatosSocio"
			{
				$datosMensaje['textoComentarios'] = "Has salido sin dar de alta al socio/a";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
			}	
			else //=(isset($_POST['siGuardarDatosSocio']))Pulsado el botón "siGuardarDatosSocio"
			{/* La siguiente función además de validar los datos personales del socio, 
							sube el archivo con la firma para protección de datos.
				*/
				require_once './modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php';
				$resValidarCamposForm = validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor($_POST,$_FILES['ficheroAltaSocioFirmado']);		
				
				//echo "<br><br>5 cCoordinador:altaSocioPorGestorCoord:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				//resValidarCamposForm  SERÁ ALGO COMO:
				//[ficheroSocioFirmado]=>Array([name]=>Tabla_total_medias_2018_06_30_1.doc [type]=>application/msword [tmp_name]=>/tmp/phpEvqaXZ [error]=>0 
				//[size]=>37888 [codError]=>00000 [errorMensaje]=>[directorioSubir]=>/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR [nombreArchExtGuardado]=>villa__SEGUNDOcientocurentaynueve2018_08_18_09_17_25.doc 		
		
				if ($resValidarCamposForm['codError'] !== '00000')//Error
				{
					if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
					{						
							$parValorCombo = parValoresRegistrarUsuario( $resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'], //para vAltaSocioPorGestorCoordInc()
																																																				$resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																				$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']);	
							//echo "<br><br>6-1 cCoordinador:altaSocioPorGestorCoord:parValorCombo: ";print_r($parValorCombo);

							if ($parValorCombo['codError'] !== '00000') 
							{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].":".$parValorCombo['errorMensaje'].":".$parValorCombo['codError'].":".$parValorCombo['errorMensaje']);	
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
							}	
							else 
							{//echo "<br><br>6-2 cCoordinador:altaSocioPorGestorCoord:resValidarCamposForm: ";print_r($resValidarCamposForm);
						
								vAltaSocioPorGestorCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$resValidarCamposForm,$parValorCombo);			
							}
					}//if $resValidarCamposForm['codError'] >= '80000')//Error lógico					
					else //$resValidarCamposForm['codError']< '80000')//Error sistema	
					{ vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
					}
				}//if ($resValidarCamposForm['codError'] !== '00000')//Error	
				else //$resValidarCamposForm['codError']=='00000'
				{ 		 
						$resAltaSocio = mAltaSocioPorGestor($resValidarCamposForm);//en modeloPresCoord.php:mAltaSocioPorGestor()//tiene que devolver CODUSER probado error 

						//echo "<br><br>7 cCoordinador:altaSocioPorGestorCoord:resAltaSocio: ";print_r($resAltaSocio);echo "<br>";

						if ($resAltaSocio['codError'] !== '00000') //siempre será ($resAltaSocio['codError'] < '80000'))
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.' modeloPresCoord.php:mAltaSocioPorGestor(): '.$resAltaSocio['codError'].": ".$resAltaSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);				
						}	
						else //$resAltaSocio['codError']=='00000' 
						{
							$textoComentariosEmail ='';
							//--------------------- Inicio Email a socio -----------------------------
							if ($resValidarCamposForm['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
							{
								require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
								$datSocioConfEmailPass['CODUSER'] = encriptarBase64($resAltaSocio['datosUsuario']['CODUSER']);//['CODUSER']=dato que se genera en mAltaSocioPorGestor()							
								
								$datSocioConfEmailPass['USUARIO']	=	$resAltaSocio['datosUsuario']['USUARIO'];//Es un dato que se genera en mAltaSocioPorGestor()		
								//$datSocioConfEmailPass['CODSOCIO']	= $resAltaSocio['datosSocio']['CODSOCIO'];//Es un dato que se genera en mAltaSocioPorGestor(), por ahora no lo utilizo						
								$datSocioConfEmailPass['EMAIL'] = $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo'];
								$datSocioConfEmailPass['SEXO'] = $resValidarCamposForm['datosFormMiembro']['SEXO']['valorCampo'];
								$datSocioConfEmailPass['NOM'] = $resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];
								$datSocioConfEmailPass['APE1'] = $resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];							
													
								$reEmailConfEstablecerPass =	emailConfirmarEmailAltaSocioPorGestor($datSocioConfEmailPass);//envía mensaje a socio si tiene email probado error ok
								//echo"<br><br>8 cCoordinador:altaSocioPorGestorCoord:reEmailConfEstablecerPass: ";print_r($reEmailConfEstablecerPass);	

								if ($reEmailConfEstablecerPass['codError'] !== '00000')
								{       
										$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta alta como socio/a.';									
										$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailConfEstablecerPass['codError'].": ".$reEmailConfEstablecerPass['errorMensaje'].$textoComentariosEmail);
								}							
							}//--------------------- Fin Email a socio -------------------------------
									
							//--------- Inicio Email a Coordinador,Secretario,Tesororero agrupacion ----									
							$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($resAltaSocio['datosUsuario']['CODUSER']);//en modeloSocios.php para buscar email de CoordSecreTesor, probado error								
				
							//echo"<br><br>9-1 cCoordinador:altaSocioPorGestorCoord:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);

							if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
							{							
								$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta.';																																						
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);    
							}							
							else// $reDatosEmailCoSeTe['codError'] == '00000'
							{						
	//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
	 			   	$reEnviarEmailCoSeTe = emailAltaSocioGestorCoordSecreTesor($reDatosEmailCoSeTe,$resValidarCamposForm);//a gestores probado error		
	//FIN COMENTAR ****************************************************************************************************************
									//echo"<br><br>9-2 cCoordinador:altaSocioPorGestorCoord:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
									if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
									{						
										$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
										$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
									}								
							}	//--------- Fin Email a Coordinador,Secretario,Tesororero agrupacion -----	
							
							$datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
							//echo"<br><br>10 cCoordinador:altaSocioPorGestorCoord:datosMensaje: ";print_r($datosMensaje);							

							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);//pantalla con la información del alta
						}//else $resAltaSocio['codError']=='00000' 
						
				}//else $resValidarCamposForm['codError']=='00000'			
			}//else isset($_POST['siGuardarDatosSocio'])
		}//else $_POST
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
/*---------- Fin altaSocioPorGestorCoord (SinCompatirConOtrosGestores) ------------------------------*/

/*--------- Inicio altaSocioPorGestorCoord_CompartiendoConOtrosGestores ------------------------------													
En esta función se dan de alta los socios, por parte de un gestor con rol Coordinación,
a petición de una persona que desea registrarse como socio.
Se insertan los datos del socio a dar de alta en las tablas correspondientes.
Se sube un archivo al servidor con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo 	

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero
													
LLAMADA: desde Menú izquierdo: Alta soci@s (cCoordinador:menuGralCoord())
LLAMA: require_once './controladores/libs/altaSocioPorGestor.php';
que es la parte común de altas de socios por gestores que a su vez incluye 
varias scripts		
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
													
OBSERVACIONES: Esta función casi idéntica a cPresidente:altaSocioPorGestorPres() 
y a cTesorero:altaSocioPorGestorTes() excepto	en la parte de navegación y 
$tituloSeccion, y link, y también casi igual el mismo código excepto	honorario.

***NOTA: 
Esta es la opción de altas de socio por gestor, campartiendo el script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y para evitar una repetición de parte del código, pueder ser complicada de seguir 
de seguir y menos flexible para cambios según el rol del gestor.	
																																						
2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones																		
------------------------------------------------------------------------------*/		
function altaSocioPorGestorCoord_CompartiendoConOtrosGestores()//funciona bien
{
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	//echo "<br><br>0-1 cCoordinador:altaSocioPorGestorCoord:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cCoordinador:altaSocioPorGestorCoord:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cCoordinador:altaSocioPorGestorCoord:_FILES: ";print_r($_FILES);		
	
	$datosMensaje['textoCabecera'] = "Alta de socio/a por coordinador/a"; 
	$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
	$nomScriptFuncionError = ' cCoordinador.php:altaSocioPorGestorCoord(). Error: ';
	$tituloSeccion = "Coordinación";		
	
	//----------------- inicio fila de navegación ---------------------------------
	$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cCoordinador&accion=altaSocioPorGestorCoord";
	$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a";			
	$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
	//echo "<br><br>1-1 cCoordinador:altaSocioPorGestorCoord:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$datosNavegacion['navegacion'] = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");		
 //echo "<br><br>1-2 cCoordinador:altaSocioPorGestorCoord:datosNavegacion: ";print_r($datosNavegacion);					
	//----------------- fin fila de navegación ------------------------------------
	
	require_once './controladores/libs/altaSocioPorGestor.php';//aquí está la parte común de altas por gestores			

}
/*------- Fin altaSocioPorGestorCoord_CompartiendoConOtrosGestores ---------------------------------*/


/*----------------------------- Inicio enviarEmailSociosCoord  --------------------------------------
Función para enviar un email personalizado por gestor con rol de coordinación en un área de gestión
(que incluye una o varias agrupaciones). En el formulario dentro de ese área de gestión se puede
seleccionar los socios de sólo una agrupación o todas las agrupaciones de ese área
Sólo se enviará a los que tienen condición MIEMBRO.EMAILERROR='NO' y MIEMBRO.INFORMACIONEMAIL='SI'

Además de subject y body puede anexar hasta dos ficheros con un límite de 4MB cada y sólo 
determinados tipos archivos.

El remitente FROM de envío será el email del área de gestión. Ejem: andalucía@europalaica.org 

Es obligatorio una dirección BCC, para que envíe una copia (puede ser como opción de prueba).

Se validan los campos del formulario en la función ":validarCamposEmailAdjuntosSociosCoord()" y 
después en "buscarSeleccionEmailSociosCoord()" se buscan los emails nombres y otros datos
correspondientes a la seleción del formulario.

Mediante la función "emailSociosPersonaGestorPresCoord()" los datos de cada socio en un foreach, 
se tratarán individualmente y el email llegará personalizado a los socios uno a uno con el 
nombre socio y se podrán añadir otros campos procedentes de la función "buscarSeleccionEmailSociosPres()"

RECIBE: campos del formulario de selección y además, el formulario tiene 
tres botones que se reciben y tratan aquí:
-siEnviarEmail: Enviar email a socios/as (Personalizado con nombres socios seleccionados)
-siPruebaEmail: Envía un solo email de prueba a BCC y muestra a cuántos se habría enviado 
-noEnviarEmail: Salir sin enviar email
												
LLAMA: modeloPresCoord.php:buscarSeleccionEmailSociosCoord(),buscarAreaGestionCoord()	
modeloEmail.php:emailSociosPersonaGestorPresCoord(), (ya no uso enviarMultiplesEmailsPhpMailer())
libs/validarCamposEmailAdjuntosSociosCoord.php:validarCamposEmailAdjuntosSociosCoord()
arrayParValor.php:parValoresAgrupaAreaGestionCoord() 
vistas/coordinador/vEnviarEmailSociosCoordInc.php, para introducir los datos, y otras funciones
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()

LLAMDA: desde menú izdo rol de coordinación

OBSERVACION: Probado PHP 7.3.21, No es necesario cambios PDO, las funciones que llama sí incluyen

Los adjuntos, se guardan en un directorio temp, que que se borran automáticamente, después de envío

NOTA: Envía emails a los socios PERSONALIZADOS (con nombre del socio en el body)											
---------------------------------------------------------------------------------------------------*/
function enviarEmailSociosCoord()
{
	//echo "<br><br>0-1 cCoordinador:enviarEmailSociosCoord:_SESSION: ";print_r($_SESSION);	
 //echo "<br><br>0-2 cCoordinador:enviarEmailSociosCoord:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cCoordinador:enviarEmailSociosCoord:_FILES: ";print_r($_FILES);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{	
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/coordinador/vEnviarEmailSociosCoordInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';			
		
		$datosMensaje['textoCabecera'] = "ENVIAR EMAIL A SOCIOS/AS";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido enviar email a socios/as. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cCoordinador.php:enviarEmailSociosCoord(). Error: ';
		$tituloSeccion = "Coordinación";		
				
		//----------------- Inicio fila de navegación ----------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cCoordinador&accion=enviarEmailSociosCoord";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Enviar email a socio/a por coordinador/a";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
			require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");			
		//echo "<br><br>2 cCoordinador:enviarEmailSociosCoord:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- Fin fila de navegación ----------------------------	
		
		if (!$_POST) 
		{
			$areaAgrupCoord = buscarAreaGestionCoord($_SESSION['vs_CODUSER']);//en modeloPresCoord.php:probado error 
			
			//echo "<br><br>3 cCoordinador:enviarEmailSociosCoord:areaAgrupCoord: ";print_r($areaAgrupCoord);	
		
			if ($areaAgrupCoord['codError'] !== '00000') 
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$areaAgrupCoord['codError'].": ".$areaAgrupCoord['errorMensaje']);			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
			}	
			else
			{
				$codAreaCoordinacion = $areaAgrupCoord['resultadoFilas']['CODAREAGESTIONAGRUP'];
				$codAgrup ='%'; //buscar todas las agrupaciones accesibles para el gestor de área 
				$valDefCodAgrupa ='';//valor por defecto			

				$parValorCombo['agrupaSocio'] = parValoresAgrupaAreaGestionCoord($codAreaCoordinacion,$codAgrup,$valDefCodAgrupa);//devuelve solo agrupaciones areas gestion							
				
				if ($parValorCombo['agrupaSocio']['codError'] !== '00000') //ERROR SISTEMA
				{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['agrupaSocio']['codError'].": ".$parValorCombo['agrupaSocio']['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
				}
				else			
				{ $datosEmail = "";//para que no de Notice
						vEnviarEmailSociosCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEmail,$parValorCombo,$areaAgrupCoord['resultadoFilas']);
				}	
			}
		}//if (!$_POST) 
			
		else //if ($_POST) y se han rellenado los datos del formulario para validar y enviar 
		{
			if (isset($_POST['noEnviarEmail'])) //ha pulsado el botón "noEnviarEmail"
			{
				$datosMensaje['textoComentarios'] = "Has salido sin haber enviado los correos a los socios/as";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
			}	
			else //isset($_POST['siEnviarEmail'] || $_POST['siPruebaEmail'] ) //enviar email a socios o de prueba a CC
			{				
				$datosCamposEmailForm = $_POST['datosEmail'];//contiene [camposEmail]:[FROM],[CC],[subject],[body],[pieProteccionDatos] y [datosSelecionEmailSocios]: agrupación, país domicilio, CCAA, y provincia		
				$datosCamposEmailForm['AddAttachment'] = $_FILES;//archivos anexados				
				
				$areaAgrupCoord = $_POST['areaAgrupCoord'];//para vEnviarEmailSociosCoordInc():CODAREAGESTIONAGRUP,NOMBREAREAGESTION,EMAIL
			 $codAreaCoordinacion = $_POST['areaAgrupCoord']['CODAREAGESTIONAGRUP'];//para buscarSeleccionEmailSociosCoord() y parValoresAgrupaAreaGestionCoord()
   
			 //echo "<br><br>4-1 cCoordinador:enviarEmailSociosCoord:datosCamposEmailForm: ";print_r($datosCamposEmailForm);
				
				require_once './modelos/libs/validarCamposEmailAdjuntosSociosCoord.php';
				$datosEnvioEmailSocios = validarCamposEmailAdjuntosSociosCoord($datosCamposEmailForm);//controla errores['codError'] sin necesitar select SQL								
				
				//echo "<br><br>4-2 cCoordinador:enviarEmailSociosCoord:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);

				if ($datosEnvioEmailSocios['codError'] !== '00000')//serán errores lógicos >80000
				{ 
						$codAgrup = '%'; //que aparezcan todas por defecto					

						require_once './modelos/libs/arrayParValor.php';
						$parValorCombo['agrupaSocio'] = parValoresAgrupaAreaGestionCoord($codAreaCoordinacion,$codAgrup,$datosEnvioEmailSocios['datosSelecionEmailSocios']['CODAGRUPACION']);					
				
						if ($parValorCombo['agrupaSocio']['codError'] !=='00000') //ERROR SISTEMA
						{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['agrupaSocio']['codError'].": ".$parValorCombo['agrupaSocio']['errorMensaje']);
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
						}							
						else
						{												
								vEnviarEmailSociosCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEnvioEmailSocios,$parValorCombo,$areaAgrupCoord);	
						}						
				}	
				else //$datosEnvioEmailSocios['codError'] === '00000' 
				{
					/*Dentro de buscarSeleccionEmailSociosCoord(), se vuelve a validar el campo del formulario "$_POST['datosEmail']['datosSelecionEmailSocios']"
							para "Selección los socios/as" por (agrupación), aunque en parte ya se validó en la función anterior
					*/
				 $resSeleccionEmailSocios = buscarSeleccionEmailSociosCoord($_POST['datosEmail']['datosSelecionEmailSocios'],$codAreaCoordinacion);//en modeloPresCoord.php:busca emails socios en agrupa seleccionadas	
				
					//echo "<br><br>5 cCoordinador:enviarEmailSociosCoord:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios); 	
									
					if ($resSeleccionEmailSocios['codError'] !== '00000')
					{
						if ($resSeleccionEmailSocios['codError'] <= '80000') //ERROR sistema SQL se sale con un mensaje
						{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSeleccionEmailSocios['codError'].": ".$resSeleccionEmailSocios['errorMensaje']);
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
						}
						elseif ($resSeleccionEmailSocios['codError'] == '80001' && $resSeleccionEmailSocios['numFilas'] === 0)//no encontrados emails
						{	
	       //	$datosMensaje['textoComentarios'] = $resSeleccionEmailSocios['errorMensaje'];	
		      $datosMensaje['textoComentarios'] = 'No hay ningún email de socios/as para la selección de socios/as en la agrupación que has elegido';																	
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);																													
						}						
					}//if ($resSeleccionEmailSocios['codError'] !== '00000'
					
					else //$resSeleccionEmailSocios['codError'] == '00000'
					{						 
						//echo "<br><br>6 cCoordinador:enviarEmailSociosCoord:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);
						
					 $datosEnvioEmailSocios['emailSociosSeleccionados'] = $resSeleccionEmailSocios['resultadoFilas'];//lista de emails socios

						/*---- Inicio opción $_POST['siPruebaEmail'])-------------------------------------				
							Se eliminan todos los emails de los socios, para que la función no pueda enviar 
							emails a los socios pero SÍ una copia a $datosEnvioEmailSocios['camposEmail']['CC']['valorCampo']
						*/
						if (isset($_POST['siPruebaEmail']) )	  
						{							
								$datosEnvioEmailSocios['emailSociosSeleccionados'] = array();
						}/*---- Fin opción $_POST['siPruebaEmail'])-------------------------------------*/				
						
	     //echo "<br><br>7 cCoordinador:enviarEmailSociosCoord:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);
									
						require_once './modelos/modeloEmail.php';
						
						//NOTA: Envía emails a los socios PERSONALIZADOS (CON nombre en el body) y usa: modeloEmail:enviarEmailPhpMailer();										
						$reEnviarEmail = emailSociosPersonaGestorPresCoord($datosEnvioEmailSocios);//¡¡La buena!! envía uno a uno dentro de un bucle, usa: enviarEmailPhpMailer();					
						
						//NOTA: Envía emails a los socios SIN PERSONALIZADOS (SIN nombre en el body) y usa: modeloEmail:enviarMultiplesEmailsPhpMailer();					
						//$reEnviarEmail = emailSociosMultipleGestorPresCoord($datosEnvioEmailSocios);//NOTA: Envía emails a los socios SIN PERSONALIZADOS (SIN nombre en el body)envío multiple: enviarMultiplesEmailsPhpMailer();					
						
						//echo "<br><br>8 cCoordinador:enviarEmailSociosCoord:reEnviarEmail: ";print_r($reEnviarEmail); 																
						
						/*----------------------Inicio Control Error en funciones de modeloEmail.php ---*/
						if ($reEnviarEmail['codError'] !== '00000')
						{						 
								$datosMensaje['textoComentarios'] = "<br /><br />".$reEnviarEmail['textoComentarios'];//personalizado enviará excluyendo los errores												
						}
						else//$reEnviarEmail['codError'] == '00000'
						{ 
							if (isset($_POST['siPruebaEmail']) )	//$addBCC = array ('EMAIL' => $datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo']."'", 'APE1' => "- COPIA DEL EMAIL ENVIADO -", 'APE2' => "", 'NOM' => "" );  
							{ $datosMensaje['textoComentarios'] = '<strong>Se ha enviado como PRUEBA UN SOLO EMAIL A: '.$datosEnvioEmailSocios['camposEmail']['BCC']['valorCampo'].
																																													'</strong><br /><br />Si hubieses elegido al opción <i>-Enviar email a socios/as-</i> se habría enviado ese mismo email 
																																														personalizado con los nombres a <strong>'.$resSeleccionEmailSocios['numFilas'].' socios/as</strong> 
																																														correspondientes en la opción que has elegido, salvo errores en los emails';
							}
							else //isset($_POST['siEnviarEmail'])
							{
								 $datosMensaje['textoComentarios'] = 		"<br /><br />".$reEnviarEmail['textoComentarios'];																	
							}					
						}//else $reEnviarEmail['codError'] == '00000'			
						
						//echo "<br><br>9 cCoordinador:enviarEmailSociosCoord:reEnviarEmail: ";print_r($reEnviarEmail); 
						/*----------------------Fin Control Error en funciones de modeloEmail.php ------*/			
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']); 
						
			 	}//else $datosEnvioEmailSocios['codError'] == '00000'		 
				}//else $datosEnvioEmailSocios['codError'] === '00000'	
			}//else isset($_POST['siEnviarEmail'])
		}//else if($_POST) 
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
}
/*--------------------------- Fin enviarEmailSociosCoord ------------------------------------------*/



/*-------------------------- Inicio excelSociosCoord ------------------------------------------------
Exporta a un fichero Excel, los socios de todas las agrupaciones correspondientesa un área de gestión
o bien de una agrupación concreta elegida dentro de ese área territorial que gestiona un coordinador. 
Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.
 
LLAMADA: desde menú izdo rol coordinador
LLAMA: modeloPresCoord.php:buscarAreaGestionCoordRol(),exportarExcelSociosCoord()
vistas/coordinador/vExcelMenuCoordInc.php	
vistas/mensajes/vMensajeCabSalirNavInc.php; 
modelos/modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: 
OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
para formar el buffer de salida a excel utiliza "header()" y no puede 
haber ningúna salida delante.

2020-05-21: Probado PHP 7.3.21. No es necesario PDO, lo incluyen internamente algunas de las que 
aquí son llamadas. 							
----------------------------------------------------------------------------------------------------*/
function excelSociosCoord()
{
	//echo "<br><br>0-1 cCoordinador:excelSociosCoord:_SESSION: "; print_r($_SESSION);
	//echo "<br><br>0-2 cCoordinador:excelSociosCoord:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';			
		require_once './vistas/coordinador/vExcelMenuCoordInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';	
		
		$datosMensaje['textoCabecera'] = "EXPORTAR DATOS DE LOS SOCIOS/AS A ARCHIVO EXCEL PARA USO INTERNO DE COORDINACIÓN";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se han podido exportar a Excel los datos de los socios/as de una agrupación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cCoordinador.php:excelSociosCoord(). Error: ';
		$tituloSeccion = "Coordinación";	
		
		$resExportar['codError'] = '00000';
		$resExportar['errorMensaje'] = ''; 
		$_SESSION['vs_reExportar'] ='';
		
		//------------ inicio navegacion ----------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cCoordinador&accion=execelSociosCoord";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Exportar socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -------------------------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{ 
				//echo "<br><br>1 cCoordinador:excelSociosCoord:salirSinExportar: "; print_r($salirSinExportar);
		
				$datosMensaje['textoComentarios'] = "No se han exportado a archivo Excel los datos de los socios/as";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{		
				$cadListaAgrupacionesArea = $_POST['datosFormSocio']['CODAGRUPACION'];//puede ser tambien % todas las correspondientes a ese área de gestión		 

				$resExportar = exportarExcelSociosCoord($cadListaAgrupacionesArea,$_SESSION['vs_CODUSER']);//en modeloPresCoord.php						
				
				//echo "<br><br>3-0 cCoordinador:excelSociosCoord:resExportarSocios: "; print_r($resExportar);
				
				if ($resExportar['codError'] !== '00000')	
				{ 		
      if ($resExportar['codError'] <= '80000')// Error sistema		
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': exportarExcelSociosCoord(): '.$resExportar['codError'].": ".$resExportar['errorMensaje']);	
						}
						elseif ($resExportar['codError'] == '80001' && $resExportar['numFilas'] === 0)//no encontrados socios, no propiamente un error
						{	
	       //	$datosMensaje['textoComentarios'] = $resSeleccionEmailSocios['errorMensaje'];	
		      $datosMensaje['textoComentarios'] = 'Actualmente no hay ningún socio/a para la agrupación que has elegido';	
						}							
				}
				else
				{ 
						$datosMensaje['textoComentarios'] = $resExportar['textoComentarios'];		
				}	
				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
				
			}//else (isset($_POST['SiExportarExcel']))	
		}//if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		else //!if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
				$areaGestionCoordRol = buscarAreaGestionCoordRol($_SESSION['vs_CODUSER']);//en modeloPresCoord.php, busca el area de gestión de ese coordinador
				
				//echo "<br><br>3-2 cCoordinador:excelSociosCoord:areaGestionCoordRol: ";print_r($areaGestionCoordRol); 
				
				if ($areaGestionCoordRol['codError'] !== '00000') 	
				{ 
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarAreaGestionCoordRol(): '.$areaGestionCoordRol['codError'].": ".$areaGestionCoordRol['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}		
				else//$areaGestionCoordRol['codError'] == '00000'
				{ 
						$areaGestionCod = $areaGestionCoordRol['resultadoFilas']['CODAREAGESTIONAGRUP'];	
						$areaGestionNom = $areaGestionCoordRol['resultadoFilas']['NOMBREAREAGESTION'];			
						$codAgrup = '%';     				
						$valorDefecto = '%'; //	$valorDefecto = $areaGestionCod;				

						//echo "<br><br>4 cCoordinador:excelSociosCoord:areaGestionCod: ";print_r($areaGestionCod);							
			
						$parValorCombo = parValoresAgrupaAreaGestionCoord($areaGestionCod,$codAgrup,$valorDefecto);//en modelos/libs/arrayParValor.php	
						
						//echo "<br><br>5 cCoordinador:excelSociosCoord:parValorCombo: ";print_r($parValorCombo);				
								
						if ($parValorCombo['codError'] !== '00000') 	
						{ 
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.':parValoresAgrupaAreaGestionCoord(): '.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						}		
						else
						{								
								vExcelMenuCoordInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,	$parValorCombo,$areaGestionNom);	
						}					
				}//else $areaGestionCoordRol['codError'] == '00000'
		}//else !if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_6']=='SI')

	//echo "<br><br>6 cCoordinador:excelSociosCoord:resExportar: ";print_r($resExportar);
}
/*--------------------------- Fin excelSociosCoord -------------------------------------------------*/



/*====== INICIO PARA PRUEBAS DESCARGAR Y SUBIR ARCHIVOS AL SERVIDOR (EN DESARROLLO) ===============*/
/*=================================================================================================*/

/*------------------------------- Inicio descargarDocsCoord ----------------------------------------
Lleva a una formulario donde se muestran los archivos para descargar  

LLAMA: modeloArchivos.php:obtenerListadoArchivosUnDirectorio(),  o obtenerListadoDeArchivosRecur()
       vistas/socios/vDescargarDocsRolSocioInc.php'; 
       require_once './controladores/libs/cNavegaHistoria.php 

LLAMADA: desde menú lateral de coordinadores

OBSERVACIONES: EN DESARROLLO habría que añadir las filas correspondientes que ahora están en las tablas:
FUNCION_descar_Arch_2020_07_29_noBorrar  y ROLTIENEFUNCION_con_descargar_Arch_2020_07_29_noBorrar,
para que aparezcan en el menú izdo según los roles.
También se podría añador otros roles: Presidencia, Tesoreria

2020-04-21: Añado y puede que sirva como plantilla para los demás roles
NOTA: existe la clase La clase SplFileInfo, que se podría utilizar como alternativa a este desarrollo
Agustin: 2020-04-21 añado para pruebas
---------------------------------------------------------------------------------------------------*/
function descargarDocsCoord()
{
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_6'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	//echo "<br><br>0-1 cCoordinador:descargarDocsCoord:_GET: ";print_r($_GET);
	//echo "<br><br>0-2 cCoordinador:descargarDocsCoord:_REQUEST: ";print_r($_REQUEST);
	echo "<br><br>0-3 cCoordinador:descargarDocsCoord:_POST: ";print_r($_POST);	 
	echo "<br><br>0-4 cCoordinador:descargarDocsCoord:_SESSION: ";print_r($_SESSION);

	require_once './modelos/modeloArchivos.php';
 require_once './vistas/socios/vDescargarDocsRolSocioInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
	require_once './modelos/modeloEmail.php';
	
	$datosMensaje['textoCabecera'] = 'Documentos y manuales para el coordinador/a';
	$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los \"Documentos y manuales\" para el coordinador/a. Pruebe de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
	$nomScriptFuncionError = ' controladorSocios.phpdescargarDocsCoord(). Error: ';		
	
	$tituloSeccion  = "Coordinación";		

 //------------ inicio navegación para socios gestores CODROL > 2 --------------	
	if (isset($_SESSION['vs_autentificadoGestor']))
 {
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cCoordinador&accion=menuGralCoord")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cCoordinador&accion=descargarDocsCoord";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Documentos y manuales para el coordinador/a<br />";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
 }
	else
	{$navegacion = "";
	}		

	echo "<br><br>3-1 cCoordinador:descargarDocsCoord:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
	echo "<br><br>3-2 cCoordinador:descargarDocsCoord:navegacion: ";print_r($navegacion);	
	//------------ fin navegación -------------------------------------------------	
	
	//echo "<br><br>4-0 cCoordinador:descargarDocsCoord:directorio actual: "; echo getcwd();
	echo "<br><br>4-0 cCoordinador:descargarDocsCoord:_SERVER['DOCUMENT_ROOT']: "; print_r($_SERVER['DOCUMENT_ROOT']);
 
	if (isset($_POST['directorio']) && !empty($_POST['directorio']))
	{ echo "<br><br>4-0a cCoordinador:descargarDocsCoord:directorio: ";print_r($_POST);
  $directorioArchivos = $_POST['directorio'];//para pruebas multinivel
	}
	else
	{echo "<br><br>4-0b cCoordinador:descargarDocsCoord:directorio: ";print_r($_POST); 
  $directorioArchivos = "/DOCUMENTOS_PRUEBA/";//para pruebas multinivel
		//$directorioArchivos = "/documentos/SOCIOS_PRUEBA/";//para un solo nivel
	}	

 //$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto 	
	//echo "<br><br>4-1 cCoordinador:descargarDocsCoord:directorioPathArchivosAbrir: "; print_r($directorioPathArchivosAbrir);
	echo "<br><br>4-2 cCoordinador:descargarDocsCoord:directorioPath: ".realpath($_SERVER['DOCUMENT_ROOT']);
	
	//str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] ) : mixed
	//$direct = str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$directorioPathArchivosAbrir );
	//echo "<br><br>4-3 cCoordinador:descargarDocsCoord:directorioPath: $directorioPathArchivosAbrir";
	//echo "<br><br>4-4 cCoordinador:descargarDocsCoord:directorioPath: $direct";

	$arrLista['codError'] = '00000';
	$arrLista['errorMensaje'] =''; 

	$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto 
 //$arrListaArchivos = obtenerListadoArchivosUnDirectorio($directorioArchivos);	
	
	echo "<br><br>4-2 cCoordinador:descargarDocsCoord:directorioPathArchivosAbrir: "; print_r($directorioPathArchivosAbrir); 
	
	//$recursivo = true;	//funciona
	$recursivo = false;	//funciona
	//function obtenerListadoDeArchivosRecur($directorioArchivos, $recursivo = false)
	//$arrListaArchivos['listaArchivos'] = obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo);
	$arrLista  = obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo);//funciona
	
	//$arrListaArchivos['listaArchivos'] = dirToArray($directorioPathArchivosAbrir);
	//$arrListaArchivos['listaArchivos'] = dirToArrayMio($directorioPathArchivosAbrir);	
	//echo "<br><br>4-3 cCoordinador:descargarDocsCoord:arrListaArchivos: "; print_r($arrListaArchivos); 
	//$arrListaArchivos['listaArchivos']= array_sort($people, 'surname', SORT_ASC)); // Sort by surname	
	
	echo "<br><br>4-4 cCoordinador:descargarDocsCoord:arrLista: "; print_r($arrLista); 
	
 if (isset($arrLista['codError']) && $arrLista['codError'] !== '00000')
 {
		$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrListaArchivos['codError'].": ".$arrListaArchivos['errorMensaje']);
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
 }      
 else 
 { 		echo "<br><br>5-1-- cCoordinador:descargarDocsCoord:arrLista: ";var_dump($arrLista);
    //array_multisort(array_column($arrLista, 'DirPath'), SORT_ASC, $arrLista);		
				//array_multisort(array_column($arrLista, 'DirPath'), SORT_DESC, array_column($arrLista, 'Nombre'), SORT_ASC, $arrLista);					
			 //array_multisort(array_column($arrLista, 'NombreDirectorio'), SORT_ASC,  $arrLista);	
				//array_multisort(array_column($arrLista, 'NombreArch'), SORT_DESC, array_column($arrLista, 'NombreDirectorio'), SORT_ASC,  $arrLista);
			 //array_multisort(array_column($arrLista, 'NombreDirectorio'), SORT_ASC, array_column($arrLista, 'NombreArch'),  SORT_DESC, $arrLista);
				
				/*
				Estos dos últimos hacen lo mismo, ordena todo solo  como SORT_DESC (directorios y archivos), 
				y no muestran errores o warning 
				OPCIONES: ORDENAR POR SEPARADO, HACER YO MISMO LA FUNCIÓN PARA MI CASO, VER ADAPTACIONES
				*/
				array_multisort(array_column($arrLista, 'DirPath'), SORT_ASC, array_column($arrLista, 'Nombre'),  SORT_DESC, $arrLista);
				//array_multisort(array_column($arrLista, 'Nombre'),  SORT_DESC, $arrLista);
					
    echo "<br><br>5-2-- cCoordinador:descargarDocsCoord:arrLista: ";var_dump($arrLista);
					
			 $arrListaArchivos['listaArchivos'] = $arrLista;
				$arrListaArchivos['directorio'] = $directorioArchivos;
				
				echo "<br><br>5-3 cCoordinador:descargarDocsCoord:arrListaArchivos: ";print_r($arrListaArchivos);
				
			vDescargarDocsRolSocioInc($tituloSeccion,$arrListaArchivos,$navegacion);	 	
 } 
}
//--------------------------- Fin descargarDocsCoord() ---------------------------------------------

/*======= FIN PARA PRUEBAS DESCARGAR Y SUBIR ARCHIVOS AL SERVIDOR (EN DESARROLLO) ===============*/

?>