<?php
/*----------------------------------------------------------------------------------------------------------------------
FICHERO: cTesorero.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este fichero se encuentran las funciones relacionadas con la gestión del tesorero, 
             cuotas y donaciones	y otras 	SEPA ...
													
OBSERVACIONES: Solo accede el tesorero	

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


----------------------------------------------------------------------------------------------------------------------*/

/*---------------------------- Inicio session_start()-------------------------------------------------------------------
VERSION: php 7.3.19
Cuando se ejecuta session_start() para utilizar las variables $_SESSION, si ya hay activada una sesión, aunque no es 
un error puede mostrar un "Notice", si warning esta activado. 
Para evitar estos Notices, uso la función is_session_started(), que he creado que controla el estado con session_status() 
para comprobar si ya está activa antes de ejecutar session_start.

OBSERVACIONES:
----------------------------------------------------------------------------------------------------------------------*/
//echo "<br><br>1_1 cTesorero.php:session_status: ";echo session_status();echo "<br>";

require_once './modelos/libs/my_sessions.php';//añadido nuevo 2020-07-27

if ( is_session_started() === FALSE ) session_start();

//echo "<br><br>2_1 cTesorero.php:session_status: ";echo session_status();echo "<br>";
/*--------------------------- Fin session_start()---------------------------------------------------------------------*/


/*--------------------------- Inicio menuGralTesorero ------------------------------------------------------------------
Se buscan en ROLTIENEFUNCION las funciones con links que tiene el rol de tesorero (CODROL=5) y se muestran 
en el menú lateral

Se pueden añadir un enlaces a archivos para descargarlo, estaría en el cuerpo debajo de la imagen de "ESCUELA LAICA".
Actualmente: documento ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf

LLAMADA: por ser gestor:controladorLogin.php:menuRolesUsuario():login/vRolInc.php también al moverse de un rol a otro
en menú izdo, o en línea superior de enlaces

LLAMA: modeloUsuarios.php:buscarRolFuncion(),cNavegaHistoria, 
vistas/login/vFuncionRolInc.php';
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: PHP 7.3.21. Aquí no necesita cambios PDO.
----------------------------------------------------------------------------------------------------------------------*/
function menuGralTesorero()//podría ser la misma para todos los usuarios,CODROL=SESSION 
{	
 //echo "<br><br>0-1 cTesorero:menuGralTesorero:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cTesorero:menuGralTesorero:_POST: "; print_r($_POST);
	  
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './modelos/modeloUsuarios.php';			
		require_once './modelos/modeloEmail.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';		
			
		$cabeceraCuerpo = "TESORERÍA";
		$textoCuerpo = 'Desde el menú puedes acceder a las funciones disponibles para el <strong>Rol de Tesorería</strong> 
		que tienes asignado en la aplicación de Gestión de Soci@s.';			
		
		$datosMensaje['textoCabecera'] = $cabeceraCuerpo;
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar los el menú correspondiente al Rol de Tesorería. Pruebe de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";	
		$datosMensaje['textoBoton'] = 'Salir de la aplicación';
		$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
		$nomScriptFuncionError = ' cTesorero.php:menuGralTesorero(). Error: ';			
		$tituloSeccion  = 'Tesorería';			

		/*------------ inicio navegación para socios gestores  ------------*/
		$_SESSION['vs_HISTORIA']['enlaces'][2]['link']="index.php?controlador=cTesorero&accion=menuGralTesorero";
		$_SESSION['vs_HISTORIA']['enlaces'][2]['textoEnlace']="Tesorería";			
		$_SESSION['vs_HISTORIA']['pagActual']=2;				
		//echo "<br><br>1 cTesorero:menuGralTesorero:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA'])					
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		/*------------ Fin navegación para socios gestores  ---------------*/				

	 $resFuncionRol = buscarRolFuncion('5');//en modeloUsuarios, para tesorería
		//echo "<br><br>2 cTesorero:menuGralTesorero:resFuncionRol: ";print_r($resFuncionRol);	

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
			
			$enlacesArchivos[2]['link'] = '../documentos/TESORERIA/EL_MANUAL_GESTOR_Tesoreria.pdf';
			$enlacesArchivos[2]['title'] = 'Descargar el Manual para Tesorería de la aplicación informática de Gestión de Soci@s';
			$enlacesArchivos[2]['textoMenu'] = 'Descargar el Manual para Tesorería de la aplicación informática de Gestión de Soci@s';		
   		

			require_once './vistas/login/vFuncionRolInc.php';
			vFuncionRolInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$cabeceraCuerpo,$textoCuerpo,$enlacesArchivos);			
	 }//else $resFuncionRol['codError'] == '00000'		 
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*------------------------------ Fin menuGralTesorero ----------------------------------------------------------------*/


/*==== INICIO FUNCIONES:  MOSTRAR ESTADOS CUOTAS, TABLAS TOTALES PAGOS CUOTAS ==========================
 - mostrarIngresosCuotas(): ESTADO CUOTAS SOCIOS/AS
	- mostrarTotalesCuotas(): TOTALES PAGOS CUOTAS POR AÑOS 
	- mostrarTotalesCuotasAnioAgrup(): TOTALES PAGOS CUOTAS POR AGRUPACIONES  
	- infTotalesCuotas(): INFORMACIÓN SOBRE LAS COLUMNAS DE "TOTALES PAGOS CUOTAS POR AÑO"   
	- infTotalesCuotasAnioAgrup():INFORMACIÓN SOBRE LAS COLUMNAS DE "TOTALES PAGOS CUOTAS POR AÑO Y AGRUPACIONES"   	
======================================================================================================================*/	

/*------------------------------- Inicio mostrarIngresosCuotas ---------------------------------------------------------
Se forma y muestra una tabla-lista páginada "ESTADO CUOTAS SOCIOS" con el resumen de las cuotas pagadas y pendientes 
de los socios. Decreciente por años (5 últimos), y creciente		por nombre en la selección por años.  

Incluye un botón para elegir por AÑO (por defecto el actual), ESTADO CUOTA (por defecto el Todos) y AGRUPACION, y otro 
botón para elegir por APE1, APE2.

Aquí se incluye la paginación de la lista de cuotas de socios. En la parte inferior se muestran número de páginas para 
poder ir directamente a un página, anterior, siguiente, primera, última.

La vista correspondiente "vMostrarIngresosCuotasInc" en forma de tabla, además de de mostrar en cada fila datos sobre 
las cuotas de los socios:	Ingreso, Gasto cobro cuota, Fecha ingreso, 	Estado cuota, 	etc. y al final para cada fila, 
hay iconos con links para acciones sobre el socio:Ver,	Pago cuota, Actualiza cuota, Baja socio 	

En el formulario-tabla, en la parte superior también está el botón "Totales pagos cuotas por años" que dirige a 
cTesorero.php:mostrarTotalesCuotas(), donde se muestra esa información en modo tabla.
	
LLAMADA: desde Menú izquierdo: "Cuotas socio/as" (cTesorero.php:menuGralPres())

LLAMA: require_once controladores/libs/cTesoreroCuotasSociosApeNomPaginarInc.php (que incluye mucho: el control de las
búsquedas: AÑO, ESTADO CUOTAS, (APE1, APE"), formación  select necesario para pasarlo "mPaginarLib")

modelos/libs/mPaginarLib.php:mPaginarLib() (llamar a buscar y paginar, algo antigua pero funciona bien)
modeloEmail.php:emailErrorWMaster()
vistas/tesorero/vMostrarIngresosCuotasInc.php	
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php
Botón: "Totales pagos cuotas por años" dirige a cTesorero.php:mostrarTotalesCuotas()

OBSERVACIONES: PHP 7.3.21 En esta función se incluye $arrBindValues para PDO
----------------------------------------------------------------------------------------------------------------------*/	
function mostrarIngresosCuotas()
{
	//echo "<br><br>0-1 cTesorero:mostrarIngresosCuotas:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cTesorero:mostrarIngresosCuotas:_POST:" ; print_r($_POST);
 
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/tesorero/vMostrarIngresosCuotasInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './controladores/libs/cNavegaHistoria.php';	
		
		$datosMensaje['textoCabecera'] = 'Cuotas socios/as';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar el 'Estado de Cuotas socios/as'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:mostrarIngresosCuotas(). Error: ';
		$tituloSeccion = "Tesorería";
		
			/*Se inicia "$codAreaCoordinacion" y otras variables para utilizarlas dentro 
					de require_once './controladores/libs/cPresidenteSociosApeNomPaginarInc.php'
					Las variables $NomFuncion...	se incluyen para asignar los valores a 
					$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']= ... 		
		*/	
		$codAreaCoordinacion = '%';	//Este rol tiene acceso a todas las agrupaciones

		require_once './controladores/libs/cTesoreroCuotasSociosApeNomPaginarInc.php';	
		/*--- Hay mucho dentro, se crean las cadSelect "$_pagi_sql", $arrBindValues, 
		(que a su vez se forman en modeloTesorero:cadBuscarCuotasSocios,...)	
			que serán necesarios para la función siguiente "mPaginarLib()	
			y también $datosFormMiembro, si se busca por APE, APE2 --------------------*/
		
		//echo 	"<br><br>1-1 cTesorero:mostrarIngresosCuotas:_pagi_sql: ";print_r($_pagi_sql);
		//echo 	"<br><br>1-2 cTesorero:mostrarIngresosCuotas:arrBindValues: ";print_r($arrBindValues);//arrBindValues para cadena SELECT $_pagi_sql	
		//echo 	"<br><br>1-3 cTesorero:mostrarIngresosCuotas:_pag_propagar_opcion_buscar: ";print_r($_pag_propagar_opcion_buscar);		
		
		//-------------------------------- inicio navegación --------------------------
		//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" ya que puede variar dentro de "require_once './controladores/libs/cTesoreroCuotasSociosApeNomPaginarInc.php'
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cTesorero&accion=mostrarIngresosCuotas";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Cuotas socios/as";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>2 cTesorero:mostrarIngresosCuotas:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//--------------------------- fin navegación ----------------------------------	

		$_pagi_cuantos = 8;
		$_pagi_nav_num_enlaces = 14;//ojo al cambiar a 17 me dió error
		$_pagi_mostrar_errores = true;
		//$_pag_propagar_opcion_buscar = '';
		//$_pag_conteo_alternativo = true;
		$conexionLink ='';	
		
		/*mPaginarLib.php(): incluye conexionDB controla errores, pero ponemos aquí 
				$conexionLink, aunque aquí no existe, pero se debe dejar, para mantener 
				compatibilidad de formato de parámetros con otras llamadas a esta función 
				desde otros lugares (posiblemente se podría quitar)
		*/	
		require_once './modelos/libs/mPaginarLib.php';//lib. de modelo para llamar a buscar y paginar: //probado error
		$resCuotasSocios = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);

		//echo "<br><br>3 cTesorero:mostrarIngresosCuotas:resCuotasSocios: ";print_r($resCuotasSocios); 

		if ($resCuotasSocios['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resCuotasSocios['codError'].": ".$resCuotasSocios['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		}   	
		else //$resCuotasSocios['codError']=='00000'
		{
			// Se busca y genera array de agrupaciones para mostrar y elegir en el formulario 
			$codAgrup = '%';//buscar todas agrupaciones por ser rol Tesorería		
			if (isset($_SESSION['vs_CODAGRUPACION']) )
			{$valorDefecto = $_SESSION['vs_CODAGRUPACION']; 
			}
			else
			{ $valorDefecto = '%';
			}
			require_once './modelos/libs/arrayParValor.php';//añadido nuevo 2020-03-28			
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefecto);
			//echo "<br><br>4 cTesorero:mostrarIngresosCuotas:parValorCombo: ";print_r($parValorCombo);		
			
			if ($parValorCombo['codError'] !== '00000')//probado error
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
			}	
			else //Si no errores en $parValorCombo 
			{			  	
				$resCuotasSocios['anioCuotasElegido'] = $_SESSION['vs_ANIOCUOTASELEGIDO'];//para select del anio		
				$resCuotasSocios['ESTADOCUOTA'] = $_SESSION['vs_ESTADOCUOTA'];
				$resCuotasSocios['navegacion'] = $navegacion;
				$resCuotasSocios['CODAGRUPACION'] = $_SESSION['vs_CODAGRUPACION'];
				
				//echo "<br><br>5cTesorero:mostrarIngresosCuotas:resCuotasSocios['navegacion']: ";print_r($resCuotasSocios['navegacion']);			
				//echo "<br><br>6 cTesorero:mostrarIngresosCuotas:resCuotasSocios: ";print_r($resCuotasSocios);	
				//echo "<br><br>7 cTesorero:mostrarIngresosCuotas:datosFormMiembro: ";print_r($datosFormMiembro);					
								
				/* $datosFormMiembro contendrá los datos APE1 o APE2 de ese socio si se busca por APE1 o APE2
							si no será $datosFormMiembro ='', este valor se forma en cTesoreroCuotasSociosApeNomPaginarInc.php
				*/
				vMostrarIngresosCuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resCuotasSocios,$parValorCombo,$datosFormMiembro);	
			}		
		}//$resCuotasSocios['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin mostrarIngresosCuotas --------------------------------------------------------------*/


