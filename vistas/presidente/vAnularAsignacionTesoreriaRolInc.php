<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAnularAsignacionTesoreriaRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Tesoreria asignado.

Mediante dos botones:  "Eliminar asignación rol Tesoreria", y para "Cancelar" 
se puede retirarle el rol de Tesorería asignado.

LLAMADA: cPresidente.php:asignarTesoreriaRolBuscar()

LLAMA: vistas/presidente/vCuerpoAnularAsignacionTesoreriaRol.php 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAnularAsignacionTesoreriaRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 		
  require_once './vistas/presidente/vCuerpoAnularAsignacionTesoreriaRol.php';		

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>