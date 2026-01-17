<?php
/*------------------------- eliminarSocioPorGestor.php --------------------------------------------------	
FICHERO: controladores/libs/eliminarSocioPorGestor.php
VERSION: PHP 7.3.21

Se eliminan datos identificativos del socio (privacidad de datos), y se insertan
algunos datos en la tabla "MIEMBROELIMINADO5ANIOS", que se mantendrán 5 años por motivos fiscales.
Se llama desde "LISTA DE SOCI@S", al hacer clic en el icono Baja.

Además en caso de baja de un socio por defunción, en "bajaSocioFallecido()", se guardarán ciertos 
datos personales en la tabla SOCIOSFALLECIDOS, para tener un histórico de los socios ya fallecidos.
La parte de navegación se añade, para mantener la fila superior la navegación de opciones 

LLAMADA: cCoordinador.php:eliminarSocioCoord(),cPresidente.php:eliminarSocioPres(),
cTesorero.php:eliminarSocioTes()		
LLAMA: validarCamposSocio.php:validarEliminarSocio(),
       modeloSocios.php:eliminarDatosSocios(), modeloPresCoord.php:bajaSocioFallecido(),
       buscarDatosSocio(),buscarEmailCoordSecreTesor()
       modeloEmail.php:emailBajaUsuario(),$reEnviarEmailCoSeTe(),emailErrorWMaster()
							vistas/gestoresComun/vEliminarSocioPorGestorInc.php
							vistas/mensajes/vMensajeCabSalirNavInc.php
							
OBSERVACIÓN: Es casi idéntico a cPresidente:eliminarSocioPres(), cCoordinador:eliminarSocioCoord(),

OBSERVACIONES: Este script es la parte común de las funciones para dar de baja
a un socio por parte de un gestor. He generado este script común para aligerar
contenido en las funciones de bajas de Socios por Gestores y mas consistencia() 
para posibles modificaciones
																																					
2020-09-10: probada PHP 7.3.21, Aquí no necesita cambios para PDO, lo incluyen 
internamente las funciones		
--------------------------------------------------------------------------------------------------------*/	
	
	//echo "<br><br>1-1 controladores/libs/eliminarSocioPorGestor.php:_POST: ";print_r($_POST);
	
	require_once './modelos/modeloSocios.php';
	require_once './modelos/modeloPresCoord.php';
	require_once './vistas/gestoresComun/vEliminarSocioPorGestorInc.php';	
	require_once './modelos/libs/validarCamposSocio.php';
	require_once './vistas/mensajes/vMensajeCabSalirNavInc.php'; 	 		
	require_once './modelos/modeloEmail.php';
	
	$nomScriptFuncionError .= 'controladores/libs/eliminarSocioPorGestor.php. Error: ';
	
 if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']) ) 
 {
		$datosSocio = $_POST;
		
		$nomApe = strtoupper($_POST['datosFormMiembro']['NOM']." ".$_POST['datosFormMiembro']['APE1']);
		
  if (isset($_POST['NoEliminar'])) //ha pulsado el botón "noGuardarDatosSocio"
	 {
				$datosMensaje['textoComentarios'] = "Ha salido sin dar de baja al socio/a ".$nomApe;
				vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
	 }				
		elseif (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) ) 
  { 	
				$resValidarCamposForm = validarEliminarSocio($_POST);//en modelos/libs/validarCamposSocio.php
				
				//echo "<br><br>3 controladores/libs/eliminarSocioPorGestor.php:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				if ($resValidarCamposForm['codError'] !=='00000')
				{	
						if ($resValidarCamposForm['codError'] >= '80000')//Error lógico		probado		
						{
								vEliminarSocioPorGestorInc($tituloSeccion,$resValidarCamposForm,$navegacion); 
						}			
						else //$resValidarCamposForm['codError']< '80000') = error sistema creo que no se producirá
						{ 
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
								vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);												
						}
				}	
				else //$resValidarCamposForm['codError']=='00000' = NO HAY ERROR
				{				
						if (isset($_POST['SiEliminar']))
						{						
								$reEliminarSocio = eliminarDatosSocios($_POST);	//en modeloSocios.php probado error 
								//echo "<br><br>4-1 controladores/libs/eliminarSocioPorGestor.php:reEliminarSocio "; print_r($reEliminarSocio);			
						}
						elseif (isset($_POST['SiEliminarFallecimiento']))
						{	
								$reEliminarSocio = bajaSocioFallecido($_POST);	//en modeloPresCoord.php probado error
								//echo "<br><br>4-2 controladores/libs/eliminarSocioPorGestor.php:reEliminarSocio "; print_r($reEliminarSocio);
						}	
						
						if ($reEliminarSocio['codError'] !== "00000")
						{         						
								$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEliminarSocio['codError'].": ".$reEliminarSocio['errorMensaje']);
        vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);		
						}
						else //($reEliminarSocio['codError'] == '00000')
						{						
								//echo"<br><br>5-1 controladores/libs/eliminarSocioPorGestor.php:sesion: ";print_r($_SESSION);
								$usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
								
								if 	( $usuarioBuscado == $_SESSION['vs_CODUSER'] )//solo si el Gestor se borra a él mismo	
								{	unset($_SESSION);//para que ya no esté como autorizado
									//echo"<br><br>5-2 controladores/libs/eliminarSocioPorGestor.php:sesion: ";print_r($_SESSION);
								}	
				
								//-------------------- Inicio email a socio -------------------------------		
								if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	//si falta, no se envía email
								{
										if (isset($_POST['SiEliminarFallecimiento']))
										{ $resultEnviarEmail = emailBajaUsuarioFallecido($datosSocio['datosFormMiembro']);		
												//echo"<br><br>6-1 controladores/libs/eliminarSocioPorGestor.php:resultEnviarEmail: ";print_r($resultEnviarEmail);	
										}	
										else
										{	$resultEnviarEmail = emailBajaUsuario($datosSocio['datosFormMiembro']);		
												//echo"<br><br>6-2 controladores/libs/eliminarSocioPorGestor.php:resultEnviarEmail: ";print_r($resultEnviarEmail);
										}	

										if ($resultEnviarEmail['codError'] !== '00000')//probado error
										{       
												$textoComentariosEmail = '<br /><br />Por un error no se ha podido envíar el email con la información de la baja, a la dirección de correo que está anotada para el socio.';									
												$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$resultEnviarEmail['codError'].": ".$resultEnviarEmail['errorMensaje'].$textoComentariosEmail);
										}			
								}//if ($datosSocio['datosFormMiembro']['EMAILERROR'] == 'NO')	
								//----------------------- Fin	email a socio -------------------------------						
											
								//-------------------- Inicio email a Coord Secre Tes Pres ----------------	
								$reDatosEmailCoSeTe = buscarEmailCoordSecreTesor($datosSocio['datosFormUsuario']['CODUSER']);
					
								//echo"<br><br>6-2- controladores/libs/eliminarSocioPorGestor.php:reDatosEmailCoSeTe:";print_r($reDatosEmailCoSeTe);
								if ($reDatosEmailCoSeTe['codError'] == '00000')
								{ 
										$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';																																						
										$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reDatosEmailCoSeTe['codError'].": ".$reDatosEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);									
								}
								else 
								{
								//OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL A Presi,Coood, Secre, Tes
			//****************************************************************************************************************
//			       $reEnviarEmailCoSeTe = emailBajaSocioCoordSecreTesor($reDatosEmailCoSeTe,$datosSocio);
			//FIN COMENTAR ****************************************************************************************************************

						   	//echo"<br><br>7 controladores/libs/eliminarSocioPorGestor.php:reEnviarEmailCoSeTe: ";print_r($reEnviarEmailCoSeTe);
							  	if ($reEnviarEmailCoSeTe['codError'] !=='00000')//
										{						
												$textoComentariosEmail .= '<br /><br />Por un error, coordinación, secretaría, tesorería y presidencia no han recibido el email con la información de esta baja.';										
												$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$reEnviarEmailCoSeTe['codError'].": ".$reEnviarEmailCoSeTe['errorMensaje'].": ".$textoComentariosEmail);
										}		
								}	
								//-------------------- Fin email a Coord Secre Tes Pres ------------------	 
										
							$datosMensaje['textoComentarios'] = $reEliminarSocio['arrMensaje']['textoComentarios'].'<b>'.$textoComentariosEmail.'</b>';
       //echo "<br><br>8 controladores/libs/altaSocioPorGestor.php:datosMensaje: ";print_r($datosMensaje);

							vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);

						}// else $reEliminarSocio['codError'] == '00000'			
				}// else $resValidarCamposForm['codError']=='00000' = NO HAY ERROR
  }// if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimeinto']) )      
 }// if (isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar'])) 
		
 else //!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar'])) 
 {
		$anioCuota = '%';
	 $usuarioBuscado = $_POST['datosFormUsuario']['CODUSER'];
			
		$datSocioEliminar = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocios.php probado error
		//echo "<br><br>9 controladores/libs/eliminarSocioPorGestor.php:datSocioEliminar: "; print_r($datSocioEliminar); 
		 		 		 
  if ($datSocioEliminar['codError'] !== '00000')
  {			
			$resEmailErrorWMaster = emailErrorWMaster($nomScriptFuncionError.$datSocioEliminar['codError'].": ".$datSocioEliminar['errorMensaje']);
			vMensajeCabSalirNavInc($tituloSeccion,$datosMensaje,$_SESSION['vs_enlacesSeccIzda'],$navegacion);
		}      
		else //$datSocioEliminar['codError']=='00000'
		{//echo "<br><br>10 controladores/libs/eliminarSocioPorGestor.php:datSocioEliminar:";print_r($datSocioEliminar);	

			vEliminarSocioPorGestorInc($tituloSeccion,$datSocioEliminar,$navegacion); 
  }   
 }//!(isset($_POST['SiEliminar']) || isset($_POST['SiEliminarFallecimiento']) || isset($_POST['NoEliminar']))

//--------------------------- Fin eliminarSocioPorGestor.php -----------------------------

?>