/*------------------------------- Inicio mostrarTotalesCuotas ----------------------------------------------------------
Se forma y muestra una tabla "TOTALES PAGOS CUOTAS POR AÑOS " con el resumen de los totales de las cuotas pagadas 
y pendientes de los socios, hasta la fecha actual, desglosadas en otros detalles, y en orden decreciente por años. 
Desde la última columna de la tabla, se podrá llamar a un función, para ver para cada año una tabla con los totales 
pagos cuotas por cada agrupación 

LLAMADA: desde el botón "Totales pagos cuotas por años" en el formulario vistas/tesorero/vMostrarIngresosCuotasInc.php	
que se llama desde  cTesorero.php:mostrarIngresosCuotas()

LLAMA: modeloTesorero.php:buscarTotalesAniosPagosCuota() 
vistas/tesorero/vTotalesCuotasInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php
modeloEmail.php:emailErrorWMaster() 

OBSERVACIONES:  PHP 7.3.21
----------------------------------------------------------------------------------------------------------------------*/	
function mostrarTotalesCuotas()
{
	//echo "<br><br>0-1 cTesorero:mostrarTotalesCuotas:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:mostrarTotalesCuotas:POST: ";print_r($_POST);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'TOTALES PAGOS CUOTAS POR AÑOS';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la tabla 'Totales pagos cuotas por años'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:mostrarTotalesCuotas(). Error: ';
		$tituloSeccion = "Tesorería";

		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		else
		{$pagActual = 4;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarTotalesCuotas";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Totales pagos cuotas por años";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:mostrarTotalesCuotas:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------	

		$codAgrupPagoCuota ="%";
		$anioCuotas ="%";
		$estadoSocio ="%";
		$estadoCuota ="%";			
		
		require_once './modelos/modeloTesorero.php';
		$totalesAniosPagosCuota = buscarTotalesAniosPagosCuota($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
		
		//echo "<br><br>2 cTesorero:mostrarTotalesCuotas:totalesAniosPagosCuota: ";print_r($totalesAniosPagosCuota);

		if ($totalesAniosPagosCuota['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$totalesAniosPagosCuota['codError'].": ".$totalesAniosPagosCuota['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		}   
		else //$reCuotaSocioAnio['codError']=='00000'
		{
			$totalesAniosPagosCuota['navegacion'] = $navegacion;
			//echo "<br><br>3 cTesorero:mostrarTotalesCuotas:totalesAniosPagosCuota: ";print_r($totalesAniosPagosCuota);			

			require_once './vistas/tesorero/vTotalesCuotasInc.php';
			vTotalesCuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$totalesAniosPagosCuota);
		}
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin mostrarTotalesCuotas ---------------------------------------------------------------*/

/*---------------------- Inicio mostrarTotalesCuotasAnioAgrup ----------------------------------------------------------
Se forma y muestra una tabla "TOTALES CUOTAS POR AGRUPACIONES" de un año concreto hasta la fecha actual, ordenadas de 
modo creciente por nombre de agrupación. 
En las columnas, se inluyen las sumas las cuotas pagadas y pendientes de los socios, y otros detalles.
En la última fila de la tabla, se mostrarán los totales del año correspondiente 

LLAMADA: desde el botón desde la "lupa" de cada año de la tabla "Totales pagos cuotas por años" desde el formulario 
vistas/tesorero/vMostrarIngresosCuotasInc.php	que se llama desde cTesorero.php:mostrarIngresosCuotas()

LLAMA: modeloTesorero.php:buscarTotalesPagosCuotaAgrup() 
vistas/tesorero/vTotalesCuotasAnioAgrupInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php
modeloEmail.php:emailErrorWMaster() 

OBSERVACIONES: PHP 7.3.21
----------------------------------------------------------------------------------------------------------------------*/	
function mostrarTotalesCuotasAnioAgrup()
{
	//echo "<br><br>0-1 cTesorero:mostrarTotalesCuotasAnioAgrup:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:mostrarTotalesCuotasAnioAgrup:POST: ";print_r($_POST);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'TOTALES PAGOS CUOTAS POR AGRUPACIONES';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la tabla 'Totales pagos cuotas por agrupaciones'. 
		                                                 Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:mostrarTotalesCuotasAnioAgrup(). Error: ';
		$tituloSeccion = "Tesorería";

		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarTotalesCuotas")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarTotalesCuotasAnioAgrup";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Totales pagos cuotas por agrupaciones";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:mostrarTotalesCuotasAnioAgrup:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------	
				
		$anioCuotas = $_POST['datosFormTotalCuotas']['ANIOCUOTA'];
		$codAgrupPagoCuota = "%";//lo inicializo para evitar warnings				
		$estadoSocio = "%";
		$estadoCuota = "%";
		
		require_once './modelos/modeloTesorero.php';	
		$totalesPagosCuotaAgrupAnio = buscarTotalesPagosCuotaAgrup($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota);
		
		//echo "<br><br>2 cTesorero:mostrarTotalesCuotasAnioAgrup:totalesAniosPagosCuota: ";print_r($totalesAniosPagosCuota);
		
		if ($totalesPagosCuotaAgrupAnio['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$totalesPagosCuotaAgrupAnio['codError'].": ".$totalesPagosCuotaAgrupAnio['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		} 
		else //$reCuotaSocioAnio['codError']=='00000'
		{
			$totalesPagosCuotaAgrupAnio['navegacion'] = $navegacion;
				
			//echo "<br><br>3 cTesorero:mostrarTotalesCuotasAnioAgru:totalesPagosCuotaAgrupAnio: ";print_r($totalesPagosCuotaAgrupAnio);			
			
			require_once './vistas/tesorero/vTotalesCuotasAnioAgrupInc.php';
			vTotalesCuotasAnioAgrupInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$totalesPagosCuotaAgrupAnio);
		}
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
//--------------------------- Fin mostrarTotalesCuotasAnioAgrup ------------------------------------------------------*/

/*------------------- Inicio infTotalesCuotas()    ---------------------------------------------------------------------
Función para llamar a la vista que muestra la información sobre el significado de las columnas que aparecen en la tabla 
de "Totales de los pagos de las cuotas por AÑOS"

LLAMADA: cTesorero:mostrarTotalesCuotas()->vTotalesCuotasInc.php -->formTotalesCuotas.php 

OBSERVACIONES: PHP 7.3.21. No necesita consulta SQL
----------------------------------------------------------------------------------------------------------------------*/
function infTotalesCuotas()
{ 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		$tituloSeccion  = "Tesorería";	
		
		require_once './vistas/tesorero/vInfTotalesCuotasInc.php'; 	

		vInfTotalesCuotasInc();
	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin  infTotalesCuotas() ----------------------------------------------------------------*/

/*------------------- Inicio infTotalesCuotasAnioAgrup()    ------------------------------------------------------------
Función para llamar a la vista que muestra la información sobre el significado de las columnas que aparecen en la tabla
de "Totales de los pagos de las cuotas por AGRUPACIONES"

LLAMADA: cTesorero:mostrarTotalesCuotasAnioAgrup()->vTotalesCuotasAnioAgrupInc.php-->formTotalesCuotasAnioAgrup.php 

OBSERVACIONES: PHP 7.3.21. No necesita consulta SQL
----------------------------------------------------------------------------------------------------------------------*/
function infTotalesCuotasAnioAgrup()
{ 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		$tituloSeccion  = "Tesorería";	
		
		require_once './vistas/tesorero/vInfTotalesCuotasAnioAgrupInc.php'; 	

		vInfTotalesCuotasAnioAgrupInc();
		
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin  infTotalesCuotasAnioAgrup() -------------------------------------------------------*/

/*======================= FIN MOSTRAR ESTADOS CUOTAS, TABLAS TOTALES PAGOS CUOTAS ====================================*/


/*=== INICIO FUNCIONES: MOSTRAR CUOTAS Y DATOS SOCIO/A, ANOTAR INGRESO CUOTA, ACTUALIZAR LA CUOTA, BAJA DEL SOCIO/A ====
 - mostrarIngresoCuotaAnio(): MOSTRAR CUOTAS Y OTROS DATOS DEL SOCIO/A
	- actualizarIngresoCuota(): ANOTAR INGRESO CUOTA SOCIO/A
	- actualizarDatosCuotaSocioTes(): ACTUALIZAR LA CUOTA Y OTROS DATOS DEL SOCIO/A   
	- eliminarSocioTes(): DAR DE BAJA AL SOCIO/A 
	
	- eliminarSocioTes_Compartiendo(): DAR DE BAJA AL SOCIO/A. No activada: la función comparte código con otras funciones	
======================================================================================================================*/	

/*------------------------------- Inicio mostrarIngresoCuotaAnio  ------------------------------------------------------
Se muestran algunos datos personales del socio y los detalles en formato tabla del estado de las cuotas de ese socio 
en todos los años ( o se podría limitar por ejemplo a los últimos 5 años dependerá del límite que ponga en el 
código "vistas/tesorero/formMostrarIngresoCuotaAnioTes.php" ) 

LLAMADA: vistas:tesorero:formMostrarIngresoCuotaAnioTes.php desde cTesorero&accion=mostrarIngresosCuotas
          Menú tesorería>>ESTADO CUOTAS SOCIOS/AS-->VER
										y también desde:cTesorero:mostrarOrdenesCobroUnaRemesaTes: 
										en vMostrarOrdenesCobroUnaRemesaInc.php -->VER
										
LLAMA: modelosSocios.php:buscarDatosSocio()
vistas/tesorero/vMostrarIngresoCuotaAnioTesInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php	
modelos/modeloEmail.php
controladores/libs/cNavegaHistoria.php:cNavegaHistoria()

OBSERVACIONES: PHP 7.3.21. En esta función no se precisa modificar $arrBindValues para PDO, lasfunciones ya lo 
incluyen internamente.
----------------------------------------------------------------------------------------------------------------------*/
function mostrarIngresoCuotaAnio()
{
	//echo "<br><br>0-1 cTesorero:mostrarIngresoCuotaAnio:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:mostrarIngresoCuotaAnio:POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './modelos/modeloSocios.php';
		require_once './vistas/tesorero/vMostrarIngresoCuotaAnioTesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'MOSTRAR CUOTAS Y OTROS DATOS DEL SOCIO/A';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al mostrar los datos de un socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$nomScriptFuncionError = ' cTesorero.php:mostrarIngresoCuotaAnio(). Error: ';	
		$tituloSeccion  = "Tesorería";	
		
		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarOrdenesCobroUnaRemesaTes")
		{ $pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}		
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar cuotas y otros datos del socio";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------	
		//echo "<br><br>3 cTesorero:mostrarIngresoCuotaAnio:navegacion: ";print_r($navegacion);			
		
		$anioCuota = '%';
		$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];

		$reCuotaSocioAnio = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modelosSocios.php
		//echo "<br><br>4 cTesorero:mostrarIngresoCuotaAnio:reCuotaSocioAnio: ";print_r($reCuotaSocioAnio);

		if ($reCuotaSocioAnio['codError'] !== '00000')
		{		
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reCuotaSocioAnio['codError'].": ".$reCuotaSocioAnio['errorMensaje']);
			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		} 	
		else //$reCuotaSocioAnio['codError']=='00000'
		{//echo "<br><br>5 cTesorero:mostrarIngresoCuotaAnio:reCuotaSocioAnio['valoresCampos']: ";print_r($reCuotaSocioAnio['valoresCampos']);	

			vMostrarIngresoCuotaAnioTesInc($tituloSeccion,$reCuotaSocioAnio['valoresCampos'],$navegacion);
		} 
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	 
}
/*--------------------------- Fin mostrarIngresoCuotaAnio ------------------------------------------------------------*/

/*------------------------------- Inicio actualizarIngresoCuota  -------------------------------------------------------
Se actualiza, por parte del tesorero, en la tabla CUOTANIOSOCIO los campos en relación con el ingreso de una cuota del 
socio correspondiente al año elegido. 

Se dan dos situaciones que requieren un tratamiento distinto según exista en ese momento para ese socio, 
una orden de cobro de remesa en el banco pendiente de efectuar que dará lugar a dos forms distintos

Desde vActualizarIngresoCuotaInc.php-->vCuerpoActualizarIngresoCuotaInc.php se dirige a dos posibles forms: 

A- formActualizarIngresoCuotaTodos.php: "caso de NO tener una orden de cobro de remesa pendiente en el banco"

			Es el form para actualizar ingreso de cuota para año actual y anteriores donde se puede añadir y modificar datos 
			de Ingreso Cuota en CUOTAANIOSOCIO, (pero sólo cuando en tabla "ORDENES_COBRO" 
			el ESTADOCUOTA !=PENDIENTE-COBRO), para ese socio y año
			
B- formActualizarIngresoCuotaObservaciones.php: "caso de SÍ tener un cobro de remesa en el banco pendiente"

			Es el form alternativo y es para pendientes de cobro de remesa en el banco, ya emitida 
			(sólo cuando en tabla "ORDENES_COBRO" el ESTADOCUOTA =PENDIENTE-COBRO), para ese socio y año 
			y por eso solo se permite	cambiar los campos "Observaciones y Motivo devolución" para evitar 
			cambios en	cuotas, pagos y gastos producir inconsistencias respecto a la remesa ya enviada al banco. 

LLAMADA: (vistas:tesorero:formMostrarIngresosCuotas.php como entrada 
          y desde vActualizarIngresoCuotaInc.php para validar)
          Menú tesorería>>Lista de cuotas socios/as-->Pago Cuota
										
LLAMA: modeloTesorero.php:buscarDatosSocioOrdenesCobro() 
							libs/validarCamposTesorero.php:validarCamposActualizarIngresoCuota()
							modeloTesorero.php:actualizarIngresoCuotaAnio()
							vistas/tesorero/vActualizarIngresoCuotaInc.php
							vistas/mensajes/vMensajeCabSalirNavInc.php
							modelos/modeloEmail.php:emailErrorWMaster()							
							controladores/libs/cNavegaHistoria.php:cNavegaHistoria()
							
OBSERVACIONES: PHP 7.3.21. En esta función no se precisa modificar $arrBindValues para PDO, las funciones ya lo 
incluyen internamente.
----------------------------------------------------------------------------------------------------------------------*/
function actualizarIngresoCuota()
{
	//echo "<br><br>0-1 cTesorero:actualizarIngresoCuota:_SESSION: ";print_r($_SESSION); 
 //echo "<br><br>0-2 cTesorero:actualizarIngresoCuota:POST: ";print_r($_POST);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{
		require_once './modelos/modeloTesorero.php';	
		require_once './vistas/tesorero/vActualizarIngresoCuotaInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'Actualizar ingreso cuota socio/a';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al actualizar ingreso cuota socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";		
		$nomScriptFuncionError = ' cTesorero.php:actualizarIngresoCuota(). Error: ';      
		$tituloSeccion  = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=actualizarIngresoCuota";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar ingreso cuota socio/a ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		//echo "<br><br>1 cTesorero:actualizarIngresoCuota:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------	
				
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{		
			$nomApe1 = strtoupper($_POST['datSocio']['datosFormMiembro']['NOM']." ".$_POST['datSocio']['datosFormMiembro']['APE1']);	
			
			if (isset($_POST['salirSinActualizar']))
			{$datosMensaje['textoComentarios'] = "No se han modificado los datos de ".$nomApe1;	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(!isset($_POST['salirSinActualizar']))
			{
				require_once './modelos/libs/validarCamposTesorero.php';		
				$resValidarCamposForm = validarCamposActualizarIngresoCuota($_POST);
				
				//echo "<br><br>3 cTesorero:actualizarIngresoCuota:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
				if (($resValidarCamposForm['codError'] !=='00000') && ($resValidarCamposForm['codError']>'80000'))
				{	
					vActualizarIngresoCuotaInc($tituloSeccion,$resValidarCamposForm,$navegacion);		
					
				}//if (($resValidarCamposForm['codError'] !=='00000') && ($resValidarCamposForm['codError']>'80000'))
				else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
				{
								
					$resActDatosSocio = actualizarIngresoCuotaAnio($resValidarCamposForm);//modeloTesorero.php, error ok
					
					//echo "<br><br>4 cTesorero:actualizarIngresoCuota:resActDatosSocio: ";print_r($resActDatosSocio);			  

					if ($resActDatosSocio['codError'] !== "00000")
					{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);								
							$datosMensaje['textoComentarios'] .= "<br /><br />No se han modificado los datos de ".$nomApe1;	
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}
     elseif ($resActDatosSocio['numFilas'] === 0 )//Esta situación no se debiera producir ya que   					
					{ //Al pulsar el botón "Guardar datos actulizados", aunque no se haya cambiado ningun campo del formulario,	se actualizará siempre
       //ya en el campo OBSERVACIONES se añade fecha y nombre del Gestor al pulsar el botón "Guardar datos actualizados"	
					  $resActDatosSocio['codError'] = "80201";
							$resActDatosSocio['errorMensaje'] = "Los campos para actualizar los datos están vacíos, al menos el campo OBSERVACIONES debiera contener datos ";
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);								
							$datosMensaje['textoComentarios'] .= "<br /><br />No se han modificado los datos de ".$nomApe1;	
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
					}	
					else //($resActDatosSocio['codError'] == '00000')
					{	$datosMensaje['textoComentarios'] = $resActDatosSocio['arrMensaje']['textoComentarios'];
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
					}	        
				}
			}//else (isset($_POST['salirSinActualizar']))	  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
			
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{
			$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];

			if ($_POST['datosFormCuotaSocio']['ANIOCUOTA'] > date('Y'))
			{			
				$datosMensaje['textoComentarios'] = "- No se puede anotar pagos de cuotas de años futuros -";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			else//	$_POST['datosFormCuotaSocio']['ANIOCUOTA'] <= date('Y'))
			{
				$reCuotaSocioAnio = buscarDatosSocioOrdenesCobro($usuarioBuscado,$_POST['datosFormCuotaSocio']['ANIOCUOTA']);//en modeloTesorero.php					
				
				//echo "<br><br>5 cTesorero:actualizarIngresoCuota:reCuotaSocioAnio:";print_r($reCuotaSocioAnio);echo "<br><br>";

				if ($reCuotaSocioAnio['codError'] !== "00000")
				{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reCuotaSocioAnio['codError'].": ".$reCuotaSocioAnio['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
				}					
				else //$reCuotaSocioAnio['codError']=='00000'
				{
					$anioCuota = $_POST['datosFormCuotaSocio']['ANIOCUOTA'];
					
					$auxDatosCuotaSocioAnio['formIngresoCuota'] = $reCuotaSocioAnio['valoresCampos']['datosFormCuotaSocio'][$anioCuota];				
					unset ($reCuotaSocioAnio['valoresCampos']['datosFormCuotaSocio']);
					
					$datosCuotaSocioAnio = $reCuotaSocioAnio['valoresCampos'];				
					$datosCuotaSocioAnio['formIngresoCuota'] = $auxDatosCuotaSocioAnio['formIngresoCuota'];				
					$datosCuotaSocioAnio['formIngresoCuotaAnterior']['anteriorIMPORTECUOTAANIOPAGADA']=$auxDatosCuotaSocioAnio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA'];
					
					//echo "<br><br>7 cTesorero:actualizarIngresoCuota:datosCuotaSocioAnio: ";print_r($datosCuotaSocioAnio);
			
					vActualizarIngresoCuotaInc($tituloSeccion,$datosCuotaSocioAnio,$navegacion);	
					
				}//else //$reCuotaSocioAnio['codError']=='00000'  	
			}//else	if (!$_POST['datosFormCuotaSocio']['ANIOCUOTA'] > date('Y')) 
			
		}//!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion']))
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*--------------------------- Fin actualizarIngresoCuota -------------------------------------------------------------*/

/*---------------------------- Inicio actualizarDatosCuotaSocioTes -----------------------------------------------------
Se actualiza la cuota elegida por el socio, los datos bancarios, ORDENARCOBROBANCO y OBSERVACIONES y otros datos personales

Necesita varias funciones entre otras 'modelos/libs/prepMostrarActualizarCuotaSocio.php' que es compartida con otros 
controladores de gestor.		
En ella se preparan campos de cuota del socio, para mostrar y actualizar la cuota en formulario de actualización
"vActualizarDatosCuotaSocioTesInc.php", y hay que guardar los valores anteriores porque algunos puede que no 
los modifiqua (habitual) o hay error al introducir los nuevos datos.												

LLAMADA: (vistas:tesorero:formActualizarDatosCuotaSocioTes.php
          Menú tesorería>>Lista de cuotas socios/as-->Actualiza Cuota
										
LLAMA: modelosSocios.php:buscarDatosSocio(),actualizarDatosSocio()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposTesorero.php:validarCamposActCuotaSocioTes()
controladores/libs/inicializaCamposActualizarSocio.php
(contiene mucho código, que acaso se pueda simplificar)
controladores/libs/arrayEnviaRecibeUrl.php:arrayRecibeUrl(),arrayEnviaUrl()
vistas/mensajes/vMensajeCabSalirNavInc.php:vMensajeCabSalirNavInc()
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php: navegación fila superior links
vistas/tesorero/vActualizarDatosCuotaSocioTesInc.php	

							
OBSERVACIONES: PHP 7.3.21. En esta función no se precisa modificar $arrBindValues para PDO, las funciones ya lo 
incluyen internamente.	
NOTA: similar a controladorSocios.php:actualizarSocio() y cCoordinador.php:actualizarSocioCoord(),
cPresidente.php:actualizarSocioPres()	

arrayEnviaUrl() y arrayRecibeUrl($_POST['campoHide']) envía y recibe un array a post con 
['anteriorUSUARIO']['anteriorEMAIL']['anteriorCODPAISDOC']['anteriorTIPODOCUMENTOMIEMBRO']['anteriorNUMDOCUMENTOMIEMBRO']
----------------------------------------------------------------------------------------------------------------------*/
function actualizarDatosCuotaSocioTes()
{
	//echo "<br><br>0-1 cTesorero:actualizarDatosCuotaSocioTes:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:actualizarDatosCuotaSocioTes:POST: ";print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './controladores/libs/arrayEnviaRecibeUrl.php'; 	
		require_once './modelos/libs/validarCamposTesorero.php';		
		require_once './modelos/modeloSocios.php';
		require_once './modelos/libs/arrayParValor.php';		
		require_once './vistas/tesorero/vActualizarDatosCuotaSocioTesInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = 'ACTUALIZAR LA CUOTA Y OTROS DATOS DEL SOCIO/A';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error del sistema al Actualizar datos de un socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org
																																							<br /><br />No se han modificado los datos del socio/a";		
		$nomScriptFuncionError = ' cTesorero.php:actualizarDatosCuotaSocioTes(). Error: ';      
		$tituloSeccion  = "Tesorería";	
		
			//------------ inicio navegacion -------------------------------------------	
			$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
			if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
			{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
			}
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cTesorero&accion=actualizarDatosCuotaSocioTes";	
			$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar datos socio/a ";
			$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
			//echo "<br><br>1 cTesorero:actualizarDatosCuotaSocioTes:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
			require_once './controladores/libs/cNavegaHistoria.php';
			$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
			//------------ fin navegacion -------------------------------------------------	
			
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{			 
			$nomApe1 = strtoupper($_POST['campoActualizar']['datosFormMiembro']['NOM']." ".$_POST['campoActualizar']['datosFormMiembro']['APE1']);	

			if (isset($_POST['salirSinActualizar']))
			{$datosMensaje['textoComentarios'] = "No se han modificado los datos de ".$nomApe1;	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			else //(isset($_POST['comprobarYactualizar']))
			{	$_POST['campoHide'] = arrayRecibeUrl($_POST['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php: pasa un string (obtenido con arrayEnviaUrl) a array	 
				
				$resValidarCamposForm = validarCamposActCuotaSocioTes($_POST);//en validarCamposTesorero.php

				//echo "<br><br>2 cTesorero:actualizarDatosCuotaSocioTes:resValidarCamposForm: ";print_r($resValidarCamposForm);		
		
				if ($resValidarCamposForm['codError'] !== '00000')
				{
					if ($resValidarCamposForm['codError'] < '80000' )//error sistema probado
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else//if ( $resValidarCamposForm['codError'] >= '80000'))//error logico probado
					{$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['campoActualizar']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																		$resValidarCamposForm['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],			
																																																		$resValidarCamposForm['campoActualizar']['datosFormSocio']['CODAGRUPACION']['valorCampo']);
						if ($parValorCombo['codError'] !== '00000')
						{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
						}
						else
						{ $resValidarCamposForm['campoHide'] = arrayEnviaUrl($resValidarCamposForm['campoHide']);	
								vActualizarDatosCuotaSocioTesInc($tituloSeccion,$resValidarCamposForm,$parValorCombo,$navegacion);		
						}
					}//else $resValidarCamposForm['codError'] >= '80000' error logico probado 					
				}//if (($resValidarCamposForm['codError'] !== '00000')
				else //$resValidarCamposForm['codError']=='00000' )
				{					
					$resActDatosSocio = actualizarDatosSocio($resValidarCamposForm['campoActualizar']);//en modeloSocios.php	probado error			

					//echo "<br><br>3 cTesorero:actualizarDatosCuotaSocioTes:resActDatosSocio: ";print_r($resActDatosSocio);			  

					if ($resActDatosSocio['codError'] !== "00000")
					{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDatosSocio['codError'].": ".$resActDatosSocio['errorMensaje']);								
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}
					else //($resActDatosSocio['codError'] == '00000')
					{ $datosMensaje['textoComentarios'] = $resActDatosSocio['arrMensaje']['textoComentarios'];
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}	  
				}//else $resValidarCamposForm['codError']=='00000' )
			}//else (isset($_POST['comprobarYactualizar']))	 				  
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
			
		else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
		{		
			//echo "<br><br>4 cTesorero:actualizarDatosCuotaSocioTes:_POST: ";print_r($_POST);  		
			$anioCuota = '%';		
			$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];		//viene de formulario listado ESTADO CUOTAS SOCIOS
				
			$resDatosSocioActualizar = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php probado error
				
			//echo "<br><br>5 cTesorero:actualizarDatosCuotaSocioTes:resDatosSocioActualizar: ";print_r($resDatosSocioActualizar);

			if ($resDatosSocioActualizar['codError'] !== '00000')
			{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDatosSocioActualizar['codError'].": ".$resDatosSocioActualizar['errorMensaje']);					
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
			}
			else //$resDatosSocioActualizar['codError']=='00000'
			{$parValorCombo = parValoresRegistrarUsuario($resDatosSocioActualizar['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resDatosSocioActualizar['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																	$resDatosSocioActualizar['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo']);				
				//echo "<br><br>6 cTesorero:actualizarDatosCuotaSocioTes:parValorCombo: ";print_r($parValorCombo);

				if ($parValorCombo['codError'] !== '00000')
				{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
				}
				else //$parValorCombo['codError']=='00000'
				{
					//require_once './modelos/libs/prepMostrarActualizarCuotaSocio.php'; //usada anteriormente				
					//$datMostrarActualizarCuotaSocio = prepMostrarActualizarCuotaSocio($resDatosSocioActualizar);//probado error: arrayEnviaUrl($datosSocioActualizar['campoHide'])
					require_once './controladores/libs/inicializaCamposActualizarSocio.php';
					$datMostrarActualizarCuotaSocio = inicializaCamposActualizarSocio($resDatosSocioActualizar);
					
					//echo "<br><br>7 cTesorero:actualizarDatosCuotaSocioTes:datMostrarActualizarCuotaSocio: ";print_r($datMostrarActualizarCuotaSocio); 
					
					if ($datMostrarActualizarCuotaSocio['codError'] !== '00000')
					{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datMostrarActualizarCuotaSocio['codError'].": ".$datMostrarActualizarCuotaSocio['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}								
					else //$datMostrarActualizarCuotaSocio['codError'] =='00000'
					{		
						$datosSocioFormActualizar['campoActualizar'] = $datMostrarActualizarCuotaSocio['campoActualizar'];					
						$datosSocioFormActualizar['campoVerAnioActual'] = $datMostrarActualizarCuotaSocio['cuotaSocioAnioActual'];								
						$datosSocioFormActualizar['campoHide'] = arrayEnviaUrl($datMostrarActualizarCuotaSocio['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php
						//arrayEnviaUrl() recibe un array, lo prepara y convierte en un string serializado, para enviarlo por URL, y después con arrayRecibeUrl() volverlo al array original									
						
						//echo "<br><br>8 cTesorero:actualizarDatosCuotaSocioTes:datosSocioFormActualizar: ";	print_r($datosSocioFormActualizar);	
						vActualizarDatosCuotaSocioTesInc($tituloSeccion,$datosSocioFormActualizar,$parValorCombo,$navegacion);
					}	
				}//else $parValorCombo['codError'] =='00000'
			}//else $resDatosSocioActualizar['codError'] =='00000'  
		}//else !(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion']))  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*--------------------------- Fin actualizarDatosCuotaSocioTes -------------------------------------------------------*/

/*----------------- Inicio eliminarSocioTes (SinCompatirConOtrosGestores) ----------------------------------------------	
Se eliminan datos identificativos del socio (privacidad de datos) de "MIEMBRO", "SOCIO", y se insertan algunos datos 
en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.

Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Papelera.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos datos personales en 
la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.
La parte de navegación se añade, para mantener la fila superior la navegación de opciones

Se muestran algunos datos del socio y en caso de que hubiese un archivo con la firma de un socio debido a alta 
por un gestor, también mostraría aquí para eliminaría el archivo del servidor. 

LLAMADA: Menú tesorería>>Lista de cuotas socios/as-->Baja (icono Papelera)
(Menú tesorería>>Lista de cuotas socios/as-->Baja	)		

LLAMA: validarCamposSocio.php:validarEliminarSocio(),
modeloSocios.php:eliminarDatosSocios(), modeloPresCoord.php:bajaSocioFallecido(),
buscarDatosSocio(),buscarEmailCoordSecreTesor()
modeloEmail.php:emailBajaUsuario(),emailBajaUsuarioFallecido(),$reEnviarEmailCoSeTe(),
emailErrorWMaster()
vistas/gestoresComun/vEliminarSocioTesInc.php.php
vistas/mensajes/vMensajeCabSalirNavInc.php

controladores/libs/cNavegaHistoria.php
							
OBSERVACIÓN: PHP 7.3.21, Aquí no necesita cambios para PDO, lo incluyen internamente las funciones		

Es casi idéntico a cPresidente:eliminarSocioPres(), cCoordinador:eliminarSocioCoord(),
excepto por la navegación y menú izdo y se podría compartir código entre ellos.
Inconveniente menos claro de seguir, y más rigidez para posibles modificaciones.
----------------------------------------------------------------------------------------------------------------------*/	
function eliminarSocioTes()
{ 
	//echo "<br><br>0-1 cTesorero:eliminarSocioTes:_SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:eliminarSocioTes:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './vistas/tesorero/vEliminarSocioTesInc.php';	
		require_once './modelos/libs/validarCamposSocio.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
		$nomScriptFuncionError = ' cTesorero.php:eliminarSocioTes(). Error: ';	
		$tituloSeccion  = "Tesorería";
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cTesorero&accion=eliminarSocioTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ fin navegacion -----------------------------------------------
		
		
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
					
					//echo "<br><br>3 cTesorero:eliminarSocioTes:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
					if ($resValidarCamposForm['codError'] !=='00000')
					{	
							if ($resValidarCamposForm['codError'] >= '80000')//Error lógico		probado		
							{
									vEliminarSocioTesInc($tituloSeccion,$resValidarCamposForm,$navegacion); 
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
									//echo "<br><br>4-1 cTesorero:eliminarSocioTes:reEliminarSocio "; print_r($reEliminarSocio);			
							}
							elseif (isset($_POST['SiEliminarFallecimiento']))
							{	
									$reEliminarSocio = bajaSocioFallecido($_POST);	//en modeloPresCoord.php probado error
									//echo "<br><br>4-2 cTesorero:eliminarSocioTes:reEliminarSocio "; print_r($reEliminarSocio);
							}	
							
							if ($reEliminarSocio['codError'] !== "00000")
							{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarSocio['codError'].": ".$reEliminarSocio['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
							}
							else //($reEliminarSocio['codError'] == '00000')
							{						
									//echo"<br><br>5-1 cTesorero:eliminarSocioTes:sesion: ";print_r($_SESSION);
									$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
									
									if 	( $usuarioBuscado == $_SESSION['vs_CODUSER'] )//solo si el Gestor se borra a él mismo	
									{	unset($_SESSION);//para que ya no esté como autorizado
										//echo"<br><br>5-2 cTesorero:eliminarSocioTes:sesion: ";print_r($_SESSION);
									}	
									
									$textoComentariosEmail = '';
									//-------------------- Inicio email a socio -------------------------------		
									if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	//si falta, no se envía email
									{
											if (isset($_POST['SiEliminarFallecimiento']))
											{ $resultEnviarEmail = emailBajaUsuarioFallecido($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-1 cTesorero:eliminarSocioTes:resultEnviarEmail: ";print_r($resultEnviarEmail);	
											}	
											else
											{	$resultEnviarEmail = emailBajaUsuario($datosSocio['datosFormMiembro']);		
													//echo"<br><br>6-2 cTesorero:eliminarSocioTes:resultEnviarEmail: ";print_r($resultEnviarEmail);
											}	

											if ($resultEnviarEmail['codError'] !== '00000')//probado error
											{ $textoComentariosEmail = '<br /><br />Por un error no se ha podido envíar el email con la información de la baja, a la dirección de correo que está anotada para el socio.';									
													$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['errorMensaje'].$textoComentariosEmail);
											}			
									}//if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	
									//----------------------- Fin	email a socio -------------------------------						
												
									//-------------------- Inicio email a Coord Secre Tes Pres ----------------	
									$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($datosSocio['datosFormUsuario']['CODUSER']);
						
									//echo"<br><br>6-2- cTesorero:eliminarSocioTes:reDatosEmailCoSeTe:";print_r($reDatosEmailCoSeTe);
									if ($reDatosEmailCoSeTe['codError'] !== '00000')
									{ $textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';																																						
											$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);									
									}
									else 
									{
									 	if (isset($datosSocio['datosFormSocio']['OBSERVACIONES']) && !empty($datosSocio['datosFormSocio']['OBSERVACIONES']))
											{$datosSocio['datosFormSocio']['OBSERVACIONES'] = "Observaciones desde gestión de Tesorería: ".$datosSocio['datosFormSocio']['OBSERVACIONES'];
											}	
											//se añade este texto a los comentarios que ya vienen de modeloSocios.php:eliminarDatosSocios() o modeloPresCoord.php:bajaSocioFallecido($_POST);	
											$reEliminarSocio['arrMensaje']['textoComentarios'] .="<br /><br /><br />Se ha enviado un email a Presidencia, Secretaría, Tesorería y Coordinación de la agrupación para informar de esta baja";
															
									//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
				//****************************************************************************************************************
				       $reEnviarEmailCoSeTe = emailBajaSocioCoordSecreTesor($reDatosEmailCoSeTe,$datosSocio);
				//FIN COMENTAR ****************************************************************************************************************

											//echo"<br><br>7 cTesorero:eliminarSocioTes:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
											if ($reEnviarEmailCoSeTe['codError'] !=='00000')//
											{						
													$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';										
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
			//echo "<br><br>9 cTesorero:eliminarSocioTes:datSocioEliminar: "; print_r($datSocioEliminar); 
										
			if ($datSocioEliminar['codError'] !== '00000')
			{			
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioEliminar['codError'].": ".$datSocioEliminar['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}      
			else //$datSocioEliminar['codError']=='00000'
			{//echo "<br><br>10 cTesorero:eliminarSocioTes:datSocioEliminar:";print_r($datSocioEliminar);	

				vEliminarSocioTesInc($tituloSeccion,$datSocioEliminar,$navegacion); 
			}   
		}//!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']))
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin eliminarSocioTes (SinCompatirConOtrosGestores) -------------------------------------*/

/*----------------- Inicio eliminarSocioTes_CompartiendoConOtrosGestores --------------------
Se eliminan datos identificativos del socio (privacidad de datos), y se insertan
algunos datos en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Baja.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos datos personales
en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.
La parte de navegación se añade, para mantener la fila superior la navegación de opciones 

LLAMADA: Menú tesorería>>Lista de cuotas socios/as-->Baja

LLAMA: require_once './controladores/libs/eliminarSocioPorGestor.php';
						 que es la parte común de bajas de socios por gestores y que a su vez incluye 
						 varias scripts		
							
							controladores/libs/cNavegaHistoria.php
							
OBSERVACIÓN: Es casi idéntico a cPresidente:eliminarSocioPres(),cCoordinador:eliminarSocioCoord(),
excepto por la nevegación y menú izdo por eso comparto código entre ellos.
Inconveniente menos claro de seguir y comparto un único formulario de baja:
vistas/gestoresComun/vEliminarSocioGestorInc.php

2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones	
--------------------------------------------------------------------------------------------*/
function eliminarSocioTes_Compartiendo()
{ 
 if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || !isset($_SESSION['vs_ROL_5']))	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	echo "<br><br>0-1 cTesorero:eliminarSocioTes:_SESSION: ";print_r($_SESSION);  
	echo "<br><br>0-2 cTesorero:eliminarSocioTes:_POST: ";print_r($_POST);
	
	$datosMensaje['textoCabecera'] = "Baja del socio/a";	
	$datosMensaje['textoComentarios'] = "<br /><br />Error al dar de baja la socia/o. No se ha podido eliminar los datos del socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																					
	$nomScriptFuncionError = ' cTesorero.php:eliminarSocioTes: ';	
 $tituloSeccion  = "Tesorería";
	
 //------------ inicio navegacion -------------------------------------------	
 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
	
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")		
	{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
	}	
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cTesorero&accion=eliminarSocioTes";	
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Baja socio/a";
 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
	require_once './controladores/libs/cNavegaHistoria.php';
 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
 //------------ fin navegacion -----------------------------------------------

 //NOTA: este es el include que se podría compartir con cPresidente:eliminarSocioPres(),cCoordinador:eliminarSocioCoord(),
 require_once './controladores/libs/eliminarSocioPorGestor.php';//aquí está la parte común de altas por gestores
	
}
/*-------------- Fin eliminarSocioTes_CompartiendoConOtrosGestores -------------------------*/
/*===== FIN MOSTRAR CUOTAS Y DATOS SOCIO/A, ANOTAR INGRESO CUOTA, ACTUALIZAR LA CUOTA, BAJA DEL SOCIO/A ================


/*==== INICIO: ALTA SOCIO POR GESTIÓN TESORERÍA (dos opciones A y B) ===================================================
 A- Sin compatir funciones con otros gestores para altas
	B- Compatiendo funciones con otros gestores para altas       
======================================================================================================================*/	

/*-------------- Inicio altaSocioPorGestorTes (SinCompatirConOtrosGestores) --------------------------------------------
En esta función se dan de alta los socios, por parte de un gestor con rol Tesorería, a petición de una persona que 
desea registrarse como socio.
Se sube un archivo al servidor con la firma de autorización del socio como garantía de protección de datos hasta que el 
socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), con los apellidos y nombre
y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo
Genera un socio con el usuario = NOM.$codUser+125 y añade un digito rand una contraseña que es: sha1($codUser.$usuario) 
(y estará encriptada)

En la tabla USUARIO, el estado quedará: 'alta-sin-password-gestor'

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado de alta y que pulse un link, 
para que elija su contraseña y confirme el email.

También llegará un email a Presidente, coordinador, secretario, tesorero

LLAMADA: desde Menú izquierdo: Alta soci@s (cTesorero:menuGralCoord()

LLAMA:  modelosSocios.php:buscarCuotasAnioEL(), buscarEmailCoordSecreTesor()       
modeloPresCoord.php:mAltaSocioPorGestorTes()        
modeloArchivos.php:arrMimeExtArchAltaSocioFirmaPermitidas(),cadExtensionesArchivos()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php		
modeloEmail.php:emailErrorWMaster(),emailConfirmarEmailAltaSocioPorGestor(),
emailAltaSocioGestorCoordSecreTesor()
vistas/presidente/vAltaSocioPorGestorTesInc.php	
usuariosLibs/encriptar/encriptacionBase64.php	
controladores/libs/cNavegaHistoria.php	
require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';
													
OBSERVACIONES: Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen 
internamente las funciones que utiliza 

Gran parte de esta función casi idéntica a cCoordinador:altaSocioPorGestorCoord(), 
que es casi el mismo código excepto	honorario la parte de navegación y $tituloSeccion y link.
Similar a cPresidente.php:altaSocioPorGestorPres()		

***NOTA: 
Esta es la opción de altas de socio por gestor, sin campartir script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y aunque se podría evitar una repetición de parte del código, pueder ser más sencilla 
de seguir y más flexible para cambios según el rol del gestor.	
----------------------------------------------------------------------------------------------------------------------*/		
function altaSocioPorGestorTes()
{
	//echo "<br><br>0-1 cTesorero:altaSocioPorGestorTes:_SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cTesorero:altaSocioPorGestorTes:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cTesorero:altaSocioPorGestorTes:_FILES: ";print_r($_FILES);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './modelos/modeloArchivos.php';
		require_once './modelos/modeloSocios.php';
		require_once './modelos/modeloPresCoord.php';
		require_once './modelos/libs/arrayParValor.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/tesorero/vAltaSocioPorGestorTesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		
		$datosMensaje['textoCabecera'] = "ALTA NUEVO/A   SOCIO/A POR TESORERÍA "; 
		$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = 'cTesorero.php:altaSocioPorGestorTes(). Error: ';
		$tituloSeccion = "Tesorería";	

		//----------------- inicio fila de navegación ----------------------------
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cTesorero&accion=altaSocioPorGestorTes";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>1-1 cTesorero:altaSocioPorGestorTes:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		require_once './controladores/libs/cNavegaHistoria.php';
		$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//echo "<br><br>1-2 cTesorero:altaSocioPorGestorTes:datosNavegacion: ";print_r($datosNavegacion);					
		//----------------- fin fila de navegación ----------------------------
		
		if (!$_POST) 
		{
			require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';//inicializa algunas variables 	
						
			$parValorCombo = parValoresRegistrarUsuario($valorDefectoPaisDoc,$valorDefectoPaisDom,$valorDefectoAgrup);//antes $parValorCombo = parValoresRegistrarUsuario("ES","ES",'00000000');						
		
			if ($parValorCombo['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);								
			}	
			else //$parValorCombo['codError']=='00000'
			{
				$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');	//en modeloSocios.php, incluye conexion()	probado error		
				
				//echo "<br><br>2 cTesorero:altaSocioPorGestorTes:resCuotasAniosEL: ";print_r($resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']);

				if ($resCuotasAniosEL['codError'] !== '00000')
				{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resCuotasAniosEL['codError'].": ".$resCuotasAniosEL['errorMensaje']);		
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);														
				}			
				else //$resCuotasAniosEL['codError'] == '00000'
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
					
					//echo "<br><br>3 cTesorero:altaSocioPorGestorTes:datosInicio: ";print_r($datosInicio);	
					
					vAltaSocioPorGestorTesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$datosInicio,$parValorCombo);
				}//else $resCuotasAniosEL['codError'] == '00000'			
			}//else $parValorCombo['codError']=='00000'
		}
		else //POST
		{		
			if (isset($_POST['noGuardarDatosSocio'])) //ha pulsado el botón "noGuardarDatosSocio"
			{
				$datosMensaje['textoComentarios'] = "Ha salido sin dar de alta al socio/a";
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
			}			
			else //==(isset($_POST['siGuardarDatosSocio']))Pulsado el botón "siGuardarDatosSocio"
			{
				/* La siguiente función además de validar los datos personales del socio para la BBDD, también valida 
							el archivo a subir al servidor con la firma de alta del socio para protección de datos.
				*/	
				require_once './modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php';
				$resValidarCamposForm = validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor($_POST,$_FILES['ficheroAltaSocioFirmado']);		
				
				//echo "<br><br>4 cTesorero:altaSocioPorGestorTes:resValidarCamposForm: ";print_r($resValidarCamposForm);

				if ($resValidarCamposForm['codError'] !== '00000')//Error
				{
					if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
					{$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																		$resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																		$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']);	
					
						if ($parValorCombo['codError'] !== '00000') 
						{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
						}	
						else //habría que mandar las cuotas y nombres del año actual buscandolas de importedescuotatasocio
						{
							//echo "<br><br>5 cTesorero:altaSocioPorGestorTes:resValidarCamposForm: ";print_r($resValidarCamposForm);

							vAltaSocioPorGestorTesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$resValidarCamposForm,$parValorCombo);			
						}
					}	//if $resValidarCamposForm['codError'] >= '80000'	)//Error lógico
					else //$resValidarCamposForm['codError']< '80000')//Error sistema					
					{
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
					}
				}//if ($resValidarCamposForm['codError'] !== '00000')//Error			
				else //$resValidarCamposForm['codError']=='00000'
				{				
					$resAltaSocio = mAltaSocioPorGestor($resValidarCamposForm);//en modeloPresCoord.php:mAltaSocioPorGestor()//tiene que devolver CODUSER  probado error
					
					//echo "<br><br>6 cTesorero:altaSocioPorGestorTes:resAltaSocio: ";print_r($resAltaSocio);		
					
					if ($resAltaSocio['codError'] !== '00000') //siempre será ($resAltaSocio['codError'] < '80000'))
					{
						$datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'];
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resAltaSocio['codError'].": ".$resAltaSocio['errorMensaje']);					
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);						
					}	
					else // ($resAltaSocio['codError']=='00000') 
					{ 				
						$textoComentariosEmail = '';
						//--------------------- Inicio Email a socio -----------------------------
						if ($resValidarCamposForm['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
						{
							require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
							$datSocioConfEstablecerPass['CODUSER'] = encriptarBase64($resAltaSocio['datosUsuario']['CODUSER']);	

							$datSocioConfEstablecerPass['USUARIO']	=	$resAltaSocio['datosUsuario']['USUARIO'];//Es un dato que se genera en mAltaSocioPorGestor()	
							//$datSocioConfEmailPass['CODSOCIO']	= $resAltaSocio['datosSocio']['CODSOCIO'];//Es un dato que se genera en mAltaSocioPorGestor(), por ahora no lo utilizo										
							$datSocioConfEstablecerPass['EMAIL'] = $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo'];
							$datSocioConfEstablecerPass['SEXO'] = $resValidarCamposForm['datosFormMiembro']['SEXO']['valorCampo'];
							$datSocioConfEstablecerPass['NOM']  = $resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];
							$datSocioConfEstablecerPass['APE1'] = $resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];	
			
							$reEmailConfEstablecerPass =	emailConfirmarEmailAltaSocioPorGestor($datSocioConfEstablecerPass);	//envía mensaje a socio si tiene email	//probado error
							//echo"<br><br>7 cTesorero:altaSocioPorGestorTes:reEmailConfEstablecerPass: ";print_r($reEmailConfEstablecerPass);	
							
							if ($reEmailConfEstablecerPass['codError'] !== '00000')
							{       
									$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta alta como socio.';									
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailConfEstablecerPass['codError'].": ".$reEmailConfEstablecerPass['errorMensaje'].$textoComentariosEmail);
							}     					
						}//--------------------- Fin Email a socio -------------------------------
						
						//--------- Inicio Email Coordinador,Secretario,Tesororero agrupacion ----
						$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($resAltaSocio['datosUsuario']['CODUSER']);//en modeloSocios.php para buscar email de CoordSecreTesor, probado error									
						
						//echo"<br><br>8-1 cTesorero:altaSocioPorGestorTes:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);					
						
						if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
						{
								$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta.';																																						
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
						}			 
						else //if ($reDatosEmailCoSeTe['codError'] == '00000')
						{
							
	//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
	//****************************************************************************************************************

							$reEnviarEmailCoSeTe = emailAltaSocioGestorCoordSecreTesor($reDatosEmailCoSeTe,$resValidarCamposForm);//a gestores
	//FIN COMENTAR ****************************************************************************************************************
							//echo"<br><br>8-2 cTesorero:altaSocioPorGestorTes:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
								if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
								{						
										$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
										$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
								}			
						}//--------- Fin Email Coordinador,Secretario,Tesororero agrupacion -----						
									
						$datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
						//echo "<br><br>9 cTesorero:altaSocioPorGestorTes:datosMensaje: ";print_r($datosMensaje);
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);//pantalla con la información del alta
					}//else $resAltaSocio['codError']=='00000' 
					
				}//$resValidarCamposForm['codError']=='00000'
			}//else isset($_POST['siGuardarDatosSocio'])
		}//else $_POST 	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*-------------- Fin altaSocioPorGestorTes_SinCompatirConOtrosGestores -----------------------------------------------*/

/*---------- Inicio altaSocioPorGestorTes_CompartiendoConOtrosGestores ------------------------------------
En esta función se dan de alta los socios, por parte de un gestor con rol de Tesorería,
a petición de una persona que desea registrarse como socio.
Se insertan los datos del socio a dar de alta en las tablas correspondientes.
Se sube un archivo al servidor con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo 	

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero
													
LLAMADA: desde Menú izquierdo: Alta soci@s (cTesorero:menuGralTes())
LLAMA: require_once './controladores/libs/altaSocioPorGestor.php';
que es la parte común de altas de socios por gestores que a su vez incluye 
varias scripts		
controladores/libs/cNavegaHistoria.php
													
OBSERVACIONES: Esta función casi idéntica a cPresidente:altaSocioPorGestorPres() 
excepto	en la parte de navegación y $tituloSeccion y link, y también casi igual 
a cCoordinador:altaSocioPorGestorCoord(), que es casi el mismo código excepto	honorario.

***NOTA: 
Esta es la opción de altas de socio por gestor, campartiendo el script
require_once './controladores/libs/altaSocioPorGestor.php' y vAltaSocioPorGestorInc.php
y para evitar una repetición de parte del código, pueder ser complicada de seguir 
de seguir y menos flexible para cambios según el rol del gestor.	
																																						
2020-09-10: Mejoras compatir código para los controladores y vistas de gestores.
Probada PHP 7.3.21. Aquí no necesita cambios para PDO, lo incluyen internamente las funciones														
---------------------------------------------------------------------------------------*/		
function altaSocioPorGestorTes_CompartiendoConOtrosGestores()//funciona bien
{
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || !isset($_SESSION['vs_ROL_5']))	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	
	//echo "<br><br>0-1 cTesorero:altaSocioPorGestorTes:_SESSION: ";print_r($_SESSION);	
	echo "<br><br>0-2 cTesorero:altaSocioPorGestorTes:POST: ";print_r($_POST);	
 //echo "<br><br>0-3 cTesorero:altaSocioPorGestorTes:_FILES: ";print_r($_FILES);		

	$datosMensaje['textoCabecera'] = "Alta de socio/a por Tesorería"; 
	$datosMensaje['textoComentarios'] = "<br /><br />No se ha podido dar de alta al socio/a. Prueba de nuevo pasado un rato. 
						                                <br /><br /><br />Si el problema continúa envía un email email indicando el problema y tus datos a adminusers@europalaica.org";
	$nomScriptFuncionError = 'cTesorero.php:altaSocioPorGestorTes(). Error: ';
	$tituloSeccion = "Tesorería";	

	//----------------- inicio fila de navegación ----------------------------
	$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cTesorero&accion=altaSocioPorGestorTes";
	$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Alta de socio/a";			
	$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
	//echo "<br><br>1-1 cTesorero:altaSocioPorGestorTes:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
	
	require_once './controladores/libs/cNavegaHistoria.php';
	$datosNavegacion['navegacion']=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
 //echo "<br><br>1-2 cTesorero:altaSocioPorGestorTes:datosNavegacion: ";print_r($datosNavegacion);					
	//----------------- fin fila de navegación ----------------------------
	
	require_once './controladores/libs/altaSocioPorGestor.php';//aquí está la parte común de altas por gestores

}
/*-------------- Fin altaSocioPorGestorTes_CompartiendoConOtrosGestores ---------------------------------*/

/*==== FIN: ALTA SOCIO POR GESTIÓN TESORERÍA (dos opciones A y B =====================================================*/


/*============================ INICIO GESTIÓN DONACIONES ===============================================================
 - mostrarDonaciones(): 
	- anotarIngresoDonacionMenu():
 - comprobarDonantePrevio_Socio():	
 - anotarIngresoDonacion():	
	- mostrarIngresoDonacion(): 
	- modificarIngresoDonacionTes(): 
	- anularDonacionErroneaTes(): 
	- mostrarTotalesDonaciones(): 
	- excelDonacionesTesorero():
	- donacionConceptos():
	- aniadirDonacionConceptoTes():
======================================================================================================================*/	

/*------------------------------- Inicio mostrarDonaciones -------------------------------------------------------------
Se forma y muestra una tabla-lista páginada "LISTADO DE LAS DONACIONES " con la lista de las donaciones ordenadas 
según LAST-IN ->FIRST-OUT.

Aquí se incluye la paginación de la lista donaciones. En la parte inferior se muestran número de páginas para poder 
ir directamente a un página, anterior, siguiente, primera, última.
La vista correspondiente en forma de tabla, además de de mostrar en cada fila datos sobre las donaciones:	Año,	Apellidos,
Nombre, etc. al final para cada fila, hay iconos con links para acciones sobre la correspondiente donación 
con Acciones: Ver,	Modificar, Eliminar.

Incluye un botón para elegir por AÑO, y otro botón para elegir por APE1, APE2.

En el formulario-tabla, en la parte superior también están los botones: "Anotar donación", "Total donaciones", 
"Exportar las donaciones a Excel" y "Mostrar y Añadir Conceptos de Donación" que dirigen a las funciones
correspondientes dentro de cTesorero.php
	
LLAMADA: desde Menú izquierdo: -Donaciones (cTesorero.php:menuGralTes())

LLAMA: require_once controladores/libs/cTesoreroDonacionesApeNomPaginarInc.php (que incluye mucho: el control de 
las búsquedas por: (APE1, APE",ANIODONACION) y formación  select necesario para pasarlo "mPaginarLib"

modelos/libs/mPaginarLib.php:mPaginarLib() (llamar a buscar y paginar, algo antigua pero funciona bien)
modeloEmail.php:emailErrorWMaster()
vistas/tesorero/vMostrarDonacionesInc.php	
vistas/mensajes/vMensajeCabSalirNavInc.php
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función se incluye $arrBindValues para PDO
----------------------------------------------------------------------------------------------------------------------*/	
function mostrarDonaciones()
{
	//echo "<br><br>0-1 cTesorero:mostrarDonaciones:SESSION: ";print_r($_SESSION);   
	//echo "<br><br>0-2 cTesorero:mostrarDonaciones:_POST: "; print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/tesorero/vMostrarDonacionesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
			
		$datosMensaje['textoCabecera'] = 'DONACIONES';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar la 'Lista de donaciones'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cTesorero.php:mostrarDonaciones(). Error: ';	
		$tituloSeccion  = 'Tesorería';	
		
		require_once './controladores/libs/cTesoreroDonacionesApeNomPaginarInc.php';//hay mucho dentro:

		/*--- Hay mucho dentro, se crean las cadSelect "$_pagi_sql", $arrBindValues, 
		(que a su vez se forman en modeloTesorero:cadBuscarDonaciones,...)	
		que serán necesarios para la función siguiente "mPaginarLib()	
		y también $datosFormMiembro, si se busca por APE, APE2 ----------------------*/
		
		//echo 	"<br><br>1 cTesorero:mostrarDonaciones:_pagi_sql: ";print_r($_pagi_sql);
		//echo 	"<br><br>2 cTesorero:mostrarDonaciones:_pag_propagar_opcion_buscar: ";print_r($_pag_propagar_opcion_buscar);		
		
		//-------------------------------- inicio navegación --------------------------		
		//-- Necesario aquí: ya que "$_SESSION['vs_HISTORIA']" ya que puede variar dentro de "require_once './controladores/libs/cPresCoordSociosApeNomPaginarInc.php'
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link']="index.php?controlador=cTesorero&accion=mostrarDonaciones";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace']="Donaciones";			
		$_SESSION['vs_HISTORIA']['pagActual']=3;		
		//echo "<br><br>5cTesorero:mostrarDonaciones:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);	
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion=cNavegaHistoria($_SESSION['vs_HISTORIA'],"Anterior");	
		//--------------------------- fin navegación ----------------------------------

		$_pagi_cuantos = 8;
		$_pagi_nav_num_enlaces = 14;//ojo al cambiar a 17 me dió error
		$_pagi_mostrar_errores = true;
		//$_pag_propagar_opcion_buscar='';
		$conexionLink ='';
		
		//echo "<br><br>3-1 cTesorero:mostrarDonaciones:_pagi_sql: ";print_r($_pagi_sql);
		
		require_once './modelos/libs/mPaginarLib.php';//lib. de modelo para llamar a buscar y paginar	
		$resDonaciones = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error	
		
		//echo "<br><br>3-2 cTesorero:mostrarDonaciones:resDonaciones: ";print_r($resDonaciones);
		
		if ($resDonaciones['codError'] !== '00000')
		{
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDonaciones['codError'].": ".$resDonaciones['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
		}      
		else //$resDonaciones['codError']=='00000'
		{		
			$resDonaciones['anioDonacionesElegido'] = $_SESSION['vs_ANIODONACIONSELEGIDO'];		
			
			$resDonaciones['navegacion'] = $navegacion;
				
			//echo "<br><br>4 cTesorero:mostrarDonaciones:datosFormMiembro: ";print_r($datosFormMiembro);					
			
			vMostrarDonacionesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resDonaciones,$datosFormMiembro);	
						
		}//$resDonaciones['codError']=='00000'  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin mostrarDonaciones ------------------------------------------------------------------*/

/*------------------------------- Inicio anotarIngresoDonacionMenu -----------------------------------------------------
Esta función de entrada creará un menú para dirigir a la anotación de donaciones según los distintos tipos 
de donantes: 

- Donante nuevo, e identificado y no socio, (llevará vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php) 

- Donante Anónimo, (llevará vistas/tesorero/vAnotarIngresoDonacionAnonimoInc.php) 

- Buscar donante por "Nº documento" (NIF, NIE, pasaporte) o por "Email" por ser socio para donantes ya registrados,
  por ser "socios" o "donantes previos". Llevará a "vistas/tesorero/vComprobarDonantePrevio_SocioInc.php" 
		y cTesorero.php:comprobarDonantePrevio_Socio() (buscará los datos en las tablas "MIEMBROS" o "DONACION")	


Cuando se considera que es un "Donante ya registrado" (por ser socio o donante previo ) los buscará en la 
tabla MIEMBRO (caso de que sea socio) o en la tabla DONACION y en caso de no encontrarlo lo tratará como 
un "Donante nuevo, e identificado yno socio"

Desde está función se pasará las vistas que terminarán en las funciones "cTesorero.php:comprobarDonantePrevio_Socio()
o anotarIngresoDonacion()" donde se completará el proceso de anotar la donación, según los anteriores casos.

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (botón superior: Anotar donaciones)

LLAMA:
modelos/libs/arrayParValor.php:parValorPais(),parValoresDonacionConceptos()
vistas/tesorero/vAnotarIngresoDonacionMenuInc.php, 
vistas/tesorero/vAnotarIngresoDonacionAnonimoInc.php
vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php
vistas/tesorero/vComprobarDonantePrevio_SocioInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. Esta función no se necesitan $arrBindValues para PDO 
----------------------------------------------------------------------------------------------------------------------*/	
function anotarIngresoDonacionMenu()
{
	//echo "<br><br>0-1 cTesorero:anotarIngresoDonacionMenu:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:anotarIngresoDonacionMenu:POST1: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/libs/arrayParValor.php';		
		require_once './vistas/tesorero/vAnotarIngresoDonacionMenuInc.php';	
		require_once './vistas/tesorero/vAnotarIngresoDonacionAnonimoInc.php';		
		require_once './vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php';	
  require_once './vistas/tesorero/vComprobarDonantePrevio_SocioInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
			
		$datosMensaje['textoCabecera'] = "MENÚ ANOTAR DONACIONES";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al entrar en - Menú Anotar Donaciones -.  Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:anotarIngresoDonacionMenu(). Error: ';	
		$tituloSeccion = "Tesorería";	

		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		else
		{$pagActual = 4;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=anotarIngresoDonacionMenu";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Menú Anotar Donación";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:anotarIngresoDonacionMenu:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------
			
		if (isset($_POST['buscarPorAnonimo'])||isset($_POST['buscarPorDonanteNuevoNoAnonimo'])||isset($_POST['buscarPorNumDocEmail'])/*||isset($_POST['salirDonacion'])*/)
		{/*
			if (isset($_POST['salirDonacion']))// Comentado por estar sustitituido por botón volver "Anterior" en vCuerpoAnotarIngresoDonacionMenuInc.php
			{ $datosMensaje['textoComentarios'] = "Has salido sin haber registrado los datos de la donación";				
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}	
			else //!isset($_POST['salirDonacion'])) //incluye si ANÓNIMO, si Donante Nuevo, else si Nº Doc, email
			*/
			{		
    //$datosDonanteInicio['datosFormDonacion']['CODAGRUPACION']['valorCampo'] = '00000000';//EL Estatal	está por defecto en formulario	
	
    $parValorPaisDoc = parValorPais("ES");					
		  //echo "<br><br>2-1 cTesorero:anotarIngresoDonacionMenu:parValorPaisDoc: "; print_r($parValorPaisDoc);
		
		  $parValoresDonacionConceptos = parValoresDonacionConceptos('GENERAL');			
		  //echo "<br><br>2-2 cTesorero:anotarIngresoDonacionMenu:parValoresDonacionConceptos: "; print_r($parValoresDonacionConceptos);	

				if ($parValorPaisDoc['codError'] !== '00000' || $parValoresDonacionConceptos['codError'] !== '00000')			
				{ 
		    $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorPaisDoc['codError'].": ".$parValorPaisDoc['errorMensaje'].$parValoresDonacionConceptos['errorMensaje']); 
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
				}	
				else // ($parValorPaisDoc['codError'] == '00000' && $parValoresDonacionConceptos['codError'] == '00000')			
				{
					if (isset($_POST['buscarPorAnonimo']) )
					{  						
								$datosDonanteInicio['datosFormDonacion']['TIPODONANTE']['valorCampo'] = 'ANONIMO' ;
			
        vAnotarIngresoDonacionAnonimoInc($tituloSeccion,$datosDonanteInicio,$parValoresDonacionConceptos,$navegacion);	
					}
					elseif (isset($_POST['buscarPorDonanteNuevoNoAnonimo']) )
					{  
								$datosDonanteInicio['datosFormDonacion']['TIPODONANTE']['valorCampo'] = 'IDENTIFICADO-NO-SOCIO';
		
						 	vAnotarIngresoDonacionNoEncontradoInc($tituloSeccion,$datosDonanteInicio,$parValorPaisDoc,$parValoresDonacionConceptos,$navegacion);											
					}
					elseif (isset($_POST['buscarPorNumDocEmail']) ) //(buscar por buscarPorNumDoc o por buscarPorEmail )
					{		
							vComprobarDonantePrevio_SocioInc($tituloSeccion,$datosDonanteInicio,$parValorPaisDoc,$navegacion);
							
					}//elseif (isset($_POST['buscarPorNumDocEmail']) )
				}//else ($parValorPaisDoc['codError'] == '00000' && $parValoresDonacionConceptos['codError'] == '00000')		
			}//!isset($_POST['salirDonacion']))
		}//if (isset($_POST['buscarPorAnonimo'])||isset($_POST['buscarPorDonanteNuevoNoAnonimo'])||isset($_POST['buscarPorNumDocEmail'])||isset($_POST['salirDonacion']) )
			
		else//!if (isset($_POST['buscarPorAnonimo'])||isset($_POST['buscarPorDonanteNuevoNoAnonimo'])||isset($_POST['buscarPorNumDocEmail'])||isset($_POST['salirDonacion']) )
		{	
			//echo "<br><br>3 cTesorero:anotarIngresoDonacionMenu:parValorPaisDoc: "; 

		 $datosDonanteInicio = "";  $parValorPaisDoc = ""; //para evitar notices
			
			vAnotarIngresoDonacionMenuInc($tituloSeccion,$datosDonanteInicio,$parValorPaisDoc,$navegacion);
			
		}//else !if (isset($_POST['buscarPorAnonimo'])||isset($_POST['buscarPorDonanteNuevoNoAnonimo'])...
		
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*------------------------------- Fin anotarIngresoDonacionMenu ------------------------------------------------------*/

/*------------------------------- Inicio comprobarDonantePrevio_Socio --------------------------------------------------
En esta función se buscan los datos personales de un donante identificado por ser socio o un donante previo.
Se buscará por "Nº documento" (NIF,..) o por "Email" en las tablas "MIEMBROS" o "DONACION" intdouciendo esos 
campo en el formulario "vistas/tesorero/vComprobarDonantePrevio_SocioInc.php" 
 
- En caso de ser SÍ encontrado, se va al formulario "vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc()",
  para introducir los datos de esa donación.
- En caso de NO ser encontrado en la tabla se le trata como "Donante nuevo e identificado (y no socio)" 
  y se va al formulario "vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc()" 

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionMenuInc.php (botón superior: Buscar datos de donante previo o socio/a)

LLAMA: modeloTesorero.php:buscarDonante()
modelos/libs/arrayParValor.php:parValorPais(),parValoresDonacionConceptos()
modelos/libs/validarCamposTesorero.php:validarComprobarDonante()
vistas/tesorero/vComprobarDonantePrevio_SocioInc.php 
vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php
vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. Esta función no se necesitan $arrBindValues para PDO, lo incluyen las funciones que utiliza. 
----------------------------------------------------------------------------------------------------------------------*/	
function comprobarDonantePrevio_Socio()
{
	//echo "<br><br>0-1 cTesorero:comprobarDonantePrevio_Socio:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:comprobarDonantePrevio_Socio:POST1: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/libs/arrayParValor.php';		
		require_once './modelos/libs/validarCamposTesorero.php';
		require_once './vistas/tesorero/vComprobarDonantePrevio_SocioInc.php';	
		require_once './vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php';	
		require_once './vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
			
		$datosMensaje['textoCabecera'] = "ANOTAR DONACIONES: COMPROBAR SI ES SOCIO/A O DONANTE PREVIO";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al anotar el ingreso de una donación, al comprobar si es socio/a o donante previo. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:comprobarDonantePrevio_Socio(). Error: ';	
		$tituloSeccion = "Tesorería";	

		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=anotarIngresoDonacionMenu")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=comprobarDonantePrevio_Socio";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Comprobar socio/a o donante previo";//"Anotar donación"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:comprobarDonantePrevio_Socio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------
  
		if (isset($_POST['buscarPorEmail']) || isset($_POST['buscarPorNumDoc']) || isset($_POST['salirDonacion']) ) 
		{
			if (isset($_POST['salirDonacion'])) 
			{ 
					$datosMensaje['textoComentarios'] = "Has salido sin haber registrado los datos de la donación";				
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}	
			else // (isset($_POST['buscarPorEmail']) || isset($_POST['buscarPorNumDoc']) ) 
			{	
				if (isset($_POST['datosFormDonacion']['CODPAISDOC']) ) //Evita notice
				{ $codPais = $_POST['datosFormDonacion']['CODPAISDOC']; }  else	{ $codPais = "ES"; }	
				
				$parValorPaisDoc = parValorPais($codPais);

				if ($parValorPaisDoc['codError'] !== '00000')	
				{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorPaisDoc['codError'].": ".$parValorPaisDoc['errorMensaje']); 
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
				}	
				else //$parValorPaisDoc['codError'] == '00000'
				{	
						$resValidarCamposForm = validarComprobarDonante($_POST);//en validarCamposTesorero.php valida campos Nº documento o email para buscar socios/donante						
						//echo "<br><br>2 cTesorero:comprobarDonantePrevio_Socio:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
						if ($resValidarCamposForm['codError'] !== '00000')//solo puede ser error lógico
						{		  
							vComprobarDonantePrevio_SocioInc($tituloSeccion,$resValidarCamposForm,$parValorPaisDoc,$navegacion);
						}	
						else //$resValidarCamposForm['codError']=='00000' //sin error lógico en los campos introducidos
						{							
							$datosDonante = buscarDonante($resValidarCamposForm['datosFormDonacion']);//en modeloTesorero.php. Primero busca en tabla MIEMBRO, después en DONACION(solo el 1º encontrado) 
							
							//echo "<br><br>3 cTesorero:comprobarDonantePrevio_Socio:datosDonante: ";print_r($datosDonante);
							
							if ($datosDonante['codError'] !== '00000') //siempre será ($datosDonante['codError'] < '80000')) error sistema
							{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datosDonante['codError'].": ".$datosDonante['errorMensaje'].$datosDonante['arrMensaje']['textoComentarios']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);										
							}	
							else // ($datosDonante['codError'] == '00000')
							{
									if (isset($_POST['datosFormDonacion']['CONCEPTO']) ) //Evita notice
				     { $conceptoDonacion = $_POST['datosFormDonacion']['CONCEPTO']; }  else	{ $conceptoDonacion = "GENERAL"; }	
									
									$parValoresDonacionConceptos = parValoresDonacionConceptos($conceptoDonacion);		
									
									if ($parValoresDonacionConceptos['codError'] !== '00000')			
									{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValoresDonacionConceptos['codError'].": ".$parValoresDonacionConceptos['errorMensaje']); 
											vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
									}	
									else //$parValoresDonacionConceptos['codError'] == '00000'
									{									
										if ($datosDonante['numFilas'] !== 1)//NO encontrado: NO SERÁ UN SOCIO y tampoco un donante ya anotado, luego le haremos 'IDENTIFICADO-NO-SOCIO'
										{
											/*----------------------- Inicio Donante no encontrado -----------------------------*/
												$resValidarCamposForm ['datosFormDonacion']['TIPODONANTE']['valorCampo'] = 'IDENTIFICADO-NO-SOCIO'; 
												$resValidarCamposForm['datosFormDonacion']['encontrado']['valorCampo'] = 'NO';												

            vAnotarIngresoDonacionNoEncontradoInc($tituloSeccion,$resValidarCamposForm,$parValorPaisDoc,$parValoresDonacionConceptos,$navegacion);  												
										}	/*----------------------- Fin Donante no encontrado --------------------------------*/
										
										else // $datosDonante['numFilas'] == 1) SÍ Encontrado
										{/*----------------------- Inicio Donante socio o donante ya registrado --------------*/
											
												$datosDonante['resultadoFilas']['datosFormDonacion']['encontrado']['valorCampo'] = 'SI';			

												vAnotarIngresoDonacionSiEncontradoInc($tituloSeccion,$datosDonante['resultadoFilas'],$parValorPaisDoc,$parValoresDonacionConceptos,$navegacion);
											/*----------------------- Fin Donante socio o donante ya registrado ------------------*/						
										}//$datosDonante['numFilas']==1) Encontrado	
							  }//else $parValoresDonacionConceptos['codError'] == '00000'										
							}//else $datosDonante['codError']=='00000' 
						}//$else $resValidarCamposForm['codError']=='00000'				
				}//else $parValorPaisDoc['codError'] == '00000'
			}//else (isset($_POST['buscarPorEmail']) || isset($_POST['buscarPorNumDoc']) ) 
		}//if (isset($_POST['buscarPorEmail']) || isset($_POST['buscarPorNumDoc']) || isset($_POST['salirDonacion']) ) 
			
		else	//!if (isset($_POST['buscarPorEmail']) || isset($_POST['buscarPorNumDoc']) || isset($_POST['salirDonacion']) )
		{	
			$parValorPaisDoc = parValorPais("ES");
			//echo "<br><br>4 cTesorero:comprobarDonantePrevio_Socio:parValorPaisDoc: "; print_r($parValorPaisDoc);	  		

			if ($parValorPaisDoc['codError'] !== '00000')	
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorPaisDoc['codError'].": ".$parValorPaisDoc['errorMensaje']); 
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			}	
			else				
			{ $datosDonanteInicio = array();//Evita notices
					vComprobarDonantePrevio_SocioInc($tituloSeccion,$datosDonanteInicio,$parValorPaisDoc,$navegacion);				
			}
		}		
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*------------------------------- Fin comprobarDonantePrevio_Socio ---------------------------------------------------*/

/*-------------------------- Inicio anotarIngresoDonacion --------------------------------------------------------------
Llama a los distintos formularios para anotar los campos de las donaciones, según los casos,
y valida y graba los datos en la tabla DONACION. 

Posibles casos que se pueden dar al anotar una donación: 

1 - Donante nuevo e identificado (no socio), no registrado previamente como donante será:vAnotarIngresoDonacionNoEncontradoInc()
2 - Anónimo: el formulario correspondiente será vAnotarIngresoDonacionAnonimoInc()

3 -	Donante ya identificado por ser donante previo o socio y SÍ encontrado: vAnotarIngresoDonacionSiEncontradoInc(), 
				en caso de NO haber sido encontrado en la tabla MIEMBRO (caso de que sea socio) o en la tabla DONACION, 
				se le trata como "Donante nuevo e identificado (y no socio)" vAnotarIngresoDonacionNoEncontradoInc()

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionMenuInc.php (casos 1 y 2)
o desde vComprobarDonantePrevio_SocioInc.php, (caso 3)

LLAMA: modeloTesorero.php:insertarDonacion()
modelos/libs/arrayParValor.php:parValorPais(), parValoresDonacionConceptos()
modelos/libs/validarCamposTesorero.php:validarCamposAnotarIngresoDonacion()
vistas/tesorero/vAnotarIngresoDonacionAnonimoInc(),vAnotarIngresoDonacionNoEncontradoInc(),vAnotarIngresoDonacionSiEncontradoInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen 
algunas de las funciones que utiliza
----------------------------------------------------------------------------------------------------------------------*/
function anotarIngresoDonacion()
{
	//echo "<br><br>0-1 cTesorero:anotarIngresoDonacion:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:anotarIngresoDonacion:_POST1: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/libs/arrayParValor.php';	
		require_once './modelos/libs/validarCamposTesorero.php';
		require_once './vistas/tesorero/vAnotarIngresoDonacionAnonimoInc.php';		
		require_once './vistas/tesorero/vAnotarIngresoDonacionNoEncontradoInc.php';	
		require_once './vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ANOTAR DONACIONES ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al anotar el ingreso de una donación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:anotarIngresoDonacionMenu(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=anotarIngresoDonacionMenu")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=anotarIngresoDonacion";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Anotar donación";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:anotarIngresoDonacion:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ fin navegacion -------------------------------------------------		
		
		if (isset($_POST['salirDonacion']) || isset($_POST['siGuardarDatosDonacion'])) 
		{	
			if (isset($_POST['salirDonacion']))
			{ 
					$datosMensaje['textoComentarios'] = "Has salido sin haber registrado los datos de la donación";				
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}	
			else //=$_POST[siGuardarDatosDonacion]
			{			 
				$resValidarCamposForm = validarCamposAnotarIngresoDonacion($_POST);//validarCamposTesorero.php
				//echo "<br><br>2 cTesorero:anotarIngresoDonacion:resValidarCamposForm: ";print_r($resValidarCamposForm);
	
				if ($resValidarCamposForm['codError'] !== '00000')//serán errores lógicos
				{
					$parValoresDonacionConceptos = parValoresDonacionConceptos($resValidarCamposForm['datosFormDonacion']['CONCEPTO']['valorCampo']);

					if ($parValoresDonacionConceptos['codError'] !== '00000')	
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValoresDonacionConceptos['codError'].": ".$parValoresDonacionConceptos['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					}	
					else	// parValoresDonacionConceptos['codError'] == '00000'	
					{	
						if ($resValidarCamposForm['datosFormDonacion']['TIPODONANTE']['valorCampo'] == 'ANONIMO')
						{						
							vAnotarIngresoDonacionAnonimoInc($tituloSeccion,$resValidarCamposForm,$parValoresDonacionConceptos,$navegacion);
						}		
						else //$resValidarCamposForm['datosFormDonacion']['TIPODONANTE']['valorCampo'] !== 'ANONIMO'
						{	
								$parValorPaisDoc = parValorPais($resValidarCamposForm['datosFormDonacion']['CODPAISDOC']['valorCampo']);
															
								if ($parValorPaisDoc['codError'] !== '00000')	
								{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorPaisDoc['codError'].": ".$parValorPaisDoc['errorMensaje']);
										vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
								}	
								else	// $parValorPaisDoc['codError'] == '00000'	
								{	
										if ($resValidarCamposForm['datosFormDonacion']['encontrado']['valorCampo'] == 'SI')
										{ 																	
												vAnotarIngresoDonacionSiEncontradoInc($tituloSeccion,$resValidarCamposForm,$parValorPaisDoc,$parValoresDonacionConceptos, $navegacion);														
										}
										else // $resValidarCamposForm['datosFormDonacion']['encontrado']['valorCampo'] !== 'SI'
										{ 	
												if ($resValidarCamposForm['datosFormDonacion']['TIPODONANTE']['valorCampo'] == 'IDENTIFICADO-NO-SOCIO')
												{
													vAnotarIngresoDonacionNoEncontradoInc($tituloSeccion,$resValidarCamposForm,$parValorPaisDoc,$parValoresDonacionConceptos,$navegacion);													
												}
												else// (otros casos no debiera haber
												{//echo "<br><br>3 cTesorero:anotarIngresoDonacion:resValidarCamposForm:";//print_r($resValidarCamposForm);
												} 
										}//else $resValidarCamposForm['datosFormDonacion']['encontrado']['valorCampo'] !== 'SI'	
								}//else $parValorPaisDoc['codError'] == '00000'				
						}//else $resValidarCamposForm['datosFormDonacion']['TIPODONANTE']['valorCampo'] !== 'ANONIMO'			
					}//else	parValoresDonacionConceptos['codError'] == '00000'	
				}//$resValidarCamposForm['codError'] !=='00000') hay error	
				
				else //$resValidarCamposForm['codError']=='00000'
				{				
					if (isset($resValidarCamposForm['datosFormDonacion']['encontrado']))
					{unset($resValidarCamposForm['datosFormDonacion']['encontrado']);//evitar error al grabar,pues no es campo de tabla
					}
					
					$resInsertarDonacion = insertarDonacion($resValidarCamposForm);//en modeloTesorero.php, probado error
					//echo "<br><br>4 cTesorero:anotarIngresoDonacion:resinsertarDonacion: ";print_r($resInsertarDonacion);

					if ($resInsertarDonacion['codError'] !== '00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resInsertarDonacion['codError'].": ".$resInsertarDonacion['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);										
					}	
					else //$resInsertarDonacion['codError']=='00000'
					{	
							$datosMensaje['textoComentarios'] = "Se ha grabado la donación de ".mb_strtoupper($resValidarCamposForm['datosFormDonacion']['NOM']['valorCampo'])." ".
																																											 mb_strtoupper($resValidarCamposForm['datosFormDonacion']['APE1']['valorCampo']);		
																																												
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}					
				}//else $resValidarCamposForm['codError']=='00000'							
			}//$_POST[siGuardarDatosDonacion]
		}//if (isset($_POST['salirDonacion']) || isset($_POST['siGuardarDatosDonacion'])) 
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*------------------------------- Fin anotarIngresoDonacion ----------------------------------------------------------*/

/*------------------------------- Inicio mostrarIngresoDonacion  -------------------------------------------------------
Busca los datos en la tabla DONACION por el campo "$codDonacion",de una donación concreta para después mostrarlo en 
el formulario vistas/tesorero/vMostrarIngresoDonacionTesInc.php.
	
LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones--> VER (icono lupa))

LLAMA: modeloTesorero.php:buscarIngresoDonacion()
vistas/tesorero/vMostrarIngresoDonacionTesInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21.	En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las 
funciones que utiliza
----------------------------------------------------------------------------------------------------------------------*/
function mostrarIngresoDonacion()
{
	//echo "<br><br>0-1 cTesorero:mostrarIngresoDonacion:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:mostrarIngresoDonacion:_POST: ";print_r($_POST);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';
		require_once './vistas/tesorero/vMostrarIngresoDonacionTesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "MOSTRAR DATOS DE UNA DONACIÓN ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al anotar el ingreso de una donación. No se han podido mostrar los datos de una donación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";
		$nomScriptFuncionError = ' cTesorero.php:mostrarIngresoDonacion(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarIngresoDonacion";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar datos de una donación";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:mostrarIngresoDonacion:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ fin navegacion -------------------------------------------------	
		
		$codDonacion = $_POST['datosFormDonacion']['CODDONACION'];
			
		$reMostrarIngresoDonacion = buscarIngresoDonacion($codDonacion);	//en modeloTesorero.php, probado error		
		
		//echo "<br><br>2 cTesorero:mostrarIngresoDonacion:reMostrarIngresoDonacion: ";print_r($reMostrarIngresoDonacion);
		
		if ($reMostrarIngresoDonacion['codError'] !== '00000')
		{
				$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reMostrarIngresoDonacion['codError'].": ".$reMostrarIngresoDonacion['errorMensaje'].$reMostrarIngresoDonacion['textoComentarios']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}	
		else //$reIngresoDonacion['codError']=='00000')
		{
			//echo "<br><br>3 cTesorero:mostrarIngresoDonacion:reMostrarIngresoDonacion: ";print_r($reMostrarIngresoDonacion);
			
			vMostrarIngresoDonacionTesInc($tituloSeccion,$reMostrarIngresoDonacion['resultadoFilas'],$navegacion);
		}  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	 
}
/*--------------------------- Fin mostrarIngresoDonacion -------------------------------------------------------------*/

/*---------------------------- Inicio modificarIngresoDonacionTes ------------------------------------------------------
Está función es para usar la cuando después de anotar una donación se comprueba que se ha cometido un error que exige 
una rectificación. 

Se crea el formulario para modificar una donación previa anotada en la tabla DONACION, pero solo los 
se podría modificar datos referentes a los pagos cantidad, gastos, concepto, modo pago, fecha pago 
y observaciones, pero no los datos personales como NIF, email y otros,

Solo se permite modificar donaciones de año anterior y actual. (Por eso condición enero año anterior al actual)

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES:  Acciones-> MODIFICA (icono pluma))

LLAMA: modelos/libs/arrayParValor.php:parValoresDonacionConceptos()
modeloTesorero.php:buscarIngresoDonacion(),actualizarDonacion()
modelos/libs/validarCamposTesorero.php:validarCamposAnotarIngresoDonacion()
vistas/tesorero/vModificarIngresoDonacionIncTes.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas 
de las funciones que utiliza

2022 cambios en "CONCEPTO" en tabla DONACION. Ahora las distintas opciones de "$parValoresDonacionConceptos" 
están y vienen de la tabla "DONACIONCONCEPTOS" con valores como "COSTAS-MEDALLA-VIRGEN-MERITO-POLICIAL", 
VIII-CONGRESO-AILP-MADRID-2022, y otros que se puedan añadir mas adelante.
----------------------------------------------------------------------------------------------------------------------*/
function modificarIngresoDonacionTes()
{
	//echo "<br><br>0-1 cTesorero:modificarIngresoDonacionTes:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:modificarIngresoDonacionTes:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php'; 

		require_once './modelos/libs/arrayParValor.php';	//añado para	parValoresDonacionConceptos()
		require_once './modelos/libs/validarCamposTesorero.php';		
		require_once './vistas/tesorero/vModificarIngresoDonacionIncTes.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "MODIFICAR DATOS DE UNA DONACIÓN";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al anotar la modificación de una donación. No se han podido modificar los datos de una donación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:modificarIngresoDonacionTes(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ Inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cTesorero&accion=modificarIngresoDonacionTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Modificar datos donación ";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>2cTesorero:modificarIngresoDonacionTes:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ Fin navegacion -----------------------------------------------	
			
		if (isset($_POST['siGuardarDatosDonacion']) || isset($_POST['salirDonacion']))  
		{	
			$nomApe1 = $_POST['datosFormDonacion']['NOM'].' '.$_POST['datosFormDonacion']['APE1']; 
			
			if (isset($_POST['salirDonacion']))
			{
				$datosMensaje['textoComentarios'] = "No se han modificado los datos de la donación realizada por: ".$nomApe1;				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['siGuardarDatosDonacion']))
			{			
				$resValidarCamposForm = validarCamposAnotarIngresoDonacion($_POST);
				//echo "<br><br>4 cTesorero:modificarIngresoDonacionTes:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				if ($resValidarCamposForm['codError'] !== '00000')
				{			
     $parValoresDonacionConceptos = parValoresDonacionConceptos($resValidarCamposForm['datosFormDonacion']['CONCEPTO']['valorCampo']);//modelos/libs/arrayParValor.php
					
					if ($parValoresDonacionConceptos['codError'] !== '00000')	
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValoresDonacionConceptos['codError'].": ".$parValoresDonacionConceptos['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					}	
					else	// parValoresDonacionConceptos['codError'] == '00000'	
					{				  
   		 vModificarIngresoDonacionIncTes($tituloSeccion,$resValidarCamposForm,$parValoresDonacionConceptos,$navegacion);		   
					}					
				}													
				else //$resValidarCamposForm['codError']=='00000' 
				{				
					$resActDonacion = actualizarDonacion($resValidarCamposForm);//en modeloTesorero.php

					//echo "<br><br>5 cTesorero:modificarIngresoDonacionTes:resActDonacion: ";print_r($resActDonacion);			  
					
					if ($resActDonacion['codError'] !== "00000")
					{ 
						$datosMensaje['textoComentarios'] .=  "No se han modificado los datos de la donación realizada por: ".$nomApe1;	
      $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resActDonacion['codError'].": ".$resActDonacion['errorMensaje'].$resActDonacion['textoComentarios']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
					}		
					else //($resActDonacion['codError'] == '00000')
					{ 
							$datosMensaje['textoComentarios'] = "Se han modificado los datos de la donación realizada por: ".$nomApe1;	
						
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}		
				}//else $resValidarCamposForm['codError']=='00000' 
			}//else isset($_POST['siGuardarDatosDonacion'])		  
		}//if (isset($_POST['siGuardarDatosDonacion']) || isset($_POST['salirDonacion'])  
			
		else //!if (isset($_POST['siGuardarDatosDonacion']) || isset($_POST['salirDonacion']))  
		{
			$resDonacionActualizar = buscarIngresoDonacion($_POST['datosFormDonacion']['CODDONACION']);//en modeloTesorero.php
						
			//echo "<br><br>6-1 cTesorero:modificarIngresoDonacionTes:resDonacionActualizar: ";	print_r($resDonacionActualizar);

			if ($resDonacionActualizar['codError'] !== '00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDonacionActualizar['codError'].": ".$resDonacionActualizar['errorMensaje'].$resDonacionActualizar['textoComentarios']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
			} 
			elseif ($resDonacionActualizar['resultadoFilas']['datosFormDonacion']['FECHAINGRESO']['valorCampo'] < (date("Y")-1)."-01-01" )//condición enero año anterior al actual		
			{ 
					$datosMensaje['textoComentarios'] =  "No se permite modificar donaciones anteriores a la fecha: <b>".(date("Y")-1)."-01-01</b>";
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
			}			
			else //$resDonacionActualizar['codError']=='00000'
			{	
					$parValoresDonacionConceptos = parValoresDonacionConceptos($resDonacionActualizar['resultadoFilas']['datosFormDonacion']['CONCEPTO']['valorCampo']);		   
		
					//echo "<br><br>6-2 cTesorero:modificarIngresoDonacionTes:parValoresDonacionConceptos: ";	print_r($parValoresDonacionConceptos);

					if ($parValoresDonacionConceptos['codError'] !== '00000')	
					{ 
				   $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValoresDonacionConceptos['codError'].": ".$parValoresDonacionConceptos['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					}	
					else	// parValoresDonacionConceptos['codError'] == '00000'	
					{
					 //echo "<br><br>7 cTesorero:modificarIngresoDonacionTes:resDonacionActualizar: ";	print_r($resDonacionActualizar);	
						
					 vModificarIngresoDonacionIncTes($tituloSeccion,$resDonacionActualizar['resultadoFilas'],$parValoresDonacionConceptos,$navegacion);
		 	 }
			}   
		}//!if (isset($_POST['siGuardarDatosDonacion']) || isset($_POST['salirDonacion']))  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}	
/*---------------------------- Fin modificarIngresoDonacionTes -------------------------------------------------------*/

/*------------------------------- Inicio anularDonacionErroneaTes ------------------------------------------------------
Solo para casos de error: Se anulan  algunos campos de la fila correspondiente a una donación previa anotada en la 
tabla DONACION, ya que es un error o se ha introducido duplicada.

Se actualiza el campo CONTROLERROR ='ERROR-ANOTACION' para que no se muestre en los listados en lugar de eliminar 
físicamente esa fila que se perdería la secuencia de CODDONACION-  

El tesoreo puede introducir comentarios para el campo OBSERVACIONES, pero no valida el contenido de ese campo.

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES:  Acciones-> ELIMINAR (icono papelera))

LLAMA: modeloTesorero.php:buscarIngresoDonacion(),anularDonacionErronea()
vistas/tesorero/vAnularDonacionErroneaIncTes.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las 
funciones que utiliza
----------------------------------------------------------------------------------------------------------------------*/
function anularDonacionErroneaTes()
{
 //echo "<br><br>0-1 cTesorero:anularDonacionErroneaTes:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:anularDonacionErroneaTes:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{
		require_once './modelos/modeloTesorero.php'; 
		require_once './vistas/tesorero/vAnularDonacionErroneaIncTes.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		$datosMensaje['textoCabecera'] = "ELIMINAR UNA DONACIÓN ERRÓNEA ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al anotar el ingreso de una donación. No se han podido eliminar los datos de una donación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:anularDonacionErroneaTes(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=cTesorero&accion=anularDonacionErroneaTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar donación";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
		//echo "<br><br>2cTesorero:anularDonacionErroneaTes:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");  
		//------------ fin navegacion ----------------------------------------------	
			
					
		if (isset($_POST['siAnularDatosDonacion']) || isset($_POST['noAnularDatosDonacion']))  
		{	
			$nomApe1 = $_POST['datosFormDonacion']['NOM'].' '.$_POST['datosFormDonacion']['APE1']; 			
			
			if (isset($_POST['noAnularDatosDonacion']))
			{			
				$datosMensaje['textoComentarios'] = "No se han eliminado los datos de la donación de: ".$nomApe1;						
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
			}
			else //(isset($_POST['noAnularDatosDonacion']))
			{   		 
				$resEliminarDonacion = anularDonacionErronea($_POST['datosFormDonacion']);

				//echo "<br><br>3 cTesorero:anularDonacionErroneaTes:resEliminarDonacion: ";print_r($resEliminarDonacion);		
				
				if ($resEliminarDonacion['codError'] !== "00000" )
				{ $datosMensaje['textoComentarios'] .= "No se han eliminado los datos de la donación realizada por: ".$nomApe1;											
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resEliminarDonacion['codError'].": ".$resEliminarDonacion['errorMensaje']);     					
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
				}		
				else //($resEliminarDonacion['codError'] == '00000')
				{	
						$datosMensaje['textoComentarios'] = "Se han eliminado los datos de la donación errónea de: ".$nomApe1;				 
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				}
			}	//(isset($_POST['noAnularDatosDonacion']))	 				  
		}//isset($_POST['siAnularDatosDonacion']) || isset($_POST['noAnularDatosDonacion'])  
		else //!if (isset($_POST['siAnularDatosDonacion']) || isset($_POST['noAnularDatosDonacion'])  
		{
			$resDonacionEliminar = buscarIngresoDonacion($_POST['datosFormDonacion']['CODDONACION']);
							
			//echo "<br><br>4 cTesorero:anularDonacionErroneaTes:resDonacionEliminar: ";	print_r($resDonacionEliminar);
		
			if ($resDonacionEliminar['codError'] !== '00000')
			{     
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resDonacionEliminar['codError'].": ".$resDonacionEliminar['errorMensaje'].$resDonacionEliminar['textoComentarios']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
			}      
			else //$resDonacionEliminar['codError']=='00000'
			{//echo "<br><br>5 cTesorero:anularDonacionErroneaTes:resDonacionEliminar:";	print_r($resDonacionEliminar);	
			
					vAnularDonacionErroneaIncTes($tituloSeccion,$resDonacionEliminar['resultadoFilas'],$navegacion);
			}   
		}//!if if (isset($_POST['siAnularDatosDonacion']) || isset($_POST['noAnularDatosDonacion'])   
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')			
}	
/*---------------------------- Fin anularDonacionErroneaTes ----------------------------------------------------------*/

/*------------------------------- Inicio mostrarTotalesDonaciones ------------------------------------------------------
A partir de la tabla "DONACION" se forma y muestra una tabla "TOTALES DONACIONES". Decreciente por años. 

Entre otros campos incluye: 
Nº total donantes, 	Tipo de donante (socios, donantes identificados, anónimos), Modo de ingreso, 
Gastos donación, Total donaciones €,      

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (botón superior: Totales donaciones)

LLAMA: modeloTesorero.php:buscarTotalesAniosDonaciones()
vistas/tesorero/vTotalesDonacionesInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las 
funciones que utiliza
----------------------------------------------------------------------------------------------------------------------*/	
function mostrarTotalesDonaciones()
{
	//echo "<br><br>0-1 cTesorero:mostrarTotalesDonaciones:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:mostrarTotalesDonaciones:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './modelos/modeloTesorero.php'; 
		require_once './vistas/tesorero/vTotalesDonacionesInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "TOTALES DONACIONES POR AÑOS";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al  mostrar los totales donaciones. No se han podido mostrar los totales donaciones por años. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:mostrarTotalesDonaciones(). Error: ';	
		$tituloSeccion = "Tesorería";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarTotalesDonaciones";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Totales donaciones por años";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:mostrarTotalesDonaciones:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ Fin navegacion -------------------------------------------------

		$codAgrup = "%";$anioDonacion = "%";$tipoDonante = "%";//Todos
		
		$totalesAniosPagosDonaciones = buscarTotalesAniosDonaciones($codAgrup,$anioDonacion,$tipoDonante);//en modeloTesorero.php, se reciben totales pago y gastos con punto decimal
		//echo "<br><br>2 cTesorero:mostrarTotalesDonaciones:totalesAniosPagosDonaciones: ";print_r($totalesAniosPagosDonaciones);
		
		if ($totalesAniosPagosDonaciones['codError'] !== '00000')
		{									
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$totalesAniosPagosDonaciones['codError'].": ".$totalesAniosPagosDonaciones['errorMensaje'].
																																														$totalesAniosPagosDonaciones['textoComentarios']);     					
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
		}      
		else //$reCuotaSocioAnio['codError']=='00000'
		{$totalesAniosPagosDonaciones['navegacion'] = $navegacion;
		
		//echo "<br><br>3 cTesorero:mostrarTotalesDonaciones:totalesAniosPagosDonaciones: ";print_r($totalesAniosPagosDonaciones);			
			
			vTotalesDonacionesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$totalesAniosPagosDonaciones);//incluye number_format()para los campos que lo necesitan formatos concretos
		}	 	
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin mostrarTotalesDonaciones -----------------------------------------------------------*/

/*-------------------------- Inicio excelDonacionesTesorero ------------------------------------------------------------
A partir de la tabla "DONACION" se exporta a un fichero Excel, todas las donaciones de un año o todos, 
con la información individual de cada donación.
 
Permite elegir por año de donación, y por tipos de donates: Todos,Socios, Simpatizantes (no socios),Anónimos

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (botón superior: Exportar las donaciones a archivo Excel)

LLAMA: modeloTesorero.php:exportarDonacionesExcel()
vistas/tesorero/vExcelDonacionesTesoreroInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
               para formar el buffer de salida a excel utiliza "header()" y no puede haber ningúna salida delante.

PHP 7.3.21. Esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las funciones que utiliza
					

NOTA: Falta controlar el buffer para que vuelva el control, ahora solo se hace en caso de error en la consulta SQL											
----------------------------------------------------------------------------------------------------------------------*/
function excelDonacionesTesorero()
{
	//echo "<br><br>0-1 cTesorero:excelDonacionesTesorero:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:excelDonacionesTesorero:_POST: "; print_r($_POST);		
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './modelos/modeloTesorero.php'; 
		require_once './vistas/tesorero/vExcelDonacionesTesoreroInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "EXPORTAR LAS DONACIONES A EXCEL";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar las donaciones a archivo Excel. No se han podido exportar las donaciones. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:excelDonacionesTesorero(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		$resExportar['codError'] = '00000';	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=excelDonacionesTesorero";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar las cuotas de los socios";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{
				$datosMensaje['textoComentarios'] = "No se han exportado las donaciones a Excel";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{				
				$codAgrup = '%';	
				$anioDonacion = $_POST['datosDonaciones']['anioDonacionElegido'];
				$tipoDonante = $_POST['datosDonaciones']['TIPODONANTE'];				 
				
				$resExportarDonacionesExcel =	exportarDonacionesExcel($codAgrup,$anioDonacion,$tipoDonante);// en modeloTesorero.php	
				
				//echo "<br><br>1 cTesorero:excelDonacionesTesorero:resExportarDonacionesExcel: "; print_r($resExportarDonacionesExcel);	

				//----------- tratamiento errores ---------------------------------	
				if ($resExportarDonacionesExcel['codError'] !== '00000')	
				{ 					
						if ($resExportarDonacionesExcel['codError'] == '80001')//no hay filas error lógico
						{
							$datosMensaje['textoComentarios'] = " No se han encontrado donaciones para las opciones elegidas";
						}			
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': '.$resExportarDonacionesExcel['codError'].": ".$resExportarDonacionesExcel['errorMensaje'].
																																																$resExportarDonacionesExcel['textoComentarios']);	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);     			
				}			
			}	
		}//if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
			
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{				
				vExcelDonacionesTesoreroInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
		}
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*-------------------------- Fin excelDonacionesTesorero -------------------------------------------------------------*/

/*------------------------------- Inicio donacionConceptos -------------------------------------------------------------
Se obtienen los datos de los "Concepto de Donación" existentes partir de la tabla "DONACIONCONCEPTOS" 
para después formar un formulario tabla-lista  "DONACIÓN CONCEPTOS", y en el botón "Añadir Nuevo Concepto de Donación"

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (desde botón superior: "Mostrar y Añadir Conceptos de Donación")

LLAMA: modeloTesorero.php:buscarDonacionConceptos()
vistas/tesorero/vDonacionConceptosInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php
modeloEmail.php:emailErrorWMaster()
controladores/libs/cNavegaHistoria.php

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las 
funciones que utiliza
----------------------------------------------------------------------------------------------------------------------*/	
function donacionConceptos()
{
	//echo "<br><br>0-1 cTesorero:donacionConceptos:SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:donacionConceptos:_POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './modelos/modeloTesorero.php'; 
		require_once './vistas/tesorero/vDonacionConceptosInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "CONCEPTOS DE DONACIÓN";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al  mostrar los Conceptos de Donación. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:donacionConceptos(). Error: ';	
		$tituloSeccion = "Tesorería";	

		//------------ Inicio navegacion ----------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarDonaciones")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		else
		{$pagActual = 4;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=donacionConceptos";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Donación Conceptos";
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		//echo "<br><br>1 cTesorero:donacionConceptos:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");  
		//------------ Fin navegacion -------------------------------------------------
		
		$conceptoDonacion = "%";//Todos

		$arrDonacionConceptos = buscarDonacionConceptos($conceptoDonacion);//en modeloTesorero.php,																												
		
		//echo "<br><br>2 cTesorero:donacionConceptos:arrDonacionConceptos: ";print_r($arrDonacionConceptos);
		
		if ($arrDonacionConceptos['codError'] !== '00000')
		{									
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrDonacionConceptos['codError'].": ".$arrDonacionConceptos['errorMensaje'].
																																														$arrDonacionConceptos['textoComentarios']);     					
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						
		}      
		else //$reCuotaSocioAnio['codError']=='00000'
		{
		 //echo "<br><br>3 cTesorero:donacionConceptos:arrDonacionConceptos: ";print_r($arrDonacionConceptos);			
	
   vDonacionConceptosInc($tituloSeccion,$arrDonacionConceptos['resultadoFilas'],$navegacion);		
		}	 
	
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin donacionConceptos ------------------------------------------------------------------*/

/*-------------------------- Inicio aniadirDonacionConceptoTes  --------------------------------------------------------
- Se añade nuevo Concepto de Donación a la tabla "DONACIONCONCEPTOS", 
																										
LLAMADA: vistas/tesorero/vDonacionConceptosInc.php y previamente desde cTesorero.php:donacionConceptos() 

LLAMA: modelos/libs/validarCamposTesorero.php:validarCamposDonacionConcepto()
       modeloTesorero.php:insertarDonacionConceptos()
       modeloEmail.php:emailErrorWMaster()
							vistas/tesorero/vAniadirDonacionConceptoInc.php';
						 vistas/mensajes/vMensajeCabSalirNavInc.php
							controladores/libs/cNavegaHistoria.php
							
OBSERVACIONES: probada PHP 7.3.21
En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las funciones que utiliza								
----------------------------------------------------------------------------------------------------------------------*/								
function aniadirDonacionConceptoTes()
{ 
	//echo "<br><br>0-1 cTesorero:aniadirDonacionConceptoTes:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:aniadirDonacionConceptoTes:_POST: ";print_r($_POST);		
 	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';		
		require_once './modelos/libs/validarCamposTesorero.php';
		require_once './vistas/tesorero/vAniadirDonacionConceptoInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "AÑADIR CONCEPTOS DE DONACIÓN";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al añadir nuevo Concepto de Donación. Prueba de nuevo pasado un rato.  Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:aniadirDonacionConceptoTes(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=cTesorero&accion=donacionConceptos")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=aniadirDonacionConceptoTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Añadir Concepto Donación"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
	 //echo "<br><br>1 cTesorero:aniadirDonacionConceptoTes:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ fin navegacion -----------------------------------------------			
				
		if (isset($_POST['SICrearConceptoDonacion']) || isset($_POST['NOCrearConceptoDonacion'])) 
		{	 
			if (isset($_POST['NOCrearConceptoDonacion']))
			{
				$datosMensaje['textoComentarios'] = "No se ha añadido nuevo Concepto de Donación";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SICrearConceptoDonacion']))
			{									
    $resValidarDonacionConcepto = validarCamposDonacionConcepto($_POST);//en modeloTesorero.php			
				
				//echo "<br><br>2 cTesorero:aniadirDonacionConceptoTes:validarCampoDonacionConcepto: ";print_r($resValidarDonacionConcepto);				
				
 			//$resValidarDonacionConcepto['codError'] = "80001";//para probar errores

				if ($resValidarDonacionConcepto['codError'] !== "00000")
				{
					vAniadirDonacionConceptoInc($tituloSeccion,$resValidarDonacionConcepto,$navegacion);//en vistas/tesorero/vAniadirDonacionConceptoInc.php			
				}
				else //$resValidarDonacionConcepto['codError']=="00000"
				{  					
     $resAniadirDonacionConcepto = insertarDonacionConceptos($resValidarDonacionConcepto);//modeloTesorero.php, error ok    					
					
					//echo "<br><br>3 cTesorero:aniadirDonacionConceptoTes:resAniadirDonacionConcepto: ";print_r($resAniadirDonacionConcepto);

					if ($resAniadirDonacionConcepto['codError'] !== "00000")
					{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:insertarDonacionConceptos(): ".$resAniadirDonacionConcepto['textoComentarios'].
																																																		": ".$resAniadirDonacionConcepto['codError'].": ".$resAniadirDonacionConcepto['errorMensaje']);									
					}
					else// $resAniadirDonacionConcepto['codError'] == '00000'
					{	
							$datosMensaje['textoComentarios'] = $resAniadirDonacionConcepto['textoComentarios'].//"<br /><br />Proceso realizado sin errores detectados.".		
																																										"<br /><br /><br />Se ha añadido el Concepto de Donación <strong>".$resValidarDonacionConcepto['CONCEPTO']['valorCampo'].
																																										"</strong>
																																										<br /><br /><br />Ahora conviene comprobar que son correctos, y en caso de anomalías habría que modificarlo en la tabla en la BBDD ";					
					}
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					
				}//else $resValidarDonacionConcepto['codError']=="00000" 
				
			}//elseif isset($_POST['SICrearConceptoDonacion'])
		}//if (isset($_POST['SICrearConceptoDonacion']) || isset($_POST['NOCrearConceptoDonacion'])) 
			
		else//!(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
		{			
   $datosInicialesDonacionConcepto = array();//evita notices			
		
			vAniadirDonacionConceptoInc($tituloSeccion,$datosInicialesDonacionConcepto,$navegacion);//en vistas/tesorero/vAniadirDonacionConceptoInc.php		
				
		}//else//!(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar']))  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*------------------------------- Fin aniadirDonacionConceptoTes -----------------------------------------------------*/

/*============================ FIN GESTIÓN DONACIONES ================================================================*/




/*============================ INICIO GESTIÓN Cuotas Vigentes EL =======================================================
       - cuotasVigentesELTes(): CUOTAS VIGENTES PARA EL AÑO ACTUAL Y ACTUALIZAR LAS CUOTAS DEL AÑO PRÓXIMO
							- actualizarCuotasVigentesELTes(): ACTUALIZAR LAS CUOTAS VIGENTES DE EUROPA LAICA PARA EL PRÓXIMO AÑO 
							
======================================================================================================================*/

/*------------------------------- Inicio cuotasVigentesELTes  ----------------------------------------------------------
Se muestran en unas tablas los datos de las cuotas vigentes de EL para los socios en el año actual y en el año siguiente,
buscando en la tabla "IMPORTEDESCUOTAANIO". 
Desde el formulario donde se muestran se puede ir a la función de cambiar los importes de las cuotas anuales vigentes 
en EL para el año siguiente. 

Con función: cTesorero.php:actualizarCuotasVigentesELTes()
	
LLAMADA: desde Menú izquierdo: "Cuotas vigentes en EL"

LLAMA:  modeloSocios.php:buscarCuotasAnioEL($anioCuota,$codCuota), que sustituye a modeloTesorero.php:buscarCuotasEL() la función ya existía

        vistas/tesorero/vMostrarCuotasVigentesELTesInc.php
								modeloEmail.php:emailErrorWMaster()
								vistas/mensajes/vMensajeCabSalirNavInc.php
								controladores/libs/cNavegaHistoria.php
								
OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las 
funciones que utiliza.		

Ahora se utiliza la función: "modeloSocios.php:buscarCuotasAnioEL($anioCuota,$codCuota)", 
para sustuir a la función modeloTesorero.php:buscarCuotasEL() la función ya existía	y así se evita duplicidad ya que 
devolvía casi el mismo return.						
----------------------------------------------------------------------------------------------------------------------*/
function cuotasVigentesELTes()
{
	//echo "<br><br>0-1 cTesorero:cuotasVigentesELTes:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:cuotasVigentesELTes:_POST: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		//require_once './modelos/modeloTesorero.php';			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "CUOTAS VIGENTES PARA EL AÑO ACTUAL Y ACTUALIZAR LAS CUOTAS DEL AÑO PRÓXIMO	 ";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar las cuotas vigentes para el año actual. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:cuotasVigentesELTes(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	 		
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link']="index.php?controlador=cTesorero&accion=cuotasVigentesELTes";
		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace']="Cuotas vigentes año actual EL";			
		$_SESSION['vs_HISTORIA']['pagActual']=3;	
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion=cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//echo "<br><br>2 cTesorero:cuotasVigentesELTes:navegacion: ";print_r($navegacion);		
		//------------------------------------------------------------------------		 
		
		$anioCuota = '%';//todos los años 	para que incluya también año siguiente 	
		$codCuota  = '%';//todos tipos cuotas		

		//sustituida $reCuotasEL = buscarCuotasEL($anioCuota,$codCuota);//en modeloTesorero.php probado errores
		
		require_once './modelos/modeloSocios.php';	
		$reCuotasEL = buscarCuotasAnioEL($anioCuota,$codCuota);//busca cuotasEL para Y; en modeloSocios.php
		
		//echo "<br><br>3 cTesorero:cuotasVigentesELTes:reCadCuotasEL: ";print_r($reCuotasEL);

		if ($reCuotasEL['codError'] !== '00000')
		{
			if ($reCuotasEL['codError'] == '80001')//no hay filas
			{		
				$datosMensaje['textoComentarios'] .= $reCuotasEL['textoComentarios'];
			}			
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reCuotasEL['codError'].": ".$reCuotasEL['errorMensaje'].': '.$reCuotasEL['textoComentarios']);  
		
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);  
		}      
		else //$reCuotasEL['codError']=='00000'
		{//echo "<br><br>4 cTesorero:cuotasVigentesELTes:reCuotasEL: ";print_r($reCuotasEL);	
			
			//antes $arrCuotasVigentesAnioActualEL = $reCuotasEL['resultadoFilas']['ANIOCUOTA'][date('Y')];			
			$arrCuotasVigentesAnioActualEL = $reCuotasEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];
			
			//antes $arrCuotasVigentesAnioSiguienteEL = $reCuotasEL['resultadoFilas']['ANIOCUOTA'][date('Y')+1];
			$arrCuotasVigentesAnioSiguienteEL = $reCuotasEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')+1];
		
			require_once './vistas/tesorero/vMostrarCuotasVigentesELTesInc.php';		
			vMostrarCuotasVigentesELTesInc($tituloSeccion,$arrCuotasVigentesAnioActualEL,$arrCuotasVigentesAnioSiguienteEL,$navegacion);
		}	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*-------------------------- Fin cuotasVigentesELTes -----------------------------------------------------------------*/

/*-------------------------- Inicio actualizarCuotasVigentesELTes  ----------------------------------------------------
Desde el formulario se muestran los datos de las cuotas vigentes para el año siguiente 
y realiza las siguientes operaciones:

-Actualiza la tabla IMPORTEDESCUOTAANIO, para modificar el importe de la cuota anual de EL columna IMPORTECUOTAANIOEL
 por el nuevo importe que se recibe en $arrayDatos, procedente del formulario, para un año concreto ANIOCUOTA	
	y un tipo de cuota CODCUOTA concreto.
-Después, para cada socio que tenga datos en el año (Y+1) se actualiza en tabla CUOTAANIOSOCIO, el importe de la 
 cuota anual de EL IMPORTECUOTAANIOEL para el año ANIOCUOTA=(Y+1) y también se si cumple las condiones según la cuota 
	que tiene elegida el socio, se actualizaría el importe el campo "IMPORTECUOTAANIOSOCIO", si procede, al nuevo valor. 
 Normalmente condición es aumentar el importe IMPORTECUOTAANIOSOCIO < IMPORTECUOTAANIOEL (aumento de cuota) aunque 
	también podría ser bajada de cuota (poco probable)

Se muestra el resultado con número de cambios de cuotas de socios afectados y actualizadas para el año siguiente,
o mensaje de error.
																										
LLAMADA: /vistas/tesorero/vMostrarCuotasVigentesELTesInc.php:vMostrarCuotasVigentesELTesInc()

LLMAA: modelos/libs/validarCamposTesorero.php:validarCamposFormCuotasVigentesEL()
       modeloTesorero.php:actualizarCuotasVigentesEL()
       modeloEmail.php:emailErrorWMaster()
							vistas/tesorero/vCambiarCuotasVigentesELTesInc.php:vCambiarCuotasVigentesELTesInc()
						 vistas/mensajes/vMensajeCabSalirNavInc.php
							modeloEmail.php:emailErrorWMaster()
							controladores/libs/cNavegaHistoria.php
							
OBSERVACIONES: probada PHP 7.3.21
En esta función no se necesitan $arrBindValues para PDO, lo incluyen algunas de las funciones que utiliza								
----------------------------------------------------------------------------------------------------------------------*/								
function actualizarCuotasVigentesELTes()
{ 
	//echo "<br><br>0-1 cTesorero:actualizarCuotasVigentesELTes:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:actualizarCuotasVigentesELTes:_POST: ";print_r($_POST);		
 	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
 else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';		
		require_once './modelos/libs/validarCamposTesorero.php';
		require_once './vistas/tesorero/vCambiarCuotasVigentesELTesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "ACTUALIZAR LAS CUOTAS VIGENTES DE EUROPA LAICA PARA EL PRÓXIMO AÑO";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al actualizar las cuotas vigentes para el año actual. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:actualizarCuotasVigentesELTes(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=cTesorero&accion=cuotasVigentesELTes")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=actualizarCuotasVigentesELTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar cuotas vigentes EL"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ fin navegacion -----------------------------------------------			
				
		if (isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
		{	 
			if (isset($_POST['NoCambiar']))
			{
				$datosMensaje['textoComentarios'] = "No se han cambiado las cuotas actualmente vigentes para el próximo año en Europa Laica";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiCambiar']))
			{						
				$resValidarCuotasVigentesEL = validarCamposFormCuotasVigentesEL($_POST);//en modelos/libs/validarCamposTesorero.php
				
				//echo "<br><br>2 cTesorero:actualizarCuotasVigentesELTes:resValidarCamposVigentesEL: ";print_r($resValidarCuotasVigentesEL);				
		
				$datosCuotaEL = $resValidarCuotasVigentesEL;
					
				if ($resValidarCuotasVigentesEL['datosFormCuotasAnioSiguienteEL']['codError'] !== "00000")
				{
					vCambiarCuotasVigentesELTesInc($tituloSeccion,$datosCuotaEL,$navegacion);//en tesorero/vCambiarCuotasVigentesELTesInc.php
				
				}
				else //$resValidarCuotasVigentesEL['datosFormCuotasVigentesEL']['codError']=="00000"
				{					
					$resCambiarCuotasVigentesEL = actualizarCuotasVigentesEL($datosCuotaEL['datosFormCuotasAnioSiguienteEL']);//modeloTesorero.php, error ok    							
					
					//echo "<br><br>3 cTesorero:actualizarCuotasVigentesELTes:resCambiarCuotasVigentesEL: ";print_r($resCambiarCuotasVigentesEL);

					if ($resCambiarCuotasVigentesEL['codError'] !== "00000")
					{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:actualizarCuotasVigentesEL(): ".$resCambiarCuotasVigentesEL['textoComentarios'].
																																																		": ".$resCambiarCuotasVigentesEL['codError'].": ".$resCambiarCuotasVigentesEL['errorMensaje']);									
					}
					else// $resCambiarCuotasVigentesEL['codError'] == '00000'
					{	
							$datosMensaje['textoComentarios'] = $resCambiarCuotasVigentesEL['textoComentarios'].//"<br /><br />Proceso realizado sin errores detectados.".		
																																										"<br /><br /><br />Ahora conviene comprobar que los importes de las cuotas vigentes de EL son correctos, 
																																										también en el caso de que en este proceso se hayan actualizado cuotas de socios/as, y en caso de anomalías 
																																										restaurar la copia previa de la BBDD ";					
					}
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					
				}//else $resValidarCuotasVigentesEL['datosFormCuotasVigentesEL']['codError']=="00000" 
			}//elseif (isset($_POST['SiCambiar']))		
		}//if (isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
			
		else//!(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar'])) 
		{
			$datosCuotaEL['datosFormCuotasAnioSiguienteEL'] = $_POST['datosFormCuotasAnioSiguienteEL'];		
			
			//echo "<br><br>4 cTesorero:actualizarCuotasVigentesELTes:datosCuotaEL: ";print_r($datosCuotaEL);	
			
			vCambiarCuotasVigentesELTesInc($tituloSeccion,$datosCuotaEL,$navegacion);//en tesorero/vCambiarCuotasVigentesELTesInc.php
		}//else//!(isset($_POST['SiCambiar']) || isset($_POST['NoCambiar']))  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*------------------------------- Fin actualizarCuotasVigentesELTes --------------------------------------------------*/

/*============================ FIN GESTIÓN Cuotas Vigentes EL ========================================================*/


/**********************************************************************************************************************/
/**********************************************************************************************************************/

/*==== INICIO: FUNCIONES RELACIONADAS CON ÓRDENES COBRO CUOTAS BANCOS ==================================================
- menuOrdenesCobroCuotasTes(),infOrdenCobroCuotasDomiciliadas()
- Funciones "Estado órdenes cobro domiciliadas":descargarAchivoSEPAXML(),actualizarCuotasCobradasEnRemesaTes()...
- GENERAR ARCHIVO B.SANTANDER "SEPA-XML", "ARCHIVOS CUOTAS EXCEL BANCOS, ARCHIVOS CUOTAS EXCEL USO INTERNO
- Envío email próximo cobro cuota domiciliada, Envío email cuota no domiciliada aún NO pagada,
- Exportar lista Emails socios cuota domiciliada pendientes pagar cuota, 
- Exportar lista Emails socios cuota no domiciliada pendientes pagar cuota				

NOTA: podría hacer un fichero independiente con nombre similar a: cTesoreroCuotasBancos.php
======================================================================================================================*/

/*==== INICIO: FUNCIONES RELACIONADAS CON MENÚ e INFORMACIÓN SOBRE ÓRDENES COBRO CUOTAS BANCOS =========================
- menuOrdenesCobroCuotasTes(): GENERAR ARCHIVO "SEPA" ÓRDENES COBRO CUOTAS DOMICILIADAS Y OTRAS OPERACIONES RELACIONADAS 
- infOrdenCobroCuotasDomiciliadas():INFORMACIÓN SOBRE PROCEDIMIENTO ENVIAR B.SANTANDER ÓRDENES COBRO CUOTAS DOMICILIADAS 
======================================================================================================================*/

/*-------------------------- Inicio menuOrdenesCobroCuotasTes ----------------------------------------------------------
Llama al menú relacionado con las órdenes de cobro de cuotas domiciliadas:											

I- Enviar emails para avisos de próxima orden de cobro de cuota.

II- Generar archivo SEPA-XML con órdenes cobro y otras operaciones relacionadas
- Generar archivo SEPA_ISO20022CORE-XML con las órdenes cobro B.Santander e Insertar órdenes en tabla ORDENES_COBRO.
- Estado de las órdenes de cobro de cuotas domiciliadas: 
 .Ver órdenes de cobro, 
	.Eliminar remesa de ORDENES_COBRO, 
	.Descargar Archivo SEPA-XML,
	.Actualizar pagos remesa en CUOTA ANIO SOCIO 
- Exportar las órdenes de pago de cuotas a archivo Excel para trabajo y contrastar con archivo XML SEPA 
- Exportar las cuotas y otros datos de los socios/as a Excel para uso interno 

III- Exportar listas emails para avisos órdenes cobro cuotas par envío desde Nodo50

LLAMA: vista/tesorero/vMenuOrdenesCobroCuotasTesInc() y cNavegaHistoria
LLAMADA: desde el link sección izda, del la función correspondiente 
         del rol de Tesorero "-Órdenes cobro cuotas"

OBSERVACIONES:
2020-10-12: function menuOrdenesCobroCuotasTes() sustituye a menuExportarCuotasDonaTesorero() solo en nombre de 
función y también en la BBDD. Por compatibilidad con BBDD lo hago ahora.
----------------------------------------------------------------------------------------------------------------------*/
function menuOrdenesCobroCuotasTes() //sustituye a menuExportarCuotasDonaTesorero()
{
	//echo "<br><br>0-1 cTesorero:menuOrdenesCobroCuotasTes:SESSION: ";print_r($_SESSION);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		$tituloSeccion = "Tesorería";		
		
		//------------ inicio navegacion ------------------------------------------- 
		//$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cTesorero&accion=menuExportarCuotasDonaTesorero";//antes
		$_SESSION['vs_HISTORIA']['enlaces'][3]['link'] = "index.php?controlador=cTesorero&accion=menuOrdenesCobroCuotasTes";	

		$_SESSION['vs_HISTORIA']['enlaces'][3]['textoEnlace'] = "Órdenes cobro cuotas";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 3;	
		//echo "<br><br>1 cTesorero:menuOrdenesCobroCuotasTes:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);							
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------			
		
		require_once './vistas/tesorero/vMenuOrdenesCobroCuotasTesInc.php'; 	
		
		vMenuOrdenesCobroCuotasTesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin menuOrdenesCobroCuotasTes ----------------------------------------------------------*/

/*-------------------------- Inicio infOrdenCobroCuotasDomiciliadas ----------------------------------------------------
Llama a la vista que muestra la información sobre el procedimiento de enviar remesas al B. Santander 
con las órdenes de cobro de las cuotas domiciliadas.

Solo contiene texto con información

LLAMA: vista/tesorero/vInfOrdenCobroCuotasDomiciliadas.php.php y cNavegaHistoria
LLAMADA: vMenuOrdenesCobroCuotasTes.php() que se llama desde cTesorero.php:menuOrdenesCobroCuotasTes()

OBSERVACIONES: Fecha creación: Agustin 2020-11-28
----------------------------------------------------------------------------------------------------------------------*/
function infOrdenCobroCuotasDomiciliadas()
{
	//echo "<br><br>0-1 cTesorero:infOrdenCobroCuotasDomiciliadas:SESSION: ";print_r($_SESSION);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{				
		$tituloSeccion = "Tesorería";		
		
		//------------ inicio navegacion -------------------------------------------		
		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=infOrdenCobroCuotasDomiciliadas";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Información procedimiento órdenes cobro cuotas domiciliadas";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------	
		
		require_once './vistas/tesorero/vInfOrdenCobroCuotasDomiciliadas.php'; 	

		vInfOrdenCobroCuotasDomiciliadas($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')
}
/*--------------------------- Fin infOrdenCobroCuotasDomiciliadas ----------------------------------------------------*/

/*==== FIN: FUNCIONES RELACIONADAS CON ÓRDENES COBRO CUOTAS BANCOS =====================================================
- menuOrdenesCobroCuotasTes(),infOrdenCobroCuotasDomiciliadas()
======================================================================================================================*/


/*== INICIO: Funciones "Estado órdenes cobro domiciliadas en bancos": estadoOrdenesCobroRemesasTes() ===================

- estadoOrdenesCobroRemesasTes(): ESTADO DE LAS ÓRDENES DE COBRO DE CORRESPONDENDIENTES A REMESAS XML NORMA SEPA 
- mostrarOrdenesCobroUnaRemesaTes(): MOSTRAR ÓRDENES COBRO DE UNA REMESA 

- eliminarOrdenesCobroUnaRemesaTes():					- Eliminar remesa de tabla "ORDENES_COBRO" (error o anulación)  
- descargarAchivoOrdenesCobroSEPAXMLTes():     - Descargar Archivo SEPA de remesa: descargarAchivoOrdenesCobroSEPAXMLTes()		
- actualizarCuotasCobradasEnRemesaTes():     - Actualizar órdenes cobro en tablas CUOTAANIOSOCIO,ORDENES_COBRO,REMESAS_SEPAXML:actualizarCuotasCobradasEnRemesaTes()
   			
========================================================================================================*/


/*-------------------------- Inicio estadoOrdenesCobroRemesasTes -------------------------------------------------------
Se muestra el estado de las órdenes de cobro de las cuotas domiciliadas por banco, que están anotadas en las 
tablas "REMESAS_SEPAXML" y "ORDENES_COBRO" y se corresponden con las remesas de cobro, 	de las que se generó el 
correspondiente archivo XML norma SEPA para enviar al banco Santander.

Además de mostrar campos con información de la remesa, hay cuatro acciones disponibles: 	
-Ver (lupa), 
-Eliminar órdenes cobro	de esta remesa (papelera). 
-Descargar Archivo "SEPA_ISO20022CORE_fecha.xml" de remesa
-Actualizar los pagos de esta remesa en CUOTAANIOSOCIO (pluma),

Si el campo ANOTADO_EN_CUOTAANIOSOCIO = 'NO', indica que aún no se ha efectuado el cobro por el banco de las cuotas 
de la remesa (normalmente se efectuará en la fecha indicada en la columna "Fecha cobro cuota en Banco", 
pero se pudiera retrasar.
Cuando se compruebe que la remesa ha sido cobrada por el banco (se verá el ingreso en la cuenta de EL del B. Santander),
ese mismo día se DEBE ejecutar la opción "Actualizar los pagos de esta remesa en CUOTAANIOSOCIO". 	

Esa acción anotará la cuota como pagada por domiciliación en la tabla "CUOTAANIOSOCIO" y pondrá el 
campo ANOTADO_EN_CUOTAANIOSOCIO="SI", y ya no se podrá "Actualizar los pagos de esta remesa en "CUOTAANIOSOCIO" 
ni "Eliminar órdenes cobro de esta remesa (papelera)" 

La acción "Eliminar órdenes cobro de esta remesa (papelera)", solo se utilizará para casos de error o cancelación en 
la remesa antes de efectuar el pago de la remesa.
Solo se permite esta opción si en la tabla "ORDENES_COBRO" el campo ANOTADO_EN_CUOTAANIOSOCIO = 'NO',	

La acción "Descargar Archivo SEPA_ISO20022CORE_fecha.xml" de remesa, sólo estará disponible hasta 	se ejecute la acción 
"Actualizar los pagos de remesa en CUOTA ANIO SOCIO", o la acción "Eliminar órdenes cobro de remesa (error o anulada)"

(Las devoluciones se irán anotando en la tabla "CUOTAANIOSOCIO"  día a  día según se vayan produciendo en la opción del 
menú "Cuotas Socios/as")	
    
LLAMADA:cTesorero.php:menuOrdenesCobroCuotasTes() y vistas/tesorero/vMenuOrdenesCobroCuotasTesInc.php
LLAMA: modeloTesorero.php:buscarDatosRemesasOrdenesCobro(), y desde el formulario en acciones en 
cTesosero.php: mostrarOrdenesCobroUnaRemesaTes(), eliminarOrdenesCobroUnaRemesaTes(),
descargarAchivoOrdenesCobroSEPAXMLTes(), mActualizarCuotasCobradasEnRemesaTes()

OBSERVACIONES:
2020-12-12: probada PHP 7.3.21
----------------------------------------------------------------------------------------------------------------------*/
function estadoOrdenesCobroRemesasTes()
{
	//echo "<br><br>0-1 cTesorero:estadoOrdenesCobroRemesasTes:_SESSION: ";print_r($_SESSION); 
	//echo "<br><br>0-2 cTesorero:estadoOrdenesCobroRemesasTes:POST: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once "./modelos/modeloTesorero.php";			
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';

		$datosMensaje['textoCabecera'] = "ESTADO DE LAS REMESAS Y ÓRDENES DE COBRO DE LAS CUOTAS DOMICILIADAS"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al mostrar la tabla de estado de órdenes cobro de cuotas domiciliadas enviadas la banco y operaciones relacionadas. 
																																							No se ha podido mostrar la tabla de estado de órdenes cobro de cuotas domiciliadas enviadas la banco.                                      
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:estadoOrdenesCobroRemesasTes(). Error: ';	
		$tituloSeccion = "Tesorería";	

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=estadoOrdenesCobroRemesasTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Estado órdenes cobro remesas y operaciones"; 	
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;						
		//echo "<br><br>2 cTesorero:estadoOrdenesCobroRemesasTes:_SESSION['vs_HISTORIA']: ";print_r($_SESSION['vs_HISTORIA']);
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------

		$arrOrdenesCobroRemesa = buscarDatosRemesasOrdenesCobro();	// en modeloTesorero.php. Probado error OK
		
		//echo "<br><br>4 cTesorero:estadoOrdenesCobroRemesasTes:arrOrdenesCobroRemesa: ";print_r($arrOrdenesCobroRemesa);

		if ($arrOrdenesCobroRemesa['codError'] !== '00000')
		{				
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrOrdenesCobroRemesa['codError'].": ".$arrOrdenesCobroRemesa['errorMensaje'],$arrOrdenesCobroRemesa['arrMensaje']['textoComentarios']);
			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}      
		else //$arrOrdenesCobroRemesa['codError']=='00000'
		{//echo "<br><br>5 cTesorero:estadoOrdenesCobroRemesasTes:arrOrdenesCobroRemesa['resultadoFilas']:";print_r($arrOrdenesCobroRemesa['resultadoFilas']);	

			require_once './vistas/tesorero/vEstadoOrdenesCobroRemesasTesInc.php';		
			vEstadoOrdenesCobroRemesasTesInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$arrOrdenesCobroRemesa['resultadoFilas']);			
		}	
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin estadoOrdenesCobroRemesasTes -------------------------------------------------------*/

/*--------------------------- Inicio mostrarOrdenesCobroUnaRemesaTes ---------------------------------------------------

Se forma y muestra una tabla-lista páginada "MOSTRAR ÓRDENES COBRO DE UNA REMESA" con los detalles de las órdenes de 
cobro individuales correspondientes a una remesa concreta (pendiente de enviar, enviada o actualizada). Y un Link "Ver" 
para ver los datos del socio correspondiente a cada orden de cobro individual.
Se mostrarán y ordenadas por APELLIDOS.  

Incluye un botón para elegir, ESTADO CUOTA (por defecto el Todos) y AGRUPACION, y otro botón para elegir por APE1, APE2

RECIBE: 1ª vez:$_POST["datosFormOrdenCobroRemesa"] desde vistas/tesorero/formEstadoOrdenesCobroRemesasTes
suscesivas: form: fomOrdenesCobroUnaRemesa.php, y además utilizará $_SESSION variados.

LLAMADA: cTesorero:estadoOrdenesCobroRemesasTes() Acción "Ver"

LLAMA:
controladores/libs/cTesoreroOrdenesCobroUnaRemesaPaginar.php:cTesoreroOrdenesCobroUnaRemesaPaginar():para función paginar
modelos/libs/mPaginarLib.php
modeloEmail.php:emailErrorWMaster(), vistas/mensajes/vMensajeCabSalirNavInc.php

OBSERVACIONES: 	
2021-06-09: Probada PDO y PHP 7.3.21
----------------------------------------------------------------------------------------------------------------------*/
function mostrarOrdenesCobroUnaRemesaTes()
{
	//echo "<br><br>0-1 cTesoreroFUNCION:mostrarOrdenesCobroUnaRemesaTes:SESSION: ";print_r($_SESSION);	
	//echo "<br><br>0-2 cTesorero:mostrarOrdenesCobroUnaRemesaTes:_POST:" ; print_r($_POST); 
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{				
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/modeloEmail.php';
		require_once './vistas/tesorero/vMostrarOrdenesCobroUnaRemesaInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './controladores/libs/cNavegaHistoria.php';	
		
		$datosMensaje['textoCabecera'] = 'MOSTRAR ÓRDENES COBRO DE UNA REMESA ';	
		$datosMensaje['textoComentarios'] = "<br /><br />Error en el sistema, no se ha podido mostrar el 'Las órdenes de cobro de una remesa'. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org";																																						
		$nomScriptFuncionError = ' cTesorero.php:mostrarOrdenesCobroUnaRemesaTes(). Error: ';
		$tituloSeccion = "Tesorería";	
		
		$resListaOrdenesCobroRemesa['codError'] = '00000';
		$resListaOrdenesCobroRemesa['errorMensaje'] ='';

		//------------ Inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];	
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=estadoOrdenesCobroRemesasTes")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
				$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=mostrarOrdenesCobroUnaRemesaTes";	
				$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Mostrar órdenes cobro remesa";
		}	
		elseif(($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio")	)
		{
				$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = NULL;//para que no aparezca en navegación lo de mostrarIngresoCuotaAnio
		}
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//echo "<br><br>0-3 cTesorero:mostrarIngresosCuotas:navegacion: ";print_r($navegacion);	
		//------------ Fin navegacion -----------------------------------------------		

		/*Se inicia "$codAreaCoordinacion" y otras variables para utilizarlas dentro
				de require_once './controladores/libs/cTesoreroOrdenesCobroUnaRemesaTesIncTES.php'  		
		*/
		$codAreaCoordinacion = '%';	//Este rol de tesorero tiene acceso a todas las agrupaciones (Tesorero gestiona todas)

		/*--- En la Función : cTesoreroOrdenesCobroUnaRemesaPaginar() hay mucho dentro, 
			se crean las cadSelect "$_pagi_sql", $arrBindValues,... 
		(que a su vez se forman en modeloTesorero:cadBuscarOrdenesCobroRemesaApeNom,...)	
			que serán necesarios para la función siguiente "mPaginarLib()	
			y también $datosFormMiembro, si se busca por APE, APE2 --------------------*/
		require_once './controladores/libs/cTesoreroOrdenesCobroUnaRemesaPaginar.php';//es una función 	
		$arrDatosCadBuscarOrdenesCobro = cTesoreroOrdenesCobroUnaRemesaPaginar($_POST,$codAreaCoordinacion);
		
		//echo 	"<br><br>1-0 cTesorero:mostrarOrdenesCobroUnaRemesaTes:arrDatosCadBuscarOrdenesCobro: ";print_r($arrDatosCadBuscarOrdenesCobro);	

		if ($arrDatosCadBuscarOrdenesCobro['codError'] !== '00000')//=80001, solo error lógico por falta de datos en campo APE
		{		
			$resListaOrdenesCobroRemesa = $arrDatosCadBuscarOrdenesCobro;
		}
		else
		{	
			$_pagi_sql = $arrDatosCadBuscarOrdenesCobro['cadSQL'];//contiene la cadena de la select asignada	
			$arrBindValues = $arrDatosCadBuscarOrdenesCobro['arrBindValues']; //contiene el array correspondiente a la select asignada	
			$_pag_propagar_opcion_buscar = $arrDatosCadBuscarOrdenesCobro['pag_propagar_opcion_buscar'];//buscar por APE, o por AGRUPACION 		

			$_pagi_cuantos = 8;
			$_pagi_nav_num_enlaces = 14;//ojo al cambiar a 17 me dió error
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
			$resListaOrdenesCobroRemesa = mPaginarLib($_pagi_sql,$_pagi_cuantos,$_pagi_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pagi_mostrar_errores,$conexionLink,$arrBindValues);//probado error				
		}
		//echo "<br><br>4-2 cTesorero:cTesorero:mostrarOrdenesCobroUnaRemesaTes:resListaOrdenesCobroRemesa: ";print_r($resListaOrdenesCobroRemesa); 
		
		if ($resListaOrdenesCobroRemesa['codError'] !== '00000' && $resListaOrdenesCobroRemesa['codError'] !== '80001')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resListaOrdenesCobroRemesa['codError'].": ".$resListaOrdenesCobroRemesa['errorMensaje']);			
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
		}
		else //if ($resListaOrdenesCobroRemesa['codError'] == '00000' || $resListaOrdenesCobroRemesa['codError'] == '80001') 
		{		
			$codAgrup = '%';//Se busca y genera array de todas las agrupaciones %, para listar formulario en un combobox para mostrar y elegir		
			$valorDefectoCodAgrup = $arrDatosCadBuscarOrdenesCobro['codAgrupacion'];//para mostrar en cabecera de Agrupación		

			require_once './modelos/libs/arrayParValor.php';
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefectoCodAgrup);
			//echo "<br><br>5 cTesorero:mostrarOrdenesCobroUnaRemesaTes:parValorCombo: ";print_r($parValorCombo);		
			
			if ($parValorCombo['codError'] !== '00000')//probado error
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);			
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);									
			}	
			else //Si no errores en $parValorCombo 
			{				 
				$resListaOrdenesCobroRemesa['navegacion'] = $navegacion;	
				$resListaOrdenesCobroRemesa['ESTADOCUOTA'] = $arrDatosCadBuscarOrdenesCobro['estadoCuota'];//para mostrar en cabecera de Agrupación			
				$datosFormMiembro = $arrDatosCadBuscarOrdenesCobro['datosFormMiembro'];//para mostrar APE en cabecera formulario		
				$datosRemesa = $arrDatosCadBuscarOrdenesCobro['datosRemesa'];//para mostrar en cabecera de formulario, datos generales remesa: Archivo Remesa,Fecha orden cobro,Fecha pago	
		
				//echo "<br><br>6-1cTesorero:mostrarOrdenesCobroUnaRemesaTes:_SESSION['vs_enlacesSeccIzda']: ";print_r($_SESSION['vs_enlacesSeccIzda']);
				//echo "<br><br>6-2 cTesorero:mostrarOrdenesCobroUnaRemesaTes:resListaOrdenesCobroRemesa['navegacion']: ";print_r($resListaOrdenesCobroRemesa['navegacion']);
				//echo "<br><br>6-3 cTesorero:mostrarOrdenesCobroUnaRemesaTes:resListaOrdenesCobroRemesa['ESTADOCUOTA']: ";print_r($resListaOrdenesCobroRemesa['ESTADOCUOTA']);
				//echo "<br><br>6-4 cTesorero:mostrarOrdenesCobroUnaRemesaTes:datosFormMiembro: ";print_r($datosFormMiembro);	
				//echo "<br><br>6-5 cTesorero:mostrarOrdenesCobroUnaRemesaTes:datosRemesa: ";print_r($datosRemesa);				
								
				/*$datosFormMiembro contendrá los datos APE1 o APE2 de ese socio si se busca por APE1 o APE2
						si no será $datosFormMiembro ='', este valor se forma en cTesoreroCuotasSociosApeNomPaginarInc.php
				*/
				vMostrarOrdenesCobroUnaRemesaInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$resListaOrdenesCobroRemesa,$parValorCombo,$datosFormMiembro,$datosRemesa);	
			}		
		}//$resListaOrdenesCobroRemesa['codError']=='00000'
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*--------------------------- Fin mostrarOrdenesCobroUnaRemesaTes ----------------------------------------------------*/

/*-------------------------- Inicio eliminarOrdenesCobroUnaRemesaTes  --------------------------------------------------
Solo en el caso de error en la remesa o bien que fuese anulada o no procesada por el banco, se eliminan las órdenes 
de cobro anotadas en tablas "REMESAS_SEPAXML" y "ORDENES_COBRO" generadas para una remesa SEPA XML y que tienen el 
campo ANOTADO_EN_CUOTAANIOSOCIO ='NO'.
A la vez se eliminará el archivo de orden de cobro de esa remesa que se creó y guardó en el servidor de esta aplicación
 de Gestión de Soci@s.

Para buscar las órdenes de cobro de esa remesa en las tablas "ORDENES_COBRO" y "REMESAS_SEPAXML" y se utiliza el 
campo "NOMARCHIVOSEPAXML" que es el nombre archivo de orden de cobro de esa remesa SEPA XML generado y subido a la web
del banco para su cobro, de nombre similar "SEPA_ISO20022CORE_2021_03_03H08_43_36.xml"																										
		
RECIBE: $_POST["datosFormOrdenCobroRemesa"] desde "formEstadoOrdenesCobroRemesasTes", que contienen 
['NOMARCHIVOSEPAXML'] y ['DIRECTORIOARCHIVOREMESA'] entre otros campos
		
LLAMADA: cTesorero.php:estadoOrdenesCobroRemesasTes(), 
       y /vistas/tesorero/:vEstadoOrdenesCobroRemesasTes.php

LLLAMA: modeloTesorero.php:mEliminarOrdenesCobroUnaRemesa()
vistas/tesorero/vEliminarOrdenesCobroUnaRemesaTesInc.php
modeloEmail.php:emailErrorWMaster(), vistas/mensajes/vMensajeCabSalirNavInc.php
							
OBSERVACIONES: probado PHP 7.3.21
----------------------------------------------------------------------------------------------------------------------*/								
function eliminarOrdenesCobroUnaRemesaTes()
{	
	//echo "<br><br>0-1 cTesorero:eliminarOrdenesCobroUnaRemesaTes:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:eliminarOrdenesCobroUnaRemesaTes:POST: ";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';		
		
		$datosMensaje['textoCabecera'] = "ELIMINAR UNA REMESA AÚN NO ENVIADA AL BANCO DE LAS TABLAS REMESAS_SEPAXML Y ORDENES_COBRO"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al eliminar las órdenes cobro de una remesa de las tablas REMESAS_SEPAXML y ORDENES_COBRO 
																																							y el correspondiente archivo del servidor.
																																							<br /><br />No se ha podido descargar el achivo de órdenes de cobro. Prueba de nuevo pasado un rato. 
																																							<br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = " cTesorero.php:eliminarOrdenesCobroUnaRemesaTes(). Error: ";	
		$tituloSeccion = "Tesorería";	
		
		$descargarAchivoRemesa['codError'] = '00000';
		$descargarAchivoRemesa['errorMensaje'] = '';	
		
		//------------ Inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];	
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=estadoOrdenesCobroRemesasTes")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=eliminarOrdenesCobroUnaRemesaTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Eliminar una remesa de tablas ORDENES_COBRO"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ Fin navegacion -----------------------------------------------	
				
		if (isset($_POST['SiEliminarOrdenesCobro']) || isset($_POST['NoEliminarOrdenesCobro'])) 
		{//echo "<br><br>3 cTesorero:eliminarOrdenesCobroUnaRemesaTes:_POST: ";print_r($_POST); 
			
			if (isset($_POST['NoEliminarOrdenesCobro']))
			{
				$datosMensaje['textoComentarios'] = "No se han eliminado las órdenes de cobro de la remesa de las tablas REMESAS_SEPAXML y ORDENES_COBRO ni el archhivo SEPA-XML correspondiente";				 	
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['SiEliminarOrdenesCobro']))
			{	
					require_once './modelos/modeloTesorero.php';
					$resEliminarOrdenesCobroUnaRemesasTes = mEliminarOrdenesCobroUnaRemesa($_POST['datosFormOrdenCobroRemesa']['DIRECTORIOARCHIVOREMESA'],$_POST['datosFormOrdenCobroRemesa']['NOMARCHIVOSEPAXML']);					
								
					//echo "<br><br>4-1 cTesorero:eliminarOrdenesCobroUnaRemesaTes:resEliminarOrdenesCobroUnaRemesasTes: ";print_r($resEliminarOrdenesCobroUnaRemesasTes);
				
					if ($resEliminarOrdenesCobroUnaRemesasTes['codError'] !== "00000")
					{//echo "<br><br>4-2 cTesorero:eliminarOrdenesCobroUnaRemesaTes:resEliminarOrdenesCobroUnaRemesasTes:";print_r($resEliminarOrdenesCobroUnaRemesasTes);
											
						$datosMensaje['textoComentarios']	= $resEliminarOrdenesCobroUnaRemesasTes['arrMensaje']['textoComentarios'];	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			
						$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.': '.$resEliminarOrdenesCobroUnaRemesasTes['arrMensaje']['textoComentarios']);		
					}		
					else //$resEliminarOrdenesCobroUnaRemesasTes['codError'] =="00000"
					{//echo"<br><br>5 cTesorero:eliminarOrdenesCobroUnaRemesaTes:resEliminarOrdenesCobroUnaRemesasTes:";print_r($resEliminarOrdenesCobroUnaRemesasTes);

						$datosMensaje['textoComentarios']	= $resEliminarOrdenesCobroUnaRemesasTes['arrMensaje']['textoComentarios'];	
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		  
					} //else $resEliminarOrdenesCobroUnaRemesasTes['codError'] =="00000"

			}//elseif (isset($_POST['SiEliminarOrdenesCobro']))		
		}//if (isset($_POST['SiEliminarOrdenesCobro']) || isset($_POST['NoEliminarOrdenesCobro'])) 
			
		else//!(isset($_POST['SiEliminarOrdenesCobro']) || isset($_POST['NoEliminarOrdenesCobro'])) 
		{  
			if (isset($_POST["datosFormOrdenCobroRemesa"]) && !empty($_POST["datosFormOrdenCobroRemesa"])) 
			{    
					$arrayDatosFormOrdenCobroRemesa = unserialize(stripslashes($_POST["datosFormOrdenCobroRemesa"]));//Obtenemos el array pasado por post
			}
			
			//echo "<br><br>7 cTesorero:eliminarOrdenesCobroUnaRemesaTes:arrayDatosFormOrdenCobroRemesa: ";print_r($arrayDatosFormOrdenCobroRemesa);
			
			$enlacesFuncionRolSeccId ='';
			
			require_once './vistas/tesorero/vEliminarOrdenesCobroUnaRemesaTesInc.php';  	 	
			vEliminarOrdenesCobroUnaRemesaTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$arrayDatosFormOrdenCobroRemesa);				
			
		}//else//!(isset($_POST['SiEliminarOrdenesCobro']) || isset($_POST['NoEliminarOrdenesCobro'])) 
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
//------------------------------- Fin eliminarOrdenesCobroUnaRemesaTes -----------------------------------------------*/

/*------------------------ Inicio descargarAchivoOrdenesCobroSEPAXMLTes  -----------------------------------------------
Se descarga el archivo SEPA_ISO20022CORE_fecha.xml	de una remesa de órdenes de cobro de cuotas domiciliadas, 
para que la persona de tesorería autorizada lo exporte a la web del B. Santander para para que después dos personas 
autorizadas firmen la orden de cobro 															

RECIBE: El POST con el nombre del archivo de la remesa y directorio del servidor a descargarlo	
DEVUELVE: la descarga del archivo XML con las órdenes de cobro para B.Santander		
																							
LLAMADA: cTesorero.php:estadoOrdenesCobroRemesasTes(), 
         desde y /vistas/tesoro/:vEstadoOrdenesCobroRemesasTes.php

LLAMA: modeloTesorero.php:descargarAchivoSEPAXML(), que descarga archivo valida errores 
      (Esta función se podría incluir dentro de modeloArchivos.php, pero para mayor indenpendencia y seguridad frente 
						 a posibles cambios, la dejo en modeloTesorero.php, para uso exclusivo ya que es una función muy crítica.       
							vistas/mensajes/vMensajeCabSalirNavInc.php.
							
							modeloEmail.php:emailErrorWMaster()
							
OBSERVACIONES: Probado PHP 7.3.21. No usa PDO
----------------------------------------------------------------------------------------------------------------------*/		
function descargarAchivoOrdenesCobroSEPAXMLTes()
{	
	//echo "<br><br>0-1 cTesorero:descargarAchivoOrdenesCobroSEPAXMLTes:SESSION: ";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:descargarAchivoOrdenesCobroSEPAXMLTes:POST: ";print_r($_POST);
	//echo "<br><br>0-3 cTesorero:descargarAchivoOrdenesCobroSEPAXMLTes:_SERVER: ";print_r($_SERVER);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './modelos/modeloEmail.php';		
		
		$datosMensaje['textoCabecera'] = "DESCARGAR ARCHIVO REMESA CON ÓRDENES DE COBRO DE CUOTAS SOCIOS/AS PARA ENVÍAR AL BANCO"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al descargar archivo de remesa con órdenes de cobro de las cuotas domiciliadas de socios/as a archivo SEPA XML para B. Santander. 
																																							No se ha podido descargar el archivo de órdenes de cobro.
																																							<br /><br />Comprueba el ESTADO DE LAS ÓRDENES DE COBRO CORRESPONDENDIENTES A LAS REMESAS (por si hubiese que anular el registro).
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = " cTesorero.php:descargarAchivoOrdenesCobroSEPAXMLTes(). Error: ";	
		$tituloSeccion = "Tesorería";	
		
		$descargarAchivoRemesa['codError'] = '00000';
		$descargarAchivoRemesa['errorMensaje'] = '';
			
		//------------ Inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=estadoOrdenesCobroRemesasTes")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=descargarAchivoOrdenesCobroSEPAXMLTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Descargar archivo SEPA XML órdenes cobro de una remesa"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
			
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ Fin navegacion -----------------------------------------------	

		$nomArchivo = $_POST['NOMARCHIVOSEPAXML'];		//$nomArchivo será parecido a SEPA_ISO20022CORE_2021_03_02H20_08_38.xml	
		
		$directorioArchivoRemesa = $_POST['DIRECTORIOARCHIVOREMESA'];
		//$directorioArchivoRemesa será "/../upload/TESORERIA/SEPAXML_ISO20022";//ok directorio no público, solo se puede acceder por programa php,	no se puede por href	

		require_once './modelos/modeloTesorero.php';	
		$descargarAchivoRemesa = descargarAchivoSEPAXML($directorioArchivoRemesa ,$nomArchivo);//trata errores OK	 
	
		//echo "<br><br>1 cTesorero:descargarAchivoOrdenesCobroSEPAXMLTes:descargarAchivoRemesa: ";print_r($descargarAchivoRemesa);

		if ($descargarAchivoRemesa['codError'] !== "00000")
		{
			$datosMensaje['textoComentarios'] .= $descargarAchivoRemesa['errorMensaje'] ;//u otro mensaje 		
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$descargarAchivoRemesa['codError'].": ".
			                                          $descargarAchivoRemesa['errorMensaje'].$descargarAchivoRemesa['textoComentarios']);		
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
		}
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
//---------------------------- Fin descargarAchivoOrdenesCobroSEPAXMLTes ---------------------------------------------*/

/*-------------------------- Inicio actualizarCuotasCobradasEnRemesaTes  -----------------------------------------------
.Se actualizan los pagos de las cuotas en la tabla "CUOTAANIOSOCIO" (ESTADOCUOTA = ABONADA y otros campos), a partir de 
 las órdenes de cobro anotadas en la tabla "ORDENES_COBRO" correspondientes a una remesa SEPA XML y que tienen el 
 campo ANOTADO_EN_CUOTAANIOSOCIO ='NO'.
.Se actualizan las correspondiente filas en tabla "ORDENES_COBRO" de la remesa SEPA XML (ANOTADO_EN_CUOTAANIOSOCIO ='SI'
 y otros campos.
.En tabla "SOCIO" el campo se pone a SECUENCIAADEUDOSEPA = 'RCUR' si es el mismo IBAN 
.Se actualizan varios campos de esa remesa en SEPA-XML en la tabla "REMESAS_SEPAXML" (IMPORTEGASTOSREMESA,FECHAPAGO,
 ANOTADO_EN_CUOTAANIOSOCIO ='SI',FECHAANOTACIONPAGO)
.Se elimina el archivo de esta remesa que se envió al banco "NOMARCHIVOSEPAXML" del servidor una vez actualizas las 
 tablas antes citadas 

Para buscar las órdenes de cobro de esa remesa en "ORDENES_COBRO" se utiliza el campo "NOMARCHIVOSEPAXML" ya que es que 
ese el nombre del archivo SEPA XML generado y subido a la web del banco para su cobro. 

Al final, se generan los datos para mostrar un listado, con el número de actualizaciones en cada tabla, y las 
variaciones que se hayan pódido producir y que afecten a la remesa: Bajas, Pagos por otros medios, Cambios de cuotas, 
Cambios de Cuenta IBAN.

Se envía un email a "tesoreria@europalaica.org" con los resultados e incidencias producidas y errores si se 
hubiesen producido.
Se tratan los posibles errores.

RECIBE: $_POST["datosFormOrdenCobroRemesa"], con datos de la remesa enviada al banco, desde
        el formulario: vistas/tesoro/vEstadoOrdenesCobroRemesasTes.php, 
								entre otros incluye 'NOMARCHIVOSEPAXML' y 'DIRECTORIOARCHIVOREMESA'

LLAMADA: cTesorero.php:estadoOrdenesCobroRemesasTes(), y vistas/tesorero/vEstadoOrdenesCobroRemesasTes.php

LLAMA: modelos/libs/validarCamposTesorero.php:validarCamposFormActualizarOrdenCobroRemesa()
       modeloTesorero.php: mActualizarCuotasCobradasEnRemesaTes()
       modeloEmail.php:enviarEmailPhpMailer()
							vistas/tesorero/vActualizarCuotasCobradasEnRemesaTes.php
							vMensajeCabSalirNavInc.php
							
OBSERVACIONES: Probado PHP 7.3.21

OJO ACCIÓN IRREVERSIBLE		

NOTA: Solo se efectuará una vez comprobado por el banco que se ha realizado el pago en la cuenta de EL, 
las devolacionesn que se hayan producido al afectuar el cobro y las que se vayan produciendo en días sucesivos 
terorería las irá anotando una a una en la tabla CUOTAANIOSOCIO.
----------------------------------------------------------------------------------------------------------------------*/								
function actualizarCuotasCobradasEnRemesaTes()
{	
	//echo "<br><br>0-1 cTesorero:actualizarCuotasCobradasEnRemesaTes:SESSION";print_r($_SESSION);  
	//echo "<br><br>0-2 cTesorero:actualizarCuotasCobradasEnRemesaTes:POST";print_r($_POST);
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once "./modelos/modeloTesorero.php";
		require_once './vistas/tesorero/vActualizarCuotasCobradasEnRemesaTesInc.php';	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	
		require_once './modelos/modeloEmail.php';	

		$datosMensaje['textoCabecera'] = "ANOTAR LAS CUOTAS DOMICILIADAS DE LOS SOCIOS/AS DE LA REMESA YA ABONADA POR EL BANCO";
		$datosMensaje['textoComentarios'] = "<br />Error al Actualizar cuotas cobradas a socios/ass en Remesa SEPA XML.". 
																																						" No se han podido actualizar las órdenes de cobro de cuotas domiciliadas correspondientes a esta remesa de cobros bancarios, ". 
																																						"comprueba que el estado y los datos anteriores de las órdenes de cobro de la remesa no se han alterado.".
																																						"	<br /><br />Prueba de nuevo pasado un rato.". 
																																						" Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$tituloSeccion = "Tesorería";		
		
		$enlacesFuncionRolSeccId = '';//evita notice
		
		//------------ inicio navegacion -------------------------------------------	
		$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=estadoOrdenesCobroRemesasTes")		
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] = "index.php?controlador=cTesorero&accion=actualizarCuotasCobradasEnRemesaTes";	
		$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Actualizar cuotas domiciliadas cobradas en Remesa"; 
		$_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;		
		require_once './controladores/libs/cNavegaHistoria.php';
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");	
		//------------ fin navegacion -----------------------------------------------		
				
		if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar'])) 
		{//echo "<br><br>1 cTesorero:actualizarCuotasCobradasEnRemesaTes:_POST: ";print_r($_POST); 
			
			if (isset($_POST['salirSinActualizar']))
			{
				$datosMensaje['textoComentarios'] = "Has elegido salir sin modificar las cuotas de soci@s cobradas en la Remesa SEPA XML";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}		
			elseif (isset($_POST['comprobarYactualizar']))
			{			
				require_once './modelos/libs/validarCamposTesorero.php';
				$validarCamposActualizarOrdenCobroRemesa = validarCamposFormActualizarOrdenCobroRemesa($_POST);
			
				//echo "<br><br>2 cTesorero:actualizarCuotasCobradasEnRemesaTes:validarCamposActualizarOrdenCobroRemesa:";print_r($validarCamposActualizarOrdenCobroRemesa);echo "<br><br>";		

				if ($validarCamposActualizarOrdenCobroRemesa['codError'] !== "00000")
				{
						vActualizarCuotasCobradasEnRemesaTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$validarCamposActualizarOrdenCobroRemesa);				
				}
				else //$resValidarCuotasVigentesEL['datosFormCuotasVigentesEL']['codError']=="00000"   		
				{
	     $resActualizarCobroCuotasRemesa =  mActualizarCuotasCobradasEnRemesaTes($validarCamposActualizarOrdenCobroRemesa['datosFormOrdenCobroRemesa']);//en modeloTesorero.php';error ok 
												
						//echo "<br><br>3 cTesorero:actualizarCuotasCobradasEnRemesaTes:resActualizarCobroCuotasRemesa: ";print_r($resActualizarCobroCuotasRemesa);	
			
						if ($resActualizarCobroCuotasRemesa['codError'] !== "00000")//error
						{					
							$textoCuerpoEmail = $datosMensaje['textoComentarios'].' '.$resActualizarCobroCuotasRemesa['arrMensaje']['textoComentarios'].$resActualizarCobroCuotasRemesa['errorMensaje'].
																											"\n\nNota: guarda este email, te podrá ser útil, para avisar a adminusers@europalaica.org sobre el error que se ha producido.";
							$datosMensaje['textoComentarios'] .= $resActualizarCobroCuotasRemesa['arrMensaje']['textoComentarios'];																															
						}		
						else //$resActualizarCobroCuotasRemesa['codError'] =="00000"
						{	     
							$textoCuerpoEmail = $resActualizarCobroCuotasRemesa['arrMensaje']['textoComentarios']."\n\nNota: guarda este email, te podrá ser útil, especialmente si hay avisos sobre cambios en datos.";
							$datosMensaje['textoComentarios'] = $resActualizarCobroCuotasRemesa['arrMensaje']['textoComentarios'];	
							
						}//$resActualizarCobroCuotasRemesa['codError'] == "00000"	 
								
						//-- Inicio Enviar email a tesorería y adminusers con el resultado incluido también caso de error ----				
						$search = array("<br />", "<strong>","</strong>");	$replace = array("\n", '', '');				
						$body = str_replace($search, $replace, $textoCuerpoEmail);
						
						$datosEnvioEmail = array( "fromAddress" =>'tesoreria@europalaica.org',	"fromAddressName" =>'Tesorería de Europa Laica',																								
																																"toAddress" 		=>'adminusers@europalaica.org',	"toAddressName"		=>'Europa Laica. Administrador de socios/as',		
																																//"toCC" 							=>'adminusers@europalaica.org',	"toCCName"		=>'Europa Laica. Administrador de socios/as',		
																																"toCC" 							=>'tesoreria@europalaica.org',	"toCCName"		=>'Tesorería de Europa Laica',																																
																																"subject" 				=>'Actualizar cuotas cobradas a soci@s en Remesa SEPA XML',																								
																																"body"		 			 	=> $body																	
																															);		
						
						$reEnvioEmail = enviarEmailPhpMailer($datosEnvioEmail);//en modeloEmail.php
						
						if ($reEnvioEmail['codError'] !== "00000")	
						{ $textoSobreEnvioEmail = 'Nota: No se ha podido enviar un email a -tesoreria@europalaica.org- con el resultado de Actualizar las cuotas cobradas a soci@s en está Remesa SEPA XML';
						}
						else						
						{ $textoSobreEnvioEmail = 'Nota: Se ha enviado un email a -tesoreria@europalaica.org- con el resultado de Actualizar las cuotas cobradas a soci@s en está Remesa SEPA XML';
						}
						//-- Fin Enviar email a tesorería y adminusers con el resultado incluido también caso de error ------
						
						$datosMensaje['textoComentarios'] .= '<br /><br />'.$textoSobreEnvioEmail;				
						
						//echo "<br><br>3-1 cTesorero:actualizarCuotasCobradasEnRemesaTes:datosMensaje['textoComentarios']: ";print_r($datosMensaje['textoComentarios']);	
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
						
				}//$resValidarCuotasVigentesEL['datosFormCuotasVigentesEL']['codError']=="00000" 	
			}//elseif (isset($_POST['comprobarYactualizar']))
		}//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar'])) 
			
		else//!(isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar'])) 
		{
			if (isset($_POST["datosFormOrdenCobroRemesa"]) && !empty($_POST["datosFormOrdenCobroRemesa"])) 
			{
					$arrayDatosFormOrdenCobroRemesa = unserialize(stripslashes($_POST["datosFormOrdenCobroRemesa"]));//Obtenemos el array pasado por post				
					//echo "<br><br>5-1 cTesorero:actualizarCuotasCobradasEnRemesaTes:arrayDatosFormOrdenCobroRemesa: ";print_r($arrayDatosFormOrdenCobroRemesa);
					
					foreach($arrayDatosFormOrdenCobroRemesa as $campo => $valCamp)
					{
						$datosOrdenesCobroUnaRemesa['datosFormOrdenCobroRemesa'][$campo]['valorCampo'] = $valCamp;
					}	
			}
			//echo "<br><br>5-2 cTesorero:actualizarCuotasCobradasEnRemesaTes:datosOrdenesCobroUnaRemesa: ";print_r($datosOrdenesCobroUnaRemesa);

			vActualizarCuotasCobradasEnRemesaTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$datosOrdenesCobroUnaRemesa);
			
		}//else !(isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	 
}
//----------------------------- Fin actualizarCuotasCobradasEnRemesaTes ----------------------------------------------*/


