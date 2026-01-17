<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarListaCoordinadoresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma una tabla con la lista actual de las Área de gestión (incluye agrupaciones) 
y de socios con los roles de coordinación correspondiente y datos relacionados

ECIBE: el array $resDatosCoordinadores, que pocede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosCoordinadores()

LLAMADA: cPresidente.php:mostrarListaCoordinadores(), que a su vez es llamada desde el botón
"LISTA DE COORDINADORES/AS" en vistas/presidentevAsignarCoordAreaBuscarInc.php y que a su vez se 
llama desde cPresidente&accion=asignarCoordinacionAreaBuscar


LLAMA: vistas/presidente/vCuerpoMostrarListaCoordinadores.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarListaCoordinadoresInc($tituloSeccion,$enlacesFuncionRolSeccId,$resDatosCoordinadores,$navegacion)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
  
		require_once './vistas/presidente/vCuerpoMostrarListaCoordinadores.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>