<?php
/*-----------------------------------------------------------------------------
FICHERO: validarCamposSocio.php 

VERSION: PHP 7.3.19
DESCRIPCION: Valida los campos recibidos desde los formularios de Socios
Llamado: desde  controladorSocios
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales)

Agustín 
2019-12-11: validarCamposFormAltaSocio(), validarCamposFormActualizarSocio(),
            validarFormEliminarSocio() 
2019_12_09: Algunas modificaciones de validar campos, entre otras validarIBAN(),
2018-1-21: Modifico en validarCamposFormAltaSocio() la parte de 
- Validar Agrupación -  en "formAltaSocio.php" ahora aparece "Elegir agrupación" 
antes era"	Europa Laica Estatal e Internacional" por defecto. 

------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 

/*---------------- Inicio validarCamposAltaSocio() -----------------------------
DESCRIPCION: Valida los campos de alta de socio, y comprueba existencia
						       en tablas de USUARIO,NUMDOCUMENTOMIEMBRO, EMAIL repetidos, 
LLAMADA: desde controladorSocios.php:altaSocio()
LLAMA: validarCamposFormAltaSocio()
                 modeloUsuarios.php:buscarUsuario(),buscarEMAIL()
------------------------------------------------------------------------------*/
function validarCamposAltaSocioSocio($camposFormRegSocio)
{
	if (!isset($camposFormRegSocio) || empty($camposFormRegSocio) )
	{ $resValidarCamposForm['codError'] = '70601';
			$resValidarCamposForm['errorMensaje'] = 'Alta Socio/a:validarCamposSocio.php:validarCamposAltaSocioSocio(). Faltan parámetros imprecindibles, codError:'.$resValidarCamposForm['codError'];
	}
 else// !(!isset($camposFormRegSocio) || empty($camposFormRegSocio) )
 {		
		//echo "<br><br>1 validarCamposSocio:validarCamposAltaSocio:camposFormRegSocio";print_r($camposFormRegSocio);
		$resValidarCamposForm = validarCamposFormAltaSocio($camposFormRegSocio);
		//echo "<br><br>2 validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm";print_r($resValidarCamposForm);

		if ($resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] =='00000')
		{$resBuscarUsuario = buscarUsuario($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']);

			if ($resBuscarUsuario['codError']!=='00000') //error sistema <80000
			{$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = $resBuscarUsuario['codError'];
				$resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje'] = $resBuscarUsuario['errorMensaje'];
			}
			else //$resBuscarUsuario['codError']=='00000'
			{if ($resBuscarUsuario['numFilas'] !==0)
				{$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = '80002';
				$resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']='Ese usuario/a ya existe, elija otro nombre';	
				}			 
				else //$resBuscarUsuario['numFilas']==0)
				{$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] ='00000';//no es necesario
				}
			} //$resBuscarUsuario['codError']=='00000'
		}
		//echo "<br><br>2-1validarCamposSocio:validarCamposAltaSocio:resBuscarUsuario";print_r($resBuscarUsuario);

		if ($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] =='00000')
		{ $resBuscarEmail = buscarEmail($resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']);
			
			if ($resBuscarEmail['codError']!=='00000')//error sistema <80000
			{$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = $resBuscarEmail['codError'];
				$resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje'] = $resBuscarEmail['errorMensaje'];
			
			}
			else //$resBuscarEmail['codError']=='00000'
			{if ($resBuscarEmail['numFilas'] !==0)
				{$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '80002';
					$resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje'] ='Ese Email ya existe, elija otro';	
				}			 
				else //$resBuscarEmail['numFilas']==0)
				{	$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '00000';//no es necesario	 
				}
			}	
		}
		//echo "<br><br>2-2validarCamposSocio:validarCamposAltaSocio:resBuscarEmail";print_r($resBuscarEmail);
		
		if ($resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] =='00000') 
		{ $resBuscarNumDoc = buscarNumDoc($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																			$resValidarCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
																																			$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']);
			if ($resBuscarNumDoc['codError'] !=='00000')//error sistema <80000
			{ $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = $resBuscarNumDoc['codError'];
					$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'] = $resBuscarNumDoc['errorMensaje'];
			}
			else //$resBuscarNumDoc['codError']=='00000'
			{ if ($resBuscarNumDoc['numFilas'] !==0)
					{ $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '80002';
							$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'] ='Ya hay registrado un socio/a
							 con ese mismo nº documento, es posible que previamente te hayas registrado como socio/a. 
								Puedes enviar un email a -secretaria@europalaica.org- para que te ayuden';	
					}			 
					else //$resBuscarNumDoc['numFilas']==0)
					{	$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '00000';//no es necesario	 
					}
			}	
		}
		//echo "<br><br>2-3validarCamposSocio:validarCamposAltaSocio:resBuscarNumDoc";print_r($resBuscarNumDoc);	

		$validarErrorSistema['codError'] ='00000';
		$validarErrorLogico['codError'] ='00000';
		$validarErrorLogico['errorMensaje'] ='';
		$totalErroresSistema = 0;
		$totalErroresLogicos = 0;
		$SOCIOSCONFIRMAR = array();	
		
		foreach ($resValidarCamposForm as $grupo => $valGrupo)
		{ 
				//echo "<br><br>3-0validarCamposSocio:validarCamposAltaSocio:valGrupo:";	print_r($valGrupo);	
				
				$SOCIOSCONFIRMAR = array_merge($SOCIOSCONFIRMAR,$valGrupo);//$SOCIOSCONFIRMAR = $SOCIOSCONFIRMAR + $valGrupo;
				
				//echo "<br><br>3-1a validarCamposSocio:validarCamposAltaSocio:SOCIOSCONFIRMAR:";	print_r($SOCIOSCONFIRMAR);	
				
				foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
				{	
					if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
					{
						if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
						{ $validarErrorSistema['codError'] =$resValidarCamposForm[$grupo][$nomCampo]['codError'];
								$validarErrorSistema['errorMensaje'] .=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
								$totalErroresSistema += 1; 
								break 2; 
						}
						else //$resValidarCamposForm[$grupo][$nomCampo]['codError'] >= '80000'
						{ $validarErrorLogico['codError'] = $resValidarCamposForm[$grupo][$nomCampo]['codError'];
								$validarErrorLogico['errorMensaje'] .=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
								$totalErroresLogicos += 1;
						}
					}		
				}	
		}
		//echo "<br><br>3-1b validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm:";	print_r($resValidarCamposForm); 
		if ($totalErroresSistema == 0 && $totalErroresLogicos == 0)
		{
				foreach ($resValidarCamposForm as $grupo => $valGrupo)
				{	unset ($resValidarCamposForm[$grupo]);	//PARA QUE NO OCUPE MEMORIA
				}
				$resValidarCamposForm['totalErrores'] = 0;
				$resValidarCamposForm['codError'] = '00000';
				$resValidarCamposForm['errorMensaje'] = '';	
				//nota el bucle es para guardar en tabla "SOCIOSCONFIRMAR" si no hay errores, si no se enviarán de nuevo

				$resValidarCamposForm['SOCIOSCONFIRMAR'] = $SOCIOSCONFIRMAR;	
				
				//echo "<br><br>3-2 validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm:";	print_r($resValidarCamposForm);								
		}	
		else
		{	if ($totalErroresSistema !==0)
				{
					$resValidarCamposForm['codError'] = $validarErrorSistema['codError'];//será el código del primer error del sistema
					$resValidarCamposForm['errorMensaje'] = $validarErrorSistema['errorMensaje'];//será el del primer error del sistema	
					$resValidarCamposForm['totalErrores'] = $totalErroresSistema;	
					$resValidarCamposForm['arrMensaje']['textoComentarios'] .=".Error del sistema, vuelva a intentarlo pasado un tiempo ";
					$resValidarCamposForm['arrMensaje']['textoBoton'] ='Salir de la aplicación';
					$resValidarCamposForm['arrMensaje']['enlaceBoton'] ='./index.php?controlador=controladorLogin&amp;accion=logOut';
					//echo "<br><br>3-3 validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm:";	print_r($resValidarCamposForm);
				} 
				else//if ($totalErroresLogicos !==0)
				{
					$resValidarCamposForm['codError'] ='80200';//o bien el último error:$validarErrorLogico['codError']
					$resValidarCamposForm['errorMensaje'] = $validarErrorLogico['errorMensaje'];//concatenación errormensaje
					$resValidarCamposForm['totalErrores'] = $totalErroresLogicos;	
					//echo "<br><br>3-4 validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm:";	print_r($resValidarCamposForm);
				} 
		}
 }//else !(!isset($camposFormRegSocio) || empty($camposFormRegSocio) )
 //echo "<br><br>3-6 validarCamposSocio:validarCamposAltaSocio:resValidarCamposForm:";	print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposAltaSocioSocio -----------------------------


