<?php
/*-----------------------------------------------------------------------------
FICHERO: vDescargarDocsRolSocioInc.php
VERSION: PHP 5.6.4
DESCRIPCION: incluye las plantillas
LLAMADA: controladorSocios.php:descargarDocsSocio()

OBSERVACIONES: Ver la posibilidad de que sirva para descargar todos archivos roles
2020-04-21: lo añado.
------------------------------------------------------------------------------*/
function vDescargarDocsRolSocioInc($tituloSeccion,$arrListaArchivos,$navegacion)	
{ 
  //echo "<br><br>0 vDescargarDocsRolSocioInc.php:arrListaArchivos: ";print_r($arrListaArchivos);
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 	
  require_once './vistas/socios/vCuerpoDescargarDocsRolSocio.php';
	
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>