<?php
/*-----------------------------------------------------------------------------
FICHERO: arrayParValor.php
VERSION: PHP 7.3.19

OBSERVACIONES: 
Agustín: 
2020-08-07: probado php7
2020-03-18: En algunas funciones he añadido "PDOStatement::bindParamValue"		
Traslado todas las funciones que están debajo de arrayParValor(), 
como tipo parValorPais()... a este script. Es más lógio que estén aquí, antes 
estaban en modeloUsuarios.php.
Fusiono en una sola función "arrayParValor($cadSql,...)" los dos funciones que 
había antes: arrayParValor($tablasBusqueda,...) y arrayParValorCadSql($cadSql,...)
2020-02-15 mejoro comentarios y añado $conexionLinkDB = NULL, para si ya está 
conectada a BBDD 
2019-03-11: Hago unas variante que puenden funcionar con antiguas llamadas a funciones
y también con nuevas llamadas con PDOStatement::bindParamValue
Para ello añado como parámetro $arrBind = NULL, 
(ojo con el orden de los parámetros al llamar estas funciones)

En algunas funciones he añadido "PDOStatement::bindParamValue"		
------------------------------------------------------------------------------*/

/*--------------------- Inicio arrayParValor($cadSql,...) ----------------------
DESCRIPCION: Con una consulta SELECT se obtiene un array asociativo con columnas:
Índice: "$campoListaCod" (ej: codigoPais) y Descripción: "$campoListaDesc" (ej: nombrePais).
Además añade los campos por defecto, y los habituales códigos de error, ...
													
RECIBE: $cadSql (string select completa), 
        campoListaCod (campo código: ejemplo "CODPAIS1"),
								$campoListaDesc(campo descripción: ejemplo "NOMBREPAIS"),
								$valorDefecto (Valor por defecto campo código: ejemplo "ES"), 
								$arrBind = NULL (con valores condiciones de la $cadSql, si no recibiese valor será NULL) 
								$conexionLinkDB:	(recibe $conexionDB['conexionLink'], si no recibiese valor será NULL)
								
DEVUELVE: $arrayParValorDescrip array asociativo que contiene ejemplo: 
[lista] = Array([AF]=>Afganistán, [AL]=>Albania,...)	un array asociativo con
          columnas índice "$campoListaCod" [AF], valor "$campoListaDesc" =>Afganistán,
[campoListaCod] => CODPAIS1 ( el nombre del campo columnas índice),
[valorDefecto]=>ES,[descDefecto]=>España (selección por defecto, código y descripción, para mostrar en el FORMULARIO),				
Campos habituales:[nomScript]=>arrayParValor.php,[nomFuncion]=>arrayParValor,[codError]=>00000,[errorMensaje]=>''
														
LLAMADA: modeloUsuarios.php:parValorPais(),parValorProvincia(),parValorAgrupacion(),
parValorAreaGestionCoord(),parValoresRegistrarUsuario(), y que a su vez se llaman
desde controladorSocios.php,cCoordinador.php,cPresidente.php,cTesorero.php 
y acaso desde otros lugares...

LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarCadSql()

OBSERVACIONES: 
2020-08-07: Modificaciones para evitar Notice cuando no existe "valorDefecto"
Es utilizado para pasar este array a los formularios que
necesitan formar comboBox dinámicos mediante la función:
comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)

No inserta errores con insertarError()	
NOTA: Es válida con PDO y sin PDO, pues esta función antes ya incluía conexionDB()
Pero ha sido necesario cambiar los parámetros de llama a esta función, antes era: 

function arrayParValor($tablasBusqueda,$cadenaCondicionesBuscar,$campoListaCod,$campoListaDesc,$valorDefecto,$conexionDB)
------------------------------------------------------------------------------*/
function arrayParValor($cadSql,$campoListaCod,$campoListaDesc,$valorDefecto,$arrBind = NULL,$conexionLinkDB = NULL) 
{
	//echo "<br><br>0-a modelos/libs/arrayParValor.php:arrayParValor:cadenaCondicionesBuscar: ";print_r($cadenaCondicionesBuscar);
	//echo "<br><br>0-b modelos/libs/arrayParValor.php:arrayParValor:campoListaCod: ";print_r($campoListaCod);	
	//echo "<br><br>0-c modelos/libs/arrayParValor.php:arrayParValor:campoListaDesc: ";print_r($campoListaDesc);
 //echo "<br><br>0-d modelos/libs/arrayParValor.php:arrayParValor:valorDefecto: ";print_r($valorDefecto);		
	//echo "<br><br>0-e modelos/libs/arrayParValor.php:arrayParValor:arrBind: ";print_r($arrBind);	
	//echo '<br><br>0-f modelos/libs/arrayParValor.php:arrayParValor:conexionLinkDB: ';var_dump($conexionLinkDB);echo '<br>';
	
	$arrayParValorDescrip['nomScript'] = 'modelos/libs/arrayParValor.php'; 
 $arrayParValorDescrip['nomFuncion'] = 'arrayParValor'; 
	$arrayParValorDescrip['codError'] = '00000';
	$arrayParValorDescrip['errorMensaje'] = '';

 if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
	{ 	
   require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
		 
			//echo "<br><br>1 modelos/libs/arrayParValor.php:arrayParValor:conexionDB: ";var_dump($conexionDB);				
 }		
	else
	{ $conexionDB['codError'] = "00000";
		 $conexionDB['conexionLink'] = $conexionLinkDB;   			
	 	//echo "<br><br>2 modelos/libs/arrayParValor.php:arrayParValor:conexionDB: ";var_dump($conexionDB);	 
	}	
	
	if ($conexionDB['codError'] !== "00000")
	{ 
   $arrayParValorDescrip['codError'] = $conexionDB['codError'];
	  $arrayParValorDescrip['errorMensaje'] = $conexionDB['errorMensaje'];
	}
	else
	{	
			require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
			
			$datosTabla = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);
			
			//echo "<br><br>3 modelos/libs/arrayParValor.php:arrayParValor:datosTabla: ";print_r($datosTabla);	
			
			if ($datosTabla['codError'] !== '00000')
			{	$arrayParValorDescrip['codError'] = $datosTabla['codError'];
					$arrayParValorDescrip['errorMensaje'] = $datosTabla['errorMensaje'];
			}
			elseif ($datosTabla['numFilas'] == 0)
			{ $arrayParValorDescrip['codError'] = '80001';
					$arrayParValorDescrip['errorMensaje'] = 'ArrayParValor.php, error en tabla: '. $tablasBusqueda;
			}
			else
			{
					for ($contFilas = 0; $contFilas < $datosTabla['numFilas']; $contFilas++)
					{
							$valorCodigo = $datosTabla['resultadoFilas'][$contFilas][$campoListaCod];
							$descripcionCodigo = $datosTabla['resultadoFilas'][$contFilas][$campoListaDesc];

							$parValorDescrip["$valorCodigo"] = $descripcionCodigo;
					}

					$arrayParValorDescrip['campoListaCod'] = $campoListaCod;
					$arrayParValorDescrip['valorDefecto'] = $valorDefecto;
					
					if ($valorDefecto =='%')
					{ $arrayParValorDescrip['descDefecto'] = 'Todas';
				 }
					elseif (isset($valorDefecto) && !empty($valorDefecto) && array_key_exists($valorDefecto,$parValorDescrip) )
					{
							//echo "<br><br>4 modelos/libs/arrayParValor.php:arrayParValor:arrayParValorDescrip: ";print_r($arrayParValorDescrip);				  
			    $arrayParValorDescrip['descDefecto'] = $parValorDescrip["$valorDefecto"];					
					}	
			
					$arrayParValorDescrip['lista'] = $parValorDescrip;
			}
	}	 
	//echo "<br><br>5 modelos/libs/arrayParValor.php:arrayParValor:arrayParValorDescrip: ";print_r($arrayParValorDescrip);	
	
	return ($arrayParValorDescrip);
}
//--------------------------- Fin arrayParValor --------------------------------

