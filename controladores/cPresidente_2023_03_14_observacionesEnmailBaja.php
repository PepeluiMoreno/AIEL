<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: cPresidente.php
PROYECTO: EL
VERSION: PHP 7.3.19
DESCRIPCION: En este fichero se encuentran las funciones relacionadas con la
             gestión de los socios por el presidente, vicepresidente	y 
													también para secretario
													
OBSERVACIONES:

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

------------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()--------------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya hay activada una sesion,
aunque no es un error puede mostrar un "Notice", si warning esta activado. 
Para evitar estos Notices, uso la función is_session_started(), que he creado que controla el estado
con session_status() para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES: 
2020-07-29: creo la función "is_session_started()" para evitar Notices
------------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 cPresidente.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 cPresidente.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()----------------------------------------------------*/


/*--------------------------- Inicio menuGralPres ----------------------------------------------------
Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de Presidencia, Vice. y 
Secretaría (CODROL=3) y se muestran en el menú lateral

Se pueden añadir un enlaces a archivos para descargarlo, estaría en el cuerpo debajo de la 
imagen de "ESCUELA LAICA".

LLAMADA: por ser gestor:controladorLogin.php:menuRolesUsuario():login/vRolInc.php 
también al moverse de un rol a otro en menú izdo, o en línea superior de enlaces

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 
2020-05-23: quí no necesita cambios PDO.
2017-04-23: Añado $enlacesArchivos, para manuales
-----------------------------------------------------------------------------------------------------*/
function menuGralPres()//podría ser la misma para todos los usuarios,CODROL=SESSION 
{	
	//echo "<br><br>0-1 cPresidente:menuGralPres:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cPresidente:menuGralPres:_POST: "; print_r($_POST);
	//echo "<br><br>0-3 cPresidente:menuGralPres:_GET: "; print_r($_GET);
		
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else // if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloUsuarios.php';		
		
		$datosMensaje['textoCabecera'] = "PRESIDENCIA, VICEPRESIDENCIA Y SECRETARIA";
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los el menú correspondiente al Rol de Presidencia, 
		                                     Vicepresidencia o Secretaría. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
		$nomScriptFuncionError = ' cPresidente.php:menuGralPres(). Error: ';			
		$tituloSeccion  = 'Presidencia, Vice. y Secretaría';			

		/*------------ inicio navegación para socios gestores CODROL >1 ------------*/
		$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cPresidente&accion=menuGralPres";
		$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Presidencia, vice. y secretaría";			
		$_SESSION['vs_HISTORIA']['pagActual']=2;	
			
		//echo "<br><br>2cPresidente:menuGralPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA'])					
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		/*------------ Fin navegación para socios gestores CODROL >1 ---------------*/				

  require_once './modelos/modeloUsuarios.php';	
	 $resFuncionRol = buscarRolFuncion('3');//CODROL=3 para presidente en modelosUsuarios.php, incluye insertarError()
		//echo "<br><br>1 cPresidente:menuGralPres:resFuncionRol: ";print_r($resFuncionRol);	

		if ($resFuncionRol['codError'] !== '00000')		
	 {require_once './modelos/modeloEmail.php';	
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resFuncionRol['codError'].": ".$resFuncionRol['errorMensaje']);		
			
			require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	 }			
  else //($resFuncionRol['codError'] == '00000')
	 {		 
		 //$_SESSION['vs_CODROL']=$_GET['CODROL'];
		 //$enlacesSeccId=$resFuncionRol['resultadoFilas'];//acaso no sea necesario			
	  $_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];//Se podrá acceder desde cualquier sitio
			
			$cabeceraCuerpo = 'PRESIDENCIA, VICEPRESIDENCIA Y SECRETARIA';	
			$textoCuerpo = 'Desde el menú puedes acceder a las funciones disponibles para el <strong>Rol de Presidencia, Vicepresidencia o Secretaría</strong> 
			que tienes asignado en la aplicación de Gestión de Soci@s.';
			
			$enlacesArchivos = array();
			/* Si quisieramos añadir unos enlaces a unos archivos para descargarlos, estarían 
			   en el cuerpo debajo de la imagen de "ESCUELA LAICA", por ejemplo lo siguiente: 
			*/
			$enlacesArchivos[1]['link'] = '../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf';
			$enlacesArchivos[1]['title'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';
			$enlacesArchivos[1]['textoMenu'] = 'Descargar el formulario de alta del socio/a para firmar por el socio';	
			
			$enlacesArchivos[2]['link'] = '../documentos/PRESIDENCIA/EL_MANUAL_GESTOR_Presidencia.pdf';
			$enlacesArchivos[2]['title'] = 'Descargar el Manual para Presidencia, Vice, o Secretaría de la aplicación informática de Gestión de Soci@s';
			$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Presidencia, Vice, o Secretaría de la aplicación informática de Gestión de Soci@s';			

			require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);			
	 }//else $resFuncionRol['codError'] == '00000'		 
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------ Fin menuGralPres ---------------------------------------------------*/

/*------------------------------- Inicio mostrarSociosCoord ------------------------------------------
Se forma y muestra una tabla-lista paginada "LISTA DE SOCI@S" de los socios de con algunos campos de 
información de los socios de todas las agrupaciones de EL, pero sólo de los que están dados de alta 
Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios.

Aquí se incluye la paginación de la lista de socios. En la parte inferior se muestran número de páginas
para poder ir directamente a un página, anterior, siguiente, primera, última.
La vista correspondiente "vMostrarSociosPresInc.php" en forma de tabla, además de mostrar algunos datos
de cada socio, al final para cada fila, hay iconos links para: ver detalles de ese socio, 
modificar datos, borrar datos socio 	
	
LLAMADA: desde Menú izquierdo: Lista soci@s (cPresidente.php:menuGralPres())
LLAMA: require_once 'controladores/libs/cPresCoordSociosApeNomPaginarInc.php'que incluye mucho: 
el control de las búsquedas (APE1, APE"), formación  select para pasarlo "mPaginarLib".
modelos/libs/mPaginarLib.php:mPaginarLib():  llama a buscar y paginar, algo antigua pero funciona bien
añadido $arrBindValues para PDO.
modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord()
vistas/mensajes/vMensajeCabSalirNavInc.php
modelos/modeloEmail.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: En esta función se incluye $arrBindValues para PDO 7.3.21
cPresCoordSociosApeNomPaginarInc.php que es de uso compartido también 
por cCoordinador.php:mostrarSociosCoord()
--------------------------------------------------------------------------------------------------------------*/	
function mostrarSociosPres()
{	
	//echo "<br><br>0-1 cPresidente:mostrarSociosPres:SESSION: ";print_r($_SESSION); 
 //echo "<br><br>0-2 cPresidente:mostrarSociosPres:_POST: "; print_r($_POST);
	//echo "<br><br>0-3 cPresidente:mostrarSociosPres:_GET: "; print_r($_GET);
	//echo "<br><br>0-4 cPresidente:mostrarSociosPres:_REQUEST: "; print_r($_REQUEST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './vistas/presidente/vMostrarSociosPresInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	
		require_once './controladores/libs/cNavegaHistoria.php';	
		
		$datosMensaje['textoCabecera'] = 'LISTA DE SOCIOS/AS (no incluye las bajas)';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Lista de socios/as'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente.php:mostrarSociosPres(). Error: ';	
		$tituloSeccion  = 'Presidencia, Vice. y Secretaría';		      
				
		//--------- Inicio controles APE, CODAGRUPACION y de navegación ---------------
		
		/* Se inicia "$codAreaCoordinacion" y otras variables para utilizarlas dentro
				de require_once './controladores/libs/cPresidenteSociosApeNomPaginarInc.php'
				Las variables $NomFuncion...	se incluyen para asignar los valores a 
				$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']= ... 		
		*/	
		$codAreaCoordinacion = '%';	//Este rol tiene acceso a todas las agrupaciones
		/* Asigno estos valores para que "cPresCoordSociosApeNomPaginarInc.php" sirva 
				tanto para "cPresidente.php:mostrarSociosPres()" como para 
				cCoordinador.php:mostrarSociosCoord()        
		*/
		$NomFuncionActualMostrarSocios ='cPresidente&accion=mostrarSociosPres';	
		$NomFuncionMostrarDatosSocio = "cPresidente&accion=mostrarDatosSocioPres";
		$NomFuncionActualizarSocio = "cPresidente&accion=actualizarSocioPres";
		$NomFuncionEliminarSocio =	"cPresidente&accion=eliminarSocioPres";
		
		require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php';
		//require_once './controladores/libs/cPresidenteSociosApeNomPaginarInc.php';//anteriormente
		/*--- Hay mucho dentro, se crean las cadSelect "$_pagi_sql", $arrBindValues, 
		(que a su vez se forman en modeloTesorero:cadBuscarCuotasSocios,...)	
		que serán necesarios para la función siguiente "mPaginarLib()	
		y también $datosFormMiembro, si se busca por APE, APE2 ----------------------*/
		
		//echo 	"<br><br>1-1 cPresidente:mostrarSociosPres:_pagi_sql: ";print_r($_pagi_sql);
		//echo 	"<br><br>1-2 cPresidente:mostrarSociosPres:arrBindValues: ";print_r($arrBindValues);//arrBindValues para cadena SELECT $_pagi_sql
		
		//-------------------------------- inicio navegación --------------------------
		//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" ya que puede variar dentro de "require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php'
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=mostrarSociosPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Lista socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>2cPresidente:mostrarSociosPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
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
		
		//echo "<br><br>4 cPresidente:mostrarSociosPres:resDatosSocios: ";print_r($resDatosSocios);
		
		if ($resDatosSocios['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocios['codError'].": ".$resDatosSocios['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		}   
		else //$resDatosSocios['codError']=='00000'
		{
			//echo "<br><br>5 cPresidente:mostrarSociosPres:_SESSION['vs_CODAGRUPACION']: ";print_r($_SESSION['vs_CODAGRUPACION']);
			// Se busca y genera array de agrupaciones para mostrar y elegir en el formulario 
			$codAgrup = '%';//buscar todas agrupaciones por ser rol Presidencia, Vice. y Secretaría
			
			require_once './modelos/libs/arrayParValor.php';//añadido nuevo 2020-03-28			
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$_SESSION['vs_CODAGRUPACION']);//probado error	
							
			//echo "<br><br>6 cPresidente:mostrarSociosPres:parValorCombo: ";print_r($parValorCombo);

			if ($parValorCombo['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
			}	
			else //Si no errores en $parValorCombo 
			{	
				$resDatosSocios['navegacion'] = $navegacion;
				
				//echo "<br><br>7 cPresidente:mostrarSociosPres:resDatosSocios['navegacion']: ";print_r($resDatosSocios['navegacion']);
				//echo "<br><br>8 cPresidente:mostrarSociosPres:datosFormMiembro: ";print_r($datosFormMiembro);
				
				/* $datosFormMiembro contendrá los datos APE1 o APE2 de ese socio si se busca por APE1 o APE2
							si no será $datosFormMiembro ='', este valor se forma en cPresCoordSociosApeNomPaginarInc.php
				*/
				vMostrarSociosPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosSocios,$parValorCombo,$datosFormMiembro);  
			}
		}//$resDatosSocios['codError']=='00000' 
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin mostrarSociosPres ------------------------------------------------*/

/*------------------------------- Inicio mostrarDatosSocioCoord -------------------------------------
Se muestran algunos datos personales de un socio
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono "Ver = Lupa"
 
La parte de navegación se añade, para mantener la fila superior la navegación de opciones enlaces 
para el rol de 'Presidencia, Vice. y Secretaría' 
	
RECIBE: $_POST['datosFormUsuario']['CODUSER']
	
LLAMADA: desde "LISTA DE SOCIO/AS" para Presidencia al hacer clic en icono Ver
LLAMA: modelos/modelos/modeloSocios.php:buscarDatosSocio()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: Probado PHP 7.3.21
En esta función no se precisa modificar $arrBindValues para PDO, laa funciones ya lo 
incluyen internamente.
----------------------------------------------------------------------------------------------------*/
function mostrarDatosSocioPres()
{ 	
 //echo "<br><br>0-1 cPresidente:mostrarDatosSocioPres:POST: ";print_r($_POST);	 
 //echo "<br><br>0-2 cPresidente:mostrarDatosSocioPres:_SESSION: ";print_r($_SESSION);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{
		require_once './modelos/modeloSocios.php';
		require_once './vistas/presidente/vMostrarDatosSocioPresInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'Mostrar datos del socio/a';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al mostrar los datos de un socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$nomScriptFuncionError = ' cPresidente.php:mostrarDatosSocioPres(). Error: ';	
		
		$tituloSeccion  = 'Presidencia, Vice. y Secretaría';	      

		//-------------------------------- inicio navegación --------------------------
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=mostrarDatosSocioPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar datos socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>2 cPresidente:mostrarDatosSocioPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- fin navegación ----------------------------------

		$anioCuota = '%';
		$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];

		$resDatosSocio = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php incluye insertarError()
		
		//echo "<br><br>3 cPresidente:mostrarDatosSocioPres:resDatosSocio: "; print_r($resDatosSocio); 
		
		if ($resDatosSocio['codError'] !== '00000')
		{		
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocio['codError'].": ".$resDatosSocio['errorMensaje']);
			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}      
		else //$resDatosSocio['codError']=='00000'
		{	
			//echo "<br><br>4 cPresidente:mostrarDatosSocioPres:resDatosSocio: "; print_r($resDatosSocio);
			
			vMostrarDatosSocioPresInc($tituloSeccion,$resDatosSocio,$navegacion);	

		} //$resDatosSocio['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin mostrarDatosSocioPres --------------------------------------------*/

/*------------------------------- Inicio actualizarSocioPres ----------------------------------------
Se actualizan datos personales de un socio, cuotas, IBAN, agrupación y afecta a 
varias varias tablas.
Necesita varias funciones entre otras 'modelos/libs/prepMostrarActualizarCuotaSocio.php' que es 
compartida con otros controladores de gestor.		
En ella se preparar campos de cuota del socio, para mostrar y actualizar la cuota en formularios 
de actualización socioSocios, y hay que guardar los valores anteriores porque algunos 
no los modifique (es común) o hay error al introducir los nuevos datos.

RECIBE: $_SESSION, $_POST[], $_POST['campoHide'] este contiene datos de 
require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php';	

LLAMADA: desde tabla "LISTA DE SOCI@S", en "formMostrarDatosSocioPres.php" 
con clic en icono Modifica				

LLAMA: modeloSocios.php:buscarDatosSocio(),actualizarDatosSocio()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor()
controladores/libs/inicializaCamposActualizarSocio.php
(contiene mucho código, que acaso se pueda simplificar)
controladores/libs/arrayEnviaRecibeUrl.php:arrayRecibeUrl(),arrayEnviaUrl()
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php: navegación fila superior links
vistas/presidente/vActualizarSocioPresInc.php:vActualizarSocioPresInc()
						
La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, según los roles

OBSERVACIONES:
2020-05-28: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza  

OJO: 	LLAMA A LA FUNCIÓN actualizarDatosSocio() que está en modeloSocios.php
NOTA: similar a controladorSocios.php:actualizarSocio() y cCoordinador.php:actualizarSocioCoord(),
cTesorero.php:actualizarSocioTes(),

arrayEnviaUrl() y arrayRecibeUrl($_POST['campoHide']) envía y recibe un array a post con 
['anteriorUSUARIO']['anteriorEMAIL']['anteriorCODPAISDOC']['anteriorTIPODOCUMENTOMIEMBRO']['anteriorNUMDOCUMENTOMIEMBRO']
---------------------------------------------------------------------------------------------------*/
function actualizarSocioPres()
{
	//echo "<br><br>0-1 cPresidente:actualizarSocioPres:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-2 cPresidente:actualizarSocioPres:POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './controladores/libs/arrayEnviaRecibeUrl.php';
		require_once './modelos/libs/arrayParValor.php';		
		require_once './modelos/modeloSocios.php';
		require_once './vistas/presidente/vActualizarSocioPresInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ACTUALIZAR DATOS SOCIA/O ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al actualizar datos de la socia/o. No se han podido actualizar los datos del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:actualizarSocioPres(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría"; 
		
		//------------ Inicio navegacion ---------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosPres")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=actualizarSocioPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar datos socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>2 cPresidente:actualizarSocioPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ Fin navegacion ------------------------------------------------	
			
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			if (isset($_POST['salirSinActualizar']))
			{
				$nomApe1 = strtoupper($_POST['campoActualizar']['datosFormMiembro']['NOM']." ".$_POST['campoActualizar']['datosFormMiembro']['APE1']);
				$datosMensaje['textoComentarios'] = "No se han modificado los datos de ".$nomApe1;	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['comprobarYactualizar']))
			{$_POST['campoHide'] = arrayRecibeUrl($_POST['campoHide']);//pasa un string (obtenido con arrayEnviaUrl) a array en /controladores/libs/arrayEnviaRecibeUrl.php	 
					
				//echo "<br><br>3 cPresidente:actualizarSocioPres:POST: ";print_r($_POST);
					
				require_once './modelos/libs/validarCamposSocioPorGestor.php';
				$resValidarCamposForm = validarCamposActualizarSocioPorGestor($_POST);			
				
		  //echo "<br><br>4 cPresidente:actualizarSocioPres:resValidarCamposForm: ";print_r($resValidarCamposForm);		
				
				if (($resValidarCamposForm['codError'] !== '00000') && ($resValidarCamposForm['codError'] > '80000'))
				{$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['campoActualizar']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resValidarCamposForm['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],			
																																																	$resValidarCamposForm['campoActualizar']['datosFormSocio']['CODAGRUPACION']['valorCampo']);
																																																	
					if ($parValorCombo['codError'] !== '00000') 
					{ vMensajeCabSalirNavInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
					}	
					else
					{ $resValidarCamposForm['campoHide'] = arrayEnviaUrl($resValidarCamposForm['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php		
							vActualizarSocioPresInc($tituloSeccion,$resValidarCamposForm,$parValorCombo,$navegacion);		
					}			
				}													
				else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
				{//echo "<br><br>5-1 cPresidente:actualizarSocioPres$resValidarCamposForm['campoActualizar']: ";print_r($resValidarCamposForm['campoActualizar']);	

					$resActDatosSocio = actualizarDatosSocio($resValidarCamposForm['campoActualizar']);//en modeloSocios.php, incluye conexion(), 
					
					//echo "<br><br>5-2 cPresidente:actualizarSocioPres:resActDatosSocio:";print_r($resActDatosSocio);		
					
					if ($resActDatosSocio['codError'] !== "00000")
					{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}		
					else // $resActDatosSocio['codError'] == '00000')
					{	$datosMensaje['textoComentarios'] = $resActDatosSocio['arrMensaje']['textoComentarios'];			
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}	        
				}//else $resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
			}//else (isset($_POST['comprobarYactualizar']))	 				  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			//echo "<br><br>7 cPresidente:actualizarSocioPres:SESSION: ";print_r($_SESSION);
			$anioCuota = '%';		
			$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];		
			
			$resDatosSocioActualizar = buscarDatosSocio($usuarioBuscado,$anioCuota);// en modeloSocios.php, probado error incluye insertarError()
				
			//echo "<br><br>8 cPresidente:actualizarSocioPres:resDatosSocioActualizar: ";print_r($resDatosSocioActualizar);
		
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
						//$datMostrarActualizarCuotaSocio = prepMostrarActualizarCuotaSocio($resDatosSocioActualizar);//incluye consultas SQL y controladores/libs/arrayEnviaRecibeUrl.php probado error
					 require_once './controladores/libs/inicializaCamposActualizarSocio.php';
					 $datMostrarActualizarCuotaSocio = inicializaCamposActualizarSocio($resDatosSocioActualizar);						
						
						//echo "<br><br>9 cPresidente:actualizarSocioPres:datMostrarActualizarCuotaSocio: ";print_r($datMostrarActualizarCuotaSocio);
										
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
							
							//echo "<br><br>10 cPresidente:actualizarSocioPres:datosSocioFormActualizar: ";	print_r($datosSocioFormActualizar);	
							vActualizarSocioPresInc($tituloSeccion,$datosSocioFormActualizar,$parValorCombo,$navegacion);
						}	
					}//else $parValorCombo['codError']=='00000'
				}//else $resDatosSocioActualizar['codError']=='00000'  
		}//!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion'])) 
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin actualizarSocioPres ---------------------------------------------*/

