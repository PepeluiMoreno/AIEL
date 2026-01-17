<?php
/*------------------------------------- exportarExcel.php ------------------------------------------
FICHERO: exportarExcel.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Funciones para generar ficheros Excel.

Incluye dos funciones que son muy parecidas: 
exportarExcel($arrExportar,$nomFile), exportarExcelCuotasTes($arrExportar,$nomFile)	que se descargan 
en el PC directamente en el momento de generarse el archivo excel (sin guardaese en el servidor )																					

AÚN NO UTILIZADAS: También incluye las funciones: 
exportarExcelEnServidor(), exportarExcelCuotasTesEnServidor()	que se generan y guardAn en un 
directOrio del servidor, y después se puden descargar o eliminar.
															
NOTA: aunque aparece que funcciona con PHP 7.3.21 aunque con un molesto mensaje que no he encontrado 
como evitarlo estaría bien sustituir esta funcion por una nueva librería que hay para la versión 7.2: 
https://phpspreadsheet.readthedocs.io/en/latest/ 															
--------------------------------------------------------------------------------------------------*/


/*------------------------------------- exportarExcel() -------------------------------------------
Funciones para generar el ficheros Excel para Triódos, uso interno, donaciones
y otros usos y donaciones con todos los campos tipo texto.
															
RECIBE: $arrExportar, con formato $arrExportar['resultadoFilas'][$fila][campos de las filas] 
y $arrExportar['numFilas'].
$nomFile: nombre del fichero a exportar, al que se le añadirá la fecha y la 
extensión "xls"	
								
DEVUELVE: un archivo en formato .xls, al cerrar el buffer interno. Todo como texto y 
         como cantidades decimales.
		
LLAMADO: modeloPresCoord.php:exportarExcelSociosPres(),exportarExcelSociosCoord(),
mExportarExcelInformeAnualPres()

modeloTesorero:exportarDonacionesExcel()

(Ya no se usa para modeloTesorero.php:exportarCuotasExcelBancos(),exportarExcelCuotasInternoTes(), 
sustituida por más especifíca "exportarExcel.php:exportarExcelCuotasTes()")

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
               para formar el buffer de salida a excel utiliza "header()" y no puede 
															haber ningúna salida delante.
															
Al descargar el archivo sale un mensaje de Excel: "El formato y la extensión del 
archivo de Excel no coinciden ..." Puede que esté dañado o no sea seguro, lo abres de no hay problemas 															

Después de descargar el archivo, al finalizar no retorna a la pantalla de resultado,
si no hay error se queda en la última, investigar como se vuelve del buffer de nuevo.

ob_start(): Esta función habilitará el uso de búferes internos para almacenar la salida. 
Mientras los búferes de salida están activos no se envía salida (navegador) desde el script PHP 
(más que las cabeceras), en su lugar la salida es almacenada en un búfer interno, hasta 
que se ordene explícitamente ob_end_flush u otras equivalente, o se acabe el script PHP.   

ob_gzhandler: para comprimir en  gzip el buffer de salida. Necesita la lib "zlib" en PHP
Antes de que ob_gzhandler() envíe realmente los datos, determina qué tipo 
de codificación de contenido acepta el navegador ("gzip", "deflate" o ninguno)

ob_end_flush(): indica a PHP que vuelque todos los contenidos de bufer interno(si existe) en la salida (navegador)
y deshabilita este búffer que fue habilitado con ob_start().

Si la función ob_end_flush(), no se ejecuta correctamente !=true, se devuelve error.

2020-09-30: probada PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function exportarExcel($arrExportar,$nomFile)
{
	//echo "<br><br>0-1 exportarExcel.php:exportarExcel:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>0-2 exportarExcel.php:exportarExcel:nomFile: ";print_r($nomFile); 
 
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] ='';
	
 //si al comprimir en gzip !ob_start("ob_gzhandler") el buffer de salida da error, se usará sin comprimir ob_start()	
 if(!ob_start("ob_gzhandler")) ob_start();
		
 if ( !isset($nomFile) || empty($nomFile))
	{
			$file = 'archivo_export';	
	}
	else
	{
		 $file = $nomFile;
	}
	
 $csv_outputHtmlTab = '<table border=1>'; 
	$csv_outputHtmlTab .= '<tr>';
		
	foreach ($arrExportar['resultadoFilas'][0] as $campo => $valCampo)                         
	{$csv_outputHtmlTab .= '<th>'.htmlspecialchars($campo).'</th>';	
	 //$csv_output .= $campo."; ";
 }	
	$csv_outputHtmlTab .= '</tr>';  
 //$csv_output .= "\n";				
	
	$f = 0;
 $fUltima=$arrExportar['numFilas'];
 
 while ($f < $fUltima)
 {$csv_outputHtmlTab .= '<tr>';
	 
		foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)                         
	 {
			if(!isset($valCampo) || empty($valCampo))
   {
		  $csv_outputHtmlTab .= '<td>'.'NULL'.'</td>'; //no afecta
   }			
			else //if(isset($valCampo) && empty($valCampo))
			{
					//Sustituir números decimales con punto decimal formato 5.02 (MySQL), a coma decimal 5,02 para formato operaciones en Excel

					if(is_numeric ($valCampo)) //acaso lo pueda descomentar por estar ya tratado en la select SQL con REPLACE, o en la función de modelo correspondiente
					{//$valCampoX=number_format($valCampo, 2, ',','');//Pone dos decimales en todos los números incluso enteros
					
						$valCampo = str_replace(".", ",", $valCampo);//Cambia el punto decimal por una coma, si funciona. 
					}	
					//$csv_outputHtmlTab .= '<td>'.htmlspecialchars(utf8_decode($valCampo)).'</td>';
					//Nota: con el siguiente formato lo pone todo como formato texto para Excel			
					if( version_compare(PHP_VERSION, '5.4.0') >= 0) 
					{ // A PARTIR DEL CAMBIO A LA VERSIÓN PHP 5.4.0 o superior, para htmlspecialchars ES NECESARIO EL SIGUIENTE FORMATO:
							// utf8_decode simply converts a string encoded in UTF-8 que proviene de la BBDD a ISO-8859-1 
											
							// style="."mso-number-format:'\@' para que en Excel "$valCampo" lo ponga todo en formato de texto aunque sea un número, 
							// por ejemplo CP y así no quitará ceros a la izda . 
							$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo), ENT_COMPAT,'ISO-8859-1', true).'</td>';
					}
					else
					{ // ANTES DEL CAMBIO A LA VERSIÓN PHP 5.4.0 htmlspecialchars FUNCIONABA CON EL SIGUIENTE FORMATO:
							$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo)).'</td>';
					}			

			}//else //if(isset($valCampo) && empty($valCampo))
			
  }
		$csv_outputHtmlTab .= '</tr>'; 

		$f++;
	}		 
 $csv_outputHtmlTab .= '</table>';
	
	$filename = $file."_".date("Y-m-d_H-i-s",time());
	
	header("Content-type: application/vnd.ms-excel");	
	//header("Content-type: application/vnd.ms-excel;charset=utf-8");//también funciona

 header( "Content-disposition: attachment;filename=".$filename.".xls"); //descarga del archivo con un nombre de archivo determinado 	

	header('Content-Length: '. strlen($csv_outputHtmlTab) );// indicar tamaño del archivo  
	
	header("Cache-Control: no-cache"); // HTTP/1.1
 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado para que no guarde en cahé
	
	echo $csv_outputHtmlTab;//salida buffer 
		
	if (ob_end_flush())//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida,
	{ $reExportar['codError'] = '00000';		 
	  $reExportar['numFilas'] = $f;		
   $reExportar['nomFile'] = $filename;	
	}
	else //si no se puede cerrar el buffer, porque no este abierto u otras causas dará error
	{ $reExportar['codError'] = '70620';
	  $reExportar['errorMensaje'] ='Error al cerrar el buffer interno en exportarExcel()';
	}	

	return 	$reExportar;//solo devuelve valores cuando hay error 
}
//----------------------- Fin exportarExcel -------------------------------------------------------

/*----------------- Inicio exportarExcelDonaciones() ---------------------------------------------
FICHERO: exportarExcelDonaciones.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Esta función genera un archivo Excel, con las donaciones anotadas y sustituye los 
decimales en formato punto decimal tipo 54.01, a formato número de Excel con comas decimales 54,01
para los campos procedentes de MYSQL y PHP: "IMPORTEDONACION" y "GASTOSDONACION" para que no sea
necesario convertir el formato de las columnas Excel para operaciones aritméticas de sumas, medias, ...
							
															
RECIBE: 
$arrExportar, con fomato $arrExportar['resultadoFilas'][$fila][campos filas] y $arrExportar['numFilas']
$nomFile:  nombre del fichero a exportar, al que se le añadirá la fecha y la extensión "xls"	
								
DEVUELVE: un archivo en formato .xls, al cerrar el buffer interno (indica)

LLAMADA:	modelo Tesorero.php:excelDonacionesTesorero()	

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
               para formar el buffer de salida a excel utiliza "header()" y no puede 
															haber ningúna salida delante.
															
Muy parecida a: exportarExcel.php:exportarExcelCuotasTes()															
--------------------------------------------------------------------------------------------------*/
function exportarExcelDonaciones($arrExportar,$nomFile)
{					
	//echo "<br><br>1-a exportarExcelDonaciones:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>1-b exportarExcelDonaciones:nomFile: ";print_r($nomFile); 
 
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] ='';

 //si al comprimir en gzip !ob_start("ob_gzhandler") el buffer de salida da error, se usará sin comprimir ob_start()	
 if(!ob_start("ob_gzhandler")) ob_start();
		
 if ( !isset($nomFile) || empty($nomFile))
	{
			$file = 'archivo_export';	
	}
	else
	{
		 $file = $nomFile;
	}
	
 $csv_outputHtmlTab = '<table border=1>'; 
	$csv_outputHtmlTab .= '<tr>';
		
	foreach ($arrExportar['resultadoFilas'][0] as $campo => $valCampo)                  
	{		 
  /* Para el nombre de las columnas que vienen en fila 0 del array $arrExportar, 
     También aquí se podrían cambiar el nombre por otros más adecuados con algo 
     como: 		if ($campo == 'IMPORTEDONACION' )	{$campo = 'IMPORTE DE LA DONACION'}			
		*/			
		 $csv_outputHtmlTab .= '<th>'.htmlspecialchars($campo).'</th>'; 	
 }	
	
	$csv_outputHtmlTab .= '</tr>'; //$csv_output .= "\n";				
	
	$f = 0;
 $fUltima=$arrExportar['numFilas'];
 
 while ($f < $fUltima)
 {$csv_outputHtmlTab .= '<tr>';
	 
		foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)                         
	 {
			if(!isset($valCampo) || empty($valCampo))
			{
				$csv_outputHtmlTab .= '<td>'.'NULL'.'</td>'; //no afecta
			}			
			else //	!if(!isset($valCampo) || empty($valCampo))
			{	
					//---- Para sustituir decimales de MYSQL tipo 54.01 a formato Excel tipo decimales 54,01 ------					
					if ($campo == 'IMPORTEDONACION' || $campo == 'GASTOSDONACION')//estos dos valores se formatean como números decimales en Excel
					{ 
						//Sustituir números decimales con punto decimal formato 5.02 (MySQL), a coma decimal 5,02 para formato operaciones en Excel							
						$valCampo=str_replace(".", ",", $valCampo);//Funciona para números enteros y decimales: 5.02->5,02, pero 5.00->5 (no añade ceros .00)

						//Excel: mso-number-format:"0\.00" pone números en formato 2 decimales con coma, enteros también	5,02->5,02; y 5 ->5.00	
						$csv_outputHtmlTab .= "<td style="."mso-number-format:'0\.00';".">".$valCampo."</td>";	//mso-number-format:"0\.00" 2 decimales en el Excel
					}
					else
					{	//Estos valores tendrán formato de texto en Excel, aunque sean números por ejemplo: CODDONACION
							//style="."mso-number-format:'\@' para que en Excel lo ponga todo en formato de texto
							
							if( version_compare(PHP_VERSION, '5.4.0') >= 0) 
							{ // A PARTIR DEL CAMBIO A LA VERSIÓN PHP 5.4.0 o superior, para htmlspecialchars ES NECESARIO EL SIGUIENTE FORMATO:
									// utf8_decode simply converts a string encoded in UTF-8 que proviene de la BBDD a ISO-8859-1 
									
									// style="."mso-number-format:'\@' para que en Excel "$valCampo" lo ponga todo en formato de texto aunque sea un número, 
									// por ejemplo CP y así no quitará ceros a la izda . 
									$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo), ENT_COMPAT,'ISO-8859-1', true).'</td>';
							}
							else
							{  // ANTES DEL CAMBIO A LA VERSIÓN PHP 5.4.0 htmlspecialchars FUNCIONABA CON EL SIGUIENTE FORMATO:
											$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo)).'</td>';
							}	
					}					
			}//else 	!if(!isset($valCampo) || empty($valCampo))	
  }//foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)  
	
		$csv_outputHtmlTab .= '</tr>'; 

		$f++;
	}		 
 $csv_outputHtmlTab .= '</table>';
	
	$filename = $file."_".date("Y-m-d_H-i",time());
	
	header("Content-type: application/vnd.ms-excel");	//original
	//header("Content-type: application/vnd.ms-excel;charset=utf-8");//también funciona

 header( "Content-disposition: attachment;filename=".$filename.".xls"); //descarga del archivo con un nombre de archivo determinado  

	header('Content-Length: '. strlen($csv_outputHtmlTab) );	// indicar tamaño del archivo  
	
	header("Cache-Control: no-cache"); // HTTP/1.1
 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado para que no guarde en cahé
	
	echo $csv_outputHtmlTab; //salida buffer
		
		
	if (ob_end_flush())//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida,
	{ $reExportar['codError'] = '00000';		 
	}
	else //si no se puede cerrar el buffer, porque no este abierto u otras causas dará error
	{ $reExportar['codError'] = '70620';
	  $reExportar['errorMensaje'] ='Error al cerrar el buffer interno';
	}	

	return 	$reExportar;//solo devuelve valores cuando hay error 
}
//----------------------- Fin exportarExcelDonaciones() ------------------------------------------
	

