<?php
/* ------------------------------------------------------------------------------------------------
FICHERO: formAltaSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para el registro de un nuevo socio/a. 
             Cando finalice el registro  le lleva a una pantalla donde le indica que ya está registrado,
													pero que falta aún su confirmación de hacerse socio. 
													Tambén le indica los modos de pago de la cuota.
													A la vez le llegará un email al socio para que acepte la confirmación de ser socio

LLAMADA: vistas/socios/vCuerpoAltaSocio.php y a su vez de controladorSocios.php:altaSocio()

OBSERVACIONES: 
2019-12-12 : Añado htmlspecialchars para mostrar bien los caracteres especiales ',",\,			
--------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<!-- Para campo textarea, además hay control interno en php -->
<script type="text/javascript">
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
</script>
<!-- ***************************************************** -->


<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->

		<span class="error">
									<?php
									if (isset($datosSocio['codError']) && $datosSocio['codError'] !== '00000') {
													echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
									}
									?>
		</span> 	
		<br /> 	<br /> 
		
		<span class="textoAzu112Left2">
						<ul>
          <li>Al asociarte ayudarás a defender el "laicismo" como principio democrático de convivencia en una sociedad que es plural</li>
										<li>No aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes</li>
										<li>Al hacerte socio/a tendrás derecho a participar en las asambleas de Europa Laica con voz y voto</li>
										<li>Tendrás acceso a los cursos de formación y otros grupos de trabajo de Europa Laica</li>
										<li>Recibirás información de las actividades y campañas por correo electrónico (si lo autorizas)</li>							
						</ul>		
						<br />
						Como socia/o, podrás entrar en el "Área de Soci@s" y modificar o eliminar tus datos personales,
						o enviar un email a <strong>secretaria@europalaica.org</strong> indicando el deseo de suprimir tus datos. 				
						<br /><br />		
						Para hacerte socio/a de pleno derecho en "Europa Laica" debes abonar tu cuota anual. Más adelante te indicaremos como puedes abonar tu cuota.
						<br /><br /><br />			
						<b>PARA REGISTRARTE COMO SOCIA/O DEBES RELLENAR EL SIGUIENTE FORMULARIO</b>
						<br /><br /> 
						Los campos con asterisco (<strong>*</strong>) son obligatorios	
		</span> 
		<br />
<?php //echo "<br><br>formAltaSocio.php:datosSocio: "; print_r($datosSocio); ?>
<!-- *************************** Inicio del formulario de datos del socio ********************* -->
		<form name="registrarSocio" method="post"
								action="./index.php?controlador=controladorSocios&amp;accion=altaSocio">

			<!-- ******************** Inicio datos de identificación MIEMBRO ************************* -->	
			<br /> 	
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
							<br />
							
							<label>*Nº documento</label> <!-- obligatorio y se valida para NIF y NIE pero no para pasaporte -->
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
							<br />
							
							<label>*País documento</label>
							<?php
								//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 
								//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
								//echo utf8_encode(comboLista($parValorComboPaisMiembro['lista'], "datosFormMiembro[CODPAIS1]",			 
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
							<br /><br />
								
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
																if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'H') 
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
																if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'M') 
																{
																				echo "checked";
																}
															?>						 
														/>
														<label>Mujer</label>		
							<br />    
							
							<label>*Nombre</label> <!-- obligatorio y se valida si existe -->
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
							<br />	
							
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
							
							<label>Fecha de nacimiento</label> <!-- no obligatorio pero se valida si existe -->		
							<?php
								//lo referente a fecha podría ser un requiere_once parValorFechas
								$parValorDia["00"] = "día";
								for ($d = 1; $d <= 31; $d++) 
								{
												if ($d < 10) 
												{
																$valor = "0" . "$d";
												}//para que los días tengan el formato 01, 02,...10,...31
												else 
												{
																$valor = "$d";
												}
												$parValorDia[$valor] = $valor;
								}
								//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
								echo comboLista($parValorDia, "datosFormMiembro[FECHANAC][dia]", $datosSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo'], $parValorDia[$datosSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo']], "00", "día");

								$parValorMes = array("00" => "mes", "01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio",
												"07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

								echo comboLista($parValorMes, "datosFormMiembro[FECHANAC][mes]", $datosSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo'], $parValorMes[$datosSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo']], "00", "mes");

								$parValorAnio["0000"] = "año";
								for ($a = date("Y") - 100; $a <= date("Y") - 15; $a++) 
								{
												$parValorAnio[$a] = $a;
								}
								echo comboLista($parValorAnio, "datosFormMiembro[FECHANAC][anio]", $datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'], $parValorAnio[$datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo']], "0000", "año");
								//$parValorAnio[$datosSocio['datosFormMiembro']['fechanac']['anio']['valorCampo']],"","");//Problemas 	
								?>	
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['FECHANAC']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['FECHANAC']['errorMensaje'];
															}
														?>
								</span>	
								<br /><br />    

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
							<label>*Repetir correo electrónico</label>
								<input type="text"
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
								<span class="error">
														<?php
															if (isset($datosSocio['datosFormMiembro']['REMAIL']['errorMensaje'])) 
															{
																			echo $datosSocio['datosFormMiembro']['REMAIL']['errorMensaje'];
															}
														?>
								</span>
							<br />	
							<label>Acepto recibir correos electrónicos de Europa Laica</label>
								<input type="checkbox"
															name="datosFormMiembro[INFORMACIONEMAIL]"
															value="SI"
																					<?php
																						if ($datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] == 'SI') 
																						{
																										echo "checked='checked'";
																						}
																					?>
									/>	 
								<br /><br />
								
							<label>Teléfono fijo (solo números sin espacios ni puntos)</label> <!--no obligatorio pero se valida si existe-->
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
							<label>Teléfono móvil (solo números sin espacios ni puntos)</label> <!--no obligatorio pero se valida si existe-->
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
							<br /><br />	
							
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
							<br />

							<label>Estudios</label> 
								<?php
									$parValorEstudios = array("" => "Elegir opción",
													"NIVEL5" => "Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
													"NIVEL4" => "Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
													"NIVEL3" => "Formación Profesional de Grado Superior",
													"NIVEL2" => "Formación Profesional de Grado Medio",
													"NIVEL1" => "Garantía Social",
													"ESO" => "ESO, Enseñanza Media",
													"PRIMARIA" => "Enseñanza Primaria",
													"INFANTIL" => "Educación Infantil (0-6 años)",
													"SINESTUDIOS" => "Sin estudios");
									echo comboLista($parValorEstudios, "datosFormMiembro[ESTUDIOS]", $datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo'],
																	//$parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']],"--","Elige opción");	
																	$parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']], "", "");
								?>

							<br />			
							<label>Puedo colaborar en </label>		
								<?php
									$parValorColabora = array("" => "Elegir opción", "secretaria" => "Tareas de secretaría", "prensa" => "Contactos con la prensa",
													"actividades" => "Organización de actividades", "formacion" => "Formación en laicismo", "web" => "Mantenimiento del sitio web",
													"manifestaciones" => "Participación en manifestaciones y concentraciones", "otros" => "Otras actividades",
													"tiempo" => "No dispongo de tiempo");
									echo comboLista($parValorColabora, "datosFormMiembro[COLABORA]", $datosSocio['datosFormMiembro']['COLABORA']['valorCampo'], $parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']], "", "");
								?>
								<br />			
								
					</p>
			</fieldset>
			<br />	
			<!-- ********************** Fin datos de identificación MIEMBRO *************** --> 	
			
			<!-- *********************** Inicio datosFormDomicilio  *********************** --> 
			<fieldset>
					<legend><strong>Domicilio del socio/a</strong></legend>	
					 <p>	
								<label>*País domicilio </label>
															<?php
															//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormDomicilio']['CODPAIS1']);			 
															//echo '<br>dentro form:parValorComboPaisDomicilio:';print_r($parValorComboPaisDomicilio);
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
								<br /><br /> 	
								<label>*Dirección: calle, plaza, dirección, nº, bloque, escalera, piso, puerta</label>
								<br />
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
								<br /><br />				
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
								<br />		
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
								<br />		

								<label>Acepto recibir cartas de Europa Laica</label>
								<input type="checkbox" 
															name="datosFormMiembro[INFORMACIONCARTAS]"
															value="SI"
																						<?php
																						if ($datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] == 'SI') 
																						{
																										echo " checked='checked'";
																						}
																						?>
															/>			
								<br />			
																						
					</p>
			</fieldset>
			<br />	
			<!-- ********************** Fin datosFormDomicilio **************************** --> 	

			<!-- ****************** Inicio Datos de SOCIO ************************************ -->
			
			<!-- ****************** Inicio Datos de Cuotas  ***************************** -->
			<fieldset>
					<legend><b>Datos de la cuota del socio/a</b></legend>
					 <p>
								<span class="comentario11"> 
												Europa Laica no recibe ninguna subvención pública. Nuestros ingresos proceden de las cuotas de las socias/os y donaciones particulares.
												<br /><br /> 
												El trabajo que realizamos algunos socios/as para la asociación,
												es totalmente voluntario y nadie cobra por ello. 
												Pero sí tenemos que pagar otros servicios: alojamientos de la web y la base de datos de socias/os, 
												imprentas para carteles, etc.	

												<br /><br />
								</span>	
								
								<label>*Elige el tipo de cuota anual para el año	<?php echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']; ?></label>	
								<span class="error">
																<?php
																if (isset($datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje'])) 
																{
																				echo $datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje'];
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
															<label><strong>General&nbsp;(mínimo <?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo'] . ')'; ?></strong></label>

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
															<label>Joven&nbsp;(mínimo <?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo'] . ')'; ?></label>	

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
								<label>Parado/a&nbsp;(mínimo <?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo'] . ')'; ?></label>					
								<br />
								<span class="comentario11"> 
												Si eres un/a joven (18 a 25 años) sin ingresos, o estás en la situación de parado/a sin ingresos o en graves
												dificultades económicas, puedes elegir una cuota mas reducida hasta que cambie tu situación. 

												<br /><br />  			
												Anota una cantidad, igual o superior a la indicada a la indicada en el tipo de cuota correspondiente, 
												(puedes anotar una cantidad superior en concepto de cuota + donación)
								</span>	
								<br />
				
								<label><strong>*Cuota total anual (euros)</strong></label>
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
																		value='<?php
																											if (isset($datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'])) 
																											{
																															echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];		
																											}
																									?>'
																		/>
					</p>
			</fieldset>
			<br />
			<!-- *********************** Fin Datos de Cuotas  ***************************** -->								

			<!-- ************************Inicio datos bancarios Socio ********************** -->	
			<fieldset>	  
							<legend><strong>Datos bancarios de domiciliación pago cuotas</strong></legend>
							<p>
											<span class="comentario11">
															- Si quieres puedes domiciliar el pago de tu cuota en tu cuenta bancaria, para que Europa Laica 
															cobre tu cuota en años sucesivos. La fecha de cobro de los recibos te lo comunicaremos con antelación por correo electrónico. 
											</span>	
											<br /><br />		

											<label>Cuenta <strong>IBAN</strong> (dos letras de país + número sin espacios)</label>  
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
											<span class="comentario11">La cuenta en formato IBAN es obligatorio para "domiciliaciones y transferencias", 
															 en los 28 estados de la Unión Europea (más Islandia, Liechtenstein, Noruega, Mónaco, San Marino  y Suiza). 
															Si no la conoces, pregúntalo en tu banco.
											</span>			
											<br /><br />	
											
											<!-- Antiguo, lo dejo por si más adelente se quisiera activar de nuevo ******************	
											<span class="comentario11">Si tu cuenta bancaria no está en formato IBAN (paises fuera de la zona SEPA), escríbela en la siguiente línea
											</span>											
											<label>Número de cuenta <strong>NO IBAN</strong></label>  
											<input type="text"
																		name="datosFormSocio[CUENTANOIBAN]"
																		value='<?php
																									/*if (isset($datosSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'])) 
																									{	echo $datosSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'];
																									}
																									*/?>'
																		size="40"
																		maxlength="60"
																		/> 			
											<span class="error">
												<?php
												/* if (isset($datosSocio['datosFormSocio']['CUENTANOIBAN']['errorMensaje']))
														{echo $datosSocio['datosFormSocio']['CUENTANOIBAN']['errorMensaje'];}
													*/
												?>
								   </span>
										** Antiguo, lo dejo por si más adelente se quisiera activar de nuevo *****************	-->
										
											<span class="comentario11">
												- También puedes pagar tu cuota por transferencia, ingreso o mediante PayPal en las cuentas de de Europa Laica que más adelante se 
												te indicarán. 
												<br /><br />
												- Si tu cuenta bancaria no pertenece a un banco con sucursales en España
											 es más fácil pagar mediante PayPal (con tarjeta de crédito o con una cuenta de PayPal). A continuación en este proceso de alta como socia/o te daremos la información para hacerlo.
											</span>
											<br /><br />
											<span class="comentario11">
															Nota: El pago de tu primera cuota, para que seas socia/o con derecho de voto en las asambleas, 
															(si no la domicilias ahora al darte de alta) lo tendrás que hacer por alguno de los otros procedimientos que antes te hemos indicado.	
											</span>
											<br />
							</p>
			</fieldset>
				<!-- ************************Fin datos bancarios Socio *********************************** -->
				
			<!-- ********************** Fin Datos de SOCIO ************************************ -->		

			<!-- ****************** Inicio Datos de agrupación territorial SOCIO ************ -->
			<br />						
			<fieldset>
					<legend><strong>Elegir agrupación territorial de Europa Laica</strong></legend>
					<p>					
					<span class="comentario11">Como socio/a de Europa Laica, debes inscribirte en una de las agrupaciones existentes en la siguiente lista
					</span>	
     <br />
			
						<label>*Agrupación territorial</label>
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
			<br />		
			<!-- ****************** Fin Datos de agrupación territorial SOCIO ************ -->

			<!-- ****************** Inicio Datos de identificación USUARIO *************** -->
			<fieldset>
					<legend><b>Datos de identificación para entrar en la gestión de socios/as de Europa Laica</b></legend>
					<span class="comentario11">		
									Para entrar en la aplicación, necesitas elegir un usuario/a 
									y una contraseña. Anótalos de forma segura, los necesitarás para entrar en la aplicación informática 
									(podrás cambiarlos más adelante) 
					</span>
					<br />
					
					<p>
							<br />
							<label for="user">*Usuario/a</label> 
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
							<br />
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
			<br />
			
		 <fieldset>
					<legend><b>*Protección de tus datos personales</b></legend>
					
     <p>				
					<span class="comentario11">
							La asociación <strong>Europa Laica</strong> tratará tus datos personales con el <strong>fin</strong> de tramitar tu alta como socia/o, 
							enviar a tu correo electrónico el Boletín diario del Observatorio del Laicismo, 
							información sobre actividades, campañas e iniciativas relacionadas con los objetivos de Europa Laica, 
							y gestionar el pago de tus cuotas o donaciones. 
							<br />
							Tus datos <strong>no se cederán a terceros</strong> salvo para los fines específicos de Europa Laica y en el caso de obligaciones legales. 
							<br />
							Puedes ejercer tus <strong>derechos</strong> de acceso, rectificación, supresión, limitación, portabilidad y oposición. 
					<br />	<br />
							<!-- si no esta activado javascript, salen pantalla entera -->	
	     <a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" 
				   target="_blank" title="Privacidad de datos" 
							onclick="ventanaSecundaria(this); return false">
							>>Consultar información adicional sobre la protección de datos       
						</a>	
					</span>	
     <br />							
							<!--<label><strong>*Autorizo la cesión de mis datos únicamente para los fines específicos de Europa Laica</strong></label>-->
							<label><strong>*He leído, entiendo la política de privacidad y autorizo el uso de mis datos personales para los fines específicos de Europa Laica (marcar la casilla)</strong></label>							
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
			<br />	 
			<!--********************** Fin Datos de identificación USUARIO ***************-->  
	<!--htmlspecialchars($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'],ENT_QUOTES);		-->
			<!--************ Inicio Datos de datosFormMiembro[comentarioSocio] ***********-->
			<fieldset>
							<legend><b>Comentarios</b></legend>
							<p>
											<textarea  id='COMENTARIOSOCIO' onKeyPress="limitarTextoArea(250, 'COMENTARIOSOCIO');"	
																						class="textoAzul8Left" name="datosFormMiembro[COMENTARIOSOCIO]" rows="3" cols="80"><?php
if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'])) {
echo htmlspecialchars($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'],ENT_QUOTES);	
}
?></textarea> 	
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
  <!--************ Fin Datos de datosFormMiembro[Comentarios] ***********-->			

		<!--********************** Fin Datos de datosFormMiembro ***************-->
	   <br />					
		<!-- **************** Inicio Botones de formAltaSocio ******************* -->  			
	
					<span class="error">
												<?php
												if (isset($datosSocio['codError']) && $datosSocio['codError'] !== '00000') 
												{
																echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
												}
												?>
					</span>
    <br />					
				<!-- <div align="center">							-->	
								<input type="submit" name="siGuardarDatosSocio" value="Guardar datos del socio/a" class="enviar" />
								&nbsp;		&nbsp;		&nbsp;
								
								<input type="submit" name="noGuardarDatosSocio" 
															onClick="return confirm('¿Salir sin guardar los campos del formulario?')"
															value='No guardar los datos' />
				<!-- </div> -->
				<br />
	 <!-- ************************* Fin Botones de formAltaSocio ************** -->		
		
		</form> 

</div> <!-- ********************* Fin <div id="registro">**************** -->

<!-- ********** Fin del formulario formAltaSocio *************************** -->		




