<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarMantenimientoRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio buscado previamente para asignarle 
rol de Mantenimiento

Tiene unos botones para "Asignación Rol Mantenimiento", y para "Cancelar asignar rol Mantenimiento"

LLAMADA: cAdmin.php:asignarMantenimientoRolBuscar(), asignarMantenimientoRol()

LLAMA: vistas/admin/vCuerpoAsignarMantenimientoRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarMantenimientoRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/admin/vCuerpoAsignarMantenimientoRol.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>