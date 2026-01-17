<?php
/* ---------------------------------------------------------------------------------------------------------------------
FICHERO: formAltaSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para el registro de un nuevo socio/a. 
             Cuando finaliza el registro le lleva a una pantalla donde le indica que ya está registrado,
													pero que falta aún su confirmación de hacerse socio. 
													También le indica los modos de pago de la cuota.
													A la vez le llegará un email al socio para que acepte la confirmación de ser socio

LLAMADA: vistas/socios/vCuerpoAltaSocio.php y a su vez de controladorSocios.php:altaSocio()

OBSERVACIONES: 
2019-12-12 : Añado htmlspecialchars para mostrar bien los caracteres especiales ',",\,			
2022-12-15 : Varias simplificaciones texto y suprimir COLABORA a petición presidencia
2023-01-22 : Cambio fecha de nacimiento completa por sólo "año de nacimiento" 
             Script para evitar copia y pega email		
----------------------------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<!-- Para control tamaño campo textarea, además hay control de tamaño interno en php -->
<script type="text/javascript">
function limitarTextoArea(max, id)
{
	if(max < document.getElementById(id).value.length)
	{ 
   document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
			
   //alert("Llegó al máximo de caracteres permitidos: "+max); //Sí funciona: sería un pop up como opción a lo siguiente		
			
			let text = ("<span class='error'> Supera al máximo número de caracteres permitidos: " + max + "</span><br>");	
			
			// Si cambia span class='error' en CSS, comentar linea anterior y descomentar la línea siguiente:			
			//let text = ("<span style='color:#cc0000' font: 0.5rem Verdana, sans-serif, Helvetica, Arial; float: left; text-align: left; > Supera al máximo número de caracteres permitidos " + max + "</span>");
			
			document.getElementById(id).insertAdjacentHTML("afterend", text);
	}			
}
</script>

<!-- ******************************************************************************* -->


<!-- *********************** Inicio <div id="registro"> Incluye todo *********************************************** -->
<div id="registro">

 <?php //echo "<br><br>formAltaSocio.php:datosSocio: "; print_r($datosSocio); ?>

		<span class="error">
									<?php
									if (isset($datosSocio['codError']) && $datosSocio['codError'] !== '00000') 
									{
													echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
									}
									?>
		</span> 	
		<br /> 	<br /> 
  <!-- ********************* Inicio texto informativo para socio *************************************************** -->		
		<span class="textoAzu112Left2">
					<!--	<ul>
          <li>Al asociarte ayudarás a defender el "laicismo" como principio democrático de convivencia en una sociedad que es plural</li>
										<li>Al hacerte socio/a tendrás derecho a participar en las asambleas de Europa Laica con voz y voto</li>
										<li>Tendrás acceso a los cursos de formación y grupos de trabajo de Europa Laica</li>
										<li>Recibirás información de las actividades y campañas por correo electrónico (si lo autorizas)</li>
										<li>No aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes</li>										
						</ul>		
						<br />
  	 	-->
						
						<b>PARA REGISTRARTE COMO SOCIA/O DEBES RELLENAR LOS SIGUIENTES DATOS</b>
						<br /><br /><br />					
      Si eres menor de edad (15 a 18 años), para hacerte socio/a es necesario que tu tutor legal presente una autorización firmada por escrito o por email a info@europalaica.org<!--la Junta Directiva-->						
						<br /><br />	
 						
						Los campos con asterisco (<strong>*</strong>) son obligatorios	
		</span> 
		<br />
		
  <!-- ********************* Fin texto informativo para socio ****************************************************** -->				


  <!-- ********************* Inicio del formulario registro de datos del socio ************************************* -->
		<form name="registrarSocio" method="post"	action="./index.php?controlador=controladorSocios&amp;accion=altaSocio">
		
		 <br /> 	
			
			<!-- ******************** Inicio datos de personales ******************************************************** -->	
			<fieldset>
					<legend><strong>Datos personales</strong></legend>			
					<p>				
					
							<label>*Documento</label>
									<?php
											$parValorNIF = array("NIF" => "NIF", "NIE" => "NIE", "Pasaporte" => "Pasaporte", "Otros" => "Otros");

											echo comboLista($parValorNIF, "datosFormMiembro[TIPODOCUMENTOMIEMBRO]", 
											$datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'], 
											$parValorNIF[$datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']], "NIF", "NIF");
									?>
							
							<label>*Nº documento</label> <!-- obligatorio, valida letra NIF y NIE pero no para pasaporte u otros -->
							<input type="text"
														name="datosFormMiembro[NUMDOCUMENTOMIEMBRO]"
														value="<?php
														if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'])) 
														{
																		echo htmlspecialchars($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'],ENT_QUOTES);	
														}
														?>"
														size="20"
														maxlength="40"
														/>	 
							<span class="error">
														<?php
														if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];	
														}
														?>
							</span>		
							
							<label>*País documento</label>
							<?php
								//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 
								//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
								
								echo comboLista($parValorComboPaisMiembro['lista'], "datosFormMiembro[CODPAISDOC]", 
								$parValorComboPaisMiembro['valorDefecto'], $parValorComboPaisMiembro['descDefecto'], "", "");
							?> 
							<span class="error">
													<?php
													if (isset($datosSocio['datosFormMiembro']['CODPAISDOC']['errorMensaje'])) 
													{
																	echo $datosSocio['datosFormMiembro']['CODPAISDOC']['errorMensaje'];
													}
													?>
							</span>	
							<br />
								
							<label>*Sexo</label>	
							<span class="error">
													<?php
															if (isset($datosSocio['datosFormMiembro']['SEXO']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['SEXO']['errorMensaje'];
															}
													?>
							</span>

							<input type="radio"
														name="datosFormMiembro[SEXO]"
														value='H' 
														<?php
																if (isset($datosSocio['datosFormMiembro']['SEXO']['valorCampo']) && $datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'H') 
																{
																				echo "checked";
																}
														?>
														/>
														<label>Hombre</label>
							<input type="radio"
														name="datosFormMiembro[SEXO]"
														value='M'
														<?php
																if (isset($datosSocio['datosFormMiembro']['SEXO']['valorCampo']) && $datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'M') 
																{
																				echo "checked";
																}
															?>						 
														/>
														<label>Mujer</label>		
   
							<!--<input type="radio"
														name="datosFormMiembro[SEXO]"
														value='X'
														<?php
																/*if (isset($datosSocio['datosFormMiembro']['SEXO']['valorCampo']) && $datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'X') 
																{
																				echo "checked";
																}*/
															?>						 
														/>
														<label>Otro</label>-->
														<!--<label>No binario</label>	-->	
							<br />    							
							
							<label>*Nombre</label> 
							<input type="text"
														name="datosFormMiembro[NOM]"
														value='<?php
																							if (isset($datosSocio['datosFormMiembro']['NOM']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['NOM']['valorCampo'],ENT_QUOTES);	
																							}
																					?>'
														size="35"
														maxlength="100"
														/>	 
							<span class="error">
																				<?php
																						if (isset($datosSocio['datosFormMiembro']['NOM']['errorMensaje'])) 
																						{
																										echo $datosSocio['datosFormMiembro']['NOM']['errorMensaje'];
																						}
																				?>
							</span>		
							<br />
							
							<label>*Apellido primero</label> <!-- obligatorio y se valida si existe -->
							<input type="text"
														name="datosFormMiembro[APE1]"
														value='<?php
																								if (isset($datosSocio['datosFormMiembro']['APE1']['valorCampo'])) 
																								{
																												echo htmlspecialchars($datosSocio['datosFormMiembro']['APE1']['valorCampo'],ENT_QUOTES);
																								}
																						?>'
														size="35"
														maxlength="100"
														/>	 
							<span class="error">
							<?php
								if (isset($datosSocio['datosFormMiembro']['APE1']['errorMensaje'])) 
								{
												echo $datosSocio['datosFormMiembro']['APE1']['errorMensaje'];
								}
							?>
							</span>	
							
							<label>Apellido segundo</label> <!-- no obligatorio pero se valida si existe -->
							<input type="text"
														name="datosFormMiembro[APE2]"
														value='<?php
																							if (isset($datosSocio['datosFormMiembro']['APE2']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['APE2']['valorCampo'],ENT_QUOTES);
																							}
																					?>'
														size="35"
														maxlength="100"
														/>	 
							<span class="error">
													<?php
														if (isset($datosSocio['datosFormMiembro']['APE2']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormMiembro']['APE2']['errorMensaje'];
														}
													?>
							</span>	
							<br />

							<label >*Año de nacimiento</label> 
							
								<input type="text"
															name="datosFormMiembro[FECHANAC][anio]"
															value='<?php
																							if (isset($datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] ) && $datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] !== '0000') 
																							{
																											echo $datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'];
																							}
																						?>'
															size="4"
															maxlength="4"
															/>	 
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['FECHANAC']['anio']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['FECHANAC']['anio']['errorMensaje'];
															}
														?>
								</span>								
								
							<br />
								
							<label>Profesión</label> <!-- no obligatorio pero se valida si existe -->
								<input type="text"
															name="datosFormMiembro[PROFESION]"
															value='<?php
																							if (isset($datosSocio['datosFormMiembro']['PROFESION']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['PROFESION']['valorCampo'],ENT_QUOTES);
																							}
																						?>'
															size="60"
															maxlength="255"
															/>	 
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['PROFESION']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['PROFESION']['errorMensaje'];
															}
														?>
								</span>


							<label>Estudios</label> 
								<?php
									
									$parValorEstudios = array(
									    "NO-ELIGE" => "Elegir opción",
													"NIVEL5" => "Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
													"NIVEL4" => "Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
													"NIVEL3" => "Formación Profesional de Grado Superior",
													"NIVEL2" => "Formación Profesional de Grado Medio",
													"NIVEL1" => "Garantía Social",
													"ESO" => "ESO, Enseñanza Media",
													"PRIMARIA" => "Enseñanza Primaria",
													"INFANTIL" => "Educación Infantil (0-6 años)",
													"SINESTUDIOS" => "Sin estudios");													

												if (!isset($datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']) || empty($datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']))
												{ $datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo'] = "NO-ELIGE";
												}		//para evitar notice al entrar (mejor enviarlo desde controlador al entrar y también parValorEstudios procedente de una tabla en BBDD) 	
			
												//echo "<br /><br />datosSocio['datosFormMiembro']['ESTUDIOS']: ";print_r($datosSocio['datosFormMiembro']['ESTUDIOS']);
														
									echo comboLista($parValorEstudios, "datosFormMiembro[ESTUDIOS]", $datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo'],
																        	$parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']], "", "");
								?>
     </p>			
					<br /><br />									
					<p>			
					 
							<label>Teléfono móvil </label> <!--no obligatorio pero se valida si existe-->
								<input type="text"
															name="datosFormMiembro[TELMOVIL]"
															value='<?php
																							if (isset($datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo'],ENT_QUOTES);
																							}
																						?>'
															size="14"
															maxlength="14"
															/>	 
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['TELMOVIL']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['TELMOVIL']['errorMensaje'];
															}
														?>
								</span>						
					
							<label>Teléfono fijo </label> <!--no obligatorio pero se valida si existe-->
								<input type="text"
															name="datosFormMiembro[TELFIJOCASA]"
															value='<?php
																								if (isset($datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'])) 
																								{
																												echo htmlspecialchars($datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'],ENT_QUOTES);
																								}
																						?>'
															size="14"
															maxlength="14"
															/>	 
								<span class="error">
												<?php
												if (isset($datosSocio['datosFormMiembro']['TELFIJOCASA']['errorMensaje'])) 
												{
																echo $datosSocio['datosFormMiembro']['TELFIJOCASA']['errorMensaje'];
												}
												?>
								</span>
	
							<br />					
					
							<label>*Correo electrónico</label>
								<input type="text"
															name="datosFormMiembro[EMAIL]"
															value='<?php
																							if (isset($datosSocio['datosFormMiembro']['EMAIL']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);
																							}
																						?>'
															size="60"
															maxlength="200"
															/>	 
								<span class="error">
															<?php
																if (isset($datosSocio['datosFormMiembro']['EMAIL']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormMiembro']['EMAIL']['errorMensaje'];
																}
															?>
								</span>	
							<br />											
							<label>*Repetir correo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
								<input type="text" id="probando"
															name="datosFormMiembro[REMAIL]"
															value='<?php
																							if (isset($datosSocio['datosFormMiembro']['REMAIL']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormMiembro']['REMAIL']['valorCampo'],ENT_QUOTES);
																							}
																					?>'
															size="60"
															maxlength="200"
															/>	
     <!-- Script Para impedir copia y pega el email -->
				 <div id="aviso"></div>			
					
					<script>
        let probando = document.getElementById("probando")
        let aviso = document.getElementById("aviso")
        
        probando.addEventListener("paste", (event) => {
          event.preventDefault()
          aviso.innerHTML = "<span class='error'>No se permite pegar el email, escríbelo manualmente </span>"
										//aviso.innerHTML = "No se permite pegar el email, escríbelo manualmente "										
        })
     </script>		
					<!-- Fin Script Para impedir copia y pega el email -->
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['REMAIL']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['REMAIL']['errorMensaje'];
															}
														?>
								</span>
							<br />	
							
		     <!--	Dejo esto por si más adelante se decide exigir este campo	INFORMACIONEMAIL			
							<label>Acepto recibir correos electrónicos de Europa Laica (ver ocultar y dejar marcado)</label>
							<input type="checkbox"
														name="datosFormMiembro[INFORMACIONEMAIL]"
														value="SI"
																				<?php
																					/*if ($datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] == 'SI') 
																					{
																									echo "checked='checked'";
																					}*/
																				?>
									/>	 
								<br /><br />
								-->
								
						<!--	Asignamos por defecto en alta INFORMACIONEMAIL	= 'SI' despues lo podrán cambiar en  "- Actualizar datos socio/a"	 -->
						
								<input type="hidden"	name="datosFormMiembro[INFORMACIONEMAIL]"
																					value='<?php echo $datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = 'SI'; ?>'
								/>					
					</p>
			</fieldset>
			<!-- ******************** Fin datos de personales *********************************************************** -->	
			<br />	
			<!-- ******************** Inicio datos Domicilio ************************************************************ --> 
			<fieldset>
					<legend><strong>Domicilio</strong></legend>	
					 <p>	
								<label>*País domicilio </label>
															<?php
															//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormDomicilio']['CODPAIS1']);			 
															//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
															echo comboLista($parValorComboPaisDomicilio['lista'], "datosFormDomicilio[CODPAISDOM]", $parValorComboPaisDomicilio['valorDefecto'], $parValorComboPaisDomicilio['descDefecto'], "", "")
															?> 
								<span class="error">
															<?php
																if (isset($datosSocio['datosFormDomicilio']['CODPAISDOM']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormDomicilio']['CODPAISDOM']['errorMensaje'];
																}
															?>
								</span>	
								<br />	
								
								<label>*Dirección: calle, plaza, nº, bloque, escalera, piso, puerta</label>
									<input type="text"			
															name="datosFormDomicilio[DIRECCION]"
															value='<?php
																								if (isset($datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo'])) 
																								{
																												echo htmlspecialchars($datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo'],ENT_QUOTES);
																								}
																							?>'
															size="70"
															maxlength="255"
									/>		
								<span class="error">
													<?php
													if (isset($datosSocio['datosFormDomicilio']['DIRECCION']['errorMensaje'])) 
													{
																	echo $datosSocio['datosFormDomicilio']['DIRECCION']['errorMensaje'];
													}
													?>
								</span>
								<br />
								<label>*Código postal</label>	
								<input type="text"			
															name="datosFormDomicilio[CP]"
															value='<?php
																							if (isset($datosSocio['datosFormDomicilio']['CP']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormDomicilio']['CP']['valorCampo'],ENT_QUOTES);
																							}
																						?>'
															size="6"
															maxlength="10"
															/>		
								<span class="error">
															<?php
																if (isset($datosSocio['datosFormDomicilio']['CP']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormDomicilio']['CP']['errorMensaje'];
																}
															?>
								</span>

								<label>*Localidad</label>	
								<input type="text"			
															name="datosFormDomicilio[LOCALIDAD]"
															value='<?php
																							if (isset($datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'],ENT_QUOTES);
																							}
																						?>'
															size="50"
															maxlength="255"
															/>		
								<span class="error">
														<?php
														if (isset($datosSocio['datosFormDomicilio']['LOCALIDAD']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormDomicilio']['LOCALIDAD']['errorMensaje'];
														}
														?>
								</span>		
					   <!--	<br />	-->	
        <!--	Dejo esto por si más adelante se decide exigir este campo	INFORMACIONCARTAS			
								
								<label>Acepto recibir cartas de Europa Laica ver </label>
								<input type="checkbox" 
															name="datosFormMiembro[INFORMACIONCARTAS]"
															value="SI"
																						<?php
																						/*if ($datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] == 'SI') 
																						{
																										echo " checked='checked'";
																						}*/
																						?>
															/>			
								<br />		
      -->  
						<!--	Asignamos porr defecto en alta INFORMACIONCARTAS	= 'SI' despues lo podrán cambiar en  "- Actualizar datos socio/a"	 -->
						<br />		
						<input type="hidden"	name="datosFormMiembro[INFORMACIONCARTAS]"
												 value='<?php echo $datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] = 'SI'; ?>'																				
						/>
							
					</p>
			</fieldset>

			<!-- ******************** Fin datos Domicilio *************************************************************** --> 
		 <br />	
			<!-- ******************** Inicio Datos de Cuotas  *********************************************************** -->
			<fieldset>
					<legend><b>*Cuota anual socio/a para año  <?php echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']; ?></b></legend>
		  	<p> 
								<span class="error"> <!-- acaso sobre porque ya está marcada por defecto:	-->
																<?php
																if (isset($datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje']; 
																?>
																<br /><br />
																<?php
																}
																?>
								</span>
   													
								<input type="radio"
															name="datosFormSocio[CODCUOTA]"
															value='General' 
															<?php
															if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo'] == 'General') 
															{
																			echo " checked";
															}
															?>
															/>
               <span class="comentario11"><strong>General&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo']).' €)'; ?></strong></span>			


        <br /><br />  		
        <span class="comentario11"><strong>Reducida:</strong></span>
        <br />		
								<input type="radio"
															name="datosFormSocio[CODCUOTA]"
															value='Joven'
														<?php
														if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo'] == 'Joven') 
														{
																		echo " checked";
														}
														?>						 
															/>			
														<label>Joven&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo']).' €)'; ?></label>	

								<input type="radio"
															name="datosFormSocio[CODCUOTA]"
															value='Parado' 
														<?php
														if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo'] == 'Parado') 
														{
																		echo " checked";
														}
														?>
															/>
							       <label>Parado/a o dificultades enconómicas&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo']).' €)'; ?></label>
								
								<br /><br /> 

        <span class="comentario11"><strong>Total anual (cuota mínima + donación opcional)</strong></span>												
										
								<input type="text"		        
															name="datosFormCuotaSocio[IMPORTECUOTAANIOSOCIO]"
															value="<?php
																							if (isset($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'])) 
																							{
																											echo htmlspecialchars($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'],ENT_QUOTES);					
																							}
																						?>"
															size="12"																														
															maxlength="30"
															/>			
								<span class="error">
															<?php
																if (isset($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje'];
																}
															?>
								</span>	
								<br/><br/>

								<span class="comentario11">No aceptamos subvenciones, nuestros ingresos proceden de las cuotas y donaciones de nuestras socias, socios y simpatizantes.</span>
									<br/>
												
												<!-- IMPORTECUOTAANIOELcccc solo se envía para validar ['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] -->
												<input type="hidden"	name="datosFormCuotaSocio[CODCUOTAGeneral]"
																			value='<?php echo $datosSocio['datosFormCuotaSocio']['CODCUOTAGeneral']['valorCampo']; ?>'
																			/>							
												<input type="hidden"	name="datosFormCuotaSocio[IMPORTECUOTAANIOELGeneral]"
																			value='<?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo']; ?>'
																			/>			
												<input type="hidden"	name="datosFormCuotaSocio[IMPORTECUOTAANIOELJoven]"
																			value='<?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo']; ?>'
																			/>			
												<input type="hidden"	name="datosFormCuotaSocio[IMPORTECUOTAANIOELParado]"
																			value='<?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo']; ?>'
																			/>																			

											<input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
																		value="<?php
																											if (isset($datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'])) 
																											{
																															echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];		
																											}
																									?>"
																		/>
					</p>
			</fieldset>
			<!-- ******************** Fin Datos de Cuotas  ************************************************************** -->								
   <br />
			<!-- ******************** Inicio datos bancarios Socio ****************************************************** -->	
			<fieldset>	  
					<legend><strong>Cuenta bancaria para el cobro de tu cuota anual</strong></legend>
					<p>
							<label>Tu cuenta <strong>IBAN</strong> (dos letras de país + número sin puntos ni guiones)</label>  
							<input type="text"
														name="datosFormSocio[CUENTAIBAN]"
														value='<?php
																					if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 
																					{
																									echo htmlspecialchars($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'],ENT_QUOTES);			
																					}
																				?>'
														size="50"
														maxlength="60"
														/> 			
							<span class="error">
														<?php
														if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormSocio']['CUENTAIBAN']['errorMensaje'];
														}
														?>
							</span>
							<br /><!--<br />			-->
							<span class="comentario11">		
								<!-- - La fecha de cobro te la comunicaremos con antelación por correo electrónico <br />								-->		
								- Otras formas de pago: transferencia o mediante PayPal (se indica más adelante)
							</span>

							<br />
					</p>
			</fieldset>
			<!-- ******************** Fin datos bancarios Socio ********************************************************* -->
			<br />					
			<!-- ******************** Inicio Datos de agrupación territorial SOCIO ************************************** -->		
			<fieldset>
					<legend><strong>Elegir agrupación territorial de Europa Laica</strong></legend>
					<p>					

						<label>*Agrupación</label>
													<?php
													//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
													
             /* para llevar la agrupación  Europa Laica Estatal e Internacional al final del array parValorComboAgrupaSocio */
												 unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
												 $parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional

													echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]", $parValorComboAgrupaSocio['valorDefecto'], $parValorComboAgrupaSocio['descDefecto'], "","");
													?>			
						 <span class="error">		
														<?php
														if (isset($datosSocio['datosFormSocio']['CODAGRUPACION']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormSocio']['CODAGRUPACION']['errorMensaje'];
														}
							?>
							</span>		
							<br />			
							
					</p>			 		
			</fieldset>
			<!-- ******************** Fin Datos de agrupación territorial SOCIO **************************************** -->
			<br />		

			<!-- ********************* Inicio COMENTARIOSOCIO *********************************************************** -->
			<fieldset>
				<legend><b>Comentarios</b></legend>
				 <p> 
					 <!--
					 <span class="comentario11">(Algo parecido a lo siguiente ....)<br />
						 Si tienes interés en colaborar con la asociación en alguno de los Grupos de Trabajo existentes 
							<strong>Feminismo, Formación y Estudios, Educación, UNI Laica,</strong> o en cualquier otra área, anótalo ahora.  
							También podrías en cualquier momento envíar un correo a <strong>info@europalaica.org</strong>
						</span>
						<br /><br />		
						-->
						
							<textarea  id='COMENTARIOSOCIO' onKeyPress="limitarTextoArea(500, 'COMENTARIOSOCIO');"	
																		class="textoAzul8Left" name="datosFormMiembro[COMENTARIOSOCIO]" rows="3" cols="80"><?php
							if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'])) {
							echo htmlspecialchars($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'],ENT_QUOTES);	
							}
							?></textarea> 			

				 <!-- no es necesario con insertAdjacentHTML<div id="avisoComentario"></div>			-->
					
		 				<span class="error">
													<?php
													if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['errorMensaje'])) 
													{
																	echo $datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['errorMensaje'];
													}
													?>
							</span>	
					</p>
			</fieldset>
			<!-- ********************* Fin COMENTARIOSOCIO ************************************************************* -->			
			<br />
			<!-- ********************* Inicio Datos de identificación USUARIO ******************************************* -->
			<fieldset>
					<legend><strong>Datos de identificación para entrar en la zona privada "Área de Soci@s"</strong></legend>
					<p>
						
							<label for="user">*Usuario (email, NIF, otro)</label> 
							<input type="text"
														id="user"
														name="datosFormUsuario[USUARIO]"
														value='<?php
														if (isset($datosSocio['datosFormUsuario']['USUARIO']['valorCampo'])) 
														{
																		echo 	htmlspecialchars($datosSocio['datosFormUsuario']['USUARIO']['valorCampo'],ENT_QUOTES);
														}
														?>'
														size="18"
														maxlength="30"
														/>
							<span class="error">		
														<?php
														if (isset($datosSocio['datosFormUsuario']['USUARIO']['errorMensaje'])) 
														{
																		echo $datosSocio['datosFormUsuario']['USUARIO']['errorMensaje'];
														}
							?>
							</span>	
		
							<br />
							<label>*Contraseña</label> <!--obligatorio y se valida-->	
							<input type="password"			
														name="datosFormUsuario[PASSUSUARIO]"
														value='<?php
																						if (isset($datosSocio['datosFormUsuario']['PASSUSUARIO']['valorCampo'])) 
																						{
																										echo htmlspecialchars($datosSocio['datosFormUsuario']['PASSUSUARIO']['valorCampo'],ENT_QUOTES);	
																						}
																				?>'
														size="16"
														maxlength="16"
														/>		
							<span class="error">
							<?php
							if (isset($datosSocio['datosFormUsuario']['PASSUSUARIO']['errorMensaje'])) 
							{
											echo $datosSocio['datosFormUsuario']['PASSUSUARIO']['errorMensaje'];
							}
							?>
							</span>

							<label>*Repetir contraseña</label> <!--obligatorio y se valida-->	 
							<input type="password"
														name="datosFormUsuario[RPASSUSUARIO]"
														value='<?php
																					if (isset($datosSocio['datosFormUsuario']['RPASSUSUARIO']['valorCampo'])) 
																					{
																									echo htmlspecialchars($datosSocio['datosFormUsuario']['RPASSUSUARIO']['valorCampo'],ENT_QUOTES);	
																					}
																					?>'
														size="16"
														maxlength="16"
														/> 			
							<span class="error">
													<?php
													if (isset($datosSocio['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'])) 
													{
																	echo $datosSocio['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'];
													}
													?>
							</span>
							<br />							
					</p>
			</fieldset>
   <!-- ********************* Inicio Datos de identificación USUARIO ******************************************* -->
		 <br /><br />
		 <!-- ********************* Inicio Protección de datos ****************************************************** -->			
		 <fieldset>
					<legend><b>*Protección de tus datos personales</b></legend>				
     <p>
						<!--<span class="textoAzu112Left2">
						Como socia/o, podrás entrar en el "Área de Soci@s" y modificar o eliminar tus datos personales,
						o enviar un email a <strong>secretaria@europalaica.org</strong> indicando el deseo de suprimir tus datos. 	
      </span>
      <br />-->								
		
						<span class="comentario11">
        <!--	Europa Laica tratará tus datos personales con el fin de tramitar tu alta y para gestionar el pago de tus cuotas o donaciones. 
								También para enviar a tu correo electrónico el Boletín del Observatorio del Laicismo e información sobre actividades relacionadas con los objetivos de Europa Laica. 							
								Tus datos <strong>no se cederán a terceros</strong> salvo para los fines específicos de Europa Laica y en el caso de obligaciones legales. 
        Sólo utilizamos las necesarias cookies de sesión temporales, al salir de la página web se eliminan.
								<br />				
        -->									
								Puedes ejercer tus derechos de acceso, rectificación, supresión, limitación, portabilidad y oposición. 	
								<a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" 
								target="_blank" title="Privacidad de datos" 
								onclick="ventanaSecundaria(this); return false">
															 <strong>>> Información Protección de Datos</strong>     
							</a>
      </span>							
						<br />									

      <span class="comentario11"><strong>*He leído y autorizo el uso de mis datos personales para los fines específicos de Europa Laica (marcar la casilla)</strong></span>						
							<input type="checkbox" 
														name="datosFormUsuario[privacidad]"
														value="SI"
														<?php
														if ($datosSocio['datosFormUsuario']['privacidad']['valorCampo'] == 'SI') 
														{
																		echo " checked='checked'";
														}
														?>
														/>
							<span class="error"><strong>
													<?php 
													if (isset($datosSocio['datosFormUsuario']['privacidad']['errorMensaje'])) 
													{
																	echo $datosSocio['datosFormUsuario']['privacidad']['errorMensaje'];
													}
													?></strong>
							</span>
					
							<br />
					</p>
			</fieldset>
		 <!-- ********************* Fin Protección de datos ********************************************************* -->					
		
		 <!-- ********************** Inicio Botones de formAltaSocio ************************************************ -->  		

   <p>
			 <span class="comentario11">
				Si necesitas ayuda: 	<strong>info@europalaica.org</strong>, &nbsp;&nbsp;&nbsp;<strong>	Teléfono</strong> <!-- o Whatsapp--> (España): <strong>670 55 60 12 </strong> 
			 </span>
	
   </p>			
		  <br /><br />	
			 <span class="error">
										<?php
										if (isset($datosSocio['codError']) && $datosSocio['codError'] !== '00000') 
										{  				 
														echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
										}			
										?>
			 </span>
	
			<!-- <div align="center">							-->	
  <br />
			<input type="submit" name="siGuardarDatosSocio" value="&nbsp; Continuar &nbsp;" class="enviar" />
			&nbsp;		&nbsp;		&nbsp; &nbsp;		&nbsp;		&nbsp;
			
			<input type="submit" name="noGuardarDatosSocio" 
										onClick="return confirm('¿Salir sin guardar los datos?')"
										value='&nbsp; Salir &nbsp;' />
			<!-- </div> -->
				
	 	<!-- ********************** Fin Botones de formAltaSocio *************************************************** -->  				
				<br /><br />
		
		</form> 

  <!-- ********************* Fin del formulario registro de datos del socio **************************************** -->
				
</div> 
<!-- *********************** Fin <div id="registro"> *************************************************************** -->
	