/*==================== INICIO parValor_Funciones() ==========================*/

/*---------------------------- Inicio parValorPais -----------------------------
Descripción: Genera un array asociativo $parValorPais con los campos de 
             CODPAIS1,NOMBREPAIS y el valor por defecto de la tabla PAIS,
													para utilizar en un comboBox PAIS que se utiliza para generar un 
													desplegable en los 	modelos de vistas	para elegir el país.
													
RECIBE: $valDefPais, valor por defecto = el código CODPAIS1 (código ISO 3166-1 alfa-2) de un PAIS 
        por ejemplo (ES), o nada "" para en el caso de todos los países
DEVUELVE: array $parValorPais con datos seleccionados de la tabla PAIS de si se
          ha encontrado la búsqueda, valor por defecto y código error																

Llamada: cPresidente.php:enviarEmailSociosPres(),cTesorero.php:anotarIngresoDonacionMenu(), 
         y acaso otros controladores
LLAMA: modelos/libs/arrayParValor.php:arrayParValor()
									
OBSERVACIONES:	No inserta errores con insertarError()													
Agustín 2020-02-13: modifico comentarios 										
------------------------------------------------------------------------------*/
function parValorPais($valDefPais)
{ 
  //echo '<br><br>0-1 modelos/libs/arrayParValor.php:parValorPais:valDefPais: ';print_r($valDefPais);

		/*--------- Inicio nueva opción ----------------------*/
	
	 $cadSql = "SELECT CODPAIS1, NOMBREPAIS FROM PAIS  ORDER BY NOMBREPAIS ASC ";		
		
		//$parValorPais = arrayParValor($cadSql,$campoListaCod,$campoListaDesc,$valorDefecto,$arrBind = NULL,$conexionLinkDB = NULL)
		$parValorPais = arrayParValor($cadSql,'CODPAIS1','NOMBREPAIS',$valDefPais);
		
		/*--------- Fin nueva opción ----------------------*/
		
	 if ($parValorPais['codError'] !== '00000')
	 {		
		 $parValorPais['errorMensaje'] .= ". Al buscar tabla países";
	 }		
	 //echo '<br><br>1 modelos/libs/arrayParValor.php:parValorPais:parValorPais: ';print_r($parValorPais);
		
	 return $parValorPais;
	}	
//---------------------------- Fin parValorPais --------------------------------

/*---------------------------- Inicio parValorProvincia ------------------------
Descripción: Genera un array asociativo $parValorProvincia con los campos de 
             CODPROV",NOMPROVINCIA de la tabla PROVINCIA, para utilizar en un 
             comboBox PROVINCIA que se utiliza para generar un desplegable en los 
													modelos de vistas	para elegir el país en el desplegable.

RECIBE: $valDefDomicilioProv, valor por defecto = el código CODPROV de una PROVINCIA
        por ejemplo (14) para Córdoba, o nada "" para en el caso de todos los provincias
DEVUELVE: array $$parValorProvincia con datos seleccionados de la tabla PROVINCIA de si se
          ha encontrado la búsqueda  valor por defecto, y código error																

Llamada: cPresidente.php:enviarEmailSociosPres() y acaso otros controladores o lugares
LLAMA: modelos/libs/arrayParValor.php:arrayParValor()
									
OBSERVACIONES:	No inserta errores con insertarError()
													
Agustín 2020-02-13: modifico comentarios													
------------------------------------------------------------------------------*/
function parValorProvincia($valDefDomicilioProv)
{
	//echo '<br><br>0-1 modelos/libs/arrayParValor.php:parValorProvincia:valDefDomicilioProv: ';print_r($valDefDomicilioProv);
	
 require_once './modelos/libs/arrayParValor.php';	
	//$parValorProvincia = arrayParValor('PROVINCIA',' ORDER BY NOMPROVINCIA ','CODPROV','NOMPROVINCIA',$valDefDomicilioProv);//***anterior	

 /*--------- Inicio nueva opción ----------------------*/

	$cadSql = "SELECT CODPROV, NOMPROVINCIA FROM PROVINCIA ORDER BY NOMPROVINCIA ASC ";		
	
	//$parValorProvincia = arrayParValor($cadSql,$campoListaCod,$campoListaDesc,$valorDefecto,$arrBind = NULL,$conexionLinkDB = NULL)
	$parValorProvincia = arrayParValor($cadSql,'CODPROV','NOMPROVINCIA',$valDefDomicilioProv);
	
	/*--------- Fin nueva opción -------------------------*/
	
	if ($parValorProvincia['codError'] !== '00000')
	{
  $parValorProvincia['errorMensaje'] .= ". Al buscar en la tabla de provincias";
	}
	//echo '<br><br>1 modelos/libs/arrayParValor.php:parValorProvincia:parValorProvincia: ';print_r($parValorProvincia);
	
	return $parValorProvincia;
}	
//---------------------------- Fin parValorProvincia ---------------------------

