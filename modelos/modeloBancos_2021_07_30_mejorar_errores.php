<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: modeloBancos.php
VERSION: PHP 7.3.21

DESCRIPCION: Este "modeloBancos" contiene funciones relacionadas con los datos bancarios 
             de las agrupaciones, cuentas IBAN de las agrupaciones de tabla 'AGRUPACIONTERRITORIAL', 
													preparar datos para cuentas IBAN agrupaciones y socios,
													para Paypal, ect.

LLAMADO: controladorSocios.php:altaSocio(),pagarCuotaSocio(),confirmarAltaSocio(),pagarCuotaSocioSinCC(),
         donarSocio()
         cTesorero:emailAvisarCuotaNoCobradaSinCC(),
LLAMA:	modeloSocios.php:buscarDatosAgrupacion()	y otros
 
OBSERVACIONES: 2020-05-10, añado mejoras en tratamiento de errores y compruebo 
que No necesita modificarse para incluir PHP: PDOStatement::bindParam, 
las funciones llamadas aquí lo tratan internamente

NOTA: A fecha 2021-08-01 las agrupaciones comparten la cuenta misma cuenta bancaria a efectos 
de automatización de las órdenes de cobro de la cuotas anuales de los socios que se realiza
desde esta aplicación.
Asturias Laica que tiene cuenta propia y realiza el cobro de modo independiente.

-Ya subido a "usuarios" (excepto mejora comentarios)
--------------------------------------------------------------------------------------------------*/

/*------------- Inicio agrupacionIBAN_papel -------------------------------------------------------
Esta función prepara los datos de una agrupación, buscando en la tabla 'AGRUPACIONTERRITORIAL' 
y trata los datos de las cuentas IBAN electrónicas de la agrupación correspondiente que se obtienen
de los campos CUENTAAGRUPIBAN1,CUENTAAGRUPIBAN2, para también obtener unas cuentas IBAN 
en "formato de papel" dígitos de 4 en 4 CUENTAAGRUPIBAN1_PAPEL, CUENTAAGRUPIBAN2_PAPEL
 
RECIBE: $codAgrupacion	
DEVUELVE: array $resDatosAgrupacion, incluidos errores 

LLAMADO: modeloBancos.php:prepararCadIBANAgrupMostrar(), 
         cTesorero:emailAvisarCuotaNoCobradaSinCC(), 
LLAMA:	modeloSocios.php:buscarDatosAgrupacion()	
--------------------------------------------------------------------------------------------------*/
function agrupacionIBAN_papel($codAgrupacion)
{
	//echo "<br><br>1 modeloBancos.php:agrupacionIBAN_papel:codAgrupacion: ";print_r($codAgrupacion);	

 $resDatosAgrupacion['nomScript'] = 'modeloBancos.php';
	$resDatosAgrupacion['nomFuncion'] = 'agrupacionIBAN_papel';
	
	if (!isset($codAgrupacion) || empty($codAgrupacion)) 
 {
		 $resDatosAgrupacion['codError'] = '70601';
	  $resDatosAgrupacion['errorMensaje'] = $resDatosAgrupacion['nomScript'].":".$resDatosAgrupacion['nomFuncion'].
			                                      ": Faltan variables-parámetros necesarios para SQL en modeloBancos.php:buscarDatosAgrupacion()";
 }
 else //if !!(!isset($codAgrupacion) || empty($codAgrupacion))
 {
			require_once './modelos/modeloSocios.php';	  
			$resDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);//$codAgrupacion admite %, contiene conexionDB()
			//estos datos también se obtienen /modelos/modeloSocios.php:buscarDatosSocio($usuarioBuscado,$anioCuota), 
			//en controladorSocios, por lo que algunos casos pudiera no ser necesaria.
																																						
			//echo "<br><br>2 modeloBancos.php:agrupacionIBAN_papel:resDatosAgrupacion: ";print_r($resDatosAgrupacion);	
																																													
			if ($resDatosAgrupacion['codError'] !== '00000')//INCLUYE ERROR para ['numFilas']==0)
			{ 
					$resDatosAgrupacion['errorMensaje'] = $resDatosAgrupacion['nomScript'].":".$resDatosAgrupacion['nomFuncion'].": ".$resDatosAgrupacion['errorMensaje'];
			}
			else //$resDatosAgrupacion['codError']=='00000'
			{//echo "<br><br>3 modeloBancos.php:agrupacionIBAN_papel:resDatosAgrupacion: ";print_r($resDatosAgrupacion);	
			
			// solo hay fila [0] por lo que el foreach se podría simplificar :
			// if ($resDatosAgrupacion['resultadoFilas'][0]['CUENTAAGRUPIBAN1'] ....)
			
				foreach ($resDatosAgrupacion['resultadoFilas'] as $fila => $valorFila)//solo hay fila [0] por lo que el foreach
				{//echo "<br><br>4 modeloBancos.php:agrupacionIBAN_papel:valorFila: ";print_r($valorFila);	

					$codAgrupacionAux = $valorFila['CODAGRUPACION'];
					
					foreach ($valorFila as $col => $valorCol)
					{
						if ($col !== 'CODAGRUPACION')//se podría dejar aunque para evitar redundancia no se incluye
						{ 
								$auxCol[$col] = $valorCol;

								if ($col == 'CUENTAAGRUPIBAN1')
								{ if (!empty($valorCol))
										{$cad1 = substr($valorCol,0,4);//cod IBAN 4
											$cad2 = substr($valorCol,4,4);//entidad 4
											$cad3 = substr($valorCol,8,4);//oficina 4
											$cad4 = substr($valorCol,12,4); 
											$cad5 = substr($valorCol,16,4);
											$cad6 = substr($valorCol,20,4);													
											$auxCol['CUENTAAGRUPIBAN1_PAPEL'] = $cad1." ".$cad2." ".$cad3." ".$cad4." ".$cad5." ".$cad6;
									
											$cad4 = substr($valorCol,12,2);//DC 2
											$cad5 = substr($valorCol,14,10);//numero 10										
											$auxCol['CUENTAAGRUPNOIBAN1_PAPEL'] = $cad2." ".$cad3." ".$cad4." ".$cad5;
										}
										else
										{ //$auxCol['CUENTAAGRUPIBAN1_PAPEL'] = NULL;
												$auxCol['CUENTAAGRUPIBAN1_PAPEL'] = NULL;
												$auxCol['CUENTAAGRUPNOIBAN1_PAPEL'] = NULL;
										}
								}
										
								if ($col == 'CUENTAAGRUPIBAN2')
								{ if (!empty($valorCol))
										{$cad1 = substr($valorCol,0,4);
											$cad2 = substr($valorCol,4,4);
											$cad3 = substr($valorCol,8,4);
											$cad4 = substr($valorCol,12,4);
											$cad5 = substr($valorCol,16,4);
											$cad6 = substr($valorCol,20,4);													
											$auxCol['CUENTAAGRUPIBAN2_PAPEL'] = $cad1." ".$cad2." ".$cad3." ".$cad4." ".$cad5." ".$cad6;
				
											$cad4 = substr($valorCol,12,2);//DC 2
											$cad5 = substr($valorCol,14,10);//numero 10										
											$auxCol['CUENTAAGRUPNOIBAN2_PAPEL'] = $cad2." ".$cad3." ".$cad4." ".$cad5;
											}
										else
										{$auxCol['CUENTAAGRUPIBAN2_PAPEL'] = NULL;
											$auxCol['CUENTAAGRUPNOIBAN2_PAPEL'] = NULL;
										}
								}											
						}//if ($col !== 'CODAGRUPACION')
					}//foreach ($valorFila as $col => $valorCol)

					$resDatosAgrupacion['datosAgrupaciones'][$codAgrupacionAux] = $auxCol;	
						//$resDatosAgrupacion['datosAgrupaciones'][$codAgrupacionAux]['COBROCUOTA'] =  $resDatosAgrupacion['resultadoFilas'][0]['COBROCUOTA'];	
					unset ($resDatosAgrupacion['resultadoFilas']);						
							
				}//foreach ($resDatosAgrupacion['resultadoFilas'] as $fila => $valorFila)				
				//echo "<br><br>5-1 modeloBancos.php:agrupacionIBAN_papel:resDatosAgrupacion: ";print_r($resDatosAgrupacion);	
			
			}//else $resDatosAgrupacion['codError']=='00000'
	}//else if !!(!isset($codAgrupacion) || empty($codAgrupacion))
 
	//echo "<br><br>5-2 modeloBancos.php:agrupacionIBAN_papel:resDatosAgrupacion: ";print_r($resDatosAgrupacion);	
	
	return $resDatosAgrupacion;
}	
/*------------- Fin agrupacionIBAN_papel ---------------------------------------------------------*/