/*----------------- Inicio exportarExcelCuotasTes() -----------------------------------------------
Función para generar y descargar ficheros Excel con cantidades de cuotas a pagar para uso interno,
y banco Tríodos(este ya no se usa pero es muy util para trabajo de Tesorería), y otros usos.

Esta función sustituye los decimales en formato punto decimal tipo 54.01 de MySQL, a formato número de Excel 
con comas decimales 54,01 para los campos procedentes de MYSQL y PHP: 
'CuotaDonacionPendienteCobro','CuotaDonacionPendienteCobro','IMPORTECUOTAANIOSOCIO','IMPORTECUOTAANIOPAGADA'
'IMPORTEGASTOSABONOCUOTA','IMPORTECUOTAANIOEL','saldo' con esas columnas adecuadas para operaciones 
aritméticas Excel de sumas, medias, ...
Los campos de agrupaciones territoriales,  CP, que tengan ceros a la izquierda que los los eliminaba
los dejará como formato texto es decir decir CP = 08010 CODAGRUPACION= 0141000.

Se cambia función htmlspecialchars() por cambio versión de PHP 5.2.3 a PHP > 5.4.4, en junio del 2016. 
														
RECIBE: 
$arrExportar, con formato $arrExportar['resultadoFilas'][$fila][campos filas]y $arrExportar['numFilas'],
$nomFile: nombre del fichero a exportar, al que se le añadirá la fecha y la extensión "xls"								
							
DEVUELVE: un archivo en formato .xls, al cerrar el buffer interno (indica)
																									
LLAMADA:	modeloTesorero.php:exportarCuotasExcelBancos(),exportarExcelCuotasInternoTes()												

OBSERVACIONES: Probado PHP 7.3.21
														
Al descargar el archivo sale un mensaje de Excel: "El formato y la extensión del 
archivo de Excel no coinciden ..." Puede que esté dañado o no sea seguro, lo abres de no hay problemas 

ob_start(): Esta función habilitará el uso de búferes internos para almacenar la salida. 
Mientras los búferes de salida están activos no se envía salida (navegador) desde el script PHP 
(más que las cabeceras), en su lugar la salida es almacenada en un búfer interno, hasta 
que se ordene explícitamente ob_end_flush u otras equivalente, o se acabe el script PHP.   

ob_gzhandler: para comprimir en  gzip el buffer de salida. Necesita la lib "zlib" en PHP
Antes de que ob_gzhandler() envíe realmente los datos, determina qué tipo 
de codificación de contenido acepta el navegador ("gzip", "deflate" o ninguno)

ob_end_flush(): indica a PHP que vuelque todos los contenidos de bufer interno(si existe) en la salida (navegador)
y deshabilita este búffer que fue habilitado con ob_start().

Si la función ob_end_flush(), no se ejecuta correctamente !=true, se devuelve error.


NOTA: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
Por defecto en explotación "usuarios" están desactivados los E_NOTICE y E_WARNING, pero para desarrollo
es aconsejable que estén activados, poner: com activo el archivo "index_si_muestra_errores.php" 
-----------------------------------------------------------------------------------------------------*/
function exportarExcelCuotasTes($arrExportar,$nomFile)//común para exportarCuotasExcelBancosTes(), exportarExcelCuotasInternoTes()				
{					
	//echo "<br><br>0-1 exportarExcel.php:exportarExcelCuotasTes:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>0-2 exportarExcel.php:exportarExcelCuotasTes:nomFile: ";print_r($nomFile); 
 
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);//Desactiva Notice, pero muestra errores de ejecución para evitar que salga defectuoso el Excel
	
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] ='';
	
	if ( !isset($arrExportar) || empty($arrExportar) ) 
	{	$reExportar['codError'] = '70601';//probado error sería un error que no permite formar correctamente el XML
			$reExportar['errorMensaje'] = 'ERROR: Faltan datos necesarios para formar y exportar el archivo Excel:  $arrExportar';
	}
	else // !if (!isset($arrExportar) || empty($arrExportar) ) 
	{	
			//si al comprimir en gzip !ob_start("ob_gzhandler") el buffer de salida da error, se usará sin comprimir ob_start()	
			if(!ob_start("ob_gzhandler")) ob_start();
				
			if ( !isset($nomFile) || empty($nomFile))
			{
					$file = 'archivo_Excel';	
			}
			else
			{
					$file = $nomFile;
			}
			
			$csv_outputHtmlTab = '<table border=1>'; 
			$csv_outputHtmlTab .= '<tr>';
				
			foreach ($arrExportar['resultadoFilas'][0] as $campo => $valCampo)                         
			{		
    /* Para el nombre de las columnas que vienen en fila 0 del array $arrExportar, 
     También aquí se podrían cambiar el nombre por otros más adecuados con algo 
     como: 		if ($campo == 'IMPORTEGASTOSABONOCUOTA' )	{$campo = 'IMPORTE DE GASTOS ABONOCUOTA'}			
		 */						
				$csv_outputHtmlTab .= '<th>'.htmlspecialchars($campo).'</th>'; //$csv_output .= $campo."; ";
			}	
			$csv_outputHtmlTab .= '</tr>'; //$csv_output .= "\n";				
			
			$f = 0;
			$fUltima = $arrExportar['numFilas'];
			
			while ($f < $fUltima)
			{
				$csv_outputHtmlTab .= '<tr>';
				
				foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)                         
				{
					if(!isset($valCampo) || empty($valCampo))
					{
						$csv_outputHtmlTab .= '<td>'.'NULL'.'</td>'; //no afecta
					}			
					else //if(isset($valCampo) && empty($valCampo))
					{	
							// Cambiar campos de cantidades "cuotas y gastos" que vienen en decimales MYSQL tipo 54.01 a formato Excel tipo decimales 54,01  					
							if ($campo == 'CuotaDonacionPendienteCobro' || $campo == 'IMPORTECUOTAANIOSOCIO' || $campo == 'IMPORTECUOTAANIOPAGADA' || 
											$campo =='IMPORTEGASTOSABONOCUOTA'      ||	$campo == 'IMPORTECUOTAANIOEL'    || $campo == 'saldo'
										)									
							{					
								//Sustituir números decimales con punto decimal formato 5.02 (MySQL), a coma decimal 5,02 para formato operaciones en Excel
								$valCampo = str_replace(".", ",", $valCampo);
														
								//Excel: mso-number-format:"0\.00" pone números en formato 2 decimales con coma, enteros también	5,02->5,02; y 5 ->5.00	
								$csv_outputHtmlTab .= "<td style="."mso-number-format:'0\.00';".">".$valCampo."</td>";
								
							}
							else // !if ($campo == 'CuotaDonacionPendienteCobro' || $campo == 'IMPORTECUOTAANIOSOCIO' ...
							{														
									if( version_compare(PHP_VERSION, '5.4.0') >= 0) 
									{ // A PARTIR DEL CAMBIO A LA VERSIÓN PHP 5.4.0 o superior, para htmlspecialchars ES NECESARIO EL SIGUIENTE FORMATO:
											// utf8_decode simply converts a string encoded in UTF-8 que proviene de la BBDD a ISO-8859-1 
											
											// style="."mso-number-format:'\@' para que en Excel "$valCampo" lo ponga todo en formato de texto aunque sea un número, 
											// por ejemplo CP y así no quitará ceros a la izda . 
											
											$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo),ENT_COMPAT,'ISO-8859-1',true).'</td>';
									}
									else
									{ // ANTES DEL CAMBIO A LA VERSIÓN PHP 5.4.0 htmlspecialchars FUNCIONABA CON EL SIGUIENTE FORMATO:
											$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo)).'</td>';//@: Para que excel lo ponga como texto
									}						
									
							}//	else // !if ($campo == 'CuotaDonacionPendienteCobro' || ...				
					}//else if(isset($valCampo) && empty($valCampo))		
				}
				$csv_outputHtmlTab .= '</tr>'; 

				$f++;
			}		
			
			$csv_outputHtmlTab .= '</table>';
			
			$filename = $file."_".date("Y-m-d_H-i-s",time());
			
			header("Content-type: application/vnd.ms-excel");	//original
			//header("Content-type: application/vnd.ms-excel;charset=utf-8");//también funciona

			header( "Content-disposition: attachment;filename=".$filename.".xls"); //descarga del archivo con un nombre de archivo determinado 	
			
			header('Content-Length: '. strlen($csv_outputHtmlTab) ); // indicar tamaño del archivo 
			
			header("Cache-Control: no-cache"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado para que no guarde en cahé
			
			echo $csv_outputHtmlTab;//salida buffer
				
			if (ob_end_flush())//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida,
			{ $reExportar['codError'] = '00000';		 
			}
			else //si no se puede cerrar el buffer, porque no este abierto u otras causas dará error
			{ $reExportar['codError'] = '70620';
					$reExportar['errorMensaje'] ='Error al cerrar el buffer interno';
			}	
 }//else // !if (!isset($arrExportar) || empty($arrExportar) ) 
	
	return 	$reExportar;//solo devuelve valores cuando hay error 
}
/*----------------------- Fin exportarExcelCuotasTes -------------------------------------------------*/


