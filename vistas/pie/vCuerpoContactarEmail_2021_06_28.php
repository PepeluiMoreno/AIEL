<?php
/****************************** Inicio vCuerpoContactarEmail.php **************
FICHERO: vCuerpoContactarEmail.php
PROYECTO: EL
VERSION: PHP 7.3.21

Contiene el texto de la información general de contactar para formar la página 
de "ContactarEmail" y la llamada al form para que el usuario introduzca 
y envíe un email a "info@europalaica.com" para pedir información

RECIBE: $datosContactarEmail, la primera vez vacío después con los datos si
hubiese error, o incompletos

LLAMADA: vistas/pie/vContactarEmailInc.php
LLAMA: vistas/pie/formContactarEmail.php

********************************************************************************/
?>
<div class="content0">

  <!--************************ Inicio Cuerpo central ************************-->
	<div class="blank">
		<h3 align="center">
	  	CONTACTAR CON EUROPA LAICA
		</h3>
		
		<br /><br />
		<span class="textoNegro8Left">

				Para realizar cualquier consulta respecto a esta aplicación informática de 
				gestión de socios/as<!--y simpatizantes-->, y otros temas sobre Europa Laica, 
				puedes rellenar el siguiente formulario y te responderemos por correo electrónico.
				<br /><br />
				Si no pudieses comunicarte mediante correo electrónico, 
				también puedes ponerte en contacto con Europa Laica mediante correo postal en la dirección:   		
				calle San Bernardo nº 20, Planta 2, Oficina 5. 28015 - MADRID

		</span>
		
		<br /><br />	
					
		<!-- *** Inicio formContactarEmail (se podría pasar com parámetro)******* -->
		<?php require_once './vistas/pie/formContactarEmail.php'; ?>
		<!-- ********************  Fin formContactarEmail *********************** -->					
	
 </div>
	<!-- ****************************** Fin Cuerpo central  ******************** -->
	
</div><!-- <div class="content0"> -->
<!-- ******************************* Fin  vCuerpoContactarEmail.php ************* -->