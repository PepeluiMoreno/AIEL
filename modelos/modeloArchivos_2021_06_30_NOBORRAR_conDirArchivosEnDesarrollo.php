<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: modeloArchivos.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye funciones para validar, subir, borrar, descargar archivos en el servidor 
             y otras relacionadas. 
													También al final hay funciones de pruebas no usadas que se pueden eliminar
													
LLAMADO: desde molelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor, que se llama a 
         su vez desde:cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(), 
         cTesorero.php:altaSocioPorGestorPres()	 

OBSERVACIONES: Al final hay unas funciones como almacén que pueden servir para probar y ampliar más
la sección de archivos.
--------------------------------------------------------------------------------------------------*/


/*-------------- IniciovalidarSubirArchivo() ------------------------------------------------------
Valida archivo que se va a subir al servidor para que cumpla ciertas condiciones en cuanto a tamaño 
archivo y nombre, tipos permitidos, que exista el directorio donde se va a subir y permita escritura, 
que	no exista otro archivo con el mismo nombre.											

RECIBE: $arrDatosArchivoSubir: array con datos: [name],[type],[size],[tmp_name],[error] viene de _$FILES 
        $directorioSubir: directorio relativo para guardar archivo. Ejem. /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR
								$arrMimeExtPermitidas: un array con tipos mime y las extensiones correspondiente permitidos,
								$tamanioMax = tamaño máximo del archivo eb bytes.

DEVUELVE: array "$arrSubirValidarArchivo" con con información de errores, y si es validado sin error, 
          también devuelve la  "extensión" correspondiente al tipo MIME del archivo
												
LLAMADA: modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php que a su vez se llama 
desde: cPresidente.php:altaSocioPorGestorPres(), cCoordinador:altaSocioPorGestorCoord(),
cTesorero.php:altaSocioPorGestorTes(),
Se podrá llamar desde otras funciones.

OBSERVACIONES: Probado PHP 7.3.21

NOTA: Parte de esta función estaría incluida en la función: subirValidarMoverArchivoClass() 
	     que utiliza la clase uploadVerot:class.upload.php, o en subirValidarMoverArchivoFunction() 
						que se hace con procedimientos	se podrían considerar como alternativas de uso	
--------------------------------------------------------------------------------------------------*/
function validarSubirArchivo($arrDatosArchivoSubir,$directorioSubir,$arrMimeExtPermitidas,$tamanioMax)
{
 //echo "<br /><br />0-1 validarSubirArchivo:arrDatosArchivoSubir: ";print_r($arrDatosArchivoSubir);//Array([name]=>Tabla_2018.doc [type]=>application/msword [tmp_name]=>/tmp/phpBSin7s [error]=>0 [size]=>37888 ) 
 //echo "<br /><br />0-2 validarSubirArchivo:directorioSubidas: ";print_r($directorioSubir); // /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR	
	//echo "<br /><br />0-3 validarSubirArchivo:arrMimeExtPermitidas: ";print_r($arrMimeExtPermitidas);//array("application/msword"	=>"doc", ... ,"application/pdf"=>"pdf")		
	
	$arrSubirValidarArchivo = $arrDatosArchivoSubir; //para devolver datos que proceden de _$FILES y lo que viene de validar
 //echo "<br /><br />0-4 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);
	
	$arrSubirValidarArchivo['codError'] = '00000';
	$arrSubirValidarArchivo['errorMensaje'] = "";
	
	//---  Inicio Preparar path absoluto ----------------------------------
	
 $directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorioSubir;		
	
	//echo "<br /><br />1 validarSubirArchivo:directorioSubirPath: ";print_r($directorioSubirPath);		
	//----- Fin Preparar path absoluto -------------------------------------
	
	/*----------- Inicio Validar archivo ----------------------------------------------------*/
	
	if (!isset($arrDatosArchivoSubir["name"]) || empty($arrDatosArchivoSubir["name"]))
	{ //echo "<br><br>2-1 validarSubirArchivo:arrDatosArchivoSubir[name]: ";print_r($arrDatosArchivoSubir["name"]);		
   $arrSubirValidarArchivo["codError"] = '82000';//4
		 $arrSubirValidarArchivo['errorMensaje'] = "ERROR: no has introducido el archivo";
	}
	elseif (!isset($arrDatosArchivoSubir["tmp_name"]) || empty($arrDatosArchivoSubir["tmp_name"]))
	{ //echo "<br><br>2-2 validarSubirArchivo:arrDatosArchivoSubir[tmp_name]: ";print_r($arrDatosArchivoSubir["tmp_name"]);	
   $arrSubirValidarArchivo["codError"] ='82040';//4
		 $arrSubirValidarArchivo['errorMensaje'] = "ERROR: no se ha creado el archivo temporal en el servidor";	
	}	 		
 elseif(!array_key_exists($arrDatosArchivoSubir["type"], $arrMimeExtPermitidas))	
 { 
	  //-- Obtener cadena de extensiones permitadas para mostrar en el mensaje de error en pantalla  
			$arrExtensionPerm =	array_values($arrMimeExtPermitidas ); //= Array([0]=>doc [1]=>doc [2]=>docx [3]=>odt [4]=>odi [5]=>gif [6]=>jpg [7]=>jpeg [8]=>pdf) 	
   $arrExtensionPerm = array_unique($arrExtensionPerm); //evita duplicados
   $cadExtPermitidas = implode(",", $arrExtensionPerm); //= doc,docx,odt,odi,gif,jpg,jpeg,pdf 		
			
   //--
		 $arrSubirValidarArchivo['codError'] = '82010';	 
   $arrSubirValidarArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$arrDatosArchivoSubir["name"]."\" es un tipo de archivo NO PERMITIDO (Sólo se aceptan: ". $cadExtPermitidas. ")";				
   //echo "<br /><br />2-3 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);			
 }
	elseif ($arrDatosArchivoSubir["size"] >= $tamanioMax)//El tamaño es en BYTES 
	{ $arrSubirValidarArchivo["codError"] ='82020';// "1","2"
			$arrSubirValidarArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$arrDatosArchivoSubir["name"]."\" es demasiado grande: ".round(($arrDatosArchivoSubir["size"]/1024/1024),2).
			"Kbytes (El máximo permitido son ".round(($tamanioMax/1024/1024),2)." MB)";			
			//echo "<br /><br />2-4-1 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);				
	}		
	elseif ($tamanioMax  > getsize(ini_get('upload_max_filesize')) ) // en modeloArchivos.php, supera la limitación del servidor para upload_files
	{ $tamanioMax = ini_get('upload_max_filesize');

   $arrSubirValidarArchivo["codError"] = '82020';// "1","2"
		 $arrSubirValidarArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$arrDatosArchivoSubir["name"]."\" es demasiado grande: ".round(($arrDatosArchivoSubir["size"]/1024/1024),2).
		 "MB (El máximo permitido por el servidor son ".round(($tamanioMax/1024/1024),2)." MB)";			
	  //echo "<br /><br />2-4-2 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);							
	}	
	elseif (($arrDatosArchivoSubir["size"] == 0))//EN BYTES
	{ $arrSubirValidarArchivo["codError"] ='82030';// "1","2"
			$arrSubirValidarArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$arrDatosArchivoSubir["name"]."\" está vacío: tamaño 0 K";			
			//echo "<br /><br />2-5 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);				
	}	   
 elseif ($arrDatosArchivoSubir["error"] > 0)
 { $arrSubirValidarArchivo["codError"] ='82000';//"3"		
			$arrSubirValidarArchivo['errorMensaje'] = "ERROR no identificado al subir archivo seleccionado previamente \"".$arrDatosArchivoSubir["name"]."\"";			
			//echo "<br /><br />2-6 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);				
 }	
	/*----------- Fin Validar archivo -------------------------------------------------------*/
	
	/*----------- Inicio Validar directorio -------------------------------------------------*/
	elseif (!isset($directorioSubir ) || empty($directorioSubir))
	{
		 $arrSubirValidarArchivo['codError'] = '82060';
  	$arrSubirValidarArchivo['errorMensaje'] = "ERROR: variable de directorio destino vacía ";	
	}	
	elseif (!realpath($directorioSubirPath))	
	{ //echo "<br><br>2-6-b-subirValidarMoverArchivo:directorioSubirPath: error: ".$directorioSubirPath;
	
	  $arrSubirValidarArchivo['codError'] = '82060';
  	$arrSubirValidarArchivo['errorMensaje'] = "ERROR: directorio absoluto de destino no existe ";			
	}	
	elseif (!is_dir(realpath($directorioSubirPath) ))
	{	//echo "<br /><br />2-7-a-subirValidarMoverArchivo:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	
	  $arrSubirValidarArchivo["codError"] ='82060';//"3"		
			$arrSubirValidarArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no es un directorio -".$directorioSubir."- <br /><br />";		
	}	
 elseif (strlen(realpath($directorioSubirPath)) + 255 > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
	{ /*	PHP_MAXPATHLEN: Número entero.Longitud máxima (incluyendo el árbol de directorios bajo el cual se encuentra y nombre completo fichero). 
	     255= máxima longitud nombre archivo en linux	    
	    echo "<br /><br />----- PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
	  */
	  //echo "<br /><br />2-7-b-subirValidarMoverArchivo:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
   $arrSubirValidarArchivo['codError'] = '82060';
	  $arrSubirValidarArchivo['errorMensaje'] = "ERROR: longitud del path del directorio supera el valor permitido por el sistema, guardar en un directorio de path más corto";			
	}	
	elseif (!is_writable(realpath($directorioSubirPath)) )
	{	//echo "<br /><br />2-7-c-subirValidarMoverArchivo:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	
	  $arrSubirValidarArchivo["codError"] ='82060';//"3"		
			$arrSubirValidarArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura -".$directorioSubir."- <br /><br />";		
 }
	/*----------- Fin Validar directorio ----------------------------------------------------*/
	
	else //=== Validación correcta sin errores, también devuelve extension
	{   
	  $arrSubirValidarArchivo['extension'] = $arrMimeExtPermitidas[$arrDatosArchivoSubir["type"]];//= extension	
		  
			//echo "<br /><br />3 validarSubirArchivo:arrSubirValidarArchivo['extension']: ";print_r($arrSubirValidarArchivo['extension']);// mostrará gif, o jpg, o pdf, etc.
	}		
	//--- Fin validar datos archivo -------------------------------------------------------------------

	//echo "<br><br>4 validarSubirArchivo:arrSubirValidarArchivo: ";print_r($arrSubirValidarArchivo);
	
	return $arrSubirValidarArchivo;		
}
/*-------------- Fin validarSubirArchivo() -------------------------------------------------------*/


/*-------------- Inicio moverArchivo() ------------------------------------------------------------
Mueve un archivo $arrDatosArchivoOrigen["tmp_name"] del directorio temporal del servidor (subido 
con HTTP POST $_FILES) a un directorio concreto, con un nombre y extension de archivo destino 
recibidos como parámetros (se sanea previamente ) y establece unos permisos sobre el archivo
determinados por parámetro $permisosArchivo, por defecto ='0444' solo lectura para todos
													
RECIBE: 
-$arrDatosArchivoSubir: array con datos: [name],[type],[size],[tmp_name],[error] viene de _$FILES 
 ([type],[size] ya deberán estar validados previamente)
 y acaso otros valores añadidos por form o programa. (aquí solo uso [name],[tmp_name] )
-$directorioSubir: directorio relativo donde se guardará el archivo. 
  Ejem. /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR, /documentos/ASOCIACION
-$nomArchivoSinExtDestino: nombre del archivo destino (sin la extensión),	
-$extension: extensión nombre del archivo destino (vine ya validado como permitida)
-$maxLongNomArchivoSinExtDestino: máxima longitud para este archivo por defecto 250
-$permisosArchivo = permisos que se asignarán al archivo, por defecto solo lectura 0444
	
DEVUELVE: array "$arrMoverArchivo" con información de errores, y ['nombreArchExtGuardado'] que 
          puede haber variado al ser saneado y ['directorioSubir']

LLAMADA: desde modeloPresCoord:mAltaSocioPorGestor(), a su vez se llama desde
cPresidente.php:altaSocioPorGestorPres(), cCoordinador:altaSocioPorGestorCoord(), 
cTesorero.php:altaSocioPorGestorCoord().
Al ser una función de tipo general también podría ser llamada desde diferente funciones

LLAMA: modeloArchivos:sanearNombreArchivo()

OBSERVACIONES: Probado PHP 7.3.21
	
NOTA: En su lugar se podría usar la función: subirValidarMoverArchivoClass() 
	     que utiliza la clase uploadVerot:class.upload.php  o en subirValidarMoverArchivoFunction()
						se podrían considerar como alternativas de uso					
--------------------------------------------------------------------------------------------------*/
function moverArchivo($arrDatosArchivoOrigen,$directorioSubir,$nomArchivoSinExtDestino,$extension,$maxLongNomArchivoSinExtDestino = 250,$permisosArchivo ='0444')
{
 //echo "<br /><br />0-1 moverArchivo:arrDatosArchivoOrigen: ";print_r($arrDatosArchivoOrigen); //Array con datos $_FILES
 //echo "<br /><br />0-2 moverArchivo:directorioSubir: ";print_r($directorioSubir);	
	//echo "<br /><br />0-3 moverArchivo:nomArchivoSinExtDestino: ";print_r($nomArchivoSinExtDestino);	//$nomArchivoSinExtDestino es el nombre del archivo sin extensión destino
	//echo "<br /><br />0-4 moverArchivo:extension: ";print_r($extension);
	//echo "<br /><br />0-5 moverArchivo:maxLongNomArchivoSinExtDestino: ";print_r($maxLongNomArchivoSinExtDestino);
	//echo "<br /><br />0-6 moverArchivo:permisosArchivo: ";print_r($permisosArchivo);	
 
	$arrMoverArchivo['codError'] = '00000';
	$arrMoverArchivo['errorMensaje'] = 	"";

 //--- Eliminar caracteres no permitidos o no deseados para nombre archivo y límite longitud -----
 $nomArchivoSinExtDestino = sanearNombreArchivo($nomArchivoSinExtDestino,$extension,$maxLongNomArchivoSinExtDestino);//en modeloArchivos.php
	
	$nomArchivoConExtDestino = $nomArchivoSinExtDestino.".".$extension;
	
 //echo "<br /><br />1-1 moverArchivo:nomArchivoConExtDestino: ";print_r($nomArchivoConExtDestino);	
		
	$directorioSubirPath = realpath($_SERVER['DOCUMENT_ROOT'].$directorioSubir);//dirección absoluta	
	
	//echo "<br /><br />1-2 moverArchivo:directorioSubirPath: ".$directorioSubirPath;

	//--- Inicio validar directorio y existencia otro archivo destino con mismo nombre ---------------
	if (!isset($directorioSubir ) || empty($directorioSubir))
	{
		 $arrMoverArchivo['codError'] = '82060';
  	$arrMoverArchivo['errorMensaje'] = "ERROR: variable de directorio destino vacía ";	
	}	
	elseif (!realpath($directorioSubirPath))	
	{	
	  $arrMoverArchivo['codError'] = '82060';
  	$arrMoverArchivo['errorMensaje'] = "ERROR: directorio absoluto de destino no existe ";			
	}	
	elseif (!is_dir(realpath($directorioSubirPath) ))
	{	//echo "<br /><br />2-1 moverArchivo:realpath(directorioSubirPath): ";print_r(realpath($directorioSubirPath));	
	
	  $arrMoverArchivo["codError"] ='82060';//"3"		
			$arrMoverArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no es un directorio -".$directorioSubir."-";		
	}
	elseif (!is_writable(realpath($directorioSubirPath)) )
	{	//echo "<br /><br />2-2 moverArchivo:realpath(directorioSubirPath): ";print_r(realpath($directorioSubirPath));	
	
	  $arrMoverArchivo["codError"] ='82060';//"3"		
			$arrMoverArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura -".$directorioSubir."-";		
	}		
	//elseif (strlen(realpath($directorioSubirPath)) + 255 > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
	elseif (strlen(realpath($directorioSubirPath)."/".$nomArchivoConExtDestino) > PHP_MAXPATHLEN )//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
	{ /*	PHP_MAXPATHLEN: Número entero.Longitud máxima (incluyendo el árbol de directorios bajo el cual se encuentra y nombre completo fichero). 
	     255= máxima longitud nombre archivo en linux	    
	    echo "<br /><br />----- PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
	  */			
	  //echo "<br /><br />2-3 moverArchivo:realpath(directorioSubirPath): ";print_r(realpath($directorioSubirPath));	
   $arrMoverArchivo['codError'] = '82060';
	  $arrMoverArchivo['errorMensaje'] = "ERROR: longitud del path del directorio supera el valor permitido por el sistema, debe reducir la longitud del nobre del archivo o guardar en un directorio de path más corto";			
	}	
 elseif (file_exists(realpath($directorioSubirPath)."/".$nomArchivoConExtDestino))
	{ 	  						
			$arrMoverArchivo["codError"] = '82040';// "1","2"						
			$arrMoverArchivo['errorMensaje'] = "ERROR: al subir el archivo ".$arrDatosArchivoOrigen["name"].	
		                       	". Con el nombre -".$nomArchivoConExtDestino."- ya existe un archivo en el directorio: ".$directorioSubir;		

			//echo "<br /><br />2-4 moverArchivo:resSubirValidarArchivo: ";print_r($resSubirValidarArchivo);						
	}
	//--- Fin validar directorio y existencia otro archivo destino con mismo nombre ------------------
	
	//------------ Inicio mover archivo y cambiar CHMOD ----------------------------------------------	

	//--- Inicio mover el archivo temporal al directorio deseado y con el nombre escogido ---------	
 elseif (!move_uploaded_file($arrDatosArchivoOrigen["tmp_name"],$directorioSubirPath."/".$nomArchivoConExtDestino)) 
	{								
			//echo "<br><br>2-5 moverArchivo:  ".$arrDatosArchivoSubir["error"]." uploading the file, ".$arrDatosArchivoSubir["name"]." please try again!";			
			$arrMoverArchivo["codError"] = '82040';// "1","2"
			$arrMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -".$arrDatosArchivoOrigen["name"].": ".$resMoverArchivoFunction["codError"]. 
			". Inténtalo de nuevo y en si no puedes avisa al adminstrador de la aplicación de Gestión de Soci@s <br /><br />";
	}	   
	else //movido al directorio $directorioSubirPath
	{	//echo "<br><br>3-1 moverArchivo: El archivo: ".$$arrDatosArchivoOrigen["name"]." Se ha guardado como: ".$nomAchivoConExtension;							
			
			//chmod($directorioSubirPath."/".$nomAchivoConExtension, octdec($permisosArchivo));// 0444: Solo Lectura para todos en octal el resultado	"mejor"	
			
			if (!chmod($directorioSubirPath."/".$nomArchivoConExtDestino, octdec($permisosArchivo)))
			//if (!chmod($directorioSubirPath."/".$nomArchivoConExtDestino, octdec($permisosArchivo)))
			{
					//echo "<br><br>3-2 moverArchivo:permisosArchivo:  ".$permisosArchivo;			
			  $arrMoverArchivo["codError"] = '82040';// "1","2"
			  $arrMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -".$arrDatosArchivoOrigen["name"].": ".$resMoverArchivoFunction["codError"]. 
			  "No se pudo asignar los permisos indicados para el archivo subido. Inténtalo de nuevo y en si no puedes avisa al adminstrador de la aplicación de Gestión de Soci@s <br /><br />";				
			}
			else
			{ $arrMoverArchivo['nombreArchExtGuardado'] = $nomArchivoConExtDestino;
		   $arrMoverArchivo['directorioSubir'] = $directorioSubir;//../upload/FIRMAS_ALTAS_SOCIOS_GESTOR				
			}
			//echo "<br><br>3-3 moverArchivo:permisos archivo: ".substr(decoct(fileperms($directorioSubirPath."/".$nomArchivoConExtDestino)), -4); //tipo 0444 solo lectura
	} 
	//--- Fin mover el archivo temporal al directorio deseado y con el nombre escogido ------------	
	//------------ Fin mover archivo y cambiar CHMOD -------------------------------------------------	
				
	//echo "<br><br>4 moverArchivo:arrMoverArchivo: ";print_r($arrMoverArchivo);

 return $arrMoverArchivo;			
}
/*-------------- Fin moverArchivo() ---------------------------------------------------------------*/

/*---------------- Inicio validarEnviarArchivosAdjuntos() -------------------------------------------
DESCRIPCION: Valida los archivos adjuntos para enviar por email

RECIBE: 
-$arrDatosArchivosAnexados: puede contener "n" archivos viene de formulario con viene de _$FILES 
 Array(	[FICHERO1]=>Array([name]=> [type]=> [tmp_name]=>  [error]=> [size]=> ) 
       	[FICHERO2]=>Array([name]=> [type]=> [tmp_name]=>  [error]=> [size]=> ) 
        	....................................................................)

-$arrMimeExtArchAdjuntosPermitidas: array con los tipos Mimes permitidos 
-$tamanioMax: El tamaño máximo del archivo en Bytes

DEVUELVE: array "$arrValidarAchivosAnexados" con información de errores, y con un subarray con los 
datos de los archivos analizados para enviar como adjuntos

LLAMADA: cPresidente: enviarEmailSociosPres() y cCoordinador: enviarEmailSociosCoord()
         mediante la función "validarCamposEmailAdjuntosSocios.php"
									Al ser una función de tipo general también podría ser llamada desde diferente funciones

OBSERVACIONES: Probado PHP 7.3.21
---------------------------------------------------------------------------------------------------*/
function validarEnviarArchivosAdjuntos($arrDatosArchivosAnexados,$arrMimeExtArchAdjuntosPermitidas,$tamanioMax)
{
	//echo "<br /><br />0-1 modeloArchivos:validarEnviarArchivosAdjuntos:datosArchivosAnexados: ";print_r($datosArchivosAnexados);
	//echo "<br /><br />0-2 modeloArchivos:validarEnviarArchivosAdjuntos:arrMimeExtArchAdjuntosPermitidas: ";print_r($arrMimeExtArchAdjuntosPermitidas);
	//echo "<br /><br />0-3 modeloArchivos:validarEnviarArchivosAdjuntos:tamanioMax: ";print_r($tamanioMax);
					
	$arrValidarAchivosAnexados["codError"] = '00000';
	$arrValidarAchivosAnexados['errorMensaje'] = '';

	$numFich = 0;		
	
 /*--------------- Inicio del bucle para validar uno o varios archivos adjuntos ---------------*/
	foreach ($arrDatosArchivosAnexados as $fichero => $datosArchivo)
	{//echo "<br /><br />2-1 modeloArchivos:validarEnviarArchivosAdjuntos:datosArchivo: ";print_r($datosArchivo);

	 if (isset($datosArchivo["name"]) && !empty($datosArchivo["name"])) 
		{//echo "<br /><br />2-2 modeloArchivos:validarEnviarArchivosAdjuntos:datosArchivo[name]: ";print_r($datosArchivo["name"]);

			if(!array_key_exists($datosArchivo["type"], $arrMimeExtArchAdjuntosPermitidas))	
			{ 
					//-- Obtener cadena de extensiones permitadas para mostrar en el mensaje de error en pantalla  
					$arrExtensionPerm =	array_values($arrMimeExtArchAdjuntosPermitidas); //= Array([0]=>doc [1]=>doc [2]=>docx ... [6]=>jpg [7]=>jpeg [8]=>pdf) 	
					$arrExtensionPerm = array_unique($arrExtensionPerm); //evita duplicados
					$cadExtPermitidas = implode(",", $arrExtensionPerm); //= doc,docx,odt,...,jpg,jpeg,pdf 			
					//--
					$datosArchivo['codError'] = '82010';	 
					$datosArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$datosArchivo["name"]."\" es un tipo de archivo NO PERMITIDO (Sólo se aceptan: ". $cadExtPermitidas. ")";				
					//echo "<br /><br />2-3 modeloArchivos:validarEnviarArchivosAdjuntos:datosArchivo: ";print_r($datosArchivo);			
			}
			elseif ($datosArchivo["size"] >= $tamanioMax)//EN BYTES = ($tamanioMax/1024/1024)." MB
			{$datosArchivo["codError"] = '82020';// "1","2"
				$datosArchivo['errorMensaje'] = "ERROR: Archivo ".$datosArchivo["name"]." demasiado grande: ".round(($datosArchivo["size"]/1024/1024),2).
				                                " MB (máx ".round(($tamanioMax/1024/1024),2)." MB)<br /><br />";
			}	
			elseif ($tamanioMax  > getsize(ini_get('upload_max_filesize')) ) // en modeloArchivos.php, supera la limitación del servidor para upload_files
			{ $tamanioMax = ini_get('upload_max_filesize');

					$datosArchivo["codError"] = '82020';// "1","2"
					$datosArchivo['errorMensaje'] = "ERROR: Archivo seleccionado previamente \"".$datosArchivo["name"]."\" es demasiado grande: ".round(($datosArchivo["size"]/1024/1024),2).
					"MB (El máximo permitido por el servidor son ".round(($tamanioMax/1024/1024),2)." MB)";			
					//echo "<br /><br />2-4 modeloArchivos:validarEnviarArchivosAdjuntos:datosArchivo: ";print_r($datosArchivo);							
			}	
			//OJO: ANTES DABA ERROR con lo del email siempre me da error aunque vaya bien acaso sea para carga de modo permanente
			/*elseif ($datosArchivo["error"] == 3)
			{ $datosArchivo["codError"]='82040';//"3"		
					$datosArchivo['errorMensaje']= 'ERROR: Envío de archivo suspendido durante la transferencia';
			}	
   */			
			elseif (($datosArchivo["size"] === 0))//EN BYTES
			{$datosArchivo["codError"] ='82030';// "1","2"
				$datosArchivo['errorMensaje'] = "ERROR: Archivo -".$datosArchivo["name"]."- tamaño 0 K <br /><br />";
			}			
			elseif ($datosArchivo["error"] > 0)
	  { $datosArchivo["codError"] = '82000';//"3"		
					$datosArchivo['errorMensaje'] = "ERROR al anexar archivo -".$datosArchivo["name"]."- <br /><br />";
		 }
		 else
			{  $datosArchivo["codError"] = '00000';//sobra
			   /*echo "<br />Upload: " . $datosArchivo["name"] . "<br />";echo "<br />Type: " . $datosArchivo["type"] . "<br />";
			   echo "<br />Size: " . ($datosArchivo["size"] / 1024) . " Kb <br />";echo "<br />Stored in: " . $datosArchivo["tmp_name"];
						*/
			}			
		
			$arrValidarAchivosAnexados[$numFich] = $datosArchivo;
			
		 if ($datosArchivo["codError"] !=='00000')
			{$arrValidarAchivosAnexados["codError"] = $datosArchivo["codError"];//o bien '82000'
			 $arrValidarAchivosAnexados['errorMensaje'] .= '<br />'.$datosArchivo['errorMensaje'].'<br />';//Va concatenado					
			}				
			
			$numFich++;				
		}
	}	
	/*--------------- Fin del bucle para validar uno o varios archivos adjuntos ------------------*/
	
	//echo "<br /><br />3 modeloArchivos:validarEnviarEmailSocios:arrValidarAchivosAnexados: ";print_r($arrValidarAchivosAnexados);

 return $arrValidarAchivosAnexados;		
}
/*---------------- Fin validarEnviarArchivosAdjuntos() --------------------------------------------*/

/*-------------- Inicio eliminarArchivo() ----------------------------------------------------------
Elimina un archivo de un directorio 

RECIBE: $directorioSubir: directorio relativo donde se guardará el archivo
        $nombreArchExt: nombre del archivo destino con la extensión
								
DEVUELVE: un array con los mensajes de error y el texto de comentarios										
								
LLAMADA:  modeloSocios.php:eliminarDatosSocios()	
modeloTesorero:mEliminarOrdenesCobroUnaRemesa(),mActualizarCuotasCobradasEnRemesaTes()	
modeloPresCoord:mAltaSocioPorGestor(),bajaSocioFallecido()	

OBSERVACIONES: Probada PHP 7.3.21						
--------------------------------------------------------------------------------------------------*/
function eliminarArchivo ($directorioArchivo,$nombreArchExt)
{ 				
 //echo "<br /><br />0-1 modeloArchivos.php:eliminarArchivo:directorioArchivo: ";print_r($directorioArchivo);	
	//echo "<br /><br />0-2 modeloArchivos.php:eliminarArchivo:nombreArchExt: ";print_r($nombreArchExt);	

 $resEliminarArchivo['codError'] = '00000';
	$resEliminarArchivo['errorMensaje'] = '';
	$resEliminarArchivo['arrMensaje']['textoComentarios'] =''; 
	
	$directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorioArchivo;			
	//echo "<br><br>1-0 modeloArchivos.php:eliminarArchivo:directorioSubirPath: error: ".$directorioSubirPath;
	
	if (!isset($nombreArchExt) || empty($nombreArchExt))
	{ //echo "<br><br>2-1 modeloArchivos.php:eliminarArchivo:nombreArchExt: error: ".$nombreArchExt;
		 $resEliminarArchivo['codError'] = '82060';
  	$resEliminarArchivo['errorMensaje'] = "ERROR: variable de nombre de archivo a eliminar no existe o está vacío ";
   $resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso:	No se ha podido eliminar el archivo porque el nombre de archivo a eliminar no existe o está vacío ";		
	}
	elseif (!isset($directorioArchivo ) || empty($directorioArchivo))
	{ //echo "<br><br>2-2 modeloArchivos.php:eliminarArchivo:directorioSubir: error: ".$directorioSubir;
		 $resEliminarArchivo['codError'] = '82060';
  	$resEliminarArchivo['errorMensaje'] = "ERROR: variable de directorio destino vacía ";
   $resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso:	No se ha podido eliminar el archivo <strong>".$nombreArchExt." </strong> porque el nombre del directorio destino no existe o está vacía";		
	}
	//realpath($directorioSubirPath) da problemas con archivo protegidos por encima de public_html
	elseif (!realpath($directorioSubirPath))	
	{ //echo "<br><br>2-3 modeloArchivos.php:eliminarArchivo:directorioSubirPath: error: ".$directorioSubirPath;
	
	  $resEliminarArchivo['codError'] = '82060';
  	$resEliminarArchivo['errorMensaje'] = "ERROR: directorio absoluto de destino no existe ";	
   $resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso: No se ha podido eliminar el archivo <strong>".$nombreArchExt."</strong> porque el directorio absoluto de destino no existe ";				
	}	
	elseif (!is_dir(realpath($directorioSubirPath) ))
	{	//echo "<br /><br />2-4 modeloArchivos.php:eliminarArchivo:realpath(directorioSubirPath): ";print_r(realpath($directorioSubirPath));	
	
	  $resEliminarArchivo["codError"] ='82060';//"3"		
			$resEliminarArchivo['errorMensaje'] = "ERROR el directorio para eliminar el archivo no es un directorio -".$directorioArchivo."-";		
			$resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso: No se ha podido eliminar el archivo <strong>".$nombreArchExt."</strong> porque el nombre del directorio para eliminar el archivo no se corresponde con un directorio -".$directorioArchivo."-";		
	}
	elseif (!is_writable(realpath($directorioSubirPath)) )
	{	//echo "<br /><br />2-5 modeloArchivos.php:eliminarArchivo:realpath(directorioSubirPath): ";print_r(realpath($directorioSubirPath));	
	
	  $resEliminarArchivo["codError"] ='82060';//"3"		
			$resEliminarArchivo['errorMensaje'] = "ERROR el directorio para eliminar el archivo no permite escritura -".$directorioArchivo."-";		
			$resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso:  No se ha podido eliminar el archivo <strong>".$nombreArchExt."</strong> porque el directorio para eliminar el archivo no permite escritura";
	}	
	elseif (!file_exists(realpath($directorioSubirPath)."/".$nombreArchExt))
	{	//echo "<br><br>2-6 modeloArchivos.php:eliminarArchivo:pathAndFilename:  no existe";print_r($nombreArchExt);
	  $resEliminarArchivo["codError"] ='82060';//"3"		
			$resEliminarArchivo['errorMensaje'] = "ERROR:  No se ha encontrado en el servidor el archivo con el nombre: - ".$nombreArchExt. " - en el directorio :".$directorioArchivo;
			$resEliminarArchivo['arrMensaje']['textoComentarios']  .= "Aviso:  No se ha podido eliminar el archivo <strong>".$nombreArchExt."</strong> porque no se ha encontrado en el servidor  en el directorio :".$directorioArchivo;
	}
	else 							
	{
			//fclose($apuntadorAFicheroAbierto);//no lo tenemos abierto y no tenemos apuntador de open
			$pathAndFilename = realpath($directorioSubirPath)."/".$nombreArchExt;		
			
			//$pathAndFilename = $directorioArchivo."/".$nombreArchExt;
			//echo "<br><br>4 modeloArchivos.php:eliminarArchivo:pathAndFilename: "; print_r($pathAndFilename);
			
			if (!unlink($pathAndFilename)) //es la instrucción para eliminar
			{
				//echo "<br><br>5 modeloArchivos.php:eliminarArchivo:pathAndFilename: error al borrar $pathAndFilename";
				
				$resEliminarArchivo['codError'] = '82000';
		  $resEliminarArchivo['errorMensaje'] = "Error: No se ha podido eliminar del servidor el archivo con el nombre: ".$nombreArchExt. " en el directorio :".$directorioArchivo.
				" Por favor, informe de este error al administrador del la aplicación de Gestión de Soci@s";				

				$resEliminarArchivo['arrMensaje']['textoComentarios']  .= " Aviso: No se ha podido eliminar del servidor el archivo con el nombre: <strong>".$nombreArchExt. "</strong> en el directorio :".$directorioArchivo.
				" Por favor, informe de este error al administrador del la aplicación de Gestión de Soci@s";
			}
			else
			{
				//echo "<br><br>6 modeloArchivos.php:eliminarArchivo:pathAndFilename: se ha eliminado $pathAndFilename";
				$resEliminarArchivo['arrMensaje']['textoComentarios']  .= "<br />Se ha eliminado del servidor el archivo con el nombre: <strong>".$nombreArchExt."</strong>";// en el directorio :".$directorioArchivo;
			}			
	}				
	//echo "<br><br>7 modeloArchivos.php:eliminarArchivo:resEliminarArchivo: ";print_r($resEliminarArchivo);
	
	return ($resEliminarArchivo);
}
//*-------------- Fin eliminarArchivo() ------------------------------------------------------------*/

/*-------------- Inicio sanearNombreArchivoo() ----------------------------------------------------
Sanea el nombre del archivo sin la extensión "$cadenaNomArchivoSinExt" y si el nombre del archivo 
mas la "$extension" excede la máxima longitud permitida recibida "$maxLongNombreArch" lo recorta 
por el final. La extension ya vendrá saneada. 

RECIBE: $cadenaNomArchivoSinExt = nombre sin extension, 
        $extension lo utiliza para calcular el la longitud total del nombre del archivo
        $maxLongNombreArch, para validar que longitud total del nombre del archivo no es superior 
								al valor recibido o a un valor de bytes (255 máx longitud nombre Linux). 			
								
DEVUELVE: solo el nombre del archivo sin la extsensión,	sanea acentos, Ñ por N, Ç por C	y demás caracteres prohibidos
          solo devuelve guión bajo, no el el guión medio
										
LLAMADA: modeloArchivos.php:moverArchivo(), disponible para otras funciones

OBSERVACIONES: Probada PHP 7.3.21	
--------------------------------------------------------------------------------------------------*/
function sanearNombreArchivo($cadenaNomArchivoSinExt,$extension,$maxLongNombreArch) //para antes de subir archivos al servidor
{ 
		$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ.-:"; //los tres ultimos los convierta a _ (guión bajo)	
		$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn___"; 
		
		//$cadenaNomArchivoSinExt = html_entity_decode( $cadenaNomArchivoSinExt, ENT_QUOTES, "utf-8" );//probar
		//$cadenaNomArchivoSinExt = htmlentities($cadenaNomArchivoSinExt, ENT_QUOTES, "utf-8");
		
		$cadenaNomArchivoSinExt = utf8_decode($cadenaNomArchivoSinExt); //utf8_decode
		$cadenaNomArchivoSinExt = strtr($cadenaNomArchivoSinExt, utf8_decode($tofind), $replac);  		
					
	 $cadenaNomArchivoSinExt = preg_replace("/[^A-Za-z0-9\_]/","",$cadenaNomArchivoSinExt);//bien elimina los que no sean Letras sin acentos, numeros o _ (guión bajo)		
				
		if (!isset($maxLongNombreArch) || $maxLongNombreArch > 255)//linux máxima longitud archivo con extension 255
		{$maxLongNombreArch = 255; 			
		}
		
		//$maxLongNomArchivoSinExt = $maxLongNombreArch - strlen($extension) + 1;//el . para el punto . maxima longitud nombre de archivo que aceptamos
		$maxLongNomArchivoSinExt = $maxLongNombreArch - mb_strlen($extension,"UTF-8") + 1;//el . para el punto . maxima longitud nombre de archivo que aceptamos
		
		//if (strlen($cadenaNomArchivoSinExt)  > $maxLongNomArchivoSinExt) 
		if (mb_strlen($cadenaNomArchivoSinExt,"UTF-8")  > $maxLongNomArchivoSinExt) 	
		{substr($cadenaNomArchivoSinExt, 0, $maxLongNomArchivoSinExt);	//se recorta al final
		}
		
		return utf8_encode($cadenaNomArchivoSinExt);      
}  
/*-------------- Fin sanearNombreArchivoo() ------------------------------------------------------*/


/* -------- Inicio  getsize($size) ----------------------------------------------------------------
Obtener el tamaño en bytes a partir de datos de tamaño tipo: 2G, 2M, 3K, 109 B

RECIBE: $size Tamaño de un archivo con posibles los formatos: 2G, 2M, 3K, 109 B 			
								
DEVUELVE: $size en bytes
										
LLAMADA: modeloArchivos.php:moverArchivo(), disponible para otras funciones

OBSERVACIONES: Probada PHP 7.3.21	

Ejemplo: saber el máximo tamaño de un archivo a un servidor
echo "upload_max_filesize: ".ini_get('upload_max_filesize');
$size =trim(ini_get('upload_max_filesize'));// upload_max_filesize esta en php.ini ver en phpinfo();
---------------------------------------------------------------------------------------------------*/
function getsize($size) 
{ 
  $size = str_replace(" ", "",$size); //quita TODOS espacios en blanco 
    
		if ($size === null) return null;
		//$last = strtolower($size{strlen($size)-1});
		$last = strtolower(substr($size, -1, 1) ); // mas clara con substr
		
		$size = (int) $size;
		
		switch($last) {
						case 'g':
						case 'gb':
										$size *= 1024;
						case 'm':
						case 'mb':
										$size *= 1024;
						case 'k':
						case 'kb':
										$size *= 1024;
		}
		//echo "<br /><br />modeloArchivos.php:getsize():size en bytes: ".	$size;					
		
		return $size;
}
/* -------- Fin  getsize($size) -------------------------------------------------------------------*/

/*--------------------------- Inicio arrMimeExtArchAltaSocioFirmaPermitidas ------------------------
Función que genera un array MIME para validar archivo de firmas de alta del socio por un gestor para 
         subirlo al servidor con más seguridad.

RECIBE: Nada
DEVUELVE: $arrMimeExtArchAltaSocioFirmaPermitidas 
										
LLAMADA: Funciones que necesitan validar archivo de firmas de alta del socio por un gestor para 
         subirlo al servidor con más seguridad.
									cTesorero.php:altaSocioPorGestorTes(),	cPresidente.php:altaSocioPorGestorPres(),	
									cCoordinador.php:altaSocioPorGestorCoord(),
									modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor()
LLAMA:Nada										

OBSERVACIONES: 2021-02-12

Para ver los MIME: src: http://www.freeformatter.com/mime-types-list.html#mime-types-list
--------------------------------------------------------------------------------------------------*/			
function arrMimeExtArchAltaSocioFirmaPermitidas()
{
		$arrMimeExtArchAltaSocioFirmaPermitidas	= array
					("application/msword"																																																							=>	"doc",
						"application/vnd.ms-office"																																																=>	"doc",
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document"	 =>	"docx",	
						"application/vnd.oasis.opendocument.text" 																																	=>	"odt",//Documento de texto OpenDocument				
						"application/vnd.oasis.opendocument.image"  																															=>	"odi" ,//imagen OpenDocument
						"image/gif" 																																																															=> "gif",
						"image/jpeg" 																																																														=>	"jpg",//por compatibilidad antiguos windows archivo con extensión 3 letras
						"image/pjpeg"                                                              => 'jpeg',
						"application/pdf" 																																																									=>	"pdf"		 	
					);	
					
		return $arrMimeExtArchAltaSocioFirmaPermitidas;			
}
/*--------------------------- Fin arrMimeExtArchAltaSocioFirmaPermitidas --------------------------*/

/*--------------------------- Inicio arrMimeExtArchAdjuntosPermitidas() ---------------------------
Función que genera un array MIME para validar archivos adjuntos enviados por email a socios
por un gestor.

RECIBE: Nada
DEVUELVE: $arrMimeExtArchAdjuntosPermitidas
										
LLAMADA: Funciones que necesitan validar el tipo de un archivo para enviarlo como adjunto a un 
email:	cPresidente.php:enviarEmailSociosPres(),	cCoordinador.php:enviarEmailSociosCoord(),
									
LLAMA:Nada										

OBSERVACIONES: 2021-02-22

Para ver los MIME: src: http://www.freeformatter.com/mime-types-list.html#mime-types-list
-------------------------------------------------------------------------------------------------*/			
function arrMimeExtArchAdjuntosPermitidas()
{
		$arrMimeExtArchAdjuntosPermitidas	= array
			('application/msword'																																																							=>	'doc',
				'application/vnd.ms-office'																																																=>	'doc',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document'	 =>	'docx',	
				'application/vnd.oasis.opendocument.text' 																																	=>	'odt',//Documento de texto OpenDocument				
				'application/vnd.oasis.opendocument.image'  																															=>	'odi',//imagen OpenDocument						
				'application/pdf' 																																																									=>	'pdf',
				'text/plain'                                                               => 'txt',
				
				'text/x-comma-separated-values'                                             => 'csv',
				'text/comma-separated-values'                                               => 'csv',
				'application/vnd.msexcel'                                                   => 'csv',		

				'application/excel'                                                         => 'xl',
				'application/msexcel'                                                       => 'xls',
				'application/x-msexcel'                                                     => 'xls',
				'application/x-ms-excel'                                                    => 'xls',
				'application/x-excel'                                                       => 'xls',
				'application/x-dos_ms_excel'                                                => 'xls',
				'application/xls'                                                           => 'xls',
				'application/x-xls'                                                         => 'xls',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
				'application/vnd.ms-excel'                                                  => 'xlsx',
				
				'application/xml'                                                           => 'xml',
				'text/xml'                                                                  => 'xml',
				'text/xsl'                                                                  => 'xsl',								

				'application/powerpoint'                                                    => 'ppt',
				'application/vnd.ms-powerpoint'                                             => 'ppt',
				'application/vnd.ms-office'                                                 => 'ppt',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',								
		
				'image/bmp'                                                                 => 'bmp',
				'image/x-bmp'                                                               => 'bmp',
				'image/x-bitmap'                                                            => 'bmp',
				'image/x-xbitmap'                                                           => 'bmp',
				'image/x-win-bitmap'                                                        => 'bmp',
				'image/x-windows-bmp'                                                       => 'bmp',
				'image/ms-bmp'                                                              => 'bmp',
				'image/x-ms-bmp'                                                            => 'bmp',
				'application/bmp'                                                           => 'bmp',
				'application/x-bmp'                                                         => 'bmp',
				'application/x-win-bitmap'                                                  => 'bmp',						
				
				'image/gif' 																																																												 			=> 'gif',
				'image/jpeg'																																																													 	 =>	'jpg',//por compatibilidad antiguos windows con extensión 3 letras
				'image/pjpeg'                                                               => 'jpeg',
				'image/tiff'                                                                => 'tiff',
				'image/png'                                                                 => 'png',
				'image/x-png'                                                               => 'png',
				
				'audio/mpeg'                                                                => 'mp3',
				'audio/mpg'                                                                 => 'mp3',
				'audio/mpeg3'                                                               => 'mp3',
				'audio/mp3'                                                                 => 'mp3',
				'video/mp4'                                                                 => 'mp4',
				'video/mpeg'                                                                => 'mpeg'		
			);	
					
		return $arrMimeExtArchAdjuntosPermitidas;			
}
/*--------------------------- Fin arrMimeExtArchAdjuntosPermitidas() ----------------------------*/

/*--------------------------- Inicio cadExtensionesArchivos -----------------------------------------
Función que genera una cadena de las extensiones, seperadas por comas(,) a partir un array con los
correspondientes tipos MIME de los archivos que posteriormente se podrán utilizada para validar 
archivo de firmas u otros, o para mostrar en pantalla o en  mensaje de error 

RECIBE: array $arrMimeExtArchivo, key es el MIME tipo, y valor es la extension
DEVUELVE: $cadExtensionesArchivos
										
LLAMADA: Funciones que necesitan validar archivo de firmas de alta del socio por un gestor para 
         subirlo al servidor con más seguridad.
LLAMA:Nada										

OBSERVACIONES: 2021-02-12: AÚN NO UTILIZADA, PERO SERÁ UTIL.
--------------------------------------------------------------------------------------------------*/			
function cadExtensionesArchivos($arrMimeExtArchivo)
{
	/*	ejemplo	
	  $arrMimeExtArchivo = array
							("application/msword"																																																						=>	"doc",
								"application/vnd.ms-office"																																															=>	"doc",
								"application/vnd.openxmlformats-officedocument.wordprocessingml.document"	=>	"docx",	
								"application/pdf" 																																																								=>	"pdf"		 	
							);	
	*/	

		$arrExtArchivo =	array_values($arrMimeExtArchivo ); //= Array([0]=>doc [1]=>doc [2]=>docx [3]=>df) 	
		$arrExtArchivo = array_unique($arrExtArchivo); //evita duplicados Array([0]=>doc [1]=>docx [2]=>df) 
		$cadExtensionesArchivos = implode(",", $arrExtArchivo); //= doc,docx,odt,pdf 																																					
					
		return $cadExtensionesArchivos;			
}
/*--------------------------- Fin cadExtensionesArchivos ------------------------------------------*/


/*====== Inicio NO USADAS ACTUALMENTE =============================================================*/
/*=================================================================================================*/

/*---------------- Inicio crearEscribirArchivoServidor.php -----------------------------------------
VERSION: PHP 7.3.21

DESCRIPCION: Se crea un archivo y se escribe en el string recibido en el parámetro "$cadenaTexto" y 
se guarda en un directorio del servidor recibido como parámetro. 
Controla divesos tipos de posibles errores													

RECIBE: 
- "$cadenaTexto" string con el contenido del archivo, puede incluir tags para formar una tabla
  (que podría servir para un archivo crear con formato excel)tabuladores 

- $directorioArchivo (sin nomArchivo viene de modeloTesorero.php:exportarCuotasXMLBancos()) que será 
  un path relativo al directorio raíz: /home/virtualmin/europalaica.com/public_html
		Los direcrorios que estén por encima de raíz	tiene restringido su acceso con href. 

- $nomArchivo: el nombre del archivo incluida la extensión 
  ejemplo: Excel_Triodos_2021-06-16_18-59-32.xls

DEVUELVE: un archivo que se guarda en un directorio recibido, y un array con los campos de errores					
													
LLAMADA: modelos ...:aún no se utiliza, probada para crear archivos excel
LLAMA: nada

OBSERVACIONES: ****Aún no se utiliza, probada para crear archivos excel. ****

Se podría definir: define('APLICATION_ROOT', getcwd());echo "<br />aplicacion-root: ".APLICATION_ROOT;
-----------------------------------------------------------------------------------------------------*/
function crearEscribirArchivoServidor_SinValidarNomArchivoExtensionBien($cadenaTexto,$directorioArchivo,$nomArchivo)	
{	
	echo "<br /><br />0-1 modeloArchivos.php:crearEscribirArchivoServidor:cadenaTexto: ";print_r($cadenaTexto);	
	echo "<br /><br />0-2 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioArchivo: ";print_r($directorioArchivo);	
	echo "<br /><br />0-3 modeloArchivos.php.php:crearEscribirArchivoServidor:nomArchivo: ";print_r($nomArchivo);
	//echo "<br /><br />0-4 modeloArchivos.php.php:crearEscribirArchivoServidor:directorio actual: ";echo getcwd();

	$arrCrearEscribirArchivoServidor['codError'] = '00000';
	$arrCrearEscribirArchivoServidor['errorMensaje'] = '';	
	
	if ( (!isset($cadenaTexto) || empty($cadenaTexto)) || (!isset($directorioArchivo) || empty($directorioArchivo)) || (!isset($nomArchivo) || empty($nomArchivo)) )
	{
			$arrCrearEscribirArchivoServidor['codError'] = '70620';
			$arrCrearEscribirArchivoServidor['errorMensaje'] = 'ERROR: faltan algunos de los siguientes datos: contenido escribir en el archivo, directorio o nombre archivo ';	
			echo '<br><br>1-1 modeloArchivos.php.php:crearEscribirArchivoServidor: ' ;print_r($arrCrearEscribirArchivoServidor);
	} 
	else //!if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	{ 			
			//---  Inicio Preparar path absoluto ----------------------------------
					
			//Aclaraciones sobre directorios: directorios públicos (por debajo de europalaica.com/public_html) y privados o restringidos (por encima de europalaica.com/public_html) 
			
			$directorioRoot = $_SERVER['DOCUMENT_ROOT'];//será:   "/home/virtualmin/europalaica.com/public_html"
			echo "<br /><br />2-1 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioRoot: ";print_r($directorioRoot);// devuelve /home/virtualmin/europalaica.com/public_html		
   
			//$directorioArchivo = "/../upload/_FILES/TESORERIA/SEPAXML_ISO20022";//ok el directorio "upload" NO es público, estó un nivel más arriba del root acceso restringido solo con PHP		
					
			$directorioAbsoluto = realpath($directorioRoot.$directorioArchivo);//será: /home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
			echo "<br><br>2-2 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioAbsoluto: ".$directorioAbsoluto;			//home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
		 $directorioAbsolutoMasArchivo = $directorioAbsoluto."/".$nomArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-21H10-38-02.xml					
		
			echo "<br><br>2-3 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioAbsolutoMasArchivo: ".$directorioAbsolutoMasArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-28H10-47-31.xml
   
			//---  Fin Preparar path absoluto -------------------------------------
			
			
			/*------------- Inicio validar datos archivo: directorio -------------------------------*/

			//$directorioAbsoluto ='hhhh'; para probar errores
			if (!is_dir($directorioAbsoluto) )//ok: comprueba si es un directorio y también si existe
			{			
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR el directorio para subir el archivo no existe o no es un directorio: -".$directorioAbsoluto."-";		
			}
			elseif (!is_writable($directorioAbsoluto) )//ok 
			{			
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura: -".$directorioAbsoluto."-";		
			}		
			elseif (strlen($directorioAbsolutoMasArchivo) > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
			{ 
			  /*	PHP_MAXPATHLEN: Número entero.Longitud máxima de un nombre completo de fichero (incluyendo el árbol de directorios bajo el cual se encuentra). 
							 echo "<br /><br />PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
					*/					
					$arrCrearEscribirArchivoServidor['codError'] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR: longitud del path del directorio -".$directorioAbsolutoMasArchivo."- supera el valor permitido por el sistema, 
					                                            debe reducir la longitud del nombre del archivo o guardar en un directorio de path más corto";			
			}	
			elseif (file_exists($directorioAbsolutoMasArchivo))//ok si ya existe ese archivo
			{ 	  						
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR: al subir el archivo ya existe un archivo con ese nombre: ".$directorioAbsolutoMasArchivo;	
			}
			
			echo "<br /><br />3-6 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ";print_r($arrCrearEscribirArchivoServidor);						
			//*------------- Fin validar datos archivo :directorio ----------------------------------*/
				

			if ($arrCrearEscribirArchivoServidor['codError'] == '00000')
   {	
				//*------------- Inicio  crear, escribir, cerrar archivo  ----------------------------------*/

 			//if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'x'))//Creación y apertura para sólo escritura. Si ya existe devuelve false y también un error tipo E_WARNING. Si no existe se intenta crear (Puntero al principio archivo)
				if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w'))//Apertura para sólo escritura y si ya existe borra su contenido. Si no existe se intenta crear. (Puntero al principio archivo)				
				{ 
						$arrCrearEscribirArchivoServidor['codError'] = '50010';
						$arrCrearEscribirArchivoServidor['errorMensaje'] = " Error al abrir archivo ";
						$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al Error al abrir archivo ";
				}
				else//if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )
				{	
			  if (fwrite($recursoArchivo, $cadenaTexto) === false)					
					{ 
				   $arrCrearEscribirArchivoServidor['codError'] = '50020';
							$arrCrearEscribirArchivoServidor['errorMensaje'] = " Error al escribir en un archivo ";
							$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al escribir en un archivo ";
					}						
		
					if (!fclose($recursoArchivo))
					{
				 		$arrCrearEscribirArchivoServidor['codError'] = '50030';
							$arrCrearEscribirArchivoServidor['errorMensaje'] .= " Error al cerrar archivo ";
							$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al cerrar archivo ";
					}
			 }//else if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )	
					
				echo '<br><br>3-7 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ';print_r($arrCrearEscribirArchivoServidor);
		 } 
		 	//*------------- Fin  crear, escribir, cerrar archivo  -------------------------------------*/
			
 }//else !if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	
	echo '<br><br>4 3-5 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ';print_r($arrCrearEscribirArchivoServidor);
	
 return $arrCrearEscribirArchivoServidor;
}
/*---------------- FIN crearEscribirArchivoServidor.php ------------------------------------------*/

function crearEscribirArchivoServidor($cadenaTexto,$directorioArchivo,$nomArchivo,$arrExtPermitidas = NULL)	
{	
	//echo "<br /><br />0-1 modeloArchivos.php:crearEscribirArchivoServidor:cadenaTexto: ";print_r($cadenaTexto);	
	//echo "<br /><br />0-2 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioArchivo: ";print_r($directorioArchivo);	
	//echo "<br /><br />0-3 modeloArchivos.php.php:crearEscribirArchivoServidor:nomArchivo: ";print_r($nomArchivo);
	//echo "<br /><br />0-4 modeloArchivos.php.php:crearEscribirArchivoServidor:directorio actual: ";echo getcwd();

	$arrCrearEscribirArchivoServidor['codError'] = '00000';
	$arrCrearEscribirArchivoServidor['errorMensaje'] = '';	
	
	if ( (!isset($cadenaTexto) || empty($cadenaTexto)) || (!isset($directorioArchivo) || empty($directorioArchivo)) || (!isset($nomArchivo) || empty($nomArchivo)) )
	{
			$arrCrearEscribirArchivoServidor['codError'] = '70620';
			$arrCrearEscribirArchivoServidor['errorMensaje'] = 'ERROR: faltan algunos de los siguientes datos: contenido escribir en el archivo, directorio o nombre archivo ';	
			//echo '<br><br>1-1 modeloArchivos.php.php:crearEscribirArchivoServidor: ' ;print_r($arrCrearEscribirArchivoServidor);
	} 
	else //!if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	{ 	

			//---  Inicio Preparar path absoluto ----------------------------------
					
			//Aclaraciones sobre directorios: directorios públicos (por debajo de europalaica.com/public_html) y privados o restringidos (por encima de europalaica.com/public_html) 
			
			$directorioRoot = $_SERVER['DOCUMENT_ROOT'];//será:   "/home/virtualmin/europalaica.com/public_html"
			//echo "<br /><br />2-1 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioRoot: ";print_r($directorioRoot);// devuelve /home/virtualmin/europalaica.com/public_html		
   
			//$directorioArchivo = "/../upload/_FILES/TESORERIA/SEPAXML_ISO20022";//ok el directorio "upload" NO es público, estó un nivel más arriba del root acceso restringido solo con PHP		
					
			$directorioAbsoluto = realpath($directorioRoot.$directorioArchivo);//será: /home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
			//echo "<br><br>2-2 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioAbsoluto: ".$directorioAbsoluto;			//home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
		 $directorioAbsolutoMasArchivo = $directorioAbsoluto."/".$nomArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-21H10-38-02.xml					
		
			//echo "<br><br>2-3 modeloArchivos.php.php:crearEscribirArchivoServidor:directorioAbsolutoMasArchivo: ".$directorioAbsolutoMasArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-28H10-47-31.xml
   
			//---  Fin Preparar path absoluto -------------------------------------
			
			
			/*------------- Inicio validar datos archivo: directorio -------------------------------*/

			//function validarNomArchivoExtension($archivoNombre_y_Extension,$arrExtPermitidas = NULL)
			$nomArchivo ='hola.1234567';
			$validarNomArchivoExt = validarNomArchivoExtension($nomArchivo,$arrExtPermitidas);
			//echo "<br /><br />2-0 modeloArchivos.php.php:crearEscribirArchivoServidor:validarNomArchivoExt: ";print_r($validarNomArchivoExt);
		
			if ($validarNomArchivoExt["codError"] !=='00000' )//
			{			
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					//$arrCrearEscribirArchivoServidor["codError"] = $validarNomArchivoExt["codError"];//[codError] => 82010 		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = $validarNomArchivoExt['errorMensaje'];		
			}

			//$directorioAbsoluto ='hhhh'; para probar errores
			elseif (!is_dir($directorioAbsoluto) )//ok: comprueba si es un directorio y también si existe
			{			
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR el directorio para subir el archivo no existe o no es un directorio: -".$directorioAbsoluto."-";		
			}
			elseif (!is_writable($directorioAbsoluto) )//ok 
			{			
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura: -".$directorioAbsoluto."-";		
			}		
			elseif (strlen($directorioAbsolutoMasArchivo) > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
			{ 
			  /*	PHP_MAXPATHLEN: Número entero.Longitud máxima de un nombre completo de fichero (incluyendo el árbol de directorios bajo el cual se encuentra). 
							 echo "<br /><br />PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
					*/					
					$arrCrearEscribirArchivoServidor['codError'] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR: longitud del path del directorio -".$directorioAbsolutoMasArchivo."- supera el valor permitido por el sistema, 
					                                            debe reducir la longitud del nombre del archivo o guardar en un directorio de path más corto";			
			}	
			elseif (file_exists($directorioAbsolutoMasArchivo))//ok si ya existe ese archivo
			{ 	  						
					$arrCrearEscribirArchivoServidor["codError"] = '50000';//es un errror del sistema, antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoServidor['errorMensaje'] = "ERROR: al subir el archivo ya existe un archivo con ese nombre: ".$directorioAbsolutoMasArchivo;	
			}
			
			//echo "<br /><br />3-6 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ";print_r($arrCrearEscribirArchivoServidor);						
			//*------------- Fin validar datos archivo :directorio ----------------------------------*/
				

			if ($arrCrearEscribirArchivoServidor['codError'] == '00000')
   {	
				//*------------- Inicio  crear, escribir, cerrar archivo  ----------------------------------*/

 			//if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'x'))//Creación y apertura para sólo escritura. Si ya existe devuelve false y también un error tipo E_WARNING. Si no existe se intenta crear (Puntero al principio archivo)
				if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w'))//Apertura para sólo escritura y si ya existe borra su contenido. Si no existe se intenta crear. (Puntero al principio archivo)				
				{ 
						$arrCrearEscribirArchivoServidor['codError'] = '50010';
						$arrCrearEscribirArchivoServidor['errorMensaje'] = " Error al abrir archivo ";
						$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al Error al abrir archivo ";
				}
				else//if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )
				{	
			  if (fwrite($recursoArchivo, $cadenaTexto) === false)					
					{ 
				   $arrCrearEscribirArchivoServidor['codError'] = '50020';
							$arrCrearEscribirArchivoServidor['errorMensaje'] = " Error al escribir en un archivo ";
							$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al escribir en un archivo ";
					}						
		
					if (!fclose($recursoArchivo))
					{
				 		$arrCrearEscribirArchivoServidor['codError'] = '50030';
							$arrCrearEscribirArchivoServidor['errorMensaje'] .= " Error al cerrar archivo ";
							$arrCrearEscribirArchivoServidor['textoComentarios'] = " Error al cerrar archivo ";
					}
			 }//else if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )	
					
				//echo '<br><br>3-7 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ';print_r($arrCrearEscribirArchivoServidor);
		 } 
		 	//*------------- Fin  crear, escribir, cerrar archivo  -------------------------------------*/
			
 }//else !if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	
	//echo '<br><br>4 3-5 modeloArchivos.php.php:crearEscribirArchivoServidor:arrCrearEscribirArchivoServidor: ';print_r($arrCrearEscribirArchivoServidor);
	
 return $arrCrearEscribirArchivoServidor;
}
/*---------------- FIN crearEscribirArchivoServidor.php ------------------------------------------*/

/*--------------------------- Inicio validarExtensionesPermitidas ---------------------------------
Validación de Extensiones permitidas de un archivo y a la vez valida si el nombre del archivo no 
contiene caracteres no aceptados.

RECIBE: $arrExtensiones_y_Mimes_Permitidos: array con la lista de extensiones y mimes 
        permitidos, si no exitiese o fuese null, tomaría por defecto la que está en 
								la función "validarExtensionesPermitidas()"
        $archivoNombre_y_Extension: será nombre y extensión la extensión de un archivo que deberá 
								´
DEVUELVE: $arrValidarNomArchivo_y_Extension, array el correspon.diente ['codError'],['errorMensaje']
          y si es válido el valores ['codError']='00000', u otros distintos si no es válido
										
LLAMADA: modeloArchivos.php:crearEscribirArchivoServidor() y Funciones que necesitan validar nombres archivos
LLAMA:modeloArchivos.php:validarExtensionesPermitidas(),sanearNombreArchivo()									

OBSERVACIONES: 2021-02-12: *** crearEscribirArchivoServidor().****
--------------------------------------------------------------------------------------------------*/		
function validarNomArchivoExtension($archivoNombre_y_Extension,$arrExtPermitidas = NULL)
{	
  //echo '<br><br>0-1 modeloArchivos.php:validarNomArchivoExtension:archivoNombre_y_Extension: ' ;print_r($archivoNombre_y_Extension);
		//echo '<br><br>0-2 modeloArchivos.php:validarNomArchivoExtension:arrExtPermitidas : ' ;print_r($arrExtPermitidas);				
		  	
		//require_once './modelos/modeloArchivos.php';
		
		$arrValidarNomArchivo_y_Extension['codError'] = '00000';
		$arrValidarNomArchivo_y_Extension['errorMensaje'] = '';

	/* Otra opción: Para probar si en lugar de array de extensiones recibiese un string con "$arrDatosArchivoSubir['cadExtPermitidas']"
		$arrArchivoNomExt = explode(".",$arrDatosArchivoSubir["name"]);//devuelve un array de string a partir del nombre del archivo obtenidos por la separación por .			
		echo '<br><br>1-1 modeloArchivos.php:validarNomArchivoExtension:arrArchivoNomExt: ' ;print_r($arrArchivoNomExt);		
		$iExt = count($arrArchivoNomExt) - 1; //por si habiese varios puntos incluidos en el nombre (más habitual en Linux), se toma el último que corresponde solo a extensión		
		
		if ($iExt < 1) //error probado
		{ $arrValidarNomArchivo_y_Extension['codError'] = '82010';	 
				$arrValidarNomArchivo_y_Extension['errorMensaje'] .= "ERROR: El archivo no tiene extensión, debe poner un tipo de extensión permitido (Sólo se aceptan: ". $extPermitidas. ")";						
				echo "<br /><br />1-2 modeloArchivos.php:validarNomArchivoExtension:arrValidarNomArchivo_y_Extension: ";print_r($arrValidarNomArchivo_y_Extension);	
		}
		else //($iExt >= 1)
		{	$extensionArchivo = $arrArchivoNomExt[$iExt]; //extensión que debiera venir en minúsculas	para que lo acptase como válid		
		  $pos = strpos($arrDatosArchivoSubir['cadExtPermitidas'], $extensionArchivo);
		  if ($pos === false) //Funciona
				{echo "<br><br>La cadena '$extensionArchivo' NO fue encontrada en la cadena: ";print_r($arrDatosArchivoSubir['cadExtPermitidas']);
    }				
				if ($pos !== false) //funciona
				{echo "<br><br>La cadena '$extensionArchivo' SI fue encontrada en la cadena: ";print_r($arrDatosArchivoSubir['cadExtPermitidas']);
    }
		}
	*/		
	
  //$archivoNombre_y_Extension = $archivoNombre_y_Extension.'hola.xml';//para probar con un nombre se archivo sin extensión
  //$archivoNombre_y_Extension = 'SEPA_ISO_2020020_2020-12-21xmlhola.pero.xml';
		
		$arrArchivoNomExt = explode(".",$archivoNombre_y_Extension);//devuelve un array de string a partir del nombre del archivo obtenidos por la separación por .	
		
		//echo '<br><br>1-1 modeloArchivos.php:validarNomArchivoExtension:arrArchivoNomExt: ' ;print_r($arrArchivoNomExt);
		
		$iExt = count($arrArchivoNomExt) - 1; //por si habiese varios puntos incluidos en el nombre (más habitual en Linux), se toma el último que corresponde solo a extensión		
		
		if ($iExt < 1) //error probado
		{ $arrValidarNomArchivo_y_Extension['codError'] = '82010';	 
				$arrValidarNomArchivo_y_Extension['errorMensaje'] .= "ERROR: El archivo no tiene extensión, debe poner un tipo de extensión permitido (Sólo se aceptan: ". $extPermitidas. ")";						
				//echo "<br /><br />1-2 modeloArchivos.php:validarNomArchivoExtension:arrValidarNomArchivo_y_Extension: ";print_r($arrValidarNomArchivo_y_Extension);	
		}
		else //($iExt >= 1)
		{						
				//$extensionArchivo = strtolower($arrArchivoNomExt[$iExt]); //extensión en minúsculas	
				$extensionArchivo = $arrArchivoNomExt[$iExt]; //extensión que debiera venir en minúsculas	para que lo acptase como válida
				
				//echo '<br><br>1-3 modeloArchivos.php:validarNomArchivoExtension:extensionArchivo: ';print_r($extensionArchivo);						

				$nomArchivoSinExtSinDir = str_replace (".".$extensionArchivo , "", $archivoNombre_y_Extension );//se quita la extensión del nombre del archivo
				//echo '<br><br>1-4 modeloArchivos.php:validarNomArchivoExtension:nomArchivoSinExtSinDir: ';print_r($nomArchivoSinExtSinDir);		
				
			 //----------------Incio Validar extensión permitida ----------------------------------			
		
				/*--- Inicio validarExtensionesPermitida ----------------------------------------------
					DONDE $arrExtPermitidas es una array: extension, myme_type (con las extensiones permitidas)
				 ejemplo = array(	"doc"=>"application/msword","gif"=>"image/gif","jpeg"	=>"image/jpeg"....) 
				--------------------------------------------------------------------------------------*/
    //$arrExtPermitidas = array(	);
				//$arrExtPermitidas = array(	"doc"=>"application/msword","gif"=>"image/gif","jpeg"	=>"image/jpeg");//para probar 	
				
			 $validadExtension = validarExtensionesPermitidas($extensionArchivo,$arrExtPermitidas);//en modeloArchivos.php
				
				if ($validadExtension !== '00000')
				{ 
						$arrValidarNomArchivo_y_Extension["codError"] = $validadExtension["codError"] ;//sería un error lógico que podría cooregir el usuario					
						$arrValidarNomArchivo_y_Extension['errorMensaje'] = $validadExtension['errorMensaje'];
				}	
	 }//else //($iExt >= 1)

		//echo "<br /><br />1-5 modeloArchivos.php:validarNomArchivoExtension:arrValidarNomArchivo_y_Extension: ";print_r($arrValidarNomArchivo_y_Extension);	
		
		//---------------- Fin validarExtensionesPermitida --------------------------------------		

		//-------- Incio validar nombre de archivo ----------------------------------------------		
			
		/*--- Inicio validar que nombre archivo (sin extension) no incluya caracteres considerados
		no válidos	en esta función (y Ñ por N Ç por C	y otros caracteres especiales) 	y 
		de longitud inferior "$maxLongNombre" --------------------------------------------------*/ 

		//$nomArchivoSinExtSinDir = $nomArchivoSinExtSinDir."-ñ.á";//para probar	
		$maxLongNombre = 100;																																																
	
		$nomArchivoSinExtSinDirSaneado = sanearNombreArchivo($nomArchivoSinExtSinDir,$extensionArchivo,$maxLongNombre);//en modeloArchivos.php	
		
		//echo '<br><br>2-1 modeloArchivos.php:validarNomArchivoExtension:sanearNombreArchivo:nomArchivoSinExtSinDir: ' ;print_r($nomArchivoSinExtSinDirSaneado);		

		if ($nomArchivoSinExtSinDirSaneado !== $nomArchivoSinExtSinDir)
		{ 
				$arrValidarNomArchivo_y_Extension["codError"] = '80420';//sería un error lógico que podría cooregir el usuario					
				$arrValidarNomArchivo_y_Extension['errorMensaje'] .= "ERROR el nombre del archivo ".$nomArchivoSinExtSinDir.".".$extensionArchivo." contiene caracteres que no están permitidos para archivos  
																																													        			- puedes poner este nombre que es válido ".$nomArchivoSinExtSinDirSaneado.".".$extensionArchivo;		
		}	 		
		//-------- Fin validar nombre de archivo -----------------------------------------------			
	
		//echo '<br><br>2 modeloArchivos.php:validarNomArchivoExtension:arrValidarNomArchivo_y_Extension: ' ;print_r($arrValidarNomArchivo_y_Extension);		
		
		return $arrValidarNomArchivo_y_Extension;	
}	

/*--------------------------- Inicio validarExtensionesPermitidas ---------------------------------
Función generaral de validación de Extensiones de los archivos.

RECIBE: $arrExtensiones_y_Mimes_Permitidos: array con la lista de extensiones y mimes 
        permitidos, si no exitiese o fuese null, tomaría por defecto la que está en 
								est función.
        $extensionArchivo: será la extensión que del archivo que deberá venir en minúsculas 
								para comprobar si está en $arrExtensiones_y_Mimes_Permitidos.
								´
DEVUELVE: $arrValidarExtension_y_Mime, array el correspondiente ['codError'],['errorMensaje']
          y ['cadExtensionesPermitidas'] string con las extensiones permitadas separadas por comas
										
LLAMADA: Funciones que necesitan validar un tipo de archivo para subirlo al servidor 
         con más seguridad en modeloArchivos.php
LLAMA:Nada									
									

OBSERVACIONES: 2021-02-12: AÚN NO UTILIZADA, PERO SERÁ UTIL.

Nota: se puede hace una similar para validar a partir de mime, en lugar de extensión
o juntar las dos validaciones en esta función-
--------------------------------------------------------------------------------------------------*/			
function validarExtensionesPermitidas($extensionArchivo, $arrExtensiones_y_Mimes_Permitidos = NULL) 
{
		//echo "<br /><br />0-1 modeloArchivos.php:validarExtensionesPermitidas:extensionArchivo: ";print_r($extensionArchivo);
		//echo "<br /><br />0-2 modeloArchivos.php:validarExtensionesPermitidas:arrExtensiones_y_Mimes_Permitidos: ";print_r($arrExtensiones_y_Mimes_Permitidos);
		
		$arrValidarExtension_y_Mime['codError'] = '00000';	 
		$arrValidarExtension_y_Mime['errorMensaje'] = '';
		
  //$extensionArchivo = 'php';
  //$extensionArchivo = '123';	//	para probar	
	 //$arrExtensiones_y_Mimes_Permitidos = array( "txt"		 	=>	"text/plain");//para probar	
					
		//La class phpMailer tiene una lista en el array mucho más grande, yo he restringido aquí a los 
		//más comunes y menos riesgos.	
		if (!isset($arrExtensiones_y_Mimes_Permitidos) || empty($arrExtensiones_y_Mimes_Permitidos) )//si no se recibe, se usarán los siguientes por defecto
		{	
				$arrExtensiones_y_Mimes_Permitidos = array(
															"bmp"		 	=>	"image/bmp",
															"doc"	 		=>	"application/msword",
															"docx"			=>	"application/vnd.openxmlformats-officedocument.wordprocessingml.document",	
															"gif"		 	=>	"image/gif",
															"jpeg"			=>	"image/jpeg",
															"jpg"		 	=>	"image/jpeg",
															"jpgv"			=>	"video/jpeg",
															"mp1"		 	=>	"audio/mpeg",
															"mp2"		 	=>	"audio/mpeg",
															"mp3"	 		=>	"audio/mpeg",
															"mp4"	 		=>	"video/mp4",
															"mp4a"			=>	"audio/mp4",
															"mpeg"			=>	"video/mpeg",
															"mpga"			=>	"audio/mpeg",
															"pdf"		 	=>	"application/pdf",
															"png"		 	=>	"image/png",
															"ppt"		 	=>	"application/vnd.ms-powerpoint",
															"pptx"			=>	"application/vnd.openxmlformats-officedocument.presentationml.presentation",
															/* ----------------------------------------------------------------------------------------------- 
																Documentos de OpenOffice, también conocida como StarOffice 
																creo que no interesa permitirlos porque habrá socios que no puedan abrirlo y se pueden despistar*/
																		
															"odp"	 		=>	"application/vnd.oasis.opendocument.presentation",//Documento presentación OpenDocument
															"ods"		 	=>	"application/vnd.oasis.opendocument.spreadsheet", //Hoja de Cálculo OpenDocument
															"odt"		 	=>	"application/vnd.oasis.opendocument.text",        //Documento de texto OpenDocument		
															/*------------------------------------------------------------------------------------------------*/
															"tiff"			=>	"image/tiff",
															"txt"		 	=>	"text/plain",
															"wm"			  =>	"video/x-ms-wm",
															"wma"		 	=>	"audio/x-ms-wma",
															"xls"		 	=>	"application/vnd.ms-excel",
															"xlsx"			=>	"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",	
															"xml"		 	=>	"application/xml",//los dos se utilizan 
															"xml"		 	=>	"text/xml",//algunos navegadores necesitan este pero se quedará obsoleto
															"xslt"			=>	"application/xslt+xml",
															"zip"		 	=>	"application/zip" );	
		}

		$cadExtensionesPermitidas = '';
		
		foreach ($arrExtensiones_y_Mimes_Permitidos as $extension => $mimeType)//for un string con los extensiones permitidas separadas por comas
		{ $cadExtensionesPermitidas .= $extension.",";	
		}
		$cadExtensionesPermitidas = rtrim($cadExtensionesPermitidas, ",");//quito coma última
		
		//echo "<br /><br />1 modeloArchivos.php:validarExtensionesPermitidas:cadExtensionesPermitidas: ";print_r($cadExtensionesPermitidas);
			
		$arrValidarExtension_y_Mime['cadExtensionesPermitidas'] = $cadExtensionesPermitidas;//podrá servir para mostrar las extensiones permitidas s
		$arrValidarExtension_y_Mime['extension'] = $extensionArchivo;//acaso sea útil devolver el valor de la extensión
		
		if (!isset($extensionArchivo) || empty($extensionArchivo) )
		{
				$arrValidarExtension_y_Mime['codError'] = '82010';	 
				$arrValidarExtension_y_Mime['errorMensaje'] .= "ERROR: El archivo no tiene extensión, debe poner un tipo de extensión permitido (Sólo se aceptan: ".$cadExtensionesPermitidas.")";						
				//echo "<br /><br />2-1 modeloArchivos.php:validarExtensionesPermitidas:arrValidarExtension_y_Mime: ";print_r($arrValidarExtension_y_Mime);	
		}				
		//elseif (!isset($arrExtensiones_y_Mimes_Permitidos[strtolower($extensionArchivo)]))//si no existe el índice en el array
		elseif (!isset($arrExtensiones_y_Mimes_Permitidos[$extensionArchivo]))//si no existe el índice en el array
		{ 
				$arrValidarExtension_y_Mime['codError'] = '82010';	 
				$arrValidarExtension_y_Mime['errorMensaje'] .= "ERROR: La extensión del archivo con extensión <strong>".$extensionArchivo." </strong>es un tipo no permitido (Sólo se aceptan: ".$cadExtensionesPermitidas.")";						
				//echo "<br /><br />2-2 modeloArchivos.php:validarExtensionesPermitidas:arrValidarExtension_y_Mime: ";print_r($arrValidarExtension_y_Mime);	
		}
		else
		{ $arrValidarExtension_y_Mime['mime'] = $arrExtensiones_y_Mimes_Permitidos[$extensionArchivo];
				
				//echo "<br /><br />2-3 modeloArchivos.php:validarExtensionesPermitidas:arrValidarExtension_y_Mime: ";print_r($arrValidarExtension_y_Mime);	
		}				

		//echo "<br /><br />3 modeloArchivos.php:validarExtensionesPermitidas:arrValidarExtension_y_Mime: ";print_r($arrValidarExtension_y_Mime);
					
		return $arrValidarExtension_y_Mime;	
}		
/*--------------------------- Fin validarExtensionesPermitidas -----------------------------------*/


/*-------------- Inicio subirValidarMoverArchivoFunction() -----------------------------------------
Agustin:2018_08_10 

DESCRIPCION: Sube un archivo al servidor y lo mueve a un directorio concreto, con un nombre saneado
             Valida archivo que se va a subir al servidor en cuanto a tamaño y tipos válidos y que 
             exista el directorio dóde se va a subir, que	no exista otro archivo con el mismo nombre
													y le establece unos permisos sobre el archivo determinados por parámetro $permisosArchivo
													que al sanear el nombre del archivo 
													La función sanearNombreArchivo() solo devuelve guiones bajos (no también guiones medios)
													
Llamado: desde  cPresidente.php:altaSocioPorGestorPres(), cCoordinador:altaSocioPorGestorCoord(), cTesorero.php:altaSocioPorGestorCoord()

Llama: modeloArchivos:sanearNombreArchivo()

Recibe: $arrDatosArchivoSubir: array con datos: [name],[type],[size],[tmp_name],[error]  viene de _$FILES 
        y podría venir con otros valores añadidos por form o programa.
 $directorioSubir: directorio relativo donde se guardará el archivo. Ejem. /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR, /documentos/ASOCIACION
 $nomArchivoSinExtDestino: nombre del archivo destino (sin la extensión), 
	$maxLongNombre: longitud máxima del nombre del archivo
	$arrExtPermitidas: un array con las extensiones y tipos mime permitidos,
	$tamanioMax = tamaño maximo del archivo
	$permisosArchivo = permisos que se asignarán al archivo, por defecto solo lectura
	
	Devuelve: un array con información de errores, ['directorioSubir'],y ['nombreArchExtGuardado'] y otros valores del archivo
	
	NOTA: NO USADA ACTUALMENTE: En su lugar se podría usar la función: subirValidarMoverArchivoClass() 
	      que utiliza la clase uploadVerot:class.upload.php
							o bien la suma de las funciones: validarSubirArchivoFunction()+moverArchivoFunction()
--------------------------------------------------------------------------------------------------*/
//function subirValidarMoverArchivoFunction($arrDatosArchivoSubir,$directorioSubir,$nombreArchivoGuardado,$maxLongNombre,$arrExtPermitidas,$tamanioMax,$permisosArchivo ='0444')
function subirValidarMoverArchivoFunction_guardar($arrDatosArchivoSubir,$directorioSubir,$nomArchivoSinExtDestino,$maxLongNombre,$arrExtPermitidas,$tamanioMax,$permisosArchivo ='0444')
{
 //echo "<br /><br />0-a-subirValidarMoverArchivoFunction:arrDatosArchivoSubir: ";print_r($arrDatosArchivoSubir);
 //echo "<br /><br />0-b-subirValidarMoverArchivoFunction:directorioSubidas: ";print_r($directorioSubir);	
	//echo "<br /><br />0-c-subirValidarMoverArchivoFunction:nomArchivoSinExtDestino: ";print_r($nomArchivoSinExtDestino);	
	echo "<br /><br />0-d-subirValidarMoverArchivoFunction:arrExtPermitidas: ";print_r($arrExtPermitidas);	
 
	$resSubirValidarMoverArchivo['codError'] = '00000';
	$resSubirValidarMoverArchivo['errorMensaje'] = 	"";
	
	/*--- Inicio preparar cadena de extensiones permitidas -------------------
	 DONDE $arrExtPermitidas es una array: extension, myme_type (con las extensiones permitidas)
	ejemplo = array(	"doc"=>"application/msword","gif"=>"image/gif","jpeg"	=>"image/jpeg"....) 
	--------------------------------------------------------------------------*/
/*
	$extPermitidas ='';	
	foreach ($arrExtPermitidas as $extension => $mimeType)
	{ $extPermitidas .= $extension.",";	//$extPermitidas="doc,docx,gif,jpeg,jpg,pdf,zip," es un string
	}
	$extPermitidas = rtrim($extPermitidas, ",");//quito la última coma final	
*/	
	//echo "<br /><br />1-a- resSubirValidarMoverArchivo:extPermitidas: ".$extPermitidas;		
	//--- Fin preparar cadena de extensiones permitidas ----------------------
	
	//----- Inicio preparar directorio y nombre de archivo a guardar -----------
	
	//---  Inicio Preparar path absoluto ----------------------------------

	$resSubirValidarMoverArchivo['directorioSubir'] = $directorioSubir;
		
	//$directorioSubirPath = $_SERVER['DOCUMENT_ROOT']."/../".$directorioSubir;		
	$directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorioSubir;		
	//echo "<br /><br />1-b-1 subirValidarMoverArchivoFunction:directorioSubirPath: ";print_r($directorioSubirPath);		
	//echo "<br /><br />1-b-2 subirValidarMoverArchivoFunction:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	//----- Fin preparar directorio ---------------------------------------

	//----- Inicio preparar nombre de archivo a guardar -------------------
	
	//-- Extraer extensión nombre de archivo fuente ----------
	
	$arrNomExtAchivoSubir = explode(".",$arrDatosArchivoSubir["name"]);//devuelve un array de string a partir del nombre del archivo obtenidos por la separación por .			 
	$iExt = count($arrNomExtAchivoSubir)-1; //por si habiese varios puntos incluidos en el nombre, se toma el último que corresponde solo a extensión			
	$extension = strtolower($arrNomExtAchivoSubir[$iExt]); //extensión en minúsculas
	
	//echo "<br /><br />1-b-3 resSubirValidarMoverArchivo:extension: ";print_r($extension);// mostrará gif, jpg, pdf, etc.

	//-- Fin de Extraer extensión --------------------------
	
	/*----- Inicio nombre archivo saneado ------------------
	   función sanearNombreArchivo($cadena,$maxLongNombre,$cambiarMayMin) en './modelos/modeloArchivos.php'
	   Devuelve solo el nombre sin la extensión,	sanea acentos, Ñ por N, Ç por C	y demás caracteres prohibidos
	   $nomArchivoSinExtDestino = nombre sin extension, la extension para calcular el tamaño del archivo
	   permite elegir  máx longitud nombre del nombre. 
	*/	
	require_once './modelos/modeloArchivos.php';//para la función sanearNombreArchivo() y Ñ por N Ç por C	
 /* 
	   Sanear nombre a subir archivos al servidor permite elegir el máx tamano del nombre (pero contrala que sea inferior a 255 bytes). 
	   Devolverá solo el nombre sin la extensión			
 */	
	$nomArchivoSinExtDestino = sanearNombreArchivo($nomArchivoSinExtDestino,$extension,$maxLongNombre);
	$nomAchivo_extension = $nomArchivoSinExtDestino.".".$extension;//añade a nombre de archivo a guardar
	
	$resSubirValidarMoverArchivo['nombreArchExtGuardado'] = $nomAchivo_extension;		
	
	//echo "<br /><br />1-b-4 subirValidarMoverArchivoFunction:nomAchivo_extension: ";print_r($nomAchivo_extension);	
	
	//----- Fin preparar directorio y nombre de archivo a guardar ------------------
		
	//--- Inicio validar datos archivo ----------------------------------------------------------------
	
	if (!isset($arrDatosArchivoSubir["name"]) || empty($arrDatosArchivoSubir["name"]))
	{ //echo "<br><br>2-1-subirValidarMoverArchivoFunction:name ";print_r($arrDatosArchivoSubir["name"]);		
   $resSubirValidarMoverArchivo["codError"] = '82000';// 4
		 $resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: no has introducido el archivo<br /><br />";
	}
	elseif (!isset($arrDatosArchivoSubir["tmp_name"]) || empty($arrDatosArchivoSubir["tmp_name"]))
	{ //echo "<br><br>2-2-subirValidarMoverArchivoFunction:tmp_name ";print_r($arrDatosArchivoSubir["tmp_name"]);	
   $resSubirValidarMoverArchivo["codError"] ='82040';// 4
		 $resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: no se ha creado el archivo temporal en el servido<br /><br />";	
	}	 		
	elseif(!array_key_exists($arrDatosArchivoSubir["type"], $arrExtPermitidas))
	//elseif (!in_array($arrDatosArchivoSubir["type"], $arrExtPermitidas))//tipo no permitido: if ($arrDatosArchivoSubir["type"]!=="image/gif")...)				
 { 
   //--
   $arrExtensionPerm =	array_values($arrExtPermitidasMime );//Array([0]=>doc [1]=>doc [2]=>docx [3]=>odt [4]=>odi [5]=>gif [6]=>jpg [7]=>jpeg [8]=>pdf) 	
   $arrExtensionPerm = array_unique($arrExtensionPerm);//evita duplicados
   $cadExtPermitidas = implode(",", $arrExtensionPerm);//= doc,docx,odt,odi,gif,jpg,jpeg,pdf
			echo "<br /><br />2-3-a subirValidarMoverArchivoFunction:cadExtPermitidas: ";print_r($cadExtPermitidas);			
   //--
		 $resSubirValidarMoverArchivo['codError'] = '82010';	 
   //$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: El archivo ".$arrDatosArchivoSubir["name"]." es un tipo de archivo no permitido (Sólo se aceptan: ". $extPermitidas.")<br /><br />";			
   $resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: El archivo ".$arrDatosArchivoSubir["name"]." es un tipo de archivo no permitido (Sólo se aceptan: ". $cadExtPermitidas.")<br /><br />";						
   echo "<br /><br />2-3-b subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);			
	}
	elseif ($arrDatosArchivoSubir["size"] >= $tamanioMax)//El tamaño es en BYTES 
	{ $resSubirValidarMoverArchivo["codError"] ='82020';// "1","2"
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Archivo ".$arrDatosArchivoSubir["name"]." es demasiado grande: ".round(($arrDatosArchivoSubir["size"]/1024/1024),2).
			"Kbytes (El máximo permitido son ".($tamanioMax/1024/1024)." KB)<br /><br />";
			
			//echo "<br /><br />2-4-a-subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);				
	}		
	elseif ($tamanioMax  > getsize(ini_get('upload_max_filesize')) ) //en modeloArchivos.php, supera la limitación del servidor para upload_files
	{$tamanioMax = ini_get('upload_max_filesize');

  $resSubirValidarMoverArchivo["codError"] = '82020';// "1","2"
		$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Archivo ".$arrDatosArchivoSubir["name"]." es demasiado grande: ".round(($arrDatosArchivoSubir["size"]/1024/1024),2).
		"MB (El máximo permitido por el sevidor son ".($tamanioMax/1024/1024)." MB)<br /><br />";
			
	  //echo "<br /><br />2-4-b-subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);							
	}	
	elseif (($arrDatosArchivoSubir["size"] == 0))//EN BYTES
	{ $resSubirValidarMoverArchivo["codError"] ='82030';// "1","2"
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Archivo -".$arrDatosArchivoSubir["name"]."- está vacío: tamaño 0 K <br /><br />";
			
			//echo "<br /><br />2-5-subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);				
	}	   
 elseif ($arrDatosArchivoSubir["error"] > 0)
 { $resSubirValidarMoverArchivo["codError"] ='82000';//"3"		
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR no identificado al subir archivo al directorio tmp-".$arrDatosArchivoSubir["name"]."- <br /><br />";		
			
			//echo "<br /><br />2-6-a-subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);				
 }
	elseif (!isset($directorioSubir ) || empty($directorioSubir))
	{
		 $resSubirValidarMoverArchivo['codError'] = '82060';
  	$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: variable de directorio destino vacía ";	
	}	
	elseif (!realpath($directorioSubirPath))	
	{ //echo "<br><br>2-6-b-subirValidarMoverArchivoFunction:directorioSubirPath: error: ".$directorioSubirPath;
	
	  $resSubirValidarMoverArchivo['codError'] = '82060';
  	$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: directorio absoluto de destino no existe ";			
	}	
	elseif (!is_dir(realpath($directorioSubirPath) ))
	{	//echo "<br /><br />2-7-a-subirValidarMoverArchivoFunction:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	
	  $resSubirValidarMoverArchivo["codError"] ='82060';//"3"		
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR el directorio elegido para subir el archivo no es un directorio -".$directorioSubir."- <br /><br />";		
	}	
 elseif (strlen(realpath($directorioSubirPath)) + 255 > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
	{ /*	PHP_MAXPATHLEN: Número entero.Longitud máxima de un nombre completo de fichero 
	    (incluyendo el árbol de directorios bajo el cual se encuentra). 
	    echo "<br /><br />----- PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
	  */
	  //echo "<br /><br />2-7-b-subirValidarMoverArchivoFunction:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
   $resSubirValidarMoverArchivo['codError'] = '82060';
	  $resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: longitud del path del directorio supera el valor permitido por el sistema, debe reducir la longitud del nobre del archivo o guardar en un directorio de path más corto";			
	}	
	elseif (!is_writable(realpath($directorioSubirPath)) )
	{	//echo "<br /><br />2-7-c-subirValidarMoverArchivoFunction:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	
	  $resSubirValidarMoverArchivo["codError"] ='82060';//"3"		
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura -".$directorioSubir."- <br /><br />";		
	}		
 elseif (file_exists(realpath($directorioSubirPath)."/".$nomAchivo_extension))
	{ 	  						
			$resSubirValidarMoverArchivo["codError"] = '82040';// "1","2"						
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: al subir el archivo ".$arrDatosArchivoSubir["name"].
			". Con el nombre -".$nomAchivo_extension."- ya existe un archivo en el directorio: ".$directorioSubir."<br />";		

			//echo "<br /><br />2-8-subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);						
	}
	//--- Fin validar datos archivo -------------------------------------------------------------------
	
	//--- Inicio mover el archivo temporal al directorio deseado y con el nombre escogido -------------	
	
	elseif (!move_uploaded_file($arrDatosArchivoSubir["tmp_name"],$directorioSubirPath."/".$nomAchivo_extension)) 					
	{								
			//echo "<br><br>3- resSubirValidarMoverArchivo:  ".$arrDatosArchivoSubir["error"]." uploading the file, ".$arrDatosArchivoSubir["name"]." please try again!";			
			$resSubirValidarMoverArchivo["codError"] = '82040';// "1","2"
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -".$arrDatosArchivoSubir["name"].": ".$resSubirValidarMoverArchivo["codError"]. 
			". Inténtalo de nuevo y en si no puedes avisa al adminstrador de la aplicación de Gestión de Soci@s <br /><br />";
	}	   
	else
	{	//echo "<br><br>4-1-subirValidarMoverArchivoFunction: El archivo: ".$arrDatosArchivoSubir["name"]." Se ha guardado como: ".$nomAchivo_extension;							
			
			//por seguridad chmod cambia los persmisos, por defecto a solo lectura =0444 en octal 
   
			if (!chmod($directorioSubirPath."/".$nomAchivo_extension, octdec($permisosArchivo))) 
			{
					//echo "<br><br>4-1--subirValidarMoverArchivoFunction:permisosArchivo:  ".$permisosArchivo;			
			  $resSubirValidarMoverArchivo["codError"] = '82040';// "1","2"
			  $resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -".$arrDatosArchivoSubir["name"].": ".$resSubirValidarMoverArchivo["codError"]. 
			  "No se pudo asignar los permisos indicados para el archivo subido. Inténtalo de nuevo y en si no puedes avisa al adminstrador de la aplicación de Gestión de Soci@s <br /><br />";				
			}
			//echo "<br><br>4-2-subirValidarMoverArchivoFunction:permisos archivo: ".substr(decoct(fileperms($directorioSubirPath."/".$nomAchivo_extension)), -4); //tipo 0444
			
	} 
	//-- Fin mover el archivo temporal al directorio deseado y con el nombre escogido -----------------	
			
	//echo "<br><br>5- -subirValidarMoverArchivoFunction:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);
	
	return $resSubirValidarMoverArchivo;			
}
/*-------------- Fin subirValidarMoverArchivoFunction() -------------------------------------------*/

/*---------------Inicio subirValidarMoverArchivoClass() -------------------------------------------
Agustin:2018_08_10 

DESCRIPCION: Sube un archivo al servidor y lo mueve a un directorio concreto, con un nombre saneado
             Valida archivo que se va a subir al servidor en cuanto a tamaño y tipos válidos y que 
             exista el directorio dónde se va a subir, que	no exista otro archivo con el mismo nombre 
													y ese caso(se podría renombrar) y establece unos permisos sobre el archivo determinados
													por parámetro $permisosArchivo, $permisosArchivo ='0444' por defecto solo lectura para todos
													Utiliza la clase: uploadVerot:class.upload.php, que al sanear el nombre del archivo 
													permite guiones bajos y también guiones medios												
													
Llamado: desde validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php en  
cPresidente.php:altaSocioPorGestorPres(), cCoordinador:altaSocioPorGestorCoord(), cTesorero.php:altaSocioPorGestorCoord():
             
Llama: uploadVerot/class_upload/class.upload.php

Recibe: $arrDatosArchivoSubir: array con datos: [name],[type],[size],[tmp_name],[error]  viene de _$FILES 
        y podría venir con otros valores añadidos por form o programa.
 $directorioSubir: directorio relativo donde se guardará el archivo. Ejem. /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR, o /documentos/ASOCIACION
 $nomArchivoSinExtDestino: nombre del archivo destino (sin la extensión), 
	$arrExtPermitidas: un array con las extensiones y tipos mime permitidos,
	$tamanioMax = tamaño máximo del archivo
	$permisosArchivo = permisos que se asignarán al archivo, por defecto solo lectura 0444
	
	Devuelve: un array con información de errores, ['directorioSubir'],y ['nombreArchExtGuardado']
	
	NOTA: NO USADA ACTUALMENTE: acualmente sustituye a la función: subirValidarMoverArchivoFunction() 
	 					o la suma de las funciones(validarSubirArchivoFunction()+moverArchivoFunction())
	      que no utiliza la clase uploadVerot:class.upload.php 
--------------------------------------------------------------------------------------------------*/
function subirValidarMoverArchivoClass_guardar($arrDatosArchivoSubir,$directorioSubir,$nomArchivoSinExtDestino,$arrExtPermitidas,$tamanioMax,$permisosArchivo ='0444')
{																																													
	//echo "<br /><br />0-a-subirValidarMoverArchivoClass:arrDatosArchivoSubir: ";print_r($arrDatosArchivoSubir);
 //echo "<br /><br />0-b-subirValidarMoverArchivoClass:directorioSubidas: ";print_r($directorioSubir);	
	//echo "<br /><br />0-c-subirValidarMoverArchivoClass:nomArchivoSinExtDestino: ";print_r($nomArchivoSinExtDestino);	
	//echo "<br /><br />0-d-subirValidarMoverArchivoClass:arrExtPermitidas: ";print_r($arrExtPermitidas);		
	
	$resSubirValidarMoverArchivo['codError'] = '00000';
	$resSubirValidarMoverArchivo['errorMensaje'] = 	"";	
	$resSubirValidarMoverArchivo['nombreArchExtFuente'] = $arrDatosArchivoSubir['name'];
	$resSubirValidarMoverArchivo['directorioSubir'] = $directorioSubir;
			
	/*--- Inicio preparar cadena de extensiones permitidas -------------------
	 DONDE $arrExtPermitidas es una array: extension, myme_type (con las extensiones permitidas)
	ejemplo = array(	"doc"=>"application/msword","gif"=>"image/gif","jpeg"	=>"image/jpeg"....) 
	--------------------------------------------------------------------------*/
	if (!isset($arrExtPermitidas) || empty($arrExtPermitidas))
	{
			$arrExtPermitidas = array(	
			                 "doc"	 		=>	"application/msword",
																				"docx"			=>	"application/vnd.openxmlformats-officedocument.wordprocessingml.document",			
																				"odt"		 	=>	"application/vnd.oasis.opendocument.text",        //Documento de texto OpenDocument		
																				"gif"		 	=>	"image/gif",
																				"jpeg"			=>	"image/jpeg",
																				"jpg"		 	=>	"image/jpeg",
																				"pdf"		 	=>	"application/pdf",
																				"zip"		 	=>	"application/zip");
	}	
	
	$extPermitidas ='';
	$arrMimePermitidas = array();
	
	foreach ($arrExtPermitidas as $extension => $mimeType)
	{ $extPermitidas .= $extension.",";	//$extPermitidas="doc,docx,gif,jpeg,jpg,pdf,zip," es un string
	  $arrMimePermitidas[] = $mimeType; //$arrMimePermitidas= rray('application/msword', 'image/gif','image/jpeg'....);	
	}
	$extPermitidas = rtrim($extPermitidas, ",");//quito la última coma final		
	
 //echo "<br /><br />1-a-subirValidarMoverArchivoClass:extPermitidas: ".$extPermitidas;		
 //echo "<br /><br />1-b-subirValidarMoverArchivoClass:arrMimePermitidas: ";print_r($arrMimePermitidas);	
	//--- Fin preparar cadena de extensiones permitidas ----------------------	

	//$directorioSubirPath = $_SERVER['DOCUMENT_ROOT']."/../".$directorioSubir;		echo "<br><br>directorioSubidas: ".$directorioSubirPath;//antiguo	
	$directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorioSubir;		
	
	if (!isset($arrDatosArchivoSubir) || empty($arrDatosArchivoSubir))
	{ //echo "<br><br>2-a-subirValidarMoverArchivoClass:arrDatosArchivoSubir: error: ";print_r($arrDatosArchivoSubir);
		 $resSubirValidarMoverArchivo['codError'] = '82060';
  	$resSubirValidarMoverArchivo['errorMensaje'] = " No se ha introducido el archivo fuente ";	
	}	
	elseif (!isset($directorioSubir ) || empty($directorioSubir))
	{ //echo "<br><br>2-b-subirValidarMoverArchivoClass:directorioSubir: error: ".$directorioSubir;
		 $resSubirValidarMoverArchivo['codError'] = '82060';
  	$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: variable de directorio destino vacía ";	
	}	
 elseif (!realpath($directorioSubirPath))	
	{ //echo "<br><br>2-c-subirValidarMoverArchivoClass:directorioSubirPath: error: ".$directorioSubirPath;
	
	  $resSubirValidarMoverArchivo['codError'] = '82060';
  	$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: directorio absoluto de destino no existe ";			
	}	
	elseif (!is_writable(realpath($directorioSubirPath)) )
	{	//echo "<br /><br />2-d-subirValidarMoverArchivoClass:realpathdirectorioSubirPath: ";print_r(realpath($directorioSubirPath));	
	
	  $resSubirValidarMoverArchivo["codError"] ='82060';		
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura -".$directorioSubir."- <br /><br />";		
	}		
	elseif (strlen($directorioSubirPath) + 255 > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
	{ /*---  Inicio Preparar path absoluto -------------------------------------
	   PHP_MAXPATHLEN: Número entero.Longitud máxima de un nombre completo de fichero 
	   (incluyendo el árbol de directorios bajo el cual se encuentra). 
	   echo "<br /><br />----- PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
   */
			$resSubirValidarMoverArchivo['codError'] = '82060';
			$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: longitud del path absoluto del directorio destino + nombre archivo supera el valor máximo permitido por el sistema,".
			PHP_MAXPATHLEN ." debe reducir la longitud del nombre del archivo o guardar en un directorio de path más corto";			
	}
	//---  Fin Preparar path absoluto -------------------------------------
	else
	{ 
			$directorioSubirPath =	realpath($directorioSubirPath)."/";
			
			//echo "<br><br>2-e-subirValidarMoverArchivoClass:directorioSubirPath: ".$directorioSubirPath;	
				
			$resSubirValidarMoverArchivo['pathArchGuardado'] = 	$directorioSubirPath;//path absoluto del directorio subir	
			//$resSubirValidarMoverArchivo['pathArchGuardado'] = 	$directorioSubir;//path relativo del directorio subir	

   // ------------------class upload ---------------------------------------------------------------
			require_once '../../usuariosLibs/classes/uploadVerot/class_upload/class.upload.php';	//versión
			/*
 			El nombre archivo destino "file_dst_name" saneado lo nombrará como venga (Mayus, mimu, mixto) 
			 La clase upload tiene una función "function sanitize($filename)" que podría servir para casi todo: a tener en cuenta como diferencias admite guión medio,
			 lo que hay en la función sanearNombreArchivo (y no se puede elegir máxima longitud del nombre del archivo, excepto la máxima que es 255 caracteres				
			 Siempre pondrá extensión en minúsculas, (255 máxima longitud de nombre archivo y extensión )
			 $handle = new upload($arrDatosArchivoSubir, 'es_ES');//class.upload.es_ES
			 Controla upload_max_filesize (200M) y file_dst_name_ext >255 bytes
			*/

			$handle = new upload($arrDatosArchivoSubir, 'es_ES_EL');//class.upload.es_ES_EL puedo personalizar los mensajes de error
				
			/*echo "<br /><br />2-1a file_src_name: ".$handle->file_src_name; //Source file name
			echo "<br /><br />2-1b file_src_name_body: ".$handle->file_src_name_body; //Source file name body
			echo "<br /><br />2-1c file_src_pathname: ".$handle->file_src_pathname;// Source file complete path and name //file_src_pathname= es el campo: [tmp_name] =  /tmp/phpbnoHFY
			*/
			$resSubirValidarMoverArchivo['tmp_name'] = $handle->file_src_pathname;// Source file complete path and name //file_src_pathname= es el campo: [tmp_name] =  /tmp/phpbnoHFY	

			if (!$handle->uploaded)
   { //echo "<br><br>2-f-subirValidarMoverArchivoClass:error: " . $handle->error;	
		   
					$resSubirValidarMoverArchivo['codError'] = '82000';// 4
					$resSubirValidarMoverArchivo['errorMensaje'] = 	$handle->error;				
			}
   else //	if ($handle->uploaded) = true //se sube a archivo temporal
			{/*echo "<br /><br />2-3 file_src_name_ext: ".$handle->file_src_name_ext; //Source file extension
				echo "<br /><br />2-4 file_src_pathname: ".$handle->file_src_pathname;// Source file complete path and name //file_src_pathname= es el campo: [tmp_name] =  /tmp/phpbnoHFY
				echo "<br /><br />2-5 file_src_mime: ".$handle->file_src_mime;// Source file mime type
				echo "<br /><br />2-6a file_src_size: ".$handle->file_src_size;// Source file size in bytes
				echo "<br /><br />2-6b file_max_size: ".$handle->file_max_size;// Source file size in bytes
				echo "<br /><br />2-7a file_src_error: ".$handle->file_src_error;// Upload error code
				echo "<br /><br />2-7b file_src_error: ";print_r($handle->file_src_error);// Upload error code	
				*/				
				/* ejemplo: $handle->allowed = array('application/pdf','application/msword', 'image/*');
				   allowed array of allowed mime-types (or one string). (default: check init())
				*/
				$handle->allowed = $arrMimePermitidas;				
				
				/*ejemplo: $this->forbidden =  array('image/*','text/csv'); 
				  No es necesario: lo que no está permitido está prohibido se acepta(default: check init())
				*/				
				/* $handle->file_max_size es el tamaño máximo del fichero fuente en bytes 
				   también se controla que no sea superior a limite en php.ini = ini_get('upload_max_filesize');//200M
				   esta variable también podría venir del formulario			
				*/
				$handle->file_max_size = $tamanioMax; 
				$handle->file_new_name_body = $nomArchivoSinExtDestino;	//ejemplo: $handle->file_new_name_body ='image_nombre';	(sin extensión)
				
				/*Si el archivo fuese tipo imagen	: 'png'|'jpeg'|'gif'|'bmp', opcionalmente se podría efectuar 
				  cambios de tamaño, rotaciones, y comprimir: png con png_compression, 
						o jpeg con jpeg_size if set to a size in bytes,
						will approximate jpeg_quality so the output image fits within the size,etc						
						$handle->image_resize  = true;//si no es un fichero de imagen por ejemplo pdf, no cambia tamaño aunque ponga true
						$handle->image_x       = 100;
						$handle->image_ratio_y = true;  
				*/
				$handle->process($directorioSubirPath	); //ejemplo: $handle->process('/home/user/filesSubir/');				

				if (!$handle->processed)  //no se pudo ejecutar el proceso de mover temporal u otros procesos
				{ //echo "<br><br>3-a-subirValidarMoverArchivoClass:error: " . $handle->error;	
						
						/*En caso de error: tipo no permitido 'Tipo de archivo incorrecto.' y otros
						los valores destino no tienen valor:
						echo "<br /><br />3-b: file_dst_path: ".$handle->file_dst_path; //Destination file path 
						y otros ...
 		   */
						$resSubirValidarMoverArchivo['codError'] = '82000';// 4
						$resSubirValidarMoverArchivo['errorMensaje'] = 	$handle->error;	
						
						/*Ver posibilidad de añadir texto en tiempo de ejecución al mensaje, 
						 ejemplo: linea 3216 $this->error = $this->translate('already_exists', array($this->file_dst_name));
				   Line 3432: $this->error = $this->translate('no_create_support', array('JPEG'));		
      */
      $handle->clean();// elimina los temporary files
						
				}// if (!$handle->processed)					
				else //else if ($handle->processed) =true // se pudo ejecutar el proceso de mover temporal u otros procesos
				{ 
				  /*echo "<br /><br />4-a: file_dst_path: ".$handle->file_dst_path; //Destination file path: /home/virtualmin/europalaica.com/upload/FIRMAS_ALTAS_SOCIOS_GESTOR/			
						echo "<br /><br />4-b: file_dst_name_body: ".$handle->file_dst_name_body; //Destination file name body: VILLA-MARTiN-Cosgaya-ayue_Nuno_SEGUNDO-noventaydos2018-08-05-07-47-25
						echo "<br /><br />4-c: file_dst_name_ext: ".$handle->file_dst_name_ext; //Destination file extension: jpg
						echo "<br /><br />4-d: file_dst_name: ".$handle->file_dst_name; //Destination file name: VILLA-MARTiN-Cosgaya-ayue_Nuno_SEGUNDO-noventaydos2018-08-05-07-47-25.jpg
						echo "<br /><br />4-e: file_dst_pathname: ".$handle->file_dst_pathname;//Destination file complete path and name:/home/virtualmin/europalaica.com/upload/FIRMAS_ALTAS_SOCIOS_GESTOR/VILLA-MARTiN-Cosgaya-ayue_Nuno_SEGUNDO-noventaydos2018-08-05-07-47-25.jpg
			   */
						$resSubirValidarMoverArchivo['nombreArchExtGuardado'] = $handle->file_dst_name; //al sanear el nombre del archivo se puede haber modificado el nombre del archivo
						$resSubirValidarMoverArchivo['pathArchGuardado'] = $handle->file_dst_path; //se reescribe porque pudiera haber sido modificado por la clase (crear uno nuevo, etc.)				
						
						/*Cambio de los permisos solo sobre el archivo a los valores de $permisosArchivo
						  por seguridad chmod cambia los permisos, por defecto a solo lectura =0444 en octal
					   chmod($handle->file_dst_path.$handle->file_dst_name, octdec($permisosArchivo));//0444: Solo Lectura para todos en octal
						*/
						if (!chmod($handle->file_dst_path.$handle->file_dst_name, octdec($permisosArchivo)))
						{
								//echo "<br><br>4-1-subirValidarMoverArchivoClass:permisosArchivo:  ".$permisosArchivo;			
								$resSubirValidarMoverArchivo["codError"] = '82040';// "1","2"
								$resSubirValidarMoverArchivo['errorMensaje'] = "ERROR: Al subir el archivo -".$handle->file_dst_name.": ".$resSubirValidarMoverArchivo["codError"]. 
								"No se pudo asignar los permisos indicados para el archivo subido . Inténtalo de nuevo y en si no puedes avisa al adminstrador de la aplicación de Gestión de Soci@s <br /><br />";				
						}
					 //echo "<br><br>4-2-subirValidarMoverArchivoClass: permisos archivo: ".substr(decoct(fileperms($handle->file_dst_path.$handle->file_dst_name)), -4); // 0444							
						
					 $handle->clean();// elimina los temporary files
				} //else if ($handle->processed) =true				
			} //if ($handle->uploaded) = true
	}
	//echo "<br><br>5- subirValidarMoverArchivoClass:resSubirValidarMoverArchivo: ";print_r($resSubirValidarMoverArchivo);
	
	return $resSubirValidarMoverArchivo;			
}
/*--------------- Fin subirValidarMoverArchivoClass() ----------------------------------------------

/*====== Fin NO USADAS ACTUALMENTE ================================================================*/


/*====== INICIO PARA PRUEBAS DESCARGAR Y SUBIR ARCHIVOS AL SERVIDOR (EN DESARROLLO) ===============*/
/*=================================================================================================*/
/*--------------------- Inicio obtenerListadoArchivosUnDirectorio() -------------------------------
Se obtiene la lista de archivos existentes en UN DIRECTERIO DEL SERVIDOR, 
sin incluir posibles subdirectorios cumpla ciertas condiciones en cuanto a tamaño 
Iterativamente. 

RECIBE: $directorioArchivos: cadena con el directorio relativo del que se quiere obtener 
el listado de los archivos. Será relativo a la dirección europalaica.com/directorio.
ej. $directorioArchivos = "documentos/SOCIOS"

DEVUELVE: array con ['directorio'] = $directorioArchivos, 
          ['listaArchivos'] = con datos de "Nombre", "Tamaño", "Modificado", 
									 e información de errores
          												
LLAMADO : ControladorSocios.php:descargarDocsSocio(), cCoordinador.php:descargarDocsCoord()
          (y se podrían completar con más funciones  ....)
LLAMA: nada

OBSERVACIONES:
NOTA: existe la clase La clase SplFileInfo, que se podría utilizar como alternativa a este desarrollo
Agustin: 2020-04-21 añado para pruebas
--------------------------------------------------------------------------------------------------*/
//function obtenerListadoArchivosUnDirectorio($directorioArchivos)//comento para unificar formatos de camampos con el recursivo
//NoRecusivo Probado en socios, para un solo directorios
function obtenerListadoArchivosUnDirectorio($directorioPathArchivosAbrir)
{ 
  //echo "<br><br>0-1 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:directorioArchivos: ";print_r($directorioArchivos);echo "<br><br>";
		echo "<br><br>0-1 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:directorioPathArchivosAbrir: ";print_r($directorioPathArchivosAbrir);echo "<br><br>";
  
		//$arrListadoDeArchivos['codError'] = '00000';//comento para unificar formatos de camampos con el recursivo
		//$arrListadoDeArchivos['errorMensaje'] ='';
  //$arrListadoDeArchivos['directorio'] = $directorioArchivos;
		
		$res = array();// Array en el que obtendremos los resultados 
				
		//$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);//dir absoluto //comento para unificar formatos de camampos con el recursivo

		if(substr($directorioPathArchivosAbrir, -1) != "/") $directorioPathArchivosAbrir .= "/"; // Agregamos la barra invertida al final en caso de que no exista
 
  echo "<br><br>1 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:directorioPathArchivosAbrir: ";print_r($directorioPathArchivosAbrir);echo "<br><br>";  
  
		//$dir = @dir($directorioPathArchivosAbrirMal);		
		$dir = @dir($directorioPathArchivosAbrir);// Creamos un puntero al directorio y obtenemos el listado de archivos				 
		
			echo "<br><br>4 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:dir: ";var_dump($dir);echo "<br><br>";	
					 
		if (!isset($dir) || empty($dir) || $dir == false)//Error al abrir el directorio $directorio para leerlo
		{ 
		  $arrListadoDeArchivos['codError'] = '82060';
			 $arrListadoDeArchivos['errorMensaje'] ='Error al listar archivos directorio';
				//se podría insertar error
		}
		else
		{	while( ($archivo = $dir->read() ) !== false) 
				{   
								echo "<br><br>3 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:res: "; print_r($res);	
							
								if($archivo[0] == ".") continue;  // Obviamos los archivos ocultos
								
								if(is_dir($directorioPathArchivosAbrir.$archivo)) 
								{
												$res[] = array(
														"DirPath" => $directorioPathArchivosAbrir,															
														"Nombre" =>  $archivo."/",
														"Tamaño" => 0,
														"Modificado" => filemtime($directorioPathArchivosAbrir.$archivo)
												);
								} 
								else if (is_readable($directorioPathArchivosAbrir.$archivo)) 
								{
												$res[] = array(
														"DirPath" => $directorioPathArchivosAbrir,															
														"Nombre" => $archivo,
														"Tamaño" => filesize($directorioPathArchivosAbrir.$archivo),
														"Modificado" => filemtime($directorioPathArchivosAbrir.$archivo)
												);
								}
				}				
    $dir->close();
				
				echo "<br><br>4-1 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:res: "; print_r($res);
				/*		
				array_multisort ( array &$array1 [, mixed $array1_sort_order = SORT_ASC [, mixed $array1_sort_flags = SORT_REGULAR [, mixed $... ]]] ) : bool
				array_multisort ( $res, array('Nombre'=>SORT_ASC), SORT_STRING);SORT_DESC
				*/
				array_multisort(array_column($res, 'Nombre'), SORT_ASC, $res);
				
				echo "<br><br>4-2 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:res: "; print_r($res);
				
				//$arrListadoDeArchivos['listaArchivos'] = $res;//comento para unificar formatos de camampos con el recursivo
				$arrListadoDeArchivos = $res;					
  }

		
		echo "<br><br>4-3 modeloArchivos.php:obtenerListadoArchivosUnDirectorio:res: "; print_r($res);
		
  return $arrListadoDeArchivos;		
}//function
/*--------------------- Fin obtenerListadoArchivosUnDirectorio() ----------------------------*/

//Probado al ordenar solo ordenar por un campo, funciona recursivo y no recursivo
//function obtenerListadoDeArchivosRecurMal($directorioArchivos, $recursivo = false)
function obtenerListadoDeArchivosRecur($directorioPathArchivosAbrir, $recursivo = false)//Probado al ordenar solo ordenar por un campo
{ 
 /*NOTA: PROBAR USAR //DIRECTORY_SEPARATOR es / o \ segun el sistema operativo
   $result[$value] = dirToArray($dir.DIRECTORY_SEPARATOR.$value);//EN LUGAR DE dirToArray($dir. "/".$value
	*/
  //echo "<br><br>0-1 modeloArchivos.php:obtenerListadoDeArchivos:directorioArchivos: ";print_r($directorioArchivos);echo "<br><br>";
  
		//$arrListadoDeArchivos['codError'] = '00000';
		//$arrListadoDeArchivos['errorMensaje'] ='';
  //$arrListadoDeArchivos['directorio'] = $directorioArchivos;
		
		$res = array();// Array en el que obtendremos los resultados 
				
		//$directorioPathArchivosAbrir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$directorioArchivos);// dir absoluto

		//if(substr($directorioPathArchivosAbrir, -1) != "/") $directorioPathArchivosAbrir .= "/"; // Agregamos la barra invertida al final en caso de que no exista
		if(substr($directorioPathArchivosAbrir, -1) != DIRECTORY_SEPARATOR) $directorioPathArchivosAbrir .= DIRECTORY_SEPARATOR; // Agregamos la barra invertida al final en caso de que no exista
  echo "<br><br>1 modeloArchivos.php:obtenerListadoDeArchivos:directorioPathArchivosAbrir: ";print_r($directorioPathArchivosAbrir);echo "<br><br>";  
  
		//$dir = @dir($directorioPathArchivosAbrirMal);		
		$dir = @dir($directorioPathArchivosAbrir);// Creamos un puntero al directorio y obtenemos el listado de archivos				 
		
		echo "<br><br>2 modeloArchivos.php:obtenerListadoDeArchivos:dir: "; var_dump($dir);echo "<br><br>";	
					 
		if (!isset($dir) || empty($dir) || $dir == false)//Error al abrir el directorio $directorio para leerlo
		{ 
		  $arrListadoDeArchivos['codError'] = '82060';
			 $arrListadoDeArchivos['errorMensaje'] ='Error al listar archivos directorio';
				return $arrListadoDeArchivos;	
		}
		else	 	
		{	
	   while( ($archivo = $dir->read() ) !== false) 
				{   
								echo "<br><br>3 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);	
							
								if($archivo[0] == ".") continue;  // Obviamos los archivos ocultos PONER TAMBIEN ..
								
								if(is_dir($directorioPathArchivosAbrir.$archivo)) 
								{
												$res[] = array(
												  "Tipo" => 'direct',
														"DirPath" => $directorioPathArchivosAbrir,
														//"NombreDirectorio" =>  $directorioPathArchivosAbrir.$archivo."/",
														//"Nombre" =>  $archivo."/",
														"Nombre" =>  $archivo.DIRECTORY_SEPARATOR,
														//"NombreDir" =>  $archivo."/",														
														"Tamaño" => 0,
														"Modificado" => filemtime($directorioPathArchivosAbrir.$archivo)
												);
												
											//if($recursivo && is_readable($directorio . $archivo . "/"))
           //if($recursivo && is_readable($directorioPathArchivosAbrir.$archivo . "/")) 				
           if($recursivo && is_readable($directorioPathArchivosAbrir.$archivo.DIRECTORY_SEPARATOR)) 																			
											{        
													//$directorioInterior= $directorioPathArchivosAbrir.$archivo . "/";
													$directorioInterior= $directorioPathArchivosAbrir.$archivo . DIRECTORY_SEPARATOR;
										
													//$res = array_merge($res, obtenerListadoDeArchivos($directorioInterior, true));
													$res = array_merge($res, obtenerListadoDeArchivosRecur($directorioInterior, true));
           }
								} 
								else if (is_readable($directorioPathArchivosAbrir.$archivo)) 
								{
												$res[] = array(
												  "Tipo" => 'arch',
              "DirPath" => $directorioPathArchivosAbrir,	
														//"NombreX" =>  $directorioPathArchivosAbrir.$archivo."/",
														"Nombre" => $archivo,
														//"NombreArch" => $archivo,
														"Tamaño" => filesize($directorioPathArchivosAbrir.$archivo),
														"Modificado" => filemtime($directorioPathArchivosAbrir.$archivo)
												);
								}
				}				
    $dir->close();
  }
		
		//$arrListadoDeArchivos['listaArchivos'] = $res;
		$arrListadoDeArchivos = $res;		
		
		echo "<br><br>4 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
		
  return $arrListadoDeArchivos;		
		//return $res;		
}//function

function obtenerListadoDeArchivosRecurOtra($directorio, $recursivo=false)
{
 echo "<br><br>0-1 modeloArchivos.php:obtenerListadoDeArchivos:directorio: ";print_r($directorio);echo "<br><br>";
  // Array en el que obtendremos los resultados
  $res = array();
 $orden =1;
  // Agregamos la barra invertida al final en caso de que no exista
  if(substr($directorio, -1) != "/") $directorio .= "/";
 
  // Creamos un puntero al directorio y obtenemos el listado de archivos
  $dir = @dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
  
	echo "<br><br>2 controladorSocios:mostrarDocsSocio:dir: "; var_dump($dir);echo "<br><br>";	
	
		while(($archivo = $dir->read()) !== false) 
		{ 
	   echo "<br><br>3-1 modeloArchivos.php:obtenerListadoDeArchivos:archivo: "; print_r($archivo);	 
    // Obviamos los archivos ocultos
    if($archivo[0] == "." || $archivo[0] == "..") continue;
    echo "<br><br>3-2 modeloArchivos.php:obtenerListadoDeArchivos:archivo: "; print_r($archivo);	 
				
				if(is_dir($directorio . $archivo)) 
				{ echo "<br><br>3-3 modeloArchivos.php:obtenerListadoDeArchivos:archivo: "; print_r($archivo);	
			   
      $res[] = array(
						  "Tipo" => 'direct',
								"path" => $directorio . $archivo . "/",
        //"Nombre" => $directorio . $archivo . "/",
								"Nombre" => "/".$archivo,
        "Tamaño" => 0,
        "Modificado" => filemtime($directorio . $archivo)
      );
      
						if($recursivo && is_readable($directorio . $archivo . "/")) 
						{        
								$directorioInterior = $directorio . $archivo . "/";
        echo "<br><br>4-1 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
								
								$res = array_merge($res, obtenerListadoDeArchivosRecur($directorioInterior, true));
								
								echo "<br><br>4-2 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
								//$res = array_merge(obtenerListadoDeArchivosRecur($directorioInterior, true),$res);
      }						
    } 
				else if (is_readable($directorio . $archivo)) 
				{   echo "<br><br>5-1 modeloArchivos.php:obtenerListadoDeArchivos:archivo: "; print_r($archivo);
        $res[] = array(
								  "Tipo" => 'arch',
										"path" => $directorio . $archivo . "/",
          //"Nombre" => $directorio . $archivo,
										"Nombre" => /*$directorio .*/ $archivo,
          "Tamaño" => filesize($directorio . $archivo),
          "Modificado" => filemtime($directorio . $archivo)
        );
								echo "<br><br>5-2 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
    }
  }
  $dir->close();
		echo "<br><br>5-1 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
		//asort($res);
		
		$res = array_sort($res, 'Nombre', SORT_ASC); // Sort by surname
	 echo "<br><br>5-2 modeloArchivos.php:obtenerListadoDeArchivos:res: "; print_r($res);
		
  return $res;		
}

/*--------------------- Inicio obtenerListadoDeArchivosRecur() ---------------------------------------*/

function dirToArray($dir) 
{
  //DIRECTORY_SEPARATOR es / o \ segun el sistema operativo
   $result = array();

   $cdir = scandir($dir);
			
   foreach ($cdir as $key => $value)
   {
      if (!in_array($value,array(".","..")))
      {
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
         {  echo "<br><br>2-1 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
												
												echo "<br><br>2-2 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
         }
         else
         {
            $result[] = $value;
												echo "<br><br>3 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
         }
      }
   }
   echo "<br><br>4 modeloArchivos.php:dirToArray:resul: "; print_r($result);
   return $result;
}

function dirToArrayMio($dir) 
{
  //DIRECTORY_SEPARATOR es / o \ segun el sistema operativo
   $result = array();

   $cdir = scandir($dir);
			
   foreach ($cdir as $key => $value)
   {
      if (!in_array($value,array(".","..")))
      {
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
         {  echo "<br><br>2-1 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
            $result[$value]= dirToArray($dir . DIRECTORY_SEPARATOR . $value);
												
												echo "<br><br>2-2 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
         }
         else
         {
            $result[] = $value;
												echo "<br><br>3 modeloArchivos.php:dirToArray:resul: "; print_r($result);
								
         }
      }
   }
			echo "<br><br>4-0 modeloArchivos.php:dirToArray:resul: "; print_r($result);
			
   echo "<br><br>4-1 modeloArchivos.php:dirToArray:value: "; print_r($value);
			unset($value);
			$re['lista'] = $result;
			echo "<br><br>4-2 modeloArchivos.php:dirToArray:re: "; print_r($re);	echo "<br><br>";
	
//$data['lista'] = $result;	
$data = $result;	
foreach ($data as $index => $value)
{
			if (is_array($value))
			{									
							foreach ($value as $ind => $val)
							{
										if (is_array($val))
										{
														foreach ($val as $i => $v)
														{
																		echo "$i : $v <br />";
														}
										}
										else
										{
														echo "$ind : $val <br />";  
										}
							}										
					
			}
			else{
							echo "$index : $value <br />";
			}
}
/*
foreach ($data as $index => $value)
{		if (is_array($value)){
							foreach ($value as $ind => $val)
							{
											if (is_array($val))
											{
															foreach ($val as $i => $v)
															{
																			echo "$i : $v <br />";
															}
											}
											else{
															echo "$ind : $val <br />";  
											}
							}
			}
			else{
							echo "$index : $value <br />";
			}
}
*/
/* funcion recursiva igual a anterior pero para indefido nº
function fn($arg)
{
    foreach ($arg as $key => $val)
				{
        if (is_array($val))
								{
            fn($val);
        }
        else
								{
            echo "$key : $val\n";
        }
    }
} 
llamada: fn($data);
*/		

   return $result;
}
//echo "<br><br>4-2 modeloArchivos.php:dirToArray:resul: "; print_r($result);
function recursive_read($directory, $entries_array = array()) 
{
    if(is_dir($directory)) 
				{
        $handle = opendir($directory);
								
        while(FALSE !== ($entry = readdir($handle))) 
								{
            if($entry == '.' || $entry == '..') 
												{
                continue;
            }
            $Entry = $directory . DS . $entry;
												
            if(is_dir($Entry)) 
												{
                $entries_array = recursive_read($Entry, $entries_array);
            } 
												else 
												{
                $entries_array[] = $Entry;
            }
        }
        closedir($handle);
    }
    return $entries_array;
}

/*Simple function to sort an array by a specific key. Maintains index association.*/

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
/*
$people = array(
    12345 => array(
        'id' => 12345,
        'first_name' => 'Joe',
        'surname' => 'Bloggs',
        'age' => 23,
        'sex' => 'm'
    ),
    12346 => array(
        'id' => 12346,
        'first_name' => 'Adam',
        'surname' => 'Smith',
        'age' => 18,
        'sex' => 'm'
    ),
    12347 => array(
        'id' => 12347,
        'first_name' => 'Amy',
        'surname' => 'Jones',
        'age' => 21,
        'sex' => 'f'
    )
);
print_r(array_sort($people, 'age', SORT_DESC)); // Sort by oldest first
print_r(array_sort($people, 'surname', SORT_ASC)); // Sort by surname
*/

/*======= FIN PARA PRUEBAS SUBIR Y DESCARGAR ARCHIVOS AL SERVIDOR (EN DESARROLLO) ===============*/
?>