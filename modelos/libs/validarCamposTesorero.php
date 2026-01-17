<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: validarCamposTesorero.php

VERSION: PHP 7.3.21
DESCRIPCION: Valida los campos recibidos desde los formularios de tesorero

Llamado: desde  cTesorero.php
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales y otras ...)
2020-11-06: Cambios en validarCamposFormActualizarIngresoCuota()
2020_09_08: "validarCamposTesorero.php:validarCamposActCuotaSocioTes()" sustituye a 
validarCamposActCuotaSocio($camposFormRegSocio)

--------------------------------------------------------------------------------------------------*/
require_once './modelos/modeloUsuarios.php';
require_once './modelos/libs/validarCampos.php'; 

/*====================== INICIO VALIDAR CUOTAS SOCIOS: INGRESO Y ACTUALIZACIÓN  ===================
 - validarCamposActualizarIngresoCuota(): validarCamposFormActualizarIngresoCuota()
	- validarCamposActCuotaSocioTes():	
=================================================================================================*/	

/*---------------- Inicio validarCamposActualizarIngresoCuota() -----------------------------------
DESCRIPCION: Valida los campos de actualizar ingreso cuota socio
Llamado desde: cTesorero.php:actualizarIngresoCuota()
Llama funciones: validarCamposFormActualizarIngresoCuota()                 
--------------------------------------------------------------------------------------------------*/
function validarCamposActualizarIngresoCuota($camposFormIngresoCuota)
{//echo "<br><br>1-0_1 validarCamposTesorero:validarCamposActualizarIngresoCuota:camposFormIngresoCuota";print_r($camposFormIngresoCuota);
 
	$resValidarCamposForm = validarCamposFormActualizarIngresoCuota($camposFormIngresoCuota['datSocio']);
 
	//echo "<br><br>1-0_2 validarCamposTesorero:validarCamposActualizarIngresoCuota:resValidarCamposForm:";print_r($resValidarCamposForm); 	

	//--- NOTA: NO SE DEBIERA PRODUCIR NINGUN ERROR SISTEMA, YA QUE PARA VALIDAR AQUI, NO SE HACEN CONSULTAS ----
	// ********** INTENTAR SIMPLIFICAR *********************
	$validarErrorSistema['codError']='00000';
	$validarErrorLogico['codError']='00000';
	$validarErrorLogico['errorMensaje'] ='';
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
				  {$validarErrorLogico['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
				   $validarErrorLogico['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
				   $totalErroresLogicos +=1;
				  }
			 }		
		}	
	}	

	if ($totalErroresSistema == 0 && $totalErroresLogicos == 0)
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
	
 //echo "<br><br>1-0_3 validarCamposTesorero:validarCamposActualizarIngresoCuota:totalErroresLogicos:$totalErroresLogicos";
	//echo "<br><br>1-0_4 validarCamposTesorero:validarCamposActualizarIngresoCuota:resValidarCamposForm:";print_r($resValidarCamposForm);
	
	return $resValidarCamposForm; //incluye arrayMensaje
}		
/*---------------- Fin validarCamposActualizarIngresoCuota ---------------------------------------*/

