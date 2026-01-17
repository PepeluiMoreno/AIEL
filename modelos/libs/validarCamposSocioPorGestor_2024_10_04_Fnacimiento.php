<?php
/*----------------------------------------------------------------------------------------------------------------------
FICHERO: validarCamposSocioPorGestor.php
														
PROYECTO: Europa Laica
VERSION: PHP 7.3.21
DESCRIPCION: Valida los campos recibidos desde los formularios de dar alta por gestores:
             formAltaSocioPorGestor.php, validarCamposActualizarSocioPorGestor(),
													
													También funciones relacionadas asignación de roles desde presidencia:
													cPresidente:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreríaRolBuscar().
													
Llamado: cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(),
         cTesorero.php:altaSocioPorGestorTes()
									cCoordinador.php:actualizarSocioCoord(),cPresidente.php:actualizarSocioPres(),
									
         cPresidente:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreríaRolBuscar().									
									
Llama: ./modelos/libs/validarCampos.php (librería de validaciones generales) y otras de usuarios

OBSERVACIONES: 2023-02-02 Modificaciones en validarCamposFormAltaSocioPorGestor()

----------------------------------------------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 

/*---------------- Inicio validarCamposAltaSocioPorGestor() ------------------------------------------------------------
DESCRIPCION: Valida campos de alta de socio, y comprueba existencia en tablas de EMAIL, NUMDOCUMENTOMIEMBRO repetidos
Llamado: desde cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(),
         cTesorero.php:altaSocioPorGestorTes()
        
Llama funciones: validarCamposFormAltaSocioPorGestor(), modeloUsuarios.php:buscarNumDoc(),buscarEmail()

OBSERVACIONES: Incluye para Honorario y demas tipos de cuotas, pero sería mejor hacer mas 
               independiente leyendo de la tabla IMPORTECUOTAANIOEL																	
----------------------------------------------------------------------------------------------------------------------*/
function validarCamposAltaSocioPorGestor($camposFormRegSocio)
{
	//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:camposFormRegSocio:";print_r($camposFormRegSocio);

 $resValidarCamposForm = validarCamposFormAltaSocioPorGestor($camposFormRegSocio);
	//echo "<br><br>2 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);

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
 {$resBuscarNumDoc = buscarNumDoc($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
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
	
	//echo "<br><br>3-0 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)
	{ 
	  //echo "<br><br>3-1 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:[$grupo]: ";print_r($resValidarCamposForm[$grupo]);
	
	  foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
			 //echo "<br><br>3-2 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:[$grupo][$nomCampo]: ";print_r($resValidarCamposForm[$grupo][$nomCampo]);
				
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')
		  {			
					if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] < '80000')							
					{ //echo "<br><br>3-2a validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:[$grupo][$nomCampo]: ";print_r($resValidarCamposForm[$grupo][$nomCampo]);
				   $validarErrorSistema['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
					  $validarErrorSistema['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
					  $totalErroresSistema +=1; 
						 break 2; //
					}
					else //$resValidarCamposForm[$grupo][$nomCampo]['codError'] >= '80000'
					{ //echo "<br><br>3-2b validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:[$grupo][$nomCampo]: ";print_r($resValidarCamposForm[$grupo][$nomCampo]);
					  $validarErrorLogico['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
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
 //echo "<br><br>4 validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor:resValidarCamposForm:";print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
/*---------------- Fin validarCamposAltaSocioPorGestor ---------------------------------------------------------------*/

/*---------------- Inicio validarCamposFormAltaSocioPorGestor ----------------------------------------------------------
DESCRIPCION: Valida los campos de formulario de alta de socio por gestor      

LLAMADA: validarCamposSocioPorGestor.php:validarCamposAltaSocioPorGestor()
LLAMA: modelos/libs/validarCampos.php (varias funciones) y 
       modeloUsuarios.php:validarPaisSEPA()
							
OBSERVACIONES: 2023-02-02 Modificaciones

-FECHANAC para que año nacimiento sea obligatoria en alta y Actualizar datos socio/a por socio, pero no por gestores
-Cambios validar IMPORTECUOTAANIOSOCIO si superior mínimo de General, CODCUOTA = General 
-COLABARA = NULL, COMENTARIOSOCIO aumento a 500
							
----------------------------------------------------------------------------------------------------------------------*/
function validarCamposFormAltaSocioPorGestor($arrCamposForm)
{
	//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:arrCamposForm: ";print_r($arrCamposForm);	
	 
 /*-------------------------------- Inicio datosFormMiembro ----------------------------------------------------------*/
	
	/*-------------------------------- Inicio Validar documento NIF, NIE, Pasaporte, otros ------------------------------*/
	
	//-- las 6 siguientes líneas son para guardar vlores originales cuando hay error ¡no se pueden eliminar! ----
 $resulValidar['datosFormMiembro']['CODPAISDOC']['valorCampo'] = $arrCamposForm['datosFormMiembro']['CODPAISDOC'];	
	$resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='00000';

	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
	$resulValidar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['codError'] ='00000';
	
 $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];	
	$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['codError'] ='00000';	
	//--------- fin 6 líneas -------------------------------------------------------------------------------------
	
 /*----------------------------- Inicio Validar documento NIF, NIE, Pasaporte ----------------------------------------*/	
	if ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Pasaporte' || $arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']=='Otros')   	
	{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNumPasaporte($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'],1,100,"");
	}
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'] =='NIF')
 {
			if ($arrCamposForm['datosFormMiembro']['CODPAISDOC'] =='ES')
			{ $resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNif($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
			}
			else
			{$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];
				$resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje'] ='Solo puedes elegir NIF si el país es España';
				$resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='80303';	
			}
	}	 
	elseif ($arrCamposForm['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'] =='NIE')	//NIE
 {
			if ($arrCamposForm['datosFormMiembro']['CODPAISDOC'] =='ES')
			{$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'] = validarNie($arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);		
			}
			else
			{$resulValidar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];		
				$resulValidar['datosFormMiembro']['CODPAISDOC']['errorMensaje'] ='Solo puedes elegir NIE si el país es España';
				$resulValidar['datosFormMiembro']['CODPAISDOC']['codError'] ='80303';
			}	 
	}
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte -------------------------------------------*/
				
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
	
	if (isset($arrCamposForm['datosFormMiembro']['APE2']) /*&& $arrCamposForm['datosFormMiembro']['APE2'] !==''*/)
	{$resulValidar['datosFormMiembro']['APE2'] = validarCampoNombres($arrCamposForm['datosFormMiembro']['APE2'],0,100, "");}

 //-- Desde 2023 para obligar a los "socios" a poner año de nacimiento, Pero NO para los gestores que no conocen -------
 //$resulValidar['datosFormMiembro']['FECHANAC'] = validarFecha($arrCamposForm['datosFormMiembro']['FECHANAC']);

	$resulValidar['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] = '00';//para compatibilidad con anteriores validaciones
	$resulValidar['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] = '00';//para compatibilidad con anteriores validaciones
	
	//$resulValidar['datosFormMiembro']['FECHANAC']['anio'] = validarNumeroEntero($arrCamposForm['datosFormMiembro']['FECHANAC']['anio'],date("Y")-110,date("Y")-15,true);	//permite vacío
 $resulValidar['datosFormMiembro']['FECHANAC']['anio'] = validarNumeroEntero($arrCamposForm['datosFormMiembro']['FECHANAC']['anio'],date("Y")-110,date("Y")-15,false);	
	
	$resulValidar['datosFormMiembro']['FECHANAC']['codError'] = $resulValidar['datosFormMiembro']['FECHANAC']['anio']['codError'];
	$resulValidar['datosFormMiembro']['FECHANAC']['errorMensaje'] = $resulValidar['datosFormMiembro']['FECHANAC']['anio']['errorMensaje'];			
 //echo "<br><br>2-1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormMiembro']['FECHANAC']: "; print_r($resulValidar['datosFormMiembro']['FECHANAC']);	

	if (isset($arrCamposForm['datosFormDomicilio']['CODPAISDOM']) && $arrCamposForm['datosFormDomicilio']['CODPAISDOM'] =='ES')
 {$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,11,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,11,"");	
	} 
	else
	{$resulValidar['datosFormMiembro']['TELFIJOCASA'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELFIJOCASA'],9,14,"");
  $resulValidar['datosFormMiembro']['TELMOVIL'] = validarTelefono($arrCamposForm['datosFormMiembro']['TELMOVIL'],9,14,"");	
	}
	
 /*----------------------------- Inicio Validar EMAIL ----------------------------------------------------------------*/			
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
   $resulValidar['datosFormMiembro']['EMAIL']['codError'] = '00000';		
	}	
 $resulValidar['datosFormMiembro']['EMAILERROR']['valorCampo'] = $arrCamposForm['datosFormMiembro']['EMAILERROR'];
 $resulValidar['datosFormMiembro']['EMAILERROR']['codError'] = '00000';		

	//En el caso de alta por Gestor, puede que el socio no tenga email en ese caso vendrá del formulario "FALTA "	
	//if (isset($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] ) && $arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] =='SI' ) //
	if (isset($arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] ) && $arrCamposForm['datosFormMiembro']['INFORMACIONEMAIL'] =='SI' 
	    && $arrCamposForm['datosFormMiembro']['EMAILERROR'] !== 'FALTA' )
	{ $infEmail='SI';	}
	else 
	{ $infEmail='NO';	}
	
 $resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = $infEmail;
	$resulValidar['datosFormMiembro']['INFORMACIONEMAIL']['codError'] = '00000';
	
 /*----------------------------- Fin Validar EMAIL -------------------------------------------------------------------*/			
	
	if ($arrCamposForm['datosFormMiembro']['INFORMACIONCARTAS'] == 'SI')
	{ $infCartas = 'SI';	}
	else 
	{ $infCartas = 'NO';	}	

	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] = $infCartas;
	$resulValidar['datosFormMiembro']['INFORMACIONCARTAS']['codError'] = '00000';	

	if (isset($arrCamposForm['datosFormMiembro']['PROFESION']) && !empty($arrCamposForm['datosFormMiembro']['PROFESION']))
	{$resulValidar['datosFormMiembro']['PROFESION'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['PROFESION'],3,255,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['PROFESION']['valorCampo'] = $arrCamposForm['datosFormMiembro']['PROFESION'];
		$resulValidar['datosFormMiembro']['PROFESION']['codError'] = '00000';	
 }	
	$resulValidar['datosFormMiembro']['ESTUDIOS']['valorCampo'] = $arrCamposForm['datosFormMiembro']['ESTUDIOS'];
	$resulValidar['datosFormMiembro']['ESTUDIOS']['codError'] = '00000';		

	/*-------------------------------------- Inicio Validar COLABORA ----------------------------------------------------*/
	/*2022-12-20: Cambio: se quita campo colabora del formulario y se envía un comentario colaborar en el email confirmación de alta
	 se deja el campo COLABORA en la BBDD por eso dejo el siguiente formato por si se vuelve a poner --------------------*/	
	
	if (isset($arrCamposForm['datosFormMiembro']['COLABORA']) && !empty($arrCamposForm['datosFormMiembro']['COLABORA']))
	{	 
		//$resulValidar['datosFormMiembro']['COLABORA']['valorCampo']=$arrCamposForm['datosFormMiembro']['COLABORA'];// si se vuelve a poner
		$resulValidar['datosFormMiembro']['COLABORA']['valorCampo'] = NULL;//  Quitar si se vuelve a poner
	 $resulValidar['datosFormMiembro']['COLABORA']['codError'] = '00000';//	Quitar si se vuelve a poner
	}
	else
	{$resulValidar['datosFormMiembro']['COLABORA']['valorCampo'] = NULL;
		$resulValidar['datosFormMiembro']['COLABORA']['codError'] = '00000';	
 }
	//echo "<br><br>3 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormMiembro']['COLABORA']: "; print_r($resulValidar['datosFormMiembro']['COLABORA']);	
	/*-------------------------------------- Fin Validar COLABORA -------------------------------------------------------*/

	/*-------------------------------------- Inicio Validar COMENTARIOSOCIO ---------------------------------------------*/	
	if (isset($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']) && !empty($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO']))
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['COMENTARIOSOCIO'],3,500,"");
 }	
 else
	{$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'] = "";
		$resulValidar['datosFormMiembro']['COMENTARIOSOCIO']['codError'] = '00000';
 }	
	/*-------------------------------------- Fin Validar COMENTARIOSOCIO ------------------------------------------------*/
	
	/*-------------------------------------- Inicio Validar OBSERVACIONES del Gestor ------------------------------------*/	
	if (isset($arrCamposForm['datosFormMiembro']['OBSERVACIONES']) && !empty($arrCamposForm['datosFormMiembro']['OBSERVACIONES']))
	{$resulValidar['datosFormMiembro']['OBSERVACIONES'] = validarCampoTexto($arrCamposForm['datosFormMiembro']['OBSERVACIONES'],3,2000,"");	
 }	
 else
	{$resulValidar['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = "";
	 $resulValidar['datosFormMiembro']['OBSERVACIONES']['codError'] = '00000';
 }		
	/*-------------------------------------- Fin Validar OBSERVACIONES del Gestor ---------------------------------------*/		
	
 /*-------------------------------------- Fin datosFormMiembro -------------------------------------------------------*/	
 
	/*-------------------------------------- Inicio Validar datosFormDomicilio ------------------------------------------*/
 $resulValidar['datosFormDomicilio'] = validarDom($arrCamposForm['datosFormDomicilio']);
	//echo "<br><br>2-1 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormDomicilio']:";print_r($resulValidar['datosFormDomicilio']); 
 /*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/
	
 /*-------------------------------------- Inicio Validar datosFormSocio ----------------------------------------------*/
	
	
	/*-------------------------------------- Inicio Validar Agrupación --------------------------------------------------*/
 $resulValidar['datosFormSocio']['CODAGRUPACION']['valorCampo'] = $arrCamposForm['datosFormSocio']['CODAGRUPACION'];
	$resulValidar['datosFormSocio']['CODAGRUPACION']['codError'] = '00000';	
	
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
   $resulValidar['datosFormSocio']['CODAGRUPACION']['errorMensaje'] =' debes elegir una agrupación';	
	}
	/*-------------------------------------- Fin Validar Agrupación -----------------------------------------------------*/		
	
	/*------------------- Inicio Validar CUENTAIBAN (solo se admiten países SEPA) ---------------------------------------*/
	
	//echo "<br><br>2-2 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:arrCamposForm['datosFormSocio']['CUENTAIBAN']: ";print_r($arrCamposForm['datosFormSocio']['CUENTAIBAN']);		
	
	//if (isset($arrCamposForm['datosFormSocio']['CUENTAIBAN']) && !empty($arrCamposForm['datosFormSocio']['CUENTAIBAN']) ) 
/**** OJO: por ahora a Honorarios no se admite Pagar Cuota y por lo tanto domiciliar 	*/
	if (isset($arrCamposForm['datosFormSocio']['CUENTAIBAN']) && !empty($arrCamposForm['datosFormSocio']['CUENTAIBAN']) && $arrCamposForm['datosFormSocio']['CODCUOTA'] !== 'Honorario') 		
	{
			$resulValidar['datosFormSocio']['CUENTAIBAN']	= validarIBAN($arrCamposForm['datosFormSocio']['CUENTAIBAN']);

			if ($resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] === '00000' && !empty($resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo']) )			
			{    
					$resulValidarPaisSEPA	= validarPaisSEPA($resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo']);//en modeloUsuarios.php
					
					//si No es un país SEPA, devuelve error "80001" aunque tenga una cuenta IBAN válida, pues no se pueden domiciliar países NO SEPA	  

					if ($resulValidarPaisSEPA['codError'] !== '00000')
					{ 
							$resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] = $resulValidarPaisSEPA['errorMensaje'];// = '80001';
							$resulValidar['datosFormSocio']['CUENTAIBAN']['errorMensaje'] = $resulValidarPaisSEPA['errorMensaje'];//= 'Error: cuenta banco, no permitida, no pertenece a un país SEPA'; 						
					}
			}
 }	
	else
	{ $resulValidar['datosFormSocio']['CUENTAIBAN']['codError'] = '00000';
   $resulValidar['datosFormSocio']['CUENTAIBAN']['errorMensaje'] = '';
   $resulValidar['datosFormSocio']['CUENTAIBAN']['valorCampo'] = NULL;	
	}	
	//echo "<br><br>2-3 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormSocio']['CUENTAIBAN']: ";print_r($resulValidar['datosFormSocio']['CUENTAIBAN']);
	
	/*------------------- Fin Validar CUENTAIBAN (solo se admiten países SEPA) ------------------------------------------*/	

	/*-------------------------------------- Inicio Validar CODCUOTA (General, Joven, Parado/dificultades, Honorario) ---*/			
	
	$resulValidar['datosFormSocio']['CODCUOTA'] = validarCampoRadio($arrCamposForm['datosFormSocio']['CODCUOTA'],"");	
	
	//echo "<br><br>3-1 validarCamposSocioPorGestor:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormSocio']['CODCUOTA']: ";print_r($resulValidar['datosFormSocio']['CODCUOTA']);		
		
	/*-------------------------------------- Fin Validar CODCUOTA (General, Joven, Parado/dificultades, Honorario) ------*/				
	
	/*-------------------------------------- Fin Validar datosFormSocio -------------------------------------------------*/	
	
	
 /*-------------------------------------- Inicio Validar datosFormCuotaSocio -----------------------------------------*/	
	
 /*-------------------------------------- Inicio Validar IMPORTECUOTAANIOSOCIO y relacionados ------------------------*/	
	
 //echo "<br><br>4-1a validarCamposSocioPorGestor:validarCamposFormAltaSocioPorGestor:arrCamposForm['datosFormCuotaSocio']: "; print_r($arrCamposForm['datosFormCuotaSocio']);		
	
	//------- Inicio: para mostrar en formulario y validar, posibilidad de mejora --------------------
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['ANIOCUOTA'];
	$resulValidar['datosFormCuotaSocio']['ANIOCUOTA']['codError'] = '00000';

 $resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['CODCUOTAGeneral'];
	$resulValidar['datosFormCuotaSocio']['CODCUOTAGeneral']['codError'] = '00000';		
	

 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['codError'] = '00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['codError'] = '00000';	
	
 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'];
	$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['codError'] = '00000';	

	if (isset($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']))//solo rol de presidencia y tesorería asigna honorario
	{	
	 $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'];
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['codError'] = '00000';		
	} 
 //echo "<br><br>4-1b validarCamposSocioPorGestor:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormCuotaSocio']: "; print_r($resulValidar['datosFormCuotaSocio']);	
	//------- Fin: para mostrar en formulario y validar, posibilidad de mejora -----------------------	

	/*-------------------------------------- Inicio Validar IMPORTECUOTAANIOSOCIO y relacionados ------------------------*/		

	if ( isset($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']) && !empty($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']))	
	{ 
		$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],0,10000.00,"Cuota no válida,  ");

		if ( $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['codError'] === '00000' )
  {		
			if ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'General')
			{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
																																																																																												$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'],10000.00,"Has marcado General,  ");
			}
			else //if ($arrCamposForm['datosFormSocio']['CODCUOTA'] !== 'General')
			{  	
				/*otra opcion: if ( $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] >= $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'] && 
									$arrCamposForm['datosFormSocio']['CODCUOTA'] !== 'Honorario')	*/
    if ( $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] >= $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'] && 
			     	$arrCamposForm['datosFormSocio']['CODCUOTA'] !== 'Honorario')														
				{ 
						$resulValidar['datosFormSocio']['CODCUOTA']['valorCampo']  = 'General';//Por ser IMPORTECUOTAANIOSOCIO Igual o superior a General
						$resulValidar['datosFormSocio']['CODCUOTA']['codError'] = '00000';					
				}				
				elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Joven')
				{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] =	validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
																																																																																											 	$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'],10000.00,"Has marcado Joven,  ");	
				}
				elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Parado')
				{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
																																																																																													$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'],10000.00,"Has marcado Parado o dificultades económicas,  ");	
				}
				elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Honorario')
				{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'],
																																																																																													$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'],1000000.00,"");	
					//echo "<br><br>4-2a validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormCuotaSocio']: ";print_r($resulValidar['datosFormCuotaSocio']);				
									
					$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] =	$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'];//= 0;por ahora exentos cuota, solo donan				
					$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'EXENTO';
					
  /* ***** OJO ***  Descomentar para que socios puedan pagar cuota.
					if ($resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] ==	0 )			
					{ $resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'EXENTO';		}							
					elseif ($resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] >	0 )							
					{ $resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';		}//¿¿¿en modeloPresCoor.php por ahora lo pone IMPORTECUOTAANIOSOCIO = 0 y EXENTO 
					*/
					$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['codError'] = '00000';	
     $resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['errorMensaje'] = '';					
					//echo "<br><br>4-2b validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormCuotaSocio']: ";print_r($resulValidar['datosFormCuotaSocio']); 																																																																																																																																																																																																																							
				}				
			}			
	 }	//	else //if ( $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['codError'] =='00000' )
	}
 else //solo ha marcado radio y no ha puesto otras cantidades
	{		 	
			if ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'General')
			{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral'];
			}
			elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Joven')
			{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] =	$arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven'];	
			}
			elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Parado')
			{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado'];	
			}	
			elseif ($arrCamposForm['datosFormSocio']['CODCUOTA'] == 'Honorario')
			{ $resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $arrCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario'];	
				 
					$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'EXENTO';			
					$resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['codError'] = '00000';	
     $resulValidar['datosFormCuotaSocio']['ESTADOCUOTA']['errorMensaje'] = '';					
	    //echo "<br><br>4-3 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormCuotaSocio']: ";print_r($resulValidar['datosFormCuotaSocio']); 																																																																																		 
			}				
			$resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['codError'] = '00000';	
	}	
	//echo "<br><br>4-5 validarCamposSocioPorGestor:validarCamposFormAltaSocioPorGestor:resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']: "; print_r($resulValidar['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']);
	//echo "<br><br>4-6 validarCamposSocioPorGestor:validarCamposFormAltaSocioPorGestor:resulValidar[['datosFormSocio']['CODCUOTA']: "; print_r($resulValidar['datosFormSocio']['CODCUOTA']);	
		
 /*-------------------------------------- Fin Validar IMPORTECUOTAANIOSOCIO y relacionados ---------------------------*/		
	/*-------------------------------------- Fin Validar datosFormCuotaSocio --------------------------------------------*/		

	//echo "<br><br>5 validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar:";print_r($resulValidar);echo "<br>";
 return $resulValidar;
}
/*----------------------------- Fin validarCamposFormAltaSocioPorGestor ----------------------------------------------*/


