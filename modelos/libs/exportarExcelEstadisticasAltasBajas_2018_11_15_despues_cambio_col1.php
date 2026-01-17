<?php
/*------------------------------------- exportarExcelEstadisticasAltasBajas.php ----------------------------------------
FICHERO: exportarExcelEstadisticasAltasBajas.php
Agustin 2018-11-15: añado paramétro $cabeceraColum1 para que sirva en modeloPresCoord.php en varias 
                    funciones: mExportarExcelEstadisticasAltasBajasAgrupPres(),
																				mExportarExcelEstadisticasAltasBajasProvPres()
Agustin 2017-02-25: Modificación para que abra bien en todos los navegadores.
Agustin 2017-01-15: Creacion de esta función, 

PROYECTO: EL
VERSION: PHP 5.4.4

DESCRIPCION: Función para generar el fichero Excel con las estadísticas por agrupaciones y año a 
             fecha Y-12-31 con los datos siguientes: 
Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)
																									
Llamado desde:	modeloPresCoord.php: mmExportarExcelEstadisticasAltasBajasAgrupPres(),
														mExportarExcelEstadisticasAltasBajasProvPres()											
															
Recibe: $arrExportar, con fomato adecuado a los campos que hay que formar.
        $cabeceraColum1: nombre de la cabecera de la tabla excel: AGRUPACIONES, PROVINCIAS, etc. 
								$nomFile:  nombre del fichero a exportar, al que se le añadirá la fecha y la extensión "xls"	
								$anioInferior,$anioSuperior, años entre los que se van a obtener los datos estadísticos
								
Devuelve: un archivo en formato .xls, al cerrar el buffer interno.
															 
OBSERVACIONES: -Como header() debe ejecutarse antes de haber enviado ningún texto de la pág. al cliente, 
previamente utilizamos un buffer derivar las salidas

A fecha de 2015-05-15 Funciona bien con XP Firefox 29.0.1, Safari 5.1.5 y 
tiene problemas al generar el Excel con Crome 34.0.1847.137, Edge, IE 8.0.6 y Opera

-Las celdas se exportan a Excel en formato "General", los número de más de 11 
dígitos aparecen en notación científica.

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
-------------------------------------------------------------------------------*/
function exportarExcelEstadisticasAltasBajas($arrExportar,$cabeceraColum1,$nomFile,$anioInferior,$anioSuperior)
{//echo "<br><br>1-a exportarExcelEstadisticasAltasBajas:arrExportar: ";print_r($arrExportar); 
 //echo "<br><br>1-b exportarExcelEstadisticasAltasBajas:nomFile: ";print_r($nomFile); 
	//echo "<br><br>1-b exportarExcelEstadisticasAltasBajas:anioInferior: ";print_r($anioInferior); 
	//echo "<br><br>1-b exportarExcelEstadisticasAltasBajas:anioSuperior: ";print_r($anioSuperior); 
 
	$reExportar['codError'] = '00000';
	
	$colorBlanco ='#FFFFFF';//
	$colorNegro ='#000000';//
	$colorAzulIntenso ='#000066';//
	$colorAzulPalido ='#BBFFFF';//#D6EAF8 	
	$colorGris ='#DBDBDB';//$colorGris ='#404040';
 $colorGrisPalido ='#E5E7E9'; 
	$colorAmarilloIntenso ='#FFFF00';//$colorAmarilloPalido ='#FFEC8B';//#FCF3CF  
	$colorVerdePalido ='#C1FFC1'; //$colorVioleta ='#9B59B6';  $colorVioletaPalido ='#EBDEF0  	';
	$colorRosaPalido ='#FFE4E1';	//$colorRojoPalido ='#FADBD8';
 
 if(!ob_start("ob_gzhandler")) ob_start();//si al comprimir en gzip !ob_start("ob_gzhandler") el buffer de salida da error, se usará sin comprimir ob_start()	
		
 if ( !isset($nomFile) || empty($nomFile))
	{	$file = 'archivo_export';	
	}
	else
	{ $file = $nomFile;
	}

	$csv_outputHtmlTab = '<table border=1>';//<table width='100%' border='1' cellspacing='0' cellpadding='0' bordercolor='#99CCFF'>
	
	//--- Inicio primera fila cabecera --------------
		$csv_outputHtmlTab .= '<tr>';
		
		//$csv_outputHtmlTab .= "<th rowspan='3' bgcolor='".$colorGris."' align='left'  style="."color:".$colorNegro.";"."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode('Agrupación'), ENT_COMPAT,'ISO-8859-1', true).'</th>';										
		$csv_outputHtmlTab .= "<th rowspan='3' bgcolor='".$colorGris."' align='left'  style="."color:".$colorNegro.";"."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($cabeceraColum1), ENT_COMPAT,'ISO-8859-1', true).'</th>';										
	
			for ($y = $anioInferior; $y <= $anioSuperior; $y++)
			{	$csv_outputHtmlTab .= 	"<th colspan='16' bgcolor='".$colorGris."'>".$y.'</th>';
			}
		$csv_outputHtmlTab .= '</tr>';
		//--- Fin primera fila cabecera -----------------
		
		//--- Inicio segunda fila cabecera --------------
		$csv_outputHtmlTab .= "<tr>";
			for ($y = $anioInferior; $y <= $anioSuperior; $y++)
			{ $csv_outputHtmlTab .= 	"<th colspan='5' bgcolor='".$colorGris."'>".'&nbsp;&nbsp;&nbsp;'.htmlspecialchars(utf8_decode('SOCIXS FINAL DE AÑO '.$y), ENT_COMPAT,'ISO-8859-1', true).'&nbsp;&nbsp;&nbsp;</th>';//TotalSocixsDeAlta				
     $csv_outputHtmlTab .= 	"<th colspan='5' bgcolor='".$colorGris."'>".htmlspecialchars(utf8_decode('ALTAS AÑO '.$y), ENT_COMPAT,'ISO-8859-1', true).'</th>';//TotalAltas año				
	    $csv_outputHtmlTab .= 	"<th colspan='5' bgcolor='".$colorGris."'>".htmlspecialchars(utf8_decode('BAJAS AÑO '.$y), ENT_COMPAT,'ISO-8859-1', true).'</th>';//TotalBajas año				
					$csv_outputHtmlTab .= 	"<th rowspan='2' bgcolor='".$colorGris."'>".'Altas<br />Netas<br /> '.$y.'</th>';
			} 
		$csv_outputHtmlTab .= '</tr>';
		//--- Fin segunda fila cabecera -----------------
		
		//--- Inicio tercera fila cabecera --------------
		$csv_outputHtmlTab .= "<tr>";

			for($y = $anioInferior; $y <= $anioSuperior; $y++)
			{ $csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;Total&nbsp;'.'</th>';//TotalDeAlta
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'H'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%H'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'M'.'</th>'; 						
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%M'.'</th>'; 
					
		   $csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;Total&nbsp;'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;H'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%H'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;M'.'</th>'; 						
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%M'.'</th>'; 
					
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;Total&nbsp;'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;H'.'</th>';  
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%H'.'</th>';
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'&nbsp;M'.'</th>';  						
					$csv_outputHtmlTab .= 	"<th bgcolor='".$colorGris."'>".'%M'.'</th>'; 		
			}			
		$csv_outputHtmlTab .= '</tr>';		
		//--- Fin tercera fila cabecera --------------
		
		//--- Inicio filas de datos  --------------------------------------------
		foreach ($arrExportar as $campo1 => $valCampo1)   				                      
		{ //echo "<br><br>1-C1-1 exportarExcelEstadisticasAltasBajas:arrExportar: $y: campo1: ";print_r($campo1)	;	echo ", valCampo1: ";print_r($valCampo1);					
			$bgcolor = $colorBlanco;
			$color = $colorNegro;
		 //-----	
			if ( $campo1 =='Todas')
			{ //$bgcolor = $colorAmarilloIntenso;
  	 $bgcolor = $colorGrisPalido	;
    $color = $colorNegro;		
							
			}		
	  //-----					
			$csv_outputHtmlTab .= '<tr>';				
				//$csv_outputHtmlTab .= '<td>'.$campo1.'</td>';//Bien sin problemas de acentos
				$csv_outputHtmlTab .= "<td bgcolor='".$bgcolor."' align='left'  style="."color:".$color.";"."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($campo1), ENT_COMPAT,'ISO-8859-1', true).'</td>';										
				
				for($y = $anioInferior; $y <= $anioSuperior; $y++)
				{	
					foreach ($valCampo1[$y] as $campo2 => $valCampo2)   				                      
					{	//echo "<br><br>1-C2-1 exportarExcelEstadisticasAltasBajas:arrExportar: $y: campo2: ";print_r($campo2);	echo ", valCampo2: ";print_r($valCampo2);						
					
							foreach ($valCampo2 as $campo3 => $valCampo3)   				                      
							{//	echo "<br><br>1-C3-1 exportarExcelEstadisticasAltasBajas:arrExportar: $y: campo3: ";print_r($campo3)	;	echo ", valCampo3: ";print_r($valCampo3);												    										

									if ( $campo2 =='TotalSocixsDeAlta')
									{ //$bgcolor = $colorAmarilloIntenso;
											if ($campo3 == 'TotalDeAlta')
											{	 $bgcolor = $colorAmarilloIntenso;
											}
											else
											{ $bgcolor = $colorBlanco;								
											}									
									}										
									elseif ( $campo2 == 'ALTAS')
									{ //$bgcolor = $colorVerdePalido;		
												if ($campo3 == 'Total')
												{ //echo "<br><br>1-C3-5 exportarExcelEstadisticasAltasBajas:arrExportar: $y: valCampo3[ALTAS][Total]: ";print_r($valCampo3);													
														$altasAnioAgrupacion = $valCampo3;	//se podría hacer en modeloAdmin
														$bgcolor = $colorVerdePalido;	
												}
												elseif ($campo3 =='H' || $campo3 =='M' || $campo3 =='%H' || $campo3 =='%M')
												{	$bgcolor = $colorBlanco;	
												}
									}
									elseif ( $campo2 == 'BAJAS')
									{ //$bgcolor = $colorRosaPalido;
												if ($campo3 == 'Total')
												{ //echo "<br><br>1-C3-5 exportarExcelEstadisticasAltasBajas:arrExportar: $y: valCampo3[ALTAS][Total]: ";print_r($valCampo3);													
														$bajasAnioAgrupacion = $valCampo3;	//se podría hacer en modeloAdmin
														$bgcolor = $colorRosaPalido;
												}
												elseif ($campo3 =='H' || $campo3 =='M' || $campo3 =='%H' || $campo3 =='%M')
												{	$bgcolor = $colorBlanco;	
												}												
									}
								 //-----	
									if ( $campo1 =='Todas')
									{ //$bgcolor = $colorAmarilloIntenso;
										$bgcolor = $colorGrisPalido	;
										$color = $colorNegro;		
													
									}		
							  //-----							

									$csv_outputHtmlTab .= "<td bgcolor='".$bgcolor."' align='right'  style="."color:".$color.";"."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($valCampo3), ENT_COMPAT,'ISO-8859-1', true).'</td>';										
									
							}	//foreach ($valCampo2 as $campo3 => $valCampo3)	
					}	//foreach ($valCampo1[$y] as $campo2 => $valCampo2)
		
					$altasNetasAnioAgrupacion = $altasAnioAgrupacion - $bajasAnioAgrupacion;//Mejor hacerlo en Modelo
					$bgcolor =$colorAzulPalido;	
     $color = $colorAzulIntenso;
					
					//echo "<br><br>1-C3-5-7 exportarExcelEstadisticasAltasBajas:arrExportar: $y: altasNetasAnioAgrupación: ";print_r($altasNetasAnioAgrupacion);
			
  			$csv_outputHtmlTab .= "<td bgcolor='".$bgcolor."' align='right'  style="."color:".$color.";"."mso-number-format:'\@';".">".htmlspecialchars(utf8_decode($altasNetasAnioAgrupacion), ENT_COMPAT,'ISO-8859-1', true).'</td>';							
				}//for($y = $anioInferior; $y <= $anioSuperior; $y++)	
					
				$csv_outputHtmlTab .= '</tr>';
		}//	foreach ($arrExportar as $campo1 => $valCampo1) 
		
		//--- Fin filas de datos  --------------------------------------------
		
		$csv_outputHtmlTab .= '</table>';		
	
	$filename = $file."_".date("Y-m-d_H-i",time());
	
	header("Content-type: application/vnd.ms-excel");	//header("Content-type: application/vnd.ms-excel;charset=utf-8");	//header("Content-type: application/vnd.ms-excel;charset=latin");	
	header( "Content-disposition: attachment;filename=".$filename.".xls"); //sin separación de campos	
	
	// indicar tamaño del archivo 
	header('Content-Length: '. strlen($csv_outputHtmlTab) ); 	

	header("Cache-Control: no-cache"); // HTTP/1.1
 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
	
	echo $csv_outputHtmlTab; 
	//exit;
 //ob_end_flush();

	if (ob_end_flush())//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida,
	{ $reExportar['codError'] = '00000';		 
	}
	else //si no se puede cerrar el buffer, porque no este abierto u otras causas dará error
	{ $reExportar['codError'] = '70620';
	  $reExportar['errorMensaje'] ='Error al cerrar el buffer interno';
	}	
	return $reExportar;
}
//----------------------- Fin exportarExcelEstadisticasAltasBajas.php --------------------------
?>