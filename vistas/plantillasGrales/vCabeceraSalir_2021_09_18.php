<?php
/*****************************************************************************************************
FICHERO: vCabeceraSalir.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21
DESCRIPCION: Escribe la cabecera de la aplicación con los logos y la barra horizontal 
             que está debajo sólo con el link de "Salir"

OBSERVACIONES: 2016-04-11 modificaciones estilos para bootstrap y (padding en div #secciones)

- "usuarios": "cssIndex.css" la versión de pública de explotación (BBDD: europalaica_com).	
               cabecera fondo color gris	  

Modificación de cabecera con <link rel="stylesheet" href="<?php echo $linkCabeceraCSS ?>: 

Cambia color fondo de la "cabecera" que contiene logos y texto cabecera, 
a distintos colores de fondo, que sirven para diferenciar las versiones de uso 
de la aplicación: usuarios,usuarios_copia,usuarios_desarrollo,usuarios_produccion.
														
- "usuarios_copia": "cssIndex_usuarios_copia_cabeceraAzul.css"
                    la versión de pruebas espejo de 'usuarios' (BBDD: europalaica_com_copia)	                    
																				(en esta versión se copia la versión "usuarios" para hacer pruebas 
																				de funciones críticas: Cierre año, Remesas, etc...)
- "usuarios_desarrollo": "cssIndex_usuarios_desarrollo_cabeceraVerde.css"
                         la versión de desarrollo (BBDD: europalaica_com_desarrollo).                          
- "usuarios_produccion": "cssIndex_usuarios_produccion_cabeceraRojo.css"
                         la versión de prueba espejo de 'usuarios_desarrollo' (BBDD europalaica_com_produccion)   
																									
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
	<link rel="stylesheet" href="./vistas/css/bootstrap-3.3.6-dist/bootstrap.min.css" type="text/css" />	
	<!-- Fin Cargar bootstrap para diseño Responsive -----> 
	
	<link rel="stylesheet" href="./vistas/css/cssIndex.css" type="text/css" />	

 <!-- ********** Inicio Modificación color cabecera *********************************************************** --> 
 <?php	
	$linkCabeceraCSS = '';

 if ($_SERVER['SCRIPT_NAME'] == '/usuarios_copia/index.php')
 { echo "*** VERSIÓN COPIA *** (MODIFICARÁ BBDD: europalaica_com_copia)";
			$linkCabeceraCSS = "./vistas/css/cssIndex_usuarios_copia_cabeceraAzul.css";			
	}
	elseif ($_SERVER['SCRIPT_NAME'] == '/usuarios_desarrollo/index.php')
	{ echo "*** VERSIÓN DESARROLLO *** (MODIFICARÁ BBDD: europalaica_com_desarrollo)";   	 	
			$linkCabeceraCSS = "./vistas/css/cssIndex_usuarios_desarrollo_cabeceraVerde.css";
	}
	elseif ($_SERVER['SCRIPT_NAME'] == '/usuarios_produccion/index.php')
 { echo "*** VERSIÓN PRODUCCIÓN *** (MODIFICARÁ BBDD: europalaica_com_produccion)";
			$linkCabeceraCSS = "./vistas/css/cssIndex_usuarios_produccion_cabeceraRojo.css";			
	}

 if (isset($linkCabeceraCSS) && !empty($linkCabeceraCSS))
 { 
 ?>
			<link rel="stylesheet" href="<?php echo $linkCabeceraCSS ?>" type="text/css" />			
	<?php
	}	
	?>
 <!-- ******** Fin Modificación color cabecera **************************************************************** -->	

</head>

<body>
	<div class="container-fluid"><!-- Afecta toda la pág. se cierra en pie de pág. -->

		<!-- ********************** Inicio Cabecera con logos ************************ --> 
		<div id ="cabecera" class="row">
						
						<div class="col-md-3 col-lg-3">
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

		<!-- ******************* Inicio barra horizontal enlace salir *************** -->
		<nav class="navbar navbar-default">
						<div class="container-fluid">
										<ul class="nav navbar-nav navbar-right">
														<li style="float:right"><a href="./index.php?controlador=controladorLogin&amp;accion=logOut"><strong>Salir</strong>&nbsp;&nbsp;&nbsp;</a></li>
										</ul>
						</div>
		</nav>
		<!-- ******************* Fin barra horizontal enlace salir ****************** -->

