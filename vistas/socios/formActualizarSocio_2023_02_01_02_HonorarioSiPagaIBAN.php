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
2023-02-01: Quito campo colabora.
            Cambio fecha de nacimiento completa por sólo "año de nacimiento". 
            Script para evitar copia y pega email
												En esta versión los socios Honorarios, tienen permitido Pagar Cuota y CUENTAIBAN,
												Esta versión SI requiere un modificaciones en antiguo modeloSocios.php:actualizarDatosSocio()            												

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
 <!-- /* para pruebas */ -->
	<span class="textoAzu112Left2">
	<?php 	
		//echo "datSocio['campoHide']: ";print_r($datSocio['campoHide']);	
	 //	echo "<br /><br />datSocio['campoActualizar']: ";print_r($datSocio['campoActualizar']);
		//echo "<br /><br />datSocio['campoVerAnioActual']:"; print_r($datSocio['campoVerAnioActual']);	
 ?>
	</span>
	<!-- /* para pruebas */ -->
	
	
	<span class="error">
	<?php		
		if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
		{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
		}			
	?>	
	</span>	
		<br /><br />			
		
	<span class="textoAzu112Left2">			
		En esta página puedes actualizar tus datos personales si hubiese cambiado alguno de ellos.
		<br /> <br />
		Si quieres darte de baja como socio/a de Europa Laica y eliminar tus datos personales debes elegir la opción "- Dar de baja socio/a" 
		del menú lateral izquierdo, o puedes enviar un email a   
		<a href="./index.php?controlador=cEnlacesPie&amp;accion=contactarEmail" 
								target="ventana1" title="Contactar con nosotros" 
								onclick="window.open('','ventana1','width=800,height=800,scrollbars=yes')">
								<strong>info@europalaica.org</strong>         
		</a> 	
		
		<br /><br /><br />
		Los campos con asterisco (<strong>*</strong>) son obligatorios.	 			
	</span>
	<br />

	<!-- ************************ Inicio formulario para actualizar datos del socio *********************************** -->
		
	<form name="actualizarSocio" method="post" class="linea" action="./index.php?controlador=controladorSocios&amp;accion=actualizarSocio">
					
		<!-- *********************** Inicio Campos hidden **************************************************************** -->

			<!-- Inicio "campoHide"(incluye anteriorUSUARIO,anteriorEMAIL,anteriorCODPAISDOC,
						 	anteriorTIPODOCUMENTOMIEMBRO,anteriorNUMDOCUMENTOMIEMBRO                     -->
								
			<input type="hidden"	name="campoHide" value="<?php echo $datSocio['campoHide']; ?>"
					/>		
			<!-- Fin "campoHide" *************************************** -->
				
			<!-- Inicio se pasa sin mostrar EMAILERROR solo el presidente, coordinador,.. validan:DEVUELTO,ERROR-FORMATO,NO,-->
			<input type="hidden"	name="campoActualizar[datosFormMiembro][EMAILERROR]"
										value='<?php echo $datSocio['campoActualizar']['datosFormMiembro']['EMAILERROR']['valorCampo']; ?>'
				/>	
			<!-- 	
			<!-- Inicio COMENTARIOSOCIO para que se pase el valor que tuviese previamente pero sin mostrarlo, actualmente 
			no lo puede actualizar el socio y tampoco lo puede ver, pues no era atendido 
			<input type="hidden"	name="campoActualizar[datosFormMiembro][COMENTARIOSOCIO]"
										value='<?php //echo $datSocio['campoActualizar']['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']; ?>'
				/>	-->
				
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
																							{  echo $datSocio['campoVerAnioActual']['ANIOCUOTA'];} ?>'
				/>						
				<input type="hidden"	name="campoVerAnioActual[CODCUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['CODCUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['CODCUOTA'];} ?>'
				/>					
			<input type="hidden"	name="campoVerAnioActual[ESTADOCUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['ESTADOCUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['ESTADOCUOTA'];} ?>'
				/>	
			<input type="hidden"	name="campoVerAnioActual[NOMBRECUOTA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['NOMBRECUOTA']))
																							{  echo $datSocio['campoVerAnioActual']['NOMBRECUOTA'];} ?>'
				/>
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOPAGADA]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA']))
																							{echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA'];} ?>'
				/>					
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOSOCIO]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO']))
																							{echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO'];} ?>'
				/>		
			<input type="hidden"	name="campoVerAnioActual[IMPORTECUOTAANIOEL]"
										value='<?php if (isset($datSocio['campoVerAnioActual']['IMPORTECUOTAANIOEL']))
																							{  echo $datSocio['campoVerAnioActual']['IMPORTECUOTAANIOEL'];} ?>'
				/>	
				<!-- Fin hidden: "campoVerAnioActual" también se usa para ver en pantalla -->	
				
				<!-- Inicio hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla -->			
				<input type="hidden"	id="codSocio" name="campoActualizar[datosFormSocio][CODSOCIO]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo'];} ?>'
				/>								
				<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][CODCUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'];} ?>'
				/>	
			<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][ANIOCUOTA]"
										value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']))
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];} ?>'
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
																							{  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo'];} ?>'
				/>				
			<!-- Fin hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla -->					
					
		<!-- *********************** Fin Campos hidden ******************************************************************* -->
			<br />
			
		<!-- *********************** Inicio Datos de identificación USUARIO ********************************************** --> 	 

		<fieldset>	 
			<legend><strong>Usuario para entrar la zona privada "Área de Soci@s"</strong></legend>	
			<p>
				<label>*Usuario</label> 
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
				<span class="comentario11">Puedes cambiar tu usuario por otro. Para cambiar la contraseña elige "- Cambiar contraseña" en el menú lateral izquierdo
				</span>
				<br />						
				
			</p>
		</fieldset>
		<!-- *********************** Fin Datos de identificación USUARIO ************************************************* -->	
		<br />

		<!-- ** INICIO Datos cuota: Honorario y No Honorario ************************************************************* -->

		<fieldset>	  
			<legend><strong>Cuota anual</strong></legend>
			<p>
				<!-- ********************* Inicio actualizar importeCuotaSocio ************************************************* -->	
				<?php 

				if (isset($datSocio['campoVerAnioActual']['ANIOCUOTA']))
				{ 
						echo "<label>Cuota total".//$error['datosFormCuotaSocioVer']['NOMBRECUOTA']['valorCampo'].
											" pagada en <strong>".$datSocio['campoVerAnioActual']['ANIOCUOTA']."</strong></label>"; 
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA']." euros</span>"; 						

						echo "<label>&nbsp;&nbsp;&nbsp;Estado cuota</label>";												
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['ESTADOCUOTA']." </span>"; 
						
						if  ($datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA' || $datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-ERROR-CUENTA')
						{echo "<span class='mostrar'>Devolución recibo de pago. Contactar con el tesorero de Europa Laica</span>";
						}

						echo "<br /><label>Cuota elegida para el año <strong>".$datSocio['campoVerAnioActual']['ANIOCUOTA']."</strong> </label>".
											"<span class='mostrar'>".$datSocio['campoVerAnioActual']['IMPORTECUOTAANIOSOCIO']." euros </span>";
						echo "<label> cuota tipo </label>";
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['CODCUOTA']."</span>";								
				}	
				?>
		
   <!-- ***** Inicio if Añado para el caso de que Honorarios se les permita PAGAR CUOTA y domiciliar en IBAN ******* -->

		 <!-- ********************** Inicio PAGAR CUOTA IMPORTECUOTAANIOSOCIO para Honorario ***************************** -->						
   <?php 
					/*Antiguo: if ( isset($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']) &&
				               $datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'EXENTO'//Honorario	)
									
				   También serviría: if ( isset($datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo']) &&
				                   $datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] == "Honorario" )
     */ 					
			if ( isset($datSocio['campoVerAnioActual']['CODCUOTA']) && $datSocio['campoVerAnioActual']['CODCUOTA'] == "Honorario" )//Nuevo										
			{
				 echo "<br /><br /><label>NOTA: En el año <strong>".$datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'].
										"</strong> como socio/a <strong> \" ".$datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'].
										" \"</strong> te corresponde pagar una cuota igual o superior a <strong>\" ". 
										ceil($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']). 
										" € \"</strong> , pero si quieres voluntariamente puedes pagar una cuota anual superior o hacer una donación.</label>";																						
				 ?>	

					<input type="hidden"	name="campoActualizar[datosFormSocio][CODCUOTA]"
			         value="<?php /*if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] ))
																									{ echo $datSocio['campoActualizar']['datosFormCuotaSocio']['CODCUOTA']['valorCampo'];}*/
																								if (isset($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'] ))
																									{ echo $datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo'];}
																			?>"	/>

					<!--	Antes era necesario en para evitar que error de validación: Sobraría este Hidden al añadir lo siguiente para poder pagar cuota -->										
					<!--<input type="hidden"	name="campoActualizar[datosFormCuotaSocio][IMPORTECUOTAANIOSOCIO]"
												value="<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo']))
																									{ echo $datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];}
																								 /* acaso otro con if (isset($datSocio['campoActualizar']['datosFormSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] ))
																									{ echo $datSocio['campoActualizar']['datosFormSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];}*/
																			?>"	/>	
						-->	
     <!--	Antes era necesario en EXENTO	para evitar que error de validación: Sobraría al añadir lo siguiente para poder pagar cuota -->						
					<br /><br />


     <!-- Cuando se ponga pago para honorarios lo siguiente podría ser común y al final de los dos -->
					<span class="comentario11">
					<strong>Elegir nuevos valores de cuota voluntaria para el año <?php echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']?>
					</strong>(también se pueden dejar sin modificar).
					</span>						
					<br />
					
					<span class="comentario11">
					Si en el presente año ya estuviese pagada tu cuota, se anotará como tu nueva cuota elegida para el próximo año.
					</span>								
					<br /><br />	
					<span class="comentario11"><strong> Modificar la Cuota total para el año 
					<?php echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'].
					" (cuota mínima ".ceil($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']) ." € + donación opcional)	</strong>";	?>
					</span>
					
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
					<br />
		   <!-- ******************** Fin PAGAR CUOTA IMPORTECUOTAANIOSOCIO para Honorario ******************************** -->						
		
		   <!-- ******************** Inicio cuenta bancaria IBAN para Honorario ****************************************** -->		
					
	 	  <!-- Cuando se ponga pago para honorarios lo siguiente podría ser común y al final de los dos -->
     <!-- ***** Inicio Descomentar para activar IBAN para Honorario -->
				<fieldset>	  
						<legend><strong>Cuenta bancaria para el cobro de tu cuota anual voluntaria</strong></legend>
						<p>

							<?php 
								if (isset($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo']))
								{  
										echo "<span class='comentario11'>		Si hubieses cambiado tu cuenta bancaria escribe la nueva cuenta </span>"; 
								}
								else
								{
										echo "<span class='comentario11'> Ahora puedes domiciliar el pago de tu cuota anual voluntaria</span>";						
								}			
							?>				
							<br /><br />		
							<label>Tu cuenta <strong>IBAN</strong> (dos letras de país + número sin puntos ni guiones)</label> 
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
							<br /><br />

							<span class="comentario11">
								- La fecha de cobro te lo comunicaremos con antelación por correo electrónico		
							<br /><br />										
								- También puedes pagar tu cuota por transferencia, ingreso o mediante PayPal en las cuentas de de Europa Laica en la opción del menú:
								<a href="../../usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a>
							</span>
							<br />			

					</p>
			 </fieldset>	
				<!-- **** Fin Descomentar para activar IBAN para Honorario -->			
				<!-- ********************** Fin cuenta bancaria IBAN para Honorario ******************************************** -->	

				<!-- ***** Fin Añado para el caso de que Honorarios se les permita PAGAR CUOTA y domiciliar en IBAN ************ -->										
		
			 <?php 	
			}
			/*-- ***** Fin if Añado para el caso de que Honorarios SÍ se les permita PAGAR CUOTA y domiciliar en IBAN ****** --*/
			
			/*--- Inicio "else" para NO Honorario (en NO Honorario: actualiza importeCuotaSocio,cuenta IBAN ) ************* ---*/
			// Se podría hacer común esta parte, para evitar repetirla 
			else //		if ($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']!== 'EXENTO')	
			{
					?>
					<br /><br /><br />
					<!-- ******************** Inicio actualizar importeCuotaSocio (No Honorario) ********************************** -->	
					
					<span class="comentario11">
					<strong>Elegir nuevos valores de cuota para el año 
					<?php echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']?>
					</strong>(también se pueden dejar sin modificar).
					</span>		
					
					<br />					
					
					<span class="comentario11">
					Si en el presente año ya estuviese pagada tu cuota, se anotará como tu nueva cuota elegida para el próximo año.
					</span>			
					
					<br /><br />
					
					<!--<label>*Tipo cuota</label> -->	
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

					<label><strong>General&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELGeneral']).' €)'; ?></strong></label>
					<br /><br />  		
					<span class="comentario11"><strong>Reducida:</strong></span>
					<br /> 				
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Joven'
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Joven')
													{  echo " checked";}
													?>						 
						/>			
						
					<label><strong>Joven&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELJoven']).' €)'; ?></strong></label>	
						
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Parado' 
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Parado')
													{  echo " checked";}
													?>
						/>
					<label><strong>Parado/a o dificultades enconómicas&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELParado']).' €)'; ?></strong></label>							
						<br /><br />

				 <!--
					<label><strong> Modificar la Cuota total para el año 
					<?php echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']." (cuota mínima + donación opcional)	</strong>";	?>					
					</label>
					-->
					<span class="comentario11"><strong> Modificar la Cuota total para el año 
					<?php echo $datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']." (cuota mínima + donación opcional)	</strong>";	?>
					</span>
					
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
					<br /><br />			
					<span class="comentario11">No aceptamos subvenciones, nuestros ingresos proceden de las cuotas y donaciones de nuestras socias, socios y simpatizantes.</span>
					<br/>					
					
			</p>
		</fieldset> 	
		<!-- *********************** Fin actualizar importeCuotaSocio (No Honorario) ************************************* -->	
		<br />
		
			<!-- ********************** Inicio cuenta bancaria (No Honorario) *********************************************** -->				
			<fieldset>	  
				<legend><strong>Cuenta bancaria para el cobro de tu cuota anual</strong></legend>
				<p>

					<?php 
						if (isset($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($datSocio['campoActualizar']['datosFormSocio']['CUENTAIBAN']['valorCampo']))
						{  
								echo "<span class='comentario11'>		Si hubieses cambiado tu cuenta bancaria escribe la nueva cuenta </span>"; 
						}
						else
						{
								echo "<span class='comentario11'> Ahora puedes domiciliar el pago de tu cuota anual</span>";						
						}			
					?>				
					<br /><br />		
					<label>Tu cuenta <strong>IBAN</strong> (dos letras de país + número sin puntos ni guiones)</label> 
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
					<br /><br />

					<span class="comentario11">
						- La fecha de cobro te lo comunicaremos con antelación por correo electrónico		
					<br /><br />										
						- También puedes pagar tu cuota por transferencia, ingreso o mediante PayPal en las cuentas de de Europa Laica en la opción del menú:
						<a href="../../usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocio"><strong>- Pagar cuota anual</strong></a>
					</span>
					<br />
					
				</p>
			</fieldset>
			<!-- ********************* Fin cuenta bancaria (No Honorario) *************************************************** -->		
				
			<?php
		 }	//else if ($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] !== 'EXENTO')
	 	/*--- FIN "else" para NO honorario (actualizar importeCuotaSocio, cuenta bancaria) --------------------------------*/ 
			?>

		<!-- **** FIN Datos cuota: Honorario (en Honorario: Tactualiza importeCuotaSocio,cuenta banco)*** -->
		
		<br />
		<!-- ********************** Inicio Agrupación territorial ******************************************************** -->		 
		<fieldset>	 
			<legend><b>Elegir agrupación territorial de Europa Laica</b></legend>	
			<p>
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
		<!-- ********************** Fin Agrupación territorial *********************************************************** -->	
			<br />
		<!-- ********************** Inicio datos personales ************************************************************** -->	
		
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
					
						<!--<input type="radio"
													name="datosFormMiembro[SEXO]"
													value='X'
													<?php
															/*if ($datSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'X') 
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
					<label >*Año de nacimiento</label> 
					
						<input type="text"
													name="campoActualizar[datosFormMiembro][FECHANAC][anio]"
													value='<?php
																					if (isset($datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] ) && $datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] !== '0000') 
																					{
																									echo $datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['valorCampo'];
																					}
																				?>'
													size="4"
													maxlength="4"
													/>	 
						<span class="error">
												<?php
													if (isset($datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['errorMensaje'])) 
													{
																	echo $datSocio['campoActualizar']['datosFormMiembro']['FECHANAC']['anio']['errorMensaje'];
													}
												?>
						</span>		
					
					<br /><br />
				
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
					
				<label>Estudios</label>
					<?php
						$parValorEstudios=array("NO-ELIGE"=>"Elegir opción",
																													"NIVEL5"=>"Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
																													"NIVEL4"=>"Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
																													"NIVEL3"=>"Formación Profesional de Grado Superior",
																													"NIVEL2"=>"Formación Profesional de Grado Medio",
																													"NIVEL1"=>"Garantía Social",
																													"ESO"=>"ESO, Enseñanza Media", 
																													"PRIMARIA"=>"Enseñanza Primaria",
																													"INFANTIL"=>"Educación Infantil (0-6 años)",																							
																													"SINESTUDIOS"=>"Sin estudios");
																													
							if (!isset($datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo']) || empty($datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo']))
							{ $datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo'] = "NO-ELIGE";
							}		//para evitar notice al entrar (mejor enviarlo desde controlador al entrar y también parValorEstudios procedente de una tabla en BBDD)																																																											
																													
							echo comboLista($parValorEstudios,"campoActualizar[datosFormMiembro][ESTUDIOS]",
																							$datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo'],
																							$parValorEstudios[$datSocio['campoActualizar']['datosFormMiembro']['ESTUDIOS']['valorCampo']],"","");										  				  
					?>	
	
				<!--	
				<label>Puedo colaborar en Se elimina del formulario, lo dejo porque sigue en la tabla MIEMBRO</label>		
					<?php	  	
					/*$parValorColabora=array(""=>"Elegir opción","secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
					"actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
					"manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades","tiempo"=>"No dispongo de tiempo");										 
					echo comboLista($parValorColabora,"campoActualizar[datosFormMiembro][COLABORA]",
																					$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo'],
																					$parValorColabora[$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo']],"","");		*/							  
					?>
					-->	
    <br />	<br />
				
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
			
				<label>*Repetir correo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>		
					<input type="text" id="probando"
												name="campoActualizar[datosFormMiembro][REMAIL]"
												value='<?php if ( isset($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['valorCampo']) )
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['valorCampo'],ENT_QUOTES);	}
																									elseif (isset($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo']))
																									{  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);	}																												
												?>'
												size="60"
												maxlength="200"
					/>	 
     <!-- Script Para impedir copia y pega el email -->		
					<div id="aviso"></div>			
					<!--<span id="aviso" class='error'> -->
					<script>
        let probando = document.getElementById("probando")
        let aviso = document.getElementById("aviso")        
        probando.addEventListener("paste", (event) => {
          event.preventDefault()
          aviso.innerHTML = "<span class='error'>No se permite pegar el email, escríbelo manualmente </span>"//no lo captura salvo que ponga antes <div id="aviso"></div>	
										//aviso.innerHTML = "No se permite pegar el email, escríbelo manualmente "
        })
     </script>			
    <!-- </span> -->
				<!-- Fin Script Para impedir copia y pega el email -->		
					<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['REMAIL']['errorMensaje'];}
						?>
					</span>
					<br /><br />		

				<label>Acepto recibir correos electrónicos de Europa Laica</label>
					<input type="checkbox"
												name="campoActualizar[datosFormMiembro][INFORMACIONEMAIL]"
												value="SI"
							<?php if ($datSocio['campoActualizar']['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=='SI')
							{	echo " checked='checked'"; }
												?>
					/>	 
			 <br />		
			</p>
		</fieldset>
			
		<!-- ********************** Fin datos personales ***************************************************************** -->			
		<br />		 	
		<!-- ********************** Inicio datosFormDomicilio  *********************************************************** -->
		
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
					
				<label>*Dirección: calle, plaza, nº, bloque, escalera, piso, puerta</label>
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
		<!-- ********************** Fin datosFormDomicilio *************************************************************** --> 
		<br />	
		<!-- ********************** Inicio de privacidad de datos ******************************************************** -->   

		<fieldset>
			<legend><b>Protección de tus datos personales</b></legend>
			<p>		
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
					<!--<label><strong>*He leído y autorizo el uso de mis datos personales para los fines específicos de Europa Laica (marcar la casilla)</strong></label>-->
					<span class="comentario11"><strong>*He leído y autorizo el uso de mis datos personales para los fines específicos de Europa Laica (marcar la casilla)</strong></span>						

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
					<br />					
			</p>
		</fieldset>	 
		<!-- ********************** Fin de privacidad de datos *********************************************************** --> 

			
		<!-- ********************** Inicio Botones de formActualizar Socios ********************************************** -->		
   <p>
			 <span class="comentario11">
				Si necesitas ayuda: 	<strong>info@europalaica.org</strong>, &nbsp;&nbsp;&nbsp;<strong>	Teléfono</strong> <!-- o Whatsapp--> (España): <strong>670 55 60 12 </strong> 
			 </span>				
	
   </p>		
			<br /><br />	
			<span class="error">
			<?php		
					if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
					{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
					}			
			?>	
			</span>
			<br />	

			<!--<div align="center">  -->
				
				<input type="submit" name="comprobarYactualizar" value="Guardar datos actualizados">		
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
				<input type='submit' name="salirSinActualizar" value="No actualizar datos"
										onClick="return confirm('¿Salir sin guardar los campos actualizados del formulario?')">	
					<!-- </div>		-->					
		<!-- *********************** Fin Botones de formActualizar Socios ************************************************ -->
			
	</form>
	
<!-- ************************* Fin formulario para actualizar datos del socio ************************************** -->	

<br /><br />

</div> <!-- de <div id="registro"> --> 