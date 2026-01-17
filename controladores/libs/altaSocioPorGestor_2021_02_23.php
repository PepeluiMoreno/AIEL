<?php
/*---------------------- altaSocioPorGestor.php -------------------------------------
FICHERO: controladores/libs/altaSocioPorGestor.php
VERSION: PHP 7.3.21

Inicializa los campos necesarios para la función altaSocioPorGestor()
Se insertan los datos del socio a dar de alta en las tablas correspondientes
Se sube un archivo al servidor con la firma de autorización del socio como garantía de 
protección de datos hasta que el socio se de de baja.
Además guarda en la tabla MIEMBRO, campo ARCHIVOFIRMAPD (hasta que el socio sea baja), 
con los apellidos y nombre y fecha y el PATH_ARCHIVO_FIRMAS, con dirección del archivo 		
	
Genera un socio con el usuario = NOM.$codUser+125 y añade un digito rand;
una contraseña que es: sha1($codUser.$usuario) (y estará encriptada)
En la tabla USUARIO, el estado quedará: 'alta-sin-password-gestor'

Llegará un email al socio (si tiene email) para pedirle decirle qu está dado 
de alta y que pulse un link, para que elija su contraseña y confirme el email.
También llegará un email a Presidente, coordinador, secretario, tesorero		

LLAMADA: cCoordinador.php:altaSocioPorGestorCoord(),
cPresidente.php:altaSocioPorGestorPres(), cTesorero.php:altaSocioPorGestorTes()		

LLAMA: modelosSocios.php:buscarCuotasAnioEL(), buscarEmailCoordSecreTesor()       
modeloPresCoord.php:mAltaSocioPorGestor()    
modeloArchivos.php:arrMimeExtArchAltaSocioFirmaPermitidas(),cadExtensionesArchivos()
modelos/libs/arrayParValor.php:parValoresRegistrarUsuario()
modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php:
validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor()			
modeloEmail.php:emailErrorWMaster(),emailConfirmarEmailAltaSocioPorGestor(),
emailAltaSocioGestorCoordSecreTesor()
vistas/gestoresComun/vAltaSocioPorGestorIncp.Php	
usuariosLibs/encriptar/encriptacionBase64.php";	

require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';

OBSERVACIONES: Este script es la parte común de las funciones para dar de alta 
a un socio por parte de un gestor. He generado este script común para aligerar
contenido en las funciones de altas Socios por Gestores y mas consistencia() 
para posibles modificaciones
																																					
2020-09-10: probada PHP 7.3.21, Aquí no necesita cambios para PDO, lo incluyen 
internamente las funciones		
------------------------------------------------------------------------------------*/

 require_once './modelos/modeloArchivos.php';
	require_once './modelos/modeloSocios.php';
 require_once './modelos/modeloPresCoord.php';
	require_once './modelos/libs/arrayParValor.php';
	require_once './modelos/modeloEmail.php';
	require_once './vistas/gestoresComun/vAltaSocioPorGestorInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php';
	
	$nomScriptFuncionError .= 'controladores/libs/altaSocioPorGestor.php. Error: ';
	
	if (!$_POST) 
 {
		require_once './controladores/libs/inicializaCamposAltaSocioGestor.php';//inicializa algunas variables 	
					
		$parValorCombo = parValoresRegistrarUsuario($valorDefectoPaisDoc,$valorDefectoPaisDom,$valorDefectoAgrup);//antes $parValorCombo = parValoresRegistrarUsuario("ES","ES",'00000000');						
		//echo "<br><br>2 controladores/libs/altaSocioPorGestor.php:parValorCombo: "; print_r($parValorCombo);

		if ($parValorCombo['codError'] !== '00000')
		{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);								
		}	
		else //$parValorCombo['codError']=='00000'
		{
			$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');	//en modeloSocios.php, incluye conexion()	probado error		
		 
			//echo "<br><br>3 controladores/libs/altaSocioPorGestor.php:resCuotasAniosEL:";print_r($resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']);

   if ($resCuotasAniosEL['codError'] !== '00000')
			{$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resCuotasAniosEL['codError'].": ".$resCuotasAniosEL['errorMensaje']);		
			 vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);														
			}			
			else
			{$datCuotaAnioEL=$resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];
		
				$datosInicio['datosFormCuotaSocio']['ANIOCUOTA'] =	$datCuotaAnioEL['General']['ANIOCUOTA'];
				$datosInicio['datosFormCuotaSocio']['CODCUOTAGeneral'] =	$datCuotaAnioEL['General']['CODCUOTA'];	
				$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']=$datCuotaAnioEL['General']['IMPORTECUOTAANIOEL'];
				$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']=	$datCuotaAnioEL['Joven']['IMPORTECUOTAANIOEL'];
				$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']=	$datCuotaAnioEL['Parado']['IMPORTECUOTAANIOEL'];
				$datosInicio['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']=	$datCuotaAnioEL['Honorario']['IMPORTECUOTAANIOEL'];
				
				/* [cadExtPermitidas], es un string con las extensiones permitidas para los archivos a subir con firma del socio, se obtiene a 
				 partir del array "arrMimeExtArchivoFirmasPermitidas()", esto podría incluirse en "controladores/libs/inicializaCamposAltaSocioGestor.php"
					o ponerlo directamente en el formulario (['cadExtPermitidas'] = "doc,docx,odt,odi,gif,jpg,jpeg,pdf").
			 */
				$arrMimeExtArchivoFirmasPermitidas = arrMimeExtArchAltaSocioFirmaPermitidas();//en modeloArchivos.php			
				$datosInicio['ficheroAltaSocioFirmado']['cadExtPermitidas'] = cadExtensionesArchivos($arrMimeExtArchivoFirmasPermitidas);//en modeloArchivos.php
				
				//echo "<br><br>4 controladores/libs/altaSocioPorGestor.php:datosInicio:";print_r($datosInicio);	
				
				vAltaSocioPorGestorInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$datosInicio,$parValorCombo);
   }			
		}//$parValorCombo['codError']=='00000'
 }
 else //POST
 {		
  if (isset($_POST['noGuardarDatosSocio'])) //ha pulsado el botón "noGuardarDatosSocio"
	 {
			$datosMensaje['textoComentarios'] = "Ha salido sin dar de alta al socio/a";
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);
	 }			
	 else //==(isset($_POST['siGuardarDatosSocio']))Pulsado el botón "siGuardarDatosSocio"
	 {/* La siguiente función además de validar los datos personales del socio,
			   sube el archivo con la firma para protección de datos.
			*/
			require_once './modelos/libs/validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor.php';
	 	$resValidarCamposForm = validarCamposSocio_y_SubirArchFirmaAltaSocioPorGestor($_POST,$_FILES['ficheroAltaSocioFirmado']);		
			
			//echo "<br><br>5 controladores/libs/altaSocioPorGestor.php:resValidarCamposForm: ";print_r($resValidarCamposForm);
			//resValidarCamposForm:  SERÁ ALGO COMO
			//[ficheroSocioFirmado] => Array ( [name] => Tabla_total_medias_2018_06_30_1.doc [type] => application/msword [tmp_name] => /tmp/phpEvqaXZ [error] => 0 
			//[size] => 37888 [codError] => 00000 [errorMensaje] => [directorioSubir] => /../upload/FIRMAS_ALTAS_SOCIOS_GESTOR [nombreArchExtGuardado] => villa__SEGUNDOcientocurentaynueve2018_08_18_09_17_25.doc ) ) 
		
		 if ($resValidarCamposForm['codError'] !== '00000')//Error
		 {
				if ($resValidarCamposForm['codError'] >= '80000')//Error lógico				
		  {$parValorCombo = parValoresRegistrarUsuario($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																																	$resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																																	$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']);	
	    //echo "<br><br>6-1 controladores/libs/altaSocioPorGestor.php:parValorCombo:";print_r($parValorCombo);

					if ($parValorCombo['codError'] !== '00000') 
		   {$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);		
		   	vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
		   }	
		   else //habría que mandar las cuotas y nombres del año actual buscandolas de importedescuotatasocio
		   {//echo "<br><br>6-2 controladores/libs/altaSocioPorGestor.php:resValidarCamposForm: ";print_r($resValidarCamposForm);
					
						 vAltaSocioPorGestorInc($tituloSeccion,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion,$resValidarCamposForm,$parValorCombo);			
		   }
		  }	//if $resValidarCamposForm['codError'] >= '80000'	)//Error lógico
				else //$resValidarCamposForm['codError']< '80000')//Error sistema					
		  {$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
     vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);					
		  }
		 }//if ($resValidarCamposForm['codError'] !== '00000')//Error			
		 else //$resValidarCamposForm['codError']=='00000'
		 {				
    $resAltaSocio = mAltaSocioPorGestor($resValidarCamposForm);//en modeloPresCoord.php:mAltaSocioPorGestor()//tiene que devolver CODUSER  probado error
			 
				//echo "<br><br>7 controladores/libs/altaSocioPorGestor.php:resAltaSocio: ";print_r($resAltaSocio);		
				
			 if ($resAltaSocio['codError'] !== '00000') //siempre será ($resAltaSocio['codError'] < '80000'))
			 {
					$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resAltaSocio['codError'].": ".$resAltaSocio['errorMensaje']);
				 $datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'];
					
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);						
			 }	
			 else // ($resAltaSocio['codError']=='00000') 
			 { 				
				 $textoComentariosEmail = '';
					//--------------------- Inicio Email a socio -----------------------------
					if ($resValidarCamposForm['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
			  {
						require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
	     $datSocioConfEstablecerPass['CODUSER'] = encriptarBase64($resAltaSocio['datosUsuario']['CODUSER']);	

						$datSocioConfEstablecerPass['USUARIO']	=	$resAltaSocio['datosUsuario']['USUARIO'];//Es un dato que se genera en mAltaSocioPorGestor()	
						//$datSocioConfEmailPass['CODSOCIO']	= $resAltaSocio['datosSocio']['CODSOCIO'];//Es un dato que se genera en mAltaSocioPorGestor(), por ahora no lo utilizo										
						$datSocioConfEstablecerPass['EMAIL'] = $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo'];
						$datSocioConfEstablecerPass['SEXO'] = $resValidarCamposForm['datosFormMiembro']['SEXO']['valorCampo'];
						$datSocioConfEstablecerPass['NOM']  = $resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];
						$datSocioConfEstablecerPass['APE1'] = $resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];	
		
						$reEmailConfEstablecerPass =	emailConfirmarEmailAltaSocioPorGestor($datSocioConfEstablecerPass);	//envía mensaje a socio si tiene email	//probado error
	     //echo"<br><br>8 controladores/libs/altaSocioPorGestor.php:reEmailConfEstablecerPass: ";print_r($reEmailConfEstablecerPass);	
						
					 if ($reEmailConfEstablecerPass['codError'] !== '00000')
						{       
								$textoComentariosEmail = '<br /><br />Por un error el socio/a no ha recibido el email con la información de esta alta como socio.';									
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEmailConfEstablecerPass['codError'].": ".$reEmailConfEstablecerPass['errorMensaje'].$textoComentariosEmail);
						}     					
     }//--------------------- Fin Email a socio -------------------------------
					
     //--------- Inicio Email Coordinador,Secretario,Tesororero agrupacion ----
     $reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($resAltaSocio['datosUsuario']['CODUSER']);//en modeloSocios.php para buscar email de CoordSecreTesor, probado error									
     
					//echo"<br><br>9-1 controladores/libs/altaSocioPorGestor.php:reDatosEmailCoSeTe: ";print_r($reDatosEmailCoSeTe);					
					
					if ($reDatosEmailCoSeTe['codError'] !== '00000') 	
					{
							$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta.';																																						
							$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
					}			 
		   else //if ($reDatosEmailCoSeTe['codError'] == '00000')
			  {
						
//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
//****************************************************************************************************************

//						$reEnviarEmailCoSeTe = emailAltaSocioGestorCoordSecreTesor($reDatosEmailCoSeTe,$resValidarCamposForm);//a gestores
//FIN COMENTAR ****************************************************************************************************************
				  //echo"<br><br>9-2 controladores/libs/altaSocioPorGestor.php:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
							if ($reEnviarEmailCoSeTe['codError'] !=='00000')//probado error
							{						
									$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta confirmación del alta';										
									$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
							}			
					}//--------- Fin Email Coordinador,Secretario,Tesororero agrupacion -----						
								
				 $datosMensaje['textoComentarios'] = $resAltaSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
     //echo "<br><br>10 controladores/libs/altaSocioPorGestor.php:datosMensaje: ";print_r($datosMensaje);
					
					vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$datosNavegacion['navegacion']);//pantalla con la información del alta
			 }//else $resAltaSocio['codError']=='00000' 
				
	  }//$resValidarCamposForm['codError']=='00000'
	 }//else isset($_POST['siGuardarDatosSocio'])
 }//else $_POST 		
/*------------------------------- Fin altaSocioPorGestor -------------------------------*/

?>