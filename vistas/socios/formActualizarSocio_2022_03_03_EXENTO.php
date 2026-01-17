<?php
/*---------------------------------------------------------------------------------------------
FICHERO: formActualizarSocio.php
VERSION: PHP 7.3.21

Es el formulario para la actualización de datos de un socio, por el mismo socio 
(incluye a los propios gestores como socios).

LLAMADA: desde "vCuerpoActualizarSocio.php"  que a su vez procede de 
controladorSocios.php:ActualizarSocio()	

RECIBE: $datSocio: array con los valores previos del socio o de datos como cuotas 
        de EL, etc.  Proceden de BBDD o los nuevos introducidos desde el formulario
								hasta que se graben en la BBDD o se descarten.
        $parValorComboAgrupaSocio, $parValorComboPaisMiembro, $parValorComboPaisDomicilio procedentes de 
							 $parValorComboActualizarSocio, array que contiene los valores previos de 
        'agrupaSocio','miembroPais','domicilioPais'										
													
OBSERVACIONES: 
2019-12-12: Añado htmlspecialchars para mostrar bien caracteres especiales ',",\,	
en caso de error al validar.

NOTA: Convendría mejorar y simplificar los campos tipo <input type="hidden" ...>	
----------------------------------------------------------------------------------------------*/
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

