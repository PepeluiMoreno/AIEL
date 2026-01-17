<?php
/*------------------------------------------------------------------------------------------------------
FICHERO: vListaAgrupacionesPresInc.php
VERSION: PHP 7.3.21

Con datos de tabla " AGRUPACIONTERRITORIAL" se forma una tabla-lista páginada "LISTADO DE AGRUPACIONES", 
y se  muestran algunos datos de cada agrupación territorial. Al final de cada fila dos enlaces: 
icono lupa (ver toda información de esa agrupación), icono pluma (modificar algunos datos de esa agrupación)

RECIBE: un array "$arrDatosAgrupaciones" con los datos de las agrupaciones y $navegacion

LLAMADA: cPresidente.php:listaAgrupacionesPres()
LLAMA: vistas/presidente/vCuerpoListaAgrupacionesPres.php e incluye plantillasGrales

OBSERVACIONES:         
------------------------------------------------------------------------------------------------------*/
function vListaAgrupacionesPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$arrDatosAgrupaciones,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoListaAgrupacionesPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>