/*---------------- Inicio validarCamposFormAltaSocio ---------------------------
DESCRIPCION:Valida los campos de formulario de alta de socio formRegistrarSocio.php  

LLAMADA: desde validarCamposSocio.php:validarCamposAltaSocio()
LLAMA: modelos/libs/validarCampos.php (varias funciones) y 
       modeloUsuarios.php:validarCampoUsuario(),validarPaisSEPA()
							
OBSERVACIONES: Un socio se puede dar de alta a el mismo como: General,Joven,Parado 
               pero no como Honorario	esto solo lo pueden hacer gestores rol
															Presidencia y Tesoreria
Agustín 
2019-12-30: Modificaciones en validación entre otras validarIBAN(),
------------------------------------------------------------------------------*/
function validarCamposFormAltaSocio($arrCamposForm)
{//echo "<br><br>1validarCamposSocio:validarCamposFormAltasSocio:arrCamposForm:";print_r($arrCamposForm);

 /*-------------------------------------- Inicio Validar  datosFormUsuario --------------------------------------------*/	
	$resulValidar['datosFormUsuario']['USUARIO'] = validarCampoUsuario($arrCamposForm['datosFormUsuario']['USUARIO'],6,30,"");

	$resulValidar['datosFormUsuario']['PASSUSUARIO'] = validarCampoPass($arrCamposForm['datosFormUsuario']['PASSUSUARIO'],6,30,"");	
 $resulValidar['datosFormUsuario']['RPASSUSUARIO'] = validarCampoPass($arrCamposForm['datosFormUsuario']['RPASSUSUARIO'],6,30,"");
	
	if ($arrCamposForm['datosFormUsuario']['PASSUSUARIO'] !== $arrCamposForm['datosFormUsuario']['RPASSUSUARIO'])
	{$resulValidar['datosFormUsuario']['PASSUSUARIO']['codError'] = '80430';
		$resulValidar['datosFormUsuario']['PASSUSUARIO']['errorMensaje']='Las dos contraseñas no eran iguales';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError'] = '80430';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']='Las dos contraseñas no eran iguales';
	}
	if (!isset($arrCamposForm['datosFormUsuario']['privacidad']) || empty($arrCamposForm['datosFormUsuario']['privacidad']) || $arrCamposForm['datosFormUsuario']['privacidad'] !=='SI')
	//if ($arrCamposForm['datosFormUsuario']['privacidad'] !=='SI')
	{$resulValidar['datosFormUsuario']['privacidad']['valorCampo']='NO';
		$resulValidar['datosFormUsuario']['privacidad']['codError']='80200';	
		$resulValidar['datosFormUsuario']['privacidad']['errorMensaje'] ='debes aceptar la política de privacidad para guardar el 
		formulario';	
	}	
	else
	{$resulValidar['datosFormUsuario']['privacidad']['valorCampo']='SI';
		$resulValidar['datosFormUsuario']['privacidad']['codError']='00000';	
		$resulValidar['datosFormUsuario']['privacidad']['errorMensaje']='';
	}	
	/*--------------------------------------------- Fin Validar  datosFormUsuario -------------------------------------------*/
	
 /*-------------------------------------- Inicio Validar datosFormSocio --------------------------------------------------*/	
	
	/*-------------------------------------- Inicio Validar Agrupación ---------------------------*/
 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
	$resulValidar['datosFormSocio']['CODAGRUPACION']['codError']='00000';
	
	if (isset($arrCamposForm['datosFormSocio']['CODAGRUPACION']) && !empty($arrCamposForm['datosFormSocio']['CODAGRUPACION']) && $arrCamposForm['datosFormSocio']['CODAGRUPACION'] !== 'Elige')
	{
		 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
 	 $resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] ='00000';	
			$resulValidar['datosFormSocio']['CODAGRUPACION']['errorMensaje'] ='';
	}
	else
	{ 
		 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo']="";
 	 $resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] ='80303';	
   $resulValidar['datosFormSocio']['CODAGRUPACION']['errorMensaje'] ='Error: debes elegir una agrupación';	
	}
	/*-------------------------------------- Fin Validar Agrupación ------------------------------*/
	
	$resulValidar['datosFormSocio']['CODCUOTA'] = validarCampoRadio($arrCamposForm['datosFormSocio']['CODCUOTA'],"");	
	
	/*------------------- Inicio Validar CUENTAIBAN (solo se admiten países SEPA) ----------------*/
																						
	/* Actualmente solo admitimos cuenta IBAN, lo dejo por si alguna vez cambiamos
	$resulValidar['datosFormSocio']['CUENTANOIBAN'] = validarCuentaNoIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);
	$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarCuentaIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);
	*/
	
	$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN']);
	//echo "<br><br>1-2-1 validarCamposSocio:validarCamposFormAltasSocio:resulValidar['datosFormSocio']['CUENTAIBAN']:";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);
	
	if ($resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] == '00000')
	{ //--- Si no es país SEPA devolverá error 
			$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarPaisSEPA($arrCamposForm['datosFormSocio']['CUENTAIBAN']);//en modeloUsuarios.php

			$resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo'] = $arrCamposForm['datosFormSocio']['CUENTAIBAN'];
	}	
 //echo "<br><br>1-2-2 validarCamposSocio:validarCamposFormAltasSocio:resulValidar['datosFormSocio']['CUENTAIBAN']:";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);	
	
	/*------------------- Fin Validar CUENTAIBAN (solo se admiten países SEPA) -------------------*/

	/*-------------------------------------- Fin Validar datosFormSocio -----------------------------------------------------*/
	

	 /*-------------------------------------- Inicio Validar datosFormDomicilio ---------------------------------------------*/
 $resulValidar['datosFormDomicilio'] = validarDom($arrCamposForm['datosFormDomicilio']);

	//echo "<br><br>validarCamposSocio validarCamposFormAltasSocio 1-1:resulValidar['datosFormDomicilio']: ";print_r($resulValidar['datosFormDomicilio']); 
  /*-------------------------------------- Fin Validar datosFormDomicilio ------------------------------------------------*/
		
 /*----------------------------------- Inicio datosFormMiembro -----------------------------------------------------------*/
	
	/*-------------------------------- Inicio Validar documento NIF, NIE, Pasaporte ------------------------------*/
	//echo "<br><br>2validarCamposSocio:validarCamposFormAltaSocio:resulValidar['datosFormMiembro']:";print_r($resulValidar['datosFormMiembro']);
	//-- las 6 siguientes líneas son para guardar vlores originales cuando hay error ¡no se pueden eliminar! ----
 $resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo'] = $arrCamposForm['datosFormMiembro']['CODPAISDOC'];	
	$resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='00000';

	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError']='00000';
	
 $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];	
	$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError']='00000';	
	//--------- fin 6 líneas -------------------------------------------------------------------------------------
	if ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Pasaporte' || $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Otros')   	
	{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNumPasaporte($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'],1,100,"");
	}
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIF')//NIF	
 {
			if ($arrCamposForm['datosFormMiembro']['CODPAISDOC']=='ES')
			{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNif($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
			}
			else
			{$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];
				$resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje']='Solo puedes elegir NIF si el país es España';
				$resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='80303';	
			}
	}	 
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIE')	//NIE
 { 
	  if ($arrCamposForm['datosFormMiembro']['CODPAISDOC']=='ES')
	  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNie($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);		
	  }
		 else
	  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];		
			 $resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje']='Solo puedes elegir NIE si el país es España';
	   $resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='80303';
	  }	 
	}
		//echo "<br><br>3validarCamposSocio:validarCamposFormAltaSocio:resulValidar['datosFormMiembro']:";print_r($resulValidar['datosFormMiembro']);
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/
	
	 //$resulValidar['datosFormMiembro']['SEXO'] = validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],""); php7 emite Notice
				
		if (isset($arrCamposForm['datosFormMiembro']['SEXO']) || !empty($arrCamposForm['datosFormMiembro']['SEXO']))
		{ $resulValidar['datosFormMiembro']['SEXO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['SEXO'];	
				$resulValidar['datosFormMiembro']['SEXO']['codError'] = '00000';
		}
  else
		{$resulValidar['datosFormMiembro']['SEXO']['codError'] ='80201';
	  $resulValidar['datosFormMiembro']['SEXO']['errorMensaje'] = "Sexo: debes elegir una opción";	
			$resulValidar['datosFormMiembro']['SEXO']['valorCampo'] = '';
		}	

	 //echo "<br><br>4validarCamposSocio:validarCamposFormAltaSocio:resulValidar['datosFormMiembro']['SEXO']:";print_r($resulValidar['datosFormMiembro']['SEXO']);
	
	$resulValidar['datosFormMiembro']['NOM'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");
	$resulValidar['datosFormMiembro']['APE1'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");

	/* Antes de 2019-12-12 lo dejo si da poblemas
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) && $arrCamposForm['datosFormMiembro']['APE2'] !=='')
	{$resulValidar['datosFormMiembro']['APE2'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],0,100, "");
	}
	else
	{ $resulValidar['datosFormMiembro']['APE2']['valorCampo'] ='';//evitar que ponga NULL, que puede dar problemas en concatenaciones		
	  $resulValidar['datosFormMiembro']['APE2']['codError'] ='00000';
	}*/
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) /*&& $arrCamposForm['datosFormMiembro']['APE2'] !==''*/)
	{$resulValidar['datosFormMiembro']['APE2'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],0,100, "");}	

 $resulValidar['datosFormMiembro']['FECHANAC'] = validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);
 
	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM']=='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}
	
	$resulValidar['datosFormMiembro']['EMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		
	$resulValidar['datosFormMiembro']['REMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['REMAIL'],"");	
	if ($arrCamposForm['datosFormMiembro']['EMAIL'] !==$arrCamposForm['datosFormMiembro']['REMAIL'])
	{$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '80430';
		$resulValidar['datosFormMiembro']['EMAIL']['errorMensaje']='Los dos email son diferentes';
		$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '80430';
		$resulValidar['datosFormMiembro']['REMAIL']['errorMensaje']='Las dos email son diferentes';
	}	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS']=='SI')
	{ $infCartas='SI';	}
	else 
	{ $infCartas='NO';	}	
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=$infCartas;
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL']=='SI')
	{ $infEmail='SI';	}
	else 
	{ $infEmail='NO';	}
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] =$infEmail;
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
 //considero que al darse de alta un usuario, despues de validado el formato, el email será correcto
	//El presidente, coordinador, secretario, podrían anotorlo como devuelto, en caso de que eso suceda 
 $resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo']='NO';
	$resulValidar['datosFormMiembro']['EMAILERROR']['codError']='00000';

	//$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],0,120,"");
	if (isset($arrCamposForm['datosFormMiembro']['PROFESION']) && !empty($arrCamposForm['datosFormMiembro']['PROFESION']))
	{$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['PROFESION']['valorCampo']=$arrCamposForm['datosFormMiembro']['PROFESION'];
		$resulValidar['datosFormMiembro']['PROFESION']['codError']='00000';	
 }	
		
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo']=$arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError']='00000';	
	
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo']=$arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError']='00000';	
	
	if (isset($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']) && !empty($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']))
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError']='00000';	
 }	
 /*------------------------------------------ Fin datosFormMiembro --------------------------------------------------------*/
		
 /*-------------------------------------- Inicio Validar datosFormCuotaSocio ----------------------------------------------*/	
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError']='00000';

 $resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['CODCUOTAGeneral'];
	$resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['codError']='00000';		

 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['codError']='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['codError']='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['codError']='00000';				

	if ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='General')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'],10000.00,"");
	}
	elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Joven')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] =	validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                               $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'],10000.00,"");	
	}
 elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Parado')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
									                                                                                	$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'],10000.00,"");	
	}		
	/*-------------------------------------- Fin Validar datosFormCuotaSocio ---------------------------------------------------*/				

	//echo "<br><br>2validarCamposSocio:validarCamposFormAltasSocio:resulValidar:"; print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormAltaSocio -----------------

