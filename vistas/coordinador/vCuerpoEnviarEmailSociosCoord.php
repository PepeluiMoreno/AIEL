<?php
/***************************** Inicio vCuerpoEnviarEmailSociosCoord.php ************
FICHERO: vCuerpoEnviarEmailSociosCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para formar los emails a enviar a los socios, por Coordinador
que permite seleccionar los emails de los socios destinatarios dentro se un área de gestión
(que incluye una o varias agrupaciones). En el formulario dentro de ese área de gestión se puede
seleccionar los socios de sólo una agrupación o todas las agrupaciones de ese área

Además del texto de subject y body (incluye cabecera personalizada y final proteccióndatos), 
puede anexar hasta dos ficheros con un límite de 4MB cada y sólo determinados tipos archivos.

El remitente FROM de envío será el email del área de gestión. Ejem: andalucía@europalaica.org 

Es obligatorio incluir un BCC que recibirá una copia del email.
Después mediante la función "emailSociosPersonaGestorPresCoord()" el email llegará a los socios uno a uno

Además el formulario tiene tres botones de selección que permiten elegir:
-siEnviarEmail: Enviar email a socios/as (seleccionados)
-siPruebaEmail: email de prueba solo a BCC oculta
-noEnviarEmail: Salir sin enviar email


LLAMADA: 	vEnviarEmailSociosCoord.php (antes desde cCoordinador.php:enviarEmailSociosCoord())

OBSERVACIONES:
Es similar a vCuerpoEnviarEmailSociosPres.php
***********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central derecho ****************/
 	
			echo $navegacion['cadNavegEnlaces'];
?>
		<br /><br />
		<h3 align="center">
	  	ENVIAR EMAIL A SOCIOS/AS 	
		</h3>	
		<?php require_once './vistas/coordinador/formEnviarEmailSociosCoord.php'; ?>
			
	</div><!-- ***************** Fin Cuerpo central derecho ************** -->	

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->

<!-- ****************************** Fin vCuerpoEnviarEmailSociosCoord.php  ******************** -->