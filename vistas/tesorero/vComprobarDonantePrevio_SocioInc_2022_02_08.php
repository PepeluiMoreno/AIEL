<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vComprobarDonantePrevio_SocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que sirve para introducir el "NIF" o el "email", para buscar 
los datos personales de un donante que puede ser socio, o un donante ya registrado previamente
por haber realizado alguna donación anteriormente:

-	Buscar por "Nº Documento" NIF, NIE, pasaporte, otros, por ser socio o donante no socio 
         ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")									
-	Buscar por "Email" por ser socio o donante no socio ya anotado 
 (buscará en las tablas "MIEMBROS" o "DONACION")
									
Después de introducir los datos pedidos en el formulario llevará de 
cTesorero.php:comprobarDonantePrevio_Socio(), que (previa validación) llama a la función buscarDonante(),  

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionMenu.php y desde cTesorero.php:comprobarDonantePrevio_Socio()

LLAMA: vistas/tesorero/vCuerpoComprobarDonantePrevio_Socio.php
e incluye plantillasGrales

OBSERVACIONES: 
---------------------------------------------------------------------------------------------------*/
function vComprobarDonantePrevio_SocioInc($tituloSeccion,$datosAnotarDonacion,$parValorComboPaisMiembro,$navegacion)										 
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoComprobarDonantePrevio_Socio.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>