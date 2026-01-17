<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: modeloAdmin.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: Tiene funciones relacionadas con las tareas del rol de "Administrador":

- cambiarControlModoAdmin(): para cambiar el modo entre 'EXPLOTACION'<->'MANTENIMIENTO'	
- mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo) y funciones relacionadas: 
  función que actualiza los datos de las tablas para cierre Año Finalizado e inicio de Año Nuevo

- Como almacén, quedan algunas funciones que se usaron anteriormente por si pudieran servir más adelante:
  encriptarCCyCEXAdmin(), desEncriptarCCyCEXAdmin()
  importarExcelSociosES(), importarExcelSociosAN(), generarTodosOrd()
													
LLAMADO: desde "cAdmin.php 			 
OBSERVACIONES: Necesita ROL de administrador


--------------------------------------------------------------------------------------------------*/

require_once 'BBDD/MySQL/modeloMySQL.php';//dejarlo o quitarlo si ya está en cada lugar

/*-------- Inicio Fechas y horas de BBDD MySQL y servidor PHP (Como información) Hacer Función -----
Devuelve un array con los datos de Fechas y horas de la BBDD MySQL,  y del servidor PHP, y también
zona horaria system por defecto de PHP. 

Añadida como dato informativo que puede ser útil para  y en tareas relacionadas con 
"Cierre Anio Pasado y Apertura Anio Nuevo", y modoExplotacionAdmin(),modoMantenimientoAdmin()
 
LLAMADA: cAdmin.php:adminPrincipal(),modoExplotacionAdmin(),modoMantenimientoAdmin()
LLAMA: BBDD/MySQL/configMySQL.php:conexionDB(, /BBDD/mySQL.php:buscarCadSql()

DEVUELVE: array con datos fechas y errores

Lo normal es que estén configuradas BBDD MySQL y servidor PHP para tener iguales valores de fechas 
OBSEVACIONES: Probada PDO con PHP 7.3.21
-------------------------------------------------------------------------------------------------*/
function fechaHoraServidor_BBDD()
{		
 $resFechaHoraServidor_BBDD['codError'] ='00000';
	$resFechaHoraServidor_BBDD['errorMensaje'] ='';
	
 $fechaHoraServidor = date("Y-m-d H:i:s e");// "e" nos indicará la zona horaria system por defecto del servidor PHP
 //echo "<br><br>1a modeloAdmin.php:fechaHoraServidor_BBDD:fecha local del servidor con date(): ".$fechaHoraServidor;
	
	$zonaHorariaServidor = date_default_timezone_get(); //zona horaria system por defecto del servidor PHP
	//echo "<br><br>1b modeloAdmin.php:fechaHoraServidor_BBDD:zona horaria system por defecto de PHP: date_default_timezone_get(): ".$zonaHorariaServidor;	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError']!=='00000')	
	{ $resFechaHoraServidor_BBDD = $resulHoraBBDD;
			$resFechaHoraServidor_BBDD['errorMensaje'] = "Error del sistema al conectarse a la base de datos para fechaHoraServidor_BBDD()"; 	
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'
	{
  $cadSqlHora = "SELECT SYSDATE() AS fechaHoraBBDD";//SYSDATE() returns the time at which it MYSQL executes.

  $resulHoraBBDD = 	buscarCadSql($cadSqlHora,$conexionDB['conexionLink']);//en modeloMySQL.php
		
		//echo "<br><br>1c modeloAdmin.php:fechaHoraServidor_BBDD:resulHoraBBDD['resultadoFilas'][0]['FechaHoraBBDD']: ";print_r($resulHoraBBDD['resultadoFilas'][0]['fechaHoraBBDD'])."<br><br>";	

  if ($resulHoraBBDD['codError'] !=='00000')
  {$resFechaHoraServidor_BBDD = $resulHoraBBDD;
			$resFechaHoraServidor_BBDD['errorMensaje'] = "Error del sistema al al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD()"; 	 
	  $arrMensaje['textoComentarios']="Error del sistema al buscar SYSDATE de la BBDD para fechaHoraServidor_BBDD(),vuelva a intentarlo pasado un tiempo ";			
  }
  else 
	 { $resFechaHoraServidor_BBDD['fechaHoraBBDD'] = $resulHoraBBDD['resultadoFilas'][0]['fechaHoraBBDD'];
				
				$resFechaHoraServidor_BBDD['fechaHoraServidor'] = $fechaHoraServidor;
				$resFechaHoraServidor_BBDD['$zonaHorariaServidor'] = $zonaHorariaServidor;			
				
				//echo "<br><br>1d modeloAdmin.php:fechaHoraServidor_BBDD:resFechaHoraServidor_BBDD: ";print_r($resFechaHoraServidor_BBDD);	  
  }	
		//$cadSqlHora = "SELECT @@global.time_zone as GLOBAL, @@session.time_zone AS SESION,@@global.system_time_zone";
		//Devuelve [@@global.time_zone]=>SYSTEM [@@session.time_zone]=>SYSTEM [@@global.system_time_zone]=>CET
		//The value 'SYSTEM' indicates that the time zone should be the same as the system time zone (del servidor donde suele estar también PHP).
		//@@global.system_time_zone: CET –Central Europe Time. CET: Areas with same time currently (UTC +1). CET, Abbreviations like CET will always be a winter time.
		//Central European Time (CET) is 1 hour ahead of Coordinated Universal Time (UTC). This time zone is in use during standard time in: Europe, Africa.		 
	}
	//echo "<br><br>1e modeloAdmin.php:fechaHoraServidor_BBDD:resFechaHoraServidor_BBDD: ";print_r($resFechaHoraServidor_BBDD);	
		
		return $resFechaHoraServidor_BBDD;
}
/*-- Fin Fechas y horas de la BBDD MySQL y del servidor PHP -------------------------------------*/


/*====================== INICIO procesos de mCierreAnioPasadoAperturaAnioNuevoAdmin()==============
Se realizan los procesos de  "Cierre de Año Pasado y Apertura de Año Nuevo" que implica e uso de la 
función "mCierreAnioPasadoAperturaAnioNuevoAdmin()" y otras muchas funciones utilizadas por ella

Se debe	ejecutar solo una vez por año "el 1 de enero a partir las 00:01 horas" y es necesario hacerlo
en modo mantenimiento.
Antes hacer un backup de BBDD "europalaica_com" y combiene PROBAR ANTES EN BBDD "europalaica_com_copia"
o "europalaica_com_desarrollo"

Tardará cierto tiempo en ejecutarse este proceso, recorre y modifica muchas tablas. No salir del 
navegador hasta que aparezca un mensaje indicando que le proceso ha finalizado, o un aviso de error.
==================================================================================================*/

/*-------------------------- buscarModoAp() ------------------------------------------------------
Buscar en la tabla "CONTROLMODOAPLICACION", la única fila que tiene, para obtener el campo 
"MODOAPLICACION" que solo puede tener dos valores "EXPLOTACION" o "MANTENIMIENTO" que nos 
informan en qué estado se encuenta la aplicación.
En estado "MANTENIMIENTO" solo podrán acceder los usuarios que tengan el rol "20" asignado
															
RECIBE: nada
DEVUELVE: un array con valor campo ['MODOAPLICACION'] y controles de errores.          

LLAMADA: modeloLogin.php:validarLogin()
LLAMA: BBDD/MySQL/modeloMySQL.php:buscarCadSql()
       usuariosConfig/BBDD/MySQL/configMySQL.php		
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
							modelos/modeloErrores.php:insertarError()
							
OBSERVACIONES: Probado con PDO en PHP 7.3.21
Nota: acaso estuviese mejor en un posible modeloLogin.php
---------------------------------------------------------------------------------------------------*/
function buscarModoAp()
{
	require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	require_once './modelos/modeloErrores.php'; 
	
 $arrBuscarModoAp['nomScript'] = 'modeloUsuarios.php';	
 $arrBuscarModoAp['nomFuncion'] = 'buscarModoAp';	
	$arrBuscarModoAp['codError'] = '00000';
	$arrBuscarModoAp['errorMensaje'] = '';	
	$arrBuscarModoAp['textoComentarios'] = '';		

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $arrBuscarModoAp = $conexionDB;
	}
	else	//$conexionDB['codError'] == '00000'
	{	
		$cadSQLBuscarModoAp = "SELECT * FROM CONTROLMODOAPLICACION "; 
		
		$arrBind = array();
		
		//echo "<br><br>2-1 modeloAdmin.php:buscarModoAp:cadSQLBuscarModoAp: ";print_r($cadSQLBuscarModoAp);
			
		$resBuscarModoAp = buscarCadSql($cadSQLBuscarModoAp,$conexionDB['conexionLink'],$arrBind);//modeloMySQL.php, probado error ok
		
		//echo "<br><br>2-2 modeloAdmin.php:buscarModoAp:resBuscarModoAp: ";print_r($resBuscarModoAp);

		if ($resBuscarModoAp['codError'] !== '00000')// error sistema			
		{ $arrBuscarModoAp['codError'] = $resBuscarModoAp['codError'];
		  $arrBuscarModoAp['errorMensaje'] = $resBuscarModoAp['errorMensaje'];
				$arrBuscarModoAp['textoComentarios'] = ". Error del sistema, al buscar en CONTROLMODOAPLICACION";
				$resInsertarErrores = insertarError($arrBuscarModoAp,$conexionDB['conexionLink']);		
		}
		else //$resBuscarModoAp['codError'] =='00000')		
		{	$arrBuscarModoAp = $resBuscarModoAp;		
		
				if ($resBuscarModoAp['numFilas'] == 0)//si es alta como usuario debe tener un rol o es un error del sistema
				{	
						$arrBuscarModoAp['codError'] = '70100';									
						$arrBuscarModoAp['errorMensaje'] = 'Error al buscar en USUARIOTIENEROL,ROL';
						$arrBuscarModoAp['textoComentarios'] = ". Error del sistema, al buscar en CONTROLMODOAPLICACION";
						$resInsertarErrores = insertarError($arrBuscarModoAp,$conexionDB['conexionLink']);											
				}						
		}
	}//else	$conexionDB['codError'] == '00000'
	
 //echo '<br><br>3 modeloAdmin.php:buscarModoAp:buscarControlActualizadoAnio:arrBuscarModoAp: ';print_r($arrBuscarModoAp);	
	
	return $arrBuscarModoAp;
}
/*---------------------- Fin buscarModoAp() -------------------------------------------------------*/	

