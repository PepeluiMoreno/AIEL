<?php
/*-----------------------------------------------------------------------------------------
FICHERO: vMenuPermisosRolesPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION:													
Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Presidencia" y que permite llamar a las páginas: 

- ASIGNAR-MODIFICAR-ANULAR ROL DE COORDINADOR/A A AGRUPACIONES TERRITORIALES 
- ASIGNAR-ANULAR ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría) 
- ASIGNAR-ANULAR ROL DE TESORERÍA

También incluye mostra las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Presidencia"
													
LLAMADA: cPresidente.php:menuPermisosRolesPres()						

LLAMA: vistas/presidente/vCuerpoMenuPermisosRolesPres.php e incluye plantillasGrales

Desde ese menú con "href" se podrá llamar a: 
cPresidente.php:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreriaRolBuscar()

OBSERVACIONES:

------------------------------------------------------------------------------*/
function vMenuPermisosRolesPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{ 		
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
 
  require_once './vistas/presidente/vCuerpoMenuPermisosRolesPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>