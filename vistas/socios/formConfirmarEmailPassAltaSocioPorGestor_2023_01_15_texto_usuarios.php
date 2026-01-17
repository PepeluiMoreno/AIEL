<?php
/*------------------------------------------------------------------------------
FICHERO: formConfirmarEmailPassAltaSocioPorGestor.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para para que el usuario elija la contraseña.
y recibe $restablecerPass['datosFormUsuario']['CODUSER'], que contiene el 
"$codUserEncriptado" por seguridad para enviarlo como parámetro. 

LLAMADA: vCuerpoConfirmarEmailPassAltaSocioPorGestorInc.php, 
que procede de controladorSocios.php:confirmarEmailPassAltaSocioPorGestor()

OBSERVACIONES: controladorSocios.php:confirmarEmailPassAltaSocioPorGestor()
------------------------------------------------------------------------------*/
?>
<div id="registro">
  <br /> 
	<!-- <span class="textoAzu112Left2">-->
	<span class="textoNegro9Left">
		<br /><br /><br />
		 Estimado socio/a 
			<strong>
			<?php if (isset($restablecerPass['datosFormMiembro']['NOM']) && !empty($restablecerPass['datosFormMiembro']['NOM']) && 
													isset($restablecerPass['datosFormMiembro']['APE1']) && !empty($restablecerPass['datosFormMiembro']['APE1']) )
									{ echo $restablecerPass['datosFormMiembro']['NOM']." ".$restablecerPass['datosFormMiembro']['APE1'];}
			?>
			</strong>
			<br /><br />
   Atendiendo tu petición y con tu autorización, un gestor/a de socios/as de Europa Laica te dio de alta en nuestra asociación.			
			<br /><br /><br />
			Para poder acceder a la zona privada del "Área de Soci@s" de Europa Laica
			necesitas tu 'Usuario' <strong>
			<?php if(isset($restablecerPass['datosFormUsuario']['USUARIO']) && !empty($restablecerPass['datosFormUsuario']['USUARIO']))
					    { echo ": ".$restablecerPass['datosFormUsuario']['USUARIO'];}
			?>	
   </strong>
			, que ya te hemos enviado en el correo electrónico.			
			<br /><br /><br />Además necesitas una <strong>'Contraseña'</strong> que debes elegir ahora y recordarla.			
			<br /><br />Al guardar la contraseña, de forma automática también se confirmará tu email.
			<br /><br /><br /><br />Una vez entres en el "Área de Soci@s", podrás comprobar tus datos y completarlos o 
			corregirlos si fuese necesario y también podrás cambiar tu 'Usuario' y 'Contraseña'
 </span>

	<br /><br /><br />

 <form name="actualizarSocio" method="post"
   action="./index.php?controlador=controladorSocios&amp;accion=confirmarEmailPassAltaSocioPorGestor&amp;parametro=<?php echo $restablecerPass['datosFormUsuario']['CODUSER']; ?>">
			
 <!-- $restablecerPass['datosFormUsuario']['CODUSER'] contiene el $codUserEncriptado por seguridad para enviarlo como parámetro	-->
				
		<!--****************** Inicio Datos de identificación USUARIO ************-->
	 <fieldset>	  
  	<p>
		<span class="comentario11">
	   La contraseña debe tener seis caracteres como mínimo (distingue entre mayúsculas y minúsculas)			
		</span>
			<br /><br />	  
		<label>*Contraseña&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
	  <input type="password" 
	         name="datosFormUsuario[PASSUSUARIO]"
	         value='<?php /*if (isset($restablecerPass['datosFormUsuario']['PASSUSUARIO']['valorCampo']))
	                        {  echo $restablecerPass['datosFormUsuario']['PASSUSUARIO']['valorCampo'];}
	                  */?>'
	         size="20"
	         maxlength="40"
	     />
		 <span class="error">
			<?php
			  if (isset($restablecerPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje']))
		    {echo $restablecerPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'];}
			?>
		</span>
	  <br />	
	  <label>*Repetir contraseña</label> 
	    <input type="password"
	           name="datosFormUsuario[RPASSUSUARIO]"
	           value='<?php /*  if (isset($restablecerPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo']))
	                        {  echo $restablecerPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo'];}
	                  */?>'
	           size="20"
	           maxlength="40"
	    /> 			
		<span class="error">
			<?php
			  if (isset($restablecerPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']))
		    {echo $restablecerPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'];}
			?>
		</span>
		<br /><br />		
	
		<!--********************** Fin Datos de identificación USUARIO ***************-->  
		</p>
	 </fieldset>
	 <br />
	 <div align="center">
	   <input type="submit" name="cambiarPass" value="Guardar contraseña" class="enviar" />	
	   <input type="submit" name="cancelarCambiarPass" value="Cancelar elegir contraseña" class="enviar" />	
	 </div>
 </form>
		<br />
</div>
