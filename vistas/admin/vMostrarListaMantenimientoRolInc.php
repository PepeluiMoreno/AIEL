<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarListaMantenimientoRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Mantenimiento y sus datos personales 

RECIBE: el array $datosSociosMantenimientoRol, que procede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosGestoresRoles($codRol)

LLAMADA: cAdmin.php:mostrarListaMantenimientoRol(), que a su vez es llamada desde el botón
"LISTA DE SOCIOS/AS CON ROL DE Mantenimiento" en vistas/admin/vAsignarMantenimientoRolBuscarInc.php 
y que a su vez se llama desde cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/vCuerpoMostrarListaMantenimientoRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarListaMantenimientoRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosSociosMantenimientoRol,$navegacion)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  
		require_once './vistas/admin/vCuerpoMostrarListaMantenimientoRol.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>