/*------------ Inicio eliminarSocioPres_SinCompatirConOtrosGestores ---------------------------------
Se eliminan datos identificativos del socio (privacidad de datos), y se insertan algunos datos 
en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.

En caso de que hubiese un archivo con la firma de un socio debido a lata por gestor, también
se eliminaría el archivo del servidor.

Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Baja.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos 
datos personales en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.

La parte de navegación se añade, para mantener la fila superior la navegación 

LLAMADA: Menú presidencia>>Lista socios/as-->Baja	(al hacer clic en el icono Baja)
(vistas/presidente/formEliminarSocioPres.php)

LLAMA: modelos/libs/validarCamposSocio.php:validarEliminarSocio()
modeloSocios.php:eliminarDatosSocios(),modeloPresCoord.php:bajaSocioFallecido()
modeloSocios.php:buscarDatosSocio(),buscarEmailCoordSecreTesor()
modeloEmail.php:emailBajaUsuario(),emailBajaUsuarioFallecido(),$reEnviarEmailCoSeTe(),
emailErrorWMaster()
vistas/presidente/vEliminarSocioPresInc.php
modelos/libs/validarCamposSocio.php:validarEliminarSocio()
controladores/libs/cNavegaHistoria.php
							
OBSERVACIÓN: Es casi idéntico a cCoordinador:eliminarSocioCoord() y cTesorero:eliminarSocioTes() 
excepto por la navegación y menú izdo y se podría compartir código entre ellos, pero con el
inconveniente menos claro de seguir, y más rigidez para posibles modificaciones.

2020-09-10: probada PHP 7.3.21, Aquí no necesita cambios para PDO, lo incluyen 
internamente las funciones		
----------------------------------------------------------------------------------------------------*/
function eliminarSocioPres()
{
 //echo "<br><br>0-1 cPresidente:eliminarSocioPres:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:eliminarSocioPres:POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './modelos/libs/validarCamposSocio.php';
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/presidente/vEliminarSocioPresInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
		$nomScriptFuncionError = ' cPresidente.php:eliminarSocioPres(). Error: ';	
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";
		
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosPres")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=eliminarSocioPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
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
					
					//echo "<br><br>3 cPresidente:eliminarSocioPres:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
					if ($resValidarCamposForm['codError'] !=='00000')
					{	
							if ($resValidarCamposForm['codError'] >= '80000')//Error lógico		probado		
							{
									vEliminarSocioPresInc($tituloSeccion,$resValidarCamposForm,$navegacion); 
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
									//echo "<br><br>4-1 cPresidente:eliminarSocioPres:reEliminarSocio "; print_r($reEliminarSocio);			
							}
							elseif (isset($_POST['SiEliminarFallecimiento']))
							{	
									$reEliminarSocio = bajaSocioFallecido($_POST);	//en modeloPresCoord.php probado error
									//echo "<br><br>4-2 cPresidente:eliminarSocioPres:reEliminarSocio "; print_r($reEliminarSocio);
							}	
							
							if ($reEliminarSocio['codError'] !== "00000")
							{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarSocio['codError'].": ".$reEliminarSocio['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
							}
							else //($reEliminarSocio['codError'] == '00000')
							{						
									//echo"<br><br>5-1 cPresidente:eliminarSocioPres:sesion: ";print_r($_SESSION);
									$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
									
									if 	( $usuarioBuscado == $_SESSION['vs_CODUSER'] )//solo si el Gestor se borra a él mismo	
									{	unset($_SESSION);//para que ya no esté como autorizado
										//echo"<br><br>5-2 cPresidente:eliminarSocioPres:sesion: ";print_r($_SESSION);
									}	
									
									$textoComentariosEmail = '';
									//-------------------- Inicio email a socio -------------------------------		
									if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	//si falta, no se envía email
									{
											if (isset($_POST['SiEliminarFallecimiento']))
											{ $resultEnviarEmail = emailBajaUsuarioFallecido($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-1 cPresidente:eliminarSocioPres:resultEnviarEmail: ";print_r($resultEnviarEmail);	
											}	
											else
											{	$resultEnviarEmail = emailBajaUsuario($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-2 cPresidente:eliminarSocioPres:resultEnviarEmail: ";print_r($resultEnviarEmail);
											}	

											if ($resultEnviarEmail['codError'] !== '00000')//probado error
											{ $textoComentariosEmail = '<br /><br />Por un error no se ha podido envíar el email con la información de la baja, a la dirección de correo que está anotada para el socio.';									
													$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['errorMensaje'].$textoComentariosEmail);
											}			
									}//if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	
									//----------------------- Fin	email a socio -------------------------------						
												
									//-------------------- Inicio email a Coord Secre Tes Pres ----------------	
									$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($datosSocio['datosFormUsuario']['CODUSER']);
						
									//echo"<br><br>6-2- cPresidente:eliminarSocioPres:reDatosEmailCoSeTe:";print_r($reDatosEmailCoSeTe);
									if ($reDatosEmailCoSeTe['codError'] !== '00000')
									{ $textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';																																						
											$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);									
									}
									else 
									{
									 	if (isset($datosSocio['datosFormSocio']['OBSERVACIONES']) && !empty($datosSocio['datosFormSocio']['OBSERVACIONES']))
											{$datosSocio['datosFormSocio']['OBSERVACIONES'] = "Observaciones desde gestión por Presidencia, Vicepresidencia, Secretaría: ".$datosSocio['datosFormSocio']['OBSERVACIONES'];
											}	
								   //se añade este texto a los comentarios que ya vienen de modeloSocios.php:eliminarDatosSocios() o modeloPresCoord.php:bajaSocioFallecido($_POST);	
								   $reEliminarSocio['arrMensaje']['textoComentarios'] .="<br /><br /><br />Se ha enviado un email a Presidencia, Secretaría, Tesorería y Coordinación de la agrupación para informar de esta baja";													

									//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
				//****************************************************************************************************************
				       $reEnviarEmailCoSeTe = emailBajaSocioCoordSecreTesor($reDatosEmailCoSeTe,$datosSocio);
				//FIN COMENTAR ****************************************************************************************************************

											//echo"<br><br>7 cPresidente:eliminarSocioPres:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
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
			//echo "<br><br>9 cPresidente:eliminarSocioPres:datSocioEliminar: "; print_r($datSocioEliminar); 
										
			if ($datSocioEliminar['codError'] !== '00000')
			{			
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioEliminar['codError'].": ".$datSocioEliminar['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioEliminar['codError']=='00000'
			{//echo "<br><br>10 cPresidente:eliminarSocioPres:datSocioEliminar:";print_r($datSocioEliminar);	

				vEliminarSocioPresInc($tituloSeccion,$datSocioEliminar,$navegacion); 
			}   
		}//!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']))
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*---------- Fin eliminarSocioPres_SinCompatirConOtrosGestores ------------------------------------*/

/*------------ Inicio eliminarSocioPres_CompartiendoConOtrosGestores -------------------------------
Se eliminan datos identificativos del socio (privacidad de datos), y se insertan
algunos datos en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Baja.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos datos personales
en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.
La parte de navegación se añade, para mantener la fila superior la navegación de opciones 

LLAMADA:  Menú presidencia>>Lista socios/as-->Baja	
(vistas/presidente/formEliminarSocioPres.php)

LLAMA: require_once './controladores/libs/eliminarSocioPorGestor.php';
						 que es la parte común de bajas de socios por gestores y que a su vez incluye 
						 varias scripts		
							
							controladores/libs/cNavegaHistoria.php
							
OBSERVACIÓN: Es casi idéntico a cTesoreria:eliminarSocioTes(),cCoordinador:eliminarSocioCoord(),
excepto por la nevegación y menú izdo por eso comparto código entre ellos.
Inconveniente menos claro de seguir y comparto un único formulario de baja:
vistas/gestoresComun/vEliminarSocioGestorInc.php

2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones	
--------------------------------------------------------------------------------------*/
function eliminarSocioPres_Compartiendo()
{ 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	//echo "<br><br>0-1 cPresidente:eliminarSocioPres:SESSION: ";print_r($_SESSION);  
	echo "<br><br>0-2 cPresidente:eliminarSocioPres:POST: ";print_r($_POST);	

	$datosMensaje['textoCabecera'] = "Baja del socio/a";	
	$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
	$nomScriptFuncionError = ' cPresidente.php:eliminarSocioPres(): ';	
 $tituloSeccion  = "Presidencia, Vice. y Secretaría";
	
 //------------ inicio navegacion ----------------------------------------------	
 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
	
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosPres")		
	{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
	}	
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=eliminarSocioPres";	
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a ";
 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
	require_once './controladores/libs/cNavegaHistoria.php';
 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
 //------------ fin navegacion -------------------------------------------------

 //NOTA: este es el include que se podría compartir con cTesorero:eliminarSocioTes(),cCoordinador:eliminarSocioCoord(),
 require_once './controladores/libs/eliminarSocioPorGestor.php';//aquí está la parte común de altas por gestores

}
/*---------- Fin eliminarSocioPres_CompartiendoConOtrosGestores -----------------------------------*/



/*----------- Inicio altaSocioPorGestorPres (SinCompatirConOtrosGestores) ---------------------------
En esta función se dan de alta los socios, por parte de un gestor con rol Presidencia,
a petición de una persona que desea registrarse como socio.
Se sube un archivo al servidor con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo
Genera un socio con el usuario = NOM.$codUser+125 y añade un digito rand;
una contraseña que es: sha1($codUser.$usuario) (y estará encriptada)
En la tabla USUARIO, el estado quedará: 'alta-sin-password-gestor'

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero

LLAMADA: desde Menú izquierdo: Alta soci@s (cPresidente:menuGralPres()

LLAMA:  modelosSocios.php:buscarCuotasAnioEL(), buscarEmailCoordSecreTesor()       
modeloPresCoord.php:mAltaSocioPorGestor()  
modeloArchivos.php:arrMimeExtArchAltaSocioFirmaPermitidas(),cadExtensionesArchivos()      
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php		
modeloEmail.php:emailConfirmarEmailAltaSocioPorGestor(),emailAltaSocioGestorCoordSecreTesor()
/vistas/presidente/vAltaSocioPorGestorPresInc.php	
usuariosLibs/encriptar/encriptacionBase64.php
controladores/libs/cNavegaHistoria.php
require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';
													
OBSERVACIONES: Gran parte de esta función casi idéntica a cCoordinador:altaSocioPorGestorCoord(), 
que es casi el mismo código excepto	honorario la parte de navegación y $tituloSeccion y link.
Similar a cTesorero.php:altaSocioPorGestorTes()			

***NOTA: 
Esta es la opción de altas de socio por gestor, sin campartir script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y aunque se podría evitar una repetición de parte del código, pueder ser más sencilla 
de seguir y más flexible para cambios según el rol del gestor.	
																								
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones								
--------------------------------------------------------------------------------------------------*/		
function altaSocioPorGestorPres()
{
	//echo "<br><br>0-1 cPresidente:altaSocioPorGestorPres:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cPresidente:altaSocioPorGestorPres:POST: ";print_r($_POST);	
	//echo "<br><br>3-3 cPresidente:altaSocioPorGestorPres:_FILES: ";print_r($_FILES);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './modelos/modeloArchivos.php';
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './modelos/libs/arrayParValor.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/presidente/vAltaSocioPorGestorPresInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		
		$datosMensaje['textoCabecera'] = "ALTA NUEVO/A SOCIO/A POR PRESIDENCIA, VICE. y SECRETARÍA"; 
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:altaSocioPorGestorPres(). Error: ';
		$tituloSeccion = "Presidencia, Vice. y Secretaría";	

		//----------------- Inicio fila de navegación ---------------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=altaSocioPorGestorPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>1-1 cPresidente:altaSocioPorGestorPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
		require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");		
		//echo "<br><br>1-2 cPresidente:altaSocioPorGestorPres:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- Fin fila de navegación ------------------------------------
			
		if (!$_POST) 
		{
			require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';//inicializa algunas variables 	

			$parValorCombo = parValoresRegistrarUsuario($valorDefectoPaisDoc,$valorDefectoPaisDom,$valorDefectoAgrup);//antes $parValorCombo = parValoresRegistrarUsuario("ES","ES",'00000000');						
			//echo "<br><br>2 cPresidente:altaSocioPorGestorPres:parValorCombo: "; print_r($parValorCombo);
			
			if ($parValorCombo['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);								
			}	
			else //$parValorCombo['codError']=='00000'
			{		
				$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');//en modeloSocios.php, incluye conexion() probado error
			
				//echo "<br><br>3 cPresidente:altaSocioPorGestorPres:resCuotasAniosEL: ";print_r($resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']);		
				
				if ($resCuotasAniosEL['codError'] !== '00000')
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
					$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'] =	$datCuotaAnioEL['Honorario']['IMPORTECUOTAANIOEL'];
					
					/* [cadExtPermitidas], es un string con las extensiones permitidas para los archivos a subir con firma del socio, se obtiene a 
						partir del array "arrMimeExtArchivoFirmasPermitidas()", esto podría incluirse en "controladores/libs/inicializaCamposAltaSocioGestor.php"
						o ponerlo directamente en el formulario (['cadExtPermitidas'] = "doc,docx,odt,odi,gif,jpg,jpeg,pdf").
					*/
					$arrMimeExtArchivoFirmasPermitidas = arrMimeExtArchAltaSocioFirmaPermitidas();//en modeloArchivos.php			
					$datosInicio['ficheroAltaSocioFirmado']['cadExtPermitidas'] = cadExtensionesArchivos($arrMimeExtArchivoFirmasPermitidas);//en modeloArchivos.php
					
					//echo "<br><br>4 cPresidente:altaSocioPorGestorPres:datosInicio:";print_r($datosInicio);				
					
					vAltaSocioPorGestorPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$datosInicio,$parValorCombo);
				}			
			}//else $parValorCombo['codError']=='00000'
		}//if (!$_POST) 
		else //if ($_POST) 
		{if (isset($_POST['noGuardarDatosSocio'])) //ha pulsado el botón "noGuardarDatosSocio"
			{
				$datosMensaje['textoComentarios'] = "Has salido sin dar de alta al socio/a";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
			}	
			else //==(isset($_POST['siGuardarDatosSocio']))Pulsado el botón "siGuardarDatosSocio"
			{/* La siguiente función además de validar los datos personales del socio,
							sube el archivo con la firma para protección de datos.
				*/
				require_once './modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php';
				$resValidarCamposForm = validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor($_POST,$_FILES['ficheroAltaSocioFirmado']);		
					
				//echo "<br><br>5 cPresidente:altaSocioPorGestorPres:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				//resValidarCamposForm  SERÁ ALGO COMO:
				//[ficheroSocioFirmado] => Array ( [name] => Tabla_total_medias_2018_06_30_1.doc [type] => application/msword [tmp_name] => /tmp/phpEvqaXZ [error] => 0 
				//[size]=>37888 [codError]=>00000 [errorMensaje]=>[directorioSubir]=>/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR [nombreArchExtGuardado]=>villa__SEGUNDOcientocurentaynueve2018_08_18_09_17_25.doc)) 					
			
				if ($resValidarCamposForm['codError'] !== '00000') //Error
				{
					if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
					{
						$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																		$resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																		$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']);	
						//echo "<br><br>6-1 cPresidente:altaSocioPorGestorPres:parValorCombo: ";print_r($parValorCombo);

						if ($parValorCombo['codError'] !== '00000') 
						{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
						}	
						else //habría que mandar las cuotas y nombres del año actual buscandolas de importedescuotatasocio
						{//echo "<br><br>6-2 cPresidente:altaSocioPorGestorPres:resValidarCamposForm: ";print_r($resValidarCamposForm);
						
							vAltaSocioPorGestorPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$resValidarCamposForm,$parValorCombo);			
						}
					}//if $resValidarCamposForm['codError'] >= '80000')//Error lógico	
					else //$resValidarCamposForm['codError']< '80000')//Error sistema					
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
					}
				}//if ($resValidarCamposForm['codError'] !== '00000')//Error		
				else //$resValidarCamposForm['codError']=='00000'
				{
						$resAltaSocio = mAltaSocioPorGestor($resValidarCamposForm);//en modeloPresCoord.php:mAltaSocioPorGestor()//tiene que devolver CODUSER probado error
						
						//echo "<br><br>7 cPresidente:altaSocioPorGestorPres:resAltaSocio: ";print_r($resAltaSocio);	
						
						if ($resAltaSocio['codError'] !== '00000') //siempre será ($resAltaSocio['codError'] < '80000'))
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resAltaSocio['codError'].": ".$resAltaSocio['errorMensaje']);					
							
							$datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'];
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);	
						}	
						else // ($resAltaSocio['codError']=='00000') 
						{
							$textoComentariosEmail = '';						
							//--------------------- Inicio Email a socio -----------------------------
							if ($resValidarCamposForm['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
							{
								require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
								$datSocioConfEstablecerPass['CODUSER'] = encriptarBase64($resAltaSocio['datosUsuario']['CODUSER']);//['CODUSER']=dato que se genera en mAltaSocioPorGestor()			
								
								$datSocioConfEstablecerPass['USUARIO']	=	$resAltaSocio['datosUsuario']['USUARIO'];//Es un dato que se genera en mAltaSocioPorGestor()	
								//$datSocioConfEmailPass['CODSOCIO']	= $resAltaSocio['datosSocio']['CODSOCIO'];//Es un dato que se genera en mAltaSocioPorGestor(), por ahora no lo utilizo										
								$datSocioConfEstablecerPass['EMAIL'] = $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo'];
								$datSocioConfEstablecerPass['SEXO'] = $resValidarCamposForm['datosFormMiembro']['SEXO']['valorCampo'];
								$datSocioConfEstablecerPass['NOM']  = $resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];
								$datSocioConfEstablecerPass['APE1'] = $resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];	
								
								$reEmailConfEstablecerPass =	emailConfirmarEmailAltaSocioPorGestor($datSocioConfEstablecerPass);//envía mensaje a socio si tiene email	//probado error
								//echo"<br><br>8 cPresidente:altaSocioPorGestorPres:reEmailConfEstablecerPass: ";print_r($reEmailConfEstablecerPass);			
					
								if ($reEmailConfEstablecerPass['codError'] !== '00000')
								{       
										$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta alta como socio.';									
										$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailConfEstablecerPass['codError'].": ".$reEmailConfEstablecerPass['errorMensaje'].$textoComentariosEmail);
								}				
							}//--------------------- Fin Email a socio -------------------------------					
						
							//--------- Inicio Email Coordinador,Secretario,Tesororero agrupacion ----
							$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($resAltaSocio['datosUsuario']['CODUSER']);//en modeloSocios.php para buscar email de CoordSecreTesor, probado error								
					
							//echo"<br><br>9-1 cPresidente:altaSocioPorGestorPres:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);
							
							if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
							{
									//$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta alta como socio.';									
							 	$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta.';																																													
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailConfEstablecerPass['codError'].": ".$reEmailConfEstablecerPass['errorMensaje'].$textoComentariosEmail);
							}					
							else	// $reDatosEmailCoSeTe['codError'] == '00000'
							{								
		//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
		//****************************************************************************************************************
								$reEnviarEmailCoSeTe = emailAltaSocioGestorCoordSecreTesor($reDatosEmailCoSeTe,$resValidarCamposForm);//a gestores						
		//FIN COMENTAR ****************************************************************************************************************
								//echo"<br><br>9-2 cPresidente:altaSocioPorGestorPres:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
								if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
								{						
									$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
								}											
							}//--------- Fin Email Coordinador,Secretario,Tesororero agrupacion -----	
							
							$datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
							//echo"<br><br>10 cPresidente:altaSocioPorGestorPres:datosMensaje: ";print_r($datosMensaje);	
							
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);//pantalla con la información del alta						
						}//else $resAltaSocio['codError']=='00000'  
						
				}//else $resValidarCamposForm['codError']=='00000'
			}//else isset($_POST['siGuardarDatosSocio'])
		}//else $_POST 	
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*------------- Fin altaSocioPorGestorPres (SinCompatirConOtrosGestores) --------------------------*/

/*---------- Inicio altaSocioPorGestorPres_CompartiendoConOtrosGestores ----------------------------
En esta función se dan de alta los socios, por parte de un gestor con rol Presidencia,
a petición de una persona que desea registrarse como socio.
Se insertan los datos del socio a dar de alta en las tablas correspondientes.
Se sube un archivo al servidor con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo 	

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero
													
LLAMADA: desde Menú izquierdo: Alta soci@s (cPresidente:menuGralPres())
LLAMA: require_once './controladores/libs/altaSocioPorGestor.php';
que es la parte común de altas de socios por gestores que a su vez incluye 
varias scripts							
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: Esta función casi idéntica a cTesorero:altaSocioPorGestorTes() 
excepto	en la parte de navegación y $tituloSeccion y link, y también casi igual 
a cCoordinador:altaSocioPorGestorCoord(), que es el mismo código excepto	honorario.

***NOTA: 
Esta es la opción de altas de socio por gestor, campartiendo el script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y para evitar una repetición de parte del código, pueder ser complicada de seguir 
de seguir y menos flexible para cambios según el rol del gestor.	
																																						
2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones				
------------------------------------------------------------------------------*/		
function altaSocioPorGestorPres_CompartiendoConOtrosGestores()//funciona bien
{
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	
	//echo "<br><br>0-1 cPresidente:altaSocioPorGestorPres:_SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cPresidente:altaSocioPorGestorPres:POST: ";print_r($_POST);	
 //echo "<br><br>3-3 cPresidente:altaSocioPorGestorPres:_FILES: ";print_r($_FILES);		

	$datosMensaje['textoCabecera'] = "Alta de socio/a por Presidencia, Vice. y Secretaría"; 
	$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
	$nomScriptFuncionError = ' cPresidente.php:altaSocioPorGestorPres(). Error: ';
	$tituloSeccion = "Presidencia, Vice. y Secretaría";	

	//----------------- inicio fila de navegación ---------------------------------
	$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=altaSocioPorGestorPres";
	$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a";			
	$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
	//echo "<br><br>1-1 cPresidente:altaSocioPorGestorPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");		
 //echo "<br><br>1-2 cPresidente:altaSocioPorGestorPres:datosNavegacion: ";print_r($datosNavegacion);					
	//----------------- fin fila de navegación ------------------------------------
 
	require_once './controladores/libs/altaSocioPorGestor.php';//aquí está la parte común de altas por gestores

}
/*--------------- Fin altaSocioPorGestorPres_CompartiendoConOtrosGestores ------------------------*/



/*----------------------------- Inicio enviarEmailSociosPres  -------------------------------------
Función para enviar un email personalizado o no según elección por gestor con rol de presidencia,
vice, secretaría a una lista de socios que se elige del formulario 
seleccionados por valores de: CODAGRUPACION, CODPAISDOM, CCAA, CODPROV

Sólo se enviará a los que tienen condición MIEMBRO.EMAILERROR='NO' y MIEMBRO.INFORMACIONEMAIL='SI'

Además de subject y body puede anexar hasta dos ficheros con un límite de 4MB cada y sólo 
determinados tipos archivos.

Permite elegir entre los siguientes emails de envío: presidencia@europalaica.org,  
vicepresidencia@europalaica.org,secretaria@europalaica.org", 

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
vistas/presidente/vEnviarEmailSociosPresInc.php, para introducir los datos, 
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
otras funciones para validar, obtener emails socios y enviar email

LLAMADA: desde menú izdo rol de presidencia, vice, secretaría

OBSERVACION: Probado PHP 7.3.21, No es necesario cambios PDO, las funciones que llama sí incluyen

Los adjuntos, se guardan en un directorio temp, que que se borran automáticamente, después de envío

NOTA: Cuando envía emails a los socios PERSONALIZADOS (el nombre del socio va en el body)
--------------------------------------------------------------------------------------------------*/
function enviarEmailSociosPres()// tres opciones envío
{
	//echo "<br><br>0-1 cPresidente:enviarEmailSociosPres:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cPresidente:enviarEmailSociosPres:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cPresidente:enviarEmailSociosPres:_FILES: ";print_r($_FILES);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/presidente/vEnviarEmailSociosPresInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';			

		$datosMensaje['textoCabecera'] = "ENVIAR EMAIL A SOCIOS/AS";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se han podido enviar los emails a socios/as seleccionados. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:enviarEmailSociosPres(). Error: ';
		$tituloSeccion = "Presidencia, Vice. y Secretaría";	
				
		//----------------- Inicio fila de navegación ---------------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=enviarEmailSociosPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Enviar email a socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
			
		//echo "<br><br>1 cPresidente:enviarEmailSociosPres:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- Fin fila de navegación ------------------------------------	
		
		if (!$_POST) //$datosContactarEmail['asunto']
		{//mejor una funcion para los cuatro???	
			$parValorCombo['agrupaSocio']   = parValoresAgrupaPresCoord('%','');//todas: bien para presidente
			$parValorCombo['paisDomicilio'] = parValorPais('');	
			$parValorCombo['CCAADomicilio'] = parValoresCCAA('');
			$parValorCombo['provDomicilio'] = parValorProvincia('');

			//echo "<br><br>2 cPresidente:enviarEmailSociosPres:parValorCombo: ";print_r($parValorCombo);	
			
			if ($parValorCombo['agrupaSocio']['codError'] !==   '00000' || $parValorCombo['paisDomicilio']['codError'] !== '00000' || 
							$parValorCombo['CCAADomicilio']['codError'] !== '00000' ||	$parValorCombo['provDomicilio']['codError'] !== '00000')
			{ 
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError." Error en funciones parValor: 70100"); //probado errores
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
			}
			else
			{ $datosEmail ='';//para evitar warning		 
					vEnviarEmailSociosPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEmail,$parValorCombo);		
			}
		}//if (!$_POST) 
			
		else //POST y se han rellenado los datos del formulario para validar y enviar 
		{
			if (isset($_POST['noEnviarEmail'])) //ha pulsado el botón "noEnviarEmail"
			{
				$datosMensaje['textoComentarios'] = "Has salido sin haber enviado los emails a los socios/as";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
			}	
			else //isset($_POST['siEnviarEmail'] || $_POST['siPruebaEmail'] ) //enviar email a socios o de prueba a CC
			{
					$datosCamposEmailForm = $_POST['datosEmail'];//contiene [camposEmail]:[FROM],[CC],[subject],[body],[pieProteccionDatos] y [datosSelecionEmailSocios]: agrupación, país domicilio, CCAA, y provincia		
					$datosCamposEmailForm['AddAttachment'] = $_FILES;//archivos anexados	
					
					//echo "<br><br>3 cPresidente:enviarEmailSociosPres:datosCamposEmailForm: ";print_r($datosCamposEmailForm); 

     require_once './modelos/libs/validarCamposEmailAdjuntosSociosPres.php';
					$datosEnvioEmailSocios = validarCamposEmailAdjuntosSociosPres($datosCamposEmailForm);//controla ['codError'], no son errores SQL
				
					//echo "<br><br>4-1 cPresidente:enviarEmailSociosPres:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios); 
	
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
							{	vEnviarEmailSociosPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion'],$datosEnvioEmailSocios,$parValorCombo);
							}						
     }
     else//($datosEnvioEmailSocios['codError'] === '00000') // bien validarCamposEmailAdjuntosSociosPres()
     {				
						require_once './modelos/modeloPresCoord.php';					
						$resSeleccionEmailSocios = buscarSeleccionEmailSociosPres($datosEnvioEmailSocios['datosSelecionEmailSocios']);//en modeloPresCoord.php:probado error ok		
					
 					//echo "<br><br>5 cPresidente:enviarEmailSociosPres:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);
											
						if ($resSeleccionEmailSocios['codError'] !== '00000')
						{ 							
							if ($resSeleccionEmailSocios['codError'] <= '80000') //ERROR sistema
							{ 
							  $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSeleccionEmailSocios['codError'].": ".$resSeleccionEmailSocios['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);			
							}
							elseif ($resSeleccionEmailSocios['codError'] == '80001' && $resSeleccionEmailSocios['numFilas'] === 0)//no encontrados emails, puede no ser un error
							{ 
		       $datosMensaje['textoComentarios'] = 'No hay ningún email de socios/as para las condiciones de selección de socios/as que has elegidas (En Agrupación, País, CCAA, Provincia )';			
														
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);																				
							}							
						}//if $resSeleccionEmailSocios['codError'] !== '00000')						
						
						else //$resSeleccionEmailSocios['codError'] == '00000')// BIEN buscarSeleccionEmailSociosPres()
						{
								//echo "<br><br>6 cPresidente:enviarEmailSociosPres:datosEnvioEmailSocios: ";print_r($datosEnvioEmailSocios);		
								
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
        //echo "<br><br>7 cPresidente:enviarEmailSociosPres:reEnviarEmail: ";print_r($reEnviarEmail);
								
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
								
								//echo "<br><br>8 cPresidente:enviarEmailSociosPres:reEnviarEmail: ";print_r($reEnviarEmail);
								/*----------------------Fin Control Error en funciones de modeloEmail.php ------*/					

								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']); 
						
					 }//else ($datosEnvioEmailSocios['codError'] === '00000')// BIEN buscarSeleccionEmailSociosPres()
					}//else $datosEnvioEmailSocios['codError']=='00000'// bien validarCamposEmailAdjuntosSociosPres()
			}//else (isset($_POST['siEnviarEmail'] || $_POST['siPruebaEmail'] ) 
		}//else $_POST
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin enviarEmailSociosPres ------------------------------------------*/


