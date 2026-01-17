<?php
/*------------------- validarCamposActualizarAgrupacionPres.php --------------------------------------------------------
Valida los campos del formulario formActualizarAgrupacionPres.php

LLAMADA: cPresidente:actualizarDatosAgrupacionPres(),

LLAMA: modelos/libs/validarCampos.php()

OBSERVACIONES:2020-04-14: comentarios																	
----------------------------------------------------------------------------------------------------------------------*/

/*---------------- Inicio validarCamposActualizarAgrupacion ------------------------------------------------------------
DESCRIPCION:Valida los campos de formulario para modificar datos de de la tabla "AGRUPACIONTERRITORIAL "    
                   
LLAMADA: cPresidente:actualizarDatosAgrupacionPres()
LLAMA:  validarCamposFormActualizarAgrupacion,  que a su vez llama a 
modelos/libs/validarCampos.php (varias funciones)

----------------------------------------------------------------------------------------------------------------------*/
function validarCamposActualizarAgrupacion($arrCamposActualizarAgrupacion)
{
	//echo "<br><br>0-1 validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion:arrCamposActualizarAgrupacion: ";print_r($arrCamposActualizarAgrupacion);

 $resValidarCamposForm = validarCamposFormActualizarAgrupacion($arrCamposActualizarAgrupacion);
	
	//echo "<br><br>1 validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion:resValidarCamposForm: ";print_r($resValidarCamposForm);
 $totalErroresLogicos = 0;
	$validarErrorLogico['errorMensaje'] ='';
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)//no es necesrio tratar error de sistema
	{ //echo "<br><br>2-1 validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion:grupo:[ ".$grupo." ], valGrupo: ";print_r($valGrupo);
	  
			foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)	
  	{	 
		  //echo "<br><br>2-1 validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion:grupo:[ ".$grupo."], nomCampo[".$nomCampo."], valNomCampo: ";print_r($valNomCampo);
			
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')//todos son >= '80000'
		  { 
					 $validarErrorLogico['codError'] = $resValidarCamposForm[$grupo][$nomCampo]['codError'];
					  $validarErrorLogico['errorMensaje'] .=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
					  $totalErroresLogicos +=1;
			 }		
		 }	
	}
	 
	if ($totalErroresLogicos === 0)
	{$resValidarCamposForm['totalErrores'] = 0;
  $resValidarCamposForm['codError'] = '00000';
  $resValidarCamposForm['errorMensaje'] = '';		
	}
	else//if ($totalErroresLogicos !==0)
	{$resValidarCamposForm['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje'] = $validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCamposForm['totalErrores'] = $totalErroresLogicos;	
	} 
 //echo "<br><br>3 validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion:resValidarCamposForm:";	print_r($resValidarCamposForm);
	return $resValidarCamposForm; //incluye arrayMensaje
}		
/*---------------- Fin validarCamposActualizarAgrupacion -------------------------------------------------------------*/

