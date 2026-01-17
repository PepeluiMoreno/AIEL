<?php
/*------------------------------------------------------------------------------------------------
FICHERO: modeloMySQL.php 
VERSION: PHP 7.3.21
DESCRIPCION: Es el modelo base de las funciones de MySQL.
             Mayormente llamadas desde los modelos "modelos" de la aplicación, aunque también
													podría ser llamada desde ortros lugares.
													En este script están las funciones base de consultas, inserciones, actualizaciones
													y borrado de la BBDD MySQL.
													Se utiliza PDO.
													
OBSERVACIONES: Necesita previa conexión a la BBDD que se hace previmente en modelos 
               (o acaso en algún controlador), y que se recibe como parámetro.
															No conviene hacer la llamada a la función de conexión aquí desde esta funciones por posibles problemas con el rollback
															de las transaciones.
------------------------------------------------------------------------------------------------*/

/*------------------------Inicio ejecutarCadSql --------------------------------------------------
PHP: 5.6.4 Y 7.x
En esta función ejecuta una orden SQL: select, update, insert, delete.
Dado que ya existen otras funciones especificas para cada uno de esos casos, esta función 
la utilizo para órdenes select más complejas, y que resultaría más complicado con las otras 								

RECIBE: "$cadSql" parámetro string con la cadena sql ya formada que puede incluir las condiciones
        necesarias. 
        "$conexionDB" link de conexión al recurso de la BBDD, 
								y opcionalmente un array "$arrBindValues".
								Sirve para SELECT, INSER, UPDATE,DELETE.
								
DEVUELVE: un array con el resultado de la consulta, 
          el número de filas implicadas (que puede ser 0, sin que sea un error)
          y los códigos de errores
								
LLAMADA: modeloAdmin.php:eliminarDatosSocioBajas5Anios(), para update con subselect 
modeloAdmin.php:insertarImporteCuotasAnioSiguiente() para insert con select

LLAMA: Métodos PDO	MySQL	y PHP: PDOStatement::bindParamValue			

OBSERVACIONES: Probada HPH.7.3.21, se utiliza pocas veces 
/------------------------------------------------------------------------------------------------*/
function ejecutarCadSql($cadSql,$conexionDB,$arrBindValues = NULL) 
{ 

	 echo '<br><br>0-1 xxxmodeloMySQL.php:ejecutarCadSql:arrayCondiciones:cadSql: ';print_r($cadSql);	
		echo '<br><br>0-2 modeloMySQL.php:ejecutarCadSql:conexionDB: ';var_dump($conexionDB);
		echo '<br><br>0-3 modeloMySQL.php:ejecutarCadSql:arrBindValues: ';var_dump($arrBindValues);

		$arrResultado = array();
		$arrResultado['nomScript'] = "modeloMySQL.php";	
		$arrResultado['nomFuncion'] = "ejecutarCadSql";
		$arrResultado['codError'] = '00000';
		$arrResultado['errorMensaje'] = '';
		$arrResultado['numFilas'] = '-1'; 

	try {		
			
			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	
						//echo '<br/><br/>0-4 modeloMySQL.php:ejecutarCadSql:conexionDB: ';var_dump($conexionDB);echo '<br/>';
						throw new Exception("Sin conexión BD");//Disparo esta excepción porque no la captura PDO, al no estar instanciada $conexionDB
			}  		
			
			$sentencia = $conexionDB->prepare($cadSql);// Se crea un objeto PDOStatement.				
			//echo '<br/><br/>1-1 modeloMySQL.php:ejecutarCadSql:sentencia: ';print_r($sentencia);
							
			if (!isset($arrBindValues) || empty($arrBindValues) || $arrBindValues == NULL)
			{ //echo '<br/><br/>2-1 modeloMySQL.php:ejecutarCadSql:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute();//Se ejecuta sentencia sin $arrBindValues porque no son necesarios para la select: no hay condiciones
			}
			else
			{	//echo '<br/><br/>2-2 modeloMySQL.php:ejecutarCadSql:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute($arrBindValues);//Se ejecuta esta sentencia con $arrBindValues con las condiciones existentes
			}	
			
			//echo '<br/><br/>3-1 modeloMySQL.php:ejecutarCadSql:sentencia: ';print_r($sentencia);
			
			$arrResultado['numFilas'] = $sentencia->rowCount(); 
			
			$sentencia->closeCursor(); //libera la conexión al servidor de BBDD, por lo que otras sentencias SQL podrían ejecutarse, pero deja la sentencia en un estado que la habilita para ser ejecutada otra vez.		
			
	} 
	catch (PDOException $e) {
		
				$arrResultado['codError'] = '70100'; 		
				$arrResultado['errno'] = $e->getCode();				
			 $arrResultado['errorMensaje'] = 'Error PDO al ejecutar la SQL '.$cadSql.', codError:'.$arrResultado['codError'].
				', Error MySQL, Código PDO: '.$e->getCode().', Script: '.$e->getFile().', Línea: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>5-1 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>5-2 modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena								
	}
	catch (Exception $e) {
			
				$arrResultado['codError'] = '70200'; 				
			 $arrResultado['errorMensaje'] = 'Error NO PDO en el sistema al ejecutar la SQL '.$cadSql.', codError:'.$arrResultado['codError'].
				', Script: '.$e->getFile().', Línea: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>6-1 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>6-2 modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena
	}	
		
	//echo '<br><br>7 modeloMySQL.php:ejecutarCadSql:arrResultado: ';print_r($arrResultado);			

	$sentencia = null;	// Se libera el recurso.					

	return $arrResultado;
}
/*------------------------------ Fin ejecutarCadSql  --------------------------------------------*/

/*------------ Inicio buscarCadSql ---------------------------------------------------------------
PHP: 5.6.4 Y 7.x

En esta función ejecuta una SELECT mediante driver PDO para mysql. 
Si la consulta contiene "$arrBindValues" para ejecutarla utiliza "sentencia->execute($arrBindValues)",
y si no lo tiene por no necesitarlo o ya venir incluidas en la "$cadSql" ejecutará "$sentencia->execute()"
Incluye el tratamiento de errores.

RECIBE: "$cadSql" parámetro string con la cadena sql ya formada, 
        "$conexionDB" link de conexión al recurso de la BBDD, 
								y opcionalmente un array "$arrBindValues" 
								
DEVUELVE: "$arrResultado" array con el resultado de la búsqueda que incluye:
          "$arrResultado['resultadoFilas']": array asociativo con el contenido de la consulta 
										en las filas ordenadas desde "0" hasta "numFilas"
										Si no encuentra ninguna coincidencia, "$arrResultado['numFilas']" = 0; 
										si hay error "$arrResultado['numFilas']" = -1
										Además los códigos de errores y mensaje de error				

LLAMADA: Desde muchas funciones especialmente modelos en procesos 
LLAMA: Métodos PDO	MySQL	y PHP: PDOStatement::bindParamValue										
									
OBSERVACIONES: Utilizada frecuentemente

El método fetch(PDO::FETCH_ASSOC)) devolverá fila a fila la consulta y después se agrupan en una sola 
matriz asociativa, consume menos memoria que	con fetchAll(PDO::FETCH_ASSOC) que pone todas las filas 
de la consulta en la matriz de un golpe										
	
OJO: PDO PDOStatement::rowCount(), dicen que no funciona bien con MySQL, por eso prefiero 
$sentencia->fetch(PDO::FETCH_ASSOC) y contar el número de filas $f del array, 
o también con la función de contar arrays: count($resulSentencia)
-------------------------------------------------------------------------------------------------*/
function buscarCadSql($cadSql,$conexionDB,$arrBindValues = NULL) { 
	
	//echo '<br/><br/>0-1 modeloMySQL.php:buscarCadSql:cadSql: '.$cadSql;
 //echo '<br/><br/>0-2 modeloMySQL.php.php:buscarCadSql:conexionDB: ';var_dump($conexionDB);
	//echo '<br/><br/>0-3 modeloMySQL.php:buscarCadSql:arrBindValues: ';var_dump($arrBindValues);echo '<br/>';

	$arrResultado = array();
	$arrResultado['nomScript'] = "modeloMySQL.php";	
	$arrResultado['nomFuncion'] = "buscarCadSql";
 $arrResultado['codError'] = '00000';
	$arrResultado['errno'] = '';
 $arrResultado['errorMensaje'] = '';
	$arrResultado['numFilas'] = -1;	
	$arrResultado['resultadoFilas'] = NULL; 	
		
	$resultadoFilasAux = array();
	$resulFilasAux = array();

	try {		
			
			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	//posiblidad de poner aquí conexionDB()
						//echo '<br/><br/>0-4 modeloMySQL.php:buscarCadSql:conexionDB: ';var_dump($conexionDB);echo '<br/>';
						throw new Exception("Sin conexión BD");//Disparo esta excepción porque no la captura PDO, al no estar instanciada $conexionD
			}  		
			
			$sentencia = $conexionDB->prepare($cadSql);//Se crea un objeto PDOStatement.				
			//echo '<br/><br/>1-1 modeloMySQL.php:buscarCadSql:sentencia: ';print_r($sentencia);
							
			if (!isset($arrBindValues) || empty($arrBindValues) || $arrBindValues == NULL)
			{ //echo '<br/><br/>2-1 modeloMySQL.php:buscarCadSql:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute();//Se ejecuta sentencia sin $arrBindValues porque no es necesario para la select: no hay condiciones en o ya están incluidas en la "$cadSql" 
			}
			else
			{	//echo '<br/><br/>2-2 modeloMySQL.php:buscarCadSql:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute($arrBindValues);//Se ejecuta esta sentencia con $arrBindValues con las condiciones existentes
			}	
			
			//echo '<br/><br/>3-1a modeloMySQL.php:buscarCadSql:sentencia: ';print_r($sentencia);
		
			/*--- Inicio recupera los resultados fila a fila y tratándolos después ----
					OJO: dicen que esta opción gestiona mejor los recursos 
			------------------------------------------------------------------------------*/
			$f = 0;
			$resulFilasAux = array();	
			
			while ($fila = $sentencia->fetch(PDO::FETCH_ASSOC))//Se recuperan los resultados fila a fila en una matriz
			{					
					foreach ($fila as $indice => $contenido)
					{						
								$resulFilasAux[$f][$indice] = utf8_encode($contenido);
					}
					$f++;
			}
			//echo "<br><br>3-2a modeloMySQL.php:buscarCadSql:filas:f:  ".$f;
			$arrResultado['numFilas'] = $f;
			
			/*---- Fin recuperar los resultados fila a fila y tratándolos después -----*/
			
			/*--- Inicio Recuperando todos los datos de un golpe en una matriz ---------
					y tratándolos después: OJO esta opción dicen podría implicar más recursos		
			----------------------------------------------------------------------------		
			$resulSentencia = $sentencia->fetchAll(PDO::FETCH_ASSOC);//Se recuperan todos los datos de un golpe en una matriz esto podría implicar mucha memoria
			//echo '<br><br>3-1b modeloMySQL.php:buscarEnTablas:resulSentencia: ';print_r($resulSentencia);
			
			$arrResultado['numFilas'] = count($resulSentencia);		
			$f = 0;
			$resulFilasAux = array();
			while ($f < count($resulSentencia))//Se recuperan los resultados en una matriz
			{	foreach ($resulSentencia[$f] as $indice => $contenido)
					{	$resulFilasAux[$f][$indice] = utf8_encode($contenido);
					}
					$f++;
			}	
			$resulSentencia = NULL;*/			
			/*--------- para comparar número de filas con "count($resulSentencia);" -----		
			$hacerConsultaNumFilas=$conexionDB->prepare("SELECT COUNT(*) FROM $tablasBusqueda $cadenaCondicionesBuscar");
			echo '<br><br>3-2b modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);
			$hacerConsultaNumFilas->execute(); 
			echo '<br><br>3-3b modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);			
			$arrResultado['numFilas'] = $hacerConsultaNumFilas->fetchColumn();			
			echo '<br><br>3-4b modeloMySQL:buscarEnTablas:arrResultado: ';print_r($arrResultado);	
			----------------------------------------------------------------------------*/
			
			/*------- Fin Recuperando todos los datos de un golpe en una matriz  ------*/
			
			//echo '<br/><br/>4-1 modeloMySQL.php:buscarCadSql:resulFilasAux: ';print_r($resulFilasAux);
			$arrResultado['resultadoFilas'] = $resulFilasAux;		

			$sentencia->closeCursor(); //libera la conexión al servidor de BBDD, por lo que otras sentencias SQL podrían ejecutarse, pero deja la sentencia en un estado que la habilita para ser ejecutada otra vez.		
			$sentencia = null;	// Se libera el recurso.			
			$resulFilasAux = null;
			$fila = null;
 } 
	catch (PDOException $e) {
	 
			$arrResultado['codError'] = '70100';
			$arrResultado['errno'] = $e->getCode();			
			$arrResultado['errorMensaje'] = 'Error PDO en el sistema al buscar en tabla/s, codError:'.$arrResultado['codError'].
			', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br><br>5a modeloMySQL.php:buscarCadSql:errorMensaje: ".$arrResultado['errorMensaje'];
			//echo "<br><br>5b modeloMySQL.php:buscarCadSql:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena									
	}
	catch (Exception $e) {
			
			$arrResultado['codError'] = '70100';
			$arrResultado['errorMensaje'] = 'Error NO PDO en el sistema al buscar en tabla/s, codError:'.$arrResultado['codError'].
				', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
			//echo "<br><br>6a modeloMySQL.php:buscarCadSql:errorMensaje: ".$arrResultado['errorMensaje'];
			//echo "<br><br>6b modeloMySQL.php:buscarCadSql:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede ser demasiada información en la cadena					
	}		

	//echo '<br/><br/>7 modeloMySQL.php:buscarCadSql:arrResultado: ';print_r($arrResultado);	

	return 	$arrResultado;
}
/*---------------- Fin  buscarCadSql ------------------------------------------------------------*/