/*=== INICIO FUNCIONES PARA GENERAR FICHEROS EXCEL Y GUARDARLOS EN EL SERVIDOR  ===================*/
/*===================== POR AHORA SIN UTILIZAR ====================================================*/

/*------------------------------------- exportarExcelEnServidor() ---------------------------------
Función para generar ficheros Excel con los campos tipo texto y guardarlos en un directorio del servidor, 
por ejemplo: para Tríodos, uso interno, donaciones. etc. y otros usos 
															
RECIBE: $arrExportar, con formato $arrExportar['resultadoFilas'][$fila][campos de las filas] 
y $arrExportar['numFilas'].
$directorioArchivo es opcional, si no se guardará en $directorioArchivo = '/usuarios_desarrollo';
$nomFile: nombre del fichero a exportar, al que se le añadirá la fecha y la extensión "xls"
								
DEVUELVE: archivo formato .xls, grabador en el servidor. Todo como texto y como cantidades decimales
		
LLAMADO: Se podría llamar desde funciones como: modeloPresCoord.php:exportarExcelSociosPres(),
exportarExcelSociosCoord(),mExportarExcelInformeAnualPres(), modeloTesorero:exportarDonacionesExcel()
....

OBSERVACIONES: Después de descargar el archivo, se podría descargar para su posterior utilización

- Las celdas se exportan a Excel en formato "Texto", 

NOTA: Esta función exportarExcelEnServidor() es la equivalente exportarExcel(), que se descarga 
en el momento y no guarda en el servidor

probada PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function exportarExcelEnServidor($arrExportar,$directorioArchivo=NULL,$nomFile)	
//function exportarExcelEnServidor($arrExportar,$nomFile)
{
	//echo "<br><br>0-1 exportarExcel.php:exportarExcelEnServidor:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>0-2 exportarExcel.php:exportarExcelEnServidor:nomFile: ";print_r($nomFile); 
 
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] ='';
	
 if ( !isset($nomFile) || empty($nomFile))
	{
			$file = 'archivo_export';	
	}
	else
	{
		 $file = $nomFile;
	}
	
	if ( !isset($directorioArchivo) || empty($directorioArchivo))
	{
				$directorioArchivo = '/usuarios_desarrollo';
				//$directorioArchivo = '/home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022';
	}
	else
	{
			$directorioArchivo = $directorioArchivo;
	}		
	
 $csv_outputHtmlTab = '<table border=1>'; 
	$csv_outputHtmlTab .= '<tr>';
		
	foreach ($arrExportar['resultadoFilas'][0] as $campo => $valCampo)                         
	{$csv_outputHtmlTab .= '<th>'.htmlspecialchars($campo).'</th>';	
	 //$csv_output .= $campo."; ";
 }	
	$csv_outputHtmlTab .= '</tr>';  
 //$csv_output .= "\n";				
	
	$f = 0;
 $fUltima=$arrExportar['numFilas'];
 
 while ($f < $fUltima)
 {$csv_outputHtmlTab .= '<tr>';
	 
		foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)                         
	 {
			if(!isset($valCampo) || empty($valCampo))
   {
		  $csv_outputHtmlTab .= '<td>'.'NULL'.'</td>'; //no afecta
   }			
			else //if(isset($valCampo) && empty($valCampo))
			{
					//Sustituir números decimales con punto decimal formato 5.02 (MySQL), a coma decimal 5,02 para formato operaciones en Excel

					if(is_numeric ($valCampo)) //acaso lo pueda descomentar por estar ya tratado en la select SQL con REPLACE, o en la función de modelo correspondiente
					{//$valCampoX=number_format($valCampo, 2, ',','');//Pone dos decimales en todos los números incluso enteros
					
						$valCampo = str_replace(".", ",", $valCampo);//Cambia el punto decimal por una coma, si funciona. 
					}	
					//$csv_outputHtmlTab .= '<td>'.htmlspecialchars(utf8_decode($valCampo)).'</td>';
					//Nota: con el siguiente formato lo pone todo como formato texto para Excel			
					if( version_compare(PHP_VERSION, '5.4.0') >= 0) 
					{ // A PARTIR DEL CAMBIO A LA VERSIÓN PHP 5.4.0 o superior, para htmlspecialchars ES NECESARIO EL SIGUIENTE FORMATO:
							// utf8_decode simply converts a string encoded in UTF-8 que proviene de la BBDD a ISO-8859-1 
											
							// style="."mso-number-format:'\@' para que en Excel "$valCampo" lo ponga todo en formato de texto aunque sea un número, 
							// por ejemplo CP y así no quitará ceros a la izda . 
							$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo), ENT_COMPAT,'ISO-8859-1', true).'</td>';
					}
					else
					{ // ANTES DEL CAMBIO A LA VERSIÓN PHP 5.4.0 htmlspecialchars FUNCIONABA CON EL SIGUIENTE FORMATO:
							$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo)).'</td>';
					}			

			}//else //if(isset($valCampo) && empty($valCampo))
			
  }
		$csv_outputHtmlTab .= '</tr>'; 

		$f++;
	}		 
 $csv_outputHtmlTab .= '</table>';
		
	$cadenaTexto =  $csv_outputHtmlTab;
	
	
	//$filename = $file."_".date("Y-m-d_H-i-s",time());
	//Ponemos este formato, porque la validación sanearNombreArchivo() dentro de crearEscribirArchivoServidor() no admite guiones medios para nombre de Archivo
	$filename = $file."_".date("Y_m_d_H_i_s",time());

	$nomArchivo =	$filename;
	
	require_once './modelos/modeloArchivos.php';
	$reExportar = crearEscribirArchivoServidor($cadenaTexto,$directorioArchivo,$nomArchivo);
	
	//echo "<br><br>2 exportarExcel.php:exportarExcelEnServidor:reExportar: ";print_r($reExportar);

	return 	$reExportar; 
}
/*----------------------- Fin exportarExcelEnServidor() ------------------------------------------*/
	

