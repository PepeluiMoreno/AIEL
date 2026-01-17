<?php
/*-------------------------------------------------------------------------------------- 
FICHERO: validarCamposEmailAdjuntosSociosPres.php
VERSION: PHP 7.3.21

DESCRIPCION: Valida los campos para enviar email a socios desde el rol de un gestor
             por presidente, vice, secretario
													
             Se validan los campos recibidos desde el formulario: 
													-[camposEmail]: [FROM]=>presidencia@europalaica.org, ...,[CC],[subject],
													                       [body],[pieProteccionDatos] 
													-[datosSelecionEmailSocios]: agrupación, país domicilio, CCAA, y provincia													
             -[AddAttachment]: Array ( [FICHERO1],[FICHERO2],[FICHERO3] )													

RECIBE: array "$datosCamposEmailForm" con datos procedentes de formulario:$_POST y $_FILES
DEVUELVE: array "$arrValidarEnvioEmailSocios" con campos validados de: 
         [camposEmail] , [datosSelecionEmailSocios] y [AddAttachment], y códigos de error.

LLAMADO: cPresidente:enviarEmailSociosPres(), GestorSimps:enviarEmailSimpsGes 
         y NO cCoordinador: enviarEmailSociosCoord()

LLAMA: modelos/libs/validarCamposEnviarEmail.php:validarCamposEnviarEmail() 
modelos/libs/validarCamposEnviarEmailSeleccionSociosPres.php:validarCamposEnviarEmailSeleccionSociosPres()       
modeloArchivos.php:arrMimeExtArchAdjuntosPermitidas() y validarEnviarArchivosAdjuntos() 

OBSERVACIONES: Probada PHP 7.3.21
Parecida a validarCamposEmailAdjuntosSociosCoord.php 			
---------------------------------------------------------------------------------------*/
function validarCamposEmailAdjuntosSociosPres($datosCamposEmailForm)//o "$datosCamposEmailForm", $arrDatosCamposEmailForm, $arrCamposEmailForm
{
	//echo "<br><br>0-1 validarCamposEmailAdjuntosSociosPres:datosCamposEmailForm: ";print_r($datosCamposEmailForm); 	

	$arrValidarEnvioEmailSocios['codError'] = '00000';
 $arrValidarEnvioEmailSocios['errorMensaje'] = '';
	$arrValidarEnvioEmailSocios['totalErrores'] = 0;
 
	//---------------------------- validarCamposEnviarEmail -------------------------------

	require_once './modelos/libs/validarCamposEnviarEmail.php';
	$arrValidarEnvioEmailSocios['camposEmail'] = validarCamposEnviarEmail($datosCamposEmailForm['camposEmail']);
	
	//echo "<br><br>1-1 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios: ";print_r($arrValidarEnvioEmailSocios); 	

	if ($arrValidarEnvioEmailSocios['camposEmail']['codError'] !== '00000')
 { 	
	  $arrValidarEnvioEmailSocios['codError'] = '81000';
	  $arrValidarEnvioEmailSocios['errorMensaje'] .= $arrValidarEnvioEmailSocios['camposEmail']['errorMensaje'];					
			$arrValidarEnvioEmailSocios['totalErrores'] += $arrValidarEnvioEmailSocios['camposEmail']['totalErrores'];		
	}
 //echo "<br /><br />1-2 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios:";print_r($arrValidarEnvioEmailSocios); 
	
	//-------- -------  validarCamposEnviarEmailSeleccionSociosPres -----------------------
	require_once './modelos/libs/validarCamposEnviarEmailSeleccionSociosPres.php';
	$arrValidarEnvioEmailSocios['datosSelecionEmailSocios'] = validarCamposEnviarEmailSeleccionSociosPres($datosCamposEmailForm['datosSelecionEmailSocios']);	
	
	//echo "<br><br>2-1 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios: ";print_r($arrValidarEnvioEmailSocios );
	
 if ($arrValidarEnvioEmailSocios['datosSelecionEmailSocios']['codError'] !== '00000')
 { 	
	  $arrValidarEnvioEmailSocios['codError'] = '81000';		
   $arrValidarEnvioEmailSocios['errorMensaje'] .= $arrValidarEnvioEmailSocios['datosSelecionEmailSocios']['errorMensaje'];					
			$arrValidarEnvioEmailSocios['totalErrores']++; 
	}
	
	//echo "<br><br>2-2 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios: ";print_r($arrValidarEnvioEmailSocios); 
	
	
 //---------------------------- validarEnviarArchivosAdjuntos -------------------------------
 
	/* [cadExtPermitidas], es un string con las extensiones permitidas para los archivos a subir con firma del socio, se obtiene a 
		partir del array "arrMimeExtArchivoFirmasPermitidas()", esto podría incluirse en "controladores/libs/inicializaCamposAltaSocioGestor.php"
		o ponerlo directamente en el formulario (['cadExtPermitidas'] = "doc,docx,odt,odi,gif,jpg,jpeg,pdf").
	*/	
	require_once './modelos/modeloArchivos.php'; 
	$arrMimeExtArchAdjuntosPermitidas = arrMimeExtArchAdjuntosPermitidas();//en modeloArchivos.php			

 //$tamanioMax = 2097152;//2097152 BYTES = 2MB
	$tamanioMax = 4194304;//4.194.304 bytes = 4MB	
	
	$arrValidarEnvioEmailSocios['AddAttachment'] = validarEnviarArchivosAdjuntos($datosCamposEmailForm['AddAttachment'],$arrMimeExtArchAdjuntosPermitidas,$tamanioMax);
	
 //echo "<br /><br />3 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios: ";print_r($arrValidarEnvioEmailSocios);
	
	if ($arrValidarEnvioEmailSocios['AddAttachment']['codError'] !== '00000')
 { $arrValidarEnvioEmailSocios['codError'] ='82000';
	  $arrValidarEnvioEmailSocios['errorMensaje'] .= $arrValidarEnvioEmailSocios['AddAttachment']['errorMensaje'];
			$arrValidarEnvioEmailSocios['totalErrores']++;		
	}		
	
	//echo "<br /><br />4 validarCamposEmailAdjuntosSociosPres:arrValidarEnvioEmailSocios: "; print_r($arrValidarEnvioEmailSocios); 
			
	return 	$arrValidarEnvioEmailSocios;				
}	
?>