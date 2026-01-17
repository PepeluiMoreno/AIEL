<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formMostrarIngresoDonacionTes.php
VERSION:  PHP 7.3.21	

DESCRIPCION: 
Es el formulario para mostrar todos los datos de una donación concreta a partir de la tabla DONACION
             
LLAMADA: Incluida desde "./vistas/tesorero/vCuerpoMostrarIngresoDonacionTes.php"

OBSERVACIONES: 
------------------------------------------------------------------------------------------------------*/
?>

<div id="registro">
	
		<!-- ****************** Inicio Nombre del donante *************************** -->
	 <fieldset>
		 <p> 
			<label>Tipo donante:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo'];
      ?>					
					</span>
					<br />	
			<label>Nombre donante:</label> 
					<span class="mostrar">
      <?php 
							      echo $datosDonacion['datosFormDonacion']['APE1']['valorCampo'];   
							      if (isset($datosDonacion['datosFormDonacion']['APE2']['valorCampo']))
             {  echo " ".$datosDonacion['datosFormDonacion']['APE2']['valorCampo'];}
													echo ", ".$datosDonacion['datosFormDonacion']['NOM']['valorCampo'];
       ?>					
					</span>

				 <?php if ($datosDonacion['datosFormDonacion']['SEXO']['valorCampo']=='H')
          {  echo " <label>Sexo</label> <span class='mostrar'>HOMBRE</span>";}
										elseif ($datosDonacion['datosFormDonacion']['SEXO']['valorCampo']=='M')
										{  echo " <label>Sexo</label> <span class='mostrar'>MUJER</span>";}
										else
										{  echo "";}
     ?>
					<br />
		</p>	
 </fieldset>
	<br />	<br />
	<!-- ********************** Fin Nombre del donante ****************************** --> 
	
		<!-- ****************** Inicio datos económicos de la donación ***************** -->
	 <fieldset>
   <legend><b>Datos econónicos</b></legend>			
		 <p> 
			<label>Importe ingreso donación (euros):</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo'];
      ?>					
					</span>
					<br />
			<label>Gastos al abonar la donación si los hubiese (cobrados a EL por PayPal o la entidad bancaria) (euros):</label> 
					<span class="mostrar">
						<?php if (isset($datosDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo']) && 
						          !empty($datosDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo']))
												{  echo $datosDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo'];}
												else
												{ echo '0.00';}	
      ?>					
					</span>					
			<label>Modo ingreso:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'];
      ?>					
					</span>
				<br /><br />		
			<label>Fecha ingreso:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['FECHAINGRESO']['valorCampo'];
      ?>					
					</span>					
			<label>Fecha anotación:</label> 
					<span class="mostrar">
      <?php 
							  echo $datosDonacion['datosFormDonacion']['FECHAANOTACION']['valorCampo'];
      ?>					
					</span>
					<br />
		<label>Donado a la agrupación</label>				
			 <span class='mostrar'>
				 <?php echo $datosDonacion['datosFormDonacion']['NOMAGRUPACION']['valorCampo'] ?>
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
							  echo $datosDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo'];
      ?>					
					</span>
					<br />
		</p>	
 </fieldset>
	<br />	<br />
	<!-- ********************** Fin datos económicos de la donación ***************** --> 


	 <!-- ********************** Inicio datos personales de donante ************** -->	
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Nº documento</label> <!--obligatorio y se valida para NIF y NIE pero no para pasaporte-->
	    <input type="text" readonly  class="mostrar"	
	           value="<?php if (isset($datosDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>"
	           size="12"
	           maxlength="20"
	    />
					<br />		
		<label>Tipo documento</label>		
	    <input type="text" readonly class="mostrar"	
	           value="<?php if (isset($datosDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>"
	           size="9"
	           maxlength="20"
	    />	
	  <label>Código país documento</label>
			 <input type="text" readonly class="mostrar"		
			        value="<?php if ($datosDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'])
			        {  echo $datosDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'];}
			        ?>"
			        size="30"
			        maxlength="50"
	     />

	  <br />
		<label>email</label>
	    <input type="text" readonly  class="mostrar"	
	           value="<?php if (isset($datosDonacion['datosFormDonacion']['EMAIL']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['EMAIL']['valorCampo'];}
	           ?>"
	           size="60"
	           maxlength="200"
	    />	 
	  <br />
	  <label>Teléfono fijo</label>
	    <input type="text" readonly class="mostrar"	
	           value="<?php if (isset($datosDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo'];}
	           ?>"
	           size="14"
	           maxlength="14"
	    />	 
			<label>Teléfono móvil</label>
     <input type="text" readonly class="mostrar"	
	           value="<?php if (isset($datosDonacion['datosFormDonacion']['TELMOVIL']['valorCampo']))
	           {  echo $datosDonacion['datosFormDonacion']['TELMOVIL']['valorCampo'];}
	           ?>"
	           size="14"
	           maxlength="14"
	    />	
					<br />
		</p>
	 </fieldset>

	 <!-- ********************** Fin datos personales de donante ***************** -->		
</div>
