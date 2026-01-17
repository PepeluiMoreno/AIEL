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
datos recibidos de "$arrValoresError" para obtener los "bindParamValue"
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
			$arrValoresError['codError']		    = $arrValoresCampos['codError'];
			
			if (isset($arrValoresCampos['errno'])) 
			{ $arrValoresError['errno']       = $arrValoresCampos['errno'];}	
		
			//if (isset($arrValoresCampos['codSQLSTATE']))
			//{ $arrValoresError['codSQLSTATE'] = $arrValoresCampos['codSQLSTATE'];}		
			if (!isset($arrValoresCampos['codSQLSTATE']) || empty($arrValoresCampos['codSQLSTATE']))
			{ if (isset($arrValoresCampos['errno']))	
			  { $arrValoresError['codSQLSTATE'] = $arrValoresCampos['errno'];}					
		 }
			if (isset($arrValoresCampos['errorMensaje'])) 
			{ $arrValoresError['errorMensaje']= addslashes($arrValoresCampos['errorMensaje']);}			
				
			if (isset($arrValoresCampos['numFilas'])) 
			{ $arrValoresError['numFilas']    = $arrValoresCampos['numFilas'];}
		
			if (isset($arrValoresCampos['nomScript'])) 
			{ $arrValoresError['nomScript']   = $arrValoresCampos['nomScript'];}	
		
			if (isset($arrValoresCampos['nomFuncion'])) 
			{ $arrValoresError['nomFuncion']  = $arrValoresCampos['nomFuncion'];}	
		
			if (isset($_SESSION['vs_CODUSER'])) 
			{ $arrValoresError['CODUSER']     = $_SESSION['vs_CODUSER'];}	
		
			if (isset($usernameDB)) 
			{ $arrValoresError['usuarioBBDD'] = $usernameDB;}

			$arrValoresError['fechaSys']   	  = date("Y-m-d:H:i:s");			

   if (isset($arrValoresCampos['textoComentarios'])) 
			{ $arrValoresError['textoComentarios']= addslashes($arrValoresCampos['textoComentarios']);}		
		
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