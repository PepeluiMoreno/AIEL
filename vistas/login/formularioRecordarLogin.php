<?php
/***************************************************************************
FICHERO: formularioRecordarLogin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para introducir el email del usuario
             para recibir un correo con los datos de usuario y contraseña
													
LLAMADA: vCuerpoRecordarLogin.php

OBSERVACIONES:
2022-12-22 cambio usuario/a por usuario
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
 <br />	
 ¿Has olvidado tu "usuario" o tu "contraseña" para entrar en el área privada de socios/as?
	<br /><br />	
	 Para restablecer tu "contraseña" o recuperar tu "usuario" de Europa Laica,
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
			<label><b>*Correo electrónico</b></label>
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
  /><label>Recordar tu usuario	</label>
		<br />
  <input type="radio"
         name="recordarPassUser[opcionPassUser]"
         value='PASSWORDYUSUARIO'
			 <?php if ($recordarPassUser['opcionPassUser']['valorCampo']=='PASSWORDYUSUARIO')
         {  echo " checked";}
         ?>						 
  /><label>Restablecer contraseña y recordar tu usuario</label>
		<br />
  </p>		
	</fieldset>
	
	<br /><br />
	
 <input type="submit" value="Enviar">		
</form>
<!--************************* Fin datos para recordar Password y user ************-->
<br />

<!--************************* Inicio link a alta socio ***************************-->
<span class="textoNegro9Left">  Puedes registrarte ahora como socio/a haciendo clic en
      <a href="./index.php?controlador=controladorSocios&amp;accion=altaSocio"><b> Asóciate </b></a> si aún no te has registrado 
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

</div><!--******** Fin <div id="registro"> Incluye todo ********** -->

