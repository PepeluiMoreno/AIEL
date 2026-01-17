<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarMantenimientoRolBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA ROLMantenimiento" mostrará una tabla con los datos de todos los 
gestores con el Rol de "Mantenimiento"

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de "Mantenimiento"
Según la situación se podrá después se podrá asignar/eliminar el rol a ese gestor

LLAMADA: cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/vCuerpoAsignarMantenimientoRolBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarMantenimientoRolBuscarInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosFormSocio,$navegacion)
{	  
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/admin/vCuerpoAsignarMantenimientoRolBuscar.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>