/*-------------------------- Inicio excelSociosPres ----------------------------------------------
Exporta los socios de una agrupación teritorial, o todas (% por defecto) a un 
fichero Excel. Permite elegir agrupación 

LLAMADA: desde menú izdo rol Presidencia, vice. y secretaría
LLAMA: modeloPresCoord.php:exportarExcelSociosPres() sustituye a exportarExcelSociosPresCoord()
vistas/coordinador/vExcelMenuPresInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modelos/modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: 
OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
para formar el buffer de salida a excel utiliza "header()" y no puede 
haber ningúna salida delante.

No es necesario PDO, lo incluyen internamente algunas de las 
que aquí son llamadas. PHP 7.3.21
								
-------------------------------------------------------------------------------------------------*/
function excelSociosPres()
{
	//echo "<br><br>0-1 cPresidente:excelSociosPres:_SESSION: "; print_r($_SESSION);
	//echo "<br><br>0-2 cPresidente:excelSociosPres:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './modelos/modeloPresCoord.php';			
		require_once './vistas/presidente/vExcelMenuPresInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';			
		
		$datosMensaje['textoCabecera'] = "EXPORTAR LISTA DE SOCIOS/AS A EXCEL";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se han podido exportar a Excel los datos de los socios/as elegidos. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente:excelSociosPres(). Error: ';
		$tituloSeccion = "Presidencia, Vice. y Secretaría";	
		
		$resExportar['codError'] = '00000';
		$resExportar['errorMensaje'] = ''; 
			
		//------------ inicio navegacion ----------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=execelSociosPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Exportar socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -------------------------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{
					$datosMensaje['textoComentarios'] = "No se han exportado a archivo Excel los datos de los socios/as";
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			}
			else //(isset($_POST['SiExportarExcel']))
			{
				$codAgrupacion = $_POST['datosFormSocio']['CODAGRUPACION'];
											
				$resExportarSocios = exportarExcelSociosPres($codAgrupacion);//en modeloPresCoord.php
				
				//echo "<br><br>2 cPresidente:excelSociosPres:resExportarSocios: "; print_r($resExportarSocios);	

				if ($resExportarSocios['codError'] !== '00000')	
				{ 		
      if ($resExportarSocios['codError'] <= '80000')// Error sistema		
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': exportarExcelSociosCoord(): '.$resExportarSocios['codError'].": ".$resExportarSocios['errorMensaje']);	
						}
						elseif ($resExportarSocios['codError'] == '80001' && $resExportarSocios['numFilas'] === 0)//no encontrados socios, no propiamente un error
						{	
	       //	$datosMensaje['textoComentarios'] = $resSeleccionEmailSocios['errorMensaje'];	
		      $datosMensaje['textoComentarios'] = 'Actualmente no hay ningún socio/a para la agrupación que has elegido';	
						}							
				}
				else
				{ 
						$datosMensaje['textoComentarios'] = $resExportarSocios['textoComentarios'];		
				}	
				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
	
			}//else (isset($_POST['SiExportarExcel']))	
		}//if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])) 
			
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
				$codAgrup = '%'; //buscar todas agrupaciones por ser presidente
				$valorDefecto = '%';
							
				$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefecto);//en modelos/libs/arrayParValor.php
				
				//echo "<br><br>3 cPresidente:excelSociosPres:parValorCombo: ";print_r($parValorCombo);
				
				if ($parValorCombo['codError'] !== '00000') //$resInsertar = $parValorCombo;
				{ 
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.':parValoresAgrupaAreaGestionCoord(): '.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
				}	
				else//$parValorCombo['codError']=='00000'
				{	
					vExcelMenuPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorCombo);										
				}//$parValorCombo['codError']=='00000'

		}//else !(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin excelSociosPres -----------------------------------------------*/



/*=================================== INCIO ESTADISTICAS ========================================*/

/*------------------- Inicio cMenuEstadisticasPres()  ---------------------------------------------
Para llamar a la página del menú del estadísticas de los socios
Uso exclusivo de rol presidecia. 

Desde ese menú se podrá llamar a: mostrarSociosFallecidosPres(),cExportarExcelInformeAnualPres(),
cExportarExcelEstadisticasAltasBajasAgrupPres(),cExportarExcelEstadisticasAltasBajasProvPres(),
cExportarExcelEstadisticasAltasBajasCCAAPres()

LLAMADA: desde menú izdo de rol presidecia
LLAMA: vistas/presidente/vMenuEstadisticasPresInc.php:vMenuEstadisticasPresInc()

OBSERVACIONES: no necesita cambio PDO. PHP 7.3.21 
--------------------------------------------------------------------------------------------------*/
function cMenuEstadisticasPres()
{
	//echo "<br><br>0-1 cPresidente.php:cMenuEstadisticasPres:_POST: "; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:cMenuEstadisticasPres:_SESSION: "; print_r($_SESSION);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		$tituloSeccion = "Presidencia, vice. y secretaría";			

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=cMenuEstadisticasPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Estadísticas socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		require_once './vistas/presidente/vMenuEstadisticasPresInc.php'; 	
		vMenuEstadisticasPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
 }// else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
//--------------------------- Fin cMenuEstadisticasPres() -----------------------------------------

/*----------------- Inicio mostrarSociosFallecidosPres --------------------------------------------
A partir de la tabla SOCIOSFALLECIDOS, en la que se inserta una fila cuando un gestor da de baja a 
un socio anotándolo como fallecido, se ontiene el listado que se muestra en formato tabla como
"LISTA DE SOCI@S FALLECIDOS" paginadas, con algunos campos de información de los socios fallecidos
de todas las agrupaciones de EL ordenados alfabéticamente. 

Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios

Aquí se incluye la páginación de la lista de socios fallecidos. En la parte inferior se muestran
número de páginas para poder ir directamente a un página, anterior, siguiente, priemera, última.

LAMADA: cPresidente&accion=cMenuEstadisticasPres
vistas/presidentes/vMenuEstadisticasPres.php (página menú ESTADÍSTICAS Y 
DATOS DISPONIBLES PARA PRESIDENCIA)

LLAMA: controladores/libs/cPresidenteSociosFellecApeNomPaginarInc.php,
modelos/libs/mPaginarLib.php
vistas/presidente/vMostrarSociosFallecidosPresInc.php,
controladores/libs/cNavegaHistoria.php,
parValoresAgrupaPresCoord, 
vistas/mensajes/vMensajeCabSalirNavInc.php, 
modelos/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: En esta función se incluye $arrBindValues para PDO. PHP 7.3.21

OJO:"controladores/libs/cPresidenteSociosFellecApeNomPaginarInc.php" incluye muchas cosas: el control
de las búsquedas (APE1, APE"), y formación de select, para pasarlo "mPaginarLib".

-------------------------------------------------------------------------------------------------*/	
function mostrarSociosFallecidosPres()
{
 //echo "<br><br>0-1 cPresidente:mostrarSociosFallecidosPres:_POST: "; print_r($_POST);
 //echo "<br><br>0-3 cPresidente:mostrarSociosFallecidosPres:SESSION: ";print_r($_SESSION);	

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{					
		require_once './modelos/modeloSocios.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	
		
		$datosMensaje['textoCabecera'] = 'LISTA DE SOCIOS/AS FALLECIDOS	 ';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Lista de socios/as fallecidos'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente.php:mostrarSociosFallecidosPres(). Error: ';	
		$tituloSeccion = "Presidencia, Vice. y Secretaría";	
		
		//$codAreaCoordinacion ='%';	
		$anioBaja	 = '%'; //por si más adelante se incluye buscar por años
		
		require_once './controladores/libs/cPresidenteSociosFallecApeNomPaginarInc.php';
		/*--- Hay mucho dentro, se crean las cadSelect "$_pagi_sql", $arrBindValues, 
			(que a su vez se forman en modeloTesorero:cadBuscarCuotasSocios,...)	
			que serán necesarios para la función siguiente "mPaginarLib()	
			y también $datosFormMiembro, si se busca por APE, APE2 --------------------*/

		//echo 	"<br><br>1-1 cPresidente:mostrarSociosFallecidosPres:_pagi_sql: ";print_r($_pagi_sql);
		//echo 	"<br><br>1-2 cPresidente:mostrarSociosFallecidosPres:arrBindValues: ";print_r($arrBindValues);//arrBindValues para cadena SELECT $_pagi_sql	

		//-------------------------------- inicio navegación --------------------------
		//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" ya que puede variar dentro de "require_once './controladores/libs/cPresidenteSociosFallecApeNomPaginarInc.php'"
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=cMenuEstadisticasPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=mostrarSociosFallecidosPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Lista de socio@s fallecidos";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;			
		require_once './controladores/libs/cNavegaHistoria.php';
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
		$resDatosSocios = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error	ok

		//echo "<br><br>2-1 cPresidente:mostrarSociosFallecidosPres:_SESSION['vs_CODAGRUPACION']: ";print_r($_SESSION['vs_CODAGRUPACION']); 
		//echo "<br><br>2-2 cPresidente:mostrarSociosFallecidosPres:_SESSION['vs_pag_actual']: ";print_r($_SESSION['vs_pag_actual']); 	
		//echo "<br><br>2-3 cPresidente:mostrarSociosFallecidosPres:resDatosSocios: ";print_r($resDatosSocios); 

		if ($resDatosSocios['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocios['codError'].": ".$resDatosSocios['errorMensaje']);  
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
		}      
		else //$resDatosSocios['codError']=='00000'
		{
			$codAgrup ='%'; //buscar todas las agrupaciones por ser presidente
			
			require_once './modelos/libs/arrayParValor.php';	
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$_SESSION['vs_CODAGRUPACION']);//probado error	ok
			
			//echo "<br><br>3 cPresidente:mostrarSociosFallecidosPres:parValorCombo: ";print_r($parValorCombo);
			if ($parValorCombo['codError'] !== '00000') 
			{
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
			}	
			else
			{
				$resDatosSocios['navegacion'] = $navegacion;
				
				//echo "<br><br>4 cPresidente:mostrarSociosFallecidosPres:resDatosSocios['navegacion': ";print_r($resDatosSocios['navegacion']);					
				//echo "<br><br>5 cPresidente:mostrarSociosFallecidosPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
				//echo "<br><br>6 cPresidente:mostrarSociosFallecidosPres:datosFormMiembro: ";print_r($datosFormMiembro);			
				/* $datosFormMiembro contendrá los datos APE1 o APE2 de ese socio fallecido si se busca por APE1 o APE2
							si no será $datosFormMiembro ='', este valor se forma en cPresidenteSociosFallecApeNomPaginarInc.php
				*/			
				
				require_once './vistas/presidente/vMostrarSociosFallecidosPresInc.php';
				vMostrarSociosFallecidosPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosSocios,$parValorCombo,$datosFormMiembro);	            
			}
		}//$resDatosSocios['codError']=='00000'  
	}//	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	
}
/*--------------------------- Fin mostrarSociosFallecidosPres -----------------------------------*/

/*-------------------------- Inicio cExportarExcelInformeAnualSec ---------------------------------
Cierre de año: exporta datos soci@s a Excel para informe anual de Secretaría

En el archivo Excel se incluye todos los socios/as que en el año correspondiente estuvieron de alta,
aunque después en ese mismo año se diesen de baja.

Permite elegir agrupación (aunque lo normal es que se incluyan todas) y el año desde 2009 
(lo normal es incluir el año que finalizó)

LLAMADA: cPresidente.php:cMenuEstadisticasPres(), mediante formulario 
vistas/presidente/vMenuEstadisticasPresInc.php()

LLAMA: modeloPresidente.php:mExportarExcelInformeAnualPres(), 
vistas/presidente/vExportarExcelInformeAnualPresInc.php
modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord(),
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
vistas/mensajes/vMensajeCabSalirNavInc.php, 
modelos/modeloEmail.php: emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.			

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error: para formar el 
buffer de salida a excel utiliza "header()" y no puede haber ningúna salida 
delante.
-------------------------------------------------------------------------------------------------*/
function cExportarExcelInformeAnualPres()
{	
	//echo "<br><br>0-1 cPresidente.php:cExportarExcelInformeAnualPres:_POST: "; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:cExportarExcelInformeAnualPres:_SESSION: "; print_r($_SESSION);
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'EXPORTAR LISTA DE SOCIOS/AS A EXCEL PARA EL INFORME ANUAL DE PRESIDENCIA';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Exportar lista de socios/as a Excel para informe anual de presidencia'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente.php:cExportarExcelInformeAnualPres(). Error: ';	
		$tituloSeccion = "Presidencia, Vice. y Secretaría";		
		
		$resExportar['codError'] = '00000';	

		//-------------------------------- Inicio navegación --------------------------
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=cMenuEstadisticasPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=cExportarExcelInformeAnualPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Exportar lista de socios/as a Excel para informe anual de presidencia";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cPresidente:cExportarExcelInformeAnualPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- Fin navegación ----------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{$datosMensaje['textoComentarios'] = "No se han exportado datos de los socios/as para informe anual de presidencia";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{
				$codAgrupacion = $_POST['exportarCampo']['CODAGRUPACION'];		
				$anioElegido = $_POST['exportarCampo']['ANIOCUOTA'];
				
				require_once './modelos/modeloPresCoord.php';		
				$resExportarSocios = mExportarExcelInformeAnualPres($codAgrupacion,$anioElegido);//Nuevo nombre independiente de Coord, //probado error OK	
		
				if ($resExportarSocios['codError'] !== '00000')	
				{ $resExportar = $resExportarSocios;				 
						// echo "<br><br>3 cPresidente.php:cExportarExcelInformeAnualPres:resExportar: "; print_r($resExportar);
				}
				else
				{ $datosMensaje['textoComentarios'] = "Se han exportado los datos de los socios/as, si no se ha abierto el archivo excel, buscalo en la carpeta de -Descargas-";				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}				
			}//else (isset($_POST['SiExportarExcel']))
		}// if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))
			
		else// !if(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
			$codAgrup = '%'; //mostrar buscar todas las agrupaciones por ser presidente 
			$valorDefecto = '%';
			
			require_once './modelos/libs/arrayParValor.php';	
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefecto);//probado error OK			
			
			//echo "<br><br>3 cPresidente.php:cExportarExcelInformeAnualPres:parValorCombo: ";print_r($parValorCombo);
			
			if ($parValorCombo['codError'] !== '00000') 
			{$resExportar = $parValorCombo;
			}	
			else//$parValorCombo['codError']=='00000'
			{
				//echo "<br><br>4 cPresidente.php:cExportarExcelInformeAnualPres:navegacion:";print_r($navegacion);	

				require_once './vistas/presidente/vExportarExcelInformeAnualPresInc.php';					
				vExportarExcelInformeAnualPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorCombo);	
				
			}//else $parValorCombo['codError']=='00000'

		}//else !if(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		
		//echo "<br><br>5 cPresidente.php:cExportarExcelInformeAnualPres:resExportar: "; print_r($resExportar);
		/*--------------- Inicio tratamiento errores --------------------------------*/
		if ($resExportar['codError'] !== '00000')
		{   
				if ($resExportar['codError'] == '80001' && $resExportar['numFilas'] === 0)//no hay filas que cumplan la condición
				{ 
						$datosMensaje['textoComentarios'] = "No se han exportado datos de los socios/as para informe anual de presidencia,
																																											porque no se han encontrado datos que cumplan la condición de selección. Revisa la elección que has realizado";	
				}
				
				//$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resExportar['codError'].": ".$resExportar['errorMensaje']);	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
		}
		/*--------------- Fin tratamiento errores -----------------------------------*/
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin cExportarExcelInformeAnualPres --------------------------------*/

/*------------------- Inicio cExportarExcelEstadisticasAltasBajasAgrupPres ------------------------
Exporta a Excel informes estadísticos por "agrupaciones" y "años" a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir agrupación (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

Realiza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, AGRUPACIONTERRITORIAL. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente

LLAMADA: cPresidente.php:cMenuEstadisticasPres(), mediante formulario vMenuEstadisticasPresInc.php()

LLAMA: modeloPresCood.php:mExportarExcelEstadisticasAltasBajasAgrupPres(), 
vistas/Presidente/vExportarExcelEstadisticasAltasBajasAgrupPresInc.php							
modeloUsuarios.php:parValoresAgrupaPresCoord(),
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: PHP 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
para formar el buffer de salida a excel utiliza "header()" y no puede 
haber ningúna salida delante.
											
--------------------------------------------------------------------------------------------------*/
function cExportarExcelEstadisticasAltasBajasAgrupPres()
{
	//echo "<br><br>0-1 cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres:_POST:" ; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres:_SESSION: "; print_r($_SESSION);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'EXPORTAR ESTADÍSTICAS A EXCEL DE LAS AGRUPACIONES POR AÑOS';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la a Excel estadísticas por agrupaciones: Totales, Altas y Bajas'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres(). Error: ';	
		$tituloSeccion = "Presidencia, Vice. y Secretaría";	
		
		$resExportar['codError'] = '00000';		

		//-------------------------------- inicio navegación --------------------------
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=cMenuEstadisticasPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=cExportarExcelEstadisticasAltasBajasPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Exportar estadísticas a Excel por agrupaciones: Totales, Altas y Bajas";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cPresidente:cExportarExcelEstadisticasAltasBajasAgrupPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- fin navegación ----------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{
				$datosMensaje['textoComentarios'] = "No se han exportado datos de las estadísticas";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{
				$codAgrupacion = $_POST['exportarCampo']['CODAGRUPACION'];		
				$anioInferior  = $_POST['exportarCampo']['ANIOCUOTA_Inferior'];
				$anioSuperior  = $_POST['exportarCampo']['ANIOCUOTA_Superior'];
				
				require_once './modelos/modeloPresCoord.php';		
				$resExportarSocios = mExportarExcelEstadisticasAltasBajasAgrupPres($codAgrupacion,$anioInferior,$anioSuperior);				
				
				//$resExportar['arrMensaje']['textoCabecera'] = "Exportar estadísticas por agrupaciones a Excel";
		
				if ($resExportarSocios['codError'] !== '00000')	
				{ $resExportar = $resExportarSocios;				 
							//echo "<br><br>3 cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres: "; print_r($resExportar);	
				}
				else
				{ $datosMensaje['textoComentarios'] = "Se han exportado los datos de las estadísticas, si no se ha abierto el archivo excel, buscalo en la carpeta de -Descargas-";			
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
				}				
			}	
		}// if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		else //! if(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{
		
			$codAgrup = '%'; //mostrar buscar todas las agrupaciones por ser presidente 
			$valorDefecto = '%';
			
			require_once './modelos/libs/arrayParValor.php';	
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefecto);	
			
			//echo "<br><br>4 cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres:parValorCombo: ";print_r($parValorCombo);
			
			if ($parValorCombo['codError'] !== '00000') //$resInsertar = $parValorCombo;
			{$resExportar = $parValorCombo;
			}	
			else//$parValorCombo['codError']=='00000'
			{
				//echo "<br><br>4 cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres:resDatosNavegacion['navegacion': ";print_r($resDatosNavegacion['navegacion']);	
				
				require_once './vistas/presidente/vExportarExcelEstadisticasAltasBajasAgrupPresInc.php';					
				vExportarExcelEstadisticasAltasBajasAgrupPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorCombo);
				
			}//else $parValorCombo['codError']=='00000'

		}//else ! if(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		
		//----------- tratamiento errores ---------------------------------	
		if ($resExportar['codError'] !== '00000')
		{ //$resExportar['arrMensaje']['textoCabecera'] ="No se han exportado los datos de las estadísticas";				
				
				if ($resExportar['codError'] == '80001')//no hay filas que cumplan la condición elegida
				{ $datosMensaje['textoComentarios'] = "No se han exportado datos de las estadísticas,	porque no se han encontrado datos que cumplan la condición.
																																											Revisa la elección que has realizado";	
				}
				
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resExportar['codError'].": ".$resExportar['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				
		}
	}//	else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
//--------------------------- Fin cExportarExcelEstadisticasAltasBajasAgrupPres ------------------*/


/*------------------- Inicio cExportarExcelEstadisticasAltasBajasProvPres --------------------------
Exporta a Excel informes estadísticos por "provincias" y "años" a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir agrupación (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

Realiza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, PROVINCIA. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente. 

LLAMADA: cPresidente.php:cMenuEstadisticasPres(), mediante formulario vMenuEstadisticasPresInc.php()

LLAMA: modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres(), 
       vistas/Presidente/vExportarExcelEstadisticasAltasBajasProvPresInc.php							

OBSERVACIONES: PHP 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
para formar el buffer de salida a excel utiliza "header()" y no puede 
haber ningúna salida delante.						
									
--------------------------------------------------------------------------------------------------*/
function cExportarExcelEstadisticasAltasBajasProvPres()
{
	//echo "<br><br>0-1 cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres:_POST: "; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres:_SESSION: "; print_r($_SESSION);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$resExportar['codError'] = '00000';		
		$tituloSeccion = "Presidencia, vice. y secretaría";

		//-------------------------------- inicio navegación -----------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=cMenuEstadisticasPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=cExportarExcelEstadisticasAltasBajasPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Exportar estadísticas a Excel por provincias: Totales, Altas y Bajas";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cPresidente:cExportarExcelEstadisticasAltasBajasProvPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- fin navegación -------------------------------------------------	
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{$salirSinExportar['arrMensaje']['textoCabecera'] = "Exportar estadísticas por por provincias Totales, Altas y Bajas a Excel";		
				$salirSinExportar['arrMensaje']['textoComentarios'] = "No se han exportado datos de las estadísticas";				
				vMensajeCabSalirNavInc($tituloSeccion,$salirSinExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{		
				$codProvincia  = $_POST['exportarCampo']['CODPROV'];		
				$anioInferior  = $_POST['exportarCampo']['ANIOCUOTA_Inferior'];
				$anioSuperior  = $_POST['exportarCampo']['ANIOCUOTA_Superior'];
				
				require_once './modelos/modeloPresCoord.php';		
				
				$resExportarSocios = mExportarExcelEstadisticasAltasBajasProvPres($codProvincia,$anioInferior,$anioSuperior);	
					
				$resExportar['arrMensaje']['textoCabecera'] = "Exportar estadísticas de provincias a Excel";
		
				if ($resExportarSocios['codError']!=='00000')	
				{ $resExportar = $resExportarSocios;				 
						//echo "<br><br>3 cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres:"; print_r($resExportar);	
				}
				else
				{ $resExportar['arrMensaje']['textoComentarios'] = "Se han exportado los datos de las estadísticas";				
						vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
				}				
			}	
		}
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
			require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
			
			if ($conexionUsuariosDB['codError']!=='00000')	
			{ $resExportar=$conexionUsuariosDB;
			}     
			else //$conexionUsuariosDB['codError']=='00000'
			{			
				//require_once './modelos/modeloUsuarios.php';
				require_once './modelos/libs/arrayParValor.php';	
				$codProv = '%'; //buscar todas por ser presidente
				$valorDefecto ='%';
				$parValorCombo = parValoresProvinciaPresCoord($codProv,$valorDefecto);
			
				//echo "<br><br>4-1 cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres:parValorCombo:";print_r($parValorCombo);
				if ($parValorCombo['codError']!=='00000') //$resInsertar = $parValorCombo;
				{$resExportar=$parValorCombo;
				}	
				else//$parValorCombo['codError']=='00000'
				{
					//echo "<br><br>4-2 cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres:resDatosNavegacion['navegacion':";print_r($resDatosNavegacion['navegacion']);	
					
					require_once './vistas/presidente/vExportarExcelEstadisticasAltasBajasProvPresInc.php';					
					vExportarExcelEstadisticasAltasBajasProvPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorCombo);										
				}//$parValorCombo['codError']=='00000'
			}//$conexionUsuariosDB['codError']=='00000'
		}
		//----------- tratamiento errores ---------------------------------	
		if ($resExportar['codError'] !=='00000')
		{ $resExportar['arrMensaje']['textoCabecera'] ="Se han exportado los datos de las estadísticas";				
				if ($resExportar['codError'] =='80001')//no hay filas
				{$resExportar['arrMensaje']['textoComentarios'] = $resExportar['errorMensaje'];
				}
				else
				{ $resExportar['arrMensaje']['textoComentarios']=
					"Error del sistema al exporta los datos de las estadísticas por provincias, vuelva a intentarlo pasado un tiempo ";	
				}
				vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				$resEmailErrorWMaster=emailErrorWMaster($resExportar['codError'].": ".$resExportar['errorMensaje']);
		}
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
//--------------------------- Fin cExportarExcelEstadisticasAltasBajasProvPres -------------------*/

