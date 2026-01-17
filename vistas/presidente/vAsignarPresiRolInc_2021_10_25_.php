<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarPresiRolInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio buscado previamente para asignarle 
rol de Presidencia que lo comparten (presidente/a, vice. y el secretario/a)

Tiene unos botones para "Asignación Rol Presidencia", y para "Cancelar asignar rol Presidencia"

LLAMADA: cPresidente.php:asignarPresidenciaRolBuscar(), asignarPresidenciaRol()

LLAMA: vistas/presidente/vCuerpoAsignarPresiRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarPresiRolInc($tituloSeccion,$enlacesFuncionRolSeccId,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/presidente/vCuerpoAsignarPresiRol.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>