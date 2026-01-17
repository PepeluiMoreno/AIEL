<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO: vAnotarIngresoDonacionNoEncontradoInc.php
VERSION: PHP 7.3.21

DESCRIPCION:
Formulario para el anotar el ingreso de una donación realizada por donante identificado 
al menos con Nombre y Apellido 1, pero NO registrado previamente como donante y tampoco es socio 

Se escriben los datos personales que se tengan y se añaden los datos de la donación

Para entrar en este formulario hay dos casos que se pueden dar al anotar una donación: 

- Donante nuevo e identificado (no socio), no registrado previamente como donante será:vAnotarIngresoDonacionNoEncontradoInc()

-	Se ha buscado en "Donante ya identificado" por Número de docuomento (NIF, NIE, pasaporte, otros), o EMAIL, 
  pero no se ha encontado ningún dato en las tablas MIEMBRO (caso de que sea socio) o en la tabla DONACION, 
		y entonces se le trata como 	"Donante nuevo e identificado (no socio)"

LLAMADA: cTesorero.php:anotarIngresoDonacionMenu, o comprobarDonantePrevio_Socio(), o anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionNoEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:

------------------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionNoEncontradoInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$parValoresDonacionConceptos,$navegacion)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionNoEncontrado.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>