/*------------------- Inicio cExportarExcelEstadisticasAltasBajasCCAAPres --------------------------
Exporta a Excel informes estadísticos por ""CCAA" (comunidades autónomas)" y "años" a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir agrupación (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

Reliza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, PROVINCIA, CCAA. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente.

LLAMADA: cPresidente.php:cMenuEstadisticasPres(), mediante formulario vMenuEstadisticasPresInc.php()

LLAMA: modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres(), 
       vistas/Presidente/vExportarExcelEstadisticasAltasBajasCCAAPresInc.php
							

OBSERVACIONES: PHP 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
para formar el buffer de salida a excel utiliza "header()" y no puede 
haber ningúna salida delante.					
								
--------------------------------------------------------------------------------------------------*/
function cExportarExcelEstadisticasAltasBajasCCAAPres()
{
	//echo "<br><br>0-1 cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres:_POST:"; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres:_SESSION:"; print_r($_SESSION);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$resExportar['codError'] = '00000';		
		$tituloSeccion = "Presidencia, vice. y secretaría";

		//-------------------------------- inicio navegación -----------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=cMenuEstadisticasPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=cExportarExcelEstadisticasAltasBajasPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Exportar estadísticas a Excel por agrupaciones: Totales, Altas y Bajas";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cPresidente:cExportarExcelEstadisticasAltasBajasCCAAPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- fin navegación -------------------------------------------------	
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{$salirSinExportar['arrMensaje']['textoCabecera'] = "Exportar estadísticas por por CCAA (comunidades autónomas) Totales, Altas y Bajas a Excel";		
				$salirSinExportar['arrMensaje']['textoComentarios'] = "No se han exportado datos de las estadísticas";				
				vMensajeCabSalirNavInc($tituloSeccion,$salirSinExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{			
				$codCCAA  = $_POST['exportarCampo']['CCAA'];		
				$anioInferior  = $_POST['exportarCampo']['ANIOCUOTA_Inferior'];
				$anioSuperior  = $_POST['exportarCampo']['ANIOCUOTA_Superior'];
				
				require_once './modelos/modeloPresCoord.php';		
				
				$resExportarSocios = mExportarExcelEstadisticasAltasBajasCCAAPres($codCCAA,$anioInferior,$anioSuperior);	
					
				$resExportar['arrMensaje']['textoCabecera'] = "Exportar estadísticas por por CCAA (comunidades autónomas) Totales, Altas y Bajas a Excel";		
		
				if ($resExportarSocios['codError']!=='00000')	
				{ $resExportar = $resExportarSocios;				 
						//echo "<br><br>3 cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres:"; print_r($resExportar);	
				}
				else
				{ $resExportar['arrMensaje']['textoComentarios'] = "Se han exportado los datos de las estadísticas";				
						vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
				}				
			}	
		}
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
			require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
			
			if ($conexionUsuariosDB['codError']!=='00000')	
			{ $resExportar=$conexionUsuariosDB;
			}     
			else //$conexionUsuariosDB['codError']=='00000'
			{			
				//require_once './modelos/modeloUsuarios.php';
				require_once './modelos/libs/arrayParValor.php';	
				$codCCAA = '%'; //buscar todas por ser presidente
				$valorDefecto ='%';

				$parValorCombo = parValoresCCAAPresCoord($codCCAA,$valorDefecto);
			
				//echo "<br><br>4-1 cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres:parValorCombo:";print_r($parValorCombo);
				if ($parValorCombo['codError']!=='00000') //$resInsertar = $parValorCombo;
				{$resExportar=$parValorCombo;
				}	
				else//$parValorCombo['codError']=='00000'
				{
					//echo "<br><br>4-2 cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres:resDatosNavegacion['navegacion':";print_r($resDatosNavegacion['navegacion']);	
					
					require_once './vistas/presidente/vExportarExcelEstadisticasAltasBajasCCAAPresInc.php';					
					vExportarExcelEstadisticasAltasBajasCCAAPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorCombo);										
				}//$parValorCombo['codError']=='00000'
			}//$conexionUsuariosDB['codError']=='00000'
		}
		//----------- tratamiento errores ---------------------------------	
		if ($resExportar['codError'] !=='00000')
		{ $resExportar['arrMensaje']['textoCabecera'] ="Se han exportado los datos de las estadísticas";				
				if ($resExportar['codError'] =='80001')//no hay filas
				{$resExportar['arrMensaje']['textoComentarios'] = $resExportar['errorMensaje'];
				}
				else
				{ $resExportar['arrMensaje']['textoComentarios']=
					"Error del sistema al exporta los datos de las estadísticas por CCAA, vuelva a intentarlo pasado un tiempo ";	
				}
				vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				$resEmailErrorWMaster=emailErrorWMaster($resExportar['codError'].": ".$resExportar['errorMensaje']);
		}
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
//--------------------------- Fin cExportarExcelEstadisticasAltasBajasCCAAPres -------------------*/


/*==================================== FIN ESTADISTICAS ==========================================*/


/*== INICIO GESTIÓN ROLES ASIGNAR-MODIFICAR-ELIMINAR COORDINACIÓN,PRESIDENCIA,TESORERÍA Y LISTAS =*/
/*================================================================================================*/

/*------------------- Inicio menuPermisosRolesPres()  ---------------------------------------------
Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Presidencia" y que permite llamar a las páginas: 

- ASIGNAR-MODIFICAR-ANULAR ROL DE COORDINADOR/A A AGRUPACIONES TERRITORIALES 
- ASIGNAR-ANULAR ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría) 
- ASIGNAR-ANULAR ROL DE TESORERÍA

También incluye mostra las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Presidencia"
													
LLAMADA: desde menú izdo de rol presidencia	"Permisos de Roles"					

LLAMA: vistas/presidente/vMenuPermisosRolesPres.php

Desde ese menú con "href" se podrá llamar a: 
cPresidente.php:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreriaRolBuscar()

OBSERVACIONES: no necesita cambio PDO. PHP 7.3.21 
--------------------------------------------------------------------------------------------------*/
function menuPermisosRolesPres()
{
	//echo "<br><br>0-1 cPresidente.php:menuPermisosRolesPres:_POST: "; print_r($_POST);	
	//echo "<br><br>0-2 cPresidente.php:menuPermisosRolesPres:_SESSION: "; print_r($_SESSION);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		$tituloSeccion = "Presidencia, vice. y secretaría";			

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=menuPermisosRolesPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Permisos de Roles: Coordinación, Presidencia, Tesorería";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		require_once './vistas/presidente/vMenuPermisosRolesPresInc.php'; 	
		vMenuPermisosRolesPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
 }// else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
//--------------------------- Fin menuPermisosRolesPres() -----------------------------------------

/*== INICIO GESTIÓN DE ASIGNAR-MODIFICAR-ELIMINAR- AREA DE COORDINACIÓN A COORDINADORES Y LISTA ==*/

