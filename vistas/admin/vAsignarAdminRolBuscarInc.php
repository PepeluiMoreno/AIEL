<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarAdminRolBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA ROL Administración" mostrará una tabla con los datos de todos los 
gestores con el Rol de "Administración"

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de "Administración"
Según la situación se podrá después se podrá asignar/eliminar el rol a ese gestor

LLAMADA: cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: vistas/admin/vCuerpoAsignarAdminRolBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
       //vAsignaAdminRolBuscarInc.php
function vAsignarAdminRolBuscarInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosFormSocio,$navegacion)
{	  
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/admin/vCuerpoAsignarAdminRolBuscar.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>