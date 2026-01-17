<?php
/*------------------------------------------------------------------------------
FICHERO: formRestablecerPass.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Es el formulario para introducir el restablecer la contraseña
             del usuario llamado desde el email recibido por el usuario,
													por petición de recordar contraseña.
OBSERVACIONES: Es incluido desde vCuerpoRestablecerPass.php
------------------------------------------------------------------------------*/
?>
<div id="registro">
  <br /> 
	<!-- <span class="textoAzu112Left2">-->
	
	<span class="textoNegro8Left">
		<br />
		 Por motivos de seguridad debes elegir un nueva contraseña	para entrar 
	   en la aplicación de usuarios de Europa Laica
 </span>
	
	<br /><br />
	
	<span class="comentario11">
	 La contraseña debe tener seis caracteres como mínimo (distingue entre mayúsculas y minúsculas)			
		<br /><br />
		Elige una nueva contraseña y la introduces a continuación		
	</span>
	<br /><br /><br /><br /><br />
 <form name="actualizarSocio" method="post"
    action="./index.php?controlador=controladorLogin&amp;accion=restablecerPass&amp;parametro=<?php echo $restablecerPass['datosFormUsuario']['CODUSER']; ?>">
		<?php //print_r($error);?>
	 
	 <!--****************** Inicio Datos de identificación USUARIO ************-->
	 <fieldset>	  
			<p>
				<br /><br />
			<label>*Contraseña nueva&nbsp;</label>
				<input type="password" 
											name="datosFormUsuario[PASSUSUARIO]"
											value='<?php /*if (isset($restablecerPass['datosFormUsuario']['PASSUSUARIO']['valorCampo']))
																										{  echo $restablecerPass['datosFormUsuario']['PASSUSUARIO']['valorCampo'];}
																				*/?>'
											size="16"
											maxlength="16"
							/>
				<span class="error">
				<?php
						if (isset($restablecerPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje']))
							{echo $restablecerPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'];}
				?>
		 	</span>
				<br />	
				<label>*Repetir contraseña nueva</label> 
					<input type="password"
													name="datosFormUsuario[RPASSUSUARIO]"
													value="<?php /*  if (isset($restablecerPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo']))
																										{  echo $restablecerPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo'];}
																				*/ ?>"
													size="16"
													maxlength="16"
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
		
	 <br /><br />		
	 <div align="center">
	   <input type="submit" name="cambiarPass" value="Guardar nueva contraseña" class="enviar" />	
	   <input type="submit" name="cancelarCambiarPass" value="Cancelar cambio contraseña" class="enviar" />	
	 </div>
 </form>
	
		<br /><br />	 		
</div>