/*------------------------Inicio insertarUnaFila -------------------------------------------------
PHP: 5.6.4 Y 7.x

En esta función ejecuta una INSERT de "sólo una fila" en una tabla mediante PDO y método bindValue()
Controla PDOException, y Exception.

RECIBE: "$tablaInsercion" recibida como string, 
        "$arrayCamposValoresInsercion" array asociativo de campos de la tabla a insertar
								y los valores correspondientes a esos de los campos a insertar
        "$conexionLinkDB" un link de conexión PDO a la BBDD
DEVUEVE: un array con el resultado de la búsqueda, el número de filas y los códigos de errores.
									
LLAMADA: Desde muchas funciones especialmente modelos en procesos de alta, baja, de socios, 
         cierre año, cobros cuotas.
LLAMA: Métodos PDO	MySQL	y PHP: PDOStatement::bindParamValue

OBSERVACIONES: No es necesario cambiar ninguna de la llamadas anteriores a PDO para esta 
nueva función, ya que a partir del array "$arrayCamposValoresInsercion" se forma el string 
"$cadenaCamposInsercion", con los nombres de las campos a insertar. Por ejemplo para la tabla USUARIO
sería ( CODUSER, PASSUSUARIO, USUARIO, ESTADO) y el string "$bindCadenaCamposInsercion" para VALUES
 con los nombres de los :bindParamValue	separados por comas (,)	de modo que VALUES en esa tabla 
sería( :CODUSER, :PASSUSUARIO, :USUARIO, :ESTADO)

Agustín: 2020-02-02
Nota: la posibilidad de incluir aquí la funcion "conexionDB()" daría problema al deshacer transaciones, 
por lo que tendria que estar antes o al menos en las funciones de INSERT, UPDATE, DELETE
-------------------------------------------------------------------------------------------------*/
function insertarUnaFila($tablaInsercion,$arrayCamposValoresInsercion,$conexionLinkDB) {
	
 //echo '<br><br>0-1 modeloMySQL.php:insertarUnaFila:arrayCamposValoresInsercion: ';print_r($arrayCamposValoresInsercion); 
	//echo '<br><br>0-2 modeloMySQL.php:insertarUnaFila:conexionLinkDB: ';var_dump($conexionLinkDB); echo '<br />';	
	
	$arrResultado = array();
	$arrResultado['nomScript'] = "modeloMySQL.php";
	$arrResultado['nomFuncion'] = "insertarUnaFila";
	$arrResultado['codError'] = '00000';
	$arrResultado['errno'] = "";
	$arrResultado['errorMensaje'] = '';
	$arrResultado['numFilas'] = -1;	
	
	$arrBindValues = array();	

	try {	
	
			if (!isset($arrayCamposValoresInsercion) || empty($arrayCamposValoresInsercion) || $arrayCamposValoresInsercion == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>1-1 modeloMySQL.php:insertarUnaFila: Falta parámetro arrayCamposValoresInsercion: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:insertarUnaFila(): Falta parámetro arrayCamposValoresInsercion:");//Disparo esta excepción porque podría no capturarla PDO, y hacer un insert con valor 0 
			} 	

			if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
			{ 	   
						//echo '<br><br>1-2 modeloMySQL.php:insertarUnaFila:conexionLinkDB: ';var_dump($conexionLinkDB);echo '<br>';
						throw new Exception("Error: modeloMySQL.php:insertarUnaFila():Sin conexión a BBDD: falta parámetro conexionDB.");//Disparo esta excepción porque no la captura PDO, al no estar instanciada $conexionDB
			} 

			$cadenaCamposInsercion = '';
			$bindCadenaCamposInsercion = '';
			
			// foreach para poder formar $cadSql con PHP: PDOStatement::bindParamValue
			foreach ($arrayCamposValoresInsercion  as $indice =>$contenido)
			{
				$cadenaCamposInsercion     = $cadenaCamposInsercion.','.$indice;
				$bindCadenaCamposInsercion = $bindCadenaCamposInsercion.', :'.$indice;	
				
				if (isset($contenido) && !empty($contenido)) //Es util el if	???
				{	
				  //$cadenaCamposInsercion     = $cadenaCamposInsercion.','.$indice;
				  //$bindCadenaCamposInsercion = $bindCadenaCamposInsercion.', :'.$indice;	
				  
				  $arrBindValues[':'.$indice] = utf8_decode($contenido);
				}
				else
				{	//$arrBindValues[':'.$indice] = NULL;//es util el NULL?
			   $arrBindValues[':'.$indice] ='';//es util el NULL?
				}
			}
											
			$cadenaCamposInsercion      = ltrim($cadenaCamposInsercion, ",");//$cadenaCamposInsercion, los campos a insertar por ejemplo para la tabla USUARIO sería ( CODUSER, PASSUSUARIO, USUARIO, ESTADO); 
			$bindCadenaCamposInsercion  = ltrim($bindCadenaCamposInsercion, ",");//$bindCadenaCamposInsercion para la tabla USUARIO sería ":CODUSER, :PASSUSUARIO, :USUARIO, :ESTADO"
			
			//echo '<br><br>1-2 modeloMySQL.php:insertarUnaFila:cadenaCamposInsercion: ';print_r($cadenaCamposInsercion); 
			//echo '<br><br>1-3 modeloMySQL.php:insertarUnaFila:bindCadenaCamposInsercion: ';print_r($bindCadenaCamposInsercion); echo '<br />';
			//echo '<br><br>1-4 modeloMySQL.php:insertarUnaFila:arrBindValues: ';print_r($arrBindValues); echo '<br />';						
						
			$cadSql = "INSERT INTO $tablaInsercion ($cadenaCamposInsercion) VALUES ($bindCadenaCamposInsercion)";
			//Para tabla USUARIO el string $cadSql sería: INSERT INTO USUARIO (CODUSER,PASSUSUARIO,USUARIO,ESTADO) VALUES ( :CODUSER, :PASSUSUARIO, :USUARIO, :ESTADO);	

			//echo '<br><br>2-1 modeloMySQL.php:insertarUnaFila:cadSql: '.$cadSql.'<br />';				
			
			$sentencia = $conexionLinkDB->prepare($cadSql);//Se crea un objeto PDOStatement.
			
			//echo '<br><br>2-2  modeloMySQL.php:insertarUnaFila:sentencia: ';print_r($sentencia);

			$sentencia->execute($arrBindValues);//Se ejecuta la sentencia.
			
			//echo '<br><br>2-3 modeloMySQL.php:insertarUnaFila:sentencia: ';print_r($sentencia);				
			//echo '<br><br>2-4 modeloMySQL.php:insertarUnaFila:pdo-número de registros: '.$sentencia->rowCount();
			//La función rowCount() va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD??
			
			/* Otra opción alternativa que también sirve: 
			foreach ($arrayCamposValoresInsercion  as $indice =>$contenido)
			{$sentencia->bindValue(':'.$indice, utf8_decode($contenido));
			}							
			$sentencia->execute(); // Se ejecuta la sentencia.
			*/			
					
			$arrResultado['numFilas'] = $sentencia->rowCount(); 
			
			$sentencia->closeCursor(); // Se libera el recurso.
			//$sentencia = null;	
 } 
	catch (PDOException $e) {
	 
			$arrResultado['codError'] = '70200'; 		
			$arrResultado['errno'] = $e->getCode();			
			$arrResultado['errorMensaje'] = 'Error PDO , no se ha podido insertar una fila en tabla: '.$tablaInsercion.', codError:'.$arrResultado['codError'].
			', Error MySQL, Código PDO: '.$e->getCode().', Script: '.$e->getFile().', Línea: '.$e->getLine().
			', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br><br>3-1 modeloMySQL.php:insertarUnaFila:errorMensaje: ".$arrResultado['errorMensaje'];
			//echo "<br><br>3-2 modeloMySQL.php:insertarUnaFila:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}
	catch (Exception $e) {
			
			$arrResultado['codError'] = '70200'; 				
			//$arrResultado['errno'] = $e->getCode();			siempre devolverá 0
			$arrResultado['errorMensaje'] = 'Error NO PDO en el sistema, no se ha podido insertar una fila en tabla: '.$tablaInsercion.', codError:'.$arrResultado['codError'].
			', Script: '.$e->getFile().', Línea: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
			
			//echo "<br><br>4-1 modeloMySQL.php:insertarUnaFila:errorMensaje: ".$arrResultado['errorMensaje'];
			//echo "<br><br>4-2 modeloMySQL.php:insertarUnaFila:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}	

	//echo "<br><br>5 modelos/BBDD/mysql.php:modeloMySQL.php:insertarUnaFila:arrResultado: ";print_r($arrResultado);
	
	return $arrResultado;
}
/*----------------------------- Fin insertarUnaFila ---------------------------------------------*/


/*----------------------------- Inicio borrarFilas -------------------------------------------------
PHP: 5.6.4 Y 7.x

En esta función ejecuta una query borra una o varias filas en una tabla, 
según la "$cadenaCondiciones" que es un string, por ejemplo: $cadenaCondiciones = "CODUSER = $codUser"; 

Controla PDOException, y Exception.			

RECIBE: "$tablaBorrar" recibida como string, tabla de la que se borran las filas, 
        "$cadenaCondiciones" que es un string, por ejemplo: $cadenaCondiciones = "CODUSER :codUser" o 
								"CODUSER = :codUser AND CODROL = :codRol"
        "$conexionDB" un link de conexión PDO a la BBDD
								
DEVUEVE:  un array con los controles de errores, y número de filas borradas
Si no se borra ninguna fila, envía ['codError']='80001'		

LLAMADA: Desde varias funciones:
									modeloSocios.php:eliminarDatosSocios(),eliminarSocioConfirmar(),									
									modeloUsuarios.php:eliminarUsuarioTieneRol(),									
									modeloPresCoord.php:bajaSocioFallecido(),eliminarCoordinacionAreaGestion(),									
									modeloTesorero.php:actualizarIngresoCuotaAnio(),mEliminarOrdenesCobroUnaRemesa()									
									modeloAdmin.php:borrarFilaCuotaSocioAnioNuevoBaja(),eliminarDatosSocioBajas5Anios(),actualizarTablaControles()
									
LLAMA:Métodos PDO	MySQL, probada	PHP 7.3.21
	
OBSERVACIONES: No es necesario cambiar ninguna de la llamadas anteriores a PDO para esta 
nueva función
	
Agustín: 2020-02-02
---------------------------------------------------------------------------------------------------*/

function borrarFilas($tablaBorrar,$cadenaCondiciones,$conexionDB,$arrBindValues = NULL) {
//acaso mejor renombrar con eliminarFilas()	
	
	//echo '<br><br>0-1 modeloMySQL.php:borrarFilas:cadenaCondiciones: ';print_r($cadenaCondiciones);echo '<br />';	
	//echo '<br><br>0-2 modeloMySQL.php:borrarFilas:conexionDB: ';var_dump($conexionDB); echo '<br />';	
 //echo '<br><br>0-3 modeloMySQL.php:borrarFilas:arrBindValues: ';print_r($arrBindValues);echo '<br />';	 
		
	$arrResultado = array();	
	$arrResultado['nomScript'] = "modeloMySQL.php";
	$arrResultado['nomFuncion'] = "borrarFilas";
	$arrResultado['codError'] = '00000';
	$arrResultado['errno'] = "";
	$arrResultado['errorMensaje'] = '';
	$arrResultado['numFilas'] = -1;		

	try {			

	 	if (!isset($cadenaCondiciones) || empty($cadenaCondiciones) || $cadenaCondiciones == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>0-3 modeloMySQL.php:borrarFilas: Falta parámetro cadenaCondiciones: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:borrarFilas(): Falta parámetro cadenaCondiciones:");//Disparo esta excepción porque podría no capturarla PDO, 
			} 	

			if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
			{ 	   
						//echo '<br><br>0-4 modeloMySQL.php:borrarFilas:conexionDB: ';var_dump($conexionDB);echo '<br>';
						throw new Exception("Error: modeloMySQL.php:borrarFilas():Sin conexión a BBDD: falta parámetro conexionDB.");
			}  			
			
			$cadSql = "DELETE FROM $tablaBorrar WHERE $cadenaCondiciones";
													
			//echo '<br><br>1- modeloMySQL.php:borrarFilas:$cadSql: ',$cadSql,'<br />';				
			
			$sentencia = $conexionDB->prepare($cadSql);// Se crea un objeto PDOStatement.
			
			//echo '<br><br>2-1 modeloMySQL.php:borrarFilas:sentencia: ';print_r($sentencia);
			
			/*Si $cadenaCondicionesBuscar no incluye :arrBindValues, 
					pero en el caso de que incluya lo pone = 0, y puede dar valores no deseados
					por ejemplo en buscarRolFuncion($codRol) que le sería igual a poner $codRol = 0
					No debiera existir codRol = 0 en tabla ROL de BBDD (cambiar a 01??), ni equivalentes para evitar esos errores
			*/
			if ( !isset($arrBindValues) || empty($arrBindValues) || $arrBindValues == NULL)
			{ //echo '<br><br>2-2 modeloMySQL.php:borrarFilas:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute();//Se ejecuta sentencia sin $arrBindValues 
			}
			else
			{	//echo '<br><br>2-3 modeloMySQL.php:borrarFilas:arrBindValues: ';print_r($arrBindValues);
					$sentencia->execute($arrBindValues);//Se ejecuta esta sentencia con $arrBindValues
			}				
				
			//echo '<br><br>2-4 modeloMySQL.php:borrarFilas:sentencia: ';print_r($sentencia);				
			//echo '<br><br>2-5 modeloMySQL.php:borrarFilas:pdo-número de registros: '.$sentencia->rowCount();
			
			$arrResultado['numFilas'] = $sentencia->rowCount();//Esta función va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD 
			/*
			if ($arrResultado['numFilas'] !== 0) 
			{ $arrResultado['codError'] = '00000';
					//$arrResultado['numFilas'] = $arrResultado['numFilas'];
			} 
			else // ==0 no esnecesariamente un error si no se cumple esa condición
			{ $arrResultado['codError'] = '80001'; 
					$arrResultado['errorMensaje'] = 'No se han encontrado filas para borrar en la tabla: '.$tablaBorrar; 
					//$arrResultado['numFilas'] = $arrResultado['numFilas'];
			}		
   */			
			//echo '<br><br>2-6 modeloMySQL.php:borrarFilas:arrResultado: ';print_r($arrResultado);	
			
			$sentencia->closeCursor(); // Se libera el recurso.
 } 
	catch (PDOException $e) {
	 
				$arrResultado['codError'] = '70400'; 		
			 $arrResultado['errno'] = $e->getCode();						
			 $arrResultado['errorMensaje'] = 'Error PDO en el sistema al borrar filas en tabla/s: '.$tablaBorrar.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>3-1 modeloMySQL.php:borrarFilas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>3-2 modeloMySQL.php:borrarFilas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena				
	}
		catch (Exception $e) {
			
				$arrResultado['codError'] = '70400';
			 $arrResultado['errorMensaje'] = 'Error en el sistema al borrar filas en tabla/s: '.$tablaBorrar.', codError:'.$arrResultado['codError'].
					', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>4-1 modeloMySQL.php:borrarFilas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>4-2 modeloMySQL.php:borrarFilas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena	
	}				
	//echo "<br>4- modeloMySQL.php:borrarFilas:arrResultado";print_r($arrResultado);
	
	return $arrResultado;
}
/*----------------------------- Fin borrarFilas_PDO -----------------------------------------------*/


/*------------------------Inicio actualizarTabla con $arrBindValues () ------------------------------
PHP: 5.6.4 Y 7.x

Esta función ejecuta una UPDATE en una tabla mediante PDO y sentencias "prepare"
y consultas parametrizadas para dificultar las injections. 

Para ello utiliza parámetros tipo $arrBindValues([:APE2]=>cosgaya) 
Se obtiene la cadena de "Campos a Actualizar" y cadena "Campos de Condiciones" 
parametrizadas por ":parametro" a partir de los arrays de entrada correspondientes.  
También se necesita preparar el array asociativo $arrBindValues con valores de
los ":parametro,". 

Ejemplo:
UPDATE MIEMBRO 	SET APE1 = :APE1, APE2 = :APE2, NOM =, :NOM (son los campos a actulizar)  
WHERE CODUSER = :CODUSER (son las condiciones)		
$arrBindValues([:APE2]=>cosgaya, [:APE2]=>ayuela, [:NOM]=>agustin), [:CODUSER]=>83)	
		
Controla excepciones PDOException, y Exception.

RECIBE: "$tablaActualizar" (string), el "$arrayCondiciones" para el WHERE,
        "$arrayCamposValoresActualizar" array asociativo de campos de la tabla y los valores valores
								de los campos a actualizar.
        "$conexionLinkDB" un link de conexión PDO a la BBDD
DEVUEVE: un array con el resultado de la actualización, el número de filas y los códigos de errores
									
LLAMADA: Desde muchas funciones especialmente modelos en procesos de alta, baja de socios, 
         cierre año, cobros cuotas.
LLAMA: Métodos PDO	MySQL	y PHP: PDOStatement::bindParamValue

OBSERVACIONES: Probada PDO y PHP 7.3.21
No es necesario cambiar ninguna de la llamadas a esta función, ya que a partir de los arrays 
se obtiene los parámetros BindValues[:] necesarios

NOTA: No utilizar esta opción cuando haya alguna repeticiones de mismos nombres de campos en 
los "campos a actualizar" y en los "campos de condiciones" en ese caso elegir la 
función ""actualizarTabla_ParamPosicion()
Agustín: 2020-03-16

ACLARACIÓN:
arrResultado['numFilas'] = $sentencia->rowCount() puede ser "0", cuando no se complan las 
condiciones WHERE a partir de "$arrayCondiciones" o bieno porque al asignar los valores de los campos
 con los SET, sean idénticos a los valores que ya tienen esos campos en la tabla "$tablaActualizar" 
	y entonces al no haber cambios rowCount() también devolvería "0" updates.
------------------------------------------------------------------------------*/
function actualizarTabla ($tablaActualizar,$arrayCondiciones,$arrayCamposValoresActualizar,$conexionLinkDB) {
	
  //echo '<br><br>0-1 modeloMySQL.php:actualizarTabla:arrayCondiciones: ';print_r($arrayCondiciones);	
		//echo '<br><br>0-2 modeloMySQL.php:actualizarTabla:arrayCamposValoresActualizar: ';print_r($arrayCamposValoresActualizar);
		//echo '<br><br>0-3 modeloMySQL.php:actualizarTabla:conexionLinkDB: ';var_dump($conexionLinkDB);
		
		$arrResultado = array();
		$arrResultado['nomScript'] = "modeloMySQL.php";	
		$arrResultado['nomFuncion'] = "actualizarTabla";
		$arrResultado['codError'] = '00000';
		$arrResultado['errorMensaje'] = '';
		$arrResultado['numFilas'] = '-1'; 

		$arrBindValues = array();		
	
	try {		
	
	 	if (!isset($arrayCondiciones) || empty($arrayCondiciones) || $arrayCondiciones == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>1-1 modeloMySQL.php:actualizarTabla: Falta parámetro arrayCondiciones: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla(): Falta parámetro arrayCondiciones. ");//Disparo esta excepción porque podría no capturarla PDO, 
			} 	
	 	if (!isset($arrayCamposValoresActualizar) || empty($arrayCamposValoresActualizar) || $arrayCamposValoresActualizar == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>1-2 modeloMySQL.php:actualizarTabla: Falta parámetro arrayCamposValoresActualizar: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla(): Falta parámetro arrayCamposValoresActualizar. ");//Disparo esta excepción porque podría no capturarla PDO, 
			} 				

			if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
			{ 	   
						//echo '<br><br>1-3 modeloMySQL.php:actualizarTabla:conexionLinkDB: ';var_dump($conexionLinkDB);echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla():Sin conexión a BBDD: falta parámetro conexionDB.");
			}  	
	
   /*Ejemplo:	UPDATE MIEMBRO SET APE1 ='cosgaya', APE2 ='ayuela',  NOM = agustin' (son los campos a actualizar)  
														WHERE CODUSER = 83 (son las condiciones)
			$cadSQL =  UPDATE MIEMBRO 	SET APE1 = :APE1, APE2 = :APE2, NOM =, :NOM (son los campos a actualizar)  
			           WHERE CODUSER = :CODUSER (son las condiciones)			
			En el foreach se crea: $cadenaCamposBinActualizar, y el array $arrBindValues([:APE2]=>cosgaya, [:APE2]=>ayuela, [:NOM]=>agustin)  
			para la correspondiente actualización de campos a actualizar	
			*/ 
			$cadenaCamposBinActualizar  = '';

			foreach ($arrayCamposValoresActualizar  as $indice =>$contenido)
			{    			 									
					$cadenaCamposBinActualizar .= ','.$indice. '='.	" :".$indice;					
						
					if (isset($contenido) && !empty($contenido)) //Es util el if	???
					{					  
							$arrBindValues[':'.$indice] = utf8_decode($contenido);
					}
					else
					{	$arrBindValues[':'.$indice] = NULL;	//????acaso mejor quitar esta opción y dejar solo el if sin else y también quitar el campo
					}
			} 
		
			$cadenaCamposBinActualizar = ltrim($cadenaCamposBinActualizar, ",");
			
			//echo '<br><br>2-1 modeloMySQL.php:actualizarTabla:cadenaCamposBinActualizar:',$cadenaCamposBinActualizar; 
			//echo '<br><br>2-2 modeloMySQL.php:actualizarTabla:arrBindValues: ';print_r($arrBindValues);
			
			/*Formación de la cadena WHERE con las condiciones  CODUSER = :CODUSER (son las condiciones)			
			 Se añaden los elementos de las condiciones ([83]), al array anterior ya existente array $$arrBindValues([:APE2]=>cosgaya, [:APE2]=>ayuela, [:NOM]=>agustin )  
			 con lo que quedará: $arrBindValues([:APE2]=>cosgaya, [:APE2]=>ayuela, [:NOM]=>agustin), [:CODUSER]=>83)		
    NOTA: aquí SÍ se podría producir un problema si se repitiese el mismo campo en campos a actualizar y en condiciones (por ejemplo añadir la condición APE1 = :APE1 daría error).	
			*/ 
			$cadenaCondicionesBind = '';
						
			foreach ($arrayCondiciones  as $indice =>$contenido) //añadir if por si es null y quitar campo??
			{ 		  				
					$cadenaCondicionesBind .= $indice.' '.$arrayCondiciones[$indice]['operador'].' '.	':'.$indice.' '.$arrayCondiciones[$indice]['opUnir'].' ';
					
					$arrBindValues[':'.$indice] = $contenido['valorCampo'];//utf8_decode($contenido);//problema que reescriba algún elemento del array al repetirse el mismo campo en dato y en condiciones				
			} 
		
			//echo '<br><br>2-3 modeloMySQL.php:actualizarTabla:cadenaCondicionesBind: ';print_r($cadenaCondicionesBind);
			//echo '<br><br>2-4 modeloMySQL.php:actualizarTabla:arrBindValues: ';print_r($arrBindValues);
			
		
			$cadSql = "UPDATE $tablaActualizar SET $cadenaCamposBinActualizar WHERE $cadenaCondicionesBind";
																														
			//echo '<br>3 modeloMySQL.php:actualizarTabla:cadSql: '.$cadSql;
			
			
			$sentencia = $conexionLinkDB->prepare($cadSql);// Se crea un objeto PDOStatement.
			
			//echo '<br><br>4-1 modeloMySQL.php:actualizarTabla:sentencia: ';print_r($sentencia);

			//$sentencia->execute(); // Se ejecuta la sentencia.
			$sentencia->execute($arrBindValues); //Se ejecuta la sentencia.
			
			//echo '<br><br>4-2 modeloMySQL.php:actualizarTabla:sentencia: ';print_r($sentencia);				
			//echo '<br><br>4-3 modeloMySQL.php:actualizarTabla:pdo-número de registros: '.$sentencia->rowCount();//Esta función va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD
			
			$arrResultado['numFilas'] = $sentencia->rowCount(); 
			
			$sentencia->closeCursor(); // Se libera el recurso.
	} 
	catch (PDOException $e) {
		
				$arrResultado['codError'] = '70100'; 		
				$arrResultado['errno'] = $e->getCode();				
			 $arrResultado['errorMensaje'] = 'Error PDO en el sistema al actualizar filas en tabla: '.$tablaActualizar.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>5_1 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>5-2 modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena								
	}
	catch (Exception $e) {
			
				$arrResultado['codError'] = '70200'; 				
			 $arrResultado['errorMensaje'] = 'Error NO PDO en el sistema al actualizar filas en tabla: '.$tablaActualizar.', codError:'.$arrResultado['codError'].
				', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>6-1 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>6-2 modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena
	}	
		
	//echo '<br><br>7 modeloMySQL.php:actualizarTabla:arrResultado: ';print_r($arrResultado);			

	$sentencia = null;	// Se libera el recurso.		
			
	return $arrResultado;
}
/*--------------------------- Fin actualizarTabla_PDO_conBind --------------------------------------*/

/*------------------------Inicio actualizarTabla_ParamPosicion --------------------------------------
PHP: 5.6.4 Y 7.x

Esta función ejecuta una UPDATE en una tabla mediante PDO y prepare sentencias y consultas 
parametrizadas para dificultar las injections.

Para ello utiliza consultas mediante parámetros de posición (?)  que permite repeticiones de nombres,
mientras que en función modeloMySQL.php:actualizarTabla() con parámetros tipo $arrBindValues([:APE2]=>cosgaya
no permite repeticiones de nombres.  
	
Se obtiene la cadena de "Campos a Actualizar" y cadena "Campos de Condiciones" parametrizadas por posición
También se preparar el array "$arrCamposPosicionValues" los con valores posicionales. 

Ejemplo:
UPDATE CUOTAANIOSOCIO SET ANIOCUOTA = ?,CODCUOTA = ?,IMPORTECUOTAANIOSOCIO = ?
WHERE ANIOCUOTA = ? AND CODSOCIO = ?
$arrPosicionValues([2020],['General'],[50.20],[2020],[83])

Controla excepciones PDOException, y Exception.

RECIBE: "$tablaActualizar" (string), el "$arrayCondiciones" para el WHERE,
        "$arrayCamposValoresActualizar" array asociativo de campos de la tabla y 
								los valores valores de los campos a actualizar.
        "$conexionLinkDB" un link de conexión PDO a la BBDD
DEVUEVE: un array con el resultado de la actualización, el número de filas y los códigos de errores
									
LLAMADA: Desde varias funciones: 
									modeloUsuarios.php:actualizUsuario(),
									modeloSocios.php:actualizCuotaAnioSocio(),
									modeloPrescoord.php:actualizarCoordinacionAreaGestion(),
									modeloTesorero.php:actualizarDonacion(),anularDonacionErronea()
									modeloAdmin.php:cambiarControlModoAdmin(),actualizarCuotasSociosExistentesAnioNuevo(),anularSociosPendientesConfirAdmin(),
									
LLAMA: Métodos PDO	MySQL	y PHP: PDOStatement::bindParamValue

OBSERVACIONES: Probada PDO y PHP /.3.21 
No es necesario cambiar ninguna de la llamadas a esta función, 
ya que a partir de los arrays se obtiene los parámetros BindValues[:] necesarios

NOTA: utilizar esta opción cuando haya repeticiones de nombres en campos a actualizar y en campos condiciones
Agustín: 2020-03-16

ACLARACIÓN:
arrResultado['numFilas'] = $sentencia->rowCount() puede ser "0", cuando no se complan las condiciones 
WHERE a partir de "$arrayCondiciones" o bien o porque asignar los valores de los camps con los SET, 
sean idénticos a los valores que ya tienen esos campos en la tabla "$tablaActualizar" y entonces al 
no haber cambios rowCount() también devolvería "0" updates.
---------------------------------------------------------------------------------------------------*/
function actualizarTabla_ParamPosicion ($tablaActualizar,$arrayCondiciones,$arrayCamposValoresActualizar,$conexionLinkDB) {
	
  //echo '<br><br>0-1 modeloMySQL.php:actualizarTabla_ParamPosicion:arrayCondiciones: ';print_r($arrayCondiciones);	
		//echo '<br><br>0-2 modeloMySQL.php:actualizarTabla_ParamPosicion:arrayCamposValoresActualizar: ';print_r($arrayCamposValoresActualizar);
		//echo '<br><br>0-3 modeloMySQL.php:actualizarTabla_ParamPosicion:conexionLinkDB: ';var_dump($conexionLinkDB);
		
		$arrResultado = array();
		$arrResultado['nomScript'] = "modeloMySQL.php";	
		$arrResultado['nomFuncion'] = "actualizarTabla_ParamPosicion";
		$arrResultado['codError'] = '00000';
		$arrResultado['errorMensaje'] = '';
		$arrResultado['numFilas'] = '-1'; 

		$arrPosicionValues = array();		
	
	try {		
	
	 	if (!isset($arrayCondiciones) || empty($arrayCondiciones) || $arrayCondiciones == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>1-1 modeloMySQL.php:actualizarTabla_ParamPosicion: Falta parámetro arrayCondiciones: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla_ParamPosicion(): Falta parámetro arrayCondiciones. ");//Disparo esta excepción porque podría no capturarla PDO, 
			} 	
	 	if (!isset($arrayCamposValoresActualizar) || empty($arrayCamposValoresActualizar) || $arrayCamposValoresActualizar == NULL)//Para una información más especifica del error 
			{ 	   
						//echo '<br><br>1-2 modeloMySQL.php:actualizarTabla_ParamPosicion: Falta parámetro arrayCamposValoresActualizar: ';echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla_ParamPosicion(): Falta parámetro arrayCamposValoresActualizar. ");//Disparo esta excepción porque podría no capturarla PDO, 
			} 				

			if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
			{ 	   
						//echo '<br><br>1-3 modeloMySQL.php:actualizarTabla_ParamPosicion:conexionLinkDB: ';var_dump($conexionLinkDB);echo '<br>';
						throw new Exception("Error: modeloMySQL.php:actualizarTabla_ParamPosicion():Sin conexión a BBDD: falta parámetro conexionDB.");
			}  	
			/*Ejemplo UPDATE CUOTAANIOSOCIO SET ANIOCUOTA =2020,CODCUOTA ='General',IMPORTECUOTAANIOSOCIO =50.20
                   WHERE ANIOCUOTA = 2020 AND CODSOCIO = 72
			  $cadSql = UPDATE CUOTAANIOSOCIO SET ANIOCUOTA = ?,CODCUOTA = ?,IMPORTECUOTAANIOSOCIO = ?
                   WHERE ANIOCUOTA = ? AND CODSOCIO = ?
			En el foreach se crea: $cadenaCamposPosicionActualizar, y array $arrPosicionValues([2020],['General'],[50.20]) 
			para la correspondiente actualización de campos a actualizar	
			*/ 		
			$cadenaCamposPosicionActualizar = '';

			foreach ($arrayCamposValoresActualizar  as $indice =>$contenido)
			{ 
     $cadenaCamposPosicionActualizar .= ','.$indice. '=  ?';							
						
					if (isset($contenido) && !empty($contenido)) //Es util el if	???
					{	
							$arrCamposPosicionValues[] = utf8_decode($contenido);
					}
					else
					{	$arrCamposPosicionValues[] = NULL;	//????acaso mejor quitar esta opción y dejar solo el if sin else y también quitar el campo
					}
			}			
			$cadenaCamposPosicionActualizar = ltrim($cadenaCamposPosicionActualizar, ",");
			
			
			//echo '<br><br>2-1 modeloMySQL.php:actualizarTabla:$cadenaCamposPosicionActualizar:',$cadenaCamposPosicionActualizar; 
			//echo '<br><br>2-2 modeloMySQL.php:actualizarTabla:arrCamposPosicionValues: ';print_r($arrCamposPosicionValues);
			
			/*En foreach se crea la cadena: WHERE $cadenaCondicionesPosicion con las condiciones ANIOCUOTA = ? AND CODSOCIO = ? 
			 Se añaden los elementos de las condiciones ([2020],[83]), al array anterior ya existente array $arrPosicionValues([2020],['General'],[50.20]) 
			 con lo que quedará: array $arrPosicionValues([2020],['General'],[50.20],[2020],[83]) 
    NOTA: aquí NO se  producirán problemas si se repitiese el mismo campo en campos a actualizar y en condiciones.	
			*/	
			$cadenaCondicionesPosicion = '';
			
			foreach ($arrayCondiciones  as $indice =>$contenido) //?????acaso aquí también if para vaores nulos
			{ 						
					$cadenaCondicionesPosicion .= $indice.' '.$arrayCondiciones[$indice]['operador'].' '.	'? '.$arrayCondiciones[$indice]['opUnir'].' ';					
							
     $arrCamposPosicionValues[] = $contenido['valorCampo'];//se añaden los valores de los condiciones a los ya existente en array $arrCamposPosicionValues[]	
			} 
		
			//echo '<br><br>2-3 modeloMySQL.php:actualizarTabla:cadenaCondicionesPosicion: ';print_r($cadenaCondicionesPosicion);
			//echo '<br><br>2-4 modeloMySQL.php:actualizarTabla:$arrCamposPosicionValues: ';print_r($arrCamposPosicionValues);
			
			$cadSql = "UPDATE $tablaActualizar SET $cadenaCamposPosicionActualizar WHERE $cadenaCondicionesPosicion";
																														
			//echo '<br>3 modeloMySQL.php:actualizarTabla:cadSql: '.$cadSql;
			
			$sentencia = $conexionLinkDB->prepare($cadSql);// Se crea un objeto PDOStatement.
			
			//echo '<br><br>4-1 modeloMySQL.php:actualizarTabla:sentencia: ';print_r($sentencia);
			
			$sentencia->execute($arrCamposPosicionValues);//Se ejecuta sustituyendo los parametros posición (?) por los correspondientes valores de $arrCamposPosicionValues según su posición
			
			//echo '<br><br>4-2 modeloMySQL.php:actualizarTabla: sentencia: ';print_r($sentencia);				
			//echo '<br><br>4-3 modeloMySQL.php:actualizarTabla:pdo-número de registros: '.$sentencia->rowCount();//Esta función va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD
			
			$arrResultado['numFilas'] = $sentencia->rowCount(); 
			
			$sentencia->closeCursor(); // Se libera el recurso.
	} 
	catch (PDOException $e) {
		
				$arrResultado['codError'] = '70100'; 		
				$arrResultado['errno'] = $e->getCode();				
			 $arrResultado['errorMensaje'] = 'Error PDO en el sistema al actualizar filas en tabla: '.$tablaActualizar.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>5 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>5b modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena								
	}
	catch (Exception $e) {
			
				$arrResultado['codError'] = '70200';
			 $arrResultado['errorMensaje'] = 'Error NO PDO en el sistema al actualizar filas en tabla: '.$tablaActualizar.', codError:'.$arrResultado['codError'].
				', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>6-1 modeloMySQL.php:actualizarTabla:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>6-2 modeloMySQL.php:actualizarTabla:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena
	}	
		
	//echo '<br><br>7 modeloMySQL.php:actualizarTabla_ParamPosicion:arrResultado: ';print_r($arrResultado);			

	$sentencia = null;	// Se libera el recurso.		
			
	return $arrResultado;
}
/*--------------------------- Fin actualizarTabla_PDO_conBind -------------------------------------*/





/**************************** INICIO YA NO USADAS *************************************************/

/**************************************************************************************************/

//----------------------------- Inicio  buscarNumFilasNumRows ------------------------
//Se utiliza mysql_num_rows() aunque es más lento que COUNT(*) que es más rápido
//Se utiliza la cadena de consulta como viene, sin eliminar ni sustituir ningún dato
//-----------------------------------------------------------------------------------
function buscarNumFilasNumRows($cadSql,$conexionDB)
{ 	
	$consultaResultado = array();
	$consultaResultado['nomFuncion'] = "buscarNumFilasNumRows";
	$consultaResultado['nomScript'] = "modeloMySQL.php";
	
	$consultaResultado['numFilas'] = -1;	
 $consultaResultado['codError'] = '00000';
	$consultaResultado['errno'] = '';
 $consultaResultado['errorMensaje'] = '';  

 //echo "<br><br>1 modeloMySQL:buscarNumFilasNumRows: cadena select:".$cadSql."<br /><br />";

 $resultQuery = mysql_query($cadSql,$conexionDB);

	if (!$resultQuery)
	{ $consultaResultado['codError']='70100'; 
	  $consultaResultado['errno']=mysql_errno();
   $consultaResultado['errorMensaje']='Error en el sistema, no se ha podido buscar en las tablas. '.
			'Error mysql_query '.mysql_error();
	}
	else
	{$consultaResultado['numFilas']=mysql_num_rows($resultQuery);  
 }
	//echo "<br><br>2 modeloMySQL:buscarNumFilasNumRows:consultaResultado['numFilas']: ";print_r($consultaResultado['numFilas']);
	return 	$consultaResultado;
}
//----------------------------- Fin  buscarNumFilasNumRows ------------------------
//------------------------------ Inicio  buscarNumFilasCount  ------------------------
//Se utiliza COUNT(*) que es más rápido
//Para ello hay que quitar los campos que tenga la cadena de la query, 
//también se quita el ORDER si exite, pues no se necesita y relentiza
//-----------------------------------------------------------------------------------
function buscarNumFilasCount($cadSql,$conexionDB)
{
	$consultaResultado = array();
	$consultaResultado['nomFuncion'] = "buscarNumFilasCount";
	$consultaResultado['nomScript'] ="modeloMySQL.php";
	
	$consultaResultado['numFilas']=-1;	
 $consultaResultado['codError']='00000';
	$consultaResultado['errno']='';
 $consultaResultado['errorMensaje']='';  

 //echo "<br><br>1 modeloMySQL:buscarNumFilasCount: cadena select 'cadSql':".$cadSql."<br />";
	
	$pos1 = stripos($cadSql,"ORDER BY" );//busco la posición de ORDER BY o order by, si existe
	if ($pos1 !== false)
	{
		 $cadSql = substr($cadSql, 0, $pos1);//elimino la parte final ORDER ...
	}
	
 $pos2 = stripos($cadSql,"FROM");//busco la posición de FROM o from
	if ($pos2 === false) //no encontrado: es error, debe existir un from
	{ $consultaResultado['codError'] = '70100';
   $consultaResultado['errorMensaje'] = 'Error en el sistema, formato consulta en tablas incorrecto. ';			
	}	
	else
	{$cadSqlParteUltima = substr($cadSql, $pos2);//elimino la parte primera desde SELECT hasta FROM ..

	 $cadSqlConta = "SELECT COUNT(*) ".$cadSqlParteUltima;	

		//echo "<br><br>2 modeloMySQL:buscarNumFilasCount: cadena select 'cadSqlConta':".$cadSqlConta."<br />";
		
 	$resultQuery = mysql_query($cadSqlConta,$conexionDB);
	 
 	if(!$resultQuery)//Si hay error 
		{$consultaResultado['codError'] = '70100'; 
	  $consultaResultado['errno'] = mysql_errno();
	  $consultaResultado['errorMensaje'] = 'Error en el sistema, no se ha podido buscar en las tablas. '.
			'Error mysql_query '.mysql_error(); 
 	}
 	else
		{
		 $consultaResultado['numFilas'] = mysql_result($resultQuery,0,0);//total de registros
	 }	
	}
	//echo "<br><br>3 modeloMySQL:buscarNumFilasCount:consultaResultado['numFilas']: ";print_r($consultaResultado['numFilas']);
	return 	$consultaResultado;
}
//------------------------------ Fin  buscarNumFilasCount  ------------------------


//------------------------Inicio buscarEnTablas --------------------------------
//Acción: En esta función ejecuta una query de busqueda
//Recibe: tres parámetros con cadenas para fromar la select sql y un link de
//        conexión al  recurso de la BBDD
//Retorna: un array con el resultado de la búsqueda, el número de filas y los
//         codigos de errores
//***OBSERVACIONES:CREO QUE YA NO LUA UTILIZO, HA SIDO SUSTITUIDA POR buscarCadSql()
//------------------------------------------------------------------------------
//------------------------ Inicio buscarEnTablas_PDO_bindValues ----------------
function buscarEnTablas_FetchAllNoUso($tablasBusqueda,$cadenaCondicionesBuscar,$camposBuscados,$conexionDB,$arrBindValues = NULL) 
{
	echo '<br><br>0-1 modeloMySQL.php:buscarEnTablas:cadenaCondicionesBuscar: ';var_dump($cadenaCondicionesBuscar);echo '<br>';
 //echo '<br><br>0-2 modeloMySQL.php:buscarEnTablas:conexionDB: ';var_dump($conexionDB);echo '<br>';
	echo '<br><br>0-3 modeloMySQL.php:buscarEnTablas:arrBindValues: ';var_dump($arrBindValues);echo '<br>';
	
	$arrResultado = array();
	$arrResultado['nomScript'] = "modeloMySQL.php";
	$arrResultado['nomFuncion'] = "buscarEnTablas";	
 $arrResultado['codError'] = '00000';
	$arrResultado['errno'] = '';
 $arrResultado['errorMensaje'] = '';
	$arrResultado['numFilas'] = -1;	
	$arrResultado['resultadoFilas'] = NULL; 	

	try {
		
		if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
		{ 	   
				echo '<br><br>0-4 modeloMySQL.php:buscarEnTablas:conexionDB: ';var_dump($conexionDB);echo '<br>';
				throw new Exception("Sin conexión BD");//Disparo esta excepción porque no la captura PDO, al no estar instanciada $conexionDB
		} 
		if (!isset($camposBuscados) || empty($camposBuscados) || $camposBuscados == NULL) //???? necesario
		{ 	   
					$camposBuscados = "*";
		}

		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		echo '<br>1 modeloMySQL.php:buscarEnTablas: cadena select:',$cadSql."<br /><br />";		

		$sentencia = $conexionDB->prepare($cadSql); // Se crea un objeto PDOStatement.
		
		echo '<br><br>2 modeloMySQL.php:buscarEnTablas.php:sentencia: ';print_r($sentencia);
		
		/*Si $cadenaCondicionesBuscar no incluye :arrBindValues, 
		  pero en el caso de que incluya lo pone = 0, y puede dar valores no deseados
		  por ejemplo en buscarRolFuncion($codRol) que le sería igual a poner $codRol = 0
				No debiera existir codRol = 0, ni equivalentes para evitar esos errores por eso:
		*/
		if ( !isset($arrBindValues) || empty($arrBindValues) || $arrBindValues == NULL)
		{ echo '<br><br>2-1 modeloMySQL.php:buscarEnTablas:arrBindValues: ';print_r($arrBindValues);
				$sentencia->execute();//Se ejecuta sentencia sin $arrBindValues porque no son necesarios para la select
		}
		else
		{	echo '<br><br>2-2 modeloMySQL.php:buscarEnTablas:arrBindValues: ';print_r($arrBindValues);
				$sentencia->execute($arrBindValues);//Se ejecuta esta sentencia con $arrBindValues
		}
		echo '<br><br>3-1 modeloMySQL.php:buscarEnTablas:sentencia: ';print_r($sentencia);
		echo "<br><br>3-2 número de registros; ".$sentencia->rowCount();
		
		$resulSentencia = $sentencia->fetchAll(PDO::FETCH_ASSOC); //Se recuperan todo los resultados en una matriz
		echo '<br><br>4-1 modeloMySQL.php:buscarEnTablas:resulSentencia: ';print_r($resulSentencia);
		echo '<br><br>4-2 modeloMySQL.php:buscarEnTablas:filas resulSentencia: ';print_r(count($resulSentencia));
		
		/* Otra opcion: while ($fila = $sentencia->fetch(PDO::FETCH_ASSOC)) {$datos[]= $fila;}*/
		
		$arrResultado['numFilas'] = count($resulSentencia);
		
		/*--------- para comparar número de filas con "count($resulSentencia);" ------		
		/*$hacerConsultaNumFilas=$conexionDB->prepare("SELECT COUNT(*) FROM $tablasBusqueda $cadenaCondicionesBuscar");
		echo '<br><br>5-1 modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);
		$hacerConsultaNumFilas->execute(); 
		echo '<br><br>5-2 modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);			
		$arrResultado['numFilas'] = $hacerConsultaNumFilas->fetchColumn();			
		echo '<br><br>5-3 modeloMySQL:buscarEnTablas:arrResultado: ';print_r($arrResultado);	
	 ----------------------------------------------------------------------------*/			
		$f = 0;
		$resulFilasAux = array();	
		
		//while ($fila = $sentencia->fetchAll(PDO::FETCH_ASSOC))//Se recuperan los resultados en una matriz
		while ($f < count($resulSentencia))//Se recuperan los resultados en una matriz
		{					
				//foreach ($fila as $indice => $contenido)
				foreach ($resulSentencia[$f] as $indice => $contenido)
				{						
							$resulFilasAux[$f][$indice] = utf8_encode($contenido);
				}
				$f++;
		}
		echo '<br><br>5-1 modeloMySQL:buscarEnTablas:resulFilasAux: ';print_r($resulFilasAux);
		
		$arrResultado['resultadoFilas'] = $resulFilasAux;	
	
		echo '<br><br>5-2 modeloMySQL.php:buscarEnTablas:f: ';print_r($f);

		$sentencia->closeCursor(); //Se libera el recurso.
		//$sentencia = null;	
	}				
	catch (PDOException $e) {
		
			$arrResultado['codError'] = '70100';					
			$arrResultado['errno'] = $e->getCode();
			$arrResultado['errorMensaje'] = 'Error PDO en el sistema al buscar en tabla/s: '.$tablasBusqueda.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				echo "<br><br>6-1 modeloMySQL.php:buscarEnTablas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>6-2 modeloMySQL.php:buscarEnTablas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}
	catch (Exception $e) {	
	
			$arrResultado['codError'] = '70100'; 
			$arrResultado['errno'] = $e->getCode();
			$arrResultado['errorMensaje'] = 'Error en el sistema al buscar en tabla/s: '.$tablasBusqueda.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				echo "<br><br>7-1 modeloMySQL.php:buscarEnTablas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>7-2 modeloMySQL.php:buscarEnTablas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}		

	echo '<br><br>9 modeloMySQL:buscarEnTablas:arrResultado: ';print_r($arrResultado);
	
	return 	$arrResultado;
}
//------------------------------ Fin buscarEnTablas_PDO_bindValues_FetchAll -------------

//------------------------ Inicio buscarEnTablas_PDO_bindValues ----------------
function buscarEnTablasNoUso($tablasBusqueda,$cadenaCondicionesBuscar,$camposBuscados,$conexionDB,$arrBindValues) 
{
	echo '<br><br>0-1 modeloMySQL.php:buscarEnTablas:cadenaCondicionesBuscar: ';var_dump($cadenaCondicionesBuscar);echo '<br>';
 //echo '<br><br>0-2 modeloMySQL.php:buscarEnTablas:conexionDB: ';var_dump($conexionDB);echo '<br>';
	//echo '<br><br>0-3 modeloMySQL.php:buscarEnTablas:arrBindValues: ';var_dump($arrBindValues);echo '<br>';
	
	$arrResultado = array();
	$arrResultado['nomScript'] = "modeloMySQL.php";
	$arrResultado['nomFuncion'] = "buscarEnTablas";	
 $arrResultado['codError'] = '00000';
	$arrResultado['errno'] = '';
 $arrResultado['errorMensaje'] = '';
	$arrResultado['numFilas'] = -1;	
	$arrResultado['resultadoFilas'] = NULL; 	

	try {
		
		if (!isset($conexionDB) || empty($conexionDB) || $conexionDB == NULL) 
		{ 	   
				echo '<br><br>0-4 modeloMySQL.php:buscarEnTablas:conexionDB: ';var_dump($conexionDB);echo '<br>';
				throw new Exception("Sin conexión BD");//Disparo esta excepción porque no la captura PDO, al no estar instanciada $conexionDB
		} 
		if (!isset($camposBuscados) || empty($camposBuscados) || $camposBuscados == NULL) //???? necesario
		{ 	   
					$camposBuscados = "*";
		}

		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		//echo '<br>1 modeloMySQL.php:buscarEnTablas: cadena select:',$cadSql."<br /><br />";		

		$sentencia = $conexionDB->prepare($cadSql); // Se crea un objeto PDOStatement.
		
		//echo '<br><br>2 modeloMySQL.php:buscarEnTablas.php:sentencia: ';print_r($sentencia);
		
		/*Si $cadenaCondicionesBuscar no incluye :arrBindValues, 
		  pero en el caso de que incluya lo pone = 0, y puede dar valores no deseados
		  por ejemplo en buscarRolFuncion($codRol) que le sería igual a poner $codRol = 0
				No debiera existir codRol = 0, ni equivalentes para evitar esos errores por eso:
		*/
		if ( !isset($arrBindValues) || empty($arrBindValues) || $arrBindValues == NULL)
		{ //echo '<br><br>2-1 modeloMySQL.php:buscarEnTablas:arrBindValues: ';print_r($arrBindValues);
				$sentencia->execute();//Se ejecuta sentencia sin $arrBindValues porque no son necesarios para la select
		}
		else
		{	//echo '<br><br>2-2 modeloMySQL.php:buscarEnTablas:arrBindValues: ';print_r($arrBindValues);
				$sentencia->execute($arrBindValues);//Se ejecuta esta sentencia con $arrBindValues
		}
		echo '<br><br>3-1 modeloMySQL.php:buscarEnTablas:sentencia: ';print_r($sentencia);	

		/*--- Inicio Recuperando los resultados fila a fila y tratándolos después ----
		  OJO: dicen que esta opción gestiona mejor los recursos 
		----------------------------------------------------------------------------*/
		$f = 0;
		$resulFilasAux = array();	
		
		while ($fila = $sentencia->fetch(PDO::FETCH_ASSOC))//Se recuperan los resultados fila a fila en una matriz
		{					
				foreach ($fila as $indice => $contenido)
				{						
							$resulFilasAux[$f][$indice] = utf8_encode($contenido);
				}
				$f++;
		}
		//echo "<br><br>3-2 modeloMySQL.php:buscarEnTablas:filas:f:  ".$f;
		$arrResultado['numFilas'] = $f;
		
		/*---- Fin Recuperando los resultados fila a fila y tratándolos después ----*/
		
		/*--- Inicio Recuperando todos los datos de un golpe en una matriz -----------
		  y tratándolos después: OJO esta opción dicen podría implicar más recursos		
		------------------------------------------------------------------------------		
		$resulSentencia = $sentencia->fetchAll(PDO::FETCH_ASSOC);//Se recuperan todos los datos de un golpe en una matriz esto podría implicar mucha memoria
		//echo '<br><br>4-1 modeloMySQL.php:buscarEnTablas:resulSentencia: ';print_r($resulSentencia);
		//echo '<br><br>4-2 modeloMySQL.php:buscarEnTablas:filas resulSentencia: ';print_r(count($resulSentencia));		
		
		$arrResultado['numFilas'] = count($resulSentencia);		
		$f = 0;
		$resulFilasAux = array();
	 while ($f < count($resulSentencia))//Se recuperan los resultados en una matriz
		{	foreach ($resulSentencia[$f] as $indice => $contenido)
				{	$resulFilasAux[$f][$indice] = utf8_encode($contenido);
				}
				$f++;
		}	
		$resulSentencia = NULL;*/
		
		/*--------- para comparar número de filas con "count($resulSentencia);" ------		
		$hacerConsultaNumFilas=$conexionDB->prepare("SELECT COUNT(*) FROM $tablasBusqueda $cadenaCondicionesBuscar");
		echo '<br><br>5-1 modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);
		$hacerConsultaNumFilas->execute(); 
		echo '<br><br>5-2 modeloMySQL:buscarEnTablas:hacerConsultaNumFilas: ';print_r($hacerConsultaNumFilas);			
		$arrResultado['numFilas'] = $hacerConsultaNumFilas->fetchColumn();			
		echo '<br><br>5-3 modeloMySQL:buscarEnTablas:arrResultado: ';print_r($arrResultado);	
	 ----------------------------------------------------------------------------*/
		
		/*------- Fin Recuperando todos los datos de un golpe en una matriz  ------ */
		
		echo '<br><br>5-1 modeloMySQL:buscarEnTablas:resulFilasAux: ';print_r($resulFilasAux);
		
		$arrResultado['resultadoFilas'] = $resulFilasAux;	
	
		//echo '<br><br>5-2 modeloMySQL.php:buscarEnTablas:f: ';print_r($f);

		$sentencia->closeCursor(); //Se libera el recurso.
		$sentencia = null;	
	}				
	catch (PDOException $e) {
		
			$arrResultado['codError'] = '70100';					
			$arrResultado['errno'] = $e->getCode();
			$arrResultado['errorMensaje'] = 'Error PDO en el sistema al buscar en tabla/s: '.$tablasBusqueda.', codError:'.$arrResultado['codError'].
				', Error en MySQL, Código PDO: '.$e->getCode().', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().
				', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				echo "<br><br>6-1 modeloMySQL.php:buscarEnTablas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>6-2 modeloMySQL.php:buscarEnTablas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}
	catch (Exception $e) {	
	
			$arrResultado['codError'] = '70100'; 
			$arrResultado['errorMensaje'] = 'Error en el sistema al buscar en tabla/s: '.$tablasBusqueda.', codError:'.$arrResultado['codError'].
				', Script de la excepción: '.$e->getFile().', Línea excepción: '.$e->getLine().', Excepción previa: '.$e->getPrevious().', getMessage(): '.$e->getMessage();
				
				//echo "<br><br>7-1 modeloMySQL.php:buscarEnTablas:errorMensaje: ".$arrResultado['errorMensaje'];
				//echo "<br><br>7-2 modeloMySQL.php:buscarEnTablas:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena		
	}		

	//echo '<br><br>9 modeloMySQL:buscarEnTablas:arrResultado: ';print_r($arrResultado);
	
	return 	$arrResultado;
}
//------------------------------ Fin buscarEnTablas_PDO_bindValues -------------

/*-------------------------------- Inicio insertarVariasFilas ------------------
Inserva varia filas en una tabla, a partir de un array que contiene varias filas
RECIBE:
1- Nombre tabla de inserción.
2- Un array con formato: 
$arrayDatos['numFilas'] = n+1;
$arrayDatos['resultadoFilas'] = ([0]=>array (camp1, camp2, ...,campn),
                                 [1]=>array (camp1, camp2, ...,campn),
																													 			...................................
																														 		[n]array (camp1, camp2, ...,campn)
																														 	)
3- Link conexion BBDD

DEVUELVE: un array $resultadoInsertar, núm filas insertadas y con control errores

LLAMADA: modeloAdmin.php:importarExcelSociosES(),importarExcelSociosAN() acaso otros	
LLAMA: nada ...

OBSERVACIONES: Se utilizó para importar las bases de datos antiguas procedentes 
de Access y Excel, cuando incié esta aplicación
Posibilidad añadir función conexión.			

Ya no se utiliza					
------------------------------------------------------------------------------*/
function insertarVariasFilas_YaNoUso($tablaInsercion,$arrayDatos,$conexionDB)
{
 $arrayCamposValoresInsercion = $arrayDatos['resultadoFilas'];
 
	echo "<br><br>1 insertarVariasFilas.php:arrayCamposValoresInsercion:";print_r($arrayCamposValoresInsercion);
	
	$resultadoInsertar = array();
	
	$resultadoInsertar['nomScript'] = "modeloMySQL.php";
	$resultadoInsertar['nomFuncion'] = "insertarVariasFilas";
	$resultadoInsertar['numFilas'] = -1;	
	$resultadoInsertar['codError'] = '00000';
	$resultadoInsertar['errno'] = "";
	$resultadoInsertar['errorMensaje'] = '';
	
	$cadenaCamposInsercion ='';
	$cadenaValorCamposInsercion ='';	
	
	 /*Formación una cadena de campos (Campo1, Campo2, Campo3,...)	*/
	foreach ($arrayCamposValoresInsercion[0]  as $indice =>$contenido)
	{
		$cadenaCamposInsercion  = $cadenaCamposInsercion.','.$indice;
	}
	$cadenaCamposInsercion   = ltrim($cadenaCamposInsercion, ",");
	
	
	/*Formación una cadena de values (Value1_1, Value1_2, Value1_3,...), (Value2_1, Value2_2, Value2_3,...), ....(ValueN_1, ValueN_2, ValueN_3,...);	*/
	$f = 0;
	$fUltima = $arrayDatos['numFilas'];
	
	while ($f < $fUltima)
	{
	 $cadenaValorFilaInsercion = "(";
		
		foreach ($arrayCamposValoresInsercion[$f]  as $indice =>$contenido)
		{
			 //echo "<br>[$f]=$contenido";
				//$cadenaValorCamposInsercion = $cadenaValorCamposInsercion.','."'".utf8_decode($contenido)."'";		
				$cadenaValorFilaInsercion = $cadenaValorFilaInsercion."'".utf8_decode($contenido)."'".',';		
				//$cadenaValorFilaInsercion = $cadenaValorFilaInsercion."'".$contenido."'".',';		
			
		}
	
		$cadenaValorFilaInsercion   = rtrim($cadenaValorFilaInsercion, ",");
		$cadenaValorFilaInsercion   = $cadenaValorFilaInsercion ."),";
		$cadenaValorCamposInsercion = $cadenaValorCamposInsercion.$cadenaValorFilaInsercion;
	
		 $f++;
	}
	$cadenaValorCamposInsercion = rtrim($cadenaValorCamposInsercion, ",");
	$cadenaValorCamposInsercion .=";";
	
	/*Formación una cadena INSERT */
	
	$consultaInsertar = "INSERT INTO $tablaInsercion ($cadenaCamposInsercion) VALUES $cadenaValorCamposInsercion";

 //echo '<br><br>2-insertarVariasFilas.php:consultaInsertar: ',$consultaInsertar,'<br />';

	$resultado = mysql_query($consultaInsertar,$conexionDB);

	if ($resultado)
	{$resultadoInsertar['codError']='00000';	  
		$resultadoInsertar['numFilas'] = mysql_affected_rows();
	}
	else
	{$resultadoInsertar['codError'] = "70200";
	 $resultadoInsertar['errno'] = mysql_errno();
		$resultadoInsertar['errorMensaje'] = 'Error en el sistema, no se ha insertado ninguna fila. '.'Error mysql_query '.mysql_error();
	}
	
 //echo '<br><br>3-insertarVariasFilas.php:resultadoInsertar: ',print_r($resultadoInsertar);
	
	return $resultadoInsertar;
}
//----------------------------- Fin insertarVariasFilas ------------------------

//function borrarFilas_PDO($tablaBorrar,$cadenaCondiciones,$conexionDB)//bien
function borrarFilas_Sin_bindValuesYaNoUsado($tablaBorrar,$cadenaCondiciones,$conexionDB)//bien
{
	echo '<br><br>0-1 modeloMySQL.php:borrarFilas:cadenaCondiciones: ';print_r($cadenaCondiciones);
	echo '<br><br>0-2 modeloMySQL.php:borrarFilas:conexionDB: ';var_dump($conexionDB); echo '<br />';	 
		
	$resultado = array();	
	$resultado['nomFuncion'] = "borrarFilas";
	$resultado['nomScript'] = "modeloMySQL.php";
	$resultado['numFilas'] = -1;	
	$resultado['codError'] = '00000';
	$resultado['errno'] = "";
	$resultado['errorMensaje'] = '';

	$cadSql = "DELETE FROM $tablaBorrar WHERE $cadenaCondiciones";
											
	echo '<br><br>1- modeloMySQL.php:borrarFilas:$cadSql: ',$cadSql,'<br />';

	try {				
				
				$sentencia = $conexionDB->prepare($cadSql);// Se crea un objeto PDOStatement.
				echo '<br><br>2a modeloMySQL:borrarFilas:sentencia: ';print_r($sentencia);	
		
				$sentencia->execute(); // Se ejecuta la sentencia.
				
				echo '<br><br>2b modeloMySQL:borrarFilas:sentencia: ';print_r($sentencia);				
				echo '<br><br>2c modeloMySQL:borrarFilas:pdo-número de registros: '.$sentencia->rowCount();
				
				$resultado['numFilas'] = $sentencia->rowCount();//Esta función va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD 
				
    if ($resultado['numFilas'] !==0) 
				{ $resultado['codError'] ='00000';
						$resultado['numFilas'] = $resultado['numFilas'];
				} 
				else // ==0
				{ $resultado['codError'] ='80001'; 
						$resultado['errorMensaje'] ='No se han encontrado filas para borrar en la tabla: '.$tablaBorrar; 
						$resultado['numFilas'] = $resultado['numFilas'];
				}				
	   echo '<br><br>2d modeloMySQL:borrarFilas:resultado: ';print_r($resultado);	
				
				$sentencia->closeCursor(); // Se libera el recurso.
 } 
	catch (PDOException $e) {
	 
				$resultado['codError'] = '70400'; 		
			 $resultado['errno'] = $e->getCode();			
				$resultadoBorrarFilasr['errorMensaje'] = 'Error en el sistema, no se ha insertado ninguna fila. Error en eMySQL, Código: '. $e->getCode(). '.Al insertar en tabla: '.$tablaInsercion.' getMessage(): '.$e->getMessage();
				
				echo "<br><br>3a modelos/BBDD/mysql.php:modeloMySQL.php:resultado:Error MySQL: getCode(): ". $e->getCode(). " getMessage(): " . $e->getMessage();
				echo "<br><br>3b modeloMySQL:resultado:Mensaje de la excepción:Excepción previa:getPrevious(): ".$e->getPrevious()."<br>";
				echo "<br><br>3d modeloMySQL:resultado:Mensaje de la excepción:Script causante de la excepción:getFile(): ".$e->getFile()."<br>";
				echo "<br><br>3e modeloMySQL:resultado:Mensaje de la excepción:Línea causante de la excepción:getLine(): ".$e->getLine()."<br>";
				//echo "<br><br>4b modeloMySQL:resultado:Mensaje de la excepción:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena				
	}
		catch (Exception $e) {
			
				$resultado['codError'] = '70400'; 
				
				echo "<br /><br />4a modelos/BBDD/mysql.php:modeloMySQL.php:Error MySQL: getCode(): ". $e->getCode(). " getMessage(): " . $e->getMessage(). "	Info: ";				
				$resultado['errno'] = $e->getCode();			
				$resultado['errorMensaje'] = 'Error en eMySQL, Código: '. $e->getCode(). '.En tablas: '.$tablasBusqueda.' getMessage(): '.$e->getMessage();				
				//echo "<br><br>4b:resultado:Mensaje de la excepción:getMessage(): ".$e->getMessage()."<br>";
				echo "<br><br>4c modeloMySQL:resultado:Mensaje de la excepción:Excepción previa:getPrevious(): ".$e->getPrevious()."<br>";
				//echo "<br><br>4d modeloMySQL:resultado:Mensaje de la excepción:Código de la excepción:getCode(): ".$e->getCode()."<br>";
				echo "<br><br>4e modeloMySQL:resultado:Mensaje de la excepción:Script causante de la excepción:getFile(): ".$e->getFile()."<br>";
				echo "<br><br>4f modeloMySQL:resultado:Mensaje de la excepción:Línea causante de la excepción:getLine(): ".$e->getLine()."<br>";
				//echo "<br><br>4g modeloMySQL:resultado:Mensaje de la excepción:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena  
	}				
	echo "<br>3- modeloMySQL.php:resultado";print_r($resultado);
	
	return $resultado;
}
//----------------------------- Fin borrarFilas_PDO ----------------------------


//----------------------------- Inicio eliminarFilas ---------------------
// el formato de "eliminarFilas()" siempre añade un "(" al principio y otro ")" al final,
// para y la condición quedará como WHERE ($cadenaCondiciones) y permite condiciones como:
// (campo1=valorCampo1 AND campo2=valorCampo2) AND (
//  campo3=valorCampo3 AND campo4=valorCampo4), siendo en ese caso ") AND (" 
// los "opUnir"
// puede borrar varias filas
// OBSERVACIONES: 
//**** no se utiliza sustituida por borrarFilas_PDO
//No borrar se llama desde modeloAdmin.php:actualizarCuotasSociosAnioNuevo($anioNuevo)
// Se podrá adaptar para PDO con arrBindValues, dentro de esta función sin afectar a llamadas
//------------------------------------------------------------------------------------
function eliminarFilasYaNoUso($tablaEliminar,$arrayCondiciones,$conexionDB)
{ 
  $resEliminarFilas = array();
  //$cadenaValores ='';
  $resEliminarFilas['codError']='00000';
  $resEliminarFilas['errorMensaje'] = '';
  $resEliminarFilas['numFilas']='-1'; 
  $cadenaDatosEliminar = '';
  $cadenaCondicionesEliminar = '';                       
//  	$consultaEliminar= "DELETE FROM $tablaBorrar 
//                      WHERE $campoBusqueda LIKE '$valorCampoBusqueda'";      
  
  foreach ($arrayCondiciones  as $indice =>$contenido)
  { 
    $cadenaCondicionesEliminar.= $indice.' '.$arrayCondiciones[$indice]['operador'].' '.
    '\''.$arrayCondiciones[$indice]['valorCampo'].'\''.' '.$arrayCondiciones[$indice]['opUnir'].' ';
  }; 
  $cadenaCondicionesEliminar='('. $cadenaCondicionesEliminar.')';//añade paréntesis 
           
  $consultaEliminar = "DELETE FROM  $tablaEliminar 
                              WHERE $cadenaCondicionesEliminar"; 
																														                                  
  //echo '<br>valores dentro modelo Eliminar :',$consultaEliminar;
  $resultado= mysql_query($consultaEliminar,$conexionDB);

  if ($resultado)
  {  
     if (mysql_affected_rows()!==0) 
     { $resEliminarFilas['codError']='00000'; 
       $resEliminarFilas['numFilas']=mysql_affected_rows();
     } 
     else //0
     { //$resultadoActualizar['codError']='1';/// acaso no conviene tratarlo como error
       //$resultadoActualizar['errorMensaje']='No se ha actualizado ningún fila. '; 
       $resEliminarFilas['numFilas']=mysql_affected_rows();
     }
  }
  else  //error sistema
  { $resEliminarFilas['codError']='70400';   
		  $resEliminarFilas['errno']=mysql_errno(); 
    $resEliminarFilas['errorMensaje']='Error en el sistema, no se ha podido eliminar ninguna fila. '.$tablaEliminar.
			           'Error mysql_query '.mysql_error();
    $resEliminarFilas['numFilas']=0;	                                     
  } 
 return $resEliminarFilas;
}
//---------------------------- Inicio eliminarFilas NO UTILIZADO ----------------------
// el formato de "eliminarFilas()" siempre añade un "(" al principio y otro ")" al final,
// para y la condición quedará como WHERE (condición) y además permite condiciones como:
// (campo1=valorCampo1 AND campo2=valorCampo2) AND (
//  campo3=valorCampo3 AND campo4=valorCampo4), siendo en ese caso ") AND (" 
// los "opUnir"
//------------------------------------------------------------------------------------
function eliminarFilaEvAreaYaNoUsado($tabla,$arrValorCampoCondiciones)
{
 $arrayCondiciones=array
 ("NUMMATRICULA"=>array("operador"=>"=",
                        "valorCampo"=>$arrValorCampoCondiciones['NUMMATRICULA'],
                        "opUnir"=>"AND"
                        ), 
  "NOMUSUARIO"=>array("operador"=>"=",
                      "valorCampo"=>$arrValorCampoCondiciones['NOMUSUARIO'],
                      "opUnir"=>"AND"
                      ), 
  "FECHAFIN"=>array("operador"=>"=",
                    "valorCampo"=>$arrValorCampoCondiciones['FECHAFIN'],
                    "opUnir"=>"AND"
                    ), 
  "CODFCT"=>array("operador"=>"=",
                  "valorCampo"=>$arrValorCampoCondiciones['CODFCT'],
                  "opUnir"=>""
                  ), 
 );
 
 $resultadoEliminar=eliminarFilas($tabla,$arrayCondiciones);                                                    

/* if ($resultadoEliminar['codError'] == '0')
 { $error='NO se ha guardado la evaluación de areas de: '.
          $_SESSION['vs_arrayCabForm']['apeNomAl'];
 }
 else
 { $error=$resultadoEliminar['errorMensaje'].
   'La tabla evAreas tiene un entrada incorrecta para el alumno: '. 
   $_SESSION['vs_arrayCabForm']['NUMMATRICULA'].
   ' Cod. error:'.$resultadoEliminar['codError'];
 };  
 */                         
 return $resultadoEliminar;
}              
//---------------------------- Fin eliminarFilas -------------------------------------



//----------------------------- Inicio actualizarTabla --------------------------
/* ejemplo de USO arrayCondiciones:
 $arrayCondiciones['CODSOCIO']['valorCampo']= $campoCondiciones;//CODSOCIO
 $arrayCondiciones['CODSOCIO']['operador']= '=';
	$arrayCondiciones['CODSOCIO']['opUnir']= 'AND'; //OR
	
	$arrayCondiciones['ANIOCUOTA']['valorCampo']= $arrayDatosAct['ANIOCUOTA']['valorCampo'];
 $arrayCondiciones['ANIOCUOTA']['operador']= '=';
	$arrayCondiciones['ANIOCUOTA']['opUnir']= '';//el último no tiene operador para unir y se deja vacio
	
 $arrayCondiciones=array
 ("CODSOCIO"=>array("operador"=>"=",
                    "valorCampo"=>$arrValorCampoCondiciones['CODSOCIO'],
                    "opUnir"=>"AND"
                   ), 
  "ANIOCUOTA"=>array("operador"=>"=",
                     "valorCampo"=>$arrValorCampoCondiciones['ANIOCUOTA'],
                     "opUnir"=>"AND"
                    ), 
........
  "ULTIMACONCION"=>array("operador"=>"=",
                  "valorCampo"=>$arrValorCampoCondiciones['ULTIMACONCION'],
                  "opUnir"=>""//el último no tiene operador para unir y se deja vacio
                  ) 
 );	
*/
//-------------------------------------------------------------------------
//function actualizarTabla_PDO ($tablaActualizar,$arrayCondiciones,$arrayCamposValoresActualizar)
function actualizarTabla_pdo_sin_bind ($tablaActualizar,$arrayCondiciones,$arrayCamposValoresActualizar,$conexionLinkDB)
{
	echo '<br><br>0-1 modeloMySQL.phpactualizarTabla_pdo_sin_bind:arrayCondiciones: ';print_r($arrayCondiciones);
	echo '<br><br>0-1 modeloMySQL.php:actualizarTabla_pdo_sin_bind:arrayCamposValoresActualizar: ';print_r($arrayCamposValoresActualizar);
	//echo '<br><br>0-2 modeloMySQL.php:actualizarTabla_pdo_sin_bind:conexionLinkDB: ';var_dump($conexionLinkDB);
	
 $resultadoActualizar = array();

	$resultadoActualizar['nomFuncion'] = "actualizarTabla";
	$resultadoActualizar['nomScript'] = "modeloMySQL.php";	
 $resultadoActualizar['codError'] = '00000';
 $resultadoActualizar['errorMensaje'] = '';
 $resultadoActualizar['numFilas'] = '-1'; 

	$cadenaDatosActualizar = '';
 $cadenaCondicionesActualizar = '';
	
	
/*  foreach ($arrayCamposValoresActualizar  as $indice =>$contenido)
  { $cadenaDatosActualizar = $cadenaDatosActualizar.','.$indice.'='."'".$contenido."'";
  };  
  $cadenaDatosActualizar = ltrim($cadenaDatosActualizar, ",");
*/
  foreach ($arrayCamposValoresActualizar  as $indice =>$contenido)
  { 
    if (isset($contenido) and $contenido !== 'NULL' and $contenido !== '')
    { 
			  //echo '<br><br>2-1 modeloMySQL.php:cadenaDatosActualizar:',$cadenaDatosActualizar; 
			  //$cadenaDatosActualizar = $cadenaDatosActualizar.','.$indice.'='."'".$contenido."'";
			  
			  $cadenaDatosActualizar = $cadenaDatosActualizar.','.$indice.'='."'".utf8_decode($contenido)."'";
    }
    else
    { $cadenaDatosActualizar = $cadenaDatosActualizar.','.$indice.'='." NULL";
        //para evitar las comillas ,set = 'NULL' da error, debe ser ,set = NULL, 
    }
  } 
  $cadenaDatosActualizar = ltrim($cadenaDatosActualizar, ",");
  //echo '<br><br>2_ 1 modeloMySQL.php:cadenaDatosActualizar:',$cadenaDatosActualizar; 
  
  foreach ($arrayCondiciones  as $indice =>$contenido)
  { 
		  $cadenaCondicionesActualizar.= $indice.' '.$arrayCondiciones[$indice]['operador'].' '.'\''.$arrayCondiciones[$indice]['valorCampo'].'\''.' '.$arrayCondiciones[$indice]['opUnir'].' ';
  }                             
  $cadSql = "UPDATE $tablaActualizar SET $cadenaDatosActualizar WHERE $cadenaCondicionesActualizar";
																														
  echo '<br>3 modeloMySQL:actualizarTabla:'.$cadSql;
		
 /*** if ($resultado)
  {  $resultadoActualizar['codError']='00000'; 
     $resultadoActualizar['numFilas']= mysql_affected_rows(); 
  }
  else
  { //
	  $resultadoActualizar['codError']='70300'; 
	  $resultadoActualizar['errno']=mysql_errno();     
   $resultadoActualizar['errorMensaje']='Error del sistema, no se ha actualizado la tabla '.$tablaActualizar;
  }  ***/ 
			try {
				///$resultado= mysql_query($cadSql);
				
				$consulta = $conexionLinkDB->prepare($cadSql);// Se crea un objeto PDOStatement.
				
				echo '<br><br>4a modeloMySQL:actualizarTabla:consulta: ';print_r($consulta);
	
				$consulta->execute(); // Se ejecuta la consulta.
				
				//echo '<br><br>4b modeloMySQL:actualizarTabla: consulta: ';print_r($consulta);				
				//echo '<br><br>4c modeloMySQL:actualizarTabla:pdo-número de registros: '.$consulta->rowCount();//Esta función va bien para INSERT, UPDATE, DELETE, pero puede con SELECT puede no ser correcta para alguna BBDD
				
				$resultadoActualizar['numFilas'] = $consulta->rowCount(); 
				
				//$consulta->closeCursor(); // Se libera el recurso.
				//$resultadoFilasAux = null;
			 //$fila = null;
 } 
	catch (PDOException $e) {
	 
				$resultadoActualizar['codError'] = '70100'; 		
			 $resultadoActualizar['errno'] = $e->getCode();			
				$resultadoActualizar['errorMensaje'] = 'Error en eMySQL, Código: '. $e->getCode(). '.Al actualizar en tabla: '.$tablaActualizar.' getMessage(): '.$e->getMessage();
				
				echo "<br><br>5a modelos/BBDD/mysql.php:modeloMySQL.php:Error MySQL: getCode(): ". $e->getCode(). " getMessage(): " . $e->getMessage();
				echo "<br><br>5b modeloMySQL:actualizarTabla:Mensaje de la excepción:Excepción previa:getPrevious(): ".$e->getPrevious()."<br>";
				echo "<br><br>5d modeloMySQL:actualizarTabla:Mensaje de la excepción:Script causante de la excepción:getFile(): ".$e->getFile()."<br>";
				echo "<br><br>5e modeloMySQL:actualizarTabla:Mensaje de la excepción:Línea causante de la excepción:getLine(): ".$e->getLine()."<br>";
				//echo "<br><br>5f modeloMySQL:actualizarTabla:Mensaje de la excepción:Cadena informativa de la excepción:__toString(): ".$e->__toString()."<br>";//Puede que demasiada información en la cadena					
	}		
/*if ($resultado)
  {  if (mysql_affected_rows()!==0) 
     { $resultadoActualizar['codError']='0'; 
       $resultadoActualizar['numFilas']=mysql_affected_rows();
       echo '<br>111numFilasaffected:',mysql_affected_rows();
     } 
     else
     { $resultadoActualizar['codError']='1'; 
       $resultadoActualizar['errorMensaje']='No se ha actualizado ningún usuario. '; 
       $resultadoActualizar['numFilas']=mysql_affected_rows();
       echo '<br>222numFilasaffected:',mysql_affected_rows();       
     }
  }
  else  
  { $resultadoActualizar['codError']=mysql_errno();    
    $resultadoActualizar['errorMensaje']='No se ha actualizado ningún usuario. '.'Error mysql_query '.mysql_error();
    $resultadoActualizar['numFilas']=0;	                                     
  }*/  
		
	//echo '<br><br>5 modeloMySQL:actualizarTabla:resultadoActualizar: ';print_r($resultadoActualizar);			

	$consulta = null;	// Se libera el recurso.		
			
 return $resultadoActualizar;
}
//--------------------------- Inicio  actualizarTabla_PDO_conBind --------------


/**************************** FIN YA NO USADAS *************************************************/

/**************************************************************************************************/

?>