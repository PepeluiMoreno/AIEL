<?php
/*------------------------------- exportarBufferFicheroXML.php -----------------------
FICHERO: exportarBufferFicheroXML.php
VERSION: PHP 7.3.21

DESCRIPCION: En este fichero se encuentran la función para descargar el buffer en un 
             fichero tipo .txt, mediante ob_start("ob_gzhandler"), echo $txt_output;
													
RECIBE: Una cadena de texto y el nombre del fichero que se descargará en el 
        directorio de descargas del PC	
DEEVUELVE: un archivo que se desgar desde el nvagador en el PC, o array con datos error								
													
LLAMADA: modeloTesorero.php:SEPA_XML_SDD_CuotasTesoreroSantanderWrite()																						 													
													

OBSERVACIONES: 
2020-12-01: Añado control error no existencia parámetros de entrada
2017-10-23: cambios para recibir el nombre completo del archivo, sin necesidad de añadir la fecha aquí
2015-02-25: Funciona bien con win 10 Firefox 29.0.1, Edge, Chrome, Opera y Safari 5.1.5 

Como header() debe ejecutarse antes de haber enviado ningún 
texto de la pág. al cliente, no debe ejecutarse ningún echo, print, warning, notice o salida
por pantalla (en controlador, o modelo) previamente a utilizar el buffer. 

OJO, cuando se trabaja en modo ob_start(): guardará toda la salida en un buffer, 
y el código HTML, con echo, print_r() no se enviará al navegador hasta o se acabe 
el script PHP. Mejorar para controlar el buffer.
 
ob_gzhandler: para comprimir en  gzip el buffer de salida. Necesita la lib "zlib" en PHP
Antes de que ob_gzhandler() envíe realmente los datos, determina qué tipo 
de codificación de contenido acepta el navegador ("gzip", "deflate" o ninguno)

ob_end_flush():indica a PHP que vuelque de todo el bufer en la salida 

output_reset_rewrite_vars(): reestablece el mecanismo de re-escritura de URLs y 
remueve todas las variables de re-escritura definidas previamente por la 
función output_add_rewrite_var() o el mecanismo de sesiones 
(si session.use_trans_sid fue definido en session_start())
-----------------------------------------------------------------------------*/
function exportarBufferFicheroXML($cadenaTextoXML,$filename)	
{	
 $reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] = '';
	
	if ( (!isset($filename) || empty($filename)) || ( !isset($cadenaTextoXML) || empty($cadenaTextoXML))  )
	{
			$reExportar['codError'] = '70620';
			$reExportar['errorMensaje'] = 'ERROR: falta el contenido XML o el nombre del archivo a exportar en función exportarBufferFicheroXML()';	
			//echo '<br><br>1 exportarBufferFicheroXML:reExportar:' ;print_r($reExportar);
	} 
	else //!if ( (!isset($filename) || empty($filename)) || ( !isset($cadenaTextoXML) || empty($cadenaTextoXML))  )
	{		
		 //si ob_gzhandler: para comprimir en gzip el buffer de salida da error (problema con algún navegador), se usa sin comprimir .
		 if ( !ob_start("ob_gzhandler")) ob_start();//ob_start() abre un nuevo buffer. All las salidas del script echo, print_r, notices, se pondrán dentro del  buffer
	
		 $txt_output = $cadenaTextoXML;//incluye todo el contenido del archivo se va a descargar.	

			
			header("Content-type: application/xml");	//lo abre en el navegador como XML
							
			//header("Content-type: application/txt");//header("Content-type: application/txt; charset: UTF-8");	//lo descarga como fichero de texto y lo abre con block de notas

			header("Content-Disposition: attachment; filename=".$filename);//ya incluye extensión .xml y todos los datos incluidos en el nombre del archivo
						
			header("Cache-Control: no-cache, max-age=0"); //no guarda nada en cache para que siempre sean datos frescos //header("Cache-Control: no-cache"); // HTTP/1.1
			
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
			
			echo $txt_output;//vuelca en el buffer el contenido de la cadena txt_output

   if (!ob_end_flush() )//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida			
			{ 
			  $reExportar['codError'] = '70620';
					$reExportar['errorMensaje'] = 'Error al cerrar el buffer interno en exportarBufferFicheroXML()';		
     //echo '<br><br>2 exportarBufferFicheroXML:reExportar:' ;print_r($reExportar);					
			}
			
 }//else !if ( (!isset($filename) || empty($filename)) || ( !isset($cadenaTextoXML) || empty($cadenaTextoXML))  )
	
 return $reExportar;
}
	/*------ FIN exportarBufferFicheroXML.php ----------------------------------*/
?>