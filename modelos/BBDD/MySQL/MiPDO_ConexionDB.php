<?php
/*-----------------------------------------------------------------------------
FICHERO: MiPDO_ConexionDB.php
VERSION: PHP 5.6.40 y 7.x
DESCRIPCION: Esta clase retorna su instancia mediante una función static.

Singleton o instancia única es un patrón de diseño que permite restringir la 
creación de objetos pertenecientes a una clase o el valor de un tipo a un único objeto.

Su intención consiste en garantizar que una clase solo tenga una "sola" instancia 
y proporcionar un punto de acceso global a ella.

El patrón singleton se implementa creando en nuestra clase un método que crea 
una instancia del objeto solo si todavía no existe alguna. 
Para asegurar que la clase no puede ser instanciada nuevamente se regula el 
alcance del constructor (con modificadores de acceso como protegido o privado).

La instrumentación del patrón puede ser delicada en programas con múltiples hilos 
de ejecución. 
Si dos hilos de ejecución intentan crear la instancia al mismo tiempo y esta no 
existe todavía, solo uno de ellos debe lograr crear el objeto. 
La solución clásica para este problema es utilizar exclusión mutua en el método 
de creación de la clase que implementa el patrón.

Las situaciones más habituales de aplicación de este patrón son aquellas en las 
que dicha clase controla el acceso a un recurso físico único (como puede ser el 
ratón o un archivo abierto en modo exclusivo) o cuando cierto tipo de datos debe 
estar disponible para todos los demás objetos de la aplicación.

El patrón singleton provee una única instancia global gracias a que:
La propia clase es responsable de crear la única instancia.
Permite el acceso global a dicha instancia mediante un método de clase.
Declara el constructor de clase como protegido o privado para que no sea 
instanciable directamente. 
Al estar internamente autoreferenciada, en lenguajes como Java, el recolector de
basura no actúa.
------------------------------------------------------------------------------*/
/*interface DatabaseRunException{} Lo dejo por si más adelante pudiera ponerlo

Then create a new exception class for each of the specific PDO methods 
you would like to handle, that implements the DatabaseRunException interface.

class PDOPrepareException extends PDOException implements DatabaseRunException{}
class PDOExecuteException extends PDOException implements DatabaseRunException{}
class PDOQueryException extends PDOException implements DatabaseRunException{}
*/

class MiPDO_ConexionDB
{		
		protected static $instance;
		protected $pdo;			

		public function __construct($dsn,$username, $password, $options = [])
		{  
		   //echo "<br /><br />0-a MiPDO_ConexionDB.php:_construct:options: ";print_r($options); 								
					
					$default_options = [ PDO::ATTR_EMULATE_PREPARES => false,
																										PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
																										//PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
																								];		
					/* Si el parámetro $options recibido está vacío se sustituye por $default_options,
					   pero si llega con datos se utilizan sólo los valores de $options       
					*/	
					if (isset($options) && !empty($options))
     { $options = array_replace($default_options, $options);
					}
					else
					{ $options = $default_options;
					}	
								
					$this->pdo = new PDO($dsn, $username, $password, $options);						
					
					//---- Lo siguiente para ver valores que de la conexión -----------
					
					//$this->pdo->conexionDB['nomFuncion'] ="MiPDO_ConexionDB";//ok					
					//echo "<br /><br />1-a MiPDO_ConexionDB.php:_construct:this: ";var_dump($this);//ok
					//echo "<br /><br />1-b MiPDO_ConexionDB.php:_construct:this->pdo: ";var_dump($this->pdo);//ok
					//echo "<br /><br />1-c MiPDO_ConexionDB.php:_construct:self::instance: ";var_dump(self::$instance);// ok	
					/*
					$attributes = array("AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS");//estos atributos puede ser distintintos para MySQl, Oracle, Postgres, ...
					foreach ($attributes as $val) 
					{ echo "<br />0-b MiPDO_ConexionDB.php:_construct:PDO::ATTR_val:$val: ";echo $this->getAttribute(constant("PDO::ATTR_$val"));
					} echo "<br />";//$default_options muestra: PDO::ATTR_AUTOCOMMIT: 1 PDO::ATTR_ERRMODE: 2 PDO::ATTR_CASE:0 PDO::ATTR_CLIENT_VERSION:5.5.62 PDO::ATTR_CONNECTION_STATUS:Localhost via UNIX socket 						
     */											
		}								
		
		/*---------------- Inicio instance() -----------------------------------------						
					El método static instance() instancia un objeto de la clase MiPDO_ConexionDB.
					Y aunque se use el método otra vez para intentar crear otra nueva instancia, 
					no se crearía una nueva, solo se crearía una única instacia de esa clase 
					y así se evita injection al crear clases dinámicamente si es que se necesitan.
					Se necesita una propiedad static para que se mantenga sin cambiar una sola instancia.							
		----------------------------------------------------------------------------*/				
		public static function instance($dsn,$username, $password, $options = [])				
		{   //echo "<br><br>2-a MiPDO_ConexionDB.php:instance(): ";var_dump(self::$instance);echo "<br><br>";		
		
						if (self::$instance === null) {
										
										self::$instance = new self($dsn,$username, $password, $options);
										//echo "<br><br>2-b MiPDO_ConexionDB.php:instance(): ";var_dump(self::$instance);echo "<br>";						
						}
						//echo "<br><br>2-c MiPDO_ConexionDB.php:instance(): ";var_dump(self::$instance);echo "<br>";	
						
						return self::$instance;								
		}
		
		/*---------------- Fin instance() ------------------------------------------*/
		
		/*------------------- Inicio  __call() ---------------------------------------		
		Es lanzado al invocar un método inaccesible en un contexto de objeto. 
		Si no se puede invocar a ese método se lanza un excepción, de esta forma se 
		asegura que se puede llamar a ese método
		
		Necesario para esta clase
		----------------------------------------------------------------------------*/		
		public function __call($method, $args)
		{  
					//echo "<br><br>3-a MiPDO_ConexionDB.php:__call:method: ";var_dump($method);echo "<br>"; 
				
					if (is_callable([$this->pdo, $method])) {
						
									//echo "<br><br>3-b MiPDO_ConexionDB.php:__call:method: ";var_dump($method);
									//echo "<br><br>3-c MiPDO_ConexionDB.php:__call:args: ";var_dump($args);		

									//php 5.6+ optimization con el nuevo operador variadic (... )
									return $this->pdo->$method(...$args); 
									
									//php <= 5.5 habría que usar: return call_user_func_array(array($this->pdo, $method), $args);
					}
					//echo "<br><br>3-d MiPDO_ConexionDB.php:__call:method: ";var_dump($method);echo "<br>"; 
					
					//En caso de no poder llamar a una función determinada, se lanza una excepción
					throw new Exception ('Error: llamada a método desconocido: '.$method);	
		}
		/*------------------- Fin  __call() ----------------------------------------*/	

}
/* -------------------- FIN class MiPDO_ConexionDB ---------------------------*/

