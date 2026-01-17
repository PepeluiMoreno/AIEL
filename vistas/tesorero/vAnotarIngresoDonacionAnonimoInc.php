<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnotarIngresoDonacionAnonimoInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante donante ANONIMO 
en el que se añaden los datos de la donación (Donación €, Gastos, Fecha, modo pago, concepto) 
pero ningún dato personal

LLAMADA: cTesorero.php:anotarIngresoDonacionMenu() o anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionAnonimo.php
e incluye plantillasGrales

OBSERVACIONES: 
---------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionAnonimoInc($tituloSeccion,$datosAnotarDonacion,$parValoresDonacionConceptos,$navegacion)			
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionAnonimo.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>