/*---------------- Inicio validarCamposActualizarSocioPorGestor() ------------------------------------------------------
Valida los campos de actualizar socio por un gestor, y comprueba existencia
en tablas de usuario, email, doc

LLAMADO: cCoordinador.php:actualizarSocioCoord(),cPresidente.php:actualizarSocioPres(),

LLAMA: validarCamposSocio.php:validarCamposActualizarSocio() y dentro validarCamposFormActualizarSocio()
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
----------------------------------------------------------------------------------------------------------------------*/
function validarCamposActualizarSocioPorGestor($camposFormRegSocio)
{ 
 //echo "<br><br>0-1 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:camposFormRegSocio: ";print_r($camposFormRegSocio);
	
	require_once './modelos/libs/validarCamposSocio.php';	
	$resValidarCampos = validarCamposActualizarSocio($camposFormRegSocio);//validarCamposSocio.php, que se comparte con rol Socio
	
 //echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos: ";print_r($resValidarCampos);	

 //echo "<br><br>2-1 actualizarSocio:validarCamposActualizarSocio:camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']: ";print_r($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']);	
	
	// Validación para el siguiente campo 	['datosFormMiembro']['OBSERVACIONES'] son las observaciones del Gestor
	if (isset($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']) && !empty($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']))
	{  
		$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES'] = validarCampoTexto($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES'],3,2000,"");				
  	
		if ($resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['codError'] !== '00000')
		{ 
			$resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']			
			$resValidarCampos['errorMensaje'] .=". ".$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['errorMensaje'];//concatenación errorMensaje
			$resValidarCampos['totalErrores']++;		
		}	
	}	
 else
	{$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = "";
		$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['codError'] = '00000';				
 }	
	//echo "<br><br>2-2 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos:['campoActualizar']['datosFormMiembro']['OBSERVACIONES']: ";print_r($resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']);	


	/*-------- Además también se devuelven estos valores desde la función validarCamposSocio.php:validarCamposActualizarSocio() -----*/
 //$resValidarCampos['campoActualizar']['datosCuotasEL'] = $camposFormRegSocio['campoActualizar']['datosCuotasEL'];//no se validan, pero se usan en validaciones
	//$resValidarCampos['campoHide'] = $camposFormRegSocio['campoHide'];//campos que hidden,se pasan sin validar
	//$resValidarCampos['campoVerAnioActual'] = $camposFormRegSocio['campoVerAnioActual'];//campos que hidden,se pasan sin validar	
	
 //echo "<br><br>3 validarCamposSocioPorGestor.php:validarCamposActualizarSocioPorGestor:resValidarCampos: ";print_r($resValidarCampos);			

	return $resValidarCampos; //incluye arrayMensaje
}

