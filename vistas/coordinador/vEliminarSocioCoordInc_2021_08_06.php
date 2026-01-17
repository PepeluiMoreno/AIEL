<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: vEliminarSocioCoordInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para eliminar los datos de de un socio, por parte de gestor coordinador. 
             Previamente muestra algunos campos de datos y se puede introducir un comentario
													Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
													Además de botón "No dar baja socio/a".
													
LLAMADA: cCoordinador.php.eliminarSocioCoord(), que se llega desde tabla de 
"LISTA DE SOCIo/AS", en "formMostrarDatosSocioCoord.php" al hacer clic en el icono Baja (Papelera)

LLAMA: vistas/coordinador/vCuerpoEliminarSocioCoord.php 
y además incluye las plantillas
												
OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/
function vEliminarSocioCoordInc($tituloSeccion,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 //$datSocio = $datosSocioEliminar['valoresCampos'];		
		
  require_once './vistas/coordinador/vCuerpoEliminarSocioCoord.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>