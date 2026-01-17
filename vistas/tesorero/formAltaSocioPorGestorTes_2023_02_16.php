<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formAltaSocioPorGestorTes.php
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para el registro de un nuevo socio por un gestor con rol Tesorería
             Incluye subir archivo con firma del socio, y relacionado con esto se establece aquí 
													lo valores fijos para variables relacionadas con ese archivo:
													[MaxArchivoSize], [directorioSubir]="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" ,
													[maxLongNomArchivoSinExtDestino]"="250", [permisosArchivo]"="0444" />solo lectura 
             	(se podrían haber recibido desde el cTesorero.php:altaSocioPorGestorTes())									
													
LLAMADA: Es incluida desde "vCuerpoAltaSocioPorGestorTes.php"
													
OBSERVACIONES: Solo se diferencia de formAltaSocioPorGestorCoord.php en que Coord, si puede dar
de alta a socios honorarios y en el link, y algún fragmento de texto, similar a formAltaSocioPorGestorPres.php
	
"formAltaSocioPorGestor.php" es una alternativa que unifica los tres pero es menos flexible	

2022-12-15 : Varias simplificaciones texto y suprimir COLABORA a petición presidencia
2023-01-22: Cambio fecha de nacimiento completa por sólo "año de nacimiento" 
2023-01-22: Cambio fecha de nacimiento completa por sólo "año de nacimiento" 	
--------------------------------------------------------------------------------------------------*/

require_once './modelos/libs/comboLista.php';
?>

<script languaje='JavaScript'>
<!-- 
function limitarTextoArea(max, id)
{	if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
    alert("Llegó al máximo de caracteres permitidos");
	}			
}

