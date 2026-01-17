<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: vAnotarIngresoDonacionMenuInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que sirve como menú para elegir entre los posibles casos 
que se pueden dar al anotar una donación:

- Donante nuevo e identificado (no socio), pero no registrado previamente como donante 
- Anónimo. 

-	Buscar por "Nº Documento NIF, NIE, pasaporte, otros" o por "email" por ser socio, 
  o donante no socio ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")				
									
									
LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (botón superior: Anotar donaciones)
y después cTesorero.php:anotarIngresoDonacionMenu()

LLAMA: vistas/tesorero/vCuerpoAnotarIngresoDonacionMenu.php
incluye plantillasGrales
       
       
OBSERVACIONES: 
-----------------------------------------------------------------------------------------------------------*/
function vAnotarIngresoDonacionMenuInc($tituloSeccion,$datosSocio,$parValorComboPaisMiembro,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
  
  require_once './vistas/tesorero/vCuerpoAnotarIngresoDonacionMenu.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>