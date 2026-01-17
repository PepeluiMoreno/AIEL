<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vAsignarCoordAreaBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE COORDINADORES/AS" mostrará una tabla con los datos de todos los coordinadores

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de cooordinador.
Según la situación se podrá después se podrá asignar/modificar/eliminar un área de coordinación

LLAMADA: cPresidente.php: asignarCoordinacionAreaBuscar()

LLAMA: vistas/presidente/vCuerpoAsignarCoordAreaBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vAsignarCoordAreaBuscarInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosFormSocio,$navegacion)
{
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoAsignarCoordAreaBuscar.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>