<?php
/*----------------------------------------------------------------------------------------------------
FICHERO:  vAnotarIngresoDonacionSiEncontradoInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para el anotar el ingreso de una donación realizada por donante socio o 
donante ya registrado previamente y buscado por Nº documento o por su email. 

Se escriben los datos personales existentes y se añaden los datos de la donación.
De los con datos previos ya existentes estos (sexo, APE1,APE2) se dejarán sin modificar (readonly) 
y los demás se podrán introducir o modificar. 

LLAMADA: cTesorero.php:anotarIngresoDonacionMenu() y anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionSiEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso mas adelente añadir una nueva tabla CONCEPTO_DONACION.
------------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionSiEncontradoInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$navegacion)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionSiEncontrado.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>