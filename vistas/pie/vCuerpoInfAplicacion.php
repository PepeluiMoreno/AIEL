<?php
/****************************** Inicio vCuerpoInfAplicacion.php ****************
FICHERO: vCuerpoInfAplicacion.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Contiene los includes necesarios para formar la página de información de la 
aplicación.
														
Muestra un ventana pop-up y blank con información sobre esta aplicación de 
Gestión de Soci@s. Dentro del texto hay algunos links y un "mailto"
No hay tratamiento de errores. No llama a ningún form

LLAMADA: vistas/pie/vInfAplicacioInc.php, 
         desde el "pie", abajo la pantalla, barra horizontal "Sobre esta aplicación"
         
LLAMA: enlaces
*******************************************************************************/
?>
<div class="content0">

  <!--************************ Inicio Cuerpo central ************************-->
	<div class="blank">
			<h3 align="center">
					APLICACIÓN INFORMÁTICA PARA LA GESTIÓN DE SOCIOS/AS DE LA ASOCIACIÓN EUROPA LAICA					
			</h3>

			<br /><br /><br />			
			<span class="textoNegro9Left">		
				En esta aplicación te puedes registrar como socia/o de Europa Laica. 
				Después podrás entrar en la aplicación cuando tu quieras, para ver, modificar o eliminar tus datos.
				<br /><br />
				Si quieres conocer mejor nuestros fines y actividades, puedes verlo en el 					
				<a href="https://laicismo.org/" 
							target="_blank" title="Sitio web de Europa Laica y Laicismo.org">sitio web de Europa Laica y Laicismo.org
				</a>
				<br /><br />
				Para cualquier consulta o sugerencia respecto a esta aplicación informática de 
				gestión de socias/os puedes contactar con Europa Laica enviando un correo electrónico a la dirección: 
				<a href="mailto:info@europalaica.org">info@europalaica.org</a>
    <br /><br />También puedes ponerte en contacto con Europa Laica mediante teléfono(España): 670 55 60 12
	
	   <!--		
			Si tienes dificultad para registrar tus datos mediante esta aplicación, puedes descargarte el formulario de 
			inscripción en formato "PDF" correspondiente a	socios o simpatizantes para
			imprimirlo en papel y después de rellenarlo y firmarlo lo envias por correo postal a:				
	
				-->
				<br /><br />		
				O por correo postal a la dirección:
				<br /><br />
				<strong>Europa Laica
				<br /><br />
				Calle San Bernardo nº 20, Planta 2, Oficina 5
				<br /><br />
					28015 - MADRID
				</strong>
				<br /><br /><br /><br /><br />			
				PROTECCION DE DATOS PERSONALES: En Europa Laica cumplimos el reglamento de Protección de Datos (RGPD, 2016/679).
				Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en 
				<a href="https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad">Protección de datos</a>

			</span>
 </div>

	<div align="center">
			<input onclick=window.close(); type=button value="Anterior" />		
	  <!-- 		<input onclick=window.close(); type=button value="Volver" />		 -->
	</div>
	<br />

	<!-- ****************************** Fin Cuerpo central  ******************** -->
</div><!-- class="content0"> -->
<!-- ******************************* Fin Cuerpo vCuerpoInfAplicacion.php ************* -->