<?php
/* -----------------------------------------------------------------------------
  FICHERO: escribirLinksSeccionIzda.php
  PROYECTO: EL
  VERSION: PHP 7.3.21
		
  DESCRIPCION:Escribe dentro del cuerpo de las vistas los enlaces de la sección
  izquierda a las funciones de los controladores que tiene asociado el c
		correspondiente rol y que están guadadas en la BBDD, "ROLTIENEFUNCION"
  Recibe como parámetros "$tituloSecc" y un array con n filas con los datos
  para formar el enlace: "controlador" (el nombre del fichero controlador),
  "nomFuncion" (el nombre de la función llamada),
  "CODROL" (el código(s) de rol del usuario, no siempre se utilizará )
  "textoMenu" (texto que se verá en el menú)
  "descripcionAlt" (el texto ALT que se verá en el menú)
		
  OBSERVACIONES: Es llamada desde el cuerpo de las vistas,desde vContent.php

		- Cambio el simbolito por el guión.
  - Añadir padding bottom en lugar de br en el menú:
  - Quito brs de 'escribirLinksSeccionIzda.php', así no afecta a diseño móvil
  ----------------------------------------------------------------------------*/
function escribirLinksSeccionIzda($tituloSecc, $enlacesSeccId) 
{
  //echo "<br><br>0-1 vistas/login/escribirLinksSeccionIzda.php:enlacesSeccId: ";print_r($enlacesSeccId);
		?>
		<nav class="navbar navbar-default " style="margin: 0; background-color: #dae3f5; border-color: none">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header" style="float:none">
								<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
												<span class="sr-only"><?php echo $tituloSecc; ?></span>
												<span class="icon-bar"></span>
												<span class="icon-bar"></span>
												<span class="icon-bar"></span>
								</button>
								<div class="navbar-brand"><?php echo $tituloSecc; ?></div>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav nav-pills nav-stacked">
									<?php
									
									if (isset($enlacesSeccId) && (!empty($enlacesSeccId)) && ($enlacesSeccId !== NULL)) 
									{
											//echo "<br><br>1 vistas/login/escribirLinksSeccionIzda.php:enlacesSeccId: ";print_r($enlacesSeccId);
											//  foreach($enlacesSeccId['resultadoFilas'] as $fila=>$enlace)	
											foreach ($enlacesSeccId as $fila => $enlace) 
											{
													?>
													<li>
																<a href="<?php echo './index.php?controlador='.$enlacesSeccId[$fila]['CONTROLADOR']."&amp;accion=".$enlacesSeccId[$fila]['NOMFUNCION'];?>"
																			title="<?php echo ($enlacesSeccId[$fila]['DESCRIPCIONALT']); ?>">
																					
																			<?php echo "- ".( $enlacesSeccId[$fila]['TEXTOMENU']); ?>	             
																</a>
													</li>
													<?php
											}
									}//if
									?>
						</ul>
				</div>
				<?php
}
?>