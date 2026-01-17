<?php
/* -----------------------------------------------------------------------------
FICHERO: formCambiarPass.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para la cambiar la contraseña o el nombre de usuario

LLAMADA: vCuerpoCambiarPassSocio.php y previamente de controladorSocios.php:cambiarPassSocio()  	
	
OBSERVACIONES:
------------------------------------------------------------------------------- */
?>
<div id="registro">
			<br /> 
			<span class="textoAzu112Left2">
							En este formulario puedes cambiar la contraseña que elegiste
							para entrar en la aplicación de "Área de Soci@s" de Europa Laica 
			</span>
			<br /><br /><br /><br /> 
			<form name="actualizarSocio" method="post"
									action="./index.php?controlador=controladorSocios&amp;accion=cambiarPassSocio">

					<!--****************** Inicio Datos de contraseña ************-->
					<fieldset>
									<legend><strong>Contraseña para entrar en el "Área de Soci@s" de Europa Laica</strong></legend>
									<p>
												<span class="comentario11">
																Para establecer una nueva contraseña, debes primero escribir la contraseña actual 
												</span>	
												<br /><br />	  
												<label>*Contraseña actual</label>
												<input type="password"
																			name="datosFormUsuario[actPASSUSUARIO]"
																			value='<?php
																			if (isset($datosPass['datosFormUsuario']['actPASSUSUARIO']['valorCampo'])) 
																			{
																							echo $datosPass['datosFormUsuario']['actPASSUSUARIO']['valorCampo'];
																			}
																			?>'
																			size="16"
																			maxlength="16"
																			/>
												<span class="error">
																<?php
																if (isset($datosPass['datosFormUsuario']['actPASSUSUARIO']['errorMensaje'])) 
																{
																				echo $datosPass['datosFormUsuario']['actPASSUSUARIO']['errorMensaje'];
																}
																?>
												</span>
												<br /><br />
												<span class="comentario11">
																La nueva contraseña debe tener seis caracteres como mínimo (distingue entre mayúsculas y minúsculas)
												</span>
												<br />
												<label>*Contraseña nueva&nbsp;</label>
												<input type="password" 
																			name="datosFormUsuario[PASSUSUARIO]"
																			value='<?php /* if (isset($datosPass['datosFormUsuario']['PASSUSUARIO']['valorCampo']))
																		{  echo $datosPass['datosFormUsuario']['PASSUSUARIO']['valorCampo'];}
																	*/ ?>'
																			size="16"
																			maxlength="16"
																			/>
												<span class="error">
																<?php
																if (isset($datosPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'])) 
																{
																				echo $datosPass['datosFormUsuario']['PASSUSUARIO']['errorMensaje'];
																}
																?>
												</span>
												<br />	
												<label>*Repetir contraseña nueva</label> 
												<input type="password"
																			name="datosFormUsuario[RPASSUSUARIO]"
																			value='<?php /*  if (isset($datosPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo']))
																		{  echo $datosPass['datosFormUsuario']['RPASSUSUARIO']['valorCampo'];}
																	*/ ?>'
																			size="16"
																			maxlength="16"
																			/> 			
												<span class="error">
												<?php
												if (isset($datosPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'])) 
												{
																echo $datosPass['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'];
												}
												?>
												</span>
												<br />															
									</p>
					</fieldset>
					<!--********************** Fin Datos de contraseña ***************-->
					
					<br />	
					<div align="center">
									<input type="submit" name="cambiarPass" value="Guardar nueva contraseña" class="enviar" />	
									<input type="submit" name="cancelarCambiarPass" value="Cancelar cambio contraseña" class="enviar" />	
					</div>
			</form> 
</div>
