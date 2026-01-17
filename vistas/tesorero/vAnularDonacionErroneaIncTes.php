<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO:vAnularDonacionErroneaIncTes.php
VERSION: probada PHP 7.3.21	

DESCRIPCION: 
Formulario sólo para casos de error: Se anulan  algunos campos de la fila correspondiente a una donación
previa anotada en la tabla DONACION, ya que es un error o se ha introducido duplicada.

El formulario muestra los datos de una donación y preguntar si se quiere anular o no esa donación

El tesoreo puede introducir comentarios en el campo OBSERVACIONES, pero no valida el contenido del campo

LLAMADA: Llamada desde cTesorero.php:anularDonacionErroneaTes() y previamente desde el formulario:
vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> ELIMINAR (icono papelera))

LLAMA: vistas/tesorero/vCuerpoAnularDonacionErroneaTes.php
e incluye plantillasGrales

OBSERVACIONES:
------------------------------------------------------------------------------*/
function vAnularDonacionErroneaIncTes($tituloSeccion,$datosDonacion,$navegacion)
{ 
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
	
  require_once './vistas/tesorero/vCuerpoAnularDonacionErroneaTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>