<?php
/*-----------------------------------------------------------------------------
FICHERO: vEliminarSocioPorGestorInc.php

VERSION: PHP 5.6.4

DESCRIPCION: mostrará el formulario para eliminar los datos de de un socio, 
             previamente muestra algunos.
													
OBSERVACIONES: Llamado desde cTesorero.php. Función eliminarSocioTes()													
Agustín: 17-04-16 añadida para rol tesorería la acción "baja": eliminarSocioTes() 

------------------------------------------------------------------------------*/
function vEliminarSocioPorGestorInc($tituloSeccion,$datSocio,$navegacion)
{ echo "<br><br> vEliminarSocioPorGestorInc.php:tituloSeccion: ";print_r($tituloSeccion);
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 //$datSocio = $datosSocioEliminar['valoresCampos'];		
		
  require_once './vistas/gestoresComun/vCuerpoEliminarSocioPorGestor.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>