<?php
/*-----------------------------------------------------------------------------
FICHERO: formContactarEmail.php
PROYECTO: EL
VERSION: PHP 7.3.21

Formulario para que el usuario introduzca y envíe un email 
a "info@europalaica.com" para pedir información

También muestra mensajes de error

LLAMADA: vistas/pie/vCuerpoContactarEmail.php
									esde el "pie", abajo de toda la pantalla, la barra horizontal "Contactar"
         formulario "vContactarEmailInc.php"
LLAMA: Envía POST a cEnlacesPie.php:contactarEmail()
           
-------------------------------------------------------------------------------*/
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
<!--		<span class="error">
			if (isset($datosContactarEmail['errorMensaje']))
			{echo $datosContactarEmail['errorMensaje'];
		 }
			?>
		</span>	
		-->

 <form name="registrarSimp" method="post" action="./index.php?controlador=cEnlacesPie&amp;accion=contactarEmail">
		
	 <!-- ****************** Inicio Datos  ********************************* -->	
	 <fieldset>
	 <legend><b>Enviar un correo electrónico</b></legend>
		<p>
			<span class="comentario11"> 
	   Campos con asterisco (<b>*</b>) son obligatorios
   </span>
			<br /><br />				
	 
		 	<label>Enviar a </label> 
	    <!--<input type="text" readonly
					       class="mostrar"		
	           name="datosContactarEmail[emailDestino]"
												value="info@europalaica.org"
	           size="35"
	    />	-->
	    <input type="text" readonly
            class="mostrar"	
	           name="datosContactarEmail[emailDestino]"
	           value="<?php if (isset($datosContactarEmail['emailDestino']['valorCampo']))
	                  {  echo $datosContactarEmail['emailDestino']['valorCampo'];
																	  }
	                  ?>"
																			size="35"
	                  maxlength="100"
	    />						
					<br /> 
	    <input type="hidden"
	           name="datosContactarEmail[nombreEmailDestino]"
	           value="<?php if (isset($datosContactarEmail['nombreEmailDestino']['valorCampo']))
	                  {  echo $datosContactarEmail['nombreEmailDestino']['valorCampo'];
																 	 }
	                  ?>"
	    />			
					
	   <label>*Tu nombre</label> 
	    <input type="text"
	           name="datosContactarEmail[NOMBRE]"
	           value='<?php if (isset($datosContactarEmail['NOMBRE']['valorCampo']))
	                 {  echo $datosContactarEmail['NOMBRE']['valorCampo'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
	 	<span class="error">
			<?php
			if (isset($datosContactarEmail['NOMBRE']['errorMensaje']))
			{echo $datosContactarEmail['NOMBRE']['errorMensaje'];}
	 		?>
	 	</span>			
		  <br />
		  <label>*Tu correo electrónico</label>
	    <input type="text"
	           name="datosContactarEmail[EMAIL]"
	           value='<?php if (isset($datosContactarEmail['EMAIL']['valorCampo']))
	           {  echo $datosContactarEmail['EMAIL']['valorCampo'];}
	           ?>'
	           size="40"
	           maxlength="200"
	    />	 
	 	<span class="error">
			<?php
			if (isset($datosContactarEmail['EMAIL']['errorMensaje']))
			{echo $datosContactarEmail['EMAIL']['errorMensaje'];}
			?>
		 </span>	
	  <br />		
		  <label>*Repetir tu correo electrónico</label>
	    <input type="text"
	           name="datosContactarEmail[REMAIL]"
	           value='<?php if (isset($datosContactarEmail['REMAIL']['valorCampo']))
	           {  echo $datosContactarEmail['REMAIL']['valorCampo'];}
	           ?>'
	           size="40"
	           maxlength="200"
	    />	 
	 	<span class="error">
			<?php
			if (isset($datosContactarEmail['REMAIL']['errorMensaje']))
			{echo $datosContactarEmail['REMAIL']['errorMensaje'];}
			?>
	 	</span>				

		 <br />	
	   <label>*Asunto</label>
	    <input type="text"
	           name="datosContactarEmail[ASUNTO]"
	           value='<?php if (isset($datosContactarEmail['ASUNTO']['valorCampo']))
	                 {  echo $datosContactarEmail['ASUNTO']['valorCampo'];}
	                  ?>'
	           size="80"
	           maxlength="100"
	     />	 
		 <span class="error">
			<?php
			if (isset($datosContactarEmail['ASUNTO']['errorMensaje']))
			{echo $datosContactarEmail['ASUNTO']['errorMensaje'];}
			?>
		 </span>
		
		 <br /><br />

				<legend><strong>*Texto del mensaje</strong></legend>
    <p>
					<textarea  id='COMENTARIOSOCIO' onKeyPress="limitarTextoArea(250,'COMENTARIOSOCIO');"	
					class="textoAzul8Left" name="datosContactarEmail[TEXTOMENSAJE]" rows="3" cols="80"><?php 
							if (isset($datosContactarEmail['TEXTOMENSAJE']['valorCampo']))                    
						{echo htmlspecialchars(stripslashes($datosContactarEmail['TEXTOMENSAJE']['valorCampo']));}
					?></textarea> 
						<span class="error">
						<?php
						if (isset($datosContactarEmail['TEXTOMENSAJE']['errorMensaje']))
						{echo $datosContactarEmail['TEXTOMENSAJE']['errorMensaje'];}
						?>
					</span>	

	   </p>
				
			</p>	
	 </fieldset>
  <br />				
	
		<fieldset>
				<legend><b>*Protección de tus datos personales</b></legend>			
				<p>
							<a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" target="_blank" title="Privacidad de datos" 
															onclick="ventanaSecundaria(this); return false">	>>Información sobre la protección de datos   
							</a>	
							<br />	
					<span class="comentario11">Europa Laica sólo utilizará los datos que ahora nos dejas para responder a tu email 
					sin almacenar después ningún dato personal tuyo.				
					</span>
     <br />	<br />	
					
					<span class="comentario11">																				
						<strong>*Al hacer clic aquí acepto el tratamiento de mis datos personales por Europa Laica.</strong> 	
					</span>
					<input type="checkbox" 
											name="datosContactarEmail[privacidad]"
											value="SI"
											<?php
											if (isset($datosContactarEmail['privacidad']['valorCampo']) && $datosContactarEmail['privacidad']['valorCampo'] == 'SI') 
											{
													echo " checked='checked'";
											}
											?>
											/>
				 <span class="error">
						<?php
						if (isset($datosContactarEmail['privacidad']['errorMensaje'])) 
						{
								echo $datosContactarEmail['privacidad']['errorMensaje'];
						}
						?>
					</span>
					<br />	
					<!--<span class="comentario11">
						*Al hacer clic aquí acepto la política de privacidad y de tratamiento de mis datos personales por Europa Laica. 	
							<a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" target="_blank" title="Privacidad de datos" 
															onclick="ventanaSecundaria(this); return false">	>>Información sobre la protección de datos   
									</a>								
					</span>			-->				
						<br /> 
	  </p>						
		</fieldset>			

  <br /> 
	 <!-- ********************** Fin Datos *************** -->  
  <div align="center">
   <input type="submit" name="siEnviarEmail" value="Enviar email a Europa Laica" class="enviar" />
		  &nbsp;		&nbsp;		&nbsp;
  	<!--<input type="submit" name="noEnviarEmail" 
		  onClick="return confirm('¿Salir sin enviar el correo electrónico a Europa Laica?')"
		  value='Cancelar enviar email' />
			-->	
  	<input onclick="window.close();" type="button" value="Cancelar enviar email" class="enviar"/>	
	 </div>	
			
		<span class="comentario11">
   <br /><b>¡ TUS DATOS NO SERÁN UTILIZADOS CON OTROS FINES NI SERÁN CEDIDOS A TERCEROS !</b>	
  </span>
  <br />
		
 </form> 
	
</div><!-- <div id="registro"> -->
