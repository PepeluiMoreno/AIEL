<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarCoordAreaBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
un área de coordinación y que permite asignarle un área de coordinación y el rol de coordinador a la vez.

Tiene unos botones para "Asignación coordinación", y para "Cancelar"

LLAMADA: cPresidente.php:asignarCoordinacionArea()

LLAMA: vistas/presidente/vCuerpoAsignarCoordArea.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarCoordAreaInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion,$parValorComboAreaGestion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/presidente/vCuerpoAsignarCoordArea.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>