/*----------------- Inicio prepararCadIBANAgrupMostrar --------------------------------------------
Esta función prepara los datos CUENTAAGRUPIBAN1,CUENTAAGRUPIBAN1_PAPEL,NOMBREIBAN1,
de la tabla 'AGRUPACIONTERRITORIAL' por la función agrupacionIBAN_papel():
CUENTAAGRUPIBAN2,CUENTAAGRUPIBAN2_PAPEL,NOMBREIBAN2, etc de una agrupación,
para ponerlos en un cadena adecuada para mostrar en vistas, de una forma más 
final y fácil. Si no tiene IBAN muestra un mensaje

RECIBE: $codAgrupacion	
DEVUELVE: string $cadBancos, con los datos IBAN

LLAMADO: controladorSocios.php:pagarCuotaSocio(),donarSocio() .....	
         cTesorero.php:emailAvisarCuotaNoCobradaSinCC()
									modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm(),mPrepararDatosSocioPagoCuotaBancosPayForm()
LLAMA:	modeloBancos.php:agrupacionIBAN_papel()	
--------------------------------------------------------------------------------------------------*/
function prepararCadIBANAgrupMostrar($codAgrupacion)
{ 
 //echo "<br><br>1 modeloBancos.php:prepararCadIBANAgrupMostrar:codAgrupacion: ";print_r($codAgrupacion);	
	
 $cadBancos['nomScript'] = 'modeloBancos.php';
	$cadBancos['nomFuncion'] = 'prepararCadIBANAgrupMostrar';
		
	if (!isset($codAgrupacion) || empty($codAgrupacion)) 
 {
		 $cadBancos['codError'] = '70601';
	  $cadBancos['errorMensaje'] = $cadBancos['nomScript'].":".$cadBancos['nomFuncion'].": Faltan variables-parámetros necesarios para SQL en modeloBancos.php:agrupacionIBAN_papel()";
 }
 else //if !!(!isset($codAgrupacion) || empty($codAgrupacion))
 {		
			$resDatosAgrupacion = agrupacionIBAN_papel($codAgrupacion);//en modeloBancos.php
			
			//echo "<br><br>2 modeloBancos.php:prepararCadIBANAgrupMostrar:resDatosAgrupacion: "; print_r($resDatosAgrupacion);
				
			if ($resDatosAgrupacion['codError'] !=='00000')//INCLUYE ERROR para ['numFilas']==0)
			{ $cadBancos['codError'] = $resDatosAgrupacion['codError'];
					$cadBancos['errorMensaje'] = $cadBancos['nomScript'].":".$cadBancos['nomFuncion'].": ".$resDatosAgrupacion['errorMensaje'];
					$cadBancos['nomScript'] = $resDatosAgrupacion['nomScript'];//se van a repetir
					$cadBancos['nomFuncion'] = $resDatosAgrupacion['nomFuncion'];//se van a repetir
			}
			else //$resDatosAgrupacion['codError']=='00000'
			{$cadBancos['codError'] = '00000';
				$cadBancos['errorMensaje'] = '';
				
				$cadenaBancos ='';
				$titularCuentasBancos = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['TITULARCUENTASBANCOS'];
			
				if (isset($resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN1']) &&
								!empty($resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN1'])	)
				{				 
					$bancoElectronica1 = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN1'];					
					$bancoPapelIBAN1  = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN1_PAPEL'];
					$bancoPapelNOIBAN1  = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPNOIBAN1_PAPEL'];			
					$bancoNombre1 = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['NOMBREIBAN1'];
					
					$cadBanco1 = strtoupper($bancoNombre1).". Titular: ".$titularCuentasBancos.". Cuenta IBAN: ".$bancoPapelIBAN1;	
				}			
				else
				{$cadBanco1 = NULL;						 
				}
				
				if (isset($resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN2']) &&
								!empty($resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN2'])	)
				{				 
					$bancoElectronica2 = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN2'];					
					$bancoPapelIBAN2  = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPIBAN2_PAPEL'];
					$bancoPapelNOIBAN2  = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['CUENTAAGRUPNOIBAN2_PAPEL'];			
					$bancoNombre2 = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['NOMBREIBAN2'];					
					
					$cadBanco2 = "<br />".strtoupper($bancoNombre2).". Titular: ".$titularCuentasBancos.". Cuenta IBAN: ".$bancoPapelIBAN2;
				}
				else
				{$cadBanco2 = NULL;						
				}		
				
				$cadenaBancos = NULL;
							
				if ($cadBanco1 == NULL && $cadBanco2 == NULL)
				{
						$cadenaBancos = "<br /><br />No se han encontrado cuentas bancarias para esta agrupación de Europa Laica, puedes pagar la cuota anual mediante PayPal, 
																								o consultar otras opciones de pago a Tesorería: -tesoreria@europalaica.org- ";  				
				}
				
				if ($cadBanco1 !== NULL)
				{
					$cadenaBancos = "<br /><br />".$cadBanco1;
				}
				
				if ($cadBanco2 !== NULL)
				{
					$cadenaBancos = $cadenaBancos."<br /><br />".$cadBanco2;
				}
				//echo "<br><br>3 modeloBancos.php:prepararCadIBANAgrupMostrar: cadenaBancos:".$cadenaBancos;
				
				$cadBancos['cadenaBancos'] = $cadenaBancos;	
				
				$cadBancos['GESTIONCUOTAS']   =  $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['GESTIONCUOTAS']; 
				
				$cadBancos['concepto'] =	"Señala como concepto: Pago cuota a Europa Laica, nombre y apellidos y NIF, NIE, o pasaporte.";	
				
				$cadBancos['emailTesoreroAgrupacion'] = $resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['EMAILTESORERO'];
				
				$cadBancos['cadEmailTesoreroAgrupacion'] = 
							"Por si hubiese algún problema, nos puedes confirmar tu pago enviando un correo electrónico a <strong>".
							$resDatosAgrupacion['datosAgrupaciones'][$codAgrupacion]['EMAILTESORERO'].
							"</strong> con asunto -Cuota-, y dentro del mensaje los datos: nombre y apellidos, cantidad, fecha pago, entidad dónde has pagado y NIF, NIE, o pasaporte.";
					
				//echo "<br><br>4 modeloBancos.php:prepararCadIBANAgrupMostrar:cadBancos: ";print_r($cadBancos);
				
			}//else $resDatosAgrupacion['codError']=='00000'
	}//else if !!(!isset($codAgrupacion) || empty($codAgrupacion))
	
	//echo "<br><br>5 modeloBancos.php:prepararCadIBANAgrupMostrar:cadBancos: ";print_r($cadBancos);
	
 return $cadBancos;
}	
/*----------------- Fin prepararCadIBANAgrupMostrar ----------------------------------------------*/


/*------------Inicio mPrepararDatosRegSocioPagoCuotaBancosPayForm()---------------------------------   
Después de registrase un socio (aún pendiente de confirmación), se muestra un 
formulario-mensaje:'./vistas/socios/vPagarCuotaSocioInc.php' con los datos de
cuota a pagar, IBAN bancos de la AGRUPACION, y botón con enlace a script de 
PayPal personalizado para ese socio, si esa agrupación permite el cobro centalizado (no en Asturias).

Prepara, a partir de "$datosSocio" que recibe del array "$resValidarCamposForm" 
desde controladorSocios.php:altsSocio(), los datos bancarios necesarios 
para el formulario "vPagarCuotaSocioInc"							

RECIBE: $datosSocio, array con datos de  "$resValidarCamposForm" del registro de
alta de un socio, pendiente de confirmar

LLAMADO: desde contoladorSocio.php:altaSocio()
LLAMA: modelos/modeloUsuarios.php:provinciaDatos()
       modelos/modeloBancos.php:prepararCadIBANAgrupMostrar()	
		 
OBSERVACIONES: OJO LA PARTE DE PAYPAL SE CAMBIA PARA PRUEBAS: 
payPalScript: PAYPAL SANBOX, y business ='prueba1@europalaica.com'

NOTA: 
Para preparar los datos personales del socio, aquí los datos proceden solo de la 
tabla "SOCIOSCONFIRMAR", ya que aún no es un alta confirmada		mientras que en 
"mPrepararDatosSocioPagoCuotaBancosPayForm()" proceden de varias tablas ya que
es un socio con alta ya confirmada (En lo damás son idénticas)	
Es casi igual a mPrepararDatosSocioPagoCuotaBancosPayForm() con la única diferencia 
de que en "mPrepararDatosSocioPagoCuotaBancosPayForm()", se calcula y utiliza 
"$datosSocioPagoCuota['faltaPagar']" puesto en ella podría haber pagado parte 
de la cuota mientras que al darse de alta, eso no sucederá.
No las unifico, por si más adelante se fuese necesario un tratamiento más independiente
--------------------------------------------------------------------------------------------------*/
function mPrepararDatosRegSocioPagoCuotaBancosPayForm($datosSocio) 
{
 //echo "<br><br>1 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosSocio: "; print_r($datosSocio);
	
 $datosAltaSocioPagoCuota['nomScript'] = 'modeloBancos.php';
	$datosAltaSocioPagoCuota['nomFuncion'] = 'mPrepararDatosRegSocioPagoCuotaBancosPayForm';	
	$datosAltaSocioPagoCuota['codError'] = '00000';
 $datosAltaSocioPagoCuota['errorMensaje'] = '';
	
	/*--- Inicio, preparar datos personales del socio ------------------------------------------------*/
	//--- ES UNA ALTERNATIVA en lugar de ESCRIBIR CADA CAMPO, PERO CREO QUE ES MENOS CLARA DE ENTENDER
	/*$validarCamposForm['SOCIOSCONFIRMAR'] = $datosSocio['SOCIOSCONFIRMAR'];//para no utilizar los campos de errores	
	 echo "<br><br>";
	foreach($validarCamposForm as $fila1 =>$valorFila1)
	{echo "<br>fila1:".$fila1." valorFila1: ";print_r($valorFila1);	  echo "<br>";		 
			foreach($valorFila1 as $fila2 =>$valorFila2)
			{echo "<br>fila2:".$fila2." valorFila2: ";print_r($valorFila2);
				if (isset($valorFila2['valorCampo']))
				{				$datosAltaSocioPagoCuota[$fila2]=$valorFila2['valorCampo'];//bien					
				}
			}
	}*/	
	
 //------ las tres siguientes asignaciones solo específicos de altaSocio (por socio)----------------	
	$datosAltaSocioPagoCuota['ESTADOCUOTA'] = 'PENDIENTE-COBRO';
 $datosAltaSocioPagoCuota['ORDENARCOBROBANCO'] = 'SI';	
	$datosAltaSocioPagoCuota['faltaPagar'] = $datosSocio['SOCIOSCONFIRMAR']['IMPORTECUOTAANIOSOCIO']['valorCampo'];
 
	$datosAltaSocioPagoCuota['IMPORTECUOTAANIOPAGADA'] = 0;	
	//------ lo anterior solo es específico de altaScio (por socio)------------------------------------		
	
 $datosAltaSocioPagoCuota['CODUSER'] = $datosSocio['SOCIOSCONFIRMAR']['CODUSER']['valorCampo'];//se usa también se envía a PayPal	
		
	$datosAltaSocioPagoCuota['CODCUOTA'] = $datosSocio['SOCIOSCONFIRMAR']['CODCUOTA']['valorCampo'];
	$datosAltaSocioPagoCuota['IMPORTECUOTAANIOSOCIO'] = $datosSocio['SOCIOSCONFIRMAR']['IMPORTECUOTAANIOSOCIO']['valorCampo'];
	$datosAltaSocioPagoCuota['ANIOCUOTA'] = $datosSocio['SOCIOSCONFIRMAR']['ANIOCUOTA']['valorCampo'];
	
	$datosAltaSocioPagoCuota['CUENTAIBAN'] = $datosSocio['SOCIOSCONFIRMAR']['CUENTAIBAN']['valorCampo'];	
	$datosAltaSocioPagoCuota['CODAGRUPACION'] = $datosSocio['SOCIOSCONFIRMAR']['CODAGRUPACION']['valorCampo'];
			
	$datosAltaSocioPagoCuota['NOM'] = $datosSocio['SOCIOSCONFIRMAR']['NOM']['valorCampo'];
	$datosAltaSocioPagoCuota['APE1'] = $datosSocio['SOCIOSCONFIRMAR']['APE1']['valorCampo'];			
	$datosAltaSocioPagoCuota['APE2'] = $datosSocio['SOCIOSCONFIRMAR']['APE2']['valorCampo'];
	
	$datosAltaSocioPagoCuota['nombrePaisDoc'] = $datosSocio['SOCIOSCONFIRMAR']['CODPAISDOC']['valorCampo'];
	$datosAltaSocioPagoCuota['CODPAISDOC'] = $datosSocio['SOCIOSCONFIRMAR']['CODPAISDOC']['valorCampo'];
	$datosAltaSocioPagoCuota['TIPODOCUMENTOMIEMBRO'] = $datosSocio['SOCIOSCONFIRMAR']['TIPODOCUMENTOMIEMBRO']['valorCampo'];
	$datosAltaSocioPagoCuota['NUMDOCUMENTOMIEMBRO'] = $datosSocio['SOCIOSCONFIRMAR']['NUMDOCUMENTOMIEMBRO']['valorCampo'];
	
	$datosAltaSocioPagoCuota['DIRECCION'] = $datosSocio['SOCIOSCONFIRMAR']['DIRECCION']['valorCampo'];
	$datosAltaSocioPagoCuota['LOCALIDAD'] = $datosSocio['SOCIOSCONFIRMAR']['LOCALIDAD']['valorCampo'];
	$datosAltaSocioPagoCuota['CP'] = $datosSocio['SOCIOSCONFIRMAR']['CP']['valorCampo'];
	$datosAltaSocioPagoCuota['CODPAISDOM'] = $datosSocio['SOCIOSCONFIRMAR']['CODPAISDOM']['valorCampo'];
			
	$datosAltaSocioPagoCuota['TELMOVIL'] = $datosSocio['SOCIOSCONFIRMAR']['TELMOVIL']['valorCampo'];
	$datosAltaSocioPagoCuota['TELFIJOCASA'] = $datosSocio['SOCIOSCONFIRMAR']['TELFIJOCASA']['valorCampo'];
	$datosAltaSocioPagoCuota['EMAIL'] = $datosSocio['SOCIOSCONFIRMAR']['EMAIL']['valorCampo'];	
			
	//echo "<br><br>2 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosAltaSocioPagoCuota: ";print_r($datosAltaSocioPagoCuota);	
		
	/*--- Fin, preparar datos personales del socio ------------------------------------------------------*/
	

	/*-- Inicio Buscar en BBDD Nombre provincia domicilio socio usado para PayPal no es imprescindible --*/	
	if ($datosAltaSocioPagoCuota['CODPAISDOM'] == 'ES')//antes de llamar funcion pregunta si país es ES
	{
		$codProv = substr($datosAltaSocioPagoCuota['CP'],0,2);
		
		require_once './modelos/modeloUsuarios.php';		
  $nombreProvincia = provinciaDatos($codProv,$datosAltaSocioPagoCuota['CODPAISDOM']);//Provincia domicilio es necesario para PayPal//contiene conexionDB()
				
		//echo "<br><br>3-1 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:nombreProvincia: "; print_r($nombreProvincia);

	 if ($nombreProvincia['codError'] !== '00000')//pero ya está registrado
	 { $resEmailErrorWMaster = emailErrorWMaster($datosAltaSocioPagoCuota['nomScript'].":".$datosAltaSocioPagoCuota['nomFuncion'].": ".$nombreProvincia['codError'].": ".$nombreProvincia['errorMensaje']);
		}
		else
		{ $datosAltaSocioPagoCuota['NOMPROVINCIA'] = $nombreProvincia['NOMPROVINCIA'];//Para PayPal, aunque no se tenga no es imprescindible, se rellena en Paypal		    
		}
	}	
	//echo "<br><br>3-2 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:nombreProvincia: "; print_r($nombreProvincia);

 /*-- Fin Buscar en BBDD Nombre provincia del domicilio del socio -------------------------------------*/		
	
	
 /*-- Incio Buscar los datos de cuentas bancarias de pago de cada agrupación de EL (y si no, tiene la estatal la comun) --*/	
	
	require_once './modelos/modeloBancos.php';				  
	$cadBancos = prepararCadIBANAgrupMostrar($datosAltaSocioPagoCuota['CODAGRUPACION']);	
		
	//devuelve $cadBancos['cadenaBancos'],	$cadBancos['GESTIONCUOTAS'],$cadBancos['concepto'],$cadBancos['cadEmailTesoreroAgrupacion']
	
	//echo "<br><br>4-1 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:cadBancos: ";print_r($cadBancos);						
	//$cadBancos['codError'] = '70000';
	
 if ($cadBancos['codError'] !== '00000')
 {$datosAltaSocioPagoCuota['codError'] = $cadBancos['codError'];//añado 2020-04-16
		$datosAltaSocioPagoCuota['errorMensaje'] = $datosAltaSocioPagoCuota['nomScript'].":".$datosAltaSocioPagoCuota['nomFuncion'].": ".$cadBancos['errorMensaje'];//añado 2020-04-16		
		$resEmailErrorWMaster = emailErrorWMaster($datosAltaSocioPagoCuota['nomScript'].":".$datosAltaSocioPagoCuota['nomFuncion'].": ".$cadBancos['codError'].": ".$cadBancos['errorMensaje']);		
		
		$datosAltaSocioPagoCuota['cadenaBancos'] = "<br /><br />No se han encontrado cuentas bancarias para esta agrupación de Europa Laica, 
		                                            puedes consultar opciones de pago a Tesorería: -tesoreria@europalaica.org- ";		 
	 $datosAltaSocioPagoCuota['GESTIONCUOTAS'] = NULL;
		$datosAltaSocioPagoCuota['concepto'] = NULL;
		$datosAltaSocioPagoCuota['cadEmailTesoreroAgrupacion'] = NULL;
 }	
 else //($cadBancos['codError']=='00000')
	{ 
  $datosAltaSocioPagoCuota['cadenaBancos'] = $cadBancos['cadenaBancos'];
	 $datosAltaSocioPagoCuota['GESTIONCUOTAS'] = $cadBancos['GESTIONCUOTAS'];
		$datosAltaSocioPagoCuota['concepto'] = $cadBancos['concepto'];
		$datosAltaSocioPagoCuota['cadEmailTesoreroAgrupacion'] = $cadBancos['cadEmailTesoreroAgrupacion'];
		
	}
	//echo "<br><br>4-2-1 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosAltaSocioPagoCuota: ";print_r($datosAltaSocioPagoCuota);
	
	/*-- Fin Buscar los datos de cuentas bancarias de pago de cada agrupación de EL (y si no, tiene la estatal) ----*/
		
	/*------------- Inicio Datos específicos PayPal ------------------------------------------------
			PARA EXCLUIR A AGRUPACIONES QUE GESTIONAN EL PAGO DIRECTAMENTE (Asturias) 
			DESCOMENTAR EL if ($cadBancos['GESTIONCUOTAS']=='ASOCIACION')
			ADEMÁS HABRÁ QUE QUITAR datosSocioPagoCuota['business'] Y $datosSocioPagoCuota['payPalScript'] 
	  en las funciones controladorSocios.php:confirmarAltaSocio(), pagarCuotaSocio()
			['GESTIONCUOTAS'] es un campo de la tabla "AGRUPACIONTERRITORIAL", que informa si cobra directamente
			esa agrupación valor "AGRUPACION" o lo cobra centralizado la asociación valor "ASOCIACION" 
			-Si el valor es ASOCIACION, se mostrará los IBAN de la Asociacion EL y "SÍ se incluirá el PayPal"
			-Si el valor es AGRUPACION, se mostrará el IBAN de esa agrupación y "NO se incluirá PayPal"
	--------------------------------------------------------------------------------------------------*/
	if ($datosAltaSocioPagoCuota['GESTIONCUOTAS']  == 'AGRUPACION')//será ['GESTIONCUOTAS'] == 'AGRUPACION'
	{ 
	  //echo "<br><br>4-3-1 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosAltaSocioPagoCuota: ";print_r($datosAltaSocioPagoCuota);			
	}
 else		//$cadBancos['GESTIONCUOTAS'] == 'ASOCIACION') 
 { $datosAltaSocioPagoCuota['item_name'] = 'PAGO CUOTA ANUAL EUROPA LAICA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			$datosAltaSocioPagoCuota['item_number'] = 'CUOTA DEL AÑO '.date('Y');		
			
			$datosAltaSocioPagoCuota['payPalScript'] = './vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php';	
			
			//echo "<br><br>4-3-2 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosAltaSocioPagoCuota: ";print_r($datosAltaSocioPagoCuota);	
			
			/*--- Inicio PARA PRUEBA SIN COBRAR: DESCOMENTAR y elegir "usuarios_desrrollo" o "usuarios_copia" -------			
				
			$datosAltaSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios_desarrollo/index.php?controlador=cPayPal&accion=confirmadoPagoAltaSocioPayPal_Registrarse';	
			$datosAltaSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios_desarrollo/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado';
			
			$datosAltaSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios_copia/index.php?controlador=cPayPal&accion=confirmadoPagoAltaSocioPayPal_Registrarse';
			$datosAltaSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios_copia/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado';					
			
	  $datosAltaSocioPagoCuota['business'] ='prueba1@europalaica.com'; //OJO: se también elige en el controladorSocios.php y anularía este valor
   $datosAltaSocioPagoCuota['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';//OJO: se también elige en el controladorSocios.php y anularía este valor					
	  ----- fin PARA PRUEBA SIN COBRAR: DESCOMENTAR -------------------------------------------------*/
			
			/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR ----------------------------------------*/		
			
			$datosAltaSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=confirmadoPagoAltaSocioPayPal_Registrarse';
		 $datosAltaSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado'; 			
			
	  //$datosAltaSocioPagoCuota['business'] ='tesoreria@europalaica.com'; //OJO: se también elige en el controladorSocios.php y anularía este valor
 	 //$datosAltaSocioPagoCuota['action'] = 'https://www.paypal.com/cgi-bin/webscr';//OJO: se también elige en el controladorSocios.php y anularía este valor
			
   /*----**** OJO fIN **** REAL PARA COBRAR: DESCOMENTAR -------------------------------------------*/	
	}		
	/*------------------- Fin Datos especificos PayPal ----------------------------------------------------*/
						
	//echo "<br><br>5 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:datosAltaSocioPagoCuota: "; print_r($datosAltaSocioPagoCuota);
		
	return $datosAltaSocioPagoCuota;	
}
/*---------Fin  mPrepararDatosRegSocioPagoCuotaBancosPayForm()------------------------------------*/		

	
/*---------Inicio mPrepararDatosSocioPagoCuotaBancosPayForm ()--------------------------------------
Al elegir pagar cuota (directemente o desde email), o después de confirmar el 
alta un socio, se muestra un formulario-mensaje:'./vistas/socios/vPagarCuotaSocioInc.php' 
con los datos de cuota a pagar, IBAN bancos de la AGRUPACION,  y botón con enlace
a script de PayPal personalizado para ese socio, si esa agrupación permite el 
cobro centalizado (no en Asturias).

Prepara, a partir de "$datosSocio" que recibe desde controladorSocios.php:pagarCuotaSocio() o 
confirmarAltaSocio(),	los datos bancarios necesarios para el formulario "vPagarCuotaSocioInc"		

RECIBE: $datosSocio, array con datos de un socio, que acaba de confirmar su alta,
o bien un socio previamente confirmada su alta
							
LLAMADO: desde contoladorSocio.php:confirmarAltaSocio(),pagarCuotaSocio() y pagarCuotaSocioSinCC()
LLAMA: modelos/modeloUsuarios.php:provinciaDatos()
       modelos/modeloBancos.php:prepararCadIBANAgrupMostrar()
		 
OBSERVACIONES: OJO LA PARTE DE PAYPAL SE CAMBIA PARA PRUEBAS:
payPalScript: PAYPAL SANBOX, y business ='prueba1@europalaica.com'

Para preparar los datos personales del socio, pues aquí ya proceden de varias tablas de un socio
que ya es alta confirmada, mientras que en  "mPrepararDatosRegSocioPagoCuotaBancosPayForm()" 
los datos solo de la tabla 	SOCIOSCONFIRMAR, ya que aún no es un alta confirmada
(En lo damás son idénticas)		
Es casi igual a "mPrepararDatosRegSocioPagoCuotaBancosPayForm()" con la única diferencia 
de que aquí en mPrepararDatosSocioPagoCuotaBancosPayForm(), se calcula y utiliza 
"$datosSocioPagoCuota['faltaPagar']" puesto en ella podría haber pagado parte 
de la cuota mientras que al darse de alta, eso no sucederá. 
No las unifico, por si más adelante se fuese necesario un tratamiento más independiente
--------------------------------------------------------------------------------------------------*/
function mPrepararDatosSocioPagoCuotaBancosPayForm($datosSocio) 
{
 //echo "<br><br>1 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocio: "; print_r($datosSocio);
	
	$datosSocioPagoCuota['nomScript'] = 'modeloBancos.php';
	$datosSocioPagoCuota['nomFuncion'] = 'mPrepararDatosSocioPagoCuotaBancosPayForm';
	$datosSocioPagoCuota['codError'] = '00000';
	$datosSocioPagoCuota['errorMensaje'] = '';
	
	/*--- Inicio, preparar datos personales del socio -----------------------------------------------*/
	
	//--- ES UNA ALTERNATIVA en lugar de ESCRIBIR CADA CAMPO, PERO CREO QUE ES MENOS CLARA DE ENTENDER
	/*unset($datosSocio['datosFormSocio']);//quitar datos de AGRUPACIÓN donde campos como DIRECCION Y CP SE solopan a los mismos de MIEMBRO 	
	foreach($datosSocio as $fila1 =>$valorFila1)
	{echo "<br>fila1:".$fila1." valorFila1: ";print_r($valorFila1);	  echo "<br>";		 
			foreach($valorFila1 as $fila2 =>$valorFila2)
			{echo "<br>fila2:".$fila2." valorFila2: ";print_r($valorFila2);
				$datosSocioPagoCuota[$fila2]=$valorFila2['valorCampo'];//bien
			}
	}//echo "<br><br>1b mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocioPagoCuota: ";print_r($datosSocioPagoCuota);		
	*/
	
 $datosSocioPagoCuota['faltaPagar'] = $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo']-
	                                      $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOPAGADA']['valorCampo'];	
																																			
	$datosSocioPagoCuota['ANIOCUOTA'] = $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];
	$datosSocioPagoCuota['CODCUOTA'] = $datosSocio['datosFormCuotaSocio']['CODCUOTA']['valorCampo'];
	$datosSocioPagoCuota['ESTADOCUOTA'] = $datosSocio['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'];	
	
	$datosSocioPagoCuota['IMPORTECUOTAANIOSOCIO'] = $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];
	$datosSocioPagoCuota['IMPORTECUOTAANIOPAGADA'] = $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOPAGADA']['valorCampo'];
	$datosSocioPagoCuota['ORDENARCOBROBANCO'] = $datosSocio['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'];
	
 $datosSocioPagoCuota['CUENTAIBAN'] = $datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'];
	$datosSocioPagoCuota['CODAGRUPACION'] = $datosSocio['datosFormSocio']['CODAGRUPACION']['valorCampo'];
			
	$datosSocioPagoCuota['NOM'] = $datosSocio['datosFormMiembro']['NOM']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['APE1'] = $datosSocio['datosFormMiembro']['APE1']['valorCampo'];//también PayPal			
	$datosSocioPagoCuota['APE2'] = $datosSocio['datosFormMiembro']['APE2']['valorCampo'];//también PayPal
	
	$datosSocioPagoCuota['nombrePaisDoc'] = $datosSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'];
	$datosSocioPagoCuota['CODPAISDOC'] = $datosSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'];
	$datosSocioPagoCuota['TIPODOCUMENTOMIEMBRO'] = $datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];
	$datosSocioPagoCuota['NUMDOCUMENTOMIEMBRO'] = $datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];
	
	$datosSocioPagoCuota['DIRECCION'] = $datosSocio['datosFormMiembro']['DIRECCION']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['LOCALIDAD'] = $datosSocio['datosFormMiembro']['LOCALIDAD']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['CP'] = $datosSocio['datosFormMiembro']['CP']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['NOMPROVINCIA'] = $datosSocio['datosFormMiembro']['NOMPROVINCIA']['valorCampo'];//también PayPal	
	$datosSocioPagoCuota['CODPAISDOM'] = $datosSocio['datosFormMiembro']['CODPAISDOM']['valorCampo'];//también PayPal
			
	$datosSocioPagoCuota['TELMOVIL'] = $datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['TELFIJOCASA'] = $datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'];//también PayPal
	$datosSocioPagoCuota['EMAIL'] = $datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];//también PayPal	

	$datosSocioPagoCuota['CODUSER'] = $datosSocio['datosFormUsuario']['CODUSER']['valorCampo'];//también PayPal					

 //echo "<br><br>3-1 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocioPagoCuota: ";print_r($datosSocioPagoCuota);		
	

	//-- Inicio Buscar en BBDD Nombre provincia domicilio socio usado para PayPal no es imprescindible --		
	if ($datosSocioPagoCuota['CODPAISDOM'] == 'ES')//antes de llamar funcion pregunta si país es ES para Paypal
	{
		$codProv = substr($datosSocioPagoCuota['CP'],0,2);
		
		require_once './modelos/modeloUsuarios.php';		
  $nombreProvincia = provinciaDatos($codProv,$datosSocioPagoCuota['CODPAISDOM']);//Provincia domicilio es necesario para PayPal en ES //contiene conexionDB()  
		//echo "<br><br>3-2 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:nombreProvincia: "; print_r($nombreProvincia);

		if ($nombreProvincia['codError'] !== '00000')//pero ya está registrado
		{ $resEmailErrorWMaster = emailErrorWMaster($datosSocioPagoCuota['nomScript'].":".$datosSocioPagoCuota['nomFuncion'].": ".$nombreProvincia['codError'].": ".$nombreProvincia['errorMensaje']);			 
		}		
		else
		{ $datosSocioPagoCuota['NOMPROVINCIA'] = $nombreProvincia['NOMPROVINCIA'];//Para PayPal, si España	pero no si no se tiene se podría rellenar en Paypal		
		}
	}	
	//echo "<br><br>3-3 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:nombreProvincia: ";print_r($nombreProvincia);
	
 //-- Fin Buscar en BBDD Nombre provincia del domicilio del socio ---------------------------------		
	/*--- Fin, preparar datos personales del socio ------------------------------------------------------*/	
	
 /*---- Incio Buscar en BBDD cuentas bancarias de pago de cada agrupación de EL (comun) --------------*/	
	
	require_once './modelos/modeloBancos.php';				  

	$cadBancos = prepararCadIBANAgrupMostrar($datosSocioPagoCuota['CODAGRUPACION']);
	//devuelve $cadBancos['cadenaBancos'],	$cadBancos['GESTIONCUOTAS'],$cadBancos['concepto'],$cadBancos['cadEmailTesoreroAgrupacion']
	
	//echo "<br><br>4-1 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:cadBancos: ";print_r($cadBancos);

	if ($cadBancos['codError'] !== '00000')
 {$datosSocioPagoCuota['codError'] = $cadBancos['codError'];//añado 2020-04-16
		$datosSocioPagoCuota['errorMensaje'] .= $cadBancos['codError'].$cadBancos['errorMensaje'];//añado 2020-04-16		
		$resEmailErrorWMaster = emailErrorWMaster($datosSocioPagoCuota['nomScript'].":".$datosSocioPagoCuota['nomFuncion'].": ".$cadBancos['codError'].": ".$cadBancos['errorMensaje']);		
		
		$datosSocioPagoCuota['cadenaBancos'] = "<br /><br />No se han encontrado cuentas bancarias para esta agrupación de Europa Laica, 
		                                            puedes consultar opciones de pago a Tesorería: -tesoreria@europalaica.org- ";		 
	 $datosSocioPagoCuota['GESTIONCUOTAS'] = NULL;
		$datosSocioPagoCuota['concepto'] = NULL;
		$datosSocioPagoCuota['cadEmailTesoreroAgrupacion'] = NULL;		

		//echo "<br><br>4-2 modeloBancos.php:mPrepararDatosRegSocioPagoCuotaBancosPayForm:cadBancos: ";print_r($cadBancos);
 }
 else //($cadBancos['codError']=='00000')
	{ 								
  $datosSocioPagoCuota['cadenaBancos'] = $cadBancos['cadenaBancos'];
	 $datosSocioPagoCuota['GESTIONCUOTAS'] = $cadBancos['GESTIONCUOTAS'];
		$datosSocioPagoCuota['concepto'] = $cadBancos['concepto'];
		$datosSocioPagoCuota['cadEmailTesoreroAgrupacion'] = $cadBancos['cadEmailTesoreroAgrupacion'];
	}
	/*---- Fin Buscar en BBDD cuentas bancarias de pago de cada agrupación de EL (comun) -------------*/	
	
		
	/*------------- Inicio Datos específicos PayPal ------------------------------------------------
			PARA EXCLUIR A AGRUPACIONES QUE GESTIONAN EL PAGO DIRECTAMENTE (Asturias) 
			DESCOMENTAR EL if ($cadBancos['GESTIONCUOTAS']=='ASOCIACION')
			ADEMÁS HABRÁ QUE QUITAR datosSocioPagoCuota['business'] Y $datosSocioPagoCuota['payPalScript'] 
	  en las funciones controladorSocios.php:confirmarAltaSocio(), pagarCuotaSocio()
			['GESTIONCUOTAS'] es un campo de la tabla "AGRUPACIONTERRITORIAL", que informa si cobra directamente
			esa agrupación valor "AGRUPACION" o lo cobra centralizado la asociación valor "ASOCIACION" 
			-Si el valor es ASOCIACION, se mostrará los IBAN de la Asociacion EL y "SÍ se incluirá el PayPal"
			-Si el valor es AGRUPACION, se mostrará el IBAN de esa agrupación y "NO se incluirá PayPal"
	--------------------------------------------------------------------------------------------------*/
	if ($datosSocioPagoCuota['GESTIONCUOTAS'] == 'AGRUPACION')//será ['GESTIONCUOTAS'] == 'AGRUPACION'
	{ 
	  //echo "<br><br>4-3-1 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocioPagoCuota: ";print_r($datosSocioPagoCuota);			
	}
 else		//$cadBancos['GESTIONCUOTAS'] == 'ASOCIACION')
 { $datosSocioPagoCuota['item_name'] = 'PAGO CUOTA ANUAL EUROPA LAICA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			$datosSocioPagoCuota['item_number'] = 'CUOTA DEL AÑO '.date('Y');
			
		 $datosSocioPagoCuota['payPalScript'] = './vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php';	 
			
			//echo "<br><br>4-3-2 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocioPagoCuota: ";print_r($datosSocioPagoCuota);		

			/*---- Aviso inicio: LO SIGUIENTE LO TRASLADO A LAS FUNCIONES: 
				controladorSocios.php:confirmarAltaSocio(),pagarCuotaSocio(),pagarCuotaSocioSinCC()		
				ESTARÁ MAS ACCESIBLE Y FACIL DE VER Y CAMBIAR PARA PRUEBAS CON SANDBOX 
			-------------------------------------------------------------------------------*/
			
			/*--- Inicio PARA PRUEBA SIN COBRAR: DESCOMENTAR y elegir "usuarios_desrrollo" o "usuarios_copia" -------			
				
			$datosSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios_desarrollo/index.php?controlador=cPayPal&accion=confirmadoPagoAltaSocioPayPal_Registrarse';	
			$datosSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios_desarrollo/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado';
			
			$datosSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios_copia/index.php?controlador=cPayPal&accion=confirmadoPagoAltaSocioPayPal_Registrarse';
			$datosSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios_copia/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado';					
			
	  $datosSocioPagoCuota['business'] ='prueba1@europalaica.com'; //OJO: se también elige en el controladorSocios.php y anularía este valor
   $datosSocioPagoCuota['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';//OJO: se también elige en el controladorSocios.php y anularía este valor
			
	  ----- fin PARA PRUEBA SIN COBRAR: DESCOMENTAR -------------------------------------------------*/
		
		/*----**** OJO inicio **** REAL PARA COBRAR: DESCOMENTAR -----------------------------------------*/		
		
		$datosSocioPagoCuota['returnPagado']='https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=confirmarPagoPayPal';
		$datosSocioPagoCuota['returnCancelado']='https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=cancelacionPagoPayPal&resultado=cancelado';	
		
		//$datosSocioPagoCuota['business'] = 'tesoreria@europalaica.com';//OJO: se también elige en el controladorSocios.php y anularía este valor
		//$datosSocioPagoCuota['action'] = 'https://www.paypal.com/cgi-bin/webscr';//OJO: se también elige en el controladorSocios.php y anularía este valor
		
		/*----**** OJO fin **** REAL PARA COBRAR: DESCOMENTAR ----------------------------------------*/		
	
		/*----------- Fin Preparar Datos Socio Para Pago Cuota con Bancos y Pay ------------------------------*/				
			
			/*-- Aviso fin: LO SIGUIENTE LO TRASLADO A LAS FUNCIONES: controladorSocios.php --*/			
							
	}//if ($cadBancos['GESTIONCUOTAS'] == 'ASOCIACION') 
		
	/*------------------- Fin Datos especificos PayPal -----------------------------------------------*/		
					
	//echo "<br><br>5 modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm:datosSocioPagoCuota: "; print_r($datosSocioPagoCuota);
		
	return $datosSocioPagoCuota;	
}
/*---------Fin  mPrepararDatosSocioPagoCuotaBancosPayForm()----------------------------------------*/	

?>