/*---------------- Inicio validarCamposActualizarSocio() -----------------------
Valida los campos de actualizar socio, y comprueba existencia en tablas de 
nuevo usuario, email, doc

LLAMADA: controladorSocios.php: actualizarSocio(),
validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor(),
(Que a su vez se llama desde: cCoordinador.php:actualizarSocioCoord(), y
cPresidente.php:actualizarSocioPres() ),
validarCamposTesorero.php:validarCamposActCuotaSocioTes()
(Que a su vez se llama desde: cTesorero.php:actualizarDatosCuotaSocioTes())

LLAMA: validarCamposSocio.php:validarCamposFormActualizarSocio()
modeloUsuarios.php:buscarUsuario(),buscarEmail(), buscarNumDoc()

OBSERVACIONES:2020-04-14: comentarios																	
------------------------------------------------------------------------------*/
function validarCamposActualizarSocio($camposFormRegSocio)
{//echo "<br><br>1 actualizarSocio:validarCamposActualizarSocio:camposFormRegSocio: ";print_r($camposFormRegSocio);

 $resValidarCamposForm = validarCamposFormActualizarSocio($camposFormRegSocio['campoActualizar']);
	
	//echo "<br><br>2 actualizarSocio:validarCamposActualizarSocio:resValidarCamposForm";print_r($resValidarCamposForm);
 
	if (($resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] == '00000') &&
	    ($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']!== $camposFormRegSocio['campoHide']['anteriorUSUARIO']))
	{ 
	  $resBuscarUsuario = buscarUsuario($resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']);
	
   //echo "<br><br>3-1 actualizarSocio:validarCamposActualizarSocio:resBuscarUsuario";print_r($resBuscarUsuario);
		 if ($resBuscarUsuario['codError']!=='00000') //error sistema <80000
		 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = $resBuscarUsuario['codError'];
		  $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']=$resBuscarUsuario['errorMensaje'];
	  }
		 else //$resBuscarUsuario['codError']=='00000'
		 {if ($resBuscarUsuario['numFilas']!==0)
	   {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError'] = '80002';
		   $resValidarCamposForm['datosFormUsuario']['USUARIO']['errorMensaje']='Ese usuario/a ya existe, elija otro nombre';	
	 	 }			 
		  else //$resBuscarUsuario['numFilas']==0)
			 {$resValidarCamposForm['datosFormUsuario']['USUARIO']['codError']='00000';//no es necesario
    }
		 } //$resBuscarUsuario['codError']=='00000'
	}	
	//echo "<br><br>3-2 actualizarSocio:validarCamposActualizarSocio:resValidarCamposForm['datosFormUsuario']: ";print_r($resValidarCamposForm['datosFormUsuario']);
		
 //Si EMAIL es formato válido (['codError']=='00000')y es distinto del anteriormente existente (es nuevo o correción)
 if (($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] == '00000') &&
	    		$resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']!==$camposFormRegSocio['campoHide']['anteriorEMAIL'])
	{
	 $resBuscarEmail = buscarEmail($resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']);
		 
	 if ($resBuscarEmail['codError']!=='00000')//error sistema <80000
	 {$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = $resBuscarEmail['codError'];
	  $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']=$resBuscarEmail['errorMensaje'];
		
	 }
	 else //$resBuscarEmail['codError']=='00000'
	 {
			if ($resBuscarEmail['numFilas']!==0) //ya existe ese email
	  {	$resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] = '80002';
		   $resValidarCamposForm['datosFormMiembro']['EMAIL']['errorMensaje']='Ese Email ya existe, elija otro';	
		 }			 
	 }
	}
	//echo "<br><br>3-3 actualizarSocio:validarCamposActualizarSocio:resValidarCamposForm['datosFormMiembro']:";print_r($resValidarCamposForm['datosFormMiembro']); 

 if (($resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] =='00000' &&  $resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['codError'] =='00000') &&
						($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo']!== $camposFormRegSocio['campoHide']['anteriorCODPAISDOC'] ||
					 	$resValidarCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']!== $camposFormRegSocio['campoHide']['anteriorTIPODOCUMENTOMIEMBRO'] ||		
						 $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']!== $camposFormRegSocio['campoHide']['anteriorNUMDOCUMENTOMIEMBRO']
					 )					
		  )
	{ $resBuscarNumDoc = buscarNumDoc($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
	                                  $resValidarCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
	                                  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']);
		 if ($resBuscarNumDoc['codError']!=='00000')//error sistema <80000
		 {$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = $resBuscarNumDoc['codError'];
		  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']=$resBuscarNumDoc['errorMensaje'];
		 }
		 else //$resBuscarNumDoc['codError']=='00000'
		 { if ($resBuscarNumDoc['numFilas']!==0)
		   { $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '80002';
			  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']='Ya hay registrado un socio/a
				  con ese mismo nº documento';	
			  }			 
		   else //$resBuscarNumDoc['numFilas']==0)
			  {	$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '00000';//no es necesario	 
     }
		 }	
	}				
 
	$validarErrorSistema['codError']='00000';
	$validarErrorLogico['codError']='00000';
	$validarErrorLogico['errorMensaje'] ='';
	$totalErroresSistema = 0;
	$totalErroresLogicos = 0;
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
		  { 
				  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
				  {//echo "<br><br>3-4 actualizarSocio:validarCamposActualizarSocio:resValidarCamposForm:";print_r($resValidarCamposForm); 			

						 $validarErrorSistema['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
				   $validarErrorSistema['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
				   $totalErroresSistema +=1; 
					  break 2; 
				  }
				  else //$resValidarCamposForm[$grupo][$nomCampo]['codError'] >= '80000'
				  {//echo "<br><br>3-5 actualizarSocio:validarCamposActualizarSocio:resValidarCamposForm:";print_r($resValidarCamposForm); 			

						 $validarErrorLogico['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
				   $validarErrorLogico['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
							$totalErroresLogicos +=1;
				  }
			 }		
		}	
	}
	//echo "<br><br>4-1 actualizarSocio:validarCamposActualizarSocio:totalErroresLogicos:";print_r($totalErroresLogicos); 
	//echo "<br><br>4-2 actualizarSocio:validarCamposActualizarSocio:totalErroresSistema:";print_r($totalErroresSistema); 	
	if ($totalErroresSistema ==0 && $totalErroresLogicos == 0)
	{ $resValidarCampos['totalErrores']=0;
	  $resValidarCampos['codError']='00000';
	  $resValidarCampos['errorMensaje']='';		
   //echo "<br><br>4-3 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos); 			
	}	
	elseif ($totalErroresSistema !==0)
	{$resValidarCampos['codError']=$validarErrorSistema['codError'];//será el código del primer error del sistema
	 $resValidarCampos['errorMensaje']=$validarErrorSistema['errorMensaje'];//será el del primer error del sistema	
		$resValidarCampos['totalErrores']=$totalErroresSistema;	
	 $resValidarCampos['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo ";
	 $resValidarCampos['arrMensaje']['textoBoton']='Salir de la aplicación';
	 
  //echo "<br><br>4-4 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos); 			
		$resValidarCampos['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	} 
	else//if ($totalErroresLogicos !==0)
	{$resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCampos['errorMensaje'] = $validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCampos['totalErrores'] = $totalErroresLogicos;	
  //echo "<br><br>4-5 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos);
	} 
	//echo "<br><br>5-1 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos);

 $resValidarCampos['campoActualizar'] = $resValidarCamposForm;//los campos que se validan 
	
 $resValidarCampos['campoActualizar']['datosCuotasEL'] = $camposFormRegSocio['campoActualizar']['datosCuotasEL'];//no se validan, pero se usan en validaciones 	

	$resValidarCampos['campoHide'] = $camposFormRegSocio['campoHide'];//campos que hidden,se pasan sin validar
	$resValidarCampos['campoVerAnioActual'] = $camposFormRegSocio['campoVerAnioActual'];//campos que hidden,se pasan sin validar
	
	//echo "<br><br>5-2 actualizarSocio:validarCamposActualizarSocio:resValidarCampos: ";print_r($resValidarCampos);
	
	return $resValidarCampos; //incluye arrayMensaje
}
//---------------- Fin validarCamposActualizarSocio ----------------------------

/*---------------- Inicio validarCamposFormActualizarSocio ---------------------
DESCRIPCION: Valida los campos de formulario de actualizarsocio formActualizarSocio.php 
        
LLAMADA: validarCamposSocio.php:validarCamposActualizarSocio()
LLAMA: modelos/libs/validarCampos.php (varias funciones)
modeloUsuarios.php:validarPaisSEPA()

OBSERVACIONES:
2020-04-14: ordeno 		
2019-12-11:['PROFESION']=validarCampoTexto(),['COMENTARIOSOCIO']=validarCampoTexto()  
------------------------------------------------------------------------------*/
function validarCamposFormActualizarSocio($arrCamposForm)
{//echo "<br><br>1 validarCamposSocio.php:validarCamposFormActualizarSocio:arrCamposForm: ";print_r($arrCamposForm);

 /*--------------------------------- Inicio Validar  datosFormUsuario --------------------------------------------*/	
	$resulValidar['datosFormUsuario']['USUARIO'] = validarCampoUsuario($arrCamposForm['datosFormUsuario']['USUARIO'],6,30,"");
	
	if (isset($arrCamposForm['datosFormUsuario']['CODUSER']))
	{$resulValidar['datosFormUsuario']['CODUSER']['valorCampo'] = $arrCamposForm['datosFormUsuario']['CODUSER'];
	 $resulValidar['datosFormUsuario']['CODUSER']['codError'] ='00000';	
 }
	if (isset($arrCamposForm['datosFormUsuario']['ESTADO']))
	{$resulValidar['datosFormUsuario']['ESTADO']['valorCampo'] = $arrCamposForm['datosFormUsuario']['ESTADO'];
	 $resulValidar['datosFormUsuario']['ESTADO']['codError'] ='00000';	
 }
	//echo "<br><br>2 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormUsuario']['CODUSER']: ";print_r($resulValidar['datosFormUsuario']['CODUSER']);		
	/*--------------------------------------- Fin Validar  datosFormUsuario ----------------------------------------*/

 /*------------------------------------ Inicio Validar datosFormSocio --------------------------------------------*/
	
 if (isset($arrCamposForm['datosFormSocio']['CODSOCIO']))
	{$resulValidar['datosFormSocio']['CODSOCIO']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODSOCIO'];
	 $resulValidar['datosFormSocio']['CODSOCIO']['codError'] = '00000';	
 }
	//echo "<br><br>3-1 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormSocio']['CODSOCIO']: ";print_r($resulValidar['datosFormSocio']['CODSOCIO']);
	
 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
	$resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] ='00000';		
																																																									
	/* Actualmente solo admitimos cuenta IBAN, lo dejo por si alguna vez cambiamos
	$resulValidar['datosFormSocio']['CUENTANOIBAN'] = validarCuentaNoIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);
	$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarCuentaIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);
	*/
 //$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN']);
 
	if (isset($arrCamposForm['datosFormSocio']['CUENTAIBAN'])) 
	{$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN']);
  
  //echo "<br><br>3-2-1 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);		
		
		if ($resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] == '00000')
		{
    $resulValidar['datosFormSocio']['CUENTAIBAN']	= validarPaisSEPA($arrCamposForm['datosFormSocio']['CUENTAIBAN']);//en modeloUsuarios.php

    $resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo'] = $arrCamposForm['datosFormSocio']['CUENTAIBAN'];
		}		
 }	
	//echo "<br><br>3-2-3 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);

	/* PARA CONTROL DE FECHA DEA ACTUALIZACION CUENTAIBAN o CUENTANOIBAN 
	( IMPORTANTE PARA DOMICILIACIÓN PAGOS SEGUN NOMAS SEPA DISTINGE SI ES NUEVA CUENTA O ANTIGUA)
	if (($arrCamposForm['datosFormSocio']['CUENTAIBAN']['codError'] == '00000') &&
	   ($arrCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']!== $camposFormRegSocio['campoHide']['anteriorCUENTAIBAN']))
	{
	 $date->setTimezone(new DateTimezone('UTC'));//El tiempo universal coordinado, o UTC
  //$arrCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] = $date->format('Y-m-d: H:i:s');//formato 2015-03-01: 11:38:53
		$arrCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] = $date->format('Y-m-d');//formato 2015-03-01		
	}				
	*/																																																									
	$resulValidar['datosFormSocio']['CODCUOTA'] = validarCampoRadio($arrCamposForm['datosFormSocio']['CODCUOTA'],"");	
	/*----------------------------------- Fin Validar datosFormSocio ------------------------------------------------*/

 /*----------------------------------- Inicio Validar datosFormDomicilio -----------------------------------------*/
	$resulValidar['datosFormDomicilio'] = validarDom($arrCamposForm['datosFormDomicilio']);
	//echo "<br><br>4 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormDomicilio']: ";print_r($resulValidar['datosFormDomicilio']); 
 /*------------------------------------ Fin Validar datosFormDomicilio -------------------------------------------*/	
	
	/*----------------------------------- Inicio Validar INFORMACIONCARTAS ------------------------------------------*/
	if (isset($arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS']) && $arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS'] =='SI')
	{ $resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] = 'SI';	}
	else 
	{ $resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] = 'NO';	}		

	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError'] = '00000';	
	/*------------------------------------ Fin Validar INFORMACIONCARTAS --------------------------------------------*/	
			
	
 /*----------------------------------- Inicio datosFormMiembro ---------------------------------------------------*/
	
	/*-------------------------------- Inicio Validar documento NIF, NIE, Pasaporte ----------------------*/
	//echo "<br><br>5-1 validarCamposSocio:validarCamposFormActualizarSocio:resulValidar['datosFormMiembro']: ";print_r($resulValidar['datosFormMiembro']);
	//-- las 6 siguientes líneas son para guardar valores originales cuando hay error ¡no se pueden eliminar! ----
 $resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo'] = $arrCamposForm['datosFormMiembro']['CODPAISDOC'];	
	$resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='00000';

	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError'] ='00000';
	
 $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];	
	$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] ='00000';	
	//--------- fin 6 líneas --------------------------------------------------------------------------------
	if ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Pasaporte' || $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Otros')   	
	{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNumPasaporte($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'],1,100,"");
	}
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIF')//NIF	
 {if ($arrCamposForm['datosFormMiembro']['CODPAISDOC']=='ES')
	 { $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNif($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
  }
		else
	 {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];
		 $resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje'] ='Solo puedes elegir NIF si el país es España';
	  $resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='80303';	
	 }
	}	 
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='NIE')	//NIE
 { if ($arrCamposForm['datosFormMiembro']['CODPAISDOC'] =='ES')
	  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNie($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);		
	  }
		 else
	  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];		
			 $resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje']='Solo puedes elegir NIE si el país es España';
	   $resulValidar['datosFormMiembro']['CODPAISDOC']['codError']='80303';
	  }	 
	}
	//echo "<br><br>5-2 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormMiembro']: ";print_r($resulValidar['datosFormMiembro']);
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/
	
	$resulValidar['datosFormMiembro']['SEXO'] = validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],"");

	//$resulValidar['datosFormMiembro']['tipoMiembro']=validarCampoRadio($arrCamposForm['datosFormMiembro']['tipoMiembro'],"");
	
	$resulValidar['datosFormMiembro']['NOM'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");
	$resulValidar['datosFormMiembro']['APE1'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");
	
	/* Antes de 2019-12-12 lo dejo si da poblemas
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) && $arrCamposForm['datosFormMiembro']['APE2'] !=='')
	{$resulValidar['datosFormMiembro']['APE2'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],0,100, "");
	}
	else
	{ $resulValidar['datosFormMiembro']['APE2']['valorCampo'] ='';//evitar que ponga NULL, que puede dar problemas en concatenaciones		
	  $resulValidar['datosFormMiembro']['APE2']['codError'] ='00000';
	}*/
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) /*&& $arrCamposForm['datosFormMiembro']['APE2'] !==''*/)
	{$resulValidar['datosFormMiembro']['APE2'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],0,100, "");}

 $resulValidar['datosFormMiembro']['FECHANAC'] = validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);

	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM']=='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}

	//----- Inicio validar EMAIL, EMAILERROR, INFORMACIONEMAIL -----------------

	if ($arrCamposForm['datosFormMiembro']['EMAILERROR'] == 'FALTA')
 { 
		$resulValidar['datosFormMiembro']['EMAIL']['valorCampo'] = "falta".$arrCamposForm['datosFormUsuario']['CODUSER']."@falta.com";		
	 $resulValidar['datosFormMiembro']['REMAIL']['valorCampo'] = "falta".$arrCamposForm['datosFormUsuario']['CODUSER']."@falta.com";
		$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '00000';			
		$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '00000';	

		$resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo'] = 'FALTA';
		$resulValidar['datosFormMiembro']['EMAILERROR']['codError'] ='00000';	
	 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = 'NO';
	 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError'] ='00000';	
 }
	else //$arrCamposForm['datosFormMiembro']['EMAILERROR'] !== 'FALTA'
	{	
	 if ($arrCamposForm['datosFormMiembro']['EMAIL'] !== $arrCamposForm['datosFormMiembro']['REMAIL'])
  {
				$resulValidar['datosFormMiembro']['EMAIL']['valorCampo'] = $arrCamposForm['datosFormMiembro']['EMAIL'];
				$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '80430';
				$resulValidar['datosFormMiembro']['EMAIL']['errorMensaje'] = 'Los dos email son diferentes';
				
				$resulValidar['datosFormMiembro']['REMAIL']['valorCampo'] = $arrCamposForm['datosFormMiembro']['REMAIL'];
				$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '80430';
				$resulValidar['datosFormMiembro']['REMAIL']['errorMensaje'] = 'Los dos email son diferentes';
	 }	
		elseif ( $arrCamposForm['datosFormMiembro']['EMAILERROR'] == 'DEVUELTO' || $arrCamposForm['datosFormMiembro']['EMAILERROR'] == 'ERROR-FORMATO')
		{ 			
				if ( isset($arrCamposForm['datosFormMiembro']['EMAIL']) && !empty($arrCamposForm['datosFormMiembro']['EMAIL']) )
				{$resulValidar['datosFormMiembro']['EMAIL']['valorCampo'] = $arrCamposForm['datosFormMiembro']['EMAIL'];}
				else
				{$resulValidar['datosFormMiembro']['EMAIL']['valorCampo'] = $arrCamposForm['datosFormUsuario']['CODUSER'].$arrCamposForm['datosFormMiembro']['EMAILERROR']."@falta.com";	}
			
				if ( isset($arrCamposForm['datosFormMiembro']['REMAIL']) && !empty($arrCamposForm['datosFormMiembro']['REMAIL']) )
				{$resulValidar['datosFormMiembro']['REMAIL']['valorCampo'] = $arrCamposForm['datosFormMiembro']['REMAIL'];}
				else
				{$resulValidar['datosFormMiembro']['REMAIL']['valorCampo'] = $arrCamposForm['datosFormUsuario']['CODUSER'].$arrCamposForm['datosFormMiembro']['EMAILERROR']."@falta.com";	}			

				$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '00000';			
				$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '00000';						
		}
		else
		{	
	 		$resulValidar['datosFormMiembro']['EMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		
		 	$resulValidar['datosFormMiembro']['REMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['REMAIL'],"");	
		}
		
		$resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo'] = $arrCamposForm['datosFormMiembro']['EMAILERROR'];
		$resulValidar['datosFormMiembro']['EMAILERROR']['codError'] = '00000';			

		if (isset($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL']) && $arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] =='SI')
		{ $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = 'SI';	}
		else 
		{ $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = 'NO';	}		
	
		$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError'] = '00000';	
 }//else $arrCamposForm['datosFormMiembro']['EMAILERROR'] !== 'FALTA'	

	//----- Fin validar EMAIL, EMAILERROR, INFORMACIONEMAIL --------------------
	
	//echo "<br><br>5-3 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormMiembro']: ";print_r($resulValidar['datosFormMiembro']);	
	
	//$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],0,120,"");
	if (isset($arrCamposForm['datosFormMiembro']['PROFESION']) && !empty($arrCamposForm['datosFormMiembro']['PROFESION']))
	{$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['PROFESION']['valorCampo'] =$arrCamposForm['datosFormMiembro']['PROFESION'];
		$resulValidar['datosFormMiembro']['PROFESION']['codError'] ='00000';	
 }		
		
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo'] = $arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError'] ='00000';	
		
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo'] = $arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError'] ='00000';	
	
	if (isset($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']) && !empty($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']))
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'],3,255,"");//lo dejo por si acaso, por ahora no se actualiza  
 }	
 else
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'];
		$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError'] ='00000';
 }
 /*-------------------------------- Fin datosFormMiembro ---------------------------------------------------------*/
	
		/*-------------------------------------- Inicio Validar datosFormCuotaSocio -------------------------------------*/					
 $resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError'] = '00000';		
	$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['ESTADOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['codError'] = '00000';
	$resulValidar['datosFormCuotaSocio']['MODOINGRESO']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['MODOINGRESO'];
	$resulValidar['datosFormCuotaSocio']['MODOINGRESO']['codError'] = '00000';	

	if ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='General')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
																																																																																										$arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELGeneral'],10000.00,"");
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']= $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELGeneral'];
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['codError']='00000';														
	}
	elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Joven')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELJoven'],10000.00,"");
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'] = $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELJoven'];
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['codError']='00000';												
	}
 elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Parado')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELParado'],10000.00,"");	
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'] = $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELParado'];
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['codError'] ='00000';												
	}
 elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Honorario')//honorario cuota=0 
	{ //$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']=
			//						validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
			//							$arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELHonorario'],10000.00,"");
			// los Honorarios tendrán cuota=0, sin posibilidad de cuota+donacion		
  $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELHonorario'];										
  $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['codError'] ='00000';			
		
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'] = $arrCamposForm['datosCuotasEL']['IMPORTECUOTAANIOELHonorario'];
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['codError'] ='00000';												
	}
	
	//echo "<br><br>6 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar['datosFormCuotaSocio']: ";print_r($resulValidar['datosFormCuotaSocio']); 
	/*-------------------------------------- Fin Validar datosFormCuotaSocio -------------------------------------------*/	
	
 /*----------------------------------- Inicio datosFormPrivacidad ---------------------------------------------------*/
	
	if (!isset($arrCamposForm['datosFormPrivacidad']['privacidad']) || empty($arrCamposForm['datosFormPrivacidad']['privacidad']) || $arrCamposForm['datosFormPrivacidad']['privacidad'] !=='SI')
	//if ($arrCamposForm['datosFormPrivacidad']['privacidad']!=='SI')
	{$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo'] = 'NO';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError'] ='80200';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje'] ='debes aceptar la política de privacidad para guardar el	formulario';	
	}	
	else
	{$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo'] ='SI';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError'] ='00000';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje'] ='';
	}		
	/*----------------------------------- Fin datosFormPrivacidad ------------------------------------------------------*/
	
	//echo "<br><br>7 validarCamposSocio.php:validarCamposFormActualizarSocio:resulValidar: "; print_r($resulValidar);
 return $resulValidar;
}
/*--------------------------- Fin validarCamposFormActualizarSocio -----------*/



