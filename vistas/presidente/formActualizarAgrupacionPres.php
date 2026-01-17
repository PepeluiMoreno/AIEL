<?php
/*-------------------------------------------------------------------------------------------------
FICHERO: formActualizarAgrupacionPres.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario en el que se muestran los datos de de una agrupación procedentes de la tabla "AGRUPACIONTERRITORIAL" 
para permitir modificar algunos de ellos. 
Los datos CIF, CUENTAAGRUPIBAN, TELFIJOTRABAJO, TELMOV,  se validan previamente 

RECIBE: array $arrDatosAgrupacion con los datos de una agrupación de tabla "AGRUPACIONTERRITORIAL" 


LLAMADA: cPresidente.php:vCuerpoActualizarAgrupacionPres.php()

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/

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


<div id="registro">

		<!--<span class="error"><?php //echo "<br /><br />formListaAgrupacionesPres:arrDatosAgrupacion['datosFormAgrupacion']: "['valorCampo']; print_r($arrDatosAgrupacion['datosFormAgrupacion'])['valorCampo'];?></span>	-->
	<span class="error">
		<?php
			//echo $datSocio['codError'];
			if (isset($arrDatosAgrupacion['codError']) && $arrDatosAgrupacion['codError'] !=='00000')
			{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
			}
			?>	
	</span>		
	
 <br />
	
 <form name="actualizarAgrupacionPres" method="post" class="linea" action="./index.php?controlador=cPresidente&amp;accion=actualizarDatosAgrupacionPres">	
		
		<!-- ******************** Inicio Datos de Agrupación ****************************** -->
	 <fieldset>	 
	  <legend><b>Datos Agrupación</b></legend>	
		 <p>		
						
					<label>Nombre</label> 
						<input type="text" readonly
						       class="mostrar"		
													name="datosFormAgrupacion[NOMAGRUPACION]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['NOMAGRUPACION']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['NOMAGRUPACION']['valorCampo'];}
													?>'
													size="35"
													maxlength="100"
						/>		
						
					<label>CIF</label>
						<input type="text" 	
													name="datosFormAgrupacion[CIF]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CIF']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CIF']['valorCampo'];}
													?>'
													size="11"
													maxlength="15"
						/>	 
						<span class="error">
							<?php
							if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CIF']['errorMensaje']))
							{echo $arrDatosAgrupacion['datosFormAgrupacion']['CIF']['errorMensaje'];}
							?>							
						</span>		
					<br /><br />	
					
					<label>Código Agrupación</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[CODAGRUPACION]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CODAGRUPACION']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CODAGRUPACION']['valorCampo'];}
													?>'
													size="8"
													maxlength="10"
						/>
					<label>Ámbito</label>		
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[AMBITO]"
													value='<?php if ($arrDatosAgrupacion['datosFormAgrupacion']['AMBITO']['valorCampo'])
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['AMBITO']['valorCampo'];}
													?>'
													size="20"
													maxlength="30"
						/>	
					<label>Estado actividad</label> 
						<input type="text" readonly
													class="mostrar"			
													name="datosFormAgrupacion[ESTADO]"
													value='<?php 
																							if (isset($arrDatosAgrupacion['datosFormAgrupacion']['ESTADO']['valorCampo']))
																							{  echo $arrDatosAgrupacion['datosFormAgrupacion']['ESTADO']['valorCampo'];}							 
																				?>'
													size="15"
													maxlength="20"
						/>							
						<br /><br />
					
					<label>Teléfono fijo</label> 
						<input type="text" 
													name="datosFormAgrupacion[TELFIJOTRABAJO]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['TELFIJOTRABAJO']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['TELFIJOTRABAJO']['valorCampo'];}
													?>'
													size="14"
														maxlength="14"
							/>	
						 <span class="error">
								<?php
								if (isset($arrDatosAgrupacion['datosFormAgrupacion']['TELFIJOTRABAJO']['errorMensaje']))
								{echo $arrDatosAgrupacion['datosFormAgrupacion']['TELFIJOTRABAJO']['errorMensaje'];}
								?>							
						 </span>	
							
					<label>Teléfono móvil</label> 
							<input type="text" 	
														name="datosFormAgrupacion[TELMOV]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['TELMOV']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['TELMOV']['valorCampo'];}
														?>'
														size="14"
														maxlength="14"
							/>	 
						 <span class="error">
							 <?php
								if (isset($arrDatosAgrupacion['datosFormAgrupacion']['TELMOV']['errorMensaje']))
								{echo $arrDatosAgrupacion['datosFormAgrupacion']['TELMOV']['errorMensaje'];}
								?>		
       </span>				
						
						<br /><br />	
						
					<label>WEB (formato: www.europalaica.org, www.asturiaslaica.com)</label> 
							<input type="text" 
														name="datosFormAgrupacion[WEB]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['WEB']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['WEB']['valorCampo'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>EmailL</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAIL]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['EMAIL']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['EMAIL']['valorCampo'];}
														?>'
														size="30"
														maxlength="35"
							/>
						<br />			
					<label>Email Coordinación</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILCOORD]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['EMAILCOORD']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['EMAILCOORD']['valorCampo'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>Email Secretaría</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILSECRETARIO]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['EMAILSECRETARIO']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['EMAILSECRETARIO']['valorCampo'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>Email Tesorería</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILTESORERO]"
														value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['EMAILTESORERO']['valorCampo']))
														{  echo $arrDatosAgrupacion['datosFormAgrupacion']['EMAILTESORERO']['valorCampo'];}
														?>'
														size="30"
														maxlength="35"
							/>
						<br />
			</p>
	 </fieldset>
	 <!-- ********************** Fin Datos de Agrupación ******************************* --> 							
	  <br /><br />		

  <!--************ Inicio Datos de Bancos para Cobro Cuotas  *************************-->		
	 <fieldset>

	  <legend><b>Datos de Bancos para Cobro Cuotas</b></legend>
				<p>
     <br />
					<span class="textoRojo9Left"><strong>NOTA: </strong></span>					
					<span class='textoAzu112Left2'>Los siguientes cuentas bancarias IBAN, se mostrarán a los socios/as de esta agrupación, 
					  para el pago de sus cuotas (transferencia o otros medios)
					</span>
					
					<br />	<br />
		   <!--			<label>GESTIONCUOTAS</label> 
						<input type="text" 	
													name="datosFormAgrupacion[GESTIONCUOTAS]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['GESTIONCUOTAS']['valorCampo']))
																			{  echo $arrDatosAgrupacion['datosFormAgrupacion']['GESTIONCUOTAS']['valorCampo'];}
																				?>'
													size="15"
													maxlength="20"
						/>	
					-->		
					
						<label>Gestiona el cobro de la cuatas de Socios/as</label>
									<?php
											$parValorGESTIONCUOTAS = array("ASOCIACION" => "Asociación Europa Laica Estatal e Internacional", "AGRUPACION" => "Agrupación Territorial");
											
						     echo comboLista($parValorGESTIONCUOTAS, "datosFormAgrupacion[GESTIONCUOTAS]", 
											$arrDatosAgrupacion['datosFormAgrupacion']['GESTIONCUOTAS']['valorCampo'], 
											$parValorGESTIONCUOTAS[$arrDatosAgrupacion['datosFormAgrupacion']['GESTIONCUOTAS']['valorCampo']],"ASOCIACION","Asociación Europa Laica Estatal e Internacional");											
									?>
							<br />

					<label>Titular para el cobro de las cuotas</label>		
						<input type="text" 	
													name="datosFormAgrupacion[TITULARCUENTASBANCOS]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['TITULARCUENTASBANCOS']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['TITULARCUENTASBANCOS']['valorCampo'];}
													?>'
													size="30"
													maxlength="35"
						/>	
						
						<br /><br /> 					
					<label>Nombre Banco de CUENTA1</label>
						<input type="text" 	
													name="datosFormAgrupacion[NOMBREIBAN1]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['NOMBREIBAN1']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['NOMBREIBAN1']['valorCampo'];}
													?>'
													size="25"
													maxlength="30"
								/>	
					<label>Cuenta IBAN1</label> 
						<input type="text" 		
													name="datosFormAgrupacion[CUENTAAGRUPIBAN1]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['valorCampo'];}
													?>'
													size="30"
													maxlength="34"
						/>							 
						<span class="error">
							 <?php
								if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['errorMensaje']))
								{echo $arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN1']['errorMensaje'];}
								?>		
      </span>			 

						<br /><br />			
				
					<label>Nombre Banco de CUENTA2</label>
						<input type="text" 	
													name="datosFormAgrupacion[NOMBREIBAN2]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['NOMBREIBAN2']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['NOMBREIBAN2']['valorCampo'];}
													?>'
													size="25"
													maxlength="30"
						/>			
					<label>Cuenta IBAN2</label> 
						<input type="text" 		
													name="datosFormAgrupacion[CUENTAAGRUPIBAN2]"
													value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['valorCampo']))
													{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['valorCampo'];}
													?>'
													size="30"
													maxlength="34"
						/>		
						<span class="error">
							 <?php
								if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['errorMensaje']))
								{echo $arrDatosAgrupacion['datosFormAgrupacion']['CUENTAAGRUPIBAN2']['errorMensaje'];}
								?>		
      </span>			

					<br />
				</p>
	 </fieldset>		
		
	 <!--************* Fin Datos de Datos de Bancos para Cobro Cuotas  ******************-->	
 	<br /><br />

	 <!-- *************************** Inicio  datosFormAgrupacion *********************** --> 	
	 <fieldset>
	  <legend><b>Domicilio</b></legend>
		  <p>	 
			
					<label>País domicilio de la Agrupación</label> 
								<input type="text" readonly
															class="mostrar"			
															name="datosFormAgrupacion[CODPAISDOM]"
															value='<?php 
																									if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CODPAISDOM']['valorCampo']))
																									{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CODPAISDOM']['valorCampo'];}							 
																						?>'
															size="3"
															maxlength="3"
								/>		
						<label>Dirección de la Agrupación</label> 
								<input type="text" 		
															name="datosFormAgrupacion[DIRECCION]"
															value='<?php 
																									if (isset($arrDatosAgrupacion['datosFormAgrupacion']['DIRECCION']['valorCampo']))
																									{  echo $arrDatosAgrupacion['datosFormAgrupacion']['DIRECCION']['valorCampo'];}							 
																						?>'
															size="70"
															maxlength="255"
								/>	
					   <span class="error">
							  <?php
									if (isset($arrDatosAgrupacion['datosFormAgrupacion']['DIRECCION']['errorMensaje']))
									{echo $arrDatosAgrupacion['datosFormAgrupacion']['DIRECCION']['errorMensaje'];}
									?>		
        </span>			
				  <br />				
				 	<label>Código postal</label>	
								<input type="text" 		
															name="datosFormAgrupacion[CP]"
															value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CP']['valorCampo']))
																												{  echo $arrDatosAgrupacion['datosFormAgrupacion']['CP']['valorCampo'];}
																						?>'
															size="6"
															maxlength="10"
								/>	
								<span class="error">
							  <?php
									if (isset($arrDatosAgrupacion['datosFormAgrupacion']['CP']['errorMensaje']))
									{echo $arrDatosAgrupacion['datosFormAgrupacion']['CP']['errorMensaje'];}
									?>		
        </span>							
					
					<label>Localidad</label>	
								<input type="text" 	
															name="datosFormAgrupacion[LOCALIDAD]"
															value='<?php if (isset($arrDatosAgrupacion['datosFormAgrupacion']['LOCALIDAD']['valorCampo']))
																												{  echo $arrDatosAgrupacion['datosFormAgrupacion']['LOCALIDAD']['valorCampo'];}
																						?>'
															size="50"
															maxlength="255"
								/>								
					   <span class="error">
							  <?php
									if (isset($arrDatosAgrupacion['datosFormAgrupacion']['LOCALIDAD']['errorMensaje']))
									{echo $arrDatosAgrupacion['datosFormAgrupacion']['LOCALIDAD']['errorMensaje'];}
									?>		
        </span>		
								
					 <br />		
	  	</p>
	 </fieldset>
		<!-- ******************* Fin datosFormAgrupacion Domicilio *************************** -->		
	 <br />		<br />

  <!--************ Inicio Datos de OBSERVACIONES  **************************************-->		
	 <fieldset>

	  <legend><b>Observaciones</b></legend>
	  <p>
		  <textarea type="text" wrap="hard" name="datosFormAgrupacion[OBSERVACIONES]" 
		          rows="3" cols="80"><?php 
		  if (isset($arrDatosAgrupacion['datosFormAgrupacion']['OBSERVACIONES']))                    
			 {echo htmlspecialchars(stripslashes($arrDatosAgrupacion['datosFormAgrupacion']['OBSERVACIONES']['valorCampo']));}
		  ?></textarea> 			 
	 	</p>
	 </fieldset>		
		
	 <!--************* Fin Datos de OBSERVACIONES  **************************************-->		

		<!-- ********************** Inicio Botones de formActualizarAgrupacionPres *************** --> 
		<span class="error">
			<?php				
				if (isset($arrDatosAgrupacion['codError']) && $arrDatosAgrupacion['codError'] !=='00000')
				{echo "<strong>ERROR AL ACTUALIZAR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";							
				}
				?>	
		</span>
		<br />
		
 	<div align="center">
			<input type="submit" name="comprobarYactualizar" value="Guardar datos actualizados">		
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
		 <input type='submit' name="salirSinActualizar" value="No actualizar datos"
		       onClick="return confirm('¿Salir sin guardar los campos actualizados del formulario?')">	
		</div>							
		<!-- ************************* Fin Botones de formActualizarAgrupacionPres *************** -->
		
 </form>
 
</div>
	