<?php
/*-------------------------------------------------------------------------------------------------------------
FICHERO: vMostrarIngresoDonacionTesInc.php
VERSION:  PHP 7.3.21	

DESCRIPCION: 
Incluye el formulario para mostrar todos los datos de una donación concreta a partir de la tabla DONACION

LLAMADA: cTesorero.php:mostrarIngresoDonacion(), y previamente desde el formulario:
vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> VER (icono lupa))

LLAMA: vistas/tesorero/vCuerpoMostrarIngresoDonacionTes.php
e incluye plantillasGrales

OBSERVACIONES: 
--------------------------------------------------------------------------------------------------------------*/
function vMostrarIngresoDonacionTesInc($tituloSeccion,$datosDonacion,$navegacion)
{ 
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
	
  require_once './vistas/tesorero/vCuerpoMostrarIngresoDonacionTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>