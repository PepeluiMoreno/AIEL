<?php
/*----------------------------- mPaginarLib.php --------------------------------
FICHERO: mPaginarLib.php
VERSION: PHP 5.6.4

Devuelve el resultado de una consulta sql por páginas, así como los enlaces de 
navegación respectivos. Graba errores.

RECIBE: Todas las variables que se describen a continuación vienen de 
cCoordinador:mostrarSociosCoord(), cPresidente:mostrarSociosPres(),
cTesorero:mostrarIngresosCuotas(), ... y las más importantes "$_pag_sql" y 
$arrBindValues se obtiene en includes cPresCoordSociosApeNomPaginarInc.php, 
cTesoreroCuotasSociosApeNomPaginarInc.php, ...

DEVUELVE: un array "$resSqlPaginar" con [resultadoFilas] => Array ( [0]...
con los datos de la select, los campos  _pag_info]=>Del 1 al 1 de un total de 1
[_pag_navegacion]=>1, la página actual dentro de la lista y campos de errores

LLAMADA: cCoordinador.php:mostrarSociosCoord(),cPresidente.php:mostrarSociosPres(),
mostrarEstadoConfirmacionSocios(),mostrarSociosFallecidosPres()
cTesorero.php:mostrarIngresosCuotas(),mostrarDonaciones()
LLAMA: conexionMySQL.php:conexionDB() que incluye PDO y instance()
       modeloMySQL.php:buscarCadSql(),modeloErrores.php:insertarError()

OBSERVACIONES:
Autor: Agustín Villacorta.
Es una modificación para adaptarlo a MVC del script de Versión 1.6.3 de Jorge 
Pinedo y www.forosdelweb.com, ej:http://jpinedo.webcindario.com. Licencia:GPL

2020-04-13: incluyo $arrBindValues para PDO.
2020-04-05: incluyo en la función conexionDB()
La función buscarCadSql($_pag_sql,$conexionLinkdb) puede funcionar sin $arrBindValues
pero sería mejor incluirlas. Habría que hacerlo en cCoordinadorSociosApeNomPaginarInc.php
donde se llama a cadBuscarCuotasSociosApeNom(),cadBuscarCuotasSocios() que 
devuelven la cadena "$_pag_sql" con la select que se necesita aquí (debería 
venir también con $arrBindValues) 
------------------------------------------------------------------------------*/