/*------------------------------- Inicio parValorAgrupacion --------------------
DESCRIPCION: array asociativo parValor para utilizar en comboBox para las Agrupaciones 
 
RECIBE: $valDefAgrupa, valor por defecto CODAGRUPACION solo se utiliza para pasarlo
        a función arrayParValor
DEVUELVE: parValor con datos seleccionados de la tabla AGRUPACIONTERRITORIAL si se
          ha encontrado la búsqueda, valor por defecto y código error			
LLAMADA: Actualmente es posible que no se utilice
LLAMA: modelos/libs/arrayParValor.php:arrayParValor()

OBSERVACIONES:	Posiblemente ahora  no se use             															
------------------------------------------------------------------------------*/
function parValorAgrupacion($valDefAgrupa)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValorAgrupacion:valDefAgrupa: ";print_r($valDefAgrupa);

 require_once './modelos/libs/arrayParValor.php';

	/*--------- Inicio nueva opción ----------------------*/

	//$cadSql = "SELECT CODAGRUPACION, NOMAGRUPACION FROM AGRUPACIONTERRITORIAL ORDER BY NOMAGRUPACION ASC";		
	
	$cadSql = "SELECT CODAGRUPACION, NOMAGRUPACION FROM AGRUPACIONTERRITORIAL ORDER BY CODPAISDOM DESC, NOMAGRUPACION ASC";	
	
											/*	"AGRUPACIONTERRITORIAL.CODPAISDOM DESC"; es para que agrupación Europa Laica Estatal e Internacional salga la última	
																ya que CODPAISDOM = '--'*/	

	$parValorAgrupacion = arrayParValor($cadSql,'CODAGRUPACION','NOMAGRUPACION',$valDefAgrupa);
	
	/*--------- Fin nueva opción ----------------------*/
	
	
	if ($parValorAgrupacion['codError'] !== '00000')
	{
  $parValorAgrupacion['errorMensaje'] .= ". Al buscar en la tabla de agrupaciones";
	}
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValorAgrupacion:parValorAgrupacion: ";print_r($parValorAgrupacion);

	return $parValorAgrupacion;
}	
/*------------------------------- Fin parValorAgrupacion -----------------------*/

/*------------------------------- Inicio parValorAreaGestionCoord --------------
DESCRIPCION: array asociativo parValor para utilizar en comboBox área de gestión
             AREAGESTION de coordinador
 
RECIBE: $valDefAreaGestion, valor por defecto CODAREAGESTION solo se utiliza 
        para pasarlo a función arrayParValor
DEVUELVE: parValor con datos seleccionados de la tabla AREAGESTION si se
          ha encontrado la búsqueda, valor por defecto y código error			
										
LLAMADA: cPresidente.php:asignarCoordinacionAreaBuscar()
LLAMA: modelos/libs/arrayParValor.php:arrayParValor()

OBSERVACIONES:	 
2020-03-06: modifico comentarios          															
------------------------------------------------------------------------------*/
function parValorAreaGestionCoord($valDefAreaGestion)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValorAreaGestionCoord:valDefAreaGestion: ";print_r($valDefAreaGestion);
 
	require_once './modelos/libs/arrayParValor.php';
	
	/*--------- Inicio nueva opción ----------------------*/
	//$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";	
	
	$cadSql = "SELECT CODAREAGESTION, NOMAREAGESTION FROM AREAGESTION  ORDER BY NOMAREAGESTION ASC ";			
	/* Europa Laica Estatal e Internacional estará más o menos en medio de la lista, para que salga primera o última se puede hacer el en el formulario	*/
	
	//$parValorAreaGestion = arrayParValor($cadSql,$campoListaCod,$campoListaDesc,$valorDefecto,$arrBind = NULL,$conexionLinkDB = NULL)
	$parValorAreaGestion = arrayParValor($cadSql,'CODAREAGESTION', 'NOMAREAGESTION',$valDefAreaGestion);
	
	/*--------- Fin nueva opción ----------------------*/																											
	
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:parValorAreaGestion: ";print_r($parValorAreaGestion );
	
	if ($parValorAreaGestion['codError'] !== '00000')
	{	
  $parValorAreaGestion['errorMensaje'] .= ". Al buscar en la tabla de AREAGESTION";
		//	echo "<br><br>2 modeloUsuarios.php:parValorAreaGestionCoord:parValorAreaGestion: ";print_r($parValorAreaGestion);

	}	
	else
	{
		if ($valDefAreaGestion == 'Elegir')
	 {$parValorAreaGestion['valorDefecto'] = "Elegir";
			$parValorAreaGestion['descDefecto'] = "Elige un área de coordinación";
				
			//echo "<br><br>3 modelos/libs/arrayParValor.php:parValorAreaGestionCoord:parValorAreaGestion: ";print_r($parValorAreaGestion);
	 }
	 //echo "<br><br>4 modelos/libs/arrayParValor.php:parValorAreaGestionCoord:parValorAreaGestion: ";print_r($parValorAreaGestion); 
	}
	return $parValorAreaGestion;
}	
//------------------------------ Fin parValorAreaGestionCoord ------------------


