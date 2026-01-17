<?php
/*****************************************************************************************************
FICHERO: vError404.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: Cuando se produce una llamada a una dirección link que no se existe, 
se produce el error 404. En lugar de que salga el mensaje de error por defecto, 
Esta instrucción de detección de error en .htaccess, le dirige a esta página personalizada.

OBSERVACIONES: 2016-04-11 modificaciones estilos para bootstrap y (padding en div #secciones)

NOTA: hay que poner direcciones absolutas para que no de error
*****************************************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- Copyright 2010 Agustín Villacorta, Asociación Europa Laica Inc. All rights reserved.-->

<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>EuropaLaica</title>
	
	<!-- Inicio Cargar bootstrap para diseño Responsive -->
	
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--<link rel="stylesheet" href="./vistas/css/bootstrap-3.3.6-dist/bootstrap.min.css" type="text/css" />	<!-- Esto da error hay que poner dir absoluta -->
	<link rel="stylesheet" href="https://www.europalaica.com/usuarios/vistas/css/bootstrap-3.3.6-dist/bootstrap.min.css" type="text/css" /> 
	
	<!-- Fin Cargar bootstrap para diseño Responsive -----> 
	
	<!--<link rel="stylesheet" href="./vistas/css/cssIndex.css" type="text/css" /> 	<!-- Esto da error hay que poner dir absoluta -->
	<link rel="stylesheet" href="https://www.europalaica.com/usuarios/vistas/css/cssIndex.css" type="text/css" />

	</head>

<body>
 <div class="container-fluid"><!-- Afecta toda la pág. se cierra en pie de pág. -->

			<!-- ********************** Inicio Cabecera con logos ************************--> 
			<div id ="cabecera" class="row">
							
						<div class="col-md-3 col-lg-3">
	         <img src="https://www.europalaica.com/usuarios/vistas/images/lambdaEL_cab_Gris.png" class="img-responsive center-block"/>											
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">										
										<img src="https://www.europalaica.com/usuarios/vistas/images/textoVerde_AreaSocios_gris_360_91.png" class="img-responsive center-block"/>										
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> 									
										<img src="https://www.europalaica.com/usuarios/vistas/images/logo_laicismo_v3_285_91.png" class="img-responsive center-block"/>										
						</div>
			</div>
		<!-- ************************ Fin Cabecera con logos ************************ -->
  <br /><br /><br /><br />
		<!-- ********************** Inicio mensaje ********************************** -->
			<div align="center">			

					<h3 align="center">	  	
						Se ha producido el error 404, que se corresponde a una página de nuestro sitio web no encontrada
						<br /><br />	
						Puede ser que hayas introducido algún caracter equivocado, o que haya algún enlace roto en nuestra
							aplicación informática.
							<br /><br />
						Por favor, inténtalo de nuevo
					</h3>
					<br /><br /><br />		

					<span class="textoAzul9C">	
					Si no has podido resolver el problema puedes enviar un correo electrónico al administrador esta aplicación
					</span>
		
					 <br /><br />	
						<img src="https://www.europalaica.com/usuarios/vistas/images/email.gif" alt="" /> 
						<a href="mailto:adminusers@europalaica.com">adminusers@europalaica.com</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</p>	
					<br /><br />	 <br /><br />	
			</div>
		<!-- ********************** Fin mensaje ************************************* -->
		
 </div><!--<div class="container-fluid">-->
	
	</body>
</html>