/*------------------- Inicio cambiarControlModoAdmin()  -------------------------------------------
Cambia los valores de la única fila de la tabla 'CONTROLMODOAPLICACION' especialmente el campo 
[MODOAPLICACION'] que podrá tener solo los valores MANTENIMIENTO o EXPLOTACIÓN. (Un UPDATE)

RECIBE: el string "$modoAplicacion" que será =  'EXPLOTACION' o 'MANTENIMIENTO'	
DEVUELVE: un array "$arrCambiarControlModo", con información de posibles errore

LLAMADA: cAdmin.php:cambiarExplotacion_MantenimientoAdmin()

LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php
							modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
							modelos/BBDD/MySQL/modeloMySQL.php:actualizarTabla_ParamPosicion()		
							modelos/modeloErrores.php:insertarError()

OBSERVACIONES: Probada PHP 7.3.21, usa PDO 
--------------------------------------------------------------------------------------------------*/
function cambiarControlModoAdmin($modoAplicacion)
{ 
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";		
	require_once './modelos/modeloErrores.php'; 
	
 $arrCambiarControlModo['nomScript'] = 'modeloAdmin.php';
 $arrCambiarControlModo['nomFuncion'] = 'cambiarControlModo';	
 $arrCambiarControlModo['codError'] = '00000';
 $arrCambiarControlModo['errorMensaje'] = '';
	$arrCambiarControlModo['textoComentarios'] ='';
		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !== '00000')	
	{ $arrCambiarControlModo = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'	
	{	//echo '<br><br>1 modeloAdmin.php:cambiarControlModo:conexionDB: ';var_dump($conexionDB);	
		
		$tablaAct = 'CONTROLMODOAPLICACION';
		
		$arrayCondiciones['MODOAPLICACION']['valorCampo'] = '********';
		$arrayCondiciones['MODOAPLICACION']['operador'] = '!= ';
		$arrayCondiciones['MODOAPLICACION']['opUnir'] = ' ';
		
		$arrayDatos['MODOAPLICACION'] = $modoAplicacion;				
		$arrayDatos['FECHACAMBIOMODO'] = date("Y-m-d H:i:s"); 
		$arrayDatos['OBSERVACIONES'] = ''; 

		//if (!isset($arrayCondiciones) || empty($arrayCondiciones) || $arrayCondiciones == NULL)//Para una información más especifica del error 

  $resActTabla = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']);//en modeloMySQL.php 	
		
		//también se podría utilizar: function ejecutarCadSql($cadSql,$conexionDB,$arrBindValues = NULL)
		
		//echo '<br><br>2 modeloAdmin.php:cambiarControlModo:resActTabla: ';print_r($resActTabla);
		
		if ($resActTabla['codError'] !== '00000')
		{$arrCambiarControlModo['codError'] = $resActTabla['codError'];	
			$arrCambiarControlModo['errorMensaje'] = $resActTabla['errorMensaje'];	
	  $arrCambiarControlModo['textoComentarios'] .= "Error del sistema al actualizar tabla CONTROLMODOAPLICACION, vuelva a intentarlo pasado un tiempo ";

			$resInsertarErrores = insertarError($arrCambiarControlModo,$conexionDB['conexionLink']); 
			
			if ($resInsertarErrores['codError'] !== '00000')
	  {$arrCambiarControlModo = $resInsertarErrores;
				$arrCambiarControlModo['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resActTabla['codError']!=='00000'
		else
		{ $arrCambiarControlModo['textoComentarios'] = "Se ha Cambiado el modo de trabajo de la aplicación de Gestión de Soci@s"; // no necesario		
		}		
	}// else $conexionDB['codError']=='00000'	

	//echo "<br><br>3 modeloAdmin.php:cambiarControlModo:arrCambiarControlModo: ";print_r($arrCambiarControlModo);
	
	return $arrCambiarControlModo;
} 
/*----------------------------- Fin cambiarControlModo() -----------------------------------------*/


/*---------------------------- Inicio mCierreAnioPasadoAperturaAnioNuevoAdmin ---------------------
Esta función actualiza los datos de las tablas para cierre Año Finalizado e inicio de Año Nuevo

-Primero en "buscarControlActualizadoAnio()" controla en la tabla CONTROLES, si previamente ya ha 
 sido ejecutada con éxito esta función, para el nuevo año. Si ya está ejecutada, se sale del proceso
 con un mensaje de aviso de que ya están actualizadas las tablas para el Año Nuevo.
-En	"buscarCuotaAnioNuevoEL()" obtiene los importes de las cuotas del la asociación vigentes para 
 el nuevo año, que se utilizaran después en las siguientes funciones.
-En "buscarCuotasSociosAnioAnteriorAnioNuevo()"	obtiene los datos de las cuotas y otros de los socios
 para el año que ha finalizado (Anterior) y los que ya hubiese para el nuevo año (AnioNuevo)

-En los dos BUCLES se buscan qué socios en "CUOTAANIOSOCIO" ya tienen una fila para AnioNuevo y 
 se comprueba si son baja o si su cuota es inferior a la de EL, para ejecutar la función adecuada:	
	-Si un socio se hubiese dado de baja en el año Anterior pero ya tuviese fila en "CUOTAANIOSOCIO" 
		con datos para año Nuevo	en "borrarFilaCuotaSocioAnioNuevoBaja()" se borrará la fila con los datos
		del socio para el año Nuevo, aunque se mantienen las del anterior a efectos de tesorería.		
	-Se comprueba si las cuotas del socio con fila en "CUOTAANIOSOCIO" para año Nuevo, son inferiores a 
		las correspondientes a ese año Nuevo para la asociación que están en "IMPORTECUOTAANIOEL" (se han 
		podido incrementar la cuota anual de la Asociación del nuevo año), y en ese caso se actualizan todas
		las cuotas en "actualizarCuotasSociosExistentesAnioNuevo()" con los nuevos importes de EL (ya que no 
		pueden ser inferiores las cuotas de los socios a las nuevas cuotas para ese año establecidas por EL)
	-Al cambiar de año al siguiente en tabla "CUOTAANIOSOCIO" la función "insertarCuotaSocioAnioNuevo()" 
		insertar las filas con la cuota del socio (IMPORTECUOTAANIOSOCIO) y otros datos de los socios desde 
		el año Anterior al añon Nuevo.
		Pero no se insertan si en "CUOTAANIOSOCIO" ya existiese fila para AnioNuevo para ese socio.
-Fin bucles

-En CUOTAANIOSOCIO, para año Anterior la función "actualizaCuotasSociosAnioAnteriorPendiente()" 
 actualiza el estado de la cuota de los socio de PENDIENTE-COBRO (pasa a)-> NOABONADA	
-En función "eliminarDatosSocioBajas5Anios(). Se aplicada a datos privados de socios con baja hace 5
 años o más). Elimina las filas 'MIEMBROELIMINADO5ANIOS', Anula el campo "CUENTAPAGO de tabla 
	CUOTAANIOSOCIO" y el campo "CUENTAIBAN de la tabla ORDENES_COBRO".		
-En anularSociosPendientesConfirAdmin() en tabla "SOCIOSCONFIRMAR se pone NULL a los datos personales,  
 y en la  tabla "USUARIO"	 se pone USUARIO.ESTADO = ANULADA-SOCITUD-REGISTRO

-Se actualiza la tabla "CONTROLES" y se pone el campo de cambio año  "ACTUALIZADO ='SI'", se inserta 
 una nueva fila para el año siguiente y se borran las filas hasta 2 años anteriores 
	
-Forma el string "$cadResumenProceso" que contiene la información de los totales de filas modificadas
 en las tablas durante la ejecución de esta función y para pasarlo al controlador 
	cAdmin:cierreAnioPasadoAperturaAnioNuevoAdmin() para mostralo por pantalla en campo ['textoComentarios']

RECIBE: $añoNuevo desde el controlador cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin() que 
        viene desde el formulario: vistas/admin/formCierreAnioPasadoAperturaAnioNuevo.php, y podrá tener 
								dos valores: para año actual $añoNuevo=date('Y'), para simular año siguiente $añoNuevo=date('Y')+1
         		
DEVUELVE: array "	$arrCierreAperturaAnio" que en el campo [comentarios] 
          incluye "$cadResumenProceso"	con detallada información, además de campos de control de errores															

LLAMADA: cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: modeloMySQLphp:buscarCadSql(), 
       modeloAdmin.php:buscarControlActualizadoAnio(), buscarCuotaAnioNuevoEL(),
							buscarCuotasSociosAnioAnteriorAnioNuevo(), borrarFilaCuotaSocioAnioNuevoBaja(),
							actualizarCuotasSociosExistentesAnioNuevo(), insertarCuotaSocioAnioNuevo(),
							actualizaCuotasSociosAnioAnteriorPendiente(), eliminarDatosSocioBajas5Anios(),
							insertarImporteCuotasAnioSiguiente(), anularSociosPendientesConfirAdmin(),
							actualizarTablaControles()
							modelos/modeloErrores.php:insertarError()
							usuariosConfig/BBDD/MySQL/configMySQL.php	
       BBDD/MySQL/conexionMySQL.php:conexionDB()

REQUISITOS: día 1 de enero, con rol de adminsitrador de la aplicación y en modo "MANTANIMIENTO",
para evitar posibles modificaciónes y bloqueos simultáneos de la tablas, además de poder recuperar 
previo backup en caso de error.

PARA PRUEBAS: incluso probar antes del 1 de enero, ir a las versiones de prueba de Gestión de Soci@s
URLs: "europalaica.com/usuarios_copia", o en "europalaica.com/usuarios_desarrollo", de esto modo sólo
se pueden modificar las correspondientes BBDD: "europalaica_com_copia" o "europalaica_com_desarrollo"
Para probar antes del 1 de enero eleige el botón correspondiente Y+1

OBSERVACIONES: Probado con PDO en PHP 7.3.21

Para mayor independencia no utilizo función ya existentes: modeloSocios.php:actualizCuotaAnioSocio(). 
Dado que es un proceso crítico es mejor evitar depender de modeloSocios.php por si se modifica.	

NOTA: Esta función es un proceso crítico y para mayor independencia no utilizo funciones ya existentes
que podría reutilizar aquí como: modeloUsuario.php:actualizUsuario(), 
modeloSocios.php:actualizCuotaAnioSocio() y actualizarSocioConfirmar(). 
Es mejor evitar depender de modeloUsuario.php, modeloSocios.php por si se hace modificaciones en ellos.			
--------------------------------------------------------------------------------------------------*/
//function actualizarCuotasSociosAnioNuevo($anioNuevo)//antes
function mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo)
{
	//echo "<br><br>0-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:anioNuevo: ";print_r($anioNuevo);
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		
	require_once './modelos/modeloErrores.php'; 
	
	$arrCierreAperturaAnio['codError'] = "00000";
	$arrCierreAperturaAnio['errorMensaje'] = "";	
	$arrCierreAperturaAnio['textoComentarios'] = '';
 
 $anioAnterior = $anioNuevo - 1;//$anioAnterior (Y-1) que ha terminado	= 2019	//	$anioNuevo = 2020 (Y)
	$anioSiguiente = $anioNuevo + 1;//el siguiente al que ha empezado	= 2021	(Y+1)
		
 //------------- Inicio NOMBRES TABLAS utilizadas --------------------------------------------------	
	
 $tControles = "CONTROLES"; //guarda los valores de control para ejecutar o no las procesos de cambio año
 $tUsuario = "USUARIO"; //identificación de login y pass
	$tCuotaSocios = "CUOTAANIOSOCIO";	//cuotas pagadas y adeudas de los socios por años	
	$tSocios      = "SOCIO";	//datos específicos de los socios, CUENTAIBAN, ...
	$tImporteCuotaAnios = "IMPORTEDESCUOTAANIO"; //guarda valor de las cuotas vigentes en EL por años y tipos
	$tMiembroElim5Anios = 'MIEMBROELIMINADO5ANIOS'; //datos socios de baja y se guardan 5 años por motivos fiscales
	$tSocioConfirmar = "SOCIOSCONFIRMAR"; //datos de socios pendientes de confirmar su alta	
	$tOrdenesCobro = "ORDENES_COBRO"; //datos de las órdenes de cobro al banco de la cuotas domiciliadas
	
 //------------- FIN NOMBRES TABLAS -----------------------------------------------------------------	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);//probado error ok	
	
	if ($conexionDB['codError'] !== "00000")
	{ $arrCierreAperturaAnio = $conexionDB;
			$arrCierreAperturaAnio['textoComentarios'] = "Error del sistema al conectarse a la base de datos, no se han modificado las tablas de la BBD"; 
	}	
 else //(1) $conexionUsuariosDB['codError']=="00000"
	{
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);		
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;							
				$resInsertar = $resIniTrans;
				
			 $arrCierreAperturaAnio = $resIniTrans;	
				$arrCierreAperturaAnio['textoComentarios'] = "Error del sistema al iniciase la transación, no se han modificado las tablas de la BBD"; 				
		}
		else //(2) $resIniTrans['codError'] == '00000'
		{
			/*---- Inicio comprobar si ya se ha ejecutado para el presente año esta función "mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo)" --
			  Si en la tabla "CONTROLES" el campo tiene el valor ACTUALIZADO=SI, para el valor de "$anioNuevo", entonces no dejará 
			  seguir con está función ya que solo se puede realizar una vez al inicio del año, 
			------------------------------------------------------------------------------------------------------------------------------*/
				
				$resBuscarControlActualizadoAnio = buscarControlActualizadoAnio($tControles,$anioNuevo);//en modeloAdmin.php, incluye conexion()
			
  		//echo "<br><br>1-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:resBuscarControlActualizadoAnio: ";print_r($resBuscarControlActualizadoAnio);			
								
				if ($resBuscarControlActualizadoAnio['codError'] !== '00000')// error sistema			
				{$arrCierreAperturaAnio = $resBuscarControlActualizadoAnio;				
				}
				else//(3) !if(($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ANIO']==$anioNuevo)&&($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ACTUALIZADO'] =='SI'))
				{     
				 if ($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ANIO'] == $anioNuevo && $resBuscarControlActualizadoAnio['resultadoFilas'][0]['ACTUALIZADO'] == 'SI')
					{ 
					  $arrCierreAperturaAnio['codError'] = '82050';//error lógico no se debe seguir con la función mCierreAnioPasadoAperturaAnioNuevoAdmin()
							$arrCierreAperturaAnio['textoComentarios'] .= "<strong>AVISO: no se puede realizar el proceso de apertura de nuevo año ".$anioNuevo.
				   " porque ya está actualizado, con fecha ".$resBuscarControlActualizadoAnio['resultadoFilas'][0]['FECHAACTUALIZACION']."</strong>";
	      
							//echo "<br><br>1-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: ";print_r($arrCierreAperturaAnio);										
					}     
				 /*---- Fin comprobar si ya se ha ejecutado para el presente año esta función "mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo)" ---*/
			  else//(3-1)
     {		
      //echo "<br><br>1-3 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: ";print_r($arrCierreAperturaAnio);								
						
						/*--- Inicio obtener las cuotas de EL vigentes para el Año Nuevo en tabla "IMPORTEDESCUOTAANIO" ------------------------------*/    

						$resCuotaAnioNuevoEL = buscarCuotaAnioNuevoEL($tImporteCuotaAnios,$anioNuevo,$conexionDB['conexionLink']);//en modeloAdmin.php
						//echo "<br><br>2-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:resCuotaAnioNuevoEL: "; print_r($resCuotaAnioNuevoEL); 
						
						if ($resCuotaAnioNuevoEL['codError'] !== '00000')			
						{$arrCierreAperturaAnio = $resCuotaAnioNuevoEL;		
							$arrCierreAperturaAnio['textoComentarios'] .=". Error del sistema al buscar en IMPORTEDESCUOTAANIO";
						}
						else //(4) else $resCuotaAnioNuevoEL['codError'] =='00000' y $resCuotaAnioNuevoEL['numFilas'] !== 0
						{ $arrCuotasAnioNuevoEL = $resCuotaAnioNuevoEL['arrCuotasAnioNuevoEL'];								
							
							/*--- Fin obtener las cuotas de EL vigentes para el Año Nuevo en tabla "IMPORTEDESCUOTAANIO" ---------------------------------*/
			
							/*--- Inicio buscar datos de cuotas socios de Año Anterior, de Año Nuevo -----------------------------------------------------*/
						
							$resBuscarCuotasSociosAnioAnteriorAnioNuevo = buscarCuotasSociosAnioAnteriorAnioNuevo($tUsuario,$tCuotaSocios,$tSocios,$anioNuevo,$conexionDB['conexionLink']);//en modeloAdmin.php
						
							//echo "<br><br>2-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:buscarCuotasSociosAnioAnterior:resBuscarCuotasSociosAnioAnteriorAnioNuevo: ";print_r($resBuscarCuotasSociosAnioAnteriorAnioNuevo);
						
							if ($resBuscarCuotasSociosAnioAnteriorAnioNuevo['codError'] !== '00000')
							{$arrCierreAperturaAnio = $resBuscarCuotasSociosAnioAnteriorAnioNuevo;
								$arrCierreAperturaAnio['textoComentarios'] .= ". Error del sistema, al buscar en CUOTAANIOSOCIO ";
							}		
							else//(5) else $resBuscarCuotasSociosAnioAnteriorAnioNuevo['codError']=='00000'
							{						
								$cuotasSociosAnioAnterior = $resBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioAnterior']['resultadoFilas'];							
								$numFilaAnteriorTotal = $resBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioAnterior']['numFilas'];//no puede ser =0			
					
								$cuotasSociosAnioNuevo = $resBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioNuevo']['resultadoFilas'];		
								$numFilaNuevoTotal = $resBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioNuevo']['numFilas'];//también puede ser =0 aun poco probable
								
								/*--- Fin buscar datos de cuotas socios de Año Anterior, de Año Nuevo  -----------------------------------------------------*/				
					
								//== Inicio bucle para buscar en tabla "CUOTAANIOSOCIO" coincidencias de socio de año anterior(Y-1) y año nuevo(Y) ===
								// Busca en "$cuotasSociosAnioAnterior" y $cuotasSociosAnioNuevo  procedentes de tabla CUOTAANIOSOCIO, socios en 
								// año anterior que ya tengan también datos en año nuevo. Habrá algunas filas para los casos de socios que hayan pagado
								// la cuota del año anterior y han elegido nueva cuota para año siguiente, y en este caso pudiera ser <,=,> a la nueva
								// cuota aprobada por EL para el año siguiente.
								// Dentro del bucle se efectúan las siguientes operaciones que algunas podrían ser funciones independientes:
								// - Actualizar Cuotas Socios Anio Anterior "CUOTAANIOSOCIO" los que tienen PENDIENTE-COBRO->NOABONADA;
								//====================================================================================================================
					
								//-------- Iniciar contadores: solo se utilizan para mostrar totales al final del proceso ---------------
								$contadorInsercionesAnioNuevo = 0;
								$contadorBajasAnioAnteriorNoInserta = 0;
								$contadorActualizaExistenteCuotaAnioNuevo = 0;	
								$contadorBorrarBajaExistenteCuotaAnioNuevo = 0;					
								//-------- Fin iniciar contadores -----------------------------------------------------------------------

								$numFilaAnterior = 0;							
				
								while ( $numFilaAnterior < $numFilaAnteriorTotal && $arrCierreAperturaAnio['codError'] == '00000' )
								{        									
									//---- Inicio Bucle para buscar socios que ya hayan anotado nueva cuota para año nuevo (Y) -----------
									// Busca la coincidencia de ['CODSOCIO'] en $anioAnterior (Y-1) y $anioNuevo	(Y)
									// se podría hacer una búsqueda ordenada por ['CODSOCIO'] para optimizar tiempo.		
									//----------------------------------------------------------------------------------------------------
									$numFilaNuevo = 0;
									$encontrado = 'NO';						
					
									while ($numFilaNuevo < $numFilaNuevoTotal && $encontrado == 'NO')//si $numFilaNuevoTotal=0 no entrará nunca
									//while ($numFilaNuevo < $numFilaNuevoTotal && $encontrado == 'NO' && $arrCierreAperturaAnio['codError'] == '00000' )//si $numFilaNuevoTotal=0 no entrará nunca
									{ 
										if ($cuotasSociosAnioAnterior[$numFilaAnterior]['CODSOCIO'] == $cuotasSociosAnioNuevo[$numFilaNuevo]['CODSOCIO']) //$encontrado = 'SI';
										{$encontrado = 'SI';	//busca en tabla CUOTAANIOSOCIO socios de año anterior que ya tengan datos en año nuevo
						
											//*---- Inicio Borrar posibles filas de socios que se dieron de baja el año anterior, pero tiene datos en año nuevo -------*/
											if($cuotasSociosAnioNuevo[$numFilaNuevo]['ESTADO'] == 'baja')//es baja pero si tiene cuota en año Nuevo=>Borrar cuota año nuevo    
											{ 
													$reBorrarCuotasSociosAnioNuevo = borrarFilaCuotaSocioAnioNuevoBaja($tCuotaSocios,$cuotasSociosAnioNuevo[$numFilaNuevo],$conexionDB['conexionLink']);//modeloAdmin.php, error OK
													//echo "<br><br>3-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:reBorrarCuotasSociosAnioNuevo: ";print_r($reBorrarCuotasSociosAnioNuevo);		
				
													if ($reBorrarCuotasSociosAnioNuevo['codError'] !== '00000')			
													{$arrCierreAperturaAnio = $reBorrarCuotasSociosAnioNuevo;
													}
													else
													{ ++$contadorBorrarBajaExistenteCuotaAnioNuevo;//solo para mostrar totales
													}
											}	/*---- Fin Borrar posibles filas de socios que se dieron de baja el año anterior ----------------------------------------*/  
											
											/*----- Inicio Actualiza filas de socios CUOTAANIOSOCIO que ya tenían anotaciones en nuevo año, si es necesario ------------
														actualiza el IMPORTECUOTAANIOEL en tabla CUOTAANIOSOCIO si IMPORTECUOTAANIOEL de anioNuevo > IMPORTECUOTAANIOEL de añoAnterior 
														o bien si IMPORTECUOTAANIOSOCIO si es < IMPORTECUOTAANIOEL. 			
														NOTA: Ya no se entrará en esta función porque ya no se dará la condición, pues en realidad  ya se actualizaron al cambiar la 
														cuota IMPORTECUOTAANIOEL, por el tesorero desde el menú "-Cuotas vigentes en EL". ---------------------------------------*/
														
											else//$cuotasSociosAnioNuevo[$numFilaNuevo]['ESTADO'] !== 'baja'			
											{								 
												$reActualizarCuotasSociosAnioNuevo = actualizarCuotasSociosExistentesAnioNuevo($tCuotaSocios,$cuotasSociosAnioNuevo[$numFilaNuevo],$arrCuotasAnioNuevoEL,$conexionDB['conexionLink']);//modeloAdmin.php, error ok
												
												//echo "<br><br>3-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:actualizarCuotasSociosExistentesAnioNuevo: ";print_r($reActualizarCuotasSociosAnioNuevo);																																																								
											
												if ($reActualizarCuotasSociosAnioNuevo['codError'] !== '00000')			
												{$arrCierreAperturaAnio = $reActualizarCuotasSociosAnioNuevo;
													// $numFilaNuevo = $numFilaNuevoTotal; se puede añadir 
												}
												else
												{if ($reActualizarCuotasSociosAnioNuevo['numFilas'] ==1)
													{++$contadorActualizaExistenteCuotaAnioNuevo;//solo para mostrar totales											
													}
												}							
											}//else si $encontrado=='SI' y if($cuotasSociosAnioNuevo[$numFilaNuevo]['ESTADO'] !== 'baja') es ALTA
											/*---- Fin Actualizar filas de socios que ya tenían anotaciones en el nuevo año ----------------------------------------------*/							
										}							
										else//$encontrado =='NO'
										{$numFilaNuevo++;																																																						
										}
									}
									//echo "<br><br>3-3 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:encontrado: ";print_r($encontrado);
									//----Fin Bucle para buscar socios que ya hayan anotado nueva cuota para año nuevo (Y)------------------------------------------ 
					
									if ( $encontrado == 'NO' )//en tabla CUOTAANIOSOCIO no hay fila en anio Nuevo que se corresponda con Anterior para ese socio		
									{
										/*--- Inicio: Si no hay datos socio en CUOTAANIOSOCIO para año nuevo (Y+1), se inserta una fila con los datos -------------------
												que de $cuotasSociosAnioAnterior[$numFilaAnterior] modificando los campos que sean necesarios 
										-------------------------------------------------------------------------------------------------------------------------------*/
									
										if($cuotasSociosAnioAnterior[$numFilaAnterior]['ESTADO'] !== 'baja')//si no es baja hay que insertar una fila  
										{	
											$reInsertarCuotasSociosAnioNuevo = insertarCuotaSocioAnioNuevo($tCuotaSocios,$cuotasSociosAnioAnterior[$numFilaAnterior],$arrCuotasAnioNuevoEL,$anioNuevo,$conexionDB['conexionLink']);//modeloAdmin.php, probado error ok		
											//echo "<br><br>3-4 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:reInsertarCuotasSociosAnioNuevo: ";print_r($reInsertarCuotasSociosAnioNuevo);

											if ($reInsertarCuotasSociosAnioNuevo['codError'] !== '00000')			
											{$arrCierreAperturaAnio = $reInsertarCuotasSociosAnioNuevo;
												$arrCierreAperturaAnio['textoComentarios'] .=". Error del sistema al insertar filas para el nuevo año en CUOTAANIOSOCIO, en insertarCuotaSocioAnioNuevo()";
											}
											else
											{++$contadorInsercionesAnioNuevo;//solo para mostrar totales	
											}																																																				
										}
										else //if($cuotasSociosAnioAnterior[$numFilaAnterior]['ESTADO'] == 'baja')
										{++$contadorBajasAnioAnteriorNoInserta;	//solo para mostrar totales			
										}
										/*--- Fin: Si no hay datos socio en CUOTAANIOSOCIO para año nuevo (Y+1), se inserta una fila con los datos -------------------*/
										
									}//if ($encontrado == 'NO')//no hay fila en anioNuevo que se corresponda con anioAnterior	
												
									$numFilaAnterior++;
									
								}// while ($numFilaAnterior < $numFilaAnteriorTotal && $arrCierreAperturaAnio['codError'] == '00000' )
									
								//===== Fin bucle para buscar en tabla CUOTAANIOSOCIO coincidencias de socio de año anterior y año nuevo =====
							
								//echo "<br><br>3-5 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: "; print_r($arrCierreAperturaAnio); 
								
								if ($arrCierreAperturaAnio['codError'] == '00000')//(6) if $arrCierreAperturaAnio['codError']=='00000'
								{
									/*--- Inicio Actualiza "CUOTAANIOSOCIO" de año anterior para socios [ESTADOCUOTA]==PENDIENTE-COBRO=>NOABONADA ------------------
										Se hace para todos los socios: los que no tienen cuota en año siguiente y para los que la tienen
										------------------------------------------------------------------------------------------------------------------------------*/
			
									$reActualizaCuotasSociosAnioAnteriorPendiente = actualizaCuotasSociosAnioAnteriorPendiente($tCuotaSocios,$anioAnterior,$conexionDB['conexionLink']);//modeloAdmin.php error ok
									//echo "<br><br>4 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:reActualizaCuotasSociosAnioAnteriorPendiente:"; print_r($reActualizaCuotasSociosAnioAnteriorPendiente); 						
									
									if ($reActualizaCuotasSociosAnioAnteriorPendiente['codError'] !== '00000')			
									{
										$arrCierreAperturaAnio = $reActualizaCuotasSociosAnioAnteriorPendiente;		
										$arrCierreAperturaAnio['textoComentarios'] .= ".Error del sistema al actualizar en CUOTAANIOSOCIO. "; 
									}
									else //(7) else $resActualizaCuotasSociosAnioAnteriorPendiente['codError'] =='00000')
									{$arrCierreAperturaAnio['textoComentarios'] .= $reActualizaCuotasSociosAnioAnteriorPendiente['textoComentarios']; 
													
										/*--- Fin Actualiza "CUOTAANIOSOCIO" de año anterior para socios [ESTADOCUOTA]==PENDIENTE-COBRO=>NOABONADA ---------------------
										
										/*--- Inicio eliminarDatosSocioBajas5Anios (se aplica a datos de socios con baja hace 5 años o más) ----------------------------
													Elimina las filas 'MIEMBROELIMINADO5ANIOS', con baja hace cinco años o más y que se guardaban por motivos fiscales, 
													Anula el campo "CUENTAPAGO de tabla CUOTAANIOSOCIO" y el campo "CUENTAIBAN de la tabla ORDENES_COBRO", pago con cuenta 
													domiciliada. Se podrían pasar lon nombres de las tablas como parámetros 
										-------------------------------------------------------------------------------------------------------------------------------*/
										//Podría pasar como parámetro: tCuotaSocios="CUOTAANIOSOCIO"; $tMiembroElim5Anios='MIEMBROELIMINADO5ANIOS';	$tOrdenesCobro="ORDENES_COBRO";							
						
										$resEliminarDatosSocioBajas5Anios = eliminarDatosSocioBajas5Anios($tCuotaSocios,$tSocios,$tOrdenesCobro,$tMiembroElim5Anios,$anioNuevo,$conexionDB['conexionLink']);//modeloAdmin.php		probado error

										//echo "<br><br>5 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:resEliminarDatosSocioBajas5Anios: "; print_r($resEliminarDatosSocioBajas5Anios);				
											
										if ($resEliminarDatosSocioBajas5Anios['codError'] !== '00000')										
										{$arrCierreAperturaAnio = $resEliminarDatosSocioBajas5Anios;		
											$arrCierreAperturaAnio['textoComentarios'] .= ".Error del sistema al eliminar filas MIEMBROELIMINADO5ANIOS, CUOTAANIOSOCIO, ORDENES_COBRO ";
										}
										else //(8) else $resEliminarSocioBajas5Anios['codError'] =='00000' 
										{//if ($resEliminarSocioBajas5Anios['numFilas'] !== 0)//No lo tratará como error en ROLLBACK
											$arrCierreAperturaAnio['textoComentarios'] .= $resEliminarDatosSocioBajas5Anios['textoComentarios'];
										
											/*--- Fin  eliminarDatosSocioBajas5Anios de todos socios con baja hace 5 años --------------------------------------------------*/	

											/*--- Inicio Inserta en tabla IMPORTEDESCUOTAANIO los valores de cuotas para AnioSiguiente (Y+1)=uno mas que AnioNuevo=(Y) ------ 
													Si estuviese actualizada (si ya tiene fila con valores para AnioSiguiente) no se insertaría nada. ---------------------------*/
					
											$insertarCuotaAnioSiguienteEL = insertarImporteCuotasAnioSiguiente($tImporteCuotaAnios,$anioNuevo,$conexionDB['conexionLink']);//modeloAdmin.php	probado error ok																		
											//echo "<br><br>6 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:insertarCuotaAnioSiguienteEL:"; print_r($insertarCuotaAnioSiguienteEL); 						
			
											if ($insertarCuotaAnioSiguienteEL['codError'] !== '00000')		
											{$arrCierreAperturaAnio = $insertarCuotaAnioSiguienteEL;
											}
											else //(9) else $insertarCuotaAnioSiguienteEL['codError'] =='00000') 
											{$arrCierreAperturaAnio['textoComentarios'] .= $insertarCuotaAnioSiguienteEL['textoComentarios'];
												/*--- Fin Inserta en tabla IMPORTEDESCUOTAANIO los valores de cuotas para AnioSiguiente (Y+1)=uno mas que AnioNuevo=(Y) --------- 
												
												/*------------ Inicio anularSociosPendientesConfirAdmin() -----------------------------------------------------------------------	        
														Se ponen a NULL los datos personales  SOCIOSCONFIRMAR y USUARIO.ESTADO=ANULADA-SOCITUD-REGISTRO, 
														para aquellos que SOCIOSCONFIRMAR.FEACHAALTA < AnioNuevo=(Y) y no se guarda ningún dato 
														en MIEMBROELIMINADO5ANIOS, ya que no ha llegado a ser confirmado como socio 
												--------------------------------------------------------------------------------------------------------------------------------*/		
											
												$resAnularSociosPendConfir = anularSociosPendientesConfirAdmin($tUsuario,$tSocioConfirmar,$anioNuevo,$conexionDB['conexionLink']);//está en modeloAdmin.php probado error ok
											
												//echo "<br><br>7-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:resAnularSociosPendConfir:"; print_r($resAnularSociosPendConfir); 
													
												/*------------ Fin anulaSociosPendientesConfirAdmin -----------------------------------------------------------------------------*/
		
												if ($resAnularSociosPendConfir['codError'] !== '00000')			
												{$arrCierreAperturaAnio = $resAnularSociosPendConfir;		
													$arrCierreAperturaAnio['textoComentarios'] .= ". Error del sistema al buscar en SOCIOSCONFIRMAR, o USUARIO ";
												}
												else //(10) else $resAnularSociosPendConfir['codError'] =='00000')	
												{
													$arrCierreAperturaAnio['textoComentarios'] .= $resAnularSociosPendConfir['textoComentarios'];
							
													// $cadResumenProceso:	contiene los datos al mostrar al final de del proceso, y también se graban en tabla CONTROLES 
													
													$cadResumenProceso = "- Se han insertado ".$contadorInsercionesAnioNuevo." filas en la tabla CUOTASANIOSOCIO para cuotas del nuevo anio ".$anioNuevo.
													"<br /><br />- Se han actualizado ".$contadorActualizaExistenteCuotaAnioNuevo." filas en la tabla CUOTASANIOSOCIO, del las ".$numFilaNuevoTotal." ya existentes del nuevo anio ".$anioNuevo.
													" necesarios como: ORDENARCOBROBANCO (NO-->SI) o otros".
													"<br /><br />- No se han insertado ".$contadorBajasAnioAnteriorNoInserta." filas en la tabla CUOTASANIOSOCIO para el anio ".$anioNuevo.", por ser baja los socios/as en el anio ".$anioAnterior.
													"<br /><br />- Se han eliminado ".$contadorBorrarBajaExistenteCuotaAnioNuevo." filas existentes en la tabla CUOTASANIOSOCIO para el anio ".$anioNuevo.
													", por haber sido baja los socias/os en anio ".$anioAnterior.												
													"<br /><br />".$arrCierreAperturaAnio['textoComentarios'];
									
													/*--- Inicio actualizar, insertar y eliminar filas en tabla "CONTROLES" -----------------------------------------------------------
															Para poder prevenir la ejecución, no deseada, de repetición de los procesos de actualización de nuevo año, para "$anioNuevo" 
															se pone el campo de cambio año  "ACTUALIZADO = SI" y en "RESUMENPROCESO" se copia	el contenido de la columna "$cadResumenProceso" 
															Se inserta una nueva fila	para "$anioSiguiente" con campo de cambio año "ACTUALIZADO = NO" 
													---------------------------------------------------------------------------------------------------------------------------------*/       									
													//echo "<br><br>7-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:actualizarTablaControles:cadResumenProceso: ";print_r($cadResumenProceso);	
																									
													$reActualizarTablaControles = actualizarTablaControles($tControles,$anioNuevo,$cadResumenProceso,$conexionDB['conexionLink']);//en modeloAdmin.php probado error ok
													
													//echo "<br><br>8-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:reActualizarTablaControles: ";print_r($reActualizarTablaControles);												

													$reActualizarTablaControles['errorMensaje'] ='PRUEBA DE ROLLBACK *************';
													
													if ($reActualizarTablaControles['codError'] !== '00000')
													{$arrCierreAperturaAnio = $reActualizarTablaControles;
														$arrCierreAperturaAnio['textoComentarios'] .= ". Error del sistema, al actualizar CONTROLES ";
													}
													/*------------ Fin actualizar, insertar y eliminar filas en tabla CONTROLES --------------------------------------------------------*/
													else// (11) else resActualizarControlActualizadoAnio['codError'] = 00000' y $resInsertarControlActualizadoAnio['codError']=='00000'
													{
														$cadResumenActualizarTablaControl = $reActualizarTablaControles['textoComentarios'];
														
														$cadResumenProceso = $cadResumenProceso."<br />".$cadResumenActualizarTablaControl.
																			"<br /><br /><br /><strong>--- HA FINALIZADO EL PROCESO DE CIERRE DE AÑO Y ADECUACIONES PARA EL NUEVO ANIO ".$anioNuevo." SIN ERRORES DETECTADOS ---</strong><br /><br />";

														//echo "<br><br>8-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:actualizarTablaControles:cadResumenProceso: ";print_r($cadResumenProceso);												

														/*---------------- Inicio COMMIT --------------------------------*/												
														$resFinTrans = commitPDO($conexionDB['conexionLink']);
																					
														if ($resFinTrans['codError'] !== '00000')//será $resFinTrans['codError'] = '70502'; 									
														{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación. ';
																$resFinTrans['numFilas'] = 0;	
																$arrMensaje['textoComentarios'] = 'Error en el sistema, no se ha podido completar el preceso. ';				
																
																$arrCierreAperturaAnio = $resFinTrans;	
																//echo "<br><br>8-3 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: ";print_r($arrCierreAperturaAnio);
														}
														/*---------------- Fin COMMIT -----------------------------------*/				
														else //se ha ejecutado correctamente
														{													 
																$arrCierreAperturaAnio['textoComentarios'] = $cadResumenProceso;
														}												
													}//(11) else resActualizarControlActualizadoAnio['codError']=00000' y $resInsertarControlActualizadoAnio['codError']=='00000'	
												}//(10) else $resAnularSociosPendConfir['codError'] =='00000')	
											}//(9) else $insertarCuotaAnioSiguienteEL['codError'] =='00000') 
										}//(8) else $resEliminarSocioBajas5Anios['codError'] =='00000' 
									}//(7) else $resActualizaCuotasSociosAnioAnteriorPendiente['codError'] =='00000')		  
								}//(6) if $arrCierreAperturaAnio['codError']=='00000'
							}//(5) else $resBuscarCuotasSociosAnioAnteriorAnioNuevo['codError']=='00000'
						}//(4) else $resCuotaAnioNuevoEL['codError'] =='00000' y $resCuotaAnioNuevoEL['numFilas'] !== 0
					}//(3-1)
				}//(3) else !if(($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ANIO']==$anioNuevo)&&($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ACTUALIZADO'] =='SI'))
			
			//echo '<br><br>9-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: ';print_r($arrCierreAperturaAnio);			
	  
			if ($arrCierreAperturaAnio['codError'] !== '00000')
			{				
				$deshacerTrans = rollbackPDO($conexionDB['conexionLink']);					
				//echo '<br><br>9-2 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:deshacerTrans: ';var_dump($deshacerTrans);					
						
				if ($deshacerTrans ['codError'] !== '00000')//será $deshacerTrans['codError'] = '70503';			
				{ $deshacerTrans['errorMensaje'] = 'Error en el sistema, no se ha podido deshacer la transación. ';
						$deshacerTrans['numFilas'] = 0;	
						
						$arrCierreAperturaAnio = $resDeshacerTrans;						
						$arrCierreAperturaAnio['textoComentarios'] .= "<br /><br />. ERROR al deshacer transación";		
				}			
				else
				{	
				  $arrCierreAperturaAnio['textoComentarios'] .= "<br /><br /><strong>NOTA: se han deshecho todas las transaciones que se hubiesen efectuado en este proceso
                                                                                        			y no se ha realizado ningún cambio en las tablas</strong>";
    }
				$arrInsertarErrores = $arrCierreAperturaAnio;
				$arrInsertarErrores['nomScript'] = 'modeloAdmin.php';
				$arrInsertarErrores['nomFuncion'] = 'mCierreAnioPasadoAperturaAnioNuevoAdmin';
				$arrInsertarErrores['textoComentarios'] = $arrCierreAperturaAnio['textoComentarios'];    
				$resInsertarErrores = insertarError($arrInsertarErrores);//modeloErrores.php, probado
				
				//echo '<br><br>9-3 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:resInsertarErrores: ';print_r($resInsertarErrores);	
				
			}//if ($arrCierreAperturaAnio['codError']!=='00000'))				
  }//else (2) $resIniTrans
 }//else (1) $conexionUsuariosDB['codError']=="00000"
																																															
	//echo '<br><br>10 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:arrCierreAperturaAnio: ';print_r($arrCierreAperturaAnio);
	
	return $arrCierreAperturaAnio;
}
/*------------------------------ Fin mCierreAnioPasadoAperturaAnioNuevoAdmin() -----------------------------*/

/*-------------------------- buscarControlActualizadoAnio() -------------------------------------------------
Comprobar si ya se ha ejecutado para el presente año las funciones:
 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin, y "modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin($anioNuevo)"

Si al buscar en la tabla "CONTROLES", el campo tiene el valor ACTUALIZADO=SI, para el valor de "$anioNuevo",
entonces no dejará seguir con está función ya que solo se puede realizar una vez al inicio del año.
															
RECIBE: $tControles tabla ="CONTROLES", $anioNuevo, $conexionDB que es = $conexionDB['conexionLink']
DEVUELVE: un array concontroles de errores, si 'ACTUALIZADO'] =='SI' lo pone como error lógico
          para no seguir con la actualización

LLAMADA: cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin, y modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:buscarCadSql()
       usuariosConfig/BBDD/MySQL/configMySQL.php
		     BBDD/MySQL/conexionMySQL.php
							
OBSERVACIONES: Probado con PDO en PHP 7.3.21
------------------------------------------------------------------------------------------------------------*/
//function buscarControlActualizadoAnio($tControles,$anioNuevo,$conexionLinkDB)
function buscarControlActualizadoAnio($tControles,$anioNuevo)
{
 //echo "<br><br>0-1 modeloAdmin:buscarControlActualizadoAnio: ";print_r($anioNuevo);	
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		
	
	$arrBuscarControlActualizadoAnio['codError'] = '00000';
	$arrBuscarControlActualizadoAnio['errorMensaje'] = '';	
	$arrBuscarControlActualizadoAnio['textoComentarios'] = '';		
	
	if (!isset($anioNuevo) || empty($anioNuevo) )		
 {$arrBuscarControlActualizadoAnio['codError'] = '706010';
  $arrBuscarControlActualizadoAnio['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrBuscarControlActualizadoAnio['textoComentarios'] .= ". Error del sistema al buscar en tabla CONTROLES, en buscarControlActualizadoAnio()";
	}
	else //	!if ( !isset($anioNuevo) || empty($anioNuevo) )		
	{	
		require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
		require_once "BBDD/MySQL/conexionMySQL.php";
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);//probado error ok	
		
		if ($conexionDB['codError'] !== "00000")
		{ $arrBuscarControlActualizadoAnio = $conexionDB;
				$arrBuscarControlActualizadoAnio['textoComentarios'] = "Error del sistema al conectarse a la base de datos, no se han modificado las tablas de la BBD"; 
		}	
		else // $conexionUsuariosDB['codError']=="00000"
		{
				$cadSQLBuscarControlActualizadoAnio = "SELECT * FROM ".$tControles." WHERE ANIO = :anioNuevo"; //anio es int(11) cambiarlo ???
				
				$arrBind = array( ':anioNuevo' => $anioNuevo);
				
				//echo "<br><br>2-1 modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin:buscarControlActualizadoAnio:cadSQLBuscarControlActualizadoAnio: ";print_r($cadSQLBuscarControlActualizadoAnio);
					
				$resBuscarControlActualizadoAnio = buscarCadSql($cadSQLBuscarControlActualizadoAnio,$conexionDB['conexionLink'],$arrBind);//modeloMySQL.php, probado error ok
				
				//echo "<br><br>2-2 modeloAdmin:buscarControlActualizadoAnio:resBuscarControlActualizadoAnio: ";print_r($resBuscarControlActualizadoAnio);

				if ($resBuscarControlActualizadoAnio['codError'] !== '00000')// error sistema			
				{$arrBuscarControlActualizadoAnio = $resBuscarControlActualizadoAnio;
					$arrBuscarControlActualizadoAnio['textoComentarios'] = ". Error del sistema, al buscar en CONTROLES";
				}
				else //$resBuscarControlActualizadoAnio['codError']=='00000')		
				{ /*			
					if (($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ANIO'] == $anioNuevo)	&& ($resBuscarControlActualizadoAnio['resultadoFilas'][0]['ACTUALIZADO'] =='SI'))
					{	$arrBuscarControlActualizadoAnio['codError'] = '82050';//error lógico no se debe seguir con la función mCierreAnioPasadoAperturaAnioNuevoAdmin()
						$arrBuscarControlActualizadoAnio['textoComentarios'] .= "<strong>AVISO: no se puede realizar el proceso de apertura de nuevo año ".$anioNuevo.
						" porque ya está actualizado, con fecha ".$resBuscarControlActualizadoAnio['resultadoFilas'][0]['FECHAACTUALIZACION']."</strong>";;			
					}	
					*/			
					$arrBuscarControlActualizadoAnio = $resBuscarControlActualizadoAnio;
				}
		
	 }//else $conexionUsuariosDB['codError']=="00000"
	}//else 	!if (!isset($anioNuevo) || empty($anioNuevo) )	
	
 //echo '<br><br>3 modeloAdmin:buscarControlActualizadoAnio:arrBuscarControlActualizadoAnio: ';print_r($arrBuscarControlActualizadoAnio);	
	
	return $arrBuscarControlActualizadoAnio;
}
/*---------------------- Fin buscarControlActualizadoAnio() ------------------------------------------------*/	

/*-------------------------- buscarCuotaAnioNuevoEL() -------------------------------------------------------
Obtiene las cuotas de la asiciación de EL vigentes para el Año Nuevo de tabla "IMPORTEDESCUOTAANIO"
															
RECIBE: $tControles tabla ="IMPORTEDESCUOTAANIO", $anioNuevo, $conexionUsuariosDB que es = $conexionUsuariosDB['conexionLink']
DEVUELVE: un array "$arrCuotasAnioNuevoEL" con las cuotas de EL vigentes para el nuevo año, 
para cada tipo: General, Parado, Joven, Honorario y además los concontroles de errores

LLAMADA: modeloAdmin: mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:buscarCadSql()
							
OBSERVACIONES: Probado con PDO en PHP 7.3.21
-----------------------------------------------------------------------------------------------------------*/
function buscarCuotaAnioNuevoEL($tImporteCuotaAnios,$anioNuevo,$conexionLinkDB)
{
	//echo "<br><br>0-1 modeloAdmin:buscarCuotaAnioNuevoEL: ";print_r($anioNuevo);	
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		

	$arrBuscarCuotaAnioNuevoEL['codError'] = '00000';
	$arrBuscarCuotaAnioNuevoEL['errorMensaje'] = '';	
	$arrBuscarCuotaAnioNuevoEL['textoComentarios'] = '';		
	
	if (!isset($anioNuevo) || empty($anioNuevo) )		
 { $arrBuscarCuotaAnioNuevoEL['codError'] = '706010';
   $arrBuscarCuotaAnioNuevoEL['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		 $arrBuscarCuotaAnioNuevoEL['textoComentarios'] .= ". Error del sistema al buscar en tabla CONTROLES, en buscarControlActualizadoAnio()";
	}
	else //	!if ( !isset($anioNuevo) || empty($anioNuevo) )		
	{	
			//--- Inicio obtener las cuotas de EL vigentes para el Año Nuevo en tabla "IMPORTEDESCUOTAANIO" ----------
			
			$cadSQLBuscarCuotaAnioNuevoEL = "SELECT * FROM ".$tImporteCuotaAnios." WHERE ANIOCUOTA = :anioNuevo ";//ANIOCUOTA varchar(4)
			
			$arrBind = array( ':anioNuevo' => $anioNuevo);
			//echo "<br><br>2-0 modeloAdmin:buscarCuotaAnioNuevoEL:cadSQLBuscarCuotaAnioNuevoEL: "; print_r($cadSQLBuscarCuotaAnioNuevoEL); 
						
			$resCuotaAnioNuevoEL = buscarCadSql($cadSQLBuscarCuotaAnioNuevoEL,$conexionLinkDB,$arrBind );//modeloMySQL.php,	probado error ok	
			
			//echo "<br><br>2-1 modeloAdmin:buscarCuotaAnioNuevoEL:resCuotaAnioNuevoEL: "; print_r($resCuotaAnioNuevoEL); 
								
			if ($resCuotaAnioNuevoEL['codError'] !== '00000')			
			{$arrBuscarCuotaAnioNuevoEL = $resCuotaAnioNuevoEL;		
				$arrBuscarCuotaAnioNuevoEL['textoComentarios'] .= ". Error del sistema al buscar en IMPORTEDESCUOTAANIO";
			}
			elseif ($resCuotaAnioNuevoEL['numFilas'] == 0)//Creo que nunca se dará este caso porque automaticamente se añaden al actualizar año anterior
			{$arrBuscarCuotaAnioNuevoEL['codError'] = '80001';//Para que haga el rollback, si no hay filas encontradas
				$arrBuscarCuotaAnioNuevoEL['textoComentarios'] .= ". No existe un importe de cuota para el nuevo anio: primero debe establecerse 
				                                                                   el importe de la nueva cuota por el gestor autorizado (tesorería)";
			}
			else // else $resCuotaAnioNuevoEL['codError'] =='00000' y $resCuotaAnioNuevoEL['numFilas'] !== 0
			{
				//--- Inicio Preparar cuotas Anio Nuevo EL en array asociativo "$arrCuotasAnioNuevoEL" 
				// ejemplo:Array([General]=>40.00 [Honorarario]=>0.00 [Joven]=>5.00 [Parado]=>4.00), se utilizará más adelante en varios sitios
				//----------------------------------------------------------------------------
				$f = 0;
				
				while ($f < $resCuotaAnioNuevoEL['numFilas'])
				{
					$codCuota = $resCuotaAnioNuevoEL['resultadoFilas'][$f]['CODCUOTA']; 
					$arrCuotasAnioNuevoEL[$codCuota] = $resCuotaAnioNuevoEL['resultadoFilas'][$f]['IMPORTECUOTAANIOEL'];
				
					$f++;
				}								
			}
			$arrBuscarCuotaAnioNuevoEL['arrCuotasAnioNuevoEL'] = $arrCuotasAnioNuevoEL;	
			
	}	//	else 	!if ( !isset($anioNuevo) || empty($anioNuevo) )	
	
	//echo "<br><br>3 modeloAdmin:buscarCuotaAnioNuevoEL:arrBuscarCuotaAnioNuevoEL: "; print_r($arrBuscarCuotaAnioNuevoEL); 
	
	return $arrBuscarCuotaAnioNuevoEL;
}	
/*-------------------------- buscarCuotaAnioNuevoEL() ------------------------------------------------------*/

/*-------------------------- buscarCuotasSociosAnioAnteriorAnioNuevo() ---------------------------------------
Buscar datos de tabla CUOTAANIOSOCIO, de Año Anterior date(Y-1), de Año Nuevo date(Y) 
y USUARIO.ESTADO (alta, baja), SOCIO.IMPORTECUOTASOCIO, CUENTAIBAN, 
															
RECIBE: tablas ="$tUsuario,$tCuotaSocios,$tSocios", $anioNuevo, y $conexionUsuariosDB que es = 
        $conexionUsuariosDB['conexionLink']
DEVUELVE: un array "$arrBuscarCuotasSociosAnioAnteriorAnioNuevo" con los datos anteriores de cada
socio y además los controles de errores

LLAMADA: modeloAdmin: mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:buscarCadSql()
							
OBSERVACIONES: Probada PDO. PHP 7.3.21
-----------------------------------------------------------------------------------------------------------*/
function buscarCuotasSociosAnioAnteriorAnioNuevo($tUsuario,$tCuotaSocios,$tSocios,$anioNuevo,$conexionLinkDB)
{
	//echo "<br><br>0-1 modeloAdmin:buscarCuotasSociosAnioAnteriorAnioNuevo:anioNuevo: "; print_r($anioNuevo); 		
	
	//------------- NOMBRES TABLAS utilizadas ----------------------------------------------------------	 
 //$tUsuario = "USUARIO"; //identificación de login
	//$tCuotaSocios = "CUOTAANIOSOCIO";	//cuotas pagadas y adeudas de los socios por años	
	//$tSocios      = "SOCIO";	//datos específicos de los socios, 
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		
	
 $arrBuscarCuotasSociosAnioAnteriorAnioNuevo['codError'] = '00000';
	$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['errorMensaje'] = '';	
	$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['textoComentarios'] = '';	
	
	if (!isset($anioNuevo) || empty($anioNuevo) )		
 {$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['codError'] = '706010';
  $arrBuscarCuotasSociosAnioAnteriorAnioNuevo['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['textoComentarios'] .= ". Error del sistema al buscar en tablas  USUARIO,CUOTAANIOSOCIO,SOCIO en buscarCuotasSociosAnioAnteriorAnioNuevo()";
	}
	else //	!if ( !isset($anioNuevo) || empty($anioNuevo) )		
	{		
		$anioAnterior = $anioNuevo - 1;//$anioAnterior (Y-1) que ha terminado	= 2019	//	$anioNuevo = 2020 (Y)	

		//------------- Inicio formación de cadenas SQL ------------------------------------------------------	
		$tablasBusqueda   = " ".$tUsuario.",".$tCuotaSocios.",".$tSocios." ";		
		
		$camposBuscados   = " ".$tCuotaSocios.".*, ".$tUsuario.".ESTADO,".
																										$tSocios.".IMPORTECUOTAANIOSOCIO as IMPORTECUOTAANIOSOCIOElegida,".	                       																					
																										$tSocios.".CUENTAIBAN,".$tSocios.".CUENTANOIBAN ";																					

		$cadCondicionesBuscarAnio = " WHERE ".$tUsuario.".CODUSER=".$tSocios.".CODUSER
																																								AND ".$tSocios.".CODSOCIO=".$tCuotaSocios.".CODSOCIO 
																																								AND ".$tCuotaSocios.".ANIOCUOTA = :anioCuota ".																									
																																								" ORDER BY ".$tCuotaSocios.".CODSOCIO ";	
																																							
		$arrBindAnterior = array( ':anioCuota' => $anioAnterior);			
		$arrBindNuevo = array( ':anioCuota' => $anioNuevo);

		$cadSQLBuscarCuotasSociosAnio = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscarAnio";	 
		
		//echo "<br><br>1 modeloAdmin:buscarCuotasSociosAnioAnteriorAnioNuevo:cadSQLBuscarCuotasSociosAnio: ";print_r($cadSQLBuscarCuotasSociosAnio);	

		$resBuscarCuotasSociosAnioAnterior = buscarCadSql($cadSQLBuscarCuotasSociosAnio,$conexionLinkDB,$arrBindAnterior);//modeloMySQL.php,	probado error ok
		//echo "<br><br>2-1 modeloAdmin:buscarCuotasSociosAnioAnteriorAnioNuevo:resBuscarCuotasSociosAnioAnterior: ";print_r($resBuscarCuotasSociosAnioAnterior);
		
		if ($resBuscarCuotasSociosAnioAnterior['codError'] !== '00000')//nunca debiera ser numFilas == 0
		{ $arrBuscarCuotasSociosAnioAnteriorAnioNuevo = $resBuscarCuotasSociosAnioAnterior;
				$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['textoComentarios'] .= ". Error del sistema al buscar en CUOTAANIOSOCIO. buscarCuotasSociosAnioAnteriorAnioNuevo()";
		}
		elseif ($resBuscarCuotasSociosAnioAnterior['numFilas'] <= 0)//nunca debiera ser numFilas == 0
		{
				$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['codError'] = '80001';
				$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['textoComentarios'] .= ". Error del sistema al buscar en CUOTAANIOSOCIO. buscarCuotasSociosAnioAnteriorAnioNuevo() ";
		}
		else	
		{ 	
				$resBuscarCuotasSociosAnioNuevo = buscarCadSql($cadSQLBuscarCuotasSociosAnio,$conexionLinkDB,$arrBindNuevo);//modeloMySQL.php,	probado error ok
				//echo "<br><br>2-2 modeloAdmin:buscarCuotasSociosAnioAnteriorAnioNuevo:resBuscarCuotasSociosAnioNuevo: ";print_r($resBuscarCuotasSociosAnioNuevo);
				
				if ($resBuscarCuotasSociosAnioNuevo['codError'] !== '00000') //casi imposible ser numFilas  == 0
				{ $arrBuscarCuotasSociosAnioAnteriorAnioNuevo = $resBuscarCuotasSociosAnioNuevo;
					 $arrBuscarCuotasSociosAnioAnteriorAnioNuevo['textoComentarios'] .= ". Error del sistema, al buscar en CUOTAANIOSOCIO. buscarCuotasSociosAnioAnteriorAnioNuevo() ";
				}
				else //else $resBuscarCuotasSociosAnioNuevo['codError']=='00000' y $resBuscarCuotasSociosAnioAnterior['codError']=='00000'
				{
						$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioAnterior']['resultadoFilas'] = $resBuscarCuotasSociosAnioAnterior['resultadoFilas'];				
						$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioAnterior']['numFilas'] = $resBuscarCuotasSociosAnioAnterior['numFilas'];									

						$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioNuevo']['resultadoFilas'] = $resBuscarCuotasSociosAnioNuevo['resultadoFilas'];
						$arrBuscarCuotasSociosAnioAnteriorAnioNuevo['cuotasSociosAnioNuevo']['numFilas'] = $resBuscarCuotasSociosAnioNuevo['numFilas'];
				}
		}	
 }//else 	!if ( !isset($anioNuevo) || empty($anioNuevo) )	
	
 //echo "<br><br>4 modeloAdmin:buscarCuotasSociosAnioAnteriorAnioNuevo:arrBuscarCuotasSociosAnioAnteriorAnioNuevo:"; print_r($arrBuscarCuotasSociosAnioAnteriorAnioNuevo); 		
	
 return $arrBuscarCuotasSociosAnioAnteriorAnioNuevo;
}
/*-------------------------- Fin buscarCuotasSociosAnioAnteriorAnioNuevo() ---------------------------------*/

/*---------------------------- Inicio  borrarFilaCuotaSocioAnioNuevoBaja() ----------------------------------
Elimina en tabla CUOTAANIOSOCIO la fila de un socio que se dio de baja  el año anterior, 
pero que ya tiene datos de cuotas anotados en CUOTAANIOSOCIO para año nuevo, y se ha mantenido 
hasta fin de año, por posibles comenterarios aclaratorios de tesorería u otros gestores, pero se 
le borra ahora por privacidad de datos

RECIBE: $tCuotaSocios =CUOTAANIOSOCIO, $cuotasSocioAnioNuevoBaja = $cuotasSociosAnioNuevo[$numFilaNuevo]
       (los valores del socio que ya tenía anotada entrada en CUOTAANIOSOCIO para año nuevo, pero 
							que ha sido 	baja posteriormente), $conexionLinkDB 
DEVUELVE: un array "$arrBorrarFilaCuotaSocioAnioNuevoBaja" con controles de errores								

LLAMADA: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: modeloMySQLphp:borrarFilas()

OBSERVACION: Probado PDO con PHP 7.3.21
----------------------------------------------------------------------------------------------------------*/
function borrarFilaCuotaSocioAnioNuevoBaja($tCuotaSocios,$cuotasSocioAnioNuevoBaja,$conexionLinkDB)
{
 //echo "<br><br>0-1 modeloAdmin:borrarFilaCuotaSocioAnioNuevoBaja:cuotasSocioAnioNuevoBaja:";print_r($cuotasSocioAnioNuevoBaja); 

	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		

	$arrBorrarFilaCuotaSocioAnioNuevoBaja['codError'] = '00000';
	$arrBorrarFilaCuotaSocioAnioNuevoBaja['errorMensaje'] = '';	
	$arrBorrarFilaCuotaSocioAnioNuevoBaja['textoComentarios'] = '';	
	
	if (!isset($cuotasSocioAnioNuevoBaja) || empty($cuotasSocioAnioNuevoBaja) )		
 {$arrBorrarFilaCuotaSocioAnioNuevoBaja['codError'] = '706010';
  $arrBorrarFilaCuotaSocioAnioNuevoBaja['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrBorrarFilaCuotaSocioAnioNuevoBaja['textoComentarios'] .= ". Error del sistema al borrar filas en tabla CUOTAANIOSOCIO en borrarFilaCuotaSocioAnioNuevoBaja()";
	}
 else
	{
		$cadenaCondicionesBorrar = " CODSOCIO = :codSocio AND ANIOCUOTA = :anioCuota";
					
		$arrBind = array( ':codSocio' => $cuotasSocioAnioNuevoBaja['CODSOCIO'],':anioCuota' => $cuotasSocioAnioNuevoBaja['ANIOCUOTA']);		
		
		$reBorrarCuotasSociosAnioNuevoBaja = borrarFilas($tCuotaSocios,$cadenaCondicionesBorrar,$conexionLinkDB,$arrBind); 
		
		//echo "<br><br>2 modeloAdmin:borrarFilaCuotaSocioAnioNuevoBaja:reBorrarCuotasSociosAnioNuevoBaja: ";print_r($reBorrarCuotasSociosAnioNuevoBaja);																																																								
		
		if ($reBorrarCuotasSociosAnioNuevoBaja['codError'] !== '00000')			
		{$arrBorrarFilaCuotaSocioAnioNuevoBaja = $reBorrarCuotasSociosAnioNuevoBaja;//añadir comentario de error
			$arrBorrarFilaCuotaSocioAnioNuevoBaja['textoComentarios'] .= ". Error del sistema al borrar una fila en CUOTAANIOSOCIO, mCierreAnioPasadoAperturaAnioNuevoAdmin:borrarCuotasSociosAnioNuevo() ";
		}
		else
		{ $arrBorrarFilaCuotaSocioAnioNuevoBaja = $reBorrarCuotasSociosAnioNuevoBaja;			
		}		
	}//else !if (!isset($cuotasSocioAnioNuevoBaja) || empty($cuotasSocioAnioNuevoBaja) )		
	
	//echo "<br><br>3 modeloAdmin:borrarFilaCuotaSocioAnioNuevoBaja:arrBorrarFilaCuotaSocioAnioNuevoBaja: ";print_r($arrBorrarFilaCuotaSocioAnioNuevoBaja);																																																								

 return $arrBorrarFilaCuotaSocioAnioNuevoBaja;								
}		
/*---------------------------- Fin borrarFilaCuotaSocioAnioNuevoBaja --------------------------------------*/

/*---------------------------- Inicio  actualizarCuotasSociosExistentesAnioNuevo ---------------------------
Actualiza en tabla CUOTAANIOSOCIO la fila de un socio que ya tenía anotaciones en anioNuevo (Y) 
pero solo en los casos en que por cambios posteriores en las cuotas vigentes de EL sea necesario:

Actualiza el IMPORTECUOTAANIOEL si el IMPORTECUOTAANIOEL vigente para EL, es mayor que el que 
el correspondiente que tenía anotado en la fila de socio.
También actualiza el IMPORTECUOTAANIOSOCIO si es < IMPORTECUOTAANIOEL, por haberse incrementado 
IMPORTECUOTAANIOEL para Nuevo con posterioridad, hay que adecuarlo	en la tabla $tablaCuotaSocios
Contempla la posiblidad (poco probable) de que también pueda bajar

RECIBE: $tCuotaSocios=CUOTAANIOSOCIO, $cuotasSocioAnioNuevo(los valores del socio en CUOTAANIOSOCIO para el año nuevo),
        $arrCuotasAnioNuevoEL (valores de cuotas vigentes para EL AnioNuevo en tabla IMPORTEDESCUOTAANIO)
								desde modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
								
DEVUELVE: un array "$arrActualizarCuotasSociosAnioNuevo" con controles de errores	

LLAMADA: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: modeloMysSQL.php:modeloactualizarTabla_ParamPosicion()
								
OBSERVACIONES: Probada PDO PHP 7.3.21

NOTA1: 
Para mayor independencia no utilizo función ya existentes: modeloSocios.php:actualizCuotaAnioSocio(). 
Dado que es un proceso crítico es mejor evitar depender de modeloSocios.php por si se modifica.				

NOTA2:Después de nueva función creada para tesorero de actualizar cuotas vigentes EL, (que a la vez 
actualiza los valores existente en CUOTAANIOSOCIO para AnioNuevo (Y+1), no se entrará en esta función
porque ya no se dará la condición, pues en realidad se actualizaron al cambiar la cuota 
IMPORTECUOTAANIOEL, por el tesorero desde el menú "-Cuotas vigentes en EL". 
Se puede dejar por si en alguna modificación "-Cuotas vigentes en EL", no se actualizase adecuamente
CUOTAANISOCIO.IMPORTECUOTAANIOSOCIO del siguiente año.		
-----------------------------------------------------------------------------------------------------------*/
function actualizarCuotasSociosExistentesAnioNuevo($tCuotaSocios,$cuotasSocioAnioNuevo,$arrCuotasAnioNuevoEL,$conexionLinkDB)
{
 //echo "<br><br>0-1 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo:cuotasSocioAnioNuevo: ";print_r($cuotasSocioAnioNuevo); 	
	//echo "<br><br>0-1 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo:arrCuotasAnioNuevoEL: ";print_r($arrCuotasAnioNuevoEL); 	
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		

	$arrActualizarCuotasSociosAnioNuevo['codError'] = '00000';
	$arrActualizarCuotasSociosAnioNuevo['errorMensaje'] = '';	
	$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] = '';	
	
	if ( (!isset($cuotasSocioAnioNuevo) || empty($cuotasSocioAnioNuevo)) ||  (!isset($arrCuotasAnioNuevoEL) || empty($arrCuotasAnioNuevoEL)) )		
 {$arrActualizarCuotasSociosAnioNuevo['codError'] = '706010';
  $arrActualizarCuotasSociosAnioNuevo['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] .= ". Error del sistema al actualizar tabla UOTAANIOSOCIO en actualizarCuotasSociosExistentesAnioNuevo()";
	}
 else //!if ( (!isset($cuotasSocioAnioNuevo) || empty($cuotasSocioAnioNuevo)) ||  (!isset($arrCuotasAnioNuevoEL) || empty($arrCuotasAnioNuevoEL)) )		
	{	 
		$cuotasSociosAnioNuevoActualizar['CODSOCIO']['valorCampo'] = $cuotasSocioAnioNuevo['CODSOCIO'];
		$cuotasSociosAnioNuevoActualizar['ANIOCUOTA']['valorCampo'] = $cuotasSocioAnioNuevo['ANIOCUOTA'];
		
		$tipoCuotaSocio = $cuotasSocioAnioNuevo['CODCUOTA'];//de cada socio=General,Parado,Joven,Honorario
		$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOEL']['valorCampo']	= $arrCuotasAnioNuevoEL[$tipoCuotaSocio];
		
		/*------------ Inicio ha SUBIDO la cuota anual IMPORTECUOTAANIOEL (normal en caso modificación de cuota EL)----------------------*/ 
		if ($cuotasSocioAnioNuevo['IMPORTECUOTAANIOEL'] < $arrCuotasAnioNuevoEL[$tipoCuotaSocio])//lo normal	
		{		
			if($cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'] < $arrCuotasAnioNuevoEL[$tipoCuotaSocio]) 
			{//echo "<br /><br />1-1 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo";
				$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo']	= $arrCuotasAnioNuevoEL[$tipoCuotaSocio];//se actualiza
			}
			else //if($cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'] >= $arrCuotasAnioNuevoEL[$tipoCuotaSocio]) //la cuota+donación es superior a la nueva cuota EL  
			{//echo "<br /><br />1-2 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo";
				$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo']	= $cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'];
				//se mantiene la cuota que tenía en 2019, ya que su cuota+donación es superior a la nueva cuota EL aunque haya subido
			}		
		}	
		/*------------ Fin ha SUBIDO la cuota anual IMPORTECUOTAANIOEL (normal en caso modificación de cuota EL)------------------------*/ 

		/*------------ Inicio ha BAJADO la cuota anual IMPORTECUOTAANIOEL (Caso NO PROBABLE de modificación de cuota EL)----------------*/
		elseif($cuotasSocioAnioNuevo['IMPORTECUOTAANIOEL'] > $arrCuotasAnioNuevoEL[$tipoCuotaSocio])//ha bajado, muy raro que suceda 
		{
			//if($cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIOElegida']>$arrCuotasAnioNuevoEL[$tipoCuotaSocio])//sustituyo por 
			if($cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'] > $arrCuotasAnioNuevoEL[$tipoCuotaSocio])			
			{//echo "<br /><br />2-1 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo";//si la cuota elegida por el socio tabla SOCIO es mayor que la nueva establecida por EL a IMPORTECUOTAANIOSOCIO se asigna IMPORTECUOTAANIOSOCIOElegida la elegida por el socio 

				//$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo']=$cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIOElegida'];	
				/* AQUI PODRÍA FALLAR $cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo']=$cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIOElegida'], ya que IMPORTECUOTAANIOSOCIOElegida 
							viene de SOCIO.IMPORTECUOTAANIOSOCIO=$cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIOElegida'], "cuota+donación" que eligió el socio al darse de alta y que solo cambia el socio o un gestor
							a petición del socio y (no se modifica aunque la cuota de EL suba o baje), por lo que si no hay modificaciones expresas, se mantendrá y puede ser antiguo como por ejemplo 30 euros del 2014.
							De ese modo asignaría un valor inferior al Cuota Nueva de EL para el año nuevo. Se sustituye por la siguiente línea:
							$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo']=$cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'] sin aplicar la bajada de a la cuaota del año anterior 
							que pasará a ser la cuota del año nuevo, pero el socio o gestor pueden aplicarse la rebaja modificando individualmente la cuota del nuevo año. 
							Además a las nuevas altas pueden elegir la cuota rebajda.				
				*/				
				$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'];//deja la que tenía			
			}
			else//if($cuotasSocioAnioNuevo['IMPORTECUOTAANIOSOCIO'] <= $arrCuotasAnioNuevoEL[$tipoCuotaSocio])	OJO: esto es no deberiera suceder aquí			
			{
				//echo "<br /><br />2-2 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo";//no se entrará en está opción	
				$cuotasSociosAnioNuevoActualizar['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $arrCuotasAnioNuevoEL[$tipoCuotaSocio];//pone nuevo valor de cuota EL	
			}			
		}/*------------ Fin ha BAJADO la cuota anual IMPORTECUOTAANIOEL (Caso NO PROBABLE de modificación de cuota EL)----------------*/
		
		/*------------ Inicio sin variación cuota anual IMPORTECUOTAANIOEL -----------------------------------------------------------*/
		else//if$cuotasSocioAnioNuevo['IMPORTECUOTAANIOEL'] = $arrCuotasAnioNuevoEL[$tipoCuotaSocio]	//no ha cambiado la cuota de EL
		{
			//echo "<br /><br />3 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo";
			// se deja como está	ya que no es necesaria la actualización
		}	
		/*------------ Fin sin variación cuota anual IMPORTECUOTAANIOEL -----------------------------------------------------------*/	

		//echo "<br><br>3-1 modeloAdmin:cuotasSociosnioNuevoActualizar:";print_r($cuotasSociosAnioNuevoActualizar); 	
	
  //---------
	 //los datos de ESTADOCUOTA de año Anterior se guardan año nuevo en algunas valores se modifican a "PENDIENTE-COBRO"
		switch ($cuotasSocioAnioNuevo['ESTADOCUOTA']) 
		{ case "ABONADA":
			 case "PENDIENTE-COBRO":
				case "NOABONADA":
				case "NOABONADA-DEVUELTA":
	   case "NOABONADA-ERROR-CUENTA": 				
	   case "ABONADA-PARTE": 
				     $cuotasSociosAnioNuevoActualizar['ESTADOCUOTA']['valorCampo'] = "PENDIENTE-COBRO";		
				
									if ($cuotasSocioAnioNuevo['MODOINGRESO'] == 'DOMICILIADA')
									{ $cuotasSociosAnioNuevoActualizar['ORDENARCOBROBANCO']['valorCampo'] ='SI';
									}
									else
									{ $cuotasSociosAnioNuevoActualizar['ORDENARCOBROBANCO']['valorCampo'] ='NO';	
									}												
				     break;	
	
				case "EXENTO":
									$cuotasSociosAnioNuevoActualizar['ESTADOCUOTA'] = "EXENTO";
									if ($cuotasSocioAnioNuevo['MODOINGRESO'] == 'DOMICILIADA' && 
									    $datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] > $cuotasSocioAnioNuevo['IMPORTECUOTAANIOEL'])
									{ $cuotasSociosAnioNuevoActualizar['ORDENARCOBROBANCO']['valorCampo'] ='SI';
									}
									else	
									{$cuotasSociosAnioNuevoActualizar['ORDENARCOBROBANCO']['valorCampo'] ='NO';	
									}								
							 	break;
				/*					
	   case "NOABONADA-ERROR-CUENTA": 
		       //$cuotasSociosAnioNuevoActualizar['ESTADOCUOTA']['valorCampo'] = "NOABONADA-ERROR-CUENTA";
         $cuotasSociosAnioNuevoActualizar['ESTADOCUOTA']['valorCampo'] = "PENDIENTE-COBRO";											
									$cuotasSociosAnioNuevoActualizar['ORDENARCOBROBANCO']['valorCampo'] = 'NO';												
							 	break;	
			*/								
	 }	
  //---------		
  //echo "<br><br>4-1 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo:cuotasSociosAnioNuevoActualizar: ";print_r($cuotasSociosAnioNuevoActualizar);																																																								

  //-- Inicio Actualiza tabla CUOTAANIOSOCIO: Hay una función que hace esto en modeloSocios.php:actualizCuotaAnioSocio(), pero más independencia lo dejo aquí como está. 

		$arrayCondiciones['CODSOCIO']['valorCampo'] = $cuotasSociosAnioNuevoActualizar['CODSOCIO']['valorCampo'];
		$arrayCondiciones['CODSOCIO']['operador'] = '=';
		$arrayCondiciones['CODSOCIO']['opUnir'] = ''; 
		$arrayCondiciones['CODSOCIO']['opUnir'] = 'AND'; 
		
		//$arrayCondiciones['ANIOCUOTA']['valorCampo'] = $arrayDatosAct['ANIOCUOTA']['valorCampo'];
		$arrayCondiciones['ANIOCUOTA']['valorCampo'] = $cuotasSociosAnioNuevoActualizar['ANIOCUOTA']['valorCampo'];
		$arrayCondiciones['ANIOCUOTA']['operador'] = '=';
		$arrayCondiciones['ANIOCUOTA']['opUnir'] = ''; 
		
		//echo "<br><br>4-2 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo:arrayCondiciones: ";print_r($arrayCondiciones);
		
		//foreach ($arrayDatosAct as $indice => $contenido)
  foreach ($cuotasSociosAnioNuevoActualizar as $indice => $contenido)		
		{ 
				if (isset($contenido) && $contenido !==NULL && $contenido !=='')
				{
						$arrayDatos[$indice] = $contenido['valorCampo'];
				}
		}	
		//echo '<br><br>4-3 modeloAdmin.php:actualizCuotaAnioSocioAdmin:arrayDatos: ';print_r($arrayDatos);

  $reActualizarCuotasSociosAnioNuevo = actualizarTabla_ParamPosicion($tCuotaSocios,$arrayCondiciones,$arrayDatos,$conexionLinkDB);//en modeloMysSQL.php
  
		if ($reActualizarCuotasSociosAnioNuevo['codError'] !== '00000')			
		{ $arrActualizarCuotasSociosAnioNuevo = $reActualizarCuotasSociosAnioNuevo;		
				$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] .= ". Error del sistema al actualizar en CUOTAANIOSOCIO, en actualizarCuotasSociosExistentesAnioNuevo() ";
		}
		else
		{ $arrActualizarCuotasSociosAnioNuevo = $reActualizarCuotasSociosAnioNuevo;			
		}
  //-- FinActualiza tabla CUOTAANIOSOCIO --------------------------------		
 }//else //!if ( (!isset($cuotasSocioAnioNuevo) || empty($cuotasSocioAnioNuevo)) ....
	
	//echo "<br><br>5 modeloAdmin:actualizarCuotasSociosExistentesAnioNuevo:arrActualizarCuotasSociosAnioNuevo: ";print_r($arrActualizarCuotasSociosAnioNuevo); 	
	
 return $arrActualizarCuotasSociosAnioNuevo;								
}		
/*---------------------------- Fin actualizarCuotasSociosExistentesAnioNuevo ------------------------------*/		

/*------------------------- Inicio insertarCuotaSocioAnioNuevo ---------------------------------------------		
Si no hay datos de un socio en CUOTAANIOSOCIO para año nuevo (else $encontrado =='NO'), se inserta una 
fila con los datos que recibe $datInsertaCuotasSociosAnioNuevo, que son copiados del año anterior date(Y)-1
y que se tratarán en esta función para adecualos teniendo en cuenta los casos posibles, 
que la cuota quede igual	(probable),	que haya subido	(probable) o que la cuota haya bajado	(improbale)		

RECIBE: $tCuotaSocios: CUOTAANIOSOCIO, $cuotasSociosAnioAnterior(los datos de año anterior),
        $arrCuotasAnioNuevoEL (los valores de las nuevas cuotas para EL)
        $anioNuevo (es es año nuevo date(Y)),$conexionLinkDB								
DEVUELVE:  un array "$arrActualizarCuotasSociosAnioNuevo" con controles de errores

LLAMADA: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: Probada PDO con PHP 7.3.21
-----------------------------------------------------------------------------------------------------------*/	
function insertarCuotaSocioAnioNuevo($tCuotaSocios,$cuotasSociosAnioAnterior,$arrCuotasAnioNuevoEL,$anioNuevo,$conexionLinkDB)
{
	//echo "<br><br>0-1 modeloAdmin:insertarCuotaSocioAnioNuevo:cuotasSociosAnioAnterior: ";print_r($cuotasSociosAnioAnterior); 	
 //echo "<br><br>0-2 modeloAdmin:insertarCuotaSocioAnioNuevo:arrCuotasAnioNuevoEL: ";print_r($arrCuotasAnioNuevoEL); 	 
 //echo "<br><br>0-3 modeloAdmin:insertarCuotaSocioAnioNuevo:anioNuevo: ";print_r($anioNuevo); 	
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		 

	$arrActualizarCuotasSociosAnioNuevo['codError'] = '00000';
	$arrActualizarCuotasSociosAnioNuevo['errorMensaje'] = '';	
	$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] = '';	

	if ( (!isset($cuotasSociosAnioAnterior) || empty($cuotasSociosAnioAnterior)) || (!isset($arrCuotasAnioNuevoEL) || empty($arrCuotasAnioNuevoEL)) || (!isset($anioNuevo) || empty($anioNuevo))	)		
 {$arrActualizarCuotasSociosAnioNuevo['codError'] = '706010';
  $arrActualizarCuotasSociosAnioNuevo['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] .= ". Error del sistema al actualizar tabla UOTAANIOSOCIO en actualizarCuotasSociosExistentesAnioNuevo()";
	}
 else//! if ( (!isset($cuotasSociosAnioAnterior) || empty($cuotasSociosAnioAnterior)) ||
	{	
		$datInsertaCuotasSociosAnioNuevo['ANIOCUOTA']	= $anioNuevo;	
		$datInsertaCuotasSociosAnioNuevo['CODSOCIO']	= $cuotasSociosAnioAnterior['CODSOCIO'];		
				
		$tipoCuotaSocio = $cuotasSociosAnioAnterior['CODCUOTA'];//de cada socio=General,Parado,Joven,Honorario  
		$datInsertaCuotasSociosAnioNuevo['CODCUOTA'] = $tipoCuotaSocio;//de cada socio=General,Parado,Joven,Honorario  

		//echo "<br><br>1-1 modeloAdmin:insertarCuotaSocioAnioNuevo:tipoCuotaSocio: ";print_r($tipoCuotaSocio); 
		$datInsertaCuotasSociosAnioNuevo['CODAGRUPACION'] = $cuotasSociosAnioAnterior['CODAGRUPACION'];
		
		//echo "<br><br>1-2 modeloAdmin:insertarCuotaSocioAnioNuevo:cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL']: ";print_r($cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL']); 			        
		//echo "<br><br>1-3 modeloAdmin:insertarCuotaSocioAnioNuevo:arrCuotasAnioNuevoEL[$tipoCuotaSocio]: ";print_r($arrCuotasAnioNuevoEL[$tipoCuotaSocio]); 	

		/*--- Inicio ha SUBIDO la cuota anual IMPORTECUOTAANIOEL (normal en caso modificación de cuota EL) ------------------------
			La cuota de EL en año anterior es menor que la nueva cuota establecida por EL para año Nuevo ---------------------------*/
			
		if ($cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL'] < $arrCuotasAnioNuevoEL[$tipoCuotaSocio])
		{ 		
				$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOEL']	= $arrCuotasAnioNuevoEL[$tipoCuotaSocio];	

				//echo "<br><br>2-1 modeloAdmin:insertarCuotaSocioAnioNuevo:datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO']: ";print_r($datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO']); 	

				if( $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] < $arrCuotasAnioNuevoEL[$tipoCuotaSocio]) 
				{//echo "<br><br>2-3 modeloAdmin:insertarCuotaSocioAnioNuevo: ";//si la del socio para año anterior es menor que la nueva establecida para EL, IMPORTECUOTAANIOSOCIO se asigna la nueva cuota establecida por EL
			
					$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO']	= $arrCuotasAnioNuevoEL[$tipoCuotaSocio];				
							
				}
				else //$cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] >= $arrCuotasAnioNuevoEL[$tipoCuotaSocio] //$cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] >= año nuevo IMPORTECUOTAANIOEL
				{//echo "<br><br>2-4 modeloAdmin:insertarCuotaSocioAnioNuevo: ";//print_r($cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO']);
					//la cuota anterior del socio ya era mayor a la cuota nueva de EL (debido a cuota+donación), se copia la (cuota+donacion) del socio de año antiguo en año nuevo: ";				
						
					/* AQUI FALLABA  $datInsertaCuotasSociosAnioNuevo[IMPORTECUOTAANIOSOCIO], para el caso de que se hubiesen subido las cuotas de IMPORTECUOTAANIOEL después de comenzado el año,
							(para caso "cuota+donación" $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] >= año nuevo IMPORTECUOTAANIOEL en CUOTAANIOSOCIO del nuevo año le asignaba el valor NULL),
								fue necesario añadir la siguiente línea en 2018-12-13
					*/
					$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'];//La del año anterior se pone en año nuevo
				}
				//echo "<br><br>2-5 modeloAdmin:insertarCuotaSocioAnioNuevo";//	print_r($datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO']);
				//La cuota de EL en año anterior es menor que la nueva cuota establecida por EL para año Nuevo:datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO']: "; 		
		}/*------------ Fin ha SUBIDO la cuota anual IMPORTECUOTAANIOEL -------------------------------------------------------------*/
		
		/*--- Inicio ha BAJADO la cuota anual IMPORTECUOTAANIOEL para el Nuevo año (muy raro que suceda, lo normal es que suba)------*/
		elseif ($cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL'] > $arrCuotasAnioNuevoEL[$tipoCuotaSocio])//ha bajado, muy raro que suceda
		{//echo "<br><br>3-0 modeloAdmin:insertarCuotaSocioAnioNuevo"; 
			//cuota año anterior de EL era mayor que la nueva cuota establecida por EL para año Nuevo, a IMPORTECUOTAANIOEL se asigna la nueva cuota de EL	que ha bajado la o la elegida si ya era mayor debido a donación+cuota ";
			
			$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOEL']	= $arrCuotasAnioNuevoEL[$tipoCuotaSocio];	

			if($cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] > $arrCuotasAnioNuevoEL[$tipoCuotaSocio])//esto es lo que deberá suceder siempre	
			{//echo "<br><br>3-1a";//si la cuota elegida por el socio tabla SOCIO es mayor que la nueva establecida por EL a IMPORTECUOTAANIOSOCIO se asigna IMPORTECUOTAANIOSOCIOElegida la elegida por el socio 
				//echo "<br><br>3-1b:arrCuotasAnioNuevoEL[tipoCuotaSocio]: ";print_r($arrCuotasAnioNuevoEL[$tipoCuotaSocio]);

				/* AQUI FALLABA $datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIOElegida'], ya que IMPORTECUOTAANIOSOCIOElegida viene de la
							tabla SOCIO.IMPORTECUOTAANIOSOCIO=$cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIOElegida'], "cuota+donación" que eligió el socio al darse de alta y que solo cambia el socio o un gestor
							a petición del socio y (no se modifica aunque la cuota de EL suba o baje), por lo que si no hay modificaciones expresas, se mantendrá y puede ser antiguo como por ejemplo 30 euros del 2014.
							De ese modo asignaría un valor inferior al Cuota Nueva de EL para el año nuevo. Se sustituye por la siguiente línea:
							$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] sin aplicar la bajada de a la cuaota del año anterior 
							que pasará a ser la cuota del año nuevo, pero el socio o gestor pueden aplicarse la rebaja modificando individualmente la cuota del nuevo año. 
							Además a las nuevas altas pueden elegir la cuota rebajda.				
				*/			
				$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'];//no se le baja la cuota para el nuevo año, se deja la que tuviese					
				
			}
			else//cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] <= $arrCuotasAnioNuevoEL[$tipoCuotaSocio])OJO: esto es no deberiera suceder nunca	
			{//echo "<br><br>3-2 modeloAdmin:insertarCuotaSocioAnioNuevo: "; //si la cuota que elegida por el socio tabla SOCIO es menor o igual que la nueva establecida por EL a IMPORTECUOTAANIOSOCIO se asigna la nueva cuota establecida por EL

				$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $arrCuotasAnioNuevoEL[$tipoCuotaSocio];												
			}
		}/*---- Fin ha BAJADO la cuota anual IMPORTECUOTAANIOEL para el Nuevo año (muy raro que suceda, lo normal es que suba)--------*/	
		
		else /*------- Inicio SIN MODIFICACIÓN de cuota anual IMPORTECUOTAANIOEL para el Nuevo año (es la misma que en año anterior)--*/
		{ //echo "<br><br>4 modeloAdmin:insertarCuotaSocioAnioNuevo: "; //son iguales $datInsertaCuotasSociosAnioNuevo no se modifica
		
				$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOSOCIO'] = $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'];
				$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOEL']	= $cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL'];		
				
		}	/*-------- Fin  SIN MODIFICACIÓN  de la cuota anual IMPORTECUOTAANIOEL para el Nuevo año (es la misma que en año anterior)---*/
							
		$datInsertaCuotasSociosAnioNuevo['IMPORTECUOTAANIOPAGADA']	= 0;
		$datInsertaCuotasSociosAnioNuevo['IMPORTEGASTOSABONOCUOTA']	= 0;
		
		//echo "<br><br>5 modeloAdmin:insertarCuotaSocioAnioNuevo:datInsertaCuotasSociosAnioNuevo: ";print_r($datInsertaCuotasSociosAnioNuevo); 	

		//los datos de ESTADOCUOTA de año Anterior se guardan año nuevo en algunas valores se modifican a "PENDIENTE-COBRO"

		//*****************************
		if ((isset($cuotasSociosAnioAnterior['CUENTAIBAN']) && !empty($cuotasSociosAnioAnterior['CUENTAIBAN'])) || 
						(isset($cuotasSociosAnioAnterior['CUENTANOIBAN']) && !empty($cuotasSociosAnioAnterior['CUENTANOIBAN']))
					) 
					{$modoIngresoCuota ='DOMICILIADA';	
					}
					else
					{$modoIngresoCuota ='SIN-DATOS';	
					}	

		$datInsertaCuotasSociosAnioNuevo['MODOINGRESO']	= $modoIngresoCuota;			
		
		//*******************************
			
		switch ($cuotasSociosAnioAnterior['ESTADOCUOTA']) 	
		{ case "ABONADA":
				case "PENDIENTE-COBRO":
				case "NOABONADA":
				case "ABONADA-PARTE": 
									$datInsertaCuotasSociosAnioNuevo['ESTADOCUOTA'] = "PENDIENTE-COBRO";		
									
									if ($modoIngresoCuota == 'DOMICILIADA')
									{ $datInsertaCuotasSociosAnioNuevo['ORDENARCOBROBANCO'] ='SI';
									}
									else
									{ $datInsertaCuotasSociosAnioNuevo['ORDENARCOBROBANCO'] ='NO';
									}	
									//En estos casos las observaciones se ponen a NULL, para dejar vacío el campo 
									$datInsertaCuotasSociosAnioNuevo['OBSERVACIONES']	= NULL;
																				
									break;	

				case "EXENTO":
									$datInsertaCuotasSociosAnioNuevo['ESTADOCUOTA'] = "EXENTO";

									if ($modoIngresoCuota == 'DOMICILIADA' && $cuotasSociosAnioAnterior['IMPORTECUOTAANIOSOCIO'] > $cuotasSociosAnioAnterior['IMPORTECUOTAANIOEL'])
									{ $datInsertaCuotasSociosAnioNuevo['ORDENARCOBROBANCO'] ='SI';
									}
									else	
									{$datInsertaCuotasSociosAnioNuevo['ORDENARCOBROBANCO'] ='NO';	
									}	
									//En este caso se guardan las observaciones para el siguiente año, puede ser útil conservarlas	
									$datInsertaCuotasSociosAnioNuevo['OBSERVACIONES']	= $cuotasSociosAnioAnterior['OBSERVACIONES'];								
									break;
						
				case "NOABONADA-DEVUELTA":
				case "NOABONADA-ERROR-CUENTA":  
									//$datInsertaCuotasSociosAnioNuevo['ESTADOCUOTA'] = "NOABONADA-ERROR-CUENTA";
									$datInsertaCuotasSociosAnioNuevo['ESTADOCUOTA'] = "PENDIENTE-COBRO";		
									$datInsertaCuotasSociosAnioNuevo['ORDENARCOBROBANCO'] = 'NO';
									//En este caso se guardan las observaciones para el siguiente año, puede ser útil conservarlas				
									$datInsertaCuotasSociosAnioNuevo['OBSERVACIONES']	= $cuotasSociosAnioAnterior['OBSERVACIONES'];
									
									break;											
		}	
		
		$datInsertaCuotasSociosAnioNuevo['FECHAPAGO']	= '0000-00-00';	
		$datInsertaCuotasSociosAnioNuevo['FECHAANOTACION']	= '0000-00-00';
			
		//echo "<br><br>6 modeloAdmin:insertarCuotaSocioAnioNuevo:datInsertaCuotasSociosAnioNuevo: ";print_r($datInsertaCuotasSociosAnioNuevo);
		
		$resInsertarCuotasSociosAnioNuevo = insertarUnaFila($tCuotaSocios,$datInsertaCuotasSociosAnioNuevo,$conexionLinkDB);//en modeloMySQL.php
		
		//echo "<br><br>7 modeloAdmin:insertarCuotaSocioAnioNuevo:resInsertarCuotasSociosAnioNuevo: ";print_r($resInsertarCuotasSociosAnioNuevo);			
		
		if ($resInsertarCuotasSociosAnioNuevo['codError'] !== '00000')			
		{$arrActualizarCuotasSociosAnioNuevo = $resInsertarCuotasSociosAnioNuevo;
			$arrActualizarCuotasSociosAnioNuevo['textoComentarios'] .= ". Error del sistema al insertar fila en CUOTAANIOSOCIO. insertarCuotaSocioAnioNuevo() ";		
		}
	}//else ! if ( (!isset($cuotasSociosAnioAnterior) || empty($cuotasSociosAnioAnterior)) ||
	
	//echo "<br><br>8 modeloAdmin:insertarCuotaSocioAnioNuevo:arrActualizarCuotasSociosAnioNuevo: ";print_r($arrActualizarCuotasSociosAnioNuevo);																																																					

	return $arrActualizarCuotasSociosAnioNuevo;
}
/* ------------------------- Fin insertarCuotaSocioAnioNuevo ----------------------------------------------*/

/*---------------------------- Inicio actualizaCuotasSociosAnioAnteriorPendiente ---------------------------
En tabla "CUOTAANIOSOCIO" actualiza ESTADOCUOTA de año anterior para socio que 
[ESTADOCUOTA]==PENDIENTE-COBRO=>NOABONADA se hace para todos los socios a la vez (para los que no 
tienen cuota en año siguiente y para los que la tienen)

RECIBE: $tCuotaSocios=CUOTAANIOSOCIO, $anioAnterior, $conexionLinkDB
DEVUELVE: array "$arrActualizarCuotasSociosAnioAnterior" con información del número de socios 
          afectados y los controles de errores

LLAMADA: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:actualizarTabla()

OBSERVACIONES: Probada PDO, PHP 7.3.21
realiza UPDATE CUOTAANIOSOCIO SET ESTADOCUOTA='NOABONADA'
               WHERE ANIOCUOTA=2019 AND ESTADOCUOTA='PENDIENTE-COBRO'
-----------------------------------------------------------------------------------------------------------*/	        
function actualizaCuotasSociosAnioAnteriorPendiente($tCuotaSocios,$anioAnterior,$conexionLinkDB)
{
	//echo "<br><br>0-1 modeloAdmin:actualizaCuotaSocioAnioAnteriorPendiente:anioAnterior: ";print_r($anioAnterior);	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		 
	
	$arrActualizarCuotasSociosAnioAnterior['codError'] = '00000';
 $arrActualizarCuotasSociosAnioAnterior['errorMensaje'] = '';	
	$arrActualizarCuotasSociosAnioAnterior['textoComentarios'] = '';	

 if (!isset($anioAnterior) || empty($anioAnterior) )		
 {$arrActualizarCuotasSociosAnioAnterior['codError'] = '706010';
  $arrActualizarCuotasSociosAnioAnterior['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$actualizarCuotasSociosAnioAnterior['textoComentarios'] .= ". Error del sistema al actualizar en CUOTAANIOSOCIO, en actualizaCuotaSociosAnioAnteriorPendiente()";	  	
	}
	else //!if (!isset($anioAnterior) || empty($anioAnterior) )		
	{
		$arrayCondiciones['ESTADOCUOTA']['valorCampo']= 'PENDIENTE-COBRO';
		$arrayCondiciones['ESTADOCUOTA']['operador']= '=';
		//$arrayCondiciones['ESTADOCUOTA']['opUnir']= ''; 
		$arrayCondiciones['ESTADOCUOTA']['opUnir']= 'AND'; 
		
		$arrayCondiciones['ANIOCUOTA']['valorCampo']= $anioAnterior;
		$arrayCondiciones['ANIOCUOTA']['operador']= '=';
		$arrayCondiciones['ANIOCUOTA']['opUnir']= ''; 

		$arrayDatos['ESTADOCUOTA'] = 'NOABONADA';	 

		//echo "<br><br>1 modeloAdmin:actualizaCuotaSocioAnioAnteriorPendiente:arrayCondiciones: ";print_r($arrayCondiciones);				
	
  $reActualizarCuotasSociosAnioAnterior = actualizarTabla_pdo_sin_bind($tCuotaSocios,$arrayCondiciones,$arrayDatos,$conexionLinkDB);		
		//echo "<br><br>2 modeloAdmin:actualizaCuotaSocioAnioAnteriorPendiente:reActualizarCuotasSociosAnioAnterior: ";print_r($reActualizarCuotasSociosAnioAnterior);																																																																																																											
			
		if ($reActualizarCuotasSociosAnioAnterior['codError'] !== '00000')			
		{$arrActualizarCuotasSociosAnioAnterior = $reActualizarCuotasSociosAnioAnterior;
			$arrActualizarCuotasSociosAnioAnterior['textoComentarios'] .= ".Error del sistema al actualizar en CUOTAANIOSOCIO, de PENDIENTE-COBRO --> NOABONADA".
                                                                               " en actualizaCuotaSociosAnioAnteriorPendiente() ";		
		}
		else
		{$arrActualizarCuotasSociosAnioAnterior = $reActualizarCuotasSociosAnioAnterior;
			$arrActualizarCuotasSociosAnioAnterior['textoComentarios'] = "- Se han actualizado ".$reActualizarCuotasSociosAnioAnterior['numFilas'].
			                                                                           " filas en CUOTAANIOSOCIO, de PENDIENTE-COBRO --> NOABONADA en el año anterior:$anioAnterior";		
		}	
	}//else !if (!isset($anioAnterior) || empty($anioAnterior) )			
	
	//echo "<br><br>3 modeloAdmin:actualizaCuotasSociosAnioAnteriorPendiente:arrActualizarCuotasSociosAnioAnterior: ";print_r($arrActualizarCuotasSociosAnioAnterior);																																																																																																											

 return $arrActualizarCuotasSociosAnioAnterior;								
}		
/*---------------------------- Fin actualizaCuotasSociosAnioAnteriorPendiente -----------------------------*/

/*-------------------------- Inicio eliminarDatosSocioBajas5Anios() -----------------------------------------
Se eliminan las filas 'MIEMBROELIMINADO5ANIOS' correspondientes a los socios que se dieron 
de baja hace cinco años y que se guardaban por motivos fiscales, 
Se anula el campo CUENTAPAGO de la tabla CUOTAANIOSOCIO de los socios que fueron baja hace
 de más de 5 años y pagaron mediante cuenta domiciliada 
Se anula el campo CUENTAIBAN de la tabla ORDENES_COBRO de los socios que fueron baja hace
de más de 5 años y pagaron mediante cuenta domiciliada 
															
RECIBE: tablas y $anioNuevo, $conexionUsuariosDB que es = $conexionUsuariosDB['conexionLink']);
DEVUELVE: un array "$arrEliminarDatosSocioBajas5Anios" con información del número de socios afectados
          y los controles de errores
	
LLAMADA: modeloAdmin: mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:ejecutarCadSql(),borrarFilas()
							
OBSERVACIONES: Probado PDO y PHP 7.3.21
2020-12-27: Se añade "anular CUENTAPAGO de CUOTAANIOSOCIO" y anular "CUENTAIBAN de ORDENES_COBRO",
antes ya existía eliminar las filas 'MIEMBROELIMINADO5ANIOS'(tabla histórica por motivos fiscales) 

//if ($eliminarDatosSocioBajas5Anios['numFilas'] == 0)//No lo tratará como error en ROLLBACK
----------------------------------------------------------------------------------------------------------*/
function eliminarDatosSocioBajas5Anios($tCuotaSocios,$tSocios,$tOrdenesCobro,$tMiembroElim5Anios,$anioNuevo,$conexionLinkDB)
{
 //echo "<br><br>0-1 modeloAdmin.php:eliminarDatosSocioBajas5Anios:anioNuevo: "; print_r($anioNuevo);
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';
		
	$arrEliminarDatosSocioBajas5Anios['nomFuncion'] = "eliminarDatosSocioBajas5Anios";
	$arrEliminarDatosSocioBajas5Anios['nomScript'] = "modeloAdmin.php";	
	$arrEliminarDatosSocioBajas5Anios['codError'] = '00000';
	$arrEliminarDatosSocioBajas5Anios['errorMensaje'] = '';
	$arrEliminarDatosSocioBajas5Anios['textoComentarios'] ='';	

	if (!isset($anioNuevo) || empty($anioNuevo) )		
 {$arrEliminarDatosSocioBajas5Anios['codError'] = '706010';
  $arrEliminarDatosSocioBajas5Anios['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		$arrEliminarDatosSocioBajas5Anios['arrMensaje']['textoComentarios'] .= ". Error del sistema al eliminar datos de los socios que se dieron 
                                                                              de baja hace cinco años, en eliminarDatosSocioBajas5Anios()";
	}
	else // !if (!isset($anioNuevo) || empty($anioNuevo) )		
	{		
		//--Inicio Eliminar de tabla 'MIEMBROELIMINADO5ANIOS'=datos todos socios con baja hace 5 años 		

		//$anioBorrado = $anioNuevo - 1;// para pruebas
		$anioBorrado = $anioNuevo - 5;//se guardaron 5 años, por motivos fiscales
		$fechaBorrado = $anioBorrado.'-00-00';		

		/*--- Inicio anular CUENTAPAGO de CUOTAANIOSOCIO de los socios con baja hace 5 años	---------------*/	
		/*	$cadSqlUpdateIBAN_CUOTAANIOSOCIO = " UPDATE CUOTAANIOSOCIO SET CUOTAANIOSOCIO.CUENTAPAGO = CONCAT(LEFT(CUOTAANIOSOCIO.CUENTAPAGO,12),'************') 
																				WHERE ANIOCUOTA <= '".$anioBorrado."' AND
																				CUOTAANIOSOCIO.CODSOCIO IN (SELECT SOCIO.CODSOCIO FROM SOCIO WHERE SOCIO.FECHABAJA != '0000-00-00' AND  SOCIO.FECHABAJA <= '".$fechaBorrado."' ) ";
		*/															
		$cadSqlUpdateIBAN_CUOTAANIOSOCIO = " UPDATE ".$tCuotaSocios." SET CUENTAPAGO = CONCAT(LEFT(CUENTAPAGO,12),'************') 
																				WHERE ANIOCUOTA <= '".$anioBorrado."' AND ".
																			$tCuotaSocios.".CODSOCIO IN (SELECT ".$tSocios.".CODSOCIO FROM SOCIO WHERE FECHABAJA != '0000-00-00' AND  FECHABAJA <= '".$fechaBorrado."' ) ";																				

		$resUpdateIBAN_CUOTAANIOSOCIO =  ejecutarCadSql($cadSqlUpdateIBAN_CUOTAANIOSOCIO,$conexionLinkDB);	

		//echo "<br><br>1 modeloAdmin.php:eliminarDatosSocioBajas5Anios:resUpdateIBAN_CUOTAANIOSOCIO: ";print_r($resUpdateIBAN_CUOTAANIOSOCIO);					
		
		if ($resUpdateIBAN_CUOTAANIOSOCIO['codError'] !== '00000')			
		{$arrEliminarDatosSocioBajas5Anios = $resUpdateIBAN_CUOTAANIOSOCIO;
			$arrEliminarDatosSocioBajas5Anios['textoComentarios'].= ". Error sistema al anular CUENTAPAGO de CUOTAANIOSOCIO de más de 5 años anteriores y baja socio, en eliminarDatosSocioBajas5Anios()";							
		}
		else //else $resUpdateIBAN_CUOTAANIOSOCIO['codError'] =='00000' 
		{$arrEliminarDatosSocioBajas5Anios['textoComentarios'] .= "<br /><br />- Se han anulado los datos de ".$resUpdateIBAN_CUOTAANIOSOCIO['numFilas'].
								" CUENTAPAGO en la tabla CUOTAANIOSOCIO que se dieron de baja antes del anio ".$anioBorrado." y que aún se conservaban por motivos contables fiscales";
								
			/*--- Fin anular CUENTAPAGO de CUOTAANIOSOCIO de los socios con baja hace 5 años	------------------*/
			
			/*--- Inicio anular CUENTAIBAN de ORDENES_COBRO de los socios con baja hace 5 años	----------------*/	
			
			/*$cadSqlUpdateIBAN_ORDENES_COBRO = " UPDATE ORDENES_COBRO SET ORDENES_COBRO.CUENTAIBAN = CONCAT(LEFT(ORDENES_COBRO.CUENTAIBAN,12),'************')
																				                     WHERE ANIOCUOTA <= '".$anioBorrado."' AND
																				                     ORDENES_COBRO.CODSOCIO IN (SELECT SOCIO.CODSOCIO FROM SOCIO WHERE SOCIO.FECHABAJA != '0000-00-00' AND  SOCIO.FECHABAJA <= '".$fechaBorrado."' ) ";	
			*/
			$cadSqlUpdateIBAN_ORDENES_COBRO = " UPDATE ".$tOrdenesCobro." SET ".$tOrdenesCobro.".CUENTAIBAN = CONCAT(LEFT(".$tOrdenesCobro.".CUENTAIBAN,12),'************')
																					WHERE ANIOCUOTA <= '".$anioBorrado."' AND ".
																					$tOrdenesCobro.".CODSOCIO IN (SELECT ".$tSocios.".CODSOCIO FROM SOCIO WHERE FECHABAJA != '0000-00-00' AND  FECHABAJA <= '".$fechaBorrado."' ) ";																					

			$resUpdateIBAN_ORDENES_COBRO = ejecutarCadSql($cadSqlUpdateIBAN_ORDENES_COBRO,$conexionLinkDB);

			//echo "<br><br>2 modeloAdmin.php:eliminarDatosSocioBajas5Anios:resUpdateIBAN_ORDENES_COBRO: ";print_r($resUpdateIBAN_ORDENES_COBRO);																																																																																																											
					
			if ($resUpdateIBAN_ORDENES_COBRO['codError'] !== '00000')			
			{ $arrEliminarDatosSocioBajas5Anios = $resUpdateIBAN_ORDENES_COBRO;
					$arrEliminarDatosSocioBajas5Anios['textoComentarios'] .= ". Error sistema al anular CUENTAIBAN de ORDENES_COBRO de más de 5 años anteriores y baja socio, en eliminarDatosSocioBajas5Anios() ";						
			}
			else //else $resUpdateIBAN_ORDENES_COBRO['codError'] =='00000' 
			{
					$arrEliminarDatosSocioBajas5Anios['textoComentarios'] .= "<br /><br />- Se han anulado los datos de ".$resUpdateIBAN_ORDENES_COBRO['numFilas'].
										" CUENTAIBAN en la tabla ORDENES_COBRO que se dieron de baja antes del anio ".$anioBorrado." y que aún se conservaban por motivos contables fiscales";
					/*--- Fin anular CUENTAIBAN de ORDENES_COBRO de los socios con baja hace 5 años	-------------------*/	
					
					/*--- Inicio Eliminar de tabla 'MIEMBROELIMINADO5ANIOS' a los socios con baja hace 5 años	--------*/			

					//$cadenaCondicionesBorrar = " FECHABAJA <= '".$fechaBorrado."' ";//ojo se debe pasar entrecomillada, como un string, para que no lo trate como una expresión numérica						
				
					$cadenaCondicionesBorrar = " FECHABAJA <= :fechaBaja ";//ojo se debe pasar entrecomillada, como un string, para que no lo trate como una expresión númerica
					
					$arrBind = array( ':fechaBaja' => $fechaBorrado);
							
					$resEliminarSocioBajas5Anios = borrarFilas($tMiembroElim5Anios,$cadenaCondicionesBorrar,$conexionLinkDB,$arrBind);					

					//echo "<br><br>3 modeloAdmin.php:eliminarDatosSocioBajas5Anios:resEliminarSocioBajas5Anios: "; print_r($resEliminarSocioBajas5Anios); 

					if ($resEliminarSocioBajas5Anios['codError'] !=='00000')			
					{$arrEliminarDatosSocioBajas5Anios = $resEliminarSocioBajas5Anios;		
						$arrEliminarDatosSocioBajas5Anios['textoComentarios'] .= " .Error del sistema al eliminar filas MIEMBROELIMINADO5ANIOS, en eliminarDatosSocioBajas5Anios() ";
					}
					else// $resEliminarSocioBajas5Anios['codError'] =='00000' 
					{ 
							$arrEliminarDatosSocioBajas5Anios['textoComentarios'] .= "<br /><br />- Se han borrado los datos personales de ".$resEliminarSocioBajas5Anios['numFilas'].
											" socios/as en la tabla MIEMBROELIMINADO5ANIOS que se dieron de baja antes del anio ".$anioBorrado." y que aún se conservaban por motivos contables fiscales";
					}
					/*--- Fin Eliminar de tabla 'MIEMBROELIMINADO5ANIOS' de los socios con baja hace 5 años	-----------*/
					
			}//else $resUpdateIBAN_ORDENES_COBRO['codError'] =='00000' 
		}//else $resUpdateIBAN_CUOTAANIOSOCIO['codError'] =='00000' 
	}//else !if (!isset($anioNuevo) || empty($anioNuevo) )		
	
	//echo "<br><br>4 modeloAdmin.php:eliminarDatosSocioBajas5Anios:arrEliminarDatosSocioBajas5Anios: "; print_r($arrEliminarDatosSocioBajas5Anios); 
	
	return 	$arrEliminarDatosSocioBajas5Anios; 
}	
/*-------------------------- Fin eliminarDatosSocioBajas5Anios() ------------------------------------------*/

/*---------------------------- Inicio  insertarImporteCuotasAnioSiguiente -----------------------------------
Inserta en tabla IMPORTEDESCUOTAANIO los valores de las cuotas de la asociación según el tipo socio
para AnioSiguiente (Y+1)=uno mas que AnioNuevo), 
Si estuviese actualizada (ya tiene fila con valores para AnioSiguiente) no se insertaría nada.

Para ejecutar esta función es necesario que haya siempre valores de AñoNuevo (Y).												 
Por defecto, se ponen los valores de cuotas de AnioNuevo (Y), para año AnioSiguiente (Y+1)

Para insertar/actualizar año AnioSiguiente (Y+1) con valores distintos del AnioNuevo, 
existe un función para el rol de tesorero function 
insertarImporteCuotasAnioSiguiente($cadSQLBuscarCuotaAnioSiguienteEL,$cadSQLInsertarCuotaAnioSiguienteEL,$conexionDB)								

	
-Primero se busca valores de AnioSiguiente (Y+1) en la tabla IMPORTEDESCUOTAANIO con la función 
buscarCadSql($cadSQLBuscarCuotaAnioSiguienteEL y si se hubiese valores encontroado numFilas !=0 
no se efectuará la operación de insertar nuevos valores de cuotas EL para AnioSiguiente (Y+1)
-Segundo si no encuentran valores en la tabla IMPORTEDESCUOTAANIO, se insertan los de AnioNuevo (Y).																							

RECIBE: $tImporteCuotaAnios=IMPORTEDESCUOTAANIO, $añoNuevo=date('Y').$anioSiguiente=date('Y'+1),
        $conexionUsuariosDB  desde modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
DEVUELVE: un array "$arrInsertarCuotaAnioSiguienteEL" con información del número filas insertadas
          y los controles de errores
								
LLAMADA: modeloAdmin.php:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: modeloMySQLphp:buscarCadSql(),ejecutarCadSql()

OBSERVACIONES: Probadp PDO com PHP 7.3.21
----------------------------------------------------------------------------------------------------------*/
function insertarImporteCuotasAnioSiguiente($tImporteCuotaAnios,$anioNuevo,$conexionLinkDB)								
{
	//echo "<br><br>0-1 modeloAdmin:insertarImporteCuotasAnioSiguiente:anioNuevo:"; print_r($anioNuevo);

	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		
	
 $arrInsertarCuotaAnioSiguienteEL['codError'] = '00000';
	$arrInsertarCuotaAnioSiguienteEL['errorMensaje'] = '';
	$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] = '';

 if (!isset($anioNuevo) || empty($anioNuevo) )		
 { $arrInsertarCuotaAnioSiguienteEL['codError'] = '706010';
			$arrInsertarCuotaAnioSiguienteEL['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
			$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] .= ". Error del sistema al insertar filas en IMPORTEDESCUOTAANIO, en insertarImporteCuotasAnioSiguiente() ";	  	
	}
	else //isset($anioNuevo) ...
	{				
			$anioSiguiente = $anioNuevo + 1;
			$cadSQLBuscarCuotaAnioSiguienteEL = "SELECT * FROM ".$tImporteCuotaAnios." WHERE ANIOCUOTA = $anioSiguiente";	
					
			$cadSQLInsertarCuotaAnioSiguienteEL = "INSERT INTO ".$tImporteCuotaAnios." (ANIOCUOTA,CODCUOTA,IMPORTECUOTAANIOEL,NOMBRECUOTA,DESCRIPCIONCUOTA)
											SELECT '$anioSiguiente',IMP.CODCUOTA,IMP.IMPORTECUOTAANIOEL,IMP.NOMBRECUOTA,IMP.DESCRIPCIONCUOTA  
											FROM ".$tImporteCuotaAnios." IMP  WHERE IMP.ANIOCUOTA= '$anioNuevo'";
			

			//echo "<br><br>2 modeloAdmin::insertarImporteCuotasAnioSiguiente:cadSQLBuscarCuotaAnioSiguienteEL: "; print_r($cadSQLBuscarCuotaAnioSiguienteEL); 						
			$resCuotaAnioSiguienteEL = buscarCadSql($cadSQLBuscarCuotaAnioSiguienteEL,$conexionLinkDB);								
			
			//echo "<br><br>3 modeloAdmin::insertarImporteCuotasAnioSiguiente:resCuotaAnioSiguienteEL:"; print_r($resCuotaAnioSiguienteEL); 
								
			if ($resCuotaAnioSiguienteEL['codError'] !== '00000')		
			{$arrInsertarCuotaAnioSiguienteEL = $resCuotaAnioSiguienteEL;		
				$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] .= ". Error del sistema al buscar en IMPORTEDESCUOTAANIO en insertarImporteCuotasAnioSiguiente()";	  	
			}
			else //else $resCuotaAnioSiguienteEL['codError'] =='00000'
			{						
				if ($resCuotaAnioSiguienteEL['numFilas'] !== 0)//No lo tratará como error en ROLLBACK
				{
					$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] .= "<br /><br />- Ya existen importes de cuotas para el nuevo año "."\"".$anioSiguiente.
					"\""." en la tabla IMPORTEDESCUOTAANIO por lo que no se han cambiado: para cambiarlo elija la opción Cuotas EL";
					
				}
				//---Fin Se busca a ver si en tabla IMPORTEDESCUOTAANIO ya tiene cuota para el año siguiente, y si es afirmativo no se actualizan		
				else//else $resCuotaAnioSiguienteEL['numFilas'] == 0
				{
					//echo "<br><br>4 modeloAdmin:resCuotaAnioSiguienteEL:insertarImporteCuotasAnioSiguiente:cadSQLInsertarCuotaAnioSiguienteEL:"; print_r($cadSQLInsertarCuotaAnioSiguienteEL); 
					
					$resInsertarCuotaAnioSiguienteEL = ejecutarCadSql($cadSQLInsertarCuotaAnioSiguienteEL,$conexionLinkDB);	
					
					//echo "<br><br>5 modeloAdmin:insertarImporteCuotasAnioSiguiente:resInsertarCuotaAnioSiguienteEL:"; print_r($resInsertarCuotaAnioSiguienteEL); 
										
					if ($resInsertarCuotaAnioSiguienteEL['codError'] !== '00000')			
					{$arrInsertarCuotaAnioSiguienteEL = $resInsertarCuotaAnioSiguienteEL;		
						$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] .= ". Error del sistema al insertar en IMPORTEDESCUOTAANIO en insertarImporteCuotasAnioSiguiente()";	  	
					}
					else
					{//if ($resInsertarCuotaAnioSiguienteEL['numFilas'] !== 0)//No lo tratará como error en ROLLBACK
						{ 
								$arrInsertarCuotaAnioSiguienteEL['textoComentarios'] = "<br /><br />- Se han añadido ".$resInsertarCuotaAnioSiguienteEL['numFilas'].
								                                                        " filas en la tabla IMPORTEDESCUOTAANIO con las cuotas vigentes de EL para el año: ".$anioSiguiente;
						}				
					}	
				}//else $resCuotaAnioSiguienteEL['numFilas'] == 0
			}//else $resCuotaAnioSiguienteEL['codError'] =='00000'
	}//else isset($anioNuevo) ...
 
	//echo "<br><br>6 modeloAdmin:insertarImporteCuotasAnioSiguiente:arrInsertarCuotaAnioSiguienteEL:"; print_r($arrInsertarCuotaAnioSiguienteEL); 
	
	return $arrInsertarCuotaAnioSiguienteEL;	
}							
/*---------------------------- Fin  insertarImporteCuotasAnioSiguiente ------------------------------------*/