/*== INICIO: FUNCIONES RELACIONADAS CON ARCHIVO ÓRDENES COBRO CUOTAS BANCOS ==============================
					- XMLCuotasTesoreroSantander(): GENERAR ARCHIVO REMESA B.SANTANDER "SEPA-XML",(la que se usa ahora)
					- excelCuotasTesoreroBancos(): ARCHIVOS CUOTAS EXCEL BANCOS (Antes Tríodos)
					- excelCuotasInternoTesorero(): ARCHIVOS CUOTAS EXCEL USO INTERNO (de tesorero)
					- AEB19CuotasTesoreroSantander(): antigua del B.Santander, ya no se usa
========================================================================================================*/

/*-------------------------- Inicio XMLCuotasTesoreroSantander ---------------------------------------------------------
Crea un archivo con el nombre "SEPA_ISO20022CORE_fecha_orden_cobrp.xml", Norma SEPA validado con "pain.008.001.02.xsd", 
que incluye los datos necesarios de cada socio para la remesa de orden de cobro de su cuota y con los datos que pide 
B. Santander y actualmente lo guarda en el directorio "/upload/TESORERIA/SEPAXML_ISO20022" que viene de 
cTesorero:XMLCuotasTesoreroSantander()(y a su vez desde formulario y como opción podría venir de constantes o BBDD).
Desde la aplicación después se descargará para subirlo a la web del B. Santander y ordenar el cobro de la Remesa. 

También insertarán las filas con los datos de esa remesa	en la tabla "ORDENES_COBRO" (que se utilizarán para actualizar 
la tabla	"CUOTAANIOASOCIO"	después de que el banco efectue el cobro), y también se insertan los datos generales de 
información de esa remesa en la tabla "REMESAS_SEPAXML"

Llama al formulario "vistas/tesorero/vXMLCuotasInc.php" aporta datos y permite elegir: 
- Fecha cobro, Fecha excluir de orden de cobro a altas posteriores, 
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España  (en este caso por la necesidad de
  BICs, actualmente no puede generar el archivo pero muestra un listado para incluilos en remesas manualmente)
- Agrupaciones Territariales seleccionadas				
- También incluye una grupo de datos fijos, relacionados con la cuenta del B. Santander, necesarios para generar 
  la orden SEPA-XML.

Además siempre se incluye: 
- socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- ORDENARCOBROBANCO ="SI",  es condición necesaria. 							
- INCLUIRÁ LOS ESTADOS DE CUOTAS: 'PENDIENTE-COBRO','ABONADA-PARTE' (las cuotas reducidas también se incluyen)

LLAMADA: vistas/tesorero:formMenuOrdenesCobroCuotasTes.php 
y vXMLCuotasInc.php:formXMLCuotas.php

LLAMA: modeloTesorero.php:exportarCuotasXMLBancos(),este a su vez utiliza la clase
usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php'
validarCamposTesorero.php:validarFormOrdenCuotasBancos()
vistas/mensajes/vMensajeCabSalirNavInc.php
modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord(),
vistas/tesorero:vXMLCuotasInc.php
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: 2020-12-19: Probada PHP 7.3.21, y últimas normas del B. Santander.

NOTA: Al final de esta función //$arrCamposFormRemesaBancoInicial['agrupaciones']['valorCampo']=... se comenta para que 
al principio NO salgan seleccionadas todas agrupaciones (checked)	en "vXMLCuotasInc.php". 
Si se quiere cambiar el marcaje de la lista de agrupaciones, para que al entrar en el formulario 1ª vez estén todos los 
box SELECCIONADOS de agrupaciones, para despues ir deseleccionando las que quieras EXCLUIR descomentar la línea indicada
----------------------------------------------------------------------------------------------------------------------*/
function XMLCuotasTesoreroSantander()
{
 //echo "<br><br>0-1 cTesorero:XMLCuotasTesoreroSantander:_POST: ";print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './modelos/modeloTesorero.php';	
		require_once './vistas/tesorero/vXMLCuotasInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/libs/arrayParValor.php'; 
		require_once './modelos/modeloEmail.php';
		
		$datosMensaje['textoCabecera'] = "GENERAR ARCHIVO 'SEPA_ISO20022CORE_fecha.xml' DE CUOTAS DOMICILIADAS, PARA ENVÍO REMESA POR B.SANTANDER Y ANOTAR EN TABLA 'ORDENES_COBRO'"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar órdenes de cobro cuotas domiciliadas de socios/as a archivo SEPA XML para B. Santander. 
																																							No se ha podido exportar orden de cobro cuotas socios/as SEPA XML.
																																							<br /><br />Comprueba el ESTADO DE LAS ÓRDENES DE COBRO CORRESPONDENDIENTES A LAS REMESAS (por si hubiese que anular el registro).
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:XMLCuotasTesoreroSantander(). Error: ';	
		$tituloSeccion = "Tesorería";			

		$resExportar['codError'] = '00000';
		
		/*
  Nota: Todos los valores fijos del formulario (readonly), se podrían iniciar aquí, (y mejor proceder de constantes conrequire_once './constantes/constantesOrdenesCobro.php'; o de BBDD)
  $directorioArchivoRemesa = $arrayFormDatosElegidos['DIRECTORIOARCHIVOREMESA'];	vendrá del formulario
		$directorioArchivoRemesa = "/../upload/TESORERIA/SEPAXML_ISO20022" es directorio protegido relativo a raiz $_SERVER['DOCUMENT_ROOT']
		*/

		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=XMLCuotasTesoreroSantander";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar cuotas socios/as SEPA XML para B. Santander";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	//acaso sea 4 y mejor sistemas +1	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------

		if (isset($_POST['SiExportarRemesaBanco']) || isset($_POST['NoExportarRemesaBanco']))   
		{
			if (isset($_POST['NoExportarRemesaBanco']))//se reutiliza por eso aparece este nombre
			{
				$datosMensaje['textoComentarios'] = "No se han exportado las órdenes de cobro cuotas socios/as SEPA XML para B. Santander y 
																																									no se ha realizado ninguna modificación en las tablas de la base de datos";				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarRemesaBanco'])...)
			{				
				require_once './modelos/libs/validarCamposTesorero.php';	 		
				$resValidarDatosFormRemesaBanco = validarFormOrdenCuotasBancos($_POST['datosFormRemesaBanco']);//se utiliza también en excelCuotasTesoreroBancos() 			

				//echo "<br><br>2 cTesorero:XMLCuotasTesoreroSantander:resValidarDatosFormRemesaBanco: ";print_r($resValidarDatosFormRemesaBanco);			

				if ($resValidarDatosFormRemesaBanco['codError'] !== '00000')
				{
					//echo "<br><br>3 cTesorero:XMLCuotasTesoreroSantander:resValidarDatosFormRemesaBanco: ";print_r($resValidarDatosFormRemesaBanco); 
						
					$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%';  
			
					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);//en modelos/libs/arrayParValor.php
							
					if ($parValorComboAgrupa['codError'] !== '00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					} 
					else
					{					
						vXMLCuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarDatosFormRemesaBanco);	
					}	
				}
				else// $resValidarDatosFormRemesaBanco['codError'] == '00000'			
				{				
					$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona tesorería para todas	
					$condicionEmail = '%';//aunque no tengan email o no sea valido se ordena cobro
							
					$resExportar = exportarCuotasXMLBancos($codAreaCoordinacion,$condicionEmail,$_POST['datosFormRemesaBanco']);//en modeloTesorero.php, probado errores.
					
					//echo "<br><br>4 cTesorero:XMLCuotasTesoreroSantander:resExportar: "; print_r($resExportar);
			
					if ($resExportar['codError'] !== '00000')//puede ser error del sistema o error lógico, como numFilas = 0, o falta BIC. 
					{			
						if ($resExportar['codError'] >= '80000')//Error lógico: no hay cuotas socios que cumplan las condicciones, o faltan BICs
						{					  
								$datosMensaje['textoComentarios'] = "No se han exportado a un archivo SEPA XML las órdenes de cobro de cuotas domiciliadas de socios/as<br /><br /><br />".$resExportar['textoComentarios'];					
						}
						else//$resExportar['codError'] < '80000' error sistema
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:exportarCuotasXMLBancos()".
																																																	$resExportar['textoComentarios'].$resExportar['codError'].": ".$resExportar['errorMensaje']);									
						}				
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}
					else// $resExportar['codError'] == '00000'
					{					
						$datosMensaje['textoComentarios'] = $resExportar['textoComentarios'].
						"<br /><br /><br />Ahora debes comprobar que todo <strong>está correcto</strong> y después <strong>descargar el archivo</strong> con las órdenes de cobro 
						para esa remesa y <strong>subirlo a la web del B. Santander</strong> para ordena el cobro.
						<br /><br /><br />Estas operaciones las debes hacer desde el menú: <i>
						II.1 - Estado de las órdenes de cobro de cuotas domiciliadas: Ver órdenes cobro, Eliminar remesa ORDENES_COBRO, Descargar Archivo SEPA-XML de la remesa, 
						Actualizar pagos remesa en CUOTA ANIO SOCIO</i>";						
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	   
					}	
				}//else $resValidarDatosFormRemesaBanco['codError'] == '00000'							
			}//else (isset($_POST['SiExportarRemesaBanco'])...)	
		}//if (isset($_POST['SiExportarRemesaBanco']) || isset($_POST['NoExportarRemesaBanco']))  
			
		else //!(if (isset($_POST['SiExportarRemesaBanco']) || isset($_POST['NoExportarRemesaBanco']))  
		{	
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';

			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);//en modelos/libs/arrayParValor.php
			
			//echo "<br><br>5 cTesorero:XMLCuotasTesoreroSantander:parValorComboAgrupa: "; print_r($parValorComboAgrupa);	
			
			if ($parValorComboAgrupa['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			} 				
			else
			{//$arrCamposFormRemesaBancoInicial['DIRECTORIOARCHIVOREMESA'] = $directorioArchivoRemesa; = "/../upload/TESORERIA/SEPAXML_ISO20022";				
				$arrCamposFormRemesaBancoInicial = array();//para evitar notice si es nula o vacío		
				
	   //echo "<br><br>6 cTesorero:XMLCuotasTesoreroSantander:arrCamposFormRemesaBancoInicial: "; print_r($arrCamposFormRemesaBancoInicial);
	
				//$arrCamposFormRemesaBancoInicial['agrupaciones']['valorCampo']= $parValorComboAgrupa['lista'];//sería para que en formulario al principio salgan seleccionadas todas agrupaciones (checked marcados)
							
					vXMLCuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrCamposFormRemesaBancoInicial);				
			}
		}
	//echo "<br><br>7 cTesorero:XMLCuotasTesoreroSantander:resExportar: "; print_r($resExportar);
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')		
}
/*--------------------------- Fin XMLCuotasTesoreroSantander ---------------------------------------------------------*/

