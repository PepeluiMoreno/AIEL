<?php
/*-----------------------------------------------------------------------------
FICHERO: validarEnviarEmail.php
PROYECTO: Europa Laica
VERSION: PHP 5.2.3
DESCRIPCION: Valida los campos recibidos desde los formularios de 
             formAltaSocioPorAdmin.php y formAltaSocioPorGestor.php
Llamado: desde  cAdmin:altaSocioPorAdmin y altaSocioPorGestor()
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales)
------------------------------------------------------------------------------*/
function validarArchivosAnexados($datosEmail)
{echo "<br /><br />---datosEmail: ";print_r($datosEmail);
	$extPermitidas = array("application/vnd.ms-excel", "application/pdf", "application/msword", "text/plain",
	                       "image/bmp","image/gif","image/jpeg","image/pjpeg","image/png","image/x-png");
	
	$numFich = 0;		
	
	foreach ($datosEmail['AddAttachment'] as $fichero => $datosArchivo)
	{ echo "<br /><br />---datosArchivo: ";print_r($datosArchivo);
	 if (isset($datosArchivo["name"]) && !empty($datosArchivo["name"]))//tipo no permitido 
		{
			if (!in_array($datosArchivo["type"], $extPermitidas))//tipo no permitido: if ($datosArchivo["type"]!=="image/gif")...)
			{$datosArchivo["codError"]='82010';
				$datosArchivo['errorMensaje']= 'ERROR en anexar archivos'. $datosArchivo["type"].
						' es un tipo archivo no permitido (solo se aceptan: pdf,doc,txt,xls,gif,jpg,jpeg,bmp,png.)';
			}
			elseif (($datosArchivo["size"] >= 10000000))//EN BYTES
			{$datosArchivo["codError"]='82020';// "1","2"
				$datosArchivo['errorMensaje']= 'ERROR:Demasiado grande '.$datosArchivo["size"].' (máx 20K). Codigo error: '.$datosArchivo["error"];
			}
			/*
			elseif ($datosArchivo["error"] = 3)
			{ $datosArchivo["codError"]='82040';//"3"		
					$datosArchivo['errorMensaje']= 'ERROR: Envío de archivo suspendido durante la transferencia';
			}
			*/
			elseif ($datosArchivo["error"] > 0)
	  { $datosArchivo["codError"]='82000';//"3"		
					$datosArchivo['errorMensaje']= 'ERROR en anexar archivos';
		 }
		 else
			{ echo "<br />Upload: " . $datosArchivo["name"] . "<br />";
			   echo "<br />Type: " . $datosArchivo["type"] . "<br />";
			   echo "<br />Size: " . ($datosArchivo["size"] / 1024) . " Kb<br />";
			   echo "<br />Stored in: " . $datosArchivo["tmp_name"];
			}	
			
			 $datosEmailAux[$numFich]=$datosArchivo;
				$numFich++;
		}	
	}
	//necesitará un bucle igual a validar para controlar si ha habido error
	
	$datosEnvioEmail['AddAttachment'] = $datosEmailAux;
	echo "<br /><br />---validarEnviarEmailSocios:datosEnvioEmail: ";print_r($datosEnvioEmail);
	
	return $datosEnvioEmail;			
}
?>