/* ------------------------- Inicio anularSociosPendientesConfirAdmin --------------------------------------
Los usuarios pendiente de confirmar su alta, y no han contestado a emails o tel. se les elimina. 
Para futuras estadísticas los datos personales se ponen a NULL en "SOCIOSCONFIRMAR" de socios 
cuyo estado USUARIO.ESTADO=PENDIENTE-CONFIRMAR que pasará a USUARIO.ESTADO=ANULADA-SOCITUD-REGISTRO, 
(no se guarda ningún dato en MIEMBROELIMINADO5ANIOS, ya que no ha llegado a ser socio y no hay pagos)
Se eliminan todos los date(Y-1), es decir un año o más años, antes de nuevo año, es decira año anterior 
(por ejemplo si el nuevo año es 2019, se eliminarán los pendientes de confirmar de 2018).	
																
RECIBE: una variable $anioAnular=date((Y)-1)=$anioAnterior, tablas $tUsuario,$tSocioConfirmar,
        $conexionLinkDB
DEVUELVE:  un array "$arrAnularSociosPendConfir" con información número anulados y controles de errores

LLAMADA: modeloAdmin:mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: modeloSocios.php:actualizarSocioConfirmar(), modeloUsuarios.php:actualizUsuario() 
       BBDD/MySQL/modeloMySQL.php:buscarCadSql()
							
OBSERVACIONES: Probado PDO con PHP 7.3.21
Es algo parecido cPresidente.php:anularAltaSocioPendienteConfirmarPres() 

NOTA: Para mayor independencia no utilizo función ya existentes: modeloUsuario.php:actualizUsuario()
      modeloSocios.php:actualizarSocioConfirmar(). Dado que es un proceso crítico es mejor evitar 
      depender de modeloUsuario.php, modeloSocios.php por si se hace modificaciones en ellos.
-----------------------------------------------------------------------------------------------------------*/
function anularSociosPendientesConfirAdmin($tUsuario,$tSocioConfirmar,$anioAnular,$conexionLinkDB)
{
 //echo "<br><br>0-1 modeloAdmin:anulaSociosPendientesConfirAdmin:anioAnterior: "; print_r($anioAnular);
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		
			
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';					
	require_once './modelos/modeloUsuarios.php';//para actualizUsuario()
	require_once './modelos/modeloSocios.php';	//actualizarSocioConfirmar()

 $arrAnularSociosPendConfir['codError'] = '00000';
	$arrAnularSociosPendConfir['errorMensaje'] = '';
	$arrAnularSociosPendConfir['textoComentarios'] = '';

 if (!isset($anioAnular) || empty($anioAnular) )		
 { $arrAnularSociosPendConfir['codError'] = '706010';
			$arrAnularSociosPendConfir['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
			$arrAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema al anular datos en SOCIOSCONFIRMAR, en anularSociosPendientesConfirAdmin() ";	  	
	}
	else //isset($anioAnular) ...
	{
			$cadSQLBuscarSociosPendConfir = "SELECT ".$tUsuario.".ESTADO, ".$tSocioConfirmar.".*  
																																					FROM ".$tUsuario.",".$tSocioConfirmar. 
																																				" WHERE ".$tUsuario.".CODUSER=".$tSocioConfirmar.".CODUSER 
																																				AND ".$tUsuario.".ESTADO ='PENDIENTE-CONFIRMAR' 
																																				AND ".$tSocioConfirmar.".ANIOCUOTA < :anioAnular ";		
			
			$arrBind = array( ':anioAnular' => $anioAnular);																																				

			//echo "<br><br>3 modeloAdmin:anulaSociosPendientesConfirAdmin:cadSQLBuscarSociosPendConfir: "; print_r($cadSQLBuscarSociosPendConfir); 
			
			$resBuscarSociosPendConfir = buscarCadSql($cadSQLBuscarSociosPendConfir,$conexionLinkDB,$arrBind);	//en modeloMySQL.php';
			
			//echo "<br><br>4 modeloAdmin:anulaSociosPendientesConfirAdmin:resBuscarSociosPendConfir: "; print_r($resBuscarSociosPendConfir); 
			
			if ($resBuscarSociosPendConfir['codError'] !== '00000')			
			{$arrAnularSociosPendConfir = $resBuscarSociosPendConfir;		
				$arrAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema al buscar en SOCIOSCONFIRMAR o USUARIO, en anularSociosPendientesConfirAdmin() ";
			}
			else //$resBuscarSociosPendConfir['codError'] =='00000')
			{			
				$datosSociosPendConfirmar = $resBuscarSociosPendConfir['resultadoFilas'];
				$textoSociosPendConfirAnulados = '';

				$f = 0;
				$resAnularSociosPendConfir['codError'] = '00000';
				
				while ($f < $resBuscarSociosPendConfir['numFilas'] && $resAnularSociosPendConfir['codError'] =='00000')//si ['numFilas']=0 no entrará en bucle, no existen socios pendientes confirmar 	
				{//--añadir texto ---
					$textoSociosPendConfirAnulados .= "<br />".($f+1)."- ".$datosSociosPendConfirmar[$f]['APE1']." ".$datosSociosPendConfirmar[$f]['APE2'].", ".$datosSociosPendConfirmar[$f]['NOM'].
					"  Num. documento: ".$datosSociosPendConfirmar[$f]['NUMDOCUMENTOMIEMBRO'].
					"  Email: ".$datosSociosPendConfirmar[$f]['EMAIL'].
					"  Tel: ".$datosSociosPendConfirmar[$f]['TELFIJOCASA'].", ".$datosSociosPendConfirmar[$f]['TELMOVIL'].
					" Localidad: ".$datosSociosPendConfirmar[$f]['LOCALIDAD'];
					
					//echo "<br><br>5-1 modeloAdmin:anulaSociosPendientesConfirAdmin:textoSociosPendConfirAnulados: "; print_r($textoSociosPendConfirAnulados); 
					//------------------
					$codUser = $resBuscarSociosPendConfir['resultadoFilas'][$f]['CODUSER'];
					//echo "<br><br>5-2 modeloAdmin:anulaSociosPendientesConfirAdmin:codUser: "; print_r($codUser); 

					/* NOTA: se dejan valores de algunos campos no personales para estadísticas*/		
					$arrValoresActualizar['usuario']['ESTADO']['valorCampo']	= 'ANULADA-SOCITUD-REGISTRO';	
					$arrValoresActualizar['usuario']['USUARIO']['valorCampo']= NULL;  
					$arrValoresActualizar['usuario']['OBSERVACIONES']['valorCampo'] = NULL;	
					$arrValoresActualizar['socioConfirmar']['FECHACONFIRMACION_ANULACION']['valorCampo'] = date('Y-m-d');
					$arrValoresActualizar['socioConfirmar']['NUMDOCUMENTOMIEMBRO']['valorCampo']= NULL;
					$arrValoresActualizar['socioConfirmar']['APE1']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['APE2']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['NOM']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['TELFIJOCASA']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['TELFIJOTRABAJO']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['TELMOVIL']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['EMAIL']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['DIRECCION']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['COMENTARIOSOCIO']['valorCampo'] = NULL;	
					$arrValoresActualizar['socioConfirmar']['OBSERVACIONES']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['CUENTAIBAN']['valorCampo'] = NULL;
					$arrValoresActualizar['socioConfirmar']['CUENTANOIBAN']['valorCampo']= NULL;					
							
					//echo "<br><br>6 modeloSocios:anulaSociosPendientesConfirAdmin:arrValoresActualizar: ";print_r($arrValoresActualizar);										
					
					//-- Inicio Actualiza tabla USUARIO: Hay una función Que hace esto en modeloUsuario.php:actualizUsuario(), pero más independencia lo dejo aquí como está. 
					$arrayCondiciones['CODUSER']['valorCampo'] = $codUser;//ok
					$arrayCondiciones['CODUSER']['operador'] = '=';
					$arrayCondiciones['CODUSER']['opUnir'] = ' ';
					
					$arrayDatosUsuario = $arrValoresActualizar['usuario'];
					
					foreach ($arrayDatosUsuario as $indice => $contenido)                         
					{      
								$arrayDatos[$indice] = $contenido['valorCampo']; 
					}	
					//echo '<br><br>7-1 modeloAdmin:anulaSociosPendientesConfirAdmin:arrayDatos: ';print_r($arrayDatos);
							
					$resActualizarUsuario = actualizarTabla_ParamPosicion($tUsuario,$arrayCondiciones,$arrayDatos,$conexionLinkDB);
					
					//echo "<br><br>7-3 modeloAdmin:anulaSociosPendientesConfirAdmin:resActualizarUsuario: ";print_r($resActualizarUsuario);	
				
					if ($resActualizarUsuario['codError'] !=='00000')			
					{$resAnularSociosPendConfir = $resActualizarUsuario;
				  $resAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema actualizar tabla USUARIO, en anularSociosPendientesConfirAdmin() ";
					}
					elseif ($resActualizarUsuario['numFilas'] != 1)//va borrando cada socio, dentro del bucle
					{$resAnularSociosPendConfir['codError'] = '70700';
						$resAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema al actualizar tabla USUARIO, inconsistencia resultados en anularSociosPendientesConfirAdmin() ";											 
					}//-- Fin Actualiza tabla USUARIO ----------------------------------------------------
					
					else //resActualizarUsuario['codError']=='00000'
					{
				  //-- Inicio Actualiza tabla SOCIOSCONFIRMAR: Hay una función Que hace esto en modeloSocios.php:actualizarSocioConfirmar(), pero más independencia lo dejo aquí como está. 
						/*$arrayCondiciones['CODUSER']['valorCampo'] = $codUser;//es igual a lo de arriba
						$arrayCondiciones['CODUSER']['operador'] = '=';
						$arrayCondiciones['CODUSER']['opUnir'] = ' ';*/
							
			  	$arrayDatosSocioConfirmar = $arrValoresActualizar['socioConfirmar'];
							
						foreach ($arrayDatosSocioConfirmar as $indice => $contenido)                         
						{      
								$arrSocioConfirmarAct[$indice] = $contenido['valorCampo']; 
						}		
						//echo '<br><br>8-1 modeloAdmin:anulaSociosPendientesConfirAdmin:arrSocioConfirmarAct: ';print_r($arrSocioConfirmarAct);

						$resActualizarSociosConfirmar = actualizarTabla($tSocioConfirmar,$arrayCondiciones,$arrSocioConfirmarAct,$conexionLinkDB);																				
						//echo '<br><br>8-2 modeloAdmin:anulaSociosPendientesConfirAdmin:resActualizarSociosConfirmar: ';print_r($resActualizarSocioConfirmar);
			  	
						if ($resActualizarSociosConfirmar['codError'] !== '00000')			
						{$resAnularSociosPendConfir = $resActualizarSociosConfirmar;
					  $resAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema al actualizar tabla SOCIOSCONFIRMAR, en anularSociosPendientesConfirAdmin() ";
						}
      elseif ($resActualizarSociosConfirmar['numFilas'] != 1)	
      {$resAnularSociosPendConfir['codError'] = '70700';
							$resAnularSociosPendConfir['textoComentarios'] .= ". Error del sistema al actualizar tablas SOCIOSCONFIRMAR, inconsistencia resultados en anularSociosPendientesConfirAdmin() ";											 
						}	
      //-- Fin Actualiza tabla SOCIOSCONFIRMAR ------------------------------------------						
					}	//resActualizarUsuario['codError']=='00000'	
			
					//echo "<br><br>9 modeloAdmin:anulaSociosPendientesConfirAdmin:resAnularSociosPendConfir: "; print_r($resAnularSociosPendConfir); 

					$f++;
				}//while ($f < ......
				
				if ($resAnularSociosPendConfir['codError'] == '00000')			
				{
			   $resAnularSociosPendConfir['textoComentarios'] ="<br /><br />- Se han borrado los datos de ".
																																	$resBuscarSociosPendConfir['numFilas']." socios/as pendiente de confirmar, existentes en la tabla 
																																	SOCIOSCONFIRMAR anteriores al año ".$anioAnular.". Datos en caso de necesitar contactar con esas personas". 
																																	"<br />".$textoSociosPendConfirAnulados;																															
				}				
			}//else $resBuscarSociosPendConfir['codError'] =='00000')		

			$arrAnularSociosPendConfir = $resAnularSociosPendConfir;								
											
	}//else isset($anioAnular) ...								
	
	//echo "<br><br>10 modeloAdmin:anulaSociosPendientesConfirAdmin:arrAnularSociosPendConfir: "; print_r($arrAnularSociosPendConfir); 
	
	return 	$arrAnularSociosPendConfir; 
}	
/* ------------------------- Fin anulaSociosPendientesConfirAdmin -----------------------------------------*/

/*------------------------- Inicio actualizarTablaControles() -----------------------------------------------
Actualizar, insertar y elimina fila en  tabla "CONTROLES"
Para poder prevenir la ejecución, no deseada, de repetición de los procesos de actualización de 
nuevo año "mCierreAnioPasadoAperturaAnioNuevoAdmin()", para "$anioNuevo" se pone el campo de 
cambio año "ACTUALIZADO = SI" y en campo "RESUMENPROCESO" se copia		el contenido de la 
columna "$cadResumenProceso". 
Se inserta una nueva fila	para "$anioSiguiente" con campo de cambio año "ACTUALIZADO = NO" 

RECIBE: $tControles, $anioNuevo, $cadResumenProceso (un string con el resumen del proceso para grabar) y
        $conexionLinkDB que es = $conexionDB['conexionLink']
DEVUELVE: un array con string de información de resumen y con los controles de errores

LLAMADA: modeloAdmin: mCierreAnioPasadoAperturaAnioNuevoAdmin()
LLAMA: BBDD/MySQL/modeloMySQL.php:actualizarTabla(),insertarUnaFila()
							
OBSERVACIONES: Probado PDO con php 7.3.21
----------------------------------------------------------------------------------------------------------*/
function actualizarTablaControles($tControles,$anioNuevo,$cadResumenProceso,$conexionLinkDB)
{	
	//echo "<br><br>0-1 modeloAdmin:actualizarTablaControles:anioNuevo: ";print_r($anioNuevo);
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';		

	$arrActualizarTablaControles['codError'] = '00000'; 
	$arrActualizarTablaControles['errorMensaje'] = ''; 
	$arrActualizarTablaControles['textoComentarios'] = '';
	
 $cadResumenActualizarTablaControl = '';
	
 $anioSiguiente = $anioNuevo + 1;//el siguiente al que ha empezado
	
 if ( (!isset($anioNuevo) || empty($anioNuevo) ) || (!isset($cadResumenProceso) || empty($cadResumenProceso) ) )		
 {
		 $arrActualizarTablaControles['codError'] = '706010';
   $arrActualizarTablaControles['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
		 $arrActualizarTablaControles['textoComentarios'] .= ". Error del sistema al actualizar en CONTROLES, en actualizarTablaControles()";	  	
	}
	else //if ( (isset($anioNuevo) ...) )
	{	
			date_default_timezone_set('Europe/Madrid');//para que lo grabe con el horario de madrid, la columna es "datetime" el servidor está en Suecia
			
			$arrayCondiciones['ANIO']['valorCampo'] = $anioNuevo;
			$arrayCondiciones['ANIO']['operador'] = '=';
			$arrayCondiciones['ANIO']['opUnir'] = ' ';
			
			$arrValoresActualizar['ACTUALIZADO']	= 'SI';	
			$arrValoresActualizar['FECHAACTUALIZACION'] = date('Y-m-d H:m:s');  
			$arrValoresActualizar['RESUMENPROCESO'] = $cadResumenProceso; 			
	
			$resActualizarControlActualizadoAnio = actualizarTabla($tControles,$arrayCondiciones,$arrValoresActualizar,$conexionLinkDB);//modeloMySQL.php	 Probado error 
			
			//echo "<br><br>1 modeloAdmin:actualizarTablaControles:resActualizarControlActualizadoAnio: ";print_r($resActualizarControlActualizadoAnio);
	
			if ($resActualizarControlActualizadoAnio['codError'] !== '00000')
			{$arrActualizarTablaControles = $resActualizarControlActualizadoAnio;
				$arrActualizarTablaControles['textoComentarios'] .= ". Error del sistema, al actualizar CONTROLES en actualizarTablaControles() ";
			}
		 elseif ($resActualizarControlActualizadoAnio['numFilas'] <= 0 )// cero filas es error
			{$arrActualizarTablaControles = $resActualizarControlActualizadoAnio;
				$arrActualizarTablaControles['textoComentarios'] .= ". Error al actualizar CONTROLES en actualizarTablaControles() ";
			}
			else // $resActualizarControlActualizadoAnio['codError'] == '00000'
			{				
				$cadResumenActualizarTablaControl = "<br /><br />- Se ha actualizado la tabla CONTROLES y anotado el resumen de todo el proceso "; 		
		
				$datosInsertarControles['ANIO']	= $anioSiguiente;	
				$datosInsertarControles['ACTUALIZADO']	= 'NO';
				$datosInsertarControles['FECHAACTUALIZACION']	= '0000-00-00';
				$datosInsertarControles['RESUMENPROCESO'] = ' ';
    $datosInsertarControles['OBSERVACIONES']	= ' ';	
   
				$resInsertarControlActualizadoAnio = insertarUnaFila($tControles,$datosInsertarControles,$conexionLinkDB);//modeloMySQL.php	 Probado error 					

				//echo "<br><br>2 modeloAdmin:actualizarTablaControles:resInsertarControlActualizadoAnio: ";print_r($resInsertarControlActualizadoAnio);

				if ($resInsertarControlActualizadoAnio['codError'] !== '00000')
				{$arrActualizarTablaControles = $resInsertarControlActualizadoAnio;
					$arrActualizarTablaControles['textoComentarios'] .= ". Error del sistema al insertar CONTROLES en actualizarTablaControles() ";
				}	
				elseif ($resInsertarControlActualizadoAnio['numFilas'] <= 0 )// cero filas es error
				{$arrActualizarTablaControles = $resInsertarControlActualizadoAnio;
					$arrActualizarTablaControles['textoComentarios'] .= ". Error al insertar CONTROLES en actualizarTablaControles() ";
				}	
				else //else $resInsertarControlActualizadoAnio['codError']=00000' y $resInsertarControlActualizadoAnio['codError']=='00000'
				{ 
				  $cadResumenActualizarTablaControl = "<br /><br />- Se ha insertado una nueva fila en la tabla CONTROLES para el año ".$anioSiguiente. $cadResumenActualizarTablaControl;
				
						//--- Inicio Eliminar de tabla 'CONTROLES' filas de años anteriores 	--------					  
						
						$cadenaCondicionesEliminar = "  ANIO  <= :anioEliminar ";
				
						$anioEliminar = $anioNuevo - 2;//se guardaron 2 años anteriores por si hubiese que hacer alguna consulta
						
						$arrBind = array( ':anioEliminar' => $anioEliminar);
						
						//echo "<br><br>3-1 modeloAdmin.php:actualizarTablaControles:arrBind: "; print_r($arrBind); 
						
						$resEliminarControles = borrarFilas($tControles,$cadenaCondicionesEliminar,$conexionLinkDB,$arrBind);//probado error ok y rollback

						//echo "<br><br>3-2 modeloAdmin.php:actualizarTablaControles:resEliminarControles: "; print_r($resEliminarControles);							

						if ($resEliminarControles['codError'] !=='00000')			
						{$arrActualizarTablaControles = $resEliminarControles;		
							$arrActualizarTablaControles['textoComentarios'] .= " .Error del sistema al eliminar filas en CONTROLES, en actualizarTablaControles() ";
						}
						elseif ($resEliminarControles['numFilas'] <= 0 )// cero filas es error,siempre debiera devolver 1
						{$arrActualizarTablaControles = $resEliminarControles;
							$arrActualizarTablaControles['textoComentarios'] .= ". Error al eliminar filas en tabla CONTROLES en actualizarTablaControles() ";
						}	
						else// $resEliminarControles['codError'] =='00000' 
						{ 											
        $cadResumenActualizarTablaControl = "<br /><br />- Se han borrado los datos de ".$resEliminarControles['numFilas'].
											                         	" filas de la tabla CONTROLES correspondientes anteriores al anio ".$anioEliminar.$cadResumenActualizarTablaControl;		

        $arrActualizarTablaControles['textoComentarios'] = $cadResumenActualizarTablaControl;									
						}			
						//--- Fin Eliminar de tabla CONTROLES' filas de años anteriores	-----------
			 }//else  $resInsertarControlActualizadoAnio['codError']=00000' y $resInsertarControlActualizadoAnio['codError']=='00000'					
			}//else $resActualizarControlActualizadoAnio['codError'] == '00000'
			
	}//else if ( (isset($anioNuevo) ...) )		
		
	//echo "<br><br>4 modeloAdmin:actualizarTablaControles:arrActualizarTablaControles: ";print_r($arrActualizarTablaControles);
	
	return $arrActualizarTablaControles;
}
/*------------------------- Fin actualizarTablaControles() ------------------------------------------------*/

/*============================== FIN procesos de mCierreAnioPasadoAperturaAnioNuevoAdmin() ================*/
/*=========================================================================================================*/



/**************** INICIO ALMACÉN ANTIGUAS FUNCIONES AHORA NO UTILIZADAS ***************************
***************************************************************************************************
***************************************************************************************************/


/******** INICIO ENCRIPTACIÓN-DESENCRIPTACIÓN de cuentas bancarias  - CONSERVAR - *****************/
/*---------------------------- Inicio  encriptarCCyCEXAdmin ----------------------------------------
Se hizo temporalmente para aumentar la seguridad en la base de datos, pero debido a que se debía permitir 
el acceso a los datos completos por parte de coordinares/as, lo quité porque pensé ya no merecía la pena, 
pero acaso fuese conveniente poner de nuevo como protección en la BBDD de las cuentas.
Estas funciones y otras están disponibles también en antiguas versiones de modeloSocios, modeloPresCood,
modeloTesorero,

Descripción:-Lee de la tabla SOCIO  las cuentas bancarias, encripta los 
             campos CCEXTRANJERA, NUMCUENTA, y los graba de nuevo encriptados 
													en la tabla SOCIO 

Llamada desde: cAdmin.php:encriptarCCyCEXAnterioresAdmin()
Llama:modeloSocios.php:actualizSocio()Dentro de ella se encripta 
Recibe: Nada 
OBSERVACIONES: SOLO LO HICE UNA VEZ CUANDO FUE NECESARIO ENCRIPTAR ESAS COLUMNAS 
No se utiliza
--------------------------------------------------------------------------------------------------*/
function encriptarCCyCEXAdmin()
{
 $resEncriptarCCyCEX['codError']='00000';
 $resEncriptarCCyCEX['errorMensaje']='';
 $resEncriptarCCyCEX['nomScript']='modeloAdmin.php';
 $resEncriptarCCyCEX['nomFuncion']='encriptarCCyCEXAdmin';
	$arrMensaje['textoCabecera']='ENCRIPTAR NÚMERO CUENTA ESPAÑOLA Y CUENTA EXTRAJERA';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al buscar los datos del socio,
	vuelva a intentarlo pasado un tiempo ";	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resEncriptarCCyCEX=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}	
 else //$conexionUsuariosDB['codError']=="00000"
	{$iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 { $resIniTrans['codError']='70501';   
			 $resIniTrans['errno']=mysql_errno(); 
	   $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				                             'Error mysql_query '.mysql_error();
	   $resIniTrans['numFilas']=0;	
			
			 $resEncriptarCCyCEX=$resIniTrans;	
	 }
		else	//($resIniTrans)
	 {$tablasBusqueda ='SOCIO';
		 $camposBuscados ='CODUSER, NUMCUENTA, CCEXTRANJERA';
		 $cadCondicionesBuscar=" ";//se busca todos
    
		 $resDatosCCyCEX = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			                     								    $camposBuscados,$conexionUsuariosDB['conexionLink']);  
		 echo "<br><br>1 modeloAdmin:encriptarCCyCEXAdmin:resDatosCCyCEX:";print_r($resDatosCCyCEX);	
																																		
		 if ($resDatosCCyCEX['codError']!=='00000')
		 {$resEncriptarCCyCEX['codError']=$resDatosCCyCEX['codError'];
    $resEncriptarCCyCEX['errorMensaje']=$resDatosCCyCEX['errorMensaje'];
		  $resEncriptarCCyCEX['nomScript']=$resDatosCCyCEX['nomScript'];//se van a repetir
		  $resEncriptarCCyCEX['nomFuncion']=$resDatosCCyCEX['nomFuncion'];//se van a repetir
		 }	
	 	elseif ($resDatosCCyCEX['numFilas']==0)
		 {$resEncriptarCCyCEX['codError']='80001'; //no encontrado
			 $resEncriptarCCyCEX['errorMensaje']="No existen socio, algo imposible";
		 }	
	  else//$resDatosCCyCEX['codError']=='00000')
		 {require_once './modelos/modeloSocios.php';
    require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
				
 		 $reActSocio['codError'] = '00000';
				$datosCCyCEX = $resDatosCCyCEX['resultadoFilas'];
    $numFilaTotal = $resDatosCCyCEX['numFilas'];				
				$numFila = 0;		
    echo "<br><br>2-0 modeloAdmin:encriptarCCyCEXAdmin:numFila"; print_r($numFila);

    //===== Inicio bucle para llamar a la función actualizSocio, que ya incluye encriptar CC y CEX  =====
				while ($numFila < $numFilaTotal && $reActSocio['codError'] == '00000' )
			 {echo "<br><br>2-1 modeloAdmin:encriptarCCyCEXAdmin:coduser:"; print_r($datosCCyCEX[$numFila]['CODUSER']);	
	    
				 if ((isset($datosCCyCEX[$numFila]['NUMCUENTA'])	&& !empty($datosCCyCEX[$numFila]['NUMCUENTA'])) ||
					    (isset($datosCCyCEX[$numFila]['CCEXTRANJERA'])	&& !empty($datosCCyCEX[$numFila]['CCEXTRANJERA']))
								)
					{
						if (isset($datosCCyCEX[$numFila]['NUMCUENTA'])	&& !empty($datosCCyCEX[$numFila]['NUMCUENTA']))
		    {$arrayDatos['NUMCUENTA'] = encriptarBase64($datosCCyCEX[$numFila]['NUMCUENTA']);
			    $arrayDatos['CCEXTRANJERA'] = NULL;	
		    }
	    	//Si existe 	['CCEXTRANJERA'] se encripta
		    else//if (isset($datosCCyCEX[$numFila]['CCEXTRANJERA'])	&& !empty($datosCCyCEX[$numFila]['CCEXTRANJERA']))
					 {$arrayDatos['CCEXTRANJERA'] = encriptarBase64($datosCCyCEX[$numFila]['CCEXTRANJERA']);
							$arrayDatos['CODENTIDAD'] = NULL;		
							$arrayDatos['CODSUCURSAL'] = NULL;		
							$arrayDatos['DC'] = NULL;		
							$arrayDatos['NUMCUENTA'] = NULL;		
						}
	     //echo '<br><br>3-1 modeloAdmin:encriptarCCyCEXAdmin:arrayDatos';print_r($arrayDatos);	
					 $arrayCondiciones['CODUSER']['valorCampo']= $datosCCyCEX[$numFila]['CODUSER'];
					 $arrayCondiciones['CODUSER']['operador']= '=';
					 $arrayCondiciones['CODUSER']['opUnir']= ' ';  
					
		    $reActSocio = actualizarTabla('SOCIO',$arrayCondiciones,$arrayDatos);
				 	//echo "<br><br>3-2 modeloAdmin:encriptarCCyCEXAdmin:reActSocio: "; print_r($reActSocio);
				
				  if ($reActSocio['codError']!=='00000')			
		    {$resEncriptarCCyCEX=$reActSocio;
			   }						
					}
     $numFila++;
    }//while ($numFila < $numFilaTotal && $reActSocio['codError'] == '00000'  )
    //===== Fin bucle para llamar a la función actualizSocio, que ya incluye encriptar CC y CEX  =====

    echo '<br><br>4-1: modeloAdmin:encriptarCCyCEXAdmin:resEncriptarCCyCEX ';print_r($resEncriptarCCyCEX);
								
				if ($resEncriptarCCyCEX['codError'] =='00000') 
				{echo '<br><br>4-2: modeloAdmin:encriptarCCyCEXAdmin:resEncriptarCCyCEX: ';print_r($resEncriptarCCyCEX);
				 $resEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />Se han actualizado las CC y CEX de los socios ";
				
			  $finTrans = "COMMIT";
	    $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
					
				 if (!$resFinTrans)
			  {$resFinTrans['codError']='70502';   
						  $resFinTrans['errno']=mysql_errno(); 
				    $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
							           'Error mysql_query '.mysql_error();
				    $resFinTrans['numFilas']=0;	
						
						  $resEncriptarCCyCEX = $resFinTrans;	
					}		
				 else //se ha ejecutado correctamente
				 {
					 $resEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />Se han actualizado encriptado las CC CEX de los socios/as <br /><br />";
					}	
	   }//if ($resEncriptarCCyCEX['codError']=='00000')			
	   else //($resEncriptarCCyCEX['codError']!=='00000')
		  {echo '<br><br>3 modeloAdmin:encriptarCCyCEXAdmin:resEncriptarCCyCEX: ';print_r($resEncriptarCCyCEX);
		
			  $deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT ";
			  $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
				 if (!$resDeshacerTrans)
			  { $resDeshacerTrans['codError']='70503';   
					  $resDeshacerTrans['errno']=mysql_errno(); 
			    $resDeshacerTrans['errorMensaje'].='Error en el sistema, no se ha podido deshacer la transación en actualizarCuotasSociosAnioNuevo '.
						                                     'Error mysql_query '.mysql_error();
			    $resDeshacerTrans['numFilas'] = 0;	
					
					  $resEncriptarCCyCEX=$resDeshacerTrans;
				   $resEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />. ERROR al deshacer transación";
			  }
				 $resEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />. ERROR: se han deshecho todas las transaciones";
	
					require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			 	$resInsertarErrores = insertarError($actualizarCuotasSociosAnioNuevo);	
		  }//else ($resEncriptarCCyCEX['codError']!=='00000')
   }//$resDatosCCyCEX['codError']=='00000')
		}//$resIniTrans
 }//$conexionUsuariosDB['codError']=="00000"
																																														
	echo '<br><br>5 modeloAdmin:encriptarCCyCEXAdmin:resEncriptarCCyCEX: ';print_r($resEncriptarCCyCEX);
	return $resEncriptarCCyCEX;
}
//------------------------------ Fin  encriptarCCyCEXAdmin------------------------------------------

/*---------------------------- Inicio  desEncriptarCCyCEXAdmin -------------------------------------
Descripción:-Lee de la tabla SOCIO  las cuentas bancarias, encripta los 
             campos CCEXTRANJERA, NUMCUENTA, y los graba de nuevo encriptados 
													en la tabla SOCIO 

Llamada desde: cAdmin.php:encriptarCCyCEXAnterioresAdmin()
Llama:modeloSocios.php:actualizSocio()Dentro de ella se encripta 
Recibe: Nada 
OBSERVACIONES: SOLO LO HICE UNA VEZ CUANDO FUE NECESARIO ENCRIPTAR ESAS COLUMNAS 
No se utiliza
--------------------------------------------------------------------------------------------------*/
function desEncriptarCCyCEXAdmin()
{
 $resDesEncriptarCCyCEX['codError']='00000';
 $resDesEncriptarCCyCEX['errorMensaje']='';
 $resDesEncriptarCCyCEX['nomScript']='modeloAdmin.php';
 $resDesEncriptarCCyCEX['nomFuncion']='desEencriptarCCyCEXAdmin';
	$arrMensaje['textoCabecera']='DESENCRIPTAR NÚMERO CUENTA ESPAÑOLA Y CUENTA EXTRAJERA';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al desencriptar los datos de cuentas banco socios,	vuelva a intentarlo pasado un tiempo ";	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resDesEncriptarCCyCEX=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}	
 else //$conexionUsuariosDB['codError']=="00000"
	{$iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 { $resIniTrans['codError']='70501';   
			 $resIniTrans['errno']=mysql_errno(); 
	   $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				                             'Error mysql_query '.mysql_error();
	   $resIniTrans['numFilas']=0;	
			
			 $resDesEncriptarCCyCEX=$resIniTrans;	
	 }
		else	//($resIniTrans)
	 {$tablasBusqueda ='SOCIO';
		 $camposBuscados ='CODUSER, NUMCUENTA, CCEXTRANJERA';
		 $cadCondicionesBuscar=" ";//se busca todos
    
		 $resDatosCCyCEX = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			                     								    $camposBuscados,$conexionUsuariosDB['conexionLink']);  
		 //echo "<br><br>1 modeloAdmin:desEncriptarCCyCEXAdmin:resDatosCCyCEX:";print_r($resDatosCCyCEX);	
																																		
		 if ($resDatosCCyCEX['codError']!=='00000')
		 {$resDesEncriptarCCyCEX['codError']=$resDatosCCyCEX['codError'];
    $resDesEncriptarCCyCEX['errorMensaje']=$resDatosCCyCEX['errorMensaje'];
		  $resDesEncriptarCCyCEX['nomScript']=$resDatosCCyCEX['nomScript'];//se van a repetir
		  $resDesEncriptarCCyCEX['nomFuncion']=$resDatosCCyCEX['nomFuncion'];//se van a repetir
		 }	
	 	elseif ($resDatosCCyCEX['numFilas']==0)
		 {$resDesEncriptarCCyCEX['codError']='80001'; //no encontrado
			 $resDesEncriptarCCyCEX['errorMensaje']="Error: No existen socios/as, algo imposible";
		 }	
	  else//$resDatosCCyCEX['codError']=='00000')
		 {require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
				
 		 $reActSocio['codError'] = '00000';
				$datosCCyCEX = $resDatosCCyCEX['resultadoFilas'];
    $numFilaTotal = $resDatosCCyCEX['numFilas'];				
				$numFila = 0;		
    echo "<br><br>2-0 modeloAdmin:desEncriptarCCyCEXAdmin:numFila"; print_r($numFilaTotal);

    //===== Inicio bucle para llamar a la función actualizSocio, que ya incluye encriptar CC y CEX  =====
				while ($numFila < $numFilaTotal)
			 {echo "<br><br>2-1 modeloAdmin:encriptarCCyCEXAdmin:numFila"; print_r($numFila);
				 echo " :coduser:"; print_r($datosCCyCEX[$numFila]['CODUSER']);	
    
					if (isset($datosCCyCEX[$numFila]['NUMCUENTA'])	&& !empty($datosCCyCEX[$numFila]['NUMCUENTA']))
	    {$resDatosCCyCEX['resultadoFilas'][$numFila]['NUMCUENTA'] = desEncriptarBase64($datosCCyCEX[$numFila]['NUMCUENTA']);
		    $resDatosCCyCEX['resultadoFilas'][$numFila]['CCEXTRANJERA'] = NULL;	
	    }
    	//Si existe 	['CCEXTRANJERA'] se encripta
	    elseif (isset($datosCCyCEX[$numFila]['CCEXTRANJERA'])	&& !empty($datosCCyCEX[$numFila]['CCEXTRANJERA']))
				 {$resDatosCCyCEX['resultadoFilas'][$numFila]['CCEXTRANJERA'] = desEncriptarBase64($datosCCyCEX[$numFila]['CCEXTRANJERA']);
						$resDatosCCyCEX['resultadoFilas'][$numFila]['CODENTIDAD'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['CODSUCURSAL'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['DC'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['NUMCUENTA'] = NULL;		
					}
					else
				 {$resDatosCCyCEX['resultadoFilas'][$numFila]['CCEXTRANJERA'] =  NULL;	
						$resDatosCCyCEX['resultadoFilas'][$numFila]['CODENTIDAD'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['CODSUCURSAL'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['DC'] = NULL;		
						$resDatosCCyCEX['resultadoFilas'][$numFila]['NUMCUENTA'] = NULL;		
					}
	    echo '<br><br>3-1 modeloAdmin:desEncriptarCCyCEXAdmin:resDatosCCyCEX[resultadoFilas][$numFila]';print_r($resDatosCCyCEX['resultadoFilas'][$numFila]);
     $numFila++;
    }//while ($numFila < $numFilaTotal && $reActSocio['codError'] == '00000'  )
    //===== Fin bucle para llamar a la función actualizSocio, que ya incluye encriptar CC y CEX  =====
    $resDesEncriptarCCyCEX['resultadoFilas'] =$resDatosCCyCEX['resultadoFilas'];
    echo '<br><br>4-1: modeloAdmin:desEncriptarCCyCEXAdmin:resDesEncriptarCCyCEX: ';print_r($resDesEncriptarCCyCEX);
								
				if ($resEncriptarCCyCEX['codError'] =='00000') //sobraría est if
				{echo '<br><br>4-2: modeloAdmin:encriptarCCyCEXAdmin:resEncriptarCCyCEX: ';print_r($resEncriptarCCyCEX);
				 $resDesEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />Se han desencriptado las CC y CEX de los socios ";
				
			  $finTrans = "COMMIT";
	    $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
					
				 if (!$resFinTrans)
			  {$resFinTrans['codError']='70502';   
						  $resFinTrans['errno']=mysql_errno(); 
				    $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
							           'Error mysql_query '.mysql_error();
				    $resFinTrans['numFilas']=0;	
						
						  $resDesEncriptarCCyCEX = $resFinTrans;	
					}		
				 else //se ha ejecutado correctamente
				 {
					 $resDesEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />Se han actualizado encriptado las CC CEX de los socios/as <br /><br />";
					}	
	   }//if ($resEncriptarCCyCEX['codError']=='00000')			
	   else //($resEncriptarCCyCEX['codError']!=='00000')
		  {echo '<br><br>3 modeloAdmin:desEncriptarCCyCEXAdmin:resEncriptarCCyCEX: ';print_r($resEncriptarCCyCEX);
		
			  $deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT ";
			  $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
				 if (!$resDeshacerTrans)
			  { $resDeshacerTrans['codError']='70503';   
					  $resDeshacerTrans['errno']=mysql_errno(); 
			    $resDeshacerTrans['errorMensaje'].='Error en el sistema, no se ha podido deshacer la transación en actualizarCuotasSociosAnioNuevo '.
						                                     'Error mysql_query '.mysql_error();
			    $resDeshacerTrans['numFilas'] = 0;	
					
					  $resDesEncriptarCCyCEX=$resDeshacerTrans;
				   $resDesEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />. ERROR al deshacer transación";
			  }
				 $resDesEncriptarCCyCEX['arrMensaje']['textoComentarios'].="<br /><br />. ERROR: se han deshecho todas las transaciones";
	
					require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			 	$resInsertarErrores = insertarError($actualizarCuotasSociosAnioNuevo);	
		  }//else ($resEncriptarCCyCEX['codError']!=='00000')
   }//$resDatosCCyCEX['codError']=='00000')
		}//$resIniTrans
 }//$conexionUsuariosDB['codError']=="00000"
																																														
	echo '<br><br>5 modeloAdmin:desEncriptarCCyCEXAdmin:resDesEncriptarCCyCEX: ';print_r($resDesEncriptarCCyCEX);
	return $resDesEncriptarCCyCEX;
}
//------------------------------ Fin  desEncriptarCCyCEXAdmin---------------------------------------

/******************* FIN ENCRIPTACIÓN-DESENCRIPTACIÓN de cuentas bancarias de tabla SOCIO *********/


/********* INICIO PROCESOS IMPORTAR LOS ANTIGUOS DATOS DE SOCIOS DE EL DESDE EXCEL  ****************
- conservar por si alguna vez hubiese que importar pudiera servir de ejemplo  

SE UTILIZÓ PARA IMPORTAR LOS DATOS QUE HABÍA EN EXCEL DE Estal y de Andalucía PREVIO A LA CREACIÓN 
DE ESTA APLICACIÓN DE GESTIÓN DE SOCI@S
***************************************************************************************************/
/*---------------------------- Inicio importarExcelSociosES ---------------------
DESCRIPCIÓN: 
Busca todos los datos en la tabla EXCELESTATAL (que viene de importar 
la hoja Excel de Europa Laica Estatal) y los inserta en la tabla
"EXCELTODOS", que es una tabla intermedia después se juntará con la 
importación de hojas Excel de otras agrupaciones (Andalucía, 
Asturias, ...) ya que cada una tiene formatos distintos.  
mediante: importarExcelSociosAN(), para después insertar los datos en 
las tablas correspondientes: USUARIO, MIEMBRO, ....

LLAMADA: cAdmin.php:importarExcelSociosESad()
LLAMA: 
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarEnTablas(),insertarVariasFilas(),
modelos/modeloErrores.php:insertarError()

OBSERVACIONES: solo se ejecuta una vez por el administrador de la aplicación
Agustín: dejar por si fuese útil para otras situaciones
//-----------------------------------------------------------------------------*/
function importarExcelSociosES()
{
 $reImportarExcelSocios['codError']='00000';
 $reImportarExcelSocios['errorMensaje']='';
 $reImportarExcelSocios['nomScript']='modeloAdmin.php';
 $$reImportarExcelSocios['nomFuncion']='buscarDatosExel';//se van a repetir
	$arrMensaje['textoCabecera']='Importar desde la BBDD Excel';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	

	require_once "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $reImportarExcelSocios=$conexionUsuariosDB;
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{ 
	 $tablasBusqueda = "EXCELESTATAL";	
		//$tablasBusqueda = "EXCELESTATAL2";	
		$camposBuscados = 
		
		"ID, ESTADO,			
			NUMERODOC,TIPODOCUMENTO,PAISDOC,
			NOMBRE,SUBSTRING_INDEX(NOMBRE,',',-1) as NOM, SUBSTRING_INDEX(SUBSTRING_INDEX(NOMBRE,',',1),' ',1)  as APE1,
			REPLACE(SUBSTRING_INDEX(NOMBRE,',',1), SUBSTRING_INDEX(SUBSTRING_INDEX(NOMBRE,',',1),' ',1), '') as APE2,
			SEXO, FECHANACIMIENTO,
			DIRECCION,LOCALIDAD,PROVINCIAES,CP, CAUTONOMAES,PAISDOM,MAIL,TEL,TELMO,PROFESIONOCUPACION,OBSERVACIONES,
			
			ID, NCUENTA,SUBSTRING(NCUENTA,'1',4) as CODENTIDAD, SUBSTRING(NCUENTA,'6',4) as CODSUCURSAL, SUBSTRING(NCUENTA,'11',2) as DC, 
			SUBSTRING(NCUENTA,'14',10) as NUMCUENTA, MODOINGRESO,
			
			FECHAALTA,FECHABAJA, 
			
			CUOTA2011,FECHAPAGO2011,CUOTA2010,FECHAPAGO2010,CUOTA2010,FECHAPAGO2010, CUOTA2009, FECHAPAGO2009 ";	
				
		//$cadCondicionesBuscar = " ORDER BY SUBSTRING(CAUTONOMAES, '2',2)";	
		$cadCondicionesBuscar = " ";	
	
		    
		$datosExel = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionUsuariosDB['conexionLink']);  
			echo "<br><br>2 modeloAdmin:importarExcelSociosES:datosExel: ";print_r($datosExel);

		if ($datosExel['codError']!=='00000')
		{ $reImportarExcelSocios['codError']=$datosExel['codError'];
    $reImportarExcelSocios['errorMensaje']=$datosExel['errorMensaje'];
		  $reImportarExcelSocios['nomScript']=$datosExel['nomScript'];//se van a repetir
		  $reImportarExcelSocios['nomFuncion']=$datosExel['nomFuncion'];//se van a repetir
		}	
		elseif ($datosExel['numFilas'] == 0)
		{$reImportarExcelSocios['codError'] = '80001'; //no encontrado
			$reImportarExcelSocios['errorMensaje'] = "Tabla BBDDEXCEL vacía";
		}	
	 else //$datosExel['codError']=='00000'
		{require_once './modelos/libs/eliminarAcentos.php';
		 $f = 0;
			$fUltima=$datosExel['numFilas'];

			while ($f < $fUltima)
		 {                        
    $reDatosExel['valoresCampos'][$f]['ID'] = $datosExel['resultadoFilas'][$f]['ID'];
				if (isset($datosExel['resultadoFilas'][$f]['ESTADO']) && $datosExel['resultadoFilas'][$f]['ESTADO']=='BAJA')
				{$reDatosExel['valoresCampos'][$f]['ESTADO']='baja';	
				}
				else
				{$reDatosExel['valoresCampos'][$f]['ESTADO']='alta';	
				}							
				$reDatosExel['valoresCampos'][$f]['TIPOMIEMBRO'] = 'socio';		
				$reDatosExel['valoresCampos'][$f]['CODCUOTA'] ='General';		
				
				$reDatosExel['valoresCampos'][$f]['CODPAISDOC'] = $datosExel['resultadoFilas'][$f]['PAISDOC'];		
				$reDatosExel['valoresCampos'][$f]['NUMDOCUMENTOMIEMBRO'] =$datosExel['resultadoFilas'][$f]['NUMERODOC'];
			 $reDatosExel['valoresCampos'][$f]['TIPODOCUMENTOMIEMBRO'] =$datosExel['resultadoFilas'][$f]['TIPODOCUMENTO'];
				$reDatosExel['valoresCampos'][$f]['APE1'] = addslashes(trim($datosExel['resultadoFilas'][$f]['APE1']));
				$reDatosExel['valoresCampos'][$f]['APE2'] = addslashes(trim($datosExel['resultadoFilas'][$f]['APE2']));
				$reDatosExel['valoresCampos'][$f]['NOM'] = addslashes(trim($datosExel['resultadoFilas'][$f]['NOM']));
    $reDatosExel['valoresCampos'][$f]['SEXO'] = $datosExel['resultadoFilas'][$f]['SEXO'];							
				$reDatosExel['valoresCampos'][$f]['FECHANAC'] =$datosExel['resultadoFilas'][$f]['FECHANACIMIENTO'];
				$reDatosExel['valoresCampos'][$f]['TELFIJOCASA'] = soloNumeros($datosExel['resultadoFilas'][$f]['TEL']);
				$reDatosExel['valoresCampos'][$f]['TELMOVIL'] = soloNumeros($datosExel['resultadoFilas'][$f]['TELMO']);
				$reDatosExel['valoresCampos'][$f]['EMAIL'] = addslashes(trim($datosExel['resultadoFilas'][$f]['MAIL']));							
				$reDatosExel['valoresCampos'][$f]['PROFESION'] = addslashes(trim($datosExel['resultadoFilas'][$f]['PROFESIONOCUPACION']));
				$reDatosExel['valoresCampos'][$f]['DIRECCION'] = addslashes(trim($datosExel['resultadoFilas'][$f]['DIRECCION']));							
				$reDatosExel['valoresCampos'][$f]['LOCALIDAD'] = addslashes(trim($datosExel['resultadoFilas'][$f]['LOCALIDAD']));
				$reDatosExel['valoresCampos'][$f]['CP'] = addslashes(trim($datosExel['resultadoFilas'][$f]['CP']));
				$reDatosExel['valoresCampos'][$f]['CODPAISDOM'] = trim($datosExel['resultadoFilas'][$f]['PAISDOM']);		
    $reDatosExel['valoresCampos'][$f]['OBSERVACIONES'] = addslashes(trim($datosExel['resultadoFilas'][$f]['OBSERVACIONES']));			
									
    if (	$reDatosExel['valoresCampos'][$f]['CODPAISDOM'] == 'ES')
				{ if (substr($reDatosExel['valoresCampos'][$f]['CP'],0,2) == 28) //provincia Madrid
						{ $reDatosExel['valoresCampos'][$f]['CODAGRUPACION'] ='01300000';	//Madrid	
						}
						else 
						{ $reDatosExel['valoresCampos'][$f]['CODAGRUPACION'] ='00000000';	//estatal		
						}
				}
				else // todos los extranjeros
				{$reDatosExel['valoresCampos'][$f]['CODAGRUPACION'] ='00000000';									
				}
				
    $reDatosExel['valoresCampos'][$f]['FECHAALTA'] =$datosExel['resultadoFilas'][$f]['FECHAALTA'];	
				$reDatosExel['valoresCampos'][$f]['FECHABAJA'] =$datosExel['resultadoFilas'][$f]['FECHABAJA'];								
				//$reDatosExel['valoresCampos'][$f]['GRUPOTERRITORIALES'] =$datosExel['resultadoFilas'][$f]['GRUPOTERRITORIALES'];
				$reDatosExel['valoresCampos'][$f]['CODENTIDAD'] =$datosExel['resultadoFilas'][$f]['CODENTIDAD'];
				$reDatosExel['valoresCampos'][$f]['CODSUCURSAL'] =$datosExel['resultadoFilas'][$f]['CODSUCURSAL'];
				$reDatosExel['valoresCampos'][$f]['DC'] =$datosExel['resultadoFilas'][$f]['DC'];
				$reDatosExel['valoresCampos'][$f]['NUMCUENTA'] =$datosExel['resultadoFilas'][$f]['NUMCUENTA'];				
				$reDatosExel['valoresCampos'][$f]['MODOINGRESO'] =$datosExel['resultadoFilas'][$f]['MODOINGRESO'];
				
		  $reDatosExel['valoresCampos'][$f]['IMPORTECUOTAANIOSOCIO']=30.00;//cuota elegida por el socio
				
				if ($datosExel['resultadoFilas'][$f]['CUOTA2011']=='VERDADERO')
				{$reDatosExel['valoresCampos'][$f]['CUOTA2011']=30.00;
				 $reDatosExel['valoresCampos'][$f]['FECHAPAGO2011']=$datosExel['resultadoFilas'][$f]['FECHAPAGO2011'];
				}							  						
				elseif ($datosExel['resultadoFilas'][$f]['CUOTA2011']=='FALSO')
				{ $reDatosExel['valoresCampos'][$f]['CUOTA2011']=00.00;
						$reDatosExel['valoresCampos'][$f]['FECHAPAGO2011']='0000-00-00';
				}		
				else// ($datosExel['resultadoFilas'][$f]['CUOTA2011']=='-1')
				{ $reDatosExel['valoresCampos'][$f]['CUOTA2011']='-1';
					  	$reDatosExel['valoresCampos'][$f]['FECHAPAGO2011']='0000-00-00';
				}							
				
				if ($datosExel['resultadoFilas'][$f]['CUOTA2010']=='VERDADERO')
				{$reDatosExel['valoresCampos'][$f]['CUOTA2010']=30.00;
				 $reDatosExel['valoresCampos'][$f]['FECHAPAGO2010']=$datosExel['resultadoFilas'][$f]['FECHAPAGO2010'];
				}							  						
				elseif ($datosExel['resultadoFilas'][$f]['CUOTA2010']=='FALSO')
				{ $reDatosExel['valoresCampos'][$f]['CUOTA2010']=00.00;
						$reDatosExel['valoresCampos'][$f]['FECHAPAGO2010']='0000-00-00';
				}
				else// ($datosExel['resultadoFilas'][$f]['CUOTA2010']=='-1')//no esta dado de alta como socio
				{$reDatosExel['valoresCampos'][$f]['CUOTA2010']=-1;
					$reDatosExel['valoresCampos'][$f]['FECHAPAGO2010']='0000-00-00';
				}		
										
				if ($datosExel['resultadoFilas'][$f]['CUOTA2009']=='VERDADERO')
				{$reDatosExel['valoresCampos'][$f]['CUOTA2009']=30.00;
				 $reDatosExel['valoresCampos'][$f]['FECHAPAGO2009']=$datosExel['resultadoFilas'][$f]['FECHAPAGO2009'];
				}							
				elseif ($datosExel['resultadoFilas'][$f]['CUOTA2009']=='FALSO')
				{ $reDatosExel['valoresCampos'][$f]['CUOTA2009']=00.00;
      $reDatosExel['valoresCampos'][$f]['FECHAPAGO2009']='0000-00-00'; 								
				}
				else// ($datosExel['resultadoFilas'][$f]['CUOTA2009']=='-1')//no esta dado de alta como socio
				{$reDatosExel['valoresCampos'][$f]['CUOTA2009']=-1;
					$reDatosExel['valoresCampos'][$f]['FECHAPAGO2009']='0000-00-00';
				}							
 
	   $f++;
	  }//while ($f < $fUltima)
			//echo "<br><br>2 modeloAdmin:importarExcelSociosES:$reDatosExel: ";print_r($reDatosExel);
			
		 $reDatosExelUsuario['numFilas'] = $datosExel['numFilas'];	
			//$reDatosExelUsuario['numFilas'] = 9;	
			$reDatosExelUsuario['resultadoFilas'] = $reDatosExel['valoresCampos'];
			
   echo "<br><br>2 modeloAdmin:reDatosExelUsuario: ";print_r($reDatosExelUsuario);			
			$resulInserUsuario = insertarVariasFilas('EXCELTODOS',$reDatosExelUsuario,$conexionUsuariosDB['conexionLink']);
			//$resulInserUsuario = insertarVariasFilas('EXCELTODOS2',$reDatosExelUsuario,$conexionUsuariosDB['conexionLink']);
			
		} //$datosExel['codError']=='00000'
 	echo "<br><br>3 modeloAdmin:resulInserUsuario: ";print_r($resulInserUsuario);
		
		if ($resulInserUsuario['codError']!=='00000')//puede ser <80000 o 80001
		{ $arrMensaje['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
		  
				require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
				$resInsertarErrores = insertarError($reDatosExelUsuario);			
				if ($resInsertarErrores['codError']!=='00000')
		  {$reDatosExelUsuario['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'].='Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo';
				}
		}
		elseif ($resulInserUsuario['numFilas'] == 0)
	 { $reImportarExcelSocios['codError'] = '80001';//no se han insertado, no existe usuario ....
    $reImportarExcelSocios['errorMensaje'] = 'Aviso: no se ha insertado ningún usuario, posible error';  
	 }
		else // acaso tratar que se han insertado 0 filas
		{  $reImportarExcelSocios=$resulInserUsuario;			
		}
		
	}	//$conexionUsuariosDB['codError']=='00000'
 echo "<br><br>4 modeloAdmin:resulInserUsuario:reImportarExcelSocios: ";print_r($reImportarExcelSocios);	
 
 return 	$reImportarExcelSocios; 	
}
//------------------------------ Fin importarExcelSociosES -----------------------

/*---------------------------- Inicio importarExcelSociosAN ---------------------
DESCRIPCIÓN: 
Busca todos los datos en la tabla EXCELANDALUCIA (viene de importar 
la hoja Excel de Europa Laica Andalucía) y los inserta en la tabla
"EXCELTODOS", que es una tabla intermedia después se juntará con la 
importación de hojas Excel de otras agrupaciones (estatal, 
Asturias, ...) ya que cada una tiene formatos distintos.  
mediante: importarExcelSociosAN(), para después insertar los datos en 
las tablas correspondientes: USUARIO, MIEMBRO, ....

LLAMADA: cAdmin.php:importarExcelSociosANad()
LLAMA: 
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarEnTablas(),insertarVariasFilas(),
modelos/modeloErrores.php:insertarError()

OBSERVACIONES: solo se ejecuta una vez por el administrador de la aplicación
Agustín: dejar por si fuese útil para otras situaciones
//-----------------------------------------------------------------------------*/
function importarExcelSociosAN()
{
 $reImportarExcelSocios['codError']='00000';
 $reImportarExcelSocios['errorMensaje']='';
 $reImportarExcelSocios['nomScript']='modeloAdmin.php';
 $$reImportarExcelSocios['nomFuncion']='buscarDatosExel';//se van a repetir
	$arrMensaje['textoCabecera']='Importar desde la BBDD Excel';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	require_once "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $reImportarExcelSocios = $conexionUsuariosDB;
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{$tablasBusqueda = "EXCELANDALUCIA";	
	 //$tablasBusqueda = "EXCELANDALUCIA2";	
		$camposBuscados = 
		" ESTADO, id as ID, activo AS CODCUOTA, 
			NIF AS NUMDOCUMENTOMIEMBRO, TIPODOC AS TIPODOCUMENTOMIEMBRO, PAISDOC AS CODPAISDOC	, nombre AS NOM, 
			
			apellidos, SUBSTRING_INDEX(apellidos,' ',1)  as APE1,	REPLACE(apellidos, SUBSTRING_INDEX(apellidos,' ',1), '') as APE2,	SEXO,			
	
			direccion AS DIRECCION,localidad AS LOCALIDAD,provincia AS PROVINCIA, cpostal AS CP, PAISDOM AS CODPAISDOM, 
			
			email AS EMAIL,telefonos AS TELFIJOCASA, TELMOVIL AS TELMOVIL,
			ctrabajo AS PROFESION,OBSERVACIONES,
			
			Cuenta_bancaria,SUBSTRING(Cuenta_bancaria,'1',4) as CODENTIDAD, SUBSTRING(Cuenta_bancaria,'6',4) as CODSUCURSAL, 
			SUBSTRING(Cuenta_bancaria,'11',2) as DC, SUBSTRING(Cuenta_bancaria,'14',10) as NUMCUENTA,			MODOINGRESO,
			
			fecha_alta AS FECHAALTA, fecha_baja AS FECHABAJA, grupo_AND AS CODAGRUPACION, 
			
			IMPORTECUOTAANIOSOCIO,CUOTA2011,FECHAPAGO2011, CUOTA2010,FECHAPAGO2010, CUOTA2009,FECHAPAGO2009 ";	
				
		$cadCondicionesBuscar = "";
			    
		$datosExel = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionUsuariosDB['conexionLink']);  

		if ($datosExel['codError']!=='00000')
		{ $reImportarExcelSocios['codError']=$datosExel['codError'];
    $reImportarExcelSocios['errorMensaje']=$datosExel['errorMensaje'];
		  $reImportarExcelSocios['nomScript']=$datosExel['nomScript'];//se van a repetir
		  $reImportarExcelSocios['nomFuncion']=$datosExel['nomFuncion'];//se van a repetir
		}	
		elseif ($datosExel['numFilas'] == 0)
		{$reImportarExcelSocios['codError'] = '80001'; //no encontrado
			$reImportarExcelSocios['errorMensaje'] = "Tabla BBDDEXCEL vacía";
		}	
	 else //$datosExel['codError']=='00000'
		{require_once './modelos/libs/eliminarAcentos.php';
		 $f = 0;
			$fUltima=$datosExel['numFilas'];

			//while ($f < 1)
	  while ($f < $fUltima)
		 { 
		  $reDatosExel['valoresCampos'][$f]['ID'] = $datosExel['resultadoFilas'][$f]['ID'];
				
				if (isset($datosExel['resultadoFilas'][$f]['ESTADO']) && $datosExel['resultadoFilas'][$f]['ESTADO']=='BAJA')
				{$reDatosExel['valoresCampos'][$f]['ESTADO']='baja';	
				}
				else
				{$reDatosExel['valoresCampos'][$f]['ESTADO']='alta';	
				}		
										
				switch ($datosExel['resultadoFilas'][$f]['CODCUOTA']) 
				{case '1': $codCuota='General'; break;
	    case 'P': $codCuota='Parado';  break;
	    case 'J': $codCuota='Joven';   break;												
    }
				$reDatosExel['valoresCampos'][$f]['CODCUOTA'] = $codCuota;	
    
    switch ($datosExel['resultadoFilas'][$f]['CODAGRUPACION'])	
			 {case 'CA':	$codAgrup='00111000';  break;
					case 'CO':	$codAgrup='00114000';  break;	
					case 'GR':	$codAgrup='00118000';  break;	
					case 'JA':	$codAgrup='00123000';  break;	
					case 'MA':	$codAgrup='00129000';  break;													
				 case 'SE':	$codAgrup='00141000';  break;
					case 'HU':	$codAgrup='00100000';  break;//Andalucía	       
					case 'AL':	$codAgrup='00100000';  break;//Andalucía
					
	    case 'ES':	$codAgrup='00000000';  break;//Estatal  //se podría elegir en funcion de CP con select    
    }	//switch ($datosExel['resultadoFilas']
				$reDatosExel['valoresCampos'][$f]['CODAGRUPACION'] =$codAgrup;		
								
				$reDatosExel['valoresCampos'][$f]['TIPOMIEMBRO'] = 'socio';										
				
				$reDatosExel['valoresCampos'][$f]['CODPAISDOC'] = $datosExel['resultadoFilas'][$f]['CODPAISDOC'];		
				$reDatosExel['valoresCampos'][$f]['NUMDOCUMENTOMIEMBRO'] =$datosExel['resultadoFilas'][$f]['NUMDOCUMENTOMIEMBRO'];
			 $reDatosExel['valoresCampos'][$f]['TIPODOCUMENTOMIEMBRO'] =$datosExel['resultadoFilas'][$f]['TIPODOCUMENTOMIEMBRO'];
				$reDatosExel['valoresCampos'][$f]['APE1'] =addslashes(trim($datosExel['resultadoFilas'][$f]['APE1']));
				$reDatosExel['valoresCampos'][$f]['APE2'] =addslashes(trim($datosExel['resultadoFilas'][$f]['APE2']));
				$reDatosExel['valoresCampos'][$f]['NOM'] =addslashes(trim($datosExel['resultadoFilas'][$f]['NOM']));
    $reDatosExel['valoresCampos'][$f]['SEXO'] =$datosExel['resultadoFilas'][$f]['SEXO'];							
				//$reDatosExel['valoresCampos'][$f]['FECHANAC'] =$datosExel['resultadoFilas'][$f]['FECHANACIMIENTO'];//no tiene dato
				$reDatosExel['valoresCampos'][$f]['TELFIJOCASA '] =soloNumeros($datosExel['resultadoFilas'][$f]['TELFIJOCASA']);
				$reDatosExel['valoresCampos'][$f]['TELMOVIL'] =soloNumeros($datosExel['resultadoFilas'][$f]['TELMOVIL']);
				$reDatosExel['valoresCampos'][$f]['EMAIL'] =addslashes(trim($datosExel['resultadoFilas'][$f]['EMAIL']));							
				$reDatosExel['valoresCampos'][$f]['PROFESION'] =addslashes(trim($datosExel['resultadoFilas'][$f]['PROFESION']));
				$reDatosExel['valoresCampos'][$f]['DIRECCION'] =addslashes(trim($datosExel['resultadoFilas'][$f]['DIRECCION']));							
				$reDatosExel['valoresCampos'][$f]['LOCALIDAD'] =addslashes(trim($datosExel['resultadoFilas'][$f]['LOCALIDAD']));
				$reDatosExel['valoresCampos'][$f]['CP'] =trim($datosExel['resultadoFilas'][$f]['CP']);
				$reDatosExel['valoresCampos'][$f]['CODPAISDOM'] =trim($datosExel['resultadoFilas'][$f]['CODPAISDOM']);		
    $reDatosExel['valoresCampos'][$f]['OBSERVACIONES'] = addslashes(trim($datosExel['resultadoFilas'][$f]['OBSERVACIONES']));

    $reDatosExel['valoresCampos'][$f]['FECHAALTA'] =$datosExel['resultadoFilas'][$f]['FECHAALTA'];	
    $reDatosExel['valoresCampos'][$f]['FECHABAJA'] =$datosExel['resultadoFilas'][$f]['FECHABAJA'];									
				
				$reDatosExel['valoresCampos'][$f]['CODENTIDAD'] =$datosExel['resultadoFilas'][$f]['CODENTIDAD'];
				$reDatosExel['valoresCampos'][$f]['CODSUCURSAL'] =$datosExel['resultadoFilas'][$f]['CODSUCURSAL'];
				$reDatosExel['valoresCampos'][$f]['DC'] =$datosExel['resultadoFilas'][$f]['DC'];
				$reDatosExel['valoresCampos'][$f]['NUMCUENTA'] =$datosExel['resultadoFilas'][$f]['NUMCUENTA'];				
				$reDatosExel['valoresCampos'][$f]['MODOINGRESO'] =$datosExel['resultadoFilas'][$f]['MODOINGRESO'];
				 
    $reDatosExel['valoresCampos'][$f]['IMPORTECUOTAANIOSOCIO'] =$datosExel['resultadoFilas'][$f]['IMPORTECUOTAANIOSOCIO'];								
				$reDatosExel['valoresCampos'][$f]['CUOTA2011'] =$datosExel['resultadoFilas'][$f]['CUOTA2011'];	
    $reDatosExel['valoresCampos'][$f]['FECHAPAGO2011'] =$datosExel['resultadoFilas'][$f]['FECHAPAGO2011'];														
				$reDatosExel['valoresCampos'][$f]['CUOTA2010'] =$datosExel['resultadoFilas'][$f]['CUOTA2010'];	
    $reDatosExel['valoresCampos'][$f]['FECHAPAGO2010'] =$datosExel['resultadoFilas'][$f]['FECHAPAGO2010'];											
				$reDatosExel['valoresCampos'][$f]['CUOTA2009'] =$datosExel['resultadoFilas'][$f]['CUOTA2009'];	
    $reDatosExel['valoresCampos'][$f]['FECHAPAGO2009'] =$datosExel['resultadoFilas'][$f]['FECHAPAGO2009'];							

		  $f++;
	  }//while ($f < $fUltima)
			//echo "<br><br>1 modeloAdmin:importarExcelSociosAN:$reDatosExel: ";print_r($reDatosExel);
			
		 $reDatosExelUsuario['numFilas'] = $datosExel['numFilas'];	
			//$reDatosExelUsuario['numFilas'] = 1;	
			$reDatosExelUsuario['resultadoFilas'] = $reDatosExel['valoresCampos'];
			
   echo "<br><br>2 modeloAdmin:reDatosExelUsuario: ";print_r($reDatosExelUsuario);			
			$resulInserUsuario = insertarVariasFilas('EXCELTODOS',$reDatosExelUsuario,$conexionUsuariosDB['conexionLink']);
			//$resulInserUsuario = insertarVariasFilas('EXCELTODOS2',$reDatosExelUsuario,$conexionUsuariosDB['conexionLink']);
			
		} //$datosExel['codError']=='00000'
 	echo "<br><br>3 modeloAdmin:resulInserUsuario: ";print_r($resulInserUsuario);
		
		if ($resulInserUsuario['codError']!=='00000')//puede ser <80000 o 80001
		{ $arrMensaje['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
		  
				require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
				$resInsertarErrores = insertarError($reDatosExelUsuario);			
				if ($resInsertarErrores['codError']!=='00000')
		  {$reDatosExelUsuario['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'].='Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo';
				}
		}
		elseif ($resulInserUsuario['numFilas'] == 0)
	 { $reImportarExcelSocios['codError'] = '80001';//no se han insertado, no existe usuario ....
    $reImportarExcelSocios['errorMensaje'] = 'Aviso: no se ha insertado ningú usuario, posible error';  
	 }
		else // acaso tratar que se han insertado 0 filas
		{ $reImportarExcelSocios=$resulInserUsuario;			
		}
		
	}	//$conexionUsuariosDB['codError']=='00000'
 echo "<br><br>4 modeloAdmin:resulInserUsuario:reImportarExcelSocios: ";print_r($reImportarExcelSocios);	
 
 return 	$reImportarExcelSocios; 	
}
//------------------------------ Fin importarExcelSociosAN -----------------------

/*---------------------------- Inicio generarTodosOrd ----------------------------
Descripción: Busca todos los datos de EXCELTODOS, donde se han juntado las 
importaciones de las hojas excel de las distintas agrupaciones y los inserta
en todas las tablas de la aplicación: USUARIO, MIEMBRO, ...

USUARIO: nombre del socio (convertido a caracteres validos+un numero)
PASSNOENCRIPTADA: aleatoria (se guarda sin encriptar en EXCELTODOSCONFIRMAREMAIL, 
para anotar confirmaciones de email) y  en USUARIOS encriptada, para su uso normal

Llamada desde: cAdmin.php:generarTodosOrdenada()
Llama: varias funciones ....

OBSERVACIONES: solo se ejecuta una vez por el administrador de la aplicación, si 
               se ejecuta dos más veces se machará los datos. 
               Anular esta función después de ejecutarse, para evitar accidentes
															
NOTA: SE HAN HECHO CAMBIOS EN  EXCELTODOSCONFIRMAREMAIL 
Agustín :dejar por si sirviese para otras parecidas tareas.														
---------------------------------------------------------------------------------*/
function generarTodosOrd()//Fila a fila
{
 $reImportarExcelSocios['codError']='00000';
 $reImportarExcelSocios['errorMensaje']='';
 $reImportarExcelSocios['nomScript']='modeloAdmin.php';
 $$reImportarExcelSocios['nomFuncion']='buscarDatosExel';//se van a repetir
	$arrMensaje['textoCabecera']='Importar desde la BBDD Excel';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al importar de la BBDD Excel";		
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	require_once "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $reImportarExcelSocios=$conexionUsuariosDB;
	  $reImportarExcelSocios['errorMensaje']="Error del sistema al conectarse a la base de datos"; 	
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
 else //$conexionUsuariosDB['codError']=="00000"
	{
	 $iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 { $resIniTrans['codError'] = '70501';   
			 $resIniTrans['errno'] = mysql_errno(); 
	   $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. '.
				             'Error mysql_query '.mysql_error();
	   $resIniTrans['numFilas'] = 0;	
			
			 $resInsertar = $resIniTrans;	
			 //echo "<br><br>2 modeloSocios:altaSocios:resIniTrans: ";print_r($resIniTrans);
	 }	
		else //$resIniTrans
		{
			require_once './modelos/libs/buscarCodMax.php';
	  $resulBuscarCodMax = buscarCodMax('USUARIO','CODUSER',$conexionUsuariosDB['conexionLink']);
	  //------------------	
		 $tablasBusqueda = "EXCELTODOS";	
			
			$camposBuscados = 
			 " ID,ESTADO,
	
				NUMDOCUMENTOMIEMBRO,TIPODOCUMENTOMIEMBRO,CODPAISDOC,APE1,APE2,NOM,SEXO,	FECHANAC,	EMAIL,TELFIJOCASA,TELMOVIL,
				PROFESION,OBSERVACIONES,	
		
				DIRECCION,LOCALIDAD,CP,CODPAISDOM, 
				
				CODCUOTA,CODAGRUPACION, FECHAALTA,FECHABAJA, CODENTIDAD,CODSUCURSAL,DC,NUMCUENTA,CCEXTRANJERA,	MODOINGRESO,			
				
			 IMPORTECUOTAANIOSOCIO,CUOTA2011,FECHAPAGO2011, CUOTA2010,FECHAPAGO2010, CUOTA2009,FECHAPAGO2009,
				
				FLOOR(99 + (RAND(180) * 1000)) AS ALEATORIO ";//el randon para ordenar los que tienen la misma fecha	
				
			$cadCondicionesBuscar = " ORDER BY FECHAALTA, ALEATORIO ";
			//Genera el orden, que sirvirá para orden de socio, CODUSER según la FECHAALTA 
			//(cerca de la mitad tiene la misma fecha ficticia por lo que además 
			// se añade un orden ALEATORIO en caso de fechas iguales (muchas)
	
			$datosExel = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionUsuariosDB['conexionLink']); 
	  //echo "<br><br>1-0 modeloAdmin:datosExel:";	print_r($datosExel);	
			
			if ($datosExel['codError']!=='00000')
			{ $reImportarExcelSocios['codError']=$datosExel['codError'];
	    $reImportarExcelSocios['errorMensaje']=$datosExel['errorMensaje'];
			  $reImportarExcelSocios['nomScript']=$datosExel['nomScript'];//se van a repetir
			  $reImportarExcelSocios['nomFuncion']=$datosExel['nomFuncion'];//se van a repetir
			}	
			elseif ($datosExel['numFilas'] == 0)
			{$reImportarExcelSocios['codError'] = '80001'; //no encontrado
				$reImportarExcelSocios['errorMensaje'] = "Tabla BBDDEXCEL vacía";
			}	
		 else //$datosExel['codError']=='00000'
			{require_once './modelos/libs/eliminarAcentos.php';
	   require_once './modelos/libs/buscarCodMax.php';
				
		  $resulBuscarCodMaxUser=buscarCodMax('USUARIO','CODUSER',$conexionUsuariosDB['conexionLink']);
			 $codUser = $resulBuscarCodMaxUser['valorCampo']-1;	
						
	   $resulBuscarCodMaxSocio=buscarCodMax('SOCIO','CODSOCIO',$conexionUsuariosDB['conexionLink']);			
	   $codSocio = $resulBuscarCodMaxSocio['valorCampo']-1; 
				
			 if ($resulBuscarCodMaxUser['codError']!=='00000' || $resulBuscarCodMaxSocio['codError']!=='00000')
		 	{ $reImportarExcelSocios['codError'] = '70100';
				  $reImportarExcelSocios['errorMensaje'] .= "Error sistema al buscar códigos máximo CODUSER, o CODSOCIO";
			 }
			 else //$resulBuscarCodMaxUser['codError']=='00000'
			 {//mt_srand (100);//para generar la semilla aleatoria
				 mt_srand(100);//para generar la semilla mas aleatoria
				 $f = 0;
				 $fUltima=$datosExel['numFilas'];
							
		   while ($f < $fUltima)  //while ($f < 3)	//para pruebas		
				 {//---------------------- INICIO tabla USUARIO -----------------------------------------
				  $reDatosExel['valoresCampos']['datosUsuario']['CODUSER'] = ++$codUser;		
					 echo "<br /><br />codUser=".$codUser; 	
						
					 $numUs = $codUser + 125; //para formar el nombre de usuario más complejo incluyendo números
							
						$usuario=cambiarParaUsuarioPass($datosExel['resultadoFilas'][$f]['NOM']).$numUs;//en './modelos/libs/eliminarAcentos.php';
						if (strlen($usuario) > 30)
						{ $usuario = substr($usuario, -30);	
						}	
						$reDatosExel['valoresCampos']['datosUsuario']['USUARIO'] = $usuario;						
		    /*---------------- inicio   PASSENCRIPTADA ----------------------------------*/			
						$strParaPass = cambiarParaUsuarioPass($datosExel['resultadoFilas'][$f]['APE1']);//quita caracteres problemáticos
	
						$strParaPass = substr($strParaPass, floor(strlen($strParaPass)/3),
						                      floor(strlen($strParaPass)/3));
																												
	     $passNoEncriptada= mt_rand(10, 99).$strParaPass.mt_rand(1000, 9999);//pass no encriptada 
						$passEncriptada = sha1($passNoEncriptada);//pass encriptada 	
						
	     //PARA PASSWORD, podría ser todos a valor "0", inicialmente, nadie podrá entrar pues exige > 6 caracteres	
					 //$reDatosExel['valoresCampos']['datosConfimarEmail']['PASSNOENCRIPTADA'] = "0";, pero podría
						//entrar alguien que lea USUARIO	
												
					 /*---------------- fin    PASSENCRIPTADA ----------------------------------*/		
						if ($datosExel['resultadoFilas'][$f]['ESTADO']=='alta')
						{$estado ='alta-sin-password-excel';		//y sin cinfirmar email					
						}		
						else
						{$estado = 'baja';		
						}		 
					 $reDatosExel['valoresCampos']['datosUsuario']['CODUSER'] = $codUser;		 
					 $reDatosExel['valoresCampos']['datosUsuario']['PASSUSUARIO'] = $passEncriptada;										
				 	$reDatosExel['valoresCampos']['datosUsuario']['ESTADO'] = $estado;	
				 	$reDatosExel['valoresCampos']['datosUsuario']['OBSERVACIONES'] ='';							 
	    
					 echo "<br><br>2-0 modeloAdmin:reDatosExel['valoresCampos']['datosUsuario'][$f]: ";
					 print_r($reDatosExel['valoresCampos']['datosUsuario']);			
					
						$reInserFilaUsuario=insertarUnaFila('USUARIO',$reDatosExel['valoresCampos']['datosUsuario'],
						                                    $conexionUsuariosDB['conexionLink']);								
								
						//---------------------- FIN tabla USUARIO -----------------------------------------
						
						if ($reInserFilaUsuario['codError']!=='00000')
	 	   { $reImportarExcelSocios = $reInserFilaUsuario;
						  $reImportarExcelSocios['errorMensaje'].=$reInserFilaUsuario['errorMensaje'];
		    }
		    else//---------------------- INICIO USUARIOTIENEROL  -----------------------------------	
		    {$reInserFilaUsuarioTieneRol['codError'] ='00000';
						 
							if ($estado == 'alta')
						 {$reDatosExel['valoresCampos']['datosUsuarioRol']['CODUSER'] = $codUser;		 
								$reDatosExel['valoresCampos']['datosUsuarioRol']['CODROL'] = '1';		
								 	
								$reInserFilaUsuarioTieneRol=insertarUnaFila('USUARIOTIENEROL',$reDatosExel['valoresCampos']['datosUsuarioRol'],
								   $conexionUsuariosDB['conexionLink']);	
							//---------------------- FIN USUARIOTIENEROL  -----------------------------------------
							}
							if ($reInserFilaUsuarioTieneRol['codError']!=='00000')
		 	   { $reImportarExcelSocios = $reInserFilaUsuarioTieneRol;
							  $reImportarExcelSocios['errorMensaje'].=$reInserFilaUsuarioTieneRol['errorMensaje'];
			    }
			    else	//---------------------- INICIO MIEMBRO  -----------------------------------------	
			    {//-------------------- inicio COMUNES MIEMBRO alta y baja ---------------													
	       $reDatosExel['valoresCampos']['datosMiembro']['CODUSER'] =$codUser;		
								$reDatosExel['valoresCampos']['datosMiembro']['TIPOMIEMBRO'] ='socio';
								$reDatosExel['valoresCampos']['datosMiembro']['TIPODOCUMENTOMIEMBRO'] = 
								                                   $datosExel['resultadoFilas'][$f]['TIPODOCUMENTOMIEMBRO'];	
								$reDatosExel['valoresCampos']['datosMiembro']['CODPAISDOC'] = $datosExel['resultadoFilas'][$f]['CODPAISDOC'];	
							 $reDatosExel['valoresCampos']['datosMiembro']['SEXO'] = $datosExel['resultadoFilas'][$f]['SEXO'];						
						 	$reDatosExel['valoresCampos']['datosMiembro']['FECHANAC'] = $datosExel['resultadoFilas'][$f]['FECHANAC'];
								$reDatosExel['valoresCampos']['datosMiembro']['PROFESION'] = addslashes($datosExel['resultadoFilas'][$f]['PROFESION']);	
								$reDatosExel['valoresCampos']['datosMiembro']['INFORMACIONEMAIL'] = 'SI';	
								$reDatosExel['valoresCampos']['datosMiembro']['INFORMACIONCARTAS'] = 'SI';	
								$reDatosExel['valoresCampos']['datosMiembro']['CODPAISDOM'] = $datosExel['resultadoFilas'][$f]['CODPAISDOM'];	
								$reDatosExel['valoresCampos']['datosMiembro']['CP'] = $datosExel['resultadoFilas'][$f]['CP'];
								
								if ($datosExel['resultadoFilas'][$f]['CODPAISDOM']=='ES' && 
								    isset($datosExel['resultadoFilas'][$f]['CP']) && !empty($datosExel['resultadoFilas'][$f]['CP']))
								{$reDatosExel['valoresCampos']['datosMiembro']['CODPROV']=substr($datosExel['resultadoFilas'][$f]['CP'],0,2);
								}
								else
								{//$reDatosExel['valoresCampos']['datosMiembro']['CODPROV'] = NULL;// da problemas porque es un int y tiene FK		
								  unset($reDatosExel['valoresCampos']['datosMiembro']['CODPROV']);					
								}	
								$reDatosExel['valoresCampos']['datosMiembro']['LOCALIDAD'] = addslashes($datosExel['resultadoFilas'][$f]['LOCALIDAD']);	
	       $reDatosExel['valoresCampos']['datosMiembro']['OBSERVACIONES'] =addslashes($datosExel['resultadoFilas'][$f]['OBSERVACIONES']);  													
							 
								//-------------------- fin  COMUNES MIEMBRO alta y baja ---------------							
							 if ($estado == 'alta')
						  {//--------------------NO COMUNES MIEMBRO solo alta ---------------						
									//acaso validar NUMDOCUMENTOMIEMBRO
									if (!isset($datosExel['resultadoFilas'][$f]['NUMDOCUMENTOMIEMBRO']) ||
									     empty($datosExel['resultadoFilas'][$f]['NUMDOCUMENTOMIEMBRO']))
									{$reDatosExel['valoresCampos']['datosMiembro']['NUMDOCUMENTOMIEMBRO'] ='falta'.$codUser;//CUANDO FALTA Nº DOC
											  $reDatosExel['valoresCampos']['datosMiembro']['TIPODOCUMENTOMIEMBRO'] = 'OTROS';
									}
									else
									{$reDatosExel['valoresCampos']['datosMiembro']['NUMDOCUMENTOMIEMBRO'] =
									  addslashes($datosExel['resultadoFilas'][$f]['NUMDOCUMENTOMIEMBRO']);
											$reDatosExel['valoresCampos']['datosMiembro']['TIPODOCUMENTOMIEMBRO'] = 
									     $datosExel['resultadoFilas'][$f]['TIPODOCUMENTOMIEMBRO'];	
									}
		     
									$reDatosExel['valoresCampos']['datosMiembro']['APE1'] = addslashes($datosExel['resultadoFilas'][$f]['APE1']);	
									$reDatosExel['valoresCampos']['datosMiembro']['APE2'] = addslashes($datosExel['resultadoFilas'][$f]['APE2']);	
									$reDatosExel['valoresCampos']['datosMiembro']['NOM'] = addslashes($datosExel['resultadoFilas'][$f]['NOM']);	
									$reDatosExel['valoresCampos']['datosMiembro']['TELFIJOCASA'] = $datosExel['resultadoFilas'][$f]['TELFIJOCASA'];	
									$reDatosExel['valoresCampos']['datosMiembro']['TELMOVIL'] = $datosExel['resultadoFilas'][$f]['TELMOVIL'];
									$reDatosExel['valoresCampos']['datosMiembro']['DIRECCION'] = addslashes($datosExel['resultadoFilas'][$f]['DIRECCION']);	
										
									if (!isset($datosExel['resultadoFilas'][$f]['EMAIL']) || empty($datosExel['resultadoFilas'][$f]['EMAIL']))
									{$reDatosExel['valoresCampos']['datosMiembro']['EMAIL'] ='falta'.$codUser.'@falta.com';//ES UN CAMPO OBLIGATORIO
									 $reDatosExel['valoresCampos']['datosMiembro']['EMAILERROR'] = 'FALTA';
										//$reDatosExel['valoresCampos']['datosConfimarEmail']['CONFIRMACIONEMAIL'] = 'FALTA';
									}
									else
									{ require_once './modelos/libs/validarCampos.php'; 
									  $resulValidarCampo = validarEmail($datosExel['resultadoFilas'][$f]['EMAIL']);
											if ($resulValidarCampo['codError']!=='00000')
											{ $reDatosExel['valoresCampos']['datosMiembro']['EMAILERROR'] = 'ERROR-FORMATO';		
											  //$reDatosExel['valoresCampos']['datosConfimarEmail']['CONFIRMACIONEMAIL'] = 'ERROR-FORMATO';										
											}
											else
											{ $reDatosExel['valoresCampos']['datosMiembro']['EMAILERROR'] = 'NO';
											  //$reDatosExel['valoresCampos']['datosConfimarEmail']['CONFIRMACIONEMAIL'] = 'NO';										
											}
											$reDatosExel['valoresCampos']['datosMiembro']['EMAIL'] = $resulValidarCampo['valorCampo'];
											//$reDatosExel['valoresCampos']['datosMiembro']['EMAIL'] = validarEmail($datosExel['resultadoFilas'][$f]['EMAIL'];
									}								
							 //--------------------NO COMUNES MIEMBRO solo alta---------------		
								}	//if ($estado == 'alta')
								echo "<br><br>2-2-bis modeloAdmin:reDatosExel['valoresCampos']['datosMiembro']: ";
									print_r($reDatosExel['valoresCampos']['datosMiembro']);														
							 $reInserFilaMiembro=insertarUnaFila('MIEMBRO',$reDatosExel['valoresCampos']['datosMiembro'],
								   $conexionUsuariosDB['conexionLink']);	
							 //---------------------- FIN MIEMBRO  -----------------------------------------		
											
							 if ($reInserFilaMiembro['codError']!=='00000')
		 	    { $reImportarExcelSocios = $reInserFilaMiembro;
							  $reImportarExcelSocios['errorMensaje'].=$reInserFilaMiembro['errorMensaje'];
			     }
								else
								{//---------------------- INICIO CONFIRMAREMAILALTAGESTOR  ----------------------------------
									
							  $reDatosExel['valoresCampos']['datosConfimarEmail']['CODUSER'] = $codUser;		 
		       //$reDatosExel['valoresCampos']['datosConfimarEmail']['FECHAENVIOEMAILULTIMO']= date('Y-m-d'); 		
									$reDatosExel['valoresCampos']['datosConfimarEmail']['FECHAENVIOEMAILULTIMO']='0000-00-00';		
									$reDatosExel['valoresCampos']['datosConfimarEmail']['FECHARESPUESTAEMAIL']='0000-00-00';
									$reDatosExel['valoresCampos']['datosConfimarEmail']['NUMENVIOS']=0;
									
									$reInserFilaConfimarEmail=insertarUnaFila('CONFIRMAREMAILALTAGESTOR',$reDatosExel['valoresCampos']['datosConfimarEmail'],
									  $conexionUsuariosDB['conexionLink']);//Esta será la que deba quedar									
									//$reInserFilaConfimarEmail=insertarUnaFila('EXCELTODOSCONFIRMAREMAIL',$reDatosExel['valoresCampos']['datosConfimarEmail'],
									  //$conexionUsuariosDB['conexionLink']);//antigua								
						     
									//echo "<br><br>2-0-0 modeloAdmin:	reInserFilaConfimarEmail:";print_r($reInserFilaConfimarEmail);			
									//---------------------- FIN CONFIRMAREMAILALTAGESTOR -----------------------------
									if ($reInserFilaConfimarEmail['codError']!=='00000')
									{ $reImportarExcelSocios = $reInserFilaConfimarEmail;
									  $reImportarExcelSocios['errorMensaje'].=$reInserFilaConfimarEmail['errorMensaje'];
					    }						
					    else//---------------------- INICIO SOCIO  -----------------------------------------			 
					    {$reDatosExel['valoresCampos']['datosSocio']['CODSOCIO'] = ++$codSocio;
										$reDatosExel['valoresCampos']['datosSocio']['CODUSER'] = $codUser;		
										$reDatosExel['valoresCampos']['datosSocio']['CODCUOTA'] = $datosExel['resultadoFilas'][$f]['CODCUOTA'];											
										$reDatosExel['valoresCampos']['datosSocio']['IMPORTECUOTAANIOSOCIO'] = $datosExel['resultadoFilas'][$f]['IMPORTECUOTAANIOSOCIO'];										
										$reDatosExel['valoresCampos']['datosSocio']['CODAGRUPACION'] = $datosExel['resultadoFilas'][$f]['CODAGRUPACION'];	
										$reDatosExel['valoresCampos']['datosSocio']['FECHAALTA'] = $datosExel['resultadoFilas'][$f]['FECHAALTA'];		
								  $reDatosExel['valoresCampos']['datosSocio']['MODOINGRESO'] = $datosExel['resultadoFilas'][$f]['MODOINGRESO'];

									 if ($estado == 'alta')
								  {$reDatosExel['valoresCampos']['datosSocio']['FECHABAJA'] = '0000-00-00'; 
											$reDatosExel['valoresCampos']['datosSocio']['CODENTIDAD'] = $datosExel['resultadoFilas'][$f]['CODENTIDAD'];	
											$reDatosExel['valoresCampos']['datosSocio']['CODSUCURSAL'] = $datosExel['resultadoFilas'][$f]['CODSUCURSAL'];	
											$reDatosExel['valoresCampos']['datosSocio']['DC'] = $datosExel['resultadoFilas'][$f]['DC'];	
											$reDatosExel['valoresCampos']['datosSocio']['NUMCUENTA'] = $datosExel['resultadoFilas'][$f]['NUMCUENTA'];	
											$reDatosExel['valoresCampos']['datosSocio']['CCEXTRANJERA'] = $datosExel['resultadoFilas'][$f]['CCEXTRANJERA'];										
										}
										else
										{$reDatosExel['valoresCampos']['datosSocio']['FECHABAJA'] = $datosExel['resultadoFilas'][$f]['FECHABAJA'];								
										}
										//echo "<br><br>2-3 modeloAdmin:reDatosExel['valoresCampos']['datosSocio'][$f]:SOCIO: $codSocio: ";
										//print_r($reDatosExel['valoresCampos']['datosSocio']);								
										$reInserFilaSocio=insertarUnaFila('SOCIO',$reDatosExel['valoresCampos']['datosSocio'],$conexionUsuariosDB['conexionLink']);	
										//---------------------- FIN SOCIO  -----------------------------------------			
													
										if ($reInserFilaSocio['codError']!=='00000')
					 	   { $reImportarExcelSocios = $reInserFilaSocio;
										  $reImportarExcelSocios['errorMensaje'].=$reInserFilaSocio['errorMensaje'];
						    }
						    else //------- INICIO CUOTAANIOSOCIO  se guardan todos los datos aunque sea baja -------			
						    {$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['CODSOCIO'] = $codSocio;													
											$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['CODCUOTA'] = $datosExel['resultadoFilas'][$f]['CODCUOTA'];
											$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['MODOINGRESO'] = $datosExel['resultadoFilas'][$f]['MODOINGRESO'];
											$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOEL'] = 30;//general
											$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOSOCIO'] = 
												 $datosExel['resultadoFilas'][$f]['IMPORTECUOTAANIOSOCIO'];	//cuota elegida por el socio										
															
					      $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['CODAGRUPACION']=
											  $datosExel['resultadoFilas'][$f]['CODAGRUPACION'];
													
											//---------------------- INICIO CUOTA 2009 -----------------------------------------
											//if ($datosExel['resultadoFilas'][$f]['FECHAALTA'] >= '2009-12-31')//No estaba dado de alta	en 2009	
											//if (substr($datosExel['resultadoFilas'][$f]['FECHAALTA'],0,4) > 2009)//No estaba dado de alta	en 2009
											if ($datosExel['resultadoFilas'][$f]['CUOTA2009'] == -1)//-1 No estaba dado de alta	en 2009			
											{ $reInserFilaCuotaAnio['codError'] ='00000';
											}
											else //$datosExel['resultadoFilas'][$f]['CUOTA2009'] !== -1
											{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ANIOCUOTA']='2009';					
												$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOPAGADA']=
												  $datosExel['resultadoFilas'][$f]['CUOTA2009'];
																			
												if($datosExel['resultadoFilas'][$f]['CODCUOTA']=='General')//'000000'
												{//if ($datosExel['resultadoFilas'][$f]['CUOTA2009']== 30)//>=30	
												 if ($datosExel['resultadoFilas'][$f]['CUOTA2009'] >= 30)										
													{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']=$datosExel['resultadoFilas'][$f]['FECHAPAGO2009'];
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2009-12-31'; 
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='ABONADA';
												 }
												 else
												 {$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='0000-00-00'; 
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2009-12-31'; 
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='NOABONADA';													
													}													
												}
												else //Joven, Parado, Honorario: EXENTO
												{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='2009-12-30'; 
												 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2009-12-31'; 
												 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='EXENTO';
													$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOEL'] = 0;
											 }		
												//echo "<br><br>2-4 modeloAdmin:reDatosExel['valoresCampos']['datosCuotaAnioSocio'][$f]: ";
											 //print_r($reDatosExel['valoresCampos']['datosCuotaAnioSocio']);				
											
										 	$reInserFilaCuotaAnio=insertarUnaFila('CUOTAANIOSOCIO',$reDatosExel['valoresCampos']['datosCuotaAnioSocio'],
											   $conexionUsuariosDB['conexionLink']);
											}//$datosExel['resultadoFilas'][$f]['CUOTA2009'] !== -1 //fin couta 2009
					      //---------------------- Fin CUOTA 2009 -----------------------------------------	
											
											if ($reInserFilaCuotaAnio['codError']!=='00000')
						 	   { $reImportarExcelSocios = $reInserFilaCuotaAnio;
										  	$reImportarExcelSocios['errorMensaje'].=$reInserFilaCuotaAnio['errorMensaje'];
							    }
							    else //else $reInserFilaCuotaAnio['codError']=='00000'
											{//---------------------- INICIO CUOTA 2010 -----------------------------------------
												if ($datosExel['resultadoFilas'][$f]['CUOTA2010'] == -1)//-1 No estaba dado de alta	en 2010			
												{ $reInserFilaCuotaAnio['codError'] ='00000';//no se inserta fila
												}
												else //$datosExel['resultadoFilas'][$f]['CUOTA2010'] !== -1
												{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ANIOCUOTA']='2010';					
													$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOPAGADA']=
													  $datosExel['resultadoFilas'][$f]['CUOTA2010'];							
																				
													if($datosExel['resultadoFilas'][$f]['CODCUOTA']=='General')//'000000'
													{if ($datosExel['resultadoFilas'][$f]['CUOTA2010'] >= 30)
														{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']=$datosExel['resultadoFilas'][$f]['FECHAPAGO2010'];
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2010-12-31'; 
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='ABONADA';
													 }
													 else
													 {$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='0000-00-00'; 
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2010-12-31'; 
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='NOABONADA';
														}	
													}
													else //Joven, Parado, Honorario: EXENTO
													{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='2010-12-30'; 
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']='2010-12-31'; 
													 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='EXENTO';
	             $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOEL'] = 0;													
												 }		
													//echo "<br><br>2-5 modeloAdmin:reDatosExel['valoresCampos']['datosCuotaAnioSocio'][$f]: ";
												 //print_r($reDatosExel['valoresCampos']['datosCuotaAnioSocio']);				
												
											 	$reInserFilaCuotaAnio=insertarUnaFila('CUOTAANIOSOCIO',$reDatosExel['valoresCampos']['datosCuotaAnioSocio'],
												   $conexionUsuariosDB['conexionLink']);
												}//$datosExel['resultadoFilas'][$f]['CUOTA2010'] !== -1
											}		//else $reInserFilaCuotaAnio['codError']=='00000'//Fin couta 2010
						      //---------------------- Fin CUOTA 2010 -----------------------------------------													
											if ($reInserFilaCuotaAnio['codError']!=='00000')
							 	  { $reImportarExcelSocios = $reInserFilaCuotaAnio;
												  $reImportarExcelSocios['errorMensaje'].=$reInserFilaCuotaAnio['errorMensaje'];
								   }
								   else //---------------------- INICIO CUOTA 2011 ---------------------------------
								   {
												 if ($datosExel['resultadoFilas'][$f]['CUOTA2011'] == -1)//-1 Esta dado de de baja en 2011			
													{ $reInserFilaCuotaAnio['codError'] ='00000'; //NO SE INSERTA FILA
													}
													else //$datosExel['resultadoFilas'][$f]['CUOTA2011'] !== -1
													{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ANIOCUOTA']='2011';					
														$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOPAGADA']=
														  $datosExel['resultadoFilas'][$f]['CUOTA2011'];							
																					
														if($datosExel['resultadoFilas'][$f]['CODCUOTA']=='General')//'000000'
														{if ($datosExel['resultadoFilas'][$f]['CUOTA2011'] >= 30)
															{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']=
															   $datosExel['resultadoFilas'][$f]['FECHAPAGO2011']; 
															 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']=
																  $datosExel['resultadoFilas'][$f]['FECHAPAGO2011']+1; 
															 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='ABONADA';
														 }
														 else
														 {$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='0000-00-00'; 
															 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']=date('Y-m-d'); 
															 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='PENDIENTE-COBRO';//NOABONADA';								
															}	
														}
														else //Joven, Parado, Honorario: EXENTO
														{$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAPAGO']='2011-06-30'; 
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['FECHAANOTACION']=date('Y-m-d'); 
														 $reDatosExel['valoresCampos']['datosCuotaAnioSocio']['ESTADOCUOTA']='EXENTO';
															$reDatosExel['valoresCampos']['datosCuotaAnioSocio']['IMPORTECUOTAANIOEL'] = 0;
													 }		
														//echo "<br><br>2-5-1 modeloAdmin:reDatosExel['valoresCampos']['datosCuotaAnioSocio'][$f]: ";
													 //print_r($reDatosExel['valoresCampos']['datosCuotaAnioSocio']);				
													
												 	$reInserFilaCuotaAnio=insertarUnaFila('CUOTAANIOSOCIO',$reDatosExel['valoresCampos']['datosCuotaAnioSocio'],
													   $conexionUsuariosDB['conexionLink']);
	            }//$datosExel['resultadoFilas'][$f]['CUOTA2011'] !== -1
													if ($reInserFilaCuotaAnio['codError']!=='00000')
								 	   { $reImportarExcelSocios = $reInserFilaCuotaAnio;
													  $reImportarExcelSocios['errorMensaje'].=$reInserFilaCuotaAnio['errorMensaje'];
									    }
									    else //$reInserFilaCuotaAnio['codError']=='00000'
									    {//---------ELIMINADOS5ANIOS si es baja se anontas los datos necesarios -----
													 $reInserEliminados5Anios['codError'] ='00000';												
													 
														if ($estado == 'baja')
													 {$arrInsEliminado5Anios['CODUSER'] = $codUser;																
								       $arrInsEliminado5Anios['TIPOMIEMBRO']='socio';
															$arrInsEliminado5Anios['CODPAISDOC'] = $datosExel['resultadoFilas'][$f]['CODPAISDOC'];
															$arrInsEliminado5Anios['TIPODOCUMENTOMIEMBRO']= $datosExel['resultadoFilas'][$f]['TIPODOCUMENTOMIEMBRO'];						
															$arrInsEliminado5Anios['NUMDOCUMENTOMIEMBRO']=$datosExel['resultadoFilas'][$f]['NUMDOCUMENTOMIEMBRO'];
															$arrInsEliminado5Anios['FECHABAJA']=$datosExel['resultadoFilas'][$f]['FECHABAJA'];
															$arrInsEliminado5Anios['NOM'] = $datosExel['resultadoFilas'][$f]['NOM'];
															$arrInsEliminado5Anios['APE1']= $datosExel['resultadoFilas'][$f]['APE1'];
															$arrInsEliminado5Anios['APE2']= $datosExel['resultadoFilas'][$f]['APE2'];		
															
									      //echo "<br><br>2-5-2 modeloAdmin::arrInsEliminado5Anios";print_r($arrInsEliminado5Anios);
															require_once './modelos/modeloUsuarios.php';
													  $resInsertarEliminado5Anios = insertarMiembroEliminado5Anios($arrInsEliminado5Anios);	
												   //echo "<br><br>2-5-3 modeloAdmin:resInsertarEliminado5Anio:s";print_r($resInsertarEliminado5Anios);
														}//---------ELIMINADOS5ANIOS -------------------------------------------------
														if ($reInserEliminados5Anios['codError']!=='00000')
									 	   { $reImportarExcelSocios = $reInserEliminados5Anios;
														  $reImportarExcelSocios['errorMensaje'].=$reInserEliminados5Anios['errorMensaje'];
										    }
										    else //$reInserEliminados5Anios['codError']=='00000'
										    {//---------
												   echo "<br><br>2-6 modeloAdmin:generarTodosOrd: SIN ERRORES ";
														 $finTrans = "COMMIT";
												   $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
															
													  if (!$resFinTrans)
												   {$resFinTrans['codError'] = '70502';   
														  $resFinTrans['errno'] = mysql_errno(); 
												    $resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación. '.
															             'Error mysql_query '.mysql_error();
												    $resFinTrans['numFilas'] = 0;	
														
														  //$resInsertar=$resFinTrans;	
																$reImportarExcelSocios = $resFinTrans;	
														  //echo "<br><br>2-7 modeloAdmin:generarTodosOrd:reImportarExcelSocios: ";print_r($reImportarExcelSocios);
												   }
													  else 
													  {$reImportarExcelSocios['codError'] = '00000';
															  $arrMensaje['textoCabecera']='Generar todas las tablas a partir de EXCETODOS';
														  $arrMensaje['textoComentarios'] = "COMMIT Se ha efecuado sin errores <br /><br />";	
															}	
											   //----------	
														}	//$reInserEliminados5Anios['codError']=='00000'			  
													}	//$reInserFilaCuotaAnio['codError']=='00000'				       
						      }	//Fin CUOTA 2011
				      }//INICIO CUOTAANIOSOCIO 	
								 }//INICIO SOCIO		
								}//INICIO EXCELTODOSCONFIRMAREMAIL					
						 }//INICIO MIEMBRO
						}//INICIO USUARIOTIENEROL									
	
					 ++$f;
			  }//while ($f < $fUltima) //inicio tabla USUARIO
				}//$resulBuscarCodMaxUser['codError']=='00000'
				/*	$reDatosExelSocio['numFilas']= $datosExel['numFilas'];				
			 $reDatosExelSocio['resultadoFilas'] = $reDatosExel['valoresCampos']['datosSocio'];				
	   echo "<br><br>2-3 modeloAdmin:reDatosExelSocio: ";print_r($reDatosExelSocio);						
				$reDatosExelSocio = insertarVariasFilas('SOCIO2',$reDatosExelSocio,$conexionUsuariosDB['conexionLink']);*/
			} //$datosExel['codError']=='00000'
		}//$resIniTrans		
 }	
		
	if ($reImportarExcelSocios['codError']!=='00000')//puede ser <80000 o 80001
	{ $arrMensaje['textoComentarios']="Error del sistema al importar desde ExcelTodos";		
	  
			require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
			$resInsertarErrores=insertarError($reImportarExcelSocios);			
			if ($resInsertarErrores['codError']!=='00000')
	  {//$reImportarExcelSocios['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].='Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo';
			}
	}
	else
	{ echo "<br><br>2-7 modeloAdmin:generarTodosOrd:Se han importado '$f' socios ";
	}

	echo "<br><br>2-8 modeloAdmin:generarTodosOrd:reImportarExcelSocios: ";print_r($reImportarExcelSocios);	

 return 	$reImportarExcelSocios; 	
	
}
//------------------------------ Fin generarTodosOrd -----------------------------

/********* FIN PROCESOS IMPORTAR LOS ANTIGUOS DATOS DE SOCIOS DE EL DESDE EXCEL  ******************/
/**************************************************************************************************/



/**************** FIN ALMACÉN ANTIGUAS FUNCIONES AHORA NO UTILIZADAS ******************************
***************************************************************************************************
***************************************************************************************************/
?>