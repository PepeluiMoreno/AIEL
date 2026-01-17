<?php
/*------------------------------------------------------------------------------------------------
FICHERO: formEnviarEmailSociosCoord.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para formar los emails a enviar a los socios, por Coordinador
que permite seleccionar los emails de los socios destinatarios dentro se un área de gestión
(que incluye una o varias agrupaciones). En el formulario dentro de ese área de gestión se puede
seleccionar los socios de sólo una agrupación o todas las agrupaciones de ese área

Además del texto de subject y body (incluye cabecera personalizada y final proteccióndatos), 
puede anexar hasta dos ficheros con un límite de 4MB cada y sólo determinados tipos archivos.

El remitente FROM de envío será el email del área de gestión. Ejem: andalucía@europalaica.org 

Es obligatorio incluir un BCC que recibirá una copia del email.
Después mediante la función "emailSociosPersonaGestorPresCoord()" el email llegará a los socios uno a uno

Además el formulario tiene tres botones de selección que permiten elegir:
-siEnviarEmail: Enviar email a socios/as (seleccionados)
-siPruebaEmail: email de prueba solo a BCC oculta
-noEnviarEmail: Salir sin enviar email
	
LLAMADA: 	vCuerpoEnviarEmailSociosCoord.php (previo desde cCoordinador.php:enviarEmailSociosCoord())

OBSERVACIONES: pongo BCC en lugar de CC
-----------------------------------------------------------------------------------------------*/
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

	
 <?php 	require_once './modelos/libs/comboLista.php';
	//echo "<br><br>1 formContactarEmail:datosContactarEmail: ";print_r($datosContactarEmail) 
 //echo "<br><br>2 formContactarEmail:areaAgrupCoord: ";print_r($areaAgrupCoord) 	
	?> 
<br />

