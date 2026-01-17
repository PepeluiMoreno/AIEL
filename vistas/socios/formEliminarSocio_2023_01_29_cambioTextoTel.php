<?php
/*------------------------------------------------------------------------------------------
FICHERO: formEliminarSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para darse de baja por el propio socio. 
             Permite (opcionamente) introducir los comentarios del socio al darse 
													de baja. Muestra algunos datos del socio.
													En el caso de que el socio haya sido dado de alta por un gestor, a partir 
													de los últimos años se incluye el archivo con el firma del socio que se
													pasaría como hidden (se podría mostrar) para eliminarlo del servidor.

LLAMADA: vistas/socios/vCuerpovEliminarSocio.php y a su vez de controladorSocios.php:eliminarSocio()
													
OBSERVACIONES: Probado PHP 7.3.21        
2022_12_26: Pequeños cambios en texto
--------------------------------------------------------------------------------------------*/
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

<!-- *********************** Inicio <div id="registro"> Incluye todo *********************************************** -->
<div id="registro">

	<?php //echo "<br/>datSocio:"; print_r($datSocio);echo "<br/>";?>
		
 <br />	
		<span class="error">
		<?php		
			if (isset($datSocio['codError']) && ($datSocio['codError']!=='00000'))
			{echo "<strong>ERROR: revisa los comentarios de error en color rojo</strong>";							
			}			
		?>	
		</span>	
 <br />
 
	<!-- ********************* Inicio texto informativo para socio *************************************************** -->		
 <span class="textoAzu112Left2">
	
	 Al darte de baja como socio/a de Europa Laica a la vez se eliminarán tus datos personales de nuestra base de datos.
		<br /><br />		
		También puedes enviar un correo electrónico a 
			<a href="./index.php?controlador=cEnlacesPie&amp;accion=contactarEmail" 
						target="ventana1" title="Contactar con nosotros" 
						onclick="window.open('','ventana1','width=800,height=800,scrollbars=yes')">
			<strong>info@europalaica.org</strong></a> 
			solicitando la baja.
	</span>		
 <br /><br />		 
		
 <!-- ********************* Fin texto informativo para socio ****************************************************** -->	
	
 <!-- <div id="formLinea"> -->	
							
 <form method="post" class="linea" action="./index.php?controlador=controladorSocios&amp;accion=eliminarSocio">													
			
	    <input type="hidden"
            name="datosFormMiembro[EMAILERROR]"
	           value="<?php if (isset($datSocio['datosFormMiembro']['EMAILERROR']['valorCampo']))
                         {  echo $datSocio['datosFormMiembro']['EMAILERROR']['valorCampo'];}
																				?>"	
	    />			
				<!-- ***************** Inicio de datos de ARCHIVOFIRMAPD si existe para borrar ********************************* -->
				<?php		
				if (isset($datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']) && !empty($datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo']))                    
				{?>
							<input type="hidden"
														name="datosFormMiembro[ARCHIVOFIRMAPD]"
														value="<?php echo $datSocio['datosFormMiembro']['ARCHIVOFIRMAPD']['valorCampo'];
																					?>"
							/>
							
							<input type="hidden"
														name="datosFormMiembro[PATH_ARCHIVO_FIRMAS]"
														value="<?php echo $datSocio['datosFormMiembro']['PATH_ARCHIVO_FIRMAS']['valorCampo'];
																					?>"
							/>			
				<?php
				}
				?>
				<!-- ***************** Fin de datos de ARCHIVOFIRMAPD si existe si existe para borrar ************************** -->	
				<br />		
				

 	<!-- ******************* Inicio Datos de identificación MIEMBRO ************************************************** --> 
	 <fieldset>	 
	  <legend><strong>Algunos de tus datos personales</strong></legend>

		 <p>
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormMiembro[NOM]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['NOM']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['NOM']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	   <br /><br />
		  <label>Apellido primero</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[APE1]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['APE1']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['APE1']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
		  <br /><br />
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[APE2]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['APE2']['valorCampo']))
	                 {  echo $datSocio['datosFormMiembro']['APE2']['valorCampo'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
		  <br /><br />
		  <label>Correo electrónico</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormMiembro[EMAIL]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['EMAIL']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['EMAIL']['valorCampo'];}
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
          value='<?php if (isset($datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
	         size="10"
	         maxlength="20"							
    />	
			 <label for="user">Documento</label> 
    <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormMiembro[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
          size="12"
          maxlength="20"																	
    />		
    <label for="user">Código País</label> 
    <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormMiembro[CODPAISDOC]"
          value='<?php if (isset($datSocio['datosFormMiembro']['CODPAISDOC']['valorCampo']))
                       {  echo $datSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'];}
                 ?>'
         size="3"
         maxlength="4"																	
    />	
		 	<!--------------------------------------------------------------------------------------->						
	 	 <br />							
 	 </p>
	 </fieldset>
  <!-- ******************* Fin Datos de identificación MIEMBRO ***************************************************** --> 	
  <br /><br />		

  <!-- ******************** Inicio Datos de datosFormSocio[OBSERVACIONES] ****************************************** -->
	 <fieldset>		
				<legend><b>Comentario</b></legend>					
				<p>
     <label><b>Puedes escribir el motivo por el que te das de baja como socia/o, y llegará por email a Europa Laica</b></label>
     <br />					
					<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(499, 'OBSERVACIONES');"	
																class="textoAzul8Left" name="datosFormSocio[OBSERVACIONES]" rows="6" cols="80"><?php
								if (isset($datSocio['datosFormSocio']['OBSERVACIONES']['valorCampo']) && !empty($datSocio['datosFormSocio']['OBSERVACIONES']['valorCampo'])) 
								{
										echo htmlspecialchars($datSocio['datosFormSocio']['OBSERVACIONES']['valorCampo'],ENT_QUOTES);
								}
?></textarea> 
						<span class="error"><strong>
										<?php
										if (isset($datSocio['datosFormSocio']['OBSERVACIONES']['errorMensaje'])) 
										{
														echo $datSocio['datosFormSocio']['OBSERVACIONES']['errorMensaje'];
										}
										?></strong>
							</span>		
      <br />				 
				</p>
		</fieldset>
			
	  <!-- ******************* Fin Datos de Datos de datosFormSocio[OBSERVACIONES] ************************************ --> 	
		<p>

			<span class="comentario11">
			Si necesitas ayuda: 	<strong>info@europalaica.org</strong>, &nbsp;&nbsp;&nbsp;<strong>	Teléfono</strong> <!-- o Whatsapp--> (España): <strong>670 55 60 12 </strong> 
			</span>			

		</p>				
	 <br /><br />
				
	 <!-- ********************** Inicio Botones de form Baja Socio **************************************************** -->  		  
		
	 <input type="submit" name="SiEliminar" onClick="return confirm('SÍ baja socio/a')" value="&nbsp; SÍ Baja  &nbsp;" class="enviar">	
  &nbsp;		&nbsp;		&nbsp; &nbsp;		&nbsp;		&nbsp;
  <input type="submit" name="NoEliminar" onClick="return confirm('NO baja socio/a')" value="&nbsp; NO dar baja  &nbsp;" class="enviar">	
		
	 <!-- ********************** Fin Botones de form Baja Socio ******************************************************* -->  	
		
 </form>					
	
<!-- </div><!--<div id="formLinea"> -->

</div><!-- <div id="registro">-->



