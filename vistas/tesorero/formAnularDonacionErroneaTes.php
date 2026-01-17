<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formAnularDonacionErroneaTes.php
VERSION: probada PHP 7.3.21	

DESCRIPCION:
Formulario sólo para casos de error: Se anulan  algunos campos de la fila correspondiente a una donación
previa anotada en la tabla DONACION, ya que es un error o se ha introducido duplicada.

El formulario muestra los datos de una donación y preguntar si se quiere anular o no esa donación

El tesoreo puede introducir comentarios en el campo OBSERVACIONES, pero no valida el contenido del campo

LLAMADA: vistas/tesorero/vCuerpoAnularDonacionErroneaTes.php

OBSERVACIONES: 

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso mas adelente añadir una nueva tabla CONCEPTO_DONACION.
--------------------------------------------------------------------------------------------------*/

?>

<div id="registro">
	<!-- ****************** Inicio  formAnularDonacionErroneaTes ***************** -->
	
  <br />
	<span class="textoAzu112Left2">	
	En caso de un error en una donación anotada previamente, se puede modificar (Acción "Modifica") si es un error 
	en algún campo concreto, o anular esa donación si es una equivocación de anotación sobre la persona donante, o una donación duplicada

	<br /> <br /> <br /> 
 Los campos con asterisco (<b>*</b>) son obligatorios	 
 </span> 				
 <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=anularDonacionErroneaTes">			
			
			
			<input type="hidden"	name="datosFormDonacion[CODDONACION]"
          value='<?php echo $datosDonacion['datosFormDonacion']['CODDONACION']['valorCampo']; ?>'
			/>									

  <!-- ********************** Inicio tipo DONANTE **************************** -->	 			
	 <fieldset>	
	 <legend><b>Tipo de donante</b></legend>
		<p>	
		<label>Tipo donante</label>
					<?php	
				/*			echo "<span class='textoAzu112Left2'>Tipo: ".$datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']. 
				"</span>";
				*/?>
					 <input type="text" readonly class="mostrar"	name="datosFormDonacion[TIPODONANTE]" 
						 value='<?php echo $datosDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']?>' 
							size="40" maxlength="40"/>	 
	
		</p>
	 </fieldset>
		<br />	
  <!-- ********************** Fin tipo DONANTE ********************..******** -->
	
	
  <!-- ********************** Inicio datos de personales DONANTE **************************** -->	 	
		<fieldset>	 
	 <legend><b>Datos personales</b></legend>	
		<p>
		<label>Tipo documento</label>
				<input type="text" readonly class="mostrar"	
				       name="datosFormDonacion[TIPODOCUMENTOMIEMBRO]"
           value='<?php if (isset($datosDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
	                {  echo $datosDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']; }
																?>'	
											size="12" maxlength="20" 
				       value="<?php {echo $datosDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}?>"
					/>
   <label>Nº documento</label> 
	    <input type="text"
					       readonly class='mostrar'
	           name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
            value='<?php if (isset($datosDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
	             {  echo $datosDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']; }
													?>'																	
	           size="12"
	           maxlength="20"					
	    />																		
		  <label>País documento</label> 					 
							<input type="text" readonly class="mostrar"	
							       name="datosFormDonacion[CODPAISDOC]"
              value='<?php if (isset($datosDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']))
	             {  echo $datosDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']; }
													?>'																		
								      size="12" maxlength="20"
					/>					
		 <br /><br />	
		 <label>Sexo</label>	
	    <input type="text" readonly class="mostrar"	
	           name="datosFormDonacion[SEXO]"
	           value="<?php echo $datosDonacion['datosFormDonacion']['SEXO']['valorCampo'];?>"
												size="2"
	           maxlength="2"
					 />								
	   <label>Nombre</label> 
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[NOM]"
	           value="<?php echo $datosDonacion['datosFormDonacion']['NOM']['valorCampo'];?>"
	           size="35"
	           maxlength="100"
	    />	 
		 <label>Apellido primero</label> 
				 <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[APE1]"
	           value="<?php echo $datosDonacion['datosFormDonacion']['APE1']['valorCampo'];?>"
	           size="35"
	           maxlength="100"
	    />	
	   <label>Apellido segundo</label> <!--no obligatorio pero se valida si existe-->
				 <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[APE2]"
            value='<?php if (isset($datosDonacion['datosFormDonacion']['APE2']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['APE2']['valorCampo']; }
 										?>'												
	           size="35"
	           maxlength="100"
	    />	
			<br /> 

		 <label>email</label>
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[EMAIL]"
            value='<?php if (isset($datosDonacion['datosFormDonacion']['EMAIL']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['EMAIL']['valorCampo']; }
 										?>'
	           size="60"
	           maxlength="200"
	    />	
	
		  <br />	

	   <label>Teléfono fijo</label> 
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[TELFIJOCASA]"
	           value='<?php if (isset($datosDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo'];}
	           ?>'
	           size="14"
	           maxlength="14"
	    />	 

		 <label>Teléfono móvil</label> 
     <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[TELMOVIL]"
	           value='<?php if (isset($datosDonacion['datosFormDonacion']['TELMOVIL']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['TELMOVIL']['valorCampo'];}
	           ?>'
	           size="14"
	           maxlength="14"
	    />
   <br />						
		</p>
	 </fieldset>	
	 <!-- ********************** Fin datos de personales DONANTE ******************************* --> 	
		<br />	
		<!-- ****************** Inicio datos económicos de la donación *************************** -->
	 <fieldset>
   <legend><b>Datos econónicos</b></legend>			
		 <p> 
			<label>Importe ingreso donación:</label> 
	    <input type="text"	readonly class="mostrar"		        
	           name="datosFormDonacion[IMPORTEDONACION]"
	           value='<?php if (isset($datosDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo']))
	                        {  echo $datosDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo'];}
	                  ?>'
	           size="12"
	           maxlength="30"
	     />					
			<label>Gastos por la donación:</label> 
    	<input type="text"	readonly class="mostrar"		      
							name="datosFormDonacion[GASTOSDONACION]"
							value="<?php if (isset($datosDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo']))
																				{  echo $datosDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo'];}
																				else
																				{ echo '0.00';}	
														?>"
							size="12"
							maxlength="30"
	     />			
			<label>Modo ingreso:</label> 
					<input type="text"	readonly class="mostrar"		      
							name="datosFormDonacion[MODOINGRESO]"
							value="<?php if (isset($datosDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']))
																				{  echo $datosDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'];}
														?>"
							size="12"
							maxlength="30"	
     />							
				<br />	
			<label>Fecha ingreso:</label> 
					<input type="text"	readonly class="mostrar"		      
							name="datosFormDonacion[FECHAINGRESO]"
							value="<?php if (isset($datosDonacion['datosFormDonacion']['FECHAINGRESO']['valorCampo']))
																				{  echo $datosDonacion['datosFormDonacion']['FECHAINGRESO']['valorCampo'];}
														?>"
							size="12"
							maxlength="12"
	     />			
			<label>Fecha anotación última:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['FECHAANOTACION']['valorCampo'];
      ?>					
					</span>
					<br />
			<label>Concepto donación:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['CONCEPTO']['valorCampo'];
      ?>					
					</span>
				<br />					
				
			<label>Observaciones:</label> 
					<span class="mostrar">
      <?php				
									if (isset($datosDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']))
									{  echo $datosDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo'];}
      ?>					
					</span>
					<br />	
		</p>	
  </fieldset>
 	<br />
	 <!-- ********************** Fin datos económicos de la donación ****************************** --> 		
				
	 
		<!-- ********************* Inicio Datos donación  ***************************************** -->
		
		<!--*** Inicio AGRUPACION por ahora será siempre '00000000' Europa Laica Estatal, 
		    dejo esto por si más adelante se cambiase **********************************		
		<fieldset>
	  <legend><b>Datos de la donación</b></legend>
		<p>
 		<label>*La donación se hace a la agrupación territorial</label>
	     <?php
				  //echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormDonacion[CODAGRUPACION]",
						//$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['descDefecto'],"","");	 
       ?>
		</p>
	 </fieldset>
		*** Fin AGRUPACION por ahora será siempre '00000000' Europa Laica Estatal **-->		
		
	 <!-- ********************* Inicio OBSERVACIONES  tesorero *********************************** -->
	 <fieldset>
	  <legend><b>Observaciones del tesorería (escribe la razón para anular la donación)</b></legend>
  <p> 
		<textarea id='OBSERVACIONES' onKeyPress="limitarTextoArea(2000,'OBSERVACIONES');"	
		class="textoAzul8Left" name="datosFormDonacion[OBSERVACIONES]" rows="10" cols="80"><?php 
		  if (isset($datosDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']))                    
			{echo htmlspecialchars(stripslashes($datosDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']));}
		?></textarea> 			 
		</p>
	 </fieldset>												
		<br /> <br />
		<!-- ********************* Fin OBSERVACIONES  tesorero ************************************** -->									

  <div align="center">   
			 <input type="submit" name="siAnularDatosDonacion" value="Eliminar donación errónea o duplicada" class="enviar"
							 onClick="return confirm('¿Eliminar donación errónea o duplicada?')"
			  />
							&nbsp;		&nbsp;		&nbsp;
			<input type="submit" name="noAnularDatosDonacion" value='No anular donación' class="enviar"  />
	  </div>
								
 </form>					

 <!-- ****************** Fin  formAnularDonacionErroneaTes ***************** -->	

</div>
