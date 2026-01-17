<?php
/*------------------------------------------------------------------------------
FICHERO: inicializaCamposActualizarSocio.php
en controladores/libs/inicializaCamposActualizarSocio.php
PROYECTO: Europa Laica

VERSION: PHP 7.3.21
DESCRIPCION: Preparar campos de cuota del socio, para mostrar y actualizar la
             cuota en formularios de actualización socioSocios, pero hay que guardar 
													los valores anteriores porque algunos no los modifique (es común) o 
													hay error al introducir los nuevos datos
													
RECIBE: $datosSocioFormActualizar
DEVUELVE: un array con todos los datos del socio para mostralos antes de actualizarlos	
							
LLMADA: desde controladorSocios.php:actualizarSocio(), cPresidente.php:actualizarSocioPres()
         cCoordinador.php:actualizarSocioCoord, cTesorero.php:actualizarDatosCuotaSocioTes
LLAMA: ./modelos/modeloSocios.php:buscarCuotasAnioEL(),insertarError()
controladores/libs/arrayEnviaRecibeUrl.php:arrayEnviaUrl()

OBSERVACIONES: NO ME GUSTA ESTO AQUI
2020-04-15: Añado insertar errores
Aquí no necesita cambios para PDO, lo incluyen internamente las funciones que utiliza 


------------------------------------------------------------------------------*/	
//antes function prepMostrarActualizarCuotaSocio($datosSocioFormActualizar) en modelos/libs/prepMostrarActualizarCuotaSocio.php
function inicializaCamposActualizarSocio($datosSocioFormActualizar) //en controladores/libs/
{
	//echo "<br><br>0-1 controladores/libs/inicializaCamposActualizarSocio:datosSocioFormActualizar: ";	print_r($datosSocioFormActualizar);
	
	$datosSocioActualizar['nomScript'] = 'prepMostrarActualizarCuotaSocio,php';
	$datosSocioActualizar['nomFuncion'] = 'prepMostrarActualizarCuotaSocio()';
	
	if (!isset($datosSocioFormActualizar) || empty ($datosSocioFormActualizar))
	{ $datosSocioActualizar['codError'] = '80201';
   $datosSocioActualizar['errorMensaje'] = 'inicializaCamposActualizarSocio(): Variable \$datosSocioFormActualizar esta vacía';
			//echo "<br><br>0-2 controladores/libs/inicializaCamposActualizarSocio:datosSocioFormActualizar: ";	print_r($datosSocioFormActualizar);
	}
 else //!(!isset($datosSocioFormActualizar) || !empty ($datosSocioFormActualizar))
	{ 
  $datosSocioActualizar['codError'] = '00000';
  $datosSocioActualizar['errorMensaje'] = '';

		$datosSocioActualizar['campoActualizar'] = $datosSocioFormActualizar['valoresCampos'];
		//echo "<br><br>1 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);
		
		//------ Inicio preparar campos de datos año actual -------------------------------------------------
		foreach ($datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')] as $campo =>$valCampo)
		{	$datosSocioActualizar['cuotaSocioAnioActual'][$campo] = $valCampo['valorCampo'];
				//interesa CODCUOTA,IMPORTECUOTAANIOSOCIO,IMPORTECUOTAANIOPAGADA,ESTADO
		}	
		//echo "<br><br>2 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);					
		// si en año actual Y aún no está abonada la cuota, se actualiza la cuota del año actual Y
		if ($datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo'] !=='ABONADA')
		{
			$datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'] = $datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')];//para actualizar input				
			//echo "<br><br>3 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);
			
			$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),"%");//busca cuotasEL para Y; en modeloSocios.php
			//echo "<br><br>4 controladores/libs/inicializaCamposActualizarSocio:resCuotasAniosEL: ";	print_r($resCuotasAniosEL);
				
			if ($resCuotasAniosEL['codError']!=='00000')
			{$datosSocioActualizar['codError'] = $resCuotasAniosEL['codError'];
				$datosSocioActualizar['errorMensaje'] = ':prepMostrarActualizarCuotaSocio(): '.$resCuotasAniosEL['errorMensaje'];
				//echo "<br><br>5 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
			}
			else
			{$cuotaAnioActual = $resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];

				$datosCuotasEL['ANIOCUOTA'] = $cuotaAnioActual['General']['ANIOCUOTA']['valorCampo'];												
				$datosCuotasEL['CODCUOTAGeneral'] = $cuotaAnioActual['General']['CODCUOTA']['valorCampo'];	
				$datosCuotasEL['IMPORTECUOTAANIOELGeneral'] = $cuotaAnioActual['General']['IMPORTECUOTAANIOEL']['valorCampo'];
				$datosCuotasEL['IMPORTECUOTAANIOELJoven'] = $cuotaAnioActual['Joven']['IMPORTECUOTAANIOEL']['valorCampo'];
				$datosCuotasEL['IMPORTECUOTAANIOELParado'] = $cuotaAnioActual['Parado']['IMPORTECUOTAANIOEL']['valorCampo'];	
				$datosCuotasEL['IMPORTECUOTAANIOELHonorario'] = $cuotaAnioActual['Honorario']['IMPORTECUOTAANIOEL']['valorCampo'];	
				
				//echo "<br><br>6 modelos/libs:prepMostrarActualizarCuota:datosCuotasEL: ";	print_r($datosCuotasEL);	
			}
		}//if ($datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo'] !=='ABONADA')
			
		else//SI $datosSocioActualizar['camposActualizarCuota']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']=='ABONADA'
		{
			// si en año actual Y ya está abonada la cuota, se actualiza la cuota del año siguiente Y+1
				
			$resCuotasAniosEL = buscarCuotasAnioEL(date('Y')+1,"%");//busca cuotasEL para Y+1	
			//echo "<br><br>7 controladores/libs/inicializaCamposActualizarSocio:resCuotasAniosEL: ";	print_r($resCuotasAniosEL);
				
			if ($resCuotasAniosEL['codError'] !=='00000')
			{$datosSocioActualizar['codError'] = $resCuotasAniosEL['codError'];
				$datosSocioActualizar['errorMensaje'] = ':prepMostrarActualizarCuotaSocio(): '.$resCuotasAniosEL['errorMensaje'];
				//echo "<br><br>8 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
			}
			else//1 $resCuotasAniosEL['codError'] !=='00000')
			{$cuotaAnioSig = $resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')+1];

				$datosCuotasEL['ANIOCUOTA'] = $cuotaAnioSig['General']['ANIOCUOTA']['valorCampo'];												
				$datosCuotasEL['CODCUOTAGeneral'] = $cuotaAnioSig['General']['CODCUOTA']['valorCampo'];	
				$datosCuotasEL['IMPORTECUOTAANIOELGeneral'] = $cuotaAnioSig['General']['IMPORTECUOTAANIOEL']['valorCampo'];
				$datosCuotasEL['IMPORTECUOTAANIOELJoven'] = $cuotaAnioSig['Joven']['IMPORTECUOTAANIOEL']['valorCampo'];
				$datosCuotasEL['IMPORTECUOTAANIOELParado'] = $cuotaAnioSig['Parado']['IMPORTECUOTAANIOEL']['valorCampo'];	
				$datosCuotasEL['IMPORTECUOTAANIOELHonorario'] = $cuotaAnioSig['Honorario']['IMPORTECUOTAANIOEL']['valorCampo'];	
				
				//echo "<br><br>9 controladores/libs/inicializaCamposActualizarSocio:datosCuotasEL: ";	print_r($datosCuotasEL);	
				//---------------- 
				if (isset ($datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')+1]))// si ya tiene anotación Y+1							
				{$datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'] = $datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')+1];//para actualizar input Y+1
					//echo "<br><br>10 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
				}	
				else //si aún no tiene anotación Y+1: se pone la del año actual Y, siempre que no sea inferior a nuevo año				
				{$cuotaSocioAnioActual = $datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'][date('Y')];
					
					//echo "<br><br>11 controladores/libs/inicializaCamposActualizarSocio:cuotaSocioAnioActual: ";	print_r($cuotaSocioAnioActual);

					if($cuotaSocioAnioActual['CODCUOTA']['valorCampo'] == 'General')	
					{if($datosCuotasEL['IMPORTECUOTAANIOELGeneral'] <= $cuotaSocioAnioActual['IMPORTECUOTAANIOEL']['valorCampo'])	
						{//echo "<br><br>11-1 controladores/libs/inicializaCamposActualizarSocio:";
							$cuotaSocioAnioSig['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $cuotaSocioAnioActual['IMPORTECUOTAANIOSOCIO']['valorCampo'];
						}
					}	
					elseif($cuotaSocioAnioActual['CODCUOTA']['valorCampo'] == 'Joven')	
					{if($datosCuotasEL['IMPORTECUOTAANIOELJoven'] <= $cuotaSocioAnioActual['IMPORTECUOTAANIOEL']['valorCampo'])
						{//echo "<br><br>11-2 controladores/libs/inicializaCamposActualizarSocio:";
							$cuotaSocioAnioSig['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $cuotaSocioAnioActual['IMPORTECUOTAANIOSOCIO']['valorCampo'];
						}
					}
					elseif($cuotaSocioAnioActual['CODCUOTA']['valorCampo'] == 'Parado')	
					{if($datosCuotasEL['IMPORTECUOTAANIOELParado'] <= $cuotaSocioAnioActual['IMPORTECUOTAANIOEL']['valorCampo'])
						{//echo "<br><br>11-3 controladores/libs/inicializaCamposActualizarSocio:";
							$cuotaSocioAnioSig['IMPORTECUOTAANIOSOCIO']['valorCampo']=$cuotaSocioAnioActual['IMPORTECUOTAANIOSOCIO']['valorCampo'];
						}
					}
					elseif($cuotaSocioAnioActual['CODCUOTA']['valorCampo'] == 'Honorario')	//en el caso de Honorario la cuota será 0
					{if($datosCuotasEL['IMPORTECUOTAANIOELHonorario'] <= $cuotaSocioAnioActual['IMPORTECUOTAANIOEL']['valorCampo'])
						{//echo "<br><br>11-4 controladores/libs/inicializaCamposActualizarSocio:";
							$cuotaSocioAnioSig['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $cuotaSocioAnioActual['IMPORTECUOTAANIOSOCIO']['valorCampo'];
						}
					}
					//echo "<br><br>11-5 controladores/libs/inicializaCamposActualizarSocio:";									
					$cuotaSocioAnioSig['ANIOCUOTA']['valorCampo'] = $cuotaAnioSig['General']['ANIOCUOTA']['valorCampo'];					
					$cuotaSocioAnioSig['CODCUOTA']['valorCampo'] = $cuotaSocioAnioActual['CODCUOTA']['valorCampo'];
					$cuotaSocioAnioSig['MODOINGRESO']['valorCampo'] = $cuotaSocioAnioActual['MODOINGRESO']['valorCampo'];
					//acaso mejor signa valor previo para ORDENARCOBROBANCO 
					if ($cuotaSocioAnioSig['MODOINGRESO']['valorCampo']=='DOMICILIADA' )
					{$cuotaSocioAnioSig['ORDENARCOBROBANCO']['valorCampo'] = 'SI';				
					}
					else
					{$cuotaSocioAnioSig['ORDENARCOBROBANCO']['valorCampo'] = 'NO';								
					}				
					$datosSocioActualizar['campoActualizar']['datosFormCuotaSocio'] = $cuotaSocioAnioSig;
		
					//echo "<br><br>12 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
				}//else si aún no tiene anotación Y+1: se pone la del año actual Y, siempre que no sea inferior a nuevo año				
			}//else 1 $resCuotasAniosEL['codError'] !=='00000')
		}//else SI $datosSocioActualizar['camposActualizarCuota']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']=='ABONADA'
		
		//echo "<br><br>13-1 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
		
		if ($datosSocioActualizar['codError'] === '00000')
		{	
		$datosSocioActualizar['campoActualizar']['datosCuotasEL'] = $datosCuotasEL;
		//echo "<br><br>13-2 controladores/libs/inicializaCamposActualizarSocio:datosCuotasEL: ";	print_r($datosCuotasEL);		
		//echo "<br><br>13-3 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
		
		//-------- Fin preparar campos --------------------------------------------------
		
		//-- Inicio PREPARAR CAMPOS HIDDEN para validar cambios en campos USUARIO,EMAIL,NUMDOCUMENTOMIEMBRO --------
		$datosSocioActualizar['campoHide']['anteriorUSUARIO'] = $datosSocioActualizar['campoActualizar']['datosFormUsuario']['USUARIO']['valorCampo'];
		$datosSocioActualizar['campoHide']['anteriorEMAIL'] = $datosSocioActualizar['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo'];					
		$datosSocioActualizar['campoHide']['anteriorCODPAISDOC'] = $datosSocioActualizar['campoActualizar']['datosFormMiembro']['CODPAISDOC']['valorCampo'];
		$datosSocioActualizar['campoHide']['anteriorTIPODOCUMENTOMIEMBRO'] = $datosSocioActualizar['campoActualizar']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];
		$datosSocioActualizar['campoHide']['anteriorNUMDOCUMENTOMIEMBRO'] = $datosSocioActualizar['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];	
	
  //Este $datosSocioActualizar['campoHide'] se utilizaron en el controlador correspondiente para enviar como arrayEnviaUrl()
	 //$datosSocioActualizar['campoHide'] = arrayEnviaUrl($datosSocioActualizar['campoHide']);//en controladores/libs/arrayEnviaRecibeUrl.php		
  		
		//-------- Fin preparar campos hidden ---------------------------------------------------------------------
		}
 }//else !(!isset($datosSocioFormActualizar) || !empty ($datosSocioFormActualizar))
	
	//echo "<br><br>14 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
	/*-------------------  insertar errores --------------------------*/

	if ($datosSocioActualizar['codError'] !== '00000')
	{ $datosError['codError'] = $datosSocioActualizar['codError'];	
			$datosError['errorMensaje']	= $datosSocioActualizar['nomScript'].":".$datosSocioActualizar['nomFuncion'].": ".$datosSocioActualizar['errorMensaje'];
			$datosError['textoComentarios'] = $datosSocioActualizar['nomScript'].":".$datosSocioActualizar['nomFuncion'].
																																										" Error del sistema en controladores/libs/inicializaCamposActualizarSocio() ";
			require_once './modelos/modeloErrores.php';																																									
			insertarError($datosError);		
	}
 /*-------------------------------------------------------------------*/	
	//echo "<br><br>15 controladores/libs/inicializaCamposActualizarSocio:datosSocioActualizar: ";	print_r($datosSocioActualizar);	
	
 return $datosSocioActualizar;
}
?>