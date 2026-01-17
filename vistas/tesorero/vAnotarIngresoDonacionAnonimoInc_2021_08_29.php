<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnotarIngresoDonacionAnonimoInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante donante ANONIMO 
en el que se añaden los datos de la donación (Donación €, Gastos, Fecha, modo pago, concepto) 
pero ninguno dato personal

LLAMADA: cTesorero.php:anotarIngresoDonacionMenu y anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionAnonimo.php
e incluye plantillasGrales

OBSERVACIONES: 

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que  incluya "GENERAL",
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso 	mas adelente añadir una nueva tabla CONCEPTO_DONACION.
---------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionAnonimoInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$navegacion)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionAnonimo.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>