/*---------------- Inicio validarCamposFormActualizarAgrupacion --------------------------------------------------------

DESCRIPCION: Valida los campos de formulario para modificar datos de de la tabla "AGRUPACIONTERRITORIAL "       
Algunos campos no se validan, porque no se pueden cambiar y otros se deja libre la introducción de datos
dado la seguridad del nivel de gestor.

LLAMADA: validarCamposActualizarAgrupacionPres:validarCamposActualizarAgrupacion() y a su vez 
desde cPresidente:actualizarDatosAgrupacionPres()

LLAMA: modelos/libs/validarCampos.php (varias funciones)
----------------------------------------------------------------------------------------------------------------------*/
function validarCamposFormActualizarAgrupacion($arrCamposForm)
{
		//echo "<br><br>0-1 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:arrCamposForm";print_r($arrCamposForm);	
		
		require_once './modelos/libs/validarCampos.php';  
		
		if (isset ($arrCamposForm['comprobarYactualizar']) ) {unset($arrCamposForm['comprobarYactualizar']);}

		foreach($arrCamposForm as $campoGrupoForm => $valCampoGrupoForm )//para incluir los campos que no se validan
		{
			foreach($valCampoGrupoForm as $item => $valItem)
			{$resulValidar[$campoGrupoForm][$item]['codError'] = '00000';
				$resulValidar[$campoGrupoForm][$item]['errorMensaje'] = '';
				$resulValidar[$campoGrupoForm][$item]['valorCampo'] = $valItem;
			}
		}		
		//echo "<br><br>1 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar: ";print_r($resulValidar);
			  
		if (validarCIF($arrCamposForm['datosFormAgrupacion']['CIF']) === false) 
  { 
			$resulValidar['datosFormAgrupacion']['CIF']['codError'] = '80200';
			$resulValidar['datosFormAgrupacion']['CIF']['errorMensaje'] = "Error en CIF: (1Letra+8números+1caracter de control (que puede ser número o letra). Sin espacios ni guiones";
		}		
		//echo "<br><br>2-1 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar['datosFormAgrupacion']['CIF']: ";print_r($resulValidar['datosFormAgrupacion']['CIF']);

		if (isset($arrCamposForm['datosFormAgrupacion']['EMAIL']) && !empty($arrCamposForm['datosFormAgrupacion']['EMAIL']))
		{$resulValidar['datosFormAgrupacion']['EMAIL'] = validarEmail($arrCamposForm['datosFormAgrupacion']['EMAIL'],"");
		}		
	 $resulValidar['datosFormAgrupacion']['TELFIJOTRABAJO']= validarTelefono($arrCamposForm['datosFormAgrupacion']['TELFIJOTRABAJO'],9,14,"");
	 $resulValidar['datosFormAgrupacion']['TELMOV'] = validarTelefono($arrCamposForm['datosFormAgrupacion']['TELMOV'],9,14,"");			
	
		//echo "<br><br>3 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar";print_r($resulValidar);
				
		/*------------------- Inicio Validar CUENTAIBAN (solo se admiten países SEPA) --------------------------------------*/
		
		//Ya no se admiten	CUENTANOIBAN: Actualmente solo admitimos cuenta IBAN, lo dejo por si alguna vez cambiamos
		//$resulValidar['datosFormAgrupacion']['CUENTANOIBAN'] = validarCuentaNoIBAN($arrCamposForm['datosFormAgrupacion']['CUENTAIBAN'],$arrCamposForm['datosFormAgrupacion']['CUENTANOIBAN']);																																	
		//$resulValidar['datosFormAgrupacion']['CUENTAIBAN']	= validarCuentaIBAN($arrCamposForm['datosFormAgrupacion']['CUENTAIBAN'],$arrCamposForm['datosFormAgrupacion']['CUENTANOIBAN']);

		$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']	= validarIBAN($arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN1']);
			
		if ($resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['codError'] === '00000')
		{ 
				$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']	= validarPaisSEPA($arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN1']);//en modeloUsuarios.php

				$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['valorCampo'] = $arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN1'];
		}		
	 //echo "<br><br>4-1 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']: ";print_r($resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN1']);

 	$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']	= validarIBAN($arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN2']);
	
		if ($resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['codError'] === '00000')
		{ 
				$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']	= validarPaisSEPA($arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN2']);//en modeloUsuarios.php
				$resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['valorCampo'] = $arrCamposForm['datosFormAgrupacion']['CUENTAAGRUPIBAN2'];
		}		
	 //echo "<br><br>4-2 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']: ";print_r($resulValidar['datosFormAgrupacion']['CUENTAAGRUPIBAN2']);
					
	/*------------------- Fin Validar CUENTAIBAN (solo se admiten países SEPA) ------------------------------------------*/	
	
	//---------------- Inicio Validar datosFormDomicilio (por ahora no se valida -----------------------------------------
 /*$arrCamposForm['datosFormDomicilio']['CODPAISDOM'] = $arrCamposForm['datosFormAgrupacion']['CODPAISDOM'];
	$arrCamposForm['datosFormDomicilio']['CP'] = $arrCamposForm['datosFormAgrupacion']['CP'];
	$arrCamposForm['datosFormDomicilio']['LOCALIDAD'] = $arrCamposForm['datosFormAgrupacion']['LOCALIDAD'];
	$arrCamposForm['datosFormDomicilio']['DIRECCION'] = $arrCamposForm['datosFormAgrupacion']['DIRECCION'] ;
   
	$resulValidarDom = validarDom($arrCamposForm['datosFormDomicilio']);//en validarCampos.php
	
	$resulValidar['datosFormAgrupacion']['CODPAISDOM'] = $resulValidarDom['CODPAISDOM'];
	$resulValidar['datosFormAgrupacion']['CP'] = $resulValidarDom['CP'];
	$resulValidar['datosFormAgrupacion']['LOCALIDAD'] = $resulValidarDom['LOCALIDAD'];
	$resulValidar['datosFormAgrupacion']['DIRECCION'] = $resulValidarDom['DIRECCION'] ;
	*/
	/*-------------------------------------- Fin Validar datosFormDomicilio ---------------------------------------------*/	
	//echo "<br><br>5 validarCamposActualizarAgrupacionPres:validarCamposFormActualizarAgrupacion:resulValidar"; print_r($resulValidar);			
	
 return $resulValidar;
}
/*---------------------------- Fin validarCamposFormActualizarAgrupacion ---------------------------------------------*/
?>