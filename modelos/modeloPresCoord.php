<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: modeloPresCoord.php

VERSION: PHP 7.3.21
DESCRIPCION: Contiene funciones relaccionadas con las tareas de gestores
LLAMADO: desde cPresidente.php, cCoordinador.php, cTesororeo.php:bajaSocioFallecido(), ....
																				 
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
require_once "BBDD/MySQL/conexionMySQL.php";
require_once "BBDD/MySQL/modeloMySQL.php";


/*-------------------------- Inicio actualizSocioPorCoord  ES POSIBLE QUE YA NO SE UTILICE --------
Descripción: Actualiza la tabla "SOCIO", pero se deja como están las c. bancos (CCES y CEX)
             puesto que los coordinadores no pueden cambiarlas
													
Llamada desde:modeloPresCoord.php:actualizarDatosSocioPorCoord()
Llama: modeloUsuarios.php:actualizarTabla()

NOTA: ES POSIBLE QUE YA NO SE UTILICE ESTA FUNCIÓN EN NINGUN LUGAR
--------------------------------------------------------------------------------------*/       
function actualizSocioPorCoord($tablaAct,$camposCondiciones,$arrayDatosAct)// NOTA: ES POSIBLE QUE YA NO SE UTILICE    
{//echo "<br><br>1-1 actualizSocioPorCoord:arrayDatosAct:";print_r($arrayDatosAct);
 $arrayCondiciones['CODUSER']['valorCampo']= $camposCondiciones;
 $arrayCondiciones['CODUSER']['operador']= '=';
 $arrayCondiciones['CODUSER']['opUnir']= ' ';  

	foreach ($arrayDatosAct as $indice => $contenido)                         
 {      
   $arrayDatos[$indice] = $contenido['valorCampo']; 
 }
	//echo "<br><br>1-2 actualizSocioPorCoord:arrayDatos:";print_r($arrayDatos);
	//unset($arrayDatos['ctaBanco']);sobra se eliminado antes
	//unset($arrayDatos['CCEXTRANJERA']);sobra se eliminado antes
	
 //echo '<br><br>2 actualizSocioPorCoord:arrayDatos';print_r($arrayDatos);	
			
	$resActualizarSocio = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos); 																					
	//echo '<br><br>3 actualizSocioPorCoord:resActualizarSocio';print_r($resActualizarSocio);	
	return $resActualizarSocio;
 } 
/*----------------------------- Fin actualizSocioPorCoord ---------------------------------------*/

/*---------------------------- Inicio buscarDatosSocioCoord ---------------------------------------
Busca todos los datos de un solo socio, para mostrarlos a un coordinador.
Antes se sustituía de num. de cuenta del banco por ***** por seguridad, pero no era operativo 
para coordinadores, por eso lo comenté.
 
RECIBE: $codUsuario = CODUSER y $anioCuota
DEVUELVE: array $resBuscarDatosSocio con los datos del socio y códigos de error

LLAMADA: cCoordinador.php:mostrarDatosSocioCoord(),actualizarSocioCoord(),y eliminarSocioCoord()
LLAMA: modeloSocios.php:buscarDatosSocio($codUsuario,$anioCuota)

OBSERVACIONES: No se necesita modificar para incluir PPDOStatement::bindParamValue ya lo 
incluye buscarDatosSocio($codUsuario,$anioCuota)	

Es una llamada a modeloSocios.php:buscarDatosSocio($codUsuario,$anioCuota), pero permitiría ocultar
o munipular ciertos campos, por eso lo dejo 
--------------------------------------------------------------------------------------------------*/
function buscarDatosSocioPorCoord($codUsuario,$anioCuota)
{	
	$resBuscarDatosSocio['nomScript'] = 'modeloPresCoord.php';
 $resBuscarDatosSocio['nomFuncion'] = 'buscarDatosSocioPorCoord';//se van a repetir
	$resBuscarDatosSocio['codError'] = '00000';
 $resBuscarDatosSocio['errorMensaje'] = '';

	
 require_once './modelos/modeloSocios.php';
	$buscarDatosSocio = buscarDatosSocio($codUsuario,$anioCuota);//controla errores e inserta error
 
	//echo "<br><br>2 modeloPresCoord:buscarDatosSocioCoord:resDatosSocio:"; print_r($resBuscarDatosSocio); 

 if ($buscarDatosSocio['codError'] !== '00000') 
 { $resBuscarDatosSocio['codError'] = $buscarDatosSocio['codError']; 
	 	$resBuscarDatosSocio['errorMensaje'] = $resBuscarDatosSocio['nomScript'].":".$resBuscarDatosSocio['nomFuncion'].":".$buscarDatosSocio['errorMensaje'];
 }      
 else //$resDatosSocio['codError']=='00000'
 {	
  $resBuscarDatosSocio = $buscarDatosSocio;
	
	 // ---Ahora no se utiliza: Lo dejo como ejemplo por siquisiera utilizar más adelante con IBAN -----------
  //echo "<br><br>3 modeloPresCoord:buscarDatosSocioCoord:resBuscarDatosSocio['NUMCUENTA']: ";
		//print_r($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']);
	
 /*----- Inicio mostrar solo los 4 últimos dígitos de la cuenta bancaria -----
		if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']) &&
		    !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']))
		{//--- a los coordinadores no se les muestra la CC completa por seguridad, se ponen ***** -------
		 $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']=
		  substr_replace($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo'],'******',0,6);
					
   //echo "<br><br>4-1 modeloPresCoord:buscarDatosSocioCoord:resDatosSocio:"; 
	  //print_r($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo'] );				
		}
		elseif (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo']) &&
		    !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo']))
		{//--- a los coordinadores no se les muestra la CC completa por seguridad, se ponen ***** -------
			$longitudCad = strlen($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo']);
			$numCaracCortar = $longitudCad - 5;
			
			$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo'] ="***************". 			
			 substr($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo'],$numCaracCortar);
				
   //echo "<br><br>4-2 modeloPresCoord:buscarDatosSocioCoord:resDatosSocio:"; 
	  //print_r($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo'] );			
		}
	----- Fin mostrar solo los 4 últimos dígitos de la cuenta bancaria -------- */
	
	}//$resDatosSocio['codError']=='00000'
 
	return 	$resBuscarDatosSocio; 	
}
/*------------------------------ Fin buscarDatosSocioCoord ---------------------------------------*/

/*---------------------------- Inicio buscarAreaGestionCoord ---------------------------------------
Busca en la tablas COORDINAAREAGESTIONAGRUP, AREAGESTION' el área de gestión y el nombre de ese área
de gestión para un $codUser. Si no lo encuentra devuele error

RECIBE: $codUser 	
DEVUELVE: un array los campos de área de gestión:
[EMAIL],NOMBREAREAGESTION],[CODAREAGESTIONAGRUP],[OBSERVACIONES] y códigos error
																												
LLAMADO: cCoordinador.php:mostrarSociosCoord(),enviarEmailSociosCoord(),excelSociosCoord(),
buscarAreaGestionCoordRol(),

LLAMA: modeloMySQL.php:buscarCadSql(),
usuariosConfig/BBDD/MySQL/configMySQL.php,conexionMySql.ph:conexionDB()
modelos/modeloErrores.php:insertarError()

OBSERVACIONES:
2020-04-09: Adapto para PDO:bindParaValue

NOTA: Aunque un socio podría ser coordinador de más de un área, según la tabla 
COORDINAAREAGESTIONAGRUP, por ahora sólo está programado para tratar solo un área de gestión 
(solo una fila:[0]), en caso de pensar en más de un área habría que adaptar la programación    
------------------------------------------------------------------------------------------------*/
function buscarAreaGestionCoord($codUser)
{
	//echo "<br /><br />0-1 modeloPresCoord.php:buscarAreaGestionCoord:_SESSION: ";print_r($_SESSION); 
	//echo "<br /><br />0-2 modeloPresCoord.php:buscarAreaGestionCoord:codUser: ";print_r($codUser);
	
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$areaGestionCoord['nomScript'] = "modeloPresCoord.php";
	$areaGestionCoord['nomFuncion'] = "buscarAreaGestionCoord";	
	$areaGestionCoord['codError'] = '00000';
	$areaGestionCoord['errorMensaje'] = '';
 $areaGestionCoord['textoComentarios'] = '';
	$arrMensaje['textoCabecera'] = 'Buscar datos del área de gestión del coordinador';
	$arrMensaje['textoComentarios'] = '';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{ $areaGestionCoord = $conexionDB;   
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{
		$tablasBusqueda = 'COORDINAAREAGESTIONAGRUP, AREAGESTION';
		
		//$camposBuscados = 'AREAGESTION.*,COORDINAAREAGESTIONAGRUP.*';		
		$camposBuscados = 'AREAGESTION.EMAIL, AREAGESTION.NOMAREAGESTION as NOMBREAREAGESTION, AREAGESTION.CODAREAGESTION as Cod,
		                    COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP,COORDINAAREAGESTIONAGRUP.OBSERVACIONES';
																					
		$cadCondicionesBuscar = " WHERE AREAGESTION.CODAREAGESTION = COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP 
		                           AND COORDINAAREAGESTIONAGRUP.CODUSER = :codUser";																													
																													
  $arrBind = array(':codUser' => $codUser); 
		
		//$reAreaGestionCoord = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionUsuariosDB['conexionLink'],$arrBind);//anterior		
			
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		//echo "<br /><br />1 modeloPresCoord.php:buscarAreaGestionCoord:cadSql: ";print_r($cadSql);
		
		$reAreaGestionCoord = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//modeloMySQL.php";		
	
  //echo "<br /><br />2 modeloPresCoord.php:buscarAreaGestionCoord:reAreaGestionCoord: ";print_r($reAreaGestionCoord);
		
  if ($reAreaGestionCoord['codError'] !== '00000')
  {$areaGestionCoord['codError'] = $reAreaGestionCoord['codError'];	
   $areaGestionCoord['errorMensaje']	= $areaGestionCoord['nomScript'].":".$areaGestionCoord['nomFuncion'].": ".$reAreaGestionCoord['errorMensaje'];
	  $areaGestionCoord['textoComentarios'] = $areaGestionCoord['nomScript'].":".$areaGestionCoord['nomFuncion'].
			                                        " Error del sistema al buscar datos del área de gestión del coordinador/a, vuelva a intentarlo pasado un tiempo ";
		 insertarError($areaGestionCoord);		
  }
  elseif ($reAreaGestionCoord['numFilas'] == 0)
	 {$areaGestionCoord['numFilas'] = 0;
			$areaGestionCoord['codError'] = '80001'; //no encontrado	 
		 $areaGestionCoord['errorMensaje']	= $areaGestionCoord['nomScript'].":".$areaGestionCoord['nomFuncion']."No existe ese coordinador/a de área ".$codUser;
			$areaGestionCoord['textoComentarios'] = $areaGestionCoord['nomScript'].":".$areaGestionCoord['nomFuncion']."No existe ese coordinador/a de área ".$codUser;
			insertarError($areaGestionCoord);
  }
  else
  { //Se podría poner más de un área, no solo una: [0] pero habría que tratarlo posteriormente
		  foreach ($reAreaGestionCoord['resultadoFilas'][0] as $indice => $contenido)                        
    {      
      $areaGestionCoord['resultadoFilas'][$indice] = $contenido; 
    }
  }
	}
	//echo "<br /><br />3-1 modeloPresCoord.php:buscarAreaGestionCoord:areaGestionCoord: ";print_r($areaGestionCoord);
	
	$areaGestionCoord['arrMensaje'] = $arrMensaje['textoComentarios'];
	
	//echo "<br /><br />3-2 modeloPresCoord.php:buscarAreaGestionCoord:areaGestionCoord: ";print_r($areaGestionCoord);
	
 return $areaGestionCoord;
}
/*------------------ Fin buscarAreaGestionCoord --------------------------------------------------*/


/*------------------------------ Inicio buscarAreaGestionCoordRo ----------------------------------
Busca si tiene rol de coordinador=6 en la tabla 'USUARIOTIENEROL', en caso de que lo encuentre 
busca en la tablas COORDINAAREAGESTIONAGRUP, AREAGESTION' el área de gestión y el nombre de ese
área de gestión

RECIBE:codUser
DEVUELVE: un array asociativo: 
$areaGestionCoord['resultadoFilas']:['CODUSER'],['CODAREAGESTIONAGRUP'], 
['NOMBREAREAGESTION'] y ['esCoordinador'] mas lo de error
 																														
LLAMADA: cCoordinador.php:excelSociosCoord()
cPresidente.php:asignarCoordinacionAreaBuscar()
LLAMA: modeloUsuarios.php:buscarRolesUsuario(), modeloPresCoord.php:buscarAreaGestionCoord();
							
OBSERVACIONES:
No necesita adaptase para PDO, lo inclueyen internamente las funciones llamadas							
-----------------------------------------------------------------------------------------------*/
function buscarAreaGestionCoordRol($codUser)
{
	//echo "<br /><br />0-1 modeloPresCoord.php:buscarAreaGestionCoordRol:codUser: ";print_r($codUser);
 
	$areaGestionCoord['nomScript'] = "modeloPresCoord.php";
	$areaGestionCoord['nomFuncion'] = "buscarAreaGestionCoordRol";	
	$areaGestionCoord['codError'] = '00000';
	$areaGestionCoord['errorMensaje'] = ''; 
	$arrMensaje['textoCabecera'] = 'Buscar datos del área de gestión del coordinador';

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== '00000')	
	{ $areaGestionCoord = $conexionDB;  
	}
	else	//$conexionDB['codError']=='00000'
	{
		require_once './modelos/modeloUsuarios.php';
		$resSocioRoles = buscarRolesUsuario($codUser);
  
		//echo "<br /><br />1 modeloPresCoord.php:buscarAreaGestionCoordRol:resSocioRoles: ";print_r($resSocioRoles);
		
  if ($resSocioRoles['codError'] !== '00000')
  {$areaGestionCoord['codError'] =	$resSocioRoles['codError'];		 
	  $areaGestionCoord['errorMensaje'] = $resSocioRoles['errorMensaje'];					
			$areaGestionCoord['textoComentarios'] = "Error del sistema al buscar datos área de gestión del coordinador/a,
	                                          vuelva a intentarlo pasado un tiempo. ".$resSocioRoles['resultadoFilas']['CODROL']['errorMensaje'];						
			require_once './modelos/modeloErrores.php';
		 insertarError($areaGestionCoord);		
  }	
		else //$resSocioRoles['codError']=='00000')
		{
			$esCoordinador = "NO";

   foreach ($resSocioRoles['resultadoFilas'] as $index => $contenido)			
	  {
			 if ($contenido['CODROL'] === '6')//Si ya es coordinador='6'
			 {	$esCoordinador = "SI";		
				}				
			}

	  if ($esCoordinador == "SI")//$esCoordinador=="SI"
			{
			 
				$reAreaGestionCoord = buscarAreaGestionCoord($codUser);//función está en modeloPresCoord.php, inserta errores 
				
				//echo "<br /><br />2 modeloPresCoord.php:buscarAreaGestionCoordRol:reAreaGestionCoord: ";print_r($reAreaGestionCoord);
				
				$areaGestionCoord =	$reAreaGestionCoord;//ya incluye errores y mensajes
				
			}//if $esCoordinador=="SI"	
		}//else $resSocioRoles['codError']=='00000'		
	}//else $conexionDB['codError']=='00000'
	
	$areaGestionCoord['resultadoFilas']['esCoordinador'] = $esCoordinador;
 
	//echo "<br /><br />3 modeloPresCoord.php:buscarAreaGestionCoordRol:areaGestionCoord: ";print_r($areaGestionCoord);
	
 return $areaGestionCoord;
}
/*------------------------------ Fin buscarAreaGestionCoordRol ----------------------------------*/


//========================== INICIO SOCIOSFALLECIDOS ==============================================

/*---------------------------- Inicio function cadBuscarSociosFallecidos --------------------------
Forma la cadena select sql para busca los datos de los SOCIOSFALLECIDOS elegido 
por una determinada agrupación territorial

RECIBE: $codAgrup (codigo agrupación),$anioBaja	 (del socio fallecido)								
DEVUELVE: $arrBuscarSociosFallecidos que contiene ['cadSQL'] y ['arrBindValues']	
													
LLAMADA: cPresidenteSociosFellecApeNomPaginarInc.php para pasarla a la función
									cPresidente.php:mostrarSociosFallecidosPres() para 
         "/modelos/libs/mPaginarLib.php"

OBSERVACIONES: 
modifico para incluir PHP: PDOStatement::bindParamValue. PHP 7.3.21	

actualmente recibe siempre $codAgrup='%' ya que el presidente es único para 
todas las agrupaciones, pero se ha dejado la opción para si pudiera haber 
tesoreros de cada agrupación.															
----------------------------------------------------------------------------------------------*/
function cadBuscarSociosFallecidos($codAgrup,$anioBaja)
{
	//echo	"<br>br>0-1 modeloPresCoord.php:cadBuscarSociosFallecidos:codAgrup: ";print_r($codAgrup);
	//echo	"<br>br>0-2 modeloPresCoord.php:cadBuscarSociosFallecidos:anioBaja: ";print_r($anioBaja);
	
	if ( !isset($codAgrup) || empty($codAgrup) || $codAgrup =='%' )
 { 
  //$condicionAgrup = '';
			$condicionAgrup = " SOCIOSFALLECIDOS.CODAGRUPACION LIKE :codAgrup";	
			
			$arrBind[':codAgrup'] = $codAgrup;
 }   
	else 
	{ 
   $condicionAgrup = " SOCIOSFALLECIDOS.CODAGRUPACION = :codAgrup ";	
   
			$arrBind[':codAgrup'] = $codAgrup;
 }
 
	$condicionAreaCoordinacion = '';//por si más adelente se decide incluir, y debiera venir como parámetro
	
	//$anioBaja = '2019'; para probar
	
	if ( !isset($anioBaja) || empty($anioBaja) || $anioBaja =='%')
 { $cadenaAnioBaja = '';	
 }
	else
	{ //$cadenaAnioBaja = " AND SUBSTRING(SOCIOSFALLECIDOS.FECHABAJA,1,4)='2017' = '$anioBaja' ";
   //$cadenaAnioBaja = " AND SUBSTRING(SOCIOSFALLECIDOS.FECHABAJA,1,4)='2017' = :anioBaja ";
			$cadenaAnioBaja = " AND SUBSTRING(SOCIOSFALLECIDOS.FECHABAJA,1,4) = :anioBaja ";
			
			$arrBind[':anioBaja'] = $anioBaja;
	}		

	$tablasBusqueda = " SOCIOSFALLECIDOS ";
	
	$camposBuscados = " SOCIOSFALLECIDOS.*, 	UPPER(CONCAT(SOCIOSFALLECIDOS.APE1,' ',IFNULL(SOCIOSFALLECIDOS.APE2,''),', ',SOCIOSFALLECIDOS.NOM )) as apeNom	 ";

 $cadCondicionesBuscar =" WHERE ".$condicionAreaCoordinacion.$condicionAgrup.$cadenaAnioBaja.
																								" ORDER BY apeNom";										

	$cadSelectSociosFallecidos = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
	
	$arrBuscarSociosFallecidos['cadSQL'] = $cadSelectSociosFallecidos;

 $arrBuscarSociosFallecidos['arrBindValues']	= $arrBind;
																																										
	
	//echo	"<br>br>1 modeloPresCoord.php:cadBuscarSociosFallecidos:arrBuscarSociosFallecidos: ";print_r($arrBuscarSociosFallecidos);

	return $arrBuscarSociosFallecidos;
}
/*------------------------------ Fin function cadBuscarSociosFallecidos --------------------------*/

/*---------------------- Inicio cadBuscarSociosFallecidosApeNom -----------------------------------
Forma la cadena select sql para busca los datos de las SOCIOSFALLECIDOS elegido 
por Ape1 yApe2

RECIBE: cadApe1,$cadApe2 (apellidos) y $codAgrup (codigo agrupación),
$anioBaja	(del socio fallecido)			
DEVUELVE: $arrBuscarSociosFallecidos que contiene ['cadSQL'] y ['arrBindValues']	

LLAMADA: cPresidenteSociosFellecApeNomPaginarInc.php para pasarla a la función
									cPresidente.php:mostrarSociosFallecidosPres() para 
         "/modelos/libs/mPaginarLib.php"
LLLAMA: modelos/libs/eliminarAcentos.php:cambiarAcentosEspeciales()

OBSERVACIONES: 
modifico para incluir PHP: PDOStatement::bindParamValue. PHP 7.3.21	

actualmente recibe siempre $codAgrup='%' ya que el presidente es único para 
todas las agrupaciones, pero se ha dejado la opción para si pudiera haber 
tesoreros de cada agrupación.															
--------------------------------------------------------------------------------------------*/
function cadBuscarSociosFallecidosApeNom($cadApe1,$cadApe2,$codAgrup='%',$anioBaja='%')
{
	//echo	"<br>br>0-1 modeloPresCoord.php:cadBuscarSociosFallecidosApeNom:cadApe1: ";print_r($cadApe1);
	//echo	"<br>br>0-2 modeloPresCoord.php:cadBuscarSociosFallecidosApeNom:cadApe2: ";print_r($cadApe2);
	//echo	"<br>br>0-1 modeloPresCoord.php:cadBuscarSociosFallecidosApeNom:codAgrup: ";print_r($codAgrup);
	//echo	"<br>br>0-2 modeloPresCoord.php:cadBuscarSociosFallecidosApeNom:anioBaja: ";print_r($anioBaja);
	
	require_once './modelos/libs/eliminarAcentos.php';

	if ( !isset($codAgrup) || empty($codAgrup) || $codAgrup =='%' )
 { 
   //$condicionAgrup = '';		  
	  $condicionAgrup = " SOCIOSFALLECIDOS.CODAGRUPACION LIKE :codAgrup";	
			
			$arrBind[':codAgrup'] = $codAgrup;		
 }   
	else 
	{ 
   $condicionAgrup = " SOCIOSFALLECIDOS.CODAGRUPACION = :codAgrup ";	
   
			$arrBind[':codAgrup'] = $codAgrup;
 }

 $cadenaAreaCoordinacion ='';//por si más adelente se decide incluir, y debiera venir como parámetro
			
 if ( !isset($anioBaja) || empty($anioBaja) || $anioBaja =='%')
 { $cadenaAnioBaja = '';	}
	else
	{ 
   $cadenaAnioBaja = " AND SUBSTRING(SOCIOSFALLECIDOS.FECHABAJA,1,4) = :anioBaja ";
			
			$arrBind[':anioBaja'] = $anioBaja;
 }		

	if ( !isset($cadApe1) || empty($cadApe1))
 { $cadenaApe1 = '';	
	}
	else
	{$cadApe1 = cambiarAcentosEspeciales($cadApe1);
		
  //$cadenaApe1 =" AND (SOCIOSFALLECIDOS.APE1 LIKE '$cadApe1') ";	
		$cadenaApe1 =" AND (SOCIOSFALLECIDOS.APE1 LIKE :cadApe1 ) ";
			
		$arrBind[':cadApe1'] = $cadApe1;		
	}	
	
	if ( !isset($cadApe2) || empty($cadApe2))
 { $cadenaApe2 = '';																	
	}
	else
	{$cadApe2 = cambiarAcentosEspeciales($cadApe2);
		
  //$cadenaApe2 =" AND (SOCIOSFALLECIDOS.APE2 LIKE '$cadApe2') ";		
		$cadenaApe2 =" AND (SOCIOSFALLECIDOS.APE2 LIKE :cadApe2 ) ";
			
		$arrBind[':cadApe2'] = $cadApe2;				
	}
	
	$tablasBusqueda =" SOCIOSFALLECIDOS ";
	
	$camposBuscados =" SOCIOSFALLECIDOS.*, UPPER(CONCAT(SOCIOSFALLECIDOS.APE1,' ',IFNULL(SOCIOSFALLECIDOS.APE2,''),', ',SOCIOSFALLECIDOS.NOM )) as apeNom	 ";

 $cadCondicionesBuscar =" WHERE ".$cadenaAreaCoordinacion.$condicionAgrup.$cadenaApe1.$cadenaApe2.$cadenaAnioBaja.																																												
																										" ORDER BY apeNom";

	$cadSelectSociosFallecidos = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
	
	$arrBuscarSociosFallecidos['cadSQL'] = $cadSelectSociosFallecidos;

 $arrBuscarSociosFallecidos['arrBindValues']	= $arrBind;
																																										
	
	//echo	"<br>br>1 modeloPresCoord.php:cadBuscarSociosFallecidosApeNom:arrBuscarSociosFallecidos: ";print_r($arrBuscarSociosFallecidos);

	return $arrBuscarSociosFallecidos;
}
/*------------------------------ Fin cadBuscarSociosFallecidosApeNom -----------------------------*/

/*------------------------------ Inicio bajaSocioFallecido ----------------------------------------
En esta función es específica para baja de socios por fallecimiento.
Primero es igual a la función "modeloSocio.php:eliminarDatosSocios()" donde se eliminan 
los datos personales del socio a dar de baja, y se insertan los datos necesarios 
en MIEMBROELIMINADO5ANIOS para los datos fiscales, al cabo de 5 años se eliminarán.
En caso de que hubiese un archivo con la firma de un socio debido a lata por gestor, también
se eliminaría el archivo del servidor.

Pero además se insertan ciertos datos en la tabla SOCIOSFALLECIDOS
Se controlan transacciones con transationPDO.php.
								
RECIBE:$datosUsuarioEliminar: array con los campos hide de los form de eliminar socios y otros
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: cPresidente:eliminarSocioPres(), cCoordinador.php:eliminarSocioCoord(),
cTesorero.php:eliminarSocioTes()
LLAMA: BBDD/MySQL/conexionMySQL.php:conexionDB(),
transationPDO.php:beginTransationPDO,commitPDO y rollbackPDO.
modeloUsuarios.php:buscarUnRolUsuario(),eliminarUsuarioTieneRol(),
actUsuarioEliminar(),insertarMiembroEliminado5Anios()
modeloSocios.php:actSocioEliminar(),buscarDatosSocio(),actualizCuotaAnioSocio()
modeloPresCoord.php:eliminarCoordinacionAreaGestion(),insertarSocioFallecido()
modeloArchivos.php:eliminarArchivo()
modeloErrores.php:insertarError()

OBSERVACIONES: probado PHP 7.3.21
2020-03-25: Incluye PHP: PDOStatement::bindParam, aunque la mayoria de funciones llamadas 
aquí tratan internamente
-------------------------------------------------------------------------------------------*/
function bajaSocioFallecido($datosUsuarioEliminar)
{
	//echo "<br><br>0-1 modeloPresCoord:bajaSocioFallecido:datosUsuarioEliminar: ";print_r($datosUsuarioEliminar);

 require_once './modelos/modeloUsuarios.php';
	require_once './modelos/modeloSocios.php';

	$resEliminarSocio['nomScript'] = "modeloPresCoord.php";
	$resEliminarSocio['nomFuncion'] = "bajaSocioFallecido";	
 $resEliminarSocio['codError'] = '00000';
 $resEliminarSocio['errorMensaje'] = '';
	 	 
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resEliminarSocio = $conexionDB;
	}
	else //$conexionDB['codError']=="00000"
	{
		/*--- Inicio de punto de rollback para transación --------------------------*/
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloPresCoord:bajaSocioFallecido:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";
	
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
	 { $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;
			 $resEliminarSocio = $resIniTrans;			
	 }	
		else //$resIniTrans['codError'] == '00000'
		{$usuarioBuscado =  $datosUsuarioEliminar['datosFormUsuario']['CODUSER'];
			
			/*---------------- Inicio buscar si es si es coordinador --------------------
			 Buscar si es si es coordinador, Si es coordinador y si lo es hay que borrale 
				también de tabla "COORDINAAREAGESTIONAGRUP"
   	--------------------------------------------------------------------------*/	
   $codRolBuscar = '6';	//6 es rol de coordinador 
			
	  $rolCoordinadorExiste = buscarUnRolUsuario($usuarioBuscado, $codRolBuscar);//en modeloUsuarios.php, para Si tiene rol de coordinador borrarlo de tabla COORDINAAREAGESTIONAGRU
			
			//echo "<br><br>2-1 modeloPresCoord:bajaSocioFallecido:rolCoordinadorExiste: ";print_r($rolCoordinadorExiste);
			
			if ($rolCoordinadorExiste['codError'] !== '00000')//'error sintaxis, error conexion, 
   { $resEliminarSocio = $rolCoordinadorExiste; 			
   }
		 else
			{$resEliminarCoordAreaGestion['codError'] = '00000';//Necesario por si el socio que es baja no es coordinador y no entra en el siguiente if
			
			 if ($rolCoordinadorExiste['numFilas'] > 0)//es coordinador de área territorial => hay que borrar Area asignada
			 {
					$datosCoordEliminar['CODUSER'] = $usuarioBuscado;
					$datosCoordEliminar['CODAREAGESTIONAGRUP'] = '%'; //todos;
				
					$resEliminarCoordAreaGestion = eliminarCoordinacionAreaGestion('COORDINAAREAGESTIONAGRUP',$datosCoordEliminar,$conexionDB['conexionLink']);//modeloPresCoord.php, borrar de tabla COORDINAAREAGESTIONAGRU 					

					//echo "<br><br>2-2 modeloPresCoord:bajaSocioFallecido:resEliminarCoordAreaGestion: ";print_r($resEliminarCoordAreaGestion);
							
					if ($resEliminarCoordAreaGestion['codError'] !== '00000')//probado error			
					{$resEliminarSocio = $resEliminarCoordAreaGestion;
					}
					elseif ($resEliminarCoordAreaGestion['numFilas'] <= 0)//probado error			
					{ $resEliminarSocio['codError'] = '80001';
							$resEliminarSocio['errorMensaje'] = 'No se pudo eliminar el coordinador de de la tabla COORDINAAREAGESTIONAGRUP';
							$resEliminarCoordAreaGestion = $resEliminarSocio;
					}
			 }/*----------------- Fin buscar si es si es coordinador --------------------*/			
				
				if ($resEliminarCoordAreaGestion['codError'] == '00000')			
			 {
					$codRol = '%';//ELIMINAR todos los roles del usuario
	
					$resRolEliminar = eliminarUsuarioTieneRol('USUARIOTIENEROL',$usuarioBuscado,$codRol,$conexionDB['conexionLink']);//en modeloUsuarios.php
     //echo "<br><br>2-3 modeloPresCoord:bajaSocioFallecido:reRolEliminar: ";print_r($reRolEliminar);

			  if ($resRolEliminar['codError'] !== '00000')			
		   {$resEliminarSocio = $resRolEliminar;
	    }	
					elseif ($resRolEliminar['numFilas'] <= 0)
					{ $resEliminarSocio['codError'] = '80001';
							$resEliminarSocio['errorMensaje'] = 'No se pudo eliminar rol de la tabla USUARIOTIENEROL';
					}								
				 else //$resRolEliminar['codError']=='00000')//el usuario hay que ponerlo a baja
				 {
						$resActUsuarioEliminar = actUsuarioEliminar('USUARIO',$usuarioBuscado,$conexionDB['conexionLink']);//en modeloUsuarios.php,       
			    		
      //echo "<br><br>2-4 modeloPresCoord:bajaSocioFallecido:reActUsuarioEliminar: ";print_r($resActUsuarioEliminar);					
				  if ($resActUsuarioEliminar['codError'] !== '00000')			
			   {$resEliminarSocio = $resActUsuarioEliminar;
	     }
						elseif ($resActUsuarioEliminar['numFilas'] <= 0)
						{ $resEliminarSocio['codError'] = '80001';
								$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
						}			
					 else //$resActUsuarioEliminar['codError']=='00000')
					 {$resActSocioEliminar = actSocioEliminar('SOCIO',$usuarioBuscado,$conexionDB['conexionLink']);//en modeloSocios.php, borra datos bancarios 
						
       //echo "<br><br>3-1 modeloPresCoord:bajaSocioFallecido:reActSocioEliminar: "; print_r($resActSocioEliminar);				
					  if ($resActSocioEliminar['codError'] !== '00000')			
			    {$resEliminarSocio = $resActSocioEliminar;
				   }
							elseif ($resActSocioEliminar['numFilas'] <= 0)
							{ $resEliminarSocio['codError'] = '80001';
									$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla SOCIO';
							}							
						 else //$resActSocioEliminar['codError']=='00000')
						 {
								/*---- Inicio actuaciones en tabla 'CUOTAANIOSOCIO' 'ORDENARCOBROBANCO']=N0 y borrar fila año siguiente ----*/
        $anioCuota = date("Y");
								
        $resDatosSocios = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocio.php,	busca todos los datos incluido CUOTAANIOSOCIO, probado error, numFilas=0 es codError=80001
        
								//echo "<br><br>3-2 modeloPresCoord:bajaSocioFallecido:resDatosSocios: "; print_r($resDatosSocios);								
							
								if ($resDatosSocios['codError'] !== '00000')			
								{ $resEliminarSocio = $resDatosSocios;
				    }				
						  else //$resDatosSocios['codError']=='00000')
						  { $codSocio = $resDatosSocios['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];						  	
										$campoCondiciones = $codSocio;		
										$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO'; 
										//$arrayDatosAct['OBSERVACIONES']['valorCampo'] = NULL;//se refieren al pago de la cuota, raramente podría contener datos personales  
										$arrayDatosAct['ANIOCUOTA']['valorCampo'] = $anioCuota;
										
										$resActualizCuotaAnioSocio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$campoCondiciones,$arrayDatosAct,$conexionDB['conexionLink']);//modeloSocios.php, probado error, numFilas=0, no es error
          //echo "<br><br>3-3 modeloPresCoord:bajaSocioFallecido:resActualizCuotaAnioSocio: "; print_r($resActualizCuotaAnioSocio);
								
          if ($resActualizCuotaAnioSocio['codError'] !== '00000')			
										{ $resEliminarSocio = $resActualizCuotaAnioSocio;
										}				
										else //$resActualizCuotaAnioSocio['codError']=='00000')
										{ 
											$cadenaCondiciones = "CODSOCIO = :codSocio AND ANIOCUOTA = :anioCuotaSiguiente"; 
											
											$anioCuotaSiguiente = $anioCuota + 1;
											$arrBind = array(':codSocio' => $codSocio, ':anioCuotaSiguiente' => $anioCuotaSiguiente);	
											
											$resCuotaSocioAnioSiguiente = borrarFilas('CUOTAANIOSOCIO',$cadenaCondiciones,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php,  probado error, numFilas=0, no es error
											
           //echo "<br><br>3-4 modeloPresCoord:bajaSocioFallecido:resCuotaSocioAnioSiguiente: "; print_r($resCuotaSocioAnioSiguiente);
		         /*---- Fin actuaciones en tabla 'CUOTAANIOSOCIO' 'ORDENARCOBROBANCO']=N0 y borrar año siguiente ----*/
											
											if ($resCuotaSocioAnioSiguiente['codError'] !== '00000')			
											{ $resEliminarSocio = $resCuotaSocioAnioSiguiente;
											}		
											else //$resCuotaSocioAnioSiguiente['codError']=='00000')
											{ 
												$arrInsEliminado5Anios['TIPOMIEMBRO'] ='socio';			
												$arrInsEliminado5Anios['CODUSER'] = $usuarioBuscado;
												$arrInsEliminado5Anios['CODPAISDOC'] = $datosUsuarioEliminar['datosFormMiembro']['CODPAISDOC'];
												$arrInsEliminado5Anios['TIPODOCUMENTOMIEMBRO'] = $datosUsuarioEliminar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
												$arrInsEliminado5Anios['NUMDOCUMENTOMIEMBRO'] = $datosUsuarioEliminar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];						
												$arrInsEliminado5Anios['NOM'] = $datosUsuarioEliminar['datosFormMiembro']['NOM'];
												$arrInsEliminado5Anios['APE1'] = $datosUsuarioEliminar['datosFormMiembro']['APE1'];
												$arrInsEliminado5Anios['APE2'] = $datosUsuarioEliminar['datosFormMiembro']['APE2'];	

												if (isset($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']) && !empty($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']))
												{$arrInsEliminado5Anios['OBSERVACIONES'] = $datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES'].". email: ".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'];
												}
												else
												{	$arrInsEliminado5Anios['OBSERVACIONES'] = "email: ".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'];
												}						
												//echo "<br><br>4 modeloPresCoord:bajaSocioFallecido:arrInsEliminado5Anios: ";print_r($arrInsEliminado5Anios);
												
												$resInsertarEliminado5Anios = insertarMiembroEliminado5Anios($arrInsEliminado5Anios);//en modeloUsuarios.php, para temas contables	
												
												//echo "<br><br>5 modeloPresCoord:bajaSocioFallecido:resInsertarEliminado5Anios: ";print_r($resInsertarEliminado5Anios);
												if ($resInsertarEliminado5Anios['codError'] !== '00000')			
												{$resEliminarSocio = $resInsertarEliminado5Anios;
												}
												else //$resInsertarEliminado5Anios['codError']=='00000'
												{         								
													$resActMiembroEliminar = actMiembroEliminar('MIEMBRO',$usuarioBuscado,$conexionDB['conexionLink']);//en modeloUsuarios.php, borrar datos personales, TAMBIÉN pone 'ARCHIVOFIRMAPD'= NULL
													
													//echo "<br><br>6-1 modeloPresCoord:bajaSocioFallecido:reActMiembroEliminar: ";print_r($resActMiembroEliminar);
													if ($resActMiembroEliminar['codError'] !== '00000')			
													{$resEliminarSocio = $resActMiembroEliminar;
													}
													elseif ($resActMiembroEliminar['numFilas'] <= 0)
													{ $resEliminarSocio['codError'] = '80001';
															$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla MIEMBRO';
													}		
													else //$resActMiembroEliminar['codError']=='00000')
													{ 
														// *************  InsertarSociosFallecidos['codError']=='00000')
														//$arrInsFallecidos['CODUSER'] = $usuarioBuscado;
													
														$arrInsFallecidos['CODUSER']= $datosUsuarioEliminar['datosFormUsuario']['CODUSER'];
														$arrInsFallecidos['CODSOCIO']= $datosUsuarioEliminar['datosFormSocio']['CODSOCIO'];
														$arrInsFallecidos['NOM']= $datosUsuarioEliminar['datosFormMiembro']['NOM'];
														$arrInsFallecidos['APE1']= $datosUsuarioEliminar['datosFormMiembro']['APE1'];
														$arrInsFallecidos['APE2']= $datosUsuarioEliminar['datosFormMiembro']['APE2'];
														$arrInsFallecidos['SEXO']= $datosUsuarioEliminar['datosFormMiembro']['SEXO'];
														$arrInsFallecidos['FECHAALTA']= $datosUsuarioEliminar['datosFormSocio']['FECHAALTA'];
														$arrInsFallecidos['FECHABAJA']= $datosUsuarioEliminar['datosFormSocio']['FECHABAJA'];
														//$arrInsFallecidos['FECHANAC']= $datosUsuarioEliminar['datosFormSocio']['FECHANAC'];
														
														if (isset($datosUsuarioEliminar['datosFormSocio']['FECHANAC']) && !empty($datosUsuarioEliminar['datosFormSocio']['FECHANAC'])
																			&& $datosUsuarioEliminar['datosFormSocio']['FECHANAC'] != '0000-00-00')
														{$arrInsFallecidos['FECHANAC'] = $datosUsuarioEliminar['datosFormSocio']['FECHANAC'];
															$cadFechaNac = ", fecha nacimiento: ".$arrInsFallecidos['FECHANAC'];
														} 
														else
														{ $cadFechaNac = "";											
														}											
														$arrInsFallecidos['CODAGRUPACION'] = $datosUsuarioEliminar['datosFormSocio']['CODAGRUPACION'];
														$arrInsFallecidos['NOMAGRUPACION'] = $datosUsuarioEliminar['datosFormSocio']['NOMAGRUPACION'];
																								
														if (isset($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']) && !empty($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']))
														{$arrInsFallecidos['OBSERVACIONES'] = $datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES'];
														}
														else 
														{$arrInsFallecidos['OBSERVACIONES'] = "";
													 } 	
														$arrInsFallecidos['CP'] = $datosUsuarioEliminar['datosFormDomicilio']['CP']; 
														$arrInsFallecidos['LOCALIDAD'] = $datosUsuarioEliminar['datosFormDomicilio']['LOCALIDAD']; 
														
														if (isset($datosUsuarioEliminar['datosFormDomicilio']['NOMPROVINCIA']) && !empty($datosUsuarioEliminar['datosFormDomicilio']['NOMPROVINCIA']))
														{$arrInsFallecidos['NOMPROVINCIA'] = $datosUsuarioEliminar['datosFormDomicilio']['NOMPROVINCIA'];
														}
												
														$arrInsFallecidos['NOMBREPAISDOM'] = $datosUsuarioEliminar['datosFormDomicilio']['nombrePaisDom'];
																				
														$resInsertarSociosFallecidos = insertarSocioFallecido($arrInsFallecidos);//en modeloPresCoord.php probado error	
	             //echo "<br><br>6-2 modeloPresCoord:bajaSocioFallecido:reInsertarSociosFallecidos: ";print_r($resInsertarSociosFallecidos);
											
														if ($resInsertarSociosFallecidos['codError'] !== '00000')			
														{ $resEliminarSocio = $resInsertarSociosFallecidos;
																//echo "<br><br>7-1 modeloPresCoord:bajaSocioFallecido:resEliminarSocio: ";print_r($resEliminarSocio);
														}
														else //$resInsertarSociosFallecidos['codError']=='00000')
														{ 
																/*--------- Inicio eliminar archivo firma socio del servidor -------
																A partir de 2018-10-20, si un gestor da de alta a un socio, el gestor 
																que da el alta tendrá que subir el formulario de alta en archivo pdf, 
																jpg, con la firma del socio.	Con la baja, se elimina el archivo.
																
																NOTA: En caso de que se produzca un error solo en el proceso de 
																eliminar Archivo	no efectuará rollback, ya que pudiera suceder que por 
																error alguien hubiese alterado el path del directorio o el nombre del 
																archivo (pero en "actMiembroEliminar()" se borrarán los datos 
																personales como nombre del archivo de esa persona en la tabla MIEMBRO
																como garantía de protección de datos)
																------------------------------------------------------------------*/
																$cadEliminarArchivo = '';		
																
																if (isset($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']) && !empty($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']))																										    						
																{ 
																		//$datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD'] ='hhhh.jpg'; //Para pruebas error
																		//$datosUsuarioEliminar['datosFormMiembro']['PATH_ARCHIVO_FIRMAS'] ='fdksdjjd.klm';
																		
																		require_once './modelos/modeloArchivos.php';	
																		$resEliminarArchivo = eliminarArchivo($datosUsuarioEliminar['datosFormMiembro']['PATH_ARCHIVO_FIRMAS'],$datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']);

																		//echo "<br><br>8-1 modeloPresCoord:bajaSocioFallecido:resEliminarArchivo: ";print_r($resEliminarArchivo);
					
																		if ($resEliminarArchivo['codError'] !== '00000')  //dejo los dos por si quiero añadir aquí algún comentario extra
																		{ 
																				$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
																				//echo "<br><br>8-2 modeloPresCoord:bajaSocioFallecido: "; print_r($cadEliminarArchivo);
																		}
																		else //if ($resEliminarArchivo['codError'] =='00000') 
																		{ 
																				$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
																				//echo "<br><br>8-3 modeloPresCoord:bajaSocioFallecido: "; print_r($cadEliminarArchivo);							
																		} 																	
																}          
																//----------- Fin eliminar archivo firma socio del servidor --------

																//----------------Inicio COMMIT ------------------------------------
																$resFinTrans = commitPDO($conexionDB['conexionLink']);
																
																//echo "<br><br>9-1 modeloPresCoord.php:bajaSocioFallecido:resFinTrans: ";var_dump($resFinTrans);
																if ($resFinTrans['codError'] !== '00000') //será ['codError'] = '70502';
																{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de eliminar socio/a. ';
																	$resFinTrans['numFilas'] = 0;																												
																	$resEliminarSocio = $resFinTrans;																	
																}	
																else //($resFinTrans['codError'] == '00000')
																{
																	if ($datosUsuarioEliminar['datosFormMiembro']['EMAILERROR'] =='NO') //cuando no tiene email da un notice
																	{	$cadEmail =	"<br /><br /><br />Se ha enviado un correo a la dirección <b>".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'].
																			" </b>, para comunicar la baja por fallecimiento, por si alguien lee su correo. ";
																	}
																	else
																	{ $cadEmail =	"<br /><br /><br />No se ha podido enviar un correo electrónico al socio/a para comunicar esta baja, porque no tiene dirección de correo o es errónea";															
																	}  
																	
																	$arrMensaje['textoComentarios'] = "Se han eliminado la mayoría de los datos personales de <b> ".
																	$datosUsuarioEliminar['datosFormMiembro']['NOM']." ".$datosUsuarioEliminar['datosFormMiembro']['APE1'].
																	" </b> de la base de datos de	Europa Laica". 					
																	$cadEliminarArchivo.$cadEmail.
																	"<br /><br /><br /><br />Como recuerdo del socio/a fallecido, guardaremos en nuestra base de Europa Laica los siguentes datos: 
																	<br /><br /><br />Num. de socio/a: ".$arrInsFallecidos['CODSOCIO'].", nombre y apellidos: ".$arrInsFallecidos['NOM']." ".$arrInsFallecidos['APE1']." ".
																	$arrInsFallecidos['APE2'].
																	"<br /><br />fecha alta: ".$arrInsFallecidos['FECHAALTA'].", fecha baja: ".$arrInsFallecidos['FECHABAJA'].$cadFechaNac.
																	"<br /><br />asociación de EL: ".$arrInsFallecidos['NOMAGRUPACION'].",Domicilio localidad: ".$arrInsFallecidos['LOCALIDAD'].", país: ".$arrInsFallecidos['NOMBREPAISDOM'].
																	"<br /><br />Observaciones: ".$arrInsFallecidos['OBSERVACIONES'];
																}//else $resFinTrans['codError'] == '00000'
														}//else $reInsertarSociosFallecidos['codError']=='00000'
													}//else $reActMiembroEliminar['codError']=='00000'
												}//else $resInsertarEliminado5Anios['codError']=='00000'
											}//else $resCuotaSocioAnioSiguiente['codError']=='00000')
						   	}//else $resActualizCuotaAnioSocio['codError']=='00000')
							 }//else $resDatosSocios['codError']=='00000')								
					  }//else $reActSocioEliminar['codError']=='00000'
			   }//else $reActUsuarioEliminar['codError']=='00000'	
					}//else $reRolEliminar['codError']=='00000'		
				}//else reEliminarCoordAreaGestion['codError']=='00000'
			}//elseif ($rolCoordinadorExiste['numFilas'] > 0)																																	
		
	  //echo "<br><br>10 modeloPresCoord:bajaSocioFallecido:resEliminarSocio: ";print_r($resEliminarSocio);
			
			//---------------- Inicio tratamiento errores -------------------------------	
   
			//--- Inicio deshacer transación en las tablas modificadas ---------------
			if ($resEliminarSocio['codError'] !== '00000')
			{			
				$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);

				if ($resDeshacerTrans['codError'] !== '00000')//será ['codError'] = '70503';
				{ $resEliminarSocio['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];					
				}	
			}//--- Fin deshacer transación en las tablas modificadas -----------------
			
		}//else $resIniTrans['codError'] == '00000'				
			
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
			
		if ($resEliminarSocio['codError'] !== '00000' || (isset($deshacerTrans['codError']) && $deshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($resEliminarSocio['textoComentarios']) ) 
			{ $resEliminarSocio['textoComentarios'] = ". modeloPresCoord.php:bajaSocioFallecido(): ".$resEliminarSocio['textoComentarios'];}	
			else
			{	$resEliminarSocio['textoComentarios'] = ". modeloPresCoord.php:bajaSocioFallecido(): ";}								
						
			require_once './modelos/modeloErrores.php';//si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores = insertarError($resEliminarSocio);	
				
			if ($resInsertarErrores['codError'] !== '00000')
	  {$resEliminarSocio['codError'] = $resInsertarErrores['codError'];
				$resEliminarSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}			
		}//if ($resEliminarSocio['codError']!=='00000')		
  //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
 	//---------------- Fin tratamiento errores ------------------------------------				
			
	}//else $conexionDB['codError']=="00000"	

 $resEliminarSocio['arrMensaje'] = $arrMensaje;	
 
	//echo "<br><br>11 modeloPresCoord:bajaSocioFallecido:resEliminarSocio: ";print_r($resEliminarSocio);	
	
 return 	$resEliminarSocio; 	
}
/*------------------------------ Fin  bajaSocioFallecido ----------------------------------------*/

/*----------------------------- Inicio insertarSocioFallecido -------------------------------------
Inserta un los datos de un socio dado de baja por fallecimiento en la 
tabla SOCIOSFALLECIDOS
RECIBE: $arrInsertar 
DEVUELVE: Array con resultado: ['codError'],['errorMensaje'] y ['numFilas'] =1,
si se efectuó correctamente, y -1 si no fue posible

LLAMADA: desde modeloPresCoord:bajaSocioFallecido(), que a su vez se llama 
cCoordinador.php:eliminarSocioCoord(),cPresidente.php:eliminarSocioPres(),
cTesorero.php:eliminarSocioCoord()

LLAMA: modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: 
2020-05-13: No se requiere modificaciones para PDO en insertarUnaFila(), 
lo gestionan dentro de ella con los par. que reciben
2017-03-20: añadido
-----------------------------------------------------------------------------------------*/
function insertarSocioFallecido($arrInsertar)				
{
 //echo "<br><br>0 modeloPresCoord.php:insertarSocioFallecido:arrInsertar: "; print_r($arrInsertar);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $reInsertarSocioFallecido = $conexionDB;
	}
	else
	{//echo "<br><br>1 modeloPresCoord.php:insertarSocioFallecido:$arrInsertar: "; print_r($arrInsertar);

	 $reInsertarSocioFallecido = insertarUnaFila('SOCIOSFALLECIDOS',$arrInsertar,$conexionDB['conexionLink']);			
	}
  //echo "<br>2 modeloPresCoord.php:insertarSocioFallecido:reInsertarSocioFallecido: "; print_r($reInsertarSocioFallecido); echo "<br>";//
		
  return $reInsertarSocioFallecido;
}
/*----------------------------- Fin insertarSocioFallecido --------------------------------------*/	

//========================== FIN SOCIOSFALLECIDOS =================================================



/*------------ Inicio buscarDatosMiembro ---------------------------------------------------------
Obtiene un array con todos los campos de la tabla MIEMBRO para un CODUSER

Recibe: $codUser que es el CODUSER (por ejemplo $_SESSION['vs_CODUSER']), $conexionUsuariosDB
Devuelve: un array con todos los campos de la tabla MIEMBRO, y los compos errores y número de filas.
          Devolver o filas (no encontrado no lo trata como error)
										
LLAMADA: modeloPresCoord:mAltaSocioPorGestor(),altaSocioPendienteConfirmadaPorGestor()
LLAMA: configMySQL.php:conexionDB(), BBDD/MySQL/modeloMySQL.php:buscarCadSql()

OBSERVACIONES: 
Lo introduje para anotar el nombre del gestor en posibles campos OBSERVACIONES o 
COMENTARIOS en operación realizadas por un gestor de socios
Agustín: 2020-03-28: Incluyo PHP: PDOStatement::bindParam		
-------------------------------------------------------------------------------------------*/
function buscarDatosMiembro($codUser,$conexionLinkDB)
{
	//echo "<br><br>0-1 modeloPresCoord.php:buscarDatosMiembro:codUser: "; print_r($codUser); 
	//echo "<br><br>0-2 modeloPresCoord.php:buscarDatosMiembro:conexionLinkDB: "; print_r($conexionLinkDB);
	
	$arrDatosMiembro['nomScript'] = 'modeloPresCoord.php'; 
 $arrDatosMiembro['nomFuncion'] = 'buscarDatosMiembro'; 
	$arrDatosMiembro['codError'] = '00000';	
	$arrDatosMiembro['errorMensaje'] = '';
	
	if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
	{ 
			require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
		 
			//echo "<br><br>1-2 modeloPresCoord.php:buscarDatosMiembro:conexionDB: ";var_dump($conexionDB);				
 }		
	else
	{ $conexionDB['codError'] = "00000";
		 $conexionDB['conexionLink'] = $conexionLinkDB;   			
	 	//echo "<br><br>1-3 modeloPresCoord.php:buscarDatosMiembro:conexionDB: ";var_dump($conexionDB);	 
	}	
	if ($conexionDB['codError'] !== "00000")
	{ $arrDatosMiembro['codError'] = $conexionDB['codError'];
	  $arrDatosMiembro['errorMensaje'] = $conexionDB['errorMensaje'];
			$arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else
	{	
		//$cadSQL = "SELECT * FROM MIEMBRO WHERE CODUSER = ".$codUser;
		$cadSQL = "SELECT * FROM MIEMBRO WHERE CODUSER = :codUser ";
		
		$arrBind = array(':codUser' => $codUser); 
		
		require_once "BBDD/MySQL/modeloMySQL.php";
		$arrDatosMiembro = buscarCadSql($cadSQL,$conexionDB['conexionLink'],$arrBind);//$consultaResultado['resultadoFilas'][0] sin ['valorCampo']	

		//echo "<br><br>2 modeloPresCoord.php:buscarDatosMiembro:arrDatosMiembro: ";print_r($arrDatosMiembro);echo "<br>";				
		
		if ($arrDatosMiembro['codError'] !=='00000')
		{			
				$textoErrores = $arrDatosMiembro['errorMensaje']. "Error al buscar en tabla MIEMBRO. CODERROR: ".$arrDatosMiembro['codError'];							
				$arrDatosMiembro['textoComentarios'] = $textoErrores;				
						
				require_once './modelos/modeloErrores.php';		
				$resInsertarErrores = insertarError($arrDatosMiembro);			
				
				if ($resInsertarErrores['codError'] !=='00000')
				{$arrDatosMiembro['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'].= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
				}
			 //echo "<br><br>3 modeloPresCoord.php:buscarDatosMiembro:arrDatosMiembro: ";print_r($arrDatosMiembro);echo "<br>";					
		}		  
	}		
	//echo "<br><br>4 modeloPresCoord.php:buscarDatosMiembro:arrDatosMiembro: ";print_r($arrDatosMiembro);echo "<br>";				
		
 return $arrDatosMiembro;  				
}
/*------------------------------ Fin buscarDatosMiembro -----------------------------------------*/



/*------------------------- Inicio mAltaSocioPorGestor  -------------------------------------------
En esta función se dan de alta los socios, por parte de un gestor, sin intervenir el socio
Se insertan los datos en las tablas correspondientes USUARIO, etc.
De utilidad para aquellos socios que no tienen email o que no se manejan bien.

En estas altas, se guarda en tabla MIEMBRO el nombre de archivo con la firma y directorio 
donde los archivos en el servidor.
Genera un socio con el usuario = NOM.$codUser+125+ramdom y una contraseña que 
es: sha1($codUser.$usuario) (y estará encriptada)
Se llama a la función ./modeloArchivos.php:moverArchivoFunction(), para subir al servidor 
el archivo con la firma del socio. 
Además guarda en la tabla MIEMBRO, los campos ARCHIVOFIRMAPD, PATH_ARCHIVO_FIRMAS,
con el nombre y dirección del archivo. Controlando transacciones.		
	
	
RECIBE: un array $resValidarCamposAlta con los campos de los formularios y archivo ya validados
DEVUELVE: array $resAltaSocios con campos:$resAltaSocios['datosUsuario']=$codUser 
(que se necesitará para cCoordinador.php), $resAltaSocios['datosSocio']=CODSOCIO
(por si más adelante se necesitara para cCoordinador.php) y los controles errores
y ['arrMensaje']['textoComentarios'] para mostrar en cCoordinador.php

LLAMADA:cCoordinardor:altaSocioPorGestorCoord(),cPresidente:altaSocioPorGestorCoord(), 
cTesorero:altaSocioPorGestorCoord() 

LLAMA: varias funciones de "modeloSocios.php:insertarCuotaAnioSocio()..." y otras 
modeloUsuarios.php.
modeloPresCoord.php:buscarDatosMiembro()
**modeloArchivos.php:moverArchivo(), para subir archivo al servidor y 
eliminarArchivo(), en caso de error al dar de alta.
BBDD/MySQL/transationPDO.php:beginTransationPDO(),commitPDO(),rollbackPDO()							

OBSERVACIONES: Probado PHP 7.3.21: PDO MySQL: BBDD/MySQL/transationPDO.php 
--------------------------------------------------------------------------------------------*/
function mAltaSocioPorGestor($resValidarCamposAlta)
{ 
 //echo "<br><br>0-1 modeloPresCoord.php:mAltaSocioPorGestor:resValidarCamposAlta: ";print_r($resValidarCamposAlta);
	
	$resInsertar['nomScript'] = "modeloPresCoord.php";
	$resInsertar['nomFuncion'] = "mAltaSocioPorGestor";	
	$resInsertar['codError'] = '00000';
 $resInsertar['errorMensaje'] = '';

 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

 //echo "<br><br>1-1 modeloPresCoord.php:mAltaSocioPorGestor:conexionsDB: ";var_dump($conexionsDB);echo "<br>";
	if ($conexionDB['codError'] !== "00000")
	{ $resInsertar = $conexionDB;	
	}
 else//$conexionUsuariosDB['codError']=="00000"
	{		
		/*--- Inicio de punto de rollback para transación --------------------------*/

  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-2 modeloPresCoord.php:mAltaSocioPorGestor:resIniTrans: ";var_dump($resIniTrans);echo "<br>";
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{ $resIniTrans['errorMensaje'] = ' Error en el sistema: beginTransationPDO() no ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;							
				$resInsertar = $resIniTrans;			
				//echo "<br><br>2 modeloPresCoord.php:mAltaSocioPorGestor:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		}				
		else //$resIniTrans['codError'] == '00000' inicio transación con exito
		{ 		
			require_once './modelos/libs/buscarCodMax.php';
	  $resulBuscarCodMax = buscarCodMax('USUARIO','CODUSER',$conexionDB['conexionLink']);//ya tiene conexionDB()
	
			if ($resulBuscarCodMax['codError'] !== '00000')
			{ $resInsertar = $resulBuscarCodMax;
			}
			else//$resulBuscarCodMax['codError']=='00000'
			{/*-----------------  Inicio insertar en tabla USUARIO ----------------------
			  Aunque se crea y guarda una PASSWORD en realidad la tiene que elegir el 
					socio cuando le llegue el email de aviso de alta y confirmación de email. 
					Se añade aquí por seguridad. El nombre de usuario "USUARIO" generado aquí 
					lo puede cambiar el socio.
			 --------------------------------------------------------------------------*/			 
				$codUser = $resulBuscarCodMax['valorCampo'];    
				$numUs = $codUser + 125;			
    $numUs = $numUs.rand(1,9);//más seguro sería rand(10,90), pero alarga el número de 5 a 6 				
				
				require_once './modelos/libs/eliminarAcentos.php';
				$usuario = cambiarParaUsuarioPass($resValidarCamposAlta['datosFormMiembro']['NOM']['valorCampo']).$numUs;
				
				if (strlen($usuario) > 30)
				{ $usuario = substr($usuario, -30);	
				}					
				$arrDatosUsuario['CODUSER'] = $codUser;						
				$arrDatosUsuario['USUARIO'] = $usuario;				
	   $arrDatosUsuario['PASSUSUARIO'] = sha1($codUser.$usuario);//sha1 devuelve 40 caracteres 	
				$arrDatosUsuario['ESTADO'] = 'alta-sin-password-gestor';	
				$arrDatosUsuario['OBSERVACIONES'] = 'Alta realizada por gestor/a CODUSER: '.$_SESSION['vs_CODUSER'];//añado 2020-03-29	 	

    $resInsertarUsuario = insertarUnaFila('USUARIO',$arrDatosUsuario, $conexionDB['conexionLink']);	
				
	   //echo "<br><br>3 modeloPresCoord:mAltaSocioPorGestor: resInsertarUsuario: "; print_r($resInsertarUsuario);			
    /*-----------------  Fin insertar en tabla USUARIO -----------------------*/

	   if ($resInsertarUsuario['codError'] !== '00000')			
	   {$resInsertar = $resInsertarUsuario;
	   }
	   else //($resInsertarUsuario['codError']=='00000')
	   {/*-----------------  Inicio insertar en tabla USUARIOTIENEROL -----------*/
  			
					$arrDatosUsuarioRol['CODUSER']['valorCampo'] =	$codUser;	
					$arrDatosUsuarioRol['CODROL']['valorCampo'] = '1';	

     $resInsertarUsuarioRol = insertarUsuarioRol($arrDatosUsuarioRol);//todos se dan de alta con rol socio codigo '1'//contiene conexionDB()	
					
		   //echo "<br><br>4 modeloPresCoord:mAltaSocioPorGestor:insertarUsuarioRol ";print_r($resInsertarUsuarioRol);echo "<br><br>";    
			  /*-----------------  Fin insertar en tabla USUARIOTIENEROL --------------*/
					
	    if ($resInsertarUsuarioRol['codError'] !== '00000')			
	    {$resInsertar = $resInsertarUsuarioRol;
	    }
		   else //$resInsertarUsuarioRol['codError']=='00000'
	    {	
 					/*--- Inicio Guardar el archivo de firma del socio validado y en directorio elegido ---						
								Previamente ya validados datos en controladores.php, con la función: 
								modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor()
						--------------------------------------------------------------------------------------*/		
      //--- Inicio Asignar nombre socio para archivo ----------------------------------						
						$cadenaNomArchivoSinExt = substr(trim($resValidarCamposAlta['datosFormMiembro']['APE1']['valorCampo']),0,50)."_".
																																substr(trim($resValidarCamposAlta['datosFormMiembro']['APE2']['valorCampo']),0,50)."_".//puede no existir APE2
																																substr(trim($resValidarCamposAlta['datosFormMiembro']['NOM']['valorCampo']),0,50)."_".date("Y-m-d-H-i-s");		
      
						$resValidarCamposAlta['ficheroAltaSocioFirmado']['maxLongNomArchivoSinExtDestino'] = 175;//maxima logitud del nombre archivo destino	
						
      $resValidarCamposAlta['ficheroAltaSocioFirmado']['nomArchivoSinExtDestino']	 = strtolower($cadenaNomArchivoSinExt);						
																																	
						//Después dentro "moverArchivo()", corrige caracteres no permitidos con la función "sanearNombreArchivo($nomArchivoSinExtDestino,$extension,$maxLongNomArchivoSinExtDestino)"
      //echo "<br><br>5-1 modeloPresCoord.php:mAltaSocioPorGestor:resValidarCamposAlta['ficheroAltaSocioFirmado']: ";print_r($resValidarCamposAlta['ficheroAltaSocioFirmado']); 
						
      //--- Fin Asignar nombre socio para archivo -------------------------------------	      
					
						/*--- moverArchivo(): Mueve el archivo del directorio tmp por defecto al directorio de firmas del servidor y guarda el archivo 
						  con nombre socio. //devuelve array con datos $resMoverArchivo['nombreArchExtGuardado'],$resMoverArchivo['directorioSubir']
							---------------------------------------------------------------------------------------------------------------------------*/		
						require_once './modelos/modeloArchivos.php';
						$resMoverArchivo = moverArchivo($resValidarCamposAlta['ficheroAltaSocioFirmado'],$resValidarCamposAlta['ficheroAltaSocioFirmado']['directorioSubir'],
																																						$resValidarCamposAlta['ficheroAltaSocioFirmado']['nomArchivoSinExtDestino'],$resValidarCamposAlta['ficheroAltaSocioFirmado']['extension'],
																																						$resValidarCamposAlta['ficheroAltaSocioFirmado']['maxLongNomArchivoSinExtDestino'],$resValidarCamposAlta['ficheroAltaSocioFirmado']['permisosArchivo']);																																						
																																																
						//echo "<br><br>5-2 modeloPresCoord:mAltaSocioPorGestor:resMoverArchivo: ";print_r($resMoverArchivo);
						
						if ($resMoverArchivo['codError'] !== '00000')//si ['numFilas']=0 también es incluido como error
						{ $resInsertar['codError'] .= $resMoverArchivo['codError'];
								$resInsertar['errorMensaje'] .= $resMoverArchivo['errorMensaje'];
								$comentarioSubirArchivo = "<br /><br /><br />No se ha podido subir al servidor el archivo con la firma de la socia/o aceptando el uso de sus datos por EL.
								<br /><br /> Archivo origen:  <b>".$resValidarCamposAlta['ficheroAltaSocioFirmado']['name'];	
						}
						else //$resMoverArchivo['codError'] =='00000'
						{ 
							$comentarioSubirArchivo = "<br /><br /><br />Por seguridad para EL, en cuanto a la legislación de protección de datos, se ha subido al servidor el archivo con la firma de la socia/o
							aceptando el uso de sus datos por EL.<br /><br /> Archivo origen:  <b>".$resValidarCamposAlta['ficheroAltaSocioFirmado']['name'].
							"</b> guardado en el servidor con el nombre: <b>".$resMoverArchivo['nombreArchExtGuardado'].	
							"</b><br /><br /><br />El archivo y demás datos personales se eliminarán al dar de baja al socio/a.";							
													
						 /*--- Fin Guardar el archivo de la firma del socio validado y en el directorio elegido ----*/

				 		/*--- Inicio Guardar datos en tabla MIEMBRO incluidos los referidos al --
							archivo de la firma 'ARCHIVOFIRMAPD'y 'PATH_ARCHIVO_FIRMAS' nombre del 
							socio y en el directorio del archivo. 					
							También se guarda en comentarios el nombre del gestor que realizó el alta.
							Es necesario preparar para insertar en tabla MIEMBRO
							-----------------------------------------------------------------------*/ 
							unset($resValidarCamposAlta['datosFormMiembro']['REMAIL']);//no existe ese campo en la tabla MIEMBRO	
							
							$arrDatosMiembro = array_merge($resValidarCamposAlta['datosFormMiembro'],$resValidarCamposAlta['datosFormDomicilio']);//fusiona
							
							$arrDatosMiembro['ARCHIVOFIRMAPD']['valorCampo'] = $resMoverArchivo['nombreArchExtGuardado'];
			    $arrDatosMiembro['PATH_ARCHIVO_FIRMAS']['valorCampo'] = $resMoverArchivo['directorioSubir'];//es el path relativo a $_SERVER['DOCUMENT_ROOT']									
							$arrDatosMiembro['CODUSER']['valorCampo'] = $codUser;
							$arrDatosMiembro['TIPOMIEMBRO']['valorCampo'] = "socio";	
							
							if ($resValidarCamposAlta['datosFormMiembro']['EMAILERROR']['valorCampo']	== 'FALTA')//No tiene email	el socio																						
							{ $arrDatosMiembro['EMAIL']['valorCampo'] = 'falta'.$codUser.'@falta.com';							
									$textoComentarioEmail = "Al no tener email la socia/o, no se ha podido enviar mensaje de confirmación para que elija la contraseña y confirme su alta y su email. 
									Un gestor/a autorizado podría añadir su email, si se lo suministrase la socia/o más adelante. ";	
							}
							else //si tiene email
							{ $textoComentarioEmail = "Llegará un email a la dirección de la socia/o <strong>".$resValidarCamposAlta['datosFormMiembro']['EMAIL']['valorCampo'].
							                          "</strong> para que elija la contraseña y confirme su alta y su email";
							}	
							$arrDatosMiembro['FECHANAC']['valorCampo'] =	$resValidarCamposAlta['datosFormMiembro']['FECHANAC']['anio']['valorCampo'].'-'.
																																																				$resValidarCamposAlta['datosFormMiembro']['FECHANAC']['mes']['valorCampo'].'-'.
																																																				$resValidarCamposAlta['datosFormMiembro']['FECHANAC']['dia']['valorCampo'];	

							/*-- Inicio buscar en tabla MIEMBRO nombre-apellidos gestor que da el 
							alta para campo OBSERVACIONES. La función está en modeloPresCoord.php 
							---------------------------------------------------------------------*/								
							$arrDatosMiembroGestor = buscarDatosMiembro($_SESSION['vs_CODUSER'],$conexionDB['conexionLink']);//$_SESSION['vs_CODUSER'] es el CODUSER del gestor//contiene conexionDB()
							
							//echo "<br><br>6-0 modeloPresCoord:mAltaSocioPorGestor:arrDatosMiembroGestor: ";print_r($arrDatosMiembroGestor);							
							if ($arrDatosMiembroGestor['codError'] !== '00000')
							{ $resInsertar = $arrDatosMiembroGestor;
							}  				
							elseif ($arrDatosMiembroGestor['numFilas'] <= 0)//sí es error si no devuelve nada
							{	$resInsertar['codError'] = '80001'; //no encontrado 
									$resInsertar['errorMensaje'] = "Error: No se han encontrado los datos del gestor en la tabla MIEMBRO";				
							}
							else//$arrDatosMiembroGestor['codError'] =='00000'
							{ $nomGestor = $arrDatosMiembroGestor['resultadoFilas'][0]['NOM']." ".$arrDatosMiembroGestor['resultadoFilas'][0]['APE1']; 	
		
								if (isset($arrDatosMiembroGestor['resultadoFilas'][0]['APE2']) && !empty($arrDatosMiembroGestor['resultadoFilas'][0]['APE2']))
								{	$nomGestor .= " ".$arrDatosMiembroGestor['resultadoFilas'][0]['APE2'];
								}			
								$arrDatosMiembro['OBSERVACIONES']['valorCampo'] .= ". Alta realizada por gestor/a: ".$nomGestor." el día ".date ('Y-m-d');	
								//Por privacidad de datos, podría sería más adecuado lo siguiente, y en ese caso sobraría "tabla MIEMBRO nombre-apellidos gestor" 									
								//$arrDatosMiembro['datosFormMiembro']['OBSERVACIONES']['valorCampo'] .= ". Alta realizada por gestor/a, CODUSER: ".$_SESSION['vs_CODUSER']." el día ".date ('Y-m-d');			
								
								/*--Fin buscar en MIEMBRO nombre-apellidos gestor para OBSERVACIONES--*/	

								//echo "<br><br>6-1 modeloPresCoord:mAltaSocioPorGestor:arrDatosMiembro: ";print_r($arrDatosMiembro);

								$resInsertarMiembro = insertarMiembro($arrDatosMiembro,$conexionDB['conexionLink']);	//se necesitan datos de DOMICILIO el codigo//contiene conexionDB()	
								
								//echo "<br><br>6-2 modeloPresCoord:mAltaSocioPorGestor:resInsertarMiembro ";print_r($resInsertarMiembro);								
								/*---------------- Fin Guardar datos en tabla MIEMBRO ----------------*/
								
								if ($resInsertarMiembro['codError'] !=='00000')			
								{ $resInsertar = $resInsertarMiembro;
								}
								else //$resInsertarMiembro['codError']=='00000'
								{
									/*----------------------Inicio  Insertar socio ----------------------*/
									$arrDatosSocio	= $resValidarCamposAlta['datosFormSocio'];
									
									$arrDatosSocio['CODUSER']['valorCampo'] = $codUser;	 
									$arrDatosSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $resValidarCamposAlta['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];									
									
									//echo "<br><br>6-3-1 modeloPresCoord:mAltaSocioPorGestor:resValidarCamposAlta['datosFormSocio']: ";print_r($resValidarCamposAlta['datosFormSocio']);
									
									if (isset($resValidarCamposAlta['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resValidarCamposAlta['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 
									{$arrDatosSocio['CUENTAIBAN']['valorCampo'] = $resValidarCamposAlta['datosFormSocio']['CUENTAIBAN']['valorCampo'];														
										$modoIngresoCuota = 'DOMICILIADA';

										if($resValidarCamposAlta['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] == 0 ) //Si existe cuenta pero IMPORTECUOTAANIOSOCIO = 0, será EXENTO.
										{	$ordenarCobroBanco = 'NO';										
										}
										else
										{	$ordenarCobroBanco = 'SI';								
										}	
										//echo "<br><br>6-3-2 modeloPresCoord:mAltaSocioPorGestor:ordenarCobroBanco: ";print_r($ordenarCobroBanco);								
									}
									elseif (isset($resValidarCamposAlta['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposAlta['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
									{	$modoIngresoCuota  = 'DOMICILIADA';
											$ordenarCobroBanco = 'NO';
										 //echo "<br><br>6-4 modeloPresCoord:mAltaSocioPorGestor:ordenarCobroBanco: ";print_r($ordenarCobroBanco);								
									}								 
									else
									{ $modoIngresoCuota  = 'SIN-DATOS';
											$ordenarCobroBanco = 'NO';	
									 	//echo "<br><br>6-5 modeloPresCoord:mAltaSocioPorGestor:ordenarCobroBanco: ";print_r($ordenarCobroBanco);								
									}			
									//echo "<br><br>6-6 modeloPresCoord:mAltaSocioPorGestor:ordenarCobroBanco: ";print_r($ordenarCobroBanco);									
									$arrDatosSocio['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;						

									$resInsertarSocio = insertarSocio($arrDatosSocio,$conexionDB['conexionLink']);//además devuelve el siguiente valor a máx CODSOCIO existente//contiene conexionDB()
									
									//echo "<br><br>7 modeloPresCoord:mAltaSocioPorGestor:resInsertarSocio: ";print_r($resInsertarSocio);
									
									/*- Dentro de esta función insertarSocio(): halla Codmax. para CODSOCIO 
										DEVUELVE CODSOCIO, añade  SECUENCIAADEUDOSEPA = FRST y 
										FECHAACTUALIZACUENTA = fecha alta										
									---------------------- Fin  Insertar socio --------------------------*/
										
									if ($resInsertarSocio['codError'] !== '00000')			
									{ $resInsertar = $resInsertarSocio;
									}
									else //$resInsertarSocio['codError']=='00000'
									{
										/*---------------------- Inicio  Insertar cuota anio socio ---------*/
		
										$arrDatosCuotaAnioSocio = $resValidarCamposAlta['datosFormCuotaSocio'];				
										$arrDatosCuotaAnioSocio['CODSOCIO']['valorCampo'] =	$resInsertarSocio['CODSOCIO'];//siguiente valor a máx CODSOCIO existente
										$arrDatosCuotaAnioSocio['CODAGRUPACION']['valorCampo'] = $resValidarCamposAlta['datosFormSocio']['CODAGRUPACION']['valorCampo'];
										$arrDatosCuotaAnioSocio['CODCUOTA']['valorCampo'] = $resValidarCamposAlta['datosFormSocio']['CODCUOTA']['valorCampo'];										
          //echo "<br><br>8-1 modeloPresCoord:mAltaSocioPorGestor:arrDatosCuotaAnioSocio: ";print_r($arrDatosCuotaAnioSocio);
									 /*--------------------------------------------------------------------
										La función insertarCuotaAnioSocio() ya controla que no se pueda insertar
										una cuota IMPORTECUOTAANIOSOCIO <IMPORTECUOTAANIOEL, y lo corrige si es
										necesario. Pero debido a que hay que controlar si es EXENTO 
										(	y que puede variar según los años por ejemplo cambió en 2015)
										y no se conoce el valor de IMPORTECUOTAANIOSOCIO para el año 
										correspondiente a ese tipo de cuota, necesario para
										if($datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo']!=0) o su 
										equivalente if($importeCuotaAnioEL_anioActual!=0), 
										se llama a la función "buscarCuotasAnioEL()" para obtener los valores 
										de las cuotas vigentes de EL para los años "Y" y 
										$datosSocioConfirmar['ANIOCUOTA'] (si se inició el proceso alta en año
										anterior (diciembre pero se da de alta en enero) y las cuotas de EL 
										exentas=0, pueden haber variado, de un año a otro
										--------------------------------------------------------------------*/
										$anioActual = date('Y');
										$tipoCuota  = $resValidarCamposAlta['datosFormSocio']['CODCUOTA']['valorCampo'];
										
										$resImporteCuotaAnio = buscarCuotasAnioEL($anioActual,$tipoCuota);//obtiene valores cuotas vigentes de EL para el año//contiene conexionDB()	
										
										//echo "<br><br>9-1 modeloPresCoord:mAltaSocioPorGestor:resImporteCuotaAnio:";print_r($resImporteCuotaAnio);
										if ($resImporteCuotaAnio['codError'] !=='00000')//si ['numFilas']=0 también es incluido como error
										{ $resInsertar['codError'] .= $resImporteCuotaAnio['codError'];
												$resInsertar['errorMensaje'] .= $resImporteCuotaAnio['errorMensaje'];
										}
										else //$resImporteCuotaAnio['codError']=='00000'
										{$importeCuotaAnioEL_anioActual = $resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioActual][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];																					
											//echo "<br><br>9-2 modeloPresCoord:mAltaSocioPorGestor:importeCuotaAnioEL_anioActual:";print_r($importeCuotaAnioEL_anioActual);																																			
											
											if($importeCuotaAnioEL_anioActual !== 0)//equivale a ['IMPORTECUOTAANIOEL'] anterior línea			
											{ $arrDatosCuotaAnioSocio['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';					
													//echo "<br><br>9-3";
											}
											else
											{ $arrDatosCuotaAnioSocio['ESTADOCUOTA']['valorCampo'] ='EXENTO';//en 2014 entraría, pero no entrará en 2015 joven=5 porque cambió															
													//echo "<br><br>9-4";		
											}							
 										$arrDatosCuotaAnioSocio['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;
											$arrDatosCuotaAnioSocio['ORDENARCOBROBANCO']['valorCampo'] = $ordenarCobroBanco;	

           //echo "<br><br>9-5 modeloPresCoord:mAltaSocioPorGestor:arrDatosCuotaAnioSocio: ";print_r($arrDatosCuotaAnioSocio);		
											
											// función está en modeloSocios.php:insertarCuotaAnioSocio()																					
											$resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($arrDatosCuotaAnioSocio,$conexionDB['conexionLink']);//contiene conexionDB()	
											
											//echo "<br><br>9-6 modeloPresCoord:mAltaSocioPorGestor:resInsertarCuotaAnioSocio: ";print_r($resInsertarCuotaAnioSocio);	
											
											/*---------------------- Fin  Insertar cuota anio socio -----------*/
											
											if ($resInsertarCuotaAnioSocio['codError']!=='00000')			
											{ $resInsertar = $resInsertarCuotaAnioSocio;
											}
											else //$resInsertarCuotaAnioSocio['codError'] =='00000'
											{
												/*-- Inicio insertar una fila en tabla CONFIRMAREMAILALTAGESTOR para
 											 anotar el número de veces que se envía email de confirmar email, 
												 ahora se pone a "1" envío. 
												------------------------------------------------------------------*/	
												$arrDatosConfirmarEmailAltaGestor['CODUSER'] = $codUser;		 
												$arrDatosConfirmarEmailAltaGestor['FECHAENVIOEMAILULTIMO'] = date('Y-m-d'); 
												$arrDatosConfirmarEmailAltaGestor['FECHARESPUESTAEMAIL'] = '0000-00-00';
												$arrDatosConfirmarEmailAltaGestor['NUMENVIOS'] = 1;						

												$reConfirmarEmailAltaGestor = insertarUnaFila('CONFIRMAREMAILALTAGESTOR',$arrDatosConfirmarEmailAltaGestor,$conexionDB['conexionLink']);//No contiene conexionDB()
												
												//echo "<br><br>10-1 modeloPresCoord:mAltaSocioPorGestor:reConfirmarEmailAltaGestor: ";print_r($reConfirmarEmailAltaGestor);echo "<br>";											
											
											/*--- Fin insertar una fila  en tabla CONFIRMAREMAILALTAGESTOR ----*/
												
												// OJO PARA PRUEBAS solo para pruebas de errors ****************			
												//$reConfirmarEmailAltaGestor['codError'] ='82000';
																							
												if ($reConfirmarEmailAltaGestor['codError'] !=='00000')
												{ $resInsertar = $reConfirmarEmailAltaGestor;
												}														
												else //$reExcelTodosConfExcel['codError']=='00000'
												{
													/*---------------- Inicio COMMIT --------------------------------*/												
													$resFinTrans = commitPDO($conexionDB['conexionLink']);
													
													//echo "<br><br>10-2 modeloPresCoord:mAltaSocioPorGestor:resFinTrans: ";var_dump($resFinTrans);													
													if ($resFinTrans['codError'] !== '00000')//será $resFinTrans['codError'] = '70502'; 									
													{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación. ';
															$resFinTrans['numFilas'] = 0;	
															$arrMensaje['textoComentarios'] = 'Error en el sistema, no se ha podido da de alta al socio/a. Pruebe de nuevo pasado un tiempo. ';													
															$resInsertar = $resFinTrans;	
														//echo "<br><br>10-2-1 modeloPresCoord:mAltaSocioPorGestor:resInsertar: ";print_r($resInsertar);echo "<br>";
													}											
													else
													{ $resAltaSocios['codError'] = '00000';//añadido 2020-03-29
               $resAltaSocios['errorMensaje'] = '';//añadido 2020-03-29
												   $resAltaSocios['datosUsuario'] = $arrDatosUsuario;//incluye ['CODUSER']=$codUser, que se necesitará para cCoordinador.php
												   $resAltaSocios['datosSocio']['CODSOCIO'] = $resInsertarSocio['CODSOCIO'];//por si más adelante se necesitara para cCoordinador.php, ahora no se usa								      												
															$arrMensaje['textoComentarios'] = "Acabas de registrar al nuevo socio/a <b> ".$resValidarCamposAlta['datosFormMiembro']['NOM']['valorCampo']." ".
																																																	$resValidarCamposAlta['datosFormMiembro']['APE1']['valorCampo']." </b>en la base de datos de Europa Laica (EL).											
																																																	<br /><br /><br />Llegará un email a Presidencia, Secretaría, Tesorería y Coordinación de la correspondiente agrupación territorial, 
																																																	con información del alta de la socia/o.<br /><br /><br />".$textoComentarioEmail.$comentarioSubirArchivo;						
													}
													/*---------- Fin COMMIT -----------------------------------------*/
												}//else $reExcelTodosConfExcel['codError']=='00000'
											}//else $resInsertarCuotaAnioSocio['codError']=='00000'
										}//else $resImporteCuotaAnio['codError']=='00000'
									}//else $resInsertarSocio['codError']=='00000'
								}//else $resInsertarMiembro['codError']=='00000'
							}//else $arrDatosMiembroGestor['codError']=='00000'							
					 }//else $resMoverArchivo['codError']=='00000'
	    }//else $resInsertarUsuarioRol['codError']=='00000'				
	   }//else $resInsertarUsuario['codError']=='00000'
			}//else $resulBuscarCodMax['codError']=='00000'
			
			//echo "<br><br>10-3-1 modeloPresCoord:mAltaSocioPorGestor:resAltaSocios: ";print_r($resAltaSocios);echo "<br>";
			//echo "<br><br>10-3-2 modeloPresCoord:mAltaSocioPorGestor:resInsertar: ";print_r($resInsertar);echo "<br>";
	  
			/*---------------- Inicio tratamiento errores ----------------------------------*/	
		
	  if ($resInsertar['codError'] !== '00000')
	  {$arrMensaje['textoComentarios'] = "<br /><br />Error del sistema al realizar el alta del socio/a: <strong> ".$resValidarCamposAlta['datosFormMiembro']['NOM']['valorCampo']." ".
				                                  $resValidarCamposAlta['datosFormMiembro']['APE1']['valorCampo'].
		              "</strong><br /><br />No se ha podido realizar el alta del socio/a.<br /><br /> Vuelva a intentarlo pasado un tiempo, y si no puedes avisa al administrador de la aplicación. ";	  	
				$resInsertar['errorMensaje'] .= $arrMensaje['textoComentarios'];				

				/*--- Inicio borrar archivo recién subido en caso de error en alta ---------*/

			 if (isset($resMoverArchivo['nombreArchExtGuardado']) && !empty($resMoverArchivo['nombreArchExtGuardado']) && $resMoverArchivo['codError'] =='00000')//ser =='00000'	quiere decir que si se pudo subir									    						
				{	require_once './modelos/modeloArchivos.php';
						$resEliminarArchivo = eliminarArchivo($resMoverArchivo['directorioSubir'],$resMoverArchivo['nombreArchExtGuardado']);//no accede a BBDD

						//echo "<br><br>11-1 modeloPresCoord:mAltaSocioPorGestor:resEliminarArchivo: ";print_r($resEliminarArchivo);			
						if ($resEliminarArchivo['codError'] !== '00000') 
						{ $resInsertar['errorMensaje'] .= "<br /><br />".$resEliminarArchivo['errorMensaje'];
								$arrMensaje['textoComentarios'] .= "<br /><br /><br />Aviso: No se ha podido realizar el alta del socio/a y 
								se ha producido un error al eliminar del servidor el archivo con la firma del socio/a,
								por favor informa al administrador de la Aplicación de Gestión de Soci@s";		
						} 	
      else
      { $arrMensaje['textoComentarios'] .= "<br /><br /><br />Aviso: No se ha subido al servidor el archivo con la firma del socio/a ";									
						}			   			
				}//----- Fin borrar el archivo  recién subido en caso de error en alta -------						
				
				//--- Inicio deshacer transación en las tablas modificadas --------------------	
				
				$deshacerTrans = rollbackPDO($conexionDB['conexionLink']);					
			 //echo "<br><br>11-2 modeloPresCoord:mAltaSocioPorGestor:deshacerTrans: ";var_dump($deshacerTrans);						
				if ($deshacerTrans ['codError'] !== '00000')//será $deshacerTrans['codError'] = '70503';			
				{ $resInsertar['errorMensaje'] .= $deshacerTrans['errorMensaje'];								
				}//--- Fin deshacer transación en las tablas modificadas ---------------------					
   		
   }//if ($resInsertar['codError']!=='00000')

  }//else $resIniTrans['codError'] == '00000'	
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--
		if ($resInsertar['codError'] !== '00000' || (isset($deshacerTrans['codError']) && $deshacerTrans['codError'] !== '00000') )
		{	if ( isset($resInsertar['textoComentarios']) ) 
				{ $resInsertar['textoComentarios'] = ". modeloPresCoord.php:mAltaSocioPorGestor(): ".$resInsertar['textoComentarios'];}	
				else
				{	$resInsertar['textoComentarios'] = ". modeloPresCoord.php:mAltaSocioPorGestor(): ";}	
			
				require_once './modelos/modeloErrores.php';
		  $resInsertarErrores = insertarError($resInsertar);				
		  if ($resInsertarErrores['codError'] !== '00000')
    { $resInsertar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			   $arrMensaje['textoComentarios'] .= "<br /><br />Error del sistema al realizar el alta del socio/a, vuelva a intentarlo pasado un tiempo ";
		  }
				
				$resAltaSocios = $resInsertar;
			 //echo "<br><br>11-3 modeloPresCoord:mAltaSocioPorGestor:resInsertar: ";print_r($resInsertar);			
  }//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
		/*---------------- Fin tratamiento errores ------------------------------------*/
	}//else $conexionDB['codError']=="00000"	0"	

	$resAltaSocios['arrMensaje'] = $arrMensaje;
	
	//echo "<br><br>12 modeloPresCoord:mAltaSocioPorGestor:resAltaSocios: ";print_r($resAltaSocios);
	
 return 	$resAltaSocios; 	
}
/*------------------------------ Fin mAltaSocioPorGestor ----------------------------------------*/


//================================ INICIO ENVIAR EMAIL A SOSCIOSPOR GESTORES =======================

/*---------------------------- Inicio buscarSeleccionEmailSociosPres --------------------------------
Forma la cadena select sql y ejecutar las consulta correspondiente para buscar datos los email de los
socios seleccionados con los valores: CODAGRUPACION, CODPAISDOM, CCAA, CODPROV,
y condición MIEMBRO.EMAILERROR = 'NO' y MIEMBRO.INFORMACIONEMAIL = 'SI' "; 

RECIBE: un array $datosSelecionEmailSocios con los valores de los campos CODAGRUPACION, CODPAISDOM,
CCAA, CODPROV, seleccionados en el formulario
 
DEVUELVE: un array $resSeleccionEmailSocios['numfilas] con emails nombre de los 
socios resSeleccionEmailSocios['datosSelecionEmailSocios'] 
y el resSeleccionEmailSocios['codError'] y	resSeleccionEmailSocios['errorMensaje']
y con ['numfilas] que pudiera ser 0	
																			
LLAMADA: cPresidente.php: enviarEmailSociosPres(), cGestorSimps:enviarEmailSimpsGes() 
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php,	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
modeloErrores.php:insertarError()

OBSERVACIONES: solo usada en cPresidente
Incluyo PDO con $arrBindValues. Probada PHP 7.3.21
---------------------------------------------------------------------------------------------------*/
function buscarSeleccionEmailSociosPres($datosSelecionEmailSocios)
{
	//echo "<br><br>0-1 modeloPresCoord:buscarSeleccionEmailSociosPres:datosSelecionEmailSocios: ";print_r($datosSelecionEmailSocios);
	
	$resSeleccionEmailSocios['nomScript'] = 'modeloPresCoord.php';
	$resSeleccionEmailSocios['nomFuncion']= 'buscarSeleccionEmailSociosPres';		
	$resSeleccionEmailSocios['codError'] = '00000';
	$resSeleccionEmailSocios['errorMensaje'] = '';
	$resSeleccionEmailSocios['numFilas'] = -1;

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !=='00000')	
	{ $resSeleccionEmailSocios = $conexionDB;
	}	
	else//	$conexionDB['codError'] =='00000')
	{	
		foreach ($datosSelecionEmailSocios as $indiceCampo => $valCampo)
		{ 
		 if ($valCampo == '-')//separador en formulario: sobraría solo debiera llegar NINGUNA
			{$datosSelecionEmailSocios[$indiceCampo] = 'NINGUNA';	
			}		
		}
		if ($datosSelecionEmailSocios['CODAGRUPACION'] == '%' && $datosSelecionEmailSocios['CODPAISDOM'] == 'NINGUNA' && 
		    $datosSelecionEmailSocios['CCAA'] == 'NINGUNA' && $datosSelecionEmailSocios['CODPROV'] == 'NINGUNA')
		{
			$tablasBusqueda = 'USUARIO,MIEMBRO,SOCIO';
		 $camposBuscados = " MIEMBRO.CODUSER,SOCIO.CODSOCIO, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
			                    IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;
		 //$camposBuscados = "USUARIO.CODUSER,USUARIO.ESTADO,MIEMBRO.EMAIL,MIEMBRO.EMAILERROR,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
			
			$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
			                          AND MIEMBRO.CODUSER   = SOCIO.CODUSER 
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO' 																											
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 
			$arrBind = array();
			
			$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
 		
			//echo '<br><br>2-1 modeloPresCoord:buscarSeleccionEmailSociosPres:cadSql: ';print_r($cadSql);		
		
			$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
		}
		elseif ($datosSelecionEmailSocios['CODAGRUPACION'] !== 'NINGUNA' && $datosSelecionEmailSocios['CODPAISDOM'] == 'NINGUNA' &&
		        $datosSelecionEmailSocios['CCAA'] == 'NINGUNA' && $datosSelecionEmailSocios['CODPROV'] == 'NINGUNA')
		{
		 $tablasBusqueda = 'USUARIO,MIEMBRO,SOCIO';			
		 //$camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";			
			$camposBuscados = " MIEMBRO.CODUSER,SOCIO.CODSOCIO, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
			                    IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;	
																							
			$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
			                          AND MIEMBRO.CODUSER   = SOCIO.CODUSER 
																													AND SOCIO.CODAGRUPACION = :codAgrupacion
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO' 																										
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 
		
			$arrBind = array(':codAgrupacion' => $datosSelecionEmailSocios['CODAGRUPACION']);	
						
			$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
 		
			//echo '<br><br>2-2 modeloPresCoord:buscarSeleccionEmailSociosPres:cadSql: ';print_r($cadSql);		
		
			$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);	
		}
		elseif ($datosSelecionEmailSocios['CODAGRUPACION'] == 'NINGUNA' && $datosSelecionEmailSocios['CODPAISDOM'] !== 'NINGUNA' &&
		    $datosSelecionEmailSocios['CCAA'] == 'NINGUNA' && $datosSelecionEmailSocios['CODPROV'] == 'NINGUNA')
		{
		 $tablasBusqueda = 'USUARIO,MIEMBRO';
		 //$camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
			$camposBuscados = " MIEMBRO.CODUSER, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
			                    IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;	
																							
			$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
			                          AND MIEMBRO.CODPAISDOM = :codPaisDom
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO' 																											
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 			
			
			$arrBind = array(':codPaisDom' => $datosSelecionEmailSocios['CODPAISDOM']);	
					
			$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
 		
			//echo '<br><br>2-3 modeloPresCoord:buscarSeleccionEmailSociosPres:cadSql: ';print_r($cadSql);		
		
			$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);				
		}
		elseif ($datosSelecionEmailSocios['CODAGRUPACION'] == 'NINGUNA' && 
		($datosSelecionEmailSocios['CODPAISDOM']=='NINGUNA' || $datosSelecionEmailSocios['CODPAISDOM']=='ES') &&
		 $datosSelecionEmailSocios['CCAA'] !== 'NINGUNA' && $datosSelecionEmailSocios['CODPROV'] == 'NINGUNA')
		{
		 $tablasBusqueda = 'USUARIO,MIEMBRO,PROVINCIA';
		 //$camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
			$camposBuscados = " MIEMBRO.CODUSER, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
			                    IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;		
																							
			$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
			                          AND MIEMBRO.CODPAISDOM = 'ES'
			                          AND MIEMBRO.CODPROV = PROVINCIA.CODPROV
																													AND PROVINCIA.CCAA = :ccaa
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO' 																											
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 
			
			$arrBind = array(':ccaa' => $datosSelecionEmailSocios['CCAA']);
		
			$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
 		
			//echo '<br><br>2-4 modeloPresCoord:buscarSeleccionEmailSociosPres:cadSql: ';print_r($cadSql);		
		
			$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);				
		}
		elseif ($datosSelecionEmailSocios['CODAGRUPACION']=='NINGUNA' && 
		        ($datosSelecionEmailSocios['CODPAISDOM']=='NINGUNA' || $datosSelecionEmailSocios['CODPAISDOM']=='ES') && 
		         $datosSelecionEmailSocios['CCAA']=='NINGUNA' && $datosSelecionEmailSocios['CODPROV'] !=='NINGUNA'
									)
		{
		 $tablasBusqueda = 'USUARIO,MIEMBRO';
		 //$camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
			$camposBuscados = " MIEMBRO.CODUSER,/*SOCIO.CODSOCIO,*/ MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
			                    IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;	
																							
			$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
		                           AND MIEMBRO.CODPROV = :codProv
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO' 																											
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 
		 		
			$arrBind = array(':codProv' => $datosSelecionEmailSocios['CODPROV']);	
			
			$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
 		
			//echo '<br><br>2-5 modeloPresCoord:buscarSeleccionEmailSociosPres:cadSql: ';print_r($cadSql);		
		
			$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
		}		
		else //Sobraría: No se debiera entrar nunca solo debiera llegar NINGUNA
		{//echo "<br><br>3 modeloPresCoord:buscarSeleccionEmailSociosPres:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);	
		
			$resSeleccionEmailSocios['codError'] = '70601';
		 $resSeleccionEmailSocios['errorMensaje'] = 'ERROR: Faltan variables-parámetros necesarios para SQL en cPresidente.php:enviarEmailSociosPres():modeloPresCoord:buscarSeleccionEmailSociosPres()';
		}
	
	 //echo "<br><br>4 modeloPresCoord:buscarSeleccionEmailSociosPres:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);	
	
	 if ($resSeleccionEmailSocios['codError'] !== '00000' && $resSeleccionEmailSocios['codError'] <= '80000')
		{
			$resSeleccionEmailSocios['textoComentarios'] =	"Error del sistema, cPresidente.php:enviarEmailSociosPres():modeloPresCoord.php:buscarSeleccionEmailSociosPres() ";			
	  
			require_once './modelos/modeloErrores.php';
			insertarError($resSeleccionEmailSocios);		
		}
		elseif ($resSeleccionEmailSocios['numFilas'] === 0 ) 
		{ 
 			$resSeleccionEmailSocios['codError'] = '80001';//no es realmente un error, puede haber selecciones que no devuelvan emails			
		  $resSeleccionEmailSocios['errorMensaje'] ="No hay ningún email de socios/as para las condiciones de selección de socios/as que has elegidas (En agrupación, País, CCAA, Provincia )";															
		}
		
	}//else $conexionDB['codError'] =='00000')
	
	//echo "<br><br> modeloPresCoord:buscarSeleccionEmailSociosPres:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);	

	return $resSeleccionEmailSocios;
}
/*---------------------------- Fin cadBuscarSeleccionEmailSociosPres ------------------------------*/	


/*---------------------------- Inicio buscarSeleccionEmailSociosCoord -------------------------------
Desde rol Coordinador, se forma la cadena select sql para buscar datos los email de los socios seleccionados
con los valores: $datosSelecionEmailSocios[CODAGRUPACION] y $codAreasGestionAgrup
y condición MIEMBRO.EMAILERROR = 'NO' y MIEMBRO.INFORMACIONEMAIL = 'SI' "

RECIBE: $datosSelecionEmailSocios con los valores de los campos CODAGRUPACION, 
$codAreasGestionAgrup: El codigo de área de gestión de ese coordinador,
que puede incluir una solo provicia, o una CCAA o alguna mezcla.  

DEVUELVE: un array $resSeleccionEmailSocios con emailS, nombre de los 
socios resSeleccionEmailSocios['datosSelecionEmailSocios'] 
y el resSeleccionEmailSocios['codError'] y	resSeleccionEmailSocios['errorMensaje']	
y con ['numfilas] que pudiera ser 0
																			
LLAMADA: cCoordinador.php:enviarEmailSociosCoord()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php,	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
modeloErrores.php:insertarError()

OBSERVACIONES: solo usada en cCoordinador
Incluyo PDO: con $arrBindValues. Probada PHP 7.3.21
---------------------------------------------------------------------------------------------------*/
function buscarSeleccionEmailSociosCoord($datosSelecionEmailSocios,$codAreasGestionAgrup)
{
	//echo "<br><br>0-1 modeloPresCoord:buscarSeleccionEmailSociosCoord:datosSelecionEmailSocios: ";print_r($datosSelecionEmailSocios);	
	//echo "<br><br>0-2 modeloPresCoord:buscarSeleccionEmailSociosCoord:codAreasGestionAgrup: ";print_r($codAreasGestionAgrup);
	
	$resSeleccionEmailSocios['nomScript'] = 'modeloPresCoord.php';
	$resSeleccionEmailSocios['nomFuncion']= 'buscarSeleccionEmailSociosCoord';	
	$resSeleccionEmailSocios['codError'] = '00000';
 $resSeleccionEmailSocios['numFilas'] = -1;	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $resSeleccionEmailSocios = $conexionDB;
	}
	else//	$conexionDB['codError'] =='00000')  $datosSelecionEmailSocios['CODAGRUPACION'] !== 'Elige'
	{
  if ( !isset($datosSelecionEmailSocios['CODAGRUPACION']) || !isset($codAreasGestionAgrup) ||
			     empty($datosSelecionEmailSocios['CODAGRUPACION']) || empty($codAreasGestionAgrup)   )
		{ 	   
			$resSeleccionEmailSocios['codError'] = '70601';
		 $resSeleccionEmailSocios['errorMensaje'] = 'ERROR: Faltan variables-parámetros necesarios para SQL en cCoordinador.ph:enviarEmailSociosCoord():modeloPresCoord:buscarSeleccionEmailSociosCoord()';
			$resSeleccionEmailSocios['arrMensaje']['textoComentarios'] = $resSeleccionEmailSocios['errorMensaje'];
			//echo "<br><br>1-1 modeloPresCoord:buscarSeleccionEmailSociosCoord:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);				  		
		}
		else //!if ( !isset($datosSelecionEmailSocios['CODAGRUPACION']) || !isset($codAreasGestionAgrup) )
		{		
	 
			if ($datosSelecionEmailSocios['CODAGRUPACION'] == 'Elige' || $datosSelecionEmailSocios['CODAGRUPACION'] == '-'  )
			{ //echo "<br><br>1-2 modeloPresCoord:buscarSeleccionEmailSociosCoord:datosSelecionEmailSocios: ";print_r($datosSelecionEmailSocios);	
					
					$datosSelecionEmailSocios['CODAGRUPACION'] = 'Elige';		//para '-' siempre mostrar 'Elige' en form 	
			}				
			//echo "<br><br>1-3 modeloPresCoord:buscarSeleccionEmailSociosCoord:datosSelecionEmailSocios: ";print_r($datosSelecionEmailSocios);	
			
			if ($datosSelecionEmailSocios['CODAGRUPACION'] == '%')//if ($codAgrupacion == '%' = Todas
			{		 
				$tablasBusqueda = 'USUARIO,MIEMBRO,SOCIO';
				$camposBuscados = " MIEMBRO.CODUSER,SOCIO.CODSOCIO, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
																								IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;			
		
				if ( $codAreasGestionAgrup == '%')//creo que no entrará aquí nunca, lo dejo por si más adelante modifico
				{ $cadCondicionesBuscarAreasGestionAgrup ='';		
						$arrBind = array();	
				}	
				else //entrará siempre 
				{ $cadCondicionesBuscarAreasGestionAgrup =' AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = :codAreasGestionAgrup';	
						$arrBind = array(':codAreasGestionAgrup' => $codAreasGestionAgrup);
				}				
		
				$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
																													AND MIEMBRO.CODUSER   = SOCIO.CODUSER 
																													AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																													AND MIEMBRO.EMAILERROR = 'NO'
																													AND MIEMBRO.INFORMACIONEMAIL = 'SI'
																													
																													AND SOCIO.CODAGRUPACION IN   
																													(SELECT DISTINCT AGRUPACIONTERRITORIAL.CODAGRUPACION 
																														FROM AREAGESTIONAGRUPACIONESCOORD,AGRUPACIONTERRITORIAL
																														WHERE AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION ".																								
																																$cadCondicionesBuscarAreasGestionAgrup."	)	";
																												//AND SUBSTRING(MIEMBRO.EMAIL, -9) != 'falta.com'";//esta incluido en EMAILERROR ='NO'	
				
				$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
				
				//echo '<br><br>2-1 modeloPresCoord:buscarSeleccionEmailSociosCoord:cadSql: ';print_r($cadSql);		
			
				$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
			
				//echo '<br><br>2-2 modeloPresCoord:buscarSeleccionEmailSociosCoord:resSeleccionEmailSocios: ';print_r($resSeleccionEmailSocios);		
				
			}//if ($datosSelecionEmailSocios['CODAGRUPACION'] == '%')//if ($codAgrupacion == '%')		
			elseif ($datosSelecionEmailSocios['CODAGRUPACION'] !== 'Elige' && $datosSelecionEmailSocios['CODAGRUPACION'] !== '-' ) //cuelquier agrupación
			{
				$tablasBusqueda = "USUARIO,MIEMBRO,SOCIO";
				$camposBuscados = " MIEMBRO.CODUSER,SOCIO.CODSOCIO, MIEMBRO.EMAIL, SEXO, NOM, APE1, IFNULL(APE2,'') as APE2, UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, 
																								IFNULL(MIEMBRO.TELMOVIL,'') as TELMOVIL, IFNULL(MIEMBRO.TELFIJOCASA,'') as TELFIJOCASA " ;						
																					
				$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
																														AND MIEMBRO.CODUSER   = SOCIO.CODUSER 
																														AND SOCIO.CODAGRUPACION = :codAgrupacion AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'	
																														AND MIEMBRO.EMAILERROR = 'NO' 		
																														AND MIEMBRO.INFORMACIONEMAIL = 'SI' "; 
																														
				$arrBind = array(':codAgrupacion' => $datosSelecionEmailSocios['CODAGRUPACION']);
			
				$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
				
				//echo '<br><br>3-1 modeloPresCoord:buscarSeleccionEmailSociosCoord:cadSql: ';print_r($cadSql);
			
				$resSeleccionEmailSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
				
				//echo '<br><br>3-2 modeloPresCoord:buscarSeleccionEmailSociosCoord:resSeleccionEmailSocios: ';print_r($resSeleccionEmailSocios);
				
			}//elseif ($datosSelecionEmailSocios['CODAGRUPACION'] !== 'Elige' && $datosSelecionEmailSocios['CODAGRUPACION'] !== '-' )//cuelquier agrupación
			else
			{	$resSeleccionEmailSocios['codError'] = '70601';
					$resSeleccionEmailSocios['errorMensaje'] = 'ERROR: Faltan variables-parámetros adecuadas en agrupación del área de gestión territorial necesarios para SQL en cCoordinador.php:enviarEmailSociosCoord():modeloPresCoord:buscarSeleccionEmailSociosCoord()';
		 }		
	 }//else !if ( !isset($datosSelecionEmailSocios['CODAGRUPACION']) || !isset($codAreasGestionAgrup) )
		
		//echo "<br><br>5 modeloPresCoord:buscarSeleccionEmailSociosCoord:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);	
			
	 if ($resSeleccionEmailSocios['codError'] !== '00000' && $resSeleccionEmailSocios['codError'] <= '80000')
		{
			$resSeleccionEmailSocios['textoComentarios'] = "Error del sistema en cCoordinador.php:enviarEmailSociosCoord():modeloPresCoord.php:buscarSeleccionEmailSociosCoord() ";			

			require_once './modelos/modeloErrores.php';
			insertarError($resSeleccionEmailSocios,$conexionDB['conexionLink']);
		}
		elseif ($resSeleccionEmailSocios['numFilas'] === 0 ) 
		{ 
		  $resSeleccionEmailSocios['codError'] = '80001';	$resSeleccionEmailSocios['codError'] = '80001';//no es realmente un error, puede haber selecciones que no devuelvan emails					
				$resSeleccionEmailSocios['errorMensaje'] = 'ERROR: No hay ningún socio/a para las condiciones de selección elegidas';								
		}
		
	}//else $conexionDB['codError'] =='00000')
	
	//echo "<br><br>6 modeloPresCoord:buscarSeleccionEmailSociosCoord:resSeleccionEmailSocios: ";print_r($resSeleccionEmailSocios);	

	return $resSeleccionEmailSocios;
}
/*---------------------------- Fin buscarSeleccionEmailSociosCoord ---------------------------------*/

//================================ FIN ENVIAR EMAIL A SOSCIOSPOR GESTORES ===========================



// *************** INICIO EXCEL EXPORTACION DATOS SOCIOS *****************************************

/*---------------------------- Inicio exportarExcelSociosPres -------------------------------------
LLama a modeloPresCoord:cadBuscarSociosExcelPresCoord() para formar la cadena 
select para buscar los datos de todos los socios de una determinada agrupación 
territorial o todas, ejecuta la consulta y la pasa la función exportarExcel() 
que forma el archivo Excel.

Al finalizar, se descarga el archivo Excel mediante el navegador, controla los 
posibles errores en todas las funciones, excepto en  exportarExcel() que no 
controla si se ha producido un error en la formación del archivo Excel 
(poco probable ya que no hay SELET).

RECIBE: $codAgrupacion el cógido de un agrupación tipo "00100000" o % para todas
para hacer el select

DEVUELVE: El archivo Excel de la agrupación correspondiente o si % de todas

LLAMADA: cPresidente.php:excelSociosPres()
        
LLAMA: modeloPresCoord:cadBuscarSociosExcelPresCoord(),
							modeloMySQL.php:buscarCadSql()
							modelos/libs/exportarExcel.php:exportarExcel();
							usuariosConfig/BBDD/MySQL/configMySQL.php,
							modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
							modelos/modeloErrores.php:insertarError()
								
OBSEVACIONES:					
OJO: Al utilizar "header()" en funciones en el proceso no se puede utilizar 
echo u otras salidas de pantalla antes de esta función pues daría error 
al formar el buffer de salida	

2020-06-08: no uso PHP:PDOStatement::bindParamValues, lo usan internamente 
algunas funciones aquí llamadas	
--------------------------------------------------------------------------------*/
function exportarExcelSociosPres($codAgrupacion)//Nuevo nombre independiente de Coord
{
	//echo "<br><br>0-1 modeloPresCoord:exportarExcelSociosPres:codAgrupacion: ";print_r($codAgrupacion);
	
	require_once './modelos/modeloErrores.php';
	require_once 'BBDD/MySQL/modeloMySQL.php';
	
	$resExportarSocios['nomScript'] = "modeloPresCoord.php";
	$resExportarSocios['nomFuncion'] = "exportarExcelSociosPres";	
	$resExportarSocios['textoComentarios'] = '';
 $resExportarSocios['codError'] = '00000';
 $resExportarSocios['errorMensaje'] = '';	
	$resExportarSocios['numFilas'] = -1;
	//$resExportarSocios['textoCabecera'] ='Exportar datos de socios/as a Excel para uso interno de Presidencia, Vice, Secretaría';
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarSocios = $conexionDB;
	} 
	else //if ($conexionDB['codError']=='00000')
	{
		$cadSql = cadBuscarSociosExcelPresCoord($codAgrupacion);//OK en en modeloPresCoord.php; $cadListaAgrupacionesArea = %, o una lista sera : "0100000","011000",...", no uso $arrBindValues 
	
  //echo "<br><br>1-1 modeloPresCoord:exportarExcelSociosPres:cadSql: ";print_r($cadSql);	
		
	 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";
	 
		$resBuscarSocios = buscarCadSql($cadSql,$conexionDB['conexionLink']);//OK en en modeloMys.php		Se podría hacer una consulta más restrigida	
  
		//echo "<br><br>3 modeloPresCoord:exportarExcelSociosPres:resBuscarSocios: ";print_r($resBuscarSocios);
			
		if ($resBuscarSocios['codError'] !== '00000')
		{ $resExportarSocios['codError'] = $resBuscarSocios['codError'];
				$resExportarSocios['errorMensaje'] = $resBuscarSocios['errorMensaje'];	
				$resExportarSocios['textoComentarios'] = $resExportarSocios['nomScript'].": ".$resExportarSocios['nomFuncion'].", buscarCadSql()";					
				insertarError($resExportarSocios);					
		}
		elseif ($resBuscarSocios['numFilas'] === 0)
		{ $resExportarSocios['codError'] = '80001';
		  $resExportarSocios['numFilas'] = $resBuscarSocios['numFilas'];
			 $resExportarSocios['textoComentarios'] =' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';
				$resExportarSocios['errorMensaje'] = $resExportarSocios['textoComentarios'];  
		}
		else	//$resExportarSocios['numFilas']>0)
		{ //echo "<br><br>4 modeloPresCoord:exportarExcelSociosPres:resBuscarSocios['numFilas']: ";print_r($resBuscarSocios['numFilas']);
	   
	   $nomFile = 'Excel_pres';
				
				require_once './modelos/libs/exportarExcel.php';				
				$archivoExcelSocios = exportarExcel($resBuscarSocios,$nomFile);//OK	
				
    if ($archivoExcelSocios['codError'] !== '00000')
				{ $resExportarSocios = $archivoExcelSocios;
						$resExportarSocios['textoComentarios'] .= ":	exportarExcelSociosPres. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
						$resExportarSocios['nomFuncion'] .=", exportarExcelSociosPres()";					
				}
				else //lo siguiente no se ve porque el buffer está cautivo al descargar el archivo Excel, habrá que arreglarlo
				{$resExportarSocios = $archivoExcelSocios; 
					$resExportarSocios['textoComentarios'] = " Se han exportado a un archivo Excel de nombre <strong>".$archivoExcelSocios['nomFile']. "</strong> los datos de </strong>". 
					                                           $archivoExcelSocios['numFilas']. " socias/os</strong>";		
					$resExportarSocios['nomFile'] = $nomFile;	
     $resExportarSocios['numFilas'] = $archivoExcelSocios['numFilas'];			
				}				
		}//else	$resExportarSocios['numFilas']>0)
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>5 modeloPresCoord:exportarExcelSociosPres:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}
/*---------------------------- Fin exportarExcelSociosPres --------------------------------------*/

/*---------------------------- Inicio exportarExcelSociosCoord ------------------------------------
LLama a buscarListaAgrupAreaGestion para obtener los datos del las agrupaciones 
que corresponden al área de gestión de ese coordinador y despues con 
cadBuscarSociosExcelPresCoord() forma la cadena select para buscar los datos de 
todos los socios de una/s determinada/s agrupación/es territorial o todas 
elegidas en el form, ejecuta la consulta y la pasa la función exportarExcel() 
que forma el archivo Excel.
		
Al finalizar, se descarga el archivo Excel mediante el navegador, controla los 
posibles errores en todas las funciones, excepto en  exportarExcel() que no 
controla si se ha producido un error en la formación del archivo Excel 
(poco probable ya que no hay SELET).

RECIBE: $cadListaAgrupacionesArea el cógido de un agrupación tipo  "00100000" o
o $cadListaAgrupacionesArea: %, en este caso habrá que buscar las agrupaciones que 
corresponden a ese área de gestión mediante buscarListaAgrupAreaGestion($codUser);	
$codUser es el del gestor $_SESSION['vs_CODUSER'] del área de gestión

LLAMADA: cCoordinador.php:excelSociosCoord()
LLAMA: modeloPresCoord:buscarListaAgrupAreaGestion($codUser),cadBuscarSociosExcelPresCoord(),
modeloMySQL.php:buscarCadSql(),modelos/libs/exportarExcel.php:exportarExcel(); 
usuariosConfig/BBDD/MySQL/configMySQL.php,
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
BBDD/MySQL/modeloMySQL.php:buscarCadSql()
modelos/modeloErrores.php:insertarError()
	
OBSEVACIONES:					
OJO: Al utilizar "header()" en funciones en el proceso no se puede utilizar 
echo u otras salidas de pantalla antes de esta función pues daría error 
al formar el buffer de salida	
				
2020-05-20: no uso PHP:PDOStatement::bindParamValues, lo usan internamente 
algunas funciones aquí llamadas	-
------------------------------------------------------------------------------*/
function exportarExcelSociosCoord($cadListaAgrupacionesArea, $codUser)//Llamada solo desde cCoordinador.php:excelSociosCoord(), acaso se puede unificar para  cPresidente.php:excelSociosPres()
{
	//echo "<br><br>0-1 modeloPresCoord:exportarExcelSociosCoord:cadListaAgrupacionesArea: ";print_r($cadListaAgrupacionesArea);
	//echo "<br><br>0-2 modeloPresCoord:exportarExcelSociosCoord:codUser: ";print_r($codUser);
	
	require_once './modelos/modeloErrores.php';
	require_once 'BBDD/MySQL/modeloMySQL.php';
	
	$resExportarSocios['nomScript'] = "modeloPresCoord.php";
	$resExportarSocios['nomFuncion'] = "exportarExcelSociosCoord";	
	$resExportarSocios['textoComentarios'] = '';
 $resExportarSocios['codError'] = '00000';
 $resExportarSocios['errorMensaje'] = '';	
	$resExportarSocios['numFilas'] = -1;
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarSocios = $conexionDB;
	}     
	else //if ($conexionDB['codError'] == '00000')
	{		
			if ($cadListaAgrupacionesArea !== '%' )//será una sola agrupación tipo $cadListaAgrupacionesArea = "011400" y no es necesario buscar la lista de agrup del area de gestión 
			{ 			  
				 $listaAgrupacionesArea = $cadListaAgrupacionesArea;
			}
   else//$cadListaAgrupacionesArea = %, y habrá que buscar "todas" las agrupaciones de ese área de gestión y obtendremos una lista como: "00100000","00111000",...,,"00141000",
			{			
	    //$reListaAgrupAreaGestion = buscarListaAgrupAreaGestion($_SESSION['vs_CODUSER']);//podría servir
					$reListaAgrupAreaGestion = buscarListaAgrupAreaGestion($codUser);//OK en modeloPresCoord.php
				
			  //echo "<br><br>2-2 modeloPresCoord:exportarExcelSociosCoord:reListaAgrupAreaGestion: ";print_r($reListaAgrupAreaGestion);
					
					if ($reListaAgrupAreaGestion['codError'] !== '00000')	
					{ $resExportarSocios['codError'] = $reListaAgrupAreaGestion['codError'];
				   $resExportarSocios['errorMensaje'] = $reListaAgrupAreaGestion['errorMensaje'];	
				   $resExportarSocios['textoComentarios'] = $resExportarSocios['nomScript'].": ".$resExportarSocios['nomFuncion'].", buscarListaAgrupAreaGestion()";
		 				insertarError($resExportarSocios);						
					}
					else
					{ 
				   $listaAgrupacionesArea = 	$reListaAgrupAreaGestion['cadListaAgrupacionesArea'];		
					}					
			}//else $cadListaAgrupacionesArea = %
			
			//echo "<br><br>3 modeloPresCoord:exportarExcelSociosCoord:resExportarSocios: ";print_r($resExportarSocios);
			
			if ($resExportarSocios['codError'] == '00000')
			{ 					
					$cadSql = cadBuscarSociosExcelPresCoord($listaAgrupacionesArea);//OK en en modeloPresCoord.php; $cadListaAgrupacionesArea = %, o una lista sera : "0100000","011000",...", no uso $arrBindValues 
					
					//echo "<br><br>4-1 modeloPresCoord:exportarExcelSociosCoord:cadSql: ";print_r($cadSql);
	    						
					$resBuscarSocios = buscarCadSql($cadSql,$conexionDB['conexionLink']);//OK en en modeloMys.php			 
					
					//echo "<br><br>4-2 modeloPresCoord:exportarExcelSociosCoord:resBuscarSocios: ";print_r($resBuscarSocios);
						
					if ($resBuscarSocios['codError'] !== '00000')
					{ $resExportarSocios['codError'] = $resBuscarSocios['codError'];
				   $resExportarSocios['errorMensaje'] = $resBuscarSocios['errorMensaje'];	
				   $resExportarSocios['textoComentarios'] = $resExportarSocios['nomScript'].": ".$resExportarSocios['nomFuncion'].", buscarCadSql()";
		 				insertarError($resExportarSocios);						
					}					
					elseif ($resBuscarSocios['numFilas'] === 0)
					{ $resExportarSocios['codError'] = '80001';
					  $resExportarSocios['numFilas'] = $resBuscarSocios['numFilas'];
							$resExportarSocios['textoComentarios'] = ' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';
							$resExportarSocios['errorMensaje'] .= $resExportarSocios['textoComentarios'];  
					}
					else	//$resExportarSocios['numFilas']>0)
					{ 							
							$nomFile = 'Excel_coord';
							
							require_once './modelos/libs/exportarExcel.php';				
							$archivoExcelSocios = exportarExcel($resBuscarSocios,$nomFile);//OK	
							
							//echo "<br><br>5 modeloPresCoord:exportarExcelSociosCoord:archivoExcelSocios: ";print_r($archivoExcelSocios);echo "<br>";
							
							if ($archivoExcelSocios['codError'] !== '00000')
							{ $resExportarSocios = $archivoExcelSocios;
									$resExportarSocios['textoComentarios'] .= ":	exportarExcelSociosCoord. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
									$resExportarSocios['nomFuncion'] .=", exportarExcelSociosCoord()";					
							}
							else //lo siguiente no se ve porque el buffer está cautivo al descargar el archivo Excel, habrá que arreglarlo
							{ $resExportarSocios = $archivoExcelSocios;
									$resExportarSocios['textoComentarios'] = " Se han exportado a un archivo Excel de nombre <strong>".$archivoExcelSocios['nomFile']. "</strong> los datos de </strong>". 
									                                           $archivoExcelSocios['numFilas']. " socias/os</strong>";		
									$resExportarSocios['nomFile'] = $nomFile;	
         $resExportarSocios['numFilas'] = $archivoExcelSocios['numFilas'];										
							}								
					}//else	$resExportarSocios['numFilas']>0
	  }//if ($resExportarSocios['codError'] == '00000
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>6 modeloPresCoord:exportarExcelSociosCoord:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}
/*---------------------------- Fin exportarExcelSociosCoord -------------------------------------*/

/*---------------------------- Inicio cadBuscarSociosExcelPresCoord -------------------------------
Forma la cadena select sql para busca los datos de todos los socios de una de 
una o más agrupaciones con el código contenidas en $cadListaAgrupacionesArea 
											
RECIBE: $cadListaAgrupacionesArea que es un string que contiene el códido de una o mas agrupaciones 
        tipo  "00100000","00111000",...,,"00141000",
DEVUELVE: una cadena (string) con la SELECT	
										
LLAMADA: modeloPresCoord:exportarExcelSociosCoord(),exportarExcelSociosPres()		
	
OBSERVACIONES:	
2020-05-20: no uso PDOStatement::bindParamValues, más complejo y no es necesario
2017-03-01: quita	AND MIEMBRO.INFORMACIONEMAIL = 'SI', para que incluya a todos				
2016-06-03: mejora gestión de áreas y agrupaciones por los coordinadores y gestores de área										
2016-04-21: Añado MIEMBRO.CP a petición de un Coordinador.	
2016-08-11: Añado MIEMBRO.TELMOVIL, MIEMBRO.TELFIJOCASA a petición del Presidente.
------------------------------------------------------------------------------*/
function cadBuscarSociosExcelPresCoord($cadListaAgrupaciones)  
{	
 //echo '<br><br>0 modeloPresCoord:cadBuscarSociosExcelPresCoord:cadListaAgrupaciones: ';print_r($cadListaAgrupaciones);

	if ( $cadListaAgrupaciones == '%' )//creo que no tendrá este valor, pero por si acaso en presi
 { $condicionAgrup = '';		
 }
	else 
	{ $condicionAgrup = " AND SOCIO.CODAGRUPACION IN ($cadListaAgrupaciones)";//no uso PHP: PDOStatement::bindParamValues, más complejo y no es necesario
	}	

	$tablasBusqueda = " USUARIO,SOCIO,AGRUPACIONTERRITORIAL,MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV ";	
																		
	$camposBuscados = " UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom,USUARIO.ESTADO,MIEMBRO.FECHANAC,
																			MIEMBRO.CODPAISDOM, MIEMBRO.LOCALIDAD,MIEMBRO.CP,PROVINCIA.NOMPROVINCIA,										
																			
																			MIEMBRO.EMAIL,EMAILERROR,INFORMACIONEMAIL,IFNULL(MIEMBRO.TELMOVIL,'-') as TELMOVIL,IFNULL(MIEMBRO.TELFIJOCASA,'-') as TELCASA,
																			AGRUPACIONTERRITORIAL.NOMAGRUPACION, SOCIO.FECHAALTA ";	
																											
 $cadCondicionesBuscar = " WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                           AND USUARIO.CODUSER=SOCIO.CODUSER
                           AND (SOCIO.FECHABAJA IS NULL OR SOCIO.FECHABAJA ='0000-00-00')
                           AND SOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION
													              AND SUBSTRING(USUARIO.ESTADO,'1',4) ='alta'"
                           .$condicionAgrup.																											
																											//" ORDER BY NOMAGRUPACION, MIEMBRO.CODPAISDOM, PROVINCIA.NOMPROVINCIA, apeNom ";	
                           " ORDER BY NOMAGRUPACION, apeNom ";																												
														
	$cadBuscarDatosSocios = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
																																														
	//echo '<br><br>2 modeloPresCoord:cadBuscarSociosExcelPresCoord:cadBuscarDatosSocios: ';print_r($cadBuscarDatosSocios);
	
	return $cadBuscarDatosSocios;
}
/*------------------------------ Fin cadBuscarSociosExcelPresCoord ------------------------------*/

/*---------- Inicio  buscarListaAgrupAreaGestion --------------------------------------------------
Para gestión de áreas y agrupaciones por los coordinadores y gestores de área.

Devuelve array con una cadena $arrListaAgrupAreaGestion['cadListaAgrupacionesArea']
tipo  "00100000","00111000",...,"00141000", con los códigos de las agrupaciones 
territoriales pertenencientes a un área de gestión y los habitules mensaje 
de error de las búsquedas.

RECIBE: $codUser del coordinador gestor
DEVUELVE: $arrListaAgrupAreaGestion: 
$arrListaAgrupAreaGestion['cadListaAgrupacionesArea'],$arrListaAgrupAreaGestion['codError'],...

LLAMADA: modeloPresCoord.php:exportarExcelSociosCoord(), 
(por ahora solo para exportar cCoordinador.php:excelSociosCoord()
LLAMA: modeloMySQL.php:buscarCadSql()
usuariosConfig/BBDD/MySQL/configMySQL.php; 
BBDD/MySQL/conexionMySQL.php:conexionDB()					
				
OBSERVACIONES:
Agustín: 2020-05-21: Incluyo PHP: PDOStatement::bindParam				
------------------------------------------------------------------------------*/
function buscarListaAgrupAreaGestion($codUser)
{ 
 //echo "<br><br>0-1 modeloPresCoord:buscarListaAgrupAreaGestion:codUser: ";print_r($codUser); 
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ $resBuscarListaAgrupAreaGestion = $conexionDB;
	}
	else //$conexionDB['codError'] == '00000'
	{
		if ( !isset($codUser) || empty($codUser) || $codUser =='%' ) //tiene que existir un unico valor de codUser
  { 
		  $arrListaAgrupAreaGestion['codError'] = '70601'; 
	   $arrListaAgrupAreaGestion['errorMensaje'] = " Error sistema: Faltan variables-parámetros necesarios para SQL ";
			 $arrMensaje['textoComentarios'] = " Faltan parámetro codUser en buscarListaAgrupAreaGestion() ";	
	 }
 	else//if ( !sset($codUser) && !empty($codUser) )
	 { 
		  $cadCondicionesBuscar = " AND COORDINAAREAGESTIONAGRUP.CODUSER = :codUser ";
				$arrBind = array(':codUser' => $codUser );
	
	
	  	$cadSqlAgrupacionesArea = " SELECT COORDINAAREAGESTIONAGRUP.*, AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP AS 'codigoAreaGestion', 
		                                   AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION AS 'codigoAgrupacion', AREAGESTION.*
	
                                FROM COORDINAAREAGESTIONAGRUP,AREAGESTIONAGRUPACIONESCOORD, AREAGESTION

																													   WHERE COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP = AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP
																												        AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = AREAGESTION.CODAREAGESTION ".$cadCondicionesBuscar;
																																				
				//echo "<br><br>1-1 modeloPresCoord:buscarListaAgrupAreaGestion:cadSqlAgrupacionesArea: ";print_r($cadSqlAgrupacionesArea);																																					
				
				require_once "./modelos/BBDD/MySQL/modeloMySQL.php";
				
				$resBuscarListaAgrupAreaGestion = buscarCadSql($cadSqlAgrupacionesArea,$conexionDB['conexionLink'],$arrBind);
				
				//echo "<br><br>1-2 modeloPresCoord:buscarListaAgrupAreaGestion:resBuscarListaAgrupAreaGestion: ";print_r($resBuscarListaAgrupAreaGestion);	
						
				if ($resBuscarListaAgrupAreaGestion['codError'] !== '00000')
				{ $arrListaAgrupAreaGestion =	$resBuscarListaAgrupAreaGestion;		 
						$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos de las agrupaciones de un ÁREA de GESTIÓN,
																																								vuelva a intentarlo pasado un tiempo ";
				}
				elseif ($resBuscarListaAgrupAreaGestion['numFilas'] == 0)
				{ $arrListaAgrupAreaGestion['codError'] = '80005'; //no encontrado
						$arrListaAgrupAreaGestion['errorMensaje'] = " Error sistema: No ese han econtrado agrupación gestionadas por este usuario ";
						$arrMensaje['textoComentarios'] = " Error del sistema al buscar datos agrupación: No existe ese AREA de GESTIONn";	
				}
				else
				{ $arrListaAgrupAreaGestion['codError'] = '00000'; 
						$arrListaAgrupAreaGestion['errorMensaje'] = "";	
						$arrMensaje['textoComentarios'] = "";
									
						$cadListaAgrupacionesArea = "";				
						$fila = 0;
						
						while ($fila < $resBuscarListaAgrupAreaGestion['numFilas'])
						{
									if (isset($resBuscarListaAgrupAreaGestion['resultadoFilas'][$fila]['codigoAgrupacion']) && !empty ($resBuscarListaAgrupAreaGestion['resultadoFilas'][$fila]['codigoAgrupacion']))
									{ 
												$cadListaAgrupacionesArea .= "\"".$resBuscarListaAgrupAreaGestion['resultadoFilas'][$fila]['codigoAgrupacion']."\",";						
									}	
									$fila++;	
						}
						$arrListaAgrupAreaGestion['cadListaAgrupacionesArea'] = rtrim($cadListaAgrupacionesArea, ",");		    
							
						//echo "<br><br>3  modeloPresCoord:buscarListaAgrupAreaGestion:	arrListaAgrupAreaGestion: ";print_r($arrListaAgrupAreaGestion);
				}
	 }//else if ( !sset($codUser) && !empty($codUser) )	
	}//else $conexionDB['codError'] == '00000'

	$arrListaAgrupAreaGestion['arrMensaje'] = $arrMensaje['textoComentarios'];

 //echo "<br><br>4 modeloSocios:buscarListaAgrupAreaGestion:arrListaAgrupAreaGestion: ";print_r($arrListaAgrupAreaGestion); 

 return $arrListaAgrupAreaGestion ;
}
/*-------------------------- Fin  buscarListaAgrupAreaGestion ----------------------------------*/

// *************** FIN EXCEL EXPORTACION DATOS SOCIOS *********************************************



//=================================== INCIO ESTADISTICAS ==========================================

/*---------------------------- Inicio mExportarExcelInformeAnualPres -----------
Cierre de año: exporta datos soci@s a Excel para informe anual parae Secretaría,
y Presidencia. 
Se puede ejecutar en cualquier fecha del año, pero para años 
antiguos, después de 5 años de efectuarse la baja, no se encontrará datos 
personales de esos socios, porque ya habrán se habrán eliminado de la tabla: 
"MIEMBROELIMINADO5ANIOS", según la ley de procteción de datos un socio que se da
de bajas, sus datos personales dejan de ser accesibles a nivel general durante 
5 años (se puede acceder a nombre y NIF por tesorería ), y despues de 5 años se 
eliminan del todo. 
Se puede elegir para una sola agrupación, lo normal es que se incluyan todas=%
En el archivo Excel se incluye todos los socios/as que en el año correspondiente
estuvieron de alta, aunque después en ese mismo año se diesen de baja.

RECIBE: $codAgrupacion (lo normal es que se incluyan todas=%),
$anioElegido (lo normal es incluir el último año que finalizó)
DEVUELVE: El archivo Excel de la agrupación correspondiente o si % de todas
	
LLAMADA: cPresidente.php:cExportarExcelInformeAnualPres()        
LLAMA: modeloMySQL.php:buscarCadSql()
							modelos/libs/exportarExcel.php:exportarExcel();
							usuariosConfig/BBDD/MySQL/configMySQL.php,
							modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
							modelos/modeloErrores.php:insertarError()							
								
OBSERVACIONES: modifico para utilizar PHP:PDOStatement::bindParamValues, PHP 7.3.21
								
OJO:Al utilizar "header()" en funciones que se llaman el proceso no se puede 
utilizar echo u otras salidas de pantalla antes de esta función pues daría error
 al formar el buffer de salida a Excel	

Al finalizar, se descarga el archivo Excel mediante el navegador, controla los 
posibles errores en todas las funciones, excepto en  exportarExcel() que no 
controla si se ha producido un error en la formación del archivo Excel.

Todos los socios que han estado de alta al menos un día en un año concreto, 
tendrán su correspondiente linea en la tabla "CUOTAANIOSOCIO", aunque después se 
hayan dado de baja ese mismo año en años posteriores. Pero no habrá ninguna 
línea para socios que se dieron de baja en años anteriores a ese Y.
------------------------------------------------------------------------------*/
function mExportarExcelInformeAnualPres($codAgrupacion,$anioElegido)//Nuevo nombre independiente de Coord
{
	//echo "<br><br>0-1 modeloPresCoord.php:mExportarExcelInformeAnualPres:codAgrupacion: ";print_r($codAgrupacion);
	//echo "<br><br>0-2 modeloPresCoord.php:mExportarExcelInformeAnualPres:anioElegido: ";print_r($anioElegido);
	
 $resExportarSocios['nomScript'] = 'modeloPresCoord.php';
 $resExportarSocios['nomFuncion'] = 'mExportarExcelInformeAnualPres';	
 $resExportarSocios['codError'] = '00000';
	$resExportarSocios['errorMensaje'] = '';	
	$resExportarSocios['textoCabecera'] = "Exportar los datos de los socios/as a Excel para informe anual de presidencia"; 
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarSocios = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{		
			
		$fechaSuperiorBaja = $anioElegido.'-12-31'; //Sería similar a $fechaSuperiorBaja = '2016-12-31';
		
		if (!isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
		{ $condicionAgrupacion = ' ';		
		}
		else
		{ $condicionAgrupacion = " AND SOCIO.CODAGRUPACION = :codAgrupacion ";			
				$arrBind[':codAgrupacion'] = $codAgrupacion;  
		}
		
		if (!isset($anioElegido) || empty($anioElegido) || $anioElegido == '%')
		{ $condicionAnio = ' ';		
		}
		else
		{ $condicionAnio = 	" AND CUOTAANIOSOCIO.ANIOCUOTA = :anioElegido ";			
				$arrBind[':anioElegido'] = $anioElegido;  
		}

		$cadSql = "SELECT DISTINCT CUOTAANIOSOCIO.ANIOCUOTA, SOCIO.CODSOCIO,
		UPPER(CONCAT(IFNULL(MIEMBRO.APE1,MIEMBROELIMINADO5ANIOS.APE1),' ',IFNULL(MIEMBRO.APE2,IFNULL(MIEMBROELIMINADO5ANIOS.APE2,'')),', ',IFNULL(MIEMBRO.NOM,MIEMBROELIMINADO5ANIOS.NOM) )) as Apellidos_Nombre, 

		SOCIO.FECHAALTA,SOCIO.FECHABAJA, USUARIO.ESTADO,  AGRUPACIONTERRITORIAL.NOMAGRUPACION as Agrupacion_Actual,			
		MIEMBRO.NUMDOCUMENTOMIEMBRO,MIEMBRO.TIPODOCUMENTOMIEMBRO,MIEMBRO.CODPAISDOC as Codigo_Pais_Documento, 
		MIEMBRO.DIRECCION, UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD, MIEMBRO.NOMPROVINCIA, 	MIEMBRO.CODPAISDOM as Codigo_Pais_Domicilio
		
		FROM USUARIO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD,	
		
		MIEMBRO LEFT JOIN MIEMBROELIMINADO5ANIOS ON MIEMBRO.CODUSER =MIEMBROELIMINADO5ANIOS.CODUSER 

		WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
		AND USUARIO.CODUSER = SOCIO.CODUSER 	
		AND SOCIO.CODAGRUPACION = AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION 
		AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION	
		AND SOCIO.CODSOCIO = CUOTAANIOSOCIO.CODSOCIO	".$condicionAgrupacion.$condicionAnio.
		" ORDER BY ANIOCUOTA DESC, SOCIO.CODSOCIO";	

		//echo "<br><br>2 modeloPresCoord:mExportarExcelInformeAnualPres:cadSql: ";print_r($cadSql);
		
	 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	 
		$resBuscarSocios = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//Se podría hacer una consulta más restrigida	
  
		//echo "<br><br>3 modeloPresCoord.php:mExportarExcelInformeAnualPres:resBuscarSocios: ";print_r($resBuscarSocios);
			
		if ($resBuscarSocios['codError'] !== '00000')
		{$resExportarSocios['codError'] = $resBuscarSocios['codError'];
	  $resExportarSocios['errorMensaje'] = $resBuscarSocios['errorMensaje'];
		 $resExportarSocios['textoComentarios'] = $resBuscarSocios['nomScript'].": ".$resBuscarSocios['nomFuncion'].", buscarCadSql()";			
			
			require_once './modelos/modeloErrores.php';
			insertarError($resExportarSocios);
		}
		elseif ($resBuscarSocios['numFilas'] === 0)
		{ $resExportarSocios['codError'] = '80001';
		  $resExportarSocios['numFilas'] = $resBuscarSocios['numFilas'];
			 $resExportarSocios['textoComentarios'] = ' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';
				$resExportarSocios['errorMensaje'] = $resExportarSocios['textoComentarios'];  
		}
		else	//$resExportarSocios['numFilas']>0)
		{ //echo "<br><br>4 modeloPresCoord.php:mExportarExcelInformeAnualPres:resBuscarSocios['numFilas']: ";print_r($resBuscarSocios['numFilas']);
	   
	   $nomFile = 'ExcelInformeAnualPres';
				
				require_once './modelos/libs/exportarExcel.php';				
			 $archivoExcelSocios = exportarExcel($resBuscarSocios,$nomFile);	
				
    if ($archivoExcelSocios['codError'] !== '00000')//
				{ $resExportarSocios = $archivoExcelSocios;						
						$resExportarSocios['textoComentarios'] .= ":	exportarExcelSociosPres. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
						$resExportarSocios['nomFuncion'] .=", mExportarExcelInformeAnualPres()";												
				}
				else //lo siguiente no se ve porque el buffer está cautivo al descargar el archivo Excel, habrá que arreglarlo
				{$resExportarSocios = $archivoExcelSocios; 
					$resExportarSocios['textoComentarios'] = " Se han exportado a un archivo Excel de nombre <strong>".$archivoExcelSocios['nomFile']. "</strong> los datos de </strong>". 
					                                           $archivoExcelSocios['numFilas']. " socias/os</strong>";		
					$resExportarSocios['nomFile'] = $nomFile;	
     $resExportarSocios['numFilas'] = $archivoExcelSocios['numFilas'];			
				}						
		}
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>5 modeloPresCoord.php:mExportarExcelInformeAnualPres:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}
/*---------------------------- Fin mExportarExcelInformeAnualPres ------------*/


/*---------------------------- Inicio mExportarExcelEstadisticasAltasBajasAgrupPres ----------------
Exporta a Excel informes por "agrupaciones", entre dos años concretos a elegir (inferior y superior) 
con los datos siguientes: Total de Altas a fin de año,	ALTAS en el ANIO(Total	H	%H	M	%M),
BAJASen el ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año). 

Realiza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, AGRUPACIONTERRITORIAL. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente

RECIBE: $codAgrupacion (lo normal es que se incluyan todas=%),anioInferior,$anioSuperior (pueden ser el mismo año)

LLAMADA: cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres()
        
LLAMA: modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
modelos/libs/exportarExcelEstadisticasAltasBajas:exportarExcelEstadisticasAltasBajas()
modeloErrores.php:insertarError()		
						
OBSERVACIONES: php 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

Muy similar a mExportarExcelEstadisticasAltasBajasProvPres() ymExportarExcelEstadisticasAltasBajasCCAAPres()	

OJO: Al finalizar, se descarga el archivo Excel mediante el navegador (algunos pueden dan problemas), 
controla los posibles errores en todas las funciones.
			
Al utilizar "header()" en funciones que se llaman el proceso no se puede utilizar echo 
u otras salidas de pantalla antes de esta función pues daría error al formar el buffer de salida a Excel								
--------------------------------------------------------------------------------------------------*/
function mExportarExcelEstadisticasAltasBajasAgrupPres($codAgrupacion,$anioInferior, $anioSuperior)
{
	//echo "<br><br>0-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:codAgrupacion: ";print_r($codAgrupacion);
 
	require_once "BBDD/MySQL/modeloMySQL.php";
	require_once './modelos/modeloErrores.php';

 $resExportarSocios['nomScript'] = 'modeloPresCoord.php';
 $resExportarSocios['nomFuncion'] = 'mExportarExcelEstadisticasAltasBajasAgrupPres';
 $resExportarSocios['codError'] = '00000';
	$resExportarSocios['errorMensaje'] = '';	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarSocios = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{
   //---------------- Inicio ejecución de cadenas sql SELECT -------------------
			
			if (!isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
			{ $condicionAgrupacion = ' ';
					$codAgrup = '%'; 
					$valorDefecto = '%';
			}
			else
			{ $condicionAgrupacion = 	" AND SOCIO.CODAGRUPACION = '".$codAgrupacion."' ";
					$codAgrup = $codAgrupacion; 
					$valorDefecto ='';
			}		
			
			require_once './modelos/libs/arrayParValor.php';
			$parValorCombo = parValoresAgrupaPresCoord($codAgrup,$valorDefecto);	
			
			//echo "<br><br>2-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:parValorCombo: ";print_r($parValorCombo);
			
			if ($parValorCombo['codError'] !== '00000') //$resInsertar = $parValorCombo;
			{$resExportarSocios = $parValorCombo;
			}	
			else//$parValorCombo['codError']=='00000'
			{ $arrAgrupaciones = $parValorCombo['lista'];
			  //echo "<br><br>2-2-a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupaciones: ";print_r($arrAgrupaciones);

				if ($codAgrup == '%')
				{ unset($arrAgrupaciones['00000000']);//para reordenar Estatal e Internacional y todas al final
						unset($arrAgrupaciones['%']);//para reordenar Estatal e Internacional y todas al final
						$arrAgrupaciones['00000000'] = 'Europa Laica Estatal e Internacional';
						$arrAgrupaciones['%'] = 'Todas';
				}	 				
				//echo "<br><br>2-2-b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupaciones: ";print_r($arrAgrupaciones);
				
				$camposDatosAltasAnio = array('Total' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);	
				$camposDatosSociosDeAltaTotales = array('TotalDeAlta' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);
				$altas_bajas = array('TotalSocixsDeAlta' => $camposDatosSociosDeAltaTotales, 'ALTAS' => $camposDatosAltasAnio, 'BAJAS' =>	$camposDatosAltasAnio);
			
			 //----------------- Por si ha elegido los años al revés --------
				if ($anioInferior > $anioSuperior)
				{ $aux = $anioSuperior;
						$anioSuperior = $anioInferior;
						$anioInferior = $aux;			
				} //-------------------------------------------------------------					
		
				foreach ($arrAgrupaciones as $clave => $valor)
				{  //$aux2[$clave] =	$aniosDatos;		//bien Con CODAGRUPACION,	//	$aux3[$valor] =	$aniosDatos;// BIEN Con ,NOMAGRUPACION		
						
							for ($y = $anioInferior; $y <= $anioSuperior; $y++)
							{ $aniosDatos[$y] =	$altas_bajas;			
									$arrAgrupacionesAltasBajas[$valor] = $aniosDatos;
							}	        						
				}						
				//echo "<br><br>2-3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas: ";print_r($arrAgrupacionesAltasBajas);									
		  
			 for ($y = $anioInferior; $y <= $anioSuperior; $y++)// bucle para recorrer entre los anos elegidos
 		 {						
					$fechaInferior = $y.'-01-01'; $fechaSuperior = $y.'-12-31';
					
					//--- Inicio consulta socios para hallar el total de nº socios ----
					//por AGRUPACIONES AL FINAL DE UN AÑO CONCRETO, DESCONTANDO LAS BAJAS PRODUCIDAS HASTA ESA FECHA 
					//---- para ir haciendo un array completo de socios por años ------
				
					$condicionFechaAlta = " AND SOCIO.FECHAALTA <= '$fechaSuperior' ";//Formato fechaSuperior: 'Y-12-31';
					
					//FECHABAJA='0000-00-00', Son los socios que aún NO se han dado de baja	
    	//FECHABAJA=fechaSuperior: 'Y-12-31' Son los socios que SÍ se han dado de baja	pero en una fecha posterior a la elegida (por lo que en esa fecha SÍ eran altas en esa fecha)									
					
					$condicionFechasBajas = 	" AND (SOCIO.FECHABAJA ='0000-00-00' OR SOCIO.FECHABAJA >'$fechaSuperior' )";	
					
	    /*	Si se quiere ver el listado en lugar de los totales podría usar la siguiente consulta:
					$cadSqlSociosAnioAgrupacionLista = "SELECT  SOCIO.CODAGRUPACION, AGRUPACIONTERRITORIAL.NOMAGRUPACION, COUNT(*)	TotalSocixsDeAlta			
					FROM SOCIO, AGRUPACIONTERRITORIAL 
					WHERE SOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION ".$condicionAgrupacion.$condicionFechaAlta.$condicionFechasBajas.					
					" GROUP BY CODAGRUPACION ORDER BY AGRUPACIONTERRITORIAL.NOMAGRUPACION ";	//Suman=1253	a 2016-12-31			
     */
				 $cadSqlSociosAnioAgrupacion = "SELECT  SOCIO.CODAGRUPACION, AGRUPACIONTERRITORIAL.NOMAGRUPACION, COUNT(*)	TotalDeAlta,
	    COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
		   COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 	
					FROM SOCIO, MIEMBRO, AGRUPACIONTERRITORIAL 
					WHERE MIEMBRO.CODUSER=SOCIO.CODUSER AND SOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION ".$condicionAgrupacion.$condicionFechaAlta.$condicionFechasBajas.					
					" GROUP BY CODAGRUPACION ORDER BY AGRUPACIONTERRITORIAL.NOMAGRUPACION ";	//Suman=1253	a 2016-12-31												
				
			  //echo "<br><br>3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:cadSqlSociosAnioAgrupacion: ";print_r($cadSqlSociosAnioAgrupacion);	
					
     $resBuscarSociosAnioAgrupacion = buscarCadSql($cadSqlSociosAnioAgrupacion,$conexionDB['conexionLink']);
     //echo "<br><br>3_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:resBuscarSociosAnioAgrupacion: $y: ";print_r($resBuscarSociosAnioAgrupacion);						
					
					//--- Fin consulta socios dentro del bucle anterior para hallar el total de nº socios por AGRUPACIONES 
			
					//--Inicio consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					$condicionFechasAltas = 	" AND FECHAALTA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';				

					$cadSqlAltas = "SELECT  SOCIO.CODAGRUPACION,NOMAGRUPACION, COUNT(*) as 'TOTAL_ALTAS', 
					COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
					COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 
					FROM USUARIO, SOCIO,  MIEMBRO, AGRUPACIONTERRITORIAL
					WHERE SOCIO.CODUSER=USUARIO.CODUSER AND MIEMBRO.CODUSER=SOCIO.CODUSER
					AND SOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION	".$condicionAgrupacion.$condicionFechasAltas."		
					GROUP BY CODAGRUPACION 	ORDER BY NOMAGRUPACION"; //USUARIO sobra
								
					//echo "<br><br>3_3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:cadSqlAltas: ";print_r($cadSqlAltas);		
					
     $resBuscarSociosAltas = buscarCadSql($cadSqlAltas,$conexionDB['conexionLink']);
     //echo "<br><br>3_4 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:resBuscarSociosAltas: $y: ";print_r($resBuscarSociosAltas);	
					
					//--Fin consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					
					//-- Inicio consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----
					$condicionFechasBajas = 	" AND FECHABAJA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';			

					$cadSqlBajas = "SELECT  SOCIO.CODAGRUPACION,NOMAGRUPACION, COUNT(*) as 'TOTAL_BAJAS', 
					COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
					COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 
					FROM USUARIO, SOCIO,  MIEMBRO, AGRUPACIONTERRITORIAL
					WHERE SOCIO.CODUSER=USUARIO.CODUSER AND MIEMBRO.CODUSER=SOCIO.CODUSER
					AND SOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION	".$condicionAgrupacion.$condicionFechasBajas."		
					GROUP BY CODAGRUPACION 	ORDER BY NOMAGRUPACION"; //USUARIO sobra
					
					//echo "<br><br>3_5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:cadSqlBajas: ";print_r($cadSqlBajas);	
					
     $resBuscarSociosBajas = buscarCadSql($cadSqlBajas,$conexionDB['conexionLink']);
     //echo "<br><br>3_6 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:resBuscarSociosBajas: $y: ";print_r($resBuscarSociosBajas);					
					
					//-- Fin consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----			
					
					/*---------------- Fin ejecución de cadenas sql SELECT ------------------*/
					
					if ($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioAgrupacion ['codError'] !=='00000' )
					{$resExportarSocios['codError'] = '70100';
				  $resExportarSocios['errorMensaje'] = 'Error en función modeloPresCoord.php:mExportarExcelEstadisticasAltasBajas:buscarCadSql()';
						$resExportarSocios['textoComentarios'] = " Error del sistema, en alguna de las 3 funciones buscarCadSql ";	

						insertarError($resExportarSocios);	
					}
					else //!!($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioAgrupacion ['codError'] !=='00000' )
					{
						/*---------- Inicio sumas en acumuladores ------------------------------*/
						
						//*************Inicio formar el array de Todos los socios que estan de alta en esa fecha  **********						
					 $acumuladorTotalSociosAnioAgrupaciones  = 0;			//[TotalSocixsDeAlta] => 3 [Hombres] => 1 [%Hombres] => 33.3333 [Mujeres] => 2 [%Mujeres] => 66.6667 )
						$acumuladorTotalSociosAnioAgrupacionesH = 0;
						$acumuladorTotalSociosAnioAgrupacionesM = 0;	
  
					 foreach ($resBuscarSociosAnioAgrupacion['resultadoFilas'] as  $indice => $contenido)
			   {// echo "<br><br>4_1_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:contenido[NOMAGRUPACION] : $y: ";print_r($contenido['NOMAGRUPACION'] );							
			    
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $contenido['TotalDeAlta'];								
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['TotalSocixsDeAlta']['H']  = $contenido['Hombres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['TotalSocixsDeAlta']['%H'] = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['TotalSocixsDeAlta']['M']  = $contenido['Mujeres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['TotalSocixsDeAlta']['%M'] = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalSociosAnioAgrupaciones  += $contenido['TotalDeAlta'];
								$acumuladorTotalSociosAnioAgrupacionesH += $contenido['Hombres'];
								$acumuladorTotalSociosAnioAgrupacionesM += $contenido['Mujeres'];
								
		    }// foreach ($resBuscarSociosAnioAgrupacion['resultadoFilas'] as  $indice => $contenido)

						if ($acumuladorTotalSociosAnioAgrupaciones === 0)//para evitar warning división por cero
						{ $porcentajeH =  number_format(0, 1, ',', '');
								$porcentajeM =  number_format(0, 1, ',', '');								
						}
						else
						{ $porcentajeH =   number_format(round(100*$acumuladorTotalSociosAnioAgrupacionesH/$acumuladorTotalSociosAnioAgrupaciones, 1), 1, ',', '');
						 	$porcentajeM =   number_format(round(100*$acumuladorTotalSociosAnioAgrupacionesM/$acumuladorTotalSociosAnioAgrupaciones, 1), 1, ',', '');
						}

						$arrAgrupacionesAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $acumuladorTotalSociosAnioAgrupaciones;							
						$arrAgrupacionesAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['H']     = $acumuladorTotalSociosAnioAgrupacionesH;	
						$arrAgrupacionesAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%H']    = $porcentajeH;									
						$arrAgrupacionesAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['M']     = $acumuladorTotalSociosAnioAgrupacionesM;			
						$arrAgrupacionesAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%M']    = $porcentajeM;	 
						
			 	 //echo "<br><br>4_1_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas: $y: ";print_r($arrAgrupacionesAltasBajas);
						//************* Fin formar el array de Todos los socios que estan de alta en esa fecha  **********	
						
      //************* Inicio formar el array parte de Altas ************************		
						$acumuladorTotalAltasAnio  = 0;
      $acumuladorTotalAltasAnioH = 0;
						$acumuladorTotalAltasAnioM = 0;
						
					 foreach ($resBuscarSociosAltas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_A_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:contenido[NOMAGRUPACION] : $y: ";print_r($contenido['NOMAGRUPACION'] );	// [TOTAL_ALTAS] => 1 [Hombres] => 1 [%Hombres] => 100.0000 [Mujeres] => 0 [%Mujeres] => 0.0000 
							
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['ALTAS']['Total']= $contenido['TOTAL_ALTAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['ALTAS']['H']    = $contenido['Hombres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['ALTAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['ALTAS']['M']    = $contenido['Mujeres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['ALTAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');

								$acumuladorTotalAltasAnio  += $contenido['TOTAL_ALTAS'];
								$acumuladorTotalAltasAnioH += $contenido['Hombres'];
								$acumuladorTotalAltasAnioM += $contenido['Mujeres'];
								
								//$arrAgrupaciones[$y] = $contenido;			//$arrAgrupaciones[$y][$contenido['CODAGRUPACION']] = $contenido;	
	     }//foreach ($resBuscarSociosAltas
							
						if ($acumuladorTotalAltasAnio === 0)//para evitar warning división por cero
						{ $porcentajeAltasH =  number_format(0, 1, ',', '');
								$porcentajeAltasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeAltasH =  number_format(round(100*$acumuladorTotalAltasAnioH/$acumuladorTotalAltasAnio, 1), 1, ',', '');
								$porcentajeAltasM =  number_format(round(100*$acumuladorTotalAltasAnioM/$acumuladorTotalAltasAnio, 1), 1, ',', '');
						}		
						
						$arrAgrupacionesAltasBajas['Todas'][$y]['ALTAS']['Total']= $acumuladorTotalAltasAnio;
						$arrAgrupacionesAltasBajas['Todas'][$y]['ALTAS']['H']    = $acumuladorTotalAltasAnioH;			
						$arrAgrupacionesAltasBajas['Todas'][$y]['ALTAS']['%H']   = $porcentajeAltasH;	
						$arrAgrupacionesAltasBajas['Todas'][$y]['ALTAS']['M']    = $acumuladorTotalAltasAnioM;			
						$arrAgrupacionesAltasBajas['Todas'][$y]['ALTAS']['%M']   = $porcentajeAltasM;	
						//echo "<br><br>4_2_A_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas: $y: ";print_r($arrAgrupacionesAltasBajas);
						
      //************* Fin formar el array parte de Altas ************************									
						
						//************* Inicio formar el array parte de Bajas ********************						
					 $acumuladorTotalBajasAnio  = 0;
						$acumuladorTotalBajasAnioH = 0;
						$acumuladorTotalBajasAnioM = 0;					
  
					 foreach ($resBuscarSociosBajas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_B_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:contenido[NOMAGRUPACION] : $y: ";print_r($contenido['NOMAGRUPACION'] );							
			     //$resultadoFilasAux[$f][$indice] = utf8_encode($fila[$indice]);//Para PHP
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['BAJAS']['Total']= $contenido['TOTAL_BAJAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['BAJAS']['H']    = $contenido['Hombres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['BAJAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['BAJAS']['M']    = $contenido['Mujeres'];
								$arrAgrupacionesAltasBajas[$contenido['NOMAGRUPACION']][$y]['BAJAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalBajasAnio  += $contenido['TOTAL_BAJAS'];
								$acumuladorTotalBajasAnioH += $contenido['Hombres'];
								$acumuladorTotalBajasAnioM += $contenido['Mujeres'];
		    }//foreach ($resBuscarSociosBajas
										
						if ($acumuladorTotalBajasAnio === 0)//para evitar warning división por cero
						{ $porcentajeBajasH =  number_format(0, 1, ',', '');
								$porcentajeBajasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeBajasH = number_format(round(100*$acumuladorTotalBajasAnioH/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						  $porcentajeBajasM = number_format(round(100*$acumuladorTotalBajasAnioM/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						}	
						
						$arrAgrupacionesAltasBajas['Todas'][$y]['BAJAS']['Total']= $acumuladorTotalBajasAnio;
						$arrAgrupacionesAltasBajas['Todas'][$y]['BAJAS']['H']    = $acumuladorTotalBajasAnioH;	
						$arrAgrupacionesAltasBajas['Todas'][$y]['BAJAS']['%H']   = $porcentajeBajasH;									
						$arrAgrupacionesAltasBajas['Todas'][$y]['BAJAS']['M']    = $acumuladorTotalBajasAnioM;			
						$arrAgrupacionesAltasBajas['Todas'][$y]['BAJAS']['%M']   = $porcentajeBajasM;								
			   
						//echo "<br><br>4_2_B_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas: $y: ";print_r($arrAgrupacionesAltasBajas);
						
					 //************* Fin formar el array parte de Bajas ***********************	
						
	     /*------------- Fin sumas en acumuladores ------------------------------*/
			 	}//else !!($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioAgrupacion ['codError'] !=='00000' )
     		
				}// for( $y....)
	   
			 //echo "<br><br>4_3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:resExportarSocios:  ";print_r($resExportarSocios);	
			
				if ($resExportarSocios['codError'] == '00000')
				{//echo "<br><br>4_3_2a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas:  ";print_r($arrAgrupacionesAltasBajas);			
					if ($codAgrup !== '%') //para eliminar el acumulador para "Todas" en el caso de elegir una sola Agrupación
					{ unset($arrAgrupacionesAltasBajas['Todas']);
					}	 
					//echo "<br><br>4_3_2b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:arrAgrupacionesAltasBajas:  ";print_r($arrAgrupacionesAltasBajas);
     
					/*------------- Inicio función archivo Excel ----------------------------*/
					$nomFile = 'ExcelAltasBajas_Agrupaciones';
					$cabeceraColum1 = 'AGRUPACIONES';
					
					require_once './modelos/libs/exportarExcelEstadisticasAltasBajas.php';				
					$archivoExcelSocios = exportarExcelEstadisticasAltasBajas($arrAgrupacionesAltasBajas,$cabeceraColum1,$nomFile,$anioInferior,$anioSuperior);	
	
					if ($archivoExcelSocios['codError'] !== '00000')
					{ $resExportarSocios = $archivoExcelSocios;						
							$resExportarSocios['textoComentarios'] .= ":	exportarExcelEstadisticasAltasBajas. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
							$resExportarSocios['nomFuncion'] .= ", exportarExcelEstadisticasAltasBajas()";		
							insertarError($resExportarSocios);						
					}
					else //lo siguiente no se ve porque el buffer está cautivo al descargar el archivo Excel, habrá que arreglarlo
					{$resExportarSocios = $archivoExcelSocios; 
						$resExportarSocios['textoComentarios'] = " Se han exportado a un archivo Excel de nombre <strong>".$archivoExcelSocios['nomFile']. "</strong> los datos de </strong>". 
																																																	$archivoExcelSocios['numFilas']. " socias/os</strong>";		
						$resExportarSocios['nomFile'] = $nomFile;	
						$resExportarSocios['numFilas'] = $archivoExcelSocios['numFilas'];			
					}
     /*------------- Fin función archivo Excel -------------------------------*/					
				}//if ($resExportarSocios['codError'] == '00000')
	  }//else $parValorCombo['codError']=='00000'
			
 }//else //if ($conexion['codError']=='00000')
 
	//echo '<br><br>5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasAgrupPres:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}
/*-------- Fin mExportarExcelEstadisticasAltasBajasAgrupPres -------------------------------------*/

/*---------------------------- Inicio mExportarExcelEstadisticasAltasBajasProvPres -----------------
Exporta a Excel informes por "provincias", entre dos años concretos a elegir (inferior y superior) 
con los datos siguientes: Total de Altas a fin de año,	ALTAS en el ANIO(Total	H	%H	M	%M),
BAJAS en el ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Realiza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, PROVINCIA. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente.
														  	
RECIBE: $codProvincia (lo normal es que se incluyan todas=%),anioInferior, $anioSuperior (pueden ser el mismo año)

LLAMADA: cPresidente.php:cExportarExcelEstadisticasAltasBajasProv()
        
LLAMA: modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
./modelos/libs/modelos/libs/exportarExcelEstadisticasAltasBajas.php:exportarExcelEstadisticasAltasBajas()


OBSERVACIONES: php 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

Muy similar a mExportarExcelEstadisticasAltasBajasAgrupPres()	y mExportarExcelEstadisticasAltasBajasCCAAPres()	

OJO: Al finalizar, se descarga el archivo Excel mediante el navegador (algunos pueden dan problemas), 
controla los posibles errores en todas las funciones.
			
Al utilizar "header()" en funciones que se llaman el proceso no se puede utilizar echo 
u otras salidas de pantalla antes de esta función pues daría error al formar el buffer de salida a Excel							
--------------------------------------------------------------------------------------------------*/
function mExportarExcelEstadisticasAltasBajasProvPres($codProvincia,$anioInferior, $anioSuperior)
{
	//echo "<br><br>1-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:codProvincia: ";print_r($codProvincia);
 //echo "<br><br>1-2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:anioInferior: ";print_r($anioInferior);
 //echo "<br><br>1-3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:anioSuperior: ";print_r($anioSuperior);

 $resExportarSocios['codError'] = '00000';
	$resExportarSocios['errorMensaje'] = '';	
	$resExportarSocios['textoCabecera'] = "Exportar informe estadístico por provincias a Excel";  	 

	if (!isset($codProvincia) || empty($codProvincia) || $codProvincia =='%')
 { $condicionProvincia = ' ';
   $codProv = '%'; 
		 $valorDefecto ='%';
 }
	else
	{ $condicionProvincia = 	" AND MIEMBRO.CODPROV = '".$codProvincia."' ";
   $codProv = $codProvincia; 
		 $valorDefecto ='';
	}		
 //echo "<br><br>1-4 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:codProv: ";print_r($codProv);
	
	if ($anioInferior > $anioSuperior)
 { $aux = $anioSuperior;
   $anioSuperior = $anioInferior;
			$anioInferior = $aux;			
 }
	
 //---------------- Inicio ejecución de cadenas sql SELECT --------------------------
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionUsuariosDB['codError']!=='00000')	
	{$resExportarSocios = $conexionUsuariosDB;
	}     
	else //if ($conexionUsuariosDB['codError']=='00000')
	{	
   require_once './modelos/libs/arrayParValor.php';			
			
			$valorDefecto ='%';
			$parValorCombo = 	parValoresProvinciaPresCoord($codProv,$valorDefecto);
			//echo "<br><br>2-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:parValorCombo: ";print_r($parValorCombo);
			
			if ($parValorCombo['codError']!=='00000') //$resInsertar = $parValorCombo;
			{$resExportarSocios = $parValorCombo;
			}	
			else//$parValorCombo['codError']=='00000'
			{ 
				$arrProvincias = $parValorCombo['lista'];
				//echo "<br><br>2-2a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvincias: ";print_r($arrProvincias);
				
				if ($codProv == '%')
				{ unset($arrProvincias['%']);//para reordenar extranjero y todas 
			   $arrProvincias['Extranjero'] = 'Extranjero';
						$arrProvincias['%'] = 'Todas';
			 }	 
				//echo "<br><br>2-2b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvincias: ";print_r($arrProvincias);	
				
				$camposDatosAltasAnio = array('Total' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);	
				$camposDatosSociosDeAltaTotales = array('TotalDeAlta' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);
				$altas_bajas = array('TotalSocixsDeAlta' => $camposDatosSociosDeAltaTotales, 'ALTAS' => $camposDatosAltasAnio, 'BAJAS' =>	$camposDatosAltasAnio);

				foreach ($arrProvincias as $clave => $valor)
				{ //$aux2[$clave] =	$aniosDatos;		//bien Con CODAGRUPACION,	//	$aux3[$valor] =	$aniosDatos;// BIEN Con ,NOMAGRUPACION		
						//echo "<br><br>2-3_0a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:clave: ";print_r($clave);	
						//echo "<br><br>2-3_0b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:valor: ";print_r($valor);	
						
							for ($y = $anioInferior; $y <= $anioSuperior; $y++)
							{ $aniosDatos[$y] =	$altas_bajas;			
									$arrProvinciasAltasBajas[$valor] = $aniosDatos;
							}	        						
				}						
				//echo "<br><br>2-3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvinciasAltasBajas: ";print_r($arrProvinciasAltasBajas);									
		  
			 for ($y = $anioInferior; $y <= $anioSuperior; $y++)
 		 {						
					$fechaInferior = $y.'-01-01'; $fechaSuperior = $y.'-12-31';
					
					//------------------ Inicio consulta socios para hallar el total de nº socios -----------------
					//por PROVINCIAS AL FINAL DE UN AÑO CONCRETO, DESCONTANDO LAS BAJAS PRODUCIDAS HASTA ESA FECHA 
					//---- para ir haciendo un array completo de socios por años ----------------------------------
					
				 //SOCIO.FECHAALTA <= '$fechaSuperior' socios que ya estaban dados de alta antes de una fecha $fechaSuperior' ";//Formato fechaSuperior: 'Y-12-31';
					$condicionFechaAlta = " AND SOCIO.FECHAALTA <= '$fechaSuperior' ";//Formato fechaSuperior: 'Y-12-31';
					
					//FECHABAJA='0000-00-00', Son los socios que aún NO se han dado de baja	
    	//FECHABAJA=fechaSuperior: 'Y-12-31' Son los socios que SÍ se han dado de baja	pero en una fecha posterior a la elegida (por lo que en esa fecha SÍ eran altas en esa fecha)
					$condicionFechasBajas = 	" AND (SOCIO.FECHABAJA ='0000-00-00' OR SOCIO.FECHABAJA >'$fechaSuperior' )";	
					
		 		$cadSqlSociosAnioProvincia = 
			 	"SELECT  IFNULL(PROVINCIA.NOMPROVINCIA,'Extranjero') AS nombreProvincia, MIEMBRO.CODPROV, COUNT(*) TotalDeAlta,
														COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
														COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

														FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV																		
																										
														WHERE USUARIO.CODUSER=MIEMBRO.CODUSER AND USUARIO.CODUSER=SOCIO.CODUSER 

															/*	AND MIEMBRO.CODPAISDOM='ES' para descontar los que viven fuera de España */"
															.$condicionProvincia.$condicionFechaAlta.$condicionFechasBajas.
															"	GROUP BY nombreProvincia ORDER BY nombreProvincia;	";		//USUARIO sobra
														
			  //echo "<br><br>3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:cadSqlSociosAnioProvincia: ";print_r($cadSqlSociosAnioProvincia);	
					
					$resBuscarSociosAnioProvincia = buscarCadSql($cadSqlSociosAnioProvincia,$conexionUsuariosDB['conexionLink']);
					
     //echo "<br><br>3_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:resBuscarSociosAnioProvincia: $y: ";print_r($resBuscarSociosAnioProvincia);						
					
					//--- Fin consulta socios dentro del bucle anterior para hallar el total de nº socios por Provincias 
			
					//--Inicio consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					$condicionFechasAltas = 	" AND FECHAALTA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';				

					$cadSqlAltas = 
					"SELECT  IFNULL(PROVINCIA.NOMPROVINCIA,'Extranjero') AS nombreProvincia, MIEMBRO.CODPROV, COUNT(*) as 'TOTAL_ALTAS', 
														COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
														COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

														FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV																		
																										
														WHERE USUARIO.CODUSER=MIEMBRO.CODUSER AND USUARIO.CODUSER=SOCIO.CODUSER 

               /*	AND MIEMBRO.CODPAISDOM='ES' para descontar los que viven fuera de España */"
															.$condicionProvincia.$condicionFechasAltas.
															"	GROUP BY nombreProvincia ORDER BY nombreProvincia;	";	//USUARIO sobra		
								
					//echo "<br><br>3_3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasPres:cadSqlAltas: ";print_r($cadSqlAltas);		
					
     $resBuscarSociosAltas = buscarCadSql($cadSqlAltas,$conexionUsuariosDB['conexionLink']);
     
					//echo "<br><br>3_4 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:resBuscarSociosAltas: $y: ";print_r($resBuscarSociosAltas);	
					
					//--Fin consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					
					//-- Inicio consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----
					$condicionFechasBajas = 	" AND FECHABAJA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';			

					$cadSqlBajas = 
					"SELECT  IFNULL(PROVINCIA.NOMPROVINCIA,'Extranjero') AS nombreProvincia, MIEMBRO.CODPROV, COUNT(*) as 'TOTAL_BAJAS',
														COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
														COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

														FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV																		
																										
														WHERE USUARIO.CODUSER=MIEMBRO.CODUSER AND USUARIO.CODUSER=SOCIO.CODUSER 

															/*AND (SOCIO.FECHABAJA IS NULL OR SOCIO.FECHABAJA ='0000-00-00')	AND MIEMBRO.CODPAISDOM='ES'*/".
															$condicionProvincia.$condicionFechasBajas.
															"	GROUP BY nombreProvincia ORDER BY nombreProvincia;	";	//USUARIO sobra
															
					//echo "<br><br>3_5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:cadSqlBajas: ";print_r($cadSqlBajas);	
					
     $resBuscarSociosBajas = buscarCadSql($cadSqlBajas,$conexionUsuariosDB['conexionLink']);
					
     //echo "<br><br>3_6 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:resBuscarSociosBajas: $y: ";print_r($resBuscarSociosBajas);					
					
					//-- Fin consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----					
					
					//if ($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioAgrupacion ['codError'] !=='00000' )
     if ($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioProvincia['codError'] !=='00000' )						
					{//$resExportarSocios = $resBuscarSociosBajas.$resBuscarSociosAltas.$resBuscarSociosAnioAgrupacion ;
				  $resExportarSocios = $resBuscarSociosBajas.$resBuscarSociosAltas.$resBuscarSociosAnioProvincia;
						$resExportarSocios['textoComentarios'] .= date('Y-m-d:H:i:s')." modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres. Error del sistema, vuelva a intentarlo pasado un tiempo ";		
						$resExportarSocios['nomFuncion'] .=", modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres()";
						
						require_once './modelos/modeloErrores.php';
						insertarError($resExportarSocios);	
					}
					else //$resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioProvincia['codError'] !=='00000' )	
					{
						//*************Inicio formar el array de Todos los socios que están de alta en esa fecha  **********						
						
					 $acumuladorTotalSociosAnioProvincias  = 0;			//[TotalSocixsDeAlta] => 3 [Hombres] => 1 [%Hombres] => 33.3333 [Mujeres] => 2 [%Mujeres] => 66.6667 )
						$acumuladorTotalSociosAnioProvinciasH = 0;
						$acumuladorTotalSociosAnioProvinciasM = 0;							

						foreach ($resBuscarSociosAnioProvincia['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_1_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:contenido[nombreProvincia] : $y: ";print_r($contenido['nombreProvincia'] );							
			    
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $contenido['TotalDeAlta'];								
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['TotalSocixsDeAlta']['H']  = $contenido['Hombres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['TotalSocixsDeAlta']['%H'] = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['TotalSocixsDeAlta']['M']  = $contenido['Mujeres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['TotalSocixsDeAlta']['%M'] = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalSociosAnioProvincias  += $contenido['TotalDeAlta'];
								$acumuladorTotalSociosAnioProvinciasH += $contenido['Hombres'];
								$acumuladorTotalSociosAnioProvinciasM += $contenido['Mujeres'];
								
		    }// foreach ($resBuscarSociosAnioProvincia['resultadoFilas'] as  $indice => $contenido)
						
						if ($acumuladorTotalSociosAnioProvincias === 0)//para evitar warning división por cero
						{ $porcentajeH =  number_format(0, 1, ',', '');
								$porcentajeM =  number_format(0, 1, ',', '');								
						}
						else
						{ $porcentajeH =   number_format(round(100*$acumuladorTotalSociosAnioProvinciasH/$acumuladorTotalSociosAnioProvincias, 1), 1, ',', '');
						  $porcentajeM =   number_format(round(100*$acumuladorTotalSociosAnioProvinciasM/$acumuladorTotalSociosAnioProvincias, 1), 1, ',', '');										
					 }

						$arrProvinciasAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $acumuladorTotalSociosAnioProvincias;							
						$arrProvinciasAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['H']     = $acumuladorTotalSociosAnioProvinciasH;	
						$arrProvinciasAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%H']    = $porcentajeH;									
						$arrProvinciasAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['M']     = $acumuladorTotalSociosAnioProvinciasM;			
						$arrProvinciasAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%M']    = $porcentajeM;	 						
						
			 	 //echo "<br><br>4_1_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvinciasAltasBajas: $y: ";print_r($arrProvinciasAltasBajas);
						//************* Fin formar el array de Todos los socios que estan de alta en esa fecha  **********	
						
      //************* Inicio formar el array parte de Altas ************************		
						$acumuladorTotalAltasAnio  = 0;
      $acumuladorTotalAltasAnioH = 0;
						$acumuladorTotalAltasAnioM = 0;

					 foreach ($resBuscarSociosAltas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_A_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:contenido[nombreProvincia] : $y: ";print_r($contenido['nombreProvincia'] );	// [TOTAL_ALTAS] => 1 [Hombres] => 1 [%Hombres] => 100.0000 [Mujeres] => 0 [%Mujeres] => 0.0000 
							
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['ALTAS']['Total']= $contenido['TOTAL_ALTAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['ALTAS']['H']    = $contenido['Hombres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['ALTAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['ALTAS']['M']    = $contenido['Mujeres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['ALTAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');

								$acumuladorTotalAltasAnio  += $contenido['TOTAL_ALTAS'];
								$acumuladorTotalAltasAnioH += $contenido['Hombres'];
								$acumuladorTotalAltasAnioM += $contenido['Mujeres'];
							
	     }//foreach ($resBuscarSociosAltas						
						
						if ($acumuladorTotalAltasAnio === 0)//para evitar warning división por cero
						{ $porcentajeAltasH =  number_format(0, 1, ',', '');
								$porcentajeAltasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeAltasH =  number_format(round(100*$acumuladorTotalAltasAnioH/$acumuladorTotalAltasAnio, 1), 1, ',', '');
								$porcentajeAltasM =  number_format(round(100*$acumuladorTotalAltasAnioM/$acumuladorTotalAltasAnio, 1), 1, ',', '');
						}								

						$arrProvinciasAltasBajas['Todas'][$y]['ALTAS']['Total']= $acumuladorTotalAltasAnio;
						$arrProvinciasAltasBajas['Todas'][$y]['ALTAS']['H']    = $acumuladorTotalAltasAnioH;			
						$arrProvinciasAltasBajas['Todas'][$y]['ALTAS']['%H']   = $porcentajeAltasH;	
						$arrProvinciasAltasBajas['Todas'][$y]['ALTAS']['M']    = $acumuladorTotalAltasAnioM;			
						$arrProvinciasAltasBajas['Todas'][$y]['ALTAS']['%M']   = $porcentajeAltasM;							
						//echo "<br><br>4_2_A_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvinciasAltasBajas: $y: ";print_r($arrProvinciasAltasBajas);
						
      //************* Fin formar el array parte de Altas ************************									
						
						//************* Inicio formar el array parte de Bajas ************************						
					 $acumuladorTotalBajasAnio  = 0;
						$acumuladorTotalBajasAnioH = 0;
						$acumuladorTotalBajasAnioM = 0;					

					 foreach ($resBuscarSociosBajas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_B_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:contenido[nombreProvincia] : $y: ";print_r($contenido['nombreProvincia'] );							
			     //$resultadoFilasAux[$f][$indice] = utf8_encode($fila[$indice]);//Para PHP
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['BAJAS']['Total']= $contenido['TOTAL_BAJAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['BAJAS']['H']    = $contenido['Hombres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['BAJAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['BAJAS']['M']    = $contenido['Mujeres'];
								$arrProvinciasAltasBajas[$contenido['nombreProvincia']][$y]['BAJAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalBajasAnio  += $contenido['TOTAL_BAJAS'];
								$acumuladorTotalBajasAnioH += $contenido['Hombres'];
								$acumuladorTotalBajasAnioM += $contenido['Mujeres'];
		    }//foreach ($resBuscarSociosBajas			
						
						if ($acumuladorTotalBajasAnio === 0)//para evitar warning división por cero
						{ $porcentajeBajasH =  number_format(0, 1, ',', '');
								$porcentajeBajasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeBajasH = number_format(round(100*$acumuladorTotalBajasAnioH/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						  $porcentajeBajasM = number_format(round(100*$acumuladorTotalBajasAnioM/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						}					

						$arrProvinciasAltasBajas['Todas'][$y]['BAJAS']['Total']= $acumuladorTotalBajasAnio;
						$arrProvinciasAltasBajas['Todas'][$y]['BAJAS']['H']    = $acumuladorTotalBajasAnioH;	
						$arrProvinciasAltasBajas['Todas'][$y]['BAJAS']['%H']   = $porcentajeBajasH;									
						$arrProvinciasAltasBajas['Todas'][$y]['BAJAS']['M']    = $acumuladorTotalBajasAnioM;			
						$arrProvinciasAltasBajas['Todas'][$y]['BAJAS']['%M']   = $porcentajeBajasM;							
			   //echo "<br><br>4_2_B_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvinciasAltasBajas: $y: ";print_r($arrProvinciasAltasBajas);
						
					 //************* Fin formar el array parte de Bajas ************************						
	
					}	//else	!!$resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioProvincia['codError'] !=='00000' )	
				
		 	}// for ($y = $anioInferior; $y <= $anioSuperior; $y++)				

					if ($codProv !== '%') //para eliminar el acumulado para "Todas" en el caso de elegir una sola provincia
					{ unset($arrProvinciasAltasBajas['Todas']);
					}	 
					//echo "<br><br>4_3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:arrProvinciasAltasBajas:  ";print_r($arrProvinciasAltasBajas);
					$nomFile = 'ExportarExcelAltasBajas_Provincias';
					$cabeceraColum1 = 'PROVINCIAS';

					require_once './modelos/libs/exportarExcelEstadisticasAltasBajas.php';				
					$archivoExcelSocios = exportarExcelEstadisticasAltasBajas($arrProvinciasAltasBajas,$cabeceraColum1,$nomFile,$anioInferior,$anioSuperior);				
					
					if ($archivoExcelSocios['codError'] !=='00000')//
					{ $resExportarSocios = $archivoExcelSocios;
							$resExportarSocios['textoComentarios'] .= date('Y-m-d:H:i:s').":	exportarExcelEstadisticasAltasBajas. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
							$resExportarSocios['nomFuncion'] .=", exportarExcelEstadisticasAltasBajas()";					
					}
		
	  }//else $parValorCombo['codError']=='00000'
 }//else //if ($conexionUsuariosDB['codError']=='00000')
 //echo '<br><br>5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasProvPres:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}
//---------------------------- Fin mExportarExcelEstadisticasAltasBajasProvPres --------------------


/*---------------------------- Inicio mExportarExcelEstadisticasAltasBajasCCAAPres -----------------
Exporta a Excel informes por comunidades autónomas CCAA, entre dos años concretos a elegir (inferior y superior) 
con los datos siguientes: Total de Altas a fin de año,	ALTAS en el ANIO(Total	H	%H	M	%M),
BAJAS en el ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Reliza tres consultas sobre las tablas USUARIO, SOCIO, MIEMBRO, PROVINCIA, CCAA. 
Después en los acumuladores se obtienen los totales, y se llama a la función "exportarExcelEstadisticasAltasBajas()" 
para generar el archivo excel Correspondiente.

RECIBE: $codCCAA (lo normal es que se incluyan todas=%),anioInferior, $anioSuperior (pueden ser el mismo año)

LLAMADA: cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAA()
        
LLAMA: modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
./modelos/libs/modelos/libs/exportarExcelEstadisticasAltasBajas.php:exportarExcelEstadisticasAltasBajas()

OBSERVACIONES: php 7.3.21
No cambio para PDO arrayBindValues, poco riesgo de injection en rol presidencia y laborioso hacerlo

Muy similar a mExportarExcelEstadisticasAltasBajasAgrupPres()	y mExportarExcelEstadisticasAltasBajasProvPres()
							
OJO: Al finalizar, se descarga el archivo Excel mediante el navegador (algunos pueden dan problemas), 
controla los posibles errores en todas las funciones.
			
Al utilizar "header()" en funciones que se llaman el proceso no se puede utilizar echo 
u otras salidas de pantalla antes de esta función pues daría error al formar el buffer de salida a Excel									
--------------------------------------------------------------------------------------------------*/
function mExportarExcelEstadisticasAltasBajasCCAAPres($codCCAA,$anioInferior, $anioSuperior)
{
	//echo "<br><br>1-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:codCCAA: ";print_r($codCCAA);
 //echo "<br><br>1-2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:anioInferior: ";print_r($anioInferior);
 //echo "<br><br>1-3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:anioSuperior: ";print_r($anioSuperior);

 $resExportarSocios['codError'] = '00000';
	$resExportarSocios['errorMensaje'] = '';	
	$resExportarSocios['textoCabecera'] = "Exportar informe estadístico por CCAA (comunidades autónomas) a Excel"; 

	if (!isset($codCCAA) || empty($codCCAA) || $codCCAA =='%')
 { $condicionCCAA = ' ';
   $codCCAA = '%'; 
		 $valorDefecto ='%';
 }
	else
	{ $condicionCCAA = 	" AND PROVINCIA.CCAA = '".$codCCAA."' ";
   //$codCCAA = $codCCAA; 
		 $valorDefecto ='';
	}		
 //echo "<br><br>1-4 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:codCCAA: ";print_r($codCCAA);
	
	if ($anioInferior > $anioSuperior)
 { $aux = $anioSuperior;
   $anioSuperior = $anioInferior;
			$anioInferior = $aux;			
 }
	
 //---------------- Inicio ejecución de cadenas sql SELECT --------------------------
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionUsuariosDB['codError']!=='00000')	
	{$resExportarSocios = $conexionUsuariosDB;
	}     
	else //if ($conexionUsuariosDB['codError']=='00000')
	{   
			require_once './modelos/libs/arrayParValor.php';		
			
			$valorDefecto ='%';

			$parValorCombo = parValoresCCAAPresCoord($codCCAA,$valorDefecto);
			//echo "<br><br>2-1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:parValorCombo: ";print_r($parValorCombo);
			
			if ($parValorCombo['codError']!=='00000') //$resInsertar = $parValorCombo;
			{$resExportarSocios = $parValorCombo;
			}	
			else//$parValorCombo['codError']=='00000'
			{ 
				$arrCCAAs = $parValorCombo['lista'];
				//echo "<br><br>2-2a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAs: ";print_r($arrCCAAs);
				
				/*unset($arrCCAAs['%']);//para que extranjero quede al final
				//$arrCCAAs[100] = 'Extranjero';
				$arrCCAAs['Extranjero'] = 'Extranjero';
				*/
				if ($codCCAA == '%')
				{ unset($arrCCAAs['%']);//para reordenar extranjero y todas 
			   $arrCCAAs['Extranjero'] = 'Extranjero';
						$arrCCAAs['%'] = 'Todas';
			 }	 

				//echo "<br><br>2-2b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAs: ";print_r($arrCCAAs);	
				
				$camposDatosAltasAnio = array('Total' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);	
				$camposDatosSociosDeAltaTotales = array('TotalDeAlta' =>0,'H' =>0,'%H' =>0,'M' =>0,'%M' =>0);
				$altas_bajas = array('TotalSocixsDeAlta' => $camposDatosSociosDeAltaTotales, 'ALTAS' => $camposDatosAltasAnio, 'BAJAS' =>	$camposDatosAltasAnio);

				foreach ($arrCCAAs as $clave => $valor)
				{ //$aux2[$clave] =	$aniosDatos;		//bien Con CODAGRUPACION,	//	$aux3[$valor] =	$aniosDatos;// BIEN Con ,NOMAGRUPACION		
						//echo "<br><br>2-3_0a modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:clave: ";print_r($clave);	
						//echo "<br><br>2-3_0b modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:valor: ";print_r($valor);	
						
							for ($y = $anioInferior; $y <= $anioSuperior; $y++)
							{ $aniosDatos[$y] =	$altas_bajas;			
									$arrCCAAsAltasBajas[$valor] = $aniosDatos;
							}	        						
				}						
				//echo "<br><br>2-3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAsAltasBajas: ";print_r($arrCCAAsAltasBajas);									
		  
			 for ($y = $anioInferior; $y <= $anioSuperior; $y++)
 		 {						
					$fechaInferior = $y.'-01-01'; $fechaSuperior = $y.'-12-31';
					
					//------------------ Inicio consulta socios para hallar el total de nº socios -----------------
					//por PROVINCIAS AL FINAL DE UN AÑO CONCRETO, DESCONTANDO LAS BAJAS PRODUCIDAS HASTA ESA FECHA 
					//---- para ir haciendo un array completo de socios por años ----------------------------------
					
				 //SOCIO.FECHAALTA <= '$fechaSuperior' socios que ya estaban dados de alta antes de una fecha $fechaSuperior' ";//Formato fechaSuperior: 'Y-12-31';
					$condicionFechaAlta = " AND SOCIO.FECHAALTA <= '$fechaSuperior' ";//Formato fechaSuperior: 'Y-12-31';
					
					//FECHABAJA='0000-00-00', Son los socios que aún NO se han dado de baja	
    	//FECHABAJA=fechaSuperior: 'Y-12-31' Son los socios que SÍ se han dado de baja	pero en una fecha posterior a la elegida (por lo que en esa fecha SÍ eran altas en esa fecha)
					$condicionFechasBajas = 	" AND (SOCIO.FECHABAJA ='0000-00-00' OR SOCIO.FECHABAJA >'$fechaSuperior' )";	
													
		 		$cadSqlSociosAnioCCAA = 														
						" SELECT IFNULL(CCAA.NOMBRECCAA,'Extranjero') AS nombreCCAA, COUNT(*) TotalDeAlta,

															COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
															COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

													FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN (PROVINCIA,CCAA) ON (MIEMBRO.CODPROV=PROVINCIA.CODPROV	AND CCAA.CCAA=PROVINCIA.CCAA)																	
																									
													WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
																			AND USUARIO.CODUSER=SOCIO.CODUSER"
                  .$condicionCCAA.$condicionFechaAlta.$condicionFechasBajas.
																		
													"	GROUP BY nombreCCAA ORDER BY nombreCCAA;	";		//USUARIO sobra	
														
			  //echo "<br><br>3_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:cadSqlSociosAnioCCAA: ";print_r($cadSqlSociosAnioCCAA);	
					
					$resBuscarSociosAnioCCAA = buscarCadSql($cadSqlSociosAnioCCAA,$conexionUsuariosDB['conexionLink']);
					
     //echo "<br><br>3_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:resBuscarSociosAnioCCAA: $y: ";print_r($resBuscarSociosAnioCCAA);						
					
					//--- Fin consulta socios dentro del bucle anterior para hallar el total de nº socios por CCAA 
			
					//--Inicio consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					$condicionFechasAltas = 	" AND FECHAALTA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';				

 				$cadSqlAltas = 									
						" SELECT IFNULL(CCAA.NOMBRECCAA,'Extranjero') AS nombreCCAA, COUNT(*) as 'TOTAL_ALTAS', 

															COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
															COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

													FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN (PROVINCIA,CCAA) ON (MIEMBRO.CODPROV=PROVINCIA.CODPROV	AND CCAA.CCAA=PROVINCIA.CCAA)																	
																									
													WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
																			AND USUARIO.CODUSER=SOCIO.CODUSER"
                  .$condicionCCAA.$condicionFechasAltas.
																		
													"	GROUP BY nombreCCAA ORDER BY nombreCCAA;	";		//USUARIO sobra																			
								
					//echo "<br><br>3_3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:cadSqlAltas: ";print_r($cadSqlAltas);		
					
     $resBuscarSociosAltas = buscarCadSql($cadSqlAltas,$conexionUsuariosDB['conexionLink']);
     
					//echo "<br><br>3_4 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:resBuscarSociosAltas: $y: ";print_r($resBuscarSociosAltas);	
					
					//--Fin consulta Altas entre dos fechas (sin descontar bajas) dentro del bucle anterior para ir haciendo un array completo de altas -----
					
					//-- Inicio consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----
					$condicionFechasBajas = 	" AND FECHABAJA BETWEEN '$fechaInferior' AND '$fechaSuperior' ";//Formato $fechaInferior: 'Y-01-01'; $fechaSuperior: 'Y-12-31';			
														
					$cadSqlBajas = 									
						" SELECT IFNULL(CCAA.NOMBRECCAA,'Extranjero') AS nombreCCAA, COUNT(*) as 'TOTAL_BAJAS',

															COUNT(IF(MIEMBRO.SEXO='H',1,NULL)) AS Hombres,  COUNT(IF(MIEMBRO.SEXO='H',1,NULL))*100/COUNT(*) AS '%Hombres',
															COUNT(IF(MIEMBRO.SEXO='M',1,NULL)) AS Mujeres, COUNT(IF(MIEMBRO.SEXO='M',1,NULL))*100/COUNT(*) AS '%Mujeres' 

													FROM USUARIO,SOCIO, MIEMBRO LEFT JOIN (PROVINCIA,CCAA) ON (MIEMBRO.CODPROV=PROVINCIA.CODPROV	AND CCAA.CCAA=PROVINCIA.CCAA)																	
																									
													WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
																			AND USUARIO.CODUSER=SOCIO.CODUSER"
                  .$condicionCCAA.$condicionFechasBajas.
																		
													"	GROUP BY nombreCCAA ORDER BY nombreCCAA;	";		//USUARIO sobra																	
															
					//echo "<br><br>3_5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:cadSqlBajas: ";print_r($cadSqlBajas);	
					
     $resBuscarSociosBajas = buscarCadSql($cadSqlBajas,$conexionUsuariosDB['conexionLink']);
					
     //echo "<br><br>3_6 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:resBuscarSociosBajas: $y: ";print_r($resBuscarSociosBajas);					
					
					//-- Fin consulta Bajas entre dos fechas dentro del bucle anterior para ir haciendo un array completo de bajas -----					
					
					//if ($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioAgrupacion ['codError'] !=='00000' )
     if ($resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioCCAA['codError'] !=='00000' )						
					{
				  $resExportarSocios = $resBuscarSociosBajas.$resBuscarSociosAltas.$resBuscarSociosAnioCCAA;
						$resExportarSocios['textoComentarios'] .= date('Y-m-d:H:i:s')." modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres. Error del sistema, vuelva a intentarlo pasado un tiempo ";		
						$resExportarSocios['nomFuncion'] .=", modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres()";
						
						require_once './modelos/modeloErrores.php';
						insertarError($resExportarSocios);	
					}
					else //$resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioCCAA['codError'] !=='00000' )	
					{
						//*************Inicio formar el array de Todos los socios que están de alta en esa fecha  **********						
						
					 $acumuladorTotalSociosAnioCCAAs  = 0;			//[TotalSocixsDeAlta] => 3 [Hombres] => 1 [%Hombres] => 33.3333 [Mujeres] => 2 [%Mujeres] => 66.6667 )
						$acumuladorTotalSociosAnioCCAAsH = 0;
						$acumuladorTotalSociosAnioCCAAsM = 0;							

						foreach ($resBuscarSociosAnioCCAA['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_1_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:contenido[nombreCCAA] : $y: ";print_r($contenido['nombreCCAA'] );							
			    
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $contenido['TotalDeAlta'];								
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['TotalSocixsDeAlta']['H']  = $contenido['Hombres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['TotalSocixsDeAlta']['%H'] = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['TotalSocixsDeAlta']['M']  = $contenido['Mujeres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['TotalSocixsDeAlta']['%M'] = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalSociosAnioCCAAs  += $contenido['TotalDeAlta'];
								$acumuladorTotalSociosAnioCCAAsH += $contenido['Hombres'];
								$acumuladorTotalSociosAnioCCAAsM += $contenido['Mujeres'];
								
		    }// foreach ($resBuscarSociosAnioCCAA['resultadoFilas'] as  $indice => $contenido)
						
						//$porcentajeH =   number_format(round(100*$acumuladorTotalSociosAnioCCAAsH/$acumuladorTotalSociosAnioCCAAs, 1), 1, ',', '');
						//$porcentajeM =   number_format(round(100*$acumuladorTotalSociosAnioCCAAsM/$acumuladorTotalSociosAnioCCAAs, 1), 1, ',', '');		

						if ($acumuladorTotalSociosAnioCCAAs === 0)//para evitar warning dicion por cero
						{ $porcentajeH =  number_format(0, 1, ',', '');
								$porcentajeM =  number_format(0, 1, ',', '');								
						}
						else
					 {	$porcentajeH =   number_format(round(100*$acumuladorTotalSociosAnioCCAAsH/$acumuladorTotalSociosAnioCCAAs, 1), 1, ',', '');
						  $porcentajeM =   number_format(round(100*$acumuladorTotalSociosAnioCCAAsM/$acumuladorTotalSociosAnioCCAAs, 1), 1, ',', '');							 						
				 	} 
						
						$arrCCAAsAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['TotalDeAlta'] = $acumuladorTotalSociosAnioCCAAs;							
						$arrCCAAsAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['H']     = $acumuladorTotalSociosAnioCCAAsH;	
						$arrCCAAsAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%H']    = $porcentajeH;									
						$arrCCAAsAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['M']     = $acumuladorTotalSociosAnioCCAAsM;			
						$arrCCAAsAltasBajas['Todas'][$y]['TotalSocixsDeAlta']['%M']    = $porcentajeM;	 						
						
			 	 //echo "<br><br>4_1_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAsAltasBajas: $y: ";print_r($arrCCAAsAltasBajas);
						//************* Fin formar el array de Todos los socios que estan de alta en esa fecha  **********	
						
      //************* Inicio formar el array parte de Altas ************************		
						$acumuladorTotalAltasAnio  = 0;
      $acumuladorTotalAltasAnioH = 0;
						$acumuladorTotalAltasAnioM = 0;

					 foreach ($resBuscarSociosAltas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_A_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:contenido[nombreCCAA] : $y: ";print_r($contenido['nombreCCAA'] );	// [TOTAL_ALTAS] => 1 [Hombres] => 1 [%Hombres] => 100.0000 [Mujeres] => 0 [%Mujeres] => 0.0000 
							
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['ALTAS']['Total']= $contenido['TOTAL_ALTAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['ALTAS']['H']    = $contenido['Hombres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['ALTAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['ALTAS']['M']    = $contenido['Mujeres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['ALTAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');

								$acumuladorTotalAltasAnio  += $contenido['TOTAL_ALTAS'];
								$acumuladorTotalAltasAnioH += $contenido['Hombres'];
								$acumuladorTotalAltasAnioM += $contenido['Mujeres'];
							
	     }//foreach ($resBuscarSociosAltas						

						if ($acumuladorTotalAltasAnio === 0)//para evitar warning división por cero
						{ $porcentajeAltasH =  number_format(0, 1, ',', '');
								$porcentajeAltasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeAltasH =  number_format(round(100*$acumuladorTotalAltasAnioH/$acumuladorTotalAltasAnio, 1), 1, ',', '');
								$porcentajeAltasM =  number_format(round(100*$acumuladorTotalAltasAnioM/$acumuladorTotalAltasAnio, 1), 1, ',', '');
						}								

						$arrCCAAsAltasBajas['Todas'][$y]['ALTAS']['Total']= $acumuladorTotalAltasAnio;
						$arrCCAAsAltasBajas['Todas'][$y]['ALTAS']['H']    = $acumuladorTotalAltasAnioH;			
						$arrCCAAsAltasBajas['Todas'][$y]['ALTAS']['%H']   = $porcentajeAltasH;	
						$arrCCAAsAltasBajas['Todas'][$y]['ALTAS']['M']    = $acumuladorTotalAltasAnioM;			
						$arrCCAAsAltasBajas['Todas'][$y]['ALTAS']['%M']   = $porcentajeAltasM;							
						//echo "<br><br>4_2_A_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAsAltasBajas: $y: ";print_r($arrCCAAsAltasBajas);
						
      //************* Fin formar el array parte de Altas ************************									
						
						//************* Inicio formar el array parte de Bajas ************************						
					 $acumuladorTotalBajasAnio  = 0;
						$acumuladorTotalBajasAnioH = 0;
						$acumuladorTotalBajasAnioM = 0;					

					 foreach ($resBuscarSociosBajas['resultadoFilas'] as  $indice => $contenido)
			   { //echo "<br><br>4_2_B_1 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:contenido[nombreCCAA] : $y: ";print_r($contenido['nombreCCAA'] );							
			     //$resultadoFilasAux[$f][$indice] = utf8_encode($fila[$indice]);//Para PHP
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['BAJAS']['Total']= $contenido['TOTAL_BAJAS'];//[Total] => 0 [H] => 0 [%H] => 0 [M] => 0 [%M] => 0
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['BAJAS']['H']    = $contenido['Hombres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['BAJAS']['%H']   = number_format(round($contenido['%Hombres'], 1), 1, ',', '');
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['BAJAS']['M']    = $contenido['Mujeres'];
								$arrCCAAsAltasBajas[$contenido['nombreCCAA']][$y]['BAJAS']['%M']   = number_format(round($contenido['%Mujeres'], 1), 1, ',', '');
							
								$acumuladorTotalBajasAnio  += $contenido['TOTAL_BAJAS'];
								$acumuladorTotalBajasAnioH += $contenido['Hombres'];
								$acumuladorTotalBajasAnioM += $contenido['Mujeres'];
		    }//foreach ($resBuscarSociosBajas			

						if ($acumuladorTotalBajasAnio === 0)//para evitar warning división por cero
						{ $porcentajeBajasH =  number_format(0, 1, ',', '');
								$porcentajeBajasM =  number_format(0, 1, ',', '');								
						}
						else
						{	$porcentajeBajasH = number_format(round(100*$acumuladorTotalBajasAnioH/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						  $porcentajeBajasM = number_format(round(100*$acumuladorTotalBajasAnioM/$acumuladorTotalBajasAnio, 1), 1, ',', '');
						}											

						$arrCCAAsAltasBajas['Todas'][$y]['BAJAS']['Total']= $acumuladorTotalBajasAnio;
						$arrCCAAsAltasBajas['Todas'][$y]['BAJAS']['H']    = $acumuladorTotalBajasAnioH;	
						$arrCCAAsAltasBajas['Todas'][$y]['BAJAS']['%H']   = $porcentajeBajasH;									
						$arrCCAAsAltasBajas['Todas'][$y]['BAJAS']['M']    = $acumuladorTotalBajasAnioM;			
						$arrCCAAsAltasBajas['Todas'][$y]['BAJAS']['%M']   = $porcentajeBajasM;							
			   //echo "<br><br>4_2_B_2 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAsAltasBajas: $y: ";print_r($arrCCAAsAltasBajas);
						
					 //************* Fin formar el array parte de Bajas ************************						
	
					}	//else	!!$resBuscarSociosBajas['codError'] !=='00000' || $resBuscarSociosAltas['codError'] !=='00000'  || $resBuscarSociosAnioCCAA['codError'] !=='00000' )	
				
		 	}// for ($y = $anioInferior; $y <= $anioSuperior; $y++)
					
					if ($codCCAA !== '%') //para eliminar el acumulado para "Todas" en el caso de elegir una sola provincia
					{ unset($arrCCAAsAltasBajas['Todas']);
					}	 				
					//echo "<br><br>4_3 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:arrCCAAsAltasBajas:  ";print_r($arrCCAAsAltasBajas);

					$nomFile = 'ExportarExcelAltasBajas_CCAA';
					$cabeceraColum1 = 'CCAA';
					
					require_once './modelos/libs/exportarExcelEstadisticasAltasBajas.php';				
					$archivoExcelSocios = exportarExcelEstadisticasAltasBajas($arrCCAAsAltasBajas,$cabeceraColum1,$nomFile,$anioInferior,$anioSuperior);				
					
					if ($archivoExcelSocios['codError'] !=='00000')//
					{ $resExportarSocios = $archivoExcelSocios;
							$resExportarSocios['textoComentarios'] .= date('Y-m-d:H:i:s').":	exportarExcelEstadisticasAltasBajas. Error del sistema, al cerrar el buffer interno. Vuelva a intentarlo pasado un tiempo ";		
							$resExportarSocios['nomFuncion'] .=", exportarExcelEstadisticasAltasBajas()";					
					}
		
	  }//else $parValorCombo['codError']=='00000'
 }//else //if ($conexionUsuariosDB['codError']=='00000')
 //echo '<br><br>5 modeloPresCoord.php:mExportarExcelEstadisticasAltasBajasCCAAPres:resExportarSocios: ';print_r($resExportarSocios);//no se verá porque el buffer está cautivo
					
	return $resExportarSocios;	
}//****Parse error: syntax error, unexpected '}', expecting end of file in /home/virtualmin/europalaica.com/public_html/usuarios_desarrollo/modelos/modeloPresCoord.php on line 3961
//---------------------------- Fin mExportarExcelEstadisticasAltasBajasCCAAPres --------------------

//==================================== FIN ESTADISTICAS =============================================



//========================= INICIO AGRUPACIONES TERRITORIALES =======================================

/*----------------------------- Inicio actualizarDatosAgrupacion -------------------------------------
Actualiza la tabla AGRUPACIONTERRITORIAL 

RECIBE: un el valor "codAgrupacion" y un array "$arrDatosActualizar" con los campos del 
formulario que después han sido validados 

DEVUELVE: un array con los controles de errores 

LLAMADA: cPresidente.php:actualizarDatosAgrupacionPres()

LLAMA:  require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
configMySQL.php:conexionDB(,)
modeloMysSQL.php:actualizarTabla_ParamPosicion()
modeloErrores.php:insertarError()

OBSERVACIONES: 
No se requiere modificaciones para PDO en la función actualizarTabla(), 
lo gestiona dentro de ella con los par. que reciben									
-----------------------------------------------------------------------------------------------------*/ 
function actualizarDatosAgrupacion($codAgrupacion, $arrDatosActualizar)
{
		//echo '<br><br>0-1 modeloPresCoord.php:actualizarDatosAgrupacion:codAgrupacion: ';print_r($codAgrupacion);
		//echo '<br><br>0-2 modeloPresCoord.php:actualizarDatosAgrupacion:arrDatosActualizar: ';print_r($arrDatosActualizar);
		
		$actualizarDatosAgrupacion['nomScript'] = 'modeloPresCoord.php';
		$actualizarDatosAgrupacion['nomFuncion'] = 'actualizarDatosAgrupacion';	
		$actualizarDatosAgrupacion['codError'] = '00000';
		$actualizarDatosAgrupacion['errorMensaje'] = "";
		
		require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
		require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
		
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
		
		if ($conexionDB['codError'] !== "00000")
		{ $actualizarDatosAgrupacion = $conexionDB;
		}
		else // conexionDB['codError'] == "00000"
		{	
		 if (!isset($codAgrupacion) || empty($codAgrupacion))
			{ 		
					$actualizarDatosAgrupacion['codError'] = "70601"; //Faltan paramámetros necesarios para UPDATE
					$actualizarDatosAgrupacion['errorMensaje'] = "modeloPresCoord.php:actualizarDatosAgrupacion. Error sistema: Falta el parámetro 'codAgrupacion' necesario para actualizar tabla AGRUPACIONTERRITORIAL";
					$resActualizarDatosAgrupacion['textoComentarios'] = $resActualizarDatosAgrupacion['errorMensaje'];
			}
			else
			{
					$arrayCondiciones['CODAGRUPACION']['valorCampo'] = $codAgrupacion;
					$arrayCondiciones['CODAGRUPACION']['operador'] = '=';
					$arrayCondiciones['CODAGRUPACION']['opUnir'] = ' ';
						
					//echo '<br><br>1-1 modeloPresCoord.php:actualizarDatosAgrupacion:arrayCondiciones: ';print_r($arrayCondiciones);
					
					foreach ($arrDatosActualizar as $indice => $contenido)                         
					{ 		
							$arrayDatosAct[$indice] = $contenido['valorCampo']; 
					}		
					
					//echo '<br><br>1-2 modeloPresCoord.php:actualizarDatosAgrupacion:arrayDatosAct: ';print_r($arrayDatosAct);
					
					$tablaActualizar = 'AGRUPACIONTERRITORIAL';
					
					//$actualizarDatosAgrupacion = actualizarTabla($tablaActualizar,$arrayCondiciones,$arrayDatosAct,$conexionDB['conexionLink']);//puede dar error por repetición de código CODAGRUPACION
					
					$resActualizarDatosAgrupacion = actualizarTabla_ParamPosicion($tablaActualizar,$arrayCondiciones,$arrayDatosAct,$conexionDB['conexionLink']);	
					
					//echo '<br><br>2 modeloPresCoord.php:resActualizarDatosAgrupacion: ';print_r($resActualizarDatosAgrupacion);			
					
					if ($resActualizarDatosAgrupacion['codError'] !== '00000')
					{$actualizarDatosAgrupacion = $resActualizarDatosAgrupacion;		
						$actualizarDatosAgrupacion['textoComentarios'] .= ". modeloPresCoord.php:actualizarDatosAgrupacion():actualizarTabla_ParamPosicion(). ";
						
						require_once './modelos/modeloErrores.php';		
						$resInsertarErrores = insertarError($resActualizarDatosAgrupacion,$conexionDB['conexionLink']);
						
						if ($resInsertarErrores['codError'] !== '00000')
						{$actualizarDatosAgrupacion['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
						}	 
					}
			}
		}// else conexionDB['codError'] == "00000"
		//echo '<br><br>3 modeloPresCoord.php:actualizarDatosAgrupacion: ';print_r($actualizarDatosAgrupacion);
		
		return $actualizarDatosAgrupacion;
} 
/*---------------------------------- Fin actualizarDatosAgrupacion ---------------------------------*/

//========================= FIN AGRUPACIONES TERRITORIALES  =========================================


//===== INICIO PERMISOS ROLES:  ASIGNAR-MODIFICAR-ELIMINAR-MOSTRAR ROLES ============================

//===== INICIO GESTIÓN DE ASIGNAR-MODIFICAR-ELIMINAR- AREA DE COORDINACIÓN A COORDINADORES ==========

/*------------------------------ Inicio asignarCoordinadorArea --------------------------------------
En esta función se asigna a un Coordinador un Area de gestión territorial, insertando una fila 
en "COORDINAAREAGESTIONAGRUP" y otra en "USUARIOTIENEROL" y controlando transacciones 

RECIBE: $datosSocioAsignar array con los campos validados, que se utilizarán para insertar una fila
en "COORDINAAREAGESTIONAGRUP" y otra en "USUARIOTIENEROL"

DEVUELVE: un array con los controles de errores, y mensajes y además el "NOMAREAGESTION"

LLAMADA: cPresidente.php:asignarCoordinacionArea()
LLAMA: modeloPresCoord.php:insertarCoordinacionAreaGestion(),
modeloUsuarios.php:insertarUsuarioRol(")
modeloMySQL.php:buscarEnTablas()
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
BBDD/MySQL/conexionMySQL.php:conexionDB()
transationPDO.php
modeloErrores.phpinsertarError()

OBSERVACIONES: Incluyo arrayBind paraPDO. PHP 7.3.21
---------------------------------------------------------------------------------------------------*/
function asignarCoordinadorArea($datosSocioAsignar)
{
	//echo "<br><br>0-1 modelPresCoord:asignarCoordinadorArea:datosSocioAsignar: ";print_r($datosSocioAsignar);
	require_once './modelos/modeloUsuarios.php';
	
	$resAsignarCoordinador['nomFuncion'] = "asignarCoordinadorArea";
	$resAsignarCoordinador['nomScript'] = "modelPresCoord.php";	
 $resAsignarCoordinador['codError'] = "00000";
 $resAsignarCoordinador['errorMensaje'] = "";
	
 //$arrMensaje['textoCabecera'] = 'Asignación de un coordinador/a de grupos territoriales';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);			

	if ($conexionDB['codError'] !== "00000")
	{ $resAsignarCoordinador = $conexionDB;
	}
	else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modelPresCoord:asignarCoordinadorArea:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";
		//echo "<br><br>1-2 modelPresCoord:asignarCoordinadorArea:conexionsDB: ";var_dump($conexionsDB);echo "<br><br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;											
				$resAsignarCoordinador = $resIniTrans;	
			//echo "<br><br>1 modelPresCoord:asignarCoordinadorArea:resAsignarCoordinador: ";print_r($resAsignarCoordinador);
		}			
		else //$resIniTrans['codError'] == '00000'
		{			
			$datosSocioAsignarArea['CODUSER'] = $datosSocioAsignar['CODUSER'];
   $datosSocioAsignarArea['CODAREAGESTIONAGRUP'] = $datosSocioAsignar['CODAREAGESTIONAGRUP'];
			$datosSocioAsignarArea['OBSERVACIONES'] = $datosSocioAsignar['OBSERVACIONES'];
			$datosSocioAsignarArea['FECHAASIGNACION'] = date('Y-m-d');
	
	  /*Revisar ---- OJO Con nueva tabla AREAGESTION no sería necesario datos del área de 
			 gestion 'email','NOMAREAGESTION creo que no??',"observaciones"
		  $datosSocioAsignarArea['NOMBREAREAGESTION'] = 'Nombre area que sobrará';
		  $datosSocioAsignarArea['EMAIL'] = 'EMAIL que sobrará';
			---------------------------------------------------------------------------*/
		 //echo "<br><br>2-1 modelPresCoord:asignarCoordinadorArea:datosSocioAsignarArea: ";print_r($datosSocioAsignarArea);
				
			$reInsertarCoordArea = insertarCoordinacionAreaGestion($datosSocioAsignarArea);//en modeloPresCoord.php, contiene $conexionDB	probado error ok
				
		 //echo "<br><br>2-2 modelPresCoord:asignarCoordinadorArea:reInsertarCoordArea: ";print_r($reInsertarCoordArea);

			if ($reInsertarCoordArea['codError'] !== '00000')			
   {$resAsignarCoordinador  = $reInsertarCoordArea;
   }	
   else //$reInsertarCoordArea['codError']=='00000
			{
    $datosSocioRol['CODUSER']['valorCampo'] = $datosSocioAsignar['CODUSER'];
    $datosSocioRol['CODROL']['valorCampo'] = '6';
				
    $reRolInsertar = insertarUsuarioRol($datosSocioRol);//en modeloUsuarios.php incluye conexionDB probado error ok
		
		  if ($reRolInsertar['codError'] !== '00000')			
	   {$resAsignarCoordinador  = $reRolInsertar;
    }		
			 else //$reRolInsertar['codError']=='00000')
			 {$tablasBusqueda = 'AREAGESTION';	
			 	$camposBuscados = 'NOMAREAGESTION';	//para mostrar en pantalla y en el email al coordinador
     //$cadCondicionesBuscar = " WHERE AREAGESTION.CODAREAGESTION=".$datosSocioAsignar['CODAREAGESTIONAGRUP'];
     $cadCondicionesBuscar = " WHERE AREAGESTION.CODAREAGESTION = :codAreaGestionAgrup ";	
     
					$arrBind = array(':codAreaGestionAgrup' => $datosSocioAsignar['CODAREAGESTIONAGRUP']);  					
							 		
					require_once "BBDD/MySQL/modeloMySQL.php";//ya está incluida al principio de modelPresCoord.php
		 		//$reBuscarAreaNom = buscarEnTablas('AREAGESTION',$cadCondicionesBuscar,$camposBuscados,$conexionDB['conexionLink']); //probado error ok		
					
					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
					$reBuscarAreaNom = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);//probado error ok 					
					
		   
					//echo "<br><br>3 modelPresCoord:asignarCoordinadorArea:reBuscarAreaNom: ";print_r($reBuscarAreaNom);
		  
			 	if ($reBuscarAreaNom['codError'] !== '00000')
		   { $resAsignarCoordinador = $reBuscarAreaNom;	
		   }	
				 else //$reBuscarAreaNom['codError']=='00000')
				 {	
						if ($reBuscarAreaNom['numFilas'] !== 1)//Se asigna bien pero el mensaje la agrupación NOMAREAGESTION, pede no ser correcto mejo no poner nada 
						{ $resAsignarCoordinador['NOMAREAGESTION'] ='';
						}	
      else
						{ $resAsignarCoordinador['NOMAREAGESTION'] = $reBuscarAreaNom['resultadoFilas'][0]['NOMAREAGESTION'];	
      }	

						/*---------------- Inicio COMMIT ---------------------------------------*/
						$resFinTrans = commitPDO($conexionDB['conexionLink']);
						
						//echo "<br><br>4-1 modelPresCoord:asignarCoordinadorArea:resFinTrans: ";var_dump($resFinTrans);						
						
						if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502'	
						{										
							$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de eliminar socio/a. ';
							$resFinTrans['numFilas'] = 0;	
															
							$resAsignarCoordinador  = $resFinTrans;	
						 //echo "<br><br>4-2 modelPresCoord:asignarCoordinadorArea:resAsignarCoordinador: ";print_r($resAsignarCoordinador);echo "<br>";
						}
						/*---------------- Fin COMMIT ------------------------------------------*/
						else // $resFinTrans['codError'] == '00000'						
					 {$arrMensaje['textoComentarios'] = "Se ha asignado el área territorial de coordinación <strong>-".$resAsignarCoordinador['NOMAREAGESTION'].
						                                   "- </strong>al socio/a <strong> ".$datosSocioAsignar['NOM']." ".$datosSocioAsignar['APE1'].	" </strong> en la base de datos de	Europa Laica
																																									<br /><br /><br />Se ha enviado un correo a la dirección personal del coordinador<strong> ".$datosSocioAsignar['EMAIL'].
																																									" </strong>para comunicar la asignación.<br /><br /><br />
                                         En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
                                         aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de coordinación en Europa Laica.
                                         <br /><br />
                                         Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org 
																													            (o por correo postal al domicilio legal de Europa Laica)";																																				
					 }
					}//else $reBuscarAreaNom['codError']=='00000')
				}//else $reRolInsertar['codError']=='00000'	
		 }//else $reInsertarCoordArea['codError']=='00000																									
	 
	 	//echo "<br><br>5 modelPresCoord:asignarCoordinadorArea:resAsignarCoordinador: ";print_r($resAsignarCoordinador);
			
			//---------------- Inicio tratamiento errores -------------------------------	   
			//--- Inicio deshacer transación en las tablas modificadas ---------------		
			if ($resAsignarCoordinador['codError'] !== '00000')
			{ 				
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
						
					if ($resDeshacerTrans['codError'] !== '00000')//será $resDeshacerTrans['codError'] = '70503';
					{ $resAsignarCoordinador['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];			   
					}		
			}//--- Fin deshacer transación en las tablas modificadas -----------------
	 }//else $resIniTrans['codError'] == '00000'		

		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
		
  if ($resAsignarCoordinador['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($resAsignarCoordinador['textoComentarios']) ) 
			{ $resAsignarCoordinador['textoComentarios'] = ". modeloPresCoord.php:resAsignarCoordinador(): ".$resAsignarCoordinador['textoComentarios'];}	
			else
			{	$resAsignarCoordinador['textoComentarios'] = ". modelPresCoord.php:asignarCoordinadorArea(): ";}					
				
				require_once './modelos/modeloErrores.php';//si es un error de conexión a la tabla error, insertar errores 
				$resInsertarErrores = insertarError($resAsignarCoordinador);	
					
				if ($resInsertarErrores['codError'] !== '00000')
				{ $resAsignarCoordinador['codError'] = $resInsertarErrores['codError'];
				 	$resAsignarCoordinador['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				}				
		}//if ($resAsignarCoordinador['codError']!=='00000')	
	 //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------
	}//else $conexionDB['codError']=="00000"	
	
 $resAsignarCoordinador['arrMensaje'] = $arrMensaje;	
 
	//echo "<br><br>6 modelPresCoord:asignarCoordinadorArea:resAsignarCoordinador: ";print_r($resAsignarCoordinador);	
	
 return 	$resAsignarCoordinador; 	
}
/*------------------------------ Fin asignarCoordinadorArea ----------------------------------------*/

/*----------------------------- Inicio insertarCoordinacionAreaGestion ------------------------------
Inserta un Coordinador de Area de gestión en tabla COORDINAAREAGESTIONAGRUP

Llamada: desde modeloPresCoord.php:asignarCoordinadorArea()
Llama: modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: PHP 7.3.21
No necesita arrayBind paraPDO: lo incluye internamente la función

ACASO HAYA QUE CONTROLAR DUPLICADOS USUARIO+CODAREAGESTION
----------------------------------------------------------------------------------------------------*/
function insertarCoordinacionAreaGestion($arrInsertar)				
{
 //echo "<br><br>O-1 modelPresCoord:insertarCoordinacionAreaGestion:arrInsertar: "; print_r($arrInsertar);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
		
	if ($conexionDB['codError'] !== "00000")
	{ $reInserCoordAreaGestion = $conexionDB;
	}
	else
	{//echo "<br><br>1 modelPresCoord:insertarCoordinacionAreaGestion:$arrInsertar:"; print_r($arrInsertar);
  
		require_once "BBDD/MySQL/modeloMySQL.php"; 
	 $reInserCoordAreaGestion = insertarUnaFila('COORDINAAREAGESTIONAGRUP',$arrInsertar,$conexionDB['conexionLink']);			
	}
  //echo "<br>2 modelPresCoord:insertarCoordinacionAreaGestion:reInserCoordAreaGestion:"; print_r($reInserCoordAreaGestion);
		
  return $reInserCoordAreaGestion;
}
/*---------------------------- Fin insertarCoordinacionAreaGestion ---------------------------------*/	

/*------------------------------ Inicio eliminarAsignCoordinadorArea --------------------------------
En esta función se elimina la Asignación a un Coordinador de un Area de gestión territorial, 
en tablas COORDINAAREAGESTIONAGRUP y USUARIOTIENEROL

RECIBE: $datosCoordinadorEliminar: array con los campos hide de los form formBorrarCambiarCoordArea.php
DEVUELVE: un array con "NOMAREAGESTION" Y controles de errores, y mensajes 

LLAMADA: cPresidente.php:eliminarCoordinacionArea()
LLAMA: modeloPresCoord.php:eliminarCoordinacionAreaGestion()
modeloUsuario.php:eliminarUsuarioTieneRol()
modeloMySQL.php:buscarCadSql()
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
BBDD/MySQL/conexionMySQL.php:conexionDB()
transationPDO.php
modeloErrores.phpinsertarError()

OBSERVACIONES: Incluyo PDO: con $arrBindValues. Probada PHP 7.3.21

Se graban los errores del sistema en ERRORES
-----------------------------------------------------------------------------------------------------*/
function eliminarAsignCoordinadorArea($datosCoordinadorEliminar)
{
	//echo "<br><br>0-1 modeloPresCoord:eliminarAsignCoordinadorArea:datosCoordinadorEliminar: ";print_r($datosCoordinadorEliminar);
	
	$resCoordinadorEliminar['nomFuncion'] ="eliminarAsignCoordinadorArea";
	$resCoordinadorEliminar['nomScript'] ="modeloPresCoord.php";	
 $resCoordinadorEliminar['codError'] ='00000';
 $resCoordinadorEliminar['errorMensaje'] = '';
	
 $arrMensaje['textoCabecera'] = 'Eliminar asignación de área de gestión territorial a un coordinador/a';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== "00000")
	{ $resCoordinadorEliminar = $conexionDB;
	}
	else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloPresCoord:eliminarAsignCoordinadorArea:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";
		//echo "<br><br>1-2 modeloPresCoord:eliminarAsignCoordinadorArea:conexionsDB: ";var_dump($conexionsDB);echo "<br><br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;	   	
											
				$resCoordinadorEliminar = $resIniTrans;	
			 //echo "<br><br>1-3 modeloPresCoord:eliminarAsignCoordinadorArea:resCoordinadorEliminar: ";print_r($resCoordinadorEliminar);	
		}			
		else //$resIniTrans['codError'] == '00000'
		{//echo "<br><br>2 modeloPresCoord:eliminarAsignCoordinadorArea:datosCoordinadorEliminar: ";print_r($datosCoordinadorEliminar);
				
			$reEliminarCoordAreaGestion = eliminarCoordinacionAreaGestion('COORDINAAREAGESTIONAGRUP',$datosCoordinadorEliminar,$conexionDB['conexionLink']);//En modeloPresCoord.php probado error ok
			
		 //echo "<br><br>3 modeloPresCoord:eliminarAsignCoordinadorArea:reEliminarCoordAreaGestion: ";print_r($reEliminarCoordAreaGestion);//
	
			if ($reEliminarCoordAreaGestion['codError']!=='00000')			
   {$resCoordinadorEliminar = $reEliminarCoordAreaGestion;
   }	
				elseif ($reEliminarCoordAreaGestion['numFilas'] <= 0)
				{ $resCoordinadorEliminar['codError'] = '80001';
						$resCoordinadorEliminar['errorMensaje'] = 'No se pudo eliminar el coordinador de tabla COORDINAAREAGESTIONAGRUP';
				}				
   else //$reEliminarCoordAreaGestion['codError']=='00000
			{$codRol  = '6';//ELIMINAR el rol de coordinador=6		
			 
				require_once './modelos/modeloUsuarios.php';
				$reRolEliminar = eliminarUsuarioTieneRol('USUARIOTIENEROL',$datosCoordinadorEliminar['CODUSER'],$codRol,$conexionDB['conexionLink']);//probado error ok
		  
				//echo "<br><br>4-1 modeloPresCoord:eliminarAsignCoordinadorArea:reRolEliminar: ";print_r($reRolEliminar);//
				
		  if ($reRolEliminar['codError']!=='00000')			
	   {$resCoordinadorEliminar = $reRolEliminar;
    }
				elseif ($reRolEliminar['numFilas'] <= 0)
				{ $resCoordinadorEliminar['codError'] = '80001';
						$resCoordinadorEliminar['errorMensaje'] = 'No se pudo eliminar el rol de tabla USUARIOTIENEROL';
				}	
				else //$reRolEliminar['codError']=='00000'			
			 {$tablasBusqueda = 'AREAGESTION';
			 	$camposBuscados = 'NOMAREAGESTION';	//para mostrar en pantalla y en el email al coordinador		
     
     $cadCondicionesBuscar = " WHERE AREAGESTION.CODAREAGESTION = :codAreaGestionAgrup ";							
			
     $arrBind = array(':codAreaGestionAgrup' => $datosCoordinadorEliminar['CODAREAGESTIONAGRUP']);  					
							 		
					require_once "BBDD/MySQL/modeloMySQL.php";//ya está incluida al principio de modelPresCoord.php					
					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
									
					$reBuscarAreaNom = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//probado error ok 
		   
					//echo "<br><br>4-2 modeloPresCoord:eliminarAsignCoordinadorArea:reBuscarAreaNom: ";print_r($reBuscarAreaNom);
		  
			 	if ($reBuscarAreaNom['codError']!=='00000')
		   { $resCoordinadorEliminar = $reBuscarAreaNom;	
		   }
				 else //$reRolEliminar['codError']=='00000')
				 {$resCoordinadorEliminar['NOMAREAGESTION'] = $reBuscarAreaNom['resultadoFilas'][0]['NOMAREAGESTION'];		

						//----------------Inicio COMMIT ------------------------------------------
						$resFinTrans = commitPDO($conexionDB['conexionLink']);
						
						//echo "<br><br>4-3 modeloPresCoord:eliminarAsignCoordinadorArea:resFinTrans: ";var_dump($resFinTrans);						
						
						if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502'	
						{										
							$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de eliminar socio/a. ';
							$resFinTrans['numFilas'] = 0;	
															
							$resCoordinadorEliminar = $resFinTrans;
							//echo "<br><br>5 modeloPresCoord:eliminarAsignCoordinadorArea:resCoordinadorEliminar: ";print_r($resCoordinadorEliminar);
						}
						//----------------Fin COMMIT ---------------------------------------------
						else // $resFinTrans['codError'] == '00000'
					 {$arrMensaje['textoComentarios'] = "Se ha cancelado el rol de cordinador/a del área teritorial <strong> -".
						                                   $resCoordinadorEliminar['NOMAREAGESTION']."- </strong>al socio/a  <strong> ".$datosCoordinadorEliminar['NOM']." ".
																																									$datosCoordinadorEliminar['APE1']." </strong> en la base de datos de	Europa Laica
							                                  <br /><br /><br />Se ha enviado un correo a la dirección <strong>".$datosCoordinadorEliminar['EMAIL']." </strong>para comunicárselo<br /><br />";
					 }
					}//else $reRolEliminar['codError']=='00000')
				}//else $reRolEliminar['codError']=='00000'				
		 }//else $reEliminarCoordAreaGestion['codError']=='00000																																
	 
		 //echo "<br><br>6 modeloPresCoord:eliminarAsignCoordinadorArea:resCoordinadorEliminar: ";print_r($resCoordinadorEliminar);
			
			//---------------- Inicio tratamiento errores -------------------------------   
			//--- Inicio deshacer transación en las tablas modificadas ---------------	
	  if ($resCoordinadorEliminar['codError']!=='00000')
	 	{								
  		$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
								
				if ($resDeshacerTrans['codError'] !== '00000')//será $resDeshacerTrans['codError'] = '70503';
				{ $resCoordinadorEliminar['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];
				}
			}//--- Fin deshacer transación en las tablas modificadas -----------------
  }//else $resIniTrans['codError'] == '00000'	
		
	 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
 	if ($resCoordinadorEliminar['codError']!=='00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($$resCoordinadorEliminar['textoComentarios']) ) 
			{ $resCoordinadorEliminar['textoComentarios'] = ". modeloPresCoord.php:eliminarAsignCoordinadorArea(): ".$resCoordinadorEliminar['textoComentarios'];}	
			else
			{ $resCoordinadorEliminar['textoComentarios'] = ". modeloPresCoord.php:eliminarAsignCoordinadorArea(): ";}								
									
			require_once './modelos/modeloErrores.php';//si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores = insertarError($resCoordinadorEliminar);	
				
			if ($resInsertarErrores['codError'] !== '00000')
			{ $resCoordinadorEliminar['codError'] = $resInsertarErrores['codError'];
					$resCoordinadorEliminar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}	
		}//if ($resCoordinadorEliminar['codError']!=='00000'...)
  //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------					
	
	}//else $conexionDB['codError']=="00000"
 
	$resCoordinadorEliminar['arrMensaje'] = $arrMensaje;	
	
 //echo "<br><br>7 modeloPresCoord:eliminarAsignCoordinadorArea:resCoordinadorEliminar: ";print_r($resCoordinadorEliminar);
	
 return 	$resCoordinadorEliminar; 	
}
/*------------------------------ Fin eliminarAsignCoordinadorArea -----------------------------------*/

/*--------------------- Inicio eliminarCoordinacionAreaGestion ---------------------------------------
Elimina la/s fila/s en la tabla 'COORDINAAREAGESTIONAGRUP', para la condición $codUser y $codArea, 
podrá eliminar una fila o varias según el número de áreas de gestión que tenga asignado un coordinador 
(lo habitual es que sólo sea un area de gestión, pero puede haber situaciones que coordine más de un área)

Cuando se da de baja a un usuario que es también coordinador, se le borran todas las filas para 
ese $codUser, es decir será $codArea =='%', pero cuando un coordinador deja de ser coordinadar, 
pero no se da de baja (lo habitual) solo se borra la fila correspondiente a $codUser, 
y será $codArea = 6 (rol coordinador),

RECIBE: $tabla='COORDINAAREAGESTIONAGRUP', 
$arrCondicionesEliminar=Array([CODUSER]=>nnn [CODAREAGESTIONAGRUP]=> '%', 6, ..) 
$conexionDB 
DEVUELVE: array $resEliminarCoordinacionAreaGestion con número de filas borradas y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido(),eliminarAsignCoordinadorArea()
LLAMA: modeloMySQL.php:borrarFilas()

OBSERVACIONES: PHP 7.3.21. modifico para incluir PHP: PDOStatement::bindParamValue
Es necesaria conexión a BBDD previa 
----------------------------------------------------------------------------------------------------*/
function eliminarCoordinacionAreaGestion($tabla,$arrCondicionesEliminar,$conexionDB)     
{
	//echo '<br><br>1 modeloPresCoord.php:eliminarCoordinacionAreaGestion:arrCoordinadorEliminar:';print_r($arrCondicionesEliminar);

 $codUser = $arrCondicionesEliminar['CODUSER'];
	$codArea = $arrCondicionesEliminar['CODAREAGESTIONAGRUP'];
	
	//$cadCondicionesCodUser = "CODUSER = '".$codUser."'";
	$cadCondicionesCodUser = "CODUSER = :codUser ";

 if ( !isset($codArea) || empty($codArea) || $codArea =='%')//cuando se quitan todas las áreas de gestión
 { 
	  $cadCondicionesAreaGestion = '';			
			$arrBind = array(':codUser' => $codUser);
 }
 else //cuando se le quita un área de gestión concreta, podría tener asignada más de una
	{ 
	  //$cadCondicionesAreaGestion = " AND COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP=\"".$codArea."\""; 
   $cadCondicionesAreaGestion = " AND COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP = :codArea "; 			
			$arrBind = array(':codUser' => $codUser, ':codArea' => $codArea); 
 }		
	$cadenaCondiciones =	$cadCondicionesCodUser.$cadCondicionesAreaGestion;	
		
 require_once "BBDD/MySQL/modeloMySQL.php"; 
	$resEliminarCoordinacionAreaGestion = borrarFilas($tabla,$cadenaCondiciones,$conexionDB,$arrBind); 	
	
	//echo '<br><br>2 modeloPresCoord.php:eliminarCoordinacionAreaGestion:resEliminarCoordinacionAreaGestion: ';print_r($resEliminarCoordinacionAreaGestion);
	
	return $resEliminarCoordinacionAreaGestion;
 } 
/*--------------------------- fin eliminarCoordinacionAreaGestion -----------------------------------*/ 

/*------------------------------ Inicio cambiarCoordinadorArea ---------------------------------------
En esta función se cambia a un Coordinador de un Area de gestión a otro, actualiza la tabla COORDINAAREAGESTIONAGRUP
        
RECIBE: array $datosCoordCambiar , que se utilizarán para actualizar "COORDINAAREAGESTIONAGRUP" 
DEVUELVE: un array el "NOMAREAGESTION" Y con los controles de errores

LLAMADA: desde cPresidente.php:cambiarCoordinacionArea()
LLAMA: modeloPresCoord.php:actualizarCoordinacionAreaGestion()
modeloMySQL.php:buscarCadSql()
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
BBDD/MySQL/conexionMySQL.php:conexionDB()
transationPDO.php
modeloErrores.php:insertarError()
       
OBSERVACIONES: PHP 7.3.21. modifico para incluir PHP: PDOStatement::bindParamValue
Se graban los errores del sistema en ERRORES
-----------------------------------------------------------------------------------------------------*/
function cambiarCoordinadorArea($datosCoordCambiarArea)
{
	//echo "<br><br>0-1 modeloPresCoord:cambiarCoordinadorArea:datosCoordCambiarArea: ";print_r($datosCoordCambiarArea);

	$resCambiarAreaCoordinador['nomFuncion'] ="cambiarCoordinadorArea";
	$resCambiarAreaCoordinador['nomScript'] ="modeloAdmin.php";	
 $resCambiarAreaCoordinador['codError'] ="00000";
 $resCambiarAreaCoordinador['errorMensaje'] = "";
	 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";				

	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB ['codError'] !== "00000")
	{ $resCambiarAreaCoordinador = $conexionDB;
	  //$arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}
	else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloPresCoord:cambiarCoordinadorArea::resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";
		//echo "<br><br>1-2 modeloPresCoord:cambiarCoordinadorArea::conexionsDB: ";var_dump($conexionsDB);echo "<br><br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;	   	
											
				$resCambiarAreaCoordinador =$resIniTrans;	
				//echo "<br><br>1-3 modeloPresCoord:cambiarCoordinadorArea:resCambiarAreaCoordinador: ";print_r($resCambiarAreaCoordinador);	
		}			
		else //$resIniTrans['codError'] == '00000'
		{
				foreach ($datosCoordCambiarArea as $campo => $valorCampo) 
				{$datosCoordCambiar[$campo]['valorCampo'] = $valorCampo;
				}
				
			//echo "<br><br>2-1 modeloPresCoord:cambiarCoordinadorArea:datosCoordCambiar: ";print_r($datosCoordCambiar);
			
	  require_once './modelos/modeloPresCoord.php';				 
 	 $reCambiarAreaCoord = actualizarCoordinacionAreaGestion('COORDINAAREAGESTIONAGRUP',$datosCoordCambiar);//probado error ok

		 //echo "<br><br>2-2 modeloPresCoord:cambiarCoordinadorArea:reCambiarAreaCoord: ";print_r($reCambiarAreaCoord);

			if ($reCambiarAreaCoord['codError'] !== '00000')			
   {$resCambiarAreaCoordinador  = $reCambiarAreaCoord;
   }	
			elseif ($reCambiarAreaCoord['numFilas'] <= 0)
			{ $resCambiarAreaCoordinador['codError'] = '80001';
					$resCambiarAreaCoordinador['textoComentarios'] =' No se han encontrado datos que cumplan las condiciones elegidas';
					$resCambiarAreaCoordinador['errorMensaje'] = $resCambiarAreaCoordinador['textoComentarios'];  
			}		
   else //$reCambiarAreaCoord['codError']=='00000
			{	
    $tablasBusqueda = 'AREAGESTION';	
				$camposBuscados = 'NOMAREAGESTION';	//para mostrar en pantalla y en el email al coordinador		

				$cadCondicionesBuscar = " WHERE AREAGESTION.CODAREAGESTION = :codAreaGestionAgrup ";							
		
				$arrBind = array(':codAreaGestionAgrup' => $datosCoordCambiar['CODAREAGESTIONAGRUP']['valorCampo']);  					
									
				require_once "BBDD/MySQL/modeloMySQL.php";//ya está incluida al principio de modelPresCoord.php					
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
								
				$reBuscarAreaNom = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//probado error ok 

				//echo "<br><br>3 modeloPresCoord:cambiarCoordinadorArea:reBuscarAreaNom: ";print_r($reBuscarAreaNom);
		  
				if ($reBuscarAreaNom['codError'] !== '00000')
		  { $resCambiarAreaCoordinador = $reBuscarAreaNom;	
		  }
			 else //$reBuscarAreaNom['codError']=='00000'
			 {
					$resCambiarAreaCoordinador['NOMAREAGESTION'] = $reBuscarAreaNom['resultadoFilas'][0]['NOMAREAGESTION'];		
	 		
					//----------------Inicio COMMIT -------------------------------------------
					$resFinTrans = commitPDO($conexionDB['conexionLink']);
					
					//echo "<br><br>8-1 modeloSocios.php:eliminarDatosSocios:resFinTrans: ";var_dump($resFinTrans);						
					
					if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502'	
					{										
						$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de eliminar socio/a. ';
						$resFinTrans['numFilas'] = 0;	
														
					 $resCambiarAreaCoordinador  = $resFinTrans;	
					//echo "<br><br>5 modeloPresCoord:cambiarCoordinadorArea:resCambiarAreaCoordinador: ";print_r($resCambiarAreaCoordinador);
					}
					//----------------Fin COMMIT ----------------------------------------------
					else // $resFinTrans['codError'] == '00000'
				 { $arrMensaje['textoComentarios'] = "Se ha asignado el área de coordinación territorial <strong>".$resCambiarAreaCoordinador['NOMAREAGESTION'].
					                                    " </strong> al socio/a <strong> ".$datosCoordCambiar['NOM']['valorCampo']." ".$datosCoordCambiar['APE1']['valorCampo'].
																																									" </strong> en la base de datos de	Europa Laica
						                                    <br /><br /><br />Se ha enviado un correo a la dirección personal del coordinador/a <strong> ".$datosCoordCambiar['EMAIL']['valorCampo'].
						                                   "  </strong>para comunicarle la asignación.<br /><br /><br />
 																																								En el email se le indica que tiene que firmar un documento (enviado como archivo adjunto -Compromiso_Proteccion_Datos.pdf-), 
                                         aceptando el cumplimiento de la ley de Protección de Datos en lo que se refiere sus funciones de coordinación en Europa Laica.
                                         <br /><br />
                                         Después lo puede escanear y enviar a Gestión de Soci@s: adminusers@europalaica.org 
																													            (o por correo postal al domicilio legal de Europa Laica)";		
				 }
				}//else $reBuscarAreaNom['codError']=='00000'
		 }//else $reCambiarAreaCoord['codError']=='00000			
			
			//echo "<br><br>6 modeloPresCoord:cambiarCoordinadorArea:datosCoordCambiar: ";print_r($datosCoordCambiar);
			
   //---------------- Inicio tratamiento errores -------------------------------				
			//--- Inicio deshacer transación en las tablas modificadas ---------------
			if ($resCambiarAreaCoordinador['codError'] !== '00000')
			{								
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
										
					if ($resDeshacerTrans['codError'] !== '00000')//será $resDeshacerTrans['codError'] = '70503';
					{ $resCambiarAreaCoordinador['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];				
					}
			}//--- Fin deshacer transación en las tablas modificadas -----------------	
		}//else $resIniTrans['codError'] == '00000'				
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--				
		if ($resCambiarAreaCoordinador['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($resCambiarAreaCoordinador['textoComentarios']) ) 
			{ $resCambiarAreaCoordinador['textoComentarios'] = ". modeloPresCoord.php:cambiarCoordinadorArea(): ".$resCambiarAreaCoordinador['textoComentarios'];}	
			else
			{	$resCambiarAreaCoordinador['textoComentarios'] = ". modeloPresCoord.php:cambiarCoordinadorArea(): ";}
		
				require_once './modelos/modeloErrores.php';
				$resInsertarErrores = insertarError($resCambiarAreaCoordinador);	
				
				if ($resInsertarErrores['codError'] !== '00000')
				{ $resCambiarAreaCoordinador['codError'] = $resInsertarErrores['codError'];
				 	$resCambiarAreaCoordinador['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				}					
		}//if ($resCambiarAreaCoordinador['codError']!=='00000
		//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
		//---------------- Fin tratamiento errores ------------------------------------	
	}//else $conexionDB['codError']=="00000"		
	
 $resCambiarAreaCoordinador['arrMensaje'] = $arrMensaje;	
 
	//echo "<br><br>7 modeloPresCoord:cambiarCoordinadorArea:resCambiarAreaCoordinador: ";print_r($resCambiarAreaCoordinador);	
	
 return 	$resCambiarAreaCoordinador; 	
}
/*------------------------------ Fin cambiarCoordinadorArea -----------------------------------------*/


/*----------------------------- Inicio actualizarCoordinacionAreaGestion -----------------------------
Actualiza un área de gestión a un coordinador en tabla COORDINAAREAGESTIONAGRUP.

LLAMADA:modeloPresCoord.php:cambiarCoordinadorArea()
LLAMA: modeloMySQL.php:actualizarTabla_ParamPosicion()

OBSERVACIONES: PHP 7.3.21.  modifico para incluir PDO
Se graban los errores del sistema en ERRORES
-----------------------------------------------------------------------------------------------------*/
function actualizarCoordinacionAreaGestion($tablaAct,$datosCoordCambiar)
{
	//echo '<br><br>0-1 modeloPresCoord:actualizarCoordinacionAreaGestion:datosCoordCambiar: ';print_r($datosCoordCambiar);

 $resActAreaGestion['nomScript'] ='modeloPresCoord.php';
 $resActAreaGestion['nomFuncion'] ='actualizarCoordinacionAreaGestion';
	$resActAreaGestion['codError'] ='00000';
 $resActAreaGestion['errorMensaje'] ='';
	$arrMensaje['textoCabecera'] ='Cambiar el área de coordinación de un coordinador/a';	

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== '00000')	
	{ $resActAreaGestion = $conexionUsuariosDB;
   //$arrMensaje['textoComentarios'] .="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'	
	{	
		$arrayCondiciones['CODUSER'] = $datosCoordCambiar['CODUSER'];	
		$arrayCondiciones['CODUSER']['operador'] = '=';
		$arrayCondiciones['CODUSER']['opUnir'] = 'AND';	
		$arrayCondiciones['CODAREAGESTIONAGRUP'] = $datosCoordCambiar['codAreaGestionOld'];//area anteriror	
		$arrayCondiciones['CODAREAGESTIONAGRUP']['operador'] = '=';
		$arrayCondiciones['CODAREAGESTIONAGRUP']['opUnir'] = ' ';
		
	 //echo '<br><br>2 modeloPresCoord:actualizarCoordinacionAreaGestion:arrayCondiciones: ';print_r( $arrayCondiciones);
			
		$arrayDatos['CODAREAGESTIONAGRUP'] = $datosCoordCambiar['CODAREAGESTIONAGRUP']['valorCampo'];//nueva area
		$arrayDatos['OBSERVACIONES'] = $datosCoordCambiar['OBSERVACIONES']['valorCampo'];//nueva area	
		$arrayDatos['FECHAASIGNACION'] = date('Y-m-d');
		
		//echo '<br><br>3 modeloPresCoord:actualizarCoordinacionAreaGestion:arrayDatos: ';print_r( $arrayDatos);
		
  require_once "BBDD/MySQL/modeloMySQL.php";
  $resActAreaGestion = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']);//$tablaAct='COORDINAAREAGESTIONAGRUP'				
		//echo '<br><br>4 modeloPresCoord:actualizarCoordinacionAreaGestion:resActAreaGestion: ';print_r($resActAreaGestion);
		
		if ($resActAreaGestion['codError'] !== '00000')
		{ $arrMensaje['textoComentarios'] = "Error del sistema al actualizar rol usuario, vuelva a intentarlo pasado un tiempo ";
		
			require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($resActAreaGestion);			
			
			if ($resInsertarErrores['codError'] !== '00000')
	  {$resActAreaGestion['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'] .="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resActUsuarioRol['codError']!=='00000'
	}	//$conexionUsuariosDB['codError']=='00000'	
	
	$resActAreaGestion['arrMensaje'] = $arrMensaje;	
	
	//echo "<br><br>5 modeloPresCoord:actualizarCoordinacionAreaGestion:resActAreaGestion: ";print_r($resActAreaGestion);	
	
	return $resActAreaGestion;
} 
/*----------------------------- Fin actualizarCoordinacionAreaGestionl ------------------------------*/


/*---------------------------- Inicio buscarMiembroApesEmail -----------------------------------------
Busca los datos personales de del socio en tablas'MIEMBRO,USUARIO' a partir del parámetro $datosApesEmail
que contiene valores para los campos de búsqueda, que pueden ser algunos o varios de los 
siguientes: email o AP1, AP2, NOM. 

Si se busca por email se excluye buscar por ApesNom, y al revés. 

RECIBE: array $datosApesEmail con posibles campos de búsqueda: email o AP1,AP2,NOM 
DEVUELVE: array  $resDatosMiembroApesEmail, con los datos del socio buscado y errores

LLAMADA: cPresidente.php:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),
         asignarTesoreríaRolBuscar()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php
BBDD/MySQL/conexionMySQL.php:conexionDB()
modelos/libs/eliminarAcentos.php:cambiarAcentosEspeciales()
modeloMySQL.php:buscarCadSql()
modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21. No modifico la consulta para incluir para añadir $arrBind, pero 
sí añado $arrBind()=''; para mantener el formato de parámetros con la función 
que incluye PDO:buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind)  
/*--------------------------------------------------------------------------------------------------*/
function buscarMiembroApesEmail($datosApesEmail)
{
	//echo "<br><br>0-1 modeloPresCoord.php:buscarMiembroApesEmail:datosApesEmail: ";print_r($datosApesEmail);

	$resDatosMiembroApesEmail['nomScript'] = "modeloPresCoord.php";	
	$resDatosMiembroApesEmail['nomFuncion'] = "buscarMiembroApesEmail";	
 $resDatosMiembroApesEmail['codError'] = '00000';
 $resDatosMiembroApesEmail['errorMensaje'] = '';	
 $arrMensaje = '';
	 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

 if ($conexionDB['codError'] !== "00000")
	{ $resDatosMiembroApesEmail = $conexionDB;
	}
	else//($conexionDB['codError']=='00000')		
	{
		$tablasBusqueda = 'MIEMBRO,USUARIO';
		$camposBuscados = 'USUARIO.CODUSER,USUARIO.ESTADO,TIPOMIEMBRO,CODPAISDOC,NUMDOCUMENTOMIEMBRO,TIPODOCUMENTOMIEMBRO,APE1,APE2,NOM,SEXO,EMAIL,TELFIJOCASA,TELMOVIL,LOCALIDAD';
	
		if (isset($datosApesEmail['EMAIL']['valorCampo']) && !empty($datosApesEmail['EMAIL']['valorCampo']))
	 { 
	   $cadCondicionesBuscar = " WHERE EMAIL= \"".$datosApesEmail['EMAIL']['valorCampo']."\"";		
				
    //echo '<br><br>1 modeloPresCoord.php:buscarMiembroApesEmail:cadCondicionesBuscar : ';print_r($cadCondicionesBuscar);
		}
		else //!isset($datosApesEmail['EMAIL']['valorCampo']) && !empty($datosApesEmail['EMAIL']['valorCampo']))		
		{
			/*--------------- Inicio buscar por apesNom -------------------------------*/			
		
   require_once './modelos/libs/eliminarAcentos.php';
		 if ( !isset($datosApesEmail['APE1']['valorCampo']) || empty($datosApesEmail['APE1']['valorCampo']))
 	 { $cadenaApe1  = '';	
			  $cadOperador = '';				
     //echo '<br><br>2-1 modeloPresCoord.php:buscarMiembroApesEmail:cadenaApe1 : ';print_r($cadenaApe1 ); 					
			}
			else
			{/*--------------- Inicio cambiar acentos y especiales --------------------*/			
			 $cadApe1 = cambiarAcentosEspeciales($datosApesEmail['APE1']['valorCampo']);				
				
			 $cadenaApe1 = " MIEMBRO.APE1 LIKE '$cadApe1' ";				
				//echo '<br><br>2-2 modeloPresCoord.php:buscarMiembroApesEmail:cadenaApe1 : ';print_r($cadenaApe1 );
				$cadOperador = ' AND ';	
			}	
		 			
			if ( !isset($datosApesEmail['APE2']['valorCampo']) || empty($datosApesEmail['APE2']['valorCampo']) )
		 { $cadenaApe2 = '';	
		  //echo '<br><br>2-3 modeloPresCoord.php:buscarMiembroApesEmail:cadenaApe2: ';print_r($cadenaApe2);														
			}
			else
			{$cadApe2 = cambiarAcentosEspeciales($datosApesEmail['APE2']['valorCampo']);
							
				$cadenaApe2 = $cadOperador." MIEMBRO.APE2 LIKE '$cadApe2' ";				
				//echo '<br><br>2-4 modeloPresCoord.php:buscarMiembroApesEmail:cadenaApe2: ';print_r($cadenaApe2);
				$cadOperador = ' AND ';
			}	
			
			if ( !isset($datosApesEmail['NOM']['valorCampo']) || empty($datosApesEmail['NOM']['valorCampo']))
		 { $cadenaNom = '';			
		   $cadOperador = '';					
    //echo '<br><br>2-5 modeloPresCoord.php:buscarMiembroApesEmail:cadenaNom: ';print_r($cadenaNom);						
			}
			else
			{$cadenaNom = cambiarAcentosEspeciales($datosApesEmail['NOM']['valorCampo']);
		
				$cadenaNom = $cadOperador." MIEMBRO.NOM LIKE '$cadenaNom' ";				
				//echo '<br><br>2-6 modeloPresCoord.php:buscarMiembroApesEmail:cadenaNom: ';print_r($cadenaNom);	
			}		
			/*--------------- Fin cambiar acentos y especiales ------------------------*/
					
   $cadCondicionesBuscar = " WHERE ".$cadenaApe1.$cadenaApe2.$cadenaNom;			
			
			/*--------------- Fin buscar por apesNom ----------------------------------*/
		}//!isset($datosApesEmail['EMAIL']['valorCampo']) && !empty($datosApesEmail['EMAIL']['valorCampo']))		
		
		$cadCondicionesBuscarApesEmail = $cadCondicionesBuscar." AND MIEMBRO.CODUSER=USUARIO.CODUSER";
	
		//echo '<br><br>3-1 modeloPresCoord.php:buscarMiembroApesEmail:cadCondicionesBuscarApesEmail: ';print_r($cadCondicionesBuscarApesEmail);
 	
		$arrBind = array();//para mantener el fomato de la función
	
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscarApesEmail";
	
		//echo '<br><br>3-2 modeloPresCoord.php:buscarMiembroApesEmail:cadSql: ';print_r($cadSql);
			
	 $resDatosMiembroApesEmail = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//probado error OK	
		
		//echo '<br><br>4 modeloPresCoord.php:buscarMiembroApesEmail:resDatosMiembroApesEmail: ';print_r($resDatosMiembroApesEmail);
																													
		if ($resDatosMiembroApesEmail['codError'] !== '00000')
		{
			$resDatosMiembroApesEmail['textoComentarios'] .=	"modeloPresCoord.php:buscarMiembroApesEmail(): ".$resDatosMiembroApesEmail['nomScript'].' '.$resDatosMiembroApesEmail['nomFuncion'];	
			require_once './modelos/modeloErrores.php';
			insertarError($resDatosMiembroApesEmail);		
		}	
		else//$resDatosMiembroApesEmail['codError']=='00000')
		{
			if ($resDatosMiembroApesEmail['numFilas'] > 0)
			{
				foreach ($resDatosMiembroApesEmail['resultadoFilas'][0] as $indice => $contenido)                         
				{				
						$aux['resultadoFilas']['datosFormSocio'][$indice]['valorCampo'] = $contenido;
				}
			 
			 $resDatosMiembroApesEmail['resultadoFilas'] = $aux['resultadoFilas'];
			}	
	 } 	
	}//($conexionDB['codError']=='00000')		
	
	//echo '<br><br>5 modeloPresCoord.php:buscarMiembroApesEmail:resDatosMiembroApesEmail: ';print_r($resDatosMiembroApesEmail);
	
	return $resDatosMiembroApesEmail;
}
/*--------------------------- Fin buscarMiembroApesEmail -------------------------------------------*/

/*---------------------------- Inicio  buscarDatosCoordinadores -------------------------------------
Busca los Datos de los Coordinadores, para mostrar la lista de los coordinadores.

Para la búsqueda utiliza las siguientes tablas:
MIEMBRO,USUARIOTIENEROL,COORDINAAREAGESTIONAGRUP,AREAGESTION, AREAGESTIONAGRUPACIONESCOORD,AGRUPACIONTERRITORIAL


RECIBE: nada
DEVUELVE: Un array con todos los datos de los coordinadores, y información de errores

LLAMADO: cPresidente:mostrarListaCoordinadores()
LLAMA:  modeloMySQL.php:buscarCadSql() y configMySQL.php:conexionDB()
modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21. No modifico la consulta para incluir PDO no es necesario $arrBind
---------------------------------------------------------------------------------------------------*/
function  buscarDatosCoordinadores()
{
	//echo "<br><br>0-1 modeloPresCoord.php:buscarDatosCoordinadores";

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== '00000')	
	{ $resBuscarDatosCoordinadores = $conexionDB;
	}
	else	//$conexionDB['codError'] =='00000'
	{								
			$cadSelectBuscar = "SELECT /*AGRUPACIONTERRITORIAL.CODAGRUPACION,AGRUPACIONTERRITORIAL.EMAILCOORD,AGRUPACIONTERRITORIAL.NOMAGRUPACION,*/
																									AREAGESTION.CODAREAGESTION,AREAGESTION.NOMAREAGESTION,AREAGESTION.EMAIL as emailAreaGestion,
																									MIEMBRO.APE1,MIEMBRO.APE2,MIEMBRO.NOM, MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,
																									MIEMBRO.NOMPROVINCIA,UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD, MIEMBRO.CP,MIEMBRO.DIRECCION
																										
																									FROM MIEMBRO,USUARIOTIENEROL,COORDINAAREAGESTIONAGRUP,AREAGESTION, AREAGESTIONAGRUPACIONESCOORD,AGRUPACIONTERRITORIAL

																									WHERE MIEMBRO.CODUSER = USUARIOTIENEROL.CODUSER
																									AND USUARIOTIENEROL.CODUSER = COORDINAAREAGESTIONAGRUP.CODUSER
																									AND COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP=AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP
																									AND COORDINAAREAGESTIONAGRUP.CODAREAGESTIONAGRUP=AREAGESTION.CODAREAGESTION
																									AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION
																									AND USUARIOTIENEROL.CODROL ='6'
																									/*GROUP BY MIEMBRO.APE1,MIEMBRO.APE2,MIEMBRO.NOM bien */
																									GROUP BY MIEMBRO.EMAIL /* bien */
																									ORDER BY AREAGESTION.CODAREAGESTION,AGRUPACIONTERRITORIAL.NOMAGRUPACION,MIEMBRO.APE1,MIEMBRO.APE2,MIEMBRO.NOM"; 																							
																																	
			//echo "<br><br>1 modeloPresCoord.php:buscarDatosCoordinadores:cadSelectBuscar: ";print_r($cadSelectBuscar);						
			
   require_once "BBDD/MySQL/modeloMySQL.php";//ya está incluida al principio de modelPresCoord.php				
			$resBuscar = buscarCadSql($cadSelectBuscar,$conexionDB['conexionLink']);	//probado errores ok
			
   //echo "<br><br>2 modeloPresCoord.php:buscarDatosCoordinadores:resBuscar: ";print_r($resBuscar);																
			
			if ($resBuscar['codError'] !== '00000')
			{$resBuscarDatosCoordinadores = $resBuscar;
			
			 $resBuscarDatosCoordinadores['arrMensaje']['textoCabecera']='Lista de coordinadores/a';
				$resBuscarDatosCoordinadores['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo";
			 		
				require_once './modelos/modeloErrores.php';
				insertarError($resBuscarDatosCoordinadores);		
				
				//echo "<br><br>3 modeloPresCoord.php:buscarDatosCoordinadores:resBuscarDatosCoordinadores: ";print_r($resBuscarDatosCoordinadores);			
			}	
			else
			{
				$resBuscarDatosCoordinadores = $resBuscar;
			 //echo "<br><br>4 modeloPresCoord.php:buscarDatosCoordinadores:resBuscarDatosCoordinadores: ";print_r($resBuscarDatosCoordinadores);			
   }
	}//else	//$conexionDB['codError'] =='00000'
	
 return $resBuscarDatosCoordinadores;
}		
/*---------------------------- Fin  buscarDatosCoordinadores ---------------------------------------*/	  

//===== FIN GESTIÓN DE ASIGNAR-MODIFICAR-ELIMINAR- AREA DE COORDINACIÓN A COORDINADORES =============


/*---------------------------- Inicio  buscarDatosGestoresRoles -------------------------------------
Busca los datos personales de los Gestores con un determinado rol.

Para la búsqueda utiliza las siguientes tablas: MIEMBRO,USUARIOTIENEROL y condición de CODROL

RECIBE: $codRol es el campo CODROL de la tabla USUARIOTIENEROL
DEVUELVE: Un array con todos los datos de los gestores, y información de errores

LLAMADO: cPresidente:mostrarListaPresidenciaRol(), mostrarListaTesoreriaRol
LLAMA:  modeloMySQL.php:buscarCadSql() y configMySQL.php:conexionDB()
modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21. incluye $arrBind
---------------------------------------------------------------------------------------------------*/
function  buscarDatosGestoresRoles($codRol)
{
	//echo "<br><br>0-1 modeloPresCoord.php:buscarDatosGestoresRoles:codRol: ";print_r($codRol);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== '00000')	
	{ $resBuscarDatosGestoresRoles = $conexionDB;
	}
	else	//$conexionDB['codError'] =='00000'
	{																												
   $cadSelectBuscar = "SELECT  MIEMBRO.APE1,MIEMBRO.APE2,MIEMBRO.NOM, MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,
				                        MIEMBRO.NOMPROVINCIA,UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD,MIEMBRO.CP,MIEMBRO.DIRECCION

				                        FROM MIEMBRO,USUARIOTIENEROL
																												
																												WHERE MIEMBRO.CODUSER = USUARIOTIENEROL.CODUSER				
																												AND USUARIOTIENEROL.CODROL = :codRol
																												ORDER BY MIEMBRO.APE1,MIEMBRO.APE2,MIEMBRO.NOM ";
  																												
								
	  //echo "<br><br>1 modeloPresCoord.php:buscarDatosGestoresRoles:cadSelectBuscar: ";print_r($cadSelectBuscar);		
			
	  $arrBind = array(':codRol' => $codRol);  		
	
   require_once "BBDD/MySQL/modeloMySQL.php";//ya está incluida al principio de modelPresCoord.php				
			$resBuscar = buscarCadSql($cadSelectBuscar,$conexionDB['conexionLink'],$arrBind);	//probado errores ok
			
   //echo "<br><br>2 modeloPresCoord.php:buscarDatosGestoresRoles:resBuscar: ";print_r($resBuscar);																
			
			if ($resBuscar['codError'] !== '00000')
			{$resBuscarDatosGestoresRoles = $resBuscar;
			
			 $resBuscarDatosGestoresRoles['arrMensaje']['textoCabecera'] = 'Lista de gestores/a';
				$resBuscarDatosGestoresRoles['arrMensaje']['textoComentarios'] .= ".Error del sistema, vuelva a intentarlo pasado un tiempo";
			 		
				require_once './modelos/modeloErrores.php';
				insertarError($resBuscarDatosGestoresRoles);		
				
				//echo "<br><br>3 modeloPresCoord.php:buscarDatosGestoresRoles:resBuscarDatosGestoresRoles: ";print_r($resBuscarDatosGestoresRoles);			
			}	
			else
			{
				$resBuscarDatosGestoresRoles = $resBuscar;
			 //echo "<br><br>4 modeloPresCoord.php:buscarDatosGestoresRoles:resBuscarDatosGestoresRoles: ";print_r($resBuscarDatosGestoresRoles);			
   }
	}//else	//$conexionDB['codError'] =='00000'
	
 return $resBuscarDatosGestoresRoles;
}		
/*---------------------------- Fin  buscarDatosGestoresRoles ---------------------------------------*/	  

//===== FIN PERMISOS ROLES:  ASIGNAR-MODIFICAR-ELIMINAR-MOSTRAR ROLES ==============================



//=============== INICIO ESTADOS ALTAS SOCIOS: PENDIENTES CONFIRMAR Y OTROS  ======================

/*----------------- Inicio cadBuscarEstadoConfirmacionAltasGestor ----------------------------------
Forma la cadena select sql para buscar según el estado de alta de los socios y según las diferentes 
situaciones: alta por ellos mismos por ellos mismos, o alta por gestores (Presidente, secretaria, 
o coordinadores), o procedentes de la importación de Excel de datos ya existentes al iniciar esta 
aplicación, o de alta por Admin al principio().

Las consulta se realizarán sobre las tablas:
SOCIOSCONFIRMAR,USUARIO,CONFIRMAREMAILALTAGESTOR,USUARIO,MIEMBRO	y en algunos 
casos se forman	UNION de SELECT.
						
RECIBE: desde vistas/presidente/formMostrarEstadoConfirSocios.php: $cadApe1,$cadApe2,
y $estadoUsuario:
que puede tomar los siguientes valores que vienen del formulario:
"PENDIENTE-CONFIRMAR"=>"Pendientes confirmar alta por socio/a",							                                
"alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",																																							
"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",																																							
"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",																																							
"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",																																							
"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a" 
(no siempre son valores de la tabla USUARIO.ESTADO) 		
											
DEVUELVE: una cadena "$cadBuscarEstadoConfirmacionAltasGestor" que contendrá una SELECT, que será 
diferente según los valores de los parámetros $cadApe1,$cadApe2,$estadoUsuario: en algún caso 
estará formada por la UNION de select.

LLAMADA: cPresidente:mostrarEstadoConfirmacionSocios(): 
en require_once './controladores/libs/cPresidenteMostrarEstadoConfSocios.php'					

OBSERVACIONES: PHP 7.3.21 probada ok
No incluye $arrBindParametro, creo que puede ser algo complejo con las UNION select en PDO. 

Al ser ejecutada solo por gestores con rol Presidencia, poco riesgo de injection 
Acaso fuese más adecuado sustituir las UNION por consultas separadas y despuás tratar los resultados
---------------------------------------------------------------------------------------------------*/
function cadBuscarEstadoConfirmacionAltasGestor($cadApe1,$cadApe2,$estadoUsuario)
{
	require_once './modelos/libs/eliminarAcentos.php';				
	
 //echo '<br>0-1 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:estadoUsuario: ';print_r($estadoUsuario);
 //echo '<br>0-2 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadApe1:';print_r($cadApe1);
 //echo '<br0->3 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadApe2:';print_r($cadApe2);

	$condicionMiembroApe1 = '';	
	$condicionSociosconfirmarApe1 = '';
	$condicionMiembroApe2 = '';	
	$condicionSociosconfirmarApe2 = '';
	$tipoMiembro ='socio';//se deja por si se quiere utilizar para otros tipos de socios o miembros
	
 //- Tablas y campos para que están aún sin confirmar por ellos mismos y los datos se encuentran en SOCIOSCONFIRMAR --
  $tablasBusquedaConfAltaSocios = 'SOCIOSCONFIRMAR,USUARIO';		
		
		$camposBuscadosConfAltaSocios = "USUARIO.CODUSER,USUARIO.ESTADO,SOCIOSCONFIRMAR.EMAIL, SOCIOSCONFIRMAR.SEXO,SOCIOSCONFIRMAR.NOM,
																																			SOCIOSCONFIRMAR.APE1,SOCIOSCONFIRMAR.APE2,
																																			UPPER(CONCAT(SOCIOSCONFIRMAR.APE1,' ',IFNULL(SOCIOSCONFIRMAR.APE2,''),', ',SOCIOSCONFIRMAR.NOM)) as apeNom,
																																			SOCIOSCONFIRMAR.TELFIJOCASA,SOCIOSCONFIRMAR.TELMOVIL,
																																			SOCIOSCONFIRMAR.DIRECCION,SOCIOSCONFIRMAR.CP,SOCIOSCONFIRMAR.LOCALIDAD,
																																			SOCIOSCONFIRMAR.CODPROV,SOCIOSCONFIRMAR.CODPAISDOM,
																																			SOCIOSCONFIRMAR.FECHAREGISTRO,SOCIOSCONFIRMAR.FECHAENVIOEMAILULTIMO,SOCIOSCONFIRMAR.NUMENVIOS";			
		
		//- Tablas y campos para que ya están confirmado por ellos mismos y los datos se encuentran en MIEMBRO y SOCIOSCONFIRMAR --
  $tablasBusquedaConfAltaSociosSiConfirmado = 'SOCIOSCONFIRMAR,USUARIO,MIEMBRO';		
		
		$camposBuscadosConfAltaSociosSiConfirmado = "USUARIO.CODUSER,USUARIO.ESTADO,MIEMBRO.EMAIL, MIEMBRO.SEXO,MIEMBRO.NOM,MIEMBRO.APE1,MIEMBRO.APE2,
																																															UPPER(CONCAT(MIEMBRO.APE1,' ',IFNULL(MIEMBRO.APE2,''),', ',MIEMBRO.NOM)) as apeNom,
																																															MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,
																																															MIEMBRO.DIRECCION,MIEMBRO.CP,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,																								
																																															SOCIOSCONFIRMAR.FECHAREGISTRO,SOCIOSCONFIRMAR.FECHAENVIOEMAILULTIMO,SOCIOSCONFIRMAR.NUMENVIOS";																						
			
		//- Tablas y campos para altas por gestores: los datos se encuentran en MIEMBRO y CONFIRMAREMAILALTAGESTOR --	 		
  $tablasBusquedaConfAltaGestor = 'CONFIRMAREMAILALTAGESTOR,USUARIO,MIEMBRO';		
		
		$camposBuscadosConfAltaGestor = "USUARIO.CODUSER,USUARIO.ESTADO,MIEMBRO.EMAIL, MIEMBRO.SEXO,MIEMBRO.NOM,MIEMBRO.APE1,MIEMBRO.APE2,
																																			UPPER(CONCAT(MIEMBRO.APE1,' ',IFNULL(MIEMBRO.APE2,''),', ',MIEMBRO.NOM)) as apeNom,
																																			MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,
																																			MIEMBRO.DIRECCION,MIEMBRO.CP,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,																								
																																			'--',CONFIRMAREMAILALTAGESTOR.FECHAENVIOEMAILULTIMO, CONFIRMAREMAILALTAGESTOR.NUMENVIOS";			
																
//-------------------------------------------------------------------------------------	

	//---- Inicio buscar por APE1, y/o APE2 en todas las tablas posibles MIEMBRO, SOCIOSCONFIRMAR --- 
	if ((isset($cadApe1) && !empty($cadApe1)) || (isset($cadApe2) && !empty($cadApe2)))//si se ha introducido un Ape
	{
	 if ( isset($cadApe1) && !empty($cadApe1))
		{$cadApe1 = cambiarAcentosEspeciales($cadApe1);
	
		 $condicionMiembroApe1 = " AND MIEMBRO.APE1 LIKE '$cadApe1' ";
			$condicionSociosconfirmarApe1 = " AND SOCIOSCONFIRMAR.APE1 LIKE '$cadApe1' ";
		}	
		if ( isset($cadApe2) && !empty($cadApe2))
		{$cadApe2 = cambiarAcentosEspeciales($cadApe2);
	
		 $condicionMiembroApe2 = " AND MIEMBRO.APE2 LIKE '$cadApe2' ";
			$condicionSociosconfirmarApe2 = " AND SOCIOSCONFIRMAR.APE2 LIKE '$cadApe2' ";			
		} //hay $cadApe1, $cadApe2
		
		//la siguiente condición acaso no sea necesaria aquí porque no lo encontraría
	 $condicionEstadoUsuario= " AND USUARIO.ESTADO !='baja' AND USUARIO.ESTADO !='ANULADA-SOCITUD-REGISTRO' ";	

  //- para que están aún sin confirmar por ellos mismos y los datos se encuentran en SOCIOSCONFIRMAR --		
		$cadCondicionesConfAltaSocios = " WHERE SOCIOSCONFIRMAR.CODUSER = USUARIO.CODUSER".																														
			     																						       $condicionSociosconfirmarApe1.$condicionSociosconfirmarApe2.$condicionEstadoUsuario;																									
																													
		$cadBuscarConfAltaSocios = "SELECT ".$camposBuscadosConfAltaSocios." FROM ".$tablasBusquedaConfAltaSocios.$cadCondicionesConfAltaSocios;			
																														
		//- para que ya están confirmado por ellos mismos y los datos se encuentran en MIEMBRO y SOCIOSCONFIRMAR --
	 $cadCondicionesConfAltaSociosSiConfirmado = " WHERE SOCIOSCONFIRMAR.CODUSER = USUARIO.CODUSER AND USUARIO.CODUSER = MIEMBRO.CODUSER	".																														
																									                       $condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario; 																									
																													
		$cadBuscarConfAltaSociosSiConfirmado = "SELECT ".$camposBuscadosConfAltaSociosSiConfirmado." FROM ".$tablasBusquedaConfAltaSociosSiConfirmado.$cadCondicionesConfAltaSociosSiConfirmado;			
																							
		//- Tablas y campos para altas por gestores: los datos se encuentran en MIEMBRO y CONFIRMAREMAILALTAGESTOR --	 		
		$cadCondicionesConfAltaGestor = " WHERE CONFIRMAREMAILALTAGESTOR.CODUSER = USUARIO.CODUSER
																																					AND USUARIO.CODUSER = MIEMBRO.CODUSER																																															
																																					AND MIEMBRO.TIPOMIEMBRO = '$tipoMiembro'".																														
																																					$condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario; 
																																		
  $cadBuscarConfAltaGestor = "SELECT ".$camposBuscadosConfAltaGestor." FROM ".$tablasBusquedaConfAltaGestor.$cadCondicionesConfAltaGestor;																															
  // union de las tres consultas
  $cadBuscarEstadoConfirmacionAltasGestor = "(".$cadBuscarConfAltaSocios.")".
                                            " UNION ".
																																												"(".$cadBuscarConfAltaSociosSiConfirmado.")".		
		                                          " UNION ".
																																												"(".$cadBuscarConfAltaGestor.")".																																												
																																												" ORDER BY apeNom";	
																																												
  //echo '<br><br>1 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);																																												
																																												
	}//if ((isset($cadApe1) && !empty($cadApe1)) || (isset($cadApe2) && !empty($cadApe2)) )
	//---- Fin buscar por APE1, y/o APE2 en todas las tablas posibles MIEMBRO, SOCIOSCONFIRMAR --- 
	
 else // Buscar por $estadoUsuario no hay $cadApe1, $cadApe2 NO SE BUSCA POR APE1 O APE2
	{
		//echo '<br>2-1 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:estadoUsuario: ';print_r($estadoUsuario);
	 //if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario =='%')
			
		//---- Inicio de Todos los pendientes de alguna confirmación pero excluimos bajas y anulados
	 if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario =='pendiente_confirmar_algo')//Todos los pendientes de algo pero excluimos bajas y anulados
	 {		
		 //$condicionEstadoUsuario = " AND USUARIO.ESTADO !='baja' AND USUARIO.ESTADO !='ANULADA-SOCITUD-REGISTRO' ";	
			$condicionEstadoUsuario = " AND USUARIO.ESTADO = 'PENDIENTE-CONFIRMAR' ";	

																																									
		 $cadCondicionesConfAltaSocios = " WHERE SOCIOSCONFIRMAR.CODUSER = USUARIO.CODUSER".																														
																																						$condicionSociosconfirmarApe1.$condicionSociosconfirmarApe2.$condicionEstadoUsuario; 																									
																													
		 $cadBuscarConfAltaSocios = "SELECT ".$camposBuscadosConfAltaSocios." FROM ".$tablasBusquedaConfAltaSocios.$cadCondicionesConfAltaSocios;		
			
			$condicionEstadoUsuario = " AND (USUARIO.ESTADO = 'alta-sin-password-gestor' 
			                                 OR USUARIO.ESTADO = 'alta-sin-password-excel') ";																										
																												
		 $cadCondicionesConfAltaGestor = " WHERE CONFIRMAREMAILALTAGESTOR.CODUSER = USUARIO.CODUSER
																																						AND USUARIO.CODUSER = MIEMBRO.CODUSER																																															
																																						AND MIEMBRO.TIPOMIEMBRO = '$tipoMiembro'".																														
																																						$condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario; 
																												
   $cadBuscarConfAltaGestor = "SELECT ".$camposBuscadosConfAltaGestor." FROM ".$tablasBusquedaConfAltaGestor.$cadCondicionesConfAltaGestor;																															

   $cadBuscarEstadoConfirmacionAltasGestor = "(".$cadBuscarConfAltaSocios.")".
																																													" UNION ".
																																												 "(".$cadBuscarConfAltaGestor.")  ORDER BY apeNom";		
																																														
   //echo '<br><br>2-2 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);	
																																														
	 }//if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario =='pendiente_confirmar_algo')	
		//---- Fin de Todos los pendientes de alguna confirmación pero excluimos bajas y anulados
																																					
		else//DISTINTO DE if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario=='%')//distinto de todos
	 {
		 //--- Inicio  PENDIENTE-CONFIRMAR por los socios que aún no confirmado por ellos mismos, ni tampoco un gestor 
		 if ($estadoUsuario == 'PENDIENTE-CONFIRMAR')//Pendientes de confirmar por socio
		 {
				$condicionEstadoUsuario = " AND USUARIO.ESTADO ='$estadoUsuario' ";
		
				$cadCondicionesConfAltaSocios = " WHERE SOCIOSCONFIRMAR.CODUSER = USUARIO.CODUSER".																														
																									             $condicionSociosconfirmarApe1.$condicionSociosconfirmarApe2.$condicionEstadoUsuario; 																									
				 								
				$cadBuscarEstadoConfirmacionAltasGestor = "SELECT ".$camposBuscadosConfAltaSocios." FROM ".$tablasBusquedaConfAltaSocios.$cadCondicionesConfAltaSocios." ORDER BY apeNom";
    
				//echo '<br><br>3-1 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);																																											
			}
			//--- Fin  PENDIENTE-CONFIRMAR por los socios que aún no confirmado por ellos mismos, ni tampoco un gestor 
			
			//--- Inicio (alta por gestor) y que "está pendiente de confirma email por los socios" (incluye a los que se socios que se registraron por ellos mismos pero un gestor confirmó el alta del socio, normalmente debido a problemas encontrados por el socio)						
			elseif ($estadoUsuario =='alta-sin-password-gestor'|| $estadoUsuario =='alta-sin-password-excel')//alta por gestor: pendiente de confirmar email 
			{
				$tipoMiembro ='socio';//se deja por si se quiere utilizar para otros tipos de socios
			 $condicionEstadoUsuario = " AND USUARIO.ESTADO ='$estadoUsuario' "; 

				$cadCondicionesConfAltaGestor = " WHERE CONFIRMAREMAILALTAGESTOR.CODUSER = USUARIO.CODUSER
																																						AND USUARIO.CODUSER = MIEMBRO.CODUSER																																															
																																						AND MIEMBRO.TIPOMIEMBRO = '$tipoMiembro'".																														
																																						$condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario." ORDER BY apeNom";		
																																		
    $cadBuscarEstadoConfirmacionAltasGestor = "SELECT ".$camposBuscadosConfAltaGestor." FROM ".$tablasBusquedaConfAltaGestor.$cadCondicionesConfAltaGestor;
    
				//echo '<br>3-2 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);	
			}
			//--- Fin (alta por gestor) y que "está pendiente de confirma email por los socios" (incluye a los que se socios que se registraron por ellos mismos pero un gestor confirmó el alta del socio, normalmente debido a problemas encontrados por el socio)						
			
			//--- Inicio alta por gestor y ya está confirmado email por los socios (incluye a los que se socios que se registraron por ellos mismos pero un gestor confirmó el alta del socio, normalmente debido a problemas encontrados por el socio)						
			elseif ($estadoUsuario =='alta_por_gestor_confirmada')//alta por gestor y confirmado email por los socios (incluye a los que se socios que se registraron por ellos mismos pero un gestor confirmó el alta del socio, normalmente debido a problemas encontrados por el socio)
			{
				$tipoMiembro ='socio';//se deja por si se quiere utilizar para otros tipos de socios
			 $condicionEstadoUsuario = " AND USUARIO.ESTADO ='alta' "; 

				$cadCondicionesConfAltaGestor = " WHERE CONFIRMAREMAILALTAGESTOR.CODUSER = USUARIO.CODUSER
																																						AND USUARIO.CODUSER = MIEMBRO.CODUSER																																															
																																						AND MIEMBRO.TIPOMIEMBRO = '$tipoMiembro'".																														
																																						$condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario." ORDER BY apeNom";		
																																		
    $cadBuscarEstadoConfirmacionAltasGestor = "SELECT ".$camposBuscadosConfAltaGestor." FROM ".$tablasBusquedaConfAltaGestor.$cadCondicionesConfAltaGestor;

    //echo '<br><br>3-3 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);								
			}
			//--- Fin alta por gestor y ya está confirmado email por los socios
			
			//--- Inicio alta confirmada por los socios que se han dado de alta y confirmado por ellos mismos 
			elseif ($estadoUsuario =='alta_por_socio_confirmada')//alta confirmada por los socios que se han dado de alta y confirmado por ellos mismos )
			{
				$tipoMiembro ='socio';//se deja por si se quiere utilizar para otros tipos de socios
			 $condicionEstadoUsuario = " AND USUARIO.ESTADO ='alta' "; 

				$cadCondicionesConfAltaSocios = " WHERE SOCIOSCONFIRMAR.CODUSER = USUARIO.CODUSER
																																						AND USUARIO.CODUSER = MIEMBRO.CODUSER																																															
																																						AND MIEMBRO.TIPOMIEMBRO = '$tipoMiembro'".																														
																																						$condicionMiembroApe1.$condicionMiembroApe2.$condicionEstadoUsuario." ORDER BY apeNom";		
																																		
    $cadBuscarEstadoConfirmacionAltasGestor = "SELECT ".$camposBuscadosConfAltaSociosSiConfirmado." FROM ".$tablasBusquedaConfAltaSociosSiConfirmado.$cadCondicionesConfAltaSocios;

				//echo '<br>3-4 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);				
			}
			//--- Fin alta confirmada por los socios que se han dado de alta y confirmado por ellos mismos 			
		 //else 	($estadoUsuario =='baja' || $estadoUsuario =='ANULADA-SOCITUD-REGISTRO')	//no se muestra																										
	 }//else DISTINTO DE if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario=='%')
	}	// no hay $cadApe1, $cadApe2				
	
	//echo '<br><br>5 modeloPresCoord:cadBuscarEstadoConfirmacionAltasGestor: ';print_r($cadBuscarEstadoConfirmacionAltasGestor);
	
	return $cadBuscarEstadoConfirmacionAltasGestor;
}
/*---------------------- Fin cadBuscarEstadoConfirmacionAltasGestor -------------------------------*/

/*----------------- Inicio altaSocioPendienteConfirmadaPorGestor -----------------------------------
En esta función se confirma el alta los socios (aún "PENDIENTE-CONFIRMACION" su alta por el mismo),
por parte de un gestor, a partir de los datos de la solicitud de alta por el socio, a partir de 
la tabla "SOCIOSCONFIRMAR".
Se elimina físicamente de SOCIOSCONFIRMAR para que no salga también duplicado en mostrar pendientes,
y otras posibles búsquedas y en la	información para estadísticas, pues ya estarán los datos
en CONFIRMAREMAILALTAGESTOR y en MIEMBRO, y otras tablas. 
También se inserta una fila en CONFIRMAREMAILALTAGESTOR, por si se quiere comunicar al socio,
con más envíos de mensajes, que ha sido confirmada su alta por un gestor y se un lin con datos para
que pueda elegir una nueva contraseña para entrar (lo mas probable es que se le haya olvidado)
Se graban los errores del sistema en ERRORES 
								
RECIBE: $codUser (viene no encriptado)
DEVUELVE: $resAltaSocios con códigos de errores de operaciones y los comentarios para ver en pantalla

LLAMADA: cPresidente.php: confirmarAltaSocioPendientePorGestor()
Tesorero también tiene habilitada acceso a esta función propia de cPresidente.php
         
LLAMA:  modeloSocios.php:buscarDatosSocioConfirmar(),buscarCuotasAnioEL(),
        "insertarSocio()
								insertarCuotaAnioSocio(),eliminarSocioConfirmar(),
							 modeloUsuarios.php:actualizUsuario(),insertarUsuarioRol(),insertarMiembro() 
								modeloPresCoord.php.buscarDatosMiembro()
								
OBSERVACIONES: PHP 7.3.21. No es necesario instrucciones PDO, lo incluyen internamente algunas 
de las que aquí son llamadas.

Como los socios que se dan de alta por ellos mismos Sí tienen email, no suben el archivo de 
firma de privacidad. Por eso añado para MIEMBRO:
$datosMiembro['ARCHIVOFIRMAPD']['valorCampo'] = NULL;		
$datosMiembro['PATH_ARCHIVO_FIRMAS']['valorCampo'] = NULL;
              
Primera parte parecida a modelosSocios.php:altaSociosConfirmada(), se diferencia 
en función eliminarSocioConfirmar() e insertarUnaFila('CONFIRMAREMAILALTAGESTOR'...,)
-------------------------------------------------------------------------------------------*/
function altaSocioPendienteConfirmadaPorGestor($codUser)
{ 
 //echo "<br><br>0-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:codUser: "; print_r($codUser);
	
 $resInsertar['nomScript'] = " modeloPresCoord.php";	
	$resInsertar['nomFuncion'] = "altaSocioPendienteConfirmadaPorGestor";
	$resInsertar['codError'] = '00000';
	$resInsertar['errorMensaje'] = '';
	$arrMensaje['textoCabecera'] = '';	
 		
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== "00000")
	{ $resInsertar = $conexionDB;	  
	}	
 else //$conexionDB['codError'] =="00000"
	{
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor: ";var_dump($resIniTrans);	echo "<br>";
		//echo "<br><br>1-2 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:conexionsDB: ";var_dump($conexionDB);echo "<br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{	$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;
							
				$resInsertar = $resIniTrans;				
				//echo "<br><br>1-3 modeloSocios.php:altaSociosConfirmada:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		}			
		else //$resIniTrans
		{ $resBuscarDatosSocioConfirmar = buscarDatosSocioConfirmar($codUser);//en modeloSocios.php
    
				//echo "<br><br>2-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resBuscarDatosSocioConfirmar: "; print_r($resBuscarDatosSocioConfirmar);		
				//-- Inicio actualizar tabla USUARIO:  PENDIENTE-CONFIRMACIO-->'alta-sin-password-gestor'
			 if ($resBuscarDatosSocioConfirmar['codError'] !== '00000')			
		  {$resInsertar = $resBuscarDatosSocioConfirmar;
		  }
		  else //($resDatosSocioConfirmar['codError']=='00000')
		  {$datosSocioConfirmar =	$resBuscarDatosSocioConfirmar['resultadoFilas'][0];
				
					//$datosUsuario['ESTADO']['valorCampo'] = 'alta';//problema con email enviado por socio "dirá" ya esta confirmado y no se podrán reenviar email
 				$datosUsuario['ESTADO']['valorCampo'] = 'alta-sin-password-gestor';//el socio estará dado de alta pero bloqueado su entrada hasta que confirme email, se pueden reenviar emails	
					$datosUsuario['OBSERVACIONES']['valorCampo'] = 'Alta inciada por usuario y finalizada por gestor.';

					$resActualizarUsuario = actualizUsuario('USUARIO',$codUser,$datosUsuario,$conexionDB['conexionLink']);//en modeloUsuarios.php          			
		
		   //echo "<br><br>2-2 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resActualizarUsuario:" ; print_r($resActualizarUsuario);
				 //-- Fin actualizar en tabla USUARIO:  PENDIENTE-CONFIRMACION -->'alta-sin-password-gestor'
					
     //------------  Inicio insertarUsuarioRol =socio --------------------------				
		   if ($resActualizarUsuario['codError'] !== '00000')			
		   {$resInsertar = $resActualizarUsuario;
		   }
					elseif ($resActualizarUsuario['numFilas'] <= 0)
					{ $resInsertar['codError'] = '80001';
							$resInsertar['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
					}						
		   else //$resActualizarUsuario['codError'] == '00000')
		   {$datosRolUsuario['CODUSER']['valorCampo'] =	$codUser;//devuelve siguiente a máx 				
					 $datosRolUsuario['CODROL']['valorCampo'] = '1';	//rol socio			
						
				  $resInsertarUsuarioRol = insertarUsuarioRol($datosRolUsuario);//todos se dan de alta con rol socio codigo '1', en modeloUsuarios.php   
			   
						//echo "<br><br>2-3 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:insertarUsuarioRol: ";print_r($resInsertarUsuarioRol);   
				  //------------  Fin insertarUsuarioRol =socio ----------------------------
						
		    if ($resInsertarUsuarioRol['codError']!=='00000')			
		    {$resInsertar = $resInsertarUsuarioRol;
		    }
			   else //$resInsertarUsuarioRol['codError']=='00000'
		    {//---------------Inicio insertarMiembro en tabla MIEMBRO ----------------
						
							//------------  prepara para insertar en tabla MIEMBRO ------------------
						 $datosMiembro['CODUSER']['valorCampo'] = $codUser;
						 $datosMiembro['TIPOMIEMBRO']['valorCampo'] =	"socio";					 
	      $datosMiembro['CODPAISDOC']['valorCampo'] = $datosSocioConfirmar['CODPAISDOC'];
							$datosMiembro['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $datosSocioConfirmar['TIPODOCUMENTOMIEMBRO'];
							$datosMiembro['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $datosSocioConfirmar['NUMDOCUMENTOMIEMBRO'];
							$datosMiembro['APE1']['valorCampo'] = $datosSocioConfirmar['APE1'];
							$datosMiembro['APE2']['valorCampo'] = $datosSocioConfirmar['APE2'];
							$datosMiembro['NOM']['valorCampo'] = $datosSocioConfirmar['NOM'];
							$datosMiembro['SEXO']['valorCampo'] = $datosSocioConfirmar['SEXO'];
							$datosMiembro['FECHANAC']['valorCampo'] = $datosSocioConfirmar['FECHANAC'];
							$datosMiembro['TELFIJOCASA']['valorCampo'] = $datosSocioConfirmar['TELFIJOCASA'];
							$datosMiembro['TELFIJOTRABAJO']['valorCampo'] = $datosSocioConfirmar['TELFIJOTRABAJO'];							
							$datosMiembro['TELMOVIL']['valorCampo'] = $datosSocioConfirmar['TELMOVIL'];
							$datosMiembro['PROFESION']['valorCampo'] = $datosSocioConfirmar['PROFESION'];
							$datosMiembro['ESTUDIOS']['valorCampo'] = $datosSocioConfirmar['ESTUDIOS'];
							$datosMiembro['EMAIL']['valorCampo'] = $datosSocioConfirmar['EMAIL'];
							$datosMiembro['EMAILERROR']['valorCampo'] = 'NO';
							$datosMiembro['INFORMACIONEMAIL']['valorCampo'] = $datosSocioConfirmar['INFORMACIONEMAIL'];
							$datosMiembro['INFORMACIONCARTAS']['valorCampo'] = $datosSocioConfirmar['INFORMACIONCARTAS'];
							$datosMiembro['COLABORA']['valorCampo'] = $datosSocioConfirmar['COLABORA'];
							$datosMiembro['CODPAISDOM']['valorCampo'] = $datosSocioConfirmar['CODPAISDOM'];
							$datosMiembro['DIRECCION']['valorCampo'] = $datosSocioConfirmar['DIRECCION'];
							$datosMiembro['CP']['valorCampo'] = $datosSocioConfirmar['CP'];
							$datosMiembro['LOCALIDAD']['valorCampo'] = $datosSocioConfirmar['LOCALIDAD'];
							
							$datosMiembro['ARCHIVOFIRMAPD']['valorCampo'] = NULL;//por defecto los pondría a NULL, o vacio		
							$datosMiembro['PATH_ARCHIVO_FIRMAS']['valorCampo'] = NULL;	
						
							$datosMiembro['COMENTARIOSOCIO']['valorCampo'] = $datosSocioConfirmar['COMENTARIOSOCIO'];

						 /*-- Inicio buscar en MIEMBRO nombre-apellidos gestor para campo OBSERVACIONES --*/
													
							$arrDatosMiembro = buscarDatosMiembro($_SESSION['vs_CODUSER'],$conexionDB['conexionLink']);//en en modeloPresCoord.php, $_SESSION['vs_CODUSER'] es el CODUSER del gestor, incluye conexionDB
							
							//echo "<br><br>2-4-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:arrDatosMiembro: ";print_r($arrDatosMiembro);   
							
							if ($arrDatosMiembro['codError'] !== '00000')
							{ $resInsertar = $arrDatosMiembro;
							}  				
							elseif ($arrDatosMiembro['numFilas'] <= 0) // sí es error 
							{	$resInsertar['codError'] = '80001'; //no encontrado 
									$resInsertar['errorMensaje'] = "Error: No se han encontrado los datos del gestor en la tabla MIEMBRO ".$arrDatosMiembro['errorMensaje'];				
							}
							else //$resInsertar['codError'] <= $arrDatosMiembro['codError'] =='00000'
							{ 
								$nomGestor = $arrDatosMiembro['resultadoFilas'][0]['NOM']." ".  $arrDatosMiembro['resultadoFilas'][0]['APE1'];		
								if (isset($arrDatosMiembro['resultadoFilas'][0]['NOM']) && !empty($arrDatosMiembro['resultadoFilas'][0]['APE2']))
								{	$nomGestor .= " ".$arrDatosMiembro['resultadoFilas'][0]['APE2'];
								}
							} 									
								$datosMiembro['OBSERVACIONES']['valorCampo'] = "Alta iniciada por socio/a y confirmada por un gestor/a ".$nomGestor." el día: ".date('Y-m-d');																
							
							/*--  Fin buscar en tabla MIEMBRO nombre-apellidos gestor para campo OBSERVACIONES  --*/										
				
			    //echo "<br><br>2-4-2 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:datosMiembro ";print_r($datosMiembro);

							$resInsertarMiembro = insertarMiembro($datosMiembro);//en modeloUsuarios.php, incluye conexionDB	
							
				   //echo "<br><br>2-5-0 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertarMiembro: ";print_r($resInsertarMiembro);
			    //--------------------- Fin insertarMiembro -----------------------------
							
							//--------------------- Inicio insertar Socio ---------------------------
			    if ($resInsertarMiembro['codError'] !== '00000')			
			    {$resInsertar = $resInsertarMiembro;
			    }
				   else //$resInsertarMiembro['codError']=='00000'
			    {$datosSocio['CODUSER']['valorCampo'] = $codUser;
							 $datosSocio['CODAGRUPACION']['valorCampo'] =  $datosSocioConfirmar['CODAGRUPACION'];
								$datosSocio['CODCUOTA']['valorCampo'] =  $datosSocioConfirmar['CODCUOTA'];
	       //	$datosSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'];							

								$datosSocio['CUENTAIBAN']['valorCampo'] =  $datosSocioConfirmar['CUENTAIBAN'];
								$datosSocio['CUENTANOIBAN']['valorCampo'] =  $datosSocioConfirmar['CUENTANOIBAN'];		
					
								//--- $modoIngresoCuota y $ordenarCobroBanco, se utilizarán también mas adelante en insertarCuotaAnioSocio ---
							 if ( isset($datosSocioConfirmar['CUENTAIBAN']) && !empty($datosSocioConfirmar['CUENTAIBAN'])	)	
								{ $modoIngresoCuota  = 'DOMICILIADA';
							
							   if($datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'] == 0 ) //Si existe cuenta pero IMPORTECUOTAANIOSOCIO = 0, será EXENTO.
										{
											$ordenarCobroBanco ='NO';	
										}
										else
										{											
           $ordenarCobroBanco = 'SI';
										}
								}
						  elseif ( isset($datosSocioConfirmar['CUENTANOIBAN']) && !empty($datosSocioConfirmar['CUENTANOIBAN']) )	
								{ 
							   $modoIngresoCuota  = 'DOMICILIADA';
          $ordenarCobroBanco = 'NO'; //por ahora no se pueden domiciliar estas cuentas en el B. Santander
								}								
								else
								{ $modoIngresoCuota  ='SIN-DATOS';
          $ordenarCobroBanco ='NO';
								}	
         /*---- las columnas SECUENCIAADEUDOSEPA = FRST y 
									 FECHAACTUALIZACUENTA=fecha alta, se añaden en función insertarSocio($datosSocio)
										 en este caso no hay que tratar datos del Formulario					
         ---------------------------------------------------------------------*/											

								$datosSocio['MODOINGRESO']['valorCampo'] =  $modoIngresoCuota;
							
								$datosSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'];	
						
					   //echo "<br><br>2-5-1a modeloPresCoord:altaSociosConfirmada:datosSocio: ";print_r($datosSocio);
								
					   $resInsertarSocio = insertarSocio($datosSocio);//en modeloSocios.php    
								/* Dentro de esta función insertarSocio(): halla Codmax. para el CODSOCIO  
								   DEVUELVE CODSOCIO y añade las columnas SECUENCIAADEUDOSEPA = FRST y 
											FECHAACTUALIZACUENTA	= fecha alta	
        */								
					   //echo "<br><br>2-5-1b modeloSocios:altaSociosConfirmada:resInsertarSocio ";print_r($resInsertarSocio);
				    //---------------------------- Fin insertar Socio()---------------------									
	
						  /*--------- Inicio insertar en tabla CuotaAnioSocio --------------------		
								  Si la confirmación se realiza en el año siguiente al que inició su registro, 
								 (probable si se registró a finales de diciembre), pueden haberse incrementado 
									 la cuota anual de EL y ser mayor que la elegida por el socio, en ese 
										caso hay que poner la cuota mayor de ambas para el año actual, pero 
										dejar la elegida por el socio en el año anterior por si ya la hubiese abonado								
								----------------------------------------------------------------------*/								
								
				    if ($resInsertarSocio['codError'] !== '00000')			
				    {$resInsertar = $resInsertarSocio;
				    }
					   else //$resInsertarSocio['codError']=='00000'
				    {//---------------------- Inicio Insertar cuotaAnioSocio ---------------------------
								
				     $datosCuotaSocio['ANIOCUOTA']['valorCampo']= $datosSocioConfirmar['ANIOCUOTA'];//date('Y') 
				     $datosCuotaSocio['CODSOCIO']['valorCampo'] =	$resInsertarSocio['CODSOCIO'];//devuelve siguiente a máx  
									$datosCuotaSocio['CODCUOTA']['valorCampo'] = $datosSocioConfirmar['CODCUOTA'];									
									$datosCuotaSocio['CODAGRUPACION']['valorCampo'] = $datosSocioConfirmar['CODAGRUPACION'];
         $datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'];

									/*---------------------------------------------------------------------------------
									La función insertarCuotaAnioSocio() ya controla que no se pueda insertar una cuota 
								 IMPORTECUOTAANIOSOCIO <IMPORTECUOTAANIOEL, y lo corrige si es necesario.
								 Pero debido a que hay que controlar si es EXENTO (que puede variar según
									los años por ejemplo cambia en 2015) y no se conoce el valor de 
									IMPORTECUOTAANIOSOCIO para el año correspondiente a ese tipo de cuota, 
									necesario para if($datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo']!=0)
									o su equivalente if($importeCuotaAnioEL_anioActual!=0), se llama a la 
								 función "buscarCuotasAnioEL()" para obtener los valores de las cuotas 
									vigentes de EL para los años Y y $datosSocioConfirmar['ANIOCUOTA'] 
									(si se inició el proceso alta en año anterior (diciembre pero se da de 
									alta en enero) y las cuotas de EL exentas=0, pueden haber variado, de un año a otro
									-----------------------------------------------------------------------------------*/
									$tipoCuota = $datosCuotaSocio['CODCUOTA']['valorCampo'];
         
         $resImporteCuotaAnio = buscarCuotasAnioEL("%",$tipoCuota);//$resImporteCuotaAnio=buscarCuotasAnioEL($anioActual,$tipoCuota);	
						
         //echo "<br><br>2-5-2a modeloPresCoord:altaSociosConfirmada:resImporteCuotaAnio:";print_r($resImporteCuotaAnio);													
							
									if ($resImporteCuotaAnio['codError'] !== '00000')//si ['numFilas']=0 también es incluido como error
									{ $resInsertar['codError'] = $resImporteCuotaAnio['codError'];
									  $resInsertar['errorMensaje'] .= $resImporteCuotaAnio['errorMensaje'];
									}
									else //$resImporteCuotaAnio['codError']=='00000'
									{ 								
									 /*if($datosSocioConfirmar['CODCUOTA']=='General')
									 {$datosCuotaSocio['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';}
									 else {$datosCuotaSocio['ESTADOCUOTA']['valorCampo']='EXENTO';}			para cuando los demás estaban exentos	*/		
																								
										$anioActual = date('Y');									
										$importeCuotaAnioEL_anioActual = $resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioActual][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];																					
	         //echo "<br><br>2-5-2b modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);																						
									
         	//if($datosCuotaSocio['IMPORTECUOTAANIOEL']['valorCampo'] != 0)//correspondiente al tipo de cuota del socio
										if($importeCuotaAnioEL_anioActual != 0)//equivale a anterior línea			
										{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';
										 //echo "<br><br>2-5-2b-1";
										}
										else
										{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'EXENTO';//en 2014 entrará, pero no entrará en 2015 joven=5 porque cambia							
	          //echo "<br><br>2-5-2b-2";									
										}
										
									 $datosCuotaSocio['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;
						
          //echo "<br><br>2-5-2c modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:datosCuotaSocio: ";print_r($datosCuotaSocio);

										/*--- Inicio: registro y confirmación de alta realizado el socio en el
  										mismo año de inicio de registro. Inserta en CUATOAANIOSOCIO, y 
												la cuota correspondiente a la fecha de confirmación de alta del socio									
										  se inserta una sola fila los datos de SOCIOSCONFIRMAR 
										--------------------------------------------------------------------*/										
      			 if ($datosSocioConfirmar['ANIOCUOTA'] == date('Y'))//inserta una sola fila para año 'Y' y nada mas			
									 {
									  $datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobroBanco;

											/*La función insertarCuotaAnioSocio() ya controla internamente que la
  											cuota elegida por el socio no pueda ser inferior a la cuota de EL 
													para este tipo de CODCUOTA y año concreto,y lo modifica dentro de 
													ella si es necesario, por lo que no es necesario controlarlo aquí.
           -------------------------------------------------------------------*/											
																				 
									  $resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una sola fila para año 'Y' y nada mas, necesita 'CODSOCIO', en modeloSocios.php 
					      //echo "<br><br>2-5-2d modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertarCuotaAnioSocio:";print_r($resInsertarCuotaAnioSocio);
 								 }
										//-Fin:registro y confirmación alta realizado el socio en el mismo año-										
										
										/*Inicio:registro y confirmación alta realizado el socio en dos años distintos (consecutivos)- 
									   Se insertan dos filas en CUATOAANIOSOCIO: una para año el de 
												registro (año anterior), con los datos de SOCIOSCONFIRMAR por si hubiese
												abonado con paypal o transferencia antes de confirmar alta) Y otra para
												año de confirmación de alta (año actual), en la que hay que poner los datos 
										  de IMPORTECUOTAANIOSOCIO y IMPORTECUOTAANIOEL, adecuados al nuevo año 
										--------------------------------------------------------------------*/
										elseif ($datosSocioConfirmar['ANIOCUOTA'] < date('Y'))//Se harán dos inserciones una para año de registro por el socio que esta en SOCIOSCONFIRMAR, y otra para año actual date(Y) 										
									 {                                                    
											/*Inicio insertar en año anterior por si hubiese abonado cuota año anterior por paypal o transferencia ---
											  La función insertarCuotaAnioSocio() ya controla que no se pueda insertar una cuota 
											  IMPORTECUOTAANIOSOCIO <IMPORTECUOTAANIOEL en un año, y lo corrige si es nescesario,
											-------------------------------------------------------------------*/
										 
											$anioAnterior = $datosSocioConfirmar['ANIOCUOTA'];
											
											$importeCuotaAnioEL_anioAnterior =	$resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioAnterior][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];	

           $datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] = 'NO';//---- no se pasan órdenes cobro de años anteriores														
											
											//echo "<br><br>2-5-2e modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);													
											
											//if($datosSocioConfirmar['CODCUOTA']=='General')
		         //if($datosCuotaSocio['IMPORTECUOTAANIOEL']['valorCampo'] != 0)//correspondiente al tipo de cuota del socio	General, ...									
	          if($importeCuotaAnioEL_anioAnterior != 0)//correspondiente al tipo de cuota del socio	General, ...									
											{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'NOABONADA';//en año anterior no puede ser PENDIENTE-CONFIRMAR
											 //echo "<br><br>2-5-2e-1 ";
											}
											else
											{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'EXENTO';
											 //echo "<br><br>2-5-2e-2 ";
											}														
									  $resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una fila con año anterior  ANIOCUOTA<date('Y') se cambia ESTADOCUOTA si procede
           //echo "<br><br>2-5-2e-3 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertarCuotaAnioSocio:";print_r($resInsertarCuotaAnioSocio);
										
										 //- Fin insertar en año anterior por si hubiese abonado cuota año anterior por paypal o transferencia ---
										
											//-Inicio insertar en año actual, despues de insertar fila en año anterior --
											
										 if ($resInsertarCuotaAnioSocio['codError'] == '00000') 		
										 {$datosCuotaSocio['ANIOCUOTA']['valorCampo'] = date('Y');// se pone año "ANIOCUOTA" = date('Y'), año actual

											 $anioActual = date('Y');
											 //$tipoCuota = $datosCuotaSocio['CODCUOTA']['valorCampo'];//ya está anteriormente
																				
												$importeCuotaAnioEL_anioActual = $resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioActual][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];
            //echo "<br><br>2-5-2f modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);		

												//las siguientes  2 líneas creo que no son necesarias porque ya están el control en la función 
												if ($datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] < $importeCuotaAnioEL_anioActual)
												{ $datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $importeCuotaAnioEL_anioActual;
												  //echo "<br><br>2-5-2f-1";
												}
												
									   //if($datosSocioConfirmar['CODCUOTA']=='General'y otros  que abonen)
	           if($importeCuotaAnioEL_anioActual != 0)//correspondiente a ese tipo de cuota, General,...												
											 { $datosCuotaSocio['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';
												  //echo "<br><br>2-5-2f-2";
												}
											 else
											 { $datosCuotaSocio['ESTADOCUOTA']['valorCampo']='EXENTO';
												  //echo "<br><br>2-5-2f-3";
												}	
            $datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobroBanco;// Viene de condiciones previas
																				
											 $resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una fila para año actual 'Y', se necesita 'CODSOCIO'
						      //echo "<br><br>2-5-2f-4-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertarCuotaAnioSocio:";print_r($resInsertarCuotaAnioSocio);
										 }//if ($resInsertarCuotaAnioSocio['codError']=='00000') 
												
										 //--- Fin insertar en año actual despues de insertar en año anterior  ---------				
											
		        }//elseif ($datosSocioConfirmar['ANIOCUOTA'] < date('Y'))
          //--- Fin: registro y confirmación alta realizado el socio en dos años distintos (consecutivos) ------- 																		
										else 
										{ $resInsertarCuotaAnioSocio['codError'] = '80303';
											 $resInsertarCuotaAnioSocio['errorMensaje'] = 'Error en AÑO CUOTA, no se ha podido finalizar transación';
										}
	       	 //---------------------- Fin Insertar cuotaAnioSocio ---------------------------						
	
					     if ($resInsertarCuotaAnioSocio['codError']!=='00000')			
					     {$resInsertar = $resInsertarCuotaAnioSocio;
					     }
										else //$resInsertarCuotaAnioSocio['codError']=='00000')		
										{
											/*---------------------- Inicio "eliminarSocioConfirmar" ------------
											  Se elimina físicamente de SOCIOSCONFIRMAR para que no salga también
													duplicado en mostrar pendientes, y otras posibles búsquedas y en la
													información para estadísticas, pues ya estarán los datos en 
													CONFIRMAREMAILALTAGESTOR y en MIEMBRO, y otras tablas	
             
													OJO: diferente de modeloSocios.php:altaSociosConfirmada()													
											-------------------------------------------------------------------*/
	
										 $tablaEliminarFila = 'SOCIOSCONFIRMAR';			
								   $resEliminarSocioConfirmar = eliminarSocioConfirmar($tablaEliminarFila,$codUser,$conexionDB['conexionLink']);//esta en modeloSocios.php utiliza $arrBind  
											
											//echo "<br><br>2-5-3 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resEliminarSocioConfirmar:"; print_r($resEliminarSocioConfirmar);
											//---------------------- Fin eliminarSocioConfirmar -----------------
											
											if ($resEliminarSocioConfirmar['codError'] !== '00000')			
					      {$resInsertar = $resEliminarSocioConfirmar;
					      }											
											elseif ($resEliminarSocioConfirmar['numFilas'] <= 0)
											{ $resInsertar['codError'] = '80001';
													$resInsertar['errorMensaje'] = 'No se pudo eliminar los datos del socio de la tabla SOCIOSCONFIRMAR';
											}													
							    else //$resActualizarSocioConfirmar['codError']=='00000'
						     {
												/*------ Inicio insertarUnaFila('CONFIRMAREMAILALTAGESTOR',...)-----			         
												  Se inserta la fila en CONFIRMAREMAILALTAGESTOR por si se quieren 
														enviar más emails al socio para que el socio sepa que ya esta dado
														de alta confirmado por un gestor pero que tiene que elegir contraseña
														
														OJO: Es diferente a modeloSocios.php: altaSociosConfirmada() 
            ------------------------------------------------------------------*/												
												
									   $datosConfirmarEmailAltaGestor['CODUSER'] = $codUser;		 
									   $datosConfirmarEmailAltaGestor['FECHAENVIOEMAILULTIMO'] = date('Y-m-d'); 	
											 $datosConfirmarEmailAltaGestor['FECHARESPUESTAEMAIL'] = '0000-00-00';
												$datosConfirmarEmailAltaGestor['NUMENVIOS'] = 1;
												$datosConfirmarEmailAltaGestor['OBSERVACIONES'] = 'Alta iniciada por socio, pero confirmada por gestor de Europa Laica';
												
												$reDatosConfirmarEmailAltaGestor = insertarUnaFila('CONFIRMAREMAILALTAGESTOR',$datosConfirmarEmailAltaGestor,$conexionDB['conexionLink']);//en modeloMySQL.php	
							     
            //echo "<br><br>2-5-4 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:reDatosConfirmarEmailAltaGestor:"; print_r($reDatosConfirmarEmailAltaGestor);
												//----------- Fin insertarUnaFila('CONFIRMAREMAILALTAGESTOR',...)---
							     
											 if ($reDatosConfirmarEmailAltaGestor['codError'] !== '00000')
											 {$resInsertar = $reDatosConfirmarEmailAltaGestor;
												}							
											 else //$reDatosConfirmarEmailAltaGestor['codError']=='00000'
												{//----------------Inicio COMMIT -----------------------------------
													$resFinTrans = commitPDO($conexionDB['conexionLink']);
													
													//echo "<br><br>3-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor: ";var_dump($resFinTrans);
													
													if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502';		
													{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación del socio/a. ';
														$resFinTrans['numFilas'] = 0;	
													
														$resInsertar = $resFinTrans;	
														//echo "<br><br>3-2 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertar: ";print_r($resInsertar);
													}	
											  else
											  {
													 $resInsertar['CODUSER'] = $codUser;
													 $resInsertar['codError'] = '00000';
												  $arrMensaje['textoComentarios'] = "Acabas de registrar el socio/a <b> ".
												 	$datosSocioConfirmar['NOM']." ".$datosSocioConfirmar['APE1'].
														" </b>en la base de datos de Europa Laica
														<br /><br /><br />
												  Llegará un email, al socio/a para que elija la contraseña y confirme su email.
														<br /><br /><br />
												  También llegará un email a Presidencia, Secretaría, Tesorería y Coordinador, 
														con información del alta del socio/a";
											  }
												}//$reDatosConfirmarEmailAltaGestor['codError']=='00000'
										 }//else $resActualizarSocioConfirmar['codError']=='00000'
										}//else $resInsertarCuotaAnioSocio['codError']=='00000'
									}//else $resImporteCuotaAnio['codError']=='00000'
						  }//else $resInsertarSocio['codError']=='00000'
			    }//else $resInsertarMiembro['codError']=='00000'
		    }//else $resInsertarUsuarioRol['codError']=='00000'				
		   }//else($resInsertarUsuario['codError']=='00000')	
				}//else $resDatosSocioConfirmar['codError']=='00000'	    
					
				//echo "<br><br>4-1 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resInsertar: ";print_r($resInsertar);
				
		 	//---------------- Inicio tratamiento errores -------------------------------	   
		  //--- Inicio deshacer transación en las tablas modificadas ---------------					
				if ($resInsertar['codError'] !== '00000')
				{						
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
											
					if ($resDeshacerTrans['codError'] !== '00000')//sera $resDeshacerTrans['codError'] = '70503';
					{ $resInsertar['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];		
					}		
    }//--- Fin deshacer transación en las tablas modificadas -----------------
	 }//else $resIniTrans
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
		
		if ($resInsertar['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($resInsertar['textoComentarios']) ) 
			{ $resInsertar['textoComentarios'] = "modeloPresCoord:altaSocioPendienteConfirmadaPorGestor(): ".$resInsertar['textoComentarios'];}	
			else
			{	$resInsertar['textoComentarios'] = "modeloPresCoord:altaSocioPendienteConfirmadaPorGestor(): ";}		
							
					require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
					$resInsertarErrores = insertarError($resInsertar,$conexionDB['conexionLink']);
			
					if ($resInsertarErrores['codError'] !== '00000')
					{ $resInsertar['codError'] = $resInsertarErrores['codError'];
							$resInsertar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					}					
		}//if $resInsertar['codError']!=='00000'
  //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------			

	}//else $conexionDB['codError']=="00000"
	
	$resAltaSocios = $resInsertar;
	$resAltaSocios['arrMensaje'] = $arrMensaje;	
	
 //echo "<br><br>5 modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:resAltaSocios: ";print_r($resAltaSocios);
	
 return 	$resAltaSocios; 	
}
/*------------------------------ Fin altaSocioPendienteConfirmadaPorGestor ----------------------*/

/*----------------------- Inicio buscarConfirmarEmailAltaGestor -----------------------------------
Busca datos socio en CONFIRMAREMAILALTAGESTOR (de los que proceden de alta por 
Gestores) y que siguen sin confirmar recepción de email y el establecimiento
de contraseña por el socio.

RECIBE: $codUser (sin encriptar), y $tipoMiembro (socio, simpatizantes)) 
        y $estadoUsuario='alta-sin-password-excel' o 'alta-sin-password-excel'
DEVUELVE: $arrDatosConfEmailAltaGestor, que es un array con los datos del socio
          resultado de la consulta y códigos errores.								
								
LLAMADA: Hay que eliminar cAdmin.php:reenviarEmailConfirmarEstablecerPassAltaGestor(),
         cPresidente.php:reenviarEmailConfirmarSocioAltaGestor()
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
						 modelos/conexionMySQL.php:conexionDB()
       modeloMySQL.php:buscarCadSql()
       modeloErrores.php:insertarError()							
     
OBSERVACIONES: 
2020-05-31: modifico para incluir PDO.php PDOStatement::bindParam		
Antes estaba en modeloAdmin.php:buscarConfirmarEmailAltaGestor, 
y hoy son restos del arranque de la aplicación, y mejor limpiar todo eso
------------------------------------------------------------------------------*/
function buscarConfirmarEmailAltaGestor($codUser,$tipoMiembro,$estadoUsuario)
{
	//echo "<br><br>0-1 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:codUser: $codUser"; 
	//echo "<br><br>0-2 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:tipoMiembro: $tipoMiembro"; 
	//echo "<br><br>0-3 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:estadoUsuario: $estadoUsuario"; 
	
	$arrDatosConfEmailAltaAdmin['nomScript'] = "modeloPresCoord.php";	
	$arrDatosConfEmailAltaAdmin['nomFuncion'] = "buscarConfirmarEmailAltaGestor";
	$arrDatosConfEmailAltaAdmin['codError'] = '00000';
	$arrDatosConfEmailAltaAdmin['errorMensaje'] = "";	
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{$arrDatosConfEmailAltaGestor = $conexionDB;
	}
	else // $conexionDB['codError'] == '00000'		
	{		
  if (!isset($codUser) || empty($codUser) || $codUser == '%' )// tiene que ser un valor de un solo socio 		
	 { 
		  $arrDatosConfEmailAltaGestor['codError'] = '70601';
				$arrDatosConfEmailAltaGestor['errorMensaje'] = 'Faltan variables-parámetros necesarios para SQL';
	 }
		else //!if (!isset($codUser) || empty($codUser) || $codUser == '%' )// es un valor de un solo socio 
		{ 	 
    $arrBind[':codUser'] = $codUser; 				
	   $arrBind[':tipoMiembro'] = $tipoMiembro; 

				if (!isset($estadoUsuario) || empty($estadoUsuario) || $estadoUsuario == '%' )
				{ $condicionEstadoUsuario = '';		
				}
				else 
				{ 						
						$condicionEstadoUsuario = " AND USUARIO.ESTADO = :estadoUsuario ";						
						$arrBind[':estadoUsuario'] = $estadoUsuario;						
				}		
			
				$tablasBusqueda = 'CONFIRMAREMAILALTAGESTOR,USUARIO,MIEMBRO';
				
				$camposBuscados = 'USUARIO.USUARIO,USUARIO.ESTADO,MIEMBRO.EMAIL, MIEMBRO.SEXO,MIEMBRO.NOM,MIEMBRO.APE1, 
																							MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.DIRECCION,MIEMBRO.CP,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,																								
																							CONFIRMAREMAILALTAGESTOR.*';			
																										
				$cadenaCondicionesBuscar = " WHERE CONFIRMAREMAILALTAGESTOR.CODUSER = USUARIO.CODUSER
																												  				AND USUARIO.CODUSER = MIEMBRO.CODUSER																																														
																												  				AND MIEMBRO.TIPOMIEMBRO = :tipoMiembro
																											 	  			AND USUARIO.CODUSER = :codUser ".$condicionEstadoUsuario.
																													  			" AND SUBSTRING(MIEMBRO.EMAIL,-9) != 'falta.com' 
																															  	ORDER BY CONFIRMAREMAILALTAGESTOR.CODUSER";	
				
				//echo "<br><br>1 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:arrBind: ";print_r($arrBind); 																												

				//$arrDatosConfEmailAltaGestor = buscarEnTablas($tablasBusqueda,$cadenaCondicionesBuscar,$camposBuscados,$conexionDB['conexionLink'],$arrBind);//en  modeloMySQL.php
			
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
				
				//echo "<br><br>2 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:cadSql: ";print_r($cadSql);				
				
				$arrDatosConfEmailAltaGestor = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php			
    
				//echo "<br><br>3 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:arrDatosConfEmailAltaGestor: ";print_r($arrDatosConfEmailAltaGestor);				
				
		}//else !if (!isset($codUser) || empty($codUser) || $codUser == '%' )// es un valor de un solo socio  
		
		//echo "<br><br>4 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:arrDatosConfEmailAltaGestor: ";print_r($arrDatosConfEmailAltaGestor);
		
		if ($arrDatosConfEmailAltaGestor['codError'] !== '00000') //si es numFilas = 0 será error 
		{		
			$arrDatosConfEmailAltaGestor['textoComentarios'] .= ". modeloPresCoord.php:buscarConfirmarEmailAltaGestor(): ";

			require_once './modelos/modeloErrores.php';
			$resInsertarErrores = insertarError($arrDatosConfEmailAltaGestor,$conexionDB['conexionLink']);	
			if ($resInsertarErrores['codError'] !== '00000')
			{ $arrDatosConfEmailAltaGestor['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}	 
	
		}		
 }//else $conexionDB['codError'] == '00000'
	
	//echo "<br><br>5 modeloPresCoord.php:buscarConfirmarEmailAltaGestor:arrDatosConfEmailAltaGestor: ";print_r($arrDatosConfEmailAltaGestor);

	return $arrDatosConfEmailAltaGestor;	
}
/*----------------------- Fin buscarConfirmarEmailAltaGestor ------------------------------------*/

/*------------------- Inicio actualizarConfirmarEmailAltaGestor -----------------------------------
Cuando se llama desde Gestores (Presidente) actualiza la tabla de CONFIRMAREMAILALTAGESTOR 
incrementando NUMENVIO y anotando en ella la última fecha envío de email de recordatorio de 
que aún no ha confirmado su email y elgido contraseña.
Se ejecuta después del envío de email (con "Usuario"),
a los usuarios importados de Excel, 
Cuando un socio dado de alta por Excel o por un gestor confirma el email y 
elige pass, se actualiza la fecha de confimado 

LLAMADA: cPresidente.php:reenviarEmailConfirmarSocioAltaGestor()
modeloSocios.php:mConfirmarEmailPassAltaGestor()
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
						 modelos/conexionMySQL.php:conexionDB()
       modeloMySQL.php:actualizarTabla()
       modeloErrores.php:insertarError()	

OBSERVACIONES: 
2020-05-31: No necesita modificar para incluir PDOStatement::bindParam, 
las funciones llamadas aquí lo tratan internamente			
------------------------------------------------------------------------------*/
//function actualizarConfirmarEmailAltaGestor($tablaAct,$campoCondiciones,$arrayDatosAct,$conexionLink) 
function actualizarConfirmarEmailAltaGestor($tablaAct,$campoCondiciones,$arrayDatosAct,$conexionLink=NULL) 
{
	//echo "<br><br>0-1 modeloPresCoord:actualizarConfirmarEmailAltaGestor:arrayDatosAct: ";print_r($arrayDatosAct);
 //echo "<br><br>0-2 modeloPresCoord:actualizarConfirmarEmailAltaGestor:campoCondiciones: ";print_r($campoCondiciones);
	//echo "<br><br>0-3 modeloPresCoord:actualizarConfirmarEmailAltaGestor:conexionLink: ";print_r($conexionLink);
	
	$actConfirmarEmailAltaGestor['nomScript'] = 'modeloPresCoord.php';
	$actConfirmarEmailAltaGestor['nomFuncion'] = 'actualizarConfirmarEmailAltaGestor';	
	$actConfirmarEmailAltaGestor['codError'] = '00000';
	$actConfirmarEmailAltaGestor['errorMensaje'] = "";
	
	if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
	{ require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	}		
	else
	{ $conexionDB['codError'] = "00000";
			$conexionDB['conexionLink'] = $conexionLinkDB;
	}	
	//echo "<br><br>2 modeloPresCoord:actualizarConfirmarEmailAltaGestor:conexionDB: ";var_dump($conexionDB);	
	
	if ($conexionDB['codError'] !== '00000')		
	{ $actConfirmarEmailAltaGestor = $conexionDB;
	}
	else // $conexionDB['codError'] == '00000'
	{	
			$arrayCondiciones['CODUSER']['valorCampo'] = $campoCondiciones;
			$arrayCondiciones['CODUSER']['operador'] = '=';
			$arrayCondiciones['CODUSER']['opUnir'] = ' ';		

   //echo "<br><br>2 modeloPresCoord:actualizarConfirmarEmailAltaGestor:arrayCondiciones: ";print_r($arrayCondiciones);			
			
			foreach ($arrayDatosAct as $indice => $contenido)                         
			{      
						$arrayDatos[$indice] = $contenido['valorCampo']; 
			}
			//echo "<br><br>3 modeloPresCoord:actualizarConfirmarEmailAltaGestor:arrayDatos: ";print_r($arrayDatos);			
		
			$reActConfirmarEmailAltaGestor = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']); 

			if ($reActConfirmarEmailAltaGestor['codError'] !== '00000')
			{$actConfirmarEmailAltaGestor = $reActConfirmarEmailAltaGestor;		
				$actConfirmarEmailAltaGestor['textoComentarios'] .= ". modeloPresCoord.php:actualizarConfirmarEmailAltaGestor(): ";
				
				require_once './modelos/modeloErrores.php';		
				$resInsertarErrores = insertarError($reActConfirmarEmailAltaGestor,$conexionLink);
				if ($resInsertarErrores['codError'] !== '00000')
				{$actConfirmarEmailAltaGestor['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				}	 
			}	
 }//else $conexionDB['codError'] == '00000'			
	
	//echo "<br><br>4 modeloPresCoord:actualizarConfirmarEmailAltaGestor:actConfirmarEmailAltaGestor: ";print_r($actConfirmarEmailAltaGestor);
	
	return $actConfirmarEmailAltaGestor;
}
/*----------------------- Fin actualizarConfirmarEmailAltaGestor -------------------------------*/

/* ------------------------- Inicio anulacionSocioPendienteConfirmarPres -------------------------
Se eliminan los datos personales (se ponen a NULL en SOCIOSCONFIRMAR)
de un socio cuyo estado USUARIO.ESTADO=PENDIENTE-CONFIRMAR que pasará a ser 
USUARIO.ESTADO=ANULADA-SOCITUD-REGISTRO, 
(no se guarda ningún dato en MIEMBROELIMINADO5ANIOS, ya que no ha llegado a ser socio)

RECIBE: una variable $codUser 
DEVUELVE: un array con ['CODUSER'] y controles de errores

LLAMADA: desde cPresidente: anularAltaSocioPendienteConfirmarPres()
LLAMA: modeloUsuarios.php:actualizUsuario() 
modeloSocios.php:actualizarSocioConfirmar()       
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
modelos/conexionMySQL.php:conexionDB()
BBDD/MySQL/transationPDO.php
modeloMySQL.php:actualizarTabla()
modeloErrores.php:insertarError()	

OBSERVACIONES: 
2020-06-16: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza 

Es igual a modeloSocios.php:anularSocioPendienteConfirmar() excepto el mensaje
valorar si conviene fusionarlos solo en uno
------------------------------------------------------------------------------*/
function anulacionSocioPendienteConfirmarPres($codUser)
{
 //echo "<br><br>0-1 modeloPresCoord:anulacionSocioPendienteConfirmarPres:codUser: "; print_r($codUser); 

 $resAnularSocioPendiente['nomScript'] = " modeloPresCoord.php";	
	$resAnularSocioPendiente['nomFuncion'] = "anulacionSocioPendienteConfirmarPres";
	$resAnularSocioPendiente['codError'] = '00000';
	$resAnularSocioPendiente['errorMensaje'] = '';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== "00000")
	{ $resAnularSocioPendiente = $conexionDB;	  
	}	
 else //$conexionDB['codError'] =="00000"
	{
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloPresCoord:anulacionSocioPendienteConfirmarPres: ";var_dump($resIniTrans);	echo "<br>";
		//echo "<br><br>1-2 modeloPresCoord:anulacionSocioPendienteConfirmarPres:conexionsDB: ";var_dump($conexionDB);echo "<br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{	$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;
							
				$resAnularSocioPendiente = $resIniTrans;	
			 //echo "<br><br>2 modeloPresCoord:anulacionSocioPendienteConfirmarPres:resIniTrans: ";print_r($resIniTrans);
		}			
		else //$resIniTrans['codError'] == '00000'
		{
	  /* NOTA: se dejan valores de algunos campos no personales para estadísticas*/		
			$arrValoresActualizar['usuario']['ESTADO']['valorCampo']	= 'ANULADA-SOCITUD-REGISTRO';	
			$arrValoresActualizar['usuario']['USUARIO']['valorCampo'] = NULL;  
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
			//$arrValoresActualizar['socioConfirmar']['CODSUCURSAL']['valorCampo'] = NULL;
			//$arrValoresActualizar['socioConfirmar']['NUMCUENTA']['valorCampo'] = NULL;
			//$arrValoresActualizar['socioConfirmar']['CCEXTRANJERA']['valorCampo']= NULL;	
			$arrValoresActualizar['socioConfirmar']['CUENTAIBAN']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['CUENTANOIBAN']['valorCampo']= NULL;	
	 			
			//echo "<br><br>3 modeloPresCoord:anulacionSocioPendienteConfirmarPres:arrValoresActualizar: ";print_r($arrValoresActualizar);
   require_once './modelos/modeloUsuarios.php';	
			$resActualizarUsuario = actualizUsuario('USUARIO',$codUser,$arrValoresActualizar['usuario'],$conexionDB['conexionLink']);   
			
			//echo "<br><br>4 modeloPresCoord:anulacionSocioPendienteConfirmarPres:resActualizarUsuario: ";print_r($resActualizarUsuario);
			
			if ($resActualizarUsuario['codError'] !== '00000')			
	  { $resAnularSocioPendiente = $resActualizarUsuario;
	  }
			elseif ($resActualizarUsuario['numFilas'] <= 0)
			{ $resAnularSocioPendiente['codError'] = '80001';
					$resAnularSocioPendiente['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
			}			
	  else //resActualizarUsuario['codError']=='00000'
	  {require_once './modelos/modeloSocios.php';	
	   $resActualizarSociosConfirmar = actualizarSocioConfirmar('SOCIOSCONFIRMAR',$codUser,$arrValoresActualizar['socioConfirmar']);//incluye conexionDB 
				
		  //echo "<br><br>5  modeloPresCoord:anulacionSocioPendienteConfirmarPres:resActualizarSociosConfirmar: ";print_r($resActualizarSociosConfirmar);
				
				if ($resActualizarSociosConfirmar['codError'] !== '00000')			
		  { $resAnularSocioPendiente = $resActualizarSociosConfirmar;
		  }
				elseif ($resActualizarSociosConfirmar['numFilas'] <= 0)
				{ $resAnularSocioPendiente['codError'] = '80001';
						$resAnularSocioPendiente['errorMensaje'] = 'No se pudo modificar la tabla SOCIOSCONFIRMAR';
				}				
		  else //$resActualizarSociosConfirmar['codError']=='00000'
		  { 
		 	 //----------------Inicio COMMIT -------------------------------------------
					$resFinTrans = commitPDO($conexionDB['conexionLink']);
					
					//echo "<br><br>6-1 modeloPresCoord:anulacionSocioPendienteConfirmarPres:resFinTrans: ";var_dump($resFinTrans);						
					
					if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502'	
					{										
						$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de eliminar socio/a. ';
						$resFinTrans['numFilas'] = 0;	
														
						$resAnularSocioPendiente = $resFinTrans;	
					//echo "<br><br>6-2 modeloPresCoord:anulacionSocioPendienteConfirmarPres:resAnularSocioPendiente: ";print_r($resAnularSocioPendiente);
					} 
					else
			  {$resAnularSocioPendiente['codError'] = '00000';
	     $resAnularSocioPendiente['CODUSER'] = $codUser; 
				  $arrMensaje['textoComentarios'] = "Se ha anuladodo la solicitud alta del socio/a de Europa Laica y se han borrado todos sus datos personales de nuestra base de datos. <br /><br />";	
					}
			 }	//resActualizarSociosConfirmar['codError']=='00000'		
				//---------------- Fin COMMIT ----------------------------------------------
			}	//resActualizarUsuario['codError']=='00000'				
   
			//echo "<br><br>7 modeloPresCoord:anulacionSocioPendienteConfirmarPres:resAnularSocioPendiente: ";print_r($resAnularSocioPendiente);
			
   //---------------- Inicio tratamiento errores -------------------------------			
			//--- Inicio deshacer transación en las tablas modificadas ---------------
			if ($resAnularSocioPendiente['codError'] !== '00000')
			{ 
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
										
					if ($resDeshacerTrans['codError'] !== '00000')//será $resDeshacerTrans['codError'] = '70503';
					{ $resAnularSocioPendiente['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];											
					}
			}//--- Fin deshacer transación en las tablas modificadas -----------------		
		}//else $resIniTrans['codError'] == '00000'			
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
		
		if ($resAnularSocioPendiente['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{				
			if ( isset($resAnularSocioPendiente['textoComentarios']) ) 
			{ $resAnularSocioPendiente['textoComentarios'] = ". modeloSocios:anulacionSocioPendienteConfirmarPres:(): ".$resAnularSocioPendiente['textoComentarios'];}	
			else
			{	$resAnularSocioPendiente['textoComentarios'] = ". modeloSocios:anulacionSocioPendienteConfirmarPres:(): ";}								
										
			$resAnularSocioPendiente['textoComentarios'] = ". modeloSocios:anulacionSocioPendienteConfirmarPres:(): ";
			
			require_once './modelos/modeloErrores.php';//si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores = insertarError($resAnularSocioPendiente);	
				
			if ($resInsertarErrores['codError'] !== '00000')
			{ $resAnularSocioPendiente['codError'] = $resInsertarErrores['codError'];
					$resAnularSocioPendiente['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}
					
		}//if ($resAnularSocioPendiente['codError']!=='00000')
		//---------------- Fin tratamiento errores ----------------------------------	
		
 //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
	
 }//else $conexionDB['codError']=="00000"
	
	$anularSocioPendienteConfirmar = $resAnularSocioPendiente;
	$anularSocioPendienteConfirmar['arrMensaje'] = $arrMensaje;	
	
	//echo "<br><br>8 modeloPresCoord:anulacionSocioPendienteConfirmarPres:anularSocioPendienteConfirmar: ";print_r($anularSocioPendienteConfirmar);
	
 return 	$anularSocioPendienteConfirmar; 	
}
/*------------------------------ Fin anulacionSocioPendienteConfirmarPres ----------------------*/

//=============== FIN ESTADOS ALTAS SOCIOS: PENDIENTES CONFIRMAR Y OTROS  =======================

?>