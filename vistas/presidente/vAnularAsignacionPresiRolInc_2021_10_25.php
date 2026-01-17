<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnularAsignacionPresiRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Presidencia (Presidencia, Vice, Secretaría) asignado.

Mediante dos botones:  "Eliminar asignación rol Presidencia", y para "Cancelar" 
se puede retirarle el rol de Presidencia asignado.

LLAMADA: cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/vCuerpoAnularAsignacionPresiRol.php 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAnularAsignacionPresiRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 		
  require_once './vistas/presidente/vCuerpoAnularAsignacionPresiRol.php';		

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>