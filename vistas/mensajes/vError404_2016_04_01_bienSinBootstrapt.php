<!-- ***************************************************************************
FICHERO: vError404.php
PROYECTO: EuropaLaica
VERSION: PHP 5.2.3
DESCRIPCION: Escribe la cabecera inicial de entrada en la aplicación, 
             y el mensaje de error 404
OBSERVACIONES: Se llama automáticamente, siempre que se produce un error 404
               ya que esta instrucción de detección de error 
															esta incluida en "htaccess"
***************************************************************************** -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--Copyright 2010 Agustín Villacorta,  Inc. All rights reserved.-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>EuropaLaica</title>
<link rel="stylesheet" href="https://www.europalaica.com/usuarios/vistas/css/cssIndex.css" type="text/css" />
</head>

<body>
<div class="contedorGeneral"><!-- Afecta toda la pág. se cierra en pie de pág. -->

<!-- ********************** Inicio Cabecera con logos ************************--> 
 <div id="cabecera">	
		<div id="cabIdz">
	   <img src="https://www.europalaica.com/usuarios/vistas/images/lambdaEL_cab_Gris.gif" />
				
  </div>
	 <div id="cabDecha">
	   <img src="https://www.europalaica.com/usuarios/vistas/images/textoVerde_Socios_gris1.gif" />
	 </div>
		<div id="sectionLinksBarCab" style="clear:both;">&nbsp;
		</div>
 </div>
<!-- ************************ Fin Cabecera con logos ************************ -->

<!-- ********************** Inicio mensaje ************** -->
 <div align="center">
	<br />

	  <br /><br /><br />
		<h3 align="center">	  	
 	 Se ha producideo el error 404, que se corresponde a una página de nuestro sitio web no encontrada
			<br /><br />	
			Puede ser que hayas introducido algún caracter equivocado, o que haya algún enlace roto en nuestra
			 aplicación infórmatica.
				<br /><br />
			Por favor, inténtalo de nuevo
		</h3>
		<br /><br /><br />		
		<span class="textoAzul9C">	
    Si no has podido resolver el problema puedes enviar un correo electrónico al administrador esta aplicación
		</span>
		<p align="right" >
	  <img src="https://www.europalaica.com/usuarios/vistas/images/email.gif" alt="" /> 
	 	<a href="mailto:adminusers@europalaica.com">adminusers@europalaica.com</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	</p>	
		<br /><br />	 <br /><br />	
	 <!-- ****************************** Fin Cuerpo central  *************************** -->		

 </div>
<!-- ********************** Fin mensaje ************** -->
</div><!--cierre de <div class="contedorGeneral">	que está en vCabeceraInicialNew2-->
	</body>
</html>