/*---------------------------- Inicio parValorProvincia ------------------------
Descripción: Genera un array asociativo $parValorProvincia con los campos de 
             CODPROV,NOMPROVINCIA de la tabla PROVINCIA, y valor por defecto 
													$valDefCodProvincia para utilizar en un comboBox PROVINCIA que se 
													utiliza para generar un desplegable en los modelos de vistas	para 
													elegir el país en el desplegable.
													
RECIBE: $codProvinciaPresCoord, el código CODPROV de una PROVINCIA
        por ejemplo (14) para Córdoba, o "%" para en el caso de todos los provincias
								$valDefCodProvincia = l = valor por defecto 
DEVUELVE: array $parValorProvincia con datos seleccionados de la tabla PROVINCIA 
          si se ha encontrado la búsqueda, valor por defecto, $arrBind para la 
										select  y código error																

Llamada: cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres() 
modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres()
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES:	
2018-11-14: lo creo para estadísticas por provincias para
vExportarExcelEstadisticasAltasBajasProvPresInc->formExportarExcelEstadisticasAltasBajasProvPres.php												
2020-02-13: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function parValoresProvinciaPresCoord($codProvinciaPresCoord,$valDefCodProvincia)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:codProvinciaPresCoord: ";print_r($codProvinciaPresCoord);
	//echo "<br><br>0-2 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:valDefCodProvincia: ";print_r($valDefCodProvincia);
		
 $parValor['nomScript'] = "modelos/libs/arrayParValor.php"; 
	$parValor['nomFuncion'] = "parValoresProvinciaPresCoord";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';
	
	if ($codProvinciaPresCoord =='%')
	{
   $cadSql = " SELECT DISTINCT CODPROV,NOMPROVINCIA FROM PROVINCIA	                      
																			      ORDER BY NOMPROVINCIA ASC ";
																									
			//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:cadSql: ";print_r($cadSql);
			$arrBind = array();
 }
 else	
 { 
	  $cadSql = " SELECT DISTINCT CODPROV,NOMPROVINCIA FROM PROVINCIA
	                        WHERE CODPROV = :codProvinciaPresCoord
																			       ORDER BY NOMPROVINCIA ASC ";
																										
   //echo "<br><br>2-1 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:cadSql: ";print_r($cadSql);
			
			$arrBind = array(':codProvinciaPresCoord' => $codProvinciaPresCoord);			
			
   //echo "<br><br>2-2 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:arrBind: ";print_r($arrBind);			
 }																				
	require_once './modelos/libs/arrayParValor.php';

	$parValor = arrayParValor($cadSql,'CODPROV','NOMPROVINCIA',$valDefCodProvincia,$arrBind);	
	
 //echo "<br><br>3 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:parValor: ";print_r($parValor);
	
	if ($parValor['codError'] !== '00000')
	{
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($codProvinciaPresCoord == '%')
	 { $parValor['lista']["%"] = "Todas"; 
	 } 
	}	
	//echo "<br><br>4 modelos/libs/arrayParValor.php:parValoresProvinciaPresCoord:parValor: ";print_r($parValor);
	
 return $parValor;
}
//------------------------------ Fin parValoresProvinciaPresCoord --------------

/*------------------------------- Inicio parValoresCCAA ------------------------
DESCRIPCION: array asociativo parValor para utilizar en comboBox para las CCAA 
 
RECIBE: $valDefCodCCAA, valor por defecto CodCCAA solo se utiliza para pasarlo
        a función arrayParValorCadSql
DEVUELVE: parValor con datos seleccionados de la tabla CCAA de si se
          ha encontrado la búsqueda, valor por defecto y código error			
LLAMADA: cPresidente.php:enviarEmailSociosPres()
"vEnviarEmailAttachFilesInc.php-->formEnviarEmailAttachFiles.php"
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSq()
              															
									
OBSERVACIONES:	
Agustín 2018-11-17:modifico comentarios y algunos nombres variables para que 
sean más significativos
------------------------------------------------------------------------------*/
function parValoresCCAA($valDefCodCCAA)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresCCA:valDefCodCCAA: $valDefCodCCAA";

 $parValor['nomScript'] = "modelos/libs/arrayParValor.php"; 
	$parValor['nomFuncion'] = "parValoresCCAA";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';

	$cadSqlCCAA = "SELECT DISTINCT CCAA.CCAA, CCAA.NOMBRECCAA  
	                 FROM CCAA,PROVINCIA
	                 WHERE CCAA.CCAA = PROVINCIA.CCAA";
																		
	require_once './modelos/libs/arrayParValor.php';								

 $parValor = arrayParValor($cadSqlCCAA,'CCAA','NOMBRECCAA',$valDefCodCCAA);		

	if ($parValor['codError'] !== '00000')
	{
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($valDefCodCCAA == '%')
	 { $parValor['lista']["%"] = "Todas"; 
	 } 
	}	
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresCCA:parValor: ";print_r($parValor);
	
 return $parValor;
}
//------------------------------ Fin parValoresCCAA ----------------------------

