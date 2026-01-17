<?php
/*----------------------------------------------------------------------------------------------
FICHERO: validarCamposEnviarEmail.php
VERSION: PHP 7.3.19

DESCRIPCION: Valida ciertos campos recibidos desde el formulario, para enviar email a socios
             por presidente, coordinador, ...
													Se validan los campos "FROM,BCC,subject,body", y aquí no se validadn archivos adjuntos
													que se validan en modeloArchivos.php:validarEnviarArchivosAdjuntos.php
													
RECIBE: array "$datosCamposEmailForm"=$datosCamposEmailForm['camposEmail']=FROM,CC,subject,body 
DEVUELVE: array "$resValidarCamposForm" con campos validados y códigos de error.
									
LLAMADA: cPresidente:enviarEmailSociosPres():  mediante validarCamposEmailAdjuntosSociosPres.php 
         cCoordinador:enviarEmailSociosCoord():mediante validarCamposEmailAdjuntosSociosCoord.php
LLAMA: validarCamposEnviarEmail.php:validarCamposFormEnviarEmail()				
									
OBSERVACIONES: Probada PHP 7.3.21 
---------------------------------------------------------------------------------------------*/
require_once './modelos/libs/validarCampos.php'; 

function validarCamposEnviarEmail($datosCamposEmailForm)//recibe $datosCamposEmailForm=$datosCamposEmailForm['camposEmail']
{
	//echo "<br><br>0-1 validarCamposEnviarEmail.php:validarCamposEnviarEmail:datosCamposEmailForm: ";print_r($datosCamposEmailForm);

 $resValidarCamposForm = validarCamposFormEnviarEmail($datosCamposEmailForm);

	$validarErrorLogico['codError'] ='00000';
	$validarErrorLogico['errorMensaje'] ='';
	$totalErroresLogicos = 0;		

 foreach ($resValidarCamposForm as $nomCampo => $valNomCampo)		
	{	
  if ($resValidarCamposForm[$nomCampo]['codError'] !== '00000')
  {					
    $validarErrorLogico['codError']=$resValidarCamposForm[$nomCampo]['codError'];
		  $validarErrorLogico['errorMensaje'].=". ".$resValidarCamposForm[$nomCampo]['errorMensaje']; 
		  $totalErroresLogicos +=1;
	 }		
 }	
		 
	if ($totalErroresLogicos == 0)
	{$resValidarCamposForm['totalErrores']=0;
  $resValidarCamposForm['codError']='00000';
  $resValidarCamposForm['errorMensaje']='';		
	}	
	else//if ($totalErroresLogicos !==0)
	{$resValidarCamposForm['codError'] ='81000';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje'] =$validarErrorLogico['errorMensaje'];
		$resValidarCamposForm['totalErrores'] =$totalErroresLogicos;	
	} 
 //echo "<br><br>2 validarCamposEnviarEmail:resValidarCamposForm: ";print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
/*---------------- Fin validarCamposEnviarEmail ---------------------------------------------*/

/*---------------- Inicio validarCamposFormEnviarEmail ----------------------------------------
DESCRIPCION: Valida los campos individuales "FROM,BCC,subject,body", del formulario para enviar 
             email a socios
     
LLAMADA: validarCamposEnviarEmail.php:validarCamposEnviarEmail()
LLAMA:  modelos/libs/validarCampos.php (varias funciones)

OBSERVACIONES: Para el campo "CODAGRUPACION" se podría validar aquí si no existe o esta vacío,
Pero actualmente para el caso de envío de email desde Coordinador se hace una validación más 
específica en la función modeloPresCoord.php:buscarSeleccionEmailSociosCoord() que se llama 
desde cCoordinador.php, 
y para el caso del Presidente: modeloPresCoord.php:buscarSeleccionEmailSociosPres() que se llama 
desde cPresidente.php, 

NOTA: Actualmente la funcion validarTextoBodyEmailGes() ya no filtra caracteres, y solo dejo 
que controle longitud mínma y máxima de caracteres. 
Son enviados desde GESTORES de EL, es gente de confianza y no intentarán hacer
injections, pero he comprobado que les crea incomodidad al enviar emails.
----------------------------------------------------------------------------------------------*/
function validarCamposFormEnviarEmail($arrFormEmail)//$arrFormEmail = $datosEmail
{
	//echo "<br><br>0-1 validarCamposFormEnviarEmail.php:arrFormEmail: ";print_r($arrFormEmail);

	if (isset($arrFormEmail['FROM']) && !empty($arrFormEmail['FROM']))
	{ $resulValidar['FROM']['valorCampo'] = $arrFormEmail['FROM'];	
			$resulValidar['FROM']['codError'] = '00000';
	}
	else
	{ $resulValidar['FROM']['codError'] = '80201';
		 $resulValidar['FROM']['errorMensaje'] = "Remitente: debes elegir una opción";	
		 $resulValidar['FROM']['valorCampo'] = '';
	}
	
	if (isset($arrFormEmail['BCC']) && !empty($arrFormEmail['BCC']))
	{ $resulValidar['BCC'] = validarEmail($arrFormEmail['BCC'],"BCC: Copia oculta del email");	
	}
	else
	{ $resulValidar['BCC']['codError'] = '80201';
		 $resulValidar['BCC']['errorMensaje'] = "Debes elegir una dirección de email para recibir una copia oculta del email";	
		 $resulValidar['BCC']['valorCampo'] = '';
	}	
		
 //antes filtraba con	$resulValidar['subject'] = validarCampoTexto($arrFormEmail['subject'],1,120,'Asunto: ');
	
	$resulValidar['subject'] = validarTextoBodyEmailGes($arrFormEmail['subject'],1,120,'Asunto: ');
	//echo "<br><br>1-1 validarCamposFormEnviarEmail:resulValidar['subject']: ";print_r($resulValidar['subject']);

 //a fecha 2021_10_19 validarTextoBodyEmailGes() permite todos los caracteres, excepto validación tamaño.	
	
	$resulValidar['body'] = validarTextoBodyEmailGes($arrFormEmail['body'],2,8000,'Contenido: ');//no admite que este vacío	
	//echo "<br><br>1-2 validarCamposFormEnviarEmail:resulValidar['body']: ";print_r($resulValidar['body']);
	
 if (isset($arrFormEmail['pieProteccionDatos']) && !empty($arrFormEmail['pieProteccionDatos']))		
	{	$resulValidar['pieProteccionDatos']['valorCampo'] = $arrFormEmail['pieProteccionDatos'];	
   $resulValidar['pieProteccionDatos']['codError'] = '00000';
   $resulValidar['pieProteccionDatos']['errorMensaje'] = "";
	}
		
	//echo "<br><br>2-2 validarCamposFormEnviarEmail:resulValidar: ";print_r($resulValidar);
	
 return $resulValidar;
}
/*----------------------------- Fin validarCamposFormEnviarEmail -----------------------------*/
?>