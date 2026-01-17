<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarListaAdminRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Administración y sus datos personales 

RECIBE: el array $datosSociosAdministracionRol, que procede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosGestoresRoles($codRol)

LLAMADA: cAdmin.php:mostrarListaAdministracionRol(), que a su vez es llamada desde el botón
"LISTA DE SOCIOS/AS CON ROL DE ADMINISTRACIÓN" en vistas/admin/vAsignarAdminRolBuscarInc.php 
y que a su vez se llama desde cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: vistas/admin/vCuerpoMostrarListaAdminRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarListaAdminRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosSociosAdministracionRol,$navegacion)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  
		require_once './vistas/admin/vCuerpoMostrarListaAdminRol.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>