/*---------------------------- Inicio parValoresCCAAPresCoord ------------------
Descripción: Genera un array asociativo $parValor con los campos de 
             CCAA,NOMBRECCAA de la tabla CCAA, y valor por defecto 
													$valDefCodCCAA para utilizar en un comboBox CCAA que se 
													utiliza para generar un desplegable en los modelos de vistas	para 
													elegir el COMUNIDAD AUTÓNAMA en el desplegable.
													
RECIBE: $codCCAAPresCoord, el código CCAA de una CCAA, ejemplo(1) para Andalucía,
        o "%" para en el caso de todas, 	$valDefCodCCAA el valor  por defecto
DEVUELVE: array $parValor con datos seleccionados de la tabla CCAA de si se
          ha encontrado la búsqueda, valor por defecto, $arrBind para la select
										y código de error

Llamada: cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres()
modeloPresCoord.php: mExportarExcelEstadisticasAltasBajasCCAAPres()
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES:	
2018-11-14: lo creo para estadísticas por CCAA para
vExportarExcelEstadisticasAltasBajasCCAAPresInc->formExportarExcelEstadisticasAltasBajasCCAAPres.php												
2020-02-13: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function parValoresCCAAPresCoord($codCCAAPresCoord,$valDefCodCCAA)
{
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:codCCAAPresCoord: ";print_r($codCCAAPresCoord);
	//echo "<br><br>2 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:valDefCodCCAA: ";print_r($valDefCodCCAA);
 
 $parValor['nomScript'] = "modelos/libs/arrayParValor.php"; 
	$parValor['nomFuncion'] = "parValoresCCAAPresCoord";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';																
																			
	if ($codCCAAPresCoord =='%' )
	{
			$cadSql = "SELECT DISTINCT CCAA,NOMBRECCAA FROM CCAA	                   
																			  ORDER BY NOMBRECCAA ASC";
																									
			//echo "<br><br>2-a modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:cadSql: ";print_r($cadSql);
			$arrBind = array();
 }
 else	
 { 
   $cadSql = "SELECT DISTINCT CCAA,NOMBRECCAA FROM CCAA
	                   WHERE CCAA = :codCCAAPresCoord
																			  ORDER BY NOMBRECCAA ASC";																					
																										
   //echo "<br><br>2-b1 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:cadSql: ";print_r($cadSql);
			
			$arrBind = array(':codCCAAPresCoord' => $codCCAAPresCoord);			
			
   //echo "<br><br>2-b2 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:arrBind: ";print_r($arrBind);
 }					
																	
	require_once './modelos/libs/arrayParValor.php';
	
	$parValor = arrayParValor($cadSql,'CCAA','NOMBRECCAA',$valDefCodCCAA,$arrBind);	
	
 //echo "<br><br>3 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:parValor: ";print_r($parValor);
	
	if ($parValor['codError'] !== '00000')
	{
		//$parValor['arrMensaje']['textoCabecera'] = "Modelo usuarios"; 
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] ='./index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($codCCAAPresCoord == '%')
	 {$parValor['lista']["%"] = "Todas"; 
	 } 
	}	
	//echo "<br><br>4 modelos/libs/arrayParValor.php:parValoresCCAAPresCoord:parValor: ";print_r($parValor);
	
 return $parValor;
}
//------------------------------ Fin parValoresCCAAPresCoord -------------------

/*---------------------------- Inicio parValoresAgrupaPresCoord ----------------
Genera un array asociativo $parValor con los campos de CODAGRUPACION,NOMAGRUPACION
de la tabla AGRUPACIONTERRITORIAL, y valor por defecto $valDefCodAgrupa para 
utilizar en un comboBox AGRUPACIONTERRITORIAL que se utiliza en distintos funciones 
													
RECIBE: $codAgrupaPresCoord, el código CODAGRUPACION de una AGRUPACIONTERRITORIAL         
        o  "%" para en el caso de todas, 	$valDefCodAgrupa el valor  por defecto
DEVUELVE: array $parValor con datos seleccionados de la tabla AGRUPACIONTERRITORIAL
          si se ha encontrado la búsqueda, valor por defecto, $arrBind para la select
										y código de error.

LLAMADA: cPresidente.php:mostrarSociosPres(),enviarEmailSociosPres(),excelSociosPres()
mostrarSociosFallecidosPres(),
cExportarExcelInformeAnualPres(),cExportarExcelEstadisticasAltasBajasAgrupPres()

cTesorero.php:XMLCuotasTesoreroSantander(),,AEB19CuotasTesoreroSantander(),
excelCuotasInternoTesorero(),excelCuotasTesoreroBancos(),
emailAvisarDomiciliadosProximoCobro(),emailAvisarCuotaNoCobradaSinCC(),
exportarEmailDomiciliadosPend(),exportarEmailDomiciliadosPendSinCC()

modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres()					
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES:									
2020-03-06: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function parValoresAgrupaPresCoord($codAgrupaPresCoord,$valDefCodAgrupa)
{
	//echo "<br><br>0-1 modeloUsuarios.php:parValoresAgrupaPresCoord:codAgrupaPresCoord: ";print_r($codAgrupaPresCoord);
	//echo "<br><br>0-2 modeloUsuarios.php:parValoresAgrupaPresCoord.php:valDefCodAgrupa: ";print_r($valDefCodAgrupa);
 
	$parValor['nomScript'] = "modelos/libs/arrayParValor.php"; 
	$parValor['nomFuncion'] = "parValoresAgrupaPresCoord";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';

	if ($codAgrupaPresCoord =='%' )
	{
	//$cadSql = " SELECT DISTINCT CODAGRUPACION,NOMAGRUPACION FROM AGRUPACIONTERRITORIAL  ORDER BY NOMAGRUPACION ASC ";		
																		
	$cadSql = "SELECT CODAGRUPACION, NOMAGRUPACION FROM AGRUPACIONTERRITORIAL ORDER BY CODPAISDOM DESC, NOMAGRUPACION ASC";	
	
											/*	"AGRUPACIONTERRITORIAL.CODPAISDOM DESC"; es para que agrupación Europa Laica Estatal e Internacional salga la última	
																ya que CODPAISDOM = '--'*/																				
   $arrBind = array();																		
																									
			//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:cadSql: ";print_r($cadSql);
 }
 else	
 { 
   $cadSql = " SELECT DISTINCT CODAGRUPACION,NOMAGRUPACION FROM AGRUPACIONTERRITORIAL
	                 WHERE CODAGRUPACION = :codAgrupaPresCoord 																			
                    ORDER BY CODPAISDOM DESC, NOMAGRUPACION ASC"; //antes  ORDER BY NOMAGRUPACION ASC ";
																										
   //echo "<br><br>2-1 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:cadSql: ";print_r($cadSql);
			
			$arrBind = array(':codAgrupaPresCoord' => $codAgrupaPresCoord);			
			
   //echo "<br><br>2-2 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:arrBind: ";print_r($arrBind);
 }																					

	require_once './modelos/libs/arrayParValor.php';		
	$parValor = arrayParValor($cadSql,'CODAGRUPACION','NOMAGRUPACION',$valDefCodAgrupa,$arrBind);	
	
 //echo "<br><br>3 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:parValor: ";print_r($parValor);
	if ($parValor['codError'] !=='00000')
	{
		//$parValor['arrMensaje']['textoCabecera'] = "Modelo usuarios"; 
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($codAgrupaPresCoord == '%')
	 {$parValor['lista']["%"] = "Todas"; 
	 } 
	}	
	//echo "<br><br>4 modelos/libs/arrayParValor.php:parValoresAgrupaPresCoord:parValor: ";print_r($parValor);
	
 return $parValor;
}
//------------------------------ Fin parValoresAgrupaPresCoord -----------------


