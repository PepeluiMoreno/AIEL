<?php
/*-------------------------------------------------------------------------------------------
FICHERO: validarCamposSocioPorGestor.php
														
PROYECTO: Europa Laica
VERSION: PHP 7.3.21
DESCRIPCION: Valida los campos recibidos desde los formularios de dar alta por gestores:
             formAltaSocioPorGestor.php,validarCamposActualizarSocioPorGestor() 
Llamado: cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(),
         cTesorero.php:altaSocioPorGestorTes()
         cAdmin.php:altaSocioPorAdmin(), altaSocioPorGestor()
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales)


-------------------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 

/*---------------- Inicio validarCamposAltaSocioPorGestor() ---------------------------------
DESCRIPCION: Valida los campos de alta de socio, y comprueba existencia
						 en tablas de EMAIL, NUMDOCUMENTOMIEMBRO repetidos
Llamado: desde cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(),
         cTesorero.php:altaSocioPorGestorTes()
        
Llama funciones: validarCamposFormAltaSocioPorGestor()
                 modeloUsuarios.php:buscarNumDoc(),buscarEmail()
OBSERVACIONES: Incluye para Honorario y demas tipos de cuotas, pero sería mejor hacer mas 
               independiente leyendo de la tabla IMPORTECUOTAANIOEL																	
------------------------------------------------------------------------------*/
function validarCamposAltaSocioPorGestor($camposFormRegSocio)
{//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:camposFormRegSocio:";print_r($camposFormRegSocio);

 $resValidarCamposForm = validarCamposFormAltaSocioPorGestor($camposFormRegSocio);
	//echo "<br><br>2validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);

 if ($resValidarCamposForm['datosFormMiembro']['EMAIL']['codError'] =='00000')
 { $resBuscarEmail = buscarEmail($resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo']);
	 
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
	  {$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '80002';
		  $resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']='Ya hay registrado un socio
			 o simpatizante con ese mismo nº documento';	
		 }			 
	  else //$resBuscarNumDoc['numFilas']==0)
		 {	$resValidarCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] = '00000';//no es necesario	 
   }
	 }	
 }	

	$validarErrorSistema['codError']='00000';
	$validarErrorLogico['codError']='00000';
	$validarErrorLogico['errorMensaje']='';
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
 //echo "<br><br>3validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposAltaSocioPorGestor --------------------------------------

