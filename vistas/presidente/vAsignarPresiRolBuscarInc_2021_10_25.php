<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarPresiRolBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA ROL PRESIDENCIA" mostrará una tabla con los datos de todos los 
gestores con el Rol de "Presidencia, Vice., Secretaría"

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de "Presidencia, Vice., Secretaría"
Según la situación se podrá después se podrá asignar/eliminar el rol a ese gestor

LLAMADA: cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/vCuerpoAsignarPresiRolBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarPresiRolBuscarInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosFormSocio,$navegacion)
{	  
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoAsignarPresiRolBuscar.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>