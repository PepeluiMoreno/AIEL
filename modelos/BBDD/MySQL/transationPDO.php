<?php
/*-----------------------------------------------------------------------------
FICHERO: transationPDO.php 
VERSION: PHP 5.2.3 y 7.x
DESCRIPCION: Funciones transaciones: beginTransationPDO(),commitPDO(),
							      rollbackPDO()
							
RECIBE: $conexionDB objeto	instanciado de la clase MiPDO_ConexionDB			
DEVUELVE:	Devuelve un array asociativo con cuatro elementos:
							$arrF['nomScript'] 
							$arrF['nomFuncion'] 
							$arrF['codError'] (número asignado por el programador), 
							$arrF['errno'], (numero int de mysql),
							$arrF['errorMensaje'], (mensaje error) 
							$arrF['conexionLink'] (tipo recurso)			
							
OBSERVACIONES:

NOTA: en MySQL esto es válido para las tablas InnoDB, pero no en el caso de 
las tablas MyISAM, que no siempre serán "autocommit"

Se podría hacer una variante con puntos SAVEPOINT, pero creo que no es necesario
------------------------------------------------------------------------------*/

/*----------- Inicio beginTransationPDO ----------------------------------------
Inicia una transacción, desactivando "autocommit" si es que estuviese activado
Es la primara etapa del tratamiento de las transaciones, de tal modo que que si 
no se ejecuta un "commit()" no se salvarán los cambios en las tablas de la BBDD.

Si existe una transación inciada beginTransationPDO, cuando el script finaliza 
o cuando una conexión está a punto de ser cerrada, si existe una transacción 
pendiente, PDO la revertirá automáticamente. 
Esto es una medida de seguridad que ayuda a evitar inconsistencia en los casos 
donde el script finaliza inesperadamente (si no se consignó la transacción, 
se asume que algo salió mal, con lo cual se realiza la reversión para la 
seguridad de los datos).
Advertencia: La reversión automática solamente ocurre si se inicia una 
transacción a través de PDO::beginTransaction(). 

NOTA: en MySQL esto es válido para las tablas InnoDB, pero no en el caso de 
las tablas MyISAM, que no siempre serán "autocommit"
------------------------------------------------------------------------------*/
function beginTransationPDO($conexionDB) 
{
	//echo "<br /><br />0 transationPDO.php:beginTransationPDO:conexionDB: ";var_dump($conexionDB); 
	
	//$arrBeginTransation = array();
	$arrBeginTransation['nomScript'] = "transationPDO.php";
	$arrBeginTransation['nomFuncion'] = "beginTransationPDO";
	$arrBeginTransation['codError'] = "00000";
	$arrBeginTransation['errno'] = "";
	$arrBeginTransation['errorMensaje'] = "";
	
	//echo "<br /><br />1 transationPDO.php:beginTransationPDO:Drivers de PDO DDBB instalados:";	var_dump(PDO::getAvailableDrivers());
	
	try {			
		
			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	   
						//echo '<br><br>1 transationPDO.php:beginTransationPDO:conexionDB: ';var_dump($conexionDB);echo '<br>';
						throw new Exception("Sin conexión BD");
			}  					
		
 		/* Iniciar una transacción, desactivando 'autocommit' */
			
			$conexionDB->beginTransaction();//Devuelve TRUE en caso de éxito o FALSE en caso de error.
			
   $arrBeginTransation['conexionLink'] = $conexionDB;
			
			//echo "<br /><br />2-1 transationPDO.php:beginTransationPDO:conexionDB: ";var_dump($conexionDB);			
			//echo "<br /><br />2-2 transationPDO.php:beginTransationPDO:arrBeginTransation: ";var_dump($arrBeginTransation);		 

	}
	catch (PDOException $e) {
	
			$arrBeginTransation['codError'] = "70501";
   $arrBeginTransation['errno'] = $e->getCode();
			$arrBeginTransation['errorMensaje'] = 'Error PDO en el sistema, no se ha podido iniciar la transación, codError:'.$arrBeginTransation['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />3-1 modelos/BBDD/MuSQL/transationPDO.php:beginTransationPDO:arrBeginTransation[errorMensaje ]: ".arrBeginTransation['errorMensaje'];	
	}
	catch (Exception $e) {
			
			$arrBeginTransation['codError'] = "70501";
   $arrBeginTransation['errno'] = $e->getCode();   		
			$arrBeginTransation['errorMensaje'] = 'Error del sistema, no PDO, no se ha podido iniciar la transación, codError:'.$arrBeginTransation['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />4-1 modelos/BBDD/MuSQL/transationPDO.php:beginTransationPDO:arrBeginTransation[errorMensaje ]: ".arrBeginTransation['errorMensaje'];	
	}		
	
	//echo "<br /><br />5 transationPDO.php:beginTransationPDO:conexionLinkDB."; var_dump($conexionLinkDB);
 
	//echo "<br /><br />6 transationPDO.php:beginTransationPDO:arrBeginTransation: "; var_dump($arrBeginTransation);echo "<br><br>";		
	
 return $arrBeginTransation;
}
//----------- Fin beginTransationPDO -------------------------------------------


/*----------- Inicio commitPDO -------------------------------------------------
Activa el 'autocommit' y guarda definitivamente en la BBDD las modificaciones 
realizadas desde el último beginTransationPDO()
Sólo se debe realizar si todas las validaciones y demás operaciones han sido 
correctas. Y ya no será posible la reversión ni con rollback.
------------------------------------------------------------------------------*/
function commitPDO($conexionDB) //Recibe conexion['conexionLink']
{	
	//echo "<br /><br />0 transationPDO.php:commitPDO:conexionDB: ";var_dump($conexionDB);

 //$arrCommit = array();	
	$arrCommit['nomScript'] = "transationPDO.php";	
 $arrCommit['nomFuncion'] = "commitPDO";
	$arrCommit['codError'] = "00000";
	$arrCommit['errno'] = "";
	$arrCommit['errorMensaje'] = "";
	
	//echo "<br /><br />1 transationPDO.php:commitPDO:Drivers de PDO DDBB instalados:";	print_r(PDO::getAvailableDrivers());
	
	try {	
	
			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	   
						//echo '<br><br>1 transationPDO.php:commitPDO:conexionDB: ';var_dump($conexionDB);echo '<br>';
						throw new Exception("Sin conexión BD");
			}  	
					
			$conexionDB->commit();//Devuelve TRUE en caso de éxito o FALSE en caso de error.

   $arrCommit['conexionLink'] = $conexionDB;
				
   //echo "<br /><br />2-1 transationPDO.php:commitPDO:conexionDB: ";var_dump($conexionDB);			
			//echo "<br /><br />2-2 transationPDO.php:commitPDO:arrCommit: ";var_dump($arrCommit);
	}
	catch (PDOException $e) {

			$arrCommit['codError'] = '70502'; 
   $arrCommit['errno'] = $e->getCode();			
			$arrCommit['errorMensaje'] = 'Error PDO en el sistema, no se ha podido hacer commit, codError:'.$arrCommit['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />3-1 modelos/BBDD/MuSQL/transationPDO.php:commitPDO:arrCommit[errorMensaje ]:: ".$arrCommit['errorMensaje'];	
	}
	catch (Exception $e) {

			$arrCommit['codError'] = '70502'; 
   $arrCommit['errno'] = $e->getCode();
			$arrCommit['errorMensaje'] = 'Error en el sistema, no PDO, no se ha podido hacer commit, codError:'.$arrCommit['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />4-1 modelos/BBDD/MuSQL/transationPDO.php:commitPDO:arrCommit[errorMensaje ]: ".$arrCommit['errorMensaje'];
	}	
 
	//echo "<br /><br />5 transationPDO.php:commitPDO:arrCommit: ";var_dump($arrCommit);
	
	return $arrCommit;
}	
//----------- Fin commitPDO ----------------------------------------------------


/*----------- Inicio rollbackPDO -----------------------------------------------
Se ignoran todas las modificaciones realizadas en la BBDD y se deja como estaba 
en el momento anterior de hacer beginTransationPDO() en: 'autocommit'.
Sólo se debe ejecutar si se ha producido algún error que de lugar a 
inconsistencias en la BBDD.

Si existe una transación inciada beginTransationPDO, cuando el script finaliza 
o cuando una conexión está a punto de ser cerrada, si existe una transacción 
pendiente, PDO también la revertirá automáticamente. 

NOTA: en MySQL esto es válido para las tablas InnoDB, pero no en el caso de 
las tablas MyISAM, que no siempre serán "autocommit" y no se podrán revertir.
------------------------------------------------------------------------------*/
function rollbackPDO($conexionDB) //Recibe conexion['conexionLink']
{	
	//echo "<br><br>0-1 transationPDO.php:rollbackPDO:conexionDB: ";var_dump($conexionDB);

 //$arrRollback = array();	
	$arrRollback['nomScript'] = "transationPDO.php";			
 $arrRollback['nomFuncion'] = "rollbackPDO";
	$arrRollback['codError'] = "00000";
	$arrRollback['errno'] = "";
	$arrRollback['errorMensaje'] = "";
	
	//echo "<br /><br />0-2 transationPDO.php:rollbackPDO:Drivers de PDO DDBB instalados:";	print_r(PDO::getAvailableDrivers());
	
	try {
		
			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	   
						//echo '<br><br>1 transationPDO.php:commitPDO:conexionDB: ';var_dump($conexionDB);echo '<br>';
						throw new Exception("Sin conexión BD");
			}	
			
			$conexionDB->rollback();//Devuelve TRUE en caso de éxito o FALSE en caso de error.
   
			$arrRollback['conexionLink'] = $conexionDB;
			
			//echo "<br /><br />2-1 transationPDO.php:rollbackPDO:conexionDB: ";var_dump($conexionDB);
			//echo "<br /><br />2-2 transationPDO.php:rollbackPDO:$arrRollback: ";var_dump($arrRollback);echo "<br><br>";
	}
	catch (PDOException $e) {
	
			$arrRollback['codError'] = '70503'; 
   $arrRollback['errno'] = $e->getCode();   
			$arrRollback['errorMensaje'] = 'Error PDO en el sistema, no se ha podido hacer deshacer la transación, codError:'.$arrRollback['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />3-1 modelos/BBDD/MuSQL/transationPDO.php:arrRollback:arrRollback[errorMensaje]: ".$arrRollback['errorMensaje'];
	}	
	catch (Exception $e) {
		
			$arrRollback['codError'] = '70503'; 
   $arrRollback['errno'] = $e->getCode();
			$arrRollback['errorMensaje'] = 'Error en el sistema, no PDO, no se ha podido hacer deshacer la transación, codError:'.$arrRollback['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />4-1 modelos/BBDD/MuSQL/transationPDO.php:arrRollback:arrRollback[errorMensaje]: ".$arrRollback['errorMensaje'];
	}	

 //echo "<br /><br />5 conexionTransationMySQL:rollbackPDO:arrRollback: ";var_dump($arrRollback);
	
	return $arrRollback;
}	
//----------- Fin rollbackPDO --------------------------------------------------
?>