/*---------------- Inicio validarCamposFormActualizarIngresoCuota ---------------------------------
DESCRIPCION:Valida los campos de formulario para actualizar el ingreso de cuota socio 
            "formAnotarIngresoCuota.php" 
									
Llamado desde: validarCamposTesorero.php:validarCamposActualizarIngresoCuota()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)

OBSERVACIONES:
2020-11-06: Cambios en ADD_IMPORTEGASTOSDEVOLUCION, MOTIVODEVOLUCION
2019-01-21: Bastantes cambios. Añado más validaciones incluido 'formOrdenesCobro' 
            y otras correcciones												
2017-01-12: Correcciones en FECHAALTA y otras			
-------------------------------------------------------------------------------------------------*/
function validarCamposFormActualizarIngresoCuota($arrCamposForm)
{	
 //echo "<br><br>1 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:arrCamposForm: "; print_r($arrCamposForm);

	foreach($arrCamposForm as $campoGrupoForm => $valCampoGrupoForm )//para incluir los campos que no se validan
	{
		foreach($valCampoGrupoForm as $item => $valItem)
	 {$resulValidar[$campoGrupoForm][$item]['codError']='00000';
	 	$resulValidar[$campoGrupoForm][$item]['errorMensaje']='';
	 	$resulValidar[$campoGrupoForm][$item]['valorCampo']=$valItem;
		}
	}	
	//echo "<br><br>2 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar"; print_r($resulValidar);
	
 /*-------------------------------------- Inicio Validar IMPORTECUOTAANIOPAGADA ------------------*/			
	$resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA'] = validarCantidadDecimal($arrCamposForm['formIngresoCuota']['IMPORTECUOTAANIOPAGADA'],0,100000.00,"");	

	if ($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError'] =='00000')
 {		
			if($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA')
			{
					if ( number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') >	0.00)
					{ $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
							$resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']=" Si has anotado el estado cuota NOABONADA-DEVUELTA, la cuota pagada debe ser 0 Euros";
					}	
			}
			if($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'PENDIENTE-COBRO')
			{ 	   
					if ( number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') >	0.00)
					{ $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
							$resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']=" Si has anotado el estado cuota PENDIENTE-COBRO, la cuota pagada debe ser 0 Euros";
					}	
			}	
			if($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'NOABONADA')//solo para años anteriores
			{
					if ( number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') >	0.00)
					{ $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
							$resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']=" Si has anotado el estado cuota NOABONADA, la cuota pagada debe ser 0 Euros";
					}	
			}			
			elseif ($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'EXENTO')
			{ if (	number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') ==	0.00)
			  {
						$resulValidar['formIngresoCuota']['ESTADOCUOTA']['codError']='80303';
						$resulValidar['formIngresoCuota']['ESTADOCUOTA']['errorMensaje']='No puedes anotar un pago de cuota de los socios/as que están EXENTOS: anótalo como una DONACION';	
					}
			}		
			elseif($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'ABONADA-PARTE')
			{ 	
		   if (	number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') ==	0.00)
			  { 
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']=" Si has anotado el estado cuota ABONADA-PARTE, la cuota pagada debe ser superior a 0 Euros"; 
			  }	
     elseif (number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') >=	number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOEL'] ['valorCampo'], 2, '.', ''))
			  { 
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']="Si has anotado el estado cuota ABONADA-PARTE, la cuota pagada no puede ser igual o superior al tipo de cuota correspondiente ".
                                                                                   $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOEL'] ['valorCampo']." Euros"; 
			  }
   }	
   elseif($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'ABONADA')
			{ 
			  if (	number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') ==	0.00)
			  { 
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']=" Si has anotado el estado cuota ABONADA, la cuota pagada debe ser superior a 0 Euros"; 
			  }	
     elseif (number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') <	number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOEL'] ['valorCampo'], 2, '.', ''))
			  { 
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']='80303';
				  $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']="Si has anotado el estado cuota ABONADA, la cuota pagada no puede ser inferior al tipo de cuota correspondiente, debes elegir ABONADA-PARTE, o igual más de  ".
                                                                                   $resulValidar['formIngresoCuota']['IMPORTECUOTAANIOEL'] ['valorCampo']." Euros"; 
			  }	
		 }	
	} //if ($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError'] =='00000')		 	
	/*-------------------------------------- Fin Validar IMPORTECUOTAANIOPAGADA ---------------------*/
	
	/*-------------------------------------- Inicio Validar GastosAbonaCuotaSocio -------------------*/					
	//-- Para añadir mas gastos ADD_IMPORTEGASTOSABONOCUOTA a los ya existentes IMPORTEGASTOSABONOCUOTA en la tabla CUOTAANIOSOCIO --
	if (isset($arrCamposForm['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']) && !empty($arrCamposForm['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']))
	{ $resulValidar['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA'] = validarCantidadDecimal($arrCamposForm['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA'],-10000.00,10000.00,"");
	}
	else
	{ $resulValidar['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['valorCampo'] = '0.00';
			$resulValidar['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['codError'] = '00000';	 		
	}
	/*-------------------------------------- Fin Validar GastosAbonaCuotaSocio  ---------------------*/		

	/*----- Inicio validar campos fechas FECHAALTA y FECHAPAGO --------------------------------------*/
	//echo "<br><br>2-1-a validarCamposTesorero:validarCamposFormActualizarIngresoCuota:arrCamposForm['datosFormSocio']['FECHAALTA']: "; print_r($arrCamposForm['datosFormSocio']['FECHAALTA']);
	$resulValidar['datosFormSocio']['FECHAALTA']['anio']['valorCampo']= $arrCamposForm['datosFormSocio']['FECHAALTA']['anio'];
	$resulValidar['datosFormSocio']['FECHAALTA']['mes']['valorCampo'] = $arrCamposForm['datosFormSocio']['FECHAALTA']['mes'];
	$resulValidar['datosFormSocio']['FECHAALTA']['dia']['valorCampo'] = $arrCamposForm['datosFormSocio']['FECHAALTA']['dia'];	
	
 //$limInferiorFecha =	($arrCamposForm['formIngresoCuota']['ANIOCUOTA']-1).'-01-01'; //por si se actualiza pago del año previo a ultimos días de diciembre????
	$limInferiorFecha =	$arrCamposForm['formIngresoCuota']['ANIOCUOTA'].'-01-01';
	$limSuperiorFecha =	date('Y-m-d');
 $permitirVacio = true;	
	//echo "<br><br>2-1-b validarCamposTesorero:validarCamposFormActualizarIngresoCuota:limInferiorFecha: "; print_r($limInferiorFecha);	
	
	//echo "<br><br>2-1-c validarCamposTesorero:validarCamposFormActualizarIngresoCuota:arrCamposForm['formIngresoCuota']['FECHAPAGO']: "; print_r($arrCamposForm['formIngresoCuota']['FECHAPAGO']);		
 $resulValidar['formIngresoCuota']['FECHAPAGO'] =	validarFechaLimites($arrCamposForm['formIngresoCuota']['FECHAPAGO'],
	                                                                     $limInferiorFecha,$limSuperiorFecha,$permitirVacio );//límite inferior, superior, permitir vacio true		
	//echo "<br><br>2-1-d validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formIngresoCuota']['FECHAPAGO']: "; print_r($resulValidar['formIngresoCuota']['FECHAPAGO']);
																																																																						
	$fechaFECHAPAGO = $resulValidar['formIngresoCuota']['FECHAPAGO']['anio']['valorCampo']."-".
																		 $resulValidar['formIngresoCuota']['FECHAPAGO']['mes']['valorCampo']."-".
																	 	$resulValidar['formIngresoCuota']['FECHAPAGO']['dia']['valorCampo'];																			
					
	//echo "<br><br>2-1-e validarCamposTesorero:validarCamposFormActualizarIngresoCuota:fechaFECHAPAGO: "; print_r($fechaFECHAPAGO);		
	if ($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['codError']=='00000')
	{	
			if (number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') ==	'0.00')
			{ 
					if ($fechaFECHAPAGO !=='0000-00-00')
					{$resulValidar['formIngresoCuota']['FECHAPAGO']['codError']='80303';
						$resulValidar['formIngresoCuota']['FECHAPAGO']['errorMensaje']="No puedes anotar fecha de ingreso si no hay pagos anotados, elige 'día-mes-año'";
					}
			}
			else //if (number_format($resulValidar['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'], 2, '.', '') !==	'0.00')
			{ 
			  if ($fechaFECHAPAGO =='0000-00-00')
					{$resulValidar['formIngresoCuota']['FECHAPAGO']['codError']='80303';
						$resulValidar['formIngresoCuota']['FECHAPAGO']['errorMensaje']="Falta anotar la fecha de ingreso del pago anotado";
					}
			}	
	} 																	
	/*----- Fin validar campos fechas FECHAALTA y FECHAPAGO -----------------------------------------*/ 

 /*----------Inicio	OBSERVACIONES en CUARTAANIOSOCIO ---------------------------------------------*/
	if (isset($arrCamposForm['formIngresoCuota']['OBSERVACIONES']) && !empty($arrCamposForm['formIngresoCuota']['OBSERVACIONES']))
	{$resulValidar['formIngresoCuota']['OBSERVACIONES'] = validarCampoTexto($arrCamposForm['formIngresoCuota']['OBSERVACIONES'],0,2000,"");		
  //echo "<br><br>2-2-a validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar:";print_r($resulValidar);echo "<br>";
 }	
 else
	{$resulValidar['formIngresoCuota']['OBSERVACIONES']['valorCampo'] = $arrCamposForm['formIngresoCuota']['OBSERVACIONES'];
	 $resulValidar['formIngresoCuota']['OBSERVACIONES']['codError'] ='00000'; 
		//echo "<br><br>2-2-b validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor:resulValidar:";print_r($resulValidar);echo "<br>";
 }
 /*----------FIN	OBSERVACIONES en CUARTAANIOSOCIO -----------------------------------------------*/	
	
	
	/*----------Inicio	ordenes_cobro	----------------------------------------------------------------*/		
 
	// REVISAR QUE IF CONVIENE: en ORDENES_COBRO, estará como  ABONADA o NOABONADA-DEVUELTA
 //if (isset($arrCamposForm['formIngresoCuota']['NOMARCHIVOSEPAXML']) && !empty($arrCamposForm['formIngresoCuota']['NOMARCHIVOSEPAXML']))
		
	if (isset($arrCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML']) && !empty($arrCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML'])) 
 {	//echo "<br><br>3-1 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar"; print_r($resulValidar);

  //--	if para evitar posible olvido de tesorería de anotar devolución, previamente a otros valores de ESTADOCUOTA que debiera anotas después
		if ( $arrCamposForm['formOrdenesCobro']['ESTADOCUOTA'] == 'ABONADA'  && 
		     ($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'ABONADA-PARTE'  || 
							 $arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'PENDIENTE-COBRO'|| 
								$arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'NOABONADA'
							)
					)
		{
			$resulValidar['formIngresoCuota']['ESTADOCUOTA']['codError'] = '80303';
			$resulValidar['formIngresoCuota']['ESTADOCUOTA']['errorMensaje'] = "Una cuota domiciliada y anotada como ABONADA por el banco, si después se ha devuelto por el banco, 
			primero se debe anotar como 'Devuelta', y después ya se la puede anotar como: 'Pendiente de cobro', 'Abonada', 'No abonada', 'Abonada parte' (lo que proceda)";					
		}	
  //-- fin if para evitar posible error de tesorera, de olvido de anotar devolución,  -----
		
		//-- Inicio Para añadir más gastos Devolución de cuota domiciliada ADD_IMPORTEGASTOSDEVOLUCION a los ya existentes IMPORTEGASTOSABONOCUOTA en la tabla CUOTAANIOSOCIO y ORDENES_COBRO --
		if (isset($arrCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']))
		{ 
	   $resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION'] = validarCantidadDecimal($arrCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION'],-10000.00,10000.00,"");	   
		}
		else
		{ $resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'] = 0.00;
				$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] = '00000'; 	
		}
		//echo "<br><br>3_2 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar: "; print_r($resulValidar['formOrdenesCobro']);
		
  //NOTA: Los gastos totales de una devolución pueden ser mayor que cero (lo normal), o cero (caso de culpa del banco, u otros) pero nunca menor que cero.
		if ($resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] == '00000')	
		{																																																																											
				//echo "<br><br>3-3 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']: "; print_r($resulValidar['formOrdenesCobro']);
			
				if($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA')	//también debiera estar igual en ['formOrdenesCobro']['ESTADOCUOTA']
				{	
				  /*$resulValidar['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro. 
						  En general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, pero NO coincidirá si fuese el caso de ESTADOCUOTA = ABONADA-PARTE, 
						  en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. */
				
					if (($resulValidar['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo'] + $resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']) >= 0)
					{
						$resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] = 'NOABONADA-DEVUELTA';
						$resulValidar['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA']['valorCampo'] = 0.00; //o $arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'];	
					}			
					else//if(($resulValidar['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo']+$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'])<0)//error no puede ser negativo los gastos de devolución
					{ 						
						$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] ='80100';//error lógico No tiene los valores requeridos
						$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['errorMensaje'] ="Los gastos totales de devolución de una cuota domiciliada, no pueden ser negativos, vuelve a revisar la anterior anotación del socio/a: ";																			
					}					
				}//if($arrayDatosValidarCamposForm['formIngresoCuota']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA')	
					
				elseif($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] !== 'NOABONADA-DEVUELTA')	//=ABONADA, ABONADA-PARTE,...
				{
					//echo "<br><br>3-4 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']: "; print_r($resulValidar['formOrdenesCobro']);									
				 
				  if ($resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'NOABONADA-DEVUELTA')//si no esta devuelta no puede tener gasto de devolución ni positivos ni negativos, sería cero
						{							
							if (($resulValidar['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo'] + $resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']) > 0)
							{								 
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] ='80100';//error lógico No tiene los valores requeridos			
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['errorMensaje'] ="No puede haber gastos de devolución si esa orden de cobro no ha sido anotada como devuelta por el banco. ";																			
							}    
							elseif (($resulValidar['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo'] + $resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']) < 0)
							{								
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] ='80100';//error lógico No tiene los valores requeridos			
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['errorMensaje'] ="Los gastos totales de devolución de una cuota domiciliada, no pueden negativos, vuelve a revisar la anterior anotación del socio/a: ";																			
							}
      }//if ($resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'NOABONADA-DEVUELTA') 
							
      else//$resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA')
						{ //no se hace nada aquí 
						
						  if(($resulValidar['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo']+$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'])<0)//error no puede ser negativo los gastos de devolución
								{						
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] ='80100';//error lógico No tiene los valores requeridos 
									$resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['errorMensaje'] ="Los gastos totales de devolución de una cuota domiciliada, no pueden negativos, vuelve a revisar la anterior anotación del socio/a: ";																			
								}	
						}//else $resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA')						
					
				}//elseif($arrayDatosValidarCamposForm['formIngresoCuota']['ESTADOCUOTA']['valorCampo']!== 'NOABONADA-DEVUELTA')				
		}	//if ($resulValidar['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['codError'] == '00000')	
		/*--------------- Fin Validar Devolución de cuota domiciliada ADD_IMPORTEGASTOSDEVOLUCION ------*/			
	
		//---------- Inicio Fecha devolución -----------------------------------------			
		
		if (isset($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']))
		{ 
			$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo']= $arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['anio'];
			$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo'] = $arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['mes'];
			$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo'] = $arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['dia'];
			
			$fechaInf = new DateTime($arrCamposForm['formOrdenesCobro']['FECHAPAGO']);//la fecha de devolución no puede ser anterior a la fecha de pago

			$limInferiorFecha = $fechaInf ->format('Y-m-d');
			$limSuperiorFecha =	date('Y-m-d');
			$permitirVacio = true;						
												
			$resulValidar['formOrdenesCobro']['FECHADEVOLUCION'] =	validarFechaLimites($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION'],
																																																										$limInferiorFecha,$limSuperiorFecha,$permitirVacio );//límite inferior, superior, permitir vacio true	

			//echo "<br><br>4_1 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['FECHADEVOLUCION']: "; print_r($resulValidar['formOrdenesCobro']['FECHADEVOLUCION']);																																																																								
	
		 $fechaFECHADEVOLUCION = $resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo']."-".
																		         $resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo']."-".
																	 	        $resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo'];
																																																											
			if ($resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['codError'] =='00000')
  	{	
					if ($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] !== 'NOABONADA-DEVUELTA') 
					{ 
				   if ($resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'NOABONADA-DEVUELTA')
				   {
								if ($fechaFECHADEVOLUCION !=='0000-00-00')
								{$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['codError']='80303';
									$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje']="No puedes anotar la fecha de devolucción por el banco de cuota, si no ha sido devuelta: elige 'día-mes-año'";
								}
							}
       else
       { //se deja la fecha de devolución que tenga sin cambiar								
							}								
					}
					else //if ($arrCamposForm['formIngresoCuota']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA')
					{ 
							if ($fechaFECHADEVOLUCION =='0000-00-00')
							{$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['codError']='80303';
								$resulValidar['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje']="Falta anotar la fecha de devolucción por el banco de cuota domiciliada";
							}
					}	
	  }																																																																									
			//echo "<br><br>4_2 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['FECHADEVOLUCION']: "; print_r($resulValidar['formOrdenesCobro']['FECHADEVOLUCION']);																																																																																																																																																			
				
		}	//if (isset($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']))
		//------------ Fin Fecha devolución ------------------------------------------	
	
  //---------- Incicio Motivo devolución ---------------------------------------		

		if ($resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA')
		{
				if (isset($arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']))
				{$resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION'] = validarCampoTexto($arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION'],0,120,"");		
					//echo "<br><br>4_3 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']: ";print_r($resulValidar);echo "<br>";
				}	
				else
				{$resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo'] = $arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION'];	
					$resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']['codError'] ='00000'; 
					//echo "<br><br>4_4 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']: ";print_r($resulValidar);echo "<br>";
				}	
		}//if ($resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA') 
			
		else//$resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'NOABONADA-DEVUELTA')
		{						
				if (isset($arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']))
				{//echo "<br><br>4_5 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']: ";print_r($resulValidar);echo "<br>";
			
					$resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']['codError'] ='80100';//error lógico si no hay devolución no se intriducen comentarios
					$resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']['errorMensaje'] =" Si no hay devolución de una cuota domiciliada no se introducen comentarios ";																			
				}	
				else
				{//no se hace nada
			  //echo "<br><br>4_6 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar['formOrdenesCobro']['MOTIVODEVOLUCION']: ";print_r($resulValidar);echo "<br>";
				}							
		}//else $resulValidar['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'NOABONADA-DEVUELTA')
	
	 //---------- Fin Motivo devolución --------------------------------------------		
		
 } //if (isset($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']) && !empty($arrCamposForm['formOrdenesCobro']['FECHADEVOLUCION']))
	
 /*----------Fin	ordenes_cobro	-------------------------------------------------------------------*/

	//echo "<br><br>5 validarCamposTesorero:validarCamposFormActualizarIngresoCuota:resulValidar:"; print_r($resulValidar);
 return $resulValidar;
}
/*----------------------------- Fin validarCamposFormActualizarIngresoCuota ---------------------*/

/*---------------- Inicio validarCamposActCuotaSocioTes() -----------------------------------------
Valida los campos de actualizar socio por tesorero, y comprueba existencia en tablas de usuario,
email, doc.
Reutilizo la función validarCamposSocio.php:validarCamposActualizarSocio() y añado validación d
e los campos adiccionales de tesorero.

LLAMADA: cTesorero:actualizarDatosCuotaSocioTes()
LLAMA: validarCamposSocio.php:validarCamposActualizarSocio() y dentro
validarCamposFormActualizarSocio() se llama modeloUsuarios.php:buscarUsuario(),
buscarEmail(), buscarNumDoc() y otras funciones concretas de validaciones.
Pero aquí se añade la validación de unos campos específico de Tesorero:
"datosFormCuotaSocio['ORDENARCOBROBANCO']" y "datosFormCuotaSocio['OBSERVACIONES']"
además del campo "datosFormMiembro['OBSERVACIONES']" que es común a todos los gestores
																	
OBSERVACIONES:
2020-09-08: Creo está función para utilizar la común función 
validarCamposSocio.php:validarCamposActualizarSocio() y añado validaciones especificas de tesorero
Sustituye a validarCamposTesorero.php:validarCamposActCuotaSocio.php										
------------------------------------------------------------------------------------------------*/
function validarCamposActCuotaSocioTes($camposFormRegSocio)
{ 
 //echo "<br><br>0 validarCamposTesorero.php:validarCamposActCuotaSocioTes:camposFormRegSocio: ";print_r($camposFormRegSocio);

	require_once './modelos/libs/validarCamposSocio.php';	
	$resValidarCampos = validarCamposActualizarSocio($camposFormRegSocio);
	
 //echo "<br><br>1 validarCamposTesorero.php:validarCamposActCuotaSocioTes:resValidarCampos: ";print_r($resValidarCampos);
	
	/* Inicio validar datosFormMiembro['OBSERVACIONES'], común para todos los gestores */
	if (isset($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']) && !empty($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']))
	{
  $resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES'] = validarCampoTexto($camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES'],3,255,"");		
 }	
 else
	{//$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = '';//acaso mejor
		$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES'];
	 $resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['codError'] = '00000'; 
 }	

	if ($resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['codError'] !=='00000')
	{ 
  $resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCampos['errorMensaje'] .=". ".$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['errorMensaje'];//concatenación errorMensaje
		$resValidarCampos['totalErrores'] += $resValidarCampos['totalErrores'];	  
	}
	/* Fin validar datosFormMiembro['OBSERVACIONES'], común para todos los gestores */
	
	/* Inicio validar datosFormCuotaSocio['OBSERVACIONES'], para gestor tesorero */
	if (isset($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']) && !empty($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']))
	{
  $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES'] = validarCampoTexto($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES'],3,2000,"");		
 }	
 else
	{//$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['valorCampo'] = '';//acaso mejor
  $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES'];
	 $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['codError'] = '00000'; 		
 }	
	if ($resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['codError'] !=='00000')
	{$resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']		
		$resValidarCampos['errorMensaje'] .=". ".$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['errorMensaje'];//concatenación errorMensaje
		$resValidarCampos['totalErrores'] += $resValidarCampos['totalErrores'];  
	}
	/* Fin validar datosFormCuotaSocio['OBSERVACIONES'], para gestor tesorero */
	
	/* Inicio validar datosFormCuotaSocio['ORDENARCOBROBANCO'], para gestor tesorero */
	if (isset($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']) && !empty($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']))
	{
		if ($camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO'] == 'SI' ||  $camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO'] == 'NO' )
  { $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO'];
	   $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['codError'] = '00000'; 			
		}
		else
		{$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO'];
			$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['codError'] = '80200'; 
			$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['errorMensaje'] = 'Ordenar cobro banco debe se SI o NO'; 		
		}			
 }	
 else
	{$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'] = $camposFormRegSocio['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO'];
	 $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['codError'] = '80200'; 
  $resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['errorMensaje'] = 'Ordenar cobro no puede estar vacío, debe se SI o NO'; 		
 }	

	if ($resValidarCampos['campoActualizar']['datosFormCuotaSocio']['ORDENARCOBROBANCO']['codError'] !=='00000')
	{$resValidarCampos['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']		
		$resValidarCampos['errorMensaje'] .=". ".$resValidarCampos['campoActualizar']['datosFormCuotaSocio']['OBSERVACIONES']['errorMensaje'];//concatenación errorMensaje
		$resValidarCampos['totalErrores'] += $resValidarCampos['totalErrores'];	
	}
	/* Fin validar datosFormCuotaSocio['ORDENARCOBROBANCO'], para gestor tesorero */
	
	//echo "<br><br>2 validarCamposTesorero.php:validarCamposActCuotaSocioTes::resValidarCampos: ";print_r($resValidarCampos);	
	
 //$resValidarCampos['campoActualizar']['datosFormMiembro']['OBSERVACIONES'] = $resulValidar['datosFormMiembro']['OBSERVACIONES'];//los campos que se validan 

	/*--- Además también se devuelven los valores de la función validarCamposSocio.php:validarCamposActualizarSocio() entre otros -----*/
 //$resValidarCampos['campoActualizar']['datosCuotasEL'] = $camposFormRegSocio['campoActualizar']['datosCuotasEL'];//no se validan, pero se usan en validaciones
	//$resValidarCampos['campoHide'] = $camposFormRegSocio['campoHide'];//campos que hidden,se pasan sin validar
	//$resValidarCampos['campoVerAnioActual'] = $camposFormRegSocio['campoVerAnioActual'];//campos que hidden,se pasan sin validar	
	
 //echo "<br><br>3 validarCamposTesorero.php:validarCamposActCuotaSocioTes:resValidarCampos: ";print_r($resValidarCampos);			

	return $resValidarCampos; 
}
/*------------------------- Fin validarCamposActCuotaSocioTes -----------------------------------*/

/*====================== INICIO VALIDAR CUOTAS SOCIOS: INGRESO Y ACTUALIZACIÓN  =================*/


/*============================ INICIO VALIDAR DONACIONES =========================================
 - validarComprobarDonante():validarFormComprobarDonante() 
	- validarCamposAnotarIngresoDonacion():validarFormAnotarIngresoDonacion()   
 - validarCamposDonacionConcepto():	
================================================================================================*/	

/*----------------- Inicio validarComprobarDonante -----------------------------------------------
DESCRIPCION:Valida los campos de formulario "formAnotarIngresoDonacionMenu.php" para buscar un donante
            en "MIEMBROS" o "DONACIONES" a ver si existe         
Llamado desde: cTesorero.php:anotarIngresoDonacionMenu()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------------------------*/
function validarComprobarDonante($camposFormIngresoDonacionMenu)
{//echo "<br><br>1 validarCamposTesorero:validarComprobarDonante:validarComprobarDonante:camposFormIngresoDonacionMenu: ";print_r($camposFormIngresoDonacionMenu);
 
	$resValidarCamposForm = validarFormComprobarDonante($camposFormIngresoDonacionMenu);
	//echo "<br><br>2 validarCamposTesorero:validarComprobarDonante:resValidarCamposForm: ";print_r($resValidarCamposForm);
	
	$validarErrorLogico['errorMensaje']='';
	$totalErroresLogicos = 0;
	foreach ($resValidarCamposForm as $grupo => $valGrupo)//no es necesArio tratar error de sistema
	{ 
	  foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)		
  	{	
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')//todos son >= '80000'
		  { 
					 $validarErrorLogico['codError']=$resValidarCamposForm[$grupo][$nomCampo]['codError'];
					  $validarErrorLogico['errorMensaje'].=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
					  $totalErroresLogicos +=1;
			 }		
		 }	
	}	 
	if ($totalErroresLogicos == 0)
	{$resValidarCamposForm['totalErrores']=0;
  $resValidarCamposForm['codError']='00000';
  $resValidarCamposForm['errorMensaje']='';		
	}
	else//if ($totalErroresLogicos !==0)
	{$resValidarCamposForm['codError'] = '80200';//o bien el último error:$validarErrorLogico['codError']
		$resValidarCamposForm['errorMensaje'] = $validarErrorLogico['errorMensaje'];//concatenación errormensaje
		$resValidarCamposForm['totalErrores'] = $totalErroresLogicos;	
	} 
	
 //echo "<br><br>3 validarCamposTesorero:validarComprobarDonante:validarComprobarDonante:resValidarCamposForm:";	print_r($resValidarCamposForm);
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarComprobarDonante ---------------------------------------------------

/*---------------- Inicio validarFormComprobarDonante --------------------------------------------
DESCRIPCION:Valida los campos de formulario NIF, NIE, Pasaporte o email 
            para buscar un donante en "MIEMBROS"  o "DONACION"          
Llamado: desde validarCamposTesorero:validarComprobarDonante()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------------------------*/
function validarFormComprobarDonante($arrCamposForm)
{//echo "<br><br>1 validarCamposTesorero:validarFormComprobarDonante:arrCamposForm:";print_r($arrCamposForm);
	/*-------------------------------- Inicio Validar email -----------------------------------*/	
 if (isset($arrCamposForm['datosFormDonacion']['EMAIL']))
	{	
		$resulValidar['datosFormDonacion']['EMAIL'] = validarEmail($arrCamposForm['datosFormDonacion']['EMAIL'],"");		
		//echo "<br><br>2 validarCamposTesorero:validarFormComprobarDonante:resulValidar:"; print_r($resulValidar);
	}
	/*----------------------------------- Fin Validar email ------------------------------------*/	

 /*-------------------------------- Inicio Validar documento NIF, NIE, Pasaporte ------------*/
 else 	//!isset($arrCamposForm['datosFormDonacion']['EMAIL']
	{	//-- las  siguientes líneas son para guardar valores originales cuando hay error ¡no se pueden eliminar! ----
	 $resulValidar['datosFormDonacion']['CODPAISDOC']['valorCampo'] = $arrCamposForm['datosFormDonacion']['CODPAISDOC'];	
		$resulValidar['datosFormDonacion']['CODPAISDOC']['codError'] = '00000';	
		$resulValidar['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormDonacion']['TIPODOCUMENTOMIEMBRO'];
		$resulValidar['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['codError'] = '00000';		
  $resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'];		
	 $resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['codError'] = '00000';
		//--------- fin  líneas guardar valores originales cuando hay error ---------------------------
		
		if ($arrCamposForm['datosFormDonacion']['TIPODOCUMENTOMIEMBRO'] == 'NIF')//NIF	
	 {if ($arrCamposForm['datosFormDonacion']['CODPAISDOC'] == 'ES')
		 { $resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'] = validarNif($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']);
	  }
			else
		 {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'];
			 $resulValidar['datosFormDonacion']['CODPAISDOC']['errorMensaje'] = 'Solo puedes elegir NIF si el país es España';
		  $resulValidar['datosFormDonacion']['CODPAISDOC']['codError'] = '80303';	
		 }
		}	 
		elseif ($arrCamposForm['datosFormDonacion']['TIPODOCUMENTOMIEMBRO'] == 'NIE')	//NIE
	 { if ($arrCamposForm['datosFormDonacion']['CODPAISDOC'] == 'ES')
		  {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'] = validarNie($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']);		
		  }
			 else
		  {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'];		
				 $resulValidar['datosFormDonacion']['CODPAISDOC']['errorMensaje'] = 'Solo puedes elegir NIE si el país es España';
		   $resulValidar['datosFormDonacion']['CODPAISDOC']['codError'] = '80303';
		  }	 
		}
		else	//=='Pasaporte' || =='Otros')   	
		{ $resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'] = validarNumPasaporte($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'],2,100,"");
		}		
		//echo "<br><br>3 validarCamposTesorero:validarFormComprobarDonante:resulValidar:"; print_r($resulValidar);
	}	
	/*----------------------------- Fin Validar documento NIF, NIE, Pasaporte -------------------*/
 //echo "<br><br>4 validarCamposTesorero:validarFormComprobarDonante:resulValidar:"; print_r($resulValidar);
	
 return $resulValidar;
}
//----------------------------- Fin validarFormComprobarDonante ----------------------------------