<div id="registro">
			<span class="error">
			<?php
			if (isset($datosEmail['codError']) && ($datosEmail['codError']!=='00000'))
			{echo "<b>ERROR AL ENVÍAR EL EMAIL: revisa los datos con comentarios de error en color rojo</b>";							
			}
			?>
		</span>	
		<br /><br />	
 <!-- ********************* Inicio  agrupación nombre *************** -->
		 <span class='textoAzul9C'>Área de gestión territorial:	</span>			
			<span class='mostrar1'><?php  echo "<b>&nbsp;".$areaAgrupCoord['NOMBREAREAGESTION']."</b>"; ?></span>
	<!-- ********************* Fin agrupación ******************* -->
	<br /><br />	
			<span class="textoAzu112Left2">
			No se enviarán emails a los socios/as que tengan el campo "INFORMACIÓN EMAIL = NO"
			<br /><br />
			Nota: Algunos servidores de correo pueden tratar como "SPAM" los emails de Europa Laica 				
			<br /><br />					
  </span>
 <!-- ****************** enctype="multipart/form-data" para subir archivos ************************ -->	
 <form name="enviarEmailSociosCoord" method="post"
    action="./index.php?controlador=cCoordinador&amp;accion=enviarEmailSociosCoord" enctype="multipart/form-data">
				
	<!-- añado -->
	    <input type="hidden" 			      
												name="areaAgrupCoord[CODAREAGESTIONAGRUP]"
	           value='<?php if (isset($areaAgrupCoord['CODAREAGESTIONAGRUP']))
	           {  echo $areaAgrupCoord['CODAREAGESTIONAGRUP'];}
	           ?>' 
	    />	
	    <input type="hidden" 			      
												name="areaAgrupCoord[EMAIL]"
	           value='<?php if (isset($areaAgrupCoord['EMAIL']))
	           {  echo $areaAgrupCoord['EMAIL'];}
	           ?>' 
	    />						
 <!-- añado -->				
		
	 <!-- ****************** Inicio Datos  ********************************* -->	
	
		<fieldset>
	  <legend><b>Texto del email</b></legend>	  
	  <p>
	   <label>Área de gestión territorial:</label> 
	    <input type="text" readonly
						      class="mostrar"		
												name="areaAgrupCoord[NOMBREAREAGESTION]"
	           value='<?php if (isset($areaAgrupCoord['NOMBREAREAGESTION']))
	           {  echo $areaAgrupCoord['NOMBREAREAGESTION'];}
	           ?>' 
	           size="50"
	           maxlength="100"
	    />	
					<br /><br />
	   <label>Email de envío (desde, from)</label> 
			  <input type="text" readonly
						      class="mostrar"		
												name="datosEmail[camposEmail][FROM]"
	           value='<?php if (isset($areaAgrupCoord['EMAIL']))
	           {  echo $areaAgrupCoord['EMAIL'];}
	           ?>'
	           size="50"
	           maxlength="100"	  
	    />	
					<br />		<br />				
					
			 <label>*<b>BCC copia oculta</b> (sólo un email)</label> 
	    <input type="text"	
	           name="datosEmail[camposEmail][BCC]"
	           value='<?php if (isset($datosEmail['camposEmail']['BCC']['valorCampo']) )
	                        {  echo htmlspecialchars($datosEmail['camposEmail']['BCC']['valorCampo'],ENT_QUOTES);	}
	                  ?>'
	           size="80"
	           maxlength="180"
	    />			
		<span class="error"><strong> 
			<?php
			if (isset($datosEmail['camposEmail']['BCC']['errorMensaje']))
			{echo $datosEmail['camposEmail']['BCC']['errorMensaje'];}
			?></strong> 
		</span>							
  <br />			
			 <label>*Asunto</label> 
	    <input type="text"	
	           name="datosEmail[camposEmail][subject]"
	           value='<?php if (isset($datosEmail['camposEmail']['subject']['valorCampo']))
	                        {  echo htmlspecialchars($datosEmail['camposEmail']['subject']['valorCampo'],ENT_QUOTES);	}																									
	                  ?>'
	           size="100"
	           maxlength="120"
	    />	
				<span class="error"><strong> 
			<?php
			if (isset($datosEmail['camposEmail']['subject']['errorMensaje'])  && !empty($datosEmail['camposEmail']['subject']['errorMensaje']) )
			{echo $datosEmail['camposEmail']['subject']['errorMensaje'];}
			?></strong> 
		 </span>	
		  <br />	
					
		 	<label><b>Encabezado</b> (se incluirá automáticamente antes del contenido del email la línea: )</label>
		  <span class="mostrar">Estimado socio (o Estimada socia) <strong>Nombre y apellidos,</strong>
		  </span>
		
		  <br />
		  <label>*Contenido del email</label>
		  <br />
		  <textarea  id='BODY' onKeyPress="limitarTextoArea(8000,'BODY');"	
		class="textoAzul8Left" name="datosEmail[camposEmail][body]" rows="14" cols="120"><?php 
		  if (isset($datosEmail['camposEmail']['body']['valorCampo']))                    
			{echo htmlspecialchars($datosEmail['camposEmail']['body']['valorCampo']);}
		?></textarea> 
				<span class="error"><strong> 
			<?php
			if (isset($datosEmail['camposEmail']['body']['errorMensaje']) && !empty($datosEmail['camposEmail']['body']['errorMensaje']) )
			{echo $datosEmail['camposEmail']['body']['errorMensaje'];}
			?></strong> 
		</span>		
		  <br />	
					
			<label>Pie Protección de Datos (se añadirá al final del contenido del email)</label>
   <br />			
			  <textarea  id='BODY' onKeyPress="limitarTextoArea(8000,'BODY');"	
		<textarea type="text" readonly class="mostrar1" wrap="hard" name="datosEmail[camposEmail][pieProteccionDatos]" 
		          rows="7" cols="120"><?php 
		                
			{echo "
\n*****************************
PROTECCION DE DATOS PERSONALES
\nEn Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679).
Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace:
\n https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad
";}
		?></textarea>					
					
			<span class="error">
			<?php
			if (isset($datosEmail['camposEmail']['pieProteccionDatos']['errorMensaje']))
			{echo $datosEmail['camposEmail']['pieProteccionDatos']['errorMensaje'];}
			?>
		</span>			
		
		 </p>
	  </fieldset>
				<br />	
			 <fieldset>
					<legend><b>Si quieres puedes anexar hasta 2 archivos (máximo 4 MB cada archivo)</b></legend>
					<p>

						<span class="error">							
						<strong>	
						
						<?php	
						//se introdujo valores para ficheros, y hay que volver a introducir de nuevo, pues no admite values
						if (isset($datosEmail['AddAttachment']['errorMensaje']) && !empty($datosEmail['AddAttachment']['errorMensaje']) )
						{ echo $datosEmail['AddAttachment']['errorMensaje'];
								echo "<br />";
								//echo "AVISO: No hay archivos adjuntos para enviar, si quieres puedes añadirlos ahora (cada archivo máximo 2 MB)";
						}
						?>	
						</strong>

						"AVISO: No hay archivos adjuntos para enviar, si quieres puedes añadirlos ahora (cada archivo máximo 4 MB)"			
						</span>
						<br />			
							<label>Archivo1</label>
								<input type="file"
															name="FICHERO1" size="80"				
								/>
							<br />
							<label>Archivo2</label>
								<input type="file"
															name="FICHERO2" size="80"
								/>
							<!-- <br />	
							<label>Archivo3</label>
								<input type="file"
															name="FICHERO3" size="80"	
								/>	
								-->
							<br /><br />								
							<span class="textoAzu112Left2">
        <b>NOTA:</b> Los archivos grandes relentizán el proceso de envío.
       </span>
							<br />							
					</p>
	   </fieldset>
				<br />				<br />		
				
		  <span class="error"><b>NOTA:</b>&nbsp; </span>				

				<span class="textoAzu112Left2">
       Al ser emails personalizados se enviarán uno a uno, por eso el proceso puede tardar varios minutos, dependiendo del número de socios/as en la agrupación. 
							<br />
				   Ten paciencia y no salgas de "Email a soci@s" hasta que en la pantalla se confirme que ya 
							se han enviado o que indique que se ha producido algún error.	
       </span>				
	 	 							
		  <br />	<br />					
				
			 <fieldset>
					<legend><b>*Selección de socios/as para emails</b></legend>
					<p>		
		
		  	<span class="textoAzu112Left2">
		   	Puedes elegir una agrupación o "Todas" para incluir a todas las agrupaciones pertenecientes al
				 	Área de gestión territorial de "<?php echo $areaAgrupCoord['NOMBREAREAGESTION']; ?>")
		  	</span> 
				 <br /><br />
				 <label>Agrupación territorial</label>		
					 <?php 
						 //echo "<br /><br />parValorComboAgrupaSocio: ";print_r($parValorComboAgrupaSocio); echo "<br /><br />";
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)

						 $parValorComboAgrupaSocio['lista']['-'] = "----------";
							$parValorComboAgrupaSocio['lista']['Elige'] = "Elige agrupación";
       //añado if para evitar notices																																							
							if (!isset($parValorComboAgrupaSocio['valorDefecto']) ||  empty($parValorComboAgrupaSocio['valorDefecto']))
							{
						  $parValorComboAgrupaSocio['valorDefecto'] = 'Elige';
							}						
       //echo '<br><br>dentro form:parValorComboAgrupaSocio: ';print_r($parValorComboAgrupaSocio);	
							
							echo comboLista($parValorComboAgrupaSocio['lista'], "datosEmail[datosSelecionEmailSocios][CODAGRUPACION]",
                       $parValorComboAgrupaSocio['valorDefecto'],
																							$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],
																							"","");	
      ?>
							<span class="error"><strong> 
							<?php
							if (isset($datosEmail['datosSelecionEmailSocios']['errorMensaje']))
							{echo $datosEmail['datosSelecionEmailSocios']['errorMensaje'];}

							?></strong> 
						 </span>		
       <br />	
	 			</p>
	   </fieldset>
				<br />					

  <br />			
			<span class="textoAzu112Left2">
			<strong>Nota:</strong> Si eliges el botón "Enviar email de prueba solo a BCC", también tienes que hacer la "Selección de socios/as para emails", 		
				y te mostrará en pantalla a cuántos socios/as se habría enviado el mismo email en caso de haber elegido "Envíar email a socios/as"		
			</span>
		<br />				

  <div align="center">
		  <br />		
				<span class="error">
				<?php
				if (isset($datosEmail['codError']) && ($datosEmail['codError']!=='00000'))
				{echo "<b>ERROR AL ENVÍAR EL EMAIL: revisa los datos con comentarios de error en color rojo</b>";							
				}
				?>
			</span>			
   <input type="submit" name="siEnviarEmail" 
	  onClick="return confirm('¿Enviar email a socios/as?')"			
			value="Enviar email a socios/as" class="enviar" />
		  &nbsp;		&nbsp;		&nbsp;
				
   <input type="submit" name="siPruebaEmail" 
	  onClick="return confirm('¿Enviar email de prueba solo a BCC?')"			
			value="Enviar email de prueba solo a BCC" class="enviar" />			
		  &nbsp;		&nbsp;		&nbsp;				
				
  	<input type="submit" name="noEnviarEmail" 
		  onClick="return confirm('¿Salir sin enviar email a socios/as?')"
		  value='Cancelar enviar email' />
	<!--	<input onclick=window.close(); type=button value="Cancelar" />-->	
	 </div>			
		
 </form> 
<!-- ********************** Fin formEnviarEmailSociosPres.php *************** -->  	
</div>