/*----------------- Inicio validarEliminarSocio --------------------------------
DESCRIPCION:Valida los campos de formulario "formEliminarSocio.php" 

Llamado desde: controladorSocios.php:eliminarSocio(),
cCordinador.php:eliminarSocioCoord(),c_Presidente.php:eliminarSocioPres(),
cTesorero.php:eliminarSocioTes()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)

Agustín: 2019-12-19 lo añado para evitar injection del campo OBSERVACIONES 
------------------------------------------------------------------------------*/
function validarEliminarSocio($camposFormEliminarSocio)
{
	//echo "<br><br>1 validarCamposSocio:validarEliminarSocio:camposFormEliminarSocio:";print_r($camposFormEliminarSocio);
 $resValidarCamposForm = validarFormEliminarSocio($camposFormEliminarSocio);
	//echo "<br><br>2 validarCamposSocio:validarEliminarSocio:resValidarCamposForm";print_r($resValidarCamposForm);
	
	$validarErrorSistema['codError'] ='00000';
	$validarErrorLogico['codError'] ='00000';
	$validarErrorLogico['errorMensaje'] ='';
	$totalErroresSistema = 0;
	$totalErroresLogicos = 0;
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
		  {if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
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
	{ $resValidarCampos['totalErrores']=0;
	  $resValidarCampos['codError']='00000';
	  $resValidarCampos['errorMensaje']='';		
   //echo "<br><br>4-3 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos); 			
	}	
	elseif ($totalErroresSistema !==0)
	{$resValidarCampos['codError']=$validarErrorSistema['codError'];//será el código del primer error del sistema
	 $resValidarCampos['errorMensaje']=$validarErrorSistema['errorMensaje'];//será el del primer error del sistema	
		$resValidarCampos['totalErrores']=$totalErroresSistema;	
	 $resValidarCampos['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo ";
	 $resValidarCampos['arrMensaje']['textoBoton']='Salir de la aplicación';
	 
  //echo "<br><br>4-4 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos); 			
		$resValidarCampos['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	} 
	else//if ($totalErroresLogicos !==0)
	{$resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCampos['errorMensaje'] = $validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCampos['totalErrores'] = $totalErroresLogicos;	
  //echo "<br><br>4-5 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos);
	} 
	//echo "<br><br>5-1 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos);

 $resValidarCampos['valoresCampos'] = $resValidarCamposForm;//los campos que se validan 
	
 //echo "<br><br>3 validarCamposSocio:validarEliminarSocio:resValidarCampos:";	print_r($resValidarCampos);

	return $resValidarCampos; //incluye arrayMensaje
}		
//---------------- Fin validarEliminarSocio ------------------------------------	
		
/*---------------- Inicio validarFormEliminarSocio -----------------------------
DESCRIPCION:Valida los campos de formulario formEliminarSocio.php      
Llamado: desde validarCamposSocio.php:validarEliminarSocio()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)

Agustín: 2019-12-19 lo añado para evitar injection del campo OBSERVACIONES 
y acaso se puedan añadir otros campos en el futuro
------------------------------------------------------------------------------*/
function validarFormEliminarSocio($arrCamposForm)
{//echo "<br><br>1 validarCamposSocio:validarFormEliminarSocio:arrCamposForm:";print_r($arrCamposForm);echo "<br>";

	/*-------------------------------- Inicio Validar  OBSERVACIONES ------------*/	
	$resValidarCamposForm = $arrCamposForm;
	
	if (isset($resValidarCamposForm['SiEliminar'])) 
	{unset($resValidarCamposForm['SiEliminar']);}

 if (isset($resValidarCamposForm['SiEliminarFallecimiento'])) 
	{unset($resValidarCamposForm['SiEliminarFallecimiento']);}

	
	//echo "<br><br>2b validarCamposSocio:validarFormEliminarSocio:resValidarCamposForm:";print_r($resValidarCamposForm);	echo "<br>";
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ //echo "<br><br>2b-1 validarCamposSocio:validarFormEliminarSocio:resValidarCamposForm:valGrupo: ";print_r($valGrupo);	echo "<br>";
	  
			foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
			 //echo "<br><br>2b-2 validarCamposSocio:validarFormEliminarSocio:resValidarCamposForm:$nomCampo:valNomCampo: ";print_r($valNomCampo);	echo "<br>";
			 $aux['valorCampo'] = $valNomCampo;
				
				$resValidarCamposForm[$grupo][$nomCampo]= $aux; 
				//echo "<br><br>2b-3 validarCamposSocio:validarFormEliminarSocio:resValidarCamposForm:: ";print_r($resValidarCamposForm);	echo "<br>";
				
			 $resValidarCamposForm[$grupo][$nomCampo]['codError'] ='00000';
		 }	
	}
	$resulValidar = $resValidarCamposForm;
	//echo "<br><br>2c validarCamposSocio:validarFormEliminarSocio:resulValidar:";print_r($resulValidar);
	
	/* ['OBSERVACIONES'] se valida para evitar posibles injection y se guardará en tabla MIEMBROELIMINADO5ANIOS y además en SOCIOSFALLECIDOS en 
    el caso de que la baja sea por fallecimiento                                                                                         
	*/
	if (isset($arrCamposForm['datosFormSocio']['OBSERVACIONES']) && !empty($arrCamposForm['datosFormSocio']['OBSERVACIONES']))
	{$resulValidar['datosFormSocio']['OBSERVACIONES'] = validarCampoTexto($arrCamposForm['datosFormSocio']['OBSERVACIONES'],0,999,"");
 }	
 else
	{$resulValidar['datosFormSocio']['OBSERVACIONES']['codError']='00000';	
 }	
 /*-------------------------------- Fin Validar  OBSERVACIONES ---------------*/	
	
	//echo "<br><br>3 validarCamposSocio:validarFormEliminarSocio:resulValidar:";print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarFormEliminarSocio -------------------

/*---------------- Inicio validarCamposSimpAsocio() ---------------------------
DESCRIPCION: Valida los campos de pasar de simpatizante a socio
Llamado desde: controladorSimpatizantes.php: simpatizanteAsocio()
Llama funciones: validarCamposFormSimpAsocio()
                 modeloUsuarios.php:buscarUsuario(),buscarEmail(), buscarNumDoc()
NOTA: NO SE USA, lo dejo por si algún día ....																	
------------------------------------------------------------------------------*/
function validarCamposSimpAsocio($camposFormRegSocio)
{//echo "<br><br>1-0_0 validarCamposSimpAsocio:camposFormRegSocio";print_r($camposFormRegSocio);
 $resValidarCamposForm=validarCamposFormSimpAsocio($camposFormRegSocio);
	//echo "<br><br>1-0_1 validarCamposSimpAsocio:resValidarCamposForm";print_r($resValidarCamposForm);

  if (($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] =='00000') &&
	      $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']!==$_SESSION['vs_EMAIL'])
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
 if ($resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] =='00000') 
 {$resBuscarNumDoc=buscarNumDoc($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
                                 $resValidarCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
                                 $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']);
	 if ($resBuscarNumDoc['codError']!=='00000')//error sistema <80000
	 {$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = $resBuscarNumDoc['codError'];
	  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']=$resBuscarNumDoc['errorMensaje'];
	 }
	 else //$resBuscarNumDoc['codError']=='00000'
	 {if ($resBuscarNumDoc['numFilas']!==0)
	  { $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '80002';
		  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']='Ya hay registrado un socio
			 o simpatizante con ese mismo nº documento';	
		 }			 
	  else //$resBuscarNumDoc['numFilas']==0)
		 {	$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '00000';//no es necesario	 
   }
	 }	
 }
 //----------SOLO PUEDE HABER ERRORES LÓGICOS ----------------------------
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
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposSimpAsocio ----------------------------------


/*---------------- Inicio validarCamposFormSimpAsocio ---------------------------
DESCRIPCION:Valida los campos de formulario de      
Llamado desde: 
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
NOTA: NO SE USA, lo dejo por si algún día ....				
------------------------------------------------------------------------------*/
function validarCamposFormSimpAsocio($arrCamposForm)
{ /* ------------------------------------ Inicio Validar datosFormDomicilio ------------------------------------------- */
	$resulValidar['datosFormDomicilio']=validarDom($arrCamposForm['datosFormDomicilio']);
	//echo "<br><br>validarCamposSocio validarCamposFormActualizarSocio1-1:resulValidar['datosFormDomicilio']:"; 
	//print_r($resulValidar['datosFormDomicilio']); 
 /* ------------------------------------ Fin Validar datosFormDomicilio ------------------------------------------- */
	/* ===================================== INICIO DATOS NUEVOS ======================================================= */
	/* -------------------------------------- Inicio Validar datosFormSocio ---------------------------------------- */	
	/* --------------------------------- Inicio Validar documento NIF, NIE, Pasaporte ------------------------------ */
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
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/		
	
 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
	$resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] = '00000';	
	
 $resulValidar['datosFormSocio']['CODCUOTA']=validarCampoRadio($arrCamposForm['datosFormSocio']['CODCUOTA'],"");
 						
	$resulValidar['datosFormSocio']['ctaBanco'] = calculoCCES($arrCamposForm['datosFormSocio']['ctaBanco'],
	                                                          $arrCamposForm['datosFormSocio']['CCEXTRANJERA']);
	$resulValidar['datosFormSocio']['CCEXTRANJERA'] = calculoCCEX($arrCamposForm['datosFormSocio']['ctaBanco'],
	                                                              $arrCamposForm['datosFormSocio']['CCEXTRANJERA']);		
	/*-------------------------------------- Fin Validar datosFormSocio -------------------------------------------------*/	
 /*-------------------------------------- Inicio Validar datosFormCuotaSocio ------------------------------------------*/	
/*	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError']='00000';
 $resulValidar['datosFormCuotaSocio']['CODCUOTA']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['CODCUOTA'];
	$resulValidar['datosFormCuotaSocio']['CODCUOTA']['codError']='00000';		
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']=
	$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['codError']='00000';
	$resulValidar['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['NOMBRECUOTA'];
	$resulValidar['datosFormCuotaSocio']['NOMBRECUOTA']['codError']='00000';
						
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']=
	     validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
						//$arrCamposForm['datosFormCuotaAnioActualSocioVer']['IMPORTECUOTAANIOEL'],1000000.00,"");
						$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL'],1000000.00,"");						
	*//*-------------------------------------- Fin Validar datosFormCuotaSocio ----------------------------------------------*/		
	
 /*-------------------------------------- Inicio Validar datosFormCuotaSocio ------------------------------------------*/	
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError']='00000';

 $resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['valorCampo']=$arrCamposForm['datosFormCuotaSocio']['CODCUOTAGeneral'];
	$resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['codError']='00000';		

 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo']=
	  $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['codError']='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo']=
	  $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['codError']='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo']=
	  $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['codError']='00000';				

	if ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='General')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']=
										validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'],1000000.00,"");
	}
	elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Joven')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']=
									validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'],1000000.00,"");	
	}
 elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Parado')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']=
									validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'],1000000.00,"");
	}		
	/*-------------------------------------- Fin Validar datosFormCuotaSocio ----------------------------------------*/			
			
	/* ========================================== FIN DATOS NUEVOS ======================================================= */
		
