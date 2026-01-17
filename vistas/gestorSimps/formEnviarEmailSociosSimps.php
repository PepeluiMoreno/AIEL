<?php
/*---------------------------------------------------------------------------------------------------------------
FICHERO: formEnviarEmailSociosSimps.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:													
Formulario de selección para formar los emails a enviar a los socios desde rol de Gestor de Simpatizantes, 
que permite seleccionar los emails de los socios destinatarios por:	CODAGRUPACION,CODPAISDOM, CCAA, CODPROV.

Además del texto de subject y body, puede anexar hasta dos ficheros con un límite de 4MB cada y sólo 
determinados tipos archivos.

Permite elegir entre los siguientes emails de envío FROM: "info@europalaica.org, 

Es obligatorio incluir un BCC que recibirá una copia del email.

Además el formulario tiene tres botones de selección que permiten elegir:
-1. Enviar emails PERSONALIZADOS a socios/as: se enviarán uno a uno y personlizados con nombres socios (lento)
-2. Enviar emails NO PERSONALIZADOS a socios/as: se enviarán todos los emails a la vez y sin personalizar (más rápido)
-3. Enviar email de prueba solo a BCC: al final mostrará en pantalla a cuántos socios/as se habría enviado el email
- Cancelar enviar emails: Salir sin enviar email

LLAMADA: vistas/gestorSimps/vCuerpoEnviarEmailSociosSimps.php y antes desde cGestorSimps.php:enviarEmailSimpsGes()	

OBSERVACIONES: Es un clon de la función  formEnviarEmailSociosPresInc.php

Sustituyo CC por BCC

--------------------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';

?>

<!-- Para campo textarea, además hay control interno en php -->
<script type="text/javascript">
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
</script>
<!-- ***************************************************** -->

<div id="registro">
			<span class="error">
			<?php
			if (isset($datosEmail['codError']) && ($datosEmail['codError']!=='00000'))
			{echo "<b>ERROR AL ENVÍAR EL EMAIL: revisa los datos con comentarios de error en color rojo</b>";							
			}
			?>
		</span>	
		<br />
		
		<span class="textoAzu112Left2">
  Nota: No se enviarán emails a los socios/as que tengan el campo "INFORMACIÓN EMAIL = NO"
  <br />				
  </span>
		<br />	
 <!-- ****** Inicio form para emay con enctype="multipart/form-data" para subir archivos ********************* -->	
	
 <form name="registrarSimp" method="post"
       action="./index.php?controlador=cGestorSimps&amp;accion=enviarEmailSimpsGes" enctype="multipart/form-data">
							
	 <!-- ****************** Inicio Datos y Contenido del email  ********************************************* -->	
	
		<fieldset>
	  <legend><b>Email</b></legend>
	  <p>
			
   <!-- ********** Inicio Selección de remitente (Se podrían añadir más remitentes para elegir) ******* -->						
	   <label>Email de envío (desde, from)</label> 
			  <input type="text" readonly
						      class="mostrar"		
												name="datosEmail[camposEmail][FROM]"
	           value='info@europalaica.org'
	           size="50"
	           maxlength="100"	  
	    />
				<br />			
			<!--
  <label>*<b>Elegir remitente (desde, from, ...)</b></label>	
		   <span class="error">
			  <?php
			  //if (isset($datosEmail['camposEmail']['FROM']['errorMensaje']))
			  //{echo $datosEmail['camposEmail']['FROM']['errorMensaje'];}
			  ?>
				</span>-->
	    <!--<input type="radio"
	           name="datosEmail[camposEmail][FROM]"
	           value='europalaica@europalaica.org' 
						 <?php //if ($datosEmail['camposEmail']['FROM']['valorCampo'] == 'info@europalaica.org')
	            //{  echo " checked";}
	           ?>
	    /><label>info@europalaica.org</label>
					-->
				<!--	
	    <input type="radio"
	           name="datosEmail[camposEmail][FROM]"
	           value='asamblea@europalaica.org' 
						 <?php //if ($datosEmail['camposEmail']['FROM']['valorCampo']=='asamblea@europalaica.org')
	           //{  echo " checked";}
	           ?>/>
				<label>asamblea@europalaica.org</label>			
				-->			 
   <!-- ************** Fin Selección de remitente ****************************************************** -->					
			
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
				
			 <label>*<b>Asunto</b></label> 
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
						if (isset($datosEmail['camposEmail']['subject']['errorMensaje']) && !empty($datosEmail['camposEmail']['subject']['errorMensaje']))
						{echo $datosEmail['camposEmail']['subject']['errorMensaje'];}
						?></strong> 
					</span>	

		    <br />
						
		  <label>*<b>Contenido del email</b></label>
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
					
			<label>Pie Protección de Datos (se añadirá automáticamente al final del contenido del email)</label>
    <br />	
				<textarea type="text" readonly class="mostrar" wrap="hard" name="datosEmail[camposEmail][pieProteccionDatos]" rows="6" cols="120"><?php		                
				{echo "
\n*****************************
PROTECCION DE DATOS PERSONALES
En Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679).
Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Puede ver mas información en:
https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad
"; }
		?>
				</textarea>					
						
				<span class="error">
					<?php
					if (isset($datosEmail['camposEmail']['pieProteccionDatos']['errorMensaje']))
					{echo $datosEmail['camposEmail']['pieProteccionDatos']['errorMensaje'];}
					?>
				</span>			
			
		 </p>
	 </fieldset>
		
	 <!-- ****************** Fin Datos y Contenido del email  ************************************************ -->			
		<br />	
  <!-- ************** Inicio Selección de archivo ********************************************************* -->							
		<fieldset>
			<legend><b>Si quieres puedes anexar hasta dos archivos (cada archivo máximo 4 MB )</b></legend>
			<p>
				
				<span class="error">	       
				<strong>	
				
				<?php				
				//si se introdujo valores para ficheros, y hay que volver a introducir de nuevo, pues no lo admite values			
				
				if (isset($datosEmail['AddAttachment']['errorMensaje']) && !empty($datosEmail['AddAttachment']['errorMensaje']))
				{ echo $datosEmail['AddAttachment']['errorMensaje'];
						echo "<br />";
					
				}
				?>	
				</strong>			
				"AVISO: No hay archivos adjuntos para enviar, si quieres puedes añadirlos ahora (cada archivo máximo 4 MB)"									
				</span>
				
				<br />			
				<label>Archivo1</label>
					<input type="file" name="FICHERO1" size="80"				
					/>
								
				<br />
				<label>Archivo2</label>
					<input type="file"
												name="FICHERO2" size="80"
					/>

						<br /><br />	
					
					<span class="textoAzu112Left2">
						<b>NOTA:</b> Los archivos grandes relentizan el proceso de envío.
						
					</span>
					<br />	
			</p>
	 </fieldset>
  <!-- ************** Fin Selección de archivo ************************************************************ -->					
			<br /><br />	
		<!-- ************** Inicio Selección de socios/as para emails ******************************************* -->		
		<fieldset>
    
			<legend><b>*Selección de socios/as para emails (elige una y solo una opción las demás deberán poner "Ninguna")</b></legend>

			<p>
						<span class="error"><strong>
							<?php
							if (isset($datosEmail['datosSelecionEmailSocios']['errorMensaje']))
							{echo $datosEmail['datosSelecionEmailSocios']['errorMensaje'];}
							?></strong>
						</span>
      <br />	
						
				<label>Por agrupación</label>		
					 <?php 
			
       //------- Ordenada la lista agrupaciones para mejorar presentación ------------------------

						 $parValorComboAgrupaSocio['lista']['-']="----------";//como separador

						 $parValorComboAgrupaSocio['lista']['NINGUNA']="Ninguna";		
	
							//------------------------------------------------------------------------------------------
       //echo '<br><br>dentro form:parValorComboAgrupaSocio: ';print_r($parValorComboAgrupaSocio);
							if (!isset($parValorComboAgrupaSocio['valorDefecto']) || empty($parValorComboAgrupaSocio['valorDefecto']))
							{
								$parValorComboAgrupaSocio['valorDefecto'] = 'NINGUNA';
							}
							
							echo comboLista($parValorComboAgrupaSocio['lista'],"datosEmail[datosSelecionEmailSocios][CODAGRUPACION]",
																						$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
      ?>
		  <br /><br />			
				
				<label>Por país domicilio</label>		
	     <?php
						$parValorComboPaisDomicilio['lista']['-']="----------";
						$parValorComboPaisDomicilio['lista']['NINGUNA']="Ninguno";

						if (!isset($parValorComboPaisDomicilio['valorDefecto']) || empty($parValorComboPaisDomicilio['valorDefecto']))
						{
							$parValorComboPaisDomicilio['valorDefecto']= 'NINGUNA';
						}					
	
	     echo comboLista($parValorComboPaisDomicilio['lista'], "datosEmail[datosSelecionEmailSocios][CODPAISDOM]",
	        	           //$parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],
																					 $parValorComboPaisDomicilio['valorDefecto'],
																						$parValorComboPaisDomicilio['lista'][$parValorComboPaisDomicilio['valorDefecto']],"","");
	     ?> 
			  <br />	
					
				<label>Por CCAA domicilio (para ES)</label>		
				<?php
				  $parValorComboCCAADomicilio['lista']['-']="----------";
				  $parValorComboCCAADomicilio['lista']['NINGUNA']="Ninguna";
						//echo '<br><br>dentro form:parValorComboCCAADomicilio: ';print_r($parValorComboCCAADomicilio);
					
					if (!isset($parValorComboCCAADomicilio['valorDefecto']) || empty($parValorComboCCAADomicilio['valorDefecto']))
						{
							$parValorComboCCAADomicilio['valorDefecto'] = 'NINGUNA';
						}						
	
						echo comboLista($parValorComboCCAADomicilio['lista'], "datosEmail[datosSelecionEmailSocios][CCAA]",
                      $parValorComboCCAADomicilio['valorDefecto'],
																						$parValorComboCCAADomicilio['lista'][$parValorComboCCAADomicilio['valorDefecto']],"","");
      ?>
		   <br />	
					
				<label>Por provincia domicilio (para ES)</label>		
				<?php
				  $parValorComboProvDomicilio['lista']['-']="----------";
				  $parValorComboProvDomicilio['lista']['NINGUNA']="Ninguna";				
						//echo '<br><br>dentro form:parValorComboProvDomicilio: ';print_r($parValorComboProvDomicilio);
						
						if (!isset($parValorComboProvDomicilio['valorDefecto']) || empty($parValorComboProvDomicilio['valorDefecto']))
						{
							$parValorComboProvDomicilio['valorDefecto']= 'NINGUNA';
						}											
							
						echo comboLista($parValorComboProvDomicilio['lista'], "datosEmail[datosSelecionEmailSocios][CODPROV]",
                      $parValorComboProvDomicilio['valorDefecto'],
																						$parValorComboProvDomicilio['lista'][$parValorComboProvDomicilio['valorDefecto']],
 																					"NINGUNA","Elige");//			"NINGUNA","Ninguna");
      ?>
		   <br />	
	 	</p>
	 </fieldset>
  <!-- ************** Fin Selección de socios/as para emails ********************************************** -->		
		  <br />
  <!-- ************** Inicio texto aclaraciones  ********************************************************** -->
		<span class="error"><b>A C L A R A C I O N E S :</b>&nbsp; </span>	
  <br />		

		<span class="textoAzu112Left2">		

			<ul>
					<li>Si eliges enviar emails <b>PERSONALIZADOS</b>, los emails se enviarán uno a uno y delante del texto que hayas escrito en "Contenido del email" se incluirá el siguiente encabezado:
					<br /><br />
					<i><b>Estimado socio (o Estimada socia) Nombre y Apellidos,</b></i>
					<br /><br />Dependiendo del número de socios/as seleccionados el proceso puede tardar varios minutos, 
					por eso en lugar de seleccionar "Todas" es aconsejable fraccionar el envío por agrupaciones o por CCAA.    
					<br />Ten paciencia y no salgas de "Email a soci@s" hasta que en la pantalla se confirme que ya se han enviado o que indique que se ha producido algún error.	
					<br /><br />
					</li>
					
					<li>Si eliges enviar emails <b>NO PERSONALIZADOS</b>, no se incluirá ningún encabezado con los nombres delante del texto escrito en  "Contenido del email".
					<br /><br />						
					Se enviarán todos los emails a la vez, y el proceso es más rápido, pero también debes esperar hasta que en la pantalla se confirme que ya se han enviado
					o que indique que se ha producido algún error.
					<br /><br />						
					</li>
					
					<li></strong>Si eliges "Enviar <b>email de prueba solo a BCC</b>", también tienes que hacer la "Selección de socios/as para emails", 		
					y al final te mostrará en pantalla a cuántos socios/as se habría enviado el mismo email en caso de haber elegido 
					"Enviar emails PERSONALIZADOS a socios/as" o "Enviar emails NO PERSONALIZADOS a socios/as ".
					</li>		
			</ul>	
				
		</span>
	 <!-- ************** Fin texto aclaraciones  ************************************************************* -->
		
		<span class="error">
				<?php
				if (isset($datosEmail['codError']) && ($datosEmail['codError']!=='00000'))
				{echo "<b>ERROR AL ENVÍAR EL EMAIL: revisa los datos con comentarios de error en color rojo</b>";							
				}
				?>
		</span>		
		
		<br />	
  <!-- ****** Inicio botones form envío email  ************************************************************ -->			
  <div align="center">
			
   <input type="submit" name="enviarEmailPersonalizado" 
	  onClick="return confirm('¿Enviar emails Personalizados a socios/as?')"			
			value="Enviar emails PERSONALIZADOS a socios/as" class="enviar" />
		  &nbsp;		&nbsp;		&nbsp;
				
			<!-- Se podría activar para incluir esta opción que está disponible en cPresidente.php:enviarEmailSociosPres()	-->
   <input type="submit" name="enviarEmailNoPersonalizado" 
	  onClick="return confirm('¿Enviar emails No Personalizados a socios/as?')"			
			value="Enviar emails NO PERSONALIZADOS a socios/as" class="enviar" />
		  &nbsp;		&nbsp;		&nbsp;				
			
   <input type="submit" name="siPruebaEmail" 
	  onClick="return confirm('¿Enviar un email de prueba solo a BCC?')"			
			value="Enviar email de prueba solo a BCC" class="enviar" />			
		  
   <br /><br />				
				
  	<input type="submit" name="noEnviarEmail" 
		  onClick="return confirm('¿Salir sin enviar emails a socios/as?')"
		  value='Cancelar enviar emails' />
				
	 <!--	<input onclick=window.close(); type=button value="Cancelar" />-->	
	 </div>				
  <!-- ****** Fin botones form envío email  *************************************************************** -->	
		
 </form> 
 <!-- ****** Fin form para email con enctype="multipart/form-data" para subir archivos ************************ -->	
</div>