function cambiaVisibilidad(valor) 
{
	if (valor ==="NO") 
	{ 
			divFALTA.style.display = 'none';
			divNO.style.display = 'block';
	} 
	else 
	{ 
			divNO.style.display = 'none';
			divFALTA.style.display = 'block';
	}
}	
-->
</script> 


 <div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->

		<span class="error">
			<?php
			if (isset($datosSocio['codError']) && $datosSocio['codError']!=='00000')
			{echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";							
			}			
			?>
		</span>
	
		<br /><br />
	 <span class= "textoRojo9Left"><strong>N U E V O </strong>
		
			<a class="textoAzu112Left2" href='../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf'
				   target='ventana1' title='Descargar el Manual la aplicación informática de Gestión de Soci@s'
							onclick=\"window.open('','ventana1','width=800,height=600,scrollbars=yes')\">
							>> Descargar el Formulario de alta Nuevo/a Socio/a para firmar.</a>		
  </span>							
	 <br />	

	 <span class="textoAzu112Left2">
 		<?php //print_r($datosSocio);
			?>
  </span>		
		
		<!-- ************************ Inicio texto con aclaraciones ****************************************************** -->
	 <span class="textoAzu112Left2">
 		<br />
			Cuando una persona no puede darse de alta directamente con la aplicación informática (mejor ayudarle a darse de alta por si misma), 
			a petición suya, le puede dar de alta un gestor/a (rol coordinación, presidencia, secretaría, o tesorería).
			<br /><br />				
			Si tiene email, al finalizar el proceso de alta por un gestor/a, automáticamente se envía un mensaje al socio/a comunicándole que ha sido dado de alta en la asociación. 
			En el email se le informa de cómo puede entrar en la zona privada de "Área de Soci@s".	
			<br /> 
			También se envían emails a presidencia, secretaría, tesorería y coordinación de la agrupación,	informando del alta del socio/a.					
 	</span>	

	 <br />	<br />
		<span class= "textoRojo9Left"><strong>AVISO: </strong></span>	
		<span class="textoAzu112Left2">
			<strong>Como garantía del cumplimiento de las normas de Protección de Datos, durante el proceso de alta, el gestor/a que inscribe el alta del socio/a
										deberá subir al servidor de Gestión de Soci@s el archivo del formulario de alta con la firma de cesión de datos a EL. 
										</strong> 
           <br /><br />											
            El gestor o el socio/a tiene que escanear o hacer una foto del formulario de alta 
												(sólo de la página del formulario que contiene los datos personales y firma del socio/a).
           <br /><br />	
            Previamente a iniciar el proceso de alta del socio/a, es conveniente que ese archivo del formulario ya lo tengas en el ordenador pues de lo contrario no podrás finalizar el alta.											
			       <br /><br />En caso de dificultades técnicas puedes contactar con "gestion@europalaica.org" (Gestión de Soci@s) para que te ayuden.
			<br /><br /><br />
				Los campos con asterisco (<b>*</b>) son obligatorios	
		</span>
	 <!-- ************************ Fin texto con aclaraciones ********************************************************* -->
	
 <form name="registrarSocio" method="post" action="./index.php?controlador=cTesorero&amp;accion=altaSocioPorGestorTes" enctype="multipart/form-data">	
  <br />		
		
		<!-- ************************ Inicio datos Perosnales MIEMBRO **************************************************** -->	
	
  <fieldset>							
			<legend><b>Datos personales</b></legend>
			<p>	
			
				<label>*Documento</label>
					<?php	  	
					$parValorNIF = array("NIF"=>"NIF","NIE"=>"NIE","Pasaporte"=>"Pasaporte","Otros"=>"Otros");	
					
					echo comboLista($parValorNIF,"datosFormMiembro[TIPODOCUMENTOMIEMBRO]",
																					$datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
													        $parValorNIF[$datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']],"NIF","NIF");									  
					?>
				<label>*Nº documento</label> <!--obligatorio y se valida para NIF y NIE pero no para pasaporte-->
					<input type="text"
												name="datosFormMiembro[NUMDOCUMENTOMIEMBRO]"
												value='<?php if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'],ENT_QUOTES);	}
												?>'
												size="20"
												maxlength="40"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];}
						?>
					</span>		

					<label>*País documento</label>
								<?php
								//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 
								//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
									echo comboLista($parValorComboPaisMiembro['lista'], "datosFormMiembro[CODPAISDOC]",
																																			$parValorComboPaisMiembro['valorDefecto'],$parValorComboPaisMiembro['descDefecto'],"","");						
									?> 
						<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['CODPAISDOC']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['CODPAISDOC']['errorMensaje'];}
						?>
						</span>								
										
					<br /><br />	
					<label>*Sexo</label>	
						<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['SEXO']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['SEXO']['errorMensaje'];}
						?>
						</span>

						<input type="radio"
													name="datosFormMiembro[SEXO]"
													value='H' 
								<?php if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo']=='H')
													{  echo " checked";}
													?>
						/><label>Hombre</label>
						<input type="radio"
													name="datosFormMiembro[SEXO]"
													value='M'
								<?php if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo']=='M')
													{  echo " checked";}
													?>						 
						/><label>Mujer</label>	
						
							<!--<input type="radio"
													name="datosFormMiembro[SEXO]"
													value='X'
													<?php
															/*if ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'] == 'X') 
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
													value='<?php if (isset($datosSocio['datosFormMiembro']['NOM']['valorCampo']))
													{  echo htmlspecialchars($datosSocio['datosFormMiembro']['NOM']['valorCampo'],ENT_QUOTES);	}
													?>'
													size="35"
													maxlength="100"
						/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['NOM']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['NOM']['errorMensaje'];}
						?>
					</span>		
					<br />
				<label>*Apellido primero</label> 
					<input type="text"
												name="datosFormMiembro[APE1]"
												value='<?php if (isset($datosSocio['datosFormMiembro']['APE1']['valorCampo']))
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['APE1']['valorCampo'],ENT_QUOTES);	}
												?>'
												size="35"
												maxlength="100"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['APE1']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['APE1']['errorMensaje'];}
						?>
					</span>	
					<br />	
				<label>Apellido segundo</label> <!--no obligatorio pero se valida si existe-->
					<input type="text"
												name="datosFormMiembro[APE2]"
												value='<?php if (isset($datosSocio['datosFormMiembro']['APE2']['valorCampo']))
																		{  echo htmlspecialchars($datosSocio['datosFormMiembro']['APE2']['valorCampo'],ENT_QUOTES);	}
																			?>'
												size="35"
												maxlength="100"
					/>	 
				<span class="error">
					<?php
					if (isset($datosSocio['datosFormMiembro']['APE2']['errorMensaje']))
					{echo $datosSocio['datosFormMiembro']['APE2']['errorMensaje'];}
					?>
				</span>	

					<br />

					<label>Año de nacimiento</label>			<!-- no obligatorio pero se valida si existe -->			
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
						
					<br /><br />
					
				<label>Teléfono móvil</label> <!--no obligatorio pero se valida si existe-->
					<input type="text"
												name="datosFormMiembro[TELMOVIL]"
												value="<?php if (isset($datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo']))
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo'],ENT_QUOTES);	}
												?>"
												size="14"
												maxlength="14"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['TELMOVIL']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['TELMOVIL']['errorMensaje'];}
						?>
					</span>						

				<label>Teléfono fijo</label> <!-- no obligatorio pero se valida si existe -->
					<input type="text"
												name="datosFormMiembro[TELFIJOCASA]"
												value="<?php if (isset($datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'])) 
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'],ENT_QUOTES);	}
											?>"
												size="14"
												maxlength="14"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['TELFIJOCASA']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['TELFIJOCASA']['errorMensaje'];}
						?>
					</span>					
					<br /><br />	
					
				<label>Profesión</label> <!--no obligatorio pero se valida si existe-->
					<input type="text"
												name="datosFormMiembro[PROFESION]"
												value="<?php if (isset($datosSocio['datosFormMiembro']['PROFESION']['valorCampo'])) 
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['PROFESION']['valorCampo'],ENT_QUOTES);	}
												?>"
												size="60"
												maxlength="255"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['PROFESION']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['PROFESION']['errorMensaje'];}
						?>
					</span>
					<br />

				<label>Estudios</label> 
					<?php	  	
						$parValorEstudios=array("NO-ELIGE" => "Elegir opción",
																														"NIVEL5"=>"Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
																														"NIVEL4"=>"Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
																														"NIVEL3"=>"Formación Profesional de Grado Superior",
																														"NIVEL2"=>"Formación Profesional de Grado Medio",
																														"NIVEL1"=>"Garantía Social",
																														"ESO"=>"ESO, Enseñanza Media", 
																														"PRIMARIA"=>"Enseñanza Primaria",
																														"INFANTIL"=>"Educación Infantil (0-6 años)",																							
																														"SINESTUDIOS"=>"Sin estudios");
																													
						if (!isset($datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']) || empty($datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']))
						{ $datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo'] = "NO-ELIGE";
						}		//para evitar notice al entrar (mejor enviarlo desde controlador al entrar y también parValorEstudios procedente de una tabla en BBDD) 	
																													
						echo comboLista($parValorEstudios,"datosFormMiembro[ESTUDIOS]",
																					$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo'],
																					//$parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']],"--","Elige opción");	
																					$parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']],"","");
																				
					?>

				<br />			
				<!--   <label>Puede colaborar en </label>	 Ya no se utiliza
				<?php	  	
					/*$parValorColabora=array(""=>"Elegir opción","secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
					"actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
					"manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades",
					"tiempo"=>"No dispongo de tiempo");										 
					echo comboLista($parValorColabora,"datosFormMiembro[COLABORA]",
																					$datosSocio['datosFormMiembro']['COLABORA']['valorCampo'],
													$parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']],"","");									  
					*/?>
					--->	
			</p>
	 </fieldset>
	 <br />	

	 <!-- ************************ INICIO INTRODUCIR EMAIL (se oculta y activa si tiene o no email) ******************* -->

		<fieldset>   
			<legend>Correo electrónico</legend>
			<p>
				<label>Si tiene correo electrónico, al efectuase el alta se enviará un email al socio/a comunicándole su alta y su usuario y elección de su contraseña</label>
		
			 <br />	
				<!-- ********************** Inicio Radio buton  'EMAILERROR' NO o FALTA ******************** -->		
				<label><strong>SÍ TIENE email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" 
												name="datosFormMiembro[EMAILERROR]"
												value='NO' 
												onClick="cambiaVisibilidad(this.value)"		
											<?php if ($datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo'] =='NO')
																	{  echo " checked";
																	}									
											?>	
					 /></strong>
				</label>			

				<label><strong>FALTA el email</strong>
					<input type="radio"	
											name="datosFormMiembro[EMAILERROR]"
											value='FALTA'									
											onClick="cambiaVisibilidad(this.value)"					
											<?php if ($datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo']=='FALTA')
																	{  echo " checked";
																	}
																?>												
						/>
				</label>
				
			</p>		
			<!-- ********************** Fin Radio buton  'EMAILERROR' NO o FALTA *********************** -->	
			<br /><br />				
			<!-- ********************** Inicio  correo electrónico   *********************************** -->						
	
			<p id="divNO"><!--   Activa u oculta según valorese javascript cambiaVisibilidad(this.value) -->
				
				<label>*Correo electrónico</label>
					<input type="text"
												name="datosFormMiembro[EMAIL]"
												value='<?php if (isset($datosSocio['datosFormMiembro']['EMAIL']['valorCampo']))
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);	}			
												?>'
												size="60"
												maxlength="200"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['EMAIL']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['EMAIL']['errorMensaje'];}
						?>
					</span>	
				 	<br />
				<label><?php if (isset($datosSocio['datosFormMiembro']['EMAILERROR']) 
																			 			&& $datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo']!=='NO')//no tiene email o está mal
																			{ echo "*Repetir correo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
																			else 
																			{ echo "*Repetir correo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
												?>						
				</label>		
					<input type="text"
												name="datosFormMiembro[REMAIL]"
												value='<?php if (isset($datosSocio['datosFormMiembro']['REMAIL']['valorCampo']))
												{  echo htmlspecialchars($datosSocio['datosFormMiembro']['REMAIL']['valorCampo'],ENT_QUOTES);	}			
												?>'
												size="60"
												maxlength="200"
					/>	 
					<span class="error">
						<?php
						if (isset($datosSocio['datosFormMiembro']['REMAIL']['errorMensaje']))
						{echo $datosSocio['datosFormMiembro']['REMAIL']['errorMensaje'];}
						?>
					</span>
					 <br/ >
					
				<label>Acepta recibir correos electrónicos de Europa Laica</label>
						<input type="checkbox"
													name="datosFormMiembro[INFORMACIONEMAIL]"
													value="SI"
								<?php if ($datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=='SI')
								{	echo " checked='checked'"; }
													?>
						/>	 
					<br />
     <!--  ******************** Fin  correo electrónico     ************************************ -->	

			</p>	<!-- Fin Activa u oculta según valorese javascript cambiaVisibilidad(this.value) -->
		</fieldset>
  <!-- ************************ FIN INTRODUCIR EMAIL (se oculta y activa si tiene o no email) ********************** -->
			
   <br /><br />	
  <!-- ************************ Inicio  Subir al servidor el Archivo de alta *************************************** -->	
		<fieldset>			 				
			<legend><strong>Subir al servidor el Archivo de alta de socia/o con firma para protección de datos</strong></legend>				 	
			<p>				  					
					
				<label>Debes escanear o fotografiar la primera página del formulario de alta del socio/a con firma incluida (tamaño inferior a 2 MB)</label>
						<span class= "textoRojo9Left"><strong>N U E V O </strong></span>		
				
			</p>	   
			<br />					
			<p>
						<!-- Para los siguientes HIDDEN los valores podrían venir desde cTesorero.php:altaSocioPorGestorTes() -->		
						
						<?php		//echo "<br /><br />2 form:datosSocio['ficheroAltaSocioFirmado']: ";print_r($datosSocio['ficheroAltaSocioFirmado']);						
					
						$cadExtensionesPermitidas = $datosSocio['ficheroAltaSocioFirmado']['cadExtPermitidas'];//Extensiones permitidas
						?> 											
						<input type="hidden" name="ficheroAltaSocioFirmado[cadExtPermitidas]" value="<?php echo $cadExtensionesPermitidas;?>" />																						
						<input type="hidden" name="ficheroAltaSocioFirmado[MaxArchivoSize]" value="2097152" /> <!-- Tamaño máximo en bytes = 2MB --> 				
						<input type="hidden" name="ficheroAltaSocioFirmado[directorioSubir]" value="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" />  <!--Directorio relativo donde se guardarán los archivos de firmas -->	
						<input type="hidden" name="ficheroAltaSocioFirmado[maxLongNomArchivoSinExtDestino]" value="250" /> <!-- Longitud maxima del nombre del archivo --> 			
						<input type="hidden" name="ficheroAltaSocioFirmado[permisosArchivo]" value="0444" />  <!-- solo lectura para todos -->							

						<label>Seleccionar el Archivo de alta socio/a incluida firma. Tipos válidos " <?php echo $cadExtensionesPermitidas ;?> ". Tamaño máximo 2 MB</label>							
						<br />			
						<input type="file" name="ficheroAltaSocioFirmado" size="100" /> <!-- Para seleccionar el archivo a subir -->

						<span class="error">
							<?php		
							echo "<br />AVISO: Tienes que introducir el archivo de alta de socio con la firma ahora.<br /> ";																				
							
							if (isset($datosSocio['codError']) && $datosSocio['codError']!=='00000')//si hay algún error de validación en los datos del socio hay que introducir de nuevo el archivo son temporales
							{ //echo "<br />";

									if (isset($datosSocio['ficheroAltaSocioFirmado']['errorMensaje']) && !empty($datosSocio['ficheroAltaSocioFirmado']['errorMensaje']))
									{	
											echo "<br />".$datosSocio['ficheroAltaSocioFirmado']['errorMensaje'];
											if (isset($datosSocio['ficheroAltaSocioFirmado']['nombreArchExtFuente']) && !empty($datosSocio['ficheroAltaSocioFirmado']['nombreArchExtFuente']))
											{
												echo ". Archivo: ".$datosSocio['ficheroAltaSocioFirmado']['nombreArchExtFuente'];
											}	
									}										
									echo "<br />";	
							}			
							?>
						</span>
					 <br /><br />									

	 	</p>	
  </fieldset>	
  <!-- ************************ Fin  Subir al servidor el Archivo de alta ****************************************** -->	
		<br />		
		
	 <!-- ************************ Fin datos Personales MIEMBRO ******************************************************* -->	
		
  <!-- ************************ Inicio  datosFormDomicilio ********************************************************* -->	
	 <fieldset>
	  <legend><b>Domicilio del socio/a</b></legend>	
			<p>	
				<label>*País domicilio </label>
					<?php
						//$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormDomicilio']['CODPAIS1']);			 
						//echo '<br>dentro form:parValorComboPaisDomicilio:';print_r($parValorComboPaisDomicilio);
						//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						echo comboLista($parValorComboPaisDomicilio['lista'], "datosFormDomicilio[CODPAISDOM]",
																																	$parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"","")
					?> 
					<span class="error">
						<?php
							if (isset($datosSocio['datosFormDomicilio']['CODPAISDOM']['errorMensaje']))
								{echo $datosSocio['datosFormDomicilio']['CODPAISDOM']['errorMensaje'];}
						?>
					</span>	
					<br /><br /> 	
				
		  <label>*Dirección: calle, plaza, nº, bloque, escalera, piso, puerta</label>
					<input type="text"			
												name="datosFormDomicilio[DIRECCION]"
												value='<?php if (isset($datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo']))
																									{  echo htmlspecialchars($datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo'],ENT_QUOTES);	}	
																			?>'
												size="70"
												maxlength="255"
					/>		
					<span class="error">
					<?php
							if (isset($datosSocio['datosFormDomicilio']['DIRECCION']['errorMensaje']))
								{echo $datosSocio['datosFormDomicilio']['DIRECCION']['errorMensaje'];}
					?>
					</span>
				<br /><br />			
					
				<label>*Código postal</label>	
					<input type="text"			
												name="datosFormDomicilio[CP]"
												value='<?php if (isset($datosSocio['datosFormDomicilio']['CP']['valorCampo']))
																									{  echo htmlspecialchars($datosSocio['datosFormDomicilio']['CP']['valorCampo'],ENT_QUOTES);	}	
																			?>'
												size="6"
												maxlength="10"
					/>		
				<span class="error">
					<?php
						if (isset($datosSocio['datosFormDomicilio']['CP']['errorMensaje']))
							{echo $datosSocio['datosFormDomicilio']['CP']['errorMensaje'];}
					?>
				</span>

				<label>*Localidad</label>	
					<input type="text"			
												name="datosFormDomicilio[LOCALIDAD]"
												value='<?php if (isset($datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
																									{  echo htmlspecialchars($datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'],ENT_QUOTES);	}	
																			?>'
												size="50"
												maxlength="255"
					/>		
					<span class="error">
					<?php
							if (isset($datosSocio['datosFormDomicilio']['LOCALIDAD']['errorMensaje']))
								{echo $datosSocio['datosFormDomicilio']['LOCALIDAD']['errorMensaje'];}
					?>
										
					</span>		
					<br />		
				
				<label>Acepta recibir cartas de Europa Laica</label>
					<input type="checkbox" 
												name="datosFormMiembro[INFORMACIONCARTAS]"
												value="SI"
												<?php if ($datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=='SI')
												{  echo " checked='checked'";}
												?>
					/>		
					<br />	
		</p>
	 </fieldset>
	 <br />	
  <!-- ************************ Fin  datosFormDomicilio ************************************************************ -->	

		<!-- ************************ Inicio Datos de Cuotas  ************************************************************ -->
	 <fieldset>
	  <legend><strong>Datos de la cuota anual del socio/a</strong></legend>
		 <p>
			
		  <label>*Tipo cuota</label>
				
				<span class="error">
				<?php
				if (isset($datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje']))
				{echo $datosSocio['datosFormSocio']['CODCUOTA']['errorMensaje'];}
				?>
				</span>
				
	    <input type="radio"
	           name="datosFormSocio[CODCUOTA]"
	           value='General' 
						 <?php if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo']=='General')
	           {  echo " checked";}
	           ?>
	    /><label><strong>General&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']['valorCampo']).' €)'; ?></strong></label>

	    <input type="radio"
	           name="datosFormSocio[CODCUOTA]"
	           value='Joven'
						 <?php if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo']=='Joven')
	           {  echo " checked";}
	           ?>						 
	    /><label><strong>Joven&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']['valorCampo']).' €)'; ?></strong></label>
					
	    <input type="radio"
	           name="datosFormSocio[CODCUOTA]"
	           value='Parado' 
						 <?php if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo']=='Parado')
	           {  echo " checked";}
	           ?>
	    /><label><strong>Parado/a&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']['valorCampo']).' €)'; ?></strong></label>
			
 				<input type="radio"
	           name="datosFormSocio[CODCUOTA]"
	           value='Honorario' 
						 <?php if ($datosSocio['datosFormSocio']['CODCUOTA']['valorCampo']=='Honorario')
	           {  echo " checked";}
	           ?>
	    />
					<label><strong>Honorario/a&nbsp;(mínimo <?php echo ceil($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['valorCampo']).' € obligatorio) ';?></strong></label>

		  <br /><br />				

				<span class="comentario11"> 
     Anota la cantidad elegida por la socia/o, igual o superior a la indicada a la indicada en el tipo de cuota correspondiente (Para Honorario = 0 €).
		  </span>	
				<br />	<br />  				
					
		  <span class="comentario11"><strong>Total anual (cuota mínima + donación opcional)</strong></span>		
	    <input type="text"		        
	           name="datosFormCuotaSocio[IMPORTECUOTAANIOSOCIO]"
	           value='<?php if (isset($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo']))
	                        {  echo htmlspecialchars($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'],ENT_QUOTES);	}	
	                  ?>'
	           size="12"
	           maxlength="30"
	     />			
		  <span class="error">
			  <?php
			  if (isset($datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje']))
		    {echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['errorMensaje'];}
			  ?>
		  </span>	
				
				<br />	
				
			  <!-- IMPORTECUOTAANIOEL solo se envía para validar ['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO'] -->
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
					<input type="hidden"	name="datosFormCuotaSocio[IMPORTECUOTAANIOELHonorario]"
	           value='<?php echo $datosSocio['datosFormCuotaSocio']['IMPORTECUOTAANIOELHonorario']['valorCampo']; ?>'
	     />						

	    <input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
					       value='<?php if (isset($datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']))
	                        {  echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo'];}
	                  ?>'
		     />					
           <!-- value="date('Y')" -->							
			</p>
	 </fieldset>
		<br />	
		<!-- ************************ Fin Datos de Cuotas  *************************************************************** -->
		
		<!-- ************************ Inicio Cuenta IBAN  **************************************************************** -->
	 <fieldset>	  
			<legend><strong>Cuenta bancaria del socio/a para el cobro de la cuota anual</strong></legend>
		 <p>
				<span class="comentario11">
				- La fecha de cobro se comunicará con antelación por correo electrónico 
				</span>					
		 	<br /><br />	
		
	   <label>Cuenta <strong>IBAN</strong> (dos letras de país + número sin espacios): </label>  
	    <input type="text"
	           name="datosFormSocio[CUENTAIBAN]"
	           value='<?php if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']))
	                        {  echo  htmlspecialchars($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'],ENT_QUOTES);	}	
	                  ?>'
	           size="50"
	           maxlength="60"
	    /> 			
	 	 <span class="error">
			 <?php
						if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['errorMensaje']))
							{echo $datosSocio['datosFormSocio']['CUENTAIBAN']['errorMensaje'];}
				
			 ?>
		  </span>
			 <br />
			
		 	<!-- Dejo el siguiente input de campo datosFormSocio[CUENTANOIBAN] para que no de warning en las validaciones de campo al no existir ese campo para validar aunque esté vacío -->
		 	<input type="hidden"	name="datosFormSocio[CUENTANOIBAN]"
											value='<?php if (isset($datosSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
																								{  echo $datosSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'];} ?>'
			 />									
				
			 <span class="comentario11">
					- También puede pagar la cuota por transferencia, ingreso o mediante PayPal, los datos de las cuentas 
							están disponibles en Área de Soci@s, -Pagar cuota anual 
					<br /><br />
					- Si la cuenta no pertenece a un banco con sucursales en España
					es más fácil pagar mediante PayPal (con tarjeta de crédito o con una cuenta de PayPal). Disponible en Área de Soci@s, -Pagar cuota anual
				</span>
				<br />
					
			</p>
	 </fieldset>
		<!-- ************************ Fin Cuenta IBAN  ******************************************************************* -->			
	 <br />	 
		
  <!-- ************************ Inicio Agrupación territorial ****************************************************** -->		
		<fieldset>
	  <legend><strong>Elige agrupación territorial</strong></legend>
	  <p>
			
				<label>*Agrupación territorial</label>
					<?php
					 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
						$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional

						echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]", $parValorComboAgrupaSocio['valorDefecto'], $parValorComboAgrupaSocio['descDefecto'], "Elige","Elige agrupación");							 					
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
 	<!-- ************************ Fin Agrupación territorial ********************************************************* -->		
	 <br />		 

	 <!-- ************************ Inicio Datos de datosFormMiembro[comentarioSocio] ********************************** -->
	 <fieldset>
	  <legend><b>Comentarios de la socia/o</b></legend>
	  <p>
		
				<textarea  id='COMENTARIOSOCIO' onKeyPress="limitarTextoArea(500,'COMENTARIOSOCIO');"	
				class="textoAzul8Left" name="datosFormMiembro[COMENTARIOSOCIO]" rows="4" cols="80"><?php 
						if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))                    
					{echo htmlspecialchars($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'],ENT_QUOTES);}	
				?></textarea> 
				
				<span class="error">
										<?php
										if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['errorMensaje'])) {
														echo $datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['errorMensaje'];
										}
										?>
				</span>			
		 </p>
	 </fieldset>
		
	 <!-- ************************ Fin Datos de datosFormMiembro[comentarioSocio] ************************************* --> 
		 <br />	
		<!-- ************************ Inicio Datos de datosFormMiembro[Observaciones] del Gestor ************************* -->
	 <fieldset>
			<legend><b>Observaciones del gestor/a</b></legend>
			<p>
				<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(2000,'OBSERVACIONES');"	
				class="textoAzul8Left" name="datosFormMiembro[OBSERVACIONES]" rows="3" cols="80"><?php 
					if (isset($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']))                    
					{echo htmlspecialchars($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo'],ENT_QUOTES);}	
				?></textarea>
				
					<span class="error">
											<?php
											if (isset($datosSocio['datosFormMiembro']['OBSERVACIONES']['errorMensaje'])) {
															echo $datosSocio['datosFormMiembro']['OBSERVACIONES']['errorMensaje'];
											}
											?>
					</span>			
			</p>
	 </fieldset>
		<!-- ************************ Fin Datos de datosFormMiembro[Observaciones] del Gestor **************************** -->

		<span class="comentario11"><strong>Nota: si el archivo del formulario de alta es grande, ten paciencia y espera hasta que en la pantalla se confirme el alta o que muestre algún mensaje de error</strong></span>			
  <br />  <br />			
			
		<span class="error">
									<?php
									if (isset($datosSocio['codError']) && $datosSocio['codError'] !== '00000') {
													echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
									}
									?>
		</span>
		
	 <br />			
		<!-- ************************ Inicio Botones de formAltaSocioPorGestorPres *************************************** -->  
		
  <div align="center">
    <input type="submit" name="siGuardarDatosSocio" value="Alta del socio/a" class="enviar" />
	  	&nbsp;		&nbsp;		&nbsp;
		 <input type="submit" name="noGuardarDatosSocio" 
		        onClick="return confirm('¿Salir sin dar el alta?')"
		        value='Cancelar el alta' />
	 </div>			
		<!-- ************************ Fin Botones de formAltaSocioPorGestorPres ****************************************** -->  								
		<br />
			
 </form> 

</div> <!-- ********************* Fin <div id="registro">**************** -->

<!-- ********** Fin del formulario formAltaSocioPorGestorTes *************** -->	

<!--<div align="center">
	<form method="post" action="./index.php?controlador=controladorLogin&amp;accion=logOut"		
							onSubmit="return confirm('¿Salir sin grabar los campos del formulario?')">
							<br />		
	   <input type="submit" value="Salir sin guardar datos" class="enviar">
	</form>
					
</div>
-->


