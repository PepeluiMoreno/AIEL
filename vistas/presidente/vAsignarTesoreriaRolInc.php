<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarTesoreriaRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio buscado previamente para asignarle 
rol de Tesorería

Tiene unos botones para "Asignación Rol Tesoreria", y para "Cancelar asignar rol Tesoreria"

LLAMADA: cPresidente.php:asignarTesoreriaRolBuscar()

LLAMA: vistas/presidente/vCuerpoAsignarTesoreriaRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarTesoreriaRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/presidente/vCuerpoAsignarTesoreriaRol.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>