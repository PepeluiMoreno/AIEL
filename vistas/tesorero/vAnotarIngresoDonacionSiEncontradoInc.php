<?php
/*----------------------------------------------------------------------------------------------------
FICHERO:  vAnotarIngresoDonacionSiEncontradoInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante socio o 
donante ya registrado previamente y buscadoNúmero de docuomento (NIF, NIE, pasaporte, otros), o EMAIL, 

Además de los datos personales recuperados de las tablas MIEMBRO o DONACION,  y se añaden los datos de la donación.
De losrecuperados estos (sexo, APE1,APE2) se dejarán sin modificar (readonly) y los demás 
se podrán introducir o modificar. 

LLAMADA: cTesorero.php:comprobarDonantePrevio_Socio(), o anotarIngresoDonacion()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionSiEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:
------------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionSiEncontradoInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$parValoresDonacionConceptos,$navegacion)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionSiEncontrado.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>