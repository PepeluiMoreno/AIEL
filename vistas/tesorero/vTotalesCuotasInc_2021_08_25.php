<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: vTotalesCuotasInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Se forma y muestra una tabla "TOTALES CUOTAS SOCIOS" con el resumen de los totales de las cuotas 
pagadas y pendientes de los socios, y deglosadas en otros detalles, hasta la fecha actual.
 
Orden decreciente por años. 
Desde la última columna de la tabla "lupa", se podrá llamar a un función, para ver para cada año 
los totales pagos cuotas por cada agrupación 

LLAMADA: cTesorero.php:mostrarTotalesCuotas() 
y previamente desde vistas/tesorero/vMostrarIngresosCuotas.php con botón "Totales pagos cuotas por años" 

LLAMA: vistas/tesorero/vCuerpoTotalesCuotas.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario										
--------------------------------------------------------------------------------------------------*/
function vTotalesCuotasInc($tituloSeccion,$enlacesFuncionRolSeccId,$totalesAniosPagosCuota)
{	

  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoTotalesCuotas.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>