/*-------------------- Inicio excelCuotasTesoreroBancos (antes para Triodos) -------------------------------------------
Genera y exporta a un archivo Excel, y se descarga, con las órdenes de pago de las cuotas de los socios, 
(se utilizaba para las órdenes de cobro en B. Tríodos) y ahora también es útil para uso interno de tesorería, cuando se 
genera y descarga a continuación de generar el archivo XML SEPA para el B. Santander (con los mismos criterios de 
selección) y así el Excel puede servir para contrastar los totales y otros datos y como un listado para anotar las 
devoluciones e incidencias de la remesa

El formulario permite elegir:
- Excluir de la orden de cobro a los socios/as con alta después de una fecha	
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España
- Agrupaciones Territariales seleccionadas												
												
Además siempre se incluye: 
- socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- ORDENARCOBROBANCO ="SI",  es condición necesaria. 							
- INCLUIRÁ LOS ESTADOS DE CUOTAS: 'PENDIENTE-COBRO','ABONADA-PARTE'   (las cuotas reducidas también se incluyen)
		
LLAMADA: vistas/tesorero/: formMenuExportarCuotasDonaTesorero.php y formExcelCuotasTesoreroBancos.php		
						
LLAMA: modeloTesorero.php:exportarCuotasExcelBancos()
validarCamposTesorero.php:validarFormOrdenCuotasBancos()
vistas/mensajes/vMensajeCabSalirNavInc.php
modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord(),
vistas/tesorero:vExcelCuotasTesoreroBancosInc.php
modeloEmail.php:emailErrorWMaster()							

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error al formar el buffer de 
salida a excel ya que utiliza "header()" y no puede haber ningúna salida delante, para poder descargar el archivo.

NOTA: Al final de esta función //arrCamposFormCuotasBancoInicial['agrupaciones']['valorCampo']=... 
se comenta para que al principio NO salgan seleccionadas todas agrupaciones (checked) en "vExcelCuotasTesoreroBancosInc.php". 
Si se quiere cambiar el marcaje de la lista de agrupaciones, para que al entrar en el formulario 1ª vez estén todos los 
box SELECCIONADOS de agrupaciones, para despues ir deseleccionando las que quieras EXCLUIR descomentar la línea indicada
----------------------------------------------------------------------------------------------------------------------*/
function excelCuotasTesoreroBancos()
{
	//echo "<br><br>0-1 cTesorero:excelCuotasTesoreroBancos:_POST: ";print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';
		require_once './vistas/tesorero/vExcelCuotasTesoreroBancosInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/libs/arrayParValor.php';   
		require_once './modelos/modeloEmail.php';	

		$datosMensaje['textoCabecera'] = "EXPORTAR A ARCHIVO EXCEL ÓRDENES DE COBRO CUOTAS DOMICILIADAS DE SOCIOS/AS "; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar las cuotas y otros datos de los socios/as a Excel                             
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:excelCuotasTesoreroBancos(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		$resExportar['codError'] = '00000';

		//echo "<br><br>0 cTesorero:excelCuotasTesoreroBancos:_POST:"; print_r($_POST);	
		
		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=excelCuotasTesorero";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar órdenes cobro cuotas socios/as a Excel";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
						
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{
				$datosMensaje['textoComentarios'] = "No se han exportado las cuotas y otros datos de los socios/as a Excel ";		 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{
				$arrayFormDatosElegidos = $_POST['datosExcelCuotas'];
				
				require_once './modelos/libs/validarCamposTesorero.php';			
				$resValidarCamposExcelCuotasBancos = validarFormOrdenCuotasBancos($arrayFormDatosElegidos);//se utiliza también en XMLCuotasTesoreroSantander()
				
				//echo "<br><br>1 cTesorero:excelCuotasTesoreroBancos:resValidarCamposExcelCuotasBancos: ";print_r($resValidarCamposExcelCuotasBancos);

				if ($resValidarCamposExcelCuotasBancos['codError'] !== '00000')
				{
					$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%';  
				
					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
											
					if ($parValorComboAgrupa['codError']!=='00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					} 
					else
					{					
						vExcelCuotasTesoreroBancosInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposExcelCuotasBancos );					
					}	
				}
				else	//if ($resValidarCamposExcelCuotasBancos['codError']=='00000')		
				{
					$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona tesorería para todas	
					$condicionEmail = '%';//aunque no tengan email o no sea valido se ordena cobro

					$resExportarCuotasSocios =	exportarCuotasExcelBancos($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);//en modeloTesorero.php, probado errores.				
     
					//echo "<br><br>2 cTesorero:excelCuotasTesoreroBancos:resExportarCuotasSocios: ";print_r($resExportarCuotasSocios);

					if ($resExportarCuotasSocios['codError'] !== '00000')//nota este error tambien se trata mas adelante y podria sobrar aquí
					{
						if ($resExportarCuotasSocios['codError'] >= '80000')//Error lógico: no hay cuotas socios que cumplan las condicciones, o faltan BICs
						{					  
								$datosMensaje['textoComentarios'] = "No se ha exportado un archivo Excel con órdenes cobro de cuotas domiciliadas de socios/as<br /><br /><br />".$resExportarCuotasSocios['textoComentarios'];					
						}
						else//$resExportarCuotasSocios['codError'] < '80000' error sistema
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:excelCuotasTesoreroBancos()".
																																																	$resExportarCuotasSocios['textoComentarios'].$resExportarCuotasSocios['codError'].": ".$resExportarCuotasSocios['errorMensaje']);									
						}		
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	
					}
					else
					{$datosMensaje['textoComentarios'] = "Se han exportado las cuotas de los socios/as a Excel para B. Tríodos y y uso interno de tesorería";				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);	//no se mostrará por captura buffer			
					}	
				}//else if ($resValidarCamposExcelCuotasBancos['codError']=='00000')	
			}//else (isset($_POST['SiExportarExcel']))
		}//if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';

			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);//en modelos/libs/arrayParValor.php

			//echo "<br><br>3 cTesorero:excelCuotasTesoreroBancos:parValorComboAgrupa: "; print_r($parValorComboAgrupa);	
			
			if ($parValorComboAgrupa['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			} 				
			else
			{		
				$arrCamposFormCuotasBancoInicial = array();//para evitar notice
				//$arrCamposFormCuotasBancoInicial['agrupaciones']['valorCampo']= $parValorComboAgrupa['lista'];//sería para que en formulario al principio salgan seleccionadas todas agrupaciones (checked marcados)	
		
				vExcelCuotasTesoreroBancosInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrCamposFormCuotasBancoInicial);	
			} 		
		}//else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']) 
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	 
}	
/*--------------------------- Fin excelCuotasTesoreroBancos ----------------------------------------------------------*/
	
