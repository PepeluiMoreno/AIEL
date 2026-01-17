<?php
/* ***************************************************************************
FICHERO: formularioLogin.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Es el formulario para introducir el nombre de usuario y contraseña
             Utiliza el parámetro "$datosUsuario" que contiene los mensajes error
             después de la validación de este formulario. 
													
OBSERVACIONES: Es incluido desde vCuerpoLogin.php
******************************************************************************/
?>

<div id="registro"><!--  Inicio <div id="registro"> Incluye todo  -->

<form method="post" action="./index.php?controlador=controladorLogin&amp;accion=validarLogin">
		
		<span class="textoNegro9Left">   	
					Si ya te has registrado como socio/a, para entrar en el área privada de socios/as tienes que introducir los siguientes datos:
		</span>	
		<br /><br />
		
	<fieldset>	
 <p>
		
		<br />
	 <label>Usuario/a&nbsp;&nbsp;</label>			
		  <input type="text"
	           name="USUARIO"
	           value='<?php if (isset($datosUsuario['USUARIO']['valorCampo']) && 
												                 $datosUsuario['USUARIO']['valorCampo']!=="")
                         {echo $datosUsuario['USUARIO']['valorCampo'];}
                    ?>'
            size="30"
            maxlength="30"
       />	 		
		  <span class="error">
        <?php if (isset($datosUsuario['USUARIO']['errorMensaje']))
              {  echo $datosUsuario['USUARIO']['errorMensaje'];}
        ?>
		  </span>
   <br />	<br />
   <label>Contraseña</label>
			<input type="password" 
		  	name="CLAVE"
      value='<?php if (isset($datosUsuario['CLAVE']['valorCampo']) && $datosUsuario['CLAVE']['valorCampo']!=="")
                   {echo $datosUsuario['CLAVE']['valorCampo'];}
            ?>'
            size="30"
            maxlength="40"												
			/>	 	
			<span class="error">
			<?php 
			  if (isset($datosUsuario['CLAVE']['errorMensaje']))
		    {echo $datosUsuario['CLAVE']['errorMensaje'];}
			?>
			</span>
			<br />	<br /><br />	<br />
			<span class="textoNegro8Left">
			<a href="./index.php?controlador=controladorLogin&amp;accion=recordarLogin">
		  ¿Recordar contraseña olvidada?</a>
		 </span>		
     
		</p>			

	 </fieldset>	
		<br /><br />
			<input type="submit" value="Entrar" />				
			
 </form>
		
		<br /><br /><br />
			<span class="textoNegro9Left">
					  Si aún no te has registrado como socio/a en Europa Laica, puedes hacerlo ahora haciendo clic en  
		  <a href="./index.php?controlador=controladorSocios&amp;accion=altaSocio">
	    "Asóciate"</a>
				<br /><br /><br /><br />				
		</span>
		
	</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->