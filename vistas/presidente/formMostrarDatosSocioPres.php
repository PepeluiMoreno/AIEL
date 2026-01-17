<?php
/*-----------------------------------------------------------------------------------------------
FICHERO: formMostrarDatosSocioPres.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario para mostrar los datos de de un 
socio al rol Presidente. Muestra si lo hubiese el archivo con la firma del socio 
													
LLAMADA: vistas/presidente/vCuerpoMostrarDatosSocioPres.php 
y a su vez desde cPresidente.php:mostrarDatosSocioPres(), en lista de socios desde el icono Ver = Lupa

OBSERVACIONES:
2022-12-15 : suprimir COLABORA a petición presidencia
2023-01-22: Cambio fecha de nacimiento completa por sólo "año de nacimiento", y tabla cuotas 
----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">
 <br />
	<!-- ******************** Inicio Datos de MIEMBRO ********************************* -->
	<fieldset>	 
		<legend><b>Datos personales</b></legend>	
		<p>				
			<label>Estado socio/a (alta, baja, ...) </label> 
					<span class="mostrar">&nbsp;
      <?php 
			      if (isset($datosSocio['datosFormUsuario']['ESTADO']['valorCampo']))
         {  echo $datosSocio['datosFormUsuario']['ESTADO']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>		
			<label>Tipo cuota</label> 
					<span class="mostrar">&nbsp;
      <?php 
							  echo $datosSocio['datosFormSocio']['CODCUOTA']['valorCampo'];   
       ?>
      &nbsp;									
					</span>			
					<br />
			<label>Nombre </label> 
				 <span class="mostrar">&nbsp;
      <?php 
			      echo $datosSocio['datosFormMiembro']['APE1']['valorCampo'];   
			      if (isset($datosSocio['datosFormMiembro']['APE2']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['APE2']['valorCampo'];}
									echo ", ".$datosSocio['datosFormMiembro']['NOM']['valorCampo']."&nbsp;";
       ?>					
					</span>
			<label> Sexo</label>
					<span class="mostrar">&nbsp;
      <?php 
	 
			      /*if (isset($datosSocio['datosFormMiembro']['SEXO']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['SEXO']['valorCampo'];}	*/
								
									switch ($datosSocio['datosFormMiembro']['SEXO']['valorCampo'])
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
			      if (isset($datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>	
					
	  <label>Nº documento</label> 
					<span class="mostrar">&nbsp;      
					<?php 						  
			      if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
	  <label>País documento</label>
					<span class="mostrar">&nbsp;
      <?php						  
			      if (isset($datosSocio['datosFormMiembro']['nombrePaisDoc']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['nombrePaisDoc']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>							
				<br /> 
				
		 <label>Año de nacimiento </label> 
					<span class="mostrar">&nbsp;
      <?php						  
			      /*if (isset($datosSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo']) && $datosSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] !== '00')
         {  echo $datosSocio['datosFormMiembro']['FECHANAC']['dia']['valorCampo']."-";}		
			      if (isset($datosSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo']) && $datosSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] !== '00')
         {  echo $datosSocio['datosFormMiembro']['FECHANAC']['mes']['valorCampo']."-";}		
								 */
			      if (isset($datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo']) && $datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] !== '0000')
         {  echo $datosSocio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'];}										
       ?>
      &nbsp;							
					</span>								
		  <br /><br /> 
				
			<label>Correo electrónico</label>
					<span class="mostrar">&nbsp;
      <?php 	
         if (isset($datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo']) && $datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo'] == 'FALTA'  )	
         {		echo " ".$datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo']; 
								 }	
         elseif (isset($datosSocio['datosFormMiembro']['EMAIL']['valorCampo']))
									{  echo " ".$datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];							
									}												
       ?>
      &nbsp;							
					</span>					
			<label>Recibir correos electrónicos de Europa Laica</label>
					<span class="mostrar">&nbsp;
      <?php 						    
			      if (isset($datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']) && $datosSocio['datosFormMiembro']['EMAILERROR']['valorCampo'] !== 'FALTA')
         {  echo " ".$datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
		  <br />				  					
	  <label>Teléfono móvil</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['TELMOVIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>
					
	  <label>Teléfono fijo</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'];}									
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
																												
          if (isset($parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']]))
	                        {  echo $parValorEstudios[$datosSocio['datosFormMiembro']['ESTUDIOS']['valorCampo']];}	                							
       ?>
      &nbsp;							
					</span>		
		  <br />								

	  <label>Profesión</label> 
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormMiembro']['PROFESION']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['PROFESION']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
		  <br />		
		 <!-- <label>Puede colaborar en </label>		 Lo quito de aquí desde 2023, no se recoge este dato pero sigue COLUMNA  COLABORA en la tabla MIEMBRO 			
		 <?php		/* 
		 $parValorColabora=array("secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
		 "actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
		 "manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades",
		 "tiempo"=>"No dispongo de tiempo");	*/				 
		  ?>
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormMiembro[COLABORA]"
	           value='<?php if (isset($parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']]))
	           {  echo $parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']];}
	           ?>'
	           size="60"
	           maxlength="100"
	    />	
					-->
		</p>
	</fieldset>
	<!-- ******************** Fin Datos de identificación MIEMBRO ********************* --> 	
		<br />		

	<!-- ******************** Inicio  datosFormDomicilio ****************************** --> 	
	<fieldset>
	 <legend><strong>Domicilio</strong></legend>
		<p>	
		 <label>País domicilio</label>		
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormDomicilio']['nombrePaisDom']['valorCampo']))
         {  echo " ".$datosSocio['datosFormDomicilio']['nombrePaisDom']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>						

	 	<label>Dirección</label> 
					<span class="mostrar">&nbsp;
      <?php						    
									if (isset($datosSocio['datosFormDomicilio']['VIA']['valorCampo']))
									{  echo $datosSocio['datosFormDomicilio']['VIA']['valorCampo'];}							 
									if (isset($datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo']))
									{  echo " ".$datosSocio['datosFormDomicilio']['DIRECCION']['valorCampo'];}						
       ?>
      &nbsp;							
					</span>						
	 	<label>Código postal</label>
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormDomicilio']['CP']['valorCampo']))
	        {  echo $datosSocio['datosFormDomicilio']['CP']['valorCampo'];}		
       ?>
      &nbsp;							
					</span>										
		 <label>Localidad</label>
					<span class="mostrar">&nbsp;
      <?php						    
			      if (isset($datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
	        {  echo $datosSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'];}		
       ?>
      &nbsp;							
					</span>	

  	<label>Provincia</label>	
					<span class="mostrar">&nbsp;
      <?php			
						/*if (isset($datosSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']) && 
		          $datosSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']=='ES' )
						{*/
         if (isset($datosSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']))
	        {  echo $datosSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo'];}
					//	}	
       ?>
      &nbsp;							
					</span>	
			
		  <br />	
   <label>Recibir cartas de Europa Laica</label>
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']))
	        {  echo $datosSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'];}
       ?>
      &nbsp;							
					</span>							
		 <br />	
		</p>
	</fieldset>
	<!-- ******************** Fin datosFormMiembro Domicilio ************************** -->		
	 <br />			
		
	<!-- ********************** Inicio Datos de identificación USUARIO **************** -->
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
         if (isset($datosSocio['datosFormUsuario']['USUARIO']['valorCampo']))
	        {  echo $datosSocio['datosFormUsuario']['USUARIO']['valorCampo'];}
       ?>
      &nbsp;							
					</span>	
					<br />					
		</p>	
	</fieldset>
	<!-- ********************** Fin Datos de identificación USUARIO ******************* --> 		
		 <br />			
	<!-- ******************** Inicio Datos de datos de ARCHIVOFIRMAPD si existe ******* -->
		<?php		
	 if (isset($datosSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']) && !empty($datosSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))                    
		 	{?>
	<fieldset>
	 <legend><strong>Archivo con firma de aceptación cesión datos por parte de socia/o</strong></legend>	
		<p> 
				<label>Nombre del archivo:</label> 
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datosSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))
	        {  echo $datosSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo'];}
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
         if (isset($datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']))
	        {  echo $datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'];}
       ?>
      &nbsp;							
					</span>								
					
	  <!-- ************ Fin Agrupación territorial SOCIO **************************** -->
			<br />
			<!-- ************ Inicio Datos FECHAALTA SOCIO ******************************** -->
	
		 <label>Fecha de alta como socio/a en Europa Laica(dd/mm/aaaa)</label> 
					<span class="mostrar">&nbsp;
      <?php						  
			      if (isset($datosSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo']))
         {  echo $datosSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo']."-";}		
			      if (isset($datosSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo']))
         {  echo $datosSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo']."-";}		
			      if (isset($datosSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo']))
         {  echo $datosSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo'];}										
       ?>
      &nbsp;							
					</span>								
	
			<!-- ************ Fin Datos FECHAALTA SOCIO *********************************** -->
			<br />					
		</p>
	</fieldset>
		<br />		
	 <!-- ****************** Inicio Datos de datosFormMiembro[COMENTARIOSOCIO] ******** -->
	<fieldset>
	  <legend><b>Comentarios del socio/a</b></legend>
	  <p>
	  	<textarea type="text" readonly class="mostrar1" wrap="hard" name="datosFormMiembro[COMENTARIOSOCIO]" 
		          rows="4" cols="80"><?php 
		  if (isset($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']))                    
			 {echo htmlspecialchars(stripslashes($datosSocio['datosFormMiembro']['COMENTARIOSOCIO']['valorCampo']));}
		  ?></textarea> 
    <br />		
	 	</p>
	</fieldset>
	
 <!-- ******************* Inicio Datos de datosFormMiembro[OBSERVACIONES] ********** -->		
	<fieldset>
	  <legend><b>Observaciones del gestor de socios/as</b></legend>
	  <p>
		  <textarea type="text" readonly class="mostrar1" wrap="hard" name="datosFormMiembro[OBSERVACIONES]" 
		          rows="7" cols="80"><?php 
		  if (isset($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']))                    
			 {echo htmlspecialchars(stripslashes($datosSocio['datosFormMiembro']['OBSERVACIONES']['valorCampo']));}
		  ?></textarea> 			 
	 	</p>
	</fieldset>
	
	<!-- ******************* Fin Datos de datosFormMiembro **************************** -->
	<br /><br />		
		
	<!-- ******************* Inicio cuenta bancaria  ********************************** -->	
 <fieldset>
	 <legend><b>Cuenta bancaria para el cobro de la cuota anual</b></legend>
	 <p>		
	  <label><strong>Cuenta IBAN</strong></label>  
						<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'])
																&& $datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']!==''
																&& $datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']!==NULL)
									{  echo $datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'];
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
	<!-- ******************* Inicio tabla ingresos cuotas por año ********************* -->
	<span class="textoAzu112Left2">
	 <b>Datos de los pagos de las cuotas anuales</b>
	</span> 
	<br />
		<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">
						<th  class="textoAzul8C">Año</th>
      <th  class="textoAzul8C">Agrupación</th>
      <th  class="textoAzul8C">Tipo<br />cuota</th>						
						<th  class="textoAzul8C">Cuota<br /> EL</th>
      <th  class="textoAzul8C">Cuota<br/>elegida</th>  
      <th  class="textoAzul8C">Ingreso</th>  
      <th  class="textoAzul8C">F. ingreso</th> 
						<!--<th  class="textoAzul8L">F. anotación</th>-->
      <th  class="textoAzul8C">&nbsp;Saldo</th> 
						<!--<th  class="textoAzul8L">Estado cuota</th> -->
						<th  class="textoAzul8C">Estado cuota</th> 						
      <th  class="textoAzul8C">Modo pago</th>
     </tr>
					
     <?php					 
						//echo "<br><br><span class='textoAzul7L'>datosSocio:";print_r($datosSocio);echo "</span>";
						$datCuotas = array_reverse($datosSocio['datosFormCuotaSocio']);//los mostrará en orden de mayor a menor

      //foreach ($datCuotas as $ordinal => $fila)
						foreach ($datCuotas as  $fila)
   	  {echo ("<tr height='25' bgcolor='#eeeded'>"); 
				
							echo ("<td class='textoAzul7C'>".$fila['ANIOCUOTA']['valorCampo']."</td>");
							echo ("<td class='textoAzul7C'>".$fila['NOMAGRUPACION']['valorCampo']."</td>");
							echo ("<td class='textoAzul7C'>".$fila['CODCUOTA']['valorCampo']."</td>");
 
							echo ("<td class='textoAzul7R'>".number_format($fila['IMPORTECUOTAANIOEL']['valorCampo'],0,",",".")."</td>");

       echo ("<td class='textoAzul7R'>".$fila['IMPORTECUOTAANIOSOCIO']['valorCampo']."</td>");							
       echo ("<td class='textoAzul7R'>".$fila['IMPORTECUOTAANIOPAGADA']['valorCampo']."</td>");	
													
						 echo ("<td class='textoAzul7C'>".$fila['FECHAPAGO']['dia']['valorCampo']."-".
																																								$fila['FECHAPAGO']['mes']['valorCampo']."-".
																																								$fila['FECHAPAGO']['anio']['valorCampo']."</td>"); 
						 /*echo ("<td class='textoAzul7L'>".$fila['FECHAANOTACION']['dia']['valorCampo']."-".
																																								$fila['FECHAANOTACION']['mes']['valorCampo']."-".
																																								$fila['FECHAANOTACION']['anio']['valorCampo']."</td>");*/
																																								
       $saldo = $fila['IMPORTECUOTAANIOPAGADA']['valorCampo']-$fila['IMPORTECUOTAANIOEL']['valorCampo'];
							
       if ($saldo< 0)							
						 {echo ("<td class='textoRojo8Right'>".$saldo."</td>");
							}
						 else 
						 {echo ("<td class='textoAzul7R'>".$saldo."</td>");
							}
							if ($fila['ESTADOCUOTA']['valorCampo'] !== 'ABONADA' && $fila['ESTADOCUOTA']['valorCampo'] !== 'EXENTO')
						 {  echo ("<td class='textoRojo7Center'>".$fila['ESTADOCUOTA']['valorCampo']."</td>");
							}
						 else               
						 { echo ("<td class='textoAzul7C'>".$fila['ESTADOCUOTA']['valorCampo']."</td>");	
							}									

				   if (isset($fila['MODOINGRESO']['valorCampo']) && !empty($fila['MODOINGRESO']['valorCampo']))
       { echo ("<td class='textoAzul7C'>".$fila['MODOINGRESO']['valorCampo']."</td>");
							}
						 else
							{ echo ("<td class='textoAzul7C'>"."&nbsp;"."</td>");
							}
			
							echo ("</tr>");
      }
     ?>
			</table>
		<!-- ***************** Fin tabla ingresos cuotas por año ************************* -->	
</div>
	