/*-------------------- Inicio excelCuotasInternoTesorero Para uso INTERNO ----------------------------------------------
Exporta todas las cuotas de un año, (o de todos) y otros datos de los socios a un fichero Excel para uso interno de 
tesorería según los valores elegidos en el formulario de selección. 

Llama a un formulario de selección de campos "vExcelCuotasInternoTesoreroInc.php" para formar las condiciones de de la 
SELECT  SQL.

El formulario además de las agrupaciones permite elegir:

- Un determinado año (dentro de los cinco últimos)
- Estado cuota: ABONADA,NOABONADA,EXENTO,PENDIENTE-COBRO,NOABONADA-ERROR-CUENTA,ABONADA-PARTE 												
- Estado de los socios/as (alta, baja, todos) 
- ORDENARCOBROBANCO = SI,NO,TODOS 
- Cuenta bancaria domiciliada:
  .Cuenta bancaria de España
  .Cuenta bancaria de países SEPA en Europa distintos de España
  .No tiene cuenta bancaria domiciliada
  .Todas las opciones
- Agrupaciones Territoriales

Se descargará en un archivo Excel

LLAMADA: cTesorero.php:menuOrdenesCobroCuotasTesdesde():vExcelCuotasInternoTesoreroInc.php
LLAMA: modeloTesorero.php:exportarExcelCuotasInternoTes()
       vistas/tesorero/vExcelCuotasInternoTesoreroInc.php
							vistas/mensajes/vMensajeCabSalirNavInc.php
       modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord(),parValoresEstadosCuotas()
       modelos/modeloEmail.php:emailErrorWMaster()							

OBSERVACIONES: PHP 7.3.21, esta función no usa directamente PDO pero si algunas de las funciones llamadas.
OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error al formar el buffer de salida a excel ya 
que utiliza "header()" y no puede haber ningúna salida delante.
----------------------------------------------------------------------------------------------------------------------*/
function excelCuotasInternoTesorero()
{
 //echo "<br><br>0-1 cTesorero:excelCuotasInternoTesorero:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{				
		require_once './modelos/modeloTesorero.php';
		require_once './modelos/libs/validarCamposTesorero.php';
		require_once './vistas/tesorero/vExcelCuotasInternoTesoreroInc.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 
		require_once './modelos/modeloEmail.php';	
		require_once './modelos/libs/arrayParValor.php';   
		
		$datosMensaje['textoCabecera'] = "EXPORTAR CUOTAS Y OTROS DATOS DE LOS SOCIOS/AS A EXCEL PARA USO INTERNO "; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar las cuotas y otros datos de los socios/as a Excel para uso interno                              
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:excelCuotasInternoTesorero(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		$resExportar['codError'] = '00000';

		//------------ inicio navegacion ------------------------------------------- 
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=excelCuotasInternoTesorero";	
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar cuotas a Excel para uso interno";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;					
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))  
		{
			if (isset($_POST['NoExportarExcel']))
			{$datosMensaje['textoComentarios'] = "No se han exportado las cuotas y otros datos de los socios/as a Excel para uso interno ";	 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportarExcel']))
			{
				$resValidarDatosFormExcelCuotas = validarFormExcelCuotasInterno($_POST['datosExcelCuotas']);//en validarCamposTesorero.php

				//echo "<br><br>1 cTesorero:excelCuotasInternoTesorero:resValidarDatosFormExcelCuotas: ";print_r($resValidarDatosFormExcelCuotas);			

				if ($resValidarDatosFormExcelCuotas['codError'] !== '00000')
				{
					//echo "<br><br>2 cTesorero:excelCuotasInternoTesorero:resValidarDatosFormExcelCuotas: ";print_r($resValidarDatosFormExcelCuotas); 
						
					$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%';  
			
					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);//en modelos/libs/arrayParValor.php						
					if ($parValorComboAgrupa['codError'] !== '00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": parValoresAgrupaPresCoord()".$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					} 
					else
					{$valDefEstadoCuota = '%';	
						$parValorEstadosCuota = parValoresEstadosCuotas($valDefEstadoCuota);				
						if ($parValorEstadosCuota['codError'] !== '00000')
						{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": parValoresEstadosCuotas()".$parValoresEstadosCuotas['codError'].": ".$parValoresEstadosCuotas['errorMensaje']);
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
						}
						else
						{				
								vExcelCuotasInternoTesoreroInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$parValorEstadosCuota,$resValidarDatosFormExcelCuotas);
						}		
					}	
				}//if ($resValidarDatosFormRemesaBanco['codError'] !== '00000')
				else// $resValidarDatosFormRemesaBanco['codError'] == '00000'
				{
						$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona tesorería para todas	
						$condicionEmail = '%';//aunque no tengan email o no sea válido se ordena cobro			
						
						$resExportarCuotasSocios =	exportarExcelCuotasInternoTes($codAreaCoordinacion,$condicionEmail,$_POST['datosExcelCuotas']);//en modeloTesorero.php				
						
						//echo "<br><br>3 cTesorero:excelCuotasInternoTesorero:resExportarCuotasSocios: "; print_r($resExportarCuotasSocios);	
																																																							
						if ($resExportarCuotasSocios['codError'] !== '00000')//puede ser error del sistema o error lógico, como numFilas = 0, o falta BIC. 
						{			
							if ($resExportarCuotasSocios['codError'] >= '80000')//Error lógico: no hay cuotas socios que cumplan las condicciones, o faltan BICs
							{					  
								$datosMensaje['textoComentarios'] = "No se han exportado las cuotas y otros datos de los socios/as a Excel para uso interno <br /><br />".$resExportarCuotasSocios['textoComentarios'];					
							}
							else //$resExportarCuotasSocios['codError'] < '80000' error sistema
							{
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:	exportarExcelCuotasInternoTes()".$resExportarCuotasSocios['textoComentarios'].
																																																		$resExportarCuotasSocios['codError'].": ".$resExportarCuotasSocios['errorMensaje']);									
							}				
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
						}			
						else //$resExportarCuotasSocios['codError'] == '00000'
						{
							$datosMensaje['textoComentarios'] = "Se han exportado las cuotas y otros datos de los socios/as a Excel para uso interno";				
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
						}	
				}//else $resValidarDatosFormRemesaBanco['codError'] == '00000'			
			}//else (isset($_POST['SiExportarExcel']))		
		}//if (isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel']))
		else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
		{	
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto			
			$valDefCodAgrupa = '%';		
		
			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);		
			if ($parValorComboAgrupa['codError'] !== '00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": parValoresAgrupaPresCoord(()".$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);	
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			}
			else
			{ $valDefEstadoCuota = '%';	
		
					$parValorEstadosCuota = parValoresEstadosCuotas($valDefEstadoCuota);			
					if ($parValorEstadosCuota['codError'] !== '00000')
					{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": parValoresEstadosCuotas()".$parValoresEstadosCuotas['codError'].": ".$parValoresEstadosCuotas['errorMensaje']);
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
					}
					else
					{	
							$arrCamposFormCuotasExcelInicial = array();//para evitar notice 
							$arrCamposFormCuotasBancoInicial['agrupaciones']['valorCampo'] = $parValorComboAgrupa['lista'];//para que en formulario al principio salgan todas agrupaciones (checked)	 
							
							vExcelCuotasInternoTesoreroInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$parValorEstadosCuota,$arrCamposFormCuotasBancoInicial);						
					}
			}		
		}//else !(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin excelCuotasInternoTesorero ---------------------------------------------------------*/


