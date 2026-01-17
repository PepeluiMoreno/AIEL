<?php
/*******************************************************************************
FICHERO: vCabeceraInicial.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: 
Escribe la cabecera inicial de entrada en la aplicación, y la barra superior 
aparece con  links de Entrar, Nuevo socio, Nuevo simpatizante, Recordar c
ontraseña y Salir de la aplicacacion.

OBSERVACIONES: 
********************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- Copyright 2010 Agustín Villacorta, Asociación Europa Laica Inc. All rights reserved.-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>EuropaLaica</title>
        <!--Cargamos bootstrap para diseño Responsive-->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="./vistas/css/bootstrap-3.3.6-dist/bootstrap.min.css" type="text/css" />
        <!--<link rel="stylesheet" href="./vistas/css/cssIndex.css" type="text/css" />-->
	
	<?php
	 /*		
			$directorioRoot = $_SERVER['DOCUMENT_ROOT'];//será:   "/home/virtualmin/europalaica.com/public_html"
			echo "<br /><br />2-1 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioRoot: ";print_r($directorioRoot);// devuelve: /home/virtualmin/europalaica.com/public_html		
   
			//$directorioArchivo = "/../upload/_FILES/TESORERIA/SEPAXML_ISO20022";//ok el directorio "upload" NO es público, estó un nivel más arriba del root acceso restringido solo con PHP		
					
			$directorioAbsoluto = realpath($directorioRoot.$directorioArchivo);//será: /home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
			echo "<br><br>2-2 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioAbsoluto: ".$directorioAbsoluto;			//home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
		 $directorioAbsolutoMasArchivo = $directorioAbsoluto."/".$nomArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-21H10-38-02.xml					
		
			echo "<br><br>2-3 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioAbsolutoMasArchivo: ".$directorioAbsolutoMasArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-28H10-47-31.xml
   */	
	//Se podría definir: define('APLICATION_ROOT', getcwd());echo "<br />aplicacion-root: ".APLICATION_ROOT;
			
 //$directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorioSubir;		
	//echo "<br /><br />vCabeceraInicial.php: ";echo getcwd();echo "<br /><br />";//==/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo
	$directorioVersion['codError'] = '00000';
	$directorioVersion['directorio'] = getcwd();
	$directorioVersion['directorio'] ='';
	//echo "<br /><br />vCabeceraInicial.php:directorio: ".$directorio;
	if ($directorioVersion['directorio'] == '/home/virtualmin/europalaica.com/public_html/usuarios')
	{ echo "<br /><br />vCabeceraInicial.php:usuarios ";
   $linkCSS = "./vistas/css/cssIndex.css";		
	}	
	if ($directorioVersion['directorio'] == '/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo')
	{ echo "<br /><br />vCabeceraInicial.php:desarrollo ";
   //$linkCSS = "./vistas/css/cssIndex.css";
	 	$linkCSS = "./vistas/css/cssIndex_usuarios_desarrollo_2019_10_13.css";
	}
 elseif ($directorioVersion['directorio'] == '/home/virtualmin/europalaica.com/public_html/usuarios_copia')	
	{ echo "<br /><br />vCabeceraInicial.php:copia ";  
	 	$linkCSS = "./vistas/css/cssIndex_usuarios_copia_azul_2019_01_07.css";
	}
	else
	{ $directorioVersion['codError'] = '80000';

  header("HTTP/1.0 404 Not Found");//asigna error 404     
		header('Location:./vistas/mensajes/vError404.php');//página personalizada 404.php.		
		//home/virtualmin/europalaica.com/public_html/usuarios_desarrollo
	}
	?>	
	<!--<link rel="stylesheet" href="./vistas/css/cssIndex.css" type="text/css" />-->
	<link rel="stylesheet" href="<?php echo $linkCSS?>" type="text/css" />
					<!-- Para IE Lo siguiente se añade a cssIndex.css -->
						<!--[if IE]>
							<link rel="stylesheet" href="./vistas/css/cssIndexIE.css" type="text/css" />
						<![endif]-->
</head>

	<body>
	<div class="container-fluid"><!-- Afecta toda la pág. se cierra en pie de pág. -->

	<!-- ********************** Inicio Cabecera con logos ************************--> 
	<div id ="cabecera" class="row">
					
					<div class="col-md-3 col-lg-3">
					<!-- <img src="./vistas/images/lambdaEL_cab_Gris.gif" /> -->
									<img src="./vistas/images/lambdaEL_cab_Gris.png" class="img-responsive center-block"/>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<img src="./vistas/images/textoVerde_AreaSocios_gris_360_91.png" class="img-responsive center-block"/>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">   
									<img src="./vistas/images/logo_laicismo_v3_285_91.png" class="img-responsive center-block"/>				
					</div>
	</div>
	<!-- ************************ Fin Cabecera con logos ************************ -->

	<!-- ******************* Inicio enlaces registrase, contraseña, ... ************** -->
	<nav class="navbar navbar-default">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-principal" aria-expanded="false">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
							</button>
					</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="menu-principal">
							<ul class="nav navbar-nav navbar-right">
									<li><a href="./index.php?controlador=controladorLogin&amp;accion=validarLogin"><b>Entrar </b></a></li>
									<li><a href="./index.php?controlador=controladorSocios&amp;accion=altaSocio"><b>Nuev@ soci@</b></a></li>
									<li><a href="./index.php?controlador=controladorLogin&amp;accion=recordarLogin"><b>Recordar usuario/a y contraseña</b>&nbsp;&nbsp;</a></li>
									<li><a href="./index.php?controlador=controladorLogin&amp;accion=logOut"><b>Salir</b></a></li>
							</ul>
					</div><!-- /.navbar-collapse -->
		</nav>
 