/*-------------------------- Inicio asignarCoordinacionAreaBuscar ----------------------------------
Es el incio de la gestión de permisos de coordinación de área territorial.

Se muesta un formulario que permite:

- Con el botón "LISTA DE COORDINADORES/AS" mostrará una tabla con los datos de todos los coordinadores

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para  ver si ya tiene rol de cooordinador en tabla "USUARIOTIENEROL" (CODROL = 6) y en la 
tabla "COORDINAAREAGESTIONAGRUP"
Según la situación se podrá después se podrá asignar/modificar/eliminar un área de coordinación

.Si no hay una asignación de área para ese socio se envía a "vAsignarCoordAreaSocioInc.php", 
para asignar un área de coordinación.	

.Si el socio sí tiene asignada	algún área de coordinación	lo envía a "vBorrarCambiarCoordAreaInc.php" 
(para borrar asignación), o vAsignarCoordAreaInc.php (para cambiar la asignación de área)
														
LLAMADA: cPresidente:menuPermisosRolesPres.php desde vMenuPermisosRolesPresInc.php 

LLAMA: modeloPresCoord.php:buscarMiembroApesEmail(),buscarAreaGestionCoordRol(),
modelos/libs/validarCampos.php:validarEmail()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormBuscarCoordApe()
vistas/presidente/vAsignarCoordAreaBuscarInc.php,vBorrarCambiarCoordAreaInc(),
vAsignarCoordAreaInc() 
vistas/mensajes/vMensajeCabSalirNavInc.php 	 		
modelos/modeloEmail.php:emailErrorWMaster()
modelos/libs/arrayParValor.php:parValorAreaGestionCoord()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 
-------------------------------------------------------------------------------------------------*/								
function asignarCoordinacionAreaBuscar()
{
	//echo "<br><br>0-1 cPresidente:asignarCoordinacionAreaBuscar:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:asignarCoordinacionAreaBuscar:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/presidente/vAsignarCoordAreaBuscarInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR-MODIFICAR-ANULAR ROL DE COORDINADOR/A A AGRUPACIONES TERRITORIALES";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/modificar/anular el rol de coordinación de área de agrupaciones territoriales. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:asignarCoordinacionAreaBuscar(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cPresidente&accion=asignarCoordinacionAreaBuscar";	
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Permisos coordinadores/as";	
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------

		if (!isset($_POST) || empty($_POST)) 
		{ $datSocioAsignar ='';
				vAsignarCoordAreaBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
		}
		elseif (isset($_POST['NoAsignar']) ||  isset($_POST['Cancelar']))
		{ 
				$datosMensaje['textoComentarios'] = "Ha salido sin modificar el estado de coordinación del socio/a"; 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
		elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail']) /*|| isset($_POST['NoAsignar'])*/) 
		{		
			if (isset($_POST['BuscarEmail']))
			{
				require_once  './modelos/libs/validarCampos.php';	
				$resulValidar['datosFormSocio']['EMAIL'] = validarEmail($_POST['datosFormSocio']['EMAIL'],"");
				
				//echo "<br><br>2-1 cPresidente:asignarCoordinacionAreaBuscar:resulValidar: "; print_r($resulValidar); 
					
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
				
				//echo "<br><br>2-3 cPresidente:asignarCoordinacionAreaBuscar:resulValidar: "; print_r($resulValidar); 
			}// (isset($_POST['BuscarApes']))
			
			if ($resulValidar['datosFormSocio']['codError'] !=='00000')	
			{
				vAsignarCoordAreaBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
			} 	
			else //$resulValidar['datosFormSocio']['codError']=='00000'
			{					
				$datSocio = buscarMiembroApesEmail($resulValidar['datosFormSocio']);//en modeloPresCoord.php
				
				//echo "<br><br>3-1 cPresidente:asignarCoordinacionAreaBuscar:datSocio: "; print_r($datSocio); 
									
				if ($datSocio['codError'] !== '00000')
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarMiembroApesEmail(): '.$datSocio['codError'].": ".$datSocio['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}   
				elseif ($datSocio['numFilas'] === 0)
				{
					//echo "<br><br>3-2 cPresidente:asignarCoordinacionAreaBuscar:datSocio: "; print_r($datSocio);	
					$resulValidar['datosFormSocio']['errorMensaje'] = 'No se ha encontrado ningún socio/a con esos datos';		
								
					vAsignarCoordAreaBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				elseif ($datSocio['numFilas'] >1 )
				{//echo "<br><br>3-3 cPresidente:asignarCoordinacionAreaBuscar:datSocio: "; print_r($datSocio);
					$resulValidar['datosFormSocio']['errorMensaje'] ='Hay mas de un socio/a que tienen esos apellidos, buscar por los dos apellidos y nombre o mejor por email';				

					vAsignarCoordAreaBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				else //$datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
				{
					$datSocioAsignar['datosFormSocio'] = $datSocio['resultadoFilas']['datosFormSocio']; 
											
					$datSocioAreaGestion = buscarAreaGestionCoordRol($datSocioAsignar['datosFormSocio']['CODUSER']['valorCampo']);//en modeloPresCoord.php';
				
					//echo "<br><br>4-1 cPresidente:asignarCoordinacionAreaBuscar:datSocioAreaGestion: "; print_r($datSocioAreaGestion);
					
					if ($datSocioAreaGestion['codError'] !== '00000')
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioAreaGestion['codError'].": ".$datSocioAreaGestion['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//$datSocioAreaGestion['codError']=='00000
					{
						if ($datSocioAreaGestion['resultadoFilas']['esCoordinador'] == "SI")
						{		
							$datSocioAsignar['datosFormSocio']['NOMBREAREAGESTION'] = $datSocioAreaGestion['resultadoFilas']['NOMBREAREAGESTION'];
							$datSocioAsignar['datosFormSocio']['CODAREAGESTIONAGRUP'] = $datSocioAreaGestion['resultadoFilas']['CODAREAGESTIONAGRUP'];						
							$datSocioAsignar['datosFormSocio']['OBSERVACIONES'] = $datSocioAreaGestion['resultadoFilas']['OBSERVACIONES'];
						
							require_once './vistas/presidente/vBorrarCambiarCoordAreaInc.php'; 
							vBorrarCambiarCoordAreaInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
							
						}//if ($datSocioAreaGestion['resultadoFilas']['esCoordinador']=="SI")
						else //$datSocioAreaGestion['resultadoFilas']['esCoordinador']!=="SI") --> $esCoordinador=="NO"
						{
							//echo "<br><br>5-1 cPresidente:asignarCoordinacionAreaBuscar:datSocioAsignar: ";print_r($datSocioAsignar);											
							
							$parValorComboAreaGestion = parValorAreaGestionCoord('Elegir');
							//echo "<br><br>5-2 cPresidente:asignarCoordinacionAreaBuscar:parValorComboAreaGestion: ";print_r($parValorComboAreaGestion);
							
							require_once './vistas/presidente/vAsignarCoordAreaInc.php';
							vAsignarCoordAreaInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion,$parValorComboAreaGestion);
							
						}//else $datSocioAreaGestion['resultadoFilas']['esCoordinador']!=="SI") --> $esCoordinador=="NO"
					}//else $datSocioAreaGestion['codError']=='00000
				}//else $datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
			}//else $resulValidar['datosFormSocio']['codError']=='00000' 
		}//elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarCoordinacionAreaBuscar ------------------------------*/

/*------------------------------- Inicio mostrarListaCoordinadores() -------------------------------
Se forma y muestra una tabla con la lista actual de socios con el rol de coordinación (CODROL =6) 
y datos relacionados

Para la búsqueda utiliza las siguientes tablas:
MIEMBRO,USUARIOTIENEROL,COORDINAAREAGESTIONAGRUP,AREAGESTION, AREAGESTIONAGRUPACIONESCOORD,AGRUPACIONTERRITORIAL 

LLAMADA: cPresidente&accion:asignarCoordinacionAreaBuscar() que incluye el formulario
vistas/presidentevAsignarCoordAreaBuscarInc.php que contiene el botón "LISTA DE COORDINADORES/AS" 

LLAMA: modelos/modeloPresCoord.php:buscarDatosCoordinadores()
controladores/libs/cNavegaHistoria.php':cNavegaHistoria()
vistas/presidente/vMostrarListaCoordinadoresInc.php:vMostrarListaCoordinadoresInc()
vistas/mensajes/vMensajeCabSalirNavInc.php';
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
	
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 	
	--------------------------------------------------------------------------------------------------*/	
function mostrarListaCoordinadores()
{
	//echo "<br><br>0-1 cPresidente.php:mostrarListaCoordinadores:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cPresidente.php:mostrarListaCoordinadores_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "LISTA DE COORDINADORES/AS";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido mostrar la lista de coordinadores/as de área de agrupaciones territoriales. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:mostrarListaCoordinadores(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarCoordinacionAreaBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cPresidente&accion=mostrarListaCoordinadores";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar lista de coordinadores/as";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cPresidente.php:mostrarListaCoordinadores_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------	

		//$codAgrup ="%";	
		require_once './modelos/modeloPresCoord.php';
		
		$resDatosCoordinadores =	buscarDatosCoordinadores();//probado error ok
		
		//echo "<br><br>2 cPresidente.php:mostrarListaCoordinadores:resDatosCoordinadores: ";print_r($resDatosCoordinadores);
		
		if ($resDatosCoordinadores['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosCoordinadores['codError'].": ".$resDatosCoordinadores['errorMensaje']);	
			//$resDatosCoordinadores['arrMensaje']['textoComentarios']="Error del sistema al buscar la lista de coordinares/as, vuelva a intentarlo pasado un tiempo ";			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
			
			//echo "<br><br>3 cPresidente.php:mostrarListaCoordinadores:resDatosCoordinadores: ";print_r($resDatosCoordinadores);		
			
		}      
		else //$resDatosCoordinadores['codError']=='00000'
		{ $resDatosCoordinadores['navegacion'] = $navegacion;
				
				require_once './vistas/presidente/vMostrarListaCoordinadoresInc.php';
				vMostrarListaCoordinadoresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosCoordinadores,$navegacion);
		}
	}//	else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')  		
}
/*--------------------------- Fin mostrarListaCoordinadores() ------------------------------------*/

/*----------------------- Inicio asignarCoordinacionArea ------------------------------------------
Asigna los permisos de coordinación de un área de gestión a un coordinador, para ello con la 
función "asignarCoordinadorArea()" inserta una fila en "COORDINAAREAGESTIONAGRUP" 
y otra en "USUARIOTIENEROL" con CODROL= 6

Envía email a socio que se hace coordinador comunicándole que se ha asignado el rol de Coordinación, 
que incluye archivos con manuales y documento protección de datos para firmar.
																										
LLAMADA: vistas/presidente/vAsignarCoordAreaInc.php 
y a su vez desde cPresidente.php:asignarCoordinacionAreaBuscar()
		
LLAMA: modeloPresCoord.php:asignarCoordinadorArea(),
modeloEmail.php:emailAsignadoRol(),emailErrorWMaster()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormAltaCoordArea()
vistas/presidente/vAsignarCoordAreaInc.php
vistas/mensajes/vMensajeCabSalirNavInc.phP
modelos/libs/arrayParValor.php:parValorAreaGestionCoord()					
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.							
-------------------------------------------------------------------------------------------------*/								
function asignarCoordinacionArea()
{ 
	//echo "<br><br>0-1 cPresidente:asignarCoordinacionArea:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:asignarCoordinacionArea:_POST: ";print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/presidente/vAsignarCoordAreaInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/libs/arrayParValor.php';		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR UN ÁREA DE COORDINACIÓN TERRITORIAL A UN SOCIO/A COORDINADOR/A";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar el rol de coordinación de área de agrupaciones territoriales. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:asignarCoordinacionArea(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarCoordinacionAreaBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=asignarCoordinacionArea";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Asignar rol de coordinador/a territorial";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//echo "<br><br>1 cPresidente:asignarCoordinacionArea:navegacion: ";print_r($navegacion);	
		//------------ fin navegacion -------------------------------------------------

		if ( !isset($_POST)) //No entrará nunca pero lo dejo por si acaso
		{ //echo "<br><br>0-3 cPresidente:asignarCoordinacionArea:_POST: ";print_r($_POST);// no entrará nunca
		}	
		elseif (isset($_POST['SiAsignar']) || isset($_POST['NoAsignar'])) 
		{		
			if (isset($_POST['NoAsignar']))
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin asignar área de coordinación al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiAsignar']))
			{			 
				require_once './modelos/libs/validarCamposSocioPorGestor.php';		 
				$resValidarCoordArea = validarCamposFormAltaCoordArea($_POST);			
				//echo "<br><br>2-1 cPresidente:asignarCoordinacionArea:resValidarCoordArea: ";print_r($resValidarCoordArea); 
					
				if ($resValidarCoordArea['datosFormSocio']['CODAREAGESTIONAGRUP']['codError'] !== '00000')
				{ 
					$parValorAreaGestion = parValorAreaGestionCoord($resValidarCoordArea['datosFormSocio']['CODAREAGESTIONAGRUP']['valorCampo']);//en modelos/libs/arrayParValor.php';
						
					//echo "<br><br>2-2 cPresidente:asignarCoordinacionArea:parValorAreaGestion: ";print_r($parValorAreaGestion); 

					if ($parValorAreaGestion['codError'] !== '00000')
					{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorAreaGestion['codError'].": ".$parValorAreaGestion['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
					}
					else//$parValorAreaGestion['codError'] !== '00000'
					{
						foreach ($_POST['datosFormSocio'] as $campo => $valorCampo) 
						{$datSocio['datosFormSocio'][$campo]['valorCampo'] = $valorCampo;//Se necesita [valorCampo] para formulario
						}
						$datSocio['datosFormSocio']['CODAREAGESTIONAGRUP'] = $resValidarCoordArea['datosFormSocio']['CODAREAGESTIONAGRUP'];
						//echo "<br><br>2-3 cPresidente:asignarCoordinacionArea:datSocio: ";print_r($datSocio); 

						vAsignarCoordAreaInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocio,$navegacion,$parValorAreaGestion);
					}
				}
				else//$resValidarCoordArea['datosFormSocio']['CODAREAGESTIONAGRUP']['codError']=='00000')
				{			
					$resAsignarCoordArea = asignarCoordinadorArea($_POST['datosFormSocio']);//en modeloPresCoord.php	probado error OK
					
					//echo "<br><br>4 cPresidente:asignarCoordinacionArea:resAsignarCoordArea: ";print_r($resAsignarCoordArea); 
								
					if ($resAsignarCoordArea['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloPresCoord.php:asignarCoordinadorArea()".$resAsignarCoordArea['codError'].": ".$resAsignarCoordArea['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
					}
					else //($resAsignarCoordArea['codError']=="00000")
					{
						//$datSocioEmailAsignCoord = $_POST['datosFormSocio'];
						//$datSocioEmailAsignCoord['NOMAREAGESTION'] = $resAsignarCoordArea['NOMAREAGESTION'];//Para enviar en el email						

						$nomRol = "Coordinación del área territorial -". $resAsignarCoordArea['NOMAREAGESTION']."-";		
						$dirManualRol = "../documentos/COORDINACION/EL_MANUAL_GESTOR_Coordinador.pdf";
						$nomManualRol = "MANUAL_GESTOR_Coordinacdor.pdf";												
						
						//$reEmailComunicarAsigna = emailComunicarAsignacionCoord($datSocioEmailAsignCoord);//en modeloEmail.php	anterior
						$reEmailComunicarAsigna = emailAsignadoRol($_POST['datosFormSocio'],$nomRol,	$dirManualRol,	$nomManualRol);//en modeloEmail.php
						
						//echo"<br><br>5 cPresidente:asignarCoordinacionArea:reEmailComunicarAsigna: ";print_r($reEmailComunicarAsigna);
										
						if ($reEmailComunicarAsigna['codError'] !== '00000')
						{
							$reEmailComunicarAsigna['textoComentarios'] = $resAsignarCoordArea['arrMensaje']['textoComentarios'].'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático
							al socio/a.  Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la asignación de los permisos de coordinación de grupo territorial';	
							$datosMensaje['textoComentarios'] = $reEmailComunicarAsigna['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarAsigna['codError'].": ".$reEmailComunicarAsigna['errorMensaje'].$reEmailComunicarAsigna['textoComentarios']);	
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $resAsignarCoordArea['arrMensaje']['textoComentarios'];
						}		
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}			
				}//else $resValidarCoordArea['datosFormSocio']['CODAREAGESTIONAGRUP']['codError']=='00000')   
			}//else isset($_POST['SiAsignar'])  
		}//(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarCoordinacionArea ------------------------------------*/

/*------------------------------- Inicio eliminarCoordinacionArea ----------------------------------
Elimina los permisos de coordinación de área de gestión territorial a un coordinador que se ha buscado
desde cPresidente.php:asignarCoordinacionAreaBuscar().
Elimina la coordinación de un área en tabla COORDINAAREAGESTIONAGRUP, y el Rol=6 de coordinador 
en  tabla USUARIOTIENEROL

Envía email a coordinador comunicándoselo

LLAMADA: vistas/presidente/vBorrarCambiarCoordAreaInc.php	 desde botón "Eliminar asignación"	
y a su vez desde CPresidente.php:asignarCoordinacionAreaBuscar()

LLAMA: modeloPreSCood.php:eliminarAsignCoordinadorArea(), 
       modeloEmail.php:emailEliminadaAsignacionRol(),emailErrorWMaster(
							vistas/mensajes/vMensajeCabSalirNavInc.php
							controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.
-------------------------------------------------------------------------------------------------*/
function eliminarCoordinacionArea()
{
 //echo "<br><br>0-1 cPresidente:eliminarCoordinacionArea:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:eliminarCoordinacionArea:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once "./modelos/modeloPresCoord.php";		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ELIMINAR ROL DE COORDINACION DE ÁREA DE AGRUPACIÓN TERRITORIAL";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido eliminar el rol de coordinación de área de agrupaciones territoriales. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:eliminarCoordinacionArea(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	
		
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarCoordinacionAreaBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=eliminarCoordinacionArea";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar rol de coordinador/a de grupo territorial";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//echo "<br><br>1 cPresidente:eliminarCoordinacionArea:navegacion: ";print_r($navegacion);	
		//------------ Fin navegacion -------------------------------------------------

		if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar'])) 
		{		
			if (isset($_POST['Cancelar']))//no entrará nunca pero lo dejo por si decido un tratamiento aislado
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin eliminar el rol de coordinación al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminar']))
			{	
					$reEliminarCoordArea = eliminarAsignCoordinadorArea($_POST['datosFormSocio']);//en modeloPresCoord.php	probado error ok
						
					//echo "<br><br>2 cPresidente:eliminarCoordinacionArea:reEliminarCoordArea: "; print_r($reEliminarCoordArea);			

					if ($reEliminarCoordArea['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarCoordArea['codError'].": ".$reEliminarCoordArea['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //($reEliminarCoordArea['codError'] == '00000')
					{$datosSocio = $_POST;
						//$datosSocio['NOMAREAGESTION'] = $reEliminarCoordArea['NOMAREAGESTION'];
						
      $nomRol = "Coordinación del área territorial -".$reEliminarCoordArea['NOMAREAGESTION']."-";						
							
						//$resultEnviarEmailCoord = emailEliminadaAsignacionCoord($datosSocio);//probado error ok		antes
      $resultEnviarEmailCoord = emailEliminadaAsignacionRol($datosSocio, $nomRol);//modeloEmail.php, probado error ok									
						//echo"<br><br>5 cPresidente:eliminarCoordinacionArea:resultEnviarEmailCoord: ";print_r($resultEnviarEmailCoord);
								
						if ($resultEnviarEmailCoord['codError'] !== '00000')
						{
							$resultEnviarEmailCoord['textoComentarios'] = $reEliminarCoordArea['arrMensaje']['textoComentarios'].'<br />AVISO: se ha producido un error al enviar el email de modo automático al socio/a. 
							Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la eliminaciom de los permisos de coordinación de grupo territorial';	
							$datosMensaje['textoComentarios'] = $resultEnviarEmailCoord['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmailCoord['codError'].": ".$resultEnviarEmailCoord['errorMensaje'].$resultEnviarEmailCoord['textoComentarios']);					
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $reEliminarCoordArea['arrMensaje']['textoComentarios'];
						}				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}//else $reEliminarCoordArea['codError'] == '00000'
			}//elseif (isset($_POST['SiEliminar']))		
		}//if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar']))
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')

}
/*---------------------------- Fin eliminarCoordinacionArea --------------------------------------*/

/*-------------------------- Inicio cambiarCoordinacionArea  ---------------------------------------
Cambia un área de gestión a un coordinador por otro área actualizando una fila 
en tabla "COORDINAAREAGESTIONAGRUP". 

Envía email a coordinador comunicándoselo
																										
LLAMADA: vistas/presidente/vBorrarCambiarCoordAreaInc.php: botón "Cambiar el área asignada" 
LLAMA: modeloPresCoord.php:cambiarCoordinadorArea(),
       modeloEmail.php:emailComunicarCambioAsignCoord()							
							vistas/presidente/vCambiarCoordAreaInc.php
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.										
--------------------------------------------------------------------------------------------------*/
function cambiarCoordinacionArea()
{ 
	//echo "<br><br>0-1 cPresidente:cambiarCoordinacionArea:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:cambiarCoordinacionArea:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './modelos/modeloSocios.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	

		$datosMensaje['textoCabecera'] = "CAMBIAR DE ÁREA DE COORDINACIÓN TERRITORIAl A COORDINADOR/A";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido cambiar el rol de coordinación el rol de coordinación de área de territorial de un cordinador/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:cambiarCoordinacionArea(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	
			
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarCoordinacionAreaBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cPresidente&accion=cambiarCoordinacionArea";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Cambiar el área de coordinación"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ Fin navegacion -------------------------------------------------
				
		if (isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
		{
			if (isset($_POST['NoCambiar']))
			{
					$datosMensaje['textoComentarios'] = "Has salido sin cambiar área de coordinación al coordinador/a";				
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
			}		
			elseif (isset($_POST['SiCambiar']))
			{							
					require_once './modelos/modeloPresCoord.php';
					$resCambiarCoordArea = cambiarCoordinadorArea($_POST['datosFormSocio']);//probado errores ok							
				
					//echo "<br><br>3 cPresidente:cambiarCoordinacionArea:resCambiarCoordArea: ";print_r($resCambiarCoordArea); 
					
					if ($resCambiarCoordArea['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resCambiarCoordArea['codError'].": ".$resCambiarCoordArea['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
					}
					else //($resAsignarCoordArea['codError']=="00000")
					{
						$datSocio = $_POST['datosFormSocio'];
						$datSocio['NOMAREAGESTION'] = $resCambiarCoordArea['NOMAREAGESTION'];
						
						//echo"<br><br>4-1 cPresidente:cambiarCoordinacionArea:reEmailComunicarCambio: ";print_r($datSocio);
						$reEmailComunicarCambio = emailComunicarCambioAsignCoord($datSocio);//En modeloEmail.php
			
						//echo"<br><br>4-2 cPresidente:cambiarCoordinacionArea:reEmailComunicarCambio: ";print_r($reEmailComunicarCambio);
						
						if ($reEmailComunicarCambio['codError'] !== '00000')
						{     
							$datosMensaje['textoComentarios'] =' AVISO: se ha producido un error al enviar el email de modo automático al socio/a. 
																																												Conviene que se le comunique por otros procedimientos para que tenga conocimiento del cambio de coordinación de grupo territorial';			
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarCambio['codError'].": ".$reEmailComunicarCambio['errorMensaje']);					
						}
						else
						{ $datosMensaje['textoComentarios'] = $resCambiarCoordArea['arrMensaje']['textoComentarios'];
						}
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						
					}//else ($resAsignarCoordArea['codError']=="00000")		   
			}//elseif (isset($_POST['SiCambiar'])) 
		}//if (isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
			
		else// !if(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
		{
				$datSocio['datosFormSocio'] = $_POST['datosFormSocio'];
				$datSocio['datosFormSocio']['codAreaGestionOld'] = $datSocio['datosFormSocio']['CODAREAGESTIONAGRUP'];
				//echo "<br><br>5 cPresidente:cambiarCoordinacionArea:datSocio: ";print_r($datSocio);
					
				require_once './modelos/libs/arrayParValor.php';	
				$parValorAreaGestion = parValorAreaGestionCoord($datSocio['datosFormSocio']['CODAREAGESTIONAGRUP']);

				//echo "<br><br>6 cPresidente:cambiarCoordinacionArea:parValorAreaGestion: ";print_r($parValorAreaGestion); 
				if ($parValorAreaGestion['codError'] !== '00000')
				{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorAreaGestion['codError'].": ".$parValorAreaGestion['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}
				else//$parValorAreaGestion['codError'] !== '00000'
				{	
						require_once './vistas/presidente/vCambiarCoordAreaInc.php';
						vCambiarCoordAreaInc($tituloSeccion,$datSocio,$navegacion,$parValorAreaGestion);
				}
		}//else !if(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
 }//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI') 
}
/*------------------------------- Fin cambiarCoordinacionArea ------------------------------------*/

/*===== FIN GESTIÓN DE ASIGNAR-MODIFICAR-ELIMINAR- AREA DE COORDINACIÓN A COORDINADORES Y LISTA ==*/

/*== INICIO GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL PRESIDENCIA (Presidencia, Vice. Secretaría) ==*/

/*-------------------------- Inicio asignarPresidenciaRolBuscar ------------------------------------
Es el inicio de la gestión de permisos de rol de Presidencia que lo comparten (presidente/a, vice. 
y el secretario/a)

Se muesta un formulario que permite:

- Con el botón "LISTA DE SOCIOS/AS CON ROL PRESIDENCIA" mostrará una tabla con los datos de todos 
  los socios con rol de Presidencia

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para  ver si ya tiene rol de Presidencia "3" en tabla "USUARIOTIENEROL" 
Según la situación se podrá después asignar/eliminar el rol de Presidencia al socio

.Si no hay una asignación de rol de Presidencia para ese socio se envía a "vAsignarPresidenciaRolInc.php", 
para asignar un área de coordinación.	

.Si el socio sí tiene asignado el rol de Presidencia	lo envía a "vAnularAsignacionPresiRolInc.php" 
(para anular asignación)
														
LLAMADA: cPresidente:menuPermisosRolesPres.php desde vMenuPermisosRolesPresInc.php 

LLAMA: modeloPresCoord.php:buscarMiembroApesEmail(),
modelos/libs/validarCampos.php:validarEmail()
modelos/modeloUsuarios.php:buscarUnRolUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom()
vistas/presidente/vAsignarPresiRolBuscarInc.php,vAsignarPresiRolInc.php,vAnularAsignacionPresiRolInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php 	 		
modelos/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 
-------------------------------------------------------------------------------------------------*/								
function asignarPresidenciaRolBuscar()//acaso mas adecuado function asignarEliminarPresidenciaRolBuscar()
{
	//echo "<br><br>0-1 cPresidente:asignarPresidenciaRolBuscar:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:asignarPresidenciaRolBuscar:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/presidente/vAsignarPresiRolBuscarInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR-ANULAR ROL DE PRESIDENCIA, VICE, SECRETARÍA";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Presidencia, Vice, Secretaría del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:asignarPresidenciaRolBuscar(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cPresidente&accion=asignarPresidenciaRolBuscar";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Permisos rol Presidencia, Vice, Secretaría";		
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
  //echo "<br><br>1 cPresidente:asignarPresidenciaRolBuscar:navegacion: ";print_r($navegacion);  			
		//------------ fin navegacion -----------------------------------------------	

		if (!isset($_POST) || empty($_POST)) 
		{ 
	   $datSocioAsignar ='';
				vAsignarPresiRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
		}
		elseif (isset($_POST['NoAsignar']) ||  isset($_POST['Cancelar']))
		{ 
				$datosMensaje['textoComentarios'] = "Has salido sin modificar el estado del rol de presidencia del socio/a"; 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
		elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail']) /*|| isset($_POST['NoAsignar'])*/) 
		{	
			if (isset($_POST['BuscarEmail']))
			{
				require_once  './modelos/libs/validarCampos.php';	
				$resulValidar['datosFormSocio']['EMAIL'] = validarEmail($_POST['datosFormSocio']['EMAIL'],"");
				
				//echo "<br><br>2-1 cPresidente:asignarPresidenciaRolBuscar:resulValidar: "; print_r($resulValidar); 
					
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
				
				//echo "<br><br>2-2 cPresidente:asignarPresidenciaRolBuscar:resulValidar: "; print_r($resulValidar); 
			}// (isset($_POST['BuscarApes']))
			
			if ($resulValidar['datosFormSocio']['codError'] !=='00000')	
			{
					vAsignarPresiRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
			} 	
			else //$resulValidar['datosFormSocio']['codError']=='00000'
			{					
				$datSocio = buscarMiembroApesEmail($resulValidar['datosFormSocio']);//en modeloPresCoord.php
				
				//echo "<br><br>3 cPresidente:asignarPresidenciaRolBuscar:datSocio: "; print_r($datSocio); 
									
				if ($datSocio['codError'] !== '00000')
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarMiembroApesEmail(): '.$datSocio['codError'].": ".$datSocio['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}   
				elseif ($datSocio['numFilas'] === 0)
				{					
					$resulValidar['datosFormSocio']['errorMensaje'] = 'No se ha encontrado ningún socio/a con esos datos';							

					vAsignarPresiRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				elseif ($datSocio['numFilas'] >1 )
				{
					$resulValidar['datosFormSocio']['errorMensaje'] ='Hay mas de un socio/a que tienen esos apellidos, buscar por los dos apellidos y nombre o mejor por email';				

					vAsignarPresiRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				else //$datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
				{
					$datSocioAsignar['datosFormSocio'] = $datSocio['resultadoFilas']['datosFormSocio']; 
			 	$codRol = "3"; //rol Presidencia
					
		 		require_once './modelos/modeloUsuarios.php';				

			 	$resSocioRol = buscarUnRolUsuario($datSocioAsignar['datosFormSocio']['CODUSER']['valorCampo'],$codRol);//en modeloUsuarios.php';
				
					//echo "<br><br>4-1 cPresidente:asignarPresidenciaRolBuscar:resSocioRol: "; print_r($resSocioRol);

					if ($resSocioRol['codError'] !== '00000')
 				{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSocioRol['codError'].": ".$resSocioRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//$resSocioRol['codError']=='00000
					{
						if ($resSocioRol['numFilas'] === 1)// tiene rol Presidencia
						{	
       //echo "<br><br>5-1 cPresidente:asignarPresidenciaRolBuscar:datSocioAsignar: ";print_r($datSocioAsignar);								

       require_once './vistas/presidente/vAnularAsignacionPresiRolInc.php'; 
 						vAnularAsignacionPresiRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);			
							
						}//if ($resSocioRol['numFilas'] === 1)
						else //$resSocioRol['numFilas'] === 0) --> $no tiene Rol Presidencia
						{							
       //echo "<br><br>5-2 cPresidente:asignarPresidenciaRolBuscar:datSocioAsignar: ";print_r($datSocioAsignar);					

							require_once './vistas/presidente/vAsignarPresiRolInc.php';
       vAsignarPresiRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
							
						}//else $resSocioRol['numFilas'] === 0) --> $no tiene Rol Presidencia
					}//else $resSocioRol['codError']=='00000
				}//else $datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
			}//else $resulValidar['datosFormSocio']['codError']=='00000' 
		}//elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarPresidenciaRolBuscar --------------------------------*/

/*------------------------------- Inicio mostrarListaPresidenciaRol() ------------------------------
Se forma y muestra una tabla con la lista actual de socios con el rol de Presidencia (Presidencia, 
Vice, Secretaría) con sus datos personales

Para la búsqueda utiliza las siguientes tablas: MIEMBRO,y USUARIOTIENEROL con CODROL=3

LLAMADA: cPresidente&accion:asignarPresidenciaRolBuscar() que incluye botón para el formulario
vistas/vMostrarListaPresiRolInc.php que contiene el botón "LISTA DE SOCIOS CON ROL PRESIDENCIA" 

LLAMA: modelos/modeloPresCoord.php:buscarDatosGestoresRoles()
controladores/libs/cNavegaHistoria.php':cNavegaHistoria()
vistas/presidente/vMostrarListaPresiRolInc()
vistas/mensajes/vMensajeCabSalirNavInc.php';
modeloEmail.php:emailErrorWMaster()
	
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 	
	--------------------------------------------------------------------------------------------------*/	
function mostrarListaPresidenciaRol()
{
	//echo "<br><br>0-1 cPresidente.php:mostrarListaPresidenciaRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cPresidente.php:mostrarListaPresidenciaRol:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "LISTA DE SOCIOS/AS CON ROL DE PRESIDENCIA, VICE, SECRETARÍA";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido mostrar la lista de socios/as con rol de 'Presidencia'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:mostrarListaPresidenciaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarPresidenciaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=mostrarListaPresidenciaRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar lista socios/s con rol Presidencia";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//echo "<br><br>1 cPresidente:mostrarListaPresidenciaRol:navegacion: ";print_r($navegacion);	
		//------------ fin navegacion -------------------------------------------------		

		$codRol = "3"; //Presidencia
		require_once './modelos/modeloPresCoord.php';			
		
		$resDatosGestoresRol =	buscarDatosGestoresRoles($codRol);//
		
		//echo "<br><br>2 cPresidente.php:mostrarListaPresidenciaRol:resDatosGestoresRol: ";print_r($resDatosGestoresRol);
		
		if ($resDatosGestoresRol['codError'] !== '00000')
		{
		 	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosGestoresRol['codError'].": ".$resDatosGestoresRol['errorMensaje']);	
		 	//$resDatosGestoresRol['arrMensaje']['textoComentarios']="Error del sistema al buscar la lista de rol de Presidencia, vuelva a intentarlo pasado un tiempo ";			
		 	vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
		}      
		else //$resDatosGestoresRol['codError']=='00000'
		{ 
				$resDatosGestoresRol['navegacion'] = $navegacion;
				
				require_once './vistas/presidente/vMostrarListaPresiRolInc.php';
				vMostrarListaPresiRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosGestoresRol,$navegacion);
		}		

	}//	else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')  		
}
/*--------------------------- Fin mostrarListaPresidenciaRol() -----------------------------------*/

/*----------------------- Inicio asignarPresidenciaRol --------------------------------------------
Asigna los permisos de de Presidencia que lo comparten (presidente/a, vice.y el secretario/a) le 
añade el CODROL '3', para ello inserta una fila en "USUARIOTIENEROL".

Envía email a socio comunicándole que se ha asignado el rol de Presidencia, que incluye archivos 
con manuales y documento protección de datos para firmar.
																										
LLAMADA: vistas/presidente/vAsignarPresiRolInc.php 
y a su vez desde cPresidente.php:asignarPresidenciaRolBuscar()
		
LLAMA:
modelos/modeloUsuarios.php:insertarUsuarioRol()
modeloEmail.php:emailAsignadoRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.phP
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.							
-------------------------------------------------------------------------------------------------*/								
function asignarPresidenciaRol()
{ 
	//echo "<br><br>0-1 cPresidente:asignarPresidenciaRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cPresidente:asignarPresidenciaRol:_POST: ";print_r($_POST);
 
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR ROL DE PRESIDENCIA, VICE, SECRETARÍA";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Presidencia, Vice, Secretaría. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:asignarPresidenciaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarPresidenciaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=asignarPresidenciaRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Asignar rol Presidencia, Vice, Secretaría";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
  //echo "<br><br>1 cPresidente:asignarPresidenciaRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------	
	
		if ( !isset($_POST)) //No entrará nunca pero lo dejo por si acaso
		{ //echo "<br><br>1 cPresidente:asignarPresidenciaRol:_POST: ";print_r($_POST);
		}	
		elseif (isset($_POST['SiAsignar']) || isset($_POST['NoAsignar'])) 
		{		
			if (isset($_POST['NoAsignar']))
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin asignar el rol de Presidencia al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiAsignar']))
			{			    
				//echo "<br><br>2 cPresidente:asignarPresidenciaRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);
				
				$arrValoresInser['CODUSER']['valorCampo'] = $_POST['datosFormSocio']['CODUSER'];		
				$arrValoresInser['CODROL']['valorCampo'] = "3";
				
				require_once './modelos/modeloUsuarios.php';
						
				$resAsignarRol = insertarUsuarioRol($arrValoresInser);
				//echo "<br><br>3 cPresidente:asignarPresidenciaRol:resAsignarRol: ";print_r($resAsignarRol); 
							
				if ($resAsignarRol['codError'] !== "00000")
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloUsuarios.php:asignarPresidenciaRol(): ".$resAsignarRol['codError'].": ".$resAsignarRol['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
				}
				else //$resAsignarRol['codError']=="00000"
				{
					$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
					$email = $_POST['datosFormSocio']['EMAIL'];
				
					$textoPantallaAsignacion = 'Se ha asignado el rol de Presidencia (Presidencia, Vice, Secretaría) al socio/a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
															Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la asignación.<br /><br />
															En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
															aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de coordinación en Europa Laica.<br /><br />
															Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org (o por correo postal al domicilio legal de Europa Laica)';
															
     $nomRol = "Presidencia (Presidencia, Vice, Secretaría)";		
	    $dirManualRol = "../documentos/PRESIDENCIA/EL_MANUAL_GESTOR_Presidencia.pdf";
	    $nomManualRol = "EL_MANUAL_GESTOR_Presidencia.pdf";															
					
					//$reEmailComunicarAsigna = emailAsignadaPresidenciaRol($_POST['datosFormSocio']);//en modeloEmail.php		antes
	    $reEmailComunicarAsigna = emailAsignadoRol($_POST['datosFormSocio'],$nomRol,	$dirManualRol,	$nomManualRol);//en modeloEmail.php	
				
					//echo"<br><br>4 cPresidente:asignarPresidenciaRol:reEmailComunicarAsigna: ";print_r($reEmailComunicarAsigna);
									
					if ($reEmailComunicarAsigna['codError'] !== '00000')
					{
						$reEmailComunicarAsigna['textoComentarios'] = $textoPantallaAsignacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático
						al socio/a '.$nomApe1.' Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la asignación del rol de Presidencia';	
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
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarPresidenciaRol -------------------------------------*/

/*------------------------------- Inicio eliminarAsignacionPresidenciaRol ------------------------
Elimina eliminar la asignación del rol de Presidencia (presidente/a, vice. y secretario/a) de un 
socio y que se ha buscado desde cPresidente.php:asignarPresiRolBuscar().

Elimina el Rol= 3 de Presidencia  en  tabla USUARIOTIENEROL

Envía email al socio comunicando anulación de Rol Presidencia

LLAMADA: vistas/presidente/vAnularAsignacionPresiRolInc.php	 desde botón "Eliminar asignación"	
y a su vez desde cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: 
modelos/modeloUsuarios:eliminarUsuarioTieneRol()
modeloEmail.php:emailEliminadaAsignacionRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.
-------------------------------------------------------------------------------------------------*/
function eliminarAsignacionPresidenciaRol()
{
 //echo "<br><br>0-1 cPresidente:eliminarAsignacionPresidenciaRol:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-2 cPresidente:eliminarAsignacionPresidenciaRol:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ELIMINAR ASIGNACIÓN DE ROL DE DE PRESIDENCIA, VICE, SECRETARÍA A SOCIO/A";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido eliminar la asignación del rol de Presidencia. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:eliminarAsignacionPresidenciaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarPresidenciaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=eliminarAsignacionPresidenciaRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar asignación rol Presidencia, Vice, Secretaría";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cPresidente:eliminarAsignacionPresidenciaRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------	

		if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar'])) 
		{		
			if (isset($_POST['Cancelar']))//no entrará nunca pero lo dejo por si decido un tratamiento aislado
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin eliminar la asignación del rol de Presidencia al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminar']))
			{	
			  //echo "<br><br>1 cPresidente:eliminarAsignacionPresidenciaRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);				
     				
     $codRol = "3";     
     $conexionLinkDB=NULL;			
					
     require_once './modelos/modeloUsuarios.php';	
					
					$reEliminarPresidenciaRol = eliminarUsuarioTieneRol('USUARIOTIENEROL',$_POST['datosFormSocio']['CODUSER'],$codRol,$conexionLinkDB);//en modeloUsuarios.php, error:ok, incluye $conexion; 	
					//echo "<br><br>2 cPresidente:eliminarAsignacionPresidenciaRol:reEliminarPresidenciaRol: "; print_r($reEliminarPresidenciaRol);			
     
					if ($reEliminarPresidenciaRol['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarPresidenciaRol['codError'].": ".$reEliminarPresidenciaRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //($reEliminarPresidenciaRol['codError'] == '00000')
					{
						$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
				 	$email = $_POST['datosFormSocio']['EMAIL'];

					 $textoPantallaAnulacion = 'Se ha anulado el rol de Presidencia (Presidencia, Vice, Secretaría) del socio/a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
														                  	Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la anulación del rol de Presidencia.';							

						$datosSocio = $_POST;
      $nomRol = "Presidencia (Presidencia, Vice, Secretaría)";											

						//$reEmailComunicarElimina = emailEliminadaAsignacionPresidenciaRol($datosSocio);//modeloEmail.php, probado error ok		antes
	
      $reEmailComunicarElimina = emailEliminadaAsignacionRol($datosSocio, $nomRol);//modeloEmail.php, probado error ok			
						//echo"<br><br>3 cPresidente:eliminarAsignacionPresidenciaRol:reEmailComunicarElimina: ";print_r($reEmailComunicarElimina);
								
						if ($reEmailComunicarElimina['codError'] !== '00000')
						{
							$reEmailComunicarElimina['textoComentarios'] = $textoPantallaAnulacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático al socio/a '.$nomApe1.' . 
							Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la eliminación de la asignación del rol de Presidencia al socio/a';	
							$datosMensaje['textoComentarios'] = $reEmailComunicarElimina['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarElimina['codError'].": ".$reEmailComunicarElimina['errorMensaje'].$reEmailComunicarElimina['textoComentarios']);					
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $textoPantallaAnulacion;
						}				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}//else $reEliminarPresidenciaRol['codError'] == '00000'
			}//elseif (isset($_POST['SiEliminar']))		
		}//if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar']))
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')

}
/*---------------------------- Fin eliminarAsignacionPresidenciaRol -----------------------------*/

/*==== FIN GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL PRESIDENCIA (Presidencia, Vice. Secretaría) ==*/

/*==== INICIO GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL TESORERIA =================================*/

/*-------------------------- Inicio asignarTesoreriaRolBuscar ------------------------------------
Es el inicio de la gestión de permisos de rol de Tesorería

Se muesta un formulario que permite:

- Con el botón "LISTA DE SOCIOS/AS CON ROL TESORERÍA" mostrará una tabla con los datos de todos 
  los socios con rol de Tesorería

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para  ver si ya tiene rol de Tesorería "5" en tabla "USUARIOTIENEROL" 
Según la situación se podrá después asignar/eliminar el rol de Tesoreria

.Si no hay una asignación de rol de Tesoreria para ese socio se envía a "vAsignarTesoreriaRolInc.php", 
para asignar rol Tesorería.	

.Si el socio sí tiene asignado el rol de Tesoreria	lo envía a "vAnularAsignacionTesoreriaRolInc.php" 
(para anular asignación)
														
LLAMADA: cPresidente:menuPermisosRolesPres.php desde vMenuPermisosRolesPresInc.php 

LLAMA: modeloPresCoord.php:buscarMiembroApesEmail(),
modelos/libs/validarCampos.php:validarEmail()
modelos/modeloUsuarios.php:buscarUnRolUsuario()
modelos/libs/validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom()
vistas/presidente/vAsignarTesoreriaRolBuscarInc.php,vAsignarTesoreriaRolInc.php,vAnularAsignacionTesoreriaRolInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php 	 		
modelos/modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 
-------------------------------------------------------------------------------------------------*/								
function asignarTesoreriaRolBuscar()
{
	//echo "<br><br>0-1 cPresidente:asignarTesoreriaRolBuscar:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:asignarTesoreriaRolBuscar:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/presidente/vAsignarTesoreriaRolBuscarInc.php'; 
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		require_once './modelos/libs/arrayParValor.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR-ANULAR ROL DE TESORERÍA";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Tesorería del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:asignarTesoreriaRolBuscar(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cPresidente&accion=asignarTesoreriaRolBuscar";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Permisos rol Tesorería";	
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------	

		if (!isset($_POST) || empty($_POST)) 
		{ $datSocioAsignar ='';
				vAsignarTesoreriaRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
		}
		elseif (isset($_POST['NoAsignar']) ||  isset($_POST['Cancelar']))
		{ 
				$datosMensaje['textoComentarios'] = "Has salido sin modificar el estado del rol de Tesorería del socio/a"; 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
		elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail']) /*|| isset($_POST['NoAsignar'])*/) 
		{				
			if (isset($_POST['BuscarEmail']))
			{
				require_once  './modelos/libs/validarCampos.php';	
				$resulValidar['datosFormSocio']['EMAIL'] = validarEmail($_POST['datosFormSocio']['EMAIL'],"");
				
				//echo "<br><br>2-1 cPresidente:asignarTesoreriaRolBuscar:resulValidar: "; print_r($resulValidar); 
					
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
				
				//echo "<br><br>2-2 cPresidente:asignarTesoreriaRolBuscar:resulValidar: "; print_r($resulValidar); 
			}// (isset($_POST['BuscarApes']))
			
			if ($resulValidar['datosFormSocio']['codError'] !=='00000')	
			{
					vAsignarTesoreriaRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
			} 	
			else //$resulValidar['datosFormSocio']['codError']=='00000'
			{					
				$datSocio = buscarMiembroApesEmail($resulValidar['datosFormSocio']);//en modeloPresCoord.php
				
				//echo "<br><br>3 cPresidente:asignarTesoreriaRolBuscar:datSocio: "; print_r($datSocio); 
									
				if ($datSocio['codError'] !== '00000')
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': buscarMiembroApesEmail(): '.$datSocio['codError'].": ".$datSocio['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}   
				elseif ($datSocio['numFilas'] === 0)
				{				
					$resulValidar['datosFormSocio']['errorMensaje'] = 'No se ha encontrado ningún socio/a con esos datos';							

					vAsignarTesoreriaRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				elseif ($datSocio['numFilas'] >1 )
				{
					$resulValidar['datosFormSocio']['errorMensaje'] ='Hay mas de un socio/a que tienen esos apellidos, buscar por los dos apellidos y nombre o mejor por email';				

					vAsignarTesoreriaRolBuscarInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resulValidar['datosFormSocio'],$navegacion);
				} 
				else //$datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
				{
					$datSocioAsignar['datosFormSocio'] = $datSocio['resultadoFilas']['datosFormSocio']; 
			 	$codRol = "5";//Tesorería
					
		 		require_once './modelos/modeloUsuarios.php';				

			 	$resSocioRol = buscarUnRolUsuario($datSocioAsignar['datosFormSocio']['CODUSER']['valorCampo'],$codRol);//en modeloUsuarios.php';
				
					//echo "<br><br>4-1 cPresidente:asignarTesoreriaRolBuscar:resSocioRol: "; print_r($resSocioRol);

					if ($resSocioRol['codError'] !== '00000')
 				{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resSocioRol['codError'].": ".$resSocioRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//$resSocioRol['codError']=='00000
					{
						if ($resSocioRol['numFilas'] === 1)// tiene rol Tesorería
						{						
       require_once './vistas/presidente/vAnularAsignacionTesoreriaRolInc.php'; 
 						vAnularAsignacionTesoreriaRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);							
							
						}//if ($resSocioRol['numFilas'] === 1)
						else //$resSocioRol['numFilas'] === 0) --> $no tiene Rol Tesorería
						{		
							require_once './vistas/presidente/vAsignarTesoreriaRolInc.php';
       vAsignarTesoreriaRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datSocioAsignar,$navegacion);
							
						}//else $resSocioRol['numFilas'] === 0) --> $no tiene Rol Tesorería
					}//else $resSocioRol['codError']=='00000
				}//else $datSocio['codError']=='00000' && $datSocio['numFilas'] == 1
			}//else $resulValidar['datosFormSocio']['codError']=='00000' 
		}//elseif (isset($_POST['BuscarApes']) || isset($_POST['BuscarEmail'])) 
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarTesoreriaRolBuscar --------------------------------*/

/*------------------------------- Inicio mostrarListaTesoreriaRol() ------------------------------
Se forma y muestra una tabla con la lista actual de socios con el rol de Tesorería
 con sus datos personales

Para la búsqueda utiliza las siguientes tablas:
MIEMBRO,USUARIOTIENEROL y CODROL = 5

LLAMADA: cPresidente&accion:asignarTesoreriaRolBuscar() que incluye botón para el formulario
vistas/vMostrarListaTesoreriaRolInc.php que contiene el botón "LISTA DE SOCIOS CON ROL TESORERIA" 

LLAMA: modelos/modeloPresCoord.php:buscarDatosGestoresRoles()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
vistas/presidente/vMostrarListaTesoreriaRolInc()
vistas/mensajes/vMensajeCabSalirNavInc.php';
modeloEmail.php:emailErrorWMaster()
	
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas. 	
	--------------------------------------------------------------------------------------------------*/	
function mostrarListaTesoreriaRol()
{
	//echo "<br><br>0-1 cPresidente.php:mostrarListaTesoreriaRol:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cPresidente.php:mostrarListaTesoreriaRol:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "LISTA DE SOCIOS/AS CON ROL DE TESORERÍA";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido mostrar la lista de socios/as con rol de 'Tesorería'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = '  cPresidente.php:mostrarListaTesoreriaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarTesoreriaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=mostrarListaTesoreriaRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar lista socios/s con rol Tesorería";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cPresidente:mostrarListaTesoreriaRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------			

		$codRol = "5"; //Tesorería
		require_once './modelos/modeloPresCoord.php';			
		
		$resDatosGestoresRol =	buscarDatosGestoresRoles($codRol);
		
		//echo "<br><br>2 cPresidente.php:mostrarListaTesoreriaRol:resDatosGestoresRol: ";print_r($resDatosGestoresRol);
		
		if ($resDatosGestoresRol['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosGestoresRol['codError'].": ".$resDatosGestoresRol['errorMensaje']);	
			//$resDatosGestoresRol['arrMensaje']['textoComentarios']="Error del sistema al buscar la lista de rol de Tesorería, vuelva a intentarlo pasado un tiempo ";			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
			
		}      
		else //$resDatosGestoresRol['codError']=='00000'
		{ 
		  $resDatosGestoresRol['navegacion'] = $navegacion;				
		
				require_once './vistas/presidente/vMostrarListaTesoreriaRolInc.php';				
				vMostrarListaTesoreriaRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosGestoresRol,$navegacion);
		}	 	

	}//	else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')  		
}
/*--------------------------- Fin mostrarListaTesoreriaRol() ------------------------------------*/

/*----------------------- Inicio asignarTesoreriaRol ----------------------------------------------
Asigna los permisos de de Tesoreria que lo comparten le añade el CODROL '5', para ello inserta una 
fila en "USUARIOTIENEROL".

Envía email a socio comunicándole que se ha asignado el rol de Tesoreria, que incluye archivos 
con manuales y documento protección de datos para firmar.
																										
LLAMADA: vistas/presidente/vAsignarTesoreriaRolInc.php 
y a su vez desde cPresidente.php:asignarTesoreriaRolBuscar()
		
LLAMA: 
modelos/modeloUsuarios.php:insertarUsuarioRol()
modeloEmail.php:emailAsignadoRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.phP
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.							
-------------------------------------------------------------------------------------------------*/								
function asignarTesoreriaRol()
{ 
	//echo "<br><br>0-1 cPresidente:asignarTesoreriaRol:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:asignarTesoreriaRol:_POST: ";print_r($_POST);
 
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ASIGNAR ROL DE TESORERÍA A SOCIO/A";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido asignar/anular el rol de Tesorería. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:asignarTesoreriaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	
		
		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarTesoreriaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=asignarTesoreriaRol";
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Asignar rol de Tesorería";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cPresidente:asignarTesoreriaRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------					

		if ( !isset($_POST)) //No entrará nunca pero lo dejo por si acaso
		{ //echo "<br><br>1 cPresidente:asignarTesoreriaRol:_POST: ";print_r($_POST);
		}	
		elseif (isset($_POST['SiAsignar']) || isset($_POST['NoAsignar'])) 
		{		
			if (isset($_POST['NoAsignar']))
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin asignar el rol de Tesorería al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiAsignar']))
			{			    
				//echo "<br><br>2 cPresidente:asignarTesoreriaRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);
				
				$arrValoresInser['CODUSER']['valorCampo'] = $_POST['datosFormSocio']['CODUSER'];		
				$arrValoresInser['CODROL']['valorCampo'] = "5";//Tesorería
				
				require_once './modelos/modeloUsuarios.php';
						
				$resAsignarRol = insertarUsuarioRol($arrValoresInser);
				//echo "<br><br>3 cPresidente:asignarTesoreriaRol:resAsignarRol: ";print_r($resAsignarRol); 
							
				if ($resAsignarRol['codError'] !== "00000")
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloUsuarios.php:asignarTesoreriaRol(): ".$resAsignarRol['codError'].": ".$resAsignarRol['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
				}
				else //$resAsignarRol['codError']=="00000"
				{
					$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
					$email = $_POST['datosFormSocio']['EMAIL'];
				
					$textoPantallaAsignacion = 'Se ha asignado el rol de Tesorería al socio/a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
															Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la asignación.<br /><br />
															En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
															aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de coordinación en Europa Laica.<br /><br />
															Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org (o por correo postal al domicilio legal de Europa Laica)';
															
    	$nomRol = "Tesorería";		
    	$dirManualRol = "../documentos/TESORERIA/EL_MANUAL_GESTOR_Tesoreria.pdf";
    	$nomManualRol = "EL_MANUAL_GESTOR_Tesoreria.pdf";	
					
				 //	$reEmailComunicarAsigna = emailAsignadaTesoreriaRol($_POST['datosFormSocio']);//en modeloEmail.php	antes
					$reEmailComunicarAsigna = emailAsignadoRol($_POST['datosFormSocio'],$nomRol,	$dirManualRol,	$nomManualRol);//en modeloEmail.php	
				
					//echo"<br><br>4 cPresidente:asignarTesoreriaRol:reEmailComunicarAsigna: ";print_r($reEmailComunicarAsigna);
									
					if ($reEmailComunicarAsigna['codError'] !== '00000')
					{
						$reEmailComunicarAsigna['textoComentarios'] = $textoPantallaAsignacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático
						al socio/a '.$nomApe1.'. Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la asignación del rol de Tesorería';	
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
	}//else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*------------------------------- Fin asignarTesoreriaRol ---------------------------------------*/

/*------------------------------- Inicio eliminarAsignacionTesoreriaRol ---------------------------
Elimina eliminar la asignación del rol de Tesorería de un socio y que se ha buscado desde 
cPresidente.php:asignarTesoreriaRolBuscar().

Elimina el Rol= 5 de Tesorería en  tabla USUARIOTIENEROL

Envía email al socio comunicando anulación de Rol Tesorería
vistas/presidente/vAnularAsignacionTeseoreriaRolInc.php';
				
LLAMADA: vistas/presidente/vAnularAsignacionTesoreriaRolInc.php	 desde botón "Eliminar asignación"	
y a su vez desde cPresidente.php:asignarTesoreriaAreaBuscar()

LLAMA:
modelos/modeloUsuarios:eliminarUsuarioTieneRol() 
modeloEmail.php:emailEliminadaAsignacionRol(),emailErrorWMaster()
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21
No es necesario PDO, lo incluyen internamente algunas de las que aquí son llamadas.
-------------------------------------------------------------------------------------------------*/
function eliminarAsignacionTesoreriaRol()
{
 //echo "<br><br>0-1 cPresidente:eliminarAsignacionTesoreriaRol:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:eliminarAsignacionTesoreriaRol:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once "./modelos/modeloPresCoord.php";		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ELIMINAR ASIGNACIÓN DE ROL DE TESORERÍA A SOCIO/A";  
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido eliminar la asignación del rol de Tesorería. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:eliminarAsignacionTesoreriaRol(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=asignarTesoreriaRolBuscar")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=eliminarAsignacionTesoreriaRol";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar asignación rol de Tesorería";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
  //echo "<br><br>1 cPresidente:eliminarAsignacionTesoreriaRol:navegacion: ";print_r($navegacion);		
		//------------ fin navegacion -------------------------------------------------						

		if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar'])) 
		{		
			if (isset($_POST['Cancelar']))//no entrará nunca pero lo dejo por si decido un tratamiento aislado
			{		 
				$datosMensaje['textoComentarios'] = "Ha salido sin eliminar la asignación del rol de Tesorería al socio/a";	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminar']))
			{	
			  //echo "<br><br>1 cPresidente:eliminarAsignacionTesoreriaRol:_POST['datosFormSocio']: ";print_r($_POST['datosFormSocio']);

     $codRol = "5";//Tesorería     
     $conexionLinkDB = NULL;		

     require_once './modelos/modeloUsuarios.php';										

					$reEliminarTesoreriaRol = eliminarUsuarioTieneRol('USUARIOTIENEROL',$_POST['datosFormSocio']['CODUSER'],$codRol,$conexionLinkDB);//en modeloUsuarios.php, error:ok, incluye $conexion; 	
					//echo "<br><br>2 cPresidente:eliminarAsignacionTesoreriaRol:reEliminarTesoreriaRol: "; print_r($reEliminarTesoreriaRol);			
     
					if ($reEliminarTesoreriaRol['codError'] !== "00000")
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarTesoreriaRol['codError'].": ".$reEliminarTesoreriaRol['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
					}
					else //($reEliminarTesoreriaRol['codError'] == '00000')
					{
						$nomApe1 = $_POST['datosFormSocio']['NOM']." ".$_POST['datosFormSocio']['APE1'];
				 	$email = $_POST['datosFormSocio']['EMAIL'];

					 $textoPantallaAnulacion = 'Se ha anulado el rol de Tesorería del socio/a <b>'.$nomApe1.'</b> en la base de datos de Europa Laica<br /><br />
														                  	Se ha enviado un correo a la dirección personal <b>'.$email.'</b> para comunicar la anulación del rol de Tesorería.';							

						$datosSocio = $_POST;						
      $nomRol = "Tesorería";	
						
						//$reEmailComunicarElimina = emailEliminadaAsignacionTesoreriaRol($datosSocio);//probado error ok antes
      $reEmailComunicarElimina = emailEliminadaAsignacionRol($datosSocio, $nomRol);//modeloEmail.php, probado error ok										
						//echo"<br><br>3 cPresidente:eliminarAsignacionTesoreriaRol:reEmailComunicarElimina: ";print_r($reEmailComunicarElimina);
								
						if ($reEmailComunicarElimina['codError'] !== '00000')
						{
							$reEmailComunicarElimina['textoComentarios'] = $textoPantallaAnulacion.'<br /><br />AVISO: se ha producido un error al enviar el email de modo automático al socio/a. '.$nomApe1.'
							Conviene que se le comunique por otros procedimientos, para que tenga conocimiento de la eliminación de la asignación del rol de Tesorería al socio/a';	
							$datosMensaje['textoComentarios'] = $reEmailComunicarElimina['textoComentarios'];					

							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailComunicarElimina['codError'].": ".$reEmailComunicarElimina['errorMensaje'].$reEmailComunicarElimina['textoComentarios']);					
						}
						else
						{ 
								$datosMensaje['textoComentarios'] = $textoPantallaAnulacion;
						}				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
						
					}//else $reEliminarTesoreriaRol['codError'] == '00000'
			}//elseif (isset($_POST['SiEliminar']))		
		}//if (isset($_POST['SiEliminar']) || isset($_POST['Cancelar']))
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')

}
/*---------------------------- Fin eliminarAsignacionTesoreriaRol --------------------------------*/

/*==== FIN GESTIÓN DE ASIGNAR-ELIMINAR Y LISTA ROL TESORERIA =====================================*/

/*=== FIN ROLES ASIGNAR-MODIFICAR-ELIMINAR COORDINACIÓN, PRESIDENCIA, TESORERÍA Y LISTAS =========*/
/*================================================================================================*/


/*========================= INICIO AGRUPACIONES TERRITORIALES =====================================*/

/*--------------------------- Inicio listaAgrupacionesPres -----------------------------------------
Se buscan en tabla " AGRUPACIONTERRITORIAL" los datos de todas las agrupaciones y muestran algunos en 
una tabla-lista páginada "LISTA DE AGRUPACIONES", con información de cada agrupación territorial y 
al final de cada fila dos enlaces: icono lupa (ver toda información de esa agrupación), 
icono pluma (modificar algunos datos de esa agrupación)

RECIBE: nada 
LLAMADA: menú lateral izdo: Agrupaciones

LLAMA: modeloSocios.php:cadBuscarDatosAgrupaciones(), modelos/libs/mPaginarLib.php
vistas/presidente/vListaAgrupacionesPresInc.php, vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster(), controladores/libs/cNavegaHistoria.php:cNavegaHistoria(), 

OBSERVACIONES: 	2022-01-05: Probada PDO y PHP 7.3.21
---------------------------------------------------------------------------------------------------*/
function listaAgrupacionesPres()
{
	//echo "<br><br>0-1 cTesoreroFUNCION:listaAgrupacionesPres:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cPresidente:listaAgrupacionesPres:_POST:" ; print_r($_POST); 
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_3'] == 'SI')	
	{	
		require_once './modelos/modeloEmail.php';
	 require_once './vistas/presidente/vListaAgrupacionesPresInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './controladores/libs/cNavegaHistoria.php';	
		
		$datosMensaje['textoCabecera'] = 'LISTA DE AGRUPACIONES TERRITORIALES';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar el 'La lista de las agrupaciones'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente.php:listaAgrupacionesPres(). Error: ';
		$tituloSeccion = "Presidencia";			
		$resListaDatosAgrupaciones['codError'] = '00000';
		$resListaDatosAgrupaciones['errorMensaje'] = '';

		//-------------------------------- inicio navegación --------------------------
		//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" ya que puede variar dentro de "require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php'
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=listaAgrupacionesPres";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Lista de Agrupaciones";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>2 cPresidente:mostrarSociosPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//--------------------------- fin navegación ----------------------------------
	
		/*--- En la Función : modeloSocios.php:cadBuscarDatosAgrupaciones() -----------
			se crean la cadSelect que se utilizará para "$_pagi_sql", $arrBindValues,... 
		 que serán necesarios para la función siguiente "mPaginarLib()	
			-----------------------------------------------------------------------------*/

		$codAgrupacion = '%'; 
		
		require_once './modelos/modeloSocios.php';			
  $arrDatosCadListaAgrupaciones = cadBuscarDatosAgrupaciones($codAgrupacion);//en modeloSocios.php	
			
		//echo 	"<br><br>3 cPresidente:listaAgrupacionesPres:arrDatosCadListaAgrupaciones: ";print_r($arrDatosCadListaAgrupaciones);			
		
		if ($arrDatosCadListaAgrupaciones['codError'] !== '00000')//=80001, solo error lógico por falta de datos en campos
		{	$resListaDatosAgrupaciones = $arrDatosCadListaAgrupaciones;
		}
		else		
		{
			$_pagi_sql = $arrDatosCadListaAgrupaciones['cadSQL'];//contiene la cadena de la select asignada	
			$arrBindValues = $arrDatosCadListaAgrupaciones['arrBindValues']; //contiene el array correspondiente a la select asignada	
			//$_pag_propagar_opcion_buscar = $arrDatosCadListaAgrupaciones['pag_propagar_opcion_buscar'];//buscar NOMAGRUPACION, EMAIL 		
			$_pag_propagar_opcion_buscar = "";

			$_pagi_cuantos = 14;
			$_pagi_nav_num_enlaces = 14;
			$_pagi_mostrar_errores = true;
			//$_pag_conteo_alternativo = true;
			$conexionLink = '';	
			
			/*mPaginarLib.php(): incluye conexionDB controla errores, pero ponemos aquí 
					$conexionLink, aunque aquí no existe, pero se debe dejar, para mantener 
					compatibilidad de formato de parámetros con otras llamadas a esta función 
					desde otros lugares (posiblemente se podría quitar)
			*/	

			require_once './modelos/libs/mPaginarLib.php';//lib. de modelo para llamar a buscar y paginar 		
			$resListaDatosAgrupaciones = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error				
		}
		//echo "<br><br>4 cPresidente:cPresidente:listaAgrupacionesPres:resListaDatosAgrupaciones: ";print_r($resListaDatosAgrupaciones); 
		
		if ($resListaDatosAgrupaciones['codError'] !== '00000' && $resListaDatosAgrupaciones['codError'] !== '80001')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resListaDatosAgrupaciones['codError'].": ".$resListaDatosAgrupaciones['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
		}
		else //if ($resListaDatosAgrupaciones['codError'] == '00000' || $resListaDatosAgrupaciones['codError'] == '80001') 
		{					
				//echo "<br><br>5 cPresidente:listaAgrupacionesPres:resListaDatosAgrupaciones: ";print_r($resListaDatosAgrupaciones);
			
				vListaAgrupacionesPresInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resListaDatosAgrupaciones,$navegacion);
			
		}//$resListaDatosAgrupaciones['codError']=='00000'
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_3'] == 'SI')		
}
/*--------------------------- Fin listaAgrupacionesPres -------------------------------------------*/

/*------------------------------- Inicio mostrarDatosAgrupacionPres --------------------------------
Se muestran los datos de una AGRUPACION TERRITORIAL procedentes de la tabla "AGRUPACIONTERRITORIAL"
Se llama desde "LISTADO DE AGRUPACIONES ", al hacer clic en el icono "Ver = Lupa"
Para solo lectura 

RECIBE: $_POST['datosFormAgrupacion']['CODAGRUPACION'];
	
LLAMADA: cPresidente:listaAgrupacionesPres, en vistas/presidente/vListaAgrupacionesPresInc.php
"LISTADO DE AGRUPACIONES" para Presidencia al hacer clic en icono Ver

LLAMA: modelos/modeloSocios.php:buscarDatosAgrupacion()
vistas/presidente/vMostrarDatosAgrupacionPresInc.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria(),
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: Probado PHP 7.3.21
En esta función no se precisa modificar $arrBindValues para PDO, laa funciones ya lo 
incluyen internamente.
---------------------------------------------------------------------------------------------------*/
function mostrarDatosAgrupacionPres()
{ 	
 //echo "<br><br>0-1 cPresidente:mostrarDatosAgrupacionPres:POST: ";print_r($_POST);	 
 //echo "<br><br>0-2 cPresidente:mostrarDatosAgrupacionPres:_SESSION: ";print_r($_SESSION);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{
		require_once './modelos/modeloSocios.php';
		require_once './vistas/presidente/vMostrarDatosAgrupacionPresInc.php';			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'DATOS DE UNA AGRUPACIÓN TERRITORIAL';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al mostrar los datos de una Agrupación Territorial. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
																																							
		$nomScriptFuncionError = ' cPresidente.php:mostrarDatosAgrupacionPres(). Error: ';			
		$tituloSeccion  = 'Presidencia, Vice. y Secretaría';	      

		//-------------------------------- inicio navegación --------------------------
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=listaAgrupacionesPres")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=mostrarDatosAgrupacionPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar datos agrupación ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>2 cPresidente:mostrarDatosAgrupacionPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//--------------------------- fin navegación ----------------------------------
		
		$codAgrupacion = $_POST['datosFormAgrupacion']['CODAGRUPACION'];

		$resDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);//en modeloSocios.php, probado error
		
		//echo "<br><br>3 cPresidente:mostrarDatosAgrupacionPres:resDatosAgrupacion: "; print_r($resDatosAgrupacion); 
		
		if ($resDatosAgrupacion['codError'] !== '00000')
		{		
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosAgrupacion['codError'].": ".$resDatosAgrupacion['errorMensaje']);
			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}      
		else //$resDatosAgrupacion['codError']=='00000'
		{	
			//echo "<br><br>4 cPresidente:mostrarDatosAgrupacionPres:resDatosAgrupacion: "; print_r($resDatosAgrupacion);
			
			vMostrarDatosAgrupacionPresInc($tituloSeccion,$resDatosAgrupacion['resultadoFilas'][0] ,$navegacion);	

		} //$resDatosAgrupacion['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin mostrarDatosAgrupacionPres --------------------------------------*/

/*------------------------------- Inicio actualizarDatosAgrupacionPres -----------------------------
Se actualizan algunos datos de tabla "AGRUPACIONTERRITORIAL" los datos de una agrupación,
los datos CIF, CUENTAAGRUPIBAN, TELFIJOTRABAJO, TELMOV,  se validan previamente 

RECIBE: $_POST [datosFormAgrupacion][CODAGRUPACION], para buscar y actualizar esa agrupación

LLAMADA: cPresidente.php:listaAgrupacionesPres(), 
desde el icono lupa del formulario vistas/presidente/vListaAgrupacionesPresInc.php

LLAMA: modeloSocios.php:buscarDatosAgrupacion(), modeloPresCoord.php:actualizarDatosAgrupacion()
modelos/libs/validarCamposActualizarAgrupacionPres.php:validarCamposActualizarAgrupacion()
vistas/presidente/vActualizarAgrupacionPresInc.php, vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster(), controladores/libs/cNavegaHistoria.php:cNavegaHistoria(), 

OBSERVACIONES:
2020-05-28: Aquí no necesita cambios para PDO, lo incluyen internamente las funciones que utiliza 
---------------------------------------------------------------------------------------------------*/
function actualizarDatosAgrupacionPres()
{
	//echo "<br><br>0-1 cPresidente:actualizarDatosAgrupacionPres:_SESSION: ";print_r($_SESSION);
	//echo "<br><br>0-2 cPresidente:actualizarDatosAgrupacionPres:POST: ";print_r($_POST);
	
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{	
		require_once './controladores/libs/arrayEnviaRecibeUrl.php';
		require_once './modelos/libs/arrayParValor.php';		
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/presidente/vActualizarAgrupacionPresInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ACTUALIZAR DATOS DE AGRUPACIÓN TERRITORIAL ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al actualizar datos de la Agrupación Territorial. No se han podido actualizar los datos de la Agrupación Territorial. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:actualizarDatosAgrupacionPres(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría"; 
		
		//------------ Inicio navegacion ---------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=listaAgrupacionesPres")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=actualizarDatosAgrupacionPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar Agrupación ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>2 cPresidente:actualizarDatosAgrupacionPres:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ Fin navegacion ------------------------------------------------	
			
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			if (isset($_POST['salirSinActualizar']))
			{
				$datosMensaje['textoComentarios'] = "No se han modificado los datos de la agrupación: ".$_POST['datosFormAgrupacion']['NOMAGRUPACION'];	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['comprobarYactualizar']))
			{										
				require_once './modelos/libs/validarCamposActualizarAgrupacionPres.php';
				$resValidarCamposForm = validarCamposActualizarAgrupacion($_POST);			
				
		  //echo "<br><br>3 cPresidente:actualizarDatosAgrupacionPres:resValidarCamposForm: ";print_r($resValidarCamposForm);				  
			
				//if (($resValidarCamposForm['codError'] !== '00000') && ($resValidarCamposForm['codError'] > '80000'))
				if ($resValidarCamposForm['codError'] !== '00000')
				{
						vActualizarAgrupacionPresInc($tituloSeccion,$resValidarCamposForm,$navegacion);			
				}    
				else  //$resValidarCamposForm['codError']=='00000' 
				{
					$codAgrupacion = $_POST['datosFormAgrupacion']['CODAGRUPACION'];
					
					$resDatosAgrupacion = actualizarDatosAgrupacion($codAgrupacion, $resValidarCamposForm['datosFormAgrupacion']);//en modeloPresCoord.php probado errores 
					
					//echo "<br><br>4 cPresidente:actualizarDatosAgrupacionPres:resDatosAgrupacion: ";print_r($resDatosAgrupacion);		

					if ($resDatosAgrupacion['codError'] !== "00000")
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosAgrupacion['textoComentarios'].$resDatosAgrupacion['codError'].": ".$resDatosAgrupacion['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}		
					else // $resDatosAgrupacion['codError'] == '00000')
					{	
					  $datosMensaje['textoComentarios'] = "Se han modificado los datos de la agrupación: ".$_POST['datosFormAgrupacion']['NOMAGRUPACION'];							
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}	        
				}//else $resValidarCamposForm['codError']=='00000' 
		
			}//else (isset($_POST['comprobarYactualizar']))	 				  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{			
			$codAgrupacion = $_POST['datosFormAgrupacion']['CODAGRUPACION'];

			$resDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);//en modeloSocios.php, probado error 
				
			//echo "<br><br>5 cPresidente:actualizarDatosAgrupacionPres:resDatosAgrupacion: ";print_r($resDatosAgrupacion);
		
			if ($resDatosAgrupacion['codError'] !== '00000' && $resDatosAgrupacion['numFilas'] !== 1)
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosAgrupacion['codError'].": ".$resDatosAgrupacion['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
			}      
			else //$resDatosAgrupacion['codError']=='00000'
			{			
				 //-- Se podría poner el siguiente bucle controladores/libs/inicializaCamposActualizarAgrupacionPres.php	---				
		
					foreach ($resDatosAgrupacion['resultadoFilas'][0] as $indice => $contenido)                         
					{					
							$datosAgrupacion['datosFormAgrupacion'][$indice]['valorCampo'] = $contenido;
					}	
					//echo "<br><br>6 cPresidente:actualizarDatosAgrupacionPres:datosAgrupacion: ";print_r($datosAgrupacion);						

					vActualizarAgrupacionPresInc($tituloSeccion,$datosAgrupacion,$navegacion);
					
			}//else $resDatosAgrupacion['codError']=='00000'  
		}//else !if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
 }//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin actualizarDatosAgrupacionPres -----------------------------------*/

/*========================= FIN AGRUPACIONES TERRITORIALES ========================================*/


/*=================== INICIO CONFIRMACIÓN SOCIOS Y TAREAS RELACCIONADAS  ==========================*/

/*------ Inicio mostrarEstadoConfirmacionSocios   -------------------------------------------------
Se forma y muestra una tabla con el estado de confirmación del alta, datos de contacto y otros
de los socios para las diferentes situaciones en 

"Pendientes confirmar alta por socio/a":
"alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",
"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",
"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",
"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",
"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a

Al final de la tabla según el estado de confirmación permite  las siguientes "Acciones":
- Reenviar email	
- Confirmar soci@	
- Borrar pendiente confirmar

Buscará en las tablas: SOCIOSCONFIRMAR,CONFIRMAREMAILALTAGESTOR,USUARIO,MIEMBRO

LLAMADA: Desde Menú izquierdo: Confirmación socios/as
LLAMA:
controladores/libs/cPresidenteMostrarEstadoConfSociosPaginar.php:cPresidenteMostrarEstadoConfSociosPaginar()
que incluye modeloPresCoord.php:cadBuscarEstadoConfirmacionAltasGestor(), 
para utilizar en la función de paginar "modelos/libs/mPaginarLib.php:mPaginarLib()"
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: PHP 7.3.21. Probada sin problemas.

Aquí ponemos $arrBindValues para PDO, aunque aquí no existen, pero se debe dejar, para mantener 
compatibilidad de formato para la función mPaginarLib(), en las funciones aquí llamadas,
Funcionan bien internamente con PDO aunque por ahora decido no incluir $arrBindParameter ya que 
puede ser complejo, en una de ellas la select está formada por UNION select. 

NOTA: por ahora esta función esta compartida con rol de Tesorería, 
ver si hacer una independiente Tesorería
-------------------------------------------------------------------------------------------------*/
function mostrarEstadoConfirmacionSocios()
{
 //echo "<br><br>0-1 cPresidente:mostrarEstadoConfirmacionSocios:SESSION: ";print_r($_SESSION);  	
 //echo "<br><br>0-2 cPresidente:mostrarEstadoConfirmacionSocios:_POST: "; print_r($_POST);
	//echo "<br><br>0-3 cPresidente:mostrarEstadoConfirmacionSocios:_GET: "; print_r($_GET);
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')//solo para presidencia sin compartir con Tesorería
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || ($_SESSION['vs_ROL_3'] !== 'SI' && $_SESSION['vs_ROL_5'] !== 'SI')	)//compartida con Tesorería		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloSocios.php';	
		require_once './vistas/presidente/vMostrarEstadoConfirSociosInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';

		$datosMensaje['textoCabecera'] = 'ESTADO DE CONFIRMACIÓN DE ALTA DE SOCIOS/AS';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Lista de Estado de confirmación del alta de los socios/as '. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente:mostrarEstadoConfirmacionSocios(). Error: ';		
		$tituloSeccion = "Presidencia, Vice. y Secretaría";

 	/*--- En la Función:cTesoreroOrdenesCobroUnaRemesaPaginar() hay mucho dentro, 
	 devuelve un array que contiene $arrDatosSocios['cadSQL'] con la cadena select para "$_pagi_sql", 
		necesaría para la función mPaginarLib() y también devuelve otros parámetros 
	 y también $datosFormMiembro, si se busca por APE, APE2 y campo [CONFIRMACIONEMAIL] ---*/
		
  require_once './controladores/libs/cPresidenteMostrarEstadoConfSociosPaginar.php';//es una función 	
	 $arrDatosSocios = cPresidenteMostrarEstadoConfSociosPaginar($_POST);
		
		//echo 	"<br><br>1 cPresidente:mostrarEstadoConfirmacionSocios:arrDatosSocios: ";print_r($arrDatosSocios);	

  //---- Inicio navegación Dejar en este sitio para que funcione bien la navegación  --
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Confirmación socios/as: mostrar estado";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>2 cPresidente:mostrarSociosPres:resDatosSocio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//--------------------------- Fin navegación ---------------------------------------	
	
 	if ($arrDatosSocios['codError'] !== '00000')//=80001, solo puede ser error lógico por falta de datos en campo APE
		{		
			$resDatosSocios = $arrDatosSocios;			
		}
		else	
		{
			$_pagi_sql = $arrDatosSocios['cadSQL'];//contiene la cadena de la select asignada				
 		$_pag_propagar_opcion_buscar = $arrDatosSocios['pag_propagar_opcion_buscar'];//necesario para buscar por APE, o por CONFIRMACIONEMAIL 		

			$_pagi_cuantos = 8;
			$_pagi_nav_num_enlaces = 14;//ojo al cambiar a 17 me dió error
			$_pagi_mostrar_errores = true;
			//$_pag_propagar_opcion_buscar = '';
			//$_pag_conteo_alternativo = true;
			$conexionLink ='';	
			//$arrBindValues = $arrDatosSocios['arrBindValues']; //aquí no existe en $arrDatosSocios
			$arrBindValues = '';		
			/*mPaginarLib.php(): incluye conexionDB controla errores, pero ponemos aquí 
					$conexionLink, aunque aquí no existe, pero se debe dejar, para mantener 
					compatibilidad de formato de parámetros con otras llamadas a esta función 
					desde otros lugares (posiblemente se podría quitar)
			*/	
			require_once './modelos/libs/mPaginarLib.php';//lib. de modelo para llamar a buscar y paginar
 		
			$resDatosSocios = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error								
		}
	 //echo "<br><br>3-1 cPresidente:mostrarEstadoConfirmacionSocios:resDatosSocios: ";print_r($resDatosSocios); 
	
  if ($resDatosSocios['codError'] !== '00000' && $resDatosSocios['codError'] !== '80001')
 	{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocios['codError'].": ".$resDatosSocios['errorMensaje']);			
	 	vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	 }
  else
	 {  
	  //echo "<br><br>3-2 cPresidente:mostrarEstadoConfirmacionSocios:arrDatosSocios: ";print_r($arrDatosSocios); 

			if(isset($arrDatosSocios['APE1']))
	  {$datosFormElegirApeEstadoConf['APE1'] = $arrDatosSocios['APE1'];//También error vacios para formulario
		  $datosFormElegirApeEstadoConf['APE2'] = $arrDatosSocios['APE2'];   				
			}

			if(isset($arrDatosSocios['CONFIRMACIONEMAIL']))
			{ $datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] = $arrDatosSocios['CONFIRMACIONEMAIL']['valorCampo'];	
   }
		 /*Pra el formulario $datosFormElegirApeEstadoConf contendrá los datos APE1 o APE2 si se busca 
					por APE1 o APE2 y CONFIRMACIONEMAIL si se busca por estado de confirmación del alta, estos valores 
					se forman controladores/libs/cPresidenteMostrarEstadoConfSociosPaginar.php que es una función 	
	  */
			$resDatosSocios['navegacion'] = $navegacion;		
			
			//echo "<br><br>4 cPresidente:mostrarEstadoConfirmacionSocios:datosFormElegirApeEstadoConf: ";print_r($datosFormElegirApeEstadoConf);
											
			vMostrarEstadoConfirSociosInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDatosSocios,$datosFormElegirApeEstadoConf);
				
		}//else $resDatosSocios['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin mostrarEstadoConfirmacionSocios -------------------------------*/

/*------ Inicio reenviarEmailConfirmarSocioAltaGestor() -------------------------------------------
Llama a unas funciones para enviar un email a los socios a los que les falta alguna acción para
completar su alta o validar su email y elegir passward.

Casos según el valor de USUARIO.ESTADO:											   
A)
- 'alta-sin-password-gestor' (el alta lo hizo un gestor y ya es efectiva pero está pendiente 
   de confirmar su email y elegir contraseña)
- 'alta-sin-password-excel' (el alta se hizo efectiva al arrancar esta aplicación importado de 
   archivo Excel y ya es efectiva pero está pendiente de confirmar su email y elegir contraseña)

Envía el email, para permitirles elegir password

Busca el email en la tabla MIEMBRO, 
Actualiza las tablas: 
CONFIRMAREMAILALTAGESTOR (se actualiza el NUMENVIOS +1, y FECHAENVIOEMAILULTIMO) Y
MIEMBRO (campo EMAILERROR solo se se produce error en el envío del email)

Se pueden repetir envíos

B) 'PENDIENTE-CONFIRMAR' (el registro de alta lo hizo el propio socio pero está pendiente 
de confirmar su alta para que sea efectiva)
Envía el email, para que confirme su alta como socio

Busca el email en la tabla SOCIOSCONFIRMAR, 
Actualiza las tablas: 
SOCIOSCONFIRMAR (se actualiza el NUMENVIOS +1, y FECHAENVIOEMAILULTIMO)													

Se pueden repetir envíos

LLAMADA: menú izdo: vistas:presidente:formMostrarEstadoConfirSocios.php

LLAMA:
A)
modeloPresCoord.php:buscarConfirmarEmailAltaGestor(),actualizarConfirmarEmailAltaGestor()
modeloEmail.php:emailConfirmarEmailAltaSocioPorGestor(),emailConfirmarEstablecerPassExcel()
modeloUsuarios.php:actualizMiembro()

B)modeloSocios.php: buscarDatosSocioConfirmar(), actualizarSocioConfirmar()
modeloEmail.php:emailPeticionConfirmarAltaUsuario()		
										
OBSERVACIONES: Probado PHP 7.3.21. No necesita modificar para incluir PHP:PDOStatement::bindParam, 
las funciones llamadas aquí lo tratan internamente		

Lo ejecuta rol de Presidente, vice, secretario o el tesorero (que tiene habilitado el acceso a esta función)

Solo se actualiza una tabla, no es necesario control transación, control error es suficiente

NOTA: por ahora esta función esta compartida con rol de Tesorería, 
ver si hacer una independiente Tesorería
------------------------------------------------------------------------------------------------*/
function reenviarEmailConfirmarSocioAltaGestor()
{
	//echo "<br><br>0-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cPresidente:reenviarEmailConfirmarSocioAltaGestor:_POST: "; print_r($_POST);
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')//solo para presidencia sin compartir con Tesorería
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || ($_SESSION['vs_ROL_3'] !== 'SI' && $_SESSION['vs_ROL_5'] !== 'SI')	)//compartida con Tesorería		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{				
		require_once './modelos/modeloUsuarios.php';	
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'REENVIAR EMAIL A SOCIO/A CON PETICIÓN DE CONFIRMACIÓN DE SU ALTA';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido 'reenviar un email al socio/a para pedirle confirmación de su alta'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cPresidente:reenviarEmailConfirmarSocioAltaGestor(). Error: ';		
		$tituloSeccion = "Presidencia, Vice. y Secretaría";
				
		//------------ Inicio navegación ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=reenviarEmailConfirmarSocioAltaGestor";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Reenviar email confirmación alta socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//echo "<br><br>0-3 cPresidente:reenviarEmailConfirmarSocioAltaGestor:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		//------------ Fin navegación -------------------------------------------------	
		
		$resReenviarEmail['codError'] = '00000';
		$resReenviarEmail['errorMensaje'] = "";
			
		$estadoUsuario = $_POST['datosFormMostrarEstadoConfSocio']['ESTADO'];		
		$codUser = $_POST['datosFormMostrarEstadoConfSocio']['CODUSER'];
		$tipoMiembro = 'socio';

		switch ($estadoUsuario) 					
		{ case 'alta-sin-password-gestor'://Para socios dados de alta por un gestor
				case 'alta-sin-password-excel'://Para los socios procedentes de importación antiguos de excel al inicio aplicación 

				$datosEmailAltaSocioConfirmar = 	buscarConfirmarEmailAltaGestor($codUser,$tipoMiembro,$estadoUsuario);//modeloPresCoord.php en CONFIRMAREMAILALTAGESTOR,USUARIO,MIEMBRO y tiene conexionDB, probado error	
				
				//echo "<br><br>1-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:datosEmailAltaSocioConfirmar: "; print_r($datosEmailAltaSocioConfirmar);

				if ($datosEmailAltaSocioConfirmar['codError'] !== '00000')			
				{ $resReenviarEmail = $datosEmailAltaSocioConfirmar;
				}
				elseif ($datosEmailAltaSocioConfirmar['numFilas'] == 0)
				{ $resReenviarEmail['codError'] = '80001';
				}
				else //$datosEmailAltaSocioConfirmar['codError'] == '00000'
				{
					require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
					$datosEmailAltaSocioConfirmar['resultadoFilas'][0]['CODUSER'] = encriptarBase64($codUser);
					//echo "<br><br>1-2-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:datosEmailAltaSocioConfirmar: "; print_r($datosEmailAltaSocioConfirmar);
					
					if ($estadoUsuario == 'alta-sin-password-gestor')
					{ $emailConfirmarSocioAltaGestor = emailConfirmarEmailAltaSocioPorGestor($datosEmailAltaSocioConfirmar['resultadoFilas'][0]);//probado error	     	
					}
					else //($estadoUsuario == 'alta-sin-password-excel')
					{ $emailConfirmarSocioAltaGestor = emailConfirmarEstablecerPassExcel($datosEmailAltaSocioConfirmar['resultadoFilas'][0]);	     		
					}
					//echo "<br><br>1-2-2 cPresidente:reenviarEmailConfirmarSocioAltaGestor:emailConfirmarSocioAltaGestor: "; print_r($emailConfirmarSocioAltaGestor);			
					
					if ($emailConfirmarSocioAltaGestor['codError'] !== '00000')			
					{	$resReenviarEmail = $emailConfirmarSocioAltaGestor;
							$datosActMiembro['EMAILERROR']['valorCampo'] = 'ERROR-ENVIO';
											
							$reActualizMiembro = actualizMiembro('MIEMBRO',$codUser,$datosActMiembro);//modeloUsuarios.php, actualiza MIEMBRO sí hay error	envío, probado error		
							
							//echo "<br><br>2-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:reActualizMiembro: "; print_r($reActualizMiembro);

							if ($reActualizMiembro['codError'] !== '00000')
							{ $resReenviarEmail = $reActualizMiembro;				
							}			 				
					}//if ($emailConfirmarSocioAltaGestor['codError'] !== '00000')			
					else
					{	$actConfirmarEmailAltaGestor['FECHAENVIOEMAILULTIMO']['valorCampo'] = date('Y-m-d'); 
							$actConfirmarEmailAltaGestor['NUMENVIOS']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['NUMENVIOS']+1;
								
							$reActualizarConfirmarEmailAltaGestor = actualizarConfirmarEmailAltaGestor('CONFIRMAREMAILALTAGESTOR',$codUser,$actConfirmarEmailAltaGestor);//modeloPresCoord.php,incluye conexionDB, probado error
																																																																																																																																																																																																																								
							//echo "<br><br>2-2 cPresidente:reenviarEmailConfirmarSocioAltaGestor:reActualizarConfirmarEmailAltaGestor: "; print_r($reActualizarConfirmarEmailAltaGestor);
							
							$nomApe1 = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['NOM']." ".$datosEmailAltaSocioConfirmar['resultadoFilas'][0]['APE1'];
							
							if ($reActualizarConfirmarEmailAltaGestor['codError'] !== '00000')
							{$resReenviarEmail = $reActualizarConfirmarEmailAltaGestor;													 
								$datosMensaje['textoComentarios'] = "Se ha enviado el mensaje recordadorio a <b>$nomApe1</b>, pero no se ha podido dejar constancia del envío en la Base de datos"; 
							}
					}					
				}//else $datosEmailAltaSocioConfirmar['codError'] == '00000'
				break;				
				
				case 'PENDIENTE-CONFIRMAR'://Para socios que ellos mismos se dieron de alta	y les falta confirmar su alta
			
				$datosEmailAltaSocioConfirmar = buscarDatosSocioConfirmar($codUser);//en modeloSocios.php, busca en SOCIOSCONFIRMAR y tiene conexión y $arrayBindParameter, probado error
				
				//echo "<br><br>3-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:datosEmailAltaSocioConfirmar: "; print_r($datosEmailAltaSocioConfirmar);
				
				if ($datosEmailAltaSocioConfirmar['codError'] !== '00000')			
				{ $resReenviarEmail = $datosEmailAltaSocioConfirmar;
				}
				else //$datosEmailAltaSocioConfirmar['codError'] =='00000'
				{ 
						//valido $datosEmailConfirmarAlta['CODUSER']=$_POST['datosFormMostrarEstadoConfSocio']['CODUSER'];
						$datosEmailConfirmarAlta['CODUSER'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['CODUSER'];			
						$datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['EMAIL']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['EMAIL'];			
						$datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['NOM']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['NOM'];
						$datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['APE1']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['APE1'];
						$datosEmailConfirmarAlta['SOCIOSCONFIRMAR']['SEXO']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['SEXO'];	
						
						$emailConfirmarSocioAltaSocio = emailPeticionConfirmarAltaUsuario($datosEmailConfirmarAlta);//probado error
					
						//echo "<br><br>3-2 cPresidente:reenviarEmailConfirmarSocioAltaGestor:emailConfirmarSocioAltaSocio: "; print_r($emailConfirmarSocioAltaSocio);
						
						if ($emailConfirmarSocioAltaSocio['codError'] !== '00000')			
						{$resReenviarEmail = $emailConfirmarSocioAltaSocio;
						}
						else
						{$datosActualizarSocioConfirmar['NUMENVIOS']['valorCampo'] = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['NUMENVIOS']+1;
							$datosActualizarSocioConfirmar['FECHAENVIOEMAILULTIMO']['valorCampo'] = date('Y-m-d');
							
							$resActualizarSocioConfirmar = actualizarSocioConfirmar('SOCIOSCONFIRMAR',$codUser,$datosActualizarSocioConfirmar);//en modeloSocios.php, tiene conexion, probado error										
							
							//echo "<br><br>3-3 cPresidente:reenviarEmailConfirmarSocioAltaGestor:resActualizarSocioConfirmar: "; print_r($resActualizarSocioConfirmar);
							
							$nomApe1 = $datosEmailAltaSocioConfirmar['resultadoFilas'][0]['NOM']." ".$datosEmailAltaSocioConfirmar['resultadoFilas'][0]['APE1'];
							
							if ($resActualizarSocioConfirmar['codError'] !== '00000' )			
							{$resReenviarEmail = $resActualizarSocioConfirmar;					
								$datosMensaje['textoComentarios'] = "Se ha enviado el mensaje recordadorio a <b>$nomApe1</b>, pero no se ha podido dejar constancia del envío en la Base de datos"; 
							}
						}
				}//else $datosEmailAltaSocioConfirmar['codError'] =='00000'
				break;			
				//default://ERROR NO DEBIERA DARSE ESTA SITUACION
		}//FIN  switch ($estadoUsuario) 	
		
		//echo "<br><br>4-1 cPresidente:reenviarEmailConfirmarSocioAltaGestor:resReenviarEmail: "; print_r($resReenviarEmail);
		
		if ($resReenviarEmail['codError'] !== '00000')
		{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resReenviarEmail['codError'].": ".$resReenviarEmail['errorMensaje']);							
		} 
		else 
		{ $datosMensaje['textoComentarios'] = "<br /><br />Ha sido enviado un email para pedir que confirme su alta al socio: <b>$nomApe1</b>
	                                        <br /><br />Es posible que algunos casos los filtros anti spam, lo envíen a la carpeta de correo no deseado";				 
		}
		//echo "<br><br>4-2 cPresidente:reenviarEmailConfirmarSocioAltaGestor:datosMensaje: "; print_r($datosMensaje);
		
		vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin reenviarEmailConfirmarSocioAltaGestor()------------------------*/

/*-------------------- Inicio confirmarAltaSocioPendientePorGestor --------------------------------
En esta función se confirma el alta de un socio (aún "PENDIENTE-CONFIRMACION" su alta por el mismo),
por un gestor autorizado (presidencia,vice,secretaría,teorería), normalmente después de contacto 
con el socio y este le solicita a un gestor que le confirme el alta (email, teléfono, etc.) 
y entonces el gestor le confirma el alta ocn esta función.

Al confirmar el alta, se insertarán en todas las tablas que correspondan los datos del socio, 
se eliminan físicamente de SOCIOSCONFIRMAR para que no salga también duplicado en mostrar pendientes,
y otras posibles búsquedas y USUARIO.ESTADO ='alta'
 
Se enviará un email al socio y también a secretaria, tesoreria, coordinador y presidencia para comunicar el alta.

RECIBE: como hidden (POST) el ['CODUSER']

LLAMADA: desde vistas/presidente/formMostrarEstadoConfirSocios.php() 
LLAMA: modeloPresCoord.php:altaSocioPendienteConfirmadaPorGestor()
       modeloSocios.php:buscarDatosSocio()	
       usuariosLibs/encriptar/encriptacionBase64.php:encriptarBase64()
							modeloEmail.php:emailErrorWMaster()
							vistas/mensajes/vMensajeCabSalirNavInc.php
							controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: probado PHP 7.3.21
Aquí no necesita cambios para PDO, lo incluyen las funciones que utiliza 

NOTA: por ahora esta función esta compartida con rol de Tesorería, 
ver si hacer una independiente Tesorería
-------------------------------------------------------------------------------------------------*/
function confirmarAltaSocioPendientePorGestor()
{
	//echo "<br><br>0-1 cPresidente:confirmarAltaSocioPendientePorGestor:_SESSION: ";print_r($_SESSION);  
 //echo "<br><br>0-2 cPresidente:confirmarAltaSocioPendientePorGestor:_POST: ";print_r($_POST);
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')//solo para presidencia sin compartir con Tesorería
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || ($_SESSION['vs_ROL_3'] !== 'SI' && $_SESSION['vs_ROL_5'] !== 'SI')	)//compartida con Tesorería		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';				
		require_once './modelos/modeloEmail.php'; 
		
		$datosMensaje['textoCabecera'] = 'CONFIRMAR ALTA DE SOCIO/A PENDIENTE DE CONFIRMACIÓN';
		$datosMensaje['textoComentarios'] = "<br /><br />Error al confirmar alta del socio/a. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cPresidente.php:confirmarAltaSocioPendientePorGestor:(). Error: ';	
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";
		
		//------------ Inicio navegación ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=confirmarAltaSocioPendientePorGestor";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Confirmar socio/a pendiente alta";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//echo "<br><br>1 cPresidente:confirmarAltaSocioPendientePorGestor:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
		//------------ Fin navegación -------------------------------------------------	
			
		if (isset($_POST['SiConfirmarAltaSocio']) || isset($_POST['NoConfirmarAltaSocio']))
		{	
			if (isset($_POST['NoConfirmarAltaSocio']))//siempre entrara por aqui, si no intento directo 
			{
				$datosMensaje['textoComentarios'] = 'No se ha confirmado el alta del socio/a';		
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}	
			else // (isset($_POST['siConfirmarAltaSocio']))
			{
				$reAltaSocioConfirmar = altaSocioPendienteConfirmadaPorGestor($_POST['datosFormSocioPendienteConfirmar']['CODUSER']);//devuelve $reAltaSocioConfirmar[CODUSER] en modeloPresCoord.php, ok error
				
				//echo"<br><br>3 cPresidente:confirmarAltaSocioPendientePorGestor:reAltaSocioConfirmar: ";print_r($reAltaSocioConfirmar);   

				if ($reAltaSocioConfirmar['codError'] !== "00000")
				{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reAltaSocioConfirmar['codError'].": ".$reAltaSocioConfirmar['errorMensaje']);							
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					 
				}
				else //($reAltaSocioConfirmar['codError'] == '00000')
				{//---- Inicio buscar datos socio -------------------------------------------
					
					$reDatosSocio = buscarDatosSocio($_POST['datosFormSocioPendienteConfirmar']['CODUSER'],date('Y'));//PARA ENVIAR EMAILS en modeloSocios.php' probado error ok
					
					//echo"<br><br>4 cPresidente:confirmarAltaSocioPendientePorGestor:resDatosSocio: ";print_r($resDatosSocio);
		
					if ($reDatosSocio['codError'] !== '00000')
					{/* El socio ya se le ha confirmado confirmado aquí y se indica por pantalla con el siguiente comentario, pero no habrá recibido el email de confirmación
							No se enviará email a buscarEmailCoordSecreTesor, pues faltarán datos del socio, tampoco se mostrará forma de pago porque 
							también faltarán datos. Nota: Sería mejor incluir el control en "buscarDatosSocio()" dentro de la función "altaSociosConfirmada" 
							para tratar en el posible error 
						*/
						$datosMensaje['textoComentarios'] = $reAltaSocioConfirmar['arrMensaje']['textoComentarios'].					
																																										'.<br /><br /><b>Por un error el socio/a no ha recibido el email con la información de está confirmación del alta 
																																										y tampoco lo habrán recibido coordinación, secretaría, tesorería y presidencia.</b>';//probado error ok																																							
																																											
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.'. modeloSocios.php:buscarDatosSocio(): '.
																																																$reDatosSocio['codError'].": ".$reDatosSocio['errorMensaje'].$datosMensaje['textoComentarios']);																																									
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
					}	
					//---- Fin  buscar datos socio ---------------------------------------------					
					else	//$reDatosSocio['codError'] == '00000' //if ($reDatosSocio['codError'] == '00000')
					{
						$textoComentariosEmail = '';
						
						//--------------------- Inicio Email a socio ------------------------------
						require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
						$datosSocio['CODUSER'] = encriptarBase64($_POST['datosFormSocioPendienteConfirmar']['CODUSER']);	
						
						$datosSocio['USUARIO']	=	$reDatosSocio['valoresCampos']['datosFormUsuario']['USUARIO']['valorCampo'];//no está en $_POST					
						$datosSocio['EMAIL'] = $reDatosSocio['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo'];//También está en $_POST
						$datosSocio['SEXO'] = $reDatosSocio['valoresCampos']['datosFormMiembro']['SEXO']['valorCampo'];//no esta en $_POST
						$datosSocio['NOM']  = $reDatosSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo'];//También está en $_POST
						$datosSocio['APE1'] = $reDatosSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo'];//También está en $_POST							

						$reEnviarEmailSocio = emailConfirmarAltaSocioPendientePorGestor($datosSocio);//comentar para pruebas si se quieres que no lleguen email a socio	//probado error ok								
						
						//echo"<br><br>5 cPresidente:confirmarAltaSocioPendientePorGestor:reEnviarEmailSocio: ";print_r($reEnviarEmailSocio);
				
						if ($reEnviarEmailSocio['codError'] !== '00000')
						{       
								$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta confirmación de alta como socio.';									
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailSocio['codError'].": ".$reEnviarEmailSocio['errorMensaje'].": ".$textoComentariosEmail);
								
						}//--------------------- Fin Email a socio --------------------------------			
						
						//--------- Inicio Email Coordinador,Secretario,Tesororero agrupacion -----	
						$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($_POST['datosFormSocioPendienteConfirmar']['CODUSER']); //probado error ok				
						
						//echo"<br><br>6 cPresidente:confirmarAltaSocioPendientePorGestor:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);				
						
						if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
						{       
								$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta. ';								
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
						}					
						else //$reDatosEmailCoSeTe['codError'] == '00000'
						{
	//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
	//****************************************************************************************************************
								$reEnviarEmailCoSeTe = emailAltaSocioCoordSecreTesor($reDatosEmailCoSeTe,$reDatosSocio['valoresCampos']);//probado error ok				
	//FIN COMENTAR ****************************************************************************************************************

								//echo"<br><br>7 cPresidente:confirmarAltaSocioPendientePorGestor:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);	
								if ($reEnviarEmailCoSeTe['codError'] !=='00000')
								{						
									$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
								}
						}//--------- Fin Email Coordinador,Secretario,Tesororero agrupacion -------	
																		
						$datosMensaje['textoComentarios'] = $reAltaSocioConfirmar['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';												
										
					}//else	$reDatosSocio['codError'] == '00000' //if ($reDatosSocio['codError'] == '00000')
									
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}//else $reAltaSocioConfirmar['codError'] == '00000' 
			}//else (isset($_POST['siConfirmarAltaSocio']))		
		}//if (isset($_POST['SiConfirmarAltaSocio']) || isset($_POST['NoConfirmarAltaSocio']))
			
		else// !if($_POST['SiConfirmarAltaSocio']) || isset($_POST['NoConfirmarAltaSocio']) 
		{	
			$datSocioPendienteConfir = buscarDatosSocioConfirmar($_POST['datosFormMostrarEstadoConfSocio']['CODUSER']);//en modeloSocios.php, busca en SOCIOSCONFIRMAR, con conexionDB probado error ok
			
			//echo "<br><br>8 cPresidente:confirmarAltaSocioPendientePorGestor:datAltaSocioConfirmar: "; print_r($datSocioPendienteConfir);
				
			if ($datSocioPendienteConfir['codError'] !== '00000')			
			{
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioPendienteConfir['codError'].": ".$datSocioPendienteConfir['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioPendienteConfir['codError']=='00000'
			{ //echo "<br><br>9 cPresidente:confirmarAltaSocioPendientePorGestor:datSocioPendienteConfir: "; print_r($datSocioPendienteConfir);

					require_once './vistas/presidente/vConfirAltaSocioPendientePorGestorInc.php';	
					vConfirAltaSocioPendientePorGestorInc($tituloSeccion,$datSocioPendienteConfir['resultadoFilas'][0] ,$navegacion); 
			}		  
		}//!if($_POST['SiConfirmarAltaSocio']) || isset($_POST['NoConfirmarAltaSocio'])
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')	
}
/*--------------------------- Fin  confirmarAltaSocioPendientePorGestor -------------------------*/

/*------------------- Inicio anularSocioPendienteConfirmarPres ------------------------------------
En esta función se eliminan los datos personales de un "casi" socio que inició el alta por el mismo 
y aún está "PENDIENTE-CONFIRMACION" su alta por el mismo. 
En el formulario y pide confirmación para anular el intento de alta del socio.

Lo realiza un gestor autorizado (presidencia,vice,secretaría,teorería), normalmente después de contacto
con el socio y que este le solicite que elimine sus datos (email, teléfono, etc.).
También por inciacitiva del gestor cuando considere que ha transcurrido cierto tiempo (después de 
varios emails al socio con petición de confirmación de alta) o por ser un registro de prueba u ofensivo

Si se anula, se actualizará tabla USUARIO el campo ESTADO='ANULADA-SOCITUD-REGISTRO' y SOCIOSCONFIRMAR
poniendo a NULL todos los campos de datos personales 

Se enviará un email al socio comunicándolo.

LLAMADA: vistas/presidente/vMostrarEstadoConfirSocios.php 
         que muestra la lista de los socios pendientes de alguna confirmación

LLAMA: modeloSocios.php:buscarDatosSocioConfirmar()
modeloPresCoord.php:anulacionSocioPendienteConfirmarPres()
modeloEmail.php:emailAnuladaAltaPendienteConfirmarUsuario(),emailErrorWMaster()
vistas/presidente/vAnularSocioPendienteConfirPresInc.php:vAnularSocioPendienteConfirPresInc()
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: Probado PHP 7.3.21. No necesita modificar para incluir PHP:PDOStatement::bindParam, 
las funciones llamadas aquí lo tratan internamente		

Es igual a controladorSocios.php:anularAltaSocioPendienteConfirmar() excepto el mensaje, se podrían fusionar

NOTA: por ahora esta función esta compartida con rol de Tesorería, 
ver si hacer una independiente Tesorería 
-------------------------------------------------------------------------------------------------*/
function anularSocioPendienteConfirmarPres()
{ 
	//echo "<br><br>0-1 cPresidente:anularSocioPendienteConfirmarPres:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cPresidente:anularSocioPendienteConfirmarPres:_POST: ";print_r($_POST);
	
	//if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_3'] !== 'SI')//solo para presidencia sin compartir con Tesorería
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || ($_SESSION['vs_ROL_3'] !== 'SI' && $_SESSION['vs_ROL_5'] !== 'SI')	)//compartida con Tesorería		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	else //if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
	{		
		require_once './modelos/modeloSocios.php';	
		require_once './modelos/modeloPresCoord.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';

		$datosMensaje['textoCabecera'] = "ELIMNAR LOS DATOS DEL SOCIO/A PENDIENTE DE CONFIRMAR SU ALTA (ACCIÓN IRREVERSIBLE)"; 
		$nomScriptFuncionError = ' cPresidente:anularSocioPendienteConfirmarPres(). Error: ';
		$tituloSeccion  = "Presidencia, Vice. y Secretaría";
			
		$reAnularSocioPendienteConfir['codError'] ='00000';
		
		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cPresidente&accion=anularSocioPendienteConfirmarPres";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar socio/a pendiente de confirmar alta ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior"); 
		//echo "<br><br>0-3 cPresidente:anularSocioPendienteConfirmarPres:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			

		//------------ fin navegacion -------------------------------------------------
		
		//$usuarioBuscado=$_POST['datosFormUsuario']['CODUSER'];
		if (isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
		{ 	
			if (isset($_POST['NoEliminar']))  
			{		 
				$datosMensaje['textoComentarios'] = 'Has salido sin eliminar los datos del socio/a pendiente de confirmar alta.';	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //isset($_POST['SiEliminar']))
			{
				$nomApe1 = $_POST['datosFormSocioPendienteConfirmar']['NOM']." ".$_POST['datosFormSocioPendienteConfirmar']['APE1'];

				$anularSocioPendienteConfir = anulacionSocioPendienteConfirmarPres($_POST['datosFormSocioPendienteConfirmar']['CODUSER']);//en modeloPresCoord.php error ok
				
				//echo "<br><br>2 cPresidente:anularSocioPendienteConfirmarPres:anularSocioPendienteConfir: "; print_r($anularSocioPendienteConfir);			

				if ($anularSocioPendienteConfir['codError'] !== "00000")
				{		
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$anularSocioPendienteConfir['codError'].": ".$anularSocioPendienteConfir['errorMensaje']);
					
					$datosMensaje['textoComentarios'] = '<br /><br />No se han podido eliminar los datos de la socia/o pendiente de confirmar alta  <b>'.$nomApe1.'</b>. Prueba de nuevo pasado un rato. 
																																										<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org';
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}
				else //($anularSocioPendienteConfir['codError'] == '00000')
				{
					$resultEnviarEmail = emailAnuladaAltaPendienteConfirmarUsuario($_POST['datosFormSocioPendienteConfirmar']);	
		
					//echo "<br><br>3 cPresidente:anularSocioPendienteConfirmarPres:resultEnviarEmail: ";print_r($resultEnviarEmail);
									
					if ($resultEnviarEmail['codError'] !== '00000')
					{ 			   
							$datosMensaje['textoComentarios'] = 'Se han eliminado los datos personales del socio/a pendiente de confirmación de alta <b>'.$nomApe1.
																																											'</b>, pero no se le ha podido enviar un mensaje de información por problemas con su email';
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['errorMensaje']);																																										
					}
					else
					{				
							$datosMensaje['textoComentarios'] ='Se han eliminado los datos personales del socio/a pendiente de confirmación de alta <b>'.$nomApe1.
																																										'</b><br /><br />También se ha enviado un email al socio/a comunicándole el borrado de sus datos';
					}			
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}//else $anularSocioPendienteConfir['codError'] == '00000'
			}//else isset($_POST['SiEliminar'])     
			
		}//if (isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
			
		else //!(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
		{	   
			$datSocioPendienteConfir = buscarDatosSocioConfirmar($_POST['datosFormMostrarEstadoConfSocio']['CODUSER']);//en modeloSocios.php, busca en SOCIOSCONFIRMAR erro ok
			
			//echo "<br><br>4 cPresidente:anularSocioPendienteConfirmarPres:datAltaSocioConfirmar: "; print_r($datSocioPendienteConfir);
				
			if ($datSocioPendienteConfir['codError']!=='00000')			
			{
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioPendienteConfir['codError'].": ".$datSocioPendienteConfir['errorMensaje']);	

				$datosMensaje['textoComentarios'] = '<br /><br />No se han podido eliminar los datos de la socia/o pendiente de confirmar alta. Prueba de nuevo pasado un rato. 
																																									<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org';					
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioPendienteConfir['codError']=='00000'
			{//echo "<br><br>5 cPresidente:anularSocioPendienteConfirmarPres:datSocioPendienteConfir:"; print_r($datSocioPendienteConfir);

				require_once './vistas/presidente/vAnularSocioPendienteConfirPresInc.php';	
				vAnularSocioPendienteConfirPresInc($tituloSeccion,$datSocioPendienteConfir['resultadoFilas'][0] ,$navegacion); 
			}   
		}//!(isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
	}//else if ($_SESSION['vs_autentificado']=='SI' &&	$_SESSION['vs_autentificadoGestor']=='SI' && $_SESSION['vs_ROL_3']=='SI')
}
/*--------------------------- Fin anularSocioPendienteConfirmarPres -----------------------------*/

/*=================== FIN CONFIRMACIÓN SOCIOS Y TAREAS RELACCIONADAS  ===========================*/

?>