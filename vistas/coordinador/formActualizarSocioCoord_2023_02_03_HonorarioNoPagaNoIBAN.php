<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: formActualizarSocioCoord.php
VERSION: PHP 7.3.21

DESCRIPCION: En este formulario el coordinador, actualiza  datos personales de un socio, cuotas, IBAN, 
agrupación, afecta a varias varias tablas

LLAMADA: vistas/coordinador/vCuerpoActualizarSocioCoord.php y a su vez desde 
cCoordinador.php:actualizarSocioCoord() en lista de socios desde el icono icono Modifica = Pluma

OBSERVACIONES:
2023-01-22: Cambio fecha de nacimiento completa por sólo "año de nacimiento" 
----------------------------------------------------------------------------------------------------*/
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
  <span class="comentario11">
	    Los campos con asterisco (<b>*</b>) son obligatorios	 
  </span>
	 <br />
		
	<span class="error">
		<?php

			if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
			{echo "<b>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";							
			}
			?>	
	</span>		
	
 <br /><br />
 <form name="actualizarSocio" method="post" class="linea" action="./index.php?controlador=cCoordinador&amp;accion=actualizarSocioCoord">
								
  <!-- ****************** Inicio Campos hidden ************************************************** -->
		
			<!-- Inicio "campoHide"(incluye anteriorUSUARIO,anteriorEMAIL,anteriorCODPAISDOC,
							anteriorTIPODOCUMENTOMIEMBRO,anteriorNUMDOCUMENTOMIEMBRO ************************** -->
			<input type="hidden"	name="campoHide" value="<?php echo $datSocio['campoHide']; ?>"	/>		
			<!-- Fin "campoHide"  ***************************************************************** -->	
	
		 <!-- Inicio de "privacidad" se pone a SI en Pres, Coor, Tes, para que la función de validación 
						"validarCamposFormActualizarSocio()" para todos los casos socios, presidente, coordinador
						donde no se hace la pregunta privacidad.	Acaso convenga hacer un tratamiento diferenciado
						validar y quitar esto de aquí   
			--------------------------------------------------------------------------------------- -->	   
			<input type="hidden" name="campoActualizar[datosFormPrivacidad][privacidad]" value="SI" />
	 	<!--  ****************** fin privacidad  ********************************************  -->		
 	
			<input type="hidden" id="codUser" name="campoActualizar[datosFormUsuario][CODUSER]"
									 value='<?php if (isset($datSocio['campoActualizar']['datosFormUsuario']['CODUSER']['valorCampo']))
																						{  echo $datSocio['campoActualizar']['datosFormUsuario']['CODUSER']['valorCampo'];}
																?>'
			/>
			<input type="hidden"	id="codSocio"	name="campoActualizar[datosFormSocio][CODSOCIO]"
								 	value='<?php if (isset($datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo']))
																						{  echo $datSocio['campoActualizar']['datosFormSocio']['CODSOCIO']['valorCampo'];}
																?>'
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

			<!-- Inicio hidden: "campoVerAnioActual" también se usa para ver en pantalla ****************** -->	
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
				<!-- Fin hidden: "campoVerAnioActual" también se usa para ver en pantalla ****************** -->	
				
    <!-- Inicio hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla -->					
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
   <input type="hidden"	name="campoActualizar[datosFormCuotaSocio][NOMBRECUOTA]"
          value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo']))
                       {  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['NOMBRECUOTA']['valorCampo'];}?>'
    />		
   <input type="hidden"	name="campoActualizar[datosFormCuotaSocio][IMPORTECUOTAANIOEL]"
          value='<?php if (isset($datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo']))
                       {  echo $datSocio['campoActualizar']['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'];}?>'
    />				
   <!-- Fin hidden: "campoActualizar[datosFormCuotaSocio]" también se usa para ver en pantalla ---->					
				
  <!-- ****************** Fin Campos hidden ***************************************************** -->
		
	 <!-- ********************** Inicio Datos de identificación PERSONALES ************** -->	
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		 <p>
			 <label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="campoActualizar[datosFormUsuario][ESTADO]"
	           value='<?php if ($datSocio['campoActualizar']['datosFormUsuario']['ESTADO']['valorCampo'])
	           {  echo $datSocio['campoActualizar']['datosFormUsuario']['ESTADO']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
					<br />		

					
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
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['NOM']['valorCampo']))
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['NOM']['valorCampo'],ENT_QUOTES);	}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	 
					<span class="error">
						<?php
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['NOM']['errorMensaje']))
						{echo $datSocio['campoActualizar']['datosFormMiembro']['NOM']['errorMensaje'];}
						?>
					</span>						

		  <label>*Apellido primero</label> 
	    <input type="text"
	           name="campoActualizar[datosFormMiembro][APE1]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE1']['valorCampo']))
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['APE1']['valorCampo'],ENT_QUOTES);	}
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
	   <label>Apellido segundo</label> <!--no obligatorio pero se valida si existe-->
	    <input type="text"
	           name="campoActualizar[datosFormMiembro][APE2]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['APE2']['valorCampo']))
	                 {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['APE2']['valorCampo'],ENT_QUOTES);	}
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
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'],ENT_QUOTES);	}
	           ?>'
	           size="20"
	           maxlength="40"
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
					<br />
					
					<label>Año de nacimiento</label> <!-- no obligatorio para gestor pero se valida si existe -->		
					
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

		  <label>*Correo electrónico</label>
	    <input type="text"
	           name="campoActualizar[datosFormMiembro][EMAIL]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo']))
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['EMAIL']['valorCampo'],ENT_QUOTES);	}
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
					
			 <label>*Error correo electrónico</label> 
					<?php
					$parValorErrorEmail=array("NO"=>"NO","FALTA"=>"FALTA","ERROR-FORMATO"=>"ERROR-FORMATO","DEVUELTO"=>"DEVUELTO");										 
					echo comboLista($parValorErrorEmail,"campoActualizar[datosFormMiembro][EMAILERROR]",
																					$datSocio['campoActualizar']['datosFormMiembro']['EMAILERROR']['valorCampo'],
													        $parValorErrorEmail[$datSocio['campoActualizar']['datosFormMiembro']['EMAILERROR']['valorCampo']],"NO","NO");								  
					?>					
			
		  <label>Recibir correos electrónicos de Europa Laica</label>
	    <input type="checkbox"
	           name="campoActualizar[datosFormMiembro][INFORMACIONEMAIL]"
	           value="SI"
						 <?php if ($datSocio['campoActualizar']['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']=='SI')
						 {	echo " checked='checked'"; }
	           ?>
	    />	 
	     <br /><br />
						
		  <label>Teléfono móvil</label> 
     <input type="text"
	           name="campoActualizar[datosFormMiembro][TELMOVIL]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['valorCampo']))
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['TELMOVIL']['valorCampo'],ENT_QUOTES);	}
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
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['TELFIJOCASA']['valorCampo'],ENT_QUOTES);	}
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

	  	<label>Profesión</label> 
	    <input type="text"
	           name="campoActualizar[datosFormMiembro][PROFESION]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['valorCampo']))
	           {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['PROFESION']['valorCampo'],ENT_QUOTES);	}
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

				<!--		<br />							
		  <label>Puedo colaborar en </label>		Ya no se utiliza
					<?php	  	
						/*$parValorColabora=array(""=>"Elegir opción","secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
						"actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
						"manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades","tiempo"=>"No dispongo de tiempo");										 
						echo comboLista($parValorColabora,"campoActualizar[datosFormMiembro][COLABORA]",
																						$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo'],
																						$parValorColabora[$datSocio['campoActualizar']['datosFormMiembro']['COLABORA']['valorCampo']],"","");									  
						*/?> -->
					<br />			
					
		 </p>			
	 </fieldset>
	 <br />	
	 <!-- ********************** Fin Datos de identificación PERSONALES ***************** -->	

	 <!-- ********************** Inicio datosFormDomicilio  ***************************** --> 
	 <fieldset>
	  <legend><b>Domicilio del socio / a</b></legend>	
	 	<p>	
		 	<label>*País domicilio </label>
					<?php
					//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
					echo comboLista($parValorComboPaisDomicilio['lista'], "campoActualizar[datosFormDomicilio][CODPAISDOM]",
																																$parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"","")
					?> 
					<span class="error">
						<?php
							if (isset($datSocio['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['errorMensaje']))
								{echo $datSocio['campoActualizar']['datosFormDomicilio']['CODPAISDOM']['errorMensaje'];}
						?>
					</span>	
		   <br />
					
			 <label>*Dirección: calle, plaza, nº, bloque, escalera, piso, puerta</label>
	    <input type="text"			
	           name="campoActualizar[datosFormDomicilio][DIRECCION]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['valorCampo']))
	                        {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormDomicilio']['DIRECCION']['valorCampo'],ENT_QUOTES);	}
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
	                        {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormDomicilio']['CP']['valorCampo'],ENT_QUOTES);	}
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
	                        {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormDomicilio']['LOCALIDAD']['valorCampo'],ENT_QUOTES);	}
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

	 <!-- ********************** Fin datosFormDomicilio ********************************* --> 
			 <br />
				
		<!-- ********************** Inicio Datos de identificación USUARIO **************** -->
	 <fieldset>
	  <legend><b>Usuario para entrar en la zona privada "Área de Soci@s"</b></legend>
		 <p>			
					<span class="comentario11">
						El usuario/a y la contraseña solo se pueden cambiar por el socio/a. 
						La contraseña no se puede mostrar por privacidad de datos
					</span>
					<br />
	   <label>*Usuario/a</label> 
	    <input type="text" readonly
					       class="mostrar"	
	           name="campoActualizar[datosFormUsuario][USUARIO]"
	           value='<?php if (isset($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['valorCampo']))
	                        {  echo htmlspecialchars($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['valorCampo'],ENT_QUOTES);	}	
	                  ?>'
	           size="35"
	           maxlength="50"
	     />
						<span class="error">		
							<?php
									if (isset($datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['errorMensaje']))
									{echo $datSocio['campoActualizar']['datosFormUsuario']['USUARIO']['errorMensaje'];}
							?>
					</span>		
					<br />			
					
	 	</p>	
  </fieldset>
 	<!-- ********************** Fin Datos de identificación USUARIO ******************* -->		
			<br />	
			
		<!-- INICIO Datos cuota: Honorario y No Honorario (en NO Honorario: actualiza importeCuotaSocio,cuenta banco) -->

		<fieldset>	  
	 <legend><strong>Datos de la cuota anual del socio/a</strong></legend>
			<p>
			 <!-- ************************** Inicio mostrar cuotas año actual ***************** -->			
				<?php
				if (isset($datSocio['campoVerAnioActual']['ANIOCUOTA']))//puede sobrar
				{ 
						echo "<label>Cuota ".//$error['datosFormCuotaSocioVer']['NOMBRECUOTA']['valorCampo'].
											" pagada por el socio/a en <strong>".$datSocio['campoVerAnioActual']['ANIOCUOTA']."</strong></label>"; 
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['IMPORTECUOTAANIOPAGADA']." euros</span>"; 
						

						echo "<label>&nbsp;&nbsp;&nbsp;Estado cuota</label>";												
						echo "<span class='mostrar'>".$datSocio['campoVerAnioActual']['ESTADOCUOTA']." </span>"; 
						
						if  ($datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA'  || $datSocio['campoVerAnioActual']['ESTADOCUOTA'] == 'NOABONADA-ERROR-CUENTA')
						{echo "<span class='mostrar'>Devolución recibo de pago. Contactar con Tesorería de Europa Laica</span>";
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
											"</strong> - está <strong>EXENTO</strong> de abonar las cuotas </label>";				
										
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
				
					<span class="comentario11">Ahora puedes modificar la cantidad elegida como cuota por el socio/a, si el socio/a así te lo ha comunicado, 
																															<br />
																																Si en el presente año ya estuviese abonada la cuota, 
																																se anotará como nueva cuota para el próximo año. 
																																<br />Nota: para socios/as Honorarios, por ahora la cuota que se anotará siempre será 0 €, 
																															 si quieren hacer alguna aportación económica será en concepto de una donación independiente de la cuota.		
					</span>							
					<br /><br />					
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
					<label><strong>General&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELGeneral']).' €)'; ?></strong></label>
						
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Joven'
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Joven')
													{  echo " checked";}
													?>						 
						/>
					<label>Joven&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELJoven']).' €)'; ?></label>	
						
						<input type="radio"
													name="campoActualizar[datosFormSocio][CODCUOTA]"
													value='Parado' 
								<?php if ($datSocio['campoActualizar']['datosFormSocio']['CODCUOTA']['valorCampo']=='Parado')
													{  echo " checked";}
													?>
						/>
					<label>Parado&nbsp;(mínimo <?php echo ceil($datSocio['campoActualizar']['datosCuotasEL']['IMPORTECUOTAANIOELParado']).' €)'; ?></label>							
						<br />
						<span class="comentario11">
						Si es un/a Joven (18 a 25 años) sin ingresos, o Parado o tiene dificultades económicas puedes elegir la correspondiente cuota reducida hasta que cambie su situación. 		
						</span>
						<br /><br />		
				
					<label>Modificar la cuota elegida por el socio/a para el año 
						<?php echo " <b>".$datSocio['campoActualizar']['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']."</b>";		
						?>
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
		<!-- ************************ Fin actualizar importeCuotaSocio *********************** -->	
		
	 <!-- ************************ Fin cuotas del socio *********************************** -->			
			<br />
		
		<!-- ********************** Inicio cuenta bancaria  ********************************** -->				
		<fieldset>	  
		<legend><strong>Modificar la cuenta del  banco del socio/a para el pago cuota anual</strong></legend>
			<p>

				<span class="comentario11">
						- Para domiciliar el pago de la cuota anual, escribe el número de su cuenta bancaria (si te lo ha proporcionado la socia/o).	
						La fecha de cobro de los recibos se le comunicará con antelación por correo electrónico 
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
				<br /><br />
				
				<span class="comentario11">
				- También puede pagar su cuota por transferencia, ingreso o mediante PayPal, los datos de las cuentas bancarias de Europa Laica
					están disponibles en Gestión de Soci@s, -Pagar cuota anual 
				<br /><br />
				- Si la cuenta bancaria no pertenece a un banco con sucursales en España
				resulta más fácil pagar mediante PayPal (con tarjeta de crédito o con una cuenta de PayPal). También disponible en Gestión de Soci@s, -Pagar cuota anual 		
				<br /><br />
					Nota: Para que la socia/o tenga derecho de voto en las asambleas, el pago de la primera cuota
					lo tendrá que hacer por alguno de los otros procedimientos que antes te hemos indicado, excepto si ha domiciliado el pago.	
				</span>	
				<br />		
				
			</p>
		</fieldset>
		<!-- ********************** Fin cuenta bancaria (No Honorario) ************************ -->		
			<br />		
				
			<?php
		}	//else if ($datSocio['campoActualizar']['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] !== 'EXENTO')
		/*--- FIN "else" para NO honorario (actualizar importeCuotaSocio, cuenta bancaria) ----------------*/ 
			?>

		<!-- FIN Datos cuota: Honorario y No Honorario (en NO Honorario: actualiza importeCuotaSocio,cuenta banco) -->

		<br />
		
		<!-- ********************** Inicio Agrupación territorial ***************************** -->		
		<fieldset>
		 <legend><b>Elegir agrupación territorial del socio/a</b></legend>
   <p>
				<label>*Agrupación</label>
					<?php		    
					//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
					
					unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
					$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional

					echo comboLista($parValorComboAgrupaSocio['lista'], "campoActualizar[datosFormSocio][CODAGRUPACION]",
																																		$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['descDefecto'],"","");		 
					?> 	
				<br />
				
		 </p>
	 </fieldset>
  <!-- *********************** Fin Agrupación territorial ******************************** -->		
			 <br />
				
	 <!-- ***** Inicio ['COMENTARIOSOCIO'] y [OBSERVACIONES] para tabla MIEMBRO ************* -->
		
		<!-- ******** Inicio  COMENTARIOSOCIO al darse de alta *********** -->
	 <fieldset>
	  <legend><b>Comentarios del socio /a</b></legend>
	 	<p>
				<textarea type="text" readonly class="mostrar1" wrap="hard" name="campoActualizar[datosFormMiembro][COMENTARIOSOCIO]" 
														rows="3" cols="80"><?php 
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))                    
					{echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'],ENT_QUOTES);}	
				?></textarea> 			 
				
			</p>
		</fieldset>
		
		<!-- ******** Fin  COMENTARIOSOCIO al darse de alta ************** -->
				<br />
				
		<!-- ****** Inicio  datosFormMiembro[OBSERVACIONES] por gestor *** -->
		
		<fieldset>			
			<legend><b>Observaciones del gestor de socios /as</b></legend>
			<p>
				<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(2000,'OBSERVACIONES');"	
				class="textoAzul8Left" name="campoActualizar[datosFormMiembro][OBSERVACIONES]" rows="7" cols="80"><?php 
						if (isset($datSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['valorCampo']))                    
					{echo htmlspecialchars($datSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['valorCampo'],ENT_QUOTES);}	
				?></textarea> 		
				
							<span class="error">
											<?php
											if (isset($datSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['errorMensaje'])) {
															echo $datSocio['campoActualizar']['datosFormMiembro']['OBSERVACIONES']['errorMensaje'];
											}
											?>
								</span>	
    							
	 	</p>
	 </fieldset>
	 <!-- ****** Fin  datosFormMiembro[OBSERVACIONES] por gestor ***** -->		
		
  <!-- ***** Fin ['COMENTARIOSOCIO'] y [OBSERVACIONES] para tabla MIEMBRO *************** -->		

	 	
		<!-- ********************** Inicio Botones de formActualizar Socios ****************** -->  
		<span class="error">
			<?php
				if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
				{echo "<b>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";							
				}
				?>	
		</span>		
	 <br />
		
		<!-- <div align="center">dio problemas con solo CHROME 2020-01-20 Versión 97.0.4692.99,  -->
		
			<input type="submit" name="comprobarYactualizar" value="Guardar datos actualizados">		
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
		 <input type='submit' name="salirSinActualizar" value="No actualizar datos"
		       onClick="return confirm('¿Salir sin guardar los campos actualizados')">
									
		<!-- </div>		-->					
		
		<!-- ************************* Fin Botones de formActualizar Socios ***************** -->
		
 </form>
	
 <br /><br />	

</div><!-- de <div id="registro"> --> 