/*---------------------------- Inicio parValoresAgrupaAreaGestionCoord ---------
Descripción: Genera un array asociativo $parValor con los campos de 
             CODAGRUPACION,NOMAGRUPACION de la tabla AGRUPACIONTERRITORIAL, 
													para utilizar en comboBox con las agrupaciones que pertenen a un 
													determinado área de gestión $codAreasGestionAgrup  
													como en el formulario "vMostrarSociosCoordInc-->forMostrarsociosCoord"
													
RECIBE: codAreasGestionAgrup, el código CODAREAGESTIONAGRUP de una AREAGESTIONAGRUPACIONESCOORD         
        o  "%" para en el caso de todas, 	$valDefCodAgrupa el valor  por defecto
DEVUELVE: array $parValor con datos seleccionados de la tabla AGRUPACIONTERRITORIAL
          si se ha encontrado la búsqueda, valor por defecto, $arrBind para la select
										y código de error.

LLMADA: cCoordinador.php:mostrarSociosCoord(),enviarEmailSociosCoord(),
               excelSociosCoord()              					
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES: solo usada en cCoordinador							
2020-03-06: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function parValoresAgrupaAreaGestionCoord($codAreasGestionAgrup,$codAgrupaPresCoord,$valDefCodAgrupa)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:codAreasGestionAgrup: ";print_r($codAreasGestionAgrup);
 //echo "<br><br>0-2 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:codAgrupaPresCoord: ";print_r($codAgrupaPresCoord);
	//echo "<br><br>0-3 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:valDefCodAgrupa: ";print_r($valDefCodAgrupa);

	$parValor['nomScript'] = "modelos/libs/arrayParValor.php"; 
	$parValor['nomFuncion'] = "parValoresAgrupaPresCoord";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';

	if ($codAreasGestionAgrup == '%' )
	{ /* si son todas las areas de gestión incluirá a todos la agrupaciones territoriales */

			$cadSql = " SELECT AGRUPACIONTERRITORIAL.CODAGRUPACION,AGRUPACIONTERRITORIAL.NOMAGRUPACION 
	                 FROM AGRUPACIONTERRITORIAL	                 				  		
																			  ORDER BY AGRUPACIONTERRITORIAL.NOMAGRUPACION ASC";																			
			$arrBind = array();																						
			//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:cadSql: ";print_r($cadSql);
 }
 else	
 { /* una determinada area de gestión solo incluirá a las agrupaciones territoriales correspondientes a ese área de gestión */
   $cadSql = " SELECT DISTINCT AGRUPACIONTERRITORIAL.CODAGRUPACION,AGRUPACIONTERRITORIAL.NOMAGRUPACION 
	                 FROM AREAGESTIONAGRUPACIONESCOORD,AGRUPACIONTERRITORIAL
	                 WHERE AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION 
														  		 AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = :codAreasGestionAgrup
																			  ORDER BY AGRUPACIONTERRITORIAL.NOMAGRUPACION ASC";
																										
   //echo "<br><br>2-1 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:cadSql: ";print_r($cadSql);
			
			$arrBind = array(':codAreasGestionAgrup' => $codAreasGestionAgrup );			
			
   //echo "<br><br>2-2 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:arrBind: ";print_r($arrBind);
 }	
	
	//echo "<br><br>3 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:cadSql: ";print_r($cadSql);										

	require_once './modelos/libs/arrayParValor.php';
 $parValor = arrayParValor($cadSql,'CODAGRUPACION','NOMAGRUPACION',$valDefCodAgrupa,$arrBind);	

/*
	if (!isset($valDefCodAgrupa) || empty($valDefCodAgrupa))//para mostrar por defecto "Elige agrupación"
	{$parValor['valorDefecto'] = "Elige";
		$parValor['descDefecto'] = "Elige agrupación";
	}	
*/
 
	//echo "<br><br>4 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:parValor: ";print_r($parValor);			
	
	if ($parValor['codError'] !== '00000')
	{
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($codAgrupaPresCoord == '%')
	 {$parValor['lista']["%"] = "Todas"; 
	 } 
	}	
	//echo "<br><br>5 modelos/libs/arrayParValor.php:parValoresAgrupaAreaGestionCoord:parValor: ";print_r($parValor);	
	
 return $parValor;
}
//------------------------------ Fin parValoresAgrupaAreaGestionCoord ----------

