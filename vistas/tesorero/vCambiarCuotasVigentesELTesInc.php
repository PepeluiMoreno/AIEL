<?php
/*--------------- vCambiarCuotasVigentesELTesInc.php  -----------------------------------------------
FICHERO: vCambiarCuotasVigentesELTesInc.php
VERSIÓN: PHP PHP 7.3.21

DESCRIPCION: Es el formulario donde se muestra el importe y datos actuales de las cuotas vigentes 
para EL para el "tipo de cuota y año elegida", y con un campo para para introducir el nuevo importe 
para ese tipo de cuota y año = (Y+1)   

Se muestra el resultado con número de cambios de cuotas de socios afectados y actualizadas para 
el año siguiente, o mensaje de error.         
	
LLAMADA: cTesorero.php:actualizarCuotasVigentesELTes() al hacer clic en icono "Modificar" (pluma) 
del formulario: vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php

LLAMA: vistas/tesorero/vCuerpoCambiarCuotasVigentesELTes.php
e incluye plantillasGrales

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/
function vCambiarCuotasVigentesELTesInc($tituloSeccion,$datosCuotaEL,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/tesorero/vCuerpoCambiarCuotasVigentesELTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>