/*----------------- Inicio exportarExcelCuotasTesEnServidor() -------------------------------------
Función para generar ficheros Excel con cantidades de cuotas a pagar para uso interno, y guardarlos
en un directorio del servidor. 
Ejemplo: para Tríodos, uso interno, donaciones. etc. y otros usos 
							

Esta función sustituye los decimales en formato punto decimal tipo 54.01 de MySQL, a formato número de Excel 
con comas decimales 54,01 para los campos procedentes de MYSQL y PHP: 
'CuotaDonacionPendienteCobro','CuotaDonacionPendienteCobro','IMPORTECUOTAANIOSOCIO','IMPORTECUOTAANIOPAGADA'
'IMPORTEGASTOSABONOCUOTA','IMPORTECUOTAANIOEL','saldo' con esas columnas adecuadas para operaciones 
aritméticas Excel de sumas, medias, ...
Los campos de agrupaciones territoriales,  CP, que tengan ceros a la izquierda que los los eliminaba
los dejará como formato texto es decir decir CP = 08010 CODAGRUPACION= 0141000.

Se cambia función htmlspecialchars() por cambio versión de PHP 5.2.3 a PHP > 5.4.4, en junio del 2016. 
															
RECIBE: $arrExportar, con formato $arrExportar['resultadoFilas'][$fila][campos de las filas] 
y $arrExportar['numFilas'].
$directorioArchivo es opcional, si no se guardará en $directorioArchivo = '/usuarios_desarrollo';
$nomFile: nombre del fichero a exportar, al que se le añadirá la fecha y la extensión "xls"							
							
DEVUELVE: un archivo en formato .xls, grabador en el servidor.
																									
LLAMADA:	 Se podría llamar desde funciones como: modeloTesorero.php:exportarCuotasExcelBancos(),
exportarExcelCuotasInternoTes()	.									

OBSERVACIONES: Probado PHP 7.3.21
Al descargar el archivo sale un mensaje de Excel: "El formato y la extensión del 
archivo de Excel no coinciden ..." Puede que esté dañado o no sea seguro, lo abres de no hay problemas 

NOTA: 
- Esta función exportarExcelCuotasTesEnServidor() es la equivalente exportarExcelCuotasTes(), 
que se descarga en el momento y no guarda en el servidor

- Probar ejemplo con funcion: modeloTesorero.php:exportarCuotasExcelBancos() descomentanto línea
--------------------------------------------------------------------------------------------------*/
function exportarExcelCuotasTesEnServidor($arrExportar,$directorioArchivo=NULL,$nomFile)	
//function exportarExcelCuotasTesEnServidor($arrExportar,$nomFile)//común para exportarCuotasExcelBancosTes(), exportarExcelCuotasInternoTes()				
{					
	//echo "<br><br>0-1 exportarExcel.php:exportarExcelCuotasTesEnServidor:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>0-2 exportarExcel.php:exportarExcelCuotasTesEnServidor:nomFile: ";print_r($nomFile); 
 
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);//Desactiva Notice, pero muestra errores de ejecución para evitar que salga defectuoso el Excel
	
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] ='';
	
	if ( !isset($arrExportar) || empty($arrExportar) ) 
	{	$reExportar['codError'] = '70601';//probado error sería un error que no permite formar correctamente el XML
			$reExportar['errorMensaje'] = 'ERROR: Faltan datos necesarios para formar y exportar el archivo Excel:  $arrExportar';
	}
	else // !if (!isset($arrExportar) || empty($arrExportar) ) 
	{	
				
			if ( !isset($nomFile) || empty($nomFile))
			{
					$file = 'archivo_Excel';	
			}
			else
			{
					$file = $nomFile;
			}			
			
			if ( !isset($directorioArchivo) || empty($directorioArchivo))
			{
						$directorioArchivo = '/usuarios_desarrollo';
	     //$directorioArchivo = '/home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022';
			}
			else
			{
					$directorioArchivo = $directorioArchivo;
			}			
			
			$csv_outputHtmlTab = '<table border=1>'; 
			$csv_outputHtmlTab .= '<tr>';
				
			foreach ($arrExportar['resultadoFilas'][0] as $campo => $valCampo)                         
			{		 
				$csv_outputHtmlTab .= '<th>'.htmlspecialchars($campo).'</th>'; //$csv_output .= $campo."; ";
			}	
			$csv_outputHtmlTab .= '</tr>'; //$csv_output .= "\n";				
			
			$f = 0;
			$fUltima = $arrExportar['numFilas'];
			
			while ($f < $fUltima)
			{
				$csv_outputHtmlTab .= '<tr>';
				
				foreach ($arrExportar['resultadoFilas'][$f] as $campo => $valCampo)                         
				{
					if(!isset($valCampo) || empty($valCampo))
					{
						$csv_outputHtmlTab .= '<td>'.'NULL'.'</td>'; //no afecta
					}			
					else //if(isset($valCampo) && empty($valCampo))
					{	
							// Cambiar campos de cantidades "cuotas y gastos" que vienen en decimales MYSQL tipo 54.01 a formato Excel tipo decimales 54,01  					
							if ($campo == 'CuotaDonacionPendienteCobro' || $campo == 'IMPORTECUOTAANIOSOCIO' || $campo == 'IMPORTECUOTAANIOPAGADA' || 
											$campo =='IMPORTEGASTOSABONOCUOTA'      ||	$campo == 'IMPORTECUOTAANIOEL'    || $campo == 'saldo'
										)									
							{					
								//Sustituir números decimales con punto decimal formato 5.02 (MySQL), a coma decimal 5,02 para formato operaciones en Excel
								$valCampo = str_replace(".", ",", $valCampo);
														
								//Excel: mso-number-format:"0\.00" pone números en formato 2 decimales con coma, enteros también	5,02->5,02; y 5 ->5.00	
								$csv_outputHtmlTab .= "<td style="."mso-number-format:'0\.00';".">".$valCampo."</td>";
								
							}
							else // !if ($campo == 'CuotaDonacionPendienteCobro' || $campo == 'IMPORTECUOTAANIOSOCIO' ...
							{														
									if( version_compare(PHP_VERSION, '5.4.0') >= 0) 
									{ // A PARTIR DEL CAMBIO A LA VERSIÓN PHP 5.4.0 o superior, para htmlspecialchars ES NECESARIO EL SIGUIENTE FORMATO:
											// utf8_decode simply converts a string encoded in UTF-8 que proviene de la BBDD a ISO-8859-1 
											
											// style="."mso-number-format:'\@' para que en Excel "$valCampo" lo ponga todo en formato de texto aunque sea un número, 
											// por ejemplo CP y así no quitará ceros a la izda . 
											
											$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo),ENT_COMPAT,'ISO-8859-1',true).'</td>';
									}
									else
									{ // ANTES DEL CAMBIO A LA VERSIÓN PHP 5.4.0 htmlspecialchars FUNCIONABA CON EL SIGUIENTE FORMATO:
											$csv_outputHtmlTab .= "<td style="."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo)).'</td>';//@: Para que excel lo ponga como texto
									}						
									
							}//	else // !if ($campo == 'CuotaDonacionPendienteCobro' || ...				
					}//else if(isset($valCampo) && empty($valCampo))		
				}
				$csv_outputHtmlTab .= '</tr>'; 

				$f++;
			}		
			
			$csv_outputHtmlTab .= '</table>';
			
			//$filename = $file."_".date("Y-m-d_H-i-s",time());
			//Ponemos este formato, porque la validación sanearNombreArchivo() dentro de crearEscribirArchivoServidor() no admite guiones medios para nombre de Archivo
			$filename = $file."_".date("Y_m_d_H_i_s",time());
			
			$cadenaTexto =  $csv_outputHtmlTab;
			
			$nomArchivo =	$filename.".xls";
				
			//echo "<br><br>2-1 exportarExcel.php:exportarExcelCuotasTesEnServidor:nomArchivo: ";print_r($nomArchivo); 
			
			require_once './modelos/modeloArchivos.php';
			$reExportar = crearEscribirArchivoServidor($cadenaTexto,$directorioArchivo,$nomArchivo);
			
			//echo "<br><br>2-2 exportarExcel.php:exportarExcelCuotasTesEnServidor:reExportar: ";print_r($reExportar);

 }//else // !if (!isset($arrExportar) || empty($arrExportar) ) 
	
	return 	$reExportar;//solo devuelve valores cuando hay error 
}
/*----------------------- Fin exportarExcelCuotasTesEnServidor() ---------------------------------*/

/*=== FIN FUNCIONES PARA GENERAR FICHEROS EXCEL Y GUARDARLOS EN EL SERVIDOR  ======================*/
/*===================== POR AHORA SIN UTILIZAR ====================================================*/


?>