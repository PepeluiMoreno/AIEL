<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnularAsignacionMantenimientoRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de "Mantenimiento"  asignado.

Mediante dos botones:  "Eliminar asignación rol Mantenimiento", y para "Cancelar" 
se puede retirarle el rol de Mantenimiento asignado.

LLAMADA: cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/vCuerpoAnularAsignacionMantenimientoRol.php 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAnularAsignacionMantenimientoRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 		
  require_once './vistas/admin/vCuerpoAnularAsignacionMantenimientoRol.php';		

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>