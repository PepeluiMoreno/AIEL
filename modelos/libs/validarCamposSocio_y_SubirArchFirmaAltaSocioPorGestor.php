<?php
/*-------------------------------------------------------------------------------------------------- 
FICHERO: validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

Include dos funciones:
1- "validarCamposAltaSocioPorGestor()" Valida los campos recibidos con los datos del socio, que serán 
para BBDD, recibido desde formularios de formAltaSocioPorGestorCoord.php, y formAltaSocioPorPres.php, ....

2- "validarSubirArchivoFunction()" para validar los datos del archivo a subir con la firma del socio.
En caso de que los validaciones sean correctas, se pasarán los datos al controlador correspondiente 
para después llamar al modeloPresCoord.php:mAltaSocioPorGestor(), que registra en la BBDD el alta	
y sube al servidor el archivo de la firma del socio (en este proceso se sube y mueve este archivo a 
un	directorio determinado, con el nombre del socio y fecha, todo validado)
	
RECIBE:"$camposFormRegSocio" array con los datos personales del socio para BBDD, y también contiene 
        el array "[ficheroAltaSocioFirmado]" con [cadExtPermitidas],[MaxArchivoSize],[directorioSubir]
								[maxLongNomArchivoSinExtDestino],[permisosArchivo] => 0444 )
        "$arrFicheroAltaSocioFirmado", array que procedente de $_FILES['ficheroAltaSocioFirmado'] con
							 los datos del archivo a subir: [name],[type],[size],[tmp_name],[error].
DEVUELVE: array "$resValidarCamposAltaSocioPorGestor"	con los datos personales del socio y datos del 
          Archivo Firmado de Alta Socio Por Gestor. Además de los códigos de error.		
										
LLAMADA: cCoordinador:altaSocioPorGestorCoord(), cPresidente:altaSocioPorGestorCoord()
               y cTesorero:altaSocioPorGestorCoord()
					
LLAMA: modelos/libs/validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor(),
       modeloArchivos.php:validarSubirArchivo(),arrMimeExtArchAltaSocioFirmaPermitidas() 
																	
OBSERVACIONES:	No afecta PDO. Probado PHP 7.3.21													
--------------------------------------------------------------------------------------------------*/
function validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor($camposFormRegSocio,$arrFicheroAltaSocioFirmado ='')
{
	//echo "<br><br>0-1 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:camposFormRegSocio: ";print_r($camposFormRegSocio);
	//echo "<br><br>0-2 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:arrFicheroAltaSocioFirmado: ";print_r($arrFicheroAltaSocioFirmado);
	
 require_once './modelos/modeloArchivos.php'; 
		
	$resValidarCamposAltaSocioPorGestor['codError'] = '00000';
 $resValidarCamposAltaSocioPorGestor['errorMensaje'] ='';
	$resValidarCamposAltaSocioPorGestor['totalErrores'] = 0;

	//------------- Inicio validarCamposAltaSocioPorGestor (para BBDD) ---------------------------

	require_once './modelos/libs/validarCamposSocioPorGestor.php';	
	$resValDatosAltaSocioPorGestor = validarCamposAltaSocioPorGestor($camposFormRegSocio);//solo válida datos alta socio para BBDD	
	
	//echo "<br><br>1 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:resValDatosAltaSocioPorGestor: ";print_r($resValDatosAltaSocioPorGestor); 	
 
	$resValidarCamposAltaSocioPorGestor = $resValDatosAltaSocioPorGestor;	
	
 if ($resValDatosAltaSocioPorGestor['codError'] !== '00000')		
 { $resValidarCamposAltaSocioPorGestor = $resValDatosAltaSocioPorGestor;	
	}
	//------------- Fin validarCamposAltaSocioPorGestor (para BBDD) -----------------------------
	
	//---------------------------- Inicio validarSubirArchivo -----------------------------------

	//echo "<br /><br />2-1 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:arrFicheroAltaSocioFirmado: ";print_r($arrFicheroAltaSocioFirmado); 					
	//echo "<br /><br />2-2 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:camposFormRegSocio['ficheroAltaSocioFirmado']: ";print_r($camposFormRegSocio['ficheroAltaSocioFirmado']); 					
	
	//--- Inicio preparar datos para validarSubirArchivoFunction y para return a controlador ---
	
	//----------------- Inicio Valores que vienen de formulario formulario	-------------------
 $arrFicheroAltaSocioFirmado = $arrFicheroAltaSocioFirmado + $camposFormRegSocio['ficheroAltaSocioFirmado'];//fusión de arrays	
	
	/*	También se puede hacer uno a uno, que facilita recordar los campos:
	$arrFicheroAltaSocioFirmado['cadExtPermitidas'] = $camposFormRegSocio['ficheroAltaSocioFirmado']['cadExtPermitidas'];//del formulario	para mostralo.	
	$arrFicheroAltaSocioFirmado['MaxArchivoSize'] = $camposFormRegSocio['ficheroAltaSocioFirmado']['MaxArchivoSize'];	//del formulario	para limitar tamaño arch.
	$arrFicheroAltaSocioFirmado['directorioSubir'] = $camposFormRegSocio['ficheroAltaSocioFirmado']['directorioSubir'];//en formulario /../upload/PD_ALTAS_SOCIOS_GESTOR
	$arrFicheroAltaSocioFirmado['permisosArchivo'] = $camposFormRegSocio['ficheroAltaSocioFirmado']['permisosArchivo'];//= '0444'; solo lectura para todos, solo se necesita para moverArchivo() 
	$arrFicheroAltaSocioFirmado['maxLongNomArchivoSinExtDestino'] = $camposFormRegSocio['ficheroAltaSocioFirmado']['maxLongNomArchivoSinExtDestino'];//='250' límite de caracteres para el nombre del archivo saneado 	
	*/	
	//----------------- Fin Valores que vienen de formulario formulario	----------------------	
 require_once './modelos/modeloArchivos.php';
	$arrMimeExtArchAltaSocioFirmaPermitidas	= 	arrMimeExtArchAltaSocioFirmaPermitidas();//en modelos/modeloArchivos.php			
	/*$arrMimeExtArchAltaSocioFirmaPermitidas	= array("application/msword"																																					=>	"doc",
																																                   	................................................................
																																	                  "application/pdf" 																																							=>	"pdf"		 	
																																                  );	
	*/								
	$arrFicheroAltaSocioFirmado['arrMimeExtPermitidas']	= $arrMimeExtArchAltaSocioFirmaPermitidas;
	
	//echo "<br /><br />2-3 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:arrFicheroAltaSocioFirmado: ";print_r($arrFicheroAltaSocioFirmado); 
	
	$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado'] = $arrFicheroAltaSocioFirmado;//para que los pase a controlador
	
	//--- Fin preparar datos para validarSubirArchivoFunction y para return a controlador -------
				
	if (!isset($arrFicheroAltaSocioFirmado["name"]) || empty($arrFicheroAltaSocioFirmado["name"]) )// NO EXISTE ARCHIVO
	{	$resValidarCamposAltaSocioPorGestor['codError'] = '82060';
			$resValidarCamposAltaSocioPorGestor['errorMensaje'] = "No se ha introducido el archivo";		
			$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['codError'] = '82060';
			$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['errorMensaje'] = ' Introduce el archivo';	
			
			//echo "<br /><br />3-1 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:	resValidarCamposAltaSocioPorGestor: ";print_r($resValidarCamposAltaSocioPorGestor); 						
	}
	else //if NOT (!isset($arrFicheroAltaSocioFirmado["name"]) || empty($arrFicheroAltaSocioFirmado["name"]) )// NO EXISTE ARCHIVO
	{		
		$resValidarSubirArchivoFirmaSocio = validarSubirArchivo($arrFicheroAltaSocioFirmado,$arrFicheroAltaSocioFirmado['directorioSubir'],$arrFicheroAltaSocioFirmado['arrMimeExtPermitidas'],
																																																									 $arrFicheroAltaSocioFirmado['MaxArchivoSize']);//en modeloArchivos.php, //devuelve extensión
	
		//echo "<br /><br />3-2 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:resValidarSubirArchivoFirmaSocio: ";print_r($resValidarSubirArchivoFirmaSocio);						
		
		if ($resValidarSubirArchivoFirmaSocio['codError'] !== '00000')
		{ $resValidarCamposAltaSocioPorGestor['codError'] = $resValidarSubirArchivoFirmaSocio['codError'];

				$resValidarCamposAltaSocioPorGestor['errorMensaje'] .= $resValidarSubirArchivoFirmaSocio['errorMensaje'];					
				$resValidarCamposAltaSocioPorGestor['totalErrores']++;//creo que sobra 

				$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['codError'] = $resValidarSubirArchivoFirmaSocio['codError'];
				$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['errorMensaje'] = $resValidarSubirArchivoFirmaSocio['errorMensaje'];	
		}
		else
		{	$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['extension'] = $resValidarSubirArchivoFirmaSocio['extension'];//si no hay error lo devuelve validarSubirArchivo()
				$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['codError'] = '00000';
				$resValidarCamposAltaSocioPorGestor['ficheroAltaSocioFirmado']['errorMensaje'] = '';	
		}						
	}//else //if NOT (!isset($arrFicheroAltaSocioFirmado) || empty($arrFicheroAltaSocioFirmado) )			
	
	//echo "<br /><br />4 validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor:resValidarCamposAltaSocioPorGestor: "; print_r($resValidarCamposAltaSocioPorGestor);
			
	return  $resValidarCamposAltaSocioPorGestor;				
}	
?>