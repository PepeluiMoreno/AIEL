<?php
/*--------------------------------------------------------------------------------------------------------------
FICHERO: formAnotarIngresoDonacionNoEncontrado.php
VERSION: PHP 7.3.21

DESCRIPCION:
Formulario para el anotar el ingreso de una donación realizada por donante identificado 
al menos con Nombre y Apellido 1, pero NO registrado previamente como donante y tampoco es socio 

Se escriben los datos personales que se tengan y se añaden los datos de la donación

Para entrar en este formulario hay dos casos que se pueden dar al anotar una donación: 

- Donante nuevo e identificado (no socio), no registrado previamente como donante será:vAnotarIngresoDonacionNoEncontradoInc()

-	Se ha buscado en "Donante ya identificado" por Número de docuomento (NIF, NIE, pasaporte, otros), o EMAIL, 
  pero no se ha encontado ningún dato en las tablas MIEMBRO (caso de que sea socio) o en la tabla DONACION, 
		y entonces se le trata como 	"Donante nuevo e identificado (no socio)"
		
LLAMADA: vistas/tesorero/vCuerpoAnotarIngresoDonacionNoEncontrado.php
LLAMA: modelos/libs/comboLista.php

OBSERVACIONES:
----------------------------------------------------------------------------------------------------------------*/

require_once './modelos/libs/comboLista.php';
?>

<script type="text/javascript">
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
</script> 