<div id="registro">

	<span class="error">
	<?php		
		if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
		{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
		}			
	?>	
	</span>	
		<br />			
		
	<span class="textoAzu112Left2">			
		En este formulario puedes actualizar los datos personales registrados en 
		la base de datos de Europa Laica. 
		<br /> <br />
		Si quieres eliminar tus datos debes elegir la opción "- Dar de baja socio/a" 
		del menú lateral izquierdo, o enviar un correo electrónico a "secretaria@europalaica.org" solicitando la baja.
		<br /><br /><br />
		Los campos con asterisco (<strong>*</strong>) son obligatorios.	 			
	</span>
	<br />

	<!-- ******************** Inicio formulario para actualizar datos del socio ****************************** -->
		
	<form name="actualizarSocio" method="post" class="linea"
								action="./index.php?controlador=controladorSocios&amp;accion=actualizarSocio">
					
		<!-- *****************************Inicio Campos hidden *********************************** -->

			<!-- Inicio "campoHide"(incluye anteriorUSUARIO,anteriorEMAIL,anteriorCODPAISDOC,
							anteriorTIPODOCUMENTOMIEMBRO,anteriorNUMDOCUMENTOMIEMBRO -->
			<input type="hidden"	name="campoHide" value="<?php echo $datSocio['campoHide']; ?>"
					/>		
			<!-- Fin "campoHide" *************************************** -->
				
			<!-- Inicio se pasa sin mostrar EMAILERROR solo el presidente, coordinador,... validan:DEVUELTO,ERROR-FORMATO,NO,-->
			<input type="hidden"	name="campoActualizar[datosFormMiembro][EMAILERROR]"
										value='<?php echo $datSocio['campoActualizar']['datosFormMiembro']['EMAILERROR']['valorCampo']; ?>'
				/>	
			<!-- Inicio COMENTARIOSOCIO para que se pase el valor que tuviese previamente pero sin mostrarlo -->
			<input type="hidden"	name="campoActualizar[datosFormMiembro][COMENTARIOSOCIO]"
										value='<?php echo $datSocio['campoActualizar']['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']; ?>'
				/>	
				
			<!-- Inicio hidden: "datosCuotasEL" para validar  -->	
			<input type="hidden"	name="campoActualizar[datosCuotasEL][CODCUOTAGeneral]"
										value='<?php echo $datSocio['campoActualizar']['datosCuotasEL']['CODCUOTAGeneral']; ?>'
				/>					
			<input type="hidden"	name="campoActualizar[datosCuotasEL][IMPORTECUOTAANIOELGeneral]"
										value='<?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELGeneral']; ?>'
				/>		
			<input type="hidden"	name="campoActualizar[datosCuotasEL][IMPORTECUOTAANIOELJoven]"
										value='<?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELJoven']; ?>'
				/>	
			<input type="hidden"	name="campoActualizar[datosCuotasEL][IMPORTECUOTAANIOELParado]"
										value='<?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELParado']; ?>'
				/>		
			<input type="hidden"	name="campoActualizar[datosCuotasEL][IMPORTECUOTAANIOELHonorario]"
										value='<?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELHonorario']; ?>'
				/>
			<!-- Fin hidden: "datosCuotasEL" para validar  -->						
				

			<!-- Inicio hidden: "campoVerAnioActual" también se usa para ver en pantalla -->	
			<input type="hidden"	name="campoVerAnioActual[ANIOCUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['ANIOCUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['ANIOCUOTA'];}?>'
				/>						
				<input type="hidden"	name="campoVerAnioActual[CODCUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['CODCUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['CODCUOTA'];}?>'
				/>					
			<input type="hidden"	name="campoVerAnioActual[ESTADOCUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['ESTADOCUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['ESTADOCUOTA'];}?>'
				/>	
			<input type="hidden"	name="campoVerAnioActual[NOMBRECUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['NOMBRECUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['NOMBRECUOTA'];}?>'
				/>
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOPAGADA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA']))
																							{echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA'];}?>'
				/>					
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOSOCIO]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO']))
																							{echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO'];}?>'
				/>		
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOEL]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOEL']))
																							{  echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOEL'];}?>'
				/>	
				<!-- Fin hidden: "campoVerAnioActual" también se usa para ver en pantalla -->	
				
				<!-- Inicio hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla -->			
				<input type="hidden"	id="codSocio" name="campoActualizar[datosFormSocio][CODSOCIO]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo'];} ?>'
				/>								
				<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][CODCUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'];}?>'
				/>	
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][ANIOCUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];}?>'
				/>						
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][ESTADOCUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'];}?>'
																						
				/>
			
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][MODOINGRESO]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['MODOINGRESO']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['MODOINGRESO']['valorCampo'];}?>' 
				/>
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][IMPORTECUOTAANIOEL]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'];}?>'
				/>				
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][NOMBRECUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo'];}?>'
				/>				
			<!-- Fin hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla -->					
					
		<!-- ***********************************Fin Campos hidden *********************************** -->
			<br />
			
		<!-- ****************** Inicio Datos de identificación USUARIO ****************************************** --> 	 

		<fieldset>	 
			<legend><strong>Nombre de usuario/a para entrar en el "Área de soci@s"</strong></legend>	
			<p>
				<label>*Usuario/a</label> 
					<input type="text" 						    
												name="campoActualizar[datosFormUsuario][USUARIO]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['valorCampo'],ENT_QUOTES);}
												?>'
												size="35"
												maxlength="100"
					/>	
				<span class="error">		
					<?php
							if (isset($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['errorMensaje']))
							{echo $datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['errorMensaje'];}
					?>					
				</span>	
				
				<br />
				<span class="comentario11">Si quieres puedes cambiar el nombre usuario/a por otro más fácil de recordar 
				</span>
				<br />						
				
			</p>
		</fieldset>
		<!-- ****************** Fin Datos de identificación USUARIO ********************************************* -->	
		<br />

		<!-- INICIO Datos cuota: Honorario y No Honorario (en NO Honorario: actualiza importeCuotaSocio,cuenta banco) -->

		<fieldset>	  
			<legend><strong>Datos de la cuota</strong></legend>
			<p>
				<!-- ********************* Inicio actualizar importeCuotaSocio *********************************** -->	
				<?php 

				if (isset($datSocio['campoVerAnioActual']['ANIOCUOTA']))
				{ 
						echo "<label>Cuota ".//$error['datosFormCuotaSocioVer']['NOMBRECUOTA']['valorCampo'].
											" pagada por el socio/a en <strong>".$datSocio['campoVerAnioActual']['ANIOCUOTA']."</strong></label>"; 
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA']." euros</span>"; 
						

						echo "<label>&nbsp;&nbsp;&nbsp;Estado cuota</label>";
												
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['ESTADOCUOTA']." </span>"; 
						
						if  ($datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA'  || $datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-ERROR-CUENTA')
						{echo "<span class='mostrar'>Devolución recibo de pago. Contactar con el tesorero de Europa Laica</span>";
						}

						echo "<br /><label>Cuota elegida por el socio/a para el año <strong>".$datSocio['campoVerAnioActual']['ANIOCUOTA'].
											"</strong> </label>".
											"<span class='mostrar'>".$datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO']." euros </span>";
						echo "<label> cuota tipo </label>";
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['CODCUOTA']."</span>";								
				}	
				
				/*--- Inicio para solo SÍ Honorario -----------------------------------------------------------*/
				
				if ( isset($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']) &&
				     $datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'EXENTO' //Honorario
							)
				{ echo "<br /><br /><label>NOTA: En el año <strong>".$datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'].
											"</strong> como socio/a <strong>- ".$datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'].
											"</strong> - estás <strong>EXENTO</strong> de abonar las cuotas </label>";				
										
					?>				
					<input type="hidden"	name="campoActualizar[datosFormSocio][CODCUOTA]"
												value="<?php /*if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] ))
																									{ echo $datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'];}*/
																								if (isset($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'] ))
																									{ echo $datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'];}
																		?>"	/>	
							<br />	
							
			</p>
		</fieldset>	<!-- Fin para solo SÍ Honorario, se cierra el  </p> y 	</fieldset>	---------------- -->
				
					<?php 	
				}
				//*--- Fin "if" para solo SÍ Honorario, (se cierra el  </p> y 	</fieldset>)	--------------------*/
				
				/*--- Inicio "else" para NO Honorario (actualizar importeCuotaSocio, cuenta bancaria) ----------*/ 
				else //		if ($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']!== 'EXENTO')	
				{
					?>
					<br /><br />
					<!-- ************************ Inicio actualizar importeCuotaSocio (No Honorario) ************ -->	
					
					<span class="comentario11">Ahora puedes modificar la cantidad elegida,  
																														(puedes anotar una cantidad superior en concepto de cuota + donación). 
																															<br />Si en el presente año ya estuviese pagada tu cuota, 
																															se anotará como nueva cuota elegida para el próximo año.
					</span>
					<br /><br />	
					
					<span class="comentario11">
					<strong>Elegir nuevos valores para el año 
					<?php //echo $datSocio['campoVerAnioActual']['ANIOCUOTA']+1 
						echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']?>
					</strong>(también se pueden dejar sin modificar).
					</span>				
					<br />	
					
					<label>*Tipo cuota</label>	
							<span class="error">
							<?php
							if (isset($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['errorMensaje']))
							{echo $datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['errorMensaje'];}
							?>
						</span>
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='General' 
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='General')
													{  echo " checked";}
													?>
						/>
					<label><strong>General&nbsp;(mínimo <?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELGeneral'].')'; ?></strong></label>
						
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Joven'
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Joven')
													{  echo " checked";}
													?>						 
						/>
					<label>Joven&nbsp;(mínimo <?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELJoven'].')'; ?></label>	
						
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Parado' 
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Parado')
													{  echo " checked";}
													?>
						/>
					<label>Parado&nbsp;(mínimo <?php echo $datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELParado'].')'; ?></label>							
						<br />
					
						<span class="comentario11"> 
							Si eres un/a joven (18 a 25 años) sin ingresos, o estás en la situación de parado/a sin ingresos o en graves
							dificultades económicas, puedes elegir la correspondiente cuota reducida hasta que cambie tu situación. 
						</span>				
						<br /><br />		
				
					<label>Modificar la cuota elegida para el año
					<?php echo " <strong>".$datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']."</strong>";	?>
					</label>
					
					<input type="text"						       		        		        
												name="campoActualizar[datosFormCuotaSocio][IMPORTECUOTAANIOSOCIO]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo']))
																								{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'],ENT_QUOTES);}
																		?>'
												size="12"
												maxlength="30"
					/><label> euros</label>				
					<span class="error">
					<?php
						if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje'];}
							?>
					</span>	
					<br />		
					
			</p>
		</fieldset> 	
		<!-- ************************ Fin actualizar importeCuotaSocio (No Honorario) **************** -->	
				<br />
		
		<!-- ********************** Inicio cuenta bancaria (No Honorario) **************************** -->				
		<fieldset>	  
			<legend><strong>Datos bancarios de domiciliación de pago cuota</strong></legend>
			<p>

				<span class="comentario11">
				- Si quieres, ahora puedes domiciliar el pago de tu cuota anual, 	(o modificar la cuenta bancaria si ya estuviese domiciliada) 
					para que Europa Laica cobre tu cuota en años sucesivos.	La fecha de cobro de los recibos te lo comunicaremos con antelación por correo electrónico
				</span>
				<br /><br />
		
				<label>Cuenta <strong>IBAN</strong> (dos letras de país + número sin espacios)</label> 
					<input type="text"
												name="campoActualizar[datosFormSocio][CUENTAIBAN]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo']))
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo'],ENT_QUOTES);}
																			?>'
												size="50"
												maxlength="50"
					/> 			
				
				<span class="error">
				<?php
						if (isset($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['errorMensaje']))
							{echo $datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['errorMensaje'];}
				?>
				</span>
				<br />
				<span class="comentario11">La cuenta en formato IBAN es necesaria para domiciliaciones en los estados de la Unión Europea 
				</span>			
				<br /><br />
				<!-- Antiguo, lo dejo por si más adelente se quisiera activar de nuevo ******************	
				<span class="comentario11">Si tu cuenta bancaria no está en formato IBAN (paises fuera de la zona SEPA), escríbela en la siguiente línea</span>
				<br />		
				<label>Número de cuenta <strong>NO IBAN</strong></label> 
						<input type="text"
													name="campoActualizar[datosFormSocio][CUENTANOIBAN]"
													value='<?php /*if (isset($datSocio['campoActualizar']['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
																										{  echo $datSocio['campoActualizar']['datosFormSocio']['CUENTANOIBAN']['valorCampo'];}*/
																				?>'
													size="50" maxlength="100"
						/> 			
				<span class="error">
					<?php				
						/* if (isset($datSocio['campoActualizar']['datosFormSocio']['CCEXTRANJERA']['errorMensaje']))
								{echo $datSocio['campoActualizar']['datosFormSocio']['CCEXTRANJERA']['errorMensaje'];}
					*/	
					?>
				</span>
					** Antiguo, lo dejo por si más adelente se quisiera activar de nuevo *****************	-->	

				<span class="comentario11">
					- También puedes pagar tu cuota por transferencia, ingreso o mediante PayPal en las cuentas de de Europa Laica en la opción del menú:
					<!--<a href="https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a> -->
					<a href="../../usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a> 
					<br /><br />		
					- Si tu cuenta bancaria no pertenece a un banco con sucursales en España es más fácil pagar mediante PayPal (con tarjeta de crédito o con una cuenta de PayPal). 
					Puedes hacerlo desde la opción del menú:
				<!--	<a href="https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a> -->
					<a href="../../usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a> 
				</span>
				<br />
				
			</p>
		</fieldset>
		<!-- ********************** Fin cuenta bancaria (No Honorario) ******************************* -->		
			<br />		
				
			<?php
		}	//else if ($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] !== 'EXENTO')
		/*--- FIN "else" para NO honorario (actualizar importeCuotaSocio, cuenta bancaria) ----------------*/ 
			?>

		<!-- FIN Datos cuota: Honorario y No Honorario (en NO Honorario: actualiza importeCuotaSocio,cuenta banco) -->
		
			<br />
		<!-- ********************* Inicio Agrupación territorial *********************************** -->		 
		<fieldset>	 
			<legend><b>Elegir agrupación territorial</b></legend>	
			<p>
				<span class="comentario11">Como socia/o de Europa Laica, debes inscribirte en una de las agrupaciones existentes
				</span>
				<br />			
				<label>*Agrupación territorial</label>
					<?php
					
						unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
						$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional

						echo comboLista($parValorComboAgrupaSocio['lista'], "campoActualizar[datosFormSocio][CODAGRUPACION]",
																																		$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['descDefecto'],"","");		 
					?> 	
					<br />
			</p>
		</fieldset>
		<!-- ********************** Fin Agrupación territorial ************************************* -->	
			<br />
		<!-- ********************** Inicio datos personales **************************************** -->	
		
		<fieldset>	 
			<legend><b>Datos personales</b></legend>	
			<p>
				<label>*Documento</label>		
					<?php	  	
					$parValorTipoDoc=array("NIF"=>"NIF","NIE"=>"NIE","Pasaporte"=>"Pasaporte","Otros"=>"Otros");										 
					echo comboLista($parValorTipoDoc,"campoActualizar[datosFormMiembro][TIPODOCUMENTOMIEMBRO]",
																					$datSocio['campoActualizar']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
													$parValorTipoDoc[$datSocio['campoActualizar']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']],"NIF","NIF");	
					?>

																			
					<label>*Nº documento</label> 
						<input type="text"
													name="campoActualizar[datosFormMiembro][NUMDOCUMENTOMIEMBRO]"
													value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
													{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'],ENT_QUOTES);}
													?>'
													size="12"
													maxlength="20"
						/>	 
						<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];}
						?>
						</span>																				
						<br />
						
				<label>*País documento</label>
						<?php
								echo comboLista($parValorComboPaisMiembro['lista'], "campoActualizar[datosFormMiembro][CODPAISDOC]",
																						$parValorComboPaisMiembro['valorDefecto'],$parValorComboPaisMiembro['descDefecto'],"","");	
							?> 
						<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['CODPAISDOC']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['CODPAISDOC']['errorMensaje'];}
						?>
						</span>															
						<br /><br />	
						
				<label>*Sexo</label>	
						<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['SEXO']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['SEXO']['errorMensaje'];}
						?>
						</span>	
					
					<input type="radio"
												name="campoActualizar[datosFormMiembro][SEXO]"
												value='H' 
							<?php if ($datSocio['campoActualizar']['datosFormMiembro']['SEXO']['valorCampo']=='H')
												{  echo " checked";}
												?>
					/><label>Hombre</label>
					<input type="radio"
												name="campoActualizar[datosFormMiembro][SEXO]"
												value='M'
							<?php if ($datSocio['campoActualizar']['datosFormMiembro']['SEXO']['valorCampo']=='M')
												{  echo " checked";}
												?>						 
					/><label>Mujer</label>		
						<br />   
						
				<label>*Nombre</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][NOM]"
												value="<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['NOM']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['NOM']['valorCampo'],ENT_QUOTES);}
												
												?>"
												size="35"
												maxlength="100"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['NOM']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['NOM']['errorMensaje'];}
					?>
					</span>		
					<br />
					
				<label>*Apellido primero</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][APE1]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE1']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['APE1']['valorCampo'],ENT_QUOTES);}
												?>'
												size="35"
												maxlength="100"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE1']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['APE1']['errorMensaje'];}
					?>
					</span>	
					<br />	
					
				<label>Apellido segundo</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][APE2]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE2']['valorCampo']))
																		{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['APE2']['valorCampo'],ENT_QUOTES);}
																			?>'
												size="35"
												maxlength="100"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE2']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['APE2']['errorMensaje'];}
					?>
					</span>	
					<br />
					
				<label>Fecha de nacimiento</label> <!--no obligatorio pero se valida si existe-->		
					<?php
							//lo referente a fecha podría ser un requiere_once parValorFechas
						$parValorDia["00"]="día"; 
					for ($d=1;$d<=31;$d++) 
					{ if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
						else {$valor="$d";}
						$parValorDia[$valor]=$valor;
					}
						echo comboLista($parValorDia, "campoActualizar[datosFormMiembro][FECHANAC][dia]",
																					$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['dia']['valorCampo'],
																					$parValorDia[$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['dia']['valorCampo']],"","");					
														
					$parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
					"07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
								
						echo comboLista($parValorMes,"campoActualizar[datosFormMiembro][FECHANAC][mes]",
																						$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['mes']['valorCampo'],
																						$parValorMes[$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['mes']['valorCampo']],"","");	 		 

					$parValorAnio["0000"]="año"; 		 
					for ($a=date("Y")-100; $a<=date("Y")-15; $a++){$parValorAnio[$a]=$a;} 
					echo comboLista($parValorAnio,"campoActualizar[datosFormMiembro][FECHANAC][anio]",
																					$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['valorCampo'],
																					$parValorAnio[$datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['valorCampo']],"","");			
					?>	
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['errorMensaje'];}
					?>
					</span>	
					<br /><br />
					
				<label>*Correo electrónico</label>
					<input type="text"
												name="campoActualizar[datosFormMiembro][EMAIL]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);}
												?>'
												size="60"
												maxlength="200"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['errorMensaje'];}
					?>
					</span>	
					<br />				
			
				<label>*Repetir correo electrónico</label>		
					<input type="text"
												name="campoActualizar[datosFormMiembro][REMAIL]"
												value='<?php if ( isset($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['valorCampo']) )
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['valorCampo'],ENT_QUOTES);	}
																									elseif (isset($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo']))
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);	}																												
												?>'
												size="60"
												maxlength="200"
					/>	 
					<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['errorMensaje'];}
						?>
					</span>
					<br />	

				<label>Recibir correos electrónicos de Europa Laica</label>
					<input type="checkbox"
												name="campoActualizar[datosFormMiembro][INFORMACIONEMAIL]"
												value="SI"
							<?php if ($datSocio['campoActualizar']['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=='SI')
							{	echo " checked='checked'"; }
												?>
					/>	 
					<br /><br />
					
				<label>Teléfono fijo</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][TELFIJOCASA]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['TELFIJOCASA']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['TELFIJOCASA']['valorCampo'],ENT_QUOTES);}
												?>'
												size="14"
												maxlength="14"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['TELFIJOCASA']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['TELFIJOCASA']['errorMensaje'];}
					?>
					</span>
					<br />		
					
				<label>Teléfono móvil</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][TELMOVIL]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['valorCampo'],ENT_QUOTES);}
												?>'
												size="14"
												maxlength="14"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['errorMensaje'];}
					?>
					</span>		
					<br /><br />	
							
				<label>Estudios</label>
					<?php
						$parValorEstudios=array(""=>"Elegir opción",
																													"NIVEL5"=>"Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
																													"NIVEL4"=>"Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
																													"NIVEL3"=>"Formación Profesional de Grado Superior",
																													"NIVEL2"=>"Formación Profesional de Grado Medio",
																													"NIVEL1"=>"Garantía Social",
																													"ESO"=>"ESO, Enseñanza Media", 
																													"PRIMARIA"=>"Enseñanza Primaria",
																													"INFANTIL"=>"Educación Infantil (0-6 años)",																							
																													"SINESTUDIOS"=>"Sin estudios");
							echo comboLista($parValorEstudios,"campoActualizar[datosFormMiembro][ESTUDIOS]",
																							$datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo'],
																							$parValorEstudios[$datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo']],"","");										  				  
					?>
					<br />
					
				<label>Profesión</label> 
					<input type="text"
												name="campoActualizar[datosFormMiembro][PROFESION]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['valorCampo']))
												{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['valorCampo'],ENT_QUOTES);}
												?>'
												size="60"
												maxlength="255"
					/>	 
					<span class="error">
					<?php
					if (isset($datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['errorMensaje']))
					{echo $datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['errorMensaje'];}
					?>
					</span>
					<br />			
					
				<label>Puedo colaborar en </label>		
					<?php	  	
					$parValorColabora=array(""=>"Elegir opción","secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
					"actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
					"manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades","tiempo"=>"No dispongo de tiempo");										 
					echo comboLista($parValorColabora,"campoActualizar[datosFormMiembro][COLABORA]",
																					$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo'],
																					$parValorColabora[$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo']],"","");									  
					?>
					<br />					
				
			</p>
		</fieldset>
			
		<!-- ********************** Fin datos personales ******************************************* -->			
		<br />		 	
		<!-- *********************** Inicio datosFormDomicilio  ************************************ -->
		
		<fieldset>
			<legend><b>Domicilio del socio/a</b></legend>	
			<p>	
				<label>*País domicilio </label>
					<?php
						echo comboLista($parValorComboPaisDomicilio['lista'], "campoActualizar[datosFormDomicilio][CODPAISDOM]",
																																$parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"","")
					?> 
					<span class="error">
						<?php
							if (isset($datSocio['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['errorMensaje']))
								{echo $datSocio['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['errorMensaje'];}
						?>
					</span>	
					<br /><br />  
					
				<label>*Dirección: calle, plaza, dirección, nº, bloque, escalera, piso, puerta</label>
					<input type="text"			
												name="campoActualizar[datosFormDomicilio][DIRECCION]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['valorCampo']))
																									{  echo htmlentities($datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['valorCampo'],ENT_QUOTES);}
																			?>'
												size="70"
												maxlength="255"
					/>		
					<span class="error">
					<?php
							if (isset($datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['errorMensaje']))
								{echo $datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['errorMensaje'];}
					?>
					</span>
					<br /><br />
					
				<label>*Código postal</label>	
					<input type="text"			
												name="campoActualizar[datosFormDomicilio][CP]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormDomicilio']['CP']['valorCampo']))
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormDomicilio']['CP']['valorCampo'],ENT_QUOTES);}
																			?>'
												size="6"
												maxlength="10"
					/>		
					<span class="error">
						<?php
								if (isset($datSocio['campoActualizar']['datosFormDomicilio']['CP']['errorMensaje']))
									{echo $datSocio['campoActualizar']['datosFormDomicilio']['CP']['errorMensaje'];}
						?>
					</span>
					<br />		
					
				<label>*Localidad</label>	
					<input type="text"			
												name="campoActualizar[datosFormDomicilio][LOCALIDAD]"
												value='<?php if (isset($datSocio['campoActualizar']['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormDomicilio']['LOCALIDAD']['valorCampo'],ENT_QUOTES);}
																			?>'
												size="50"
												maxlength="255"
					/>		
				<span class="error">
					<?php
							if (isset($datSocio['campoActualizar']['datosFormDomicilio']['LOCALIDAD']['errorMensaje']))
								{echo $datSocio['campoActualizar']['datosFormDomicilio']['LOCALIDAD']['errorMensaje'];}
					?>
				</span>		
				<br />
				
				<label>Acepto recibir cartas de Europa Laica</label>
					<input type="checkbox" 
												name="campoActualizar[datosFormMiembro][INFORMACIONCARTAS]"
							value="SI"
												<?php if ($datSocio['campoActualizar']['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=='SI')
												{  echo " checked='checked'";}
												?>
					/>
					<br />	
					
				</p>			
		</fieldset>		
		<!-- ********************** Fin datosFormDomicilio ***************************************** --> 
		<br />	
		<!-- ********************** Inicio de privacidad de datos ********************************** -->   

		<fieldset>
			<legend><b>Protección de tus datos personales</b></legend>
			<p>					
				<label>*Autorizo la cesión de mis datos únicamente para los fines específicos de Europa Laica (marcar la casilla)</label>
					<input type="checkbox" 
												name="campoActualizar[datosFormPrivacidad][privacidad]"
												value="SI"
												<?php if ($datSocio['campoActualizar']['datosFormPrivacidad']['privacidad']['valorCampo']=='SI')
												{  echo " checked='checked'";}
												?>
					/>
					<span class="error"><strong>
					<?php
						if (isset($datSocio['campoActualizar']['datosFormPrivacidad']['privacidad']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormPrivacidad']['privacidad']['errorMensaje'];}
					?></strong>
					</span>	
					<br /><br />
					
					<!-- si no esta activado javascript, salen pantalla entera -->	
					<a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" 
								target="_blank" title="Privacidad de datos" 
								onclick="ventanaSecundaria(this); return false">
								>>Más información sobre la protección de tus datos personales          
					</a> 
					
			</p>
		</fieldset>	 
		<!-- ********************** Fin de privacidad de datos ************************************* --> 
		<br />	
			
		<!-- ********************** Inicio Botones de formActualizar Socios *********************** -->		
			<span class="error">
			<?php		
					if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
					{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
					}			
			?>	
			</span>
			<br />	

			<!--<div align="center">dio problemas con solo CHROME 2020-01-20 Versión 97.0.4692.99,  -->
				
				<input type="submit" name="comprobarYactualizar" value="Guardar datos actualizados">		
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
				<input type='submit' name="salirSinActualizar" value="No actualizar datos"
										onClick="return confirm('¿Salir sin guardar los campos actualizados del formulario?')">	
					<!-- </div>		-->					
		<!-- ************************* Fin Botones de formActualizar Socios *********************** -->
			
	</form>
	
<!-- ******************** Fin formulario para actualizar datos del socio ********************************* -->	
<br /><br />

</div> <!-- de <div id="registro"> --> 