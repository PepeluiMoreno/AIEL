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
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 

/*---------------- Inicio validarCamposAltaSocio() ------------------------------------
DESCRIPCION: Valida los campos de alta de socio, y comprueba existencia
						 en tablas de EMAIL, NUMDOCUMENTOMIEMBRO repetidos
Llamado: desde "cAdmin.php:altaSocioPorAdmin(), altaSocioPorGestor
Llama funciones: validarCamposFormAltaSocioPorGestor()
                 modeloUsuarios.php:buscarNumDoc(),buscarEmail()
------------------------------------------------------------------------------*/
function validarCamposEnviarEmail($datosEmail)
{echo "<br><br>1 validarEnviarEmail.php:validarCamposEnviarEmail:datosEmail:";print_r($datosEmail);

 $resValidarCamposForm = validarCamposFormEnviarEmail($datosEmail);
	echo "<br><br>2 validarEnviarEmail.php:validarCamposEnviarEmail:resValidarCamposForm:";print_r($resValidarCamposForm);

	$validarErrorLogico['codError']='00000';
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
	{$resValidarCamposForm['codError']='80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje']=$validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCamposForm['totalErrores']=$totalErroresLogicos;	
	} 
 echo "<br><br>3 validarEnviarEmail.php:validarCamposEnviarEmail:resValidarCamposForm:";print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposAltaSocioPorGestor ----------------------------------

/*---------------- Inicio validarCamposFormAltaSocioPorGestor ---------------------------
DESCRIPCION:Valida los campos de formulario de alta de socio formAltaSocioPorAdmin.php           
Llamado: desde "validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------*/
function validarCamposFormEnviarEmail($arrFormEmail)
{echo "<br><br>1 validarEnviarEmail.php:validarCamposFormEnviarEmail:arrCamposForm:";print_r($arrForm);

	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/
 if (isset($arrFormEmail['CC']) && !empty($arrFormEmail['CC']))		
	{		$resulValidar['CC'] = validarEmail($arrFormEmail['CC'],"Copia del email");	
	}
	
	$resulValidar['subject'] = validarCampoNoVacio($arrFormEmail['subject'],'Asunto: ');
	$resulValidar['body'] = validarTextArea($arrFormEmail['body'],2,8000,'Contenido: ');//no admite que este vacío
		
	//$resulValidar['datosFormMiembro']['PROFESION']['valorCampo']=$arrCamposForm['datosFormMiembro']['PROFESION'];
	//$resulValidar['datosFormMiembro']['PROFESION']['codError']='00000';

 /*------------------------------------------ Fin datosFormMiembro ------------------------------------------------------*/
	echo "<br><br>2 validarEnviarEmail.php:validarCamposFormEnviarEmail:resulValidar:";print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormAltaSocioPorGestor ---------------------
?>