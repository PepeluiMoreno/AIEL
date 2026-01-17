<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: vTotalesCuotasAnioAgrupInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario que muestra una tabla "TOTALES CUOTAS POR AGRUPACIONES" 
de un año concreto, hasta la fecha actual.
Ordenadas de modo creciente por nombre de agrupación. 
Detalles en las columnas, incluido las sumas las cuotas pagadas y pendientes de los socios, y otros.  

En la última fila de la tabla, se mostrarán los totales del año correspondiente 

LLAMADA: cTesorero.php:mostrarTotalesCuotasAnioAgrup() 
y previamente desde icono lupa en "vistas/tesorero/vTotalesCuotas.php"

LLAMA: vistas/tesorero/vCuerpoTotalesCuotasAnioAgrup.php
e incluye plantillasGrales

OBSERVACIONES:El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario
---------------------------------------------------------------------------------------------------------*/
function vTotalesCuotasAnioAgrupInc($tituloSeccion,$enlacesFuncionRolSeccId,$totalesPagosCuotaAgrupAnio)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoTotalesCuotasAnioAgrup.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>