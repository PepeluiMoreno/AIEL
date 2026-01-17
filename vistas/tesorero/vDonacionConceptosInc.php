<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: vDonacionConceptosInc.php
VERSION: PHP 7.3.21

DESCRIPCION:
Se obtienen los datos de los "Concepto de Donación" existentes partir de la tabla "DONACIONCONCEPTOS" 
para después formar un formulario tabla-lista  "DONACIÓN CONCEPTOS", con las columnas:
"CONCEPTO,NOMBRECONCEPTO,FECHACREACIONCONCEPTO,OBSERVACIONES" y el botón "Añadir Nuevo Concepto de Donación"

LLAMADA: cTesorero.php:mostrarDonaciones() y desde botón "Mostrar y Añadir Conceptos de Donación"
        del formulario vistas/tesorero/vMostrarDonacionesInc.php

LLAMA: vistas/tesorero/vCuerpoDonacionConceptos.php 

OBSERVACIONES:													
------------------------------------------------------------------------------------------------------*/
function vDonacionConceptosInc($tituloSeccion,$arrDonacionConceptos,$navegacion)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoDonacionConceptos.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>