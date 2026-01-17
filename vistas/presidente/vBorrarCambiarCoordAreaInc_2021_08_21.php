<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vBorrarCambiarCoordAreaInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante tres botones: "Cambiar el área asignada", "Eliminar asignación ", y para "Cancelar" 
se puede retirarle o cambiarle una coordinación de área territorial,

LLAMADA: cPresidente.php:asignarCoordinacionAreaBuscar()

LLAMA: vistas/presidente/vCuerpoBorrarCambiarCoordArea.php 
e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/

function vBorrarCambiarCoordAreaInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 		
  require_once './vistas/presidente/vCuerpoBorrarCambiarCoordArea.php';		

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>