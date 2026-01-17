<?php
/*--------------- formCambiarCuotasVigentesELTes.php  --------------------------------------------
FICHERO: formCambiarCuotasVigentesELTes.php
VERSION: PHP PHP 7.3.21

DESCRIPCION: Es el formulario donde se muestra el importe y datos actuales de las cuotas vigentes 
para EL para el "tipo de cuota y año elegida", y con un campo para para introducir el nuevo importe 
para ese tipo de cuota y año = (Y+1)   

Se muestra el resultado con número de cambios de cuotas de socios afectados y actualizadas para 
el año siguiente, o mensaje de error.                 	

Tiene unos botones para "Cambiar cuota", y para "Cancelar cambio cuota"

Con llamada a función: cTeserero.php:actualizarCuotasVigentesELTes()
	
LLAMADA: vistas/tesorero/vCambiarCuotasVigentesELTesInc.php y previmanente al hacer clic en 
icono "Modificar" (pluma) del formulario: vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php									

OBSERVACIONES:
----------------------------------------------------------------------------------------------*/
?>

<div id="registro">
 <br />

		<span class="error">
				<?php
				if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['codError']) && $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['codError'] !== '00000') 
				{
							echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
				}
				?>
		</span>
		<br />

		<span class="textoAzu112Left2">
		 Solo se pueden cambiar las cuotas del año <strong>
			<?php echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['ANIOCUOTA']['valorCampo']?></strong>
			previa aprobación por la asamblea en el año anterior<strong> 
			<?php echo ($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['ANIOCUOTA']['valorCampo']-1)?></strong>
			<br /><br />
	  La aplicación no permite cambiar las cuotas del año actual, pues habrá socios/as que ya hayan pagado
		 y daría lugar a que quedasen como deudoras/es, si posteriormente se incrementasen las cuotas de Europa Laica
			<br /><br />
  <strong>NOTA: antes de efectuar el proceso de cambio de cuotas vigentes, es aconsejable hacer una copia de seguridad de la BBDD		</strong>						
	 </span> 
		<br /><br /><br />		
		
 <div id="formLinea">
	
  <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=actualizarCuotasVigentesELTes"	
			     onSubmit="return confirm('¿Actualizar importe cuota?')">				
	
 	
	  <fieldset>	 
				<legend><b>Datos de la cuota</b></legend>	
				<p>
					<label>Año cuota a modificar</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormCuotasVigentesEL[ANIOCUOTA]"
													value='<?php if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['ANIOCUOTA']['valorCampo']))
													{  echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['ANIOCUOTA']['valorCampo'];}
													?>'
													size="35"
													maxlength="100"
						/>
						<br />
					<label>Tipo de cuota a modificar</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormCuotasVigentesEL[CODCUOTA]"
													value='<?php if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['CODCUOTA']['valorCampo']))
													{  echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['CODCUOTA']['valorCampo'];}
													?>'
													size="35"
													maxlength="100"
						/>
						<br />
					<label for="user">Descripción cuota</label> 
						<input type="text"	readonly	
													class="mostrar"							
													name="datosFormCuotasVigentesEL[DESCRIPCIONCUOTA]"
													value='<?php if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['DESCRIPCIONCUOTA']['valorCampo']))
																								{  echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['DESCRIPCIONCUOTA']['valorCampo'];}
																		?>'
													size="50"
													maxlength="100"																	
						/>	
						<br /><br />							
					<label>Importe del año actual para este tipo de cuota (euros)</label> 
						<input type="text" readonly		
													class="mostrar"					      
													name="datosFormCuotasVigentesEL[IMPORTECUOTAANIOEL]"
													value='<?php if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL']['valorCampo']))
													{  echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL']['valorCampo'];}
													?>'
													size="10"
													maxlength="20"
						/>	
					<br /><br />							
					<label>Introduce el nuevo importe para el próximo año para este tipo de cuota (euros)</label> 
						<input type="text"						      
													name="datosFormCuotasVigentesEL[IMPORTECUOTAANIOEL_NUEVO]"
													value='<?php if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['valorCampo']))
													{  echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'];}
													?>'
													size="10"
													maxlength="20"
						/> 
				<span class="error"><strong>
					<?php
							if (isset($datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['errorMensaje']))
							{echo $datosCuotaEL['datosFormCuotasAnioSiguienteEL']['IMPORTECUOTAANIOEL_NUEVO']['errorMensaje'];}
						?></strong>
				</span>					
				<br />
				</p>
			</fieldset>
			<br />		 		
 
	  <input type="submit" name="SiCambiar" value="Cambiar cuota" class="enviar">	
  </form>

  <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=actualizarCuotasVigentesELTes">		
   <input type="submit" name="NoCambiar" value="Cancelar cambio cuota" class="enviar">
  </form>	
		
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




