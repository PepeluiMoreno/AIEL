<?php
/*--------------------------------------------------------------------------------------------------------
FICHERO: vModificarIngresoDonacionIncTes.php
VERSION: PHP 7.3.21

DESCRIPCION:
Se crea el formulario para modificar una donación previa anotada en la tabla DONACION, pero solo los 
se podría modificar datos referentes a los pagos cantidad, gastos, y no los datos personales NIF, email,

Solo se permite modificar donaciones de año anterior y actual. (Por eso condición enero año anterior al actual)

Sólo se usará la cuando después de anotar una donación se comprueba que se ha cometido un error que exige 
una rectificación. 

LLAMADA: cTesorero.php:modificarIngresoDonacionTes(), y previamente desde el formulario:
vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> MODIFICA (icono pluma))

LLAMA: vistas/tesorero/vCuerpoModificarIngresoDonacionTes.php
e incluye plantillasGrales

OBSERVACIONES: 

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso mas adelente añadir una nueva tabla CONCEPTO_DONACION.
-----------------------------------------------------------------------------------------------------------*/
function vModificarIngresoDonacionIncTes($tituloSeccion,$datosAnotarDonacion,$navegacion)												 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoModificarIngresoDonacionTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>