/*---------------- Fin validarCamposActualizarSocioPorGestor() -------------------------------------------------------*/

/*---------------- Inicio validarCamposFormAltaCoordArea ---------------------------------------------------------------
DESCRIPCION:Valida los campos de formulario de asignación de un área de gestión a un coordinador           
Llamado: cPresidente:asignarCoordinacionArea()
Llama:
----------------------------------------------------------------------------------------------------------------------*/
function validarCamposFormAltaCoordArea($arrCamposForm)
{
	//echo "<br><br>1 validarCamposSocioPorGestor.php:validarCamposFormAltaCoordArea:arrCamposForm:";print_r($arrCamposForm);
	
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

	//echo "<br><br>2 validarCamposSocioPorGestor.php:validarCamposFormAltaCoordArea:resulValidar:";print_r($resulValidar);
 return $resulValidar;
}
/*---------------- Fin validarCamposFormAltaCoordArea ----------------------------------------------------------------*/

/*---------------- Inicio validarCamposFormBuscarGestorApesNom() -------------------------------------------------------
DESCRIPCION: Valida los campos APE1, APE2, NOM introducidos en el formulario de entrada 
             para después buscar los datos personales del socio en la tabla MIEMBRO, 
													Actualmente se utliza para posterior asignación-eliminacion de los roles
             de Coordinación, Presidencia (), y Tesorería		
													
LLAMADA: cPresidente:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar()
         asignarTesoreríaRolBuscar()

LLAMA: modelos/libs/validarCampos.php:validarCampoNoVacio()

NOTA: no se validan caracteres dentro de campos, poco riesgo injection al utilizar Gestores
----------------------------------------------------------------------------------------------------------------------*/
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
/*----------------------------- Fin validarCamposFormBuscarGestorApesNom() -------------------------------------------*/

?>