<?php
/*-------------------------------------------------------------------------------------------------
FICHERO: formMostrarDatosAgrupacionPres.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario se muestran los datos de una AGRUPACION TERRITORIAL procedentes de la 
tabla "AGRUPACIONTERRITORIAL". 

RECIBE: array "$arrDatosAgrupacion" con los datos de la agrupación

LLAMADA: vistas/presidente/vCuerpoMostrarDatosAgrupacionPres.php y previamente desde 
cPresidente.php:mostrarDatosAgrupacionPres()

LLAMA: vistas/presidente/formMostrarDatosAgrupacionPres.php

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">
 <br />
	
		<!-- ******************** Inicio Datos de Agrupación ****************************** -->
	 <fieldset>	 
	  <legend><b>Datos Agrupación</b></legend>	
		 <p>		
		
					<label>Nombre</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[NOMAGRUPACION]"
													value='<?php if (isset($arrDatosAgrupacion['NOMAGRUPACION']))
													{  echo $arrDatosAgrupacion['NOMAGRUPACION'];}
													?>'
													size="35"
													maxlength="100"
						/>
					<label>CIF</label>
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[CIF]"
													value='<?php if (isset($arrDatosAgrupacion['CIF']))
													{  echo $arrDatosAgrupacion['CIF'];}
													?>'
													size="11"
													maxlength="15"
						/>	 
					<label>Código Agrupación</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[CODAGRUPACION]"
													value='<?php if (isset($arrDatosAgrupacion['CODAGRUPACION']))
													{  echo $arrDatosAgrupacion['CODAGRUPACION'];}
													?>'
													size="8"
													maxlength="10"
						/>
						<br />
						
					<label>Ámbito</label>		
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[AMBITO]"
													value='<?php if ($arrDatosAgrupacion['AMBITO'])
													{  echo $arrDatosAgrupacion['AMBITO'];}
													?>'
													size="20"
													maxlength="30"
						/>	
					<label>Estado actividad</label> 
						<input type="text" readonly
													class="mostrar"			
													name="datosFormAgrupacion[ESTADO]"
													value='<?php 
																							if (isset($arrDatosAgrupacion['ESTADO']))
																							{  echo $arrDatosAgrupacion['ESTADO'];}							 
																				?>'
													size="15"
													maxlength="20"
						/>							
						<br /><br />
					
					<label>Teléfono fijo</label> 
						<input type="text" readonly
													class="mostrar"	
													name="datosFormAgrupacion[TELFIJOTRABAJO]"
													value='<?php if (isset($arrDatosAgrupacion['TELFIJOTRABAJO']))
													{  echo $arrDatosAgrupacion['TELFIJOTRABAJO'];}
													?>'
													size="14"
														maxlength="14"
							/>	
					<label>Teléfono móvil</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[TELMOVIL]"
														value='<?php if (isset($arrDatosAgrupacion['TELMOV']))
														{  echo $arrDatosAgrupacion['TELMOV'];}
														?>'
														size="14"
														maxlength="14"
							/>	 
						<br /><br />	
					<label>WEB</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[WEB]"
														value='<?php if (isset($arrDatosAgrupacion['WEB']))
														{  echo $arrDatosAgrupacion['WEB'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>Email</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAIL]"
														value='<?php if (isset($arrDatosAgrupacion['EMAIL']))
														{  echo $arrDatosAgrupacion['EMAIL'];}
														?>'
														size="30"
														maxlength="35"
							/>
						<br />			
					<label>Email Coordinación</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILCOORD]"
														value='<?php if (isset($arrDatosAgrupacion['EMAILCOORD']))
														{  echo $arrDatosAgrupacion['EMAILCOORD'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>Email Secretaría</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILSECRETARIO]"
														value='<?php if (isset($arrDatosAgrupacion['EMAILSECRETARIO']))
														{  echo $arrDatosAgrupacion['EMAILSECRETARIO'];}
														?>'
														size="30"
														maxlength="35"
							/>
					<label>Email Tesorería</label> 
							<input type="text" readonly
														class="mostrar"		
														name="datosFormAgrupacion[EMAILTESORERO]"
														value='<?php if (isset($arrDatosAgrupacion['EMAILTESORERO']))
														{  echo $arrDatosAgrupacion['EMAILTESORERO'];}
														?>'
														size="30"
														maxlength="35"
							/>
						<br />
			</p>
	 </fieldset>
	 <!-- ********************** Fin Datos de Agrupación ******************************* --> 							
	  <br />
			
  <!--************ Inicio Datos de Bancos para Cobro Cuotas  *************************-->		
	 <fieldset>

	  <legend><b>Datos de Bancos para Cobro Cuotas</b></legend>
				<p>
					<label>Gestiona el cobro de la cuatas de Socios/as</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[GESTIONCUOTAS]"
													value='<?php if (isset($arrDatosAgrupacion['GESTIONCUOTAS']))
																			{  echo $arrDatosAgrupacion['GESTIONCUOTAS'];}
																				?>'
													size="15"
													maxlength="20"
						/>	 

					<label>Titular para el cobro de las cuotas</label>		
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[TITULARCUENTASBANCOS]"
													value='<?php if (isset($arrDatosAgrupacion['TITULARCUENTASBANCOS']))
													{  echo $arrDatosAgrupacion['TITULARCUENTASBANCOS'];}
													?>'
													size="30"
													maxlength="35"
						/>	
						
						<br /><br /> 					
					<label>Nombre Banco de CUENTA1</label>
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[NOMBREIBAN1]"
													value='<?php if (isset($arrDatosAgrupacion['NOMBREIBAN1']))
													{  echo $arrDatosAgrupacion['NOMBREIBAN1'];}
													?>'
													size="25"
													maxlength="30"
								/>	
					<label>Cuenta IBAN1</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[CUENTAAGRUPIBAN1]"
													value='<?php if (isset($arrDatosAgrupacion['CUENTAAGRUPIBAN1']))
													{  echo $arrDatosAgrupacion['CUENTAAGRUPIBAN1'];}
													?>'
													size="30"
													maxlength="34"
						/>	 

						<br /><br />			
				
					<label>Nombre Banco de CUENTA2</label>
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[NOMBREIBAN2]"
													value='<?php if (isset($arrDatosAgrupacion['NOMBREIBAN2']))
													{  echo $arrDatosAgrupacion['NOMBREIBAN2'];}
													?>'
													size="25"
													maxlength="30"
						/>			
					<label>Cuenta IBAN2</label> 
						<input type="text" readonly
													class="mostrar"		
													name="datosFormAgrupacion[CUENTAAGRUPIBAN2]"
													value='<?php if (isset($arrDatosAgrupacion['CUENTAAGRUPIBAN2']) )
													{  echo $arrDatosAgrupacion['CUENTAAGRUPIBAN2'];}
													?>'
													size="30"
													maxlength="34"
						/>					

					<br />	 
				</p>
	 </fieldset>		
		
	 <!--************* Fin Datos de Datos de Bancos para Cobro Cuotas  ******************-->	
			
	 <br />

	 <!-- *************************** Inicio  datosFormAgrupacion *********************** --> 	
	 <fieldset>
	  <legend><b>Domicilio</b></legend>
		  <p>	 
			
					<label>País domicilio de la Agrupación</label> 
								<input type="text" readonly
															class="mostrar"			
															name="datosFormAgrupacion[CODPAISDOM]"
															value='<?php 
																									if (isset($arrDatosAgrupacion['CODPAISDOM']))
																									{  echo $arrDatosAgrupacion['CODPAISDOM'];}							 
																						?>'
															size="3"
															maxlength="3"
								/>		
						<label>Dirección de la Agrupación</label> 
								<input type="text" readonly
															class="mostrar"			
															name="datosFormAgrupacion[DIRECCION]"
															value='<?php 
																									if (isset($arrDatosAgrupacion['DIRECCION']))
																									{  echo $arrDatosAgrupacion['DIRECCION'];}							 
																						?>'
															size="70"
															maxlength="255"
								/>		
				 <br />
					
					<label>Código postal</label>	
								<input type="text" readonly
															class="mostrar"			
															name="datosFormAgrupacion[CP]"
															value='<?php if (isset($arrDatosAgrupacion['CP']))
																												{  echo $arrDatosAgrupacion['CP'];}
																						?>'
															size="6"
															maxlength="10"
								/>		
					<label>Localidad</label>	
								<input type="text" readonly
															class="mostrar"			
															name="datosFormAgrupacion[LOCALIDAD]"
															value='<?php if (isset($arrDatosAgrupacion['LOCALIDAD']))
																												{  echo $arrDatosAgrupacion['LOCALIDAD'];}
																						?>'
															size="50"
															maxlength="255"
								/>
										
	      <br /> 

			 </p>
	 </fieldset>

	 <!-- ******************* Fin datosFormAgrupacion Domicilio *************************** -->		
		
	<br />
	
<!--************ Inicio Datos de OBSERVACIONES  **************************************-->		
	 <fieldset>

	  <legend><b>Observaciones</b></legend>
	  <p>
		  <textarea type="text" readonly class="mostrar1" wrap="hard" name="datosFormAgrupacion[OBSERVACIONES]" 
		          rows="3" cols="80"><?php 
		  if (isset($arrDatosAgrupacion['OBSERVACIONES']))                    
			 {echo htmlspecialchars(stripslashes($arrDatosAgrupacion['OBSERVACIONES']));}
		  ?></textarea> 			 
	 	</p>
	 </fieldset>		
		
	 <!--************* Fin Datos de OBSERVACIONES  **************************************-->		

</div>
	