//******************* INICIO VALIDAR DATOS ANTERIORES QUE PUEDEN SER MODIFICADOS AQUÍ *************************
/*----------------------------------- Inicio Validar datosFormDomicilio --------------------------------------------*/
	$resulValidar['datosFormDomicilio']=validarDom($arrCamposForm['datosFormDomicilio']);
	//echo "<br><br>validarCamposSocio validarCamposFormActualizarSocio1-1:resulValidar['datosFormDomicilio']:"; 
	//print_r($resulValidar['datosFormDomicilio']); 
 /*------------------------------------ Fin Validar datosFormDomicilio -------------------------------------------*/
	
 /*--------------------------------- Inicio Validar  datosFormUsuario --------------------------------------------*/	
	$resulValidar['datosFormUsuario']['USUARIO']['valorCampo']=$arrCamposForm['datosFormUsuario']['USUARIO'];
	$resulValidar['datosFormUsuario']['USUARIO']['codError']='00000';

	//echo "<br><br>datosFormUsuario:validar 1 USUARIO: ";print_r($resulValidar['datosFormUsuario']['USUARIO']);		

	/*--------------------------------------- Fin Validar  datosFormUsuario ----------------------------------------*/
 /*----------------------------------- Inicio datosFormPrivacidad -----------------------------------------------*/
	if ($arrCamposForm['datosFormPrivacidad']['privacidad']!=='SI')
	{$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo']='NO';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError']='80200';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje']='debes aceptar la política de 
																																																																						privacidad para guardar el	formulario';	
	}	
	else
	{$resulValidar['datosFormPrivacidad']['privacidad']['valorCampo']='SI';
		$resulValidar['datosFormPrivacidad']['privacidad']['codError']='00000';	
		$resulValidar['datosFormPrivacidad']['privacidad']['errorMensaje']='';
	}		
	/*----------------------------------- Fin datosFormPrivacidad ------------------------------------------------*/
 /*----------------------------------- Inicio datosFormMiembro ------------------------------------------------*/

	$resulValidar['datosFormMiembro']['SEXO']=validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],"");

	$resulValidar['datosFormMiembro']['NOM']=validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");
	$resulValidar['datosFormMiembro']['APE1']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) && $arrCamposForm['datosFormMiembro']['APE2'] !=='')
	{$resulValidar['datosFormMiembro']['APE2']=validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],1,100, "");}

 $resulValidar['datosFormMiembro']['FECHANAC']=validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);

 $resulValidar['datosFormMiembro']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
 $resulValidar['datosFormMiembro']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	
	$resulValidar['datosFormMiembro']['EMAIL']=validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		
			
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS']=='SI')
	{ $infCartas='SI';	}
	else 
	{ $infCartas='NO';	}	
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=$infCartas;
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL']=='SI')
	{ $infEmail='SI';	}
	else 
	{ $infEmail='NO';	}
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=$infEmail;
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';	
	
	$resulValidar['datosFormMiembro']['PROFESION']['valorCampo']=$arrCamposForm['datosFormMiembro']['PROFESION'];
	$resulValidar['datosFormMiembro']['PROFESION']['codError']='00000';	
		
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo']=$arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError']='00000';	
		
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo']=$arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError']='00000';	
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']=$arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'];
	$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError']='00000';		
 /*-------------------------------- Fin datosFormMiembro ------------------------------------------------------*/		

	//echo "<br><br>validarCamposFormSimpAsocio2:"; print_r($resulValidar);
 return $resulValidar;
}
/*---------------- Fin validarCamposFormSimpAsocio ---------------------------*/

?>