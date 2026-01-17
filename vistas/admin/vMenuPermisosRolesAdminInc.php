<?php
/*-----------------------------------------------------------------------------------------
FICHERO: vMenuPermisosRolesAdminInc.php
VERSION: PHP 7.3.21

DESCRIPCION:				
									
Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Administración" y que permite llamar a las páginas: 

- ASIGNAR-ANULAR ROL DE ADMINISTRACIÓN
- ASIGNAR-ANULAR ROL DE MANTENIMIENTO

También incluye mostrar las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Administración"
													
LLAMADA: cAdmin.php:menuPermisosRolesAdmin()						

LLAMA: vistas/admin/vCuerpoMenuPermisosRolesAdmin.php e incluye plantillasGrales

Desde ese menú con "href" se podrá llamar a: 
cPresidente.php:asignarAdministracionBuscar(),asignarMantenimientoRolBuscar()

OBSERVACIONES:

------------------------------------------------------------------------------*/
function vMenuPermisosRolesAdminInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{ 		
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
 
  require_once './vistas/admin/vCuerpoMenuPermisosRolesAdmin.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>