/* Variables que se pueden añadir como parámetros en la llamada a la función 
			o bien definir antes de incluir el script vía include():
--------------------------------------------------------------------------------
$conexionLinkDB: es un parámetro con el link de recursos de la BBDD
$_pag_sql: 				OBLIGATORIA.	Cadena. Debe contener una sentencia sql válida (y sin la cláusula "limit"). 

$_pag_cuantos:	OPCIONAL.		Entero. Cantidad de registros que contendrá como máximo cada página.
								Por defecto está en 20.											
$_pag_nav_num_enlaces:	OPCIONAL.	Entero. Cantidad de enlaces a los números de página que se mostrarán como 
								máximo en la barra de navegación.Por defecto se muestran todos.											
$_pag_mostrar_errores:	OPCIONAL.	Booleano. Define si se muestran o no los errores de MySQL
	        que se puedan producir.	Por defecto está en "true";											
$_pag_propagar:	OPCIONAL.	Array de cadenas. Contiene los nombres de las variables que se quiere propagar
								por el url. 
								Por defecto se propagarán todas las que ya vengan por el url mediante (GET).
		Agus: yo utilizo GET y además he creado un parámetro "$_pag_propagar_opcion_buscar" para pasar valores
								desde en las llamadas href pasando "_opcion_buscar" para que propoguen el tipo de busquedas 
								que está realizando :por Ape, Agrupación, ...
$_pag_conteo_alternativo:	OPCIONAL.		Booleano. Define si se utiliza mysql_num_rows() (true) o 
         COUNT(*) (false) (puede dar errores cuando haya DISTINCT en la select).
								Por defecto yo le pongo en true, para que utilice "[numFilas] de la consulta 
$_pag_separador:	OPCIONAL.	Cadena. Cadena que separa los enlaces numéricos en la
	        barra de navegación entre páginas.Por defecto se utiliza la cadena " | ".
$_pag_nav_estilo:	OPCIONAL.	Cadena. Contiene el nombre del estilo CSS para los enlaces de paginación.
 								Por defecto no se especifica estilo.
$_pag_nav_anterior:	OPCIONAL.	Cadena. Contiene lo que debe ir en el enlace a la página anterior.
	        Puede ser un tag <img>.	Por defecto se utiliza la cadena "&laquo; Anterior".
$_pag_nav_siguiente:	OPCIONAL.	Cadena. Contiene lo que debe ir en el enlace a la página siguiente.
	        Puede ser un tag <img>. Por defecto se utiliza la cadena "Siguiente &raquo;"
$_pag_nav_primera:	OPCIONAL.	Cadena. Contiene lo que debe ir en el enlace a la primera página. 
	        Puede ser un tag <img>.Por defecto se utiliza la cadena "&laquo;&laquo; Primera".
$_pag_nav_ultima:	OPCIONAL.	Cadena. Contiene lo que debe ir en el enlace a la página siguiente. 
	        Puede ser un tag <img>.	Por defecto se utiliza la cadena "&Uacute;ltima &raquo;&raquo;"									
------------------------------------------------------------------------------*/
//function mPaginarLib($conexionLinkDB,$_pag_sql,$_pag_cuantos,$_pag_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pag_mostrar_errores,$arrBindValues = NULL)
function mPaginarLib($_pag_sql,$_pag_cuantos,$_pag_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pag_mostrar_errores,$conexionLinkDB = NULL,$arrBindValues = NULL)
//function mPaginarLib($conexionLink,$_pag_sql,$_pag_cuantos,$_pag_nav_num_enlaces,$_pag_propagar_opcion_buscar,$_pag_mostrar_errores,$_pag_conteo_alternativo)
{				
 //echo "<br><br>0-1 mPaginarLib:_pag_sql: ";print_r($_pag_sql);
	//echo "<br><br>0-2 mPaginarLib:_pag_cuantos: ";print_r($_pag_cuantos);	
	//echo "<br><br>0-3 mPaginarLib:_pag_nav_num_enlaces: ";print_r($_pag_nav_num_enlaces);	
 //echo "<br><br>0-4 mPaginarLib:_pag_propagar_opcion_buscar: ";print_r($_pag_propagar_opcion_buscar);	
 //echo "<br><br>0-5 mPaginarLib:arrBindValues: ";print_r($arrBindValues);				
			
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';	 
		
	$resSqlPaginar['nomScript'] = 'mPaginarLib.php';
	$resSqlPaginar['nomFuncion'] = 'mPaginarLib';
	$resSqlPaginar['codError'] = '00000';
	$resSqlPaginar['errorMensaje'] = '';	
	
 /*-----------------------------------------------------------------------------
 Verificación de los parámetros obligatorios y opcionales.
 -----------------------------------------------------------------------------*/				
	/*	if(empty($_pag_sql))
				{//Si no se definió $_pag_sql... grave error!
					//Este error se muestra sí o sí (ya que no es un error de mysql)
					die("<b>Error mPaginarLib : </b>No se ha definido la variable \$_pag_sql");
				}	Agus: Yo trato los errores por mi cuenta
	*/
	if(empty($_pag_cuantos))
	{//Si no se ha especificado la cantidad de registros por página
		//$_pag_cuantos será por defecto 7
		//$_pag_cuantos = 7;
	} 
	if(!isset($_pag_mostrar_errores))
	{// Si no se ha elegido si se mostrará o no errores
		// $_pag_errores será por defecto true. (se muestran los errores)
		$_pag_mostrar_errores = true;
	}
	if(!isset($_pag_conteo_alternativo))
	{// Si no se ha elegido el tipo de conteo
		// Se realiza el conteo desde mySQL con COUNT(*), puede dar errores cuando haya DISTINCT
		$_pag_conteo_alternativo = true; 
		// si estuviese $_pag_conteo_alternativo = true; evita el problema DISTINCT
		// se realizaría con las funciones de modeloMySQL mías, "[numFilas] de la consulta 
	} 
	if(!isset($_pag_separador))
	{//Si no se ha elegido separador, toma el separador por defecto $_pag_separador= " | ";
		//yo elijo mi propio separador: dos espacios
		$_pag_separador = "&nbsp;&nbsp;";
	}
	if(isset($_pag_nav_estilo))
	{// Si se ha definido un estilo para los enlaces, se genera el atributo "class" para el enlace
		$_pag_nav_estilo_mod = "class=\"$_pag_nav_estilo\"";
	}
	else
	{// Si no, se utiliza una cadena vacía.
		$_pag_nav_estilo_mod = "";
	} 
	if(!isset($_pag_nav_anterior))
	{// Si no se ha elegido una cadena para el enlace "siguiente"
		// Se toma la cadena por defecto.
		$_pag_nav_anterior = "&laquo; Anterior&nbsp;&nbsp;";
	} 
	if(!isset($_pag_nav_siguiente))
	{// Si no se ha elegido una cadena para el enlace "siguiente"
		// Se toma la cadena por defecto.
		$_pag_nav_siguiente = "&nbsp;&nbsp;Siguiente &raquo;";
	}
	if(!isset($_pag_nav_primera))
	{// Si no se ha elegido una cadena para el enlace "primera"
		// Se toma la cadena por defecto.
		$_pag_nav_primera = "Primera&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	if(!isset($_pag_nav_ultima))
	{// Si no se ha elegido una cadena para el enlace "siguiente"
		// Se toma la cadena por defecto.
		$_pag_nav_ultima = "&nbsp;&nbsp;&nbsp;&nbsp;&Uacute;ltima";
	} 
	
	//echo "<br><br>1-1 mPaginarLib:conexionDB:conexionLinkDB: ";var_dump($conexionLinkDB);	
 
 if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 		
	{ require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
		 $conexionLinkDB = $conexionDB['conexionLink']; 
			//echo "<br><br>1-2 mPaginarLib:conexionDB: ";var_dump($conexionDB);				
 }		
	else
	{ $conexionDB['codError'] = "00000";		 
   $conexionDB['conexionLink'] = $conexionLinkDB;
			//echo "<br><br>1-3 mPaginarLib:conexionDB:conexionDB: ";var_dump($conexionDB);	 
	}	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resSqlPaginar['codError'] = $conexionDB['codError'];
	  $resSqlPaginar['errorMensaje'] = $conexionDB['errorMensaje'];
	}
	else//$conexionDB['codError'] == "00000"
	{		
		//echo "<br><br>1-4 mPaginarLib:conexionLinkDB: ";var_dump($conexionLinkDB);		
		/*----------------------------------------------------------------------------
		Establecimiento de la página actual:
		Agus: Como puede saltar a mostrar, modificar, borrar, etc. se pierde _pag_pg
		y por ello he utilizado var sesiones, para guardar a que nº de la lista 
		tiene dónde tiene que volver.
		-----------------------------------------------------------------------------*/
		/*---------- esto es lo original  ------------------------------------
		if (empty($_GET['_pag_pg']))
		{//Si no se ha hecho click a ninguna página específica, o sea si es la 
			//primera vez que se ejecuta el script $_pag_actual será por defecto la primera.
			$_pag_actual = 1;
		}
		else //Si se "pidió" una página específica: $_pag_actual será la que se pidió.
		{$_pag_actual = $_GET['_pag_pg'];
		}
		---------------------------------------------------------------------*/
		
		/*------- Inicio Agustín: modificación $_SESSION['vs_pag_actual'] ------------
			Uso $_SESSION['vs_pag_actual'] para que vuelva al mismo nº. página despés
			de ir a mostrar, actualizar,	borrar, etc. 
			La otra alternativa es abrir una nueva ventana con	target=_blank y 
			javaScript windows.close para que cierre y quede abierta la anterior. 
			Pero en actualización es más complejo
			Nota: ver posibilidad de propagar la pág en lugar de usar session
			---------------------------------------------------------------------------*/		
		//echo "<br><br>1-0 mPaginarLib:_pag_actual: ";print_r($_pag_actual);
		
		if (empty($_GET['_pag_pg']))
		{/*--------------------------------------------------------------------------- 
				Si no se ha hecho click a ninguna página específica o sea si es la primera 
				vez que se ejecuta el script, $_pag_actual será por defecto la primera.		
				Si va a ver, actualizar, ...., tendrá guardado $_SESSION['vs_pag_actual']
				para volver a la misma pág cuando salga.
				Si existe $_SESSION['vs_pag_actual'] es que se vuelve desde un nivel superior
				a la misma pág. en que estaba antes $_pag_actual=$_SESSION['vs_pag_actual']	
			---------------------------------------------------------------------------*/			
				if (isset($_SESSION['vs_pag_actual']) && $_SESSION['vs_pag_actual']!==NULL && $_SESSION['vs_pag_actual']!=='')
				{
					$_pag_actual = $_SESSION['vs_pag_actual'];
     //echo "<br><br>1-1-1 mPaginarLib:_pag_actual: ";print_r($_pag_actual);	
				}
				else//Si no existe $_SESSION['vs_pag_actual']: pagina actual-->será la primera.
				{ 
					$_pag_actual = 1;
					//$_SESSION['vs_pag_actual']=	$_pag_actual;
     //echo "<br><br>1-1-2 mPaginarLib:_pag_actual: ";print_r($_pag_actual);						
				}
				//echo "<br><br>1-0 mPaginarLib:_pag_actual:";print_r($_pag_actual);
		}
		else//!empty($_GET['_pag_pg'])
		{//Si se "pidió" una pág.específica de la lista:$_pag_actual será la que se pidió.		
			$_pag_actual = $_GET['_pag_pg'];			
			//echo "<br><br>1-1-3 mPaginarLib:_pag_actual: ";print_r($_pag_actual);	
			
			$_SESSION['vs_pag_actual'] =	$_pag_actual;			
			//echo "<br><br>1-2 mPaginarLib:_pag_actual:";print_r($_pag_actual);
		}
		//echo "<br><br>1-3 mPaginarLib:_SESSION['vs_pag_actual']:";print_r($_SESSION['vs_pag_actual']);

		/*------------ Fin Agustín: modificación $_SESSION['vs_pag_actual'] --------*/
		
		/*------- Inicio total de registros y cálculo del número de páginas ----------
			Establecimiento del total de registros y cálculo del número de páginas que serán
			teniendo en cuenta el parámetro $_pag_cuantos (nº de registros por página)
			Dos opciones, que dependerá de la variable $_pag_conteo_alternativo: 
			por defecto estará a "true", salvo que se envíe el parámetro a "false"
		----------------------------------------------------------------------------*/
		//echo "<br><br>2-1 mPaginarLib:pag_conteo_alternativo:_pag_sql:";print_r($_pag_sql);		
	
		if($_pag_conteo_alternativo == false)
		{ 
	  /*--- Inicio total de registros con COUNT(*) problema con select DISTINCT ---
			variable $_pag_conteo_alternativo = "false"                                
			---------------------------------------------------------------------------*/
			$posOrder = stripos($_pag_sql,"ORDER BY");		
			
			if ($posOrder === false) {
					$_pag_sqlConta2 = substr($_pag_sql, stripos($_pag_sql,"FROM"));//elimino la parte primera hasta FROM ...
			}
			else
			{ $_pag_sqlConta1 = substr($_pag_sql, 0, $posOrder);//elimino la parte final ORDER BY ...
					$_pag_sqlConta2 = substr($_pag_sqlConta1, stripos($_pag_sqlConta1,"FROM"));//elimino la parte primera hasta FROM ...
			}			
			$_pag_sqlConta = "SELECT COUNT(*) ".$_pag_sqlConta2;
			
			//echo "<br><br>2-2-a mPaginarLib:pag_conteo_alternativo:_pag_sqlConta:";print_r($_pag_sqlConta);
			/* Originalmente era: 
				$_pag_result2 = mysql_query($_pag_sqlConta);			
				if($_pag_result2==false && $_pag_mostrar_errores==true)//Si hay error y errores está activado
				{die("Error en la consulta de conteo de registros: $_pag_sqlConta. Mysql dijo: <b>".
					mysql_error()."</b>");
				}			
				$_pag_totalReg = mysql_result($_pag_result2,0,0);//total de registros
			*/	
			
   $resConsultaSql = buscarCadSql($_pag_sqlConta,$conexionLinkDB,$arrBindValues);//en modeloMySQL.php';		
   //echo "<br><br>2-3-a mPaginarLib:resConsultaSql: ";print_r($resConsultaSql);

			if ($resConsultaSql['codError'] =='00000')			
			{		
		   $_pag_totalReg = $resConsultaSql['resultadoFilas']['0']['COUNT(*)'];
			}
			/*--- Fin total de registros con COUNT(*) problema con select DISTINCT ----*/
		}
		else //($_pag_conteo_alternativo == true)
		{/*--- Inicio 	variable $_pag_conteo_alternativo = "false"------------------*/                               
		
			/*---- Antigua: Obtiene el total de registros con mysql_num_rows ------
			$_pag_result3 = mysql_query($_pag_sql);	 
			if($_pag_result3 == false && $_pag_mostrar_errores == true)//Si hay error y errores está activado
			{die ("Error en la consulta de conteo alternativo de registros:$_pag_sql. Mysql dijo:<b>".mysql_error()."</b>");
			} $_pag_totalReg = mysql_num_rows($_pag_result3);
			*/	 
			/*------ Inicio AGUS: USO MIS PROPIAS FUNCIONES EQUIVALENTE A LA ANTERIOR ---
			para total de registros: modeloMySQL.php:buscarCadSql($_pag_sql,$conexionLinkDB)
			que incluye PDO, y se podría añadir array el parámetro bindParamValue opcionalmente
			Evita:  COUNT(*) que puede dar problemas con select DISTINCT
			-----------------------------------------------------------------------*/		

			//$resConsultaSql = buscarCadSql($_pag_sql,$conexionLinkDB);//en modeloMySQL.php';	
			$resConsultaSql = buscarCadSql($_pag_sql,$conexionLinkDB,$arrBindValues);//en modeloMySQL.php';	
				
			//echo "<br><br>2-3-b mPaginarLib:resConsultaSql: ";print_r($resConsultaSql);
			
			if ($resConsultaSql['codError'] == '00000')			
			{		$_pag_totalReg = $resConsultaSql['numFilas'];	
			}
			  /*--- Fin AGUS: USO MIS PROPIAS FUNCIONES EQUIVALENTE A LA ANTERIOR ---*/
			/*--- Fin 	variable $_pag_conteo_alternativo = "false"----------------------*/  
		}			
		if ($resConsultaSql['codError'] !== '00000')//error en las consultas anteriores		
		{
			$resSqlPaginar['codError'] = $resConsultaSql['codError'];
	  $resSqlPaginar['errorMensaje'] = 'Error en mPaginarLib.php: '.$resConsultaSql['errorMensaje'];			
			//echo "<br><br>2-4-1 mPaginarLib:resSqlPaginar: ";print_r($resSqlPaginar);
		}
		else//1 $resSqlPaginar['codError']=='00000'
		{		
		 //echo "<br><br>2-4-2 mPaginarLib:_pag_totalReg: ";print_r($_pag_totalReg);
   			
			/* Calculamos el número de páginas (saldrá decimal), con ceil() redondeamos y 
			   $_pag_totalPags será el número total (entero) de páginas que tendremos */
			
			$_pag_totalPags = ceil($_pag_totalReg/$_pag_cuantos);			
   //echo "<br><br>2-5 mPaginarLib:_pag_totalPags:";print_r($_pag_totalPags);
			
		 /*---------------------------------------------------------------------------
				Propagación de variables por el URL.
				La idea es pasar también en los enlaces las variables hayan llegado por url.
		 ---------------------------------------------------------------------------*/ 
			$_pag_enlace = $_SERVER['PHP_SELF'];			
			$_pag_query_string = "?";
			
			//echo "<br><br>3_0 mPaginarLib:_pag_enlace=_SERVER['PHP_SELF']: ";print_r($_pag_enlace);
		
			/*---- Original, que he sustuituido por lo que le sigue	-----
			if(!isset($_pag_propagar))
			{if (isset($_GET['_pag_pg'])) unset($_GET['_pag_pg']); 
				$_pag_propagar = array_keys($_GET); 
			}
			elseif(!is_array($_pag_propagar))// si $_pag_propagar no es un array, grave error!
			{die("<b>Error mPaginarLib : </b>La variable \$_pag_propagar debe ser un array");
			}------------------------------------------------------------*/						
		
   if(isset($_pag_propagar) && !is_array($_pag_propagar))//si $_pag_propagar no es un array grave error, se sale
	 	{	
			 //echo "<br><br>3_1 mPaginarLib:_pag_propagar: ";print_r($_pag_propagar);
		 	$resSqlPaginar['codError'] = '80300';//tipo dato no permitido 
	   $resSqlPaginar['errorMensaje'] = 'Error en mPaginarLib.php: la variable \$_pag_propagar, no es un array (tipo dato no permitido )';				
			}	
   else// $_pag_propagar no existe o no es array
			{/*------------------------------------------------------------------
				 Si no se definió qué variables propagar, se propagará todo el $_GET 
				 (por compatibilidad con versiones anteriores)
			  Perdón... no todo el $_GET. Todo menos la variable _pag_pg
				-------------------------------------------------------------------*/
    //echo "<br><br>3_2 mPaginarLib:_GET: ";print_r($_GET);		
				
				if (isset($_GET['_pag_pg'])) 
				{	unset($_GET['_pag_pg']); //Eliminamos esa variable del $_GET ??por qué
				}		
				$_pag_propagar = array_keys($_GET); //los campos de $_GET

				//-------------------------------------------------------------- 
				//echo "<br><br>3_3 mPaginarLib:_pag_propagar: ";print_r($_pag_propagar);
				
				foreach($_pag_propagar as $var)
				{
					//echo "<br><br>3_4 mPaginarLib:var: ";print_r($var);
					if(isset($GLOBALS[$var]))//Si la variable es global al script
					{
						//echo "<br><br>3_5 mPaginarLib:GLOBALS[var]: ";print_r($GLOBALS[$var]);
						//$_pag_query_string.= $var."=".$GLOBALS[$var]."&";//original
						
						if($var !=='_opcion_buscar')//esta se añade después de bucle, solo una vez 
						{/*-----------------------------------------------------------------------
							Agus:las dos siguientes líneas son para adaptarlo a mi formato de llamada
							a index.php. He modificado el original para que sea compatible con 
							"index.php" que en mi caso ya incluyo el directorio por defecto del 
							controlador:"/.controladores/" y extensión ".php" por eso lo quito de los
							valores que tree $GLOBALS[$VAR] que incluyen el dir y extensión 
							-----------------------------------------------------------------------*/
							$controladorSinDirExt[$var] = str_replace("./controladores/", "", $GLOBALS[$var]);//Quita el directorio
							$controladorSinDirExt[$var] = str_replace(".php", "", $controladorSinDirExt[$var]);//Quita la extensión		
										
							$_pag_query_string .= $var."=".$controladorSinDirExt[$var]."&";
						}			
						//echo "<br><br>3_6 mPaginarLib:_pag_query_string:GLOBALS: ";print_r($_pag_query_string);
					}
					elseif(isset($_REQUEST[$var]))//Si no es global (o register globals está en OFF)	
					{			
						//echo "<br><br>3_7 mPaginarLib:_REQUEST[var]: ";print_r($_REQUEST[$var]);
										
						if($var !== '_opcion_buscar')
						{
							//como viene de GET no es necesario quitar dir y extensión ya viene adecuedo a index.ph 
							$_pag_query_string .= $var."=".$_REQUEST[$var]."&";
						}					
					}
				}
				//if es para poder elegir las opciones de buscar por APE o por Agrupación en cPresidente
				if(isset($_pag_propagar_opcion_buscar) && !empty($_pag_propagar_opcion_buscar))
				{
					$_pag_query_string .= "_opcion_buscar=".$_pag_propagar_opcion_buscar."&";
				}	
				//echo "<br><br>3_8 mPaginarLib:_pag_query_string: ";print_r($_pag_query_string);
				
				$_pag_enlace .= $_pag_query_string; // Añadimos el query string a la url.
				//echo "<br><br>3_9 mPaginarLib:_pag_enlace: ";print_r($_pag_enlace);
				
				/*------------------------------------------------------------------------
			   Generación de los enlaces de paginación.
				  La variable $_pag_navegacion contendrá los enlaces a las páginas
				------------------------------------------------------------------------*/
				$_pag_navegacion_temporal = array();
				
				if ($_pag_actual != 1)
				{//----- Si no estamos en la página 1. Ponemos el enlace "primera" ------
					
					$_pag_url = 1; //será el número de página al que enlazamos
					$_pag_navegacion_temporal[] = "<a ".$_pag_nav_estilo_mod." href='".$_pag_enlace."_pag_pg=".$_pag_url."'>$_pag_nav_primera</a>";
				
					//------ Si no estamos en la página 1. Ponemos el enlace "anterior"------
					$_pag_url = $_pag_actual - 1; //será el número de página al que enlazamos					
					$_pag_navegacion_temporal[] = "<a ".$_pag_nav_estilo_mod." href='".$_pag_enlace."_pag_pg=".$_pag_url."'>$_pag_nav_anterior</a>";
																																				
					//echo "<br><br>4_1 mPaginarLib:_pag_navegacion_temporal:" ;print_r($_pag_navegacion_temporal);			
				} 
				
				/*La variable $_pag_nav_num_enlaces sirve para definir cuántos enlaces con 
				  números de página se mostrarán como máximo.
				  Ojo: siempre mostrará un número impar de enlaces. Más info en la documentación.
				*/				
				if(!isset($_pag_nav_num_enlaces))
				{/* Si no se definió la variable $_pag_nav_num_enlaces, Se asume que se 
			     mostrarán todos los números de página en los enlaces
					*/
					$_pag_nav_desde = 1;//Desde la primera
					$_pag_nav_hasta = $_pag_totalPags;//hasta la última
				}
				else // Si se definió la variable $_pag_nav_num_enlaces
				{// Calculamos el intervalo para restar y sumar a partir de la página actual
					$_pag_nav_intervalo = ceil($_pag_nav_num_enlaces/2) - 1;			
					$_pag_nav_desde = $_pag_actual - $_pag_nav_intervalo;//desde qué número de página se mostrará		
					$_pag_nav_hasta = $_pag_actual + $_pag_nav_intervalo;//hasta qué número de página se mostrará
					
					//-- Ajustamos los valores anteriores en caso sean resultados no válidos --					
					if($_pag_nav_desde < 1)	// Si $_pag_nav_desde es un número negativo
					{// Le sumamos la cantidad sobrante al final para mantener el número de enlaces que se quiere mostrar. 
						$_pag_nav_hasta -= ($_pag_nav_desde - 1);			
						$_pag_nav_desde = 1;// Establecemos $_pag_nav_desde como 1.
					}
					
					if($_pag_nav_hasta > $_pag_totalPags)// Si $_pag_nav_hasta es un número mayor que el total de páginas
					{// Le restamos la cantidad excedida al comienzo para mantener el número de enlaces que se quiere mostrar.
						$_pag_nav_desde -= ($_pag_nav_hasta - $_pag_totalPags);			
						$_pag_nav_hasta = $_pag_totalPags;// Establecemos $_pag_nav_hasta como el total de páginas.
						
						// Hacemos el último ajuste verificando que al cambiar $_pag_nav_desde no haya quedado con un valor no válido.
						if($_pag_nav_desde < 1)
						{	$_pag_nav_desde = 1;
						}
					}
				}
				
				for ($_pag_i = $_pag_nav_desde; $_pag_i <= $_pag_nav_hasta; $_pag_i++)//Desde página 1 hasta última página ($_pag_totalPags)
				{
					if ($_pag_i == $_pag_actual)//Si número de página es la actual ($_pag_actual). Se escribe el número, pero sin enlace y en negrita. 
					{
						$_pag_navegacion_temporal[] = "<span ".$_pag_nav_estilo_mod.">$_pag_i</span>";
					}
					else// Si es cualquier otro. Se escibe el enlace a dicho número de página.
					{
						$_pag_navegacion_temporal[] = "<a ".$_pag_nav_estilo_mod." href='".$_pag_enlace."_pag_pg=".$_pag_i."'>".$_pag_i."</a>";
					}
				}
				//echo "<br><br>4_2 mPaginarLib:_pag_navegacion_temporal:";print_r($_pag_navegacion_temporal);		
				if ($_pag_actual < $_pag_totalPags)//Si no estamos en la última página. 
				{
					//Ponemos el enlace "Siguiente"					
					$_pag_url = $_pag_actual + 1; //será el número de página al que enlazamos
					$_pag_navegacion_temporal[] = "<a ".$_pag_nav_estilo_mod." href='".$_pag_enlace."_pag_pg=".$_pag_url."'>$_pag_nav_siguiente</a>";

					//Ponemos el enlace "Última"
					$_pag_url = $_pag_totalPags; //será el número de página al que enlazamos
					$_pag_navegacion_temporal[] = "<a ".$_pag_nav_estilo_mod." href='".$_pag_enlace."_pag_pg=".$_pag_url."'>$_pag_nav_ultima</a>";
				}
				//echo "<br><br>4_3 mPaginarLib:_pag_navegacion_temporal: ";print_r($_pag_navegacion_temporal);		
				$_pag_navegacion = implode($_pag_separador, $_pag_navegacion_temporal);
				//echo "<br><br>4_4_1 mPaginarLib:_pag_navegacion: ";print_r($_pag_navegacion);	
					
				/*--------------------------------------------------------------------------
				   Obtención cuántos registros $_pag_cuantos que se mostrarán en la 
							página actual	y desde que fila $_pag_inicial de la select se comenzará 
				   Al contar desde qué registro se mostrará en esta página, el conteo 
							empieza desde CERO para la primerá página.	Con LIMIT						
				--------------------------------------------------------------------------*/	

				$_pag_inicial = ($_pag_actual-1) * $_pag_cuantos;

				//echo "<br><br>4_4_2 mPaginarLib:_pag_inicial: ";print_r($_pag_inicial);
				//echo "<br><br>4_4_3 mPaginarLib:_pag_cuantos: ";print_r($_pag_cuantos);							
				
				/*Consulta origen SQL. Devuelve $cantidad registros empezando en $_pag_inicial
					$_pag_sqlLim = $_pag_sql." LIMIT $_pag_inicial,$_pag_cuantos";
					$_pag_result = mysql_query($_pag_sqlLim);
					// Si ocurrió error y mostrar errores está activado
					if($_pag_result == false && $_pag_mostrar_errores == true)
					{die("Error en la consulta limitada:$_pag_sqlLim.Mysql dijo:".mysql_error());
					}*/
				//------Agus: utilizo mis funciones de consulta SQL ------------------------		
			
				$_pag_sqlLim = $_pag_sql." LIMIT $_pag_inicial,$_pag_cuantos";
				
				//$resConsultaSql = buscarCadSql($_pag_sqlLim,$conexionLinkDB);
				$resConsultaSql = buscarCadSql($_pag_sqlLim,$conexionLinkDB,$arrBindValues);//en modeloMySQL.php';
				
				//echo "<br><br>4-5-1 mPaginarLib:resConsultaSql: ";print_r($resConsultaSql);	
		
				if ($resConsultaSql['codError'] !=='00000')//error en las consultas anteriores		
				{
					$resSqlPaginar['codError'] = $resConsultaSql['codError'];
					$resSqlPaginar['errorMensaje'] = 'Error en mPaginarLib.php: '.$resConsultaSql['errorMensaje'];
				}
				else//2 $resSqlPaginar['codError']=='00000'
				{
					$resSqlPaginar = $resConsultaSql;
					/*-------------------------------------------------------------------------
					 Generación de la información sobre los registros mostrados.
					-------------------------------------------------------------------------*/ 
					$_pag_desde = $_pag_inicial + 1; //Número del primer registro de la página actual
					$_pag_hasta = $_pag_inicial + $_pag_cuantos;//Número del último registro de la página actual
					
					if($_pag_hasta > $_pag_totalReg)// Si estamos en la última página
					{ 
						$_pag_hasta = $_pag_totalReg;//ultimo registro de la pág. actual será igual al número de registros.
					} 
					$_pag_info = "Del $_pag_desde al $_pag_hasta de un total de $_pag_totalReg";

					$resSqlPaginar['_pag_info'] = $_pag_info;
					$resSqlPaginar['_pag_navegacion'] = $_pag_navegacion;
					
				}//else 2 $resSqlPaginar['codError']=='00000'
			}//else is_array($_pag_propagar)
	 }//else 1 $resSqlPaginar['codError']=='00000'		
	}//else $conexionDB['codError'] == "00000"acaso se pueda subir más arriba
	
	
	if ($resSqlPaginar['codError'] !== '00000')
	{$datosError['codError'] = $resSqlPaginar['codError'];	
		$datosError['errorMensaje']	= $resSqlPaginar['nomScript'].":".$resSqlPaginar['nomFuncion'].": ".$resSqlPaginar['errorMensaje'];
		$datosError['textoComentarios'] = $resSqlPaginar['nomScript'].":".$resSqlPaginar['nomFuncion'].
																																									" Error del sistema al buscar hacer la páginación de la lista de socios/as ";
  require_once './modelos/modeloErrores.php';																																									
		insertarError($datosError);		
	}

	//echo "<br><br>5_0 mPaginarLib:resSqlPaginar: ";print_r($resSqlPaginar);
	
	return $resSqlPaginar;
}	
/*------------------------------------------------------------------------------
	Datos que quedan disponibles en el array "$resSqlPaginar" que devuelve
 la función:	mPaginarLib()
-------------------------------------------------------------------------------- 
$resSqlPaginar['resultadoFilas'][0]([ANIOCUOTA],[ESTADO],[apeNom],[Agrupacion_Actual],
[Codigo_Agrup_Actual],[IMPORTECUOTAANIOEL],[IMPORTECUOTAANIOSOCIO],[IMPORTECUOTAANIOPAGADA],
[IMPORTEGASTOSABONOCUOTA],[FECHAPAGO],[FECHAANOTACION],[MODOINGRESO],[ORDENARCOBROBANCO],
[ESTADOCUOTA],[saldo],[CodAgrup_PagoCuota],[OBSERVACIONES]) 

$resSqlPaginar['resultadoFilas'][1]	
.........
$resSqlPaginar['resultadoFilas'][11]	

$resSqlPaginar[numFilas]
			
$resSqlPaginar['_pag_navegacion']:Cadena que contiene la barra de navegación con enlaces a las diferentes páginas.
 				Ejemplo: "<<primera  <anterior  1  2  3  4  siguiente>  última>>".							
$resSqlPaginar['_pag_info']:Cadena que contiene información sobre los registros de la página actual.
 				Ejemplo: "desde el 10 hasta el 20 de un total de 500";
					
$resSqlPaginar[codError]
$resSqlPaginar[errorMensaje]

$resSqlPaginar[nomScript]
$resSqlPaginar[nomFuncion]
*/
?>