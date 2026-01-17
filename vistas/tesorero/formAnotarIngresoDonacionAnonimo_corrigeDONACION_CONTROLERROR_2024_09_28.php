<?php
/*------------------------------------------------------------------------------------------------------
FICHERO: formAnotarIngresoDonacionSiEncontrado.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante donante ANONIMO 
en el que se añaden los datos de la donación (Donación €, Gastos, Fecha, modo pago, concepto) 
pero ningún dato personal


LLAMADA: vistas/tesorero/vCuerpoAnotarIngresoDonacionAnonimo.php	
y previamente desde cTesorero.php:anotarIngresoDonacionMenu o cTesorero.php:anotarIngresoDonacion()

LLAMA: modelos/libs/comboLista.php

OBSERVACIONES: 

2024-09-28 Por cambio versiones a 10.3.39-MariaDB para evitar error 
'CONTROLERROR' doesn't have a default value,  Se añade input type="hidden"	name="datosFormDonacion[CONTROLERROR]" ....
-------------------------------------------------------------------------------------------------------*/
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

	<!--********************** Inicio formAnotarIngresoDonacionAnonimo ******************* -->  
	
<div id="registro">

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
  En este formulario se anotarán las donaciones realizadas por donantes de los que no se tienen 
		datos personales (Nombre y Apellido al menos).
  <br /><br />Los campos con asterisco (<b>*</b>) son obligatorios	
	</span>
	 <br /><br /><br />

 <!-- ********************** Inicio form *********************************************** -->
 <form name="registrarSocio" method="post" action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacion">
							
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
			
	 	<!-- Se pone aquí para que por defecto se anote como CONTROLERROR ='CORRECTA' si no tiene ningún valor para el campo
		 (en realidad no sería necesario ya que tamaría el valor por defecto para ese campo esá asignado en la BBDD como DONACION.CONTROLERROR = 'CORRECTA' )
				por si más adelante se quisiera poder anotar donaciones a agrupcaiones concretas
			-->
			<input type="hidden"	name="datosFormDonacion[CONTROLERROR]" 
										value="<?php  if (isset($datosAnotarDonacion['datosFormDonacion']['CONTROLERROR']['valorCampo']) && !empty($datosAnotarDonacion['datosFormDonacion']['CONTROLERROR']['valorCampo']))
																								{ echo $datosAnotarDonacion['datosFormDonacion']['CONTROLERROR']['valorCampo'];	}
																								else
																								{ echo $datosAnotarDonacion['datosFormDonacion']['CONTROLERROR']['valorCampo'] = 'CORRECTA';	}//Estatal	
																								?>"
			/>			
			
	
			
  <!-- ********************** Inicio datos de  TIPODONANTE ***************************** -->	 	
		
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
				<label>Nº documento (NIF, NIE, pasaporte, otros)</label> 
						<input type="text" readonly class='mostrar' 
													name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
													value="ANONIMO"												
													size="12"  maxlength="20"
						/>																		
				<br />
		
				<label>Nombre</label> 
					<input type="text" readonly class="mostrar"			
												name="datosFormDonacion[NOM]"
												value="ANONIMO"
												size="35"
												maxlength="100"
					/>	 
		
				<br />
				<label>Apellido primero</label> 
						<input type="text" readonly class="mostrar"	
													name="datosFormDonacion[APE1]"
													value="ANONIMO"
													size="35"
													maxlength="100"
						/>	 

				<br />	
				<label>Apellido segundo</label> 
					<input type="text" readonly class="mostrar"				
												name="datosFormDonacion[APE2]"
												value="ANONIMO"
												size="35"
												maxlength="100"
					/>	 

			 <br /> 
			</p>
	 </fieldset>	
	 <!-- ********************** Fin datos de identificación MIEMBRO-DONANTE*************** --> 			
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
								
				<!-- ************************ Inicio [FECHAPAGO] *********************** -->
				<br /><br />
				<label>*Fecha del ingreso realizado por el donante (dd/mm/aaaa)</label> 
				<?php    	
						$parValorDia["00"]="día"; 
					for ($d=1; $d<=31; $d++) 
					{if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
						else {$valor="$d";}
						$parValorDia[$valor]=$valor;
					}
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
				<!-- ************************ Fin [FECHAPAGO] ************************* -->		
				<br />
					<!-- ************************ Incio anotación por el tesorero ********** -->	
				<label>Fecha de la anotación por el tesorero <b><?php echo date("d/m/Y") ?> </b></label> 		
				
				<!-- ************************ Fin anotación por el tesorero ************* -->	
				<br /><br />
				
				<!-- ************************ Inicio [MODOINGRESO] ************************* -->
				<label>Modo de pago</label> 
					<?php 			
					$parValorModoIngreso = array("SIN-DATOS"=>"Sin datos","DOMICILIADA"=>"Domiciliada","TRANSFERENCIA"=>"Transferencia",
																																		"TARJETA"=>"Tarjeta","CHEQUE"=>"Cheque","METALICO"=>"Metálico","PAYPAL"=>"PayPal");

					if (!isset($datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']))
					{ $datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'] = 'SIN-DATOS'; }//evita Notice		
			
					echo comboLista($parValorModoIngreso,"datosFormDonacion[MODOINGRESO]",
																					$datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'],
																					$parValorModoIngreso[$datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']],"","");	
				?>
				<br />	
				<!-- ************************ Fin datSocio[MODOINGRESO] ************************* -->			

		  <!--************ Inicio Datos de datosFormDonacion[CONCEPTO] ***********-->

	   <label><b>Concepto de la donación</b></label>

				<?php 			
					//$parValorConcepto=array("GENERAL"=>"GENERAL","COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL"=>"Costas Medalla Virgen Mérito Policial",..."mas=>"más");
					//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
																				
					echo comboLista($parValoresDonacionConceptos['lista'], "datosFormDonacion[CONCEPTO]", 
																					$parValoresDonacionConceptos['valorDefecto'], $parValoresDonacionConceptos['descDefecto'], "","");				
				?>
    <br />
	 	 <!--************ Fin Datos de datosFormDonacion[CONCEPTO] **************-->		
		
		 </p>
	 </fieldset>

		<!-- ********************* Fin Datos donación  *************************************** -->	
  	 <br />	 
				
	 <!--************ Inicio Datos de datosFormDonacion[OBSERVACIONES] ******************** -->
		
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
		<!--************ Fin Datos de datosFormDonacion[OBSERVACIONES] *********************** -->
 
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
	
	<!--********************** Fin formAnotarIngresoDonacionAnonimo **********************-->  
</div>



