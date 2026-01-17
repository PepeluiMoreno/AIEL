<?php
/*-----------------------------------------------------------------------------
FICHERO:validarCamposSimp.php
PROYECTO: Europa Laica
VERSION: PHP 5.2.3
DESCRIPCION: Valida los campos recibidos desde los formularios de Simpatizantes
Llamado: desde  controladorSimpatizantes
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales)
------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 
/*---------------- Inicio validarCamposAltaSimp() ---------------------------
DESCRIPCION: Valida los campos de alta de simpatizante, y comprueba existencia
						 en tablas USUARIO y email repetidos
Llamado: desde "controladorSimpatizantes.php", altaSimpatizante()
Llama funciones: validarCamposFormAltaSimp()
                 modeloUsuarios.php:buscarUsuario(),buscarEmail()
------------------------------------------------------------------------------*/
function validarCamposAltaSimp($camposFormRegSimp)
{//echo "<br><br>1validarCamposSimp:validarCamposAltaSimp:camposFormRegSimp";print_r($camposFormRegSimp);
 $resValidarCamposForm=validarCamposFormAltaSimp($camposFormRegSimp);
	//echo "<br><br>2validarCamposSimp:validarCamposAltaSimp:resValidarCamposForm";print_r($resValidarCamposForm);

	if ($resValidarCamposForm['datosFormUsuario']['USUARIO']['codError']=='00000')
	{$resBuscarUsuario=buscarUsuario($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']);

	 if ($resBuscarUsuario['codError']!=='00000') //error sistema <80000
	 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = $resBuscarUsuario['codError'];
	  $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']=$resBuscarUsuario['errorMensaje'];
  }
	 else //$resBuscarUsuario['codError']=='00000'
	 { if ($resBuscarUsuario['numFilas']!==0)
	   {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = '80002';
		  $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']='Ese usuario ya existe, elija otro nombre';	
	 	 }			 
	   else //$resBuscarUsuario['numFilas']==0)
			 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError']='00000';//no es necesario
    }
		 } //$resBuscarUsuario['codError']=='00000'
	 }

  if ($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] =='00000')
	 { $resBuscarEmail=buscarEmail($resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']);
		 
		 if ($resBuscarEmail['codError']!=='00000')//error sistema <80000
		 {$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = $resBuscarEmail['codError'];
		  $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']=$resBuscarEmail['errorMensaje'];
			
		 }
		 else //$resBuscarEmail['codError']=='00000'
		 {if ($resBuscarEmail['numFilas']!==0)
		  {$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '80002';
			  $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']='Ese Email ya existe, elija otro';	
			 }			 
		  else //$resBuscarEmail['numFilas']==0)
			 {	$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '00000';//no es necesario	 
    }
		 }	
	 }

	$validarErrorSistema['codError']='00000';
	$validarErrorLogico['codError']='00000';
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
	//echo "<br><br>3validarCamposSimp:validarCamposAltaSimp:resValidarCamposForm:";	print_r($resValidarCamposForm);
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposAltaSimp ----------------------------------

/*---------------- Inicio validarCamposFormAltaSimp ---------------------------
DESCRIPCION:Valida los campos de formulario de alta de Simp formRegistrarSimp.php           
Llamado: desde "validarCamposSimp.php", validarCamposAltaSimp()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------*/
function validarCamposFormAltaSimp($arrCamposForm)
{ //echo "<br><br>1validarCamposSimp:validarCamposFormAltasSimp:arrCamposForm:";print_r($arrCamposForm);

 /*----------------------------- Inicio Validar datosFormDomicilio -------------------------------------------------*/
	$resulValidar['datosFormDomicilio']=validarDomSimp($arrCamposForm['datosFormDomicilio']);
	
	//echo "<br><br>1-4validarCamposSimp:validarCamposFormAltasSimp:resulValidar['datosFormDomicilio']:"; 
	//print_r($resulValidar['datosFormDomicilio']);
	/*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/
	
 /*-------------------------------------- Inicio Validar  datosFormUsuario --------------------------------------------*/	
	$resulValidar['datosFormUsuario']['USUARIO']=validarCampoUsuario($arrCamposForm['datosFormUsuario']['USUARIO'],6,30,"");

	$resulValidar['datosFormUsuario']['PASSUSUARIO']=validarCampoPass($arrCamposForm['datosFormUsuario']['PASSUSUARIO'],6,30,"" );	
 $resulValidar['datosFormUsuario']['RPASSUSUARIO']=validarCampoPass($arrCamposForm['datosFormUsuario']['RPASSUSUARIO'],6,30,"" );
	if ($arrCamposForm['datosFormUsuario']['PASSUSUARIO']!==$arrCamposForm['datosFormUsuario']['RPASSUSUARIO'])
	{$resulValidar['datosFormUsuario']['PASSUSUARIO']['codError']='80430';
		$resulValidar['datosFormUsuario']['PASSUSUARIO']['errorMensaje']='Las dos contraseñas no eran iguales';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError']='80430';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']='Las dos contraseñas no eran iguales';
	}
	if ($arrCamposForm['datosFormUsuario']['privacidad']!=='SI')
	{$resulValidar['datosFormUsuario']['privacidad']['valorCampo']='NO';
		$resulValidar['datosFormUsuario']['privacidad']['codError']='80200';	
		$resulValidar['datosFormUsuario']['privacidad']['errorMensaje']='debes aceptar la política de privacidad para guardar el 
		formulario';	
	}	
	else
	{	$resulValidar['datosFormUsuario']['privacidad']['valorCampo']='SI';
		$resulValidar['datosFormUsuario']['privacidad']['codError']='00000';	
		$resulValidar['datosFormUsuario']['privacidad']['errorMensaje']='';
	}	
	/*--------------------------------------------- Fin Validar  datosFormUsuario -------------------------------------------*/
	
 /*----------------------------------- Inicio datosFormMiembro -----------------------------------------------*/

	$resulValidar['datosFormMiembro']['SEXO']=validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],"");
	//$resulValidar['datosFormMiembro']['SEXO']['valorCampo']=$arrCamposForm['datosFormMiembro']['SEXO'];
	//$resulValidar['datosFormMiembro']['SEXO']['codError']='00000';		
	
	$resulValidar['datosFormMiembro']['NOM']=validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");	
	if (isset($arrCamposForm['datosFormMiembro']['APE1']) && $arrCamposForm['datosFormMiembro']['APE1'] !=='')
	{$resulValidar['datosFormMiembro']['APE1']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");};
		
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) && $arrCamposForm['datosFormMiembro']['APE2'] !=='')
	{$resulValidar['datosFormMiembro']['APE2']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],1,100, "");};

 $resulValidar['datosFormMiembro']['FECHANAC']=validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);

	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM']=='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}		
	
	$resulValidar['datosFormMiembro']['EMAIL']=validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		
	$resulValidar['datosFormMiembro']['REMAIL']=validarEmail($arrCamposForm['datosFormMiembro']['REMAIL'],"");	
	if ($arrCamposForm['datosFormMiembro']['EMAIL']!==$arrCamposForm['datosFormMiembro']['REMAIL'])
	{$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '80430';
		$resulValidar['datosFormMiembro']['EMAIL']['errorMensaje']='Los dos email son diferentes';
		$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '80430';
		$resulValidar['datosFormMiembro']['REMAIL']['errorMensaje']='Las dos email son diferentes';
	}	
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=$arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS'];
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=$arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'];
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
 //considero que al darse de alta un usuario, despues de validado el formato, el email será correcto
	//El presidente, coordinador, secretario, podrían anotorlo como devuelto, en caso de que eso suceda 
 $resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo']='NO';
	$resulValidar['datosFormMiembro']['EMAILERROR']['codError']='00000';	
	
	$resulValidar['datosFormMiembro']['PROFESION']['valorCampo']=$arrCamposForm['datosFormMiembro']['PROFESION'];
	$resulValidar['datosFormMiembro']['PROFESION']['codError']='00000';
	
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo']=$arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError']='00000';	
		
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo']=$arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError']='00000';	
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']=$arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'];
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError']='00000';		
  /*------------------------------------------ Fin datosFormMiembro ------------------------------------------------------*/

	//echo "<br><br>2validarCamposSimp:validarCamposFormAltasSimp:resulValidar:"; print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormAltaSocio ---------------------


