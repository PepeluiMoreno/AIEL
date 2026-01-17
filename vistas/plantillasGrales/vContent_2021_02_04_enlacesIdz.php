<?php
/******************************************************************************
FICHERO: vContent.php
PROYECTO: EL
VERSION: php 7.3.19

Escribe la sección enlaces de menús según rol anotado para ese usuario en BBDD
También el mensaje en grande que puede ser el título en algunas ocasiones

LLAMADO: Es incluido desde los scripts de cuerpo de todas las vistas
LLAMA: escribirLinksSeccionIzda.php:escribirLinksSeccionIzda()

OBSERVACIONES: 

2020-07-31: añado if (isset($_SESSION['vs_enlacesSeccIzda']) && !empty($_SESSION['vs_.....])
para evitar Notices, e incluir las dos opciones $_SESSION['vs_enlacesSeccIzda'] y $enlacesSeccIzda
2020-04-24: Quito enlace a manual de ayuda de aquí y se lleva a BBDD y 
controladorSocios.php:descargarDocsSocio(),cCordinador.php:descargarDocsCoord()..
2016-03-31: Cambios en col-leg, para disminuir ancho de la columna de menú izdo									
<div id ="secciones" class="col-xs-12 col-sm-3 col-md-3 col-lg-3 a col-lg-2"> 
<div id = "content1" class="col-xs-12 col-sm-9 col-md-9 col-lg-9 a col-lg-10"> 

*******************************************************************************/
?>
<div class="content0">

			<!--  <div id ="secciones" class="col-xs-12 col-sm-3 col-md-3 col-lg-3"> --> <!-- alberto -->
			<div id ="secciones" class="col-xs-12 col-sm-3 col-md-3 col-lg-2"> <!-- agustin -->				
			
					<!-- ********* Div para desplazar contenido hacia abajo (excepto en xs) ********** -->
					<div class="hidden-xs" style="padding-top:20px">								
					</div>
					
					<!-- *********************** Inicio Links Idz  *************************** -->
					<?php
					/* Es el texto grande que aparece arriba en la zona izda. de secciones 
								de enlaces en cuando no hay sección de enlaces en lado izdo. 
								'escribirLinksSeccionIzda()'ej: cuando un socio se va a dar de alta.
								Si viene mensaje lo sacamos, si no ponemos enlaces en lado izdo.
					*/								
					//$mensajeIzquierda = 'hola mundo';   
					if (isset($mensajeIzquierda) && !empty($mensajeIzquierda))								
					{
							?>
							<div class="hidden-xs col-sm-12 col-md-12 col-lg-12">
											<p align="center">
															<br /><br />
															<span class="textoAzul18Center"><?php echo $mensajeIzquierda ?></span>
															<br /><br /><br />
											</p>
							</div>
							<?php
					}								
					else 	
					{/* Inicio zona izda. de secciones de enlaces menú según los roles de los usuarios */	
	
							require_once './vistas/login/escribirLinksSeccionIzda.php';
							/* Cuando no ha hecho login como usuario registrado no existe $_SESSION['vs_enlacesSeccIzda'] 
							   porque no tiene funciones de usuario asignadas aún, también en algún otro caso podría recibir
										directamente $enlacesSeccIzda, creo que será siempre con valor $enlacesSeccIzda	='',
										en cualquier caso evitará "Notices" con mensajes si no hay valores.
							*/ 
							if (isset($_SESSION['vs_enlacesSeccIzda']) && !empty($_SESSION['vs_enlacesSeccIzda']))
							{ 
						   $enlacesSeccIzda = $_SESSION['vs_enlacesSeccIzda'];								
							}	
							elseif (!isset($enlacesSeccIzda) || empty($enlacesSeccIzda)) // no está registrado y no tiene funciones asignadas
							{ 
							  $enlacesSeccIzda	='';
							}
							//echo "<br><br>1 vContent.php:enlacesSeccIzda: ";print_r($enlacesSeccIzda);echo "<br>";
							//escribirLinksSeccionIzda($tituloSeccion, $_SESSION['vs_enlacesSeccIzda']);
							escribirLinksSeccionIzda($tituloSeccion,$enlacesSeccIzda);
							
							/* Fin zona izda. de secciones de enlaces menú según los roles de los usuarios  */	
					}
     ?>
					<!-- ************************* Inicio imagen decorativa ******************* -->
					<!--
					<div class="hidden-xs col-sm-12 col-md-12 col-lg-12"><img src="./vistas/images/el_susi.png"  width="150" class="center-block"/>
					</div>					-->
					<!-- ***************************  Fin imagen   *************************** -->
						
			</div> <!-- fin <div id ="secciones" class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
			
			<!-- **************************** Inicio div content1 ************************* -->
			
			<!-- <div id = "content1" class="col-xs-12 col-sm-9 col-md-9 col-lg-9"> --><!-- alberto -->
			<div id = "content1" class="col-xs-12 col-sm-9 col-md-9 col-lg-10"> <!-- agustin -->				

		<!-- Los divs como <div class="content0">,<div id = "content1" se cierran más adelante en otros archivos  -->