<?php
/*-----------------------------------------------------------------------------
FICHERO: conexionMySQL.php  
VERSION: PHP 5.2.3 y 7.x
DESCRIPCION: Función que establece conexión a BBDD MySQL llamando al método
             MiPDO_ConexionDB::instance(), para devolver una instancia única
													de la clase MiPDO_ConexionDB. Esta conexión despues se utilizará 
													por las funciones SQL contenidadas en modeloMySQL.php
													
		           El método static instance(), crea una instancia de la conexión a la
													BBDD y aunque se use el método repetidas veces para intentar crear 
													otra nueva instancia, no se crearía una nueva, solo se crearía una 
													única instancia de esa clase. 		
   												
													Controla errores con try ..catch y devuelve códigos de error 
													que yo he personalizado para esta aplicación
													
RECIBE: Los parámetros de conexión de tipo string:
							$serverDB, $usernameDB, $passwordDB, $esquemaDB.
							
DEVUELVE: un array asociativo con cuatro elementos:
       $conexionDB['nomScript'] 
       $conexionDB['nomFuncion']
							$conexionDB['codError'] (número asignado por el programador), 
							$conexionDB['errno'], (numero int de mysql),
							$conexionDB['errorMensaje'], (mensaje error programador) 
							$conexionDB['conexionLink'] (tipo recurso de conexión a BBDD)
							
LLAMA: require_once "./modelos/BBDD/MySQL/MiPDO_ConexionDB.php": para llamar al
       método 	MiPDO_ConexionDB::instance(.....)
LLAMADA: desde muchas funciones, para acceder la la BBDD							
							
OBSERVACIONES: 
Acaso estaría mejor cambiar el nombre conexionMySQL.php y poner conexionPDO.php,
ya que esta conexión se podría utilizar para conectarse a otras BBDD,
pero eso implicaría cambiarlo en muchos lugares, creo que no merece la pena
------------------------------------------------------------------------------*/
function conexionDB($serverDB, $usernameDB, $passwordDB, $esquemaDB) 
{ 
 //echo "<br /><br />0-1 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:serverDB: ";print_r($serverDB);
	//echo "<br /><br />0-2 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:usernameDB: ";print_r($usernameDB);
	//echo "<br /><br />0-3 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:passwordDB: ";print_r($passwordDB);
	//echo "<br /><br />0-4 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:esquemaDB: ";print_r($esquemaDB);
 //echo "<br /><br />0-5 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:Drivers de PDO DDBB instalados:";	print_r(PDO::getAvailableDrivers());		echo "<br />";

 //$conexionDB = array();	
	$conexionDB['nomScript'] = "conexionMySQL.php";		
 $conexionDB['nomFuncion'] = "conexionDB";	
	$conexionDB['codError'] = "00000";
	$conexionDB['errno'] = "";
	$conexionDB['errorMensaje'] = "";
	
	try {			
		
		 if (!isset($serverDB) || empty($serverDB) || !isset($usernameDB) || empty($usernameDB) || !isset($passwordDB) || empty($passwordDB) || !isset($esquemaDB) || empty($esquemaDB) )
		 { 	   
						//echo '<br><br>1-0 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB: ';var_dump($conexionDB);echo '<br>';
						throw new Exception("Error: Faltan datos de conexión a la BBDD");
			}  
			require_once "./modelos/BBDD/MySQL/MiPDO_ConexionDB.php";
			
			$nameDB = $esquemaDB;			
		 $dsn ='mysql:host='.$serverDB.';dbname='.$nameDB;// para prueba $dsn = 'mysql:host=localhost;dbname=europalaica_com_desarrollo;
			
			//echo "<br /><br />1-1 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:dsn: ";	print_r($dsn);		echo "<br />";
		
			$options = [	PDO::ATTR_EMULATE_PREPARES => false,
																PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
																//PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //este mejor ponerlo en las funciones SELECT que se desee
														];		

			$conexionLinkDB = MiPDO_ConexionDB::instance($dsn,$usernameDB,$passwordDB,$options);//Instancia objeto de la clase MiPDO_ConexionDB
			
   //echo "<br /><br />1-2 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:conexionLinkDB: ";var_dump($conexionLinkDB); echo "<br />";	

   //$conexionLinkDBxx = MiPDO_ConexionDB::instance($dsn,$usernameDB,$passwordDB);//No crearía una nueva instacia, porque ya hay una pero no daría error		
			//echo "<br /><br />1-3 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:conexionLinkDBxx: ";var_dump($conexionLinkDBxx); echo "<br />";	
						
			$conexionDB['conexionLink'] = $conexionLinkDB;		
			
			/*------- Inicio Ver algunos valores de la conexión -------------		
			$attributes = array("AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS");//estos atributos puede ser distintintos para MySQl, Oracle, Postgres, ...
			foreach ($attributes as $val) 
			{ echo "<br />2-1 modelos/BBDD.mysql.php:conexionDB:PDO::ATTR_val:$val: ";echo $conexionLinkDB ->getAttribute(constant("PDO::ATTR_$val"));
			} echo "<br />";
   //Muestra: PDO::ATTR_AUTOCOMMIT: 1 PDO::ATTR_ERRMODE: 2 PDO::ATTR_CASE: 0 PDO::ATTR_CLIENT_VERSION: 5.5.62 PDO::ATTR_CONNECTION_STATUS: Localhost via UNIX socket 												
   
			$databases2 = $conexionLinkDB->query('SHOW DATABASES'); //Does not require a prepared statement since it takes no input.
 		$resultado2 = $databases2->fetchAll(PDO::FETCH_ASSOC);
	  echo "<br /><br />2-2 modelos/BBDD.mysql.php:conexionDB:resultado2: ";print_r($resultado2);//muestra: Array([0]=>Array([Database]=>information_schema) [1]=>Array([Database]=>europalaica_com) [2] => Array ( [Database] => europalaica_com_copia ) [3] => Array ( [Database] => europalaica_com_desarrollo ) [4] => Array ( [Database] => europalaica_com_dr ) [5] => Array ( [Database] => europalaica_com_drmadrid ) [6] => Array ( [Database] => europalaica_com_produccion ) [7] => Array ( [Database] => europalaica_com_red ) ) 						
			------- Fin Ver algunos valores de la conexión ------------------*/
	}
	catch (PDOException $e) {
		
			$conexionDB['codError'] = "08000";
   $conexionDB['errno'] = $e->getCode();   
  	$conexionDB['errorMensaje'] = 'Error PDO en el sistema al conectar a MySQL, codError:'.$conexionDB['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />3-1 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:conexionDB[errorMensaje ]: ".conexionDB['errorMensaje'];	  
	}
	catch (Exception $e) {

			$conexionDB['codError'] = "08000";
   $conexionDB['errno'] = $e->getCode();
  	$conexionDB['errorMensaje'] = 'Error, NO PDO, en el sistema al conectar a MySQL, codError:'.$conexionDB['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br /><br />4-1 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:conexionDB[errorMensaje ]: ".conexionDB['errorMensaje'];	 
	}	
		
 //echo "<br><br>5 modelos/BBDD/MySQL/conexionMySQL.php:conexionDB:conexionDB: "; var_dump($conexionDB);echo "<br>";
	
 return $conexionDB;
}
/*----------- Fin conexionDB_MiPDO_ConexiobDB_Instance -----------------------*/

?>
