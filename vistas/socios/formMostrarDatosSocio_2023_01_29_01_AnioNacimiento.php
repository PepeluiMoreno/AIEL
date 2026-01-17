<?php
/*-----------------------------------------------------------------------------
FICHERO: formMostrarDatosSocio.php
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para mostrar los datos de un socio

LLAMADA: vistas/socios/vCuerpoMostrarDatosSocio.php y a su vez desde controladoSocios.php:mostrarDatosSocio() 
 	
OBSERVACIONES:
2020-09-08: Quito comentarios del socio al darse de alta
2023_01_10: Modifico texto
2023-01-22: Cambio fecha de nacimiento completa por sólo "año de nacimiento" 
-------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<div id="registro">
	
 <br />	
 <span class="textoAzu112Left2">
	 <!-- Te mostramos los datos que tenemos anotados de ti en Europa Laica. <br /><br />-->
		Si quieres actualizar alguno de tus datos o darte de baja como socio/a, debes elegir la opción correspondiente del menú lateral izquierdo.
		<br /><br />Puedes contactar con el coordinador/a de tu agrupación <strong> <?php	echo $datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']."</strong> enviado un email a ".
		"<b>".$datosSocio['datosFormSocio']['EMAILCOORD']['valorCampo']."</b>" ?>		
 </span>
 <br /><br /><br />		

	<!-- ******************** Inicio Datos de MIEMBRO ********************************* -->
	<fieldset>	 
	 <legend><strong>Datos personales</strong></legend>	
		<p>
			<label>Nombre </label> 
					<span class="mostrar">&nbsp;
      <?php 
						   echo $datosSocio['datosFormMiembro']['NOM']['valorCampo']." ".$datosSocio['datosFormMiembro']['APE1']['valorCampo'];   
			      if (isset($datosSocio['datosFormMiembro']['APE2']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['APE2']['valorCampo'];}									
       ?>
      &nbsp;							
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
				
		 <label>Año de nacimiento</label> 
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
			      if (isset($datosSocio['datosFormMiembro']['EMAIL']['valorCampo']))
         {  echo " ".$datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];}									
       ?>
      &nbsp;							
					</span>					
			<label>Recibir correos electrónicos de Europa Laica</label>
					<span class="mostrar">&nbsp;
      <?php 						    
			      if (isset($datosSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']))
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
	  <!-- <label>Puedo colaborar en </label> Lo quito de aquí desde 2023, no se recoge este dato pero sigue COLUMNA  COLABORA en la tabla MIEMBRO 			
		 <?php		 
		 /*$parValorColabora=array("secretaria"=>"Tareas de secretaría","prensa"=>"Contactos con la prensa",
		 "actividades"=>"Organización de actividades","formacion"=>"Formación en laicismo","web"=>"Mantenimiento del sitio web",
		 "manifestaciones"=>"Participación en manifestaciones y concentraciones","otros"=>"Otras actividades",
		 "tiempo"=>"No dispongo de tiempo");	*/				 
		 ?>
	    <input type="text" readonly
            class="mostrar"		
	           name="datosFormMiembro[COLABORA]"
	           value='<?php /*if (isset($parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']]))
	                        {  echo $parValorColabora[$datosSocio['datosFormMiembro']['COLABORA']['valorCampo']];}*/
	                  ?>'
	           size="60"
	           maxlength="100"
	    />	--> 		 
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
	<!-- ******************* Fin datosFormMiembro Domicilio *************************** -->
  <br />	
		
	<!-- ******************** Inicio Datos de Cuotas  ********************************* -->
	<fieldset>
	 <legend><strong>Cuota anual socio/a</strong></legend>
		<p>
   <?php //echo "<label><br />datosSocio['datosFormCuotaSocio': ";  print_r($datosSocio['datosFormCuotaSocio']);
                               //echo "<label><br><br>datSocio: "; print_r($datosSocio);echo "</label><br>";
				if (isset($datosSocio['datosFormCuotaSocio'][date('Y')]['ANIOCUOTA']['valorCampo']))							
				{
					echo "<label>Cuota total elegida por el socio/a para el año<strong>".$datosSocio['datosFormCuotaSocio'][date('Y')]['ANIOCUOTA']['valorCampo'].
										"</strong></label>"."<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOSOCIO']['valorCampo']." euros </span>";
					
					if ($datosSocio['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo'] == 'Joven' || $datosSocio['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo'] == 'Parado' )	
     {	echo "<label>Cuota Reducida tipo </label>";}
				 elseif ($datosSocio['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo'] == 'Honorario')
				 {	echo "<label>Cuota tipo <strong>Honorario</strong> estás exento de pagar cuota </label>";}
				 elseif ($datosSocio['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo'] == 'General')
				 {	echo "<label>Cuota tipo </label>";}			
					
					echo "<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo']."</span><br />";															
					echo "<br />";							
					echo "<label>Cuota ".//$error['datosFormCuotaSocioVer']['NOMBRECUOTA']['valorCampo'].
							" pagada por el socio/a en <strong>".$datosSocio['datosFormCuotaSocio'][date('Y')]['ANIOCUOTA']['valorCampo']."</strong></label>"; 
					echo "<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOPAGADA']['valorCampo']." euros</span>"; 


					echo "<label>Estado cuota </label>";
					echo "<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']."</span>";		

					if  ($datosSocio['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA'  || 
												$datosSocio['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-ERROR-CUENTA'	)
					{echo "<span class='mostrar'>Devolución recibo de pago. Contactar con el tesorero de Europa Laica</span>";
					}						
				}
				//--------- Si tiene datos para nuevo año Y+1 (anterior Y ya pagado)					
				if (isset($datosSocio['datosFormCuotaSocio'][date('Y')+1]['ANIOCUOTA']['valorCampo']))				
				{ echo "<br /><br /><label>Cuota elegida por el socio/a para el año <strong>".$datosSocio['datosFormCuotaSocio'][date('Y')+1]['ANIOCUOTA']['valorCampo'].
						"</strong> </label>".	"<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')+1]['IMPORTECUOTAANIOSOCIO']['valorCampo']." euros </span>";
					//echo "<label> cuota tipo </label>";
					if ($datosSocio['datosFormCuotaSocio'][date('Y')+1]['CODCUOTA']['valorCampo'] == 'Joven' || $datosSocio['datosFormCuotaSocio'][date('Y')+1]['CODCUOTA']['valorCampo'] == 'Parado' )	
     {	echo "<label>Cuota Reducida tipo </label>";}
				 elseif ($datosSocio['datosFormCuotaSocio'][date('Y')+1]['CODCUOTA']['valorCampo'] == 'Honorario')
				 {	echo "<label>Cuota tipo <strong>Honorario</strong> estás exento de pagar cuota </label>";}
				 elseif ($datosSocio['datosFormCuotaSocio'][date('Y')+1]['CODCUOTA']['valorCampo'] == 'General')
				 {	echo "<label>Cuota tipo </label>";}						
					
				
						echo "<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')+1]['CODCUOTA']['valorCampo']."</span>";															
						echo "<label>Estado cuota </label>";
						echo "<span class='mostrar'>".$datosSocio['datosFormCuotaSocio'][date('Y')+1]['ESTADOCUOTA']['valorCampo']."</span>";										

				}								
			//--------- fin nuevo 	año	--------------------------------------			
   ?>
		  <br /><br />
	 	<!-- *********************** Fin Datos de Cuotas  ***************************** -->
			
		<!-- ****************** Inicio cuenta bancaria  ******************************** -->	
 	  <span class="comentario11">Cuenta bancaria para el cobro de tu cuota anual
		  </span>		
		  <br />
	  <label><strong>Tu cuenta IBAN</strong></label>  
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
	<!-- ******************** Fin cuenta bancaria  ************************************ -->	
	 <br />	 			

	<fieldset>
	<!-- ******************** Inicio Agrupación territorial SOCIO ********************* -->	
		<legend><strong>Agrupación territorial de Europa Laica</strong></legend>
	 <p>
	  <label>Tu agrupación </label>	
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']))
	        {  echo $datosSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'];}
       ?>
      &nbsp;							
					</span>								
					
			<!-- ************ Fin Agrupación territorial SOCIO *************************** -->
			<br />
			<!-- ************ Inicio Datos FECHAALTA SOCIO ******************************* -->
	
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
	
					<!-- ************ Fin Datos FECHAALTA SOCIO ********************************* -->		
			<br />					
		</p>
	</fieldset>
	
	<br />
	<!-- ******************** Inicio Datos de identificación USUARIO ****************** --> 	 
	<fieldset>	 
	 <legend><strong>Datos de identificación para entrar en el "Área de Soci@s" de Europa Laica</strong></legend>	
		<p>
	  <label>Usuario</label> 		
					<span class="mostrar">&nbsp;
      <?php						    
         if (isset($datosSocio['datosFormUsuario']['USUARIO']['valorCampo']))
	        {  echo $datosSocio['datosFormUsuario']['USUARIO']['valorCampo'];}
       ?>
      &nbsp;							
					</span>	
				<br />	
    <span class="comentario11">
				La contraseña no la mostramos aquí por seguridad				
			 </span>
			 <br />	
	 </p>
	</fieldset>
	
	<!-- ******************** Fin Datos de identificación USUARIO ********************* --> 
	 <br />

		<p>

			<span class="comentario11">
			Si necesitas ayuda: 	<strong>info@europalaica.org</strong>, &nbsp;&nbsp;&nbsp;<strong>	Teléfono</strong> <!-- o Whatsapp--> (España): <strong>670 55 60 12 </strong> 
			</span>			

		</p>				

</div>

	