/*-------------------------- Inicio AEB19CuotasTesoreroSantander ---------------
Exporta todas órdenes de pago de las cuotas de los socios a un fichero norma AEB19 con las celdas 
en formato texto, según las condiciones que pide "---B. Santander----"
Permite elegir año cuota, fecha cobro, agrupación territorial y cuenta bancaria en España o en el extranjero

- ORDENARCOBROBANCO ="SI" es condición necesaria para que se incluya en la lista	de cobros
El tesorero en -Cuotas socios/as->Pago cuota, campo *Incluir en lista de órdenes de pagos a los bancos:SI /NO														
												
- Ademas INCLUIRÁ LOS ESTADOS DE CUOTAS: 'PENDIENTE-COBRO','ABONADA-PARTE'
(las cuotas reducidas también se incluyen)

- NO INCLUYE ->	condicionFechaAltaExentosPago superior al valor el tesorero puede eligir fecha para 
  excluir de pago a las altas cerca de final año 

LLama: modeloTesorero.php:exportarCuotasAEB19Bancos(),que al final utiliza  class.AEB19Writer.php
       libs/validarCamposTesorero.php:validarCamposFormAEB19Cuotas()
							modeloUsuarios.php:parValoresAgrupaPresCoord()

LLamado desde: vistas/tesorero/: formMenuExportarCuotasDonaTesorero.php y vAEB19CuotasInc.php: formAEB19Cuotas.php

OBSERVACIONES:-  OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error
                 al formar el buffer de salida a txt ya que utiliza "header()" y no puede 
															  haber ningúna salida delante.
															
															- Al final de la función se puede cambiar la forma en que aparecerán la lista de agrupaciones:
                 * --AGRUPACIONES--  DESCOMENTAR LO SIGUIENTE SI: se quiere que al entrar el formulario 1ª vez estén todos 
																	     los box SELECCIONADOS para despues ir deseleccionando las que quieras EXCLUIR de la lista de cobros
			              *															

NOTA: una vez ejecutada la función exportarCuotasAEB19Bancos(), la pantalla última permanece fija
      creo que por el buffer, habría que liberarla para que indique que se ha hecho bien															
------------------------------------------------------------------------------*/
function AEB19CuotasTesoreroSantander()
{if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || !isset($_SESSION['vs_ROL_5']))	
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	require_once './vistas/tesorero/vAEB19CuotasInc.php';
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';  
 require_once './modelos/modeloEmail.php';
	
	$tituloSeccion = "Tesorería";		
		
	$resExportar['codError'] = '00000';

 //echo "<br><br>1 cTesorero:AEB19CuotasTesoreroSantander:_POST:"; print_r($_POST);	
	
 //------------ inicio navegacion -------------------------------------------		
 $_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=AEB19CuotasTesoreroSantander";
	$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar archivo NORMA AEB19 para B. Santander";			
	$_SESSION['vs_HISTORIA']['pagActual'] = 4;	
					
	require_once './controladores/libs/cNavegaHistoria.php';	
 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
	//------------ fin navegacion -----------------------------------------------
	
 if (isset($_POST['SiExportarAEB19Cuotas']) || isset($_POST['NoExportarAEB19Cuotas']))  
 {
	 if (isset($_POST['NoExportarAEB19Cuotas']))
  {$salirSinExportar['arrMensaje']['textoCabecera']="Exportar cuotas socios/as NORMA AEB19 para B. Santander"; 
	  $salirSinExportar['arrMensaje']['textoComentarios']="No se han exportado las cuotas de los socios";				
		 vMensajeCabSalirNavInc($tituloSeccion,$salirSinExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}
		else //(isset($_POST['SiExportarExcel']))
		{				
			$arrayFormDatosElegidos = $_POST['datosAEB19Cuotas'];

			require_once './modelos/libs/validarCamposTesorero.php'; 
			$resValidarCamposFormAEB19 = validarCamposFormAEB19Cuotas($_POST['datosAEB19Cuotas']);

   //echo "<br><br>2 cTesorero:resValidarCamposFormAEB19:";print_r($resValidarCamposFormAEB19);

			if ($resValidarCamposFormAEB19['codError']!=='00000')
			{//echo "<br><br>3 cTesorero:resValidarCamposFormAEB19:";print_r($resValidarCamposFormAEB19); 
     
				$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
		  $valDefCodAgrupa = '%';  
				
    require_once './modelos/modeloUsuarios.php';				
		  $parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
						
				if ($parValorComboAgrupa['codError']!=='00000')
			 {$resExportar['arrMensaje']['textoCabecera']='Exportar cuotas socios/as NORMA AEB19 para B. Santander';
				 $resExportar['arrMensaje']['textoComentarios']=$resExportar['errorMensaje']."Fecha:".date("Y-m-d:H:i:s");
			  
					$resEmailErrorWMaster=emailErrorWMaster($parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);		
					vMensajeCabSalirNavInc($tituloSeccion,$parValorComboAgrupa['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			 } 
				else
				{					
			  vAEB19CuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormAEB19);				
			 }	
			}
			else			
			{// los siguientes valores, no se icluyen en el formulario porque se deben cumplir siempre
		  $codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona el tesorero general
			 $condicionEmail ='';//en la condición de busqueda se aceptan todos para el campo email, aunque no existan o sean error

			 $arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que esten dados de alta	       
									
				$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE');		  
			 
				//se excluyen los que tengan ['ORDENARCOBROBANCO']='NO' elegido portesorero, aunque esté pendiente de cobro;
				$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'SI';
			
			 /*Además de cumplir el anterior array como condición (algún exento es posible que quiera donar), también debe cumplir las siguientes:
				- CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA-CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO)<0 
				- AND SOCIO.FECHAALTA <= '2014-02-15' //se exluyen los socios con altas posteriores a cierta fecha que se pide en el formulario 
				- AND CUOTAANIOSOCIO.ORDENARCOBROBANCO = 'SI'	se excluyen los que no tengan ORDENARCOBROBANCO = 'SI'		
			 */
    //echo "<br><br>4 cTesorero:AEB19CuotasTesoreroSantander:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
			
			 require_once './modelos/modeloTesorero.php';
					
		  $resExportar = exportarCuotasAEB19Bancos($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);//si esta bien queda parado aquí
				
		  //echo "<br><br>5 cTesorero:AEB19CuotasTesoreroSantander:resExportar: "; print_r($resExportar);
		
				if ($resExportar['codError'] !=='00000')//nota este error tambien se trata mas adelante y podria sobrar aquí
				{$salirSinExportar['arrMensaje']['textoCabecera']="Exportar cuotas socios/as NORMA AEB19 para B. Santander"; 
		   $salirSinExportar['arrMensaje']['textoComentarios']=". No se han exportado las cuotas de los socios debido a un error del sistema: ".
					                                                    $resExportar['codError'].$resExportar['errorMensaje'];				
			  vMensajeCabSalirNavInc($tituloSeccion,$salirSinExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			 }
			 else
			 {
			  $resExportar['arrMensaje']['textoCabecera'] = "Exportar cuotas socios/as NORMA AEB19 para B. Santander"; 
	    $resExportar['arrMensaje']['textoComentarios'] ="Se han exportado las cuotas de los socios/as para uso NORMA AEB19 para B. Santander";				
		   vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
		 	}					
			}				
	 }	
 }
	else //!(isset($_POST['SiExportarExcel']) || isset($_POST['NoExportarExcel'])  
	{	
		$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
		$valDefCodAgrupa = '%';
		
		require_once './modelos/modeloUsuarios.php';   
		$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
		
		if ($parValorComboAgrupa['codError']!=='00000')
	 {$resExportar['arrMensaje']['textoCabecera']='Exportar cuotas socios/as NORMA AEB19 para B. Santander';
		 $resExportar['arrMensaje']['textoComentarios']=$resExportar['errorMensaje']."Fecha:".date("Y-m-d:H:i:s");
	  
			$resEmailErrorWMaster=emailErrorWMaster($parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);		
			vMensajeCabSalirNavInc($tituloSeccion,$parValorComboAgrupa['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	 } 
		else
		{/* --AGRUPACIONES--  DESCOMENTAR LO SIGUIENTE SI: se quiere que al entrar el formulario 1ª vez estén todos los box SELECCIONADOS 
		      para despues ir deseleccionando las que quieras EXCLUIR de la lista de cobros
			*/

		 $resValidarCamposFormAEB19['agrupaciones']['valorCampo'] = $parValorComboAgrupa['lista'];
			$resValidarCamposFormAEB19['codError'] ='00000';
		 //echo "<br><br>6a cTesorero:AEB19CuotasTesoreroSantander:parValorComboAgrupa: "; print_r($parValorComboAgrupa);					
			vAEB19CuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormAEB19);	

			/*--AGRUPACIONES-- DESCOMENTAR LO SIGUIENTE SI: se quiere que al entrar el formulario 1ª vez estén todos los box DES-SELECCIONADOS 
		     para despues ir seleccionando las que quieras INCLUIR de la lista de cobros
   */
	  //echo "<br><br>6b cTesorero:AEB19CuotasTesoreroSantander:parValorComboAgrupa: "; print_r($parValorComboAgrupa);		

	  //vAEB19CuotasInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,'');
	 }
 }
	//echo "<br><br>7 cTesorero:AEB19CuotasTesoreroSantander:resExportar: "; print_r($resExportar);	

	//----------- tratamiento errores ---------------------------------	
	if ($resExportar['codError']!=='00000')
	{ $resExportar['arrMensaje']['textoCabecera']='Exportar cuotas socios/as NORMA AEB19 para B. Santander';
	  if ($resExportar['codError']=='80001')//no hay filas
			{$resExportar['arrMensaje']['textoComentarios']=$resExportar['errorMensaje'].$resExportar['codError']." Fecha:".date("Y-m-d:H:i:s");	
			}
			else
			{$resExportar['arrMensaje']['textoComentarios']=". Error del sistema al exportar las cuotas de los socios/as ";	
			 $resEmailErrorWMaster=emailErrorWMaster($resExportar['arrMensaje']['textoComentarios'].$resExportar['codError'].": ".$resExportar['errorMensaje']);
			}
			//echo "<br><br>7 cTesorero:AEB19CuotasTesoreroSantander:resExportar: "; print_r($resExportar);	

	  vMensajeCabSalirNavInc($tituloSeccion,$resExportar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
	}
}
//--------------------------- Fin AEB19CuotasTesoreroSantander -----------------

/*== FIN: FUNCIONES RELACIONADAS CON ARCHIVO ÓRDENES COBRO CUOTAS BANCOS =================================


/*==== INICIO: FUNCIONES RELACIONADAS CON EMAILS COBRO CUOTAS ==========================================================
							- emailAvisarDomiciliadosProximoCobro(): Envío email próximo cobro cuota domiciliada 
							- emailAvisarCuotaNoCobradaSinCC(): Envío email cuota no domiciliada aún NO pagada
							- exportarEmailDomiciliadosPend(): Exportar lista Emails socios cuota domciliada pendientes pagar
							- exportarEmailDomiciliadosPendSinCC(): Exportar lista Emails socios cuota no domciliada pendientes pagar
======================================================================================================================*/	

/*-------------------------- Inicio emailAvisarDomiciliadosProximoCobro ------------------------------------------------
Envía un email personalizado, con su nombre, APE1, CCIBAN, Cuota, a los socios para avisar de que se va a pasar al 
cobro las cuotas mediante un sistema de domicialiación bancaria: SEPA XML, (anteriormente AEB19 para el Santander,
o archivo Excel que utilizaba Tríodos).
 
Envía emails para socios a partir de la selección que se realiza en el formulario, 'PENDIENTE-COBRO','ABONADA-PARTE', 
con cuenta IBAN España o en país IBAN SEPA distinto ES (por ahora no porque hay problemas con BIC), 
y ORDENARCOBROBANCO=SI siempre que estén de "alta" en el momento actual y NO INCLUYE las cuotas "abonadas,exentas, 
y socios dados de baja",  y de sólo de las "AGRUPACIONES" elegidas en el formulario.

A partir de una fecha de alta que se introduce en el formulario se excluye el envío a esos socios (para excluir pagar
cuotas altas en noviembre y diciembre) Se envía con cuenta "tesoreria@europalaica.org"

Para formar la lista de emails, desde formEmailAvisarDomiciliadosProximoCobro.php
se puede elegir:
- Cuenta bancaria en España
- Cuenta bancaria de países SEPA (distintos de España)

LLAMADA:cTesorero.php:menuOrdenesCobroCuotasTes()
LLAMA:modeloTesorero.php:datosEmailAvisoProximoCobro(),
libs/validarCamposTesorero.php:validarEmailAvisarDomiciliadosProximoCobro()
arrayParValor.php:parValoresAgrupaPresCoord()	
vistas/tesorero/vEmailAvisarDomiciliadosProximoCobroInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php		 
modelos/modeloEmail.php:emailErrorWMaster()								
							
OBSERVACIONES: 
2020-10-11: Aquí no necesita cambios para PDO, lo incluyen internamente las funciones que utiliza. Probado PHP 7.3.21 

Todos los datos de body del email, se incluye desde el formulario: formEmailAvisarCuotaNoCobradaSinCC.php
2015-12-10: Para socios otros paises SEPA distintos de ES por ahora no se cobran, ya que falta cálculo BIC, 
pero se deja envíar email por si acaso se incluyese.
----------------------------------------------------------------------------------------------------------------------*/
function emailAvisarDomiciliadosProximoCobro()
{
	//echo "<br><br>0-1 cTesorero:emailAvisarDomiciliadosProximoCobro:_POST: "; print_r($_POST);

	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{		
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './vistas/tesorero/vEmailAvisarDomiciliadosProximoCobroInc.php';
		require_once './modelos/libs/arrayParValor.php';
		require_once './modelos/libs/validarCamposTesorero.php';				
		require_once './modelos/modeloTesorero.php';				 
		require_once './modelos/modeloEmail.php';	
		
		$datosMensaje['textoCabecera'] = "ENVIAR EMAIL A SOCIOS/AS PARA AVISO DE PROXIMA ORDEN DE COBRO DE CUOTAS DOMICILIADAS";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al enviar emails aviso orden de cobro cuotas domiciliadas. No se han podido enviar los emails. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:emailAvisarDomiciliadosProximoCobr(). Error: ';	
		$tituloSeccion = "Tesorería";	
		$resEnviar['codError'] = '00000';//este sobra 	 
		
		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=emailAvisarDomiciliadosProximoCobro";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Emails aviso orden cobro cuota ";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;					
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))  
		{
			if (isset($_POST['NoEnviar']))
			{
				$datosMensaje['textoComentarios'] = 'No se han enviado los emails a socios/as para avisar del próximo cobro de las cuotas  domiciliadas. ';				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiEnviar']))
			{	  
				$resValidarCamposFormDatosElegidos = validarEmailAvisarDomiciliadosProximoCobro($_POST['camposFormDatosElegidos']);//en validarCamposTesorero.php	
				
				//echo "<br><br>2-1 cTesorero:emailAvisarDomiciliadosProximoCobro:resValidarCamposFormDatosElegidos: ";print_r($resValidarCamposFormDatosElegidos);			

				if ($resValidarCamposFormDatosElegidos['codError'] !== '00000')
				{//echo "<br><br>2-3 cTesorero:emailAvisarDomiciliadosProximoCobro:resValidarCamposFormDatosElegidos: ";print_r($resValidarCamposFormDatosElegidos); 
					
					$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%'; 
			
					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);	
										
					if ($parValorComboAgrupa['codError'] !== '00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
					} 
					else
					{//echo "<br><br>2-4 cTesorero:emailAvisarDomiciliadosProximoCobro:resValidarCamposFormDatosElegidos: ";print_r($resValidarCamposFormDatosElegidos);
						
						vEmailAvisarDomiciliadosProximoCobroInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormDatosElegidos);				
					}	
				}//	if ($resValidarCamposFormDatosElegidos['codError']!=='00000')
				else //		if ($resValidarCamposFormDatosElegidos['codError']=='00000')//sin error
				{
					$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona el tesorero general	

					$datosEmailAvisoProximoCobro = datosEmailAvisoProximoCobro($codAreaCoordinacion,$_POST['camposFormDatosElegidos']);//en modeloTesorero.php' probado error					
					
					//echo "<br><br>3 cTesorero:emailAvisarDomiciliadosProximoCobro:datosEmailAvisoProximoCobro: ";print_r($datosEmailAvisoProximoCobro);

					if ($datosEmailAvisoProximoCobro['codError'] !== '00000')//puede ser error del sistema o error lógico como numFilas = 0
					{			
						if ($datosEmailAvisoProximoCobro['codError'] >= '80000')//Error lógico: será ['numFilas']== '0',no hay socios que cumplan las condicciones 
						{					  					
								$datosMensaje['textoComentarios'] =  "No se ha envíado ningún email.<br /><br />".$datosEmailAvisoProximoCobro['textoComentarios'];		
						}
						else//$datosEmailAvisoProximoCobro['codError'] < '80000' error sistema
						{
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:datosEmailAvisoProximoCobro()".$datosEmailAvisoProximoCobro['textoComentarios'].
																																																	$datosEmailAvisoProximoCobro['codError'].": ".$datosEmailAvisoProximoCobro['errorMensaje']);									
						}				
						
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}
					else //($datosEmailAvisoProximoCobro['codError'] == '00000'
					{ 
							$datosEmailAvisoProximoCobroCuotas['datosEmailCuotaSocios'] = $datosEmailAvisoProximoCobro['resultadoFilas'];
												
							$datosEmailAvisoProximoCobroCuotas['textoFechaPrevistaCobro'] = $_POST['camposFormDatosElegidos']['textoFechaPrevistaCobro'];
							$datosEmailAvisoProximoCobroCuotas['textoEmail'] = $_POST['camposFormDatosElegidos']['textoEmail'];
							$URLgastosLaicismo = $resValidarCamposFormDatosElegidos['URLgastosLaicismo']['valorCampo'] ;			
							
	//****		Comentar para pruebas y que NO envie	emails 
	$resEnviarEmailSocio = emailAvisarDomiciliadosProxCobro($datosEmailAvisoProximoCobroCuotas, $URLgastosLaicismo);//en modeloEmail.php';	Incluye el tratamiento de errores de email
					
							//echo "<br><br>4 cTesorero:emailAvisarDomiciliadosProximoCobro:resEnviarEmailSocio: ";print_r($resEnviarEmailSocio);

							$datosMensaje['textoComentarios'] = $resEnviarEmailSocio['arrMensaje']['textoComentarios'];//textoComentarios informa de totales enviados y también casos de error	envío email
							
							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}// else ($datosEmailAvisoProximoCobro['codError'] =='00000'
				}//else 	if ($resValidarCamposFormDatosElegidos['codError']=='00000')//sin error	
			}//else isset($_POST['SiEnviar']	
		}
		else //!if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))   
		{	
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';				
					
			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
			
			if ($parValorComboAgrupa['codError'] !=='00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			} 
			else
			{ $arrayFormDatosElegidos = array();
					//$arrayFormDatosElegidos['agrupaciones']['valorCampo'] = $parValorComboAgrupa['lista'];//sería para que al principio salgan seleccionadas todas agrupaciones (checked marcados)
				
					//echo "<br><br>5 cTesorero:emailAvisarDomiciliadosProximoCobro:arrayFormDatosElegidos: "; print_r($arrayFormDatosElegidos);	
		
					vEmailAvisarDomiciliadosProximoCobroInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrayFormDatosElegidos);		
			}
		}//else !if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*---------------------Fin emailAvisarDomiciliadosProximoCobro -------------------------------------------------------*/

/*-------------------------- Inicio emailAvisarCuotaNoCobradaSinCC() ---------------------------------------------------
Envía un email personalizado, con nombre, Cuota, cantidad que debe a los socios, para comunicarles que aún no han pagado
la cuota anual de la asociación Europa Laica.
Envía nombre y APE1, datos bancos Europa Laica, enlace a PayPal con cuota abierta a pagar y también enlace a web de 
laicismo.org con informacion gastos-ingresos

Se incluyen siguiente los casos posibles tipo cuenta banco: SIN CUENTA, CUENTA-NOIBAN (actualmente ya no hay) y 
CUENTA-IBAN país SEPA distinto de ES y además con condicción "Ordenar cobro banco = NO". 
Y ESTADOCUOTA de socios/as:'PENDIENTE-COBRO','ABONADA-PARTE','NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA' 
siempre que estén de "alta" en el momento actual y NO INCLUYE las cuotas "abonadas, exentas, y socios/as que 
estén de baja" y de sólo de las "AGRUPACIONES" elegidas en el formulario. 

A partir de una fecha de alta, que se introduce en el formulario, se excluye el envío a esos socios (para excluir pagar
cuotas altas en noviembre y diciembre)
Se envía con cuenta "tesoreria@europalaica.org"

Para formar la lista de emails, desde formEmailAvisarCuotaNoCobradaSinCC.php se puede elegir:
-No tiene cuenta bancaria domiciliada
-Tiene cuenta bancaria de países NO SEPA (o no está en formato IBAN)
-Cuenta bancaria de países SEPA (distintos de España) y Ordenar cobro banco= "NO"  																			

LLAMADA: cTesorero.php:menuOrdenesCobroCuotasTes()
LLAMA:modeloTesorero.php:datosEmailAvisoCuotaNoCobradaSinCC(),
libs/validarCamposTesorero.php:validarEmailAvisarCuotaNoCobradaSinCC()
arrayParValor.php:parValoresAgrupaPresCoord()
modelos/modeloBancos.php:prepararCadIBANAgrupMostrar()	
vistas/tesorero/vEmailAvisarCuotaNoCobradaSinCCInc.php
vistas/mensajes/vMensajeCabSalirNavInc.php		 
modelos/modeloEmail.php:emailErrorWMaster()	

OBSERVACIONES:
2020-10-11: Aquí no necesita cambios para PDO, lo incluyen internamente las funciones que utiliza. Probado PHP 7.3.21 
2023-10-01: Cambios para mejorar presentación al quitar Banco Tríodos de la BBDD y otras mejoras de información 

Todos los datos de body del email, se incluye en el formulario: formEmailAvisarCuotaNoCobradaSinCC.php
----------------------------------------------------------------------------------------------------------------------*/
function emailAvisarCuotaNoCobradaSinCC()
{
	//echo "<br><br>0-1 cTesorero:emailAvisarCuotaNoCobradaSinCC:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';				
		require_once './modelos/modeloBancos.php';				 	
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
		require_once './vistas/tesorero/vEmailAvisarCuotaNoCobradaSinCCInc.php';
		require_once './modelos/libs/arrayParValor.php';
		require_once './modelos/libs/validarCamposTesorero.php';	
		require_once './modelos/modeloEmail.php';	
		
		$datosMensaje['textoCabecera'] = "ENVIAR EMAIL A SOCIOS/AS SIN DOMICILIACIÓN BANCARIA PARA INFORMAR DE CUOTA ANUAL AÚN NO PAGADA";	
		$datosMensaje['textoComentarios'] = "<br /><br />Error al enviar emails aviso cuota aún no pagada para cuotas no domiciliadas. No se han podido enviar los emails. Prueba de nuevo pasado un rato. 
																																							<br /><br /><br />Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:emailAvisarCuotaNoCobradaSinCC(). Error: ';	
		$tituloSeccion = "Tesorería";	
		
		//$resEnviar['codError'] = '00000'; 
		
		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=emailAvisarCuotaNoCobradaSinCC";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Emails aviso cuota no pagada no domiciliada";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;					
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion ----------------------------------------------
		
		if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))  
		{
			if (isset($_POST['NoEnviar']))
			{
				$datosMensaje['textoComentarios'] = 'No se han enviado los emails para informar a los socios/as que su cuota aún no ha sido pagada.';		 
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
			}
			else //(isset($_POST['SiEnviar']))
			{				
			 $codAgrupBanco = '%';	//Para todas Cuentas bancarias de todas las agrupaciones 	
				
				$arrBancos = agrupacionIBAN_papel($codAgrupBanco);//en modeloBancos.php' probado error OK pero solo envía mensaje a adminusers, y no tabla errores
				//echo "<br><br>1 cTesorero:emailAvisarCuotaNoCobradaSinCC:arrBancos: "; print_r($arrBancos);	
			    
				if ($arrBancos['codError'] !== '00000')
				{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$arrBancos['codError'].": ".$arrBancos['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
				}
				else //$cadBancos['codError'] == '00000'
				{					
				  $_POST['camposFormDatosElegidos']['bancosAgrup']	=	$arrBancos['datosAgrupaciones']; 						
				
						$resValidarCamposFormDatosElegidos = validarEmailAvisarCuotaNoCobradaSinCC($_POST['camposFormDatosElegidos']);//en modelos/libs/validarCamposTesorero.php   				

						//echo "<br><br>2-1 cTesorero:emailAvisarCuotaNoCobradaSinCC:resValidarCamposFormDatosElegidos: ";print_r($resValidarCamposFormDatosElegidos);	     				

						if ($resValidarCamposFormDatosElegidos['codError'] !== '00000')
						{    
							$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
							$valDefCodAgrupa = '%'; 

							$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);	
												
							if ($parValorComboAgrupa['codError'] !== '00000')
							{	$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
							} 
							else
							{	vEmailAvisarCuotaNoCobradaSinCCInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormDatosElegidos);				
							}	
 			  }//	if ($resValidarCamposFormDatosElegidos['codError']!=='00000')
						else //		if ($resValidarCamposFormDatosElegidos['codError']=='00000')//sin error		 
						{												
							$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: actualmente lo gestiona el tesorero general		
										
							$resDatosEmailAvisoCuotasNoCobradas = datosEmailAvisoCuotaNoCobradaSinCC($codAreaCoordinacion,$_POST['camposFormDatosElegidos']); //modeloTesorero.php, probado error
						
							//echo "<br><br>3-1 cTesorero:emailAvisarCuotaNoCobradaSinCC:resDatosEmailAvisoCuotasNoCobradas: ";print_r($resDatosEmailAvisoCuotasNoCobradas);

							if ($resDatosEmailAvisoCuotasNoCobradas['codError'] !== '00000')//puede ser error del sistema o error lógico como numFilas = 0
							{			
								if ($resDatosEmailAvisoCuotasNoCobradas['codError'] >= '80000')//Error lógico: será ['numFilas']== '0',no hay socios que cumplan las condicciones 
								{					  					
										$datosMensaje['textoComentarios'] =  "No se ha envíado ningún email a los socios/as.<br /><br />".$resDatosEmailAvisoCuotasNoCobradas['textoComentarios'];		
								}
								else//$resDatosEmailAvisoCuotasNoCobradas['codError'] < '80000' error sistema
								{
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:datosEmailAvisoCuotaNoCobradaSinCC()".$resDatosEmailAvisoCuotasNoCobradas['textoComentarios'].
																																																			$resDatosEmailAvisoCuotasNoCobradas['codError'].": ".$resDatosEmailAvisoCuotasNoCobradas['errorMensaje']);									
								}
								
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
							}				
							else // $resDatosEmailAvisoCuotasNoCobradas['codError'] =='00000'
							{
									$datosEmailAvisoCuotasNoCobradas['datosEmailCuotaSocios'] = $resDatosEmailAvisoCuotasNoCobradas['resultadoFilas'];    
       		$datosEmailAvisoCuotasNoCobradas['bancosAgrup'] =	$resValidarCamposFormDatosElegidos['bancosAgrup'];//será igual $arrBancos['datosAgrupaciones'] = $_POST['camposFormDatosElegidos']['bancosAgrup']
									$datosEmailAvisoCuotasNoCobradas['textoEmail'] = $_POST['camposFormDatosElegidos']['textoEmail'];
									$URLgastosLaicismo = $_POST['camposFormDatosElegidos']['URLgastosLaicismo']; 

									//echo "<br><br>3-2 cTesorero:emailAvisarCuotaNoCobradaSinCC:datosEmailAvisoCuotasNoCobradas: ";print_r($datosEmailAvisoCuotasNoCobradas);
						
			//OJO***	para prueba comentar siguiente línea	
			$resEnviarEmailSocio = emailAvisarCuotaSinCobrarSinCC($datosEmailAvisoCuotasNoCobradas,$URLgastosLaicismo);//modeloEmail.php ya inclue el tratamiento de error	
							
									//echo "<br><br>4-1 cTesorero:emailAvisarCuotaNoCobradaSinCC:resEnviarEmailSocio: ";print_r($resEnviarEmailSocio);
									
									$datosMensaje['textoComentarios'] = $resEnviarEmailSocio['arrMensaje']['textoComentarios'];//textoComentarios informa de totales enviados y también mensaje de error	envío email
									
									//echo "<br><br>4-2 cTesorero:emailAvisarCuotaNoCobradaSinCC:resEnviar: ";print_r($resEnviar);			
									
									vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);						

							}//else $resDatosEmailAvisoCuotasNoCobradas['codError'] =='00000'
						}//else if ($resValidarCamposFormDatosElegidos['codError']=='00000')//sin error	
				}// $cadBancos['codError'] == '00000'
			}//else isset($_POST['SiEnviar'])
		}//if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))  
		else //!if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))   
		{	
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';		

			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
			//echo "<br><br>5-1 cTesorero:emailAvisarCuotaNoCobradaSinCC:parValorComboAgrupa: "; print_r($parValorComboAgrupa);			
				
			if ($parValorComboAgrupa['codError'] !== '00000')
			{ $resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);			
			} 
			else //$parValorComboAgrupa['codError'] == '00000'
			{ $arrayFormDatosElegidos = array();//evitar Notice
					//echo "<br><br>5-2 cTesorero:emailAvisarCuotaNoCobradaSinCC:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);

					vEmailAvisarCuotaNoCobradaSinCCInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrayFormDatosElegidos);	

			}//else $parValorComboAgrupa['codError'] == '00000'
		}//else !if (isset($_POST['SiEnviar']) || isset($_POST['NoEnviar']))
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
//--------------------------- Fin emailAvisarCuotaNoCobradaSinCC -----------------------------------------------------*/



