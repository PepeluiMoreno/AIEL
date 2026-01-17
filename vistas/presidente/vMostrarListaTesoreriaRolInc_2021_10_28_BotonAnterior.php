<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarListaTesoreriaRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Tesoreria y sus datos personales 

RECIBE: el array $datosSociosTesoreriaRol, que procede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosGestoresRoles ($codRol = 5)

LLAMADA: cPresidente.php:mostrarListaTesoreriaRol(), que a su vez es llamada desde el botón
"LISTA DE SOCIOS/AS CON ROL DE TESORERÍA" en vistas/presidente/vAsignarTesoreriaRolBuscarInc.php 
y que a su vez se llama desde cPresidente:asignarTesoreriaRolBuscar()

LLAMA: vistas/presidente/vCuerpoMostrarListaTesoreriaRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarListaTesoreriaRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosSociosTesoreriaRol,$navegacion)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  
		require_once './vistas/presidente/vCuerpoMostrarListaTesoreriaRol.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>