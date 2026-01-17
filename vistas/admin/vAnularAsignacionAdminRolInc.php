<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnularAsignacionAdminRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de "Administración"  asignado.

Mediante dos botones:  "Eliminar asignación rol Administración", y para "Cancelar" 
se puede retirarle el rol de Administración asignado.

LLAMADA: cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: vistas/admin/vCuerpoAnularAsignacionAdminRol.php 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAnularAsignacionAdminRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 		
  require_once './vistas/admin/vCuerpoAnularAsignacionAdminRol.php';		

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>