/*-------------------------- Inicio exportarEmailDomiciliadosPend ------------------------------------------------------
Exporta los emails de los socios en forma de lista separados por (;) a un fichero ".txt" para copiar y pegar 
en el correo de NODO50 (tesoreria@europalica.org) y enviar un email a los socios de la lista, con texto libre para 
avisar a los socios que se van enviar "las órdenes de cobro de las cuotas domiciliadas" de los socios para el cobro 
por el banco (actualmente B.Santander norma SEPA-XML), de las agrupaciones elegidas y que están de alta en el momento 
actual y según la siguiente selección:

- Cuenta bancaria en España
- Cuenta bancaria países SEPA (distintos de España) y "Ordenar cobro banco = SI", POR AHORA NO SE PUEDE GENERAR 
  EL SEPA-XML CON ESTA APLICACIÓN por falta cálculo BIC   otros países SEPA, pero se podría hacer manualmente en 
		la web del B.Santander si se consiguiesen esos BICs.

INCLUYE:		
- Estado Cuotas de socios:'PENDIENTE-COBRO','ABONADA-PARTE'
- 'ORDENARCOBROBANCO' = 'SI' (está domiciliada y hay orden de cobro)

EXCLUIDOS:
- Error email, o falta email.
- Cuotas "abonadas", "exentas (honorarios)"
- Los que se dieron de alta después de la fecha elegida en formulario (en noviembre o diciembre)

LLAMADA: cTesorero.php:menuOrdenesCobroCuotasTes(),
         vistas/tesorero/vExportarEmailDomiciliadosPendInc:formExportarEmailDomiciliadosPend.php
LLAMA: modeloTesorero.php:exportarEmailDomiciliadosPendientes()
       libs/validarCamposTesorero.php:exportarEmailAvisarCuotas()
							modeloUsuarios.php:parValoresAgrupaPresCoord()
							vistas/mensajes/vMensajeCabSalirNavInc.php

OBSERVACIONES: Probada PHP 7.3.21

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error al formar el buffer de salida a txt 
ya que utiliza "header()" y no puede haber ninguna salida delante ni notice, warning, ...

NOTA: una vez ejecutada la función exportarEmailDomiciliadosPendSinCC(), si no ha habido error la pantalla última 
permanece fija creo que por el buffer, habría que liberarla para que indique que se ha hecho bien															
----------------------------------------------------------------------------------------------------------------------*/
function exportarEmailDomiciliadosPend()
{
	//echo "<br><br>0-1 cTesorero:exportarEmailDomiciliadosPend:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{			
		require_once './modelos/modeloTesorero.php';	
		require_once './modelos/libs/validarCamposTesorero.php';		
		require_once './vistas/tesorero/vExportarEmailDomiciliadosPendInc.php';		 
		require_once './modelos/libs/arrayParValor.php'; 
		require_once './modelos/modeloEmail.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 

		$datosMensaje['textoCabecera'] = "EXPORTAR EMAILS DE SOCIOS/AS PARA ENVIAR AVISOS DE PROXIMA ORDEN DE COBRO DE CUOTAS DOMICILIADAS"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar los emails. No se han podido exportar los emails de los socios a un fichero -txt-                              
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:exportarEmailDomiciliadosPend(). Error: ';	
		$tituloSeccion = "Tesorería";		
		
		$resExportar['codError'] = '00000';	 
		
		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=exportarEmailDomiciliadosPend";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar emails socios/as avisos próximo cobro cuotas domiciliadas";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;					
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiExportar']) || isset($_POST['NoExportar']))  
		{
			if (isset($_POST['NoExportar']))
			{$datosMensaje['textoComentarios'] = 'No se han exportado los emails de los socios a un fichero -txt-';				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //(isset($_POST['SiExportar']))
			{ 
				$resValidarCamposFormExportarEmail = exportarEmailAvisarCuotas($_POST['camposFormDatosElegidos']);//en libs/validarCamposTesorero.php, probado error 		   			
																													
				//echo "<br><br>1 cTesorero:exportarEmailDomiciliadosPend:resValidarCamposFormExportarEmail: ";print_r($resValidarCamposFormExportarEmail);			

				if ($resValidarCamposFormExportarEmail['codError'] !== '00000')
				{    
					$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%'; 

					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);	
										
					if ($parValorComboAgrupa['codError']!=='00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					} 
					else
					{
						vExportarEmailDomiciliadosPendInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormExportarEmail);
					}	
				}//	if ($resValidarCamposFormExportarEmail['codError']!=='00000')		
				else	//$resValidarCamposFormExportarEmail['codError']=='00000')			
				{
					$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona el tesorero general

					$resExportar = exportarEmailDomiciliadosPendientes($codAreaCoordinacion,$_POST['camposFormDatosElegidos']);//en modeloTesorero.php, probado error, si esta bien queda parado aquí				

					//echo "<br><br>2 cTesorero:exportarEmailDomiciliadosPend:resExportar: "; print_r($resExportar);

					if ($resExportar['codError'] !== '00000')
					{
						if ($resExportar['codError'] >= '80000')//Error lógico: no hay socios que cumplan las condicciones
						{					  
								$datosMensaje['textoComentarios'] = 'No se han podido exportar los emails de los socios a un fichero -txt- <br /><br /><br />'.$resExportar['textoComentarios'];					
						}
						else//$resExportarCuotasSocios['codError'] < '80000' error sistema
						{//$datosMensaje['textoComentarios'] = 'No se han podido exportar los emails de los socios a un fichero -txt- '.$resExportar['codError'].$resExportar['errorMensaje'];
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:exportarEmailDomiciliadosPend()".
																																																	$resExportar['textoComentarios'].$resExportar['codError'].": ".$resExportar['errorMensaje']);									
						}		
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}	
					else
					{
						$datosMensaje['textoComentarios'] = "Se han exportado exportar los emails de los socios a un fichero -txt-";				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);					
					}	
				}//else	$resValidarCamposFormExportarEmail['codError']=='00000')							
			}//else isset($_POST['SiExportar'])
		}//if (isset($_POST['SiExportar']) || isset($_POST['NoExportar'])) 
			
		else //!(isset($_POST['SiExportar']) || isset($_POST['NoExportar'])  
		{	
			$codAgrup = '%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';
		
			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
			
			if ($parValorComboAgrupa['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			} 		
			else
			{
				$arrCamposFormExportarEmailInicial = array();//para evitar notice
				//$arrCamposFormExportarEmailInicial['agrupaciones']['valorCampo']= $parValorComboAgrupa['lista'];//sería para que en formulario al principio salgan seleccionadas todas agrupaciones (checked marcados)	
			
				vExportarEmailDomiciliadosPendInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrCamposFormExportarEmailInicial); 
			}
		}//else !(isset($_POST['SiExportar']) || isset($_POST['NoExportar'])  
	}//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin exportarEmailDomiciliadosPend ------------------------------------------------------*/

