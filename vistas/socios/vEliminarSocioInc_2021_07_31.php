<?php
/*-----------------------------------------------------------------------------
FICHERO: vEliminarSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para darse de baja por el propio socio. 
             Permite (opcionamente) introducir los comentarios del socio al 
													darse de baja. Muestra algunos datos del socio.
													
RECIBE: $datosSocioEliminar: array con algunos datos del socio												
													
LLAMADA: controladorSocios.php:eliminarSocio()
LLAMA: vistas/socios/vCuerpoEliminarSocio.php 
e incluye las plantillas

OBSERVACIONES:  Probado PHP 7.3.21
------------------------------------------------------------------------------*/
function vEliminarSocioInc($tituloSeccion,$datosSocioEliminar,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
		$datSocio = $datosSocioEliminar['valoresCampos'];	//para form
		
  require_once './vistas/socios/vCuerpoEliminarSocio.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>