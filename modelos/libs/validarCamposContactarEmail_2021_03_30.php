<?php
/*-----------------------------------------------------------------------------
FICHERO:validarCamposContactarEmail.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: Valida los campos recibidos desde el formulario de contactarEmail
          
LLAMADA: cEnlacesPie.php:contactarEmail()
LLAMA: modelos/libs/validarCampos.php (una librería de validaciones generales)
------------------------------------------------------------------------------*/

require_once './modelos/libs/validarCampos.php'; 
/*---------------- Inicio validarCamposContactarEmail() -----------------------
DESCRIPCION: Valida los campos de ContactarEmail
Llamado: desde "cEnlacesPie.php", contactarEmail()
Llama funciones: validarCamposContactarEmail()
                 
------------------------------------------------------------------------------*/
function validarCamposContactarEmail($camposFormContactarEmail)
{
	//echo "<br><br>0-1 validarCamposContactarEmail.php:validarCamposContactarEmail:camposFormContactarEmail: ";print_r($camposFormContactarEmail);
 $resValidarCamposForm=validarCamposFormContactarEmail($camposFormContactarEmail);
	//echo "<br><br>1 validarCamposContactarEmail.php:validarCamposContactarEmail:resValidarCamposForm: ";print_r($resValidarCamposForm);

	$validarErrorSistema['codError'] = '00000';
	$validarErrorLogico['codError'] = '00000';
	$validarErrorLogico['errorMensaje'] = '';
	$totalErroresSistema = 0;
	$totalErroresLogicos = 0;
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
		  { if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
				  {$validarErrorSistema['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
				   $validarErrorSistema['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
				   $totalErroresSistema +=1; 
					  break 2; //
				  }
				  else //$resValidarCamposForm[$grupo][$nomCampo]['codError'] >= '80000'
						{ $validarErrorLogico['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
						  $validarErrorLogico['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
						  $totalErroresLogicos +=1;
						}
			}		
		}	
	}
	 
	if ($totalErroresSistema ==0 && $totalErroresLogicos == 0)
	{$resValidarCamposForm['totalErrores']=0;
	 $resValidarCamposForm['codError']='00000';
	 $resValidarCamposForm['errorMensaje']='';		
	}	
	elseif ($totalErroresSistema !==0)
	{$resValidarCamposForm['codError']=$validarErrorSistema['codError'];//será el código del primer error del sistema
  $resValidarCamposForm['errorMensaje']=$validarErrorSistema['errorMensaje'];//será el del primer error del sistema	
	 $resValidarCamposForm['totalErrores']=$totalErroresSistema;	
  $resValidarCamposForm['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo ";
  $resValidarCamposForm['arrMensaje']['textoBoton']='Salir de la aplicación';
  $resValidarCamposForm['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	} 
	else//if ($totalErroresLogicos !==0)
	{$resValidarCamposForm['codError']='80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje']=$validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCamposForm['totalErrores']=$totalErroresLogicos;	
	} 
	//echo "<br><br>2 validarCamposContactarEmail.php:validarCamposContactarEmail:resValidarCamposForm: ";	print_r($resValidarCamposForm);
	return $resValidarCamposForm; //incluye arrayMensaje
}		
/*---------------- Fin validarCamposContactarEmail ---------------------------*/

/*---------------- Inicio validarCamposFormContactarEmail ---------------------
DESCRIPCION: Valida los campos de formulario de  ContactarEmail           
LLAMADA: "validarCamposContactarEmail.php:validarCamposContactarEmail()
LLAMA:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------*/
function validarCamposFormContactarEmail($arrCamposForm)
{ //echo "<br><br>0-1 validarCamposContactarEmail.php::validarCamposFormContactarEmail:arrCamposForm: ";print_r($arrCamposForm);	
		
 /*----------------------------------- Inicio  -------------------------------*/
	
	$resulValidar['datosContactarEmail']['emailDestino']['valorCampo']=$arrCamposForm['datosContactarEmail']['emailDestino'];	
	$resulValidar['datosContactarEmail']['emailDestino']['codError'] = '00000';
	$resulValidar['datosContactarEmail']['nombreEmailDestino']['valorCampo']=$arrCamposForm['datosContactarEmail']['nombreEmailDestino'];
 $resulValidar['datosContactarEmail']['nombreEmailDestino']['codError'] = '00000';	
	
	$resulValidar['datosContactarEmail']['NOMBRE']=validarCampoNombres($arrCamposForm['datosContactarEmail']['NOMBRE'],3,200, "");
		
	$resulValidar['datosContactarEmail']['EMAIL']=validarEmail($arrCamposForm['datosContactarEmail']['EMAIL'],"");		
	$resulValidar['datosContactarEmail']['REMAIL']=validarEmail($arrCamposForm['datosContactarEmail']['REMAIL'],"");	
	
	if ($arrCamposForm['datosContactarEmail']['EMAIL']!== $arrCamposForm['datosContactarEmail']['REMAIL'])
	{$resulValidar['datosContactarEmail']['EMAIL']['codError'] = '80430';
		$resulValidar['datosContactarEmail']['EMAIL']['errorMensaje']='Los dos email son diferentes';
		$resulValidar['datosContactarEmail']['REMAIL']['codError'] = '80430';
		$resulValidar['datosContactarEmail']['REMAIL']['errorMensaje']='Las dos email son diferentes';
	}	

	$resulValidar['datosContactarEmail']['ASUNTO']=validarCampoTexto($arrCamposForm['datosContactarEmail']['ASUNTO'],3,100, "");
	if ($resulValidar['datosContactarEmail']['ASUNTO']['codError']=='00000')
	{
		$resulValidar['datosContactarEmail']['ASUNTO'] = 
		  validarInjectionDatosEmail($resulValidar['datosContactarEmail']['ASUNTO']['valorCampo']);		
	}
	
	$resulValidar['datosContactarEmail']['TEXTOMENSAJE']= validarTextArea($arrCamposForm['datosContactarEmail']['TEXTOMENSAJE'],10,200,'Error');
	if ($resulValidar['datosContactarEmail']['TEXTOMENSAJE']['codError']=='00000')
	{
		$resulValidar['datosContactarEmail']['TEXTOMENSAJE'] = validarInjectionDatosEmail($resulValidar['datosContactarEmail']['TEXTOMENSAJE']['valorCampo']);		
	}

	if (!isset($arrCamposForm['datosContactarEmail']['privacidad']) || empty($arrCamposForm['datosContactarEmail']['privacidad']) || $arrCamposForm['datosContactarEmail']['privacidad'] !=='SI')
	//if ($arrCamposForm['datosFormUsuario']['privacidad'] !=='SI')
	{$resulValidar['datosContactarEmail']['privacidad']['valorCampo']='NO';
		$resulValidar['datosContactarEmail']['privacidad']['codError']='80200';	
		$resulValidar['datosContactarEmail']['privacidad']['errorMensaje'] ='debes aceptar la política de privacidad para guardar el formulario';	
	}	
	else
	{$resulValidar['datosContactarEmail']['privacidad']['valorCampo']='SI';
		$resulValidar['datosContactarEmail']['privacidad']['codError']='00000';	
		$resulValidar['datosContactarEmail']['privacidad']['errorMensaje']='';
	}		
 /*----------------------------------- Fin  -----------------------------------*/

	//echo "<br><br>1 validarCamposContactarEmail.php:validarCamposFormContactarEmail:resulValidar: "; print_r($resulValidar);
	
 return $resulValidar;
 /*----------------------------- Fin validarCamposContactarEmail ---------------*/
}
?>