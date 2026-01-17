<?php
/*------------------------------- exportarBufferFicheroTXT.php ------------------------
FICHERO: exportarBufferFicheroTXT.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este fichero se encuentran la función para descargar el buffer en un 
             fichero tipo .txt, mediante ob_start("ob_gzhandler"), echo $txt_output;
													
RECIBE: Una cadena de texto y el nombre del fichero para descargarse en el PC	

DEVUELVE: un archivo en formato .TXT, al cerrar el buffer interno. Todo como texto y 
         como cantidades decimales, y mensaje de error si le hubiese 
									
LLAMADA: modeloTesorero.php:exportarEmailsWrite(), AEB19CuotasTesoreroSantanderWrite(),											

OBSERVACIONES: 
Como header() debe ejecutarse antes de haber enviado ningún 
texto de la pág. al cliente, no debe ejecutarse ningún echo print, o salida
por pantalla (en controlador, o modelo) previamente utilizamos un buffer derivar las salidas

ob_start(): guardará toda la salida en un buffer, el código HTML o con echo, 
no se enviará al navegador hasta que se ordene explícitamente, o se acabe 
el script PHP. 
ob_gzhandler: para comprimir en  gzip el buffer de salida. Necesita la lib "zlib" en PHP
Antes de que ob_gzhandler() envíe realmente los datos, determina qué tipo 
de codificación de contenido acepta el navegador ("gzip", "deflate" o ninguno)

ob_end_flush():indica a PHP que vuelque de todo el bufer en la salida

output_reset_rewrite_vars(): reestablece el mecanismo de re-escritura de URLs y remueve todas las variables 
de re-escritura definidas previamente por la función output_add_rewrite_var()
o el mecanismo de sesiones (si session.use_trans_sid fue definido en session_start())
---------------------------------------------------------------------------------------*/
function exportarBufferFicheroTXT($cadenaTexto,$nomFile)
{	
 $reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] = '';	
	
 //si ob_gzhandler: para comprimir en gzip el buffer de salida da error (problema con algún navegador), se usa sin comprimir .
	if(!ob_start("ob_gzhandler")) ob_start();

	if ( !isset($nomFile) || empty($nomFile))
	{
			$file = 'archivo_export';	
	}
	else
	{
		 $file = $nomFile;
	}	
	
 $txt_output = $cadenaTexto;		

	$filename = $nomFile."_".date("Y-m-d_H-i-s",time());
	
 header("Content-type: application/txt");	
	//header("Content-type: application/txt; charset: UTF-8");					
	header("Content-disposition: filename=".$filename.".txt");
	header("Cache-Control: no-cache"); // HTTP/1.1
 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado para que no guarde en cahé

	echo $txt_output; //salida buffer 


	if (ob_end_flush())//Fuerza a PHP a se realize el volcado de todo el bufer interno en la salida,
	{  $reExportar['codError'] = '00000';	
	  	output_reset_rewrite_vars();	
	}
	else //si no se puede cerrar el buffer, porque no este abierto u otras causas dará erro
	{ $reExportar['codError'] = '70620';
	  $reExportar['errorMensaje'] ='Error al cerrar el buffer interno';
	}

 return $reExportar;
}
/*------ FIN exportarBufferFicheroTXT.php ------------------------------------------------*/
?>