/*---------------- Inicio validarCamposFormAltaSocioPorGestor -------------------------------
DESCRIPCION: Valida los campos de formulario de alta de socio por gestor      

LLAMADA: validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor()
LLAMA: modelos/libs/validarCampos.php (varias funciones) y 
       modeloUsuarios.php:validarPaisSEPA()
--------------------------------------------------------------------------------------------------*/
function validarCamposFormAltaSocioPorGestor($arrCamposForm)
{
	//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:arrCamposForm:";print_r($arrCamposForm);
 
	/*-------------------------------------- Inicio Validar datosFormDomicilio ---------------------------------------------*/
 $resulValidar['datosFormDomicilio'] = validarDom($arrCamposForm['datosFormDomicilio']);
	//echo "<br><br>2-1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormDomicilio']:"; 
	//print_r($resulValidar['datosFormDomicilio']); 
  /*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/
	
 /*-------------------------------------- Inicio Validar datosFormSocio ----------------------------------------------*/	

	/*-------------------------------------- Inicio Validar Agrupación ---------------------------*/
 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
	$resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] ='00000';	
	
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
	
	//Ya no se admiten	CUENTANOIBAN: Actualmente solo admitimos cuenta IBAN, lo dejo por si alguna vez cambiásemos y aceptásemos CUENTANOIBAN
	//$resulValidar['datosFormSocio']['CUENTANOIBAN'] = validarCuentaNoIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);																																	
	//$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarCuentaIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN'],$arrCamposForm['datosFormSocio']['CUENTANOIBAN']);
	
	//echo "<br><br>2-3-1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:arrCamposForm['datosFormSocio']['CUENTAIBAN']: ";print_r($arrCamposForm['datosFormSocio']['CUENTAIBAN']);		
		 
	if (isset($arrCamposForm['datosFormSocio']['CUENTAIBAN'])) 
	{$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN']);
  
  //echo "<br><br>2-3-2 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);		
		
		if ($resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] === '00000')
		{    
				$resulValidarPaisSEPA	= validarPaisSEPA($resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo']);//en modeloUsuarios.php
				//si no es un país SEPA, devuelve error "80001" aunque tenga una cuenta IBAN válida, pues no se pueden domiciliar países NO SEPA	  
			 //echo "<br><br>2-3-3 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);				
				
    if ($resulValidarPaisSEPA['codError'] !== '00000')
				{ 
			   $resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] = $resulValidarPaisSEPA['errorMensaje'];// = '80001';
    		$resulValidar['datosFormSocio']['CUENTAIBAN']['errorMensaje'] = $resulValidarPaisSEPA['errorMensaje'];//= 'Error: cuenta banco, no permitida, no pertenece a un país SEPA'; 						
				}
		
    //echo "<br><br>2-3-4 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:validarCamposFormAltasSocio['datosFormSocio']['CUENTAIBAN']:-";var_dump($resulValidar['datosFormSocio']['CUENTAIBAN']);echo "-";				
		}		
 }	
	//echo "<br><br>2-3-5 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);
	
	/*------------------- Fin Validar CUENTAIBAN (solo se admiten países SEPA) -------------------*/
	
	/*-------------------------------------- Fin Validar datosFormSocio -------------------------------------------------*/	
	
 /*-------------------------------------- Inicio Validar datosFormCuotaSocio ------------------------------------------*/	
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError'] ='00000';

 $resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['CODCUOTAGeneral'];
	$resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['codError'] ='00000';		

 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['codError'] ='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['codError'] ='00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['codError'] ='00000';				
	
	if (isset($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']))//solo rol de presidencia asigna honorario
	{	
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'];
	} 		
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['codError'] ='00000';			

	if ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='General')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'],1000000.00,"");
	}
	elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Joven')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] =	validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'],1000000.00,"");	
	}
 elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Parado')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'],1000000.00,"");	
	}
	elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] =='Honorario')
	{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
										                                                                                $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'],1000000.00,"");	
	}		
	/*-------------------------------------- Fin Validar datosFormCuotaSocio ----------------------------------------------*/		
	 
 /*----------------------------------- Inicio datosFormMiembro -----------------------------------------------*/
	
	/*-------------------------------- Inicio Validar documento NIF, NIE, Pasaporte ------------------------------*/
	//-- las 6 siguientes líneas son para guardar vlores originales cuando hay error ¡no se pueden eliminar! ----
 $resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo'] = $arrCamposForm['datosFormMiembro']['CODPAISDOC'];	
	$resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='00000';

	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError'] ='00000';
	
 $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];	
	$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] ='00000';	
	//--------- fin 6 líneas -------------------------------------------------------------------------------------
	if ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Pasaporte' || $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Otros')   	
	{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNumPasaporte($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'],1,100,"");
	}
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'] =='NIF')//NIF	
 {if ($arrCamposForm['datosFormMiembro']['CODPAISDOC'] =='ES')
	 { $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNif($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
  }
		else
	 {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];
		 $resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje'] ='Solo puedes elegir NIF si el país es España';
	  $resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='80303';	
	 }
	}	 
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'] =='NIE')	//NIE
 {if ($arrCamposForm['datosFormMiembro']['CODPAISDOC'] =='ES')
  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNie($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);		
  }
	 else
  {$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];		
		 $resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje'] ='Solo puedes elegir NIE si el país es España';
   $resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='80303';
  }	 
	}
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte ------------------------------*/
	
	//$resulValidar['datosFormMiembro']['SEXO'] = validarCampoRadio($arrCamposForm['datosFormMiembro']['SEXO'],"");php7 emite Notice
				
	if (isset($arrCamposForm['datosFormMiembro']['SEXO']) || !empty($arrCamposForm['datosFormMiembro']['SEXO']))
	{ $resulValidar['datosFormMiembro']['SEXO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['SEXO'];	
			$resulValidar['datosFormMiembro']['SEXO']['codError'] = '00000';
	}
	else
	{$resulValidar['datosFormMiembro']['SEXO']['codError'] ='80201';
		$resulValidar['datosFormMiembro']['SEXO']['errorMensaje'] = "Sexo: debes elegir una opción";	
		$resulValidar['datosFormMiembro']['SEXO']['valorCampo'] = '';
	}	
	
	$resulValidar['datosFormMiembro']['NOM'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['NOM'],1,100, "");
	$resulValidar['datosFormMiembro']['APE1'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE1'],1,100, "");
	
	/*Antes de 2019-12-12 lo dejo si da poblemas
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
 
	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM'] =='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}
	
	if ($arrCamposForm['datosFormMiembro']['EMAILERROR'] == 'NO')//si no tiene email o se ve error formato es FALTA
	{
		$resulValidar['datosFormMiembro']['EMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['EMAIL'],"");		
		$resulValidar['datosFormMiembro']['REMAIL'] = validarEmail($arrCamposForm['datosFormMiembro']['REMAIL'],"");	
		
		if ($arrCamposForm['datosFormMiembro']['EMAIL'] !== $arrCamposForm['datosFormMiembro']['REMAIL'])
		{$resulValidar['datosFormMiembro']['EMAIL']['codError'] = '80430';
			$resulValidar['datosFormMiembro']['EMAIL']['errorMensaje']= 'Los dos email son diferentes';
			$resulValidar['datosFormMiembro']['REMAIL']['codError'] = '80430';
			$resulValidar['datosFormMiembro']['REMAIL']['errorMensaje'] = 'Las dos email son diferentes';
		}
	}
	else //añado par evitar algún warning
	{ $resulValidar['datosFormMiembro']['EMAIL']['valorCampo'] = "";//$arrCamposForm['datosFormMiembro']['EMAIL'];
   $resulValidar['datosFormMiembro']['EMAIL']['codError'] ='00000';		
	}	
 $resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo'] = $arrCamposForm['datosFormMiembro']['EMAILERROR'];
 $resulValidar['datosFormMiembro']['EMAILERROR']['codError'] ='00000';		
	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] =='SI')
	{ $infEmail='SI';	}//aunque falte por si luego lo envía
	else 
	{ $infEmail='NO';	}
	
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] =$infEmail;
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError'] ='00000';
 //considero que al darse de alta un usuario, despues de validado el formato, el email será correcto
	//El presidente, coordinador, secretario, podrían anotorlo como devuelto, en caso de que eso suceda 
	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS'] == 'SI')
	{ $infCartas = 'SI';	}
	else 
	{ $infCartas = 'NO';	}	
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] =$infCartas;
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError'] ='00000';

	if (isset($arrCamposForm['datosFormMiembro']['PROFESION']) && !empty($arrCamposForm['datosFormMiembro']['PROFESION']))
	{$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['PROFESION']['valorCampo'] = $arrCamposForm['datosFormMiembro']['PROFESION'];
		$resulValidar['datosFormMiembro']['PROFESION']['codError'] = '00000';	
 }	
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo'] = $arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError'] = '00000';	
	
	$resulValidar['datosFormMiembro']['COLABORA']['valorCampo'] = $arrCamposForm['datosFormMiembro']['COLABORA'];
	$resulValidar['datosFormMiembro']['COLABORA']['codError'] = '00000';	
	
	if (isset($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']) && !empty($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']))
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'];
		$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError']='00000';
 }
	
	if (isset($arrCamposForm['datosFormMiembro']['OBSERVACIONES']) && !empty($arrCamposForm['datosFormMiembro']['OBSERVACIONES']))
	{$resulValidar['datosFormMiembro']['OBSERVACIONES'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['OBSERVACIONES'],3,255,"");	
 }	
 else
	{$resulValidar['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = $arrCamposForm['datosFormMiembro']['OBSERVACIONES'];
	 $resulValidar['datosFormMiembro']['OBSERVACIONES']['codError'] = '00000';
 }	
	
 /*------------------------------------------ Fin datosFormMiembro ------------------------------------------------------*/
	//echo "<br><br>4 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar:";print_r($resulValidar);echo "<br>";
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormAltaSocioPorGestorBien -----------------

/*---------------- Inicio validarCamposActualizarSocioPorGestor() ---------------------------
Valida los campos de actualizar socio por un gestor, y comprueba existencia
en tablas de usuario, email, doc

LLAMADO: cCoordinador.php:actualizarSocioCoord(),cPresidente.php:actualizarSocioPres(),

LLAMA: validarCamposSocio.php:validarCamposActualizarSocio() y dentro
validarCamposFormActualizarSocio()
modeloUsuarios.php:buscarUsuario(),buscarEmail(), buscarNumDoc()
																	
OBSERVACIONES:
2020-04-14: Creo está función para hacerla independiente, al menos en parte, del 
anterior uso con solo con validarCamposSocio.php:validarCamposActualizarSocio(),
ya que los gestores podrían actuar sobre algún campo vetado a socios, 
ejemplo: ['datosFormMiembro']['OBSERVACIONES'] que solo es para gestores.
Reutilizo la función validarCamposSocio.php:validarCamposActualizarSocio()
y añado validación de campo ['datosFormMiembro']['OBSERVACIONES']
NOTA: si se necesitase más independencia de validarCamposActualizarSocio() 
se podría hacer una función totalmente autónoma.															
------------------------------------------------------------------------------*/
function validarCamposActualizarSocioPorGestor($camposFormRegSocio)
{ 
 //echo "<br><br>0 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:camposFormRegSocio: ";print_r($camposFormRegSocio);

	require_once './modelos/libs/validarCamposSocio.php';	
	$resValidarCampos = validarCamposActualizarSocio($camposFormRegSocio);//socio
	
 //echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos: ";print_r($resValidarCampos);
	
	if (isset($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']) && !empty($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']))
	{$resulValidar['datosFormMiembro']['OBSERVACIONES'] = validarCampoTexto($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES'],3,255,"");			
 }	
 else
	{$resulValidar['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES'];
	 $resulValidar['datosFormMiembro']['OBSERVACIONES']['codError'] = '00000'; 
 }	

	if ($resulValidar['datosFormMiembro']['OBSERVACIONES']['codError'] !=='00000')
	{ 
  $resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCampos['errorMensaje'] .=". ".$resulValidar['datosFormMiembro']['OBSERVACIONES']['errorMensaje'];//concatenación errorMensaje
		$resValidarCampos['totalErrores'] += $resValidarCampos['totalErrores'];	
  //echo "<br><br>4-5 actualizarSocio:validarCamposActualizarSocio:resValidarCampos";print_r($resValidarCampos);
	}
	
	//echo "<br><br>2 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos: ";print_r($resValidarCampos);	
	
 $resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES'] = $resulValidar['datosFormMiembro']['OBSERVACIONES'];//los campos que se validan 

	/*-------- Además también se devuelven estos valores desde la función validarCamposSocio.php:validarCamposActualizarSocio() -----*/
 //$resValidarCampos['campoActualizar']['datosCuotasEL'] = $camposFormRegSocio['campoActualizar']['datosCuotasEL'];//no se validan, pero se usan en validaciones
	//$resValidarCampos['campoHide'] = $camposFormRegSocio['campoHide'];//campos que hidden,se pasan sin validar
	//$resValidarCampos['campoVerAnioActual'] = $camposFormRegSocio['campoVerAnioActual'];//campos que hidden,se pasan sin validar	
	
 //echo "<br><br>3 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos: ";print_r($resValidarCampos);			

	return $resValidarCampos; //incluye arrayMensaje
}

/*---------------- Inicio validarCamposFormAltaCoordArea ------------------------------------
DESCRIPCION:Valida los campos de formulario de asignación de un área de gestión a un 
            coordinador           
Llamado: cPresidente:asignarCoordinacionArea()
Llama funciones:
--------------------------------------------------------------------------------------------*/
function validarCamposFormAltaCoordArea($arrCamposForm)
{//echo "<br><br>1validarCamposSocioPorGestor.php:validarCamposFormAltaCoordArea:arrCamposForm:";print_r($arrCamposForm);
	
	if (isset($arrCamposForm['datosFormSocio']['CODAREAGESTIONAGRUP']) && !empty($arrCamposForm['datosFormSocio']['CODAREAGESTIONAGRUP'])
	    && $arrCamposForm['datosFormSocio']['CODAREAGESTIONAGRUP']!=='Elegir' )
	{$resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['valorCampo']=$arrCamposForm['datosFormSocio']['CODAREAGESTIONAGRUP'];
	 $resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['errorMensaje']='';
  $resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['codError']='00000';
	}
	else
 {$resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['valorCampo']=$arrCamposForm['datosFormSocio']['CODAREAGESTIONAGRUP'];		
	 $resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['errorMensaje']='Debes elegir un área de gestión';
  $resulValidar['datosFormSocio']['CODAREAGESTIONAGRUP']['codError']='80201';
 }	 

	//echo "<br><br>2validarCamposSocioPorGestor.php:validarCamposFormAltaCoordArea:resulValidar:";print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarCamposFormAltaCoordArea --------------------------

/*---------------- Inicio validarCamposFormBuscarGestorApesNom() ----------------------------
DESCRIPCION: Valida los campos APE1, APE2, NOM introducidos en el formulario de entrada 
             para después búscar los datos personales del socio en la tabla MIEMBRO, 
													Actualmen se utliza para posterior asignación-eliminacion de los roles
             de Coordinación, Presidencia (), y Tesorería		
													
LLAMADA: cPresidente:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar()
         asignarTesoreríaRolBuscar()

LLAMA: modelos/libs/validarCampos.php:validarCampoNoVacio()

NOTA: no se validan caracteres dentro de campos, poco riesgo injection al utilizar Gestores
-------------------------------------------------------------------------------------------*/
function validarCamposFormBuscarGestorApesNom($arrCamposForm)
{
	//echo "<br><br>0-1 validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom:arrCamposForm:";print_r($arrCamposForm);
	
	$resulValidar['datosFormSocio']['codError'] ='00000';
	$resulValidar['datosFormSocio']['errorMensaje'] ='';
 $textoErrorCampo = 'Campos apellidos y nombre vacíos';

 $resulValidar['datosFormSocio']['APE1']=	validarCampoNoVacio($arrCamposForm['datosFormSocio']['APE1'],$textoErrorCampo);			
 $resulValidar['datosFormSocio']['APE2']=	validarCampoNoVacio($arrCamposForm['datosFormSocio']['APE2'],$textoErrorCampo);			
	$resulValidar['datosFormSocio']['NOM'] =	validarCampoNoVacio($arrCamposForm['datosFormSocio']['NOM'],$textoErrorCampo);			
 
	if ($resulValidar['datosFormSocio']['APE1']['codError']!=='00000' && $resulValidar['datosFormSocio']['APE2']['codError']!=='00000'	&& $resulValidar['datosFormSocio']['NOM']['codError']!=='00000'	)	
	{
		//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom:resulValidar:";print_r($resulValidar);
		$resulValidar['datosFormSocio']['codError']='80201';
		$resulValidar['datosFormSocio']['errorMensaje']='Error:  campos de apellidos y nombre vacíos. Debes introducir al menos un apellido';		
	 $resulValidar['datosFormSocio']['APE1']['codError']='00000';
	 $resulValidar['datosFormSocio']['APE1']['errorMensaje']='';
	 $resulValidar['datosFormSocio']['APE2']['codError']='00000';
		$resulValidar['datosFormSocio']['APE2']['errorMensaje']='';		
	}
	else
	{//echo "<br><br>2 validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom:resulValidar:";print_r($resulValidar);
	 $resulValidar['datosFormSocio']['APE1']['codError']='00000';
	 $resulValidar['datosFormSocio']['APE1']['errorMensaje']='';
	 $resulValidar['datosFormSocio']['APE2']['codError']='00000';
		$resulValidar['datosFormSocio']['APE2']['errorMensaje']='';
		$resulValidar['datosFormSocio']['NOM']['codError']='00000';
		$resulValidar['datosFormSocio']['NOM']['errorMensaje']='';
			
		//echo "<br><br>3 validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom:resulValidar:";print_r($resulValidar);
	}	
 //echo "<br><br>4 validarCamposSocioPorGestor.php:validarCamposFormBuscarGestorApesNom:resulValidar:";print_r($resulValidar);
	
	return $resulValidar;
}
//----------------------------- Fin validarCamposFormBuscarGestorApesNom() --------------------
 


?>