/*---------------- Inicio validarCamposAnotarIngresoDonacion -------------------------------------
DESCRIPCION:Valida los campos de los formularios para anotar o modificar una donación 
                   
Llamado desde: cTesorero.php:anotarIngresoDonacion(),modificarIngresoDonacionTes()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------------------------*/
function validarCamposAnotarIngresoDonacion($camposFormDonacion)
{//echo "<br><br>1 validarCamposTesorero:validarCamposAnotarIngresoDonacion:camposFormDonacion:";print_r($camposFormDonacion);

 $resValidarCamposForm = validarFormAnotarIngresoDonacion($camposFormDonacion);
	
	//echo "<br><br>2-1 validarCamposTesorero:validarCamposAnotarIngresoDonacion:resValidarCamposForm: ";print_r($resValidarCamposForm);
 $totalErroresLogicos = 0;
	$validarErrorLogico['errorMensaje'] ='';
	
	foreach ($resValidarCamposForm as $grupo => $valGrupo)//no es necesrio tratar error de sistema
	{ //echo "<br><br>2-1a validarCamposTesorero:validarCamposAnotarIngresoDonacion:grupo:[ ".$grupo." ], valGrupo: ";print_r($valGrupo);
	  
			foreach ($resValidarCamposForm[$grupo] as $nomCampo => $valNomCampo)	
  	{	 
		  //echo "<br><br>2-1b validarCamposTesorero:validarCamposAnotarIngresoDonacion:grupo:[ ".$grupo."], nomCampo[".$nomCampo."], valNomCampo: ";print_r($valNomCampo);
			
		  if ($resValidarCamposForm[$grupo][$nomCampo]['codError'] !== '00000')//todos son >= '80000'
		  { 
					 $validarErrorLogico['codError'] = $resValidarCamposForm[$grupo][$nomCampo]['codError'];
					  $validarErrorLogico['errorMensaje'] .=". ".$resValidarCamposForm[$grupo][$nomCampo]['errorMensaje']; 
					  $totalErroresLogicos +=1;
			 }		
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
 //echo "<br><br>3 validarCamposTesorero:validarCamposAnotarIngresoDonacion:resValidarCamposForm:";	print_r($resValidarCamposForm);
	return $resValidarCamposForm; //incluye arrayMensaje
}		
//---------------- Fin validarCamposAnotarIngresoDonacion ----------------------------------------

/*---------------- Inicio validarFormAnotarIngresoDonacion ---------------------------------------
DESCRIPCION:Valida los campos de formulario para anotar una donación por parte del tesorero 
            en los distintos tipos de casos de donates que hay           
Llamado desde: validarCamposTesorero.php:validarCamposAnotarIngresoDonacion()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)
------------------------------------------------------------------------------------------------*/
function validarFormAnotarIngresoDonacion($arrCamposForm)
{	
 //echo "<br><br>1 validarCamposTesorero:validarFormAnotarIngresoDonacion:arrCamposForm"; print_r($arrCamposForm);
 unset($arrCamposForm['siGuardarDatosDonacion']);	

	foreach($arrCamposForm as $campoGrupoForm => $valCampoGrupoForm )//para incluir los campos que no se validan
	{foreach($valCampoGrupoForm as $item => $valItem)
	 {$resulValidar[$campoGrupoForm][$item]['codError']='00000';
	 	$resulValidar[$campoGrupoForm][$item]['errorMensaje']='';
	 	$resulValidar[$campoGrupoForm][$item]['valorCampo']=$valItem;
		}
	}		
	//echo "<br><br>2 validarCamposTesorero:validarFormAnotarIngresoDonacion:arrCamposForm: "; print_r($arrCamposForm);
	
	/*----------- Inicio validar IDENTIFICADO-NO-SOCIO y SOCIO --------------------------------------*/
	
	/*------------------------ Inicio Validar documento NIF, NIE, Pasaporte, otros ------------*/
	if ($arrCamposForm['datosFormDonacion']['TIPODONANTE'] !== 'ANONIMO') 
	{
		if (isset($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']) && !empty($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']))
	 {
		 if ($arrCamposForm['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']=='NIF')
		 {if ($arrCamposForm['datosFormDonacion']['CODPAISDOC']=='ES')
		  {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']=
			      validarNif($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']);
	   }
		 	else
		  {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']=
			          $arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'];
			  $resulValidar['datosFormDonacion']['CODPAISDOC']['errorMensaje']='Solo puedes elegir NIF si el país es España';
		   $resulValidar['datosFormDonacion']['CODPAISDOC']['codError']='80303';	
		  }
		 }											 
			elseif ($arrCamposForm['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']=='NIE')	
		 {if ($arrCamposForm['datosFormDonacion']['CODPAISDOC']=='ES')
	   {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']=
			     validarNie($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']);		
	   }
		  else
	   {$resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']=
			          $arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'];
			  $resulValidar['datosFormDonacion']['CODPAISDOC']['errorMensaje']='Solo puedes elegir NIE si el país es España';
	    $resulValidar['datosFormDonacion']['CODPAISDOC']['codError']='80303';
	   }
	  }											
			else	//=='Pasaporte' || =='Otros')   	
			{ $resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']=
			                         validarNumPasaporte($arrCamposForm['datosFormDonacion']['NUMDOCUMENTOMIEMBRO'],2,100,"");
		 }
	 }/*------------------------ Fin Validar documento NIF, NIE, Pasaporte, otros ------------*/
	
		//$resulValidar['datosFormDonacion']['SEXO']=validarCampoRadio($arrCamposForm['datosFormDonacion']['SEXO'],"");		 php7 emite Notice				
			
		if (isset($arrCamposForm['datosFormDonacion']['SEXO']) || !empty($arrCamposForm['datosFormDonacion']['SEXO']))
		{ $resulValidar['datosFormDonacion']['SEXO']['valorCampo'] = $arrCamposForm['datosFormDonacion']['SEXO'];	
				$resulValidar['datosFormDonacion']['SEXO']['codError'] = '00000';
		}
  else
		{$resulValidar['datosFormDonacion']['SEXO']['codError'] ='80201';
	  $resulValidar['datosFormDonacion']['SEXO']['errorMensaje'] = "Sexo: debes elegir una opción";	
			$resulValidar['datosFormDonacion']['SEXO']['valorCampo'] = '';
		}	
		
		$resulValidar['datosFormDonacion']['NOM']=validarCampoNombres($arrCamposForm['datosFormDonacion']['NOM'],1,100, "");	
		$resulValidar['datosFormDonacion']['APE1']=validarCampoNombres($arrCamposForm['datosFormDonacion']['APE1'],1,100, "");
		
		if (isset($arrCamposForm['datosFormDonacion']['APE2']) && !empty($arrCamposForm['datosFormDonacion']['APE2']))
		{$resulValidar['datosFormDonacion']['APE2']=validarCampoNombres($arrCamposForm['datosFormDonacion']['APE2'],1,100, "");
		}
		if (isset($arrCamposForm['datosFormDonacion']['EMAIL']) && !empty($arrCamposForm['datosFormDonacion']['EMAIL']))
		{$resulValidar['datosFormDonacion']['EMAIL']=validarEmail($arrCamposForm['datosFormDonacion']['EMAIL'],"");
		}		
	 $resulValidar['datosFormDonacion']['TELFIJOCASA']=validarTelefono($arrCamposForm['datosFormDonacion']['TELFIJOCASA'],9,14,"");
	 $resulValidar['datosFormDonacion']['TELMOVIL']=validarTelefono($arrCamposForm['datosFormDonacion']['TELMOVIL'],9,14,"");			
	
		//echo "<br><br>3 validarCamposTesorero:validarFormAnotarIngresoDonacion:resulValidar"; print_r($resulValidar);
		
	}/*----------- Fin validar IDENTIFICADO-NO-SOCIO y SOCIO -----------------------------------------*/
	
	//------------------------ Inicio validar socio ----------------------------------------
 if ($arrCamposForm['datosFormDonacion']['TIPODONANTE'] == 'socio') //donante socio ya existente	
	{ //no se valida nada especifico de socio porque los datos personales ya se han validado
	  //echo "<br><br>4 validarCamposTesorero:validarFormAnotarIngresoDonacion:resulValidar"; print_r($resulValidar);
	}	
	//------------------------ Fin validar socio --------------------------------------------
	//------------------------ Inicio validar ANONIMO ---------------------------------------	
 elseif ($arrCamposForm['datosFormDonacion']['TIPODONANTE'] == 'ANONIMO') //donante socio ya existente	
	{ //No se valida nada específico en anónimo		
	 //echo "<br><br>5 validarCamposTesorero:validarFormAnotarIngresoDonacion:resulValidar"; print_r($resulValidar);
	}
	//------------------------ Fin validar ANONIMO -----------------------------------------

	//echo "<br><br>6-1 validarCamposTesorero:validarFormAnotarIngresoDonacion:resulValidar"; print_r($resulValidar);
	
	/*---------------- Inicio campos datosFormDonacion comunes a todo tipo de donantes ----------------*/
 
	//------------------------ Inicio validar fecha de pago  donación -----------------------

	//$fechInf = '2009-01-01'; //puede ser cualquiera desde el inicio de anotar cuotas		
	$fechInf = (date('Y')-1)."-01-01";	//inicio no puede ser antes del año anterior				
	$fechSup = date('Y-m-d');	//no puede ser superior que el día actual				
	$permitirVacio = false;	
	
 //echo "<br><br>6-2 validarCamposTesorero:validarFormAnotarIngresoDonacion:fechInf: ".$fechInf;	//no puede ser antes del año anterior		
																																																																							
 $resulValidar['datosFormDonacion']['FECHAINGRESO'] =	validarFechaLimites($arrCamposForm['datosFormDonacion']['FECHAINGRESO'],
	                                                     $fechInf,$fechSup,$permitirVacio);

 //------------------------ Fin validar fecha de pago  donación) -------------------------
																																																						
	$resulValidar['datosFormDonacion']['IMPORTEDONACION'] = validarCantidadDecimal($arrCamposForm['datosFormDonacion']['IMPORTEDONACION'],0,1000000.00,"");				
						
	if (isset($arrCamposForm['datosFormDonacion']['GASTOSDONACION']) && !empty($arrCamposForm['datosFormDonacion']['GASTOSDONACION']))
 { $resulValidar['datosFormDonacion']['GASTOSDONACION']= validarCantidadDecimal($arrCamposForm['datosFormDonacion']['GASTOSDONACION'],0.00,10000.00,"");	
	}
 else
	{ $resulValidar['datosFormDonacion']['GASTOSDONACION']['valorCampo'] = '0.00';
	  $resulValidar['datosFormDonacion']['GASTOSDONACION']['codError'] = '00000';	 		
	}	
	
	/*---------------- Fin campos datosFormDonacion comunes a todo tipo de donantes -------------------*/
			
	//echo "<br><br>7 validarCamposTesorero:validarFormAnotarIngresoDonacion:resulValidar: "; print_r($resulValidar);
 return $resulValidar;
}
//----------------------------- Fin validarFormAnotarIngresoDonacion -----------------------------

/*---------------- Inicio validarCamposDonacionConcepto ------------------------------------------
DESCRIPCION: Valida los campos del formulario "formAniadirDonacionConcepto.php" 

RECIBE:  $arrCamposForm = array['CONCEPTO'], ['OBSERVACIONES']
DEVUELVE: array "$resulValidar" con los valores de los campos validados y campos de errores

LLAMADA: cTesorero.php:aniadirDonacionConceptoTes()
LLAMA: modeloTesorero.php:buscarDonacionConceptos()
modelos/libs/validarCampos.php:validarMayusculasNumerosGuiones(),validarCampoTexto()
-------------------------------------------------------------------------------------------------*/
function validarCamposDonacionConcepto($arrCamposForm)
{
	//echo "<br><br>0-1 validarCamposTesorero:validarCamposDonacionConcepto:arrCamposForm: "; print_r($arrCamposForm);
	
	$resulValidar['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['errorMensaje'] = '';

	/*---------- Inicio	validación CONCEPTO -----------------------------------------------*/	
 
	$resulValidar['CONCEPTO'] = validarMayusculasNumerosGuiones($arrCamposForm['CONCEPTO'],4,50,"Corregir");		
	
	if ($resulValidar['CONCEPTO']['codError'] !== '00000')	
	{$resulValidar['codError'] = $resulValidar['CONCEPTO']['codError'];
  //$resulValidar['errorMensaje'] = $resulValidar['CONCEPTO']['errorMensaje'];
	}
	else
	{
  //--- Inicio validación campo CONCEPTO NO repetido en tabla DONACIONCONCEPTOS ---	
		
		require_once './modelos/modeloTesorero.php';	
		$resulDonacionConceptos = buscarDonacionConceptos($arrCamposForm['CONCEPTO']);//en modeloTesroro.php, busca en DONACIONCONCEPTO			
		
		//echo "<br><br>1-1 modeloTesorero:validarCamposDonacionConcepto:resulDonacionConceptos: ";print_r($resulDonacionConceptos);
		
		if ($resulDonacionConceptos['codError'] !== '00000')
		{ $resulValidar['CONCEPTO'] = $resulDonacionConceptos;
	   $resulValidar['codError'] = $resulDonacionConceptos['codError'];
    $resulValidar['errorMensaje'] = $resulDonacionConceptos['errorMensaje'];				
				$resInsertarDonacionConceptos['textoComentarios'] = $resulDonacionConceptos['textoComentarios'];	
		}
		elseif ($resulDonacionConceptos['numFilas'] >= 1)
		{				
				$resulValidar['CONCEPTO']['codError'] = '80002';
				$resulValidar['CONCEPTO']['errorMensaje'] = "Ya existe ese concepto";	 
				$resulValidar['codError'] = $resulValidar['CONCEPTO']['codError'] = '80002';
				$resulValidar['errorMensaje'] =		$resulValidar['CONCEPTO']['errorMensaje'] = "Ya existe ese concepto";	 	
		}
		else // $resulDonacionConceptos['codError']=='00000'
		{		
		}
		//--- Fin validación campo CONCEPTO NO repetido ----------------------	
		/*--- Fin validación campo CONCEPTO -------------------------------------------------*/	
	}	
	//echo "<br><br>1-2 validarCamposTesorero:validarCamposDonacionConcepto:resulValidar: ";print_r($resulValidar);
		
		/*----------Inicio	validar NOMBRECONCEPTO -------------------------------------------*/
 if (isset($arrCamposForm['NOMBRECONCEPTO']) && !empty($arrCamposForm['NOMBRECONCEPTO']))
	{
		$resulValidar['NOMBRECONCEPTO'] = validarCampoTexto($arrCamposForm['NOMBRECONCEPTO'],5,255,"");		
 		
		if ($resulValidar['NOMBRECONCEPTO']['codError'] !== '00000')	
		{$resulValidar['codError'] = $resulValidar['NOMBRECONCEPTO']['codError'];
			$resulValidar['errorMensaje'] = $resulValidar['NOMBRECONCEPTO']['errorMensaje'];
		}
 }
 else
	{$resulValidar['NOMBRECONCEPTO']['valorCampo'] = $arrCamposForm['NOMBRECONCEPTO'];
	 $resulValidar['NOMBRECONCEPTO']['codError'] = '00000'; 	
 }
 /*---------- FIN	validar NOMBRECONCEPTO ----------------------------------------------*/			
	//echo "<br><br>2 validarCamposTesorero:validarCamposDonacionConcepto:resulValidar: ";print_r($resulValidar); 
	
	/*----------Inicio	validar OBSERVACIONES ---------------------------------------------*/
 if (isset($arrCamposForm['OBSERVACIONES']) && !empty($arrCamposForm['OBSERVACIONES']))
	{
		$resulValidar['OBSERVACIONES'] = validarCampoTexto($arrCamposForm['OBSERVACIONES'],0,255,"");		
 		
		if ($resulValidar['OBSERVACIONES']['codError'] !== '00000')	
		{$resulValidar['codError'] = $resulValidar['OBSERVACIONES']['codError'];
			//$resulValidar['errorMensaje'] = $resulValidar['OBSERVACIONES']['errorMensaje'];
		}
 }
 else
	{$resulValidar['OBSERVACIONES']['valorCampo'] = $arrCamposForm['OBSERVACIONES'];
	 $resulValidar['OBSERVACIONES']['codError'] = '00000'; 	
 }
 /*---------- FIN	validar OBSERVACIONES --------------------------*---------------------*/			
	//echo "<br><br>3 validarCamposTesorero:validarCamposDonacionConcepto:resulValidar: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarCamposDonacionConcepto -----------------------------------*/

/*============================ FIN VALIDAR DONACIONES ===========================================*/


/*=========================== INICIO VALIDAR CUOTAS VIGENTES EL  ================================*/

/*---------------- Inicio validarCamposFormCuotasVigentesEL ---------------------------------------

DESCRIPCION: Valida los campos de formulario de formCambiarCuotasVigentesELTes.php 

RECIBE:  $arrCamposForm = array['datosFormCuotasVigentesEL'][ANIOCUOTA],[CODCUOTA],[IMPORTECUOTAANIOEL],
[IMPORTECUOTAANIOEL_NUEVO],[DESCRIPCIONCUOTA]

Sólo valida ['IMPORTECUOTAANIOEL_NUEVO'] que sea formato decimal entre  0 y el máximo 100000.00 

Los demás campos los devuelve con el mismo valor ya que readonly 

LLAMADA: cTesorero.php:actualizarCuotasVigentesELTes()
LLAMA:  modelos/libs/validarCampos.php (varias funciones)
--------------------------------------------------------------------------------------------------*/
function validarCamposFormCuotasVigentesEL($arrCamposForm)
{//echo "<br><br>1 validarCamposTesorero:validarCamposFormCuotasVigentesEL:arrCamposForm:"; print_r($arrCamposForm);
	
	foreach($arrCamposForm['datosFormCuotasVigentesEL'] as $item=>$valItem)
	{	//echo "<br><br>2-1 validarCamposTesorero:validarCamposFormCuotasVigentesEL:valItem:"; 	print_r($valItem);	
	 $resulValidar['datosFormCuotasAnioSiguienteEL'][$item]['codError']='00000';
	 $resulValidar['datosFormCuotasAnioSiguienteEL'][$item]['errorMensaje']='';
	 $resulValidar['datosFormCuotasAnioSiguienteEL'][$item]['valorCampo']=$valItem;
	}
	//echo "<br><br>2-2 validarCamposTesorero:validarCamposFormCuotasVigentesEL:resulValidar['datosFormCuotasAnioSiguienteEL']:";print_r($resulValidar);	
	
 $resulValidar['datosFormCuotasAnioSiguienteEL']['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['datosFormCuotasAnioSiguienteEL']['errorMensaje'] = '';
								
 $resulValidar['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO'] = validarCantidadDecimal($arrCamposForm['datosFormCuotasVigentesEL']['IMPORTECUOTAANIOEL_NUEVO'],0,10000.00,"");										
	
	if ($resulValidar['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['codError'] !== '00000')	
	{$resulValidar['datosFormCuotasAnioSiguienteEL']['codError'] = $resulValidar['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['codError'];
  $resulValidar['datosFormCuotasAnioSiguienteEL']['errorMensaje'] = $resulValidar['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['errorMensaje'];
	}										
		
	//echo "<br><br>3 validarCamposTesorero:validarCamposFormCuotasVigentesEL:resulValidar['datosFormCuotasAnioSiguienteEL']: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarCamposFormCuotasVigentesEL -------------------------------*/

/*=========================== FIN VALIDAR CUOTAS VIGENTES EL  ===================================*/


/*============================ INICIO VALIDAR ORDENES COBROS Y RELACIONADO ========================
 - validarFormOrdenCuotasBancos() 
	- validarCamposFormAEB19Cuotas(): Ya no se usa  
 - validarFormExcelCuotasInterno() 
 - validarEmailAvisarCuotaDomic_Y_SinDomic()	
 - validarEmailAvisarDomiciliadosProximoCobro()
 - validarEmailAvisarCuotaNoCobradaSinCC()	
 - exportarEmailAvisarCuotas()	
 - validarCamposFormActualizarOrdenCobroRemesa()
=================================================================================================*/	

/*---------------- Inicio validarFormOrdenCuotasBancos --------------------------------------------
DESCRIPCION: Valida los campos de formulario de: vXMLCuotasInc.php, vExcelCuotasTesoreroBancosInc.php, 
(formAEB19Cuotas.php??)

RECIBE: "$arrCamposForm" un Array con los datos enviados por el controlador de llamada, 
procedentes del los correspondientes formularios.

LLAMADA: cTesorero.php:XMLCuotasTesoreroSantander(),excelCuotasTesoreroBancos()
LLAMA: modelos/libs/validarCampos.php (varias funciones) 

OBSERVACIONES: Similar al anteriormente usado validarCamposFormAEB19Cuotas($arrCamposForm)

NOTA: al ser compartido por dos formularios y funciones ligeramamente distintas 
el campo "$arrCamposForm['fechacobro']" en la función "excelCuotasTesoreroBancos()" tiene el 
significado de "fecha creación del archivo Excel"
-------------------------------------------------------------------------------------------------*/
function validarFormOrdenCuotasBancos($arrCamposForm)
{//echo "<br><br>0-1 validarCamposTesorero:validarFormOrdenCuotasBancos:arrCamposForm: "; print_r($arrCamposForm);
	/*
	//Activar esto si se quiere incluir el resto de los campos procedentes del formulario como readonly, headden
 //si se necesitan, normalmente se incluyen desde controlador o funciones de modelo correspondiente
	foreach($arrCamposForm as $item=>$valItem)
	{	
	 $resulValidar[$item]['codError']='00000';
	 $resulValidar[$item]['errorMensaje']='';
	 $resulValidar[$item]['valorCampo']=$valItem;
	}
	echo "<br><br>1 validarCamposTesorero:validarFormOrdenCuotasBancos:resulValidar['datosFormRemesaBanco']: ";print_r($resulValidar);	
	*/
	//------------------- Inicio validar IVA ------------------------------------------------
 $resulValidar['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['errorMensaje'] = '';		
	
	//--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	--------
	if (!isset($arrCamposForm['agrupaciones']) /*|| count($arrCamposForm['agrupaciones'] ==0)*/)
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';
		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] = '00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}
 //echo "<br><br>2 validarCamposTesorero:validarFormOrdenCuotasBancos:resulValidar: "; print_r($resulValidar);	
	
 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	
 /* en envío email
	if (!isset($arrCamposForm['agrupaciones']) || empty( array_filter($arrCamposForm['agrupaciones']) ) )//mejor opción controla además si todos los elementos del array son NULL o " "
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] = '00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}	*/
	//--
	
	//--- Inicio validar tipo cuenta bancaria, ES, otros países de europa SEPA, No SEPA, no Puede haber error: es un radio button
 $resulValidar['paisCC']['valorCampo'] = $arrCamposForm['paisCC'];
 $resulValidar['paisCC']['codError'] = '00000';	
	$resulValidar['paisCC']['errorMensaje'] = '';		

 $resulValidar['IVA'] =	validarCantidadDecimal($arrCamposForm['IVA'],0,100.00,"");	

	if ($resulValidar['IVA']['codError'] !== '00000')	
	{$resulValidar['codError'] = $resulValidar['IVA']['codError'];
  $resulValidar['errorMensaje'] = $resulValidar['IVA']['errorMensaje'];
	}	
	//------------------- Fin validar tipo cuenta bancaria ---------------------------------
	
	//-- Inicio año cuota a cobrar (fijo=date(Y) desde formulario no necesita validar ------
	if (!isset($arrCamposForm['anioCuotasElegido']) || empty($arrCamposForm['anioCuotasElegido']) )	
	{$resulValidar['anioCuotasElegido']['codError'] = '80201';	
  $resulValidar['anioCuotasElegido']['errorMensaje'] = 'Debes elegir el año de la cuota a cobrar';
	}
	else
	{$resulValidar['anioCuotasElegido']['codError'] = '00000';	
	 $resulValidar['anioCuotasElegido']['errorMensaje'] = '';	 		
	}
	$resulValidar['anioCuotasElegido']['valorCampo'] = $arrCamposForm['anioCuotasElegido'];
	//------------------- Fin año cuota a cobrar --------------------------------------------
	
	//----------------- Inicio validar fechacobro (fecha de cargo del cobro por el banco) ---
	$fechInf = date('Y-m-d');	//no puede ser menor que el día actual			
	$fechSup = date('Y')+1 .'-12-31'; //no puede ser superior al final del siguiente año	
	$permitirVacio = false;	
	
	$resulValidar['fechacobro'] = validarFechaLimites($arrCamposForm['fechacobro'],$fechInf,$fechSup,$permitirVacio);
	
	if ($resulValidar['fechacobro']['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechacobro']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechacobro']['errorMensaje'];
	} 	
	//echo "<br><br>3 validarCamposTesorero:validarFormOrdenCuotasBancos:resulValidar: ";print_r($resulValidar); 
						
	//------------------- Fin validar fechacobro -------------------------------------------	
	
	//--- Inicio validar fechaAltaExentosPago (fecha de alta del socio por encima de la cual no se les cobra) --
	
	/* En segundas, terceras órdenes de cobro "de repesca" el tesorero elige un fecha para excluir pago cuotas a altas
	   con fechas posteriores al valor $fechaAltaExentosPago que serán fechas de alta cerca de final año 
	*/
	$fechInf = date('Y') .'-01-01'; //limite inicio de año actual
	$fechSup = date('Y-m-d');	//no puede ser superior que el día actual				
	
	$permitirVacio = false;	
	
	$resulValidar['fechaAltaExentosPago'] = validarFechaLimites($arrCamposForm['fechaAltaExentosPago'],$fechInf,$fechSup,$permitirVacio);
	
	if ($resulValidar['fechaAltaExentosPago']['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechaAltaExentosPago']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechaAltaExentosPago']['errorMensaje'];
	} 									
	//------------------- Fin validar fechaAltaExentosPago ----------------------------------	
	
 //echo "<br><br>4 validarCamposTesorero:validarFormOrdenCuotasBancos:resulValidar: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarFormOrdenCuotasBancos ------------------------------------*/

/*---------------- Inicio validarCamposFormAEB19Cuotas --------------------------------------------
DESCRIPCION:Valida los campos de formulario de formAEB19Cuotas.php 
Recibe en "$arrCamposForm" un Array([datosAEB19Cuotas] con [anioCuotasElegido],
[fechaAltaExentosPago][dia][mes][anio], [paisCC] y datos de 

Llamado desde: cTesorero.php:AEB19CuotasTesoreroSantander() y XMLCuotasTesoreroSantander()
Llama funciones:  modelos/libs/validarCampos.php 
-------------------------------------------------------------------------------------------------*/
function validarCamposFormAEB19Cuotas($arrCamposForm)
{//echo "<br><br>1 validarCamposTesorero:validarCamposFormAEB19Cuotas:arrCamposForm:"; print_r($arrCamposForm);
	/*
	foreach($arrCamposForm['datosAEB19Cuotas'] as $item=>$valItem)
	{	echo "<br><br>2-1 validarCamposTesorero:validarCamposFormAEB19Cuotas:valItem:"; 	print_r($valItem);	
	 $resulValidar['datosAEB19Cuotas'][$item]['codError']='00000';
	 $resulValidar['datosAEB19Cuotas'][$item]['errorMensaje']='';
	 $resulValidar['datosAEB19Cuotas'][$item]['valorCampo']=$valItem;
	}
	echo "<br><br>2-2 validarCamposTesorero:validarCamposFormAEB19Cuotas:resulValidar['datosAEB19Cuotas']:";print_r($resulValidar);	
	*/
	//------------------- Inicio validar IVA ------------------------------------------------
 $resulValidar['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['errorMensaje'] = '';
	
	
	
	 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	
	if (!isset($arrCamposForm['agrupaciones']) /*|| count($arrCamposForm['agrupaciones'] ==0)*/)
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';
		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] ='00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}
 //echo "<br><br>2-3 validarCamposTesorero:validarCamposFormAEB19Cuotas:resulValidar:"; print_r($resulValidar);	
	
		//--- Inicio validar tipo cuenta bancaria, ES, otros paises de europa SEPA, No SEPA, no Puede haber error: es un radio button
 $resulValidar['paisCC']['valorCampo'] = $arrCamposForm['paisCC'];
 $resulValidar['paisCC']['codError'] ='00000';	
	$resulValidar['paisCC']['errorMensaje'] = '';	
	

 $resulValidar['IVA'] =	validarCantidadDecimal($arrCamposForm['IVA'],0,100.00,"");	
	
	if ($resulValidar['IVA']['codError'] !== '00000')	
	{$resulValidar['codError'] = $resulValidar['datosAEB19Cuotas']['IVA']['codError'];
  $resulValidar['errorMensaje'] = $resulValidar['datosAEB19Cuotas']['IVA']['errorMensaje'];
	}	
	//------------------- Fin validar IVA ---------------------------------------------------
	
	//------------------- Inicio año cuota a cobrar ----------------------------------------
	if ($arrCamposForm['anioCuotasElegido'] == '0')	
	{$resulValidar['anioCuotasElegido']['codError'] = '80201';	
  $resulValidar['anioCuotasElegido']['errorMensaje'] = 'Debes elegir el año de la cuota a cobrar';
	}
	else
	{$resulValidar['anioCuotasElegido']['codError'] = '00000';	
	 $resulValidar['anioCuotasElegido']['errorMensaje'] = '';	 		
	}
	$resulValidar['anioCuotasElegido']['valorCampo'] = $arrCamposForm['anioCuotasElegido'];
	//------------------- Fin año cuota a cobrar --------------------------------------------
	
	//----------------- Inicio validar fechacobro (fecha de cargo del cobro por el banco) ---
	$fechInf = date('Y-m-d');	//no puede ser menor que el día actual			
	$fechSup = date('Y')+1 .'-12-31'; //no puede ser superior al final del siguiente año	
	$permitirVacio = false;	
	
	$resulValidar['fechacobro'] = validarFechaLimites($arrCamposForm['fechacobro'],
	                                                                      $fechInf,$fechSup,$permitirVacio);
	
	if ($resulValidar['fechacobro']['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechacobro']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechacobro']['errorMensaje'];
	} 	
	//echo "<br><br>3 validarCamposTesorero:validarCamposFormAEB19Cuotas:resulValidar:";print_r($resulValidar); 
						
	//------------------- Fin validar fechacobro -------------------------------------------	
	
	//--- Inicio validar fechaAltaExentosPago (fecha de alta del socio por encima de la cual no se les cobra) --
	// en segundas, terceras órdenes de cobro "de repesca" el tesorero elige un fecha para excluir pago cuotas a altas
	// con fechas posteriores al valor $fechaAltaExentosPago que serán fechas de alta cerca de final año
	 
	// $resulValidar['fechaAltaExentosPago']= validarFechaAltaExcluirPagoCuota($arrCamposForm);
	
	$fechInf = date('Y') .'-01-01'; //limite inicio de año actual	
	//echo "<br><br>3 validarCamposTesorero:validarCamposFormAEB19Cuotas:fechInf:";print_r($fechInf); 

	$fechSup = date('Y-m-d');	//no puede ser superior que el día actual				
	//echo "<br><br>3 validarCamposTesorero:validarCamposFormAEB19Cuotas:fechSup:";print_r($fechSup); 
	$permitirVacio = false;	
	
	$resulValidar['fechaAltaExentosPago'] = validarFechaLimites($arrCamposForm['fechaAltaExentosPago'],$fechInf,$fechSup,$permitirVacio);
	
	if ($resulValidar['fechaAltaExentosPago']['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechaAltaExentosPago']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechaAltaExentosPago']['errorMensaje'];
	} 									
	//------------------- Fin validar fechaAltaExentosPago ----------------------------------	
 //echo "<br><br>4 validarCamposTesorero:validarCamposFormAEB19Cuotas:resulValidar:";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarCamposFormAEB19Cuotas ------------------------------------*/

/*---------------- Inicio validarFormExcelCuotasInternos ------------------------------------------
DESCRIPCION: Valida los campos de formulario de formExcelCuotasInternoTesorero.php 

RECIBE: "$arrCamposForm" un Array con los datos enviados por el controlador de llamada, 
procedentes del los correspondiente formulario.

LLAMADA: cTesorero.php:exportarExcelCuotasInternoTes()
LLAMA: nada

OBSERVACIONES: 
-------------------------------------------------------------------------------------------------*/
function validarFormExcelCuotasInterno($arrCamposForm)
{//echo "<br><br>0-1 validarCamposTesorero:validarFormExcelCuotasInterno:arrCamposForm: "; print_r($arrCamposForm);
	
	//Activar esto si se quiere incluir el resto de los campos procedentes del formulario como readonly, headden
 //si se necesitan, normalmente se incluyen desde controlador o funciones de modelo correspondiente
	foreach($arrCamposForm as $item=>$valItem)
	{
	 $resulValidar[$item]['codError']='00000';
	 $resulValidar[$item]['errorMensaje']='';
	 $resulValidar[$item]['valorCampo']=$valItem;
	}
	//echo "<br><br>1 validarCamposTesorero:validarFormExcelCuotasInterno:resulValidar: ";print_r($resulValidar);	
	
	//------------------- Inicio validar IVA ------------------------------------------------
 $resulValidar['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['errorMensaje'] = '';		
	
	 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	
	if (!isset($arrCamposForm['agrupaciones']) /*|| count($arrCamposForm['agrupaciones'] ==0)*/)
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';
		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] ='00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}
 //echo "<br><br>2 validarCamposTesorero:validarFormExcelCuotasInterno:resulValidar: "; print_r($resulValidar);	
	
 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	
 /* en envío email
	if (!isset($arrCamposForm['agrupaciones']) || empty( array_filter($arrCamposForm['agrupaciones']) ) )//mejor opción controla además si todos los elementos del array son NULL o " "
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] = '00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}	*/
 //------------------- Inicio estado Cuotas ---------------------------------------------
	if (!isset($arrCamposForm['estadosCuotas']) /*|| count($arrCamposForm['estadosCuotas'] ==0)*/)
	{$resulValidar['estadosCuotas']['codError'] = '80201';	
	 $resulValidar['estadosCuotas']['errorMensaje'] = 'No has elegido ningún estado de Cuotas ';
		
  $resulValidar['codError'] = $resulValidar['estadosCuotas']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['estadosCuotas']['errorMensaje'];
	}	
	else
	{$resulValidar['estadosCuotas']['valorCampo'] = $arrCamposForm['estadosCuotas'];
  $resulValidar['estadosCuotas']['codError'] = '00000';	
	 $resulValidar['estadosCuotas']['errorMensaje'] = '';
	}
 //echo "<br><br>3 validarCamposTesorero:validarFormExcelCuotasInterno:resulValidar: "; print_r($resulValidar);	
	
	//--- Inicio validar tipo cuenta bancaria, ES, otros paises de europa SEPA, No SEPA, no Puede haber error: es un radio button
 $resulValidar['paisCC']['valorCampo'] = $arrCamposForm['paisCC'];
 $resulValidar['paisCC']['codError'] = '00000';	
	$resulValidar['paisCC']['errorMensaje'] = '';		


	//------------------- Fin estado Cuotas ------------------------------------------------
	
	//------------------- Inicio año cuota a cobrar ----------------------------------------
	if (!isset($arrCamposForm['anioCuotasElegido']) || empty($arrCamposForm['anioCuotasElegido']))	
	{$resulValidar['anioCuotasElegido']['codError'] = '80201';	
  $resulValidar['anioCuotasElegido']['errorMensaje'] = 'Debes elegir el año de la cuota a cobrar';
	}
	else
	{$resulValidar['anioCuotasElegido']['codError'] = '00000';	
	 $resulValidar['anioCuotasElegido']['errorMensaje'] = '';	 		
	}
	$resulValidar['anioCuotasElegido']['valorCampo'] = $arrCamposForm['anioCuotasElegido'];
	//------------------- Fin año cuota a cobrar --------------------------------------------

 // echo "<br><br>4 validarCamposTesorero:validarFormExcelCuotasInterno:resulValidar: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarFormExcelCuotasInterno -----------------------------------*/

/*---------------- Inicio validarEmailAvisarCuotaDomic_Y_SinDomic----------------------------------
DESCRIPCION: FUNCION COMPATID: Valida la mayor parte de los los campos de los formularios:
"validarEmailAvisarDomiciliadosProximoCobro.php" y "formEmailAvisarCuotaNoCobradaSinCC.php"  

Recibe en "$arrCamposForm" un Array con ['agrupaciones'],'paisCC'], ['URLgastosLaicismo'],
[fechaAltaExentosPago][dia][mes][anio,], ...

ES UNA "FUNCION COMPATIDA" PARA VALIDAR LOS DOS FORMULARIOS (excepto en la validación de dos 
campos que se validan independientemente)

LLAMADA: validarCamposTesorero.php:validarEmailAvisarDomiciliadosProximoCobro(),validarEmailAvisarCuotaNoCobradaSinCC()
que a su vez se llaman desde cTesorero.php:emailAvisarCuotaNoCobradaSinCC()
LLAMA: algunas funciones de modelos/libs/validarCampos.php 

OBSERVACIONES: probado PHP 7.2.31
2020-10-22: añado más controles, para evitar riesgo con estos parámetros a pasarlos a la select,
dado lo crítico que es envíar un email no deseado. Además habrá otros controles adiccionales 
sobre estos parámetros. 
NOTA: Actualmente quito filtros de caracteres, y solo dejo que control de longitud mínima y máxima
     de caracteres, y validarFechaLimites() 
	
--------------------------------------------------------------------------------------------------*/
function validarEmailAvisarCuotaDomic_Y_SinDomic($arrCamposForm)
{
	//echo "<br><br>0-1 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:arrCamposForm: "; print_r($arrCamposForm);
	
	$resulValidar['codError'] = '00000';
 $resulValidar['errorMensaje'] = '';	
	
	//--- Inicio validar campos que hay texto para el body email -------------------------------

	if (!isset($arrCamposForm['textoEmail']) || empty( array_filter($arrCamposForm['textoEmail']) ) )
	{ $resulValidar['textoEmail']['codError'] = '80201';	
			$resulValidar['textoEmail']['errorMensaje'] = 'Error: No hay texto para el contenido del cuerpo del email';		
			$resulValidar['codError'] = $resulValidar['textoEmail']['codError'];
			$resulValidar['errorMensaje'] .= $resulValidar['textoEmail']['errorMensaje'];
	}
	else
	{	$resulValidar['textoEmail'] = $arrCamposForm['textoEmail'];

	  //--- Inicio validar que hay texto[nota] para el body 
			if (isset($arrCamposForm['textoEmail']['nota']) && !empty( $arrCamposForm['textoEmail']['nota']) )// es un campo opcional y puede estar vacío y entonces no se vlida el contenido
			{ //NOTA: Actualmente quito filtros de caracteres, y solo dejo que control de longitud mínima y máxima de caracteres. 		
			  //$resulValidar['textoEmail']['nota'] = validarTextArea($arrCamposForm['textoEmail']['nota'],0,400,"");
     $resulValidar['textoEmail']['nota'] = validarTextoBodyEmailGes($arrCamposForm['textoEmail']['nota'],0,600,"");// se validan lo introducido						
	                                         //validarTextoBodyEmailGes($cadTexto,$longMin,$longMax, $textoErrorCampo)
					if (	$resulValidar['textoEmail']['nota']['codError'] !== '00000')
					{ 
						$resulValidar['codError'] =  $resulValidar['textoEmail']['nota']['codError'];//o bien el último error:$validarErrorLogico['codError']
						$resulValidar['errorMensaje'] .=". ". $resulValidar['textoEmail']['nota']['errorMensaje'];//concatenación errorMensaje					 
					}		
			}
   //--- Fin validar que hay texto[nota] para el body 			
	}
	//echo "<br><br>1 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:resulValidar['textoEmail']: "; print_r($resulValidar['textoEmail']);	
 //--- Fin validar que hay texto para el body ------------------------------------------------

	//--- Inicio validar el texto que se inserta en el email, con URLgastosLaicismo.org, no es obligatorio este campo																																							
 $resulValidar['URLgastosLaicismo']['valorCampo'] = $arrCamposForm['URLgastosLaicismo'];
 $resulValidar['URLgastosLaicismo']['codError']  = '00000';	
 $resulValidar['URLgastosLaicismo']['errorMensaje'] ='';	
 
		
	//-- Inicio año cuota a cobrar (fijo=date(Y) desde formulario, no necesita validar ------
	if (!isset($arrCamposForm['anioCuotasElegido']) || empty( $arrCamposForm['anioCuotasElegido']) )
	{$resulValidar['anioCuotasElegido']['codError'] = '80302';
	 $resulValidar['anioCuotasElegido']['errorMensaje'] = 'Debes elegir el año de la cuota a cobrar';	
  $resulValidar['codError'] = $resulValidar['anioCuotasElegido']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['anioCuotasElegido']['errorMensaje'];
	}
	else
	{	$resulValidar['anioCuotasElegido'] = $arrCamposForm['anioCuotasElegido'];
 }		
	//echo "<br><br>3 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:resulValidar['anioCuotasElegido']: "; print_r($resulValidar['anioCuotasElegido']);	
	
	//--- Inicio validar fechaAltaExentosPago (fecha de alta del socio por encima de la cual no se les cobra) --
	// en segundas, terceras órdenes de cobro "de repesca" el tesorero elige un fecha para excluir pago cuotas a altas
	// con fechas posteriores al valor $fechaAltaExentosPago que serán fechas de alta cerca de final año 
	
	$fechInf = date('Y') .'-01-01'; //limite inicio de año actual	
	$fechSup = date('Y-m-d');	//no puede ser superior que el día actual
	$permitirVacio = false;	
	
	$resulValidar['fechaAltaExentosPago'] = validarFechaLimites($arrCamposForm['fechaAltaExentosPago'],$fechInf,$fechSup,$permitirVacio);
 	
	if ($resulValidar['fechaAltaExentosPago']['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechaAltaExentosPago']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechaAltaExentosPago']['errorMensaje'];
	} 	
 //echo "<br><br>4 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:resulValidar['fechaAltaExentosPago']: "; print_r($resulValidar['fechaAltaExentosPago']);	
 //--- Fin validar fechaAltaExentosPago (fecha de alta del socio por encima de la cual no se les cobra) --	
		
	//--- Inicio validar tipo cuenta banco: No tiene cuenta, Tiene cuenta bancaria de países NO SEPA, Cuenta bancaria de países SEPA distintos de España. No debiera haber error: es un radio button
		
	if (!isset($arrCamposForm['paisCC']) || empty($arrCamposForm['paisCC']) )
	{$resulValidar['paisCC']['codError'] = '80201';	
	 $resulValidar['paisCC']['errorMensaje'] = 'No has elegido ningún tipo de cuenta';		
  $resulValidar['codError'] = $resulValidar['paisCC']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['paisCC']['errorMensaje'];
	}	
	else
	{$resulValidar['paisCC']['valorCampo'] = $arrCamposForm['paisCC'];
  $resulValidar['paisCC']['codError'] = '00000';	
	 $resulValidar['paisCC']['errorMensaje'] = '';	
	}	
	//echo "<br><br>5 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:resulValidar['paisCC']: "; print_r($resulValidar['paisCC']);
	
 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación		

	if (!isset($arrCamposForm['agrupaciones']) || empty( array_filter($arrCamposForm['agrupaciones']) ) ) 
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email de aviso de cobro de cuota';		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo'] = $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError'] = '00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}
 //echo "<br><br>6 validarCamposTesorero:validarEmailAvisarCuotaDomic_Y_SinDomic:resulValidar['agrupaciones']: "; print_r($resulValidar['agrupaciones']);
	
	//echo "<br><br>7 validarCamposTesorero:validarEmailAvisarCuotaNoCobradaSinCC:resulValidar: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarEmailAvisarCuotaDomic_Y_SinDomic -------------------------*/

/*---------------- Inicio validarEmailAvisarDomiciliadosProximoCobro ------------------------------
DESCRIPCION: Valida los campos de formulario de "formEmailAvisarDomiciliadosProximoCobro.php.php" 
Recibe en "$arrCamposForm" un Array con ['agrupaciones'],'paisCC'],['textoFechaPrevistaCobro'],
['URLgastosLaicismo'],[fechaAltaExentosPago][dia][mes][anio,]

LLAMADA: cTesorero.php:emailAvisarDomiciliadosProximoCobro()
LLAMA:  modelos/libs/validarEmailAvisarCuotaDomic_Y_SinDomic() y funciones validarCampos.php 

OBSERVACIONES: probado PHP 7.2.31
2020-10-22: añado más controles, para evitar riesgo con estos parámetros a pasarlos a la select,
dado lo crítico que es envíar un email no deseado. Además habrá otros controles adiccionales 
sobre estos parámetros. 
NOTA: Actualmente quito filtros de caracteres, y solo dejo que control de longitud mínima y máxima de caracteres. 	
--------------------------------------------------------------------------------------------------*/
function validarEmailAvisarDomiciliadosProximoCobro($arrCamposForm)
{
	//echo "<br><br>0-1 validarCamposTesorero:validarEmailAvisarDomiciliadosProximoCobro:arrCamposForm: "; print_r($arrCamposForm);
	
	$resulValidar['codError'] = '00000';
 $resulValidar['errorMensaje'] = '';

 //--- Inicio validar campos comunes para Emails Avisar sobre cuotas SI Domiciliados y NO Domiciliadas ---
	$resulValidar = validarEmailAvisarCuotaDomic_Y_SinDomic($arrCamposForm);	//en	validarCamposTesorero.php	

 //echo "<br><br>1 validarCamposTesorero:validarEmailAvisarDomiciliadosProximoCobro:resulValidar: ";print_r($resulValidar);
 //--- Fin validar campos comunes  -----------------------------------------------------------------------
	
	/*--- Inicio validar el texto que se inserta en el email, con la fecha prevista envíar orden de cobro ---			
	   Es la fecha límite de los socios para hacer correciones o anulaciones 
	*/
 //$resulValidar['textoFechaPrevistaCobro'] = validarCampoTexto($arrCamposForm['textoFechaPrevistaCobro'],10,255,"Debes introducir un texto con la fecha, entre 10 y 255 caracteres");																																														
 $resulValidar['textoFechaPrevistaCobro'] = validarTextoBodyEmailGes($arrCamposForm['textoFechaPrevistaCobro'],10,255,"Debes introducir un texto con la fecha, entre 10 y 255 caracteres");		
 //NOTA: Actualmente quito filtros de caracteres, y solo dejo que control de longitud mínima y máxima de caracteres. 	
 
	if ($resulValidar['textoFechaPrevistaCobro']['codError'] !== '00000')	
	{$resulValidar['codError'] = $resulValidar['textoFechaPrevistaCobro']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['textoFechaPrevistaCobro']['errorMensaje'];
	}
	//echo "<br><br>2 validarCamposTesorero:validarEmailAvisarDomiciliadosProximoCobro:resulValidar['textoFechaPrevistaCobro']: "; print_r($resulValidar['textoFechaPrevistaCobro']);	
	//--- Fin validar el texto que se inserta en el email, con la fecha prevista envíar orden de cobro ------			
	
 //echo "<br><br>3 validarCamposTesorero:validarEmailAvisarDomiciliadosProximoCobro:resulValidar: ";print_r($resulValidar); 

 return $resulValidar;
}
/*------------------------- Fin validarEmailAvisarDomiciliadosProximoCobro ----------------------*/

/*---------------- Inicio validarEmailAvisarCuotaNoCobradaSinCC -----------------------------------
DESCRIPCION: Valida los campos de formulario de "formEmailAvisarCuotaNoCobradaSinCC.php" 
Recibe en "$arrCamposForm" un Array con ['agrupaciones'],'paisCC'],
['URLgastosLaicismo'],[fechaAltaExentosPago][dia][mes][anio,], ...

LLAMADA: cTesorero.php:emailAvisarCuotaNoCobradaSinCC()
LLAMA:  modelos/libs/validarEmailAvisarCuotaDomic_Y_SinDomic() y funciones validarCampos.php 

OBSERVACIONES: probado PHP 7.2.31
2020-10-22: añado más controles, para evitar riesgo con estos parámetros a pasarlos a la select,
dado lo crítico que es envíar un email no deseado. Además habrá otros controles adiccionales 
sobre estos parámetros. 
--------------------------------------------------------------------------------------------------*/
function validarEmailAvisarCuotaNoCobradaSinCC($arrCamposForm)
{
	//echo "<br><br>0-1 validarCamposTesorero:validarEmailAvisarCuotaNoCobradaSinCC:arrCamposForm: "; print_r($arrCamposForm);
	
	$resulValidar['codError'] = '00000';
 $resulValidar['errorMensaje'] = '';	
	
 //--- Inicio validar campos comunes para Emails Avisar sobre cuotas SI Domiciliados y NO Domiciliadas ---
	$resulValidar = validarEmailAvisarCuotaDomic_Y_SinDomic($arrCamposForm);	//en	validarCamposTesorero.php	

 //echo "<br><br>1 validarCamposTesorero:validarEmailAvisarCuotaNoCobradaSinCC:resulValidar: ";print_r($resulValidar);
 //--- Fin validar campos comunes  -----------------------------------------------------------------------

	//-- Inicio validar que hay datos de los bancos de Europa Laica en formulario para body y enviar a socios --- 
	
	if (!isset($arrCamposForm['bancosAgrup']) || empty( array_filter($arrCamposForm['bancosAgrup']) ) )//mejor opción controla además si todos los elementos del array son NULL o " "
	{$resulValidar['bancosAgrup']['codError'] = '80201';	
	 $resulValidar['bancosAgrup']['errorMensaje'] = 'Error: No hay datos de los bancos de EL para el contenido del cuerpo del email';		
  $resulValidar['codError'] = $resulValidar['bancosAgrup']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['bancosAgrup']['errorMensaje'];
	}
	else
	{	$resulValidar['bancosAgrup'] = $arrCamposForm['bancosAgrup'];
 }	
	//echo "<br><br>2 validarCamposTesorero:validarEmailAvisarCuotaNoCobradaSinCC:resulValidar['bancosAgrup']: "; print_r($resulValidar['bancosAgrup']);

 //-- Fin validar que hay datos de los bancos de Europa Laica en formulario para body y enviar a socios ------ 
	
	//echo "<br><br>3 validarCamposTesorero:validarEmailAvisarCuotaNoCobradaSinCC:resulValidar: ";print_r($resulValidar); 
	
 return $resulValidar;
}
/*------------------------- Fin validarEmailAvisarCuotaNoCobradaSinCC ---------------------------*/

/*---------------- Inicio exportarEmailAvisarCuotas -----------------------------------------------
DESCRIPCION:Valida los campos de formulario de formExportarEmailDomiciliadoPendSinCC.php
y formExportarEmailDomiciliadoPend.php 
Recibe en "$arrCamposForm" un Array con ['agrupaciones'],'paisCC'],
[fechaAltaExentosPago][dia][mes][anio,]

Llamado desde: cTesorero.php:exportarEmailDomiciliadosPendSinCC(), exportarEmailDomiciliadosPend()
Llama funciones:  modelos/libs/validarCampos.php 
--------------------------------------------------------------------------------------------------*/
function exportarEmailAvisarCuotas($arrCamposForm)
{//echo "<br><br>1 validarCamposTesorero:exportarEmailAvisarCuotas:arrCamposForm:"; print_r($arrCamposForm);
	
	$resulValidar['codError'] = '00000';//ponemos error form a 00000 
 $resulValidar['errorMensaje'] = '';
	
 //--- Inicio validar en agrupaciones que se haya elegido al menos una agrupación	
	if (!isset($arrCamposForm['agrupaciones']) /*|| count($arrCamposForm['agrupaciones'] ==0)*/)
	{$resulValidar['agrupaciones']['codError'] = '80201';	
	 $resulValidar['agrupaciones']['errorMensaje'] = 'No has elegido ninguna agrupación para enviar email a socios/as';
		
  $resulValidar['codError'] = $resulValidar['agrupaciones']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['agrupaciones']['errorMensaje'];
	}	
	else
	{$resulValidar['agrupaciones']['valorCampo']= $arrCamposForm['agrupaciones'];
  $resulValidar['agrupaciones']['codError']='00000';	
	 $resulValidar['agrupaciones']['errorMensaje'] = '';
	}
 //echo "<br><br>2 validarCamposTesorero:exportarEmailAvisarCuotas:resulValidar:"; print_r($resulValidar);
	
	//--- Inicio validar tipo cuenta bancaria, ES, otros paises de europa SEPA, no Puede haber error: es un radio button
 $resulValidar['paisCC']['valorCampo']= $arrCamposForm['paisCC'];
 $resulValidar['paisCC']['codError']='00000';	
	$resulValidar['paisCC']['errorMensaje'] = '';	
	
	
	//--- Inicio validar fechaAltaExentosPago (fecha de alta del socio por encima de la cual no se les cobra) --
	// en segundas, terceras órdenes de cobro "de repesca" el tesorero elige un fecha para excluir pago cuotas a altas
	// con fechas posteriores al valor $fechaAltaExentosPago que serán fechas de alta cerca de final año 
	
	$fechInf = date('Y') .'-01-01'; //limite inicio de año actual	
	//echo "<br><br>3 validarCamposTesorero:exportarEmailAvisarCuotas:fechInf:";print_r($fechInf); 

	$fechSup = date('Y-m-d');	//no puede ser superior que el día actual				
	//echo "<br><br>4 validarCamposTesorero:exportarEmailAvisarCuotas:fechSup:";print_r($fechSup); 
	$permitirVacio = false;	
	
	$resulValidar['fechaAltaExentosPago'] = validarFechaLimites($arrCamposForm['fechaAltaExentosPago'],$fechInf,$fechSup,$permitirVacio);
 //$resulValidar = validarFechaLimites($arrFechaDDMMYYYY,$fechInf,$fechSup,$permitirVacio);
	
	if ($resulValidar['fechaAltaExentosPago']['codError'] !== '00000')	
	//if ($resulValidar['codError'] !== '00000')	
	{$resulValidar['codError'] .= $resulValidar['fechaAltaExentosPago']['codError'];
  $resulValidar['errorMensaje'] .= $resulValidar['fechaAltaExentosPago']['errorMensaje'];
	} 									
 //echo "<br><br>5 validarCamposTesorero:exportarEmailAvisarCuotas:resulValidar:";print_r($resulValidar); 
	
 return $resulValidar;
}
//------------------------- Fin exportarEmailAvisarCuotas -----------------------------------------

/*---------------- Inicio validarCamposFormActualizarOrdenCobroRemesa -----------------------------
DESCRIPCION:Valida los campos de formulario de vistas/tesorero/vActualizarCuotasCobradasEnRemesaTesInc.php 
Recibe un array['datosFormOrdenCobroRemesa'][NOMARCHIVOSEPAXML],[IMPORTEREMESA],[IMPORTEGASTOSREMESA][FECHAORDENCOBRO][FECHAORDENPAGO],...[FECHAPAGO],[OBSERVACIONES]
solo valida ['IMPORTEGASTOSREMESA'] entre el valor mínimo 0 y el máximo 100000.00 y el campo 
[FECHAPAGO] ..... mayor que [FECHAORDENCOBRO]  
Llamado desde: cTesorero.php:actualizarCuotasCobradasEnRemesaTes()
Llama funciones:  modelos/libs/validarCampos.php (varias funciones)

--------------------------------------------------------------------------------------------------*/
function validarCamposFormActualizarOrdenCobroRemesa($arrCamposForm)
{//echo "<br><br>1 validarCamposTesorero:validarCamposFormActualizarOrdenCobroRemesa:arrCamposForm:"; print_r($arrCamposForm);
	foreach($arrCamposForm['datosFormOrdenCobroRemesa'] as $item=>$valItem)
	{	
	 $resulValidar['datosFormOrdenCobroRemesa'][$item]['codError']='00000';
	 $resulValidar['datosFormOrdenCobroRemesa'][$item]['errorMensaje']='';
	 $resulValidar['datosFormOrdenCobroRemesa'][$item]['valorCampo']=$valItem;
	}
	
	$resulValidar['codError'] ='00000';
	$resulValidar['errorMensaje'] ='';
	
	//echo "<br><br>2 validarCamposTesorero:validarCamposFormActualizarOrdenCobroRemesa:resulValidar: "; print_r($resulValidar);echo "<br><br>";		
	
	//------------------- Inicio validar ['FECHAPAGO'] (fecha de cargo del cobro por el banco) --------
	//$fechInf = date('Y-m-d');	//no puede ser menor que el día actual			
 $fechInf = $arrCamposForm['datosFormOrdenCobroRemesa']['FECHAORDENCOBRO'];//no puede ser menor FECHAORDENCOBRO			
	//$fechSup = date('Y')+1 .'-12-31'; //no puede ser superior al final del siguiente año	
	$fechSup = date('Y-m-d'); //no podría ser superior al día actual	
	$permitirVacio = false;		
																																																																				
 $resulValidar['datosFormOrdenCobroRemesa']['FECHAPAGO']=	validarFechaLimites($arrCamposForm['datosFormOrdenCobroRemesa']['FECHAPAGO'],
	                                                                                         $fechInf,$fechSup,$permitirVacio);	
	
	if ($resulValidar['datosFormOrdenCobroRemesa']['FECHAPAGO']['codError'] !== '00000')	
	{$resulValidar['codError'] = $resulValidar['datosFormOrdenCobroRemesa']['FECHAPAGO']['codError'];
  $resulValidar['errorMensaje'] = $resulValidar['datosFormOrdenCobroRemesa']['FECHAPAGO']['errorMensaje'];
	} 	
	//echo "<br><br>3 validarCamposTesorero:validarCamposFormActualizarOrdenCobroRemesa:resulValidar:";print_r($resulValidar); echo "<br><br>";		
	
	if (isset($arrCamposForm['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']))
 { $resulValidar['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']= validarCantidadDecimal($arrCamposForm['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA'],0.00,1000000.00,"");	
	}
	
	if ( $resulValidar['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['codError'] !== '00000')	
	{$resulValidar['codError'] =  $resulValidar['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['codError'];
  $resulValidar['errorMensaje'] =  $resulValidar['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['errorMensaje'];
	}
	
	//echo "<br><br>4 validarCamposTesorero:validarCamposFormActualizarOrdenCobroRemesa:resulValidar['camposFormActualizarOrdenCobroRemesa']:"; print_r($resulValidar); echo "<br><br>";			
	
 return $resulValidar;
}
//------------------------- Fin validarCamposFormActualizarOrdenCobroRemesa -----------------------

/*============================ FIN VALIDAR ORDENES COBROS Y RELACIONADO ==========================*/
