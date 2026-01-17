<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO: vAnotarIngresoDonacionNoEncontradoInc.php
VERSION: PHP 7.3.21

DESCRIPCION:
Formulario para el anotar el ingreso de una donación realizada por donante identificado con nombre y NIF 
pero NO registrado previamente 

Se escriben los datos personales y se añaden los datos de la donación

Para entrar en este formulario casos que se pueden dar al anotar una donación: 
- Donante nuevo e identificado (no socio), no registrado previamente como donante será:vAnotarIngresoDonacionNoEncontradoInc()
-	Se ha buscado en "Donante ya identificado" por NIF, o EMAIL, pero no se ha encontado ningún donante previo o socio 
  en la tabla MIEMBRO (caso de que sea socio) o en la tabla DONACION, en la búsqueda y entonces se le trata como 
		"Donante nuevo e identificado (no socio)"

LLAMADA: cTesorero.php:anotarIngresoDonacionMenu o cTesorero.php:anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionNoEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso mas adelente añadir una nueva tabla CONCEPTO_DONACION.
------------------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionNoEncontradoInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$navegacion)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionNoEncontrado.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>