<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: formMostrarIngresoCuotaAnioTes.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Se muestran algunos datos personales del socio y los detalles en formato tabla del estado de 
las cuotas de ese socio en todos los años ( o se podría limitar por ejemplo a los últimos 5 años dependerá 
del límite que se ponga en el código "vistas/tesorero/formMostrarIngresoCuotaAnioTes.php" ) 

Muestra nombre archivo con la firma del socio en caso de que exista

LLAMADA: vistas/tesorero/vCuerpoMostrarIngresoCuotaAnioTes.php 
y previamente desde icono lupa en "vistas/tesorero/vMostrarIngresosCuotasInc.php"

OBSERVACIONES: 2022-12-15 : suprimir COLABORA a petición presidencia y otras
---------------------------------------------------------------------------------------------------------*/
?>

<div id="registro">
				
	<span class="textoAzu112Left2"> 	<!--  <?php // print_r($datSocio); ?> -->
	</span> 				
	<!-- ******************* Inicio Nombre del socio ********************************** -->
	<!-- <fieldset> -->
		 <p> 
			<label>Nombre socio/a:</label> 
					<span class="mostrar">
      <?php 
			      echo $datSocio['datosFormMiembro']['APE1']['valorCampo'];   
			      if (isset($datSocio['datosFormMiembro']['APE2']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['APE2']['valorCampo'];}
									echo ", ".$datSocio['datosFormMiembro']['NOM']['valorCampo']."&nbsp;";
       ?>					
					</span>

			<label>Estado actual socio/a (alta, baja, ...)</label> 
					<span class="mostrar">
      <?php 
							  echo $datSocio['datosFormUsuario']['ESTADO']['valorCampo'];   
       ?>					
					</span>	
		</p>	
 <!--</fieldset> -->
	<!-- ******************* Fin Nombre del socio ************************************* --> 
	<br /><br /><br />
	<!-- ******************* Inicio cuenta bancaria  ********************************** -->	
 <fieldset>
	 <legend><b>Cuenta bancaria para el cobro de la cuota anual</b></legend>
	 <p>		
	  <label><strong>Cuenta IBAN</strong></label>  
						<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'])
																&& $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'] !==''
																&& $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']!==NULL)
									{  echo $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'];
									}
									else 
									{
												echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									}								
       ?>
      &nbsp;							
					</span>	
					
		 <br />		
		</p>
	</fieldset>
	<!-- ******************* Fin cuenta bancaria  ************************************* -->				
	<br />		

	<!-- ****************** Inicio datos personales de SOCIO ************************** -->	
	<fieldset>	 
	 <legend><b>Datos personales</b></legend>	
		<p>
		
			<label>Nombre</label> 
					<span class="mostrar">
      <?php 
			      echo " ".$datSocio['datosFormMiembro']['APE1']['valorCampo'];   
			      if (isset($datSocio['datosFormMiembro']['APE2']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['APE2']['valorCampo'];}
									echo ", ".$datSocio['datosFormMiembro']['NOM']['valorCampo']."&nbsp;";
       ?>					
					</span>
			
			<label> Sexo</label>
					<span class="mostrar">&nbsp;
      <?php 
	 
			      /*if (isset($datSocio['datosFormMiembro']['SEXO']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['SEXO']['valorCampo'];}	*/
								
									switch ($datSocio['datosFormMiembro']['SEXO']['valorCampo'])
									{ case 'H':
											echo "Hombre";
											break;
											case 'M':
											echo "Mujer";
											break;
											
											default:
											echo "Otro";
									}
       ?>
      &nbsp;							
					</span>

		  <br /><br /> 
		 <label>Documento</label>	
					<span class="mostrar">&nbsp;
      <?php						  
			      if (isset($datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>	
					
	  <label>Nº documento</label> 
					<span class="mostrar">&nbsp;      
					<?php 						  
			      if (isset($datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
	  <label>País documento</label>
					<span class="mostrar">&nbsp;
      <?php						  
			      if (isset($datSocio['datosFormMiembro']['nombrePaisDoc']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['nombrePaisDoc']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>
				<br /> 
				
		 <label>Año de nacimiento</label> 
					<span class="mostrar">&nbsp;
      <?php						  
			      /*if (isset($datSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo']) && $datSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] !== '00')
         {  echo $datSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo']."-";}		
			      if (isset($datSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo']) && $datSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] !== '00')
         {  echo $datSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo']."-";}	
         */								
			      if (isset($datSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo']) && $datSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] !== '0000')
         {  echo $datSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'];}										
       ?>
      &nbsp;							
					</span>								
		  <br /><br />
			
			<label>Correo electrónico</label>
					<span class="mostrar">&nbsp;
      <?php 						    
			      if (isset($datSocio['datosFormMiembro']['EMAIL']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['EMAIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
			<label>Recibir correos electrónicos de Europa Laica</label>
					<span class="mostrar">&nbsp;
      <?php 						    
			      if (isset($datSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
		  <br />		
				
	  <label>Teléfono móvil</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormMiembro']['TELMOVIL']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['TELMOVIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
	  <label>Teléfono fijo</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>	
	    <br /><br />	
		
		 <label>Estudios</label> 
					<span class="mostrar">&nbsp;
      <?php			
							   $parValorEstudios=array(
                            "NIVEL5"=>"Doctorado, Licenciatura, Ingeniería Superior, Arquitectura",
                            "NIVEL4"=>"Grado Universitario (ciclo 1º), Ingeniería Técnica, Diplomado",
                            "NIVEL3"=>"Formación Profesional de Grado Superior",
                            "NIVEL2"=>"Formación Profesional de Grado Medio",
                            "NIVEL1"=>"Garantía Social",
                            "ESO"=>"ESO, Enseñanza Media", 
                            "PRIMARIA"=>"Enseñanza Primaria",
                            "INFANTIL"=>"Educación Infantil (0-6 años)",																							
                            "SINESTUDIOS"=>"Sin estudios");		
																												
          if (isset($parValorEstudios[$datSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']]))
	                        {  echo $parValorEstudios[$datSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']];}	                							
       ?>
      &nbsp;							
					</span>	

	  <label>Profesión</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormMiembro']['PROFESION']['valorCampo']))
         {  echo " ".$datSocio['datosFormMiembro']['PROFESION']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
		  <br />		

		</p>
	 </fieldset>
	<!-- ****************** Fin datos personales de SOCIO ***************************** -->		
		<br />		

	<!-- ****************** Inicio  datosFormDomicilio ******************************** --> 	
	<fieldset>
	 <legend><strong>Domicilio</strong></legend>
		<p>	
		 <label>País domicilio</label>		
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormDomicilio']['nombrePaisDom']['valorCampo']))
         {  echo " ".$datSocio['datosFormDomicilio']['nombrePaisDom']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>
	 	<label>Dirección</label> 
					<span class="mostrar">&nbsp;
      <?php						    
									if (isset($datSocio['datosFormDomicilio']['VIA']['valorCampo']))
									{  echo $datSocio['datosFormDomicilio']['VIA']['valorCampo'];}							 
									if (isset($datSocio['datosFormDomicilio']['DIRECCION']['valorCampo']))
									{  echo " ".$datSocio['datosFormDomicilio']['DIRECCION']['valorCampo'];}						
       ?>
      &nbsp;							
					</span>						
	 	<label>Código postal</label>
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormDomicilio']['CP']['valorCampo']))
	        {  echo $datSocio['datosFormDomicilio']['CP']['valorCampo'];}		
       ?>
      &nbsp;							
					</span>										
		 <label>Localidad</label>
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
	        {  echo $datSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'];}		
       ?>
      &nbsp;							
					</span>
  	<label>Provincia</label>	
					<span class="mostrar">&nbsp;
      <?php			
						/*if (isset($datosSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']) && 
		          $datSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']=='ES' )
						{*/
         if (isset($datSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']))
	        {  echo $datSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo'];}
					//	}	
       ?>
      &nbsp;							
					</span>	
			
		  <br />	
   <label>Recibir cartas de Europa Laica</label>
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']))
	        {  echo $datSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'];}
       ?>
      &nbsp;							
					</span>							
		 <br />	
			
		</p>
	</fieldset>
	<!-- ****************** Fin datosFormMiembro Domicilio **************************** -->		
 <br />			
	
	<!-- ********************** Inicio Datos de identificación USUARIO ***************** -->
	<fieldset>
		<legend><b>Usuario para entrar en la zona privada "Área de Soci@s"</b></legend>
		<p>			
			<span class="comentario11">
				Usuario y contraseña solo se pueden cambiar por el socio/a. 
				La contraseña no se puede mostrar por privacidad de datos.
			</span>
			<br />
			
			<label>*Usuario/a</label>			
					<span class="mostrar">&nbsp;
      <?php
         if (isset($datSocio['datosFormUsuario']['USUARIO']['valorCampo']))
	        {  echo $datSocio['datosFormUsuario']['USUARIO']['valorCampo'];}
       ?>
      &nbsp;							
					</span>	
					<br />					
		</p>	
	</fieldset>
	<!-- ********************** Fin Datos de identificación USUARIO ******************** --> 	
		
 <br />					
	<!-- ****************** Inicio Datos de datos de ARCHIVOFIRMAPD si existe ********* -->
		<?php		
	 if (isset($datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']) && !empty($datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))                    
		 	{?>
	<fieldset>
	 <legend><strong>Archivo con firma de aceptación cesión datos por parte de socia/o</strong></legend>	
		<p> 
				<label>Nombre del archivo:</label> 
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))
	        {  echo $datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo'];}
       ?>
      &nbsp;							
					</span>								
     <br />					
					<label>Este archivo será eliminado del servidor al ser baja como socia/o de EL, de acuerdo con nuestro protocolo de protección de datos personales</label> 
			<br />								
	  </p>
 	</fieldset>		
		<?php
		}
		?>
	<!-- ******************* Fin Datos de datos de ARCHIVOFIRMAPD si existe *********** -->
	<br />		
	<!-- ******************* Inicio Agrupación territorial SOCIO ********************** -->
	<fieldset>
		<legend><strong>Agrupación territorial de Europa Laica</strong></legend>
	 <p>
	  <label>Agrupación actual</label>	
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']))
	        {  echo $datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'];}
       ?>
      &nbsp;							
					</span>
	  <!-- ************ Fin Agrupación territorial SOCIO **************************** -->
			<br />
			<!-- ************ Inicio Datos FECHAALTA SOCIO ******************************** -->
	
		 <label>Fecha de alta como socio/a en Europa Laica(dd/mm/aaaa)</label> 
					<span class="mostrar">&nbsp;
      <?php						  
			      if (isset($datSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo']))
         {  echo $datSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo']."-";}		
			      if (isset($datSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo']))
         {  echo $datSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo']."-";}		
			      if (isset($datSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo']))
         {  echo $datSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo'];}										
       ?>
      &nbsp;							
					</span>
			<!-- ************ Fin Datos FECHAALTA SOCIO *********************************** -->
			<br />				
			
		</p>
	</fieldset>
 <!-- ******************* Inicio COMENTARIOSOCIO y GESTOR y TESORERO *************** -->
		<br />		
	<!-- ******************* Inicio Datos de datosFormMiembro[COMENTARIOSOCIO] ******** -->
	<fieldset>
	  <legend><b>Comentarios del socio/a</b></legend>
	  <p>		
   <!--<label>Puedes desplazar hacia abajo para ver todo el contenido del área de texto</label> <br />-->		
			<!-- Opción tamaño fijo de textarea
			<textarea type="text" readonly class="mostrar1" wrap="hard" name="datFormMiembro[COMENTARIOSOCIO]" 
		          rows="3" cols="80"><?php/* 
		  if (isset($datSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))                    
			 {echo htmlspecialchars(stripslashes($datSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']));}
		  */?></textarea> 
		 -->
			 <!-- Está opción de textarea se adapta al tamaño, con un mímino de 2 filas --> 
    <textarea type="text" readonly class="mostrar1" wrap="hard" cols="80"
				          rows=" <?php if (isset($datSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))                    
			                  { 	echo ceil(strlen($datSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'])/80)+2;?>
																			" >
			          	     <?php echo htmlspecialchars(stripslashes($datSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo'])); } ?>
				</textarea> 			
	 	</p>
	</fieldset>
		<br />	
	
 <!-- ******************* Inicio Datos de datosFormMiembro[OBSERVACIONES] ********** -->		
	<fieldset>
	  <legend><b>Observaciones del gestor de socios/as</b></legend>
	  <p>		
   <!--<label>Puedes desplazar hacia abajo para ver todo el contenido del área de texto</label> <br />-->			
    <textarea type="text" readonly class="mostrar1" wrap="hard" cols="80"
				          rows=" <?php if (isset($datSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']))                    
			                  { 	echo ceil(strlen($datSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo'])/80)+2; ?>
																			" >
			          	     <?php echo htmlspecialchars(stripslashes($datSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']));} ?>
				</textarea> 			
	 	</p>
	</fieldset>
	<!-- ******************* Fin Datos de datosFormMiembro **************************** -->
	<br />	

	<!-- ******************* Inicio Datos de datosFormCuotaSocio[Y][OBSERVACIONES] **** -->		
	<fieldset>
	  <legend><b>Observaciones de Tesorería en el año calculo <?php echo date('Y')?> </b></legend>
	  <p>		
    <textarea type="text" readonly class="mostrar1" wrap="hard" cols="80"
				          rows=" <?php if (isset($datSocio['datosFormCuotaSocio'][date('Y')]['OBSERVACIONES']['valorCampo']))                    
			                  { 	echo ceil(strlen($datSocio['datosFormCuotaSocio'][date('Y')]['OBSERVACIONES']['valorCampo'])/80)+2; ?>
																			" >
			          	     <?php echo htmlspecialchars(stripslashes($datSocio['datosFormCuotaSocio'][date('Y')]['OBSERVACIONES']['valorCampo']));} ?>
				</textarea> 			
	 	</p>
	</fieldset>
		
	<!-- ****************** Fin Datos COMENTARIOSOCIO y GESTOR y TESORERO ************* -->	
	<br /><br />
	<!-- ******************* Inicio ingresos cuotas por año *************************** -->
	<span class="textoAzu112Left2">
	 <strong>Datos de las cuotas del socio</strong> 
	</span> 
	<br />			
		<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
     <tr bgcolor='#CCCCCC'>
						<th  class="textoAzul8L">Año</th>
      <th  class="textoAzul8L">Agrupación</th>
      <th  class="textoAzul8L">Tipo</th>						
						<th  class="textoAzul8L">Cuota EL</th>
      <th  class="textoAzul8L">Cuota elegida</th>  
      <th  class="textoAzul8L">Ingreso</th>
						<th  class="textoAzul8L">Gastos</th>  
      <th  class="textoAzul8L">F.ingreso</th> 
						<th  class="textoAzul8L">F.anotación</th>
      <th  class="textoAzul8L">&nbsp;Saldo</th>
      <th  class="textoAzul8L">Estado cuota</th>
						<th  class="textoAzul8L">Modo pago</th>  
     </tr>
     <?php 
					 
						//echo "<br><br>datSocio:";print_r($datSocio);
						$datCuotas=array_reverse($datSocio['datosFormCuotaSocio']);
      //echo "<br><br>datCuotas:";print_r($datCuotas);
      //foreach ($datCuotas as $ordinal => $fila)
						foreach ($datCuotas as  $fila)
   	  {echo ("<tr height='25' bgcolor='#eeeded'>"); 
				
							echo ("<td class='textoAzul7L'>".$fila['ANIOCUOTA']['valorCampo']."</td>");
							echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$fila['NOMAGRUPACION']['valorCampo']."</td>");
							echo ("<td class='textoAzul7L'>".$fila['CODCUOTA']['valorCampo']."</td>");
       echo ("<td class='textoAzul7R'>".$fila['IMPORTECUOTAANIOEL']['valorCampo']."</td>");       						 
       echo ("<td class='textoAzul7R'>".$fila['IMPORTECUOTAANIOSOCIO']['valorCampo']."</td>");							
       echo ("<td class='textoAzul7R'>".$fila['IMPORTECUOTAANIOPAGADA']['valorCampo']."</td>");							
						 echo ("<td class='textoAzul7R'>".$fila['IMPORTEGASTOSABONOCUOTA']['valorCampo']."</td>");
							echo ("<td class='textoAzul7L'>".$fila['FECHAPAGO']['dia']['valorCampo']."-".
																																								$fila['FECHAPAGO']['mes']['valorCampo']."-".
																																								$fila['FECHAPAGO']['anio']['valorCampo']."</td>"); 
						 echo ("<td class='textoAzul7L'>".$fila['FECHAANOTACION']['dia']['valorCampo']."-".
																																								$fila['FECHAANOTACION']['mes']['valorCampo']."-".
																																								$fila['FECHAANOTACION']['anio']['valorCampo']."</td>");
       $saldo = $fila['IMPORTECUOTAANIOPAGADA']['valorCampo']-$fila['IMPORTECUOTAANIOEL']['valorCampo'];																																								 
							//echo ("<td class='textoAzul7L'>".$saldo."</td>");		
							
       if ($saldo< 0)							
						 {echo ("<td class='textoRojo8Right'>".$saldo."</td>");
							}
						 else 
						 {echo ("<td class='textoAzul7R'>".$saldo."</td>");
							}
							if ($fila['ESTADOCUOTA']['valorCampo'] !== 'ABONADA' && $fila['ESTADOCUOTA']['valorCampo'] !== 'EXENTO')
						 { echo ("<td class='textoRojo7Left' style='word-wrap: break-word'>".$fila['ESTADOCUOTA']['valorCampo']."</td>");	
							}
						 else 
						 { echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$fila['ESTADOCUOTA']['valorCampo']."</td>");	
							}							
	
				   if (isset($fila['MODOINGRESO']['valorCampo']) && !empty($fila['MODOINGRESO']['valorCampo']))
       { echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$fila['MODOINGRESO']['valorCampo']."</td>");
							}
						 else
							{ echo ("<td class='textoAzul7L'style='word-wrap: break-word'>"."&nbsp;"."</td>");
							}							
							echo ("</tr>");
      }
     ?>
			</table>

	<!-- ****************** Fin ingresos cuotas por año ******************************* -->	

</div>