/*-------------------------- Inicio exportarEmailDomiciliadosPendSinCC() -----------------------------------------------
Exporta los emails de los socios en forma de lista separados por (;) a un fichero ".txt" para copiar y pegar en el 
correo de NODO50 (tesoreria@europalica.org), y enviar un email a los socios de la lista, con texto libre para avisar 
a los socios que aún "no han abonado la cuota" de las agrupaciones elegidas y que están de alta en el momento actual
y según la siguiente selección:

- No tiene cuenta bancaria domiciliada
- Tiene cuenta bancaria de países NO SEPA (o no es IBAN, ya no se permite CUENTAS NO IBAN, este caso devolverá 0 socios)
- Cuenta bancaria de países SEPA (distintos de España), junto con "Ordenar cobro banco = NO" 
  (por falta de BIC necesario para otros países SEPA, por eso se envía este email de aviso no pagado)

INCLUYE:		
- Estado Cuotas de socios:'PENDIENTE-COBRO','ABONADA-PARTE','NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA',
  'NOABONADA-ERROR-CUENTA' 
- 'ORDENARCOBROBANCO' = 'NO' (no está domiciliada o no hay orden de cobro)

EXCLUIDOS:
- Error email, o falta email.
- Cuotas "abonadas", "exentas (honorarios)"
- Los que se dieron de alta después de la fecha elegida en formulario (en noviembre o diciembre)

LLAMADA: cTesorero.php:menuOrdenesCobroCuotasTes(),
         vistas/tesorero/vExportarEmailDomiciliadosPendSinCCInc:formExportarEmailDomiciliadosPendSinCC.php
LLAMA: modeloTesorero.php:exportarEmailSinCCSEPAPendientes()
       libs/validarCamposTesorero.php:exportarEmailAvisarCuotas()
							modeloUsuarios.php:parValoresAgrupaPresCoord()
							vistas/mensajes/vMensajeCabSalirNavInc.php

OBSERVACIONES: Probada PHP 7.3.21

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error al formar el buffer de salida a txt
 ya que utiliza "header()" y no puede haber ninguna salida delante ni notice, warning, ...

NOTA: una vez ejecutada la función exportarEmailDomiciliadosPendSinCC(), si no ha habido error la pantalla última 
permanece fija creo que por el buffer, habría que liberarla para que indique que se ha hecho bien															
----------------------------------------------------------------------------------------------------------------------*/
function exportarEmailDomiciliadosPendSinCC()
{
	//echo "<br><br>0-1 cTesorero:exportarEmailDomiciliadosPendSinCC:_POST: "; print_r($_POST);	
	
	if ($_SESSION['vs_autentificado'] !== 'SI' ||	$_SESSION['vs_autentificadoGestor'] !== 'SI' || $_SESSION['vs_ROL_5'] !== 'SI' )		
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else //if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
	{				
		require_once './modelos/modeloTesorero.php';	
		require_once './modelos/libs/validarCamposTesorero.php';		
		require_once './vistas/tesorero/vExportarEmailDomiciliadosPendSinCCInc.php';		 
		require_once './modelos/libs/arrayParValor.php'; 
		require_once './modelos/modeloEmail.php';
		require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';  

		$datosMensaje['textoCabecera'] = "EXPORTAR EMAILS DE SOCIOS/AS SIN DOMICILIACIÓN BANCARIA PARA INFORMAR DE CUOTA ANUAL AÚN NO PAGADA"; 
		$datosMensaje['textoComentarios'] = "<br /><br />Error al exportar los emails. No se han podido exportar los emails de los socios a un fichero -txt-                              
																																							<br /><br />Prueba de nuevo pasado un rato. 
																																							Si el problema continúa envía un email indicando el problema y tus datos a adminusers@europalaica.org<br /><br />";
		$nomScriptFuncionError = ' cTesorero.php:exportarEmailDomiciliadosPendSinCC(). Error: ';	
		$tituloSeccion = "Tesorería";	
			
		$resExportar['codError'] = '00000';	
		
		//------------ inicio navegacion -------------------------------------------		
		$_SESSION['vs_HISTORIA']['enlaces'][4]['link'] = "index.php?controlador=cTesorero&accion=exportarEmailDomiciliadosPendSinCC";
		$_SESSION['vs_HISTORIA']['enlaces'][4]['textoEnlace'] = "Exportar email socios/as para avisos cuotas aún no pagadas";			
		$_SESSION['vs_HISTORIA']['pagActual'] = 4;					
		require_once './controladores/libs/cNavegaHistoria.php';	
		$navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Anterior");
		//------------ fin navegacion -----------------------------------------------
		
		if (isset($_POST['SiExportar']) || isset($_POST['NoExportar']))  
		{
			if (isset($_POST['NoExportar']))
			{
				$datosMensaje['textoComentarios'] = 'No se han exportado los emails de los socios a un fichero -txt-';				
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
			}
			else //isset($_POST['SiExportar'])
			{				
				$resValidarCamposFormExportarEmail = exportarEmailAvisarCuotas($_POST['camposFormDatosElegidos']);//en libs/validarCamposTesorero.php, probado error 		
																													
				//echo "<br><br>1 cTesorero:exportarEmailDomiciliadosPendSinCC:$resValidarCamposFormExportarEmail: ";print_r($resValidarCamposFormExportarEmail);			

				if ($resValidarCamposFormExportarEmail['codError'] !== '00000')
				{    
					$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
					$valDefCodAgrupa = '%'; 

					$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);//en arrayParValor.php	
										
					if ($parValorComboAgrupa['codError'] !== '00000')
					{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
					} 
					else
					{   
						vExportarEmailDomiciliadosPendSinCCInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$resValidarCamposFormExportarEmail);						
					}	
				}//	if ($resValidarCamposFormExportarEmail['codError']!=='00000')		
				else	//$resValidarCamposFormExportarEmail['codError']=='00000')			
				{							
					$codAreaCoordinacion = '%';//para que sirva para todas las áreas de coordinación: lo gestiona el tesorero general
								
					$resExportar = exportarEmailSinCCSEPAPendientes($codAreaCoordinacion,$_POST['camposFormDatosElegidos']);//en modeloTesorero.php, probado error, si esta bien queda parado aquí				
					
					//echo "<br><br>2 cTesorero:exportarEmailDomiciliadosPendSinCC:resExportar: "; print_r($resExportar);
			
					if ($resExportar['codError'] !== '00000')
					{
						if ($resExportar['codError'] >= '80000')//Error lógico: no hay socios que cumplan las condicciones
						{					  
								$datosMensaje['textoComentarios'] = 'No se han podido exportar los emails de los socios a un fichero -txt- <br /><br /><br />'.$resExportar['textoComentarios'];					
						}
						else//$resExportarCuotasSocios['codError'] < '80000' error sistema
						{//$datosMensaje['textoComentarios'] = 'No se han podido exportar los emails de los socios a un fichero -txt- '.$resExportar['codError'].$resExportar['errorMensaje'];
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.": modeloTesorero.php:exportarEmailSinCCSEPAPendientes()".
																																																	$resExportar['textoComentarios'].$resExportar['codError'].": ".$resExportar['errorMensaje']);									
						}		
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
					}				
					else
					{
						$datosMensaje['textoComentarios'] = "Se han exportado exportar los emails de los socios seleccionado a un fichero -txt-";				
						vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);				
					}	
				}//else $resValidarCamposFormExportarEmail['codError']=='00000')				
			}//else isset($_POST['SiExportar'])	
		}//if if (isset($_POST['SiExportar']) || isset($_POST['NoExportar']))  
			
		else //!(isset($_POST['SiExportar']) || isset($_POST['NoExportar'])  
		{	
			$codAgrup ='%'; //buscar todas las agrupaciones por ser tesorero general por defecto		
			$valDefCodAgrupa = '%';
		
			$parValorComboAgrupa = parValoresAgrupaPresCoord($codAgrup,$valDefCodAgrupa);
			
			if ($parValorComboAgrupa['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorComboAgrupa['codError'].": ".$parValorComboAgrupa['errorMensaje']);
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);							
			} 
			else
			{
				$arrCamposFormExportarEmailInicial = array();//para evitar notice
				//$arrCamposFormExportarEmailInicial['agrupaciones']['valorCampo']= $parValorComboAgrupa['lista'];//sería para que en formulario al principio salgan seleccionadas todas agrupaciones (checked marcados)	

				vExportarEmailDomiciliadosPendSinCCInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$navegacion,$parValorComboAgrupa,$arrCamposFormExportarEmailInicial); 
			}
		}//else !(isset($_POST['SiExportar']) || isset($_POST['NoExportar'])  
 }//else if ($_SESSION['vs_autentificado'] == 'SI' &&	$_SESSION['vs_autentificadoGestor'] == 'SI' && $_SESSION['vs_ROL_5'] == 'SI')	
}
/*--------------------------- Fin exportarEmailDomiciliadosPendSinCC -------------------------------------------------*/

/*==== FIN: FUNCIONES RELACIONADAS CON EMAILS COBRO CUOTAS ===========================================================*/

/*==== FIN: FUNCIONES RELACIONADAS CON ÓRDENES COBRO CUOTAS BANCOS ===================================================*/



?>