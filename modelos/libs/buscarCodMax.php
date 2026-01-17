<?php
/*------------------------------ Inicio buscarCodMax --------------------------- 
Busca el máximo valor de un determinado campo "$camposBuscados" en 
la tabla '$tablasBusqueda'. Si no devuelve ninguna fila lo considera valor "0"
Después incrementa en +1 el valor máximo y lo devuelve como siguiente valor máximo
dentro de un array que contiene los códigos de error. 
Normalmente se usará ese valor para después insertar registro en una 
tabla: USUARIO, SOCIO, DONACION, ...

RECIBE: $tablasBusqueda,$camposBuscados,$conexionLinkDB
DEVUELVE: array $arrValMax con valor máximo y código error

LLAMADO: modeloSocios.php:altaSocios(),modeloPresCoord.php:mAltaSocioPorGestor()
modeloTesorero.php:insertarDonacion()
LLAMA: modeloMySQL.php:buscarCadSql(),
       usuariosConfig/BBDD/MySQL/configMySQL.php, 
				   modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: 
2020-06-21: Adapto para PDOStatement::bindParamValue
Añado controlar si ya hay conexión a BBDD y si no hay se conecta
anteriormente ya recibía $conexionLinkDB, pero de este modo es más flexibe 
Versión: PHP 7.3.19
------------------------------------------------------------------------------*/
function buscarCodMax($tablasBusqueda,$camposBuscados,$conexionLinkDB)
{ 
		//echo "<br><br>0-1 buscarCodMax.php:camposBuscados: ".$camposBuscados;		

		$arrValMax['nomScript'] = "/modelos/libs/buscarCodMax.php";	
		$arrValMax['nomFuncion'] = "buscarCodMax";
		$arrValMax['codError'] = "00000";
		$arrValMax['errorMensaje'] = "";
		
		if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
		{ 	   
				require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
				require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
				
				$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
				
				//echo "<br><br>1-1 modelos/libs/buscarCodMax.php:buscarCodMax:conexionDB: ";var_dump($conexionDB);
		}		
		else
		{ $conexionDB['codError'] = "00000";
				$conexionDB['conexionLink'] = $conexionLinkDB; 
				
				//echo "<br><br>1-2 modelos/libs/buscarCodMax.php:buscarCodMax:conexionDB: ";var_dump($conexionDB);	 
		}	
		
		if ($conexionDB['codError'] !== "00000")
		{ 
				$arrValMax['codError'] = $conexionDB['codError'];
				$arrValMax['errorMensaje'] = $conexionDB['errorMensaje'];
		}
		else	
		{ 				
				$camposBuscados = "max(".$camposBuscados.")";				
				$cadCondicionesBuscar = "";
				
				$arrBind = array(); 
				
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
				
				//echo "<br><br>2-1 modelos/libs/buscarCodMax.php:buscarCodMax:resulBuscarMax: ";var_dump($resulBuscarMax);	
				
				require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
				$resulBuscarMax = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind); 
				
				//echo "<br><br>2-2 modelos/libs/buscarCodMax.php:buscarCodMax:resulBuscarMax: ";var_dump($resulBuscarMax);	 
			
							
				if ($resulBuscarMax['codError'] !== '00000')
				{ 
						$arrValMax = $resulBuscarMax;
				}
				else
				{
						if ($resulBuscarMax['numFilas'] == 0)//tabla vacía o primera vez
						{ 
								$arrValMax['valorCampo'] = 0;	
						}	
						else //$resulBuscarMax['numFilas']>=1
						{			 			
								$arrValMax['valorCampo'] = $resulBuscarMax['resultadoFilas'][0][$camposBuscados];
						}
						
					$arrValMax['valorCampo'] += 1;
				}
		}	
		//echo "<br><br>3 buscarCodMax.php:arrValMax: ", print_r($arrValMax);
		
		return $arrValMax;	
}
/*------------------------------ Fin buscarCodMax ----------------------------*/
?>