/*---------------------------- Inicio parValoresEstadosCuotas ------------------
Descripción: Genera un array asociativo $parValor con todos los posibles valores 
             para el estado de las cuotas
													
RECIBE: $valDefEstadoCuota el valor  por defecto
DEVUELVE: array $parValor con los distintos valores posibles datos ESTADOCUOTA 
          en  CUOTAANIOSOCIO

LLAMADA: cTesorero.php: excelCuotasInternoTesorero()             					
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES: solo usada en cTesorero.php							
2020-03-06: añadir comentarios									
------------------------------------------------------------------------------*/
function parValoresEstadosCuotas($valDefEstadoCuota)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresEstadosCuotas:valDefEstadoCuota: $valDefEstadoCuota";
	
	$parValor['nomScript'] = "modelos/libs/arrayParValor.php";
	$parValor['nomFuncion'] = "parValoresEstadosCuotas";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';

	$cadSql = " SELECT DISTINCT ESTADOCUOTA FROM CUOTAANIOSOCIO ";
	$arrBind = array();																			
	
	require_once './modelos/libs/arrayParValor.php';	

	$parValor = arrayParValor($cadSql,'ESTADOCUOTA','ESTADOCUOTA',$valDefEstadoCuota,$arrBind);

	if ($parValor['codError'] !== '00000')
	{		
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($valDefEstadoCuota == '%')
	 {$parValor['lista']["%"] = "Todas"; 
	 } 
	}
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresEstadosCuotas:parValor: ";print_r($parValor);
 	
 return $parValor;
}
/*------------------------------ Fin parValoresEstadosCuotas -------------------*/

/*------------------------------ Inicio parValoresDomPaisProvUser --------------
Genera un array asociativo parValor para utilizr en comboBox pais, provincia 
Pero solo de los que tengan correspondencia en miembros, para hacer la búsqueda
en el formulario "forMostrarSimps"

Llamado desde: cGestorSimps.php "mostrarSocios()

OBSERVACIONES: Actualmente no se usa				
------------------------------------------------------------------------------*/
function parValoresDomPaisProvUser($valDefDomicilioPais,$valDefDomicilioProv)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresDomPaisProvUser:$valDefDomicilioPais,$valDefDomicilioProv";

 $parValor['nomScript'] = "modelos/libs/arrayParValor.php";
	$parValor['nomFuncion'] = "parValoresDomPaisProvUser";	
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';
	
	$cadSqlPais ="SELECT DISTINCT CODPAIS1,NOMBREPAIS FROM MIEMBRO,SIMPATIZANTE,PAIS 
	               WHERE MIEMBRO.CODUSER = SIMPATIZANTE.CODUSER
																  AND MIEMBRO.CODPAISDOM = PAIS.CODPAIS1 
																		AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00') 
																			  ORDER BY NOMBREPAIS ASC";
																
	require_once './modelos/libs/arrayParValor.php';
	
 $parValor['domicilioPais'] = arrayParValor($cadSqlPais,'CODPAIS1','NOMBREPAIS',$valDefDomicilioPais);		
	
	if ($parValor['domicilioPais']['codError']!=='00000')
	{
		$parValor['codError'] = $parValor['domicilioPais']['codError'];
		$parValor['errorMensaje'] .= ". ". $parValor['domicilioPais']['errorMensaje'];
	}	
	
	$cadSqlProv ="SELECT DISTINCT PROVINCIA.CODPROV as CODPROV,PROVINCIA.NOMPROVINCIA FROM MIEMBRO,SIMPATIZANTE,PROVINCIA 
	               WHERE MIEMBRO.CODUSER = SIMPATIZANTE.CODUSER 
																    AND MIEMBRO.CODPROV = PROVINCIA.CODPROV 
																				AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00') 
																				  ORDER BY CODPROV ASC";
																
	$parValor['domicilioProvincia'] = arrayParValor($cadSqlProv,'CODPROV','NOMPROVINCIA',$valDefDomicilioProv);
	
	if ($parValor['domicilioProvincia']['codError']!=='00000')
	{
  $parValor['codError'] = $parValor['domicilioPais']['codError'];
		$parValor['errorMensaje'].= ". ". $parValor['domicilioPais']['errorMensaje'];
	}

	if ($parValor['codError'] !=='00000')
	{ 
	 //$parValor['arrMensaje']['textoCabecera'] ="Administrar usuarios"; 
	 $parValor['arrMensaje']['textoComentarios'] .="Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] ='./index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] ='Salir de la aplicación';

		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
 return $parValor;
}
//------------------------------ Fin parValoresDomPaisProvUser -----------------

/*------------------------------ Inicio parValoresRegistrarUsuario -------------
Genera un array asociativo parValor para utilizar en comboBox 
con datos de PAIS, AGRUPACION.

RECIBE: $valDefMiembroPais,$valDefDomicilioPais,$valDefAgrupa valores  por defecto
DEVUELVE: array $parValor 

Llamado desde casi todos los controladores:
contoladorSocios.php, cCoordinador.php, cPresidente.php, cTesorero.php,
y controladorSimpatizantes.php. 
Se utiliza en casi todas las funciones de los controladores, y Simpatizantes
LLAMA: modelos/libs/arrayParValor.php:arrayParValor()
									
OBSERVACIONES: 				
2020-038-06: Modifico agrupación para mostrar por defecto "Elige agrupación"
------------------------------------------------------------------------------*/
function parValoresRegistrarUsuario($valDefMiembroPais,$valDefDomicilioPais,$valDefAgrupa)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario: $valDefMiembroPais,$valDefDomicilioPais,$valDefAgrupa";
	
 require_once './modelos/libs/arrayParValor.php';
	
	$parValor = array();
 $parValor['nomScript'] = "modelos/libs/arrayParValor.php";	
	$parValor['nomFuncion'] = "parValoresRegistrarUsuario";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';	
 
	/*--------- Inicio para lista documento NIF, ...-----------*/
	
	$cadSql = "SELECT CODPAIS1, NOMBREPAIS FROM PAIS ORDER BY NOMBREPAIS ASC";		
	
	$parValor['miembroPais'] = arrayParValor($cadSql,'CODPAIS1','NOMBREPAIS',$valDefMiembroPais);
	
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario:parValor['miembroPais']: ";print_r($parValor['miembroPais']);

	if ($parValor['miembroPais']['codError'] !== '00000')
	{
		$parValor['codError'] = $parValor['miembroPais']['codError'];
		$parValor['errorMensaje'] = $parValor['miembroPais']['errorMensaje'];
	}
	/*--------- Fin para lista documento NIF, ...--------------*/	
		
	/*--------- Inicio para lista domicilio país ---------------*/
	
	$cadSql = "SELECT CODPAIS1, NOMBREPAIS FROM PAIS ORDER BY NOMBREPAIS ASC ";			
	
	$parValor['domicilioPais'] = arrayParValor($cadSql,'CODPAIS1','NOMBREPAIS',$valDefDomicilioPais);
	
	//echo "<br><br>2 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario:parValor['domicilioPais']: ";print_r($parValor['domicilioPais']);

	if ($parValor['domicilioPais']['codError'] !== '00000')
	{
		$parValor['codError'] = $parValor['domicilioPais']['codError'];
		$parValor['errorMensaje'] .= ". ". $parValor['domicilioPais']['errorMensaje'];
	}	
 //unset($parValor['domicilioPais']['lista']['ES']);
	//$parValor['domicilioPais']['lista']['-']='---';
	//$parValor['domicilioPais']['lista']['ES']='---';

 /*--------- Fin para lista domicilio país ------------------*/		
		
	
	/*--------- Inicio para lista agrupaciones ----------------*/

	//$cadSql = "SELECT CODAGRUPACION, NOMAGRUPACION FROM AGRUPACIONTERRITORIAL ORDER BY NOMAGRUPACION ASC ";		

	$cadSql = "SELECT CODAGRUPACION, NOMAGRUPACION FROM AGRUPACIONTERRITORIAL ORDER BY CODPAISDOM DESC, NOMAGRUPACION ASC";	
	
											/*	"AGRUPACIONTERRITORIAL.CODPAISDOM DESC"; es para que agrupación Europa Laica Estatal e Internacional salga la última	
																ya que CODPAISDOM = '--'*/		
		
	$parValor['agrupaSocio'] = arrayParValor($cadSql,'CODAGRUPACION','NOMAGRUPACION',$valDefAgrupa);

	if (!isset($valDefAgrupa) || empty($valDefAgrupa))//para mostrar por defecto "Elige agrupación"
	{$parValor['agrupaSocio']['valorDefecto'] = "Elige";
		$parValor['agrupaSocio']['descDefecto'] = "Elige agrupación";
	}	
		
	//echo "<br><br>3 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario:parValor['agrupaSocio']: ";print_r($parValor['agrupaSocio']);
	
	if ($parValor['agrupaSocio']['codError'] !== '00000')
	{
		$parValor['codError'] = $parValor['agrupaSocio']['codError'];
		$parValor['errorMensaje'] .= ". ". $parValor['agrupaSocio']['errorMensaje'];
	}	
	/*--------- Fin para lista agrupaciones -------------------*/
	
	//echo "<br><br>4 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario :parValor";print_r($parValor);echo "<br><br>";
	
	if ($parValor['codError'] !== '00000')
	{ 
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';

		require_once './modelos/modeloErrores.php';	
		insertarError($parValor,$conexionDB['conexionLink']);		
	}	

	//echo "<br><br>5 modelos/libs/arrayParValor.php:parValoresRegistrarUsuario:parValor: ";print_r($parValor);echo "<br><br>";
	
 return $parValor;
}
/*---------------------------- Fin parValoresRegistrarUsuario ---------------*/


/*---------------------------- Inicio parValoresDonacionConceptos -------------
Descripción: Genera un array asociativo $parValor con todos los posibles valores 
             para 'CONCEPTO'=>'NOMBRECONCEPTO'
													
RECIBE: $valDefDonacionConcepto el valor  por defecto "GENERAL, u otros
DEVUELVE: array $parValor con los distintos valores de 'CONCEPTO'=>'NOMBRECONCEPTO'
          en la tabla "DONACIONCONCEPTOS"

LLAMADA: cTesorero.php: anotarIngresoDonacionMenu(),comprobarDonantePrevio_Socio(),anotarIngresoDonacion()             					
LLAMA: modelos/libs/arrayParValor.php:arrayParValorCadSql()
									
OBSERVACIONES: solo usada en cTesorero.php									
------------------------------------------------------------------------------*/
function parValoresDonacionConceptos($valDefDonacionConcepto)
{
	//echo "<br><br>0-1 modelos/libs/arrayParValor.php:parValoresDonacionConceptos:valDefDonacionConcepto: $valDefDonacionConcepto";
	
	$parValor['nomScript'] = "modelos/libs/arrayParValor.php";
	$parValor['nomFuncion'] = "parValoresDonacionConceptos";
	$parValor['codError'] = '00000';
	$parValor['errorMensaje'] = '';

	$cadSql = " SELECT * FROM DONACIONCONCEPTOS ORDER BY FECHACREACIONCONCEPTO ASC";
	$arrBind = array();																			
	
	require_once './modelos/libs/arrayParValor.php';	
	
	$parValor = arrayParValor($cadSql,'CONCEPTO','NOMBRECONCEPTO',$valDefDonacionConcepto,$arrBind);	

	if ($parValor['codError'] !== '00000')
	{		
	 $parValor['arrMensaje']['textoComentarios'] .= "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$parValor['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$parValor['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		require_once './modelos/modeloErrores.php';
		insertarError($parValor);		
	}	
	else
	{
		if ($valDefDonacionConcepto == '%')
	 {$parValor['lista']["%"] = "Todas"; 
	 } 
	}
	//echo "<br><br>1 modelos/libs/arrayParValor.php:parValoresDonacionConceptos:parValor: ";print_r($parValor);
 	
 return $parValor;
}
/*------------------------------ Fin parValoresDonacionConceptos ------------*/


/*===================== FIN parValor_Funciones() =============================*/