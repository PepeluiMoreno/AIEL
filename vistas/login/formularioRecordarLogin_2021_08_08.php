<?php
/***************************************************************************
FICHERO: formularioRecordarLogin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para introducir el email del usuario
             para recibir un correo con los datos de usuario y contraseña
													
LLAMADA: vCuerpoRecordarLogin.php

OBSERVACIONES:
****************************************************************************/
?>

<br />
<div id="registro"> <!--******** Inicio <div id="registro"> Incluye todo ********** -->

<!--************************* Inicio textoDesdeControlador **********************-->
<span class="textoNegro9Left">
 <strong>
 <?php echo $textoPrimero;//contiene el texto que se envía como parámetro desde controlador
	?>
	</strong>
</span>
<!--************************* Fin textoDesdeControlador *************************-->
<br /><br />
<!--************************* Inicio textoFijo **********************************-->
<span class="textoNegro9Left">
 ¿Has olvidado tu usuario/a o tu contraseña para entrar en el área privada de socios/as?
	<br /><br />	
	 Para restablecer tu contraseña o recuperar tu nombre de usuario/a de Europa Laica,
		escribe tu dirección de correo electrónico y recibirás los datos en tu correo electrónico 
	(el que está registrado en Europa Laica)
</span>
<!--************************* Fin textoFijo ************************************-->
<br /><br />

<!--************************* Inicio datos para recordar Password y user *********-->
<form method="post"
	     action="./index.php?controlador=controladorLogin&accion=recordarLogin">
	<fieldset>	
		<p>							
			<label>*Correo electrónico</label>
	    <input type="text"
	           name="recordarPassUser[EMAIL]"
	           value='<?php if (isset($recordarPassUser['EMAIL']['valorCampo'] ))
                          {  echo $recordarPassUser['EMAIL']['valorCampo'] ;}
                    ?>'
             size="24"
             maxlength="254"
       />
		<span class="error"><strong>
	   <?php
			  if (isset($recordarPassUser['EMAIL']['errorMensaje']))
		    {echo $recordarPassUser['EMAIL']['errorMensaje'];}
			 ?></strong>
  </span>		
		<br /><br />
		
		<label><b>*Elige una opción:</b></label>
  <span class="error"><strong>
  <?php
  if (isset($recordarPassUser['opcionPassUser']['errorMensaje']))
  {echo $recordarPassUser['opcionPassUser']['errorMensaje'];}
  ?></strong>
	</span>
		<br />
  <input type="radio"
         name="recordarPassUser[opcionPassUser]"
         value='PASSWORD' 
			 <?php if ($recordarPassUser['opcionPassUser']['valorCampo']=='PASSWORD')
         {  echo " checked";}
         ?>
  /><label>Restablecer contraseña</label>
		<br />
  <input type="radio"
         name="recordarPassUser[opcionPassUser]"
         value='USUARIO'
			 <?php if ($recordarPassUser['opcionPassUser']['valorCampo']=='USUARIO')
         {  echo " checked";}
         ?>						 
  /><label>Recordar nombre de usuario/a	</label>
		<br />
  <input type="radio"
         name="recordarPassUser[opcionPassUser]"
         value='PASSWORDYUSUARIO'
			 <?php if ($recordarPassUser['opcionPassUser']['valorCampo']=='PASSWORDYUSUARIO')
         {  echo " checked";}
         ?>						 
  /><label>Restablecer contraseña y recordar nombre de usuario/a</label>
  </p>		
	</fieldset>
	
 <input type="submit" value="Enviar">		
</form>
<!--************************* Fin datos para recordar Password y user ************-->
<br />

<!--************************* Inicio link a alta socio ***************************-->
<span class="textoNegro9Left">
  Puedes registrarte ahora como socio/a haciendo clic
  <a href="./index.php?controlador=controladorSocios&amp;accion=altaSocio">
  "Asóciate"</a> si aún no te has registrado 
		<?php
		/* desactivado por si más adelante se decide incorporar
		<br /><br /> o bien como simpatizante seleccionando
		<a href="./index.php?controlador=controladorSimpatizantes&amp;accion=altaSimpatizante">
  "Nuevo simpatizante"</a>
		*/ 
		?>
</span>
<!--************************* Fin link a alta socio ********************************-->

<br /><br />
<!--************************* Inicio botón a página de login ************************-->
<form method="post"
      action="./index.php?controlador=controladorLogin&accion=validarLogin">
   <input type="submit" value="Salir e ir a página de inicio">
</form>

<!--************************* Fin botón a página de login ***************************-->
</div><!--******** Fin <div id="registro"> Incluye todo ********** -->