<!-- ********************** Inicio formAnotarIngresoDonacionNOEncontrado *************** -->  
<div id="registro">
 <br /> 
	<span class="error">
			<?php
			if (isset($datosAnotarDonacion['codError']) && $datosAnotarDonacion['codError'] !== '00000') 
			{
						echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
			}
			?>
	</span>		
	<br />
	<span class="textoAzu112Left2">
		<?php	//el texto podría ir en  modelo o controlador
			 if (isset($datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo']) && 
				    $datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo'] == 'NO'
							)
				{echo "No se han encontrado datos para este donante con los datos introducidos: no será socio/a y no habrá realizado donaciones previas por lo que no hay datos. 
							  	<br />Comprueba que no hayas cometido algún error al introducir los datos de búsqueda (NIF, NIE, Pasaporte, o email)
							  	<br /><br />
								  En ese caso vuelve a introducirlos de nuevo en '<b>-Anotar donación</b>'			 
										<br /><br />";
				}
   ?>
			En este formulario se anotará la donación realizada por un donante, del que se tiene datos personales (Nombre y Apellido al menos), que no es socio/a 
   y que no ha realizado donaciones previas 
   <br /><br />Los campos con asterisco (<b>*</b>) son obligatorios	
	</span>
 <br /><br /><br />

 <!-- ********************** Inicio form *********************************************** -->
 <form name="registrarSocio" method="post" action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacion">
   
			<input type="hidden" readonly class="mostrar"	name="datosFormDonacion[encontrado]" 
											value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo']) && !empty($datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo']))																							
											             {echo $datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo'];}?>" 
				/>						
			<!-- Se pone aquí para que por defecto se anote como DONACION, realizada a la agrupación CODAGRUPACION='00000000' = Estatal
			     por si más adelante se quisiera poder anotar donaciones a agrupcaiones concretas--
			-->
			<input type="hidden"	name="datosFormDonacion[CODAGRUPACION]" 
										value="<?php  if (isset($datosAnotarDonacion['datosFormDonacion']['CODAGRUPACION']['valorCampo']) && !empty($datosAnotarDonacion['datosFormDonacion']['CODAGRUPACION']['valorCampo']))
																								{ echo $datosAnotarDonacion['datosFormDonacion']['CODAGRUPACION']['valorCampo'];	}
																								else
																								{ echo $datosAnotarDonacion['datosFormDonacion']['CODAGRUPACION']['valorCampo'] = '00000000';	}//Estatal	
																								?>"
			/>	
									
  <!-- ********************** Inicio datos de  TIPODONANTE *************************** -->	 	 
	 <fieldset>		
			<legend><b>Tipo de donante</b></legend>
			<p>	
				<label>Tipo donante</label>
				
					<input type="text" readonly class="mostrar"	name="datosFormDonacion[TIPODONANTE]" 
												value="<?php echo $datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']?>" 
												size="30" maxlength="60" />		

			</p>
	 </fieldset>

	 <!-- ********************** Fin datos de TIPODONANTE ********************************* --> 
  	<br />
  <!-- ********************** Inicio datos de identificación MIEMBRO-DONANTE *********** --> 	
		<fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		 <p>
				<label>Si no conoces el Nº de documento puedes dejarlo en blanco</label>	
				<br />	
				<!-- *********** Inicio TIPODOCUMENTOMIEMBRO,NUMDOCUMENTOMIEMBRO,CODPAISDOC ****** --> 							
				<label>Tipo documento</label>	
				<?php
					$parValorTipoDoc=array("NIF"=>"NIF","NIE"=>"NIE","Pasaporte"=>"Pasaporte","OTROS"=>"Otros");			

					if (!isset($datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
					{ $datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = 'NIF'; }//evita Notice		
							
						echo comboLista($parValorTipoDoc,"datosFormDonacion[TIPODOCUMENTOMIEMBRO]",
																				$datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
																				$parValorTipoDoc[$datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']],"NIF","NIF");																	
					?>
					
				<label>Nº documento</label> 
						<input type="text"
													name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
													size="12"
													maxlength="20"									
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
																			{echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
							?>"
						/>
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];}
					?>
				</span>																			
				<br />
				<label>País documento</label> 
					<?php
					
							if (!isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']))
							{ $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'] = 'ES'; /*$parValorComboPaisMiembro['descDefecto']="España";*/}//evita Notice	
					
							echo comboLista($parValorComboPaisMiembro['lista'], "datosFormDonacion[CODPAISDOC]",
																							$datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'],
																							$parValorComboPaisMiembro['lista'][$datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']],
																							"ES","España");
					?> 
					<span class="error">
					<?php
						if (isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje']))
						{echo $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje'];}
					?>
					</span>		
				<!-- **************** Fin TIPODOCUMENTOMIEMBRO,NUMDOCUMENTOMIEMBRO,CODPAISDOC ***** --> 	
				<br /><br />	
				
				<label>*Sexo</label>	
						<span class="error">
						<?php
						if (isset($datosAnotarDonacion['datosFormDonacion']['SEXO']['errorMensaje']) && 
										!empty($datosAnotarDonacion['datosFormDonacion']['SEXO']['errorMensaje'])
									)
						{echo $datosAnotarDonacion['datosFormDonacion']['SEXO']['errorMensaje'];}
						?>
					</span>	
						<input type="radio"
													name="datosFormDonacion[SEXO]"
													value='H' 
													<?php if ($datosAnotarDonacion['datosFormDonacion']['SEXO']['valorCampo']=='H')
													{  echo " checked";}
													?>
						/><label>Hombre</label>
						<input type="radio"
													name="datosFormDonacion[SEXO]"
													value='M'
													<?php if ($datosAnotarDonacion['datosFormDonacion']['SEXO']['valorCampo']=='M')
													{  echo " checked";}
													?>						 
						/><label>Mujer</label>
								
					<br />    
					<label>*Nombre</label> 
						<input type="text"																
													name="datosFormDonacion[NOM]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['NOM']['valorCampo']))
																			{  echo $datosAnotarDonacion['datosFormDonacion']['NOM']['valorCampo'];}
																			?>"
													size="35"
													maxlength="100"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['NOM']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['NOM']['errorMensaje'];}
					?>
				</span>	
				
					<br />
					
				<label>*Apellido primero</label> 
						<input type="text"
													name="datosFormDonacion[APE1]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']))
													{  echo $datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo'];}
													?>"
													size="35"
													maxlength="100"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['APE1']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['APE1']['errorMensaje'];}
					?>
				</span>	
				<br />	
					<label>Apellido segundo</label> 
						<input type="text"
													name="datosFormDonacion[APE2]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo']))
																			{  echo $datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo'];}
																				?>"
													size="35"
													maxlength="100"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['APE2']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['APE2']['errorMensaje'];}
					?>
				</span>	
				
				<br /> 

				<label>email</label>
						<input type="text"
													name="datosFormDonacion[EMAIL]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo']))
																			{  echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo'];}
																			?>"
													size="60"
													maxlength="200"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje'];}
					?>
				</span>	
					<br />	

					<label>Teléfono fijo</label> 
						<input type="text"
													name="datosFormDonacion[TELFIJOCASA]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo']))
																			{  echo $datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo'];}
																			?>"
													size="14"
													maxlength="14"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['errorMensaje'];}
					?>
				</span>
					<br />		
				<label>Teléfono móvil</label> 
						<input type="text"
													name="datosFormDonacion[TELMOVIL]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo']))
																			{  echo $datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo'];}
																			?>"
													size="14"
													maxlength="14"
						/>	 
				<span class="error">
					<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['errorMensaje'];}
					?>
				</span>
				
	 	</p>
	 </fieldset>	
	 <!-- ********************** Fin datos de identificación MIEMBRO-DONANTE ************** --> 			
		<br />	
	 
		<!-- ********************* Inicio Datos donación  ************************************ -->

 	<fieldset>
	  <legend><b>Datos de la donación</b></legend>
			<p>
				<label>*Donación (euros)</label>
						<input type="text"		        
													name="datosFormDonacion[IMPORTEDONACION]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo']))
																										{  echo $datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo'];}
																				?>"
													size="12"
													maxlength="30"
							/>			
				<span class="error">
				<?php
						if (isset($datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['errorMensaje']))
							{echo $datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['errorMensaje'];}
				?>
				</span>
			
				<br />
				<label>*Gastos al abonar la cuota si los hubiese (cobrados a EL por PayPal o la entidad bancaria)</label>
						<input type="text"		        
													name="datosFormDonacion[GASTOSDONACION]"
													value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo']))
																										{  echo $datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo'];}
																										else
																										{ echo '0.00';}	
																				?>"
													size="12"
													maxlength="30"
							/>			
				<span class="error">
				<?php
						if (isset($datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['errorMensaje']))
							{echo $datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['errorMensaje'];}
				?>
				</span>
			
				<br /><br />
				
				<!-- ************************ Inicio [FECHAPAGO] *********************************** -->
				<label>*Fecha del ingreso realizado por el donante (dd/mm/aaaa)</label> 
				<?php    	
				$parValorDia["00"]="día"; 
				for ($d=1;$d<=31;$d++) 
				{if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
					else {$valor="$d";}
					$parValorDia[$valor]=$valor;
				}			
				//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
				if (!isset($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo']))
				{ $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo'] = '00'; }//evita Notice			
			
				echo comboLista($parValorDia,"datosFormDonacion[FECHAINGRESO][dia]",
																				$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo'],
																				$parValorDia[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo']],"","");	
													
				$parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
				"07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
							
				if (!isset($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo']))
				{ $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo'] = '00'; }		
			
				echo comboLista($parValorMes,"datosFormDonacion[FECHAINGRESO][mes]",
																				$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo'],
																				$parValorMes[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo']],"","");

				$parValorAnio["0000"]="año"; 		 
				for ($a=date("Y")-1; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;} 
				
				if (!isset($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo']))
				{ $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'] = '0000'; }
			
				echo comboLista($parValorAnio,"datosFormDonacion[FECHAINGRESO][anio]",
																				$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'],
																				$parValorAnio[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo']],"","");	
				?>	
				<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['errorMensaje'];}
				?>
				</span>	
				<!-- ************************ Fin [FECHAPAGO] ************************************** -->		
				<br />
				
				<!-- ************************ Incio anotación por el tesorero ********************* -->	
				<label>Fecha de la anotación por el tesorero <b><?php echo date("d/m/Y") ?> </b></label> 		
				
				<!-- ************************ Fin anotación por el tesorero *********************** -->		
				<br /><br />
				
				<!-- ************************ Inicio [MODOINGRESO] ******************************** -->
				<label>Modo de pago</label> 
				<?php 			
				$parValorModoIngreso=array("SIN-DATOS"=>"Sin datos","DOMICILIADA"=>"Domiciliada","TRANSFERENCIA"=>"Transferencia",
																															"TARJETA"=>"Tarjeta","CHEQUE"=>"Cheque","METALICO"=>"Metálico","PAYPAL"=>"PayPal");
						//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
			
			
				if (!isset($datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']))
				{ $datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'] = 'SIN-DATOS'; }//evita Notice		
							
				echo comboLista($parValorModoIngreso,"datosFormDonacion[MODOINGRESO]",
																				$datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'],
																				$parValorModoIngreso[$datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']],"","");	
				?>
				<!-- ************************ Fin datSocio[MODOINGRESO] *************************** -->					
					<br /><br />		
					
				<!--************ Inicio Datos de datosFormDonacion[CONCEPTO] ********************** -->

				<label><b>Concepto de la donación</b> </label>

				<?php 			
					//$parValorConcepto=array("GENERAL"=>"GENERAL","COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL"=>"Costas Medalla Virgen Mérito Policial",..."mas=>"más");
					//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
																				
					echo comboLista($parValoresDonacionConceptos['lista'], "datosFormDonacion[CONCEPTO]", 
																					$parValoresDonacionConceptos['valorDefecto'], $parValoresDonacionConceptos['descDefecto'], "","");				
				?>
				<!--************ Fin Datos de datosFormDonacion[CONCEPTO] ************************* -->		
					<br />	
					
			</p>
	 </fieldset> 
		
		<!-- ********************* Inicio Datos donación  ************************************ -->
		
  <br /><br />
	 <!-- ************ Inicio Datos de datosFormDonacion[OBSERVACIONES] ******************* -->
		
	 <fieldset>
	  <legend><b>Observaciones del tesorero</b></legend>
			<p>
			<textarea id='OBSERVACIONES' onKeyPress="limitarTextoArea(2000,'OBSERVACIONES');"	
			class="textoAzul8Left" name="datosFormDonacion[OBSERVACIONES]" rows="10" cols="80"><?php 
					if (isset($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']))                    
				{echo htmlspecialchars(stripslashes($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']));}
			?></textarea> 			 
			</p>
	 </fieldset>
		<!-- ************ Fin Datos de datosFormDonacion[OBSERVACIONES] ********************** -->
	
		<span class="error">
			<?php
			if (isset($datosAnotarDonacion['codError']) && $datosAnotarDonacion['codError'] !== '00000') 
			{
						echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
			}
			?>
	 </span>		
  <br />
		
  <div align="center">
    <input type="submit" name="siGuardarDatosDonacion" value="Guardar datos de la donación" class="enviar" />
			&nbsp;		&nbsp;		&nbsp;
			<input type="submit" name="salirDonacion" 
			 onClick="return confirm('¿Salir de donación sin guardar datos?')"
			 value='No guardar los datos' />
	 </div>
		
 </form> 
	<!-- ********************** Fin formAnotarIngresoDonacionNOEncontrado ******************* -->
</div>