/*---------------- Inicio validarCamposActualizarSimp() ---------------------------
DESCRIPCION: Valida los campos de actualizar simpatizante, y comprueba existencia
						 en tablas de nuevo usuario, email, doc
Llamado: desde "controladorSimpatizantes.php", Simpatizante())
Llama funciones: validarCamposFormActualizarSimp()
                 modeloUsuarios.php:buscarUsuario(),buscarEmail(), buscarNumDoc()
------------------------------------------------------------------------------*/
function validarCamposActualizarSimp($camposFormRegSimp)
{//echo "<br><br>1-0_0 actualizarSimp:validarCamposActualizarSimp:camposFormRegSimp";print_r($camposFormRegSimp);
 $resValidarCamposForm=validarCamposFormActualizarSimp($camposFormRegSimp);
	//echo "<br><br>1-0_1 actualizarSimp:validarCamposActualizarSimp:resValidarCamposForm";print_r($resValidarCamposForm);
	//echo "<br><br>1-0_2 actualizarSimp:validarCamposActualizarSimp:_SESSION['vs_USUARIO']";print_r($_SESSION['vs_USUARIO']);

	if (($resValidarCamposForm['datosFormUsuario']['USUARIO']['codError']=='00000') &&
	    ($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']!==$_SESSION['vs_USUARIO']))
	{	 $resBuscarUsuario=buscarUsuario($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']);

		 if ($resBuscarUsuario['codError']!=='00000') //error sistema <80000
		 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = $resBuscarUsuario['codError'];
		  $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']=$resBuscarUsuario['errorMensaje'];
	  }
		 else //$resBuscarUsuario['codError']=='00000'
		 {if ($resBuscarUsuario['numFilas']!==0)
		  {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = '80002';
			  $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']='Ese usuario ya existe, elija otro nombre';	
		 	}			 
		  else //$resBuscarUsuario['numFilas']==0)
			 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError']='00000';//no es necesario
    }
		 } //$resBuscarUsuario['codError']=='00000'
	 }			

  if (($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] =='00000') &&
	  $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']!==$_SESSION['vs_EMAIL'])
	 { $resBuscarEmail=buscarEmail($resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']);
		 
		 if ($resBuscarEmail['codError']!=='00000')//error sistema <80000
		 {$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = $resBuscarEmail['codError'];
		  $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']=$resBuscarEmail['errorMensaje'];
			
		 }
		 else //$resBuscarEmail['codError']=='00000'
		 {if ($resBuscarEmail['numFilas']!==0)
		  {	$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '80002';
			  $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']='Ese Email ya existe, elija otro';	
			 }			 
		  else //$resBuscarEmail['numFilas']==0)
			 {	$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '00000';//no es necesario	 
    }
		 }	
	 }		
 
	//echo "<br><br>1-3-0actualizarSimp:validarCamposActualizarSimp:SESSION:",$_SESSION['vs_CODPAISDOC'],
	//$_SESSION['vs_TIPODOCUMENTOMIEMBRO'],$_SESSION['vs_NUMDOCUMENTOMIEMBRO'];

	$validarErrorSistema['codError']='00000';
	$validarErrorLogico['codError']='00000';
	$totalErroresSistema = 0;
	$totalErroresLogicos = 0;
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
		  { if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
				{ $validarErrorSistema['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
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
	{ $resValidarCamposForm['totalErrores']=0;
	  $resValidarCamposForm['codError']='00000';
	  $resValidarCamposForm['errorMensaje']='';		
	}	
	elseif ($totalErroresSistema !==0)
	{ $resValidarCamposForm['codError']=$validarErrorSistema['codError'];//será el código del primer error del sistema
	  $resValidarCamposForm['errorMensaje']=$validarErrorSistema['errorMensaje'];//será el del primer error del sistema	
		$resValidarCamposForm['totalErrores']=$totalErroresSistema;	
	  $resValidarCamposForm['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo ";
	  $resValidarCamposForm['arrMensaje']['textoBoton']='Salir de la aplicación';
	  $resValidarCamposForm['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	} 
	else//if ($totalErroresLogicos !==0)
	{ $resValidarCamposForm['codError']='80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje']=$validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCamposForm['totalErrores']=$totalErroresLogicos;	
	} 
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposActualizarSimp ----------------------------------


/*---------------- Inicio validarCamposFormActualizarSimp ---------------------------
DESCRIPCION:Valida los campos de formulario de actualizarsocio formActualizarSimp.php           
Llamado desde: validarCamposSimp.php:validarCamposActualizarSimp()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------*/
function validarCamposFormActualizarSimp($arrCamposForm)
{	/*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/
	
	//echo "<br><br>validarCamposSimp:validarCamposFormActualizarSimp1-1:resulValidar['datosFormDomicilio']:"; 
 
	/*----------------------------- Inicio Validar datosFormDomicilio -------------------------------------------------*/
	$resulValidar['datosFormDomicilio']=validarDomSimp($arrCamposForm['datosFormDomicilio']);
	
	//echo "<br><br>1-4validarCamposSimp:validarCamposFormAltasSimp:resulValidar['datosFormDomicilio']:"; 
	//print_r($resulValidar['datosFormDomicilio']);
  /*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/

  /*-------------------------------------- Inicio Validar  datosFormUsuario ----------------------------------------------*/	
	$resulValidar['datosFormUsuario']['USUARIO']=validarCampoUsuario($arrCamposForm['datosFormUsuario']['USUARIO'],6,30,"");
	//echo "<br><br>datosFormUsuario:validar 1 USUARIO: ";print_r($resulValidar['datosFormUsuario']['USUARIO']);	
	
	$resulValidar['datosFormUsuario']['CODUSER']['valorCampo']=$arrCamposForm['datosFormUsuario']['CODUSER'];
	$resulValidar['datosFormUsuario']['CODUSER']['codError']='00000';		

	/*--------------------------------------------- Fin Validar  datosFormUsuario -------------------------------------------*/
  /*----------------------------------- Inicio datosFormPrivacidad ---------------------------------------------*/
	if ($arrCamposForm['datosFormPrivacidad']['privacidad']!=='SI')
	{$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo']='NO';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError']='80200';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje']='debes aceptar la política de privacidad para guardar el 
		formulario';	
	}	
	else
	{	$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo']='SI';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError']='00000';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje']='';
	}		
	/*----------------------------------- Fin datosFormPrivacidad ---------------------------------------------*/
 /*----------------------------------- Inicio datosFormMiembro -----------------------------------------------*/
	/*------------------------ Inicio Validar documento NIF, NIE, Pasaporte ------------------------------*/
 /*if ( //$arrCamposForm['datosFormMiembro']['CODPAISDOC'] !=='' ||
	     //$arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'] !=='' || 
	     (!isset($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']) || 
						  $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] == NULL ||
								$arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] ==''
						)		
				)
	{
		//$resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo']=$arrCamposForm['datosFormMiembro']['CODPAISDOC'];
		$resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo'] = NULL;
		$resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='00000';
		//$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']=
		//                                           $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
  $resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = NULL;		
		$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError']='00000';
		//$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']=
		//                                           $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];
		$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']= NULL;	
		$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError']='00000';																																													
	}
	else			
	{
	 $resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo']=$arrCamposForm['datosFormMiembro']['CODPAISDOC'];
		$resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='00000';
		$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']=
		                                           $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
		$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError']='00000';	
	 if ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIF')
	 { $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']=
		                                           validarNif($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
	 } 
		elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIE')	
		{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']=
		                                           validarNie($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
		}	
	 else //'Pasaporte')  	
		{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']=
			                         validarNumPasaporte($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'],1,100,"");		
		}
	}
	*/
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/

	$resulValidar['datosFormMiembro']['SEXO']=validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],"");
	//$resulValidar['datosFormMiembro']['SEXO']['valorCampo']=$arrCamposForm['datosFormMiembro']['SEXO'];
	//$resulValidar['datosFormMiembro']['SEXO']['codError']='00000';		
	
	$resulValidar['datosFormMiembro']['NOM']=validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");
	if (isset($arrCamposForm['datosFormMiembro']['APE1']) && $arrCamposForm['datosFormMiembro']['APE1'] !=='')
	{$resulValidar['datosFormMiembro']['APE1']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");};
	
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) && $arrCamposForm['datosFormMiembro']['APE2'] !=='')
	{$resulValidar['datosFormMiembro']['APE2']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],1,100, "");};

 $resulValidar['datosFormMiembro']['FECHANAC']=validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);
	
	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM']=='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}	

	$resulValidar['datosFormMiembro']['EMAIL']=validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		

	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=$arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS'];
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=$arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'];
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
	$resulValidar['datosFormMiembro']['PROFESION']['valorCampo']=$arrCamposForm['datosFormMiembro']['PROFESION'];
	$resulValidar['datosFormMiembro']['PROFESION']['codError']='00000';

	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo']=$arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError']='00000';	
	
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo']=$arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError']='00000';	
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']=$arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'];
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError']='00000';		
  /*------------------------------------------ Fin datosFormMiembro ------------------------------------------------------*/		
	//echo "<br><br>validarCamposSimp:validarCamposFormActualizarSimp:resulValidar:";print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormActualizarSimp -----------------
?>