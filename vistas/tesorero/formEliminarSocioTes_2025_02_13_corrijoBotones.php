<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: formEliminarSocioTes.php
VERSION: PHP 7.3.21

DESCRIPCION:
Es el formulario para eliminar los datos de un socio, por un gestor con rol Tesorería. 
Previamente muestra algunos campos de datos y se puede introducir un comentario

Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
Además de botón "No dar baja socio/a".

Se muestran algunos datos del socio y en caso de que hubiese un archivo con la firma de un socio 
debido a alta por un gestor, también mostraría aquí para eliminaría el archivo del servidor. 

LLAMADA: vistas/tesorero/vCuerpoEliminarSocioTes.php										

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<script languaje='JavaScript'>
<!-- 
function limitarTextoArea(max, id)
{	if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
    alert("Llegó al máximo de caracteres permitidos");
	}			
}
-->
</script>

<div id="registro">
 <br />	
		<span class="error">
							<?php //echo "datSocio:"; print_r($datSocio);  echo "<br />";//para puebas 
							if (isset($datSocio['codError']) && $datSocio['codError'] !== '00000') 
							{
											echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
							}
							?>
		</span> 	
		<br />
 <!--<div id="formLinea">-->
	
 <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=eliminarSocioTes">	
					
			
					<input type="hidden"
												name="datosFormMiembro[EMAILERROR]"
												value="<?php echo $datSocio['valoresCampos']['datosFormMiembro']['EMAILERROR']['valorCampo'];
																			?>"
												size="80"
												maxlength="140"
					/>						
						
	 <!-- ******************* Inicio Datos de CODUSER, CODSOCIO y CODAGRUPACION ****************** -->
    <input type="hidden"
         	id="codUser"
          name="datosFormUsuario[CODUSER]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormUsuario']['CODUSER']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormUsuario']['CODUSER']['valorCampo'];}
                 ?>'
   />

    <input type="hidden"
         	id="codUser"
          name="datosFormSocio[CODSOCIO]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];}
                 ?>'
   />			
    <input type="hidden"
         	id="codAgrupacion"
          name="datosFormSocio[CODAGRUPACION]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo'];}
                 ?>'
   />					
 	 <!-- ******************* Fin Datos de CODUSER, CODSOCIO y CODAGRUPACION *********** -->
			
		<!--------------------------------- Inicio datos socio y miembro ------------------------------->		
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
					<label>Sexo</label> 
					<input type="text" readonly
												class="mostrar"		
												name="datosFormMiembro[SEXO]"
												value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['SEXO']['valorCampo']))
												{  echo $datSocio['valoresCampos']['datosFormMiembro']['SEXO']['valorCampo'];}
												?>'
												size="10"
												maxlength="10"
					/>
					<br />	
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormMiembro[NOM]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	  <br />
		<label>Apellido primero</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[APE1]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[APE2]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['APE2']['valorCampo']))
	                 {  echo $datSocio['valoresCampos']['datosFormMiembro']['APE2']['valorCampo'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
					<br />
					<label>Fecha de nacimiento</label> 
					<input type="text" readonly
												class="mostrar"		
												name="datosFormSocio[FECHANAC]"
												value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['valorCampo']))
												{  echo $datSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['valorCampo'];}
												?>'
												size="10"
												maxlength="10"
					/>
					<br />	
					<label>Agrupación</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[NOMAGRUPACION]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormSocio']['NOMAGRUPACION']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormSocio']['NOMAGRUPACION']['valorCampo'];}
	           ?>'
	           size="50"
	           maxlength="100"
	    />
					<br />	
					<label>Fecha del alta</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[FECHAALTA]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['valorCampo'];}
	           ?>'
	           size="10"
	           maxlength="10"
	    />
					<label>Fecha de la baja</label>												
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[FECHABAJA]"
	           value="<?php 	echo date('Y-m-d') ?>"	           
	           size="10"
	           maxlength="10"
	    />
   	<br />		<br />
		<label>Correo electrónico</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[EMAIL]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	  
	  <br />
			<!-- Los tres siguientes para insertar nº documento en tabla	"MIEMBROELIMINADO5ANIOS" -->
   
		 <label for="user">Tipo documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormMiembro[TIPODOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
	         size="10"
	         maxlength="20"							
   />	
			<label for="user">Documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormMiembro[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
          size="12"
          maxlength="20"																	
   />		
  <label for="user">Código País</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormMiembro[CODPAISDOC]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo'];}
                 ?>'
         size="3"
         maxlength="4"																	
   />	
			<!--------------------------------- Fin socio y miembro ------------------------------------------->			

<br />
<!-------------------------------------Inicio domicilio ---------------------------------------------->
  <label for="user">País Domicilio</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormDomicilio[nombrePaisDom]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormDomicilio']['nombrePaisDom']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormDomicilio']['nombrePaisDom']['valorCampo'];}
                 ?>'
         size="20"
         maxlength="40"																	
   />	
			<label for="user">Provincia</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormDomicilio[NOMPROVINCIA]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo'];}
                 ?>'
         size="20"
         maxlength="40"																	
   />

		<label for="user">Localidad</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormDomicilio[LOCALIDAD]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo'];}
                 ?>'
         size="20"
         maxlength="40"																	
   />	
		<label for="user">CP</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormDomicilio[CP]"
          value='<?php if (isset($datSocio['valoresCampos']['datosFormDomicilio']['CP']['valorCampo']))
                       {  echo $datSocio['valoresCampos']['datosFormDomicilio']['CP']['valorCampo'];}
                 ?>'
         size="20"
         maxlength="40"																	
   />
  <br />					
		</p>
	 </fieldset>
	 <br />	
	 <!------------------------------------- Fin domicilio ------------------------------------------>
	
	
		<!--******************** Inicio Datos de datosFormSocio[OBSERVACIONES]*******************-->
			<fieldset>
					<legend><b>Observaciones para guardar en la base de datos y también enviar por email a presidencia, secretaría, tesorería, coordinación y gestión soci@s</b></legend>
					<p>
					<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(999, 'OBSERVACIONES');"	
																						class="textoAzul8Left" name="datosFormSocio[OBSERVACIONES]" rows="8" cols="80"><?php
								if (isset($datSocio['valoresCampos']['datosFormSocio']['OBSERVACIONES']['valorCampo']) && !empty($datSocio['valoresCampos']['datosFormSocio']['OBSERVACIONES']['valorCampo'])) 
								{
										echo htmlspecialchars($datSocio['valoresCampos']['datosFormSocio']['OBSERVACIONES']['valorCampo'],ENT_QUOTES);	
								}
?></textarea> 
							<span class="error">
									<?php
									if (isset($datSocio['valoresCampos']['datosFormSocio']['OBSERVACIONES']['errorMensaje'])) {
													echo $datSocio['valoresCampos']['datosFormSocio']['OBSERVACIONES']['errorMensaje'];
									}
									?>
						 </span>		
      <br />				 
						</p>
			</fieldset>
	 <!-- ******************* Fin Datos de Datos de datosFormSocio[OBSERVACIONES] ************** --> 
		
		
	 <br />	
	 <!-- ****************** Inicio Datos de identificación USUARIO *************************** -->
	 <fieldset>
		 <legend><b>Otros datos del socio/a</b></legend>			
		<p>  	
			<label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormUsuario[ESTADO]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormUsuario']['ESTADO']['valorCampo']))
	           {  echo $datSocio['valoresCampos']['datosFormUsuario']['ESTADO']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	   <label for="user">Usuari@</label> 
	    <input type="text" readonly
						 class="mostrar"		
		         id="user"
	           name="datosFormUsuario[USUARIO]"
	           value='<?php if (isset($datSocio['valoresCampos']['datosFormUsuario']['USUARIO']['valorCampo']))
	                        {  echo $datSocio['valoresCampos']['datosFormUsuario']['USUARIO']['valorCampo'];}
	                  ?>'
	           size="30"
	           maxlength="50"
	     />
		</p>
	 </fieldset>
		<br />		

		<!--************ Inicio Datos de datos de ARCHIVOFIRMAPD si existe *************-->
		<?php		
	 if (isset($datSocio['valoresCampos']['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']) && !empty($datSocio['valoresCampos']['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))                    
		{?>
	 <fieldset>
	  <legend><strong>Archivo con firma de aceptación cesión datos por parte de socia/o</strong></legend>	
			<p> 
				 <label>Nombre del archivo con la firma:</label> 
	    <input type="text" readonly
						      class="mostrar"		
            name="datosFormMiembro[ARCHIVOFIRMAPD]"
	           value="<?php echo $datSocio['valoresCampos']['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo'];
	                  ?>"
	           size="100"
	           maxlength="140"
	    />
				
	    <input type="hidden"
						      class="mostrar"		
            name="datosFormMiembro[PATH_ARCHIVO_FIRMAS]"
	           value="<?php echo $datSocio['valoresCampos']['datosFormMiembro']['PATH_ARCHIVO_FIRMAS']['valorCampo'];
	                  ?>"
	           size="80"
	           maxlength="140"
	    />	
	  </p>
 	</fieldset>		
		<?php
		}
		?>
		<!--************** Fin Datos de datos de ARCHIVOFIRMAPD si existe *************-->	
		<br /><br />				
  <!-- ****************** Fin Datos de identificación USUARIO ************ -->	

 <span class="textoAzu112Left2">
	 NOTA: Deberás hacer clic en el botón "Baja socio/a" (para una baja normal), 
		o en "Baja socio/a por fallecimiento y guardar su nombre" (en caso de baja por fallecimiento) y en este caso además efectuar la baja también
		se guadará en la tabla "SOCIOSFALLECIDOS" el nombre, num. socio/a, agrupación, fecha alta, fecha baja, localidad y observaciones, para conservar un recuerdo histórico.
	<?php //echo "datSocio:";print_r($datSocio);                 ?>
  </span>
  <br /><br />
		
		
	 <!-- ******************* Fin Datos de SOCIO ******************************** -->	
		<span class="error">
							<?php //echo "datSocio:"; print_r($datSocio);  echo "<br />"; 
							if (isset($datSocio['codError']) && $datSocio['codError'] !== '00000') 
							{
											echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
							}
							?>
		</span> 	
		<br />

		<!-- ********************** Inicio Botones de form Baja Socio **************************************************** -->  		  
		
	 <input type="submit" name="SiEliminar" onClick="return confirm('SÍ baja socio/a')" value="&nbsp; SÍ Baja socio/a  &nbsp;" class="enviar">	
		<input type="submit" name="SiEliminarFallecimiento" onClick="return confirm('SÍ Baja socio/a por fallecimiento')" value="Baja socio/a por fallecimiento y guardar su nombre" class="enviar">
  &nbsp;		&nbsp;		&nbsp; &nbsp;		&nbsp;		&nbsp;
		
		<input type="submit" name="NoEliminar" value="No dar baja socio/a" class="enviar">	
		
	 <!-- ********************** Fin Botones de form Baja Socio ******************************************************* -->  		
			
 </form>

 <br /><br />	
 <!--</div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




