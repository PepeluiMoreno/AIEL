<?php
/*------------------------------------------------------------------------------
FICHERO: modeloErrores.php
VERSION: PHP 7.3.19

DESCRIPCION: Cuando se produce un error esta función graba información en la 
             tabla ERRORES con algunos datos informativos 

sólo contiene la función: insertarError($arrValoresCampos)
------------------------------------------------------------------------------*/

/*----------------------------- Inicio insertarError ---------------------------
DESCRIPCION: Cuando se produce un error, al llamar a esta función graba 
             información en la tabla 'ERRORES' con algunos datos informativos

LLLAMADA: desde algunas funciones en los modelos.php
									
LLLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
							modeloMySQL.php:insertarUnaFila()		

OBSERVACIONES:	No es necesario utilizar aquí "PDOStatement::bindParamValue para 
la función "insertarUnaFila()" ya incluye la transformación para PDO de los 
datos recibidos de "$arrValoresError" para obtener los "bindParamValue".
2024-10-04 modifico "if (isset($arrValoresCampos['numFilas']))" y otros
------------------------------------------------------------------------------*/
function insertarError($arrValoresCampos,$conexionLinkDB = NULL)
{
 //echo "<br><br>0-1 modeloErrores.php:insertarError:arrValoresCampo: ";print_r($arrValoresCampos);
 //echo "<br><br>0-2 modeloErrores.php:insertarError:conexionLinkDB: ";var_dump($conexionLinkDB);	

	$arrInserFilaError['nomScript']    = 'modeloErrores.php';
	$arrInserFilaError['nomFuncion']   = 'insertarError';
	$arrInserFilaError['codError']     = '00000';
	$arrInserFilaError['errorMensaje'] = '';
 
	if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
	{ 	
   require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
		 
			//echo "<br><br>1 modeloErrores.php:insertarError:conexionDB: ";var_dump($conexionDB);				
 }		
	else
	{ $conexionDB['codError'] = "00000";
		 $conexionDB['conexionLink'] = $conexionLinkDB;  			
	 	//echo "<br><br>2 modeloErrores.php:insertarError:conexionDB: ";var_dump($conexionDB);	 
	}	
		
	if ($conexionDB['codError'] !== "00000")
	{ $arrInserFilaError['codError'] = $conexionDB['codError'];
	  $arrInserFilaError['errorMensaje'] = $conexionDB['errorMensaje'];
	}
	else
	{				
			if (!isset($arrValoresCampos['codError']) || empty($arrValoresCampos['codSQLSTATE']) ) 
			{ $arrValoresError['codError']     = "9000";}	//pongo 9000 = "error no definido",	porque en BBDD es un campo no nulo			

		
		 if (isset($arrValoresCampos['errno'])) 
			{ $arrValoresError['errno']        = $arrValoresCampos['errno'];}	
			
	
			if (!isset($arrValoresCampos['codSQLSTATE']) || empty($arrValoresCampos['codSQLSTATE']))
			{ if (isset($arrValoresCampos['errno']) )	
			  {$arrValoresError['codSQLSTATE'] = $arrValoresCampos['errno'];}					
		 }
   
			if (isset($arrValoresCampos['errorMensaje'])) 
			{if (strlen($arrValoresCampos['errorMensaje']) > 1000)
				{ $arrValoresCampos['errorMensaje'] = substr($arrValoresCampos['errorMensaje'], 0, 100); }	
		
		   $arrValoresError['errorMensaje'] = addslashes($arrValoresCampos['errorMensaje']);
			} 

			if (isset($arrValoresCampos['numFilas']) && $arrValoresCampos['numFilas'] >= 0)//si es valor negativo (-1) da error ya que tabla ERRORES columna "numFilas" tipo	"int(10)	UNSIGNED" y no amite valor negativo, 		 
			{ $arrValoresError['numFilas']    = $arrValoresCampos['numFilas'];}
			//else {asignará el valor predeterminado tabla ERRORES.numFilas que es NULL}		
		
			if (isset($arrValoresCampos['nomScript'])) 
			{ $arrValoresError['nomScript']   = $arrValoresCampos['nomScript'];}	
		
			if (isset($arrValoresCampos['nomFuncion'])) 
			{ $arrValoresError['nomFuncion']  = $arrValoresCampos['nomFuncion'];}	
		
			if (isset($_SESSION['vs_CODUSER'])) 
			{ $arrValoresError['CODUSER']     = $_SESSION['vs_CODUSER'];}	
		
			if (isset($usernameDB)) 
			{ $arrValoresError['usuarioBBDD'] = $usernameDB;}

			//$arrValoresError['fechaSys']   	  = date("Y-m-d:H:i:s");		error incopatibilidad formatos con nueva MariaDB	
			//No es necesario para "fechaSys" ya que en la tabla ERRORES el campo "fechaSys" inserta 
			//el valor predeterminado "current_timestamp()" con formato como "2024-10-01 23:04:32" 

   if (isset($arrValoresCampos['textoComentarios'])) //máximo 1000 caracteres en BBDD 
			{if (strlen($arrValoresCampos['textoComentarios']) > 1000)
				{ $arrValoresCampos['textoComentarios'] = substr($arrValoresCampos['textoComentarios'], 0, 1000); }	
			
			 $arrValoresError['textoComentarios'] = addslashes($arrValoresCampos['textoComentarios']);
			}		
 
		 //echo "<br><br>3 modeloErrores.php:insertarError:arrValoresError: ";print_r($arrValoresError); 
			
   require_once "BBDD/MySQL/modeloMySQL.php";				
   $arrInserFilaError = insertarUnaFila('ERRORES',$arrValoresError,$conexionDB['conexionLink']);
  
			if ($arrInserFilaError['codError'] !== '00000') 
			{	//no se prepara mensaje para email, porque ya se tratará en el lugar de llamada a insertarError
					//echo "<br><br>4 modeloErrores.php:insertarError:arrInserFilaError:";print_r($arrInserFilaError);
			}															
	}	
 //echo "<br>5 modeloErrores.php:insertarError:arrInserFilaError: "; print_r($arrInserFilaError);

 return $arrInserFilaError;
	
}
/*----------------------------- Fin insertarError -----------------------------*/
?>