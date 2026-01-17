<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarAdminRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio buscado previamente para asignarle 
rol de Administración

Tiene unos botones para "Asignación Rol Administración", y para "Cancelar asignar rol Administración"

LLAMADA: cAdmin.php:asignarAdministracionRolBuscar(), asignarAdministracionRol()

LLAMA: vistas/admin/vCuerpoAsignarAdminRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarAdminRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/admin/vCuerpoAsignarAdminRol.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>