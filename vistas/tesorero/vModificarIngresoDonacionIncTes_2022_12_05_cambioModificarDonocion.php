<?php
/*--------------------------------------------------------------------------------------------------------
FICHERO: vModificarIngresoDonacionIncTes.php
VERSION: PHP 7.3.21

DESCRIPCION:
Formulario para modificar una donación previa anotada en la tabla DONACION, pero solo los 
se podría modificar datos referentes a los pagos cantidad, gastos, concepto, modo pago, fecha pago 
y observaciones, pero no los datos personales como NIF, email,

Solo se permite modificar donaciones de año anterior y actual. (Por eso condición enero año anterior al actual)

Sólo se usará la cuando después de anotar una donación se comprueba que se ha cometido un error que exige 
una rectificación. 

RECIBE: $datosAnotarDonacion desde cTesorero:modificarIngresoDonacionTes,
$parValoresDonacionConceptos desde  cTesorero:parValoresDonacionConceptos()

LLAMADA: cTesorero.php:modificarIngresoDonacionTes(), y previamente desde el formulario:
vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> MODIFICA (icono pluma))

LLAMA: vistas/tesorero/vCuerpoModificarIngresoDonacionTes.php
e incluye plantillasGrales

OBSERVACIONES: 

2022 cambios en "CONCEPTO" en tabla DONACION. Ahora las distintas opciones de "$parValoresDonacionConceptos" 
están y vienen de la tabla "DONACIONCONCEPTOS" con valores como "COSTAS-MEDALLA-VIRGEN-MERITO-POLICIAL", 
VIII-CONGRESO-AILP-MADRID-2022, y otros que se puedan añadir mas adelante.
-----------------------------------------------------------------------------------------------------------*/
function vModificarIngresoDonacionIncTes($tituloSeccion,$datosAnotarDonacion,$parValoresDonacionConceptos,$navegacion)	
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoModificarIngresoDonacionTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>