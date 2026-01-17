<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarListaPresiRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Presidencia (Presidencia, Vice, Secretaría) y sus datos personales 

RECIBE: el array $datosSociosPresidenciaRol, que procede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosGestoresRoles($codRol)

LLAMADA: cPresidente.php:mostrarListaPresidenciaRol(), que a su vez es llamada desde el botón
"LISTA DE SOCIOS/AS CON ROL DE PRESIDENCIA" en vistas/presidentevAsignarPresiRolBuscarInc.php 
y que a su vez se llama desde cPresidente:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/vCuerpoMostrarListaPresiRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarListaPresiRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosSociosPresidenciaRol,$navegacion)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  
		require_once './vistas/presidente/vCuerpoMostrarListaPresiRol.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>