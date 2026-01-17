<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vCambiarCoordAreaInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante botón: "Cambiar área coordinación", se puede cambiarle la coordinación del área territorial
 actual por otra área, (o botón para "Cancelar" )


LLAMADA: cPresidente.php:cambiarCoordinacionArea() 

LLAMA: vistas/presidente/vCuerpoCambiarCoordArea.php, 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vCambiarCoordAreaInc($tituloSeccion,$datSocio,$navegacion,$parValorComboAreaGestion